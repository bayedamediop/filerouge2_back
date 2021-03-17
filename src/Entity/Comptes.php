<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ComptesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ComptesRepository::class)
 * @ApiResource(
 *           attributes={
 *         "security" = "(is_granted('ROLE_ADMIN') )",
 *      "security_message" = " OBBB ,vous n'avez pas accès a cette resource"
 *      },
 *      collectionOperations={
 *          "get"={
 *                  "method" = "GET",
 *                  "path" = "/admin/comptes",
 *                  "normalization_context"={"groups"={"comptes:read"}}
 *
 *      },
 *     "post"={
 *                  "method" = "POST",
 *                  "path" = "/admin/comptes",
 *      },
 *
 *      },
 *     itemOperations={
 *     "get_CoompteByNumero"={
 *                  "route_name"="getCoompteByNumero",
 *                   
 *      },
 *     "get"={
 *                  "method" = "GET",
 *                  "path" = "/admin/comptes/{id}",
 *                  "normalization_context"={"groups"={"cmopte:read"}}
 *                  },
 *      },
 *     )
 */
class Comptes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *  @Groups ({"user:read"})
     *
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups ({"depot:read","comptes:read","userdepot:read","getOndepotUserCompt:read"})
     */
          private $numCompte ;

    /**
     * @ORM\Column(type="float")
     *  @Groups ({"numcompte:read","user:read"})
     */
    private $solde;
    

    /**
     * @ORM\OneToMany(targetEntity=Depots::class, mappedBy="compte",cascade={"persist"})
     *  @Groups ({"numcompte:read"})
     */
    private $depots;

    /**
     * @ORM\OneToMany(targetEntity=Transactions::class, mappedBy="copmte",cascade={"persist"})
     *  @Groups ({"numcompte:read","agence:read","mestransactions:read"})

     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity=Agences::class, mappedBy="compte",cascade={"persist"})
     *  @Groups ({"getOndepotUserCompt:read"})
     * 
     * 
     */
    private $agences;

    public function __construct()
    {
        $this->depots = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->agences = new ArrayCollection();
        $this->numCompte = rand(9, 1000000000);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumCompte(): ?string
    {
        return $this->numCompte;
    }

    public function setNumCompte(string $numCompte): self
    {
        $this->numCompte = $numCompte;

        return $this;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    /**
     * @return Collection|Depots[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depots $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setCompte($this);
        }

        return $this;
    }

    public function removeDepot(Depots $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getCompte() === $this) {
                $depot->setCompte(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transactions[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transactions $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setCopmte($this);
        }

        return $this;
    }

    public function removeTransaction(Transactions $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getCopmte() === $this) {
                $transaction->setCopmte(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Agences[]
     */
    public function getAgences(): Collection
    {
        return $this->agences;
    }

    public function addAgence(Agences $agence): self
    {
        if (!$this->agences->contains($agence)) {
            $this->agences[] = $agence;
            $agence->setCompte($this);
        }

        return $this;
    }

    public function removeAgence(Agences $agence): self
    {
        if ($this->agences->removeElement($agence)) {
            // set the owning side to null (unless already changed)
            if ($agence->getCompte() === $this) {
                $agence->setCompte(null);
            }
        }

        return $this;
    }

    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    public function getPassword()
    {
        // TODO: Implement getPassword() method.
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
