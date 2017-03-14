(function() {
  "use strict";

  angular
    .module("screen")
    .controller("ConfirmationModalController", [
      "$rootScope",
      "$uibModalInstance",
      "title",
      "text",
      "dismissLabel",
      "confirmLabel",
      ConfirmationModalController
    ]);

    function ConfirmationModalController($rootScope, $uibModalInstance, title,
      text, dismissLabel, confirmLabel) {

      var vm = this;

      vm.title = title;
      vm.text = text;
      vm.dismissLabel = dismissLabel;
      vm.confirmLabel = confirmLabel;

      vm.dismiss = dismiss;
      vm.confirm = confirm;

      activate();

      function activate() {
        $rootScope.$on("dismissModals", function() {
          $uibModalInstance.dismiss("rootScope broadcast");
        });
      }

      function confirm() {
        $uibModalInstance.close(true);
      };

      function dismiss() {

        $uibModalInstance.dismiss("cancel button");
      };
    }
})();
