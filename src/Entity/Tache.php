<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TacheRepository::class)]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $Categorie = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Statut $Statut = null;

    #[ORM\Column(length: 100)]
    private ?string $Titre = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $date_echeance = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $priorite = null;

    /**
     * @var Collection<int, TacheArchive>
     */
    #[ORM\OneToMany(targetEntity: TacheArchive::class, mappedBy: 'Tache')]
    private Collection $tacheArchives;

    public function __construct()
    {
        $this->tacheArchives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->Categorie;
    }

    public function setCategorie(?Categorie $Categorie): static
    {
        $this->Categorie = $Categorie;

        return $this;
    }

    public function getStatut(): ?Statut
    {
        return $this->Statut;
    }

    public function setStatut(?Statut $Statut): static
    {
        $this->Statut = $Statut;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->Titre;
    }

    public function setTitre(string $Titre): static
    {
        $this->Titre = $Titre;

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

    public function getDateEcheance(): ?\DateTime
    {
        return $this->date_echeance;
    }

    public function setDateEcheance(?\DateTime $date_echeance): static
    {
        $this->date_echeance = $date_echeance;

        return $this;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(?string $priorite): static
    {
        $this->priorite = $priorite;

        return $this;
    }

    /**
     * @return Collection<int, TacheArchive>
     */
    public function getTacheArchives(): Collection
    {
        return $this->tacheArchives;
    }

    public function addTacheArchive(TacheArchive $tacheArchive): static
    {
        if (!$this->tacheArchives->contains($tacheArchive)) {
            $this->tacheArchives->add($tacheArchive);
            $tacheArchive->setTache($this);
        }

        return $this;
    }

    public function removeTacheArchive(TacheArchive $tacheArchive): static
    {
        if ($this->tacheArchives->removeElement($tacheArchive)) {
            // set the owning side to null (unless already changed)
            if ($tacheArchive->getTache() === $this) {
                $tacheArchive->setTache(null);
            }
        }

        return $this;
    }
}
