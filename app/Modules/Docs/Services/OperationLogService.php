<?php

namespace App\Modules\Docs\Services;

use App\Models\Docs\OperationLog;
use App\Services\Service;

class OperationLogService extends Service
{
    public function index()
    {
        $login_user_id = getLoginUserId();
        $request = request();
        $project_id = $request->input('project_id');
        if (empty($project_id)) {
            return $this->getPaginateFormat([]);
        }
        $log_type = $request->input('log_type', 0);

        $search = $request->input('search', '');
        $build = OperationLog::with('userInfo');
        $lists = $build
            ->where('project_id', '=', $project_id)
            // 日志类型
            ->where('log_type', '=', $log_type)
            ->where(function ($query) use ($search){
                if (!empty($search)){
                    $query->where('api_name', 'LIKE', trim($search) . '%');
                }
            })
            ->orderByDESC('id')
            ->paginate($this->getLimit());

        return $this->getPaginateFormat($lists);
    }
}
