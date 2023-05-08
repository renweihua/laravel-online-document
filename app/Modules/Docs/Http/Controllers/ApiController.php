<?php

namespace App\Modules\Docs\Http\Controllers;

use App\Modules\Docs\Http\Requests\ApiIdRequest;
use App\Modules\Docs\Http\Requests\ApiRequest;
use App\Modules\Docs\Services\ApiService;
use Illuminate\Http\JsonResponse;

class ApiController extends DocsController
{
    public function __construct(ApiService $apiService)
    {
        $this->service = $apiService;
    }

    public function index(): JsonResponse
    {
        $lists = $this->service->index();
        return $this->successJson($lists);
    }

    public function detail(ApiIdRequest $request): JsonResponse
    {
        $lists = $this->service->detail($request->input('api_id'));
        return $this->successJson($lists);
    }

    public function createOrUpdate(ApiRequest $request): JsonResponse
    {
        $detail = $this->service->createOrUpdate($request);

        return $this->successJson($detail, 'API`' . $detail->api_name . '`保存成功！');
    }

    public function delete(ApiIdRequest $request): JsonResponse
    {
        $detail = $this->service->delete($request);

        return $this->successJson([], 'API`' . $detail->api_name . '`删除成功！');
    }
}
