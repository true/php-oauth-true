<?php

namespace True\OAuth2\Guzzle\Handler;

use Cake\Http\Client;
use Cake\Http\Client\Request;
use GuzzleHttp\Promise\FulfilledPromise;
use Psr\Http\Message\RequestInterface;

class Cake
{
    /**
     * @var array
     */
    protected $requestOptions;
    /**
     * @var Client
     */
    protected $client;

    public function __construct(array $requestOptions = [], Client $client = null)
    {
        if (!$client) {
            $client = new Client();
        }

        $this->requestOptions = $requestOptions;
        $this->client = $client;
    }

    public function __invoke(RequestInterface $request, array $options)
    {
        $cakeRequest = new Request(
            (string) $request->getUri(),
            $request->getMethod(),
            $request->getHeaders(),
            $request->getBody()->getContents()
        );

        $response = $this->client->send($cakeRequest, $this->requestOptions);

        return new FulfilledPromise($response);
    }
}
