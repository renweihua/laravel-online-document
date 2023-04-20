<?php

namespace App\Modules\Docs\Http\Controllers;

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
}
