<?php

namespace App\Repository;

use App\Entity\Purchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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
    /**
     */
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




    //loisir par mois

    public function findSumOfTypeOnMonthById($dateStart, $dateEnd, $type, $user): ?array
    {
        $qb = $this->createQueryBuilder('p')
            ->select("sum(p.how) as loisir, p.who_id as id")
            ->where('p.type = :type')
            ->andWhere('p.date > :dateStart')
            ->andWhere('p.date < :dateEnd')
            ->andWhere('p.who_id = :id')
            ->setParameter('id', $user)
            ->setParameter('type', $type)
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd)
            ->getQuery()
            ->getArrayResult();


        return $qb;
    }

    //Salaire

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findSumOfSecondTypebyid($type, $user): ?array
    {

       $qb = $this->createQueryBuilder('p')
            ->select("sum(p.how) as salaire, p.who_id as id")
            ->where('p.type = :type')
            ->andWhere('p.who_id = :id')
            ->setParameter('id', $user)
            ->setParameter('type', $type)
            ->getQuery()
            ->getArrayResult();


        return $qb;

    }


    //Total
    public function findSumOfAll($user): ?array
    {

        $qb = $this->createQueryBuilder('p')
            ->select("sum(p.how) total, p.who_id as id")
            ->Where('p.who_id = :id')
            ->setParameter('id', $user)
            ->getQuery()
            ->getArrayResult();

        $datas = $qb;

        return $datas;
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
