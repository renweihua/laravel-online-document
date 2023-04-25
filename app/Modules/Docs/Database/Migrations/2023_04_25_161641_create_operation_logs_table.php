<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateOperationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = 'operation_logs';
        if (Schema::hasTable($table)) return;
        Schema::create($table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->bigInteger('project_id')->unsigned()->default(0)->comment('项目Id');
            $table->bigInteger('user_id')->unsigned()->default(0)->comment('操作会员Id');
            $table->boolean('log_type')->unsigned()->default(0)->comment('日志类型：0.项目；1.分组；2.API；3.文档；4.字段映射；5.成员');
            $table->string('action', 200)->default('')->comment('动作');
            $table->string('content', 500)->default('')->comment('操作内容');
            $table->bigInteger('relation_id')->unsigned()->default(0)->comment('关联Id');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->boolean('is_delete')->unsigned()->default(0)->comment('是否删除');
            $table->index('project_id');
            $table->index('user_id');
            $table->index('log_type');
            $table->index('relation_id');
            $table->index('is_delete');
        });
        $table = get_db_prefix() . $table;
        // 设置表注释
        DB::statement("ALTER TABLE `{$table}` comment '操作日志表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operation_logs');
    }
}
