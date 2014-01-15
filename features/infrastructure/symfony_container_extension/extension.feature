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
    And the container service "akamon.oauth2_server.scopes_obtainer" should be gettable
    And the container service "akamon.oauth2_server.token_creator" should be gettable

  Scenario: Scopes
    Given I have a symfony container
    When I register the container extension "Akamon\OAuth2\Server\Infrastructure\SymfonyContainerExtension\AkamonOAuth2ServerExtension"
    And I load the container yaml config:
      """
      akamon_oauth2_server:
        token_lifetime: 200
        scopes: [read, write, delete, all]
        repositories:
          client: oauth.client_repo
          access_token: oauth.access_token_repo

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


  Scenario: Refresh token repository
    Given I have a symfony container
    When I register the container extension "Akamon\OAuth2\Server\Infrastructure\SymfonyContainerExtension\AkamonOAuth2ServerExtension"
    And I load the container yaml config:
      """
      akamon_oauth2_server:
        token_lifetime: 200
        repositories:
          client: oauth.client_repo
          access_token: oauth.access_token_repo
          refresh_token: oauth.refresh_token_repo

      services:
        oauth.client_repo:
          class: Akamon\OAuth2\Server\Infrastructure\Filesystem\FileClientRepository
          arguments: [./oauth_clients]
        oauth.access_token_repo:
          class: Akamon\OAuth2\Server\Infrastructure\DoctrineCache\DoctrineCacheAccessTokenRepository
          arguments: [@oauth.access_token_repo.cache]
        oauth.refresh_token_repo:
          class: Akamon\OAuth2\Server\Infrastructure\DoctrineCache\DoctrineCacheRefreshTokenRepository
          arguments: [@oauth.refresh_token_repo.cache]

        oauth.access_token_repo.cache:
          class: Doctrine\Common\Cache\FilesystemCache
          arguments: [./oauth_access_tokens]
        oauth.refresh_token_repo.cache:
          class: Doctrine\Common\Cache\FilesystemCache
          arguments: [./oauth_refresh_tokens]
      """
    Then the container should be compilable
    And the container service "akamon.oauth2_server.server" should be gettable

  Scenario: Password grant type custom
    Given I have a symfony container
    When I register the container extension "Akamon\OAuth2\Server\Infrastructure\SymfonyContainerExtension\AkamonOAuth2ServerExtension"
    And I load the container yaml config:
      """
      akamon_oauth2_server:
        token_lifetime: 200
        repositories:
          client: oauth.client_repo
          access_token: oauth.access_token_repo
          refresh_token: oauth.refresh_token_repo

        token_grant_type_processors:
          password:
            id: oauth.password_token_grant_type_processor

      services:
        oauth.client_repo:
          class: Akamon\OAuth2\Server\Infrastructure\Filesystem\FileClientRepository
          arguments: [./oauth_clients]
        oauth.access_token_repo:
          class: Akamon\OAuth2\Server\Infrastructure\DoctrineCache\DoctrineCacheAccessTokenRepository
          arguments: [@oauth.access_token_repo.cache]
        oauth.refresh_token_repo:
          class: Akamon\OAuth2\Server\Infrastructure\DoctrineCache\DoctrineCacheRefreshTokenRepository
          arguments: [@oauth.refresh_token_repo.cache]

        oauth.access_token_repo.cache:
          class: Doctrine\Common\Cache\FilesystemCache
          arguments: [./oauth_access_tokens]
        oauth.refresh_token_repo.cache:
          class: Doctrine\Common\Cache\FilesystemCache
          arguments: [./oauth_refresh_tokens]

        oauth.password_token_grant_type_processor:
          class: Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\PasswordTokenGrantTypeProcessor
          arguments:
            - @oauth.user_credentials_checker
            - @oauth.user_id_obtainer
            - @akamon.oauth2_server.scopes_obtainer
            - @akamon.oauth2_server.token_creator

        oauth.user_credentials_checker:
          class: Akamon\OAuth2\Server\Domain\Service\User\UserCredentialsChecker\IterableUserCredentialsChecker
          arguments: [[]]
        oauth.user_id_obtainer:
          class: Akamon\OAuth2\Server\Domain\Service\User\UserIdObtainer\IterableUserIdObtainer
          arguments: [[]]
      """
    Then the container should be compilable
    And the container service "akamon.oauth2_server.server" should be gettable

  Scenario: Token Grant Type
    Given I have a symfony container
    When I register the container extension "Akamon\OAuth2\Server\Infrastructure\SymfonyContainerExtension\AkamonOAuth2ServerExtension"
    And I load the container yaml config:
      """
      akamon_oauth2_server:
        token_lifetime: 200
        repositories:
          client: oauth.client_repo
          access_token: oauth.access_token_repo
          refresh_token: oauth.refresh_token_repo

        token_grant_type_processors:
          direct:
            id: oauth.direct_token_grant_type_processor

      services:
        oauth.client_repo:
          class: Akamon\OAuth2\Server\Infrastructure\Filesystem\FileClientRepository
          arguments: [./oauth_clients]
        oauth.access_token_repo:
          class: Akamon\OAuth2\Server\Infrastructure\DoctrineCache\DoctrineCacheAccessTokenRepository
          arguments: [@oauth.access_token_repo.cache]
        oauth.refresh_token_repo:
          class: Akamon\OAuth2\Server\Infrastructure\DoctrineCache\DoctrineCacheRefreshTokenRepository
          arguments: [@oauth.refresh_token_repo.cache]

        oauth.access_token_repo.cache:
          class: Doctrine\Common\Cache\FilesystemCache
          arguments: [./oauth_access_tokens]
        oauth.refresh_token_repo.cache:
          class: Doctrine\Common\Cache\FilesystemCache
          arguments: [./oauth_refresh_tokens]

        oauth.direct_token_grant_type_processor:
          class: Akamon\OAuth2\Server\Domain\Service\Token\TokenGrantTypeProcessor\DirectTokenGrantTypeProcessor
          arguments:
            - @akamon.oauth2_server.scopes_obtainer
            - @akamon.oauth2_server.token_creator
      """
    Then the container should be compilable
    And the container service "akamon.oauth2_server.server" should be gettable
