<?php

namespace App\Controller;

use App\Entity\Application;
use App\Service\ApplicationService;
use App\Exception\ValidatorParamNotFoundException;
use App\Service\EncryptService;
use App\Utils\RequestContextParser;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use App\Service\UserService;
use App\Entity\User;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class ApplicationController
{
    private $applicationService;
    private $request;

    /**
     * RegistrationController constructor.
     * @param UserService $userService
     */
    public function __construct(ApplicationService $applicationService, RequestStack $request)
    {
        $this->applicationService = $applicationService;
        $this->request = $request;
    }

    /**
     * @param Application $data
     * @return array
     * In custom controllers By Api-Platform, The __invoke method of the action is called when the matching route is hit.
     * It can return either an instance of Symfony\Component\HttpFoundation\Response (that will be displayed to the client
     * immediately by the Symfony kernel) or, like in this example, an instance of an entity mapped as a resource
     * (or a collection of instances for collection operations). In this case, the entity will pass through all built-in event
     * listeners of API Platform. It will be automatically validated, persisted and serialized in JSON-LD. Then the Symfony kernel
     * will send the resulting document to the client.
     *
     */
    public function __invoke(Application $data): Application
    {
        $this->applicationService->saveApplication($data);
        return $data;
    }
}
