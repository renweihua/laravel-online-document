<?php

namespace App\Models\Docs;

use App\Models\Model;
use App\Models\User\User;
use App\Models\User\UserInfo;

class ProjectMember extends Model
{
    protected $is_delete = 0; // 是否开启删除（0.假删除；1.开启删除，就是直接删除；）

    protected $appends = ['time_formatting', 'role_power_text'];

    /**
     * 角色权限
     */
    // 读权限
    const ROLE_POWER_READ = 0;
    // 写权限
    const ROLE_POWER_WRITE = 1;
    // 创建人
    const ROLE_POWER_CREATOR = 2;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function userInfo()
    {
        return $this->belongsTo(UserInfo::class, 'user_id', 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function getRolePowerTextAttribute($key)
    {
        $text = '只读';
        if (!isset($this->attributes['role_power'])){
            return $text;
        }
        switch ($this->attributes['role_power']){
            case self::ROLE_POWER_WRITE:
                $text = '读写';
        }
        return $text;
    }
}
