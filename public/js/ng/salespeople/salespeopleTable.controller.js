(function () {
  'use strict';

  angular
          .module('salespeople')
          .controller('SalespeopleTableController', ['$rootScope', '$scope', '$filter', '$compile', '$window', 'DTOptionsBuilder', 'DTColumnBuilder', '$uibModal', '$http', 'config', 'screenService', 'tableService', 'localStorageService', SalespeopleTableController]);

  function SalespeopleTableController($rootScope, $scope, $filter, $compile, $window, DTOptionsBuilder, DTColumnBuilder, $uibModal, $http, config, screenService, tableService, localStorageService) {

    //screenService.showOverlay();

    /*
     * controllerAs syntax
     * @type this
     */
    var vm = this;
    
    vm.salespeopleTablePageSize = 'salespeopleTablePageSize';

    vm.start;
    vm.pageSize;
    vm.pageSizes = config.pageSizes;
    vm.page = 1; //not zero based.
    vm.status; //string status eg. Enabled or Disabled
    vm.recordsTotal;
    vm.recordsFiltered;

    // 0. Web Service returns no Salespeople. (Display Warning Modal explaining Web Service has returned zero Salespeople)
    // 1. Web Service returns same number as DB. (Do nothing - we're good to go)
    // 2. Web Service returns more than DB. (Render Add Salesperson Button)
    // 3. Web Service returns less than DB. (Render Manage Users Button in table header)

    vm.zff_username;
    vm.zff_email;
    vm.zff_status;
    vm.zff_fullname;
    vm.zff_createddate;
    vm.zff_lastlogindate;
    vm.zff_createddate_open = false;
    vm.zff_lastlogindate_open = false;

    //mismatch between Web Service and DB
    vm.missingFromDBSalespeople;
    vm.missingFromWebServiceSalespeople;

    vm.dateformat = 'MM/dd/yyyy';
    vm.altInputFormats = ['M!/d!/yyyy'];
    vm.dateOptions = {
      dateDisabled: false,
      formatYear: 'yy',
      maxDate: new Date(),
      minDate: new Date(2016, 1, 1),
      startingDay: 1
    };

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
      DTColumnBuilder.newColumn(1).withTitle('Username'),
      DTColumnBuilder.newColumn(2).withTitle('Email'),
      DTColumnBuilder.newColumn(3).withTitle('Status').renderWith(renderStatus),
      DTColumnBuilder.newColumn(4).withTitle('Full Name'),
      DTColumnBuilder.newColumn(5).withTitle('Created'),
      DTColumnBuilder.newColumn(6).withTitle('Last Login'),
      DTColumnBuilder.newColumn(7).withTitle('Actions').renderWith(renderActions)
    ];

    function createdRow(row, data, dataIndex) {
      // Recompiling so we can bind Angular directive to the DT
      if (data[data.length - 1] === 'missing') {

        angular.element(row).addClass('missing-from-web-service');
      }
      $compile(angular.element(row).contents())($scope);

    }
    
    vm.clickCustomers = function(sales_attr_id, salesperson_name){
      localStorageService.set('salesperson_name', salesperson_name);
      localStorageService.set('sales_attr_id', sales_attr_id);
      $window.location = '/customer/view/' + sales_attr_id;
    }

    //initialize
    activate();

    function renderStatus(data, type, full) {
      return data === '1' ? 'Active' : 'Inactive';
    }

    function missing(row) {
      return row[row.length - 1] === 'missing';
    }

    //actions columns renderer
    function renderActions(data, type, full) {

      if (missing(full)) {
        return '';
      }

      var aroundTableActions = angular.element('<div/>', {
        class: 'around-table-actions'
      });

      //link to customers
      var linkButton = angular.element('<a/>', {
        'ng-click': 'salespeopleCtrl.clickCustomers(' + data + ', "' + full[4] + '")',
        class: 'btn btn-default btn-square btn-transparent salespeople-btn',
        'uib-popover': 'View ' + full[4] + '\'s Customers',
        'popover-placement': 'left',
        'popover-trigger': "'mouseenter'",
        'popover-append-to-body': "'true'"
      }).
              //add button to div
              appendTo(aroundTableActions);

      angular.element('<i/>', {
        class: 'ion ion-person spin-logo'
      }).
              //add icon to edit user button
              appendTo(linkButton);

      return aroundTableActions.prop('outerHTML');
    }

    vm.reloadData = function () {
      vm.dtInstance.rerender();
    };

    function warn(title, msg) {
      screenService.showWarning(title, msg);
    }

    function searchSalespeople() {
      var params = [
      ];
      if (prop('zff_username')) {
        params.push('zff_username=' + encodeURIComponent(vm.zff_username));
      }
      if (prop('zff_email')) {
        params.push('zff_email=' + encodeURIComponent(vm.zff_email));
      }
      if (prop('zff_status')) {
        params.push('zff_status=' + encodeURIComponent(vm.zff_status));
      }
      if (prop('zff_fullname')) {
        params.push('zff_fullname=' + encodeURIComponent(vm.zff_fullname));
      }
      if (prop('zff_createddate')) {
        params.push('zff_createddate=' + encodeURIComponent(format(vm.zff_createddate, 'yyyy-MM-dd')));
      }
      if (prop('zff_lastlogindate')) {
        //2017-03-10 09:08:20
        params.push('zff_lastlogindate=' + encodeURIComponent(format(vm.zff_lastlogindate, 'yyyy-MM-dd')));
      }
      if (vm.pageSize) {
        params.push('zff_length=' + encodeURIComponent(vm.pageSize));
      }
      if (vm.status) {
        params.push('zff_status=' + encodeURIComponent(vm.status === 'Active' ? 1 : 0));
      }
      params.push('zff_page=' + encodeURIComponent(vm.page));

      return tableService.getTableUrl('Salespeople', config, params);
    }

    function prop(key) {
      return vm[key];
    }

    function identifyMissingFromWebServiceRowsInTable(rows) {
      for (var i = 0; i < rows.length; i++) {
        //data array is array of arrays
        var clazzed = false;
        for (var j = 0; j < vm.missingFromWebServiceSalespeople.length; j++) {
          //array of objects -- match ids to identify
          //co-erse both to string
          var idwebservice = vm.missingFromWebServiceSalespeople[j]['id'] + '';
          var idrows = rows[i][0] + '';
          if (idwebservice === idrows) {
            rows[i].push('missing');
            clazzed = true;
          }
        }
        if (!clazzed) {
          rows[i].push('found');
        }
      }
    }

    function search(sSource, aoData, fnCallback, oSettings) {
      screenService.showOverlay();
      oSettings.jqXHR = $.ajax({
        'dataType': 'json',
        'type': 'POST',
        'url': searchSalespeople(),
        'data': {jsonData: JSON.stringify(aoData)},
        'success': function (data, textStatus, jqXHR) {
          //local stuff.
          vm.missingFromDBSalespeople = data.missingFromDBSalespeople;
          vm.missingFromWebServiceSalespeople = data.missingFromWebServiceSalespeople;
          vm.recordsTotal = data.recordsTotal;
          vm.recordsFiltered = data.recordsFiltered;
          vm.start = data.start;
          identifyMissingFromWebServiceRowsInTable(data.data);
          $scope.$apply();
          fnCallback(data, textStatus, jqXHR);
          screenService.hideOverlay();
        },
        'error': function (jqXHR, errorMsg, exception) {
          screenService.hideOverlay();
          //now show a warn modal to inform the request failed.
          var msg = 'Table request has failed. \n';
          if (exception) {
            msg += 'Message: ' + JSON.stringify(exception) + '. ';
          } else if (errorMsg) {
            msg += 'Message: ' + JSON.stringify(errorMsg) + '. ';
          }
          msg += 'Please inform IT of this error.';
          warn('Table Data Source Failed', msg);
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

    vm.manageUsers = function (id) {
      $window.location.href = '';
    }
    
    vm.addSalesperson = function (type, index) {

      var modalInstance;

      //call the modal instance to do it's job.
      $rootScope.$broadcast("dismissModals", {launcher: "salespeopleTable::edit"});

      modalInstance = $uibModal.open({
        size: 'lg',
        animation: true,
        ariaLabelledBy: 'modal-title',
        ariaDescribedBy: 'modal-body',
        templateUrl: 'addSalespersonModal.html',
        controller: 'AddSalespersonModalController',
        controllerAs: 'vmc',
        resolve: {
          salesAttrId: function () {

            return vm[type][index]['salesAttrId'];
          },
          full_name: function () {

            return vm[type][index]['full_name'];
          },
          username: function () {

            return vm[type][index]['username'];
          },
          email: function () {

            return vm[type][index]['email'];
          },
          phone1: function () {

            return vm[type][index]['phone1'];
          },
          status: function () {

            return vm[type][index]['status']
          },
          id: function () {

            return vm[type][index]['id']
          },
          role: function () {

            //currently pluck the first one, but if we ever need to support 
            //multiple roles or inheritance for Roles
            //this will become a bug.
            return vm[type][index]['roles'] ? vm[type][index]['roles'][0] : 'sales';
          }
        }
      });

      modalInstance.result.then(function (vmc) {

        screenService.showOverlay();

        var email = vmc.addSalespersonModal.email,
                scenario = vmc.addSalespersonModal.scenario,
                role = vmc.addSalespersonModal.role,
                username = vmc.addSalespersonModal.username,
                id = vmc.addSalespersonModal.id,
                password = vmc.addSalespersonModal.password,
                password_verify = vmc.addSalespersonModal.password_verify,
                full_name = vmc.addSalespersonModal.full_name,
                phone1 = vmc.addSalespersonModal.phone1,
                status = vmc.addSalespersonModal.status,
                salesAttrId = vmc.addSalespersonModal.salesAttrId;

        var resultHandler = function (data) {
          //reload data on success
          vm.reloadData();
        };
        var finalHandler = function () {
          ///hide overlay regardless
          screenService.hideOverlay();
        };

        addSalesperson(scenario, role, id, password, password_verify, email, username, full_name, phone1, status, salesAttrId, resultHandler, finalHandler);

        modalInstance = null;

      }, function (dismissed) {

        modalInstance = null;

      });

    };

    function addSalesperson(scenario, role, id, password, password_verify, email, username, full_name, phone1, status, salesAttrId, resultHandler, finalHandler) {

      var data = {
        id: id,
        password: password,
        password_verify: password_verify,
        email: email,
        username: username,
        role: role,
        full_name: full_name,
        phone1: phone1,
        status: status,
        salesAttrId: salesAttrId,
        scenario: scenario
      };


      $http
              .post(config.urls.addSalespersonUrl, $.param(data))
              .then(function (response) {

                if (typeof resultHandler === "function") {

                  resultHandler(response.data, data);
                }

                if (typeof finalHandler === "function") {
                  finalHandler();
                }

              }, function () {

                if (typeof finalHandler === "function") {
                  finalHandler();
                }
              });
    }

    vm.selectStatus = function (status) {
      if (status !== vm.status) {
        vm.status = status;
        vm.reloadData();
      }
    };

    vm.selectPageSize = function (size) {
      if (size !== vm.pageSize) {
        vm.pageSize = size;
        localStorageService.set(vm.salespeopleTablePageSize, size);
        vm.reloadData();
      }
    };

    function rowClickHandler(data) {
      //only allow click when no missing class on the data
      if (!missing(data)){
        localStorageService.set('salesperson_name', data[4]);
        localStorageService.set('sales_attr_id', data[7]);
        $window.location = '/customer/view/' + data[7];
      }
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

    function format(dt, format) {
      return $filter('date')(new Date(dt), format);
    }

    function resetVmProps() {
      vm.start = 0;
      vm.pageSize = config.pageSize;
      vm.page = 0;
      vm.recordsTotal = 0;
      vm.recordsFiltered = 0;
      delete vm.zff_username;
      vm.zff_username = '';
      delete vm.zff_email;
      vm.zff_email = '';
      delete vm.zff_status;
      vm.zff_status = '';
      delete vm.zff_fullname;
      vm.zff_fullname = '';
      delete vm.zff_createddate;
      vm.zff_createddate;
      delete vm.zff_lastlogindate;
      vm.zff_lastlogindate;
      delete vm.zff_createddate_open;
      vm.zff_createddate_open = false;
      delete vm.zff_lastlogindate_open;
      vm.zff_lastlogindate_open = false;
      
      vm.pageSize = localStorageService.get(vm.salespeopleTablePageSize) ?
              localStorageService.get(vm.salespeopleTablePageSize) :
              config.pageSize;
    }

    function activate() {

      resetVmProps();

    }
  }
})();
