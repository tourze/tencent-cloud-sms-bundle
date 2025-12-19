<?php

namespace TencentCloudSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<SmsRecipient>
 */
#[AsRepository(entityClass: SmsRecipient::class)]
final class SmsRecipientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsRecipient::class);
    }

    /**
     * 查找需要同步状态的记录
     *
     * @param \DateTime $sendAfter 发送时间在此之后的记录
     *
     * @return array<SmsRecipient>
     */
    public function findNeedSyncStatus(\DateTime $sendAfter): array
    {
        /** @var array<SmsRecipient> */
        return $this->createQueryBuilder('r')
            ->andWhere('r.sendTime >= :sendAfter')
            ->andWhere('r.receiveTime IS NULL')
            ->andWhere('r.serialNo IS NOT NULL')
            ->setParameter('sendAfter', $sendAfter)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 查找未知状态的记录
     *
     * @param int $limit 最大返回数量
     *
     * @return array<SmsRecipient>
     */
    public function findUnknownStatus(int $limit = 100): array
    {
        /** @var array<SmsRecipient> */
        return $this->createQueryBuilder('r')
            ->andWhere('r.status IS NULL')
            ->andWhere('r.serialNo IS NOT NULL')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 查找所有记录
     *
     * @return list<SmsRecipient>
     */
    public function findAll(): array
    {
        return $this->findBy([]);
    }

    public function save(SmsRecipient $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SmsRecipient $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
