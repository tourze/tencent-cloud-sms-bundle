<?php

namespace TencentCloudSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TencentCloudSmsBundle\Entity\SmsMessage;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<SmsMessage>
 */
#[AsRepository(entityClass: SmsMessage::class)]
class SmsMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsMessage::class);
    }

    /**
     * @return array<SmsMessage>
     */
    public function findBySerialNo(string $serialNo): array
    {
        $qb = $this->createQueryBuilder('m')
            ->innerJoin('m.recipients', 'r')
            ->where('r.serialNo = :serialNo')
            ->setParameter('serialNo', $serialNo)
            ->orderBy('m.createTime', 'DESC')
        ;

        /** @var array<SmsMessage> */
        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<SmsMessage>
     */
    public function findByPhoneNumber(string $phoneNumber, ?string $nationCode = null): array
    {
        $qb = $this->createQueryBuilder('m')
            ->innerJoin('m.recipients', 'r')
            ->innerJoin('r.phoneNumber', 'p')
            ->where('p.phoneNumber = :phoneNumber')
            ->setParameter('phoneNumber', $phoneNumber)
        ;

        if (null !== $nationCode) {
            $qb->andWhere('p.nationCode = :nationCode')
                ->setParameter('nationCode', $nationCode)
            ;
        }

        $qb->orderBy('m.createTime', 'DESC');

        /** @var array<SmsMessage> */
        return $qb->getQuery()->getResult();
    }

    public function save(SmsMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SmsMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
