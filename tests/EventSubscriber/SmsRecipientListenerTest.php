<?php

namespace TencentCloudSmsBundle\Tests\EventSubscriber;

use Doctrine\ORM\Event\PostPersistEventArgs;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use TencentCloudSmsBundle\EventSubscriber\SmsRecipientListener;
use TencentCloudSmsBundle\Service\SmsSendService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(SmsRecipientListener::class)]
#[RunTestsInSeparateProcesses]
final class SmsRecipientListenerTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
        // 通过容器获取服务
        // 这个测试比较简单，不需要额外的设置
    }

    public function testCanBeInstantiated(): void
    {
        $listener = self::getService(SmsRecipientListener::class);
        $this->assertInstanceOf(SmsRecipientListener::class, $listener);
    }

    public function testPostPersistWithoutMessage(): void
    {
        // 创建测试用的SmsRecipient（不关联消息）
        $recipient = new SmsRecipient();

        // 创建EntityManager和Event对象
        $entityManager = self::getEntityManager();
        $event = new PostPersistEventArgs($recipient, $entityManager);

        // 通过容器获取SmsRecipientListener服务
        $listener = self::getService(SmsRecipientListener::class);

        // 测试postPersist方法调用 - 应该抛出异常因为recipient没有关联消息
        $this->expectException(\TencentCloudSmsBundle\Exception\SmsException::class);
        $this->expectExceptionMessage('短信接收者未关联消息');

        $listener->postPersist($recipient, $event);
    }
}
