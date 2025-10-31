# TencentCloudSmsBundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/tencent-cloud-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/tencent-cloud-sms-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/tencent-cloud-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/tencent-cloud-sms-bundle)
[![License](https://img.shields.io/packagist/l/tourze/tencent-cloud-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/tencent-cloud-sms-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/tencent-cloud-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/tencent-cloud-sms-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master&style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo/master.svg?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

腾讯云短信服务集成模块，提供全面的短信发送、状态同步和统计数据管理功能。

## 目录

- [功能特性](#功能特性)
- [系统要求](#系统要求)
- [安装](#安装)
- [配置](#配置)
  - [1. 注册 Bundle](#1-注册-bundle)
  - [2. 配置 Doctrine 实体](#2-配置-doctrine-实体)
- [快速开始](#快速开始)
  - [基本短信发送](#基本短信发送)
  - [状态同步](#状态同步)
- [可用命令](#可用命令)
  - [同步命令](#同步命令)
    - [同步手机号码信息](#同步手机号码信息)
    - [同步短信签名状态](#同步短信签名状态)
    - [同步短信投递状态](#同步短信投递状态)
    - [同步短信模板状态](#同步短信模板状态)
    - [同步未知状态的短信记录](#同步未知状态的短信记录)
    - [同步短信统计数据](#同步短信统计数据)
- [核心服务](#核心服务)
  - [SmsSendService](#smssendservice)
  - [StatusSyncService](#statussyncservice)
  - [StatisticsSyncService](#statisticssyncservice)
  - [PhoneNumberInfoService](#phonenumberinfoservice)
  - [SmsClient](#smsclient)
- [高级用法](#高级用法)
  - [自定义模板参数](#自定义模板参数)
  - [批量状态同步](#批量状态同步)
  - [统计报告](#统计报告)
- [贡献](#贡献)
- [许可证](#许可证)

## 功能特性

- 📤 支持模板的短信发送服务
- 📊 短信统计数据同步
- 📝 短信模板状态管理和审核
- ✉️ 短信签名状态管理和审核
- 🔍 手机号码信息查询服务
- 📈 实时短信投递状态同步
- 🖼️ 签名/模板的图片上传和处理
- 📋 全面的短信消息和接收者管理
- 🔄 自动化状态同步命令
- 📈 高级统计和报告

## 系统要求

- PHP >= 8.1
- Symfony >= 7.3
- Doctrine ORM >= 3.0
- 腾讯云短信 SDK

## 安装

```bash
composer require tourze/tencent-cloud-sms-bundle
```

## 配置

### 1. 注册 Bundle

在 `config/bundles.php` 中添加 bundle：

```php
<?php

return [
    // ...
    TencentCloudSmsBundle\TencentCloudSmsBundle::class => ['all' => true],
];
```

### 2. 配置 Doctrine 实体

该 bundle 提供以下需要在数据库中配置的主要实体：

- `Account` - 腾讯云短信账户配置
- `SmsMessage` - 短信消息记录
- `SmsRecipient` - 短信接收者信息
- `SmsSignature` - 短信签名管理
- `SmsTemplate` - 短信模板管理
- `SmsStatistics` - 短信统计数据
- `PhoneNumberInfo` - 手机号码信息

## 快速开始

### 基本短信发送

```php
<?php

use TencentCloudSmsBundle\Service\SmsSendService;
use TencentCloudSmsBundle\Entity\SmsRecipient;

class SmsController
{
    public function __construct(
        private SmsSendService $smsSendService
    ) {}

    public function sendSms(SmsRecipient $recipient): void
    {
        // 向指定接收者发送短信
        $this->smsSendService->send($recipient);
    }
}
```

### 状态同步

```php
<?php

use TencentCloudSmsBundle\Service\StatusSyncService;

class SyncController
{
    public function __construct(
        private StatusSyncService $statusSyncService
    ) {}

    public function syncStatus(): void
    {
        // 同步短信投递状态
        $this->statusSyncService->sync();
    }
}
```

## 可用命令

### 同步命令

#### 同步手机号码信息
```bash
bin/console tencent-cloud:sms:sync:phone-number-info
```
同步所有账户的手机号码信息。

#### 同步短信签名状态
```bash
bin/console tencent-cloud:sms:sync:sign-status
```
同步短信签名审核状态。

#### 同步短信投递状态
```bash
bin/console tencent-cloud:sms:sync:status
```
同步短信投递状态（成功、失败等）。

#### 同步短信模板状态
```bash
bin/console tencent-cloud:sms:sync:template-status
```
同步短信模板审核状态。

#### 同步未知状态的短信记录
```bash
bin/console tencent-cloud:sms:sync:unknown-status [--limit=100]
```
同步状态未知的短信记录。可以指定每批同步的最大记录数。

#### 同步短信统计数据
```bash
bin/console tencent-cloud:sms:sync-statistics [--start-time="Y-m-d H:i:s"] [--end-time="Y-m-d H:i:s"] [--account-id=123]
```
同步指定时间范围和账户的短信统计数据。

## 核心服务

### SmsSendService
处理支持模板的短信发送操作。

### StatusSyncService  
管理从腾讯云同步短信投递状态。

### StatisticsSyncService
同步用于报告和分析的短信统计数据。

### PhoneNumberInfoService
查询和管理手机号码信息及验证。

### SmsClient
腾讯云短信 SDK 客户端操作的包装器。

## 高级用法

### 自定义模板参数

```php
<?php

use TencentCloudSmsBundle\Entity\SmsMessage;

$message = new SmsMessage();
$message->setTemplateParams([
    'code' => '123456',
    'expiry' => '5'
]);
```

### 批量状态同步

```php
<?php

use TencentCloudSmsBundle\Service\StatusSyncService;

class BatchSyncService
{
    public function __construct(
        private StatusSyncService $statusSyncService
    ) {}

    public function syncBatchStatus(): void
    {
        // 同步所有待处理消息的状态
        $this->statusSyncService->sync();
    }
}
```

### 统计报告

```php
<?php

use TencentCloudSmsBundle\Repository\SmsStatisticsRepository;

class ReportService
{
    public function __construct(
        private SmsStatisticsRepository $statisticsRepository
    ) {}

    public function getDailyStats(\DateTime $date): array
    {
        return $this->statisticsRepository->findByDate($date);
    }
}
```

## 测试

运行测试套件：

```bash
# 运行所有测试
./vendor/bin/phpunit packages/tencent-cloud-sms-bundle/tests

# 运行带覆盖率的测试
./vendor/bin/phpunit packages/tencent-cloud-sms-bundle/tests --coverage-html=coverage

# 运行 PHPStan 分析
./vendor/bin/phpstan analyse packages/tencent-cloud-sms-bundle
```

## 贡献

请参阅 [CONTRIBUTING.md](../../CONTRIBUTING.md) 了解如何为此项目贡献的详细信息。

## 许可证

MIT 许可证 (MIT)。更多信息请参见 [许可证文件](LICENSE)。