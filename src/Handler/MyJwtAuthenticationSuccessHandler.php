<?php

namespace App\Handler;


use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\HttpFoundation\RedirectResponse;
Use App\Service\RedisCacheService;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * MyJwtAuthenticationSuccessHandler.
 *
 * @author Ing.Juan Ramón Borges
 *
 *  This handler extend original success Authentication of Lexik-JwtAuthentication-Bundle, the main reason is
 *  that the system implement some thing like Oauth2 standard and SSO, and we need to response the authentication success request
 *  with redirection response to application url page.
 */
class MyJwtAuthenticationSuccessHandler extends AuthenticationSuccessHandler
{

    public function handleAuthenticationSuccess(UserInterface $user, $jwt = null)
    {
        if (null === $jwt) {
            $jwt = $this->jwtManager->create($user);
        }

        $response = new JWTAuthenticationSuccessResponse($jwt);
        $event = new AuthenticationSuccessEvent(['token' => $jwt], $user, $response);

        #Authentication success event is now listen by JwtAuthenticationListener class..
        if ($this->dispatcher instanceof ContractsEventDispatcherInterface) {
            $this->dispatcher->dispatch($event, Events::AUTHENTICATION_SUCCESS);
        } else {
            $this->dispatcher->dispatch(Events::AUTHENTICATION_SUCCESS, $event);
        }

        #$response->headers->setCookie(new Cookie('instapack_suite_jwt', $jwt, new \DateTime('tomorrow'), '/', 'localhost', false, true, false, 'lax'));

        #Once then finish event success propagation is time to response to clien
        $response->setData($event->getData());
        return $response;

        #in case of app redirection external link (Deprecated now session is managed by redis);
        #return new RedirectResponse('http://localhost:3000/#/roles?token='.$jwt);
    }
}
