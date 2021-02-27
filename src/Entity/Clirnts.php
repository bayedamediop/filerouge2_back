<?php

namespace App\Entity;

use App\Repository\ClirntsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClirntsRepository::class)
 */
class Clirnts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $nomComplet;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $cni;

    /**
     * @ORM\ManyToMany(targetEntity=Transactions::class, mappedBy="client",cascade={"persist"})
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity=Transactions::class, mappedBy="clientEnvoie")
     */
    private $transaction;

    public function __construct()
    {
        $this->transaction = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): self
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCni(): ?string
    {
        return $this->cni;
    }

    public function setCni(string $cni): self
    {
        $this->cni = $cni;

        return $this;
    }

    /**
     * @return Collection|Transactions[]
     */
    public function getTransaction(): Collection
    {
        return $this->transaction;
    }

    public function addTransaction(Transactions $transaction): self
    {
        if (!$this->transaction->contains($transaction)) {
            $this->transaction[] = $transaction;
        }

        return $this;
    }

    public function removeTransaction(Transactions $transaction): self
    {
        $this->transaction->removeElement($transaction);

        return $this;
    }

    /**
     * @return Collection|Transactions[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }
}
