<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $dateCommande = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalHtCo = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalTaxe = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Adresse $adresse = null;

    /**
     * @var Collection<int, Sav>
     */
    #[ORM\OneToMany(targetEntity: Sav::class, mappedBy: 'commande')]
    private Collection $savs;

    /**
     * @var Collection<int, Parvenir>
     */
    #[ORM\OneToMany(targetEntity: Parvenir::class, mappedBy: 'commande')]
    private Collection $parvenirs;

    public function __construct()
    {
        $this->savs = new ArrayCollection();
        $this->parvenirs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): ?\DateTime
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTime $dateCommande): static
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    public function getTotalHtCo(): ?string
    {
        return $this->totalHtCo;
    }

    public function setTotalHtCo(string $totalHtCo): static
    {
        $this->totalHtCo = $totalHtCo;

        return $this;
    }

    public function getTotalTaxe(): ?string
    {
        return $this->totalTaxe;
    }

    public function setTotalTaxe(string $totalTaxe): static
    {
        $this->totalTaxe = $totalTaxe;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
    }

    public function setAdresse(?Adresse $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return Collection<int, Sav>
     */
    public function getSavs(): Collection
    {
        return $this->savs;
    }

    public function addSav(Sav $sav): static
    {
        if (!$this->savs->contains($sav)) {
            $this->savs->add($sav);
            $sav->setCommande($this);
        }

        return $this;
    }

    public function removeSav(Sav $sav): static
    {
        if ($this->savs->removeElement($sav)) {
            // set the owning side to null (unless already changed)
            if ($sav->getCommande() === $this) {
                $sav->setCommande(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Parvenir>
     */
    public function getParvenirs(): Collection
    {
        return $this->parvenirs;
    }

    public function addParvenir(Parvenir $parvenir): static
    {
        if (!$this->parvenirs->contains($parvenir)) {
            $this->parvenirs->add($parvenir);
            $parvenir->setCommande($this);
        }

        return $this;
    }

    public function removeParvenir(Parvenir $parvenir): static
    {
        if ($this->parvenirs->removeElement($parvenir)) {
            // set the owning side to null (unless already changed)
            if ($parvenir->getCommande() === $this) {
                $parvenir->setCommande(null);
            }
        }

        return $this;
    }
}
