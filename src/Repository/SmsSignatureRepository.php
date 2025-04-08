<?php

namespace TencentCloudSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TencentCloudSmsBundle\Entity\SmsSignature;

/**
 * @method SmsSignature|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsSignature|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsSignature[] findAll()
 * @method SmsSignature[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsSignatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsSignature::class);
    }

    public function findBySignId(string $signId): ?SmsSignature
    {
        return $this->findOneBy(['signId' => $signId]);
    }

    public function findValidSignatures(): array
    {
        return $this->findBy(['signStatus' => 1, 'valid' => true]);
    }
}
