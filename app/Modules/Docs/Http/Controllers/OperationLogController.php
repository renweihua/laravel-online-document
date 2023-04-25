<?php

namespace App\Modules\Docs\Http\Controllers;

// 操作日志
use App\Modules\Docs\Services\OperationLogService;
use Illuminate\Http\JsonResponse;

class OperationLogController extends DocsController
{
    public function __construct(OperationLogService $operationLogService)
    {
        $this->service = $operationLogService;
    }

    public function index(): JsonResponse
    {
        $lists = $this->service->index();
        return $this->successJson($lists);
    }
}
