<?php

namespace App\Modules\Docs\Http\Controllers;

use App\Modules\Docs\Http\Requests\ApiIdRequest;
use App\Modules\Docs\Http\Requests\DocIdRequest;
use App\Modules\Docs\Http\Requests\DocRequest;
use App\Modules\Docs\Http\Requests\ProjectIdRequest;
use App\Modules\Docs\Services\ApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function createOrUpdate(DocRequest $request): JsonResponse
    {
        $doc = $this->service->createOrUpdate($request);

        return $this->successJson($doc, '文档`' . $doc->doc_name . '`保存成功！');
    }
}
