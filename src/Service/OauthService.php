<?php

namespace App\Service;


use App\Entity\Oauth;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class OauthService
{
    private $dbManager;
    private $validator;
    private $oauthRepository;
    private $encoder;
    private $decoder;
    private $encrypter;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface $manager, ValidatorInterface $validator, EncryptService $encrypter)
    {
        $this->dbManager = $manager;
        $this->validator = $validator;
        $this->encrypter = $encrypter;
        $this->encoder = new JsonEncode();
        $this->decoder = new JsonDecode();
        $this->oauthRepository = $this->dbManager->getRepository(Oauth::class);
    }

    /**
     * @param string $randomCode
     * @return string
     */
    public function generateOauthCode(\DateTime $expiration, string $randomCode): string
    {
        $code = $randomCode;
        $code = $this->encoder->encode(array("code" => $code, "expiration" => $expiration->format('Y-m-d H:i:s')), 'json');

        return $this->encrypter->encrypt($code);
    }

    public function decodeOauthCode(string $code)
    {
        $code = str_replace(' ', '+', $code);
        $codeDecrypt = $this->encrypter->decrypt($code);
        return get_object_vars($this->decoder->decode($codeDecrypt, 'array'));
    }

    public function getOauthByParams(array $params, string $strategy = 'findBy'): Oauth
    {
        $oauth = $this->oauthRepository->{$strategy}($params);
        if (!$oauth) {
            throw new NotFoundHttpException();
        }
        return $oauth;
    }

    public function removeOauth(Oauth $auth): void
    {
        $this->oauthRepository->removeOauth($auth);
    }

    /**
     * @param Oauth $auth
     * @return array
     */
    public function registerOauth(Oauth $oauth): void
    {
        $errors = $this->validator->validate($oauth);
        if (!count($errors) > 0) {
            $this->oauthRepository->persistOauth($oauth);
        };
    }

    /**
     * @param Oauth $oauth
     * @return bool
     * @throws \Exception
     */
    public function isOauthCodeExpired(Oauth $oauth): bool
    {
        $now = new \DateTime('now');

        if (!$oauth || $oauth->getExpiration() < $now) {
            return true;
        }

        return false;
    }

}