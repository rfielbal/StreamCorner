<?php

namespace App\Controller;

use App\Entity\Noter;
use App\Entity\Produit;
use App\Entity\User;
use App\Form\NoterType;
use App\Repository\NoterRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CatalogueController extends AbstractController
{
    #[Route('/produit', name: 'app_produit_demo')]
    public function demo(): Response
    {
        return $this->redirectToRoute('app_catalogue');
    }

    #[Route('/produit/{id<\d+>}', name: 'app_produit_show', methods: ['GET', 'POST'])]
    public function show(
        Produit $produit,
        Request $request,
        EntityManagerInterface $entityManager,
        NoterRepository $noterRepository,
    ): Response {
        $user = $this->getUser();
        $reviewFormView = null;
        $userReview = null;

        if ($user instanceof User) {
            $userReview = $noterRepository->findOneBy([
                'user' => $user,
                'produit' => $produit,
            ]);

            if ($userReview === null) {
                $noter = new Noter();
                $form = $this->createForm(NoterType::class, $noter);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $noter
                        ->setUser($user)
                        ->setProduit($produit)
                        ->setDateMessage(new \DateTime());

                    try {
                        $entityManager->persist($noter);
                        $entityManager->flush();
                        $this->addFlash('success', 'Avis enregistré.');
                    } catch (UniqueConstraintViolationException) {
                        $this->addFlash('danger', 'Vous avez déjà donné un avis sur ce produit.');
                    }

                    return $this->redirectToRoute('app_produit_show', ['id' => $produit->getId()]);
                }

                $reviewFormView = $form->createView();
            }
        }

        return $this->render('catalogue/show.html.twig', [
            'produit' => $produit,
            'demo_mode' => false,
            'noterForm' => $reviewFormView,
            'userReview' => $userReview,
        ]);
    }
}
