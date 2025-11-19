<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 50)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $mot_de_passe = null;

    #[ORM\Column]
    private ?bool $admin = null;

    /**
     * @var Collection<int, Tache>
     */
    #[ORM\OneToMany(targetEntity: Tache::class, mappedBy: 'User')]
    private Collection $taches;

    /**
     * @var Collection<int, TacheArchive>
     */
    #[ORM\OneToMany(targetEntity: TacheArchive::class, mappedBy: 'User')]
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

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): static
    {
        $this->mot_de_passe = $mot_de_passe;

        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): static
    {
        $this->admin = $admin;

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
            $tach->setUser($this);
        }

        return $this;
    }

    public function removeTach(Tache $tach): static
    {
        if ($this->taches->removeElement($tach)) {
            // set the owning side to null (unless already changed)
            if ($tach->getUser() === $this) {
                $tach->setUser(null);
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
            $tacheArchive->setUser($this);
        }

        return $this;
    }

    public function removeTacheArchive(TacheArchive $tacheArchive): static
    {
        if ($this->tacheArchives->removeElement($tacheArchive)) {
            // set the owning side to null (unless already changed)
            if ($tacheArchive->getUser() === $this) {
                $tacheArchive->setUser(null);
            }
        }

        return $this;
    }
}
