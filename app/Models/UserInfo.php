<?php

namespace App\Models;

class UserInfo extends Model
{
    // 连接User库
    protected $connection = 'user_mysql';

    protected $primaryKey = 'user_id';
}
