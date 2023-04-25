<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateProjectMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = 'project_members';
        if (Schema::hasTable($table)) return;
        Schema::create($table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->bigInteger('project_id')->unsigned()->default(0)->comment('项目Id');
            $table->bigInteger('user_id')->unsigned()->default(0)->comment('会员Id');
            $table->boolean('is_leader')->unsigned()->default(0)->comment('是否管理者：0.否；1.是');
            $table->boolean('role_power')->unsigned()->default(0)->comment('角色权限：0.读；1.可编辑；2.创建人');
            $table->string('alias_name', 200)->default('')->comment('别名');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->boolean('is_delete')->unsigned()->default(0)->comment('是否删除');
            $table->index('project_id');
            $table->index('user_id');
            $table->index('is_delete');
        });
        $table = get_db_prefix() . $table;
        // 设置表注释
        DB::statement("ALTER TABLE `{$table}` comment '项目成员表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_members');
    }
}
