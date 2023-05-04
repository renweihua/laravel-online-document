<?php

namespace App\Models\Docs;

use App\Models\Model;
use App\Models\User\UserInfo;

class FieldMapping extends Model
{
    protected $is_delete = 0; // 是否开启删除（0.假删除；1.开启删除，就是直接删除；）

    protected $appends = ['time_formatting'];

    public function userInfo()
    {
        return $this->belongsTo(UserInfo::class, 'user_id', 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}
