Feature: OAuth Token Grant Password

  Background:
    Given there are clients:
      | name     | secret | allowedGrantTypes |
      | pablodip | abc    | ["password"]      |
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

  Scenario: Token Granted
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
    And the response parameter "scope" should be ""
