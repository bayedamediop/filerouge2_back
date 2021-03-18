<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource(
 *           attributes={
 *        
 *      },
 *      collectionOperations={
 *          "get"={
 *                  "method" = "GET",
 *                  "path" = "/admin/users",
 *                  "normalization_context"={"groups"={"user:read"}}
 *                  },
 *          "add_users"={
 *                  "route_name"="addUser",
 *              },
 *
 *      },
 *     itemOperations={
 *          "mest_ransactions"={
 *                  "method" = "GET",
 *                  "path" = "/admin/user/{id}",
 *                 "normalization_context"={"groups"={"transactions:read"}}

 *              },
 *     "getuserbyid"={
 *                  "method" = "GET",
 *                  "path" = "/admin/users/{id}",
 *                 "normalization_context"={"groups"={"userbyid:read"}}

 *              },
 *            "archiver_users"={
 *                      "route_name"="archiver",
 *              },
 * 
 *            "find_Transaction_depot"={
 *                  "route_name"="findTransactiondepot",
 *              },
 *  "find_Transaction_retrait"={
 *                  "route_name"="findTransactionretrait",
 *              },
 *      "putUserId":{
 *           "method":"put",
 *          "path":"/admin/users/{id}",
 *
 *              "deserialize"= false,
 *          },
 *
 *         
 * },
 * )
 * @UniqueEntity ("email",
 *      message="Ndanidite dougalalle benénn Email bi Amne!!!!!.")
 *  @UniqueEntity(
 * fields={"email"},
 * message={"cet email est déjà utilisé"})
 *
 */

class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"agence:write","user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="L ' email doit etre unique")
     * * @Assert\Email(message = "The email '{{ value }}' is not a valid email.")
     * @Groups ({"user:read","depot:write","userbyid:read"})
     * 
     */
    private $email;

    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups ({"userbyid:read"})
     * @)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"userdepot:read","user:read","getOndepotUserCompt:read",
     * "numcompte:read","transactionunuser:read","getOndepotUserCompt:read",
     *     "cmopte:read","userbyid:read"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"userdepot:read","user:read","getOndepotUserCompt:read",
     *     "numcompte:read","transactionunuser:read","cmopte:read","userbyid:read"})
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"userdepot:read","getOndepotUserCompt:read","user:read","userbyid:read"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"userdepot:read","userbyid:read"})
     */
    private $cni;

    /**
     * @ORM\Column(type="blob", nullable=true)
     *  @Groups ({"user:read","userbyid:read"})
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"userdepot:read","getOndepotUserCompt:read"})
     *  @Groups ({"user:read","userbyid:read"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="boolean")
     *
     */
    private $archivage = 0;

    /**
     * @ORM\ManyToOne(targetEntity=Profils::class, inversedBy="profil")
     * @ApiSubresource()
     * @Groups ({"userdepot:read"})
     */
    private $profils;

    /**
     * @ORM\OneToMany(targetEntity=Depots::class, mappedBy="users",cascade={"persist"})
     * @Groups ({"userdepot:read"})
     */
    private $depots;

    /**
     * @ORM\OneToMany(targetEntity=Agences::class, mappedBy="user",cascade={"persist"})
     * @Groups ({"userdepot:read","user:read"})
     */
    private $agences;


    /**
     * @ORM\OneToMany(targetEntity=Depots::class, mappedBy="users")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=UserTransaction::class, inversedBy="user")
     *
     */
    private $userTransaction;

    /**
     * @ORM\OneToMany(targetEntity=UserTransaction::class, mappedBy="user")
     *  @Groups ({"transactions:read"})
     */
    private $userTransactions;



    public function __construct()
    {
        $this->depots = new ArrayCollection();
        $this->user = new ArrayCollection();
        $this->agences = new ArrayCollection();
        $this->userTransactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_'.$this->profils->getLibelle();

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

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

    public function getCni(): ?int
    {
        return $this->cni;
    }

    public function setCni(int $cni): self
    {
        $this->cni = $cni;

        return $this;
    }

    public function getAvatar()
    {
        $avatar = $this->avatar;
        if ($avatar) {
            return (base64_encode(stream_get_contents($this->avatar)));
        }
        return $avatar;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getArchivage(): ?bool
    {
        return $this->archivage;
    }

    public function setArchivage(bool $archivage): self
    {
        $this->archivage = $archivage;

        return $this;
    }

    public function getProfils(): ?Profils
    {
        return $this->profils;
    }

    public function setProfils(?Profils $profils): self
    {
        $this->profils = $profils;

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
            $depot->setUser($this);
        }

        return $this;
    }

    public function removeDepot(Depots $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getUsers() === $this) {
                $depot->setUsers(null);
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
            $agence->setUser($this);
        }

        return $this;
    }

    public function removeAgence(Agences $agence): self
    {
        if ($this->agences->removeElement($agence)) {
            // set the owning side to null (unless already changed)
            if ($agence->getUser() === $this) {
                $agence->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Depots[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(Depots $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
            $user->setUsers($this);
        }

        return $this;
    }

    public function removeUser(Depots $user): self
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getUsers() === $this) {
                $user->setUsers(null);
            }
        }

        return $this;
    }

    public function getUserTransaction(): ?UserTransaction
    {
        return $this->userTransaction;
    }

    public function setUserTransaction(?UserTransaction $userTransaction): self
    {
        $this->userTransaction = $userTransaction;

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
            $userTransaction->setUser($this);
        }

        return $this;
    }

    public function removeUserTransaction(UserTransaction $userTransaction): self
    {
        if ($this->userTransactions->removeElement($userTransaction)) {
            // set the owning side to null (unless already changed)
            if ($userTransaction->getUser() === $this) {
                $userTransaction->setUser(null);
            }
        }

        return $this;
    }


}
