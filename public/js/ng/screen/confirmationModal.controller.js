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
      "confirmFnc",
      ConfirmationModalController
    ]);
    
    /**
     * 
     * @param {type} $rootScope
     * @param {type} $uibModalInstance
     * @param {type} title Title for the popup
     * @param {type} text Content for the popup
     * @param {type} dismissLabel Dismiss Label for dismiss button
     * @param {type} confirmLabel Confirm Label for confirm button
     * @param {type} confirmFnc Confirmation function to execute on confirmation | 
     *               can be null - then nothing happens on confirm
     * @returns {void} executes the passed in function and dismisses the modal 
     *               when confirm or dismiss is clicked. If the function is asynchronous, 
     *               the modal will not wait for successful completion before closing the dialog.
     */

    function ConfirmationModalController($rootScope, $uibModalInstance, title,
      text, dismissLabel, confirmLabel, confirmFnc) {

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
        if(confirmFnc)//execute confirm function
          confirmFnc();
        $uibModalInstance.close(true);
      };

      function dismiss() {
        $uibModalInstance.dismiss("cancel button");
      };
    }
})();
