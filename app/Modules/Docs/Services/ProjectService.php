<?php

namespace App\Modules\Docs\Services;

use App\Exceptions\Exception;
use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\Docs\OperationLog;
use App\Models\Docs\Project;
use App\Models\Docs\ProjectMember;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class ProjectService extends Service
{
    public function index()
    {
        $login_user_id = getLoginUserId();
        $request = request();
        $type = $request->input('type', -1);
        $search = $request->input('search', '');
        $projectBuild = Project::getInstance();
        $lists = $projectBuild->leftJoin('project_members', 'projects.project_id', '=', 'project_members.project_id')
            ->where(function ($query) use ($login_user_id){
                $query->where('projects.user_id', $login_user_id)
                    ->orWhere('project_members.user_id', $login_user_id);
            })
            ->where(function ($query) use ($search, $type){
                if (!empty($search)){
                    $query->where('projects.project_name', 'LIKE', trim($search) . '%');
                }
                if ($type > -1){
                    $query->where('projects.project_type', '=', $type);
                }
            })
            ->select(['projects.*', 'project_members.is_leader', 'project_members.role_power'])
            ->orderByDESC('projects.project_id')
            ->groupBy('projects.project_id')
            ->paginate($this->getLimit());

        // 设置权限参数
        foreach ($lists as $item){
            // 是否可以删除：仅创建人可删除
            $item->can_delete = false;
            // 是否可以编辑：1.创建人；2.读写权限的成员；3.项目管理员
            $item->can_update = false;
            // 是否可以查看
            $item->can_view = false;

            if ($item->user_id == $login_user_id){
                $item->can_delete = $item->can_update = $item->can_view = true;
            }else{
                // 管理员与读成员，则可编辑与查看
                if ($item->is_leader == 1 || in_array($item->role_power, [ProjectMember::ROLE_POWER_WRITE, ProjectMember::ROLE_POWER_CREATOR])){
                    $item->can_update = $item->can_view = true;
                }
                // 查看权限
                if (!$item->can_view && ($item->is_public == 1 || $item->role_power == ProjectMember::ROLE_POWER_READ)){
                    $item->can_view = true;
                }
            }
        }

        return $this->getPaginateFormat($lists);
    }

    protected function getProjectById($project_id, $check_auth = true, $throw_msg = '')
    {
        $project = Project::with(['userInfo'])->find($project_id);
        if (empty($project)){
            throw new BadRequestException('项目不存在或已删除！');
        }
        if ($check_auth && $project->user_id != getLoginUserId()){
            throw new ForbiddenException($throw_msg ? $throw_msg : ('您无权限查看项目`' . $project->project_name . '`！'));
        }
        return $project;
    }

    public function detail($project_id)
    {
        return $this->getProjectById($project_id);
    }

    public function createOrUpdate($request)
    {
        $login_user_id = getLoginUserId();
        $create = true;
        $project_id = $request->input('project_id', 0);
        if (!$project_id){
            $detail = new Project();
            $detail->user_id = $login_user_id;
        }else{
            $create = false;
            $detail = $this->getProjectById($project_id, false);
            if ($detail->user_id != $login_user_id){
                throw new ForbiddenException('您无权限编辑项目`' . $detail->project_name . '`！');
            }
        }

        DB::beginTransaction();
        try {
            $detail->project_name = $request->input('project_name');
            $detail->project_type = $request->input('project_type', Project::PROJECT_TYPE_PC);
            if ($request->has('project_description')){
                $detail->project_description = $request->input('project_description', '');
            }
            if ($request->has('project_version')){
                $detail->project_version = $request->input('project_version', '');
            }
            $detail->is_public = $request->input('is_public', 0);
            $detail->save();

            DB::commit();
        }catch (Exception $e){
            DB::rollBack();
            throw new BadRequestException('项目' . ($create ? '创建' : '更新') . '失败，请重试！');
        }

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_PROJECT, $create ? OperationLog::ACTION['CREATE'] : OperationLog::ACTION['UPDATE'], $detail);

        return $detail;
    }

    public function delete($project_id)
    {
        // 验证登录会员的项目权限
        $detail = $this->getProjectById($project_id, false);
        // 创建人才可删除项目
        if ($detail->user_id != getLoginUserId()){
            throw new ForbiddenException('您无权限删除项目`' . $detail->project_name . '`！');
        }

        $detail->delete();

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_PROJECT, OperationLog::ACTION['DELETE'], $detail);

        return $detail;
    }
}
