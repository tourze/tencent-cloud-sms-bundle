<?php

namespace TencentCloudSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TencentCloudSmsBundle\Entity\SmsMessage;

/**
 * @method SmsMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsMessage[] findAll()
 * @method SmsMessage[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsMessage::class);
    }

    public function findBySerialNo(string $serialNo): ?SmsMessage
    {
        return $this->findOneBy(['serialNo' => $serialNo]);
    }

    public function findByPhoneNumber(string $phoneNumber, ?string $countryCode = null): array
    {
        $criteria = ['phoneNumber' => $phoneNumber];
        if ($countryCode !== null) {
            $criteria['countryCode'] = $countryCode;
        }
        return $this->findBy($criteria, ['createTime' => 'DESC']);
    }
}
