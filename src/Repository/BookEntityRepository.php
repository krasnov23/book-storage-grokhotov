<?php

namespace App\Repository;

use App\Entity\BookEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookEntity>
 *
 * @method BookEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookEntity[]    findAll()
 * @method BookEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookEntity::class);
    }

    public function save(BookEntity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BookEntity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function bookPagination(int $offset,int $limit)
    {
        $query = $this->createQueryBuilder('b')
            ->orderBy('b.id','DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($query,false);
    }

    public function booksByCategoryAndPages(int $categoryId,int $offset,int $limit): Paginator
    {
        $query = $this->createQueryBuilder('b')
            ->addSelect('c')
            ->leftJoin('b.categories','c')
            ->where('c.id = :id')
            ->setParameter('id',$categoryId)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($query,false);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getBookWithCategories(int $bookId): BookEntity
    {
        return $this->createQueryBuilder('b')
            ->addSelect('c')
            ->leftJoin('b.categories','c')
            ->where('b.id = :id')
            ->setParameter('id',$bookId)
            ->getQuery()->getSingleResult();

    }

    public function getBookWithSameCategories(array $categories): array
    {
        $query =  $this->createQueryBuilder('b')
            ->addSelect('c')
            ->leftJoin('b.categories','c')
            ->where('c.id IN (:categories)')
            ->setParameter('categories',$categories);

        return $query->getQuery()->getArrayResult();
    }




//    /**
//     * @return BookEntity[] Returns an array of BookEntity objects
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

//    public function findOneBySomeField($value): ?BookEntity
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
