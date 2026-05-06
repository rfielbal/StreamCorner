<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\Commande;
use App\Entity\Parvenir;
use App\Entity\User;
use App\Form\AdresseType;
use App\Form\CommandeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CheckoutController extends AbstractController
{
    #[Route('/panier', name: 'app_panier_public')]
    public function cart(): Response
    {
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/checkout', name: 'app_checkout', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $panier = $user->getPanier();
        $lignes = $panier?->getAjouters()->toArray() ?? [];

        if ($panier === null || count($lignes) === 0) {
            $this->addFlash('danger', 'Votre panier est vide.');

            return $this->redirectToRoute('app_panier');
        }

        $adresses = $user->getAdresses()->toArray();

        $adresse = new Adresse();
        $addressForm = $this->createForm(AdresseType::class, $adresse);
        $addressForm->handleRequest($request);

        if ($addressForm->isSubmitted() && $addressForm->isValid()) {
            $adresse->setUser($user);

            $entityManager->persist($adresse);
            $entityManager->flush();

            $this->addFlash('success', 'Adresse enregistrée.');

            return $this->redirectToRoute('app_checkout');
        }

        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande, [
            'adresses' => $adresses,
        ]);
        $form->handleRequest($request);

        $totalHt = $this->calculateTotalHt($lignes);
        $totalTaxe = round($totalHt * 0.20, 2);
        $totalTtc = round($totalHt + $totalTaxe, 2);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($lignes as $ligne) {
                $produit = $ligne->getProduit();

                if ($produit === null || $produit->getStock() < $ligne->getQuantite()) {
                    $this->addFlash('danger', 'Le stock a changé pour un produit du panier.');

                    return $this->redirectToRoute('app_panier');
                }
            }

            $commande
                ->setUser($user)
                ->setDateCommande(new \DateTime())
                ->setTotalHtCo(number_format($totalHt, 2, '.', ''))
                ->setTotalTaxe(number_format($totalTaxe, 2, '.', ''))
                ->setTotal(number_format($totalTtc, 2, '.', ''));

            $entityManager->persist($commande);

            foreach ($lignes as $ligne) {
                $produit = $ligne->getProduit();

                $parvenir = (new Parvenir())
                    ->setCommande($commande)
                    ->setProduit($produit)
                    ->setQuantite($ligne->getQuantite())
                    ->setPrixHt($ligne->getPrixHt());

                $produit->setStock($produit->getStock() - $ligne->getQuantite());
                $panier->removeAjouter($ligne);

                $entityManager->persist($parvenir);
                $entityManager->remove($ligne);
            }

            $panier->setTotalHtPa('0.00');
            $entityManager->flush();
            $this->addFlash('success', 'Commande créée.');

            return $this->redirectToRoute('app_commande_show', ['id' => $commande->getId()]);
        }

        return $this->render('checkout/index.html.twig', [
            'form' => $form->createView(),
            'address_form' => $addressForm->createView(),
            'has_adresses' => count($adresses) > 0,
            'lignes' => $lignes,
            'totalHt' => $totalHt,
            'totalTaxe' => $totalTaxe,
            'totalTtc' => $totalTtc,
        ]);
    }

    /**
     * @param array<int, mixed> $lignes
     */
    private function calculateTotalHt(array $lignes): float
    {
        $total = 0.0;

        foreach ($lignes as $ligne) {
            $total += (float) $ligne->getPrixHt() * $ligne->getQuantite();
        }

        return round($total, 2);
    }
}
