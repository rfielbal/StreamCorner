<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Sav;
use App\Entity\User;
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
            'savForm' => $form->createView(),
        ]);
    }
}
