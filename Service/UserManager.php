<?php

namespace KFI\FrameworkBundle\Service;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Security\LoginManagerInterface;

class UserManager
{
    protected $userManager;
    protected $loginManager;
    protected $firewallName;
    protected $securityContext;
    protected $encoderFactory;

    public function __construct(
        UserManagerInterface $userManager,
        LoginManagerInterface $loginManager,
        $firewallName,
        SecurityContext $securityContext,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->userManager     = $userManager;
        $this->loginManager    = $loginManager;
        $this->firewallName    = $firewallName;
        $this->securityContext = $securityContext;
        $this->encoderFactory  = $encoderFactory;
    }

    public function login($email, $password)
    {
        $user = $this->userManager->findUserByEmail($email);
        if (empty($user)) {
            throw new \Exception('bad email');
        }
        if (!$this->validatePassword($user, $password)) {
            throw new \Exception('bad password');
        }

        try {
            $this->loginManager->loginUser(
                $this->firewallName,
                $user
            );
        } catch (AccountStatusException $ex) {
            throw new \Exception('bad account status');
        }
    }

    public function register($email, $password)
    {
        $user = $this->userManager->findUserByEmail($email);
        if (!empty($user)) {
            throw new \Exception('email exists');
        }
        $user = $this->userManager->createUser();
        $user->setEmail($email);
        $user->setUsername($email);
        $user->setPlainPassword($password);
        $user->setEnabled(true);
        $user->addRole('ROLE_SUPER_ADMIN');
        $this->userManager->updateUser($user);
    }

    /**
     * @param UserInterface $user
     * @param $password
     * @return bool
     */
    public function validatePassword(UserInterface $user, $password)
    {
        $pass = $this->encoderFactory
            ->getEncoder($user)
            ->encodePassword($password, $user->getSalt());

        return ($user->getPassword() == $pass);
    }

    /**
     * @return bool
     */
    public function isLogged()
    {
        return $this->securityContext->isGranted('ROLE_USER');
    }

    /**
     * @return mixed
     */
    public function getCurrent(){
        return $this->securityContext->getToken()->getUser();
    }

    /**
     * @return UserInterface
     */
    public function createUser(){
        return $this->userManager->createUser();
    }
}
