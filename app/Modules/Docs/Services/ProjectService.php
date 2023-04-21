<?php

namespace App\Modules\Docs\Services;

use App\Exceptions\HttpStatus\BadRequestException;
use App\Exceptions\HttpStatus\ForbiddenException;
use App\Models\Docs\Project;
use App\Services\Service;

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
}
