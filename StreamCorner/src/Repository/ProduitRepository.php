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
     * @param string[]|string|null $categorieIds
     *
     * @return Produit[]
     */
    public function search(
        ?string $term = null,
        array|string|null $categorieIds = null,
        ?string $sort = null,
        ?string $maxPrice = null,
        bool $inStock = false,
    ): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.categorie', 'c')
            ->addSelect('c');

        if ($term !== null && trim($term) !== '') {
            $qb
                ->andWhere('LOWER(p.designation) LIKE :term OR LOWER(p.description) LIKE :term OR LOWER(c.nomCategorie) LIKE :term')
                ->setParameter('term', '%' . strtolower(trim($term)) . '%');
        }

        $selectedCategories = is_array($categorieIds) ? $categorieIds : [$categorieIds];
        $selectedCategories = array_values(array_filter(
            array_map(static fn (mixed $id): string => trim((string) $id), $selectedCategories),
            static fn (string $id): bool => $id !== ''
        ));

        if ($selectedCategories !== []) {
            $categoryIds = [];
            $categorySlugs = [];

            foreach ($selectedCategories as $category) {
                if (ctype_digit($category)) {
                    $categoryIds[] = $category;
                } else {
                    $categorySlugs[] = $this->normalizeCategorySlug($category);
                }
            }

            $categoryConditions = [];

            if ($categoryIds !== []) {
                $categoryConditions[] = 'c.id IN (:categoryIds)';
                $qb->setParameter('categoryIds', $categoryIds);
            }

            foreach (array_unique($categorySlugs) as $index => $slug) {
                $aliases = $this->categoryAliases($slug);
                if ($aliases === []) {
                    continue;
                }

                $aliasConditions = [];
                foreach ($aliases as $aliasIndex => $alias) {
                    $parameter = sprintf('categorySlug_%d_%d', $index, $aliasIndex);
                    $aliasConditions[] = sprintf('LOWER(c.nomCategorie) LIKE :%s', $parameter);
                    $qb->setParameter($parameter, '%' . $alias . '%');
                }

                $categoryConditions[] = '(' . implode(' OR ', $aliasConditions) . ')';
            }

            if ($categoryConditions !== []) {
                $qb->andWhere('(' . implode(' OR ', $categoryConditions) . ')');
            } else {
                $qb->andWhere('1 = 0');
            }
        }

        if ($maxPrice !== null && is_numeric($maxPrice) && (float) $maxPrice < 2000.0) {
            $qb
                ->andWhere('p.prixUnitHT <= :maxPrice')
                ->setParameter('maxPrice', number_format((float) $maxPrice, 2, '.', ''));
        }

        if ($inStock) {
            $qb->andWhere('p.stock > 0');
        }

        if ($sort === 'price_asc') {
            $qb->orderBy('p.prixUnitHT', 'ASC');
        } elseif ($sort === 'price_desc') {
            $qb->orderBy('p.prixUnitHT', 'DESC');
        } elseif ($sort === 'name_asc') {
            $qb->orderBy('p.designation', 'ASC');
        } elseif ($sort === 'name_desc') {
            $qb->orderBy('p.designation', 'DESC');
        } elseif ($sort === 'stock_desc') {
            $qb->orderBy('p.stock', 'DESC')
                ->addOrderBy('p.designation', 'ASC');
        } else {
            $qb->orderBy('p.id', 'DESC');
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

    private function normalizeCategorySlug(string $value): string
    {
        $normalized = strtolower(trim($value));
        $normalized = str_replace(['é', 'è', 'ê', 'ë'], 'e', $normalized);
        $normalized = str_replace(['à', 'â', 'ä'], 'a', $normalized);
        $normalized = str_replace(['î', 'ï'], 'i', $normalized);
        $normalized = str_replace(['ô', 'ö'], 'o', $normalized);
        $normalized = str_replace(['ù', 'û', 'ü'], 'u', $normalized);
        $normalized = str_replace(['ç'], 'c', $normalized);

        return preg_replace('/[^a-z0-9]+/', '', $normalized) ?? $normalized;
    }

    /**
     * @return string[]
     */
    private function categoryAliases(string $slug): array
    {
        return match ($slug) {
            'audio' => ['audio'],
            'eclairage' => ['eclairage', 'éclairage', 'light'],
            'diffusion', 'video' => ['diffusion', 'video', 'vidéo'],
            default => [$slug],
        };
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
