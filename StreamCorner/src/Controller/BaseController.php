<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BaseController extends AbstractController
{
    private const SETUP_PRODUCT_NAMES = [
        'Nexus Stream Deck',
        'Obsidian Webcam',
        'Aether Pro LED Panels',
        'Pulse MIC',
        'Vox Monitor Speaker',
        'Cobalt Stream Deck',
    ];

    private const UNIVERSE_PRODUCT_NAMES = [
        'Vector Boom Arm',
        'Hyper Capture Card 4K',
        'All-In Monitor Audio',
    ];

    private const FEATURED_PRODUCT_NAMES = [
        'Nexus Stream Deck',
        'Pulse MIC',
        'Aether Pro LED Panels',
        'Obsidian Webcam',
    ];

    #[Route('/', name: 'app_accueil')]
    public function index(ProduitRepository $produitRepository): Response
    {
        $setupProducts = $this->findProductsByName($produitRepository, self::SETUP_PRODUCT_NAMES);
        $universeProducts = $this->findProductsByName($produitRepository, self::UNIVERSE_PRODUCT_NAMES);
        $featuredProducts = $this->productsInOrder(
            $this->findProductsByName($produitRepository, self::FEATURED_PRODUCT_NAMES),
            self::FEATURED_PRODUCT_NAMES
        );

        return $this->render('home/index.html.twig', [
            'featuredProducts' => $featuredProducts,
            'setupProducts' => $setupProducts,
            'universeProducts' => $universeProducts,
        ]);
    }

    /**
     * @param string[] $names
     *
     * @return array<string, Produit>
     */
    private function findProductsByName(ProduitRepository $produitRepository, array $names): array
    {
        $products = [];
        foreach ($produitRepository->findBy(['designation' => $names]) as $produit) {
            $products[$produit->getDesignation()] = $produit;
        }

        return $products;
    }

    /**
     * @param array<string, Produit> $products
     * @param string[] $names
     *
     * @return Produit[]
     */
    private function productsInOrder(array $products, array $names): array
    {
        $orderedProducts = [];
        foreach ($names as $name) {
            if (isset($products[$name])) {
                $orderedProducts[] = $products[$name];
            }
        }

        return $orderedProducts;
    }
}
