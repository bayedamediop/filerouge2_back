<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AgencesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AgencesRepository::class)
 * @ApiResource(
 *           attributes={
 *         "security" = "(is_granted('UTILISATEUR') or is_granted('ADMIN_PARTENAIRE'))",
 *      "security_message" = " OBBB ,vous n'avez pas accès a cette resource"
 *      },
 *      collectionOperations={
 *          "get"={
 *                  "method" = "GET",
 *                  "path" = "/admin/users",
 *                  "normalization_context"={"groups"={"user:read"}}
 *                  },
 *          "add_agence"={
 *                  "route_name"="creatAgence",
 *              }
 *      },
 *     )
 */
class Agences
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $numAgence;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresseAgence;

    /**
     * @ORM\Column(type="boolean")
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="agences",cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="agences",cascade={"persist"})
     */
    private $userCreat;

    /**
     * @ORM\ManyToOne(targetEntity=Comptes::class, inversedBy="agences",cascade={"persist"})
     */
    private $compte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumAgence(): ?int
    {
        return $this->numAgence;
    }

    public function setNumAgence(int $numAgence): self
    {
        $this->numAgence = $numAgence;

        return $this;
    }

    public function getAdresseAgence(): ?string
    {
        return $this->adresseAgence;
    }

    public function setAdresseAgence(string $adresseAgence): self
    {
        $this->adresseAgence = $adresseAgence;

        return $this;
    }

    public function getStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUserCreat(): ?User
    {
        return $this->userCreat;
    }

    public function setUserCreat(?User $userCreat): self
    {
        $this->userCreat = $userCreat;

        return $this;
    }

    public function getCompte(): ?Comptes
    {
        return $this->compte;
    }

    public function setCompte(?Comptes $compte): self
    {
        $this->compte = $compte;

        return $this;
    }
}
