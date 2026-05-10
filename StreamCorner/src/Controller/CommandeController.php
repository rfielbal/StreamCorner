<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Sav;
use App\Entity\User;
use App\Form\CommandeType;
use App\Form\SavType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/private-commandes')]
final class CommandeController extends AbstractController
{
    #[Route('', name: 'app_commande_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('commande/index.html.twig', [
            'commandes' => $user->getCommandes(),
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET', 'POST'])]
    public function show(Commande $commande, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($commande->getUser()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        $addressForm = $this->createForm(CommandeType::class, $commande, [
            'adresses' => $user->getAdresses()->toArray(),
            'action' => $this->generateUrl('app_commande_update_address', ['id' => $commande->getId()]),
            'method' => 'POST',
        ]);

        $sav = new Sav();
        $form = $this->createForm(SavType::class, $sav);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sav
                ->setCommande($commande)
                ->setDateMessage(new \DateTime())
                ->setTraitement('Nouveau');

            $entityManager->persist($sav);
            $entityManager->flush();
            $this->addFlash('success', 'Message SAV envoyé.');

            return $this->redirectToRoute('app_commande_show', ['id' => $commande->getId()]);
        }

        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
            'addressForm' => $addressForm->createView(),
            'savForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}/adresse', name: 'app_commande_update_address', methods: ['POST'])]
    public function updateAddress(Commande $commande, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($commande->getUser()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(CommandeType::class, $commande, [
            'adresses' => $user->getAdresses()->toArray(),
        ]);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('danger', 'Adresse de livraison invalide.');

            return $this->redirectToRoute('app_commande_show', ['id' => $commande->getId()]);
        }

        if ($commande->getAdresse()?->getUser()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        $entityManager->flush();
        $this->addFlash('success', 'Adresse de livraison mise à jour.');

        return $this->redirectToRoute('app_commande_show', ['id' => $commande->getId()]);
    }

    #[Route('/{id}/annuler', name: 'app_commande_cancel', methods: ['POST'])]
    public function cancel(Commande $commande, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($commande->getUser()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('cancel-commande-' . $commande->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Annulation impossible : jeton de sécurité invalide.');

            return $this->redirectToRoute('app_commande_show', ['id' => $commande->getId()]);
        }

        $connection = $entityManager->getConnection();
        $connection->beginTransaction();

        try {
            foreach ($commande->getSavs()->toArray() as $sav) {
                $entityManager->remove($sav);
            }

            foreach ($commande->getParvenirs()->toArray() as $ligne) {
                $produit = $ligne->getProduit();

                if ($produit !== null) {
                    $produit->setStock(($produit->getStock() ?? 0) + ($ligne->getQuantite() ?? 0));
                }

                $entityManager->remove($ligne);
            }

            $entityManager->remove($commande);
            $entityManager->flush();
            $connection->commit();
        } catch (\Throwable $exception) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }

            throw $exception;
        }

        $this->addFlash('success', 'Commande annulée. Les produits ont été remis en stock.');

        return $this->redirectToRoute('app_commande_index');
    }
}
