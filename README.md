# TencentCloudSmsBundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/tencent-cloud-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/tencent-cloud-sms-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/tencent-cloud-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/tencent-cloud-sms-bundle)
[![License](https://img.shields.io/packagist/l/tourze/tencent-cloud-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/tencent-cloud-sms-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/tencent-cloud-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/tencent-cloud-sms-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master&style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo/master.svg?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

Tencent Cloud SMS service integration module providing comprehensive SMS sending, status synchronization, 
and statistical data management functionality.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
  - [1. Register the Bundle](#1-register-the-bundle)
  - [2. Configure Doctrine Entities](#2-configure-doctrine-entities)
- [Quick Start](#quick-start)
  - [Basic SMS Sending](#basic-sms-sending)
  - [Status Synchronization](#status-synchronization)
- [Available Commands](#available-commands)
  - [Synchronization Commands](#synchronization-commands)
    - [Sync Phone Number Information](#sync-phone-number-information)
    - [Sync SMS Signature Status](#sync-sms-signature-status)
    - [Sync SMS Delivery Status](#sync-sms-delivery-status)
    - [Sync SMS Template Status](#sync-sms-template-status)
    - [Sync Unknown Status SMS Records](#sync-unknown-status-sms-records)
    - [Sync SMS Statistics Data](#sync-sms-statistics-data)
- [Core Services](#core-services)
  - [SmsSendService](#smssendservice)
  - [StatusSyncService](#statussyncservice)
  - [StatisticsSyncService](#statisticssyncservice)
  - [PhoneNumberInfoService](#phonenumberinfoservice)
  - [SmsClient](#smsclient)
- [Advanced Usage](#advanced-usage)
  - [Custom Template Parameters](#custom-template-parameters)
  - [Batch Status Synchronization](#batch-status-synchronization)
  - [Statistics Reporting](#statistics-reporting)
- [Contributing](#contributing)
- [License](#license)

## Features

- 📤 SMS sending service with template support
- 📊 SMS statistics data synchronization
- 📝 SMS template status management and review
- ✉️ SMS signature status management and review
- 🔍 Phone number information query service
- 📈 Real-time SMS delivery status synchronization
- 🖼️ Image upload and processing for signatures/templates
- 📋 Comprehensive SMS message and recipient management
- 🔄 Automated status sync commands
- 📈 Advanced statistics and reporting

## Requirements

- PHP >= 8.1
- Symfony >= 7.3
- Doctrine ORM >= 3.0
- Tencent Cloud SMS SDK

## Installation

```bash
composer require tourze/tencent-cloud-sms-bundle
```

## Configuration

### 1. Register the Bundle

Add the bundle to your `config/bundles.php`:

```php
<?php

return [
    // ...
    TencentCloudSmsBundle\TencentCloudSmsBundle::class => ['all' => true],
];
```

### 2. Configure Doctrine Entities

The bundle provides these main entities that need to be configured in your database:

- `Account` - Tencent Cloud SMS account configuration
- `SmsMessage` - SMS message records
- `SmsRecipient` - SMS recipient information
- `SmsSignature` - SMS signature management
- `SmsTemplate` - SMS template management
- `SmsStatistics` - SMS statistics data
- `PhoneNumberInfo` - Phone number information

## Quick Start

### Basic SMS Sending

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
        // Send SMS to the specified recipient
        $this->smsSendService->send($recipient);
    }
}
```

### Status Synchronization

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
        // Sync SMS delivery status
        $this->statusSyncService->sync();
    }
}
```

## Available Commands

### Synchronization Commands

#### Sync Phone Number Information
```bash
bin/console tencent-cloud:sms:sync:phone-number-info
```
Synchronizes phone number information for all accounts.

#### Sync SMS Signature Status
```bash
bin/console tencent-cloud:sms:sync:sign-status
```
Synchronizes SMS signature review status.

#### Sync SMS Delivery Status
```bash
bin/console tencent-cloud:sms:sync:status
```
Synchronizes SMS delivery status (success, failure, etc.).

#### Sync SMS Template Status
```bash
bin/console tencent-cloud:sms:sync:template-status
```
Synchronizes SMS template review status.

#### Sync Unknown Status SMS Records
```bash
bin/console tencent-cloud:sms:sync:unknown-status [--limit=100]
```
Synchronizes SMS records with unknown status. You can specify the maximum number of records to sync per batch.

#### Sync SMS Statistics Data
```bash
bin/console tencent-cloud:sms:sync-statistics \
  [--start-time="Y-m-d H:i:s"] [--end-time="Y-m-d H:i:s"] [--account-id=123]
```
Synchronizes SMS statistics data for the specified time range and account.

## Core Services

### SmsSendService
Handles SMS sending operations with template support.

### StatusSyncService  
Manages synchronization of SMS delivery status from Tencent Cloud.

### StatisticsSyncService
Synchronizes SMS statistics data for reporting and analytics.

### PhoneNumberInfoService
Queries and manages phone number information and validation.

### SmsClient
Wrapper for Tencent Cloud SMS SDK client operations.

## Advanced Usage

### Custom Template Parameters

```php
<?php

use TencentCloudSmsBundle\Entity\SmsMessage;

$message = new SmsMessage();
$message->setTemplateParams([
    'code' => '123456',
    'expiry' => '5'
]);
```

### Batch Status Synchronization

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
        // Sync status for all pending messages
        $this->statusSyncService->sync();
    }
}
```

### Statistics Reporting

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

## Testing

Run the test suite with:

```bash
# Run all tests
./vendor/bin/phpunit packages/tencent-cloud-sms-bundle/tests

# Run with coverage
./vendor/bin/phpunit packages/tencent-cloud-sms-bundle/tests --coverage-html=coverage

# Run PHPStan analysis
./vendor/bin/phpstan analyse packages/tencent-cloud-sms-bundle
```

## Contributing

Please see [CONTRIBUTING.md](../../CONTRIBUTING.md) for details on how to contribute to this project.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.