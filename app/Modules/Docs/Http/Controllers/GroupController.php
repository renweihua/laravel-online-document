<?php

namespace App\Modules\Docs\Http\Controllers;

use App\Modules\Docs\Http\Requests\GroupBatchSaveRequest;
use App\Modules\Docs\Http\Requests\GroupRequest;
use App\Modules\Docs\Services\GroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function batchSave(GroupBatchSaveRequest $request): JsonResponse
    {
        $this->service->batchSave($request->input('groups'));

        return $this->successJson([], '分组设置成功！');
    }
}
