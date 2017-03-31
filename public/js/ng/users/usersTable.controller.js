(function () {
  'use strict';

  angular
          .module('users')
          .controller('UsersTableController', ['$scope', 'localStorageService', '$filter', '$compile', '$http', 'DTOptionsBuilder', 'DTColumnBuilder', 'config', 'screenService', UsersTableController]);

  function UsersTableController($scope, localStorageService, $filter, $compile, $http, DTOptionsBuilder, DTColumnBuilder, config, screenService) {

    //screenService.showOverlay();

    /*
     * controllerAs syntax
     * @type this
     */
    var vm = this;
    
    vm.userTablePageSize = 'userTablePageSize';

    vm.start;
    vm.pageSize;
    vm.pageSizes = config.pageSizes;
    vm.page = 1; //not zero based.
    vm.status; //string status eg. Enabled or Disabled
    vm.recordsTotal;
    vm.recordsFiltered;
    vm.zff_username;
    vm.zff_email;
    vm.zff_status;
    vm.zff_fullname;
    vm.zff_createddate;
    vm.zff_lastlogindate;
    vm.zff_createddate_open = false;
    vm.zff_lastlogindate_open = false;

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
      $compile(angular.element(row).contents())($scope);
    }
    
    function warn(title, msg){
      screenService.showWarning(title, msg);
    }

    //initialize
    activate();

    function renderStatus(data, type, full) {
      //wrapping div
      var bootstrapSelect = angular.element('<div/>', {
        class: 'btn-group bootstrap-select filter form-control',
        'uib-dropdown': true
      });

      //ul
      var listdropdown = angular.element('<div/>', {
        class: 'dropdown-menu table-head',
        'uib-dropdown-menu': true
      }).appendTo(bootstrapSelect);

      //liItem1
      var listItem1 = angular.element('<li/>', {}).appendTo(listdropdown);
      angular.element('<a/>', {
        href: '#',
        'ng-click': 'userCtrl.setActive("Active", ' + full[0] + ')',
        text: 'Active'
      }).appendTo(listItem1);

      //liItem2
      var listItem2 = angular.element('<li/>', {}).appendTo(listdropdown);
      
      angular.element('<a/>', {
        href: '#',
        'ng-click': 'userCtrl.setActive("Inactive", ' + full[0] + ')',
        text: 'Inactive'
      }).appendTo(listItem2);

      //Edit button
      var toggleButton = angular.element('<button/>', {
        id: 'single-button',
        type: 'button',
        class: 'btn btn-default',
        'uib-dropdown-toggle': true
      }).
              //add button to div
              appendTo(bootstrapSelect);

      //span1
      angular.element('<span/>', {
        class: 'filter-option pull-left',
        'text': data === '1' ? 'Active' : 'Inactive'
      }).appendTo(toggleButton);

      //caret
      angular.element('<span/>', {
        class: 'caret'
      }).appendTo(toggleButton);

      return bootstrapSelect.prop('outerHTML');
    }

    //actions columns renderer
    function renderActions(data, type, full) {

      var aroundTableActions = angular.element('<div/>', {
        class: 'around-table-actions'
      });

      //Edit button
      var editUserButton = angular.element('<a/>', {
        href: 'users/edit/' + data,
        class: 'btn btn-default btn-square btn-transparent',
        title: 'Edit User'
      }).
              //add button to div
              appendTo(aroundTableActions);

      angular.element('<i/>', {
        class: 'ion ion-edit spin-logo'
      }).
              //add icon to edit user button
              appendTo(editUserButton);

      //Change Password button
      var changePasswordButton = angular.element('<a/>', {
        href: 'users/change-password/' + data,
        class: 'btn btn-default btn-square btn-transparent',
        title: 'Change User Password'
      }).
              //add button to div
              appendTo(aroundTableActions);

      angular.element('<i/>', {
        class: 'ion ion-locked spin-logo'
      }).
              //add icon to edit user button
              appendTo(changePasswordButton);

      return aroundTableActions.prop('outerHTML');
    }

    vm.reloadData = function () {
      vm.dtInstance.rerender();
    }

    function searchUsers() {
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

      var query = config.urls.usersTableAjax + (params.length ? '?' + params.join('&') : '');

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
          $scope.$apply();
          fnCallback(data, textStatus, jqXHR);
          screenService.hideOverlay();
        },
        'error': function(jqXHR, errorMsg, exception){
          screenService.hideOverlay();
          //now show a warn modal to inform the request failed.
          var msg = 'Table request has failed. \n';
          if(exception){
            msg += 'Message: ' + JSON.stringify(exception) + '. ';
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

    vm.selectStatus = function (status) {
      if (status !== vm.status) {
        vm.status = status;
        vm.reloadData();
      }
    };

    vm.setActive = function (status, id) {
      screenService.showOverlay();
      var url = config.urls.usersTableUpdateStatusAjax + '?status=' + (status === 'Active' ? 1 : 0) + '&user_id=' + id;
      //console.log('URL: '+url);
      $http.get(url).success(function (data) {
        if(data.success){
          screenService.hideOverlay();
          vm.reloadData();
        }else{
          screenService.hideOverlay();
          alert('Error! '+JSON.stringify(data));
        }
      }).error(function(error){
        screenService.hideOverlay();
        alert('Error! '+JSON.stringify(error));
      });
    };

    vm.selectPageSize = function (size) {
      if (size != vm.pageSize) {
        vm.pageSize = size;
        localStorageService.set(vm.userTablePageSize, size);
        vm.reloadData();
      }
    };

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
      vm.pageSize = localStorageService.get(vm.userTablePageSize) ?
              localStorageService.get(vm.userTablePageSize) :
              config.pageSize;
    }

    function activate() {

      resetVmProps();

      //initialize pageSize filter
      vm.filters.pageSize = {
        open: false,
        value: vm.pageSize
      };

    }
  }
})();
