<?php

namespace Fortnite\Http;

use GuzzleHttp\Psr7\Request;

final class FortniteAuthMiddleware {

    const FORTNITE_AUTHORIZATION    = 'MzQ0NmNkNzI2OTRjNGE0NDg1ZDgxYjc3YWRiYjIxNDE6OTIwOWQ0YTVlMjVhNDU3ZmI5YjA3NDg5ZDMxM2I0MWE=';
    
    public function __invoke(Request $request, array $options = [])
    {
        return $request->withHeader(
            'Authorization', sprintf('Basic %s', self::FORTNITE_AUTHORIZATION)
        );
    }
}