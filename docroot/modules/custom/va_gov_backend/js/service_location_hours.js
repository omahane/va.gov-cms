/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function (Drupal) {
  var displayHours = function displayHours(value, toggle, table) {
    if (toggle.checked) {
      if (toggle.value === value) {
        table.style.display = "block";
      } else {
        table.style.display = "none";
      }
    }
  };

  Drupal.behaviors.vaGovServiceLocationHours = {
    attach: function attach() {
      var hourSelects = document.querySelectorAll(".field--name-field-hours input");
      hourSelects.forEach(function (hourSelect) {
        var hours = hourSelect.parentElement.parentElement.parentElement.parentElement.parentElement.nextElementSibling;
        var facilityHours = hourSelect.parentElement.parentElement.parentElement.parentElement.nextElementSibling;

        window.addEventListener("load", function () {
          displayHours("2", hourSelect, hours);
          displayHours("0", hourSelect, facilityHours);
        });

        hourSelect.addEventListener("change", function () {
          displayHours("2", hourSelect, hours);
          displayHours("0", hourSelect, facilityHours);
        });
      });
    }
  };
})(Drupal);