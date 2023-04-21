<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = 'groups';
        if (Schema::hasTable($table)) return;
        Schema::create($table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->bigInteger('project_id')->unsigned()->default(0)->comment('项目Id');
            $table->boolean('group_type')->unsigned()->default(0)->comment('分组类型：0.文档');
            $table->string('group_name', 100)->default('')->comment('分组名称');
            $table->integer('sort')->unsigned()->default(100)->comment('排序：升序');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->boolean('is_delete')->unsigned()->default(0)->comment('是否删除');
            $table->index('group_name');
            $table->index(['project_id', 'group_type']);
            $table->index('sort');
            $table->index('is_delete');
        });
        $table = get_db_prefix() . $table;
        // 设置表注释
        Db::statement("ALTER TABLE `{$table}` comment '分组表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
