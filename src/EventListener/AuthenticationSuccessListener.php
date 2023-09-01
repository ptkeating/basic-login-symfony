<?php

namespace App\EventListener;
// src/App/EventListener/JWTAuthenticatedListener.php

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener
{
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        /** @var \App\Entity\User */
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $data['user_id'] =  $user->getId();

        $event->setData($data);
    }
}
