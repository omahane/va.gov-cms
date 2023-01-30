/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function (Drupal) {
  var statusId = void 0;
  var textSetter = function textSetter() {
    var covidStatusValue = document.querySelectorAll(".form-item--field-supplemental-status input");

    covidStatusValue.forEach(function (element) {
      if (element.checked) {
        statusId = element.value;
      }
    });

    var iframeDocument = "";
    if (document.querySelector("#cke_edit-field-supplemental-status-more-i-0-value iframe")) {
      iframeDocument = document.querySelector("#cke_edit-field-supplemental-status-more-i-0-value iframe").contentDocument;
      iframeDocument.body.innerHTML = "<p>\n        " + drupalSettings.vamcCovidStatusTermText[statusId].name + "</p>\n        " + drupalSettings.vamcCovidStatusTermText[statusId].description;
    }
  };

  Drupal.behaviors.vaGovSetCovidTermText = {
    attach: function attach() {
      window.addEventListener("DOMContentLoaded", textSetter);
      var supplementalStatusChoices = document.querySelectorAll(".form-item--field-supplemental-status [id^='edit-field-supplemental-status-']");

      supplementalStatusChoices.forEach(function (choice) {
        document.getElementById(choice.id).addEventListener("click", textSetter);
      });
    }
  };
})(Drupal);