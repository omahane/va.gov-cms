/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function (Drupal) {
  var myFacility = "";

  var adminField = document.getElementById("edit-field-administration");
  var facilityFieldOptions = document.querySelectorAll("#edit-field-facility-location option");
  var systemFieldOptions = document.querySelectorAll("#edit-field-regional-health-service option");
  var facilityField = document.getElementById("edit-field-facility-location");
  var systemField = document.getElementById("edit-field-regional-health-service");
  var lovellPattern = /Lovell/i;
  var lovellTricarePattern = /TRICARE/i;
  var lovellVaPattern = /VA/i;
  var winnower = function winnower() {
    var pathType = drupalSettings.path.currentPath.split("/")[1];

    if (typeof facilityField !== "undefined" && facilityField !== null && pathType === "add") {
      facilityField.selectedIndex = "_none";
    }
    if (typeof systemField !== "undefined" && systemField !== null && pathType === "add") {
      systemField.selectedIndex = "_none";
    }

    var adminFieldText = adminField.options[adminField.selectedIndex].text;

    var adminMatcher = adminFieldText.replace(/(^-+)/g, "");

    facilityFieldOptions.forEach(function (i) {
      i.classList.add("hidden-option");
      if (i.text.includes(adminMatcher)) {
        i.classList.remove("hidden-option");
      } else if (i.text.search(lovellPattern) > -1 && adminFieldText.search(lovellPattern) > -1) {
        if (i.text.search(lovellTricarePattern) > -1 && adminFieldText.search(lovellTricarePattern) > -1) {
          i.classList.remove("hidden-option");
        } else if (i.text.search(lovellVaPattern) > -1 && adminFieldText.search(lovellVaPattern) > -1) {
          i.classList.remove("hidden-option");
        }
      }
    });

    systemFieldOptions.forEach(function (i) {
      i.classList.add("hidden-option");
      if (i.text.includes(adminMatcher)) {
        i.classList.remove("hidden-option");
      } else if (i.text.search(lovellPattern) > -1 && adminFieldText.search(lovellPattern) > -1) {
        if (i.text.search(lovellTricarePattern) > -1 && adminFieldText.search(lovellTricarePattern) > -1) {
          i.classList.remove("hidden-option");
        } else if (i.text.search(lovellVaPattern) > -1 && adminFieldText.search(lovellVaPattern) > -1) {
          i.classList.remove("hidden-option");
        }
      }
    });
  };

  Drupal.behaviors.vaGovLimitServiceOptions = {
    attach: function attach() {
      if (myFacility === "" || window.onload) {
        winnower();
      }
      adminField.addEventListener("change", winnower);
      if (facilityField !== null) {
        facilityField.addEventListener("change", function setText() {
          myFacility = facilityField.options[facilityField.selectedIndex].text;
        });
      }
    }
  };
})(Drupal);