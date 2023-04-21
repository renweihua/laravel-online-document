<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = 'docs';
        if (Schema::hasTable($table)) return;
        Schema::create($table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('doc_id');
            $table->bigInteger('user_id')->unsigned()->default(0)->comment('会员Id');
            $table->bigInteger('project_id')->unsigned()->default(0)->comment('项目Id');
            $table->bigInteger('group_id')->unsigned()->default(0)->comment('分组Id');
            $table->string('doc_name', 100)->default('')->comment('文档名称');
            $table->longtext('content_html')->nullable()->comment('内容');
            $table->longtext('content_markdown')->nullable()->comment('md内容');
            $table->integer('sort')->unsigned()->default(100)->comment('排序：升序');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->boolean('is_delete')->unsigned()->default(0)->comment('是否删除');
            $table->integer('view_count')->unsigned()->default(0)->comment('浏览量');
            $table->index('doc_name');
            $table->index('user_id');
            $table->index(['project_id', 'group_id']);
            $table->index('sort');
            $table->index('is_delete');
        });
        $table = get_db_prefix() . $table;
        // 设置表注释
        Db::statement("ALTER TABLE `{$table}` comment '文档表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('docs');
    }
}
