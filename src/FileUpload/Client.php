<?php

/*
 * This file is part of the mingyoung/dingtalk.
 *
 * (c) 张铭阳 <mingyoungcheung@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyDingTalk\FileUpload;

use EasyDingTalk\Kernel\BaseClient;
use EasyDingTalk\Kernel\Exceptions\RuntimeException;
use EasyDingTalk\Kernel\Exceptions\InvalidArgumentException;

/**
 * file upload
 */
class Client extends BaseClient
{
    /**
     * 分块上传文件
     *
     * @param  string $filePath 文件地址
     *
     * @throws \EasyDingTalk\Kernel\Exceptions\InvalidArgumentException
     */
    public function chunk($filePath)
    {
        if (!is_file($filePath)) {
            throw new InvalidArgumentException('Invalid file');
        }

        $filename = basename($filePath);
        // 每个拆分成 7.5M，文档要求不超过 8M，且大于 100K
        $minPieceSize = 102400;
        $pieceSize    = 7864320;
        $fileSize     = (int)filesize($filePath);
        if ($fileSize > $minPieceSize) {
            $lastPieceSize = $fileSize % $pieceSize;
            // 如果有余数并且小于 100K，则需要处理
            if ($lastPieceSize && $lastPieceSize < $minPieceSize) {
                // 获取分割后的文件数量(比正常少1)
                $nums = floor($fileSize / $pieceSize);
                // 每个匀一点(向上取整。只能多不能少)
                $partSize = ceil(($minPieceSize - $lastPieceSize) / $nums);
                $pieceSize -= $partSize;
            }
            // 计算分割的文件数量
            $chunkNumbers = (int)ceil($fileSize / $pieceSize);
        } else {
            $chunkNumbers = 1;
        }

        // 开启分块上传事务
        $transaction = $this->transaction($fileSize, $chunkNumbers);
        // $transaction['upload_id'] = '18F03BA734304807A212B058EA642741_7#iAEHAqRmaWxlA6h5dW5kaXNrMATOCw-idAXNBqUGzciYB85e_T_UCM0BTA';

        $fileHandler = fopen($filePath, 'rb');
        $chunkSequence = 1;

        $query = http_build_query([
            'agent_id'       => $this->app['config']['agent_id'],
            'upload_id'      => $transaction['upload_id'],
            'chunk_sequence' => $chunkSequence
        ]);
        // 上传文件块
        while(!feof($fileHandler)) {
            $tmpFilePath = '/tmp/' . $filename;
            @file_put_contents($tmpFilePath, fread($fileHandler, $pieceSize));
            $this->client->upload('file/upload/chunk', ['file' => $tmpFilePath], [], [
                'agent_id'       => $this->app['config']['agent_id'],
                'upload_id'      => $transaction['upload_id'],
                'chunk_sequence' => $chunkSequence
            ]);
            $chunkSequence++;
        }

        // 提交文件上传事务
        return $this->transaction($fileSize, $chunkNumbers, $transaction['upload_id']);
    }

    /**
     * 分布上传文件事务
     *
     * @param  integer $fileSize     文件大小
     * @param  integer $chunkNumbers 分割数量
     * @param  string  $uploadId     上传事务id
     *
     * @return array
     */
    protected function transaction($fileSize, $chunkNumbers, $uploadId = '')
    {
        $params = [
           'file_size'     => $fileSize,
           'chunk_numbers' => $chunkNumbers,
           'agent_id'      => $this->app['config']['agent_id']
        ];
        if ($uploadId) {
            $params['upload_id'] = $uploadId;
        }
        return $this->client->get('file/upload/transaction', $params);
    }
}
