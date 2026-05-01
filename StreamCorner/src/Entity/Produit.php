<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $designation = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prixUnitHT = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    /**
     * @var Collection<int, Ajouter>
     */
    #[ORM\OneToMany(targetEntity: Ajouter::class, mappedBy: 'produit')]
    private Collection $ajouters;

    /**
     * @var Collection<int, Parvenir>
     */
    #[ORM\OneToMany(targetEntity: Parvenir::class, mappedBy: 'produit')]
    private Collection $parvenirs;

    /**
     * @var Collection<int, Noter>
     */
    #[ORM\OneToMany(targetEntity: Noter::class, mappedBy: 'produit')]
    private Collection $noters;

    /**
     * @var Collection<int, Utilisateur>
     */
    #[ORM\ManyToMany(targetEntity: Utilisateur::class, mappedBy: 'produitsAimers')]
    private Collection $UtilisateursAimant;

    public function __construct()
    {
        $this->ajouters = new ArrayCollection();
        $this->parvenirs = new ArrayCollection();
        $this->noters = new ArrayCollection();
        $this->UtilisateursAimant = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    public function getPrixUnitHT(): ?string
    {
        return $this->prixUnitHT;
    }

    public function setPrixUnitHT(string $prixUnitHT): static
    {
        $this->prixUnitHT = $prixUnitHT;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Ajouter>
     */
    public function getAjouters(): Collection
    {
        return $this->ajouters;
    }

    public function addAjouter(Ajouter $ajouter): static
    {
        if (!$this->ajouters->contains($ajouter)) {
            $this->ajouters->add($ajouter);
            $ajouter->setProduit($this);
        }

        return $this;
    }

    public function removeAjouter(Ajouter $ajouter): static
    {
        if ($this->ajouters->removeElement($ajouter)) {
            // set the owning side to null (unless already changed)
            if ($ajouter->getProduit() === $this) {
                $ajouter->setProduit(null);
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
            $parvenir->setProduit($this);
        }

        return $this;
    }

    public function removeParvenir(Parvenir $parvenir): static
    {
        if ($this->parvenirs->removeElement($parvenir)) {
            // set the owning side to null (unless already changed)
            if ($parvenir->getProduit() === $this) {
                $parvenir->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Noter>
     */
    public function getNoters(): Collection
    {
        return $this->noters;
    }

    public function addNoter(Noter $noter): static
    {
        if (!$this->noters->contains($noter)) {
            $this->noters->add($noter);
            $noter->setProduit($this);
        }

        return $this;
    }

    public function removeNoter(Noter $noter): static
    {
        if ($this->noters->removeElement($noter)) {
            // set the owning side to null (unless already changed)
            if ($noter->getProduit() === $this) {
                $noter->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateursAimant(): Collection
    {
        return $this->UtilisateursAimant;
    }

    public function addUtilisateursAimant(Utilisateur $utilisateursAimant): static
    {
        if (!$this->UtilisateursAimant->contains($utilisateursAimant)) {
            $this->UtilisateursAimant->add($utilisateursAimant);
            $utilisateursAimant->addProduitsAimer($this);
        }

        return $this;
    }

    public function removeUtilisateursAimant(Utilisateur $utilisateursAimant): static
    {
        if ($this->UtilisateursAimant->removeElement($utilisateursAimant)) {
            $utilisateursAimant->removeProduitsAimer($this);
        }

        return $this;
    }
}
