<?php

namespace App\Modules\Docs\Services;

use App\Models\Docs\Project;
use App\Services\Service;

class ProjectService extends Service
{
    public function index()
    {
        // $login_user_id = getLoginUserId();
        $request = request();
        $type = $request->input('type', -1);
        $search = $request->input('search', '');
        $projectBuild = Project::getInstance();
        if (empty($projectBuild)) {
            return $this->getPaginateFormat([]);
        }
        // ->where('user_id', $login_user_id)
        $lists = $projectBuild
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
}
