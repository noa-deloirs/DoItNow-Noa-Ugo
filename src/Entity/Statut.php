<?php

namespace App\Entity;

use App\Repository\StatutRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatutRepository::class)]
class Statut
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, Tache>
     */
    #[ORM\OneToMany(targetEntity: Tache::class, mappedBy: 'Statut')]
    private Collection $taches;

    /**
     * @var Collection<int, TacheArchive>
     */
    #[ORM\OneToMany(targetEntity: TacheArchive::class, mappedBy: 'Statut')]
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
            $tach->setStatut($this);
        }

        return $this;
    }

    public function removeTach(Tache $tach): static
    {
        if ($this->taches->removeElement($tach)) {
            // set the owning side to null (unless already changed)
            if ($tach->getStatut() === $this) {
                $tach->setStatut(null);
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
            $tacheArchive->setStatut($this);
        }

        return $this;
    }

    public function removeTacheArchive(TacheArchive $tacheArchive): static
    {
        if ($this->tacheArchives->removeElement($tacheArchive)) {
            // set the owning side to null (unless already changed)
            if ($tacheArchive->getStatut() === $this) {
                $tacheArchive->setStatut(null);
            }
        }

        return $this;
    }
}
