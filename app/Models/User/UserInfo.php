<?php

namespace App\Models\User;

use App\Models\Model;

class UserInfo extends Model
{
    // 连接User库
    protected $connection = 'user_mysql';

    protected $primaryKey = 'user_id';

    protected $appends = ['time_formatting'];
}
