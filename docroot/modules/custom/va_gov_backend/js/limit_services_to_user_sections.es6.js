/**
 * @file
 */

((Drupal) => {
  let myFacility = "";
  // Grab our fields and options.
  const adminField = document.getElementById("edit-field-administration");
  const facilityFieldOptions = document.querySelectorAll(
    "#edit-field-facility-location option"
  );
  const systemFieldOptions = document.querySelectorAll(
    "#edit-field-regional-health-service option"
  );
  const facilityField = document.getElementById("edit-field-facility-location");
  const systemField = document.getElementById(
    "edit-field-regional-health-service"
  );
  const lovellVaPattern = /Lovell.*VA/i;
  const lovellTricarePattern = /Lovell.*TRICARE/i;
  const winnower = () => {
    const pathType = drupalSettings.path.currentPath.split("/")[1];
    // Set our selects back to "Select a value." on add forms.
    if (
      typeof facilityField !== "undefined" &&
      facilityField !== null &&
      pathType === "add"
    ) {
      facilityField.selectedIndex = "_none";
    }
    if (
      typeof systemField !== "undefined" &&
      systemField !== null &&
      pathType === "add"
    ) {
      systemField.selectedIndex = "_none";
    }

    const adminFieldText = adminField.options[adminField.selectedIndex].text;
    // Get our search string from the field text.
    let adminMatcher;
    if (adminFieldText.search(lovellTricarePattern) > -1) {
      adminMatcher = "Lovell Federal TRICARE health care";
    } else if (adminFieldText.search(lovellVaPattern) > -1) {
      adminMatcher = "Lovell Federal VA health care";
    } else {
      adminMatcher = adminFieldText.replace(/(^-+)/g, "");
    }

    // Hide all options and only show options matching field text.
    function hideSeekShow(domElement) {
      domElement.forEach((i) => {
        i.classList.add("hidden-option");
        if (i.text.includes(adminMatcher)) {
          i.classList.remove("hidden-option");
        }
      });
    }

    hideSeekShow(facilityFieldOptions);
    hideSeekShow(systemFieldOptions);
  };

  Drupal.behaviors.vaGovLimitServiceOptions = {
    attach() {
      if (myFacility === "" || window.onload) {
        winnower();
      }
      adminField.addEventListener("change", winnower);
      if (facilityField !== null) {
        facilityField.addEventListener("change", function setText() {
          myFacility = facilityField.options[facilityField.selectedIndex].text;
        });
      }
    },
  };
})(Drupal);
