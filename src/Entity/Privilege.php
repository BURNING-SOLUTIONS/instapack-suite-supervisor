<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     attributes={"security"="is_granted('ROLE_SUPER_ADMIN')", "security_message"="Only admins can manage Privileges"}
 * )
 * @ApiFilter(OrderFilter::class, properties={"id": "ASC", "name": "ASC", "description": "ASC"}, arguments={"orderParameterName"="order"})
 * @ORM\Entity(repositoryClass="App\Repository\PrivilegeRepository")
 * @ORM\Table(name="core_privilege")
 * @UniqueEntity("name", message="There is already a Privilege with this name")
 */
class Privilege
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @Groups({"Permission_Relations"})
     * @ApiFilter(SearchFilter::class, strategy="exact")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     * @Groups({"Permission_Relations"})
     * @ApiFilter(SearchFilter::class, strategy="partial")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @ApiFilter(SearchFilter::class, strategy="partial")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Permission", mappedBy="privilege")
     */
    private $permissions;

    public function __construct()
    {
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
            $permission->setPrivilege($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->contains($permission)) {
            $this->permissions->removeElement($permission);
            // set the owning side to null (unless already changed)
            if ($permission->getPrivilege() === $this) {
                $permission->setPrivilege(null);
            }
        }

        return $this;
    }

}
