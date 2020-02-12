<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Dto\UserInput;
use App\Controller\RegistrationController;
use App\Controller\PasswordResetController;

# "output"=false,
# input=UserInput::class,

/**
 * @ApiResource(
 *     collectionOperations={
 *         "get"={
 *             "security"="is_granted('ROLE_SUPER_ADMIN','ROLE_APPLICATION_ADMIN')",
 *          },
 *         "post"={
 *             "security"="is_granted('ROLE_SUPER_ADMIN')",
 *             "method"="POST",
 *             "controller"=RegistrationController::class
 *         },
 *     },
 *     itemOperations={
 *         "get",
 *         "put",
 *         "patch",
 *         "delete",
 *         "post_reset_password"={
 *              "security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *              "method"="POST",
 *              "path"="/users/password/{operation}",
 *              "requirements"={"operation"="reset|request"},
 *              "controller"=PasswordResetController::class,
 *              "read"=false
 *     }
 *   }
 * )
 * @ApiFilter(OrderFilter::class, properties={"id": "ASC", "email": "ASC", "phone": "ASC"}, arguments={"orderParameterName"="order"})
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="core_user")
 * @UniqueEntity("email", message="There is already a user with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ApiFilter(SearchFilter::class, strategy="exact")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=false)
     * @Assert\Email()
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @ApiFilter(SearchFilter::class, strategy="partial")
     */
    private $email;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^\S*(?=\S{6,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/",
     *     message="La contraseña debe tener al menos, 6 carácteres, 1 letra minúscula, 1 letra mayúscula y 1 carácter numérico."
     * )
     * @ORM\Column(type="string", nullable=false)
     */
    private $password;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", inversedBy="users")
     * @JoinTable(name="core_user_group",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="group_id", referencedColumnName="id")}
     *    )
     */
    private $groups;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Regex(pattern="/(?:\d{1}\s)?\(?(\d{3})\)?-?\s?(\d{3})-?\s?(\d{4})/", message="El teléfono proporcionado es inválido.")
     * @ApiFilter(SearchFilter::class, strategy="partial")
     */
    private $phone;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
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
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $groups = $this->getGroups();
        $finalRoles = array();

        foreach ($groups as $group) {
            $groupRoles = $group->getRoles();
            foreach ($groupRoles as $role) {
                array_push($finalRoles, $role->getName());
            }
        }
        $unifiedRoles = array_unique(array_merge($roles, $finalRoles));
        $result = array();
        foreach ($unifiedRoles as $key => $name) {
            array_push($result, $name);
        }
        // guarantee every user at least has ROLE_USER
        //if (!$roles) $roles[] = 'ROLE_GUEST';
        return $result ?: ['ROLE_GUEST'];
    }

    public function getSingelRoles()
    {
        return $this->roles;
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
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}
