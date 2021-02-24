<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProfilsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProfilsRepository::class)
 * @ApiResource()
 */
class Profils
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"user:read"})
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="profils")
     */
    private $profil;

    public function __construct()
    {
        $this->profil = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getProfil(): Collection
    {
        return $this->profil;
    }

    public function addProfil(User $profil): self
    {
        if (!$this->profil->contains($profil)) {
            $this->profil[] = $profil;
            $profil->setProfils($this);
        }

        return $this;
    }

    public function removeProfil(User $profil): self
    {
        if ($this->profil->removeElement($profil)) {
            // set the owning side to null (unless already changed)
            if ($profil->getProfils() === $this) {
                $profil->setProfils(null);
            }
        }

        return $this;
    }
}
