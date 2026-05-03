<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FavorisController extends AbstractController
{
    #[Route('/private-favoris/{id}', name: 'app_favoris')]
    public function toggle(
        Produit $produit,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($user->getProduitsAimers()->contains($produit)) {
            $user->removeProduitsAimer($produit);
            $this->addFlash('notice', 'Produit retiré des favoris.');
        } else {
            $user->addProduitsAimer($produit);
            $this->addFlash('notice', 'Produit ajouté aux favoris.');
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_mes_produits'));
    }

    #[Route('/private-liste-favoris', name: 'app_liste_favoris')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('favoris/index.html.twig', [
            'produits' => $user->getProduitsAimers(),
            'user' => $user,
        ]);
    }
}
