<?php

/*
 * This file is part of the mingyoung/dingtalk.
 *
 * (c) 张铭阳 <mingyoungcheung@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyDingTalk\Cspace;

use EasyDingTalk\Kernel\BaseClient;
use EasyDingTalk\Kernel\Exceptions\RuntimeException;
use EasyDingTalk\Kernel\Exceptions\InvalidArgumentException;

/**
 * cspace
 */
class Client extends BaseClient
{
    /**
     * 新增文件到用户自定义空间
     *
     * @param string $code    [description]
     * @param [type] $mediaId [description]
     * @param [type] $name    [description]
     */
    public function add($userId, $code, $mediaId, $name)
    {
        $space = $this->info($userId);
        return $this->app->client->get('cspace/add', [
            'agent_id' => $this->app['config']['agent_id'],
            'code'     => $code,
            'media_id' => $mediaId,
            'name'     => $name,
            'space_id' => $space['result']['space_id'],
        ]);
    }

    /**
     * 审批钉盘空间id
     *
     * @return array
     */
    public function info($userId)
    {
        return $this->app->client->get('topapi/processinstance/cspace/info', ['user_id' => $userId]);
    }
}
