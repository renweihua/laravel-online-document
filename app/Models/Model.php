<?php

namespace App\Models;

use App\Models\SoftDelete\SoftDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    use SoftDelete;
    use HasFactory;

    /**
     * 与表关联的主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 是否主动维护时间戳
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 模型日期的存储格式：录入时，创建与更新的时间为：时间戳
     *
     * @var string
     */
    protected $dateFormat = 'U';

    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';

    public function getCreatedTimeAttribute()
    {
        return $this->attributes[self::CREATED_AT];
    }

    public function getUpdatedTimeAttribute()
    {
        return $this->attributes[self::UPDATED_AT];
    }

    /**
     * 不可批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [];

    public static function firstByWhere($where)
    {
        return self::where($where)->first();
    }

    // 定义按月分表的组成部分，避免逻辑报错
    const MIN_TABLE    = '';// 表名最小的月份
    const MONTH_FORMAT = '';
    public function setMonthTable(string $month = '')
    {
        return $this;
    }
}
