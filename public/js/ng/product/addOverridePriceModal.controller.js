(function () {
  'use strict';

  angular
          .module('product')
          .controller('AddOverridePriceModalController', [
            '$rootScope',
            '$uibModalInstance',
            'customer_id',
            'sales_attr_id',
            'product_id',
            'overrideprice',
            AddOverridePriceModalController
          ]);

  function AddOverridePriceModalController(
          $rootScope, 
          $uibModalInstance, 
          customer_id,
          sales_attr_id,
          product_id, 
          overrideprice
                  ) {

    var vm = this;
    
    vm.scenario = !overrideprice ? 'create' : 'edit';


    vm.product_id = product_id;
    
    vm.sales_attr_id = sales_attr_id;
    
    vm.customer_id = customer_id;
    
    vm.overrideprice = overrideprice;
    
    
    vm.label = {};
    
    //labels
    vm.label.overrideprice = 'Override Price';
    
    vm.message = {};
    //default messages
    vm.message.overrideprice = 'You must enter a valid dollar amount.';
    
    //validators
    vm.pattern = {};
    
    //Standard Phone
    vm.pattern.money = /^(?=.)(?!\$$)(([1-9][0-9]{0,2}(,[0-9]{3})*)|[0-9]+)?(\.[0-9]{2})?$/;

    vm.error = '';

    activate();

    function activate() {
      $rootScope.$on("dismissModals", function () {
        $uibModalInstance.dismiss("rootScope broadcast");
      });
    }

    vm.ok = function (valid) {

      if (valid) {
        
        var vars = JSON.stringify({
            "customer_id": vm.customer_id,
            "sales_attr_id": vm.sales_attr_id,
            "product_id": vm.product_id,
            "overrideprice": vm.overrideprice,
            "scenario": vm.scenario
          })

      //pass to calling script
        $uibModalInstance.close({
          "$value": vars
        });

      }
    };

    vm.cancel = function () {

      $uibModalInstance.dismiss("cancel button");
    };
  }
})();


