<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/private-profil')]
final class ProfileController extends AbstractController
{
    #[Route('', name: 'app_profil', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $commandes = $user->getCommandes()->toArray();
        usort(
            $commandes,
            static fn (Commande $left, Commande $right): int =>
                ($right->getDateCommande()?->getTimestamp() ?? 0) <=> ($left->getDateCommande()?->getTimestamp() ?? 0)
        );

        $panier = $user->getPanier();
        $lignesPanier = $panier?->getAjouters() ?? [];
        $articlesPanier = 0;

        foreach ($lignesPanier as $ligne) {
            $articlesPanier += $ligne->getQuantite() ?? 0;
        }

        $profileName = trim(sprintf('%s %s', $user->getPrenom() ?? '', $user->getNom() ?? ''));

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'profile_name' => $profileName !== '' ? $profileName : $user->getUserIdentifier(),
            'commandes' => $commandes,
            'commandes_recentes' => array_slice($commandes, 0, 3),
            'adresses' => $user->getAdresses(),
            'adresse_principale' => $user->getAdresses()->first() ?: null,
            'favoris' => $user->getProduitsAimers(),
            'panier' => $panier,
            'articles_panier' => $articlesPanier,
        ]);
    }
}
