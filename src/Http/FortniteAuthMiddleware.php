<?php

namespace Fortnite\Http;

use GuzzleHttp\Psr7\Request;

final class FortniteAuthMiddleware {

    const FORTNITE_AUTHORIZATION    = 'MzQ0NmNkNzI2OTRjNGE0NDg1ZDgxYjc3YWRiYjIxNDE6OTIwOWQ0YTVlMjVhNDU3ZmI5YjA3NDg5ZDMxM2I0MWE=';

    private $deviceId;

    public function __construct(string $deviceId)
    {
        $this->deviceId = $deviceId;
    }
    
    public function __invoke(Request $request, array $options = [])
    {
        return $request->withHeader(
            'Authorization', sprintf('Basic %s', self::FORTNITE_AUTHORIZATION)
        )
        ->withHeader(
            'User-Agent', 'Fortnite/++Fortnite+Release-6.01-CL-4413911 IOS/11.3.1'
        )
        ->withHeader(
            'X-Epic-Device-ID', $this->deviceId
        );
    }
}