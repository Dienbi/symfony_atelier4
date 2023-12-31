<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

//    /**
//     * @return Author[] Returns an array of Author objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Author
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function listAuthorByEmail()
{
    return $this->createQueryBuilder('a')//ORDER BY EMAIL
        ->orderBy('a.email', 'ASC')
        ->getQuery()
        ->getResult();
}
    public function findAuthorsByCount($minBooks, $maxBooks)
    {
        $qb = $this->createQueryBuilder('a');

        if ($minBooks !== null) {
            $qb->andWhere($qb->expr()->gte('a.nb_books', $minBooks));
        }

        if ($maxBooks !== null) {
            $qb->andWhere($qb->expr()->lte('a.nb_books', $maxBooks));
        }

        return $qb->getQuery()->getResult();
    }
    public function findAuthorsByBookCountRange($minBooks, $maxBooks)
    {
        $qb = $this->createQueryBuilder('a');

        if ($minBooks !== null) {
            $qb->andWhere($qb->expr()->gte('a.nb_books', $minBooks));
        }

        if ($maxBooks !== null) {
            $qb->andWhere($qb->expr()->lte('a.nb_books', $maxBooks));
        }

        return $qb->getQuery()->getResult();
    }
}

