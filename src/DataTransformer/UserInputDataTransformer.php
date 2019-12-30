<?php
// src/DataTransformer/BookInputDataTransformer.php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\Role;
use \Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

final class UserInputDataTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($data, string $to, array $context = [])
    {
        $user = new User();
        $user->setEmail($data->email);
        $user->setPassword($data->password);
        $user->setPhone($data->phone);
        $user->setCreatedAt($data->createdAt);
        $arrRolesPath = $data->roles;
        $arrGroupsPath = $data->groups;
        $roles = array();
        foreach ($arrRolesPath as $role) {
            $rolId = explode("/", $role)[3];
            $roleName = $this->manager->find(Role::class, (int)$rolId)->getName();
            array_push($roles, $roleName);
        }
        foreach ($arrGroupsPath as $group){
            $user->addGroup($group);
        }
        $user->setRoles($roles);
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        // in the case of an input, the value given here is an array (the JSON decoded).
        // if it's a book we transformed the data already
        if ($data instanceof User) {
            return false;
        }

        return User::class === $to && null !== ($context['input']['class'] ?? null);
    }
}