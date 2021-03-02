<?php

namespace App\Entity;

use App\Repository\TypeTransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TypeTransactionRepository::class)
 */
class TypeTransaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"numcompte:read"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="float")
     * @Groups ({"numcompte:read"})
     */
    private $frais;

    /**
     * @ORM\OneToMany(targetEntity=Transactions::class, mappedBy="typeTransaction")
     */
    private $typeTransaction;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups ({"numcompte:read"})
     */
    private $dateTransaction;

    public function __construct()
    {
        $this->typeTransaction = new ArrayCollection();
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

    public function getFrais(): ?float
    {
        return $this->frais;
    }

    public function setFrais(float $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    /**
     * @return Collection|Transactions[]
     */
    public function getTypeTransaction(): Collection
    {
        return $this->typeTransaction;
    }

    public function addTypeTransaction(Transactions $typeTransaction): self
    {
        if (!$this->typeTransaction->contains($typeTransaction)) {
            $this->typeTransaction[] = $typeTransaction;
            $typeTransaction->setTypeTransaction($this);
        }

        return $this;
    }

    public function removeTypeTransaction(Transactions $typeTransaction): self
    {
        if ($this->typeTransaction->removeElement($typeTransaction)) {
            // set the owning side to null (unless already changed)
            if ($typeTransaction->getTypeTransaction() === $this) {
                $typeTransaction->setTypeTransaction(null);
            }
        }

        return $this;
    }

    public function getDateTransaction(): ?\DateTimeInterface
    {
        return $this->dateTransaction;
    }

    public function setDateTransaction(?\DateTimeInterface $dateTransaction): self
    {
        $this->dateTransaction = $dateTransaction;

        return $this;
    }

}
