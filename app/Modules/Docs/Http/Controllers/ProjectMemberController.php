<?php

namespace App\Modules\Docs\Http\Controllers;

use App\Modules\Docs\Http\Requests\ProjectMemberRequest;
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
}
