<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * @return Produit[]
     */
    public function search(?string $term = null, ?string $categorieId = null, ?string $sort = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.categorie', 'c')
            ->addSelect('c');

        if ($term !== null && trim($term) !== '') {
            $qb
                ->andWhere('LOWER(p.designation) LIKE :term OR LOWER(p.description) LIKE :term OR LOWER(c.nomCategorie) LIKE :term')
                ->setParameter('term', '%' . strtolower(trim($term)) . '%');
        }

        if ($categorieId !== null && $categorieId !== '') {
            $qb
                ->andWhere('c.id = :categorie')
                ->setParameter('categorie', $categorieId);
        }

        if ($sort === 'price_asc') {
            $qb->orderBy('p.prixUnitHT', 'ASC');
        } elseif ($sort === 'price_desc') {
            $qb->orderBy('p.prixUnitHT', 'DESC');
        } else {
            $qb->orderBy('p.designation', 'ASC');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Produit[]
     */
    public function recherche(string $value): array
    {
        return $this->search($value);
    }

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
