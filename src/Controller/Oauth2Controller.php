<?php

namespace App\Controller;

use App\Entity\Oauth;
use App\Entity\Role;
use App\Exception\ApplicationIdNotFoundException;
use App\Exception\ValidatorParamNotFoundException;
use App\Service\ApplicationService;
use App\Service\EncryptService;
use App\Utils\RequestContextParser;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use App\Service\UserService;
use App\Entity\User;
use App\Service\OauthService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class Oauth2Controller
{
    private $oauthService;
    private $applicationService;
    private $request;

    /**
     * RegistrationController constructor.
     * @param UserService $userService
     */
    public function __construct(OauthService $oauthService, ApplicationService $applicationService, RequestStack $request)
    {
        $this->oauthService = $oauthService;
        $this->applicationService = $applicationService;
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
    public function __invoke(): Response
    {
        $parser = new RequestContextParser($this->request);
        $oauth = new Oauth();
        $application = null;
        $code = substr(md5(uniqid(mt_rand(), true)), 0, 8);
        $now = new \DateTime('now');
        $expiration = $now->add(new \DateInterval('PT1H')); #The expiration of generate token code for future authorization token..
        $clientId = $parser->getRequestValue('client_id');

        if (!$clientId) {
            throw new ApplicationIdNotFoundException('We can\'t recognize your app, please provide valid CLIENT_ID');
        }
        try {
            $application = $this->applicationService->getApplicationsByParams(['clientId' => $clientId, 'available' => true], 'findOneBy');
        } catch (NotFoundHttpException $exception) {
            throw new ApplicationIdNotFoundException('We can\'t recognize your app, please provide valid CLIENT_ID');
        }

        $oauth
            ->setApplication($application)
            ->setAccessCode($code)
            ->setExpiration($expiration);

        $this->oauthService->registerOauth($oauth);

        $encryptedCode = $this->oauthService->generateOauthCode($expiration, $code);

        # Return Json Response witch array of code and expiration encrypted
        return new JsonResponse(["code" => $encryptedCode]);
    }
}
