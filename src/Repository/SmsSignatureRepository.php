<?php

namespace TencentCloudSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TencentCloudSmsBundle\Entity\SmsSignature;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<SmsSignature>
 */
#[AsRepository(entityClass: SmsSignature::class)]
final class SmsSignatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsSignature::class);
    }

    public function findBySignId(string $signId): ?SmsSignature
    {
        return $this->findOneBy(['signId' => $signId]);
    }

    /**
     * @return SmsSignature[]
     */
    public function findValidSignatures(): array
    {
        return $this->findBy(['signStatus' => 1, 'valid' => true]);
    }

    public function save(SmsSignature $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SmsSignature $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
