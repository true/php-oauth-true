<?php

namespace True\OAuth2\Provider;

use Lcobucci\JWT\Parser;
use League\OAuth2\Client\Grant\AbstractGrant;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class TrueAccounts extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected $accountsHostname = 'accounts.true.nl';

    public function getAccountsHostname()
    {
        return $this->accountsHostname;
    }

    /**
     * Returns the base URL for authorizing a client.
     *
     * Eg. https://oauth.service.com/authorize
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://' . $this->getAccountsHostname() . '/oauth/authorize';
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * Eg. https://oauth.service.com/token
     *
     * @param  array  $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://' . $this->getAccountsHostname() . '/api/oauth/access-token';
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param  AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://' . $this->getAccountsHostname() . '/api/profile';
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return ['profile'];
    }

    protected function getDefaultHeaders()
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    protected function getScopeSeparator()
    {
        return ' ';
    }

    protected function getAllowedClientOptions(array $options)
    {
        return array_merge(parent::getAllowedClientOptions($options), ['handler']);
    }

    /**
     * Checks a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface         $response
     * @param  array|string              $data     Parsed response data
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() !== 200 && $data['success'] !== true) {
            throw new IdentityProviderException($data['error'] . ': ' . $data['message'], $data['code'], $response);
        }
    }

    protected function createAccessToken(array $response, AbstractGrant $grant)
    {
        $options = $response;
        $token = (new Parser())->parse($response['access_token']);

        $options['resource_owner_id'] = $token->getClaim('actor_user_id');

        return new AccessToken($options);
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param  array                  $response
     * @param  AccessToken            $token
     * @return ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new User($response['data']);
    }
}
