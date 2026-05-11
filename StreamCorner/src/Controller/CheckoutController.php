<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\Commande;
use App\Entity\Panier;
use App\Entity\Parvenir;
use App\Entity\Produit;
use App\Entity\User;
use App\Form\AdresseType;
use App\Form\CommandeType;
use Doctrine\DBAL\LockMode;
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

        if ($panier === null) {
            $this->addFlash('danger', 'Votre panier est vide.');

            return $this->redirectToRoute('app_panier');
        }

        if ($this->syncCartWithStock($panier, $entityManager)) {
            $this->refreshTotal($panier);
            $entityManager->flush();
            $this->addFlash('danger', 'Certaines quantités ont été ajustées selon le stock disponible.');

            return $this->redirectToRoute('app_panier');
        }

        $lignes = $panier->getAjouters()->toArray();

        if (count($lignes) === 0) {
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
            $connection = $entityManager->getConnection();
            $connection->beginTransaction();

            try {
                $stockIssue = $this->findStockIssueWithLocks($lignes, $entityManager);

                if ($stockIssue !== null) {
                    $connection->rollBack();
                    $this->addFlash('danger', $stockIssue);

                    return $this->redirectToRoute('app_panier');
                }

                $totalHt = $this->calculateTotalHt($lignes);
                $totalTaxe = round($totalHt * 0.20, 2);
                $totalTtc = round($totalHt + $totalTaxe, 2);

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

                    $produit->setStock($this->availableStock($produit) - $ligne->getQuantite());
                    $panier->removeAjouter($ligne);

                    $entityManager->persist($parvenir);
                    $entityManager->remove($ligne);
                }

                $panier->setTotalHtPa('0.00');
                $entityManager->flush();
                $connection->commit();
            } catch (\Throwable $exception) {
                if ($connection->isTransactionActive()) {
                    $connection->rollBack();
                }

                throw $exception;
            }

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

    private function syncCartWithStock(Panier $panier, EntityManagerInterface $entityManager): bool
    {
        $changed = false;

        foreach ($panier->getAjouters()->toArray() as $ligne) {
            $produit = $ligne->getProduit();

            if ($produit === null || $this->availableStock($produit) <= 0) {
                $panier->removeAjouter($ligne);
                $entityManager->remove($ligne);
                $changed = true;

                continue;
            }

            $availableStock = $this->availableStock($produit);

            if ($ligne->getQuantite() > $availableStock) {
                $ligne->setQuantite($availableStock);
                $changed = true;
            }
        }

        return $changed;
    }

    /**
     * @param array<int, mixed> $lignes
     */
    private function findStockIssueWithLocks(array $lignes, EntityManagerInterface $entityManager): ?string
    {
        foreach ($lignes as $ligne) {
            $produit = $ligne->getProduit();

            if ($produit === null) {
                return 'Un produit du panier n’est plus disponible.';
            }

            $entityManager->refresh($produit, LockMode::PESSIMISTIC_WRITE);

            if ($ligne->getQuantite() <= 0) {
                return sprintf('La quantité de %s est invalide.', $produit->getDesignation());
            }

            if ($this->availableStock($produit) < $ligne->getQuantite()) {
                return sprintf(
                    'Stock insuffisant pour %s : %d unité(s) disponible(s).',
                    $produit->getDesignation(),
                    $this->availableStock($produit),
                );
            }
        }

        return null;
    }

    private function availableStock(Produit $produit): int
    {
        return max(0, $produit->getStock() ?? 0);
    }

    private function refreshTotal(Panier $panier): void
    {
        $panier->setTotalHtPa(number_format($this->calculateTotalHt($panier->getAjouters()->toArray()), 2, '.', ''));
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
