<?php

namespace App\Controller;

use App\Entity\Ajouter;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            return $this->redirectToRoute('app_login');
        }

        $panier = $this->getOrCreatePanier($user, $entityManager);
        $ligne = $this->findLine($panier, $produit);

        if ($produit->getStock() <= 0) {
            $this->addFlash('notice', 'Ce produit est en rupture de stock.');

            return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_mes_produits'));
        }

        if ($ligne !== null) {
            if ($ligne->getQuantite() >= $produit->getStock()) {
                $this->addFlash('notice', 'Stock maximum atteint pour ce produit.');
            } else {
                $ligne->setQuantite($ligne->getQuantite() + 1);
                $this->addFlash('notice', 'Quantité mise à jour dans le panier.');
            }
        } else {
            $ligne = (new Ajouter())
                ->setPanier($panier)
                ->setProduit($produit)
                ->setQuantite(1)
                ->setPrixHt($produit->getPrixUnitHT());

            $entityManager->persist($ligne);
            $this->addFlash('notice', 'Produit ajouté au panier.');
        }

        $this->refreshTotal($panier);
        $entityManager->persist($panier);
        $entityManager->flush();

        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_panier'));
    }

    #[Route('/private-liste-panier', name: 'app_panier')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $panier = $user->getPanier();

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
        EntityManagerInterface $entityManager,
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $panier = $this->getOrCreatePanier($user, $entityManager);
        $ligne = $this->findLine($panier, $produit);

        if ($ligne !== null && $ligne->getQuantite() < $produit->getStock()) {
            $ligne->setQuantite($ligne->getQuantite() + 1);
        }

        $this->refreshTotal($panier);
        $entityManager->flush();

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/private-panier-moins/{id}', name: 'app_panier_moins')]
    public function moins(
        Produit $produit,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $panier = $this->getOrCreatePanier($user, $entityManager);
        $ligne = $this->findLine($panier, $produit);

        if ($ligne !== null) {
            if ($ligne->getQuantite() > 1) {
                $ligne->setQuantite($ligne->getQuantite() - 1);
            } else {
                $entityManager->remove($ligne);
                $panier->removeAjouter($ligne);
            }
        }

        $this->refreshTotal($panier);
        $entityManager->flush();

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/private-panier-supprimer/{id}', name: 'app_panier_supprimer')]
    public function supprimer(
        Produit $produit,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
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
            $produit = $ligne->getProduit();

            if ($produit !== null) {
                $total += (float) $produit->getPrixUnitHT() * $ligne->getQuantite();
            }
        }

        $panier->setTotalHtPa(number_format($total, 2, '.', ''));
    }
}
