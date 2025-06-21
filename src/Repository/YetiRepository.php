<?php

namespace App\Repository;

use App\Entity\Yeti;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Yeti>
 */
class YetiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Yeti::class);
    }

    /**
     * Returns the top yetis ordered by their votes.
     */
    public function findTopByVotes(int $limit = 10): array
    {
        return $this->createQueryBuilder('y')
            ->orderBy('y.votes', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns the total count of yetis registered since the given date.
     */
    public function countNewSince(\DateTimeInterface $since): int
    {
        return (int)$this->createQueryBuilder('y')
            ->select('count(y.id)')
            ->where('y.created >= :since')
            ->setParameter('since', $since)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Returns the average rating of all yetis.
     */
    public function getAverageVotes(): float
    {
        return (float)$this->createQueryBuilder('y')
            ->select('AVG(y.votes)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
