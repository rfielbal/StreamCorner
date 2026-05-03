<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class UtilisateurContext
{
    public function __construct(
        private readonly Security $security,
        private readonly UtilisateurRepository $utilisateurRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getUtilisateur(bool $createIfMissing = true): ?Utilisateur
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return null;
        }

        $email = (string) $user->getEmail();
        $utilisateur = $this->utilisateurRepository->findOneBy(['emailU' => $email]);

        if ($utilisateur !== null || !$createIfMissing) {
            return $utilisateur;
        }

        $localPart = trim((string) strstr($email, '@', true));

        $utilisateur = (new Utilisateur())
            ->setNomU($localPart !== '' ? $localPart : 'Client')
            ->setPrenomU('StreamCorner')
            ->setEmailU($email)
            ->setMdpU((string) $user->getPassword())
            ->setRoleU(in_array('ROLE_ADMIN', $user->getRoles(), true) ? 'ROLE_ADMIN' : 'ROLE_USER');

        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();

        return $utilisateur;
    }
}
