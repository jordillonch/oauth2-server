<?php

namespace Akamon\OAuth2\Server\Tests\Service\ClientCredentialsObtainer;

use Akamon\OAuth2\Server\Service\ClientCredentialsObtainer\HttpBasicClientCredentialsObtainer;
use Symfony\Component\HttpFoundation\Request;

class HttpBasicClientCredentialsObtainerTest extends \PHPUnit_Framework_TestCase
{
    /** @var HttpBasicClientCredentialsObtainer */
    private $obtainer;

    protected function setUp()
    {
        $this->obtainer = new HttpBasicClientCredentialsObtainer();
    }

    public function testGetClientCredentialsShouldReturnAClientCredentialsInstanceFromRequestAuthorizationHeader()
    {
        $clientId = '123';
        $clientSecret = '123';

        $request = $this->createRequest($clientId, $clientSecret);

        $clientCredentials = $this->obtainer->getClientCredentials($request);

        $this->assertInstanceOf('Akamon\OAuth2\Server\Model\Client\ClientCredentials', $clientCredentials);
        $this->assertSame($clientId, $clientCredentials->getId());
        $this->assertSame($clientSecret, $clientCredentials->getSecret());
    }

    private function createRequest($clientId, $clientSecret)
    {
        $query = [];
        $request = [];
        $attributes = [];
        $cookies = [];
        $files = [];
        $server = ['HTTP_AUTHORIZATION' => 'Basic '.base64_encode($clientId . ':' . $clientSecret)];

        return new Request($query, $request, $attributes, $cookies, $files, $server);
    }

    /**
     * @expectedException \Akamon\OAuth2\Server\Exception\OAuthError\ClientCredentialsNotFoundOAuthErrorException
     */
    public function testGetClientCredentialsShouldThrowAnExceptionWhenThereAreNoCredentials()
    {
        $this->obtainer->getClientCredentials(new Request());
    }
}
