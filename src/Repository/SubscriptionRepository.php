<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Email;
use App\Entity\SubscriptionEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubscriptionEntity>
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscriptionEntity::class);
    }

    public function findByEmail(Email $email): ?SubscriptionEntity
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.email.value = :email')
            ->setParameter('email', $email->getValue())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByConfirmToken(string $token): ?SubscriptionEntity
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.confirmToken.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(SubscriptionEntity $subscription, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->persist($subscription);

        if ($flush) {
            $em->flush();
        }
    }
}
