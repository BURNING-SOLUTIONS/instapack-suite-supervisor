<?php

namespace App\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Application;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ApplicationService
{
    private $dbManager;
    private $encoder;
    private $validator;
    private $applicationRepository;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator)
    {
        $this->dbManager = $manager;
        $this->encoder = $encoder;
        $this->validator = $validator;
        $this->applicationRepository = $this->dbManager->getRepository(Application::class);
    }

    /**
     * @param int $id
     * @return Application
     */
    public function getApplication(int $id): Application
    {
        return $this->applicationRepository->find($id);
    }

    /**
     * @param array $params
     * @param string $strategy
     * @return array
     */
    public function getApplicationsByParams(array $params, string $strategy = 'findBy'): Application
    {
        $application = $this->applicationRepository->{$strategy}($params);
        if (!$application) {
            throw new NotFoundHttpException();
        }
        return $application;
    }

    /**
     * @param array $roles
     * @return array
     */
    public function getAllUnifiedRoles(array $roles): array
    {
        return $this->applicationRepository->findRolesByApplications($roles);
    }
}