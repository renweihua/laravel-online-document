<?php

namespace App\Models\Docs;

use App\Models\Model;
use App\Models\UserInfo;

class FieldMapping extends Model
{
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
