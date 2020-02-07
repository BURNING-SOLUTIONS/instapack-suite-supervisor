<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     attributes={"security"="is_granted('ROLE_SUPER_ADMIN')", "security_message"="Only admins can manage Roles"},
 *     normalizationContext={"groups"={"Role_Apps"}}
 *     )
 * @ApiFilter(OrderFilter::class, properties={"id": "ASC", "name": "ASC"}, arguments={"orderParameterName"="order"})
 * @ORM\Table(name="core_role")
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 * @UniqueEntity("name", message="There is already a Role with this name")
 */
class Role
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @Groups({"Role_Apps","Permission_Relations"})
     * @ApiFilter(SearchFilter::class, strategy="exact")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     * @ApiFilter(SearchFilter::class, strategy="partial")
     * @Assert\NotNull()
     * @Groups({"Role_Apps","Groups_Roles","Permission_Relations"})
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"Role_Apps"})
     * @ApiFilter(SearchFilter::class, strategy="partial")
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" = false})
     * @Groups({"Role_Apps","Groups_Roles"})
     * @ApiFilter(BooleanFilter::class)
     */
    private $available;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Application", mappedBy="roles")
     * @Groups({"Role_Apps"})
     * @ApiFilter(SearchFilter::class, properties={"applications.name": "partial"})
     * @ApiSubresource
     */
    private $applications;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", mappedBy="roles")
     */
    private $groups;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Permission", mappedBy="role")
     */
    private $permissions;


    public function __construct()
    {
        $this->applications = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->permissions = new ArrayCollection();

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
     * @return Collection|Application[]
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications[] = $application;
            $application->addRole($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->contains($application)) {
            $this->applications->removeElement($application);
            $application->removeRole($this);
        }

        return $this;
    }

    /**
     * @return Collection|Groups[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Groups $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addRole($this);
        }

        return $this;
    }

    public function removeGroup(Groups $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeRole($this);
        }

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
            $permission->setRole($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->contains($permission)) {
            $this->permissions->removeElement($permission);
            // set the owning side to null (unless already changed)
            if ($permission->getRole() === $this) {
                $permission->setRole(null);
            }
        }

        return $this;
    }

}
