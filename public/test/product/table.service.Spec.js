/* 
 * Unit Tests for productTable.controller.js
 */

describe('common api', function () {

  var config = {
    urls: {
      'productsTableAjax': '/products-table-ajax',
      'customersTableAjax': '/customer-table-ajax',
      'salespeopleTableAjax': '/salespeople-table-ajax',
      'usersTableAjax': '/user-table-ajax'
    }
  };

  var tableService = {};

  beforeEach(angular.mock.module('common'));

  beforeEach(angular.mock.inject(function (_tableService_) {
    
    tableService = _tableService_;
    
  }));

  describe('table service initialization', function () {
    
    it('table service tableTitle(total, filtered) should return either filtered or total string', function () {
      
      var total = 1000, filtered = 500, title = tableService.tableTitle(total, filtered);
      
      expect(title).toEqual('500 Filtered Records');
      
      filtered = 1000, title = tableService.tableTitle(total, filtered);
      
      expect(title).toEqual('1000 Total Records');
      
    });
    
    it('table service getTableUrl(name, config, options) should return valid URLs', function () {
      
      var options = {sales_attr_id: 183, customer_id: 398};
      
      var productsTableUrl = tableService.getTableUrl('Products', config, options);
      
      expect(productsTableUrl).toEqual('/products-table-ajax?zff_sales_attr_id=183&zff_customer_id=398');
    
    });
    
  });

});


