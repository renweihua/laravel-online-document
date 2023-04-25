<?php

namespace App\Modules\Docs\Http\Controllers;

use App\Models\User;
use App\Modules\Docs\Http\Requests\GroupRequest;
use App\Modules\Docs\Services\GroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// 通用属性配置
class PropertyController extends DocsController
{
    public function index(): JsonResponse
    {
        $lists = [
            // 请求方式
            'http_method' => [
                [
                    'label' => '常用',
                    'options' => [
                        'GET',
                        'POST',
                        'PUT',
                        'PATCH',
                        'DELETE',
                    ],
                ],
                [
                    'label' => '其他',
                    'options' => [
                        'OPTIONS',
                        'HEAD',
                        'CONNECT',
                    ],
                ],
            ],
            // 请求协议
            'http_protocol' => [
                'HTTP',
                'IP',
                'UDP',
                'TCP',
            ],
            // 字段类型
            'field_type' => [
                [
                    'label' => '常用',
                    'options' => [
                        'string',
                        'integer',
                        'number',
                        'text',
                        'array',
                        'object',
                        'boolean',
                        'file',
                        'date',
                        'float',
                    ]
                ],
                [
                    'label' => '常规',
                    'options' => [
                        'double',
                        'resource',
                        'dateTime',
                        'timeStamp',
                        'null',
                    ]
                ]
            ],
        ];
        return $this->successJson($lists);
    }

    public function users(Request $request): JsonResponse
    {
        $search = $request->input('search');
        if (empty($search)){
            return $this->errorJson('请输入你要检索的会员相关 账户/手机号/邮箱！');
        }
        $users = User::where('user_name', $search)
            ->orWhere('user_mobile', $search)
            ->orWhere('user_email', $search)
            ->with('userInfo')
            ->get();

        return $this->successJson($users);
    }
}
