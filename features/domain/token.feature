Feature: OAuth Token

  Background:
    Given there are oauth2 clients:
      | id       | secret | allowedGrantTypes |
      | pablodip | abc    | ["direct"]        |

  Scenario: Empty request
    When I make a token request
    Then the response status code should be "400"
    And the response should have the oauth2 right format and cache headers
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "Client credentials are required."

  Scenario: Without client credentials
    When I add the request parameters:
      | grant_type | direct |
      | user_id    | foo    |
    And I make a token request
    Then the response status code should be "400"
    And the response should have the oauth2 right format and cache headers
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "Client credentials are required."

  Scenario: Without grant type
    Given I add the http basic authentication for the oauth2 client "pablodip" and "abc"
    And I make a token request
    Then the response status code should be "400"
    And the response should have the oauth2 right format and cache headers
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "The grant type is required."

  Scenario: Unauthorized client
    Given I add the http basic authentication for the oauth2 client "pablodip" and "abc"
    When I add the request parameter "grant_type" with "implicit"
    When I make a token request
    Then the response status code should be "400"
    And the response should have the oauth2 right format and cache headers
    And the response parameter "error" should be "unauthorized_client"
    And the response parameter "message" should be "The client is unauthorized for the grant type."

  Scenario: Invalid client credentials (invalid id)
    Given I add the http basic authentication with "no" and "abc"
    When I add the request parameters:
      | grant_type | direct |
      | user_id    | foo    |
    And I make a token request
    Then the response status code should be "401"
    And the response should have the oauth2 right format and cache headers
    And the response parameter "error" should be "invalid_client"
    And the response parameter "message" should be "Client authentication failed."
    And the response header "www-authenticate" should be "Basic realm="OAuth2""

  Scenario: Invalid client credentials (invalid secret)
    Given I add the http basic authentication for the oauth2 client "pablodip" and "567"
    When I add the request parameters:
      | grant_type | direct |
      | user_id    | foo    |
    And I make a token request
    Then the response status code should be "401"
    And the response should have the oauth2 right format and cache headers
    And the response parameter "error" should be "invalid_client"
    And the response parameter "message" should be "Client authentication failed."

  Scenario: Token Granted
    Given I add the http basic authentication for the oauth2 client "pablodip" and "abc"
    When I add the request parameters:
      | grant_type | direct |
      | user_id    | foo    |
    And I make a token request
    Then the response status code should be "200"
    And the response should have the oauth2 right format and cache headers
    And the response parameter "access_token" should exist
    And the response parameter "token_type" should be "bearer"
    And the response parameter "refresh_token" should exist
    And the response parameter "expires_in" should be "3600"
    And the response parameter "scope" should be ""
