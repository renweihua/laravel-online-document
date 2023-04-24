<?php

namespace App\Modules\Docs\Http\Controllers;

use App\Modules\Docs\Http\Requests\GroupRequest;
use App\Modules\Docs\Services\GroupService;
use Illuminate\Http\JsonResponse;

// 通用属性配置
class PropertyController extends DocsController
{
    public function index(): JsonResponse
    {
        $lists = [
            // 请求方式
            'http_method' => [
                'GET',
                'POST',
                'PUT',
                'PATCH',
                'DELETE',
                'OPTIONS',
                'HEAD',
                'CONNECT',
            ],
            // 请求协议
            'http_protocol' => [
                'HTTP',
                'IP',
                'UDP',
                'TCP',
            ],
            // 字段类型
            'filed_type' => [
                'String',
                'Int',
                'Text',
                'Array',
                'Object',
                'Double',
                'Float',
                'Boolean',
                'Resource',
                'File',
                'Null',
            ],
        ];
        return $this->successJson($lists);
    }
}
