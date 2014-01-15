Feature: AkamonOAuth2ServerExtension
  In order to use the AkamonOAuth2ServerExtension
  As a developer
  I want to use the extension

  Scenario: Minimum config
    Given I have a symfony container
    When I register the container extension "Akamon\OAuth2\Server\Infrastructure\SymfonyContainerExtension\AkamonOAuth2ServerExtension"
    And I load the container yaml config:
      """
      akamon_oauth2_server:
        token_lifetime: 200
        repositories:
          client: oauth.client_repo
          access_token: oauth.access_token_repo

      services:
        oauth.client_repo:
          class: stdClass
        oauth.access_token_repo:
          class: stdClass

      services:
        oauth.client_repo:
          class: Akamon\OAuth2\Server\Infrastructure\Filesystem\FileClientRepository
          arguments: [./oauth_clients]
        oauth.access_token_repo:
          class: Akamon\OAuth2\Server\Infrastructure\DoctrineCache\DoctrineCacheAccessTokenRepository
          arguments: [@oauth.access_token_repo.cache]

        oauth.access_token_repo.cache:
          class: Doctrine\Common\Cache\FilesystemCache
          arguments: [./oauth_access_tokens]
      """
    Then the container should be compilable
    And the container service "akamon.oauth2_server.server" should be gettable
