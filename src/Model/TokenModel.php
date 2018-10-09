<?php
namespace Fortnite\Model;

// Note (Tustin): Maybe this should be abstract and have more specific classes for both access and refresh tokens?
class TokenModel
{
    private $token;
    private $expiresIn;
    private $expiresAt;

    public function __construct(string $token, int $expiresIn, string $expiresAt)
    {
        $this->token = $token;
        $this->expiresIn = $expiresIn;
        $this->expiresAt = $expiresAt;
    }

    /**
     * The current token.
     *
     * @return string Token.
     */
    public function token() : string
    {
        return $this->token;
    }

    /**
     * Gets the seconds until the token expires.
     *
     * @return integer Seconds.
     */
    public function expiresIn() : int
    {
        return $this->expiresIn;
    }

    /**
     * Gets the date & time that the token expires at.
     *
     * @return \DateTime Token expiration date & time.
     */
    public function expiresAt() : \DateTime
    {
        return new \DateTime($this->expiresAt);
    }
}