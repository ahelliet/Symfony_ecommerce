<?php

namespace App\Repository;

use App\Entity\Annonce;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    /**
     * Renvoie les annonces correspondantes aux critères de recherche
     * Premier critère : Category
     * Seconde critère : Champ de recherche
     */
    public function findQueryResults(Category $category = null, $query = null)
    {
        $query_results =  $this->createQueryBuilder('a')
            ->where('a.id < 4');
        // $query_results->addOrderBy('a.datePublished', 'desc')
        //     ->leftJoin('a.category', 'category.name');
        // Si la catégorie est précisé on l'ajoute aux critères de recherche
        // if (null !== $category) {
        //     $query_results->where('a.title LIKE :q OR a.content LIKE :q')
        //         ->setParameter('q', '%' . $query . '%');
        // }

        // // Sinon on recherche uniquement par la query
        // if (null !== $query) {
        //     $query_results->where('a.title LIKE :q OR a.content LIKE :q')
        //         ->setParameter('q', '%' . $query . '%');
        // }

        $query_results->getQuery()->execute();
        return $query_results;
    }

    // /**
    //  * @return Annonce[] Returns an array of Annonce objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Annonce
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
