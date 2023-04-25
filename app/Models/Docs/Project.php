<?php

namespace App\Models\Docs;

use App\Models\Model;
use App\Models\UserInfo;

class Project extends Model
{
    protected $primaryKey = 'project_id';

    protected $appends = ['project_type_text', 'time_formatting'];

    // 项目类型
    // PC端
    const PROJECT_TYPE_PC = 0;

    public function getProjectTypeTextAttribute($key)
    {
        $text = 'PC端';
        return $text;
    }

    public function userInfo()
    {
        return $this->belongsTo(UserInfo::class, 'user_id', 'user_id');
    }

    public static function getDetailById($id)
    {
        return self::find($id);
    }
}
