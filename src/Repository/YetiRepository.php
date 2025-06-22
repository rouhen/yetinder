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
     * Returns the next Yeti to be voted on.
     */
    public function findYetiForVote(): ?Yeti
    {
        $candidates = $this->createQueryBuilder('y')
            ->orderBy('CASE WHEN y.voteTimestamp IS NULL THEN 0 ELSE 1 END', 'ASC')
            ->addOrderBy('y.voteTimestamp', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        if (!$candidates) {
            return null;
        }

        return $candidates[array_rand($candidates)];
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
        return (int) $this->createQueryBuilder('y')
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
        return (float) $this->createQueryBuilder('y')
            ->select('AVG(y.votes)')
            ->where('y.votes != 0')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
