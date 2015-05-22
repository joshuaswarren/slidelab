Feature: Basic Uploads
  In order to process and view my slides
  As a user
  I need to be able to upload slides and view them

  Scenario: Slide Uploader Exists
    When I go to "/slide/new"
    Then I should see "Upload a Slide"



