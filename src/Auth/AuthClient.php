<?php

/*
 * This file is part of the mingyoung/dingtalk.
 *
 * (c) 张铭阳 <mingyoungcheung@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyDingTalk\Auth;

use EasyDingTalk\Kernel\Http\Client;

class AuthClient extends Client
{
    /**
     * 获取应用后台免登 AccessToken
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->get('gettoken', [
            'appkey'    => $this->app['config']->get('app_key'),
            'appsecret' => $this->app['config']->get('app_secret'),
        ]);
    }

    /**
     * 获取用户身份信息
     *
     * @return mixed
     */
    public function user()
    {
        return $this->user->getUserByCode($this->app['request']->get('code'));
    }
}
