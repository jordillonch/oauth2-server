Feature: AkamonOAuth2ServerExtension
  In order to use the AkamonOAuth2ServerExtension
  As a developer
  I want to use the extension

  Scenario: Minimum config
    Given I have a symfony container
    When I register the container extension "Akamon\OAuth2\Server\Infrastructure\SymfonyContainerExtension\AkamonOAuth2ServerExtension"
    Then the container should be compilable
