<?php

namespace App\Modules\Docs\Services;

use App\Exceptions\Exception;
use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\Docs\OperationLog;
use App\Models\Docs\Project;
use App\Models\Docs\ProjectMember;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectMemberService extends Service
{
    public function index()
    {
        $login_user_id = getLoginUserId();
        $request = request();
        $type = $request->input('type', -1);
        $search = $request->input('search', '');
        $projectBuild = Project::getInstance();
        $lists = $projectBuild
            ->where('user_id', $login_user_id)
            ->where(function ($query) use ($search, $type){
                if (!empty($search)){
                    $query->where('project_name', 'LIKE', trim($search) . '%');
                }
                if ($type > -1){
                    $query->where('project_type', '=', $type);
                }
            })
            ->orderByDESC('project_id')
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
        $project = Project::getDetailById($project_id);
        if ($project->user_id != $login_user_id){
            throw new ForbiddenException('您无权设置项目成员！');
        }

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
}
