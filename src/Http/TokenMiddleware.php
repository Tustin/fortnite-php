<?php

namespace Fortnite\Http;

use GuzzleHttp\Psr7\Request;

final class TokenMiddleware {

    private $accessToken;
    private $refreshToken;
    private $expireTime;
    private $deviceId;

    public function __construct(string $accessToken, string $refreshToken, string $expireTime, string $deviceId)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expireTime = $expireTime;
        $this->deviceId = $deviceId;
    }

    public function __invoke(Request $request, array $options = [])
    {
        return $request->withHeader(
            'Authorization', sprintf('Bearer %s', $this->accessToken)
        )
        ->withHeader(
            'User-Agent', 'Fortnite/++Fortnite+Release-6.01-CL-4413911 IOS/11.3.1'
        )
        ->withHeader(
            'X-Epic-Device-ID', $this->deviceId
        );
    }
}