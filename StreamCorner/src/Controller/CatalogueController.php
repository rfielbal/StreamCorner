<?php

namespace App\Controller;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CatalogueController extends AbstractController
{
    #[Route('/produit', name: 'app_produit_demo')]
    public function demo(): Response
    {
        return $this->render('catalogue/show.html.twig', [
            'produit' => null,
            'demo_mode' => true,
        ]);
    }

    #[Route('/produit/{id<\d+>}', name: 'app_produit_show')]
    public function show(Produit $produit): Response
    {
        return $this->render('catalogue/show.html.twig', [
            'produit' => $produit,
            'demo_mode' => false,
        ]);
    }
}
