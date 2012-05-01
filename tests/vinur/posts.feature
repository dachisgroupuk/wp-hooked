Feature: Posts, their permissions and their display

  Scenario: Read more link from homepage
    Given I am on the homepage
    When I click on the readmore link of the article "Readability Test"
    Then I should be redirected to the article "Readability Test"
    
  Scenario: Read more link from homepage
    Given I am on the homepage and not logged in
    When I click on the readmore link of the article "Readability Test"
    Then I should be redirected to the article "Readability Test"
    And I should not see the edit link