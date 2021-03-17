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
 *  
 *      },
 *      collectionOperations={
 *          "get"={
 *                  "method" = "GET",
 *                  "path" = "/admin/transactions",
 *                  "normalization_context"={"groups"={"user:read"}}
 *                  },
 *          "add_transaction"={
 *                  "route_name"="creatTransaction",
 *              },
 *            "calculer_frais"={
 *                  "route_name"="calculerfrais",
 *              },
 *    
 *      },
 *     itemOperations={
 *          "retiret_Transaction"={
 *                  "route_name"="retiret",
 *              },
 * "find_Transaction_depot"={
 *                  "route_name"="findTransactiondepot",
 *              },
 *              "delet_Transaction"={
 *                  "route_name"="deletTransaction",
 *              },
 *     "get"={
 *                  "method" = "GET",
 *                  "path" = "/admin/comptes/{id}",
 *                  "normalization_context"={"groups"={"cmopte:read"}}
 *                  },
 *         "recherche_transaction"={
 *                  "route_name"="recherchetransaction",
 *                  "normalization_context"={"groups"={"transactionById:read"}}
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
     * @Groups ({"transactionunuser:read","transactionretrait:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups ({"numcompte:read","agence:read","transactionById:read","transactionunuser:read",
     *     "transactionretrait:read","mestransactions:read"})
     * 
     */
    private $montant;


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
     * @Groups ({"user:read","userdepot:read"})
     */
    private $copmte;

    /**
     * @ORM\ManyToMany(targetEntity=Clirnts::class, inversedBy="transactions",cascade={"persist"})
     * 
     * @Groups ({"transactionById:read"})
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=Clirnts::class, inversedBy="transaction")
     *  @Groups ({"transactionById:read"})
     */
    private $clientEnvoie;

    /**
     * @ORM\ManyToOne(targetEntity=Clirnts::class, inversedBy="transaction")
     *  @Groups ({"transactionById:read"})
     */
    private $clientRecu;

    /**
     * @ORM\Column(type="boolean")
     */
    private $statut = true;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="transaction")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=UserTransaction::class, mappedBy="transaction")
     */
    private $userTransactions;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->userTransactions = new ArrayCollection();
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

    /**
     * @return Collection|UserTransaction[]
     */
    public function getUserTransactions(): Collection
    {
        return $this->userTransactions;
    }

    public function addUserTransaction(UserTransaction $userTransaction): self
    {
        if (!$this->userTransactions->contains($userTransaction)) {
            $this->userTransactions[] = $userTransaction;
            $userTransaction->setTransaction($this);
        }

        return $this;
    }

    public function removeUserTransaction(UserTransaction $userTransaction): self
    {
        if ($this->userTransactions->removeElement($userTransaction)) {
            // set the owning side to null (unless already changed)
            if ($userTransaction->getTransaction() === $this) {
                $userTransaction->setTransaction(null);
            }
        }

        return $this;
    }





}
