<?php

namespace App\Repository;

use App\Entity\Purchase;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Purchase>
 *
 * @method Purchase|null find($id, $lockMode = null, $lockVersion = null)
 * @method Purchase|null findOneBy(array $criteria, array $orderBy = null)
 * @method Purchase[]    findAll()
 * @method Purchase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    public function add(Purchase $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Purchase $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    //Loisir
    public function findSumOfFirstTypebyid($type): array
    {

        $qb = $this->createQueryBuilder('p')
            ->select("sum(p.how) as loisir, p.who_id as qui")
            ->where('p.type = :type')
            ->setParameter('type', $type)
            ->groupBy('p.who_id');

        $query = $qb->getQuery();

        return $query->execute();

    }

    //Salaire
    public function findSumOfSecondTypebyid($type): array
    {

        $qb = $this->createQueryBuilder('p')
            ->select("sum(p.how) as salaire, p.who_id as qui")
            ->where('p.type = :type')
            ->setParameter('type', $type)
            ->groupBy('p.who_id');

        $query = $qb->getQuery();

        return $query->execute();

    }

    public function findSumOfAll():array
    {
        $qb = $this->createQueryBuilder('p')
            ->select("sum(p.how) total, p.who_id as qui")
            ->groupBy('p.who_id');

        $query = $qb->getQuery();

        return $query->execute();
    }

//    /**
//     * @return Purchase[] Returns an array of Purchase objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Purchase
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
