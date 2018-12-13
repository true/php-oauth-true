<?php

namespace True\OAuth2\Test\TestCase\Integration;

use Cake\Http\Client;
use CvoTechnologies\StreamEmulation\Emulation\HttpEmulation;
use CvoTechnologies\StreamEmulation\Test\EmulationAssertionTrait;
use GuzzleHttp\Psr7\Response;
use Lcobucci\JWT\Builder;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use True\OAuth2\Guzzle\Handler\Cake;
use True\OAuth2\Provider\TrueAccounts;

class AccessTokenTest extends TestCase
{
    use EmulationAssertionTrait;

    public function testClientCredentials()
    {
        $this->expectEmulations(
            ['http', 'https'],
            HttpEmulation::fromCallable(
                function (RequestInterface $request) {
                    $accessToken = (new Builder())
                        ->setAudience('some-id')
                        ->setId('49572beadea68e0be91ce79681cb8d710e5a2d4abf86f7aac7aa5a8c71c15ddc977b9ab4402fcca7', true)
                        ->setIssuedAt(time())
                        ->setNotBefore(time())
                        ->setExpiration(time() + 3600)
                        ->setSubject(null)
                        ->set('scopes', ['profile'])
                        ->set('actor_user_id', 'b90fd49b-52da-4bdc-ba16-10d69833253e')
                        ->getToken();

                    return new Response(200, [], json_encode([
                        'access_token' => (string) $accessToken,
                        'expires_in' => 3600,
                    ]));
                },
                function (RequestInterface $request) {
                    Assert::assertSame('accounts.true.nl', $request->getUri()->getHost());
                    Assert::assertSame('/api/oauth/access-token', $request->getUri()->getPath());

                    parse_str($request->getBody()->getContents(), $data);
                    Assert::assertSame('some-id', $data['client_id']);
                    Assert::assertSame('some-secret', $data['client_secret']);
                    Assert::assertSame('client_credentials', $data['grant_type']);
                    Assert::assertSame('profile', $data['scope']);
                }
            )
        );

        $trueAccounts = new TrueAccounts([
            'clientId' => 'some-id',
            'clientSecret' => 'some-secret',
            'handler' => new Cake([
                'ssl_cafile' => dirname(__DIR__, 3) . '/vendor/cakephp/cakephp/config/cacert.pem',
            ], new Client([
                'adapter' => Client\Adapter\Stream::class,
            ])),
        ]);
        $accessToken = $trueAccounts->getAccessToken('client_credentials', [
            'scope' => 'profile',
        ]);

        $this->assertEmulations();
        $this->assertInstanceOf(AccessToken::class, $accessToken);
        $this->assertSame('b90fd49b-52da-4bdc-ba16-10d69833253e', $accessToken->getResourceOwnerId());
    }
}
