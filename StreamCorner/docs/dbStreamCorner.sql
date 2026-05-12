-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : database
-- Généré le : mar. 12 mai 2026 à 21:36
-- Version du serveur : 10.3.39-MariaDB-1:10.3.39+maria~ubu2004
-- Version de PHP : 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `dbStreamCorner`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email_a` varchar(180) NOT NULL,
  `mdp_a` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `adresse`
--

CREATE TABLE `adresse` (
  `id` int(11) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `rue` varchar(255) NOT NULL,
  `cp` varchar(10) NOT NULL,
  `pays` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `adresse`
--

INSERT INTO `adresse` (`id`, `ville`, `rue`, `cp`, `pays`, `user_id`) VALUES
(1, 'Lille', '11 rue des Bruyères', '59000', 'France', 3),
(2, 'Paris', '1 rue du Test Audit', '75000', 'France', 1),
(3, 'MaVille', 'rue', '69000', 'France', 6);

-- --------------------------------------------------------

--
-- Structure de la table `aimer`
--

CREATE TABLE `aimer` (
  `user_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `aimer`
--

INSERT INTO `aimer` (`user_id`, `produit_id`) VALUES
(1, 7),
(3, 3),
(3, 4),
(3, 5),
(6, 8);

-- --------------------------------------------------------

--
-- Structure de la table `ajouter`
--

CREATE TABLE `ajouter` (
  `id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_ht` decimal(10,2) NOT NULL,
  `panier_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ajouter`
--

INSERT INTO `ajouter` (`id`, `quantite`, `prix_ht`, `panier_id`, `produit_id`) VALUES
(9, 2, 249.99, 3, 8),
(19, 1, 119.99, 1, 11),
(20, 1, 149.00, 1, 5);

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id` int(11) NOT NULL,
  `nom_categorie` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id`, `nom_categorie`) VALUES
(1, 'Audio'),
(2, 'Eclairage'),
(3, 'Controle'),
(4, 'Diffusion'),
(5, 'Accessoires');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
  `id` int(11) NOT NULL,
  `date_commande` datetime NOT NULL,
  `total_ht_co` decimal(10,2) NOT NULL,
  `total_taxe` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user_id` int(11) NOT NULL,
  `adresse_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id`, `date_commande`, `total_ht_co`, `total_taxe`, `total`, `user_id`, `adresse_id`) VALUES
(1, '2026-05-06 13:54:29', 149.99, 30.00, 179.99, 1, 2),
(2, '2026-05-06 20:37:13', 439.98, 88.00, 527.98, 3, 1),
(3, '2026-05-07 08:32:51', 538.99, 107.80, 646.79, 3, 1),
(4, '2026-05-08 12:16:50', 759.99, 152.00, 911.99, 3, 1),
(5, '2026-05-10 17:46:13', 2529.87, 505.97, 3035.84, 1, 2),
(6, '2026-05-11 13:01:12', 399.98, 80.00, 479.98, 3, 1);

-- --------------------------------------------------------

--
-- Structure de la table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `sujet` varchar(150) NOT NULL,
  `message` longtext NOT NULL,
  `date_envoi` datetime NOT NULL,
  `email` varchar(180) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `contact`
--

INSERT INTO `contact` (`id`, `nom`, `prenom`, `sujet`, `message`, `date_envoi`, `email`) VALUES
(3, 'Coursier', 'Raphaël', 'question', 'je me questionne', '2026-05-11 12:59:48', 'coursierap@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260501132137', '2026-05-01 13:21:51', 122),
('DoctrineMigrations\\Version20260501140737', '2026-05-01 14:07:40', 10),
('DoctrineMigrations\\Version20260501141005', '2026-05-01 14:10:08', 37),
('DoctrineMigrations\\Version20260501141355', '2026-05-01 14:14:05', 13),
('DoctrineMigrations\\Version20260501141910', '2026-05-01 14:19:11', 5),
('DoctrineMigrations\\Version20260502123428', '2026-05-02 12:34:41', 22),
('DoctrineMigrations\\Version20260502125839', '2026-05-02 12:58:44', 16),
('DoctrineMigrations\\Version20260503120000', '2026-05-03 19:50:12', 131),
('DoctrineMigrations\\Version20260503195100', '2026-05-03 19:51:35', 18),
('DoctrineMigrations\\Version20260504120000', '2026-05-04 12:05:19', 119),
('DoctrineMigrations\\Version20260504173000', '2026-05-04 16:24:03', 18),
('DoctrineMigrations\\Version20260505100000', '2026-05-05 17:56:33', 53),
('DoctrineMigrations\\Version20260505101000', '2026-05-05 18:12:17', 24),
('DoctrineMigrations\\Version20260510100000', '2026-05-10 16:29:30', 31),
('DoctrineMigrations\\Version20260510103000', '2026-05-10 18:24:47', 196),
('DoctrineMigrations\\Version20260511120000', '2026-05-11 14:15:25', 6);

-- --------------------------------------------------------

--
-- Structure de la table `noter`
--

CREATE TABLE `noter` (
  `id` int(11) NOT NULL,
  `message` longtext NOT NULL,
  `date_message` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `note` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `noter`
--

INSERT INTO `noter` (`id`, `message`, `date_message`, `user_id`, `produit_id`, `note`) VALUES
(1, 'nickel', '2026-05-10 16:34:27', 1, 6, 5),
(2, 'Trop bien', '2026-05-10 17:35:21', 1, 8, 5),
(3, 'c\'est bien 👍', '2026-05-11 12:57:50', 3, 4, 4),
(4, 'C\'est de la bonne', '2026-05-11 13:07:35', 6, 2, 5);

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

CREATE TABLE `panier` (
  `id` int(11) NOT NULL,
  `total_ht_pa` decimal(10,2) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `panier`
--

INSERT INTO `panier` (`id`, `total_ht_pa`, `user_id`) VALUES
(1, 268.99, 1),
(2, 0.00, 3),
(3, 499.98, 5),
(4, 0.00, 6);

-- --------------------------------------------------------

--
-- Structure de la table `parvenir`
--

CREATE TABLE `parvenir` (
  `id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_ht` decimal(10,2) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `parvenir`
--

INSERT INTO `parvenir` (`id`, `quantite`, `prix_ht`, `commande_id`, `produit_id`) VALUES
(1, 1, 149.99, 1, 1),
(2, 1, 189.99, 2, 7),
(3, 1, 249.99, 2, 8),
(4, 1, 149.99, 3, 1),
(5, 1, 389.00, 3, 9),
(6, 1, 129.99, 4, 3),
(7, 3, 210.00, 4, 5),
(8, 12, 189.99, 5, 7),
(9, 1, 249.99, 5, 8),
(10, 2, 199.99, 6, 4);

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE `produit` (
  `id` int(11) NOT NULL,
  `designation` varchar(150) NOT NULL,
  `prix_unit_ht` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `stock` int(11) NOT NULL,
  `categorie_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id`, `designation`, `prix_unit_ht`, `image`, `description`, `stock`, `categorie_id`) VALUES
(1, 'Nexus Stream Deck', 249.99, 'streamCornerDeck1-6a01b6c49c1a19.30339114.png', 'Console de contrôle compacte pour streamer : touches LCD personnalisables, profils par logiciel, lancement de scènes OBS, macros système et contrôle audio rapide. Pensée pour centraliser les actions fréquentes sans quitter le live.', 16, 3),
(2, 'Pulse MIC', 169.99, 'pulseMIC2-6a01b7f7793a92.84276241.png', 'Microphone XLR avec monitoring direct, traitement vocal clair et support anti-vibration. Il cible les voix de streaming, podcasts et appels longs avec une captation nette et peu de bruit ambiant.', 24, 1),
(3, 'Aether Pro LED Panels', 329.99, 'aetherLED1-6a01b668aa8762.92922369.png', 'Kit de deux panneaux LED bi-couleur pour studio, webcam et plans produit. Température ajustable, diffusion douce et pied compact pour obtenir une lumière stable sans sur-exposer le visage.', 15, 2),
(4, 'Obsidian Webcam', 199.99, 'obsidianWebcam1-6a01b79f396183.54741632.png', 'Webcam 4K pour setup premium avec autofocus rapide, colorimétrie stable et cadrage propre. Elle convient aux streams, cours en ligne et visios qui demandent une image plus nette qu\'une webcam classique.', 10, 4),
(5, 'Cobalt Stream Deck', 149.00, 'cobaltDeck1-6a01b745ee2084.46973836.png', 'Simplifiez votre setup avec le Cobalt Deck, un contrôleur élégant et compact conçu pour les streamers, créateurs de contenu, gamers et professionnels du multimédia. Grâce à ses 12 touches personnalisables avec écran intégré et ses 3 molettes de réglage, vous pouvez lancer vos scènes, gérer votre stream, contrôler l’enregistrement, afficher vos alertes, ouvrir le chat, activer vos lumières ou accéder à vos médias en un seul geste.', 17, 3),
(6, 'Vector Boom Arm', 69.99, 'boomArm1-6a01b89e933017.70932383.png', 'Bras articulé robuste pour microphone, avec passage de câble propre et serrage bureau. Il libère l\'espace de travail tout en gardant le micro à la bonne distance pendant le live.', 28, 5),
(7, 'Vox Monitor Speaker', 89.99, 'voxSpeaker1-6a01b8cd86fe35.55089936.png', 'Enceinte de monitoring compacte pour contrôler le rendu audio d\'un stream, d\'un montage ou d\'une piste voix. Restitution claire, faible encombrement et connectique adaptée au bureau.', 1, 1),
(8, 'Hyper Capture Card 4K', 249.99, 'hyperCard1-6a01b57ed464e4.92004166.png', 'Carte de capture 4K pour console, appareil photo ou second PC. Latence réduite, signal stable et intégration facile dans OBS pour produire un flux vidéo propre.', 8, 4),
(9, 'All-In Monitor Audio', 389.00, 'AllInMonitor1-6a01b866aa09b5.33513881.png', 'Écran secondaire avec retour audio intégré pour surveiller le chat, les niveaux sonores et les alertes. Idéal pour garder le contrôle du direct sans multiplier les fenêtres sur l\'écran principal.', 7, 3),
(10, 'Internal Soundproof Foam', 25.99, 'internalFoam1-6a01ca663ec7c8.58506371.png', 'Optimisez l’acoustique de votre bureau, studio ou espace gaming avec cette mousse insonorisante à relief pyramidal. Elle aide à réduire les échos, les résonances et les bruits parasites pour un son plus clair lors de vos streams, enregistrements, podcasts ou visioconférences. Facile à installer sur un mur, elle apporte aussi une touche professionnelle à votre setup.\n\nDimensions : 30 x 30 cm\nÉpaisseur : environ 5 cm', 65, 5),
(11, 'Astral Ring Light', 119.99, 'astralRing1-6a01cac700bdd6.59077841.png', 'Améliorez la qualité de vos vidéos, lives et appels avec cette ring light LED au design moderne. Sa lumière puissante et homogène éclaire parfaitement le visage, réduit les ombres et met en valeur votre image à l’écran. Idéale pour le streaming, le gaming, les réseaux sociaux, les visioconférences ou la création de contenu, elle s’intègre facilement à tout setup grâce à son support pour smartphone et son pied réglable.', 6, 2),
(12, 'Headset Neo', 109.99, 'headsetNeo1-6a01cb64010469.98623071.png', 'Ajoutez une touche premium à votre setup avec ce casque Bluetooth sans micro au design sobre et moderne. Idéal pour les streamers, gamers et créateurs de contenu, il permet de profiter d’un son immersif tout en gardant un espace de travail propre, sans câble apparent ni micro intégré.', 50, 1);

-- --------------------------------------------------------

--
-- Structure de la table `produit_image`
--

CREATE TABLE `produit_image` (
  `id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `alt` varchar(180) DEFAULT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produit_image`
--

INSERT INTO `produit_image` (`id`, `produit_id`, `filename`, `alt`, `position`) VALUES
(1, 1, 'streamCornerDeck1-6a01b6c49c1a19.30339114.png', 'Nexus Stream Deck', 0),
(2, 1, 'streamCornerDeck2-6a01b6cb3f1e68.15365961.jpg', 'Nexus Stream Deck', 1),
(3, 1, 'streamCornerDeck3-6a01b6d0e8ead3.52186875.jpg', 'Nexus Stream Deck', 2),
(4, 2, 'pulseMIC1-6a01b7f27e4cc5.51320285.png', 'Pulse MIC', 1),
(5, 2, 'pulseMIC2-6a01b7f7793a92.84276241.png', 'Pulse MIC', 0),
(7, 3, 'aetherLED1-6a01b668aa8762.92922369.png', 'Aether Pro LED Panels', 0),
(8, 3, 'aetherLED2-6a01b66d6f5a41.76444656.png', 'Aether Pro LED Panels', 1),
(9, 3, 'aetherLED3-6a01b6634e82d5.81761674.png', 'Aether Pro LED Panels', 2),
(10, 4, 'obsidianWebcam1-6a01b79f396183.54741632.png', 'Obsidian Webcam', 0),
(11, 4, 'obsidianWebcam2-6a01b7a3d01aa4.48915069.png', 'Obsidian Webcam', 1),
(12, 4, 'obsidianWebcam3-6a01b7ac6f9c94.97489812.png', 'Obsidian Webcam', 2),
(13, 5, 'cobaltDeck1-6a01b745ee2084.46973836.png', 'Cobalt Stream Deck', 0),
(14, 5, 'cobaltDeck2-6a01b749e7a395.42816044.png', 'Cobalt Stream Deck', 1),
(15, 5, 'cobaltDeck3-6a01b74e08aeb1.05441780.png', 'Cobalt Stream Deck', 2),
(16, 6, 'boomArm1-6a01b89e933017.70932383.png', 'Vector Boom Arm', 0),
(17, 6, 'boomArm2-6a01b8a2d22b89.20826146.png', 'Vector Boom Arm', 1),
(18, 6, 'boomArm3-6a01b8a75a1307.82248293.png', 'Vector Boom Arm', 2),
(19, 7, 'voxSpeaker1-6a01b8cd86fe35.55089936.png', 'Vox Monitor Speaker', 0),
(20, 7, 'voxSpeaker2-6a01b8d23495c3.00206901.png', 'Vox Monitor Speaker', 1),
(21, 7, 'voxSpeaker3-6a01b8d7306e08.17918351.png', 'Vox Monitor Speaker', 2),
(22, 8, 'hyperCard1-6a01b57ed464e4.92004166.png', 'Capture Frame 4K', 0),
(23, 8, 'hyperCard2-6a01b585d44e95.85575075.png', 'Capture Frame 4K', 1),
(24, 8, 'hyperCard3-6a01b58b36ebc0.56568370.png', 'Capture Frame 4K', 2),
(25, 9, 'AllInMonitor1-6a01b866aa09b5.33513881.png', 'All-In Monitor Audio', 0),
(26, 9, 'AllInMonitor2-6a01b86ca830f4.40310838.png', 'All-In Monitor Audio', 1),
(27, 9, 'AllInMonitor3-6a01b872013c69.84537244.png', 'All-In Monitor Audio', 2),
(30, 8, 'hyperCard4-6a01b5a3964a93.14438691.png', 'Hyper Capture Card 4K', 3),
(31, 3, 'aetherLED4-6a01b6754b3778.75124822.png', 'Aether Pro LED Panels', 3),
(32, 1, 'streamCornerDeck4-6a01b6b9befe18.34238585.png', 'Nexus Stream Deck', 3),
(33, 5, 'cobaltDeck4-6a01b7032a1fb5.36819797.png', 'Cobalt Stream Deck', 3),
(34, 4, 'obsidianWebcam4-6a01b79649b2e7.28195473.png', 'Obsidian Webcam', 3),
(35, 2, 'pulseMIC3-6a01b7e70f1620.36823374.png', 'Pulse MIC', 2),
(36, 9, 'AllInMonitor4-6a01b857e0aaa9.98551216.png', 'All-In Monitor Audio', 3),
(37, 6, 'boomArm4-6a01b897193d33.85468415.png', 'Vector Boom Arm', 3),
(39, 10, 'internalFoam1-6a01ca663ec7c8.58506371.png', 'Internal Soundproof Foam', 0),
(40, 11, 'astralRing1-6a01cac700bdd6.59077841.png', 'Astral Ring Light', 0),
(41, 11, 'astralRing2-6a01cac7023734.40603860.png', 'Astral Ring Light', 1),
(42, 11, 'astralRing3-6a01cac702ceb3.00509466.png', 'Astral Ring Light', 2),
(43, 11, 'astralRing4-6a01cac7034b60.07487391.png', 'Astral Ring Light', 3),
(44, 10, 'internalFoam2-6a01caf93fa809.72132005.png', 'Internal Soundproof Foam', 1),
(45, 10, 'internalFoam3-6a01caf940c8d9.94698593.png', 'Internal Soundproof Foam', 2),
(46, 12, 'headsetNeo1-6a01cb64010469.98623071.png', 'Headset Neo', 0),
(47, 12, 'headsetNeo2-6a01cb64038ac3.99930647.png', 'Headset Neo', 1),
(48, 12, 'headsetNeo3-6a01cb6403e6b4.63845778.png', 'Headset Neo', 2),
(49, 12, 'headsetNeo4-6a01cb64046065.81393692.png', 'Headset Neo', 3);

-- --------------------------------------------------------

--
-- Structure de la table `sav`
--

CREATE TABLE `sav` (
  `id` int(11) NOT NULL,
  `message` longtext NOT NULL,
  `date_message` datetime NOT NULL,
  `traitement` varchar(50) NOT NULL,
  `commande_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sav`
--

INSERT INTO `sav` (`id`, `message`, `date_message`, `traitement`, `commande_id`) VALUES
(3, 'vous êtes sur que le materiel fonctionne ?', '2026-05-11 12:59:12', 'Nouveau', 4);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(4) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `is_verified`, `prenom`, `nom`) VALUES
(1, 'coursierap@icloud.com', '[\"ROLE_ADMIN\"]', '$2y$13$LCxiNi9VISY0bI.w0w4TjOlCE7Qc.F662OJeJaHlVtRbD6UvQqQ0G', 0, NULL, NULL),
(3, 'coursierap@gmail.com', '[\"ROLE_USER\",\"ROLE_VISITEUR\"]', '$2y$13$DNwHU7uG5Odj5u/jLup/ieiKlasTE2gWq135Sy5.asV1LQCQggbBC', 0, 'Raphaël', 'Coursier'),
(4, 'louis@gmail.com', '[\"ROLE_USER\"]', '$2y$13$14EtrHF1AZs1gMhWTdOegu/mw869AK1RUy2.yGNHhdK19f4S7M0S2', 0, 'antoine', 'louis'),
(5, 'btsinfo@gmail.co', '[\"ROLE_USER\"]', '$2y$13$y2UfTG4LUtlp/7FHta/KHe4wEgV7.KWxqw/MYpIZpkbX876ALvOc6', 0, 'louis', 'jean'),
(6, 'Tom@mail.fr', '[\"ROLE_VISITEUR\"]', '$2y$13$FSI00M9zc9eSX/9ZN193C.3dLo/Wf43CefuGMnjZ19GZUA5EgrK7C', 0, 'Thom', 'Dom'),
(7, 'visiteur@streamcorner.com', '[\"ROLE_VISITEUR\"]', '$2y$13$vb411MauE2M9vRjZvOreWeO/sQVAsU97rHPhPIXpZoaZl/jXxLfCm', 0, 'Visiteur', 'StreamCorner'),
(8, 'admin@streamcorner.com', '[\"ROLE_USER\",\"ROLE_ADMIN\"]', '$2y$13$wgZX7t/H59pugQ.HJRRee.S9yoJSxACbr/HbdCpozTh.uHbjrBYlC', 0, 'AdminType', 'Guy');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_880E0D76A76ED395` (`user_id`);

--
-- Index pour la table `adresse`
--
ALTER TABLE `adresse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C35F0816A76ED395` (`user_id`);

--
-- Index pour la table `aimer`
--
ALTER TABLE `aimer`
  ADD PRIMARY KEY (`user_id`,`produit_id`),
  ADD KEY `IDX_C2D0C6E8F347EFB` (`produit_id`),
  ADD KEY `IDX_C2D0C6E8A76ED395` (`user_id`);

--
-- Index pour la table `ajouter`
--
ALTER TABLE `ajouter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_AJOUTER_PANIER_PRODUIT` (`panier_id`,`produit_id`),
  ADD KEY `IDX_AB384B5FF77D927C` (`panier_id`),
  ADD KEY `IDX_AB384B5FF347EFB` (`produit_id`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6EEAA67D4DE7DC5C` (`adresse_id`),
  ADD KEY `IDX_6EEAA67DA76ED395` (`user_id`);

--
-- Index pour la table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `noter`
--
ALTER TABLE `noter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_NOTER_USER_PRODUIT` (`user_id`,`produit_id`),
  ADD KEY `IDX_761C961AF347EFB` (`produit_id`),
  ADD KEY `IDX_761C961AA76ED395` (`user_id`);

--
-- Index pour la table `panier`
--
ALTER TABLE `panier`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_24CC0DF2A76ED395` (`user_id`);

--
-- Index pour la table `parvenir`
--
ALTER TABLE `parvenir`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_PARVENIR_COMMANDE_PRODUIT` (`commande_id`,`produit_id`),
  ADD KEY `IDX_904E91E882EA2E54` (`commande_id`),
  ADD KEY `IDX_904E91E8F347EFB` (`produit_id`);

--
-- Index pour la table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_29A5EC27BCF5E72D` (`categorie_id`);

--
-- Index pour la table `produit_image`
--
ALTER TABLE `produit_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F5A163CBF347EFB` (`produit_id`);

--
-- Index pour la table `sav`
--
ALTER TABLE `sav`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6C7681F482EA2E54` (`commande_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `adresse`
--
ALTER TABLE `adresse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `ajouter`
--
ALTER TABLE `ajouter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `noter`
--
ALTER TABLE `noter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `panier`
--
ALTER TABLE `panier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `parvenir`
--
ALTER TABLE `parvenir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `produit`
--
ALTER TABLE `produit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `produit_image`
--
ALTER TABLE `produit_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT pour la table `sav`
--
ALTER TABLE `sav`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `FK_880E0D76A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `adresse`
--
ALTER TABLE `adresse`
  ADD CONSTRAINT `FK_C35F0816A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `aimer`
--
ALTER TABLE `aimer`
  ADD CONSTRAINT `FK_C2D0C6E8A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_C2D0C6E8F347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `ajouter`
--
ALTER TABLE `ajouter`
  ADD CONSTRAINT `FK_AB384B5FF347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`),
  ADD CONSTRAINT `FK_AB384B5FF77D927C` FOREIGN KEY (`panier_id`) REFERENCES `panier` (`id`);

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `FK_6EEAA67D4DE7DC5C` FOREIGN KEY (`adresse_id`) REFERENCES `adresse` (`id`),
  ADD CONSTRAINT `FK_6EEAA67DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `noter`
--
ALTER TABLE `noter`
  ADD CONSTRAINT `FK_761C961AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_761C961AF347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`);

--
-- Contraintes pour la table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `FK_24CC0DF2A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `parvenir`
--
ALTER TABLE `parvenir`
  ADD CONSTRAINT `FK_904E91E882EA2E54` FOREIGN KEY (`commande_id`) REFERENCES `commande` (`id`),
  ADD CONSTRAINT `FK_904E91E8F347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`);

--
-- Contraintes pour la table `produit`
--
ALTER TABLE `produit`
  ADD CONSTRAINT `FK_29A5EC27BCF5E72D` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`);

--
-- Contraintes pour la table `produit_image`
--
ALTER TABLE `produit_image`
  ADD CONSTRAINT `FK_5D291BB3F347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sav`
--
ALTER TABLE `sav`
  ADD CONSTRAINT `FK_6C7681F482EA2E54` FOREIGN KEY (`commande_id`) REFERENCES `commande` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
