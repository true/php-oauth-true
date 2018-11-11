<?php

namespace True\OAuth2\Test\TestCase\Provider;

use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use True\OAuth2\Provider\TrueAccounts;

class TrueAccountsTest extends TestCase
{
    /**
     * @var TrueAccounts
     */
    protected $provider;

    protected function setUp()
    {
        parent::setUp();

        $this->provider = new TrueAccounts([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ]);
    }

    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);
        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
        $this->assertContains('profile', $query['scope']);
        $this->assertAttributeNotEmpty('state', $this->provider);
    }

    public function testBaseAccessTokenUrl()
    {
        $url = $this->provider->getBaseAccessTokenUrl([]);
        $uri = parse_url($url);
        $this->assertEquals('/api/oauth/access-token', $uri['path']);
    }

    public function testResourceOwnerDetailsUrl()
    {
        $token = $this->mockAccessToken();
        $url = $this->provider->getResourceOwnerDetailsUrl($token);
        $uri = parse_url($url);
        $this->assertEquals('/api/profile', $uri['path']);
        $this->assertNotContains('mock_access_token', $url);
    }

    /**
     * @return AccessToken
     */
    private function mockAccessToken()
    {
        return new AccessToken([
            'access_token' => 'mock_access_token',
        ]);
    }
}
