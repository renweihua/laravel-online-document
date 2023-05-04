<?php

namespace App\Modules\Docs\Services;

use App\Exceptions\AuthException;
use App\Exceptions\HttpStatus\UnauthorizedException;
use App\Models\User\User;

class AuthService
{
    /**
     * 登录流程
     *
     * @param $params
     * @return array
     * @throws AuthException
     */
    public function login($params, bool $third_login = false): array
    {
        $userInstance = User::getInstance();
        if (!$third_login){
            $auth_success = false;
            if ($user_name = $userInstance->getUserByName($params['user_name'])){
                if (hash_verify($params['password'], $user_name->password)){
                    $auth_success = true;
                    $user = $user_name;
                }
            }
            if (
                $auth_success == false
                &&
                $user_email = $userInstance->getUserByEmail($params['user_name'])
            )
            {
                if (hash_verify($params['password'], $user_email->password)){
                    $auth_success = true;
                    $user = $user_email;
                }
            }
            if (
                $auth_success == false
                &&
                $user_mobile = $userInstance->getUserByMobile($params['user_name'])
            )
            {
                if (hash_verify($params['password'], $user_mobile->password)){
                    $auth_success = true;
                    $user = $user_mobile;
                }
            }
            // 如果账户、邮箱、手机号，都验证失败 ，那么登录失败
            if (!$auth_success){
                throw new AuthException('认证失败！');
            }
        }else{
            $user = $params;
        }

        if (!$user) throw new AuthException('账户不存在！');
        switch ($user->is_check) {
            case 0:
                throw new AuthException('该账户已被禁用！', 0, $user->user_id);
                break;
            case 2:
                throw new AuthException('异地登录，请重新登录！', 0, $user->user_id);
                break;
        }

        // 登录日志
        // UserLoginLog::getInstance()->add($user->user_id, 1, '登录成功');

        // 加载个人资料模型
        $user->load(['userInfo' => function($query){
            $query->select('user_id', 'nick_name', 'user_avatar');
        }]);

        $result = $this->respondWithToken($user);

        // 记录最新的登录Token
        $user->update(['login_token' => $result['access_token']]);

        // Token存入Redis
        UserLoginRedisService::getInstance()->saveUserToken($result);

        // // 分发`登录`奖励任务
        // LoginReward::dispatch($user, get_client_info())
        //     ->onConnection('database') // job 存储的服务：当前存储mysql
        //     ->onQueue('asktao-queue'); // asktao-queue 队列
        //
        // // 同步其他站点的Token
        // $current_domain = env('APP_SITE_URL');
        // SyncClientToken::dispatch($current_domain, $result['access_token'])
        //     ->onConnection('database') // job 存储的服务：当前存储mysql
        //     ->onQueue('sync-token'); // sync-token 队列

        return $result;
    }

    /**
     * 登录管理员信息获取
     *
     * @param $request
     *
     * @return mixed
     * @throws \App\Exceptions\HttpStatus\UnauthorizedException
     */
    public function me($request)
    {
        if (!$user = $request->attributes->get('login_user')){
            throw new UnauthorizedException('认证失败！');
        }
        $user->load('userInfo');
        return $user;
    }

    /**
     * 退出登录
     *
     * @return bool
     */
    public function logout($token)
    {
        UserLoginRedisService::getInstance()->deleteUserToken($token);
        return true;
    }

    /**
     * Get the token array structure.
     *
     * @param $token
     * @return array
     */
    protected function respondWithToken($user): array
    {
        $user_id = $user->user_id;
        $token = UserLoginRedisService::getInstance()->getUserToken($user_id, $expires_time);
        return [
            'user_id' => $user_id,
            'auth_type' => 'user', // Token认证类型
            'access_token' => $token,
            'expires_time'   => $expires_time,
            'login_time' => time(),
            'user_info' => $user->userInfo,
        ];
    }
}
