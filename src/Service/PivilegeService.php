<?php

namespace App\Service;

use App\Entity\Privilege;
use App\Exception\AppEntityValidationException;
use App\Exception\ValidatorParamNotFoundException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Application;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PivilegeService
{
    private $dbManager;
    private $validator;
    private $privilegesRepository;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $manager
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $this->dbManager = $manager;
        $this->validator = $validator;
        $this->privilegesRepository = $this->dbManager->getRepository(Privilege::class);
    }

}