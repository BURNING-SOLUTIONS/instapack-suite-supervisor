<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Controller\ApplicationController;

#attributes={"security"="is_granted('ROLE_ADMIN')"}
#attributes={"security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"}

/**
 * @ApiResource(
 *     attributes={"security"="is_granted('ROLE_SUPER_ADMIN')"},
 *     collectionOperations={
 *         "get"={
 *             "security"="is_granted('ROLE_SUPER_ADMIN')",
 *          },
 *         "post"={
 *             "security"="is_granted('ROLE_SUPER_ADMIN')",
 *             "method"="POST",
 *             "controller"=ApplicationController::class
 *         }
 *      }
 *     )
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "description": "partial", "clientId": "partial", "redirectUri": "partial"})
 * @ApiFilter(OrderFilter::class, properties={"id": "ASC", "name": "ASC","description": "ASC", "code": "ASC", "redirectUri": "ASC", "clientId": "ASC"}, arguments={"orderParameterName"="order"})
 * @ApiFilter(BooleanFilter::class)
 * @ORM\Table(name="core_application")
 * @ORM\Entity(repositoryClass="App\Repository\ApplicationRepository")
 * @UniqueEntity("name", message="There is already Applcation with this name")
 * @UniqueEntity("clientId", message="There is already Applcation with this Client ID")
 */
class Application
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @Groups({"Role_Apps","Permission_Relations"})
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     * @Assert\NotNull()
     * @ApiFilter(SearchFilter::class, strategy="partial")
     * @Groups({"Role_Apps","Permission_Relations"})
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @Groups({"Role_Apps"})
     * @ORM\Column(type="boolean", nullable=true, options={"default" = false})
     */
    private $available;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", inversedBy="applications")
     * @JoinTable(name="core_application_role",
     *      joinColumns={@JoinColumn(name="application_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="role_id", referencedColumnName="id")}
     *    )
     * @ApiFilter(SearchFilter::class, properties={"roles.name": "partial"})
     */
    private $roles;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $clientId;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $redirectUri;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $logo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Permission", mappedBy="application")
     */
    private $permissions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Oauth", mappedBy="application", orphanRemoval=true)
     */
    private $oauths;


    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->oauths = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

        return $this;
    }

    public function __toString(): string
    {
        // TODO: Implement __toString() method.
        return $this->name;

    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getRedirectUri(): ?string
    {
        return $this->redirectUri;
    }

    public function setRedirectUri(string $redirectUri): self
    {
        $this->redirectUri = $redirectUri;

        return $this;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function setLogo($logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection|Permission[]
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions[] = $permission;
            $permission->setApplication($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->contains($permission)) {
            $this->permissions->removeElement($permission);
            // set the owning side to null (unless already changed)
            if ($permission->getApplication() === $this) {
                $permission->setApplication(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Oauth[]
     */
    public function getOauths(): Collection
    {
        return $this->oauths;
    }

    public function addOauth(Oauth $oauth): self
    {
        if (!$this->oauths->contains($oauth)) {
            $this->oauths[] = $oauth;
            $oauth->setApplication($this);
        }

        return $this;
    }

    public function removeOauth(Oauth $oauth): self
    {
        if ($this->oauths->contains($oauth)) {
            $this->oauths->removeElement($oauth);
            // set the owning side to null (unless already changed)
            if ($oauth->getApplication() === $this) {
                $oauth->setApplication(null);
            }
        }

        return $this;
    }

}
