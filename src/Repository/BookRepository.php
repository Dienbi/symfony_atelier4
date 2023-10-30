<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function findOneByRef($ref)//SEARCH BY REF
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.ref = :ref')
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function booksListByAuthors()
    {
        return $this->createQueryBuilder('b')
            ->join('b.author', 'a') // Assuming the association between Book and Author is named 'author'
            ->orderBy('a.username', 'ASC') // Replace 'authorName' with the actual property of Author representing the author's name
            ->getQuery()
            ->getResult();
    }
    public function find2023()
    {
        $date = new \DateTime('2023-01-01');

        $qb = $this->createQueryBuilder('b');
        $qb->select('b')
           ->innerJoin('b.author', 'a')
           ->where($qb->expr()->lt('b.publicationdate', ':date'))
           ->setParameter('date', $date)
           ->groupBy('a.id')
           ->having($qb->expr()->gt($qb->expr()->count('b'), 10))
           ->orderBy('a.username', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function updateCategory()
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery('UPDATE App\Entity\Book b SET b.category = :newCategory WHERE b.category = :oldCategory');
        $query->setParameter('newCategory', 'Romance');
        $query->setParameter('oldCategory', 'Science-Fiction');

        return $query->execute();
    }
    public function countBooksByCategory($category)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT COUNT(b.ref)
            FROM App\Entity\Book b
            WHERE b.category = :category
        ');
        $query->setParameter('category', $category);

        return $query->getSingleScalarResult();
    }
    public function dates($startDate, $endDate)//findBooksPublishedBetweenDates
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT b
            FROM App\Entity\Book b
            WHERE b.publicationdate BETWEEN :start AND :end
        ');
        $query->setParameter('start', $startDate);
        $query->setParameter('end', $endDate);

        return $query->getResult();
    }
    public function findAuthorsByBookCountRange($minBooks, $maxBooks)
    {
        $qb = $this->createQueryBuilder('a');

        if ($minBooks !== null) {
            $qb->andWhere($qb->expr()->gte('a.booksCount', $minBooks));
        }

        if ($maxBooks !== null) {
            $qb->andWhere($qb->expr()->lte('a.booksCount', $maxBooks));
        }

        return $qb->getQuery()->getResult();
    }
}
