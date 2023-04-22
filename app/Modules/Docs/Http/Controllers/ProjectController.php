<?php

namespace App\Modules\Docs\Http\Controllers;

use App\Modules\Docs\Http\Requests\ProjectIdRequest;
use App\Modules\Docs\Http\Requests\ProjectRequest;
use App\Modules\Docs\Services\ProjectService;
use Illuminate\Http\JsonResponse;

class ProjectController extends DocsController
{
    public function __construct(ProjectService $projectService)
    {
        $this->service = $projectService;
    }

    public function index(): JsonResponse
    {
        $lists = $this->service->index();
        return $this->successJson($lists);
    }

    public function detail(ProjectIdRequest $request): JsonResponse
    {
        $lists = $this->service->detail($request->input('project_id'));
        return $this->successJson($lists);
    }

    public function createOrUpdate(ProjectRequest $request): JsonResponse
    {
        $detail = $this->service->createOrUpdate($request);

        return $this->successJson($detail, '项目`' . $detail->project_name . '`保存成功！');
    }
}
