<?php

namespace App\EventListener;

use App\Service\RedisCacheService;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Symfony\Component\Security\Core\Security;

/*
 * This listener exist because Im listen JWT-Created-Event from (Jwt-Lexik-Bundle), the reason is simple it's necessary
 * listen this event because we need to update de payload of the generate json web token (JWT) and include the all roles
 * divided by client application for the current user, on this way thirds apps can be know de permission of user in your apps
 */

class JWTExpiredListener
{

    private $security;
    private $redis;

    /**
     * JWTExpiredListener constructor.
     * @param Security $security
     */
    public function __construct(Security $security, RedisCacheService $redis)
    {
        $this->security = $security;
        $this->redis = $redis;
    }

    /**
     * @param JWTExpiredEvent $event
     *
     * @return void
     */
    public function onJWTExpired(JWTExpiredEvent $event)
    {
        $username = $this->security->getUser()->getUsername();
        if ($this->redis->exist($username)) {
            $this->redis->invalidate($username);
        }

    }

}



