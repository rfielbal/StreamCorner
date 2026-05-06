<?php

namespace App\Controller;

use App\Entity\Ajouter;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PanierController extends AbstractController
{
    #[Route('/private-panier/{id}', name: 'app_panier_add')]
    public function add(
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

        $panier = $this->getOrCreatePanier($user, $entityManager);
        $ligne = $this->findLine($panier, $produit);

        if ($produit->getStock() <= 0) {
            if ($this->isAjaxRequest($request)) {
                return $this->cartJson($panier, $produit, $ligne, 'Ce produit est en rupture de stock.', Response::HTTP_CONFLICT);
            }

            return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_mes_produits'));
        }

        if ($ligne !== null) {
            if ($ligne->getQuantite() >= $produit->getStock()) {
                if ($this->isAjaxRequest($request)) {
                    return $this->cartJson($panier, $produit, $ligne, 'Stock maximum atteint pour ce produit.', Response::HTTP_CONFLICT);
                }
            } else {
                $ligne->setQuantite($ligne->getQuantite() + 1);
            }
        } else {
            $ligne = (new Ajouter())
                ->setProduit($produit)
                ->setQuantite(1)
                ->setPrixHt($produit->getPrixUnitHT());
            $panier->addAjouter($ligne);

            $entityManager->persist($ligne);
        }

        $this->refreshTotal($panier);
        $entityManager->persist($panier);
        $entityManager->flush();

        if ($this->isAjaxRequest($request)) {
            return $this->cartJson($panier, $produit, $ligne);
        }

        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_panier'));
    }

    #[Route('/private-liste-panier', name: 'app_panier')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $panier = $user->getPanier();

        if ($panier !== null) {
            $this->refreshTotal($panier);
            $entityManager->flush();
        }

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
            'lignes' => $panier?->getAjouters() ?? [],
            'total' => $panier?->getTotalHtPa() ?? '0.00',
            'user' => $user,
        ]);
    }

    #[Route('/private-panier-plus/{id}', name: 'app_panier_plus')]
    public function plus(
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

        $panier = $this->getOrCreatePanier($user, $entityManager);
        $ligne = $this->findLine($panier, $produit);

        if ($ligne !== null && $ligne->getQuantite() < $produit->getStock()) {
            $ligne->setQuantite($ligne->getQuantite() + 1);
        } elseif ($ligne !== null && $this->isAjaxRequest($request)) {
            return $this->cartJson($panier, $produit, $ligne, 'Stock maximum atteint pour ce produit.', Response::HTTP_CONFLICT);
        }

        $this->refreshTotal($panier);
        $entityManager->flush();

        if ($this->isAjaxRequest($request)) {
            return $this->cartJson($panier, $produit, $ligne);
        }

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/private-panier-moins/{id}', name: 'app_panier_moins')]
    public function moins(
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

        $panier = $this->getOrCreatePanier($user, $entityManager);
        $ligne = $this->findLine($panier, $produit);
        $removed = false;

        if ($ligne !== null) {
            if ($ligne->getQuantite() > 1) {
                $ligne->setQuantite($ligne->getQuantite() - 1);
            } else {
                $entityManager->remove($ligne);
                $panier->removeAjouter($ligne);
                $removed = true;
            }
        }

        $this->refreshTotal($panier);
        $entityManager->flush();

        if ($this->isAjaxRequest($request)) {
            return $this->cartJson($panier, $produit, $ligne, null, Response::HTTP_OK, $removed);
        }

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/private-panier-supprimer/{id}', name: 'app_panier_supprimer')]
    public function supprimer(
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

        $panier = $this->getOrCreatePanier($user, $entityManager);
        $ligne = $this->findLine($panier, $produit);

        if ($ligne !== null) {
            $entityManager->remove($ligne);
            $panier->removeAjouter($ligne);
        }

        $this->refreshTotal($panier);
        $entityManager->flush();

        if ($this->isAjaxRequest($request)) {
            return $this->cartJson($panier, $produit, $ligne, null, Response::HTTP_OK, true);
        }

        return $this->redirectToRoute('app_panier');
    }

    private function getOrCreatePanier(User $user, EntityManagerInterface $entityManager): Panier
    {
        $panier = $user->getPanier();

        if ($panier !== null) {
            return $panier;
        }

        $panier = (new Panier())->setTotalHtPa('0.00');
        $user->setPanier($panier);

        $entityManager->persist($user);
        $entityManager->persist($panier);

        return $panier;
    }

    private function findLine(Panier $panier, Produit $produit): ?Ajouter
    {
        foreach ($panier->getAjouters() as $ligne) {
            if ($ligne->getProduit()?->getId() === $produit->getId()) {
                return $ligne;
            }
        }

        return null;
    }

    private function refreshTotal(Panier $panier): void
    {
        $total = 0.0;

        foreach ($panier->getAjouters() as $ligne) {
            $total += (float) $ligne->getPrixHt() * $ligne->getQuantite();
        }

        $panier->setTotalHtPa(number_format($total, 2, '.', ''));
    }

    private function isAjaxRequest(Request $request): bool
    {
        return $request->isXmlHttpRequest() || str_contains((string) $request->headers->get('Accept'), 'application/json');
    }

    private function cartJson(
        Panier $panier,
        Produit $produit,
        ?Ajouter $ligne,
        ?string $message = null,
        int $status = Response::HTTP_OK,
        bool $removed = false,
    ): JsonResponse {
        $itemsCount = 0;

        foreach ($panier->getAjouters() as $cartLine) {
            $itemsCount += $cartLine->getQuantite();
        }

        $totalHt = (float) $panier->getTotalHtPa();
        $lineQuantity = $removed ? 0 : ($ligne?->getQuantite() ?? 0);
        $lineUnitPrice = $ligne !== null ? (float) $ligne->getPrixHt() : (float) $produit->getPrixUnitHT();
        $lineTotal = $lineQuantity * $lineUnitPrice;

        return new JsonResponse([
            'message' => $message,
            'productId' => $produit->getId(),
            'removed' => $removed,
            'line' => [
                'quantity' => $lineQuantity,
                'unitPrice' => $this->formatMoney($lineUnitPrice),
                'total' => $this->formatMoney($lineTotal),
            ],
            'cart' => [
                'itemsCount' => $itemsCount,
                'linesCount' => $panier->getAjouters()->count(),
                'totalHt' => $this->formatMoney($totalHt),
                'tax' => $this->formatMoney($totalHt * 0.2),
                'totalTtc' => $this->formatMoney($totalHt * 1.2),
                'isEmpty' => $panier->getAjouters()->count() === 0,
            ],
        ], $status);
    }

    private function formatMoney(float $amount): string
    {
        return number_format($amount, 2, ',', ' ') . ' EUR';
    }
}
