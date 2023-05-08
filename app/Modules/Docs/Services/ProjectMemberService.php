<?php

namespace App\Modules\Docs\Services;

use App\Exceptions\Exception;
use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\Docs\FieldMapping;
use App\Models\Docs\OperationLog;
use App\Models\Docs\Project;
use App\Models\Docs\ProjectMember;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProjectMemberService extends Service
{
    public function index()
    {
        $login_user_id = getLoginUserId();
        $request = request();
        $project_id = $request->input('project_id');
        if (empty($project_id)) {
            return $this->getPaginateFormat([]);
        }
        // 验证访问权限
        $project = Project::getDetailById($project_id);
        Project::checkRolePowerThrow($project, ProjectMember::ROLE_POWER_ADMIN);

        $type = $request->input('type', -1);
        $search = $request->input('search', '');
        $projectBuild = ProjectMember::getInstance();
        $lists = $projectBuild
            ->where('project_id', $project_id)
            ->where(function ($query) use ($search, $type){
                if (!empty($search)){
                    $query->where('project_name', 'LIKE', trim($search) . '%');
                }
                if ($type > -1){
                    $query->where('project_type', '=', $type);
                }
            })
            ->with(['user', 'userInfo'])
            ->orderByDESC('id')
            ->paginate($this->getLimit());

        return $this->getPaginateFormat($lists);
    }

    protected function getProjectUserById($project_id, $id, $role_power = ProjectMember::ROLE_POWER_READ)
    {
        $projectUser = ProjectMember::with(['project', 'userInfo'])->where('project_id', $project_id)->find($id);
        if (empty($projectUser)){
            throw new BadRequestException('项目成员不存在或已删除！');
        }
        // 验证访问权限
        if ($role_power != -1){
            $throw_msg = '您无权限查看项目成员`' . $projectUser->userInfo->nick_name . '`！';
            switch ($role_power){
                case ProjectMember::ROLE_POWER_WRITE:
                    $throw_msg = '您无权限编辑项目成员`' . $projectUser->userInfo->nick_name . '`！';
                    break;
                case ProjectMember::ROLE_POWER_DELETE_PROJECT_CHILDS:
                    $throw_msg = '您无权限删除项目成员`' . $projectUser->userInfo->nick_name . '`！';
                    break;
            }
            Project::checkRolePowerThrow($projectUser->project, $role_power, $throw_msg);
        }

        return $projectUser;
    }

    public function createOrUpdate(Request $request)
    {
        $login_user_id = getLoginUserId();
        $project_id = $request->input('project_id');
        $project = $this->getProjectById($project_id);
        if (!$project){
            throw new BadRequestException('项目不存在或已删除！');
        }
        // 验证新增编辑权限
        Project::checkRolePowerThrow($project, ProjectMember::ROLE_POWER_ADMIN);

        $user_id = $request->input('user_id');
        $id = $request->input('id', 0);

        if ($id){
            $lock_key = 'create:project:mermber:' . $id;
        }else{
            $lock_key = 'create:project:mermber:' . $user_id;
        }
        $lock = Cache::lock($lock_key, 60);
        try{
            $create = true;
            if (!$id){
                // 验证会员是否已成员项目成员
                if (ProjectMember::where('project_id', $project->project_id)->where('user_id', $user_id)->first()){
                    throw new BadRequestException('该会员已成为项目成员！');
                }

                $detail = new ProjectMember();
                $detail->user_id = $user_id;
                $detail->project_id = $project->project_id;
            }else{
                $create = false;
                $detail = $this->getProjectUserById($project->id, $id, -1);
            }

            DB::beginTransaction();
            try {
                $detail->role_power = $request->input('role_power', ProjectMember::ROLE_POWER_READ);
                if ($request->has('alias_name')){
                    $detail->alias_name = $request->input('alias_name', '');
                }
                $detail->save();

                DB::commit();
            }catch (Exception $e){
                DB::rollBack();
                throw new BadRequestException('项目成员' . ($create ? '创建' : '更新') . '失败，请重试！' . $e->getMessage());
            }
        }catch (Exception $e){
            throw new BadRequestException($e->getMessage());
        } finally {
            Cache::restoreLock($lock_key, $lock->owner());
        }

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_PROJECT_MEMBER, $create ? OperationLog::ACTION['CREATE'] : OperationLog::ACTION['UPDATE'], $detail);

        return $detail;
    }

    // 设置成员权限
    public function setRolePower(Request $request)
    {
        $login_user_id = getLoginUserId();
        $project_id = $request->input('project_id');
        $project = $this->getProjectById($project_id);
        // 验证登录会员的权限
        Project::checkRolePowerThrow($project, ProjectMember::ROLE_POWER_ADMIN);

        $user_id = $request->input('user_id');

        $lock_key = 'set:project:mermber:power:' . $user_id;
        $lock = Cache::lock($lock_key, 60);
        try{
            $member = ProjectMember::where('project_id', $project->project_id)
                ->where('user_id', $user_id)
                ->lock()
                ->first();
            if (!$member){
                throw new BadRequestException('项目成员不存在或已删除');
            }
            $member->role_power = $request->input('role_power', ProjectMember::ROLE_POWER_READ);
            $member->save();
        }catch (Exception $e){
            throw new BadRequestException($e->getMessage());
        } finally {
            Cache::restoreLock($lock_key, $lock->owner());
        }

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_PROJECT_MEMBER, OperationLog::ACTION['ROLE_POWER'], $member);

        return $member;
    }

    // 设置成员是否为管理员
    public function setLeader(Request $request)
    {
        $login_user_id = getLoginUserId();
        $project_id = $request->input('project_id');
        $project = $this->getProjectById($project_id);
        if ($project->user_id != $login_user_id){
            throw new ForbiddenException('仅限项目创建人可设置成员的管理员权限！');
        }

        $user_id = $request->input('user_id');

        $lock_key = 'set:project:mermber:power:' . $user_id;
        $lock = Cache::lock($lock_key, 60);
        try{
            $member = ProjectMember::where('project_id', $project->project_id)
                ->where('user_id', $user_id)
                ->lock()
                ->first();
            if (!$member){
                throw new BadRequestException('项目成员不存在或已删除！');
            }
            $member->is_leader = $request->input('is_leader', 0);
            $member->save();
        }catch (Exception $e){
            throw new BadRequestException($e->getMessage());
        } finally {
            Cache::restoreLock($lock_key, $lock->owner());
        }

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_PROJECT_MEMBER, OperationLog::ACTION['IS_LEADER'], $member);

        return $member;
    }

    protected function getProjectById($project_id)
    {
        $project = Project::getDetailById($project_id);
        if (empty($project)){
            throw new BadRequestException('项目不存在或已删除！');
        }
        return $project;
    }

    public function delete(Request $request)
    {
        $project_id = $request->input('project_id');
        // 验证登录会员的权限
        $project = $this->getProjectById($project_id);
        // 验证登录会员的权限
        Project::checkRolePowerThrow($project, ProjectMember::ROLE_POWER_ADMIN);

        $user_id = $request->input('user_id');

        $member = ProjectMember::where('project_id', $project->project_id)
            ->where('user_id', $user_id)
            ->first();
        if (!$member){
            throw new BadRequestException('项目成员不存在或已删除');
        }

        $member->delete();

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_PROJECT_MEMBER, OperationLog::ACTION['DELETE'], $member);

        return $member;
    }
}
