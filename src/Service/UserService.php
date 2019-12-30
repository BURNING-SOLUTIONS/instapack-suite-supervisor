<?php

namespace App\Service;

use App\Entity\Role;
use App\Exception\AppEntityValidationException;
use App\Exception\ApplicationIdNotFoundException;
use App\Exception\ValidatorParamNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use ApiPlatform\Core\Validator\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $dbManager;
    private $encoder;
    private $validator;
    private $userRepository;
    private $normalizer;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator, DenormalizerInterface $normalizer)
    {
        $this->dbManager = $manager;
        $this->encoder = $encoder;
        $this->validator = $validator;
        $this->userRepository = $this->dbManager->getRepository(User::class);
        $this->normalizer = $normalizer;
    }

    public function denormalizeRoles(array $stringRolesWay): array
    {
        $rolesName = array();
        foreach ($stringRolesWay as $roleWay) {
            $role = $this->normalizer->denormalize($roleWay, Role::class);
            array_push($rolesName, $role->getName());
        }
        return $rolesName;
    }

    public function getUserByParams(array $params, string $strategy = 'findBy'): User
    {
        $user = $this->userRepository->{$strategy}($params);
        if (!$user) {
            throw new NotFoundHttpException("The user with provide data does not exist");
        }
        return $user;
    }

    /**
     * @param User $user
     * @return array
     */
    public function registerUser(User $user): void
    {
        $errors = $this->validator->validate($user);
        /*
        if (count($errors) > 0) {
            throw new ValidationException($errors[0]->getMessage());
        } else ...
        */
        if (!count($errors) > 0) {
            $encodedPassword = $this->encoder->encodePassword($user, $user->getPassword());
            $this->userRepository->persistUser($user, $encodedPassword);
        };
    }

    public function updateUser(User $user): void
    {
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new ValidatorException($errors[0]->getMessage());
        };
        $encodedPassword = $this->encoder->encodePassword($user, $user->getPassword());
        $this->userRepository->updateUser($user, $encodedPassword);
    }
}