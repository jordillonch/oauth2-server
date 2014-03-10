Feature: Scope

  Background:
    Given there are scopes:
      | all    |
      | read   |
      | write  |
      | delete |
    And there are oauth2 clients:
      | id       | secret | allowedGrantTypes | allowedScopes     | defaultScope |
      | pablodip | abc    | ["direct"]        | ["read", "write"] | read         |

  Scenario Outline: Invalid scope
    When I try to grant a token with the client "pablodip" and the user id "foo" and the scope "<scope>"
    Then the response status code should be "400"
    And the response should have the oauth2 right format and cache headers
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "Invalid scope."

  Examples:
    | scope    |
    | ups      |
    | read ups |

  Scenario Outline: Not allowed scope
    When I try to grant a token with the client "pablodip" and the user id "foo" and the scope "<scope>"
    Then the response status code should be "400"
    And the response should have the oauth2 right format and cache headers
    And the response parameter "error" should be "invalid_request"
    And the response parameter "message" should be "Scope not allowed."

  Examples:
    | scope       |
    | delete      |
    | read delete |
    | all         |

  Scenario: Access Token Granted with client default scope
    When I try to grant a token with the client "pablodip" and the user id "foo" and no scope
    Then the response status code should be "200"
    And the response should have the oauth2 right format and cache headers
    And the response parameter "access_token" should exist
    And the response parameter "token_type" should be "bearer"
    And the response parameter "refresh_token" should exist
    And the response parameter "expires_in" should be "3600"
    And the response parameter "scope" should be "read"

  Scenario Outline: Access Token Granted with custom scope
    When I try to grant a token with the client "pablodip" and the user id "foo" and the scope "<scope>"
    Then the response status code should be "200"
    And the response should have the oauth2 right format and cache headers
    And the response parameter "access_token" should exist
    And the response parameter "token_type" should be "bearer"
    And the response parameter "refresh_token" should exist
    And the response parameter "expires_in" should be "3600"
    And the response parameter "scope" should be "<scope>"

  Examples:
    | scope      |
    | write      |
    | read write |
