Feature: Refresh Token

  Background:
    Given there are oauth2 clients:
      | name     | secret | allowedGrantTypes |
      | pablodip | abc    | ["refresh_token"] |

  Scenario: Without Refresh Token
    When I add the http basic authentication for the oauth2 client "pablodip" and "abc"
    And I add the request parameter "grant_type" with "refresh_token"
    And I make a token request
    Then the response status code should be "400"
    And the response should have the oauth2 right format and cache headers
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "Refresh token is required."

  Scenario: Invalid refresh token
    When I add the http basic authentication for the oauth2 client "pablodip" and "abc"
    And I add the request parameter "grant_type" with "refresh_token"
    And I add the request parameter "refresh_token" with "foo"
    And I make a token request
    Then the response status code should be "400"
    And the response should have the oauth2 right format and cache headers
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "Refresh token is invalid."

  Scenario: Expired refresh token
    Given there is an expired oauth2 access token "foo"
    And there is an expired oauth2 refresh token "bar" for the access token "foo"
    When I add the http basic authentication for the oauth2 client "pablodip" and "abc"
    And I add the request parameter "grant_type" with "refresh_token"
    And I add the request parameter "refresh_token" with "bar"
    And I make a token request
    Then the response status code should be "400"
    And the response should have the oauth2 right format and cache headers
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "Refresh token is expired."

  Scenario: Refreshing OK
    Given there is a valid oauth2 access token "foo"
    And there is a valid oauth2 refresh token "bar" for the access token "foo"
    When I add the http basic authentication for the oauth2 client "pablodip" and "abc"
    And I add the request parameter "grant_type" with "refresh_token"
    And I add the request parameter "refresh_token" with "bar"
    And I make a token request
    Then the response status code should be "200"
    And the response should have the oauth2 right format and cache headers
    And the response parameter "access_token" should exist
    And the response parameter "token_type" should be "bearer"
    And the response parameter "refresh_token" should exist
    And the response parameter "expires_in" should be "3600"
