(function () {

  'use strict';

  /*
   * adds ffm-enter to element allowing for function execution
   * on keypress "Enter".
   */

  angular.module('ffm').
          directive('ffmEnter', ['$timeout', function ($timeout) {
              return function (scope, element, attrs) {
                element.bind("keydown keypress", function (event) {
                  if (event.which === 13) {
                    $timeout(function () {//$timeout forces async
                      scope.$eval(attrs.ffmEnter);
                    }, 0);
                    event.preventDefault();
                  }
                });
              };
            }]);
})();


