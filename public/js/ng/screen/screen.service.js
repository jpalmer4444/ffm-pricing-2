(function() {
  "use strict";

  angular
    .module("screen")
    .factory("screenService", ["$rootScope", "$uibModal", screenFactory]);

    function screenFactory($rootScope, $uibModal) {

      return {
        showWarning: showWarning,
        showOverlay: showOverlay,
        hideOverlay: hideOverlay
      };

      function showOverlay() {
        angular.element(document.getElementById("loadingOverlay")).css("display", "block");
      }

      function hideOverlay() {
        angular.element(document.getElementById("loadingOverlay")).css("display", "none");
      }

      function showWarning(title, text) {

        $rootScope.$broadcast("dismissModals", {launcher: "screenService::showWarning"});

        $uibModal.open({
          animation: true,
          ariaLabelledBy: 'modal-title',
          ariaDescribedBy: 'modal-body',
          templateUrl: 'warningModal.html',
          controller: 'WarningModalController',
          controllerAs: 'vmc',
          resolve: {
            title: function() {
              return title;
            },
            text: function() {
              return text;
            }
          }
        });
      }
    }
})();
