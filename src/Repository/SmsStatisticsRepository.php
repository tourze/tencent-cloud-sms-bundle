<?php

namespace TencentCloudSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsStatistics;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<SmsStatistics>
 */
#[AsRepository(entityClass: SmsStatistics::class)]
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
     * @return array<SmsStatistics>
     */
    public function findByDateRange(\DateTimeImmutable $start, \DateTimeImmutable $end, Account $account): array
    {
        /** @var array<SmsStatistics> */
        return $this->createQueryBuilder('s')
            ->andWhere('s.hour >= :start')
            ->andWhere('s.hour <= :end')
            ->andWhere('s.account = :account')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('account', $account)
            ->orderBy('s.hour', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(SmsStatistics $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SmsStatistics $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
