Feature: AkamonOAuth2ServerBundle working
  In order to use the AkamonOAuth2ServerBundle
  As a developer
  I want to make it work

  Background:
    Given I have a symfony kernel
    And I use the bundle "Symfony\Bundle\FrameworkBundle\FrameworkBundle"
    And I use the bundle "Akamon\OAuth2\Server\Infrastructure\SymfonyBundle\AkamonOAuth2ServerBundle"
    And I have the file "routing.yml" in the kernel root dir with:
      """
      oauth_token:
        path: /oauth/token
        defaults: { _controller: akamon.oauth2_server.server:token }
      """
    And I have the kernel yaml config:
      """
      framework:
        secret: foo
        router:
          resource: "%kernel.root_dir%/routing.yml"

      akamon_oauth2_server:
        token_lifetime: 100

        repositories:
          client: oauth.client_repository
          access_token: oauth.access_token_repository

      services:
        oauth.client_repository:
          class: Akamon\OAuth2\Server\Infrastructure\Filesystem\FileClientRepository
          arguments: [%kernel.root_dir%/oauth_clients]
        oauth.access_token_repository:
          class: Akamon\OAuth2\Server\Infrastructure\DoctrineCache\DoctrineCacheAccessTokenRepository
          arguments: [@oauth.access_token_repository.cache]

        oauth.access_token_repository.cache:
          class: Doctrine\Common\Cache\FilesystemCache
          arguments: [%kernel.root_dir%/oauth_access_tokens]
      """
    And I boot the symfony kernel
    And I use the kernel
    And there are oauth clients:
      | name     | secret | allowedGrantTypes |
      | pablodip | abc    | ["direct"]        |

  Scenario: Requesting an access token with no data
    When I make a "POST" request to "/oauth/token"
    Then the response status code should be "400"
    And the response header "content-type" should be "application/json"
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "Client credentials are required."

  Scenario: Requesting an access token with no grant types
    When I add the http basic authentication for the oauth client "pablodip" and "abc"
    And I add the request parameters:
      | grant_type | direct |
    And I make a "POST" request to "/oauth/token"
    Then the response status code should be "400"
    And the response header "content-type" should be "application/json"
    And the response parameter "error" should be "unsupported_grant_type"
    And the response parameter "message" should be "The grant type is not supported."
