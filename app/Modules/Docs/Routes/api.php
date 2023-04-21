<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CorsMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('')->middleware([
    CorsMiddleware::class,
])->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        // 登录
        Route::match(['get', 'post'], 'login', 'AuthController@login');
        // // 登录会员信息
        // Route::match(['get', 'post'], 'me', 'AuthController@me')->middleware(CheckAuth::class);
        // // 退出登录
        // Route::post('logout', 'AuthController@logout');
    });

    Route::prefix('')->group(function () {
        // 项目列表
        Route::get('projects', 'ProjectController@index');
        Route::get('project-detail', 'ProjectController@detail');


        // 文档列表
        Route::get('docs', 'DocController@index');
        Route::get('doc-detail', 'DocController@detail');
        Route::post('doc-create', 'DocController@createOrUpdate');
        Route::put('doc-update', 'DocController@createOrUpdate');
    });
});
