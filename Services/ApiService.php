<?php

namespace Agazzi\RestClientAuthenticatorBundle\Services;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class ApiService extends Controller
{
    /**
     * Call force logout in rounded network
     *
     */
    public function logout()
    {
        $context = $this->get('security.context');
        $context->setToken(null);

        $session = $this->container->get('session');
        $session->remove('user');

        return true;
    }
}
