<?php

namespace App\Models;

class User extends Model
{
    // 连接User库
    protected $connection = 'user_mysql';

    protected $primaryKey = 'user_id';
    public $timestamps = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public function userInfo()
    {
        return $this->hasOne(UserInfo::class, 'user_id', 'user_id');
    }

    /**
     * 通过用户名搜索
     *
     * @param string $user_name
     *
     * @return mixed
     */
    public function getUserByName(string $user_name)
    {
        return $this->where('user_name', $user_name)->first();
    }

    /**
     * 通过邮箱进行搜索
     *
     * @param  string  $user_email
     *
     * @return mixed
     */
    public function getUserByEmail(string $user_email)
    {
        return $this->where('user_email', $user_email)->first();
    }

    /**
     * 通过手机号进行搜索
     *
     * @param  string  $user_mobile
     *
     * @return mixed
     */
    public function getUserByMobile(string $user_mobile)
    {
        return $this->where('user_mobile', $user_mobile)->first();
    }
}
