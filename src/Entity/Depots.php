<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\DepotsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DepotsRepository::class)
 * @ApiResource(
 *           attributes={
 *         "security" = "(is_granted('ROLE_ADMIN') or is_granted('ROLE_CAISSIER'))",
 *      "security_message" = " OBBB ,vous n'avez pas accès a cette resource"
 *      },
 *      collectionOperations={
 *             "add_depot"={
 *                  "route_name"="creatdepot"
 *              },
 *          "get"={
 *                  "method" = "GET",
 *                  "path" = "/admin/depots",
 *                  "normalization_context"={"groups"={"depot:read"}}
 *
 *      },
 *     "post"={
 *                  "method" = "POST",
 *                  "path" = "/admin/comptes",
 *      },
 *      },
 *         itemOperations={
 *          "les_depot_un_user_d_un_compte"={
 *          "method" = "GET",
 *          "path"  = "/admin/depots/user/{id}/compte",
 *           "normalization_context"={"groups"={"depot:read"}}
 *      }
 *      },
 *     )
 */
class Depots
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"depot:read","user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups ({"depot:read"})
     */
    private $montantDepot;

    /**
     * @ORM\ManyToOne(targetEntity=Comptes::class, inversedBy="depots",cascade={"persist"})
     * @Groups ({"depot:read"})
     *
     */
    private $compte;


    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     * @var string A "Y-m-d H:i:s" formatted value
     * @Groups ({"depot:write"})
     * @Groups ({"depot:read"})
     */
    private $dateDepot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="user")
     * @Groups ({"depot:read"})
     */
    private $users;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getMontantDepot(): ?float
    {
        return $this->montantDepot;
    }

    public function setMontantDepot(float $montantDepot): self
    {
        $this->montantDepot = $montantDepot;

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


    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->dateDepot;
    }

    public function setDateDepot(\DateTimeInterface $dateDepot): self
    {
        $this->dateDepot = $dateDepot;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        $this->users = $users;

        return $this;
    }
}
