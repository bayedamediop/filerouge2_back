<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TarifsRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TarifsRepository::class)
 * @ApiResource(
 *          collectionOperations={
 *  "get"={
 *                  "method" = "GET",
 *                  "path" = "/admin/tarifs",
 *                  "normalization_context"={"groups"={"tarif:read"}}
 *                  },
 *                  },
 * )
 */
class Tarifs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups ({"tarif:read"})
     */
    private $born_inf;

    /**
     * @ORM\Column(type="integer")
     * @Groups ({"tarif:read"})
     */
    private $born_supp;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"tarif:read"})
     */
    private $frais;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBornInf(): ?int
    {
        return $this->born_inf;
    }

    public function setBornInf(int $born_inf): self
    {
        $this->born_inf = $born_inf;

        return $this;
    }

    public function getBornSupp(): ?int
    {
        return $this->born_supp;
    }

    public function setBornSupp(int $born_supp): self
    {
        $this->born_supp = $born_supp;

        return $this;
    }

    public function getFrais(): ?string
    {
        return $this->frais;
    }

    public function setFrais(string $frais): self
    {
        $this->frais = $frais;

        return $this;
    }
}
