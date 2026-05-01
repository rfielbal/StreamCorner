<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nomU = null;

    #[ORM\Column(length: 100)]
    private ?string $prenomU = null;

    #[ORM\Column(length: 255)]
    private ?string $mdpU = null;

    #[ORM\Column(length: 180)]
    private ?string $emailU = null;

    #[ORM\Column(length: 50)]
    private ?string $roleU = null;

    #[ORM\OneToOne(mappedBy: 'utilisateur', cascade: ['persist', 'remove'])]
    private ?Admin $admin = null;

    /**
     * @var Collection<int, Adresse>
     */
    #[ORM\OneToMany(targetEntity: Adresse::class, mappedBy: 'utilisateur')]
    private Collection $adresses;

    #[ORM\OneToOne(mappedBy: 'utilisateur', cascade: ['persist', 'remove'])]
    private ?Panier $panier = null;

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'utilisateur')]
    private Collection $commandes;

    #[ORM\OneToOne(mappedBy: 'utilisateur', cascade: ['persist', 'remove'])]
    private ?Noter $noter = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\ManyToMany(targetEntity: Produit::class, inversedBy: 'UtilisateursAimant')]
    #[ORM\JoinTable(name: 'aimer')]
    private Collection $produitsAimers;

    public function __construct()
    {
        $this->adresses = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->produitsAimers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomU(): ?string
    {
        return $this->nomU;
    }

    public function setNomU(string $nomU): static
    {
        $this->nomU = $nomU;

        return $this;
    }

    public function getPrenomU(): ?string
    {
        return $this->prenomU;
    }

    public function setPrenomU(string $prenomU): static
    {
        $this->prenomU = $prenomU;

        return $this;
    }

    public function getMdpU(): ?string
    {
        return $this->mdpU;
    }

    public function setMdpU(string $mdpU): static
    {
        $this->mdpU = $mdpU;

        return $this;
    }

    public function getEmailU(): ?string
    {
        return $this->emailU;
    }

    public function setEmailU(string $emailU): static
    {
        $this->emailU = $emailU;

        return $this;
    }

    public function getRoleU(): ?string
    {
        return $this->roleU;
    }

    public function setRoleU(string $roleU): static
    {
        $this->roleU = $roleU;

        return $this;
    }

    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    public function setAdmin(Admin $admin): static
    {
        // set the owning side of the relation if necessary
        if ($admin->getUtilisateur() !== $this) {
            $admin->setUtilisateur($this);
        }

        $this->admin = $admin;

        return $this;
    }

    /**
     * @return Collection<int, Adresse>
     */
    public function getAdresses(): Collection
    {
        return $this->adresses;
    }

    public function addAdress(Adresse $adress): static
    {
        if (!$this->adresses->contains($adress)) {
            $this->adresses->add($adress);
            $adress->setUtilisateur($this);
        }

        return $this;
    }

    public function removeAdress(Adresse $adress): static
    {
        if ($this->adresses->removeElement($adress)) {
            // set the owning side to null (unless already changed)
            if ($adress->getUtilisateur() === $this) {
                $adress->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(Panier $panier): static
    {
        // set the owning side of the relation if necessary
        if ($panier->getUtilisateur() !== $this) {
            $panier->setUtilisateur($this);
        }

        $this->panier = $panier;

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUtilisateur() === $this) {
                $commande->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getNoter(): ?Noter
    {
        return $this->noter;
    }

    public function setNoter(Noter $noter): static
    {
        // set the owning side of the relation if necessary
        if ($noter->getUtilisateur() !== $this) {
            $noter->setUtilisateur($this);
        }

        $this->noter = $noter;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduitsAimers(): Collection
    {
        return $this->produitsAimers;
    }

    public function addProduitsAimer(Produit $produitsAimer): static
    {
        if (!$this->produitsAimers->contains($produitsAimer)) {
            $this->produitsAimers->add($produitsAimer);
        }

        return $this;
    }

    public function removeProduitsAimer(Produit $produitsAimer): static
    {
        $this->produitsAimers->removeElement($produitsAimer);

        return $this;
    }
}
