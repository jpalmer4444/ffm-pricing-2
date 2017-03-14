(function() {
  "use strict";

  angular
    .module("screen")
    .directive("selectOnFocus", selectOnFocus);

  function selectOnFocus() {
    return {
      restrict: 'A',
      link: function (scope, element, attrs) {
        element.on("focus", function () {
          this.select();
        });
      }
    };
  }
})();
