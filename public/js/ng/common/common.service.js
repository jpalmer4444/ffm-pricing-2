(function() {
  "use strict";

  angular
    .module("tables")
    .factory("utilService", [utilFactory]);

    function utilFactory() {

      return {
        aodata: aodata
      };

     /**
     * 
     * @returns {Array}
     */
    function aodata(dtColumns) {
      var draw = {"name": "draw", value: 1};
      var columns = {name: "columns", value: []};
      for (var i = 0; i < dtColumns.length; i++) {
        var dt = dtColumns[i];
        var column = {
          data: dt.mData,
          name: '',
          searchable: true,
          orderable: dt.bSortable,
          search: {
            value: '',
            regex: false
          }
        };
        columns['value'].push(column);
      }
      return [draw, columns];
    }
      
    }
})();
