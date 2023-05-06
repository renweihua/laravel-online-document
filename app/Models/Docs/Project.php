<?php

namespace App\Models\Docs;

use App\Models\Model;
use App\Models\User\UserInfo;

class Project extends Model
{
    protected $primaryKey = 'project_id';

    protected $is_delete = 0; // 是否开启删除（0.假删除；1.开启删除，就是直接删除；）
    protected $appends = ['project_type_text', 'time_formatting'];

    // 项目类型
    // PC端
    const PROJECT_TYPE_PC = 0;

    public function getProjectTypeTextAttribute($key)
    {
        $text = 'PC端';
        if(!isset($this->attributes['project_type'])) return $text;
        switch ($this->attributes['project_type']){
            case 1:
                $text = 'Web移动端';
                break;
        }
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

    // 验证访问权限
    public static function checkPower($project)
    {
        $login_user_id = getLoginUserId();
        // 非创建人
        if ($project->user_id != $login_user_id){
            // 是否为项目成员
            if (!ProjectMember::where('project_id', $project->project_id)->where('user_id', $login_user_id)->first()){
                return false;
            }
        }
        return true;
    }
}
