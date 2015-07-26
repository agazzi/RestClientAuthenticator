<?php

namespace Agazzi\RestClientAuthenticatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    /**
     * @Route("/api/client/request/{uid}::{token}::{key}::{roles}", name="api_client_request")
     */
    public function requestAction($uid, $token, $key, $roles)
    {
        $apikey = $this->getParameter('api_key');

        $crypter = $this->get('service.client.crypter');

        $data = $crypter->digest($token, $uid, $key, $roles);

        $context = $this->get('security.context');

        $route = 'http://' . $this->getParameter('api_domain') . $this->getParameter('api_redirect_uri');

        $curl = $this->get('service.client.curl');

        $curl->get('http://api.nativgaming.com/user/me', [
            'apikey'    => $apikey,
            'token'     => $data[0]
        ]);

        $query = $curl->response;

        $userdata = (array) json_decode($query);

        $user = $this->get('service.client.user');

        $user = $user->setUser($userdata);

        $session = $this->container->get('session');

        $session->set('user', $user);

        $token = new UsernamePasswordToken($data[0], $token, 'main', $data[2]);

        $context->setToken($token);

        $user = $this->get('service.client.user')->loadUser();

        return $this->redirect($route);
    }
}
