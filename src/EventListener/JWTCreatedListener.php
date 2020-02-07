<?php

namespace App\EventListener;

use App\Entity\Application;
use App\Exception\AppUnauthorizedHttpException;
use App\Service\EncryptService;
use App\Service\OauthService;
use App\Service\PermissionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use App\Exception\ApplicationIdNotFoundException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidPayloadException;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\ApplicationService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Exception\ObjectNotFoundException;
use Symfony\Component\Serializer\Encoder\JsonDecode;

/*
 * This listener exist because Im listen JWT-Created-Event from (Jwt-Lexik-Bundle), the reason is simple it's necessary
 * listen this event because we need to update de payload of the generate json web token (JWT) and include the all roles
 * divided by client application for the current user, on this way thirds apps can be know de permission of user in your apps
 */

class JWTCreatedListener
{

    /**
     * @var RequestStack
     */
    private $requestStack;
    private $manager;
    private $applicationService;
    private $permissionService;
    private $oauthService;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, EntityManagerInterface $manager, ApplicationService $applicationService, PermissionService $permissionService, OauthService $oauthService)
    {
        $this->requestStack = $requestStack;
        $this->manager = $manager;
        $this->applicationService = $applicationService;
        $this->permissionService = $permissionService;
        $this->oauthService = $oauthService;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload = $event->getData();
        $request = $this->requestStack->getCurrentRequest();
        $clientId = $request->query->get('client_id');
        $code = $request->query->get('code');
        $isAuthByOauthCode = ($clientId || $code);

        if ($isAuthByOauthCode && (!$clientId || !$code)) {
            throw new ApplicationIdNotFoundException('We can\'t recognize your app, please provide valid code and/or client_id params');
        }

        if ($clientId && $code) {

            # Handling non-existent app by client id provided
            try {
                $application = $this->applicationService->getApplicationsByParams(['clientId' => $clientId], 'findOneBy');
            } catch (NotFoundHttpException $exception) {
                throw new ApplicationIdNotFoundException('We can\'t recognize your app, please provide valid client_id');
            }

            # Handling non-existent key code in oauth code payload
            try {
                $oauthCode = $this->oauthService->decodeOauthCode($code);
                if (!array_key_exists('code', $oauthCode)) {
                    throw new InvalidPayloadException('code');
                }
            } catch (InvalidPayloadException $e) {
                throw new InvalidPayloadException('code');
            }

            # Handling expired oauth code
            try {
                $oauth = $this->oauthService->getOauthByParams(['accessCode' => $oauthCode['code']], 'findOneBy');
                $isOauthCodeExpired = $this->oauthService->isOauthCodeExpired($oauth);
                if ($isOauthCodeExpired) {
                    throw new AppUnauthorizedHttpException('', 'The code present in this address link to get access token has been expired, please obtain another code in "/api/oauths/oauth2" ');
                }

                # One time check the code is valid we proceed to remove to prevent future utilization
                $this->oauthService->removeOauth($oauth);

            } catch (\Exception $e) {
                if ($e instanceof NotFoundHttpException) {
                    throw new ObjectNotFoundException('oauth');
                }

                if ($e instanceof AppUnauthorizedHttpException) {
                    throw new ApplicationIdNotFoundException($e->getMessage());
                }

            }

        }


        $payload['clientId'] = $clientId;
        $payload['privileges'] = $this->permissionService->getAllPermissionsByRoles($payload['roles']);
        //unset($payload['roles']);
        $header = $event->getHeader();
        $header['cty'] = 'JWT';
        $event->setHeader($header);
    }

}



