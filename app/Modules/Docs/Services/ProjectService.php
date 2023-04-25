<?php

namespace App\Modules\Docs\Services;

use App\Exceptions\Exception;
use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\Docs\OperationLog;
use App\Models\Docs\Project;
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

    protected function getProjectById($project_id, $with = [], $check_auth = true)
    {
        $project = Project::with(array_merge(['userInfo'], $with))->find($project_id);
        if (empty($project)){
            throw new BadRequestException('项目不存在或已删除！');
        }
        if ($check_auth && $project->user_id != getLoginUserId()){
            throw new ForbiddenException('您无权限查看项目`' . $project->project_name . '`！');
        }
        return $project;
    }

    public function detail($project_id)
    {
        return $this->getProjectById($project_id);
    }

    public function createOrUpdate($request)
    {
        $create = true;
        $project_id = $request->input('project_id', 0);
        if (!$project_id){
            $detail = new Project();
            $detail->user_id = getLoginUserId();
        }else{
            $create = false;
            $detail = $this->getProjectById($project_id);
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

            // 记录操作日志
            OperationLog::createLog(OperationLog::LOG_TYPE_PROJECT, $create ? OperationLog::ACTION['CREATE'] : OperationLog::ACTION['UPDATE'], $detail);

            return $detail;
        }catch (Exception $e){
            DB::rollBack();
            throw new BadRequestException('项目更新失败，请重试！');
        }
    }
}
