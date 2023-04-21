<?php

namespace App\Modules\Docs\Http\Controllers;

use App\Modules\Docs\Http\Requests\GroupRequest;
use App\Modules\Docs\Services\GroupService;
use Illuminate\Http\JsonResponse;

class GroupController extends DocsController
{
    public function __construct(GroupService $groupService)
    {
        $this->service = $groupService;
    }

    public function index(): JsonResponse
    {
        $lists = $this->service->index();
        return $this->successJson($lists);
    }

    public function createOrUpdate(GroupRequest $request): JsonResponse
    {
        $group = $this->service->createOrUpdate($request);

        return $this->successJson($group, '分组`' . $group->group_name . '`保存成功！');
    }
}
