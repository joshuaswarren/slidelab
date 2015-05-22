Feature: Up and Running
  In order to confirm Behat and Goutte are Working
  As a developer
  I need to see a homepage

  Scenario: Homepage Exists
    When I go to "/"
    Then I should see "Welcome to Slidelab"




