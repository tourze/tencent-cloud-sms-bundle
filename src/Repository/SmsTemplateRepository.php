<?php

namespace TencentCloudSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TencentCloudSmsBundle\Entity\SmsTemplate;

/**
 * @method SmsTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsTemplate[] findAll()
 * @method SmsTemplate[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsTemplate::class);
    }

    public function findByTemplateId(string $templateId): ?SmsTemplate
    {
        return $this->findOneBy(['templateId' => $templateId]);
    }

    public function findValidTemplates(): array
    {
        return $this->findBy(['templateStatus' => 1, 'valid' => true]);
    }
}
