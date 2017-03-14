(function() {
  "use strict";

  angular
    .module("screen")
    .controller("WarningModalController", ["$rootScope", "$uibModalInstance",
      "title", "text", WarningModalController]);

    function WarningModalController($rootScope, $uibModalInstance, title, text) {
      var vm = this;

      vm.title = title;
      vm.text = text;

      vm.dismiss = dismiss;

      activate();

      function activate() {
        $rootScope.$on("dismissModals", function() {
          $uibModalInstance.dismiss("rootScope broadcast");
        });
      }

      function dismiss() {

        $uibModalInstance.dismiss("cancel button");
      }
    }
})();
