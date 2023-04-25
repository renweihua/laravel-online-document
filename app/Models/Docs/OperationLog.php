<?php

namespace App\Models\Docs;

use App\Exceptions\HttpStatus\ServerErrorException;
use App\Models\Model;
use App\Models\UserInfo;

class OperationLog extends Model
{
    protected $appends = ['time_formatting'];

    const ACTION = [
        'CREATE' => 'CREATE',
        'UPDATE' => 'UPDATE',
        'DELETE' => 'DELETE',
        'query' => ['query', '查询'],
        'restore' => ['restore', '恢复'],
    ];
    const ACTION_SHOW = [
        'CREATE' => '创建',
        'UPDATE' => '编辑',
        'DELETE' => '删除',
        'query' => ['query', '查询'],
        'restore' => ['restore', '恢复'],
    ];

    /**
     * 日志类型
     */
    // 项目
    const LOG_TYPE_PROJECT = 0;
    // 分组
    const LOG_TYPE_GROUP = 1;
    // API
    const LOG_TYPE_API = 2;
    // 文档
    const LOG_TYPE_DOC = 3;
    // 字段映射
    const LOG_TYPE_FIELD_MAPPING = 4;
    // 项目成员
    const LOG_TYPE_PROJECT_MEMBER = 5;

    public function userInfo()
    {
        return $this->belongsTo(UserInfo::class, 'user_id', 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public static function createLog($log_type, $action, $detail)
    {
        $log = new self;
        $log->log_type = $log_type;
        $log->action = $action;
        $show_action = self::ACTION_SHOW[$action] ?? '';
        $content = empty($show_action) ? '' : ($show_action . '-');
        switch ($log->log_type){
            case self::LOG_TYPE_PROJECT: // 项目
                $log->project_id = $detail->project_id;
                $log->relation_id = $detail->project_id;
                $log->user_id = $detail->user_id;
                $content .= '项目:`' . $detail->project_name . '`';
                break;
            case self::LOG_TYPE_GROUP: // 分组
                $log->project_id = $detail->project_id;
                $log->relation_id = $detail->group_id;
                $log->user_id = $detail->user_id;
                $content .= '分组:`' . $detail->group_name . '`';
                break;
            case self::LOG_TYPE_API: // API接口
                $log->project_id = $detail->project_id;
                $log->relation_id = $detail->api_id;
                $log->user_id = $detail->user_id;
                $content .= 'API:`' . $detail->api_name . '`';
                break;
            case self::LOG_TYPE_DOC: // 文档
                $log->project_id = $detail->project_id;
                $log->relation_id = $detail->doc_id;
                $log->user_id = $detail->user_id;
                $content .= '文档:`' . $detail->doc_name . '`';
                break;
            case self::LOG_TYPE_FIELD_MAPPING: // 字段映射
                $log->project_id = $detail->project_id;
                $log->relation_id = $detail->id;
                $log->user_id = $detail->user_id;
                $content .= '字段映射:`' . $detail->field_name . '`';
                break;
            case self::LOG_TYPE_PROJECT_MEMBER: // 项目成员
                $log->project_id = $detail->project_id;
                $log->relation_id = $detail->id;
                $log->user_id = $detail->user_id;
                $content .= '成员:`' . $detail->userInfo->nick_name . '`';
                break;
            default:
                throw new ServerErrorException('未处理的日志类型：' . $log->log_type);
                break;
        }
        $log->content = $content;
        $log->save();

        return $log;
    }
}
