<?php

namespace TencentCloudSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<PhoneNumberInfo>
 */
#[AsRepository(entityClass: PhoneNumberInfo::class)]
final class PhoneNumberInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhoneNumberInfo::class);
    }

    public function findByPhoneNumber(string $phoneNumber, ?string $nationCode = null): ?PhoneNumberInfo
    {
        $criteria = ['phoneNumber' => $phoneNumber];
        if (null !== $nationCode) {
            $criteria['nationCode'] = $nationCode;
        }

        return $this->findOneBy($criteria);
    }

    public function save(PhoneNumberInfo $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PhoneNumberInfo $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
