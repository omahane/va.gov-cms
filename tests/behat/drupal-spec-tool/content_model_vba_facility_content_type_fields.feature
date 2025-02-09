@api
Feature: Content model: VBA facility Content Type fields
  In order to enter structured content into my site
  As a content editor
  I want to have content type fields that reflect my content model.

  @dst @field_type @content_type_fields @dstfields
     Scenario: Fields
       Then exactly the following fields should exist for bundles "vba_facility" of entity type node
       | Type | Bundle | Field label | Machine name | Field type | Required | Cardinality | Form widget | Translatable |
| Content type | VBA Facility | Address | field_address | Address |  | 1 | Address | Translatable |
| Content type | VBA Facility | Section | field_administration | Entity reference | Required | 1 | Select list | Translatable |
| Content type | VBA Facility | Facility Locator API ID | field_facility_locator_api_id | Text (plain) |  | 1 | Textfield | Translatable |
| Content type | VBA Facility | Classification | field_facility_vba_classificatio | List (text) |  | 1 | Select list |  |
| Content type | VBA Facility | Geolocation | field_geolocation | Geofield |  | 1 | Latitude/Longitude | Translatable |
| Content type | VBA Facility | Hours | field_office_hours | Office hours |  | Unlimited | Office hours (week) | Translatable |
| Content type | VBA Facility | Operating status | field_operating_status_facility | List (text) | Required | 1 | Select list | Translatable |
| Content type | VBA Facility | Operating status - more info | field_operating_status_more_info | Text (plain, long) |  | 1 | Textarea (multiple rows) with counter | Translatable |
| Content type | VBA Facility | Phone Number | field_phone_number | Telephone number |  | 1 | Telephone number | Translatable |
