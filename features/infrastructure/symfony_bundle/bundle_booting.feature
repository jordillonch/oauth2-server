Feature: AkamonOAuth2ServerBundle booting
  In order to use the AkamonOAuth2ServerBundle
  As a developer
  I want to boot the bundle

  Scenario: Using alone with minimum config
    Given I have a symfony kernel
    And I use the bundle "Akamon\OAuth2\Server\Infrastructure\SymfonyBundle\AkamonOAuth2ServerBundle"
    And I have the kernel yaml config:
      """
      akamon_oauth2_server:
        token_lifetime: 100

        repositories:
          client: foo
          access_token: bar

      services:
        foo:
          class: stdClass
        bar:
          class: stdClass
      """
    When I boot the symfony kernel
    Then the symfony kernel should be booted

  Scenario: Using with the SymfonyFrameworkBundle with minimum config
    Given I have a symfony kernel
    And I use the bundle "Symfony\Bundle\FrameworkBundle\FrameworkBundle"
    And I use the bundle "Akamon\OAuth2\Server\Infrastructure\SymfonyBundle\AkamonOAuth2ServerBundle"
    And I have the kernel yaml config:
      """
      framework:
        secret: foo

      akamon_oauth2_server:
        token_lifetime: 100

        repositories:
          client: foo
          access_token: bar

      services:
        foo:
          class: stdClass
        bar:
          class: stdClass
      """
    When I boot the symfony kernel
    Then the symfony kernel should be booted
