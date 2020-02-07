<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     attributes={"security"="is_granted('ROLE_SUPER_ADMIN')", "security_message"="Only admins can manage Roles"},
 *     normalizationContext={"groups"={"Permission_Relations"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\PermissionRepository")
 * @ORM\Table(name="core_permission")
 */
class Permission
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @Groups({"Permission_Relations"})
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @ApiFilter(SearchFilter::class, strategy="partial")
     * @Groups({"Permission_Relations"})
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="permissions")
     * @Groups({"Permission_Relations"})
     * @ApiFilter(SearchFilter::class, properties={"role.name": "partial"})
     * @ApiSubresource
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Application", inversedBy="permissions")
     * @Groups({"Permission_Relations"})
     * @ApiFilter(SearchFilter::class, properties={"application.name": "partial"})
     * @ApiSubresource
     */
    private $application;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Privilege", inversedBy="permissions")
     * @Groups({"Permission_Relations"})
     * @ApiFilter(SearchFilter::class, properties={"privilege.name": "partial"})
     * @ApiSubresource
     */
    private $privilege;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"Permission_Relations"})
     */
    private $available;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getPrivilege(): ?Privilege
    {
        return $this->privilege;
    }

    public function setPrivilege(?Privilege $privilege): self
    {
        $this->privilege = $privilege;

        return $this;
    }

    public function getAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(?bool $available): self
    {
        $this->available = $available;

        return $this;
    }
}
