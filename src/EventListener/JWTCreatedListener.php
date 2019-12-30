<?php

namespace App\EventListener;

use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use App\Exception\ApplicationIdNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\ApplicationService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, EntityManagerInterface $manager, ApplicationService $applicationService)
    {
        $this->requestStack = $requestStack;
        $this->manager = $manager;
        $this->applicationService = $applicationService;
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
        $clientId = $request->query->get('CLIENT_ID');
        /*if (!$clientId) {
            throw new ApplicationIdNotFoundException('We can\'t recognize your app, please provide valid CLIENT_ID');
        }*/
        try {
            if ($clientId) {
                $this->applicationService->getApplicationsByParams(['clientId' => $clientId], 'findOneBy');
            }
        } catch (NotFoundHttpException $exception) {
            throw new ApplicationIdNotFoundException('We can\'t recognize your app, please provide valid CLIENT_ID');
        }
        $payload['clientId'] = $clientId;
        $payload['privileges'] = $this->applicationService->getAllUnifiedRoles($payload['roles']);
        //unset($payload['roles']);
        $event->setData($payload);

        $header = $event->getHeader();
        $header['cty'] = 'JWT';
        $event->setHeader($header);
    }

}



