(function () {

  'use strict';

  /*
   * adds ffm-validate-money makes sure the value is within bounds.
   */

  angular.module('ffm').
          directive('ffmValidateMoney', function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl) {
      function myValidation(value) {
        
        if(value && value < .3){
          mCtrl.$setValidity('boundsLow', false);
        }else{
          mCtrl.$setValidity('boundsLow', true);
        }
        
        if(value > 9999){
          mCtrl.$setValidity('boundsHigh', false);
        }else{
          mCtrl.$setValidity('boundsHigh', true);
        }
        
        return value;
      }
      mCtrl.$parsers.push(myValidation);
    }
  };
});
})();


