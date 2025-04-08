<?php

namespace TencentCloudSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsStatistics;

/**
 * @method SmsStatistics|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsStatistics|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsStatistics[] findAll()
 * @method SmsStatistics[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsStatisticsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsStatistics::class);
    }

    public function findByHourAndAccount(\DateTimeImmutable $hour, Account $account): ?SmsStatistics
    {
        return $this->findOneBy([
            'hour' => $hour,
            'account' => $account,
        ]);
    }

    /**
     * @return SmsStatistics[]
     */
    public function findByDateRange(\DateTimeImmutable $start, \DateTimeImmutable $end, Account $account): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.hour >= :start')
            ->andWhere('s.hour <= :end')
            ->andWhere('s.account = :account')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('account', $account)
            ->orderBy('s.hour', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
