Feature: OAuth Token Grant Password

  Background:
    Given there are scopes:
      | all    |
      | read   |
      | write  |
      | delete |
    And there are clients:
      | name     | secret | allowedGrantTypes | allowedScopes     | defaultScope |
      | pablodip | abc    | ["password"]      | ["read", "write"] | read         |
    And there is a user "foo" with password "bar"

  Scenario: Invalid client credentials (invalid id)
    Given I add the http basic authentication header with "no" and "abc"
    When I add the request parameters:
        | grant_type | password |
    And I make a token request
    Then the response status code should be "401"
    And the oauth response format and cache are right
    And the response parameter "error" should be "invalid_client"
    And the response parameter "message" should be "Client authentication failed."
    And the response header "www-authenticate" should be "Basic realm="OAuth2""

  Scenario: Invalid client credentials (invalid secret)
    Given I add the http basic authentication for the client "pablodip" and "567"
    When I add the request parameters:
        | grant_type | password |
    And I make a token request
    Then the response status code should be "401"
    And the oauth response format and cache are right
    And the response parameter "error" should be "invalid_client"
    And the response parameter "message" should be "Client authentication failed."

  Scenario: Without user credentials
    Given I add the http basic authentication for the client "pablodip" and "abc"
    When I add the request parameters:
        | grant_type | password |
    And I make a token request
    Then the response status code should be "400"
    And the oauth response format and cache are right
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "The user credentials are required."

  Scenario Outline: Invalid user credentials
    Given I add the http basic authentication for the client "pablodip" and "abc"
    When I add the request parameters:
        | grant_type | password   |
        | username   | <username> |
        | password   | <password> |
    And I make a token request
    Then the response status code should be "400"
    And the oauth response format and cache are right
    And the response parameter "error" should be "invalid_grant"
    And the response parameter "message" should be "User authentication failed."

    Examples:
        | username | password |
        | foo      | no       |
        | no       | bar      |

  Scenario Outline: Inavlid scope
    Given I add the http basic authentication for the client "pablodip" and "abc"
    When I add the request parameters:
        | grant_type | password |
        | username   | foo      |
        | password   | bar      |
        | scope      | <scope>  |
    And I make a token request
    Then the response status code should be "400"
    And the oauth response format and cache are right
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "Invalid scope."

    Examples:
        | scope    |
        | ups      |
        | read,ups |

  Scenario Outline: Not allowed scope
    Given I add the http basic authentication for the client "pablodip" and "abc"
    When I add the request parameters:
        | grant_type | password |
        | username   | foo      |
        | password   | bar      |
        | scope      | <scope>  |
    And I make a token request
    Then the response status code should be "400"
    And the oauth response format and cache are right
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "Scope not allowed."

    Examples:
        | scope       |
        | delete      |
        | read,delete |
        | all         |

  Scenario: Access Token Granted with client default scope
    Given I add the http basic authentication for the client "pablodip" and "abc"
    When I add the request parameters:
        | grant_type | password   |
        | username   | foo        |
        | password   | bar        |
    And I make a token request
    Then the response status code should be "200"
    And the oauth response format and cache are right
    And the response parameter "access_token" should exist
    And the response parameter "token_type" should be "bearer"
    And the response parameter "refresh_token" should exist
    And the response parameter "expires_in" should be "3600"
    And the response parameter "scope" should be "read"

  Scenario Outline: Access Token Granted with custom scope
    Given I add the http basic authentication for the client "pablodip" and "abc"
    When I add the request parameters:
        | grant_type | password   |
        | username   | foo        |
        | password   | bar        |
        | scope      | <scope>    |
    And I make a token request
    Then the response status code should be "200"
    And the oauth response format and cache are right
    And the response parameter "access_token" should exist
    And the response parameter "token_type" should be "bearer"
    And the response parameter "refresh_token" should exist
    And the response parameter "expires_in" should be "3600"
    And the response parameter "scope" should be "<scope>"

    Examples:
      | scope      |
      | write      |
      | read,write |
