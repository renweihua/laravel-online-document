<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateApisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = 'apis';
        if (Schema::hasTable($table)) return;
        Schema::create($table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('api_id');
            $table->bigInteger('user_id')->unsigned()->default(0)->comment('会员Id');
            $table->bigInteger('project_id')->unsigned()->default(0)->comment('项目Id');
            $table->bigInteger('group_id')->unsigned()->default(0)->comment('分组Id');
            $table->string('api_url', 200)->default('')->comment('URL');
            $table->string('api_name', 100)->default('')->comment('API名称');
            $table->string('api_description', 200)->default('')->comment('API描述');
            $table->string('http_protocol', 200)->default('')->comment('协议');
            $table->string('develop_language', 200)->default('')->comment('开发语言');
            $table->string('http_method', 200)->default('')->comment('Http请求方式');
            $table->json('http_header')->nullable()->comment('请求头部信息');
            $table->json('http_params')->nullable()->comment('请求参数');
            $table->unsignedInteger('http_status')->unsigned()->default(0)->comment('http请求状态');
            $table->json('response_sample')->nullable()->comment('响应数据样例');
            $table->json('response_params')->nullable()->comment('请求响应参数');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->boolean('is_delete')->unsigned()->default(0)->comment('是否删除');
            $table->index('api_name');
            $table->index('user_id');
            $table->index(['project_id', 'group_id']);
            $table->index('is_delete');
        });
        $table = get_db_prefix() . $table;
        // 设置表注释
        DB::statement("ALTER TABLE `{$table}` comment 'API接口表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apis');
    }
}
