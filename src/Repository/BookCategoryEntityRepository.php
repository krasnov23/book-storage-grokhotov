<?php

namespace App\Repository;

use App\Entity\BookCategoryEntity;
use App\Entity\BookEntity;
use App\Models\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookCategoryEntity>
 *
 * @method BookCategoryEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookCategoryEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookCategoryEntity[]    findAll()
 * @method BookCategoryEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookCategoryEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookCategoryEntity::class);
    }

    public function save(BookCategoryEntity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BookCategoryEntity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllHighLevelCategories(int $level): array
    {
        $categories = $this->createQueryBuilder('c')
            ->where('c.level = :level')
            ->setParameter('level',$level)
            ->getQuery()->getArrayResult();

        return $categories;
    }

    public function getBooksAndSubcategoryByCategory(int $id): BookCategoryEntity
    {
        return $this->createQueryBuilder('c')
            ->addSelect('cc')
            ->leftJoin('c.categoryChild','cc')
            ->addSelect('b')
            ->leftJoin('c.books','b')
            ->where('c.id = :id')
            ->setParameter('id',$id)
            ->getQuery()->getSingleResult();

    }



//    /**
//     * @return BookCategoryEntity[] Returns an array of BookCategoryEntity objects
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

//    public function findOneBySomeField($value): ?BookCategoryEntity
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
