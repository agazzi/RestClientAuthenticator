<?php

namespace Agazzi\RestClientAuthenticatorBundle\Services;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class UserService extends Controller
{
    public function setUser($userdata)
    {
        $class = $this->getParameter('api_user_entity');

        $user = new $class;
        $user->setId($userdata['id']);
        $user->setUsername($userdata['username']);
        $user->setFirstname($userdata['firstname']);
        $user->setLastname($userdata['lastname']);
        $user->setEmail($userdata['email']);
        $user->setEnabled($userdata['enabled']);
        $user->setLocked($userdata['locked']);
        $user->setExpired($userdata['expired']);
        $user->setRoles($userdata['roles']);
        $user->setUserKey($userdata['userKey']);

        return $user;
    }

    public function loadUser()
    {
        $apikey = $this->getParameter('api_key');

        $token = $this->getRequest()->getSession()->get('user')->getUsername();

        $curl = $this->get('service.client.curl');

        $curl->get('http://api.nativgaming.com/user/me', [
            'apikey'    => $apikey,
            'token'     => $token
        ]);

        $query = $curl->response;

        $userdata = (array) json_decode($query);

        $user = $this->setUser($userdata);

        return $user;
    }
}
