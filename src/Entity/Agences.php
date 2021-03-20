<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AgencesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AgencesRepository::class)
 * @ApiResource(
 *           attributes={

 *      },
 *      collectionOperations={
 * "post"={
 *                   "method"="POST",
 *                    "path" = "/admin/agences",
 *                     "denormalization_context"={"groups"={"agence:write"}},
 *                        "security_message" = " OBBB ,vous n'avez pas accès a cette resource"
 *
 *      },
 *          "get"={
 *                  "method" = "GET",
 *                  "path" = "/admin/agences",
 *                  "normalization_context"={"groups"={"agences:read"}}
 *                  },
 *          "add_agence"={
 *                  "route_name"="creatAgence",
 *                   " security" = "(is_granted('ROLE_ADMIN') )",
 *                    "security_message" = " OBBB ,vous n'avez pas accès a cette resource"
 *              },
 *     "archive_Agence"={
 *                  "route_name"="archive",
 *              }
 *      },
 *      itemOperations={
 *           "get_agence_by_id"={
 *                   "method"="GET",
 *                    "path" = "/admin/agences/{id}",
 *                       "normalization_context"={"groups"={"agence:read"}},
 *  
 *
 *      },
 *      },
 *     )
 */
class Agences
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"getOndepotUserCompt:read","agences:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     *@Groups ({"getOndepotUserCompt:read","agence:read","agence:write","agences:read"})
     */
    private $numAgence;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups ({"getOndepotUserCompt:read","agence:read","agence:write"})
     */
    private $adresseAgence;

    /**
     * @ORM\Column(type="boolean")
     */
    private $statut = false;


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="agences",cascade={"persist"})
     * 
     */
    private $userCreat;

    /**
     * @ORM\ManyToOne(targetEntity=Comptes::class, inversedBy="agences",cascade={"persist"})
     * @Groups ({"agence:read","user:read"})
     */
    private $compte;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="agence")
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups ({"agence:read","user:read"})
     */
    private $nom;
    public function __construct()
    {
       $this->numAgence = rand(9,1000000000);
      // $this->getUser()->getId();
      $this->users = new ArrayCollection();
    }

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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setAgence($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getAgence() === $this) {
                $user->setAgence(null);
            }
        }

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
}
