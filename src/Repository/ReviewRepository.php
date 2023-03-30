<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\BookToBookFormat;
use App\Entity\Review;
use App\Entity\Subscriber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review}>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function countByBookId(int $id): int
    {
        return (int) $this->count(['book' => $id]);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getBookTotalRatingSum(int $id): ?int
    {
        return (int) $this->_em
            ->createQuery('SELECT SUM(r.rating) FROM App\Entity\Review r WHERE r.book = :id')
            ->setParameter('id', $id)
            ->getSingleScalarResult()
        ;
    }

    public function getPageByBookId(int $id, int $offset, int $limit): Paginator
    {
        $query = $this->_em
            ->createQuery('SELECT r FROM App\Entity\Review r WHERE r.book = :id ORDER BY r.createdAt DESC')
            ->setParameter('id', $id)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        return new Paginator($query, false);
    }
}
