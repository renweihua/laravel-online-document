<?php

namespace App\Modules\Docs\Http\Controllers;

use App\Modules\Docs\Http\Requests\FieldMappingIdRequest;
use App\Modules\Docs\Http\Requests\FieldMappingRequest;
use App\Modules\Docs\Services\FieldMappingService;
use Illuminate\Http\JsonResponse;

// 字段映射
class FieldMappingController extends DocsController
{
    public function __construct(FieldMappingService $fieldMappingService)
    {
        $this->service = $fieldMappingService;
    }

    public function index(): JsonResponse
    {
        $lists = $this->service->index();
        return $this->successJson($lists);
    }

    public function detail(FieldMappingIdRequest $request): JsonResponse
    {
        $lists = $this->service->detail($request->input('project_id'));
        return $this->successJson($lists);
    }

    public function createOrUpdate(FieldMappingRequest $request): JsonResponse
    {
        $detail = $this->service->createOrUpdate($request);

        return $this->successJson($detail, '字段映射`' . $detail->field_name . '`保存成功！');
    }
}
