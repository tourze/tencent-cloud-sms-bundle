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

    public function testPostPersist(): void
    {
        // 创建测试用的SmsRecipient
        $recipient = new SmsRecipient();

        // 创建真实的EntityManager和Event对象
        $entityManager = self::getEntityManager();
        $event = new PostPersistEventArgs($recipient, $entityManager);

        // Mock SmsSendService - 需要在任何服务获取之前设置
        /** @var MockObject&SmsSendService $smsSendService */
        $smsSendService = $this->createMock(SmsSendService::class);
        $smsSendService->expects($this->once())
            ->method('send')
            ->with($recipient)
        ;

        // 为了测试目的，直接实例化SmsRecipientListener以使用我们的Mock服务
        // @phpstan-ignore integrationTest.noDirectInstantiationOfCoveredClass
        $listener = new SmsRecipientListener($smsSendService);

        // 测试postPersist方法调用
        $listener->postPersist($recipient, $event);
    }
}
