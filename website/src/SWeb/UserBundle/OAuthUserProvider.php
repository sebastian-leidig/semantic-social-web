<?php

namespace SWeb\UserBundle;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseOAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;


class OAuthUserProvider extends BaseOAuthUserProvider {

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $user_details = $response->getResponse();

        if(!isset($user_details['hd']) || $user_details['hd'] !== "iiitd.ac.in")
            return null; //TODO: Show error

        $user = new OAuthUser($user_details['email']);
        return $user;
    }
}
