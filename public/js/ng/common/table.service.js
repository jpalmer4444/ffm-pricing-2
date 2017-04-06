(function () {
  "use strict";

  angular
          .module("common")
          .factory("tableService", [tableFactory]);

  function tableFactory() {

    return {
      getTableUrl: getTableUrl,
      tableTitle: tableTitle
    };

    /**
     * builds a backend (php) URL for datatables ajax call.
     * @param {String} name table name
     * @param {Object} config app configuration object
     * @param {Object|array} options hashmap | array of extra or needed paramters
     * @returns {undefined|String}
     */
    function getTableUrl(name, config, options) {
      if(!name){
        return;
      }
      switch(name){
        case 'Products' : {
            return config.urls.productsTableAjax + '?zff_sales_attr_id=' + options['sales_attr_id'] + '&zff_customer_id=' + options['customer_id'];
        }
        case 'Salespeople' : {
            return config.urls.salespeopleTableAjax + (options.length ? '?' + options.join('&') : '');
        }
        case 'Users' : {
            return config.urls.usersTableAjax + (options.length ? '?' + options.join('&') : '');
        }
        case 'Customers' : {
            return config.urls.customersTableAjax + (options.length ? '?' + options.join('&') : '');
        }
        default : {
            throw new Error('No Table Found!');
        }
      }
    }
    
    function tableTitle(total, filtered){
      if ((total === filtered)) {
        return filtered + ' Total Records';
      } else {
        return filtered + ' Filtered Records';
      }
    }


  }
})();
