<?php

namespace App\Models\Docs;

use App\Models\Model;
use App\Models\UserInfo;

class Api extends Model
{
    protected $primaryKey = 'api_id';
    protected $appends = ['time_formatting'];
    protected $casts = [
        'http_header' => 'array',
        'http_params' => 'array',
        'response_sample' => 'array',
        'response_params' => 'array',
    ];

    public function userInfo()
    {
        return $this->belongsTo(UserInfo::class, 'user_id', 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}