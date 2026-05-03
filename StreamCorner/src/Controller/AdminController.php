<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard')]
    public function dashboard(ProduitRepository $produitRepository): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'produits_count' => $produitRepository->count([]),
        ]);
    }

    #[Route('/inventaire', name: 'app_admin_inventory')]
    public function inventory(ProduitRepository $produitRepository): Response
    {
        return $this->render('admin/inventory.html.twig', [
            'produits' => $produitRepository->findBy([], ['designation' => 'ASC']),
        ]);
    }
}
