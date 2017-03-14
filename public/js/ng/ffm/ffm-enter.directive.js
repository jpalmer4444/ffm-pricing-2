(function () {
  
  'use strict';
  
  /*
   * adds ffm-enter to element allowing for function execution
   * on keypress "Enter".
   */
  
  angular.module('ffm').
          directive('ffmEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if(event.which === 13) {
                scope.$apply(function (){
                    scope.$eval(attrs.ffmEnter);
                });
                event.preventDefault();
            }
        });
    };
});
})();


