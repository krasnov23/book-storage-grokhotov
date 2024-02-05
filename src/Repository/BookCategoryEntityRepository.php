<?php

namespace App\Repository;

use App\Entity\BookCategoryEntity;
use App\Entity\BookEntity;
use App\Entity\Category;
use App\Models\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function getAllHighLevelCategories(int $level,string $bookName = '',string $authorName = '',string $bookStatus = ''): array
    {
        return $this->createQueryBuilder('c')
            ->addSelect('b')
            ->leftJoin('c.books','b')
            ->where('c.level = :level')
            ->setParameter('level',$level)
            ->getQuery()->getArrayResult();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getBooksAndSubcategoryByCategory(int $id, string $bookName = '', string $authorBook = '', string $bookStatus = ''): ?BookCategoryEntity
    {
        $category = $this->createQueryBuilder('c')
            ->addSelect('cc')
            ->leftJoin('c.categoryChild','cc')
            ->addSelect('b')
            ->leftJoin('c.books','b')
            ->where('c.id = :id')
            ->setParameter('id',$id);

        if ($bookName)
        {
            $category->andWhere('b.name = :name')
                ->setParameter('name',$bookName);
        }

        if ($bookStatus)
        {
            $category->andWhere('b.status = :status')
                ->setParameter('status',$bookStatus);

        }

        if ($authorBook)
        {
            $category->addSelect('a')
                ->leftJoin('b.authors','a')
                ->andWhere('a.name = :authorName')
                ->setParameter('authorName',$authorBook);
        }

        return $category->getQuery()->getOneOrNullResult();
    }

    public function findParentCategory(array $category): BookCategoryEntity
    {
        return $this->findOneBy($category);
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
