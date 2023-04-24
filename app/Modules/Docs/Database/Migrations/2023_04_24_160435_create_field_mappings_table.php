<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = 'field_mappings';
        if (Schema::hasTable($table)) return;
        Schema::create($table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->bigInteger('project_id')->unsigned()->default(0)->comment('项目Id');
            $table->bigInteger('user_id')->unsigned()->default(0)->comment('会员Id');
            $table->string('field_name', 100)->default('')->comment('字段名称');
            $table->string('field_type', 100)->default('')->comment('字段类型');
            $table->string('field_description', 200)->default('')->comment('字段描述');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->boolean('is_delete')->unsigned()->default(0)->comment('是否删除');
            $table->index('project_id');
            $table->index('user_id');
            $table->index('field_name');
            $table->index('is_delete');
        });
        $table = get_db_prefix() . $table;
        // 设置表注释
        Db::statement("ALTER TABLE `{$table}` comment '字段映射表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('field_mappings');
    }
}
