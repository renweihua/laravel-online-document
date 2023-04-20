<?php

namespace App\Modules\Docs\Http\Controllers;

use App\Modules\Docs\Http\Requests\LoginRequest;
use App\Modules\Docs\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends DocsController
{
    public function __construct(AuthService $authService)
    {
        $this->service = $authService;
    }

    /**
     * 登录
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        // 登录流程
        $user = $this->service->login($data);

        return $this->successJson($user, '登录成功！');
    }

    /**
     * 获取登录会员信息
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Bbs\AuthTokenException
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successJson($this->service->me($request), '会员信息获取成功！');
    }

    /**
     * 退出登录
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->service->logout($request->header('Authorization'));
        return $this->successJson([], '退出成功！');
    }
}
