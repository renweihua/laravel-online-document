<?php

namespace App\Constants;

class CacheKeys
{
    // 默认的缓存时长：默认为1天
    const KEY_DEFAULT_TIMEOUT = 86400;

    const USER_LOGIN_TOKEN = 'users_token:';
    // users_token：token:会员基本信息（List）
    const USER_LOGIN_TOKEN_LIST = 'users_token_list:';
}
