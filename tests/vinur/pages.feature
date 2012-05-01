Feature: Test Pages
  Test the test pages created by the Theme Unit Test
  
  Scenario: Page with comments
    Given I am on the homepage
    When I click on the menu item "Page with comments"
    Then I should see the page content 
    And I should see the comments posted 
    