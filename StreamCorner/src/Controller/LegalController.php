<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegalController extends AbstractController
{
    #[Route('/conditions-generales-utilisation', name: 'app_legal_cgu')]
    public function cgu(): Response
    {
        return $this->render('legal/page.html.twig', [
            'title' => 'CGU',
            'headline' => 'Conditions_Generales_Utilisation',
            'kicker' => 'Accès et utilisation du site',
            'updated_at' => '6 mai 2026',
            'intro' => 'Ces conditions encadrent l’utilisation de StreamCorner, site e-commerce fictif réalisé dans un cadre académique BTS SIO. Elles sont rédigées comme un support pédagogique et doivent être adaptées avant toute mise en production réelle.',
            'sections' => [
                [
                    'title' => 'Objet du site',
                    'content' => [
                        'StreamCorner présente un catalogue de matériel destiné aux créateurs de contenu, streamers et utilisateurs de setups audio/vidéo.',
                        'Le site permet notamment de consulter des produits, rechercher dans le catalogue, gérer un panier, des favoris, des commandes de démonstration, un profil client et des demandes de contact ou de SAV.',
                    ],
                ],
                [
                    'title' => 'Accès au service',
                    'content' => [
                        'La navigation publique est accessible sans compte. Certaines fonctionnalités, comme le panier persistant, les favoris, le profil, les adresses ou les commandes, peuvent nécessiter une authentification.',
                        'L’espace d’administration est réservé aux profils autorisés. Toute tentative d’accès non autorisée à cet espace est interdite.',
                    ],
                ],
                [
                    'title' => 'Compte utilisateur',
                    'content' => [
                        'L’utilisateur s’engage à fournir des informations exactes lors de son inscription et à préserver la confidentialité de ses identifiants.',
                        'Dans le cadre académique du projet, les comptes et commandes servent uniquement à démontrer le fonctionnement applicatif.',
                    ],
                ],
                [
                    'title' => 'Comportements interdits',
                    'content' => [
                        'Il est interdit d’utiliser le site pour perturber son fonctionnement, contourner les contrôles d’accès, extraire massivement des données ou déposer des contenus illicites dans les formulaires.',
                        'Les messages transmis via le contact, les avis ou le SAV doivent rester liés à l’objet du site.',
                    ],
                ],
                [
                    'title' => 'Responsabilité',
                    'content' => [
                        'StreamCorner étant un projet académique, les informations, prix, stocks, commandes et services affichés sont fournis à titre de démonstration.',
                        'Aucune vente réelle, livraison réelle ou prestation commerciale effective n’est engagée par la consultation ou l’utilisation de cette version du site.',
                    ],
                ],
            ],
        ]);
    }

    #[Route('/conditions-generales-vente', name: 'app_legal_cgv')]
    public function cgv(): Response
    {
        return $this->render('legal/page.html.twig', [
            'title' => 'CGV',
            'headline' => 'Conditions_Generales_Vente',
            'kicker' => 'Vente en ligne simulée',
            'updated_at' => '6 mai 2026',
            'intro' => 'Ces CGV décrivent le fonctionnement attendu d’une boutique en ligne StreamCorner. Dans cette version, elles ont une valeur pédagogique : le site ne réalise pas de vente réelle et ne collecte pas de paiement effectif.',
            'sections' => [
                [
                    'title' => 'Produits',
                    'content' => [
                        'Les produits présentés correspondent à du matériel de streaming, audio, éclairage, vidéo, contrôle et accessoires de setup.',
                        'Les photographies, descriptions, stocks et prix servent à illustrer les fonctionnalités du catalogue et de l’administration.',
                    ],
                ],
                [
                    'title' => 'Prix',
                    'content' => [
                        'Les prix affichés sont indiqués en euros et peuvent être exprimés hors taxe ou toutes taxes comprises selon les écrans du projet.',
                        'Dans un site marchand réel, les prix, taxes, frais de livraison et conditions de paiement devraient être clairement présentés avant validation de commande.',
                    ],
                ],
                [
                    'title' => 'Commande',
                    'content' => [
                        'Le parcours de commande permet de démontrer la gestion d’un panier, d’une adresse, d’un total et d’un historique de commandes.',
                        'La validation d’une commande sur cette version ne constitue pas un achat réel et n’entraîne aucune obligation de livraison.',
                    ],
                ],
                [
                    'title' => 'Paiement et livraison',
                    'content' => [
                        'Aucun paiement réel n’est traité dans le cadre académique actuel.',
                        'Les mentions relatives aux moyens de paiement, délais, transporteurs, frais de livraison et suivi colis devront être complétées avant une exploitation commerciale.',
                    ],
                ],
                [
                    'title' => 'Droit de rétractation et garanties',
                    'content' => [
                        'Pour une boutique réelle destinée à des consommateurs, les informations relatives au droit de rétractation, aux garanties légales et au service après-vente devraient être précisées avant l’achat.',
                        'Dans StreamCorner, le module SAV sert uniquement à représenter la relation entre commandes, messages et traitement administratif.',
                    ],
                ],
            ],
        ]);
    }

    #[Route('/mentions-legales', name: 'app_legal_mentions')]
    public function mentions(): Response
    {
        return $this->render('legal/page.html.twig', [
            'title' => 'Mentions légales',
            'headline' => 'Mentions_Legales',
            'kicker' => 'Identification et transparence',
            'updated_at' => '6 mai 2026',
            'intro' => 'Ces mentions légales sont adaptées à un projet académique fictif. Les informations d’identification d’une entreprise réelle devront être remplacées avant toute publication commerciale.',
            'sections' => [
                [
                    'title' => 'Éditeur du site',
                    'content' => [
                        'Nom du site : StreamCorner.',
                        'Nature : projet académique individuel réalisé en BTS SIO.',
                        'Responsable de publication : Raphaël Coursier',
                        'Contact : formulaire disponible sur la page Contact du site.',
                    ],
                ],
                [
                    'title' => 'Hébergement',
                    'content' => [
                        'Version de développement : application Symfony exécutée dans un environnement local Docker avec serveur Apache.',
                        'L\'hébergement du site est assuré par OVH, en multi-site avec mon portfolio.',
                    ],
                ],
                [
                    'title' => 'Propriété intellectuelle',
                    'content' => [
                        'La structure du site, les textes rédigés pour le projet, les éléments graphiques intégrés et le code applicatif sont utilisés dans un cadre pédagogique.',
                        'Toute réutilisation commerciale devra vérifier les droits sur les images, polices, icônes, marques, bibliothèques et contenus externes.',
                    ],
                ],
                [
                    'title' => 'Données personnelles',
                    'content' => [
                        'Le site peut traiter des données liées aux comptes, adresses, favoris, paniers, commandes, messages de contact, avis et demandes SAV.',
                        'Ces données sont utilisées pour démontrer les fonctionnalités e-commerce et d’administration du projet.',
                    ],
                ],
                [
                    'title' => 'Cookies et traceurs',
                    'content' => [
                        'Le site ne contient et n\'utilise aucun traceur ni cookie.',
                    ],
                ],
            ],
        ]);
    }
}
