(function() {
  "use strict";

  angular
    .module("header")
    .controller("HeaderController", ['config', 'localStorageService', HeaderController]);

  function HeaderController(config, localStorageService) {

    var vm = this;
    
    vm.key = {};
    
    vm.key.sales_attr_id = 'sales_attr_id';
    
    vm.linkToCustomers = function(){
      
    };

    activate();

    function activate() {

    }
  }
})();
