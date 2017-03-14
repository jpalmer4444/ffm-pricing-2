(function() {
  "use strict";

  angular
    .module("screen")
    .directive("opened", ["$timeout", opened]);

  function opened($timeout) {
    return {
      require: 'uiSelect',
      link: function($scope, element, attrs, $select) {

        $timeout(function() {
          $select.activate();
        });
      }
    }
  }
})();
