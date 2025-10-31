<?php

namespace TencentCloudSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TencentCloudSmsBundle\Entity\SmsTemplate;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<SmsTemplate>
 */
#[AsRepository(entityClass: SmsTemplate::class)]
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

    /**
     * @return SmsTemplate[]
     */
    public function findValidTemplates(): array
    {
        return $this->findBy(['templateStatus' => 1, 'valid' => true]);
    }

    public function save(SmsTemplate $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SmsTemplate $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
