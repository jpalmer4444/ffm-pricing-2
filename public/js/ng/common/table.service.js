(function () {
  "use strict";

  angular
          .module("common")
          .factory("tableService", [tableFactory]);

  function tableFactory() {

    return {
      ajax: ajax
    };

    /*
     .withOption('ajax', {
     url: '/serverside/url/here',
     type: 'POST',
     data: function(data, dtInstance) {
     
     // Modify the data object properties here before being passed to the server
     
     }
     })
     */

    function ajax(options) {
      var options = {
        url: '/serverside/url/here',
        type: 'POST',
        data: function (data, dtInstance) {

          // Modify the data object properties here before being passed to the server

        }
      };
    }
    

  }
})();
