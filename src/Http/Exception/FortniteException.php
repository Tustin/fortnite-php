<?php

namespace Fortnite\Http\Exception;

class FortniteException extends \Exception {
    private $info;

    public function __construct(string $body) {
        $this->info = json_decode($body);
    }

    public function code() : string
    {
        return $this->info->errorCode;
    }

    public function message() : string
    {
        return $this->info->errorMessage;
    }

    public function numericCode() : int 
    {
        return $this->info->numericErrorCode;
    }

    public function challenge() : string
    {
        return $this->info->challenge;
    }

    public function metadata() : object
    {
        return $this->info->metadata;
    }

    public function __toString() : string
    {
        return $this->message();
    }

}