<?php

namespace BDC\PollBundle\Service;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class PasswordEncrypt implements PasswordEncoderInterface
{

    public function encodePassword($raw, $salt)
    {
       
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded === $this->encodePassword($raw, $salt);
    }

}