<?php

namespace Agazzi\RestClientAuthenticatorBundle\Services;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class CrypterService extends Controller
{
    /**
     * Secret key used for encryption / decryption
     * @var string
     */
    private $key;

    /**
     * Mcrypt generated IV
     * @var string
     */
    private $_iv;

    /**
     * The constructor
     * @throws \Exception
     * @throws \Exception
     */
    public function __construct() {
        if (!function_exists('mcrypt_encrypt')) {
            throw new \Exception("Mcrypt library is not loaded on your server !");
        } else {
            $this->_iv = mcrypt_create_iv(32);
        }

    }

    /**
     * Set key
     * @param string $key
     * @return key
     */
    function setKey($key) {
        $this->key = $key;
    }

    /**
     * Encrypt
     * @param string $input
     * @return string
     */
    function encrypt($input) {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,
            $this->key, $input, MCRYPT_MODE_ECB, $this->_iv));
    }

    /**
     * Decrypt
     * @param string $input
     * @return string
     */
    function decrypt($input) {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256,
            $this->key, base64_decode($input), MCRYPT_MODE_ECB, $this->_iv));
    }

    /**
     * Digest
     * @param string $token
     * @param string $uid
     * @param string $key
     * @param string $roles
     * @return string
     */
    function digest($token, $uid, $key, $roles) {
        $token = str_replace("&=&", "/", $token);
        $key = str_replace("&=&", "/", $key);
        $uid = str_replace("&=&", "/", $uid);
        $roles = str_replace("&=&", "/", $roles);

        $digest = new MessageDigestPasswordEncoder;
        $privatekey = $digest->encodePassword($this->getParameter('api_domain'), $token);

        if (strlen($privatekey) > 32) {
            $privatekey = substr($privatekey, 0, 32);
        }

        $this->setKey($privatekey);

        $data[] = $this->decrypt($uid);
        $data[] = $this->decrypt($key);
        $data[] = (array) $this->decrypt($roles);

        return $data;
    }
}
