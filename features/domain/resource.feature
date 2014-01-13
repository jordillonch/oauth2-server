Feature: OAuth Resource

  Scenario: Without authorization
    When I make a resource request
    Then the response status code should be "400"
    And the oauth response format and cache are right
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "The access token is required."

  Scenario: Invalid access token
    Given I add the request header "authorization" with "Bearer blahblah"
    When I make a resource request
    Then the response status code should be "400"
    And the oauth response format and cache are right
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "Invalid access token."

  Scenario: Expired access token
    Given there is an expired access token "foo"
    Given I add the request header "authorization" with "Bearer foo"
    When I make a resource request
    Then the response status code should be "400"
    And the oauth response format and cache are right
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "Expired access token."

  Scenario: Accessing to the resource
    Given there is a valid access token "foo"
    Given I add the request header "authorization" with "Bearer foo"
    When I make a resource request
    Then the response status code should be "200"
    And the response header "content-type" should be "text/plain"
    And the response content should be:
        """
        My resource!
        """
