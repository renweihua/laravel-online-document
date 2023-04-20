<?php

namespace App\Models\Docs;

use App\Models\Model;

class Project extends Model
{
    protected $primaryKey = 'project_id';

    protected $appends = ['project_type_text', 'time_formatting'];

    public function getProjectTypeTextAttribute($key)
    {
        $text = 'PC端';
        return $text;
    }

    // 时间戳格式化
    public function getTimeFormattingAttribute($value)
    {
        if(!isset($this->attributes['created_time'])) return '';
        return formatting_timestamp($this->attributes['created_time'], false);
    }
}
