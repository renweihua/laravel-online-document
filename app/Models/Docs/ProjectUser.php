<?php

namespace App\Models\Docs;

use App\Models\Model;
use App\Models\UserInfo;

class ProjectUser extends Model
{
    protected $appends = ['time_formatting'];

    /**
     * 角色权限
     */
    // 读权限
    const ROLE_POWER_READ = 0;
    // 写权限
    const ROLE_POWER_WRITE = 1;
    // 创建人
    const ROLE_POWER_CREATOR = 2;

    public function userInfo()
    {
        return $this->belongsTo(UserInfo::class, 'user_id', 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}
