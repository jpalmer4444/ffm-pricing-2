(function () {
  'use strict';

  angular
          .module('customer')
          .controller('CustomerTableController', ['$timeout', '$scope', '$filter', '$compile', '$window', 'DTOptionsBuilder', 'DTColumnBuilder', 'config', 'screenService', 'localStorageService', CustomerTableController]);

  function CustomerTableController($timeout, $scope, $filter, $compile, $window, DTOptionsBuilder, DTColumnBuilder, config, screenService, localStorageService) {

    //screenService.showOverlay();

    /*
     * controllerAs syntax
     * @type this
     */
    var vm = this;

    vm.companies = [];
    vm.emails = [];
    vm.names = [];

    vm.customerTablePageSize = 'customerTablePageSize';

    vm.db_synced_once = false;
    vm.start;
    vm.pageSize;
    vm.pageSizes = config.pageSizes;
    vm.page = 1; //not zero based.
    vm.recordsTotal;
    vm.recordsFiltered;
    vm.isSalesperson = !config.salesAttrId;
    vm.zff_company;
    vm.zff_name;
    vm.zff_email;
    vm.zff_created;
    vm.zff_updated;
    vm.zff_created_open = false;
    vm.zff_updated_open = false;

    vm.salesperson = localStorageService.get('salesperson_name');


    vm.dateformat = 'MM/dd/yyyy';
    vm.altInputFormats = ['M!/d!/yyyy'];
    vm.dateOptions = {
      dateDisabled: false,
      formatYear: 'yy',
      maxDate: new Date(),
      minDate: new Date(2016, 1, 1),
      startingDay: 1
    };

    //table filters
    vm.filters = {};

    //get a reference to the table
    vm.dtInstance = {};

    //build angular-datables instance
    //@see http://l-lin.github.io/angular-datatables/archives/#!/api

    //build table options
    vm.dtOptions = DTOptionsBuilder.newOptions()
            .withFnServerData(search)
            .withDataProp('data')
            .withDOM('<"ffmtoolbar">t')
            .withOption('processing', true)
            .withOption('scrollY', config.scrollY)
            .withOption('scrollX', true)
            .withOption('scrollCollapse', true)
            .withOption('serverSide', true)
            .withOption('createdRow', createdRow)
            .withOption('rowCallback', rowCallback)
            .withOption('headerCallback', function (thead, data, start, end, display) {

            });

    //build columns
    vm.dtColumns = [
      DTColumnBuilder.newColumn(0).withTitle('ID '),
      DTColumnBuilder.newColumn(1).withTitle('Company'),
      DTColumnBuilder.newColumn(2).withTitle('Name'),
      DTColumnBuilder.newColumn(3).withTitle('Email'),
      DTColumnBuilder.newColumn(4).withTitle('Created'),
      DTColumnBuilder.newColumn(5).withTitle('Updated'),
      DTColumnBuilder.newColumn(6).withTitle('Actions').renderWith(renderActions)
    ];

    /**
     * @returns {String}
     */
    vm.tableTitle = function () {
      var total = vm.recordsTotal;
      var filtered = vm.recordsFiltered;
      if ((total === filtered)) {
        return filtered + ' Total Records';
      } else {
        return filtered + ' Filtered Records';
      }
    };

    function createdRow(row, data, dataIndex) {
      // Recompiling so we can bind Angular directive to the DT
      $compile(angular.element(row).contents())($scope);
    }

    //initialize
    activate();

    vm.clickProducts = function (company, name, id) {
      localStorageService.set('company', company);
      localStorageService.set('name', name);
      localStorageService.set('customer_id', id);
      $window.location = '/product/view/' + id;
    }

    //actions columns renderer
    function renderActions(data, type, full) {

      var aroundTableActions = angular.element('<div/>', {
        class: 'around-table-actions'
      });

      var viewProductsButton = angular.element('<a/>', {
        'ng-click': 'customerCtrl.clickProducts("' + full[1] + '","' + full[2] + '", ' + full[0] + ')',
        class: 'btn btn-default btn-square btn-transparent',
        'uib-popover': 'View ' + full[1] + ' Product List',
        'popover-placement': 'left',
        'popover-trigger': "'mouseenter'",
        'popover-append-to-body': "'true'"
      }).appendTo(aroundTableActions);

      angular.element('<i/>', {
        class: 'ion ion-eye spin-logo',
      }).appendTo(viewProductsButton);

      return aroundTableActions.prop('outerHTML');
    }

    vm.reloadData = function () {
      $timeout(function () {//$timeout forces async
        vm.dtInstance.rerender();
      }, 0);
    }

    function searchUsers() {
      var params = [
      ];
      if (!vm.db_synced_once) {
        params.push('zff_sync=1');
        vm.db_synced_once = true;
      } else {
        params.push('zff_sync=0');
      }
      if (prop('zff_company')) {
        params.push('zff_company=' + encodeURIComponent(vm.zff_company));
      }
      if (prop('zff_name')) {
        params.push('zff_name=' + encodeURIComponent(vm.zff_name));
      }
      if (prop('zff_email')) {
        params.push('zff_email=' + encodeURIComponent(vm.zff_email));
      }
      if (prop('zff_created')) {
        params.push('zff_created=' + encodeURIComponent(format(vm.zff_created, 'yyyy-MM-dd')));
      }
      if (prop('zff_updated')) {
        params.push('zff_updated=' + encodeURIComponent(format(vm.zff_updated, 'yyyy-MM-dd')));
      }
      if (vm.pageSize) {
        params.push('zff_length=' + encodeURIComponent(vm.pageSize));
      }
      if (vm.status) {
        params.push('zff_status=' + encodeURIComponent(vm.status === 'Active' ? 1 : 0));
      }
      params.push('zff_page=' + encodeURIComponent(vm.page));

      var sales_attr_id = localStorageService.get('sales_attr_id') ?
              localStorageService.get('sales_attr_id') :
              config.salesAttrId;

      //always add sales_attr_id

      params.push('zff_sales_attr_id=' + encodeURIComponent(sales_attr_id));

      var query = config.urls.customersTableAjax + (params.length ? '?' + params.join('&') : '');

      return query;
    }

    function prop(key) {
      return vm[key];
    }

    function search(sSource, aoData, fnCallback, oSettings) {
      screenService.showOverlay();
      oSettings.jqXHR = $.ajax({
        'dataType': 'json',
        'type': 'POST',
        'url': searchUsers(),
        'data': {jsonData: JSON.stringify(aoData)},
        'success': function (data, textStatus, jqXHR) {
          //local stuff.
          vm.recordsTotal = data.recordsTotal;
          vm.recordsFiltered = data.recordsFiltered;
          vm.start = data.start;
          if (data.companies) {
            vm.companies = [];
            for (var i = 0; i < data.companies.length; i++) {
              vm.companies.push(data.companies[i]);
            }
          }
          if (data.names) {
            vm.names = [];
            for (var i = 0; i < data.names.length; i++) {
              vm.names.push(data.names[i]);
            }
          }
          if (data.emails) {
            vm.emails = [];
            for (var i = 0; i < data.emails.length; i++) {
              vm.emails.push(data.emails[i]);
            }
          }
          localStorageService.set('salesperson_phone', data.salesperson_phone);
          localStorageService.set('salesperson_email', data.salesperson_email);
          $scope.$apply();
          fnCallback(data, textStatus, jqXHR);
          screenService.hideOverlay();
        }
      });
    }

    vm.dtInstanceCallback = function (instance) {
      vm.dtInstance = instance;
    };

    vm.resetFilters = function () {
      resetVmProps();
      vm.reloadData();
    };

    vm.selectStatus = function (status) {
      if (status !== vm.status) {
        vm.status = status;
        vm.reloadData();
      }
    };

    vm.selectPageSize = function (size) {
      if (size !== vm.pageSize) {
        vm.pageSize = size;
        localStorageService.set(vm.customerTablePageSize, size);
        vm.reloadData();
      }
    };

    function format(dt, format) {
      return $filter('date')(new Date(dt), format);
    }

    function rowClickHandler(data) {
      localStorageService.set('company', data[1]);
      localStorageService.set('name', data[2]);
      localStorageService.set('customer_id', data[0]);
      $window.location = '/product/view/' + data[0];
    }

    function rowCallback(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
      $('td', nRow).unbind('click');
      $('td', nRow).bind('click', function () {
        $scope.$apply(function () {
          rowClickHandler(aData);
        });
      });
      return nRow;
    }

    function resetVmProps() {
      vm.start = 0;
      vm.pageSize = config.pageSize;
      vm.page = 0;
      vm.recordsTotal = 0;
      vm.recordsFiltered = 0;
      delete vm.zff_company;
      vm.zff_company = '';
      delete vm.zff_name;
      vm.zff_name = '';
      delete vm.zff_email;
      vm.zff_email = '';
      delete vm.zff_created;
      vm.zff_created;
      delete vm.zff_updated;
      vm.zff_updated;
      delete vm.zff_created_open;
      vm.zff_created_open = false;
      delete vm.zff_updated_open;
      vm.zff_updated_open = false;
      vm.pageSize = localStorageService.get(vm.customerTablePageSize) ?
              localStorageService.get(vm.customerTablePageSize) :
              config.pageSize;
    }

    function activate() {


      //if salesperson_name is null (and this can only be when an Admin is a Salesperson 
      //and they click the Customers link directly.) - then look it up on the data-ffm-salesperson 
      //attribute on customers_link from server-side.
      if (!vm.salesperson) {
        vm.salesperson = angular.element('#customers_link').attr('data-ffm-salesperson');
        localStorageService.set('salesperson_name', vm.salesperson);
      }

      resetVmProps();

      //initialize pageSize filter
      vm.filters.pageSize = {
        open: false,
        value: vm.pageSize
      };

    }
  }
})();
