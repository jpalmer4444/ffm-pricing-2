(function () {
  
  'use strict';
  
angular.module('ffm')
  .directive('ffmPwCheck', [function () {
    return {
      require: 'ngModel',
      link: function (scope, elem, attrs, ctrl) {
        var firstPassword = '#' + attrs.ffmPwCheck;
        elem.add(firstPassword).on('keyup', function () {
          scope.$apply(function () {
            var v = elem.val()===$(firstPassword).val();
            ctrl.$setValidity('ffmpwmatch', v);
          });
        });
      }
    }
  }]);
})();


