<?php

namespace App\EventListener;

use App\Service\ApplicationService;
use App\Service\RedisCacheService;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/*
 * This event is for when user is correct authenticate, add attributes for the final response
 *  just together with token value.
 *
*/

class JWTAuthenticationSuccessListener
{
    private $redis;
    private $requestStack;
    private $applicationService;

    public function __construct(RedisCacheService $redis, RequestStack $requestStack, ApplicationService $applicationService)
    {
        $this->redis = $redis;
        $this->requestStack = $requestStack;
        $this->applicationService = $applicationService;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $clientId = $request->query->get('client_id');
        $data = $event->getData();
        $user = $event->getUser();
        $data['email'] = $user->getUsername();
        #$redisData = array('token' => $data['token'], 'data' => $user->getRoles());
        $this->redis->set($user->getUsername(), $data['token'], RedisCacheService::TTL_DAY);
        if ($clientId) {
            $application = $this->applicationService->getApplicationsByParams(['clientId' => $clientId], 'findOneBy');
            $data['redirect'] = $application->getRedirectUri().'?external_token='.$data['token'];
        }

        if (!$user instanceof UserInterface) {
            return;
        }
        $event->setData($data);
    }

}
