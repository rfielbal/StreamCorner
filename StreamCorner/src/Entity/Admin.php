<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $emailA = null;

    #[ORM\Column(length: 255)]
    private ?string $mdpA = null;

    #[ORM\OneToOne(inversedBy: 'admin', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmailA(): ?string
    {
        return $this->emailA;
    }

    public function setEmailA(string $emailA): static
    {
        $this->emailA = $emailA;

        return $this;
    }

    public function getMdpA(): ?string
    {
        return $this->mdpA;
    }

    public function setMdpA(string $mdpA): static
    {
        $this->mdpA = $mdpA;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
