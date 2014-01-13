Feature: OAuth Token

  Background:
    Given there are clients:
      | name     | secret | allowed_grant_types |
      | pablodip | abc    | ["password"]        |

  Scenario: Empty request
    When I make a token request
    Then the response status code should be "400"
    And the oauth response format and cache are right
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "Client credentials are required."

  Scenario: Without client credentials
    When I add the request parameters:
      | grant_type | password |
    And I make a token request
    Then the response status code should be "400"
    And the oauth response format and cache are right
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "Client credentials are required."

  Scenario: Without grant type
    Given I add the http basic authentication for the client "pablodip" and "abc"
    And I make a token request
    Then the response status code should be "400"
    And the oauth response format and cache are right
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "The grant type is required."

  Scenario: Unauthorized client
    Given I add the http basic authentication for the client "pablodip" and "abc"
    When I add the request parameter "grant_type" with "implicit"
    When I make a token request
    Then the response status code should be "400"
    And the oauth response format and cache are right
    And the response parameter "error" should be "unauthorized_client"
    And the response parameter "message" should be "The client is unauthorized for the grant type."
