<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CheckoutController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function cart(): Response
    {
        return $this->redirectToRoute('app_checkout');
    }

    #[Route('/checkout', name: 'app_checkout')]
    public function index(): Response
    {
        return $this->render('checkout/index.html.twig');
    }
}
