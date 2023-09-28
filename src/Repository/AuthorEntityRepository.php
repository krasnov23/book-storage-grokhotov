<?php

namespace App\Repository;

use App\Entity\AuthorEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuthorEntity>
 *
 * @method AuthorEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthorEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthorEntity[]    findAll()
 * @method AuthorEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthorEntity::class);
    }

//    /**
//     * @return AuthorEntity[] Returns an array of AuthorEntity objects
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

//    public function findOneBySomeField($value): ?AuthorEntity
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
