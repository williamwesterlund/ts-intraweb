<?php

namespace App\Listeners;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;

class AuthenticationSuccessListener {

    private $secure = false;
    private $tokenTtl;

    public function __construct($tokenTtl) {

        $this->tokenTtl = $tokenTtl;
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)  {
        $response = $event->getResponse();
        $data = $event->getData();

        // TODO: Fix this and the TokenTtl
        $response->headers->clearCookie('Bearer');

        $token = $data['token'];
        unset($data['token']);
        $event->setData($data);

        $response->headers->setCookie(
            new Cookie('Bearer', $token,
                (new \DateTime())->add(new \DateInterval('PT' . $this->tokenTtl . 'S'))
                ), '/', null, $this->secure
        );
    }
}