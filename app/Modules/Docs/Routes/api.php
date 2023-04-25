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
        // 通用协议定义
        Route::get('properties', 'PropertyController@index');
        // 搜索会员
        Route::get('search-users', 'PropertyController@users');
        // 操作日志列表
        Route::get('operation-logs', 'OperationLogController@index');

        // 项目管理
        Route::get('projects', 'ProjectController@index');
        Route::get('project-detail', 'ProjectController@detail');
        Route::post('project-create', 'ProjectController@createOrUpdate');
        Route::put('project-update', 'ProjectController@createOrUpdate');

        // 分组管理
        Route::get('groups', 'GroupController@index');
        Route::get('group-detail', 'GroupController@detail');
        Route::post('group-create', 'GroupController@createOrUpdate');
        Route::put('group-update', 'GroupController@createOrUpdate');

        // API管理
        Route::get('apis', 'ApiController@index');
        Route::get('api-detail', 'ApiController@detail');
        Route::post('api-create', 'ApiController@createOrUpdate');
        Route::put('api-update', 'ApiController@createOrUpdate');

        // 文档管理
        Route::get('docs', 'DocController@index');
        Route::get('doc-detail', 'DocController@detail');
        Route::post('doc-create', 'DocController@createOrUpdate');
        Route::put('doc-update', 'DocController@createOrUpdate');

        // 字段映射管理
        Route::get('field-mappings', 'FieldMappingController@index');
        Route::get('field-mapping/detail', 'FieldMappingController@detail');
        Route::post('field-mapping/create', 'FieldMappingController@createOrUpdate');
        Route::put('field-mapping/update', 'FieldMappingController@createOrUpdate');
        Route::delete('field-mapping/delete', 'FieldMappingController@delete');

        // 项目成员管理
        Route::get('project-members', 'ProjectMemberController@index');
        Route::get('project-member/detail', 'ProjectMemberController@detail');
        Route::post('project-member/create', 'ProjectMemberController@createOrUpdate');
        Route::put('project-member/update', 'ProjectMemberController@createOrUpdate');
        Route::delete('project-member/delete', 'ProjectMemberController@delete');
    });
});
