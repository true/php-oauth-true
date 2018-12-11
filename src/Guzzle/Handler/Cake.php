<?php

namespace True\OAuth2\Guzzle\Handler;

use Cake\Http\Client;
use Cake\Http\Client\Request;
use GuzzleHttp\Promise\FulfilledPromise;
use Psr\Http\Message\RequestInterface;

class Cake
{
    public function __invoke(RequestInterface $request, array $options)
    {
        $cakeRequest = new Request(
            (string) $request->getUri(),
            $request->getMethod(),
            $request->getHeaders(),
            $request->getBody()->getContents()
        );

        $cakeClient = new Client();
        $response = $cakeClient->send($cakeRequest);

        return new FulfilledPromise($response);
    }
}
