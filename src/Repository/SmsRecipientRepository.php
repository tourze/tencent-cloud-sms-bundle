<?php

namespace TencentCloudSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TencentCloudSmsBundle\Entity\SmsRecipient;

/**
 * @method SmsRecipient|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsRecipient|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsRecipient[] findAll()
 * @method SmsRecipient[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsRecipientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsRecipient::class);
    }

    /**
     * 查找需要同步状态的记录
     *
     * @param \DateTime $sendAfter 发送时间在此之后的记录
     * @return SmsRecipient[]
     */
    public function findNeedSyncStatus(\DateTime $sendAfter): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.sendTime >= :sendAfter')
            ->andWhere('r.receiveTime IS NULL')
            ->andWhere('r.serialNo IS NOT NULL')
            ->setParameter('sendAfter', $sendAfter)
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找未知状态的记录
     *
     * @param int $limit 最大返回数量
     * @return SmsRecipient[]
     */
    public function findUnknownStatus(int $limit = 100): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.status IS NULL')
            ->andWhere('r.serialNo IS NOT NULL')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
