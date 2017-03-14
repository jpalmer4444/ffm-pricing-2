(function() {
  "use strict";

  angular
    .module("screen")
    .directive("datepickerLocaldate", [datepickerLocaldate]);

    function datepickerLocaldate() {
      var directive = {
          restrict: 'A',
          require: ['ngModel'],
          link: link
      };
      return directive;

      function link(scope, element, attr, ctrls) {
        var ngModelController = ctrls[0];

        // called with a JavaScript Date object when picked from the datepicker
        ngModelController.$parsers.push(function (viewValue) {

          if (!viewValue) {
            return undefined;
          }

          var value = ((viewValue.getMonth() <= 8 ? "0" + (viewValue.getMonth() + 1) : (viewValue.getMonth() + 1)) + "/" +
            (viewValue.getDate() <= 9 ? "0" + viewValue.getDate() : viewValue.getDate()) + "/" + viewValue.getFullYear());

          return value;
        });

        // called with a 'MM/dd/yyyy' string to format
        ngModelController.$formatters.push(function (modelValue) {

          if (!modelValue) {
            return new Date();
          }

          var dt = new Date();

          try {
            var dateParts = modelValue.split('/');

            dt.setMonth(dateParts[0] - 1);
            dt.setDate(dateParts[1]);
            dt.setFullYear(dateParts[2]);

          } catch (e) {}

          return dt;
        });
      }
    };
})();
