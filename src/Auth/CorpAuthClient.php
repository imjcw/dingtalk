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

class CorpAuthClient extends Client
{
    /**
     * 获取应用后台免登 AccessToken
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->get('gettoken', [
            'accessKey'    => $this->app['config']->get('access_key'),
            'accessSecret' => $this->app['config']->get('access_secret'),
            'suiteTicket'  => $this->app['config']->get('suite_ticket'),
            'auth_corpid'  => $this->app['config']->get('auth_corpid'),
            'timestamp'    => $timestamp = (int) microtime(true) * 1000,
            'signature'    => $this->signature($timestamp)
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

    /**
     * 计算签名
     *
     * @param int $timestamp
     *
     * @return string
     */
    public function signature($timestamp)
    {
        return base64_encode(hash_hmac('sha256', $timestamp + "\n" + $this->app['config']->get('suite_ticket'), true));
    }
}
