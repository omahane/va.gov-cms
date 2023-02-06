@content_editing_vamc_facility
Feature: CMS Users may effectively interact with the VAMC Facility form
  In order to confirm that cms users have access to the necessary functionality
  As anyone involved in the project
  I need to have certain functionality available

  Scenario: Log in and edit a VAMC Facility as an editor
    When I am logged in as a user with the roles "vamc_content_creator, content_publisher"
    And my workbench access sections are set to "372"
    When I am at "/alaska-health-care/locations/anchorage-va-medical-center"
    And I click the edit tab
    And I click the "Break lock" link
    And I click the "Confirm break lock" button
    And I wait "2" seconds
    And I select the radio button with the value "1037"
    And I wait "2" seconds
    And I scroll to element 'div.cke_inner'
    Then I should see "low" in ckeditor "field-supplemental-status-more-i-0"
    And I wait "2" seconds
    And I select the radio button with the value "1036"
    And I wait "2" seconds
    And I scroll to element 'div.cke_inner'
    Then I should see "medium" in ckeditor "field-supplemental-status-more-i-0"
    And I wait "2" seconds
    And I select the radio button with the value "1035"
    And I wait "2" seconds
    And I scroll to element 'div.cke_inner'
    Then I should see "high" in ckeditor "field-supplemental-status-more-i-0"

