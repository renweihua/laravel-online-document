<?php

namespace App\Models\Docs;

use App\Models\Model;
use App\Models\UserInfo;

class Project extends Model
{
    protected $primaryKey = 'project_id';

    protected $appends = ['project_type_text', 'time_formatting'];

    public function getProjectTypeTextAttribute($key)
    {
        $text = 'PC端';
        return $text;
    }

    public function userInfo()
    {
        return $this->belongsTo(UserInfo::class, 'user_id', 'user_id');
    }
}
