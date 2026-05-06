<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            if ($this->isAjaxRequest($request)) {
                return new JsonResponse(['redirect' => $this->generateUrl('app_login')], Response::HTTP_UNAUTHORIZED);
            }

            return $this->redirectToRoute('app_login');
        }

        if ($user->getProduitsAimers()->contains($produit)) {
            $user->removeProduitsAimer($produit);
            $isFavorite = false;
        } else {
            $user->addProduitsAimer($produit);
            $isFavorite = true;
        }

        $entityManager->persist($user);
        $entityManager->flush();

        if ($this->isAjaxRequest($request)) {
            return new JsonResponse([
                'favorited' => $isFavorite,
                'favoritesCount' => $user->getProduitsAimers()->count(),
                'label' => $isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris',
                'productId' => $produit->getId(),
            ]);
        }

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

    private function isAjaxRequest(Request $request): bool
    {
        return $request->isXmlHttpRequest() || str_contains((string) $request->headers->get('Accept'), 'application/json');
    }
}
