<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TransactionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TransactionsRepository::class)
 * @ApiResource(
 *           attributes={
 *         "security" = "(is_granted('ADMIN_PARTENAIRE') or is_granted('UTILISATEUR'))",
 *      "security_message" = " OBBB ,vous n'avez pas accès a cette resource"
 *      },
 *      collectionOperations={
 *          "get"={
 *                  "method" = "GET",
 *                  "path" = "/admin/transactions",
 *                  "normalization_context"={"groups"={"user:read"}}
 *                  },
 *          "add_transaction"={
 *                  "route_name"="creatTransaction",
 *              }
 *      },
 *     itemOperations={
 *          "retiret_Transaction"={
 *                  "route_name"="retiret",
 *              },
 *              "delet_Transaction"={
 *                  "route_name"="deletTransaction",
 *              },
 *     "get"={
 *                  "method" = "GET",
 *                  "path" = "/admin/comptes/{id}",
 *                  "normalization_context"={"groups"={"cmopte:read"}}
 *                  },
 *      },
 *     )
 */
class Transactions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups ({"numcompte:read","agence:read"})
     */
    private $montant;

    /**
     * @ORM\Column(type="date",nullable=true)
     *  @Groups ({"agence:read"})
     */

    private $dateDepot;

    /**
     * @ORM\Column(type="date",nullable=true)
     *  @Groups ({"agence:read"})
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="float")
     */
    private $comission;

    /**
     * @ORM\Column(type="float")
     */
    private $fraisEtat;

    /**
     * @ORM\Column(type="float")

     */
    private $fraisSysteme;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $fraisEnvoie;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $fraisRetrait;

    /**
     * @ORM\Column(type="integer")
     */
    private $codeTransaction;

    /**
     * @ORM\ManyToOne(targetEntity=Comptes::class, inversedBy="transactions",cascade={"persist"})
     */
    private $copmte;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="transactions",cascade={"persist"})
     * @Groups ({"numcompte:read"})
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Clirnts::class, inversedBy="transactions",cascade={"persist"})
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=Clirnts::class, inversedBy="transaction")
     */
    private $clientEnvoie;

    /**
     * @ORM\ManyToOne(targetEntity=Clirnts::class, inversedBy="transaction")
     */
    private $clientRecu;

    /**
     * @ORM\Column(type="boolean")
     */
    private $statut = true;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     *  @Groups ({"agence:read"})
     * 
     */
    private $userDepot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     *  @Groups ({"agence:read"})
     */
    private $userRetrait;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->client = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

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

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->dateRetrait;
    }

    public function setDateRetrait(\DateTimeInterface $dateRetrait): self
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    public function getComission(): ?float
    {
        return $this->comission;
    }

    public function setComission(float $comission): self
    {
        $this->comission = $comission;

        return $this;
    }

    public function getFraisEtat(): ?float
    {
        return $this->fraisEtat;
    }

    public function setFraisEtat(float $fraisEtat): self
    {
        $this->fraisEtat = $fraisEtat;

        return $this;
    }

    public function getFraisSysteme(): ?float
    {
        return $this->fraisSysteme;
    }

    public function setFraisSysteme(float $fraisSysteme): self
    {
        $this->fraisSysteme = $fraisSysteme;

        return $this;
    }

    public function getFraisEnvoie(): ?float
    {
        return $this->fraisEnvoie;
    }

    public function setFraisEnvoie(float $fraisEnvoie): self
    {
        $this->fraisEnvoie = $fraisEnvoie;

        return $this;
    }

    public function getFraisRetrait(): ?float
    {
        return $this->fraisRetrait;
    }

    public function setFraisRetrait(float $fraisRetrait): self
    {
        $this->fraisRetrait = $fraisRetrait;

        return $this;
    }

    public function getCodeTransaction(): ?string
    {
        return $this->codeTransaction;
    }

    public function setCodeTransaction(string $codeTransaction): self
    {
        $this->codeTransaction = $codeTransaction;

        return $this;
    }

    public function getCopmte(): ?Comptes
    {
        return $this->copmte;
    }

    public function setCopmte(?Comptes $copmte): self
    {
        $this->copmte = $copmte;

        return $this;
    }
    /**
     * @return Collection|Clirnts[]
     */
    public function getClient(): Collection
    {
        return $this->client;
    }

    public function addClient(Clirnts $client): self
    {
        if (!$this->client->contains($client)) {
            $this->client[] = $client;
        }

        return $this;
    }

    public function removeClient(Clirnts $client): self
    {
        $this->client->removeElement($client);

        return $this;
    }

    public function getClientEnvoie(): ?Clirnts
    {
        return $this->clientEnvoie;
    }

    public function setClientEnvoie(?Clirnts $clientEnvoie): self
    {
        $this->clientEnvoie = $clientEnvoie;

        return $this;
    }

    public function getClientRecu(): ?Clirnts
    {
        return $this->clientRecu;
    }

    public function setClientRecu(?Clirnts $clientRecu): self
    {
        $this->clientRecu = $clientRecu;

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

    public function getUserDepot(): ?User
    {
        return $this->userDepot;
    }

    public function setUserDepot(?User $userDepot): self
    {
        $this->userDepot = $userDepot;

        return $this;
    }

    public function getUserRetrait(): ?User
    {
        return $this->userRetrait;
    }

    public function setUserRetrait(?User $userRetrait): self
    {
        $this->userRetrait = $userRetrait;

        return $this;
    }


}
