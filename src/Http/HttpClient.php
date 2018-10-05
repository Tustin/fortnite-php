<?php

namespace Fortnite\Http;

use Fortnite\Http\Exception\FortniteException;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;

use GuzzleHttp\Exception\ClientException;

class HttpClient {

    private $client;
    private $options;

    // Flags
    const FORM  = 1;
    const JSON  = 2;
    const MULTI = 4;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function get(string $path, array $body = [], array $headers = []) 
    {
        $response = $this->request('GET', $path, $body, self::FORM, $headers);

        return ResponseParser::parse($response);
    }

    public function post(string $path, $body, int $type = self::FORM, array $headers = []) 
    {
        $response = $this->request('POST', $path, $body, $type, $headers);

        return ResponseParser::parse($response);
    }

    public function delete(string $path, array $parameters = [], array $headers = [])
    {
        $response = $this->request('DELETE', $path, $parameters, self::FORM, $headers);

        return ResponseParser::parse($response);
    }

    public function patch(string $path, $body = null, bool $json = false, array $headers = [])
    {
        $response = $this->request('PATCH', $path, $body, ($json) ? self::JSON : self::FORM, $headers);

        return ResponseParser::parse($response);
    }

    public function put(string $path, $body = null, bool $json = false, array $headers = [])
    {
        $response = $this->request('PUT', $path, $body, ($json) ? self::JSON : self::FORM, $headers);

        return ResponseParser::parse($response);
    }

    private function request(string $method, string $path, $body = null, int $type = self::FORM, array $headers = []) 
    {
        $options = [];

        if (($method === 'GET' || $method === 'DELETE') && $body != null) {
            $path .= (strpos($path, '?') === false) ? '?' : '&';
            $path .= urldecode(http_build_query($body));
        } else {
             if ($type & self::FORM) {
                $options["form_params"] = $body;
            } else if ($type & self::JSON) {
                $options["json"] = $body;
            } else if ($type & self::MULTI) {
                $options['multipart'] = $body;
            }
        }

        try {
            return $this->client->request($method, $path, $options);
        } catch (ClientException $e) {
            $body = $e->getResponse()->getBody(true);
            throw new FortniteException($body);
        } catch (GuzzleException $e) {
            throw $e;
        }
    }
}