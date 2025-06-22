<?php

namespace TencentCloudSmsBundle\Service;

use TencentCloudSmsBundle\Exception\SignatureException;

class ImageService
{
    /**
     * 从 URL 获取图片并转换为 base64 格式
     *
     * @throws SignatureException
     */
    public function getBase64FromUrl(string $url): string
    {
        // 读取图片内容
        $imageContent = @file_get_contents($url);
        if ($imageContent === false) {
            throw new SignatureException(sprintf('无法读取图片文件：%s', $url));
        }

        // 转换为 base64 并去掉前缀
        $base64 = base64_encode($imageContent);

        // 如果是 data URI，去掉前缀
        if (str_starts_with($base64, 'data:image/')) {
            $base64 = preg_replace('/^data:image\/[^;]+;base64,/', '', $base64);
        }

        return $base64;
    }
}
