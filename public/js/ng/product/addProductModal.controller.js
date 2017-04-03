(function () {
  'use strict';

  angular
          .module('product')
          .controller('AddProductModalController', [
            '$rootScope',
            '$http',
            'config',
            '$uibModalInstance',
            'customer_id',
            'sales_attr_id',
            'product',
            'description',
            'comment',
            'overrideprice',
            'uom',
            'sku',
            AddProductModalController
          ]);

  function AddProductModalController(
          $rootScope,
          $http,
          config,
          $uibModalInstance,
          customer_id,
          sales_attr_id,
          product,
          description,
          comment,
          overrideprice,
          uom,
          sku
          ) {

    var vm = this;

    vm.sales_attr_id = sales_attr_id;

    vm.customer_id = customer_id;

    vm.comment = comment;


    vm.label = {};

    //labels
    vm.label.product = 'Product';
    vm.label.description = 'Description';
    vm.label.comment = 'Comment';
    vm.label.overrideprice = 'Override Price';
    vm.label.uom = 'UOM';
    vm.label.sku = 'SKU';
    
    vm.typeaheadProduct = {
      productname:'',
      description:'',
      retail:'',
      uom:'',
      sku:''
    };
    
    vm.product = {};

    vm.message = {};
    //default messages
    vm.message.product = 'Product is required';
    vm.message.overrideprice = 'You must enter a valid dollar amount.';
    vm.message.uom = 'UOM is required';
    vm.message.sku = 'You must enter a valid SKU (alpha-numeric).';

    //validators
    vm.pattern = {};

    //SKU
    vm.pattern.sku = /^[a-z0-9A-Z\-]{1,20}$/;

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

        //pass to calling script
        $uibModalInstance.close({
          "$value": {
            "customer_id": vm.customer_id,
            "sales_attr_id": vm.sales_attr_id,
            "product": vm.typeaheadProduct && vm.typeaheadProduct.productname ? 
            vm.typeaheadProduct.productname : 
                    angular.element('#products-typeahead').val(),
            "description": vm.product.description,
            "comment": vm.comment,
            "overrideprice": vm.product.retail,
            "uom": vm.product.uom,
            "sku": vm.product.sku
          }
        });

      }
    };

    vm.cancel = function () {

      $uibModalInstance.dismiss("cancel button");
    };

    vm.getTypeaheadProducts = function (searchTerm) {

      return $http({
        url: config.urls.productsFormTypeahead,
        method: "POST",
        data: $.param({'term': searchTerm})
      }).then(function(response){
        return response.data;
      });

    }
    
    vm.selectProduct = function(){
      
      vm.product.description = vm.typeaheadProduct.description;
      vm.product.retail = vm.typeaheadProduct.retail;
      vm.product.uom = vm.typeaheadProduct.uom;
      vm.product.sku = vm.typeaheadProduct.sku;
      
    }

  }
})();


