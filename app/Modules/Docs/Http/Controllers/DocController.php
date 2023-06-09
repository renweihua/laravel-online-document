<?php

namespace App\Modules\Docs\Http\Controllers;

use App\Modules\Docs\Http\Requests\DocIdRequest;
use App\Modules\Docs\Http\Requests\DocRequest;
use App\Modules\Docs\Http\Requests\DocTopRequest;
use App\Modules\Docs\Http\Requests\ProjectIdRequest;
use App\Modules\Docs\Services\DocService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocController extends DocsController
{
    public function __construct(DocService $docService)
    {
        $this->service = $docService;
    }

    public function index(): JsonResponse
    {
        $lists = $this->service->index();
        return $this->successJson($lists);
    }

    public function detail(DocIdRequest $request): JsonResponse
    {
        $lists = $this->service->detail($request->input('doc_id'));
        return $this->successJson($lists);
    }

    public function createOrUpdate(DocRequest $request): JsonResponse
    {
        $doc = $this->service->createOrUpdate($request);

        return $this->successJson($doc, '文档`' . $doc->doc_name . '`保存成功！');
    }

    public function setTop(DocTopRequest $request): JsonResponse
    {
        $doc = $this->service->setTop($request->input('doc_id'), $request->input('is_top'));

        return $this->successJson($doc, '文档`' . $doc->doc_name . '`设置成功！');
    }

    public function delete(DocIdRequest $request): JsonResponse
    {
        $doc = $this->service->delete($request->input('doc_id'));

        return $this->successJson([], '文档`' . $doc->doc_name . '`删除成功！');
    }
}
