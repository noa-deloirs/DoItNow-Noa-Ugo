<?php

namespace App\Entity;

use App\Repository\TacheArchiveRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TacheArchiveRepository::class)]
class TacheArchive
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tacheArchives')]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?Tache $Tache = null;

    #[ORM\ManyToOne(inversedBy: 'tacheArchives')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;

    #[ORM\ManyToOne(inversedBy: 'tacheArchives')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $Categorie = null;

    #[ORM\ManyToOne(inversedBy: 'tacheArchives')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Statut $Statut = null;

    #[ORM\Column(length: 50)]
    private ?string $Titre = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_echeance = null;

    #[ORM\Column(length: 50)]
    private ?string $priotite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_archivage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTache(): ?Tache
    {
        return $this->Tache;
    }

    public function setTache(?Tache $Tache): static
    {
        $this->Tache = $Tache;

        return $this;
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

    public function setDateEcheance(\DateTime $date_echeance): static
    {
        $this->date_echeance = $date_echeance;

        return $this;
    }

    public function getPriotite(): ?string
    {
        return $this->priotite;
    }

    public function setPriotite(string $priotite): static
    {
        $this->priotite = $priotite;

        return $this;
    }

    public function getDateArchivage(): ?\DateTime
    {
        return $this->date_archivage;
    }

    public function setDateArchivage(\DateTime $date_archivage): static
    {
        $this->date_archivage = $date_archivage;

        return $this;
    }
}
