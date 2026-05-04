<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProduitController extends AbstractController
{
    #[Route('/catalogue', name: 'app_catalogue')]
    #[Route('/boutique', name: 'app_mes_produits')]
    public function index(
        Request $request,
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository,
    ): Response {
        $filters = $this->catalogueFilters($request);
        $user = $this->getUser();

        return $this->render('catalogue/index.html.twig', [
            'produits' => $produitRepository->search(
                null,
                $filters['selectedCategories'],
                $filters['sort'],
                $filters['maxPrice'],
                $filters['inStock']
            ),
            'categories' => $categorieRepository->findBy([], ['nomCategorie' => 'ASC']),
            'selectedCategories' => $filters['selectedCategories'],
            'sort' => $filters['sort'],
            'maxPrice' => $filters['maxPrice'],
            'inStock' => $filters['inStock'],
            'searchTerm' => null,
            'user' => $user instanceof User ? $user : null,
        ]);
    }

    #[Route('/recherche', name: 'app_resultat')]
    public function search(
        Request $request,
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository,
    ): Response {
        $mot = trim((string) $request->query->get('q', ''));
        $filters = $this->catalogueFilters($request);
        $user = $this->getUser();

        return $this->render('catalogue/index.html.twig', [
            'produits' => $mot === '' ? [] : $produitRepository->search(
                $mot,
                $filters['selectedCategories'],
                $filters['sort'],
                $filters['maxPrice'],
                $filters['inStock']
            ),
            'categories' => $categorieRepository->findBy([], ['nomCategorie' => 'ASC']),
            'selectedCategories' => $filters['selectedCategories'],
            'sort' => $filters['sort'],
            'maxPrice' => $filters['maxPrice'],
            'inStock' => $filters['inStock'],
            'searchTerm' => $mot,
            'user' => $user instanceof User ? $user : null,
        ]);
    }

    /**
     * @return array{selectedCategories: string[], sort: string, maxPrice: string, inStock: bool}
     */
    private function catalogueFilters(Request $request): array
    {
        $rawCategories = $request->query->all()['categories'] ?? [];
        if (!is_array($rawCategories)) {
            $rawCategories = [$rawCategories];
        }

        $selectedCategories = array_values(array_filter(
            array_map(static fn (mixed $id): string => trim((string) $id), $rawCategories),
            static fn (string $id): bool => $id !== ''
        ));

        $legacyCategory = $request->query->get('categorie');
        if ($selectedCategories === [] && $legacyCategory !== null && trim((string) $legacyCategory) !== '') {
            $selectedCategories = [trim((string) $legacyCategory)];
        }

        $sort = (string) $request->query->get('sort', 'newest');
        if (!in_array($sort, ['newest', 'name_asc', 'name_desc', 'price_asc', 'price_desc', 'stock_desc'], true)) {
            $sort = 'newest';
        }

        $maxPrice = (string) $request->query->get('max_price', '2000');
        if (!is_numeric($maxPrice)) {
            $maxPrice = '2000';
        }

        return [
            'selectedCategories' => $selectedCategories,
            'sort' => $sort,
            'maxPrice' => $maxPrice,
            'inStock' => $request->query->get('in_stock') === '1',
        ];
    }
}
