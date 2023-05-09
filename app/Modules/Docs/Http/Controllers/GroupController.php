<?php

namespace App\Modules\Docs\Http\Controllers;

use App\Modules\Docs\Http\Requests\ApiIdRequest;
use App\Modules\Docs\Http\Requests\GroupBatchSaveRequest;
use App\Modules\Docs\Http\Requests\GroupIdRequest;
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

    public function batchSave(GroupBatchSaveRequest $request): JsonResponse
    {
        $this->service->batchSave($request->input('groups'));

        return $this->successJson([], '分组设置成功！');
    }

    public function setDefaultExpand(GroupIdRequest $request): JsonResponse
    {
        $detail = $this->service->setDefaultExpand($request);

        return $this->successJson([], '分组`' . $detail->group_name . '`默认节点设置成功！');
    }

    public function delete(GroupIdRequest $request): JsonResponse
    {
        $detail = $this->service->delete($request->input('group_id'));

        return $this->successJson([], '分组`' . $detail->group_name . '`删除成功！');
    }
}
