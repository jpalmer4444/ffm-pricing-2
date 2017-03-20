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
    
    function setSalesAttrId(sales_attr_id){
      localStorageService.set(vm.key.sales_attr_id, sales_attr_id);
    }
    
    function getSalesAttrId(){
      localStorageService.get(vm.key.sales_attr_id);
    }

    activate();

    function activate() {

    }
  }
})();
