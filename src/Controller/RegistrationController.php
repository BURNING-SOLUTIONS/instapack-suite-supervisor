<?php

namespace App\Controller;

use App\Entity\Role;
use App\Exception\ValidatorParamNotFoundException;
use App\Service\EncryptService;
use App\Utils\RequestContextParser;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use App\Service\UserService;
use App\Entity\User;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class RegistrationController
{
    private $userService;
    private $request;

    /**
     * RegistrationController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService, RequestStack $request)
    {
        $this->userService = $userService;
        $this->request = $request;
    }

    /**
     * @param User $data
     * @return array
     * In custom controllers By Api-Platform, The __invoke method of the action is called when the matching route is hit.
     * It can return either an instance of Symfony\Component\HttpFoundation\Response (that will be displayed to the client
     * immediately by the Symfony kernel) or, like in this example, an instance of an entity mapped as a resource
     * (or a collection of instances for collection operations). In this case, the entity will pass through all built-in event
     * listeners of API Platform. It will be automatically validated, persisted and serialized in JSON-LD. Then the Symfony kernel
     * will send the resulting document to the client.
     *
     */
    public function __invoke(User $data): User
    {
        $violations = null;
        $resource = null;
        $message = 'Resource create succesfully';
        $status = Response::HTTP_OK;
        $parser = new RequestContextParser($this->request);
        $repeat_password = $parser->getRequestValue('repeat_password');

        if (!$repeat_password) {
            throw new ValidatorParamNotFoundException('repeat_password');
        }
        if (!$data->getEmail()) {
            throw new ValidatorParamNotFoundException('email');
        }
        $arrRolesName = $this->userService->denormalizeRoles($data->getSingelRoles());
        $data->setRoles($arrRolesName);
        $this->userService->registerUser($data, $repeat_password);
        #try {
        #$this->userService->registerUser($data);
        #} catch (ValidationException $exception) {
        #$message = 'Validation error';
        #$status = Response::HTTP_BAD_REQUEST;
        #$violations = $exception->getMessage();
        #return new JsonResponse(array('status' => $status, 'message' => $message, 'violation' => $violations), $status);
        #}
        return $data;
    }
}
