<?php

namespace App\Modules\Docs\Http\Controllers;

use App\Traits\Json;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DocsController extends Controller
{
    use Json;

    protected $service;

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('docs::index');
    }
}
