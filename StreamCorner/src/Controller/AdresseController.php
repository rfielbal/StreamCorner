<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\User;
use App\Form\AdresseType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/private-adresses')]
final class AdresseController extends AbstractController
{
    #[Route('', name: 'app_adresse_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('adresse/index.html.twig', [
            'adresses' => $user->getAdresses(),
        ]);
    }

    #[Route('/nouvelle', name: 'app_adresse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $adresse = new Adresse();
        $form = $this->createForm(AdresseType::class, $adresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adresse->setUser($user);
            $entityManager->persist($adresse);
            $entityManager->flush();
            $this->addFlash('success', 'Adresse enregistrée.');

            return $this->redirectToRoute('app_adresse_index');
        }

        return $this->render('form/page.html.twig', [
            'title' => 'Nouvelle_Adresse',
            'kicker' => 'Espace client',
            'form' => $form->createView(),
            'submit_label' => 'Envoyer',
            'back_path' => $this->generateUrl('app_adresse_index'),
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_adresse_edit', methods: ['GET', 'POST'])]
    public function edit(Adresse $adresse, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($adresse->getUser()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(AdresseType::class, $adresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Adresse modifiée.');

            return $this->redirectToRoute('app_adresse_index');
        }

        return $this->render('form/page.html.twig', [
            'title' => 'Modifier_Adresse',
            'kicker' => 'Espace client',
            'form' => $form->createView(),
            'submit_label' => 'Envoyer',
            'back_path' => $this->generateUrl('app_adresse_index'),
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_adresse_delete', methods: ['POST'])]
    public function delete(Adresse $adresse, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($adresse->getUser()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete-adresse-' . $adresse->getId(), (string) $request->request->get('_token'))) {
            try {
                $entityManager->remove($adresse);
                $entityManager->flush();
                $this->addFlash('success', 'Adresse supprimée.');
            } catch (ForeignKeyConstraintViolationException) {
                $this->addFlash('danger', 'Cette adresse est utilisée par une commande et ne peut plus être supprimée.');
            }
        }

        return $this->redirectToRoute('app_adresse_index');
    }
}
