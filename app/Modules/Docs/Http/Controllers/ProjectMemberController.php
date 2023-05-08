<?php

namespace App\Modules\Docs\Http\Controllers;

use App\Modules\Docs\Http\Requests\ProjectMemberRequest;
use App\Modules\Docs\Http\Requests\ProjectMemberSetLeaderRequest;
use App\Modules\Docs\Http\Requests\ProjectMemberSetRolePowerRequest;
use App\Modules\Docs\Services\ProjectMemberService;
use Illuminate\Http\JsonResponse;

class ProjectMemberController extends DocsController
{
    public function __construct(ProjectMemberService $projectUserService)
    {
        $this->service = $projectUserService;
    }

    public function index(): JsonResponse
    {
        $lists = $this->service->index();
        return $this->successJson($lists);
    }

    public function createOrUpdate(ProjectMemberRequest $request): JsonResponse
    {
        $detail = $this->service->createOrUpdate($request);

        return $this->successJson($detail, '项目成员`' . $detail->userInfo->nick_name . '`保存成功！');
    }

    public function setRolePower(ProjectMemberSetRolePowerRequest $request): JsonResponse
    {
        $detail = $this->service->setRolePower($request);

        return $this->successJson($detail, '项目成员`' . $detail->userInfo->nick_name . '`权限设置成功！');
    }

    public function setLeader(ProjectMemberSetLeaderRequest $request): JsonResponse
    {
        $detail = $this->service->setLeader($request);

        return $this->successJson($detail, '项目成员`' . $detail->userInfo->nick_name . '`管理员权限设置成功！');
    }

    public function delete(ProjectMemberRequest $request): JsonResponse
    {
        $detail = $this->service->delete($request);

        return $this->successJson([], '项目成员`' . $detail->userInfo->nick_name . '`删除成功！');
    }
}
