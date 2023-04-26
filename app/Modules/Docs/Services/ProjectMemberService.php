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

    protected function getProjectUserById($project_id, $id, $with = [], $check_auth = true)
    {
        $projectUser = ProjectMember::with(array_merge(['project'], $with))->where('project_id', $project_id)->find($id);
        if (empty($projectUser)){
            throw new BadRequestException('项目成员不存在或已删除！');
        }
        return $projectUser;
    }

    public function detail($project_id)
    {
        return $this->getProjectUserById($project_id);
    }

    public function createOrUpdate(Request $request)
    {
        $login_user_id = getLoginUserId();
        $project_id = $request->input('project_id');
        // 验证登录会员的项目权限
        $project = $this->getProjectById($project_id);

        $create = true;
        $id = $request->input('id', 0);
        if (!$id){
            $detail = new ProjectMember();
            $detail->user_id = $request->input('user_id');
            $detail->project_id = $project->project_id;
        }else{
            $create = false;
            $detail = $this->getProjectUserById($project->id, $id);
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

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_PROJECT_MEMBER, $create ? OperationLog::ACTION['CREATE'] : OperationLog::ACTION['UPDATE'], $detail);

        return $detail;
    }

    public function setRolePower(Request $request)
    {
        $login_user_id = getLoginUserId();
        $project_id = $request->input('project_id');
        // 验证登录会员的项目权限
        $project = $this->getProjectById($project_id);

        $user_id = $request->input('user_id');

        $lock_key = 'set:project:mermber:power:' . $user_id;
        $lock = Cache::lock($lock_key, 60);
        try{
            $member = ProjectMember::where('project_id', $project->project_id)
                ->where('user_id', $user_id)
                ->lock()
                ->first();
            if (!$member){
                throw new BadRequestException('项目成员不存在');
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

    protected function getProjectById($project_id, $check_auth = true)
    {
        $project = Project::getDetailById($project_id);
        if (empty($project)){
            throw new BadRequestException('项目不存在或已删除！');
        }
        if ($check_auth && $project->user_id != getLoginUserId()){
            throw new ForbiddenException('您无权设置项目成员');
        }
        return $project;
    }

    public function delete(Request $request)
    {
        $project_id = $request->input('project_id');
        // 验证登录会员的项目权限
        $project = $this->getProjectById($project_id);

        $user_id = $request->input('user_id');

        $member = ProjectMember::where('project_id', $project->project_id)
            ->where('user_id', $user_id)
            ->first();

        $member->delete();

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_PROJECT_MEMBER, OperationLog::ACTION['DELETE'], $member);

        return $member;
    }
}
