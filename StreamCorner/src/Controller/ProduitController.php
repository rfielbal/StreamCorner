<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Service\UtilisateurContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProduitController extends AbstractController
{
    #[Route('/catalogue', name: 'app_catalogue')]
    #[Route('/boutique', name: 'app_mes_produits')]
    public function index(
        Request $request,
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository,
        UtilisateurContext $utilisateurContext,
    ): Response {
        $sort = $request->query->get('sort');
        $categorie = $request->query->get('categorie');

        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->search(null, $categorie, $sort),
            'categories' => $categorieRepository->findBy([], ['nomCategorie' => 'ASC']),
            'selectedCategorie' => $categorie,
            'sort' => $sort,
            'searchTerm' => null,
            'utilisateur' => $utilisateurContext->getUtilisateur(false),
        ]);
    }

    #[Route('/recherche', name: 'app_resultat')]
    public function search(
        Request $request,
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository,
        UtilisateurContext $utilisateurContext,
    ): Response {
        $mot = trim((string) $request->query->get('q', ''));
        $sort = $request->query->get('sort');

        return $this->render('produit/index.html.twig', [
            'produits' => $mot === '' ? [] : $produitRepository->search($mot, null, $sort),
            'categories' => $categorieRepository->findBy([], ['nomCategorie' => 'ASC']),
            'selectedCategorie' => null,
            'sort' => $sort,
            'searchTerm' => $mot,
            'utilisateur' => $utilisateurContext->getUtilisateur(false),
        ]);
    }
}
