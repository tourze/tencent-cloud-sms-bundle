<?php

namespace TencentCloudSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;

/**
 * @method PhoneNumberInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhoneNumberInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhoneNumberInfo[] findAll()
 * @method PhoneNumberInfo[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhoneNumberInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhoneNumberInfo::class);
    }

    public function findByPhoneNumber(string $phoneNumber, ?string $countryCode = null): ?PhoneNumberInfo
    {
        $criteria = ['phoneNumber' => $phoneNumber];
        if ($countryCode) {
            $criteria['countryCode'] = $countryCode;
        }
        return $this->findOneBy($criteria);
    }
}
