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

    public function findByUnsubscribeToken(string $token): ?SubscriptionEntity
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.unsubscribeToken.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<string>
     */
    public function getUniqueCities(): array
    {
        $qb = $this->createQueryBuilder('s')
            ->select('DISTINCT s.city AS city')
            ->andWhere('s.confirmed = :confirmed')
            ->andWhere('s.subscribed = :subscribed')
            ->setParameter('confirmed', true)
            ->setParameter('subscribed', true)
            ->orderBy('s.city', 'ASC');

        $rows = $qb->getQuery()->getScalarResult();
        return array_column($rows, 'city');
    }

    /**
     * @return SubscriptionEntity[]
     */
    public function getActiveSubscribersForCity(string $city): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.city = :city')
            ->andWhere('s.confirmed = :confirmed')
            ->andWhere('s.subscribed = :subscribed')
            ->setParameter('city', $city)
            ->setParameter('confirmed', true)
            ->setParameter('subscribed', true)
            ->getQuery()
            ->getResult();
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
