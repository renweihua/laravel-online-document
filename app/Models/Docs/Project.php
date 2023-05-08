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

    // 验证项目的访问权限：role_power 0.访问权限；1.编辑权限
    public static function checkRolePower($project, $role_power = ProjectMember::ROLE_POWER_READ)
    {
        $login_user_id = getLoginUserId();

        // 创建人
        if ($project->user_id == $login_user_id){
            return true;
        }
        if ($role_power == ProjectMember::ROLE_POWER_READ){ // 访问权限
            // 公开项目
            if ($project->is_public == 1){
                return true;
            }
            // 项目成员皆有访问权限
            if (ProjectMember::where('project_id', $project->project_id)->where('user_id', $login_user_id)->first()){
                return true;
            }
        }else{
            // 是否为项目成员
            $member = ProjectMember::where('project_id', $project->project_id)->where('user_id', $login_user_id)->first();
            if (!$member){
                return false;
            }
            // 项目管理员有编辑权限、项目成员有编辑权限
            if ($member->is_leader == 1 || $member->role_power == ProjectMember::ROLE_POWER_WRITE){
                return true;
            }
        }
        return false;
    }
}
