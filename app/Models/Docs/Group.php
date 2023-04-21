<?php

namespace App\Models\Docs;

use App\Models\Model;
use App\Models\UserInfo;

class Group extends Model
{
    protected $appends = ['time_formatting'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}
