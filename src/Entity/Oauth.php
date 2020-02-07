<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Oauth2Controller;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *    collectionOperations={
 *         "get"={
 *             "security"="is_granted('ROLE_SUPER_ADMIN','ROLE_APPLICATION_ADMIN')",
 *          },
 *         "post"={
 *              "security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *              "method"="POST",
 *              "path"="oauths/oauth2",
 *              "controller"=Oauth2Controller::class,
 *              "read"=false
 *         },
 *     }
 *)
 * @ORM\Entity(repositoryClass="App\Repository\OauthRepository")
 * @ORM\Table(name="core_oauth")
 */
class Oauth
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $accessCode;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiration;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Application", inversedBy="oauths")
     * @ORM\JoinColumn(nullable=false)
     */
    private $application;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccessCode(): ?string
    {
        return $this->accessCode;
    }

    public function setAccessCode(string $accessCode): self
    {
        $this->accessCode = $accessCode;

        return $this;
    }

    public function getExpiration(): ?\DateTimeInterface
    {
        return $this->expiration;
    }

    public function setExpiration(\DateTimeInterface $expiration): self
    {
        $this->expiration = $expiration;

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
}
