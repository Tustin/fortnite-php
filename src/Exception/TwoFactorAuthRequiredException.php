<?php
namespace Fortnite\Exception;

class TwoFactorAuthRequiredException extends \Exception 
{
    private $challenge;

    public function __construct($challenge)
    {
        $this->challenge = $challenge;
    }

    public function getChallenge() 
    {
        return $this->challenge;
    }
}
