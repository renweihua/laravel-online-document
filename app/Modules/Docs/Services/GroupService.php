<?php

namespace App\Modules\Docs\Services;

use App\Exceptions\Exception;
use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\Docs\Group;
use App\Models\Docs\OperationLog;
use App\Models\Docs\Project;
use App\Models\Docs\ProjectMember;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class GroupService extends Service
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
        Project::checkRolePowerThrow($project);

        $parent_id = $request->input('parent_id', 0);
        $group_type = $request->input('group_type', Group::GROUP_TYPE_API);
        $search = $request->input('search', '');
        $docBuild = Group::getInstance();
        $lists = $docBuild
            ->where('project_id', $project_id)
            // 分组类型
            ->where('group_type', $group_type)
            // 后期：管理员都可访问~
            // 还会存在对外可访问的文档
            ->where('user_id', $login_user_id)
            ->where(function ($query) use ($search, $parent_id){
                if (!empty($search)){
                    $query->where('group_name', 'LIKE', trim($search) . '%');
                }
                if ($parent_id > 0){
                    $query->where('parent_id', '=', $parent_id);
                }
            })
            ->orderBy('sort')
            ->get();

        // Tree结构
        $lists = list_to_tree($lists->toArray(), 'group_id');

        return $lists;
    }

    protected function getGroupById($doc_id, $role_power = ProjectMember::ROLE_POWER_READ)
    {
        $detail = Group::with(['project', 'userInfo'])->find($doc_id);
        if (empty($detail)){
            throw new BadRequestException('分组不存在或已删除！');
        }
        // 验证访问权限
        $throw_msg = '您无权限查看分组`' . $detail->group_name . '`！';
        switch ($role_power){
            case ProjectMember::ROLE_POWER_WRITE:
                $throw_msg = '您无权限编辑分组`' . $detail->group_name . '`！';
                break;
            case ProjectMember::ROLE_POWER_DELETE_PROJECT_CHILDS:
                $throw_msg = '您无权限删除分组`' . $detail->group_name . '`！';
                break;
        }
        Project::checkRolePowerThrow($detail->project, $role_power, $throw_msg);

        return $detail;
    }

    public function detail($group_id)
    {
        return $this->getGroupById($group_id);
    }

    public function createOrUpdate($request)
    {
        $create = true;
        $group_id = $request->input('group_id', 0);
        if (!$group_id){
            $project_id = $request->input('project_id');
            $project = Project::getDetailById($project_id);
            if (!$project){
                throw new BadRequestException('项目不存在或已删除！');
            }
            // 验证新增编辑权限
            Project::checkRolePowerThrow($project, ProjectMember::ROLE_POWER_WRITE);

            $detail = new Group();
            $detail->user_id = getLoginUserId();
            $detail->project_id = $project->project_id;
            $detail->group_type = $request->input('group_type');
        }else{
            $create = false;
            $detail = $this->getGroupById($group_id, ProjectMember::ROLE_POWER_WRITE);
        }

        DB::beginTransaction();
        try {
            $detail->group_name = $request->input('group_name');
            if ($request->has('parent_id')){
                $detail->parent_id = $request->input('parent_id');
            }
            if ($request->has('sort')){
                $detail->sort = $request->input('sort');
            }
            if ($request->has('default_expand')){
                $detail->default_expand = $request->input('default_expand');
            }
            $detail->save();

            DB::commit();
        }catch (Exception $e){
            DB::rollBack();
            throw new BadRequestException('分组' . ($create ? '创建' : '更新') . '失败，请重试！');
        }

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_GROUP, $create ? OperationLog::ACTION['CREATE'] : OperationLog::ACTION['UPDATE'], $detail);

        return $detail;
    }

    public function batchSave($param_groups)
    {
        $group_ids = array_column($param_groups, 'group_id');
        $param_groups = array_column($param_groups, null, 'group_id');
        $groups = Group::findMany($group_ids);
        foreach ($groups as $group){
            if (!isset($param_groups[$group->group_id])){
                continue;
            }
            $param_group =  $param_groups[$group->group_id];
            $group->parent_id = $param_group['parent_id'];
            $group->sort = $param_group['sort'];
            $group->save();
        }
        return true;
    }

    public function delete($group_id)
    {
        // 验证登录会员的权限
        $detail = $this->getGroupById($group_id, ProjectMember::ROLE_POWER_DELETE_PROJECT_CHILDS);

        $detail->delete();

        // 记录操作日志
        OperationLog::createLog(OperationLog::LOG_TYPE_GROUP, OperationLog::ACTION['DELETE'], $detail);

        return $detail;
    }
}
