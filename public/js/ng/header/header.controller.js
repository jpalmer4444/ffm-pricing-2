(function() {
  "use strict";

  angular
    .module("header")
    .controller("HeaderController", ['$scope', HeaderController]);

  function HeaderController($scope) {

    var vm = this;

    activate();

    function activate() {

    }
  }
})();
