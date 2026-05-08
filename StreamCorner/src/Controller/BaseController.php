<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BaseController extends AbstractController
{
    private const SETUP_PRODUCT_NAMES = [
        'Command Center',
        'Obsidian Stream Webcam',
        'Aether Pro LED Panels',
        'Pulse Mic',
        'Vox Monitor Speaker',
        'Kraken TKL Keyboard',
    ];

    #[Route('/', name: 'app_accueil')]
    public function index(ProduitRepository $produitRepository): Response
    {
        $setupProducts = [];
        foreach ($produitRepository->findBy(['designation' => self::SETUP_PRODUCT_NAMES]) as $produit) {
            $setupProducts[$produit->getDesignation()] = $produit;
        }

        return $this->render('home/index.html.twig', [
            'featuredProducts' => $produitRepository->findBy([], ['id' => 'DESC'], 4),
            'setupProducts' => $setupProducts,
        ]);
    }
}
