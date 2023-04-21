<?php

namespace App\Models\Docs;

use App\Models\Model;
use App\Models\UserInfo;

class Doc extends Model
{
    protected $primaryKey = 'doc_id';
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
