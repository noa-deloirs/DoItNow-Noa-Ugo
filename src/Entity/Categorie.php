<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $libelle = null;

    #[ORM\Column(length: 10)]
    private ?string $color = null;

    /**
     * @var Collection<int, Tache>
     */
    #[ORM\OneToMany(targetEntity: Tache::class, mappedBy: 'Categorie')]
    private Collection $taches;

    /**
     * @var Collection<int, TacheArchive>
     */
    #[ORM\OneToMany(targetEntity: TacheArchive::class, mappedBy: 'Categorie')]
    private Collection $tacheArchives;

    public function __construct()
    {
        $this->taches = new ArrayCollection();
        $this->tacheArchives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection<int, Tache>
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTach(Tache $tach): static
    {
        if (!$this->taches->contains($tach)) {
            $this->taches->add($tach);
            $tach->setCategorie($this);
        }

        return $this;
    }

    public function removeTach(Tache $tach): static
    {
        if ($this->taches->removeElement($tach)) {
            // set the owning side to null (unless already changed)
            if ($tach->getCategorie() === $this) {
                $tach->setCategorie(null);
            }
        }

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
            $tacheArchive->setCategorie($this);
        }

        return $this;
    }

    public function removeTacheArchive(TacheArchive $tacheArchive): static
    {
        if ($this->tacheArchives->removeElement($tacheArchive)) {
            // set the owning side to null (unless already changed)
            if ($tacheArchive->getCategorie() === $this) {
                $tacheArchive->setCategorie(null);
            }
        }

        return $this;
    }
}
