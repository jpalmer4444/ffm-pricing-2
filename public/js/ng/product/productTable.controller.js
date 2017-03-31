(function () {
  'use strict';

  angular
          .module('product')
          .controller('ProductTableController', ['$timeout', '$scope', '$filter', '$uibModal', '$compile', '$http', '$window', 'DTOptionsBuilder', 'DTColumnBuilder', 'config', 'screenService', 'localStorageService', ProductTableController]);

  function ProductTableController($timeout, $scope, $filter, $uibModal, $compile, $http, $window, DTOptionsBuilder, DTColumnBuilder, config, screenService, localStorageService) {

    /*
     * controllerAs syntax
     * @type this
     */
    var vm = this;

    vm.pdfrows;
    vm.skus = [];
    vm.products = [];
    vm.uoms = [];

    //constants (keys)
    vm.productTablePageSize = 'productTablePageSize';

    vm.columns = [
      '', 'ID', 'Product',
      'Description', 'Comment', 'Option',
      'Wholesale', 'Retail', 'Override',
      'UOM', 'Status', 'Saturday Enabled',
      'SKU', 'Actions'
    ];

    //do not change after constructor is called
    vm.db_synced_once = false;
    vm.selected = false;
    vm.callbacksAdded = false;

    vm.start;
    vm.pageSize;
    vm.pageSizes = config.pageSizes;
    vm.page = 1; //not zero based.
    vm.zff_status = ''; //string status eg. Enabled or Disabled
    vm.recordsTotal;
    vm.salesperson = localStorageService.get('salesperson_name'); //set on Customer page.
    vm.company = localStorageService.get('company');//set on customer page.
    vm.name = localStorageService.get('name');//set on customer page.
    vm.recordsFiltered;
    vm.zff_productname;
    vm.zff_description;
    vm.zff_comment;
    vm.zff_option;
    vm.zff_wholesale;
    vm.zff_retail;
    vm.zff_override;
    vm.zff_uom;
    vm.zff_saturdayenabled;
    vm.zff_sku;


    //get a reference to the table
    vm.dtInstance = {};

    //build table options
    vm.dtOptions = DTOptionsBuilder.newOptions()
            .withFnServerData(search)
            .withDataProp('data')
            .withDOM('t')
            .withOption('processing', true)
            .withOption('initComplete', function () {
              //place the PDF button in the header.
              var button = element('.dt-buttons')
                      .children('a')
                      .eq(1);
              button.removeClass('hide')
                      .addClass('table-header-btn reset-filters-btn btn-accent')
                      .attr('title', 'Download PDF')
                      .insertAfter(element('#add_product_button'));
              element('<i/>', {
                'class': 'ion-arrow-down-c'
              }).appendTo(button);

            })
            .withOption('scrollY', config.scrollY)
            .withOption('scrollX', true)
            .withOption('order', [[2, 'asc']])
            .withOption('scrollCollapse', true)
            .withOption('serverSide', true)
            .withOption('serverSide', true)
            .withButtons(buttons())
            .withOption('createdRow', createdRow)
            .withOption('headerCallback', function (thead, data, start, end, display) {
              if (!vm.callbacksAdded) {
                element('#productsTable tbody').on('click', 'tr > td:nth-child(' + (columnindex("") + 1) + ')', checkbox_click);
                element('#productsTable tbody').on('click', 'tr > td:nth-child(' + (columnindex("ID") + 1) + ')', checkbox_click);
                element('#productsTable tbody').on('click', 'tr > td:nth-child(' + (columnindex("Product") + 1) + ')', checkbox_click);
                element('#productsTable tbody').on('click', 'tr > td:nth-child(' + (columnindex("Description") + 1) + ')', checkbox_click);
                element('#productsTable tbody').on('click', 'tr > td:nth-child(' + (columnindex("Comment") + 1) + ')', checkbox_click);
                element('#productsTable tbody').on('click', 'tr > td:nth-child(' + (columnindex("Option") + 1) + ')', checkbox_click);
                element('#productsTable tbody').on('click', 'tr > td:nth-child(' + (columnindex("Wholesale") + 1) + ')', checkbox_click);
                element('#productsTable tbody').on('click', 'tr > td:nth-child(' + (columnindex("Retail") + 1) + ')', checkbox_click);
                element('#productsTable tbody').on('click', 'tr > td:nth-child(' + (columnindex("Override") + 1) + ')', checkbox_click);
                element('#productsTable tbody').on('click', 'tr > td:nth-child(' + (columnindex("UOM") + 1) + ')', checkbox_click);
                element('#productsTable tbody').on('click', 'tr > td:nth-child(' + (columnindex("Status") + 1) + ')', checkbox_click);
                element('#productsTable tbody').on('click', 'tr > td:nth-child(' + (columnindex("Saturday Enabled") + 1) + ')', checkbox_click);
                element('#productsTable tbody').on('click', 'tr > td:nth-child(' + (columnindex("SKU") + 1) + ')', checkbox_click);
                vm.callbacksAdded = true;
              }
            });

    vm.columns = [
      '', 'ID', 'Product', 'Description', 'Comment', 'Option', 'Wholesale',
      'Retail', 'Override', 'UOM', 'Status', 'Saturday Enabled', 'SKU', 'Actions'
    ];

    vm.dtColumns = [
      DTColumnBuilder.newColumn(columnindex("")).withTitle(vm.columns[columnindex("")]).renderWith(renderCheckbox).notSortable(),
      DTColumnBuilder.newColumn(columnindex("ID")).withTitle(vm.columns[columnindex("ID")]),
      DTColumnBuilder.newColumn(columnindex("Product")).withTitle(vm.columns[columnindex("Product")]).withOption('width', '300px'),
      DTColumnBuilder.newColumn(columnindex("Description")).withTitle(vm.columns[columnindex("Description")]),
      DTColumnBuilder.newColumn(columnindex("Comment")).withTitle(vm.columns[columnindex("Comment")]),
      DTColumnBuilder.newColumn(columnindex("Option")).withTitle(vm.columns[columnindex("Option")]),
      DTColumnBuilder.newColumn(columnindex("Wholesale")).withTitle(vm.columns[columnindex("Wholesale")]).renderWith(renderWholesale),
      DTColumnBuilder.newColumn(columnindex("Retail")).withTitle(vm.columns[columnindex("Retail")]).renderWith(renderRetail),
      DTColumnBuilder.newColumn(columnindex("Override")).withTitle(vm.columns[columnindex("Override")]).renderWith(renderMoney),
      DTColumnBuilder.newColumn(columnindex("UOM")).withTitle(vm.columns[columnindex("UOM")]).withOption('width', '25px'),
      DTColumnBuilder.newColumn(columnindex("Status")).withTitle(vm.columns[columnindex("Status")]).renderWith(renderStatus),
      DTColumnBuilder.newColumn(columnindex("Saturday Enabled")).withTitle(vm.columns[columnindex("Saturday Enabled")]).renderWith(renderSaturdayEnabled),
      DTColumnBuilder.newColumn(columnindex("SKU")).withTitle(vm.columns[columnindex("SKU")]),
      DTColumnBuilder.newColumn(columnindex("Actions")).withTitle(vm.columns[columnindex("Actions")]).renderWith(renderActions).notSortable()
    ];

    //Private API

    function api() {
      return vm.dtInstance.DataTable;
    }

    /**
     * 
     * @returns {Array}
     */
    function aodata() {
      var draw = {"name": "draw", value: 1};
      var columns = {name: "columns", value: []};
      for (var i = 0; i < vm.dtColumns.length; i++) {
        var dt = vm.dtColumns[i];
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

    /**
     * 
     * @param {String} columnname
     * @returns {Number}
     */
    function columnindex(columnname) {
      return vm.columns.indexOf(columnname);
    }

    /**
     * 
     * @param {String} selector
     * @param {Object} context
     * @returns {$}
     */
    function element(selector, context) {
      return selector ? angular.element(selector, context) : $;
    }

    /**
     * 
     * @param {object} data
     * @returns {unresolved}
     */
    function param(data) {
      return element().param(data);
    }

    /**
     * 
     * @param {object} settings
     * @returns {unresolved}
     */
    function ajax(settings) {
      return element().ajax(settings);
    }

    /**
     * 
     * @param {array} row
     * @param {string} data
     * @param {string} dataIndex
     * @returns {undefined}
     */
    function createdRow(row, data, dataIndex) {
      if (eq(data[columnindex("Status")], '0')) {
        element(row).addClass('disabled');
      }
      if (eq(data[columnindex("")], '1')) {
        element(row).addClass('selected');
      }
      if (getDuplicateSkuStyle(data[columnindex('SKU')])) {
        element(row).addClass('skus-match');
      }
      $compile(element(row).contents())($scope);
    }

    /**
     * 
     * @param {string} get
     * @param {string} set
     * @returns {unresolved}
     */
    function storage(get, set) {
      var op = set ? localStorageService.set(get, set) :
              localStorageService.get(get);
      return op;
    }

    /**
     * 
     * @returns {boolean}
     */
    function anySelected() {
      var selected = false;
      api().rows({page: 'all'}).every(function (rowIdx, tableLoop, rowLoop) {
        if (celldata(rowIdx, 0) === '1') {
          selected = true;
        }
      });
      return selected;
    }

    /**
     * 
     * @param {Date} date
     * @param {string} format
     * @param {string} timezone
     * @returns {unresolved}
     */
    function formatDate(date, format, timezone) {
      return $filter('date')(date, format, timezone);
    }

    /**
     * 
     * @param {int} date
     * @returns {String}
     */
    function dateending(date) {
      switch (date) {
        case 1:
        case 21:
        case 31:
          return 'st';
        case 2:
        case 22:
          return 'nd';
        default :
          return 'th';
      }
    }

    /**
     * 
     * @param {mixed} a
     * @param {mixed} b
     * @returns {Boolean}
     */
    function eq(a, b) {
      return a === b;
    }

    /**
     * 
     * @param {int} row
     * @param {int} column
     * @returns {unresolved}
     */
    function cell(row, column) {
      return api().cell(row, column);
    }

    /**
     * 
     * @param {int} row
     * @param {int} column
     * @returns {unresolved}
     */
    function celldata(row, column) {
      return cell(row, column).data();
    }

    /**
     * 
     * @param {object} options
     * @returns {unresolved}
     */
    function rows(options) {
      return api().rows(options);
    }

    /**
     * 
     * @param {string} string
     * @returns {String}
     */
    function stringify(string) {
      return JSON.stringify(string);
    }

    /**
     * 
     * @param {string} message
     * @returns {undefined}
     */
    function log(message) {
      console.log((typeof message === 'object') ? stringify(message) : message);
    }

    /**
     * 
     * @returns {unresolved}
     */
    function getSalesAttrId() {
      return storage('sales_attr_id') ? storage('sales_attr_id') : config.salesAttrId;
    }

    /**
     * 
     * @param {array} params
     * @param {string} name
     * @param {boolean} stripzff
     * @returns {undefined}
     */
    function encodename(params, name, stripzff) {
      stripzff ? params.push(name + '=' + encodeURIComponent(vm[name.substr(4)])) :
              params.push(name + '=' + encodeURIComponent(vm[name]));
    }

    /**
     * 
     * @param {array} params
     * @param {string} name
     * @param {string} vmname
     * @returns {undefined}
     */
    function encodeunname(params, name, vmname) {
      params.push(name + '=' + encodeURIComponent(vm[vmname]));
    }

    /**
     * 
     * @param {array} params
     * @param {string} name
     * @param {string} test
     * @returns {undefined}
     */
    function encodebool(params, name, test) {
      params.push(name + '=' + encodeURIComponent(vm[name] === test ? 1 : 0));
    }

    /**
     * 
     * @param {String} key
     * @returns {unresolved}
     */
    function prop(key) {
      return vm[key];
    }

    /**
     * 
     * @param {string} data column data 
     * @param {string} type datatype datatables expects for this column
     * @param {array} full row data
     * @returns {String}
     */
    function renderMoney(data, type, full) {
      return data && data !== '0' ? '$' + data : data;
    }

    /**
     * 
     * @param {string} data column data 
     * @param {string} type datatype datatables expects for this column
     * @param {array} full row data
     * @returns {String}
     */
    function renderStatus(data, type, full) {
      return data && data === '1' ? 'Enabled' : 'Disabled';
    }

    /**
     * 
     * @param {string} data column data 
     * @param {string} type datatype datatables expects for this column
     * @param {array} full row data
     * @returns {String}
     */
    function renderCheckbox(data, type, full) {
      return data && data === '1' ? '<i class="ion ion-android-checkbox-outline checker"></i>' :
              '<i class="ion ion-android-checkbox-outline-blank checker"></i>';
    }

    /**
     * 
     * @param {string} data column data 
     * @param {string} type datatype datatables expects for this column
     * @param {array} full row data
     * @returns {String}
     */
    function renderSaturdayEnabled(data, type, full) {
      return data && data === '1' ? 'On' : 'Off';
    }

    /**
     * 
     * @param {string} data column data 
     * @param {string} type datatype datatables expects for this column
     * @param {array} full row data
     * @returns {String}
     */
    function renderWholesale(data, type, full) {
      var rendered = '';
      var wholesale = full[columnindex('Wholesale')];
      var status = full[columnindex('Status')];
      if (eq(status, '0')) {
        rendered = 'N/A';
      } else {
        rendered = renderMoney(wholesale);
      }
      return rendered;
    }

    /**
     * 
     * @param {string} data column data 
     * @param {string} type datatype datatables expects for this column
     * @param {array} full row data
     * @returns {String}
     */
    function renderRetail(data, type, full) {
      var rendered = '';
      var retail = full[columnindex('Retail')];
      var status = full[columnindex('Status')];
      if (eq(status, '0')) {
        rendered = 'Call for availability';
      } else {
        rendered = renderMoney(retail);
      }
      return rendered;
    }

    /**
     * 
     * @param {string} data column data 
     * @param {string} type datatype datatables expects for this column
     * @param {array} full row data
     * @param {object} meta row metadata (index can be obtained)
     * @returns {String}
     */
    function renderActions(data, type, full, meta) {

      var aroundTableActions = element('<div/>', {
        class: 'around-table-actions'
      });

      var addProductButton = element('<a/>', {
        id: 'addProductButton',
        'ng-click': 'productCtrl.addProduct()',
        class: 'btn btn-default btn-square btn-transparent',
        'uib-popover': 'Add Product',
        'popover-placement': 'left',
        'popover-trigger': "'mouseenter'",
        'popover-append-to-body': "'true'"
      }).appendTo(aroundTableActions);

      element('<i/>', {
        class: 'ion ion-plus spin-logo'
      }).appendTo(addProductButton);

      var overrideprice = full[columnindex('Override')] ? full[columnindex('Override')] : '';
      var product_id = full[columnindex('ID')] ? full[columnindex('ID')] : '';

      var overridePriceButton = element('<a/>', {
        id: 'overridePriceButton',
        'ng-click': 'productCtrl.addOverridePrice("' + overrideprice + '", "' + product_id + '")',
        class: 'btn btn-default btn-square btn-transparent',
        'uib-popover': 'Override Price',
        'popover-placement': 'left',
        'popover-trigger': "'mouseenter'",
        'popover-append-to-body': "'true'"
      }).appendTo(aroundTableActions);

      element('<i/>', {
        class: 'ion ion-social-usd spin-logo'
      }).appendTo(overridePriceButton);

      var firstLetterOfId = full[1][0];

      if (firstLetterOfId === 'A') {

        var removeAddedProductButton = element('<a/>', {
          id: 'removeAddedProductButton',
          'ng-click': 'productCtrl.confirmDeleteAddedProduct("' + full[columnindex('ID')] + '")',
          class: 'btn btn-default btn-square btn-transparent',
          'uib-popover': 'Remove Added Product',
          'popover-placement': 'left',
          'popover-trigger': "'mouseenter'",
          'popover-append-to-body': "'true'"
        }).appendTo(aroundTableActions);

        element('<i/>', {
          class: 'ion ion-close spin-logo'
        }).appendTo(removeAddedProductButton);

      }
      return aroundTableActions.prop('outerHTML');
    }

    /**
     * 
     * @param {Object} $e
     * @returns {undefined}
     */
    function checkbox_click($e) {

      //log('checkbox_click() ');

      var td = $(this);

      //log(td);

      if (!td.is('td')) {
        var label = 'Row Click Error';
        var text = [
          'Row Click Error has occurred.',
          ' We are sorry for the inconv',
          'enience. Please inform IT th',
          'at a Row Click Error has occ',
          'urred on the Products page.'
        ].join('');

        screenService.showWarning(label, text);

      }

      var tr = $(this).parent('tr'),
              rowindex = tr.index();

      var selected = tr.hasClass('selected');

      selected ? tr.removeClass('selected') : tr.addClass('selected');

      var checkbox = cell(rowindex, 0);

      checkbox.data(selected ? '0' : '1');

      checkbox.render();

      var id = celldata(rowindex, 1);

      var data = {

        'id': id,

        'salesperson': getSalesAttrId(),

        'customer': storage('customer_id'),

      };

      var query = selected ? '?myaction=deselect' : '?myaction=select';

      var url = config.urls.productsTableChecked + query;

      $http.post(url, param(data))
              .then(function (response) {
                //this should not be necessary, but it is because of a bug - fix it!
                api().draw(false);
              }, function (err) {
                log('Error! ' + stringify(err));
              });
    }

    /**
     * 
     * @param {String} sku
     * @returns {undefined}
     */
    function getDuplicateSkuStyle(sku) {
      var found = false;
      var foundAgain = false;
      vm.skus.forEach(function (val, index, array) {
        if (eq(sku, val)) {
          if (!found) {
            found = true;
          } else if (found) {
            foundAgain = true;
          }
        }
      });
      return foundAgain;
    }
    ;

    /**
     * 
     * @param {boolean} hidePageParams
     * @returns {String}
     */
    function searchUsers(hidePageParams) {
      var params = [
      ];
      if (!vm.db_synced_once) {
        params.push('zff_sync=1');
        vm.db_synced_once = true;
      } else {
        params.push('zff_sync=0');
      }

      if (prop('zff_productname')) {
        encodename(params, 'zff_productname');
      }
      if (prop('zff_description')) {
        encodename(params, 'zff_description');
      }
      if (prop('zff_comment')) {
        encodename(params, 'zff_comment');
      }
      if (prop('zff_option')) {
        encodename(params, 'zff_option');
      }
      if (prop('zff_wholesale')) {
        encodename(params, 'zff_wholesale');
      }
      if (prop('zff_retail')) {
        encodename(params, 'zff_retail');
      }
      if (prop('zff_override')) {
        encodename(params, 'zff_override');
      }
      if (prop('zff_uom')) {
        encodename(params, 'zff_uom');
      }
      if (eq(vm.zff_saturdayenabled, 'Off') || eq(vm.zff_saturdayenabled, 'On')) {
        encodebool(params, 'zff_saturdayenabled', 'On');
      }
      if (prop('zff_sku')) {
        encodename(params, 'zff_sku');
      }
      if (vm.pageSize && !hidePageParams) {
        encodeunname(params, 'zff_length', 'pageSize');
      }
      if (eq(vm.zff_status, 'Disabled') || eq(vm.zff_status, 'Enabled')) {
        encodebool(params, 'zff_status', 'Enabled');
      }

      (!hidePageParams)
      encodename(params, 'zff_page', true)

      var sales_attr_id = getSalesAttrId();
      params.push('zff_sales_attr_id=' + sales_attr_id);

      var customer_id = storage('customer_id');

      if (!customer_id) {

        var confirmationCallbackFnc = function () {

          $window.location = '/customer/view/';

        };

        var title = 'No Customer';
        var cancel = 'Cancel';
        var back = 'Back';
        var text = [
          'No Customer was found. You can go ba',
          'ck to Customers page and choose one.'
        ].join('')

        screenService.hideOverlay();

        return screenService.showConfirmation(title, text, cancel, back, confirmationCallbackFnc);
      }

      params.push('zff_customer_id=' + customer_id);

      var query = config.urls.productsTableAjax + (params.length ? '?' + params.join('&') : '');

      return query;
    }

    /**
     * 
     * @param {function} cb
     * @returns {undefined}
     */
    function searchpdf(cb) {

      var url = searchUsers(true);

      url += '&zff_page=0';
      url += '&zff_length=1000';

      ajax({
        'dataType': 'json',
        'type': 'POST',
        'url': url,
        'data': {jsonData: stringify(aodata())},
        'success': function (data, textStatus, jqXHR) {
          //local stuff.
          cb(null, data);
        },
        'error': function (error) {
          cb(error, null);
        }
      });
    }

    /**
     * 
     * @param {String} sSource
     * @param {array} aoData
     * @param {function} fnCallback
     * @param {Object} oSettings
     * @returns {undefined}
     */
    function search(sSource, aoData, fnCallback, oSettings) {
      screenService.showOverlay();
      ajax({
        'dataType': 'json',
        'type': 'POST',
        'url': searchUsers(),
        'data': {jsonData: stringify(aoData)},
        'success': function (data, textStatus, jqXHR) {
          //local stuff.
          vm.recordsTotal = data.recordsTotal;
          vm.recordsFiltered = data.recordsFiltered;
          if (data.skus) {
            vm.skus = [];
            for (var i = 0; i < data.skus.length; i++) {
              vm.skus.push(data.skus[i]);
            }
          }
          if (data.products) {
            vm.products = [];
            for (var i = 0; i < data.products.length; i++) {
              var value = data.products[i].substr(0, data.products[i].indexOf('-') + 1);
              if (vm.products.indexOf(value) === -1) {
                vm.products.push(value);
              }
            }
          }
          if (data.uoms) {
            vm.uoms = [];
            for (var i = 0; i < data.uoms.length; i++) {
              if (vm.uoms.indexOf(data.uoms[i]) === -1) {
                vm.uoms.push(data.uoms[i]);
              }
            }
          }
          vm.start = data.start;
          $scope.$apply();
          fnCallback(data, textStatus, jqXHR);
          screenService.hideOverlay();
        }
      });
    }

    /**
     * 
     * @returns {Array}
     */
    function buttons() {
      return [{
          extend: 'pdfHtml5',
          orientation: 'landscape',
          message: '',
          pageSize: 'LEGAL',
          className: 'hide',
          'customize': function (doc) {
            var newRowData = [];
            var postrows = [];
            var salesperson = vm.salesperson;
            var customer = vm.name;
            var now = new Date();
            var dayofmonth = formatDate(now, "d");
            var guarantee = 'Prices valid as of ' + formatDate(now, "EEEE MMMM d'" + dateending(dayofmonth) + ",' yyyy h:m a");
            var docDefinition = {
              pageSize: 'LEGAL',
              pageOrientation: 'landscape',
              pageMargins: [40, 60, 40, 60],
              styles: {
                guarantee: {
                  fontSize: 18,
                  fontColor: '999999',
                  bold: true
                },
                title: {
                  fontSize: 18,
                  bold: true
                },
                tableStyle: {
                  fontSize: 14,
                  width: '580'
                }
              },
              content: [
                {
                  columns: [
                    [
                      {
                        text: salesperson,
                        alignment: 'left',
                        style: 'title'
                      },
                      {
                        text: storage('salesperson_email'),
                        alignment: 'left',
                        style: 'title'
                      },
                      {
                        text: storage('salesperson_phone'),
                        alignment: 'left',
                        style: 'title'
                      },
                      {
                        text: customer,
                        alignment: 'left',
                        style: 'title'
                      },
                      {
                        text: guarantee,
                        alignment: 'left',
                        style: 'guarantee',
                        width: '70%',
                        margin: [0, 20]
                      }
                    ],
                    {
                      image: 'data:image/png;base64,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',
                      alignment: 'right'
                    }
                  ],
                  columnGap: 10
                },
                {
                  layout: 'lightHorizontalLines',
                  margin: [10, 10],
                  style: 'tableStyle',
                  table: {
                    headerRows: 1,
                    widths: ['*', '*', '*', 'auto', 'auto', 'auto'],
                    body: [
                    ]
                  }
                }
              ]
            };

            var orderBy = $filter('orderBy');

            var pdfrowobjects = [];

            for (var i = 0; i < vm.pdfrows.length; i++) {
              var array = vm.pdfrows[i];
              var j = 0;
              var product = {
                '': array[j++],
                'ID': array[j++],
                'Product': array[j++],
                'Description': array[j++],
                'Comment': array[j++],
                'Option': array[j++],
                'Wholesale': array[j++],
                'Retail': array[j++],
                'Override': array[j++],
                'UOM': array[j++],
                'Status': array[j++],
                'Saturday Enabled': array[j++],
                'SKU': array[j++],
                'Actions': array[j++]
              };
              pdfrowobjects.push(product);
            }

            //orderBy Product
            var pdfrowobjectsfiltered = orderBy(pdfrowobjects, 'Product');

            var pdfrows = [];

            pdfrowobjectsfiltered.forEach(function (pdfrow) {
              pdfrows.push([
                pdfrow[''],
                pdfrow['ID'],
                pdfrow['Product'],
                pdfrow['Description'],
                pdfrow['Comment'],
                pdfrow['Option'],
                pdfrow['Wholesale'],
                pdfrow['Retail'],
                pdfrow['Override'],
                pdfrow['UOM'],
                pdfrow['Status'],
                pdfrow['Saturday Enabled'],
                pdfrow['SKU'],
                pdfrow['Actions']
              ]);
            });

            for (var idx = 0; idx < pdfrows.length; idx++) {

              var tablerow = pdfrows[idx];

              if (tablerow && tablerow[0] === '1') {
                var row = [];
                row.push(tablerow[vm.columns.indexOf('Product')]);
                row.push(tablerow[vm.columns.indexOf('Description')]);
                row.push(tablerow[vm.columns.indexOf('Comment')]);
                row.push(tablerow[vm.columns.indexOf('UOM')]);
                row.push(tablerow[vm.columns.indexOf('SKU')]);
                var override = tablerow[vm.columns.indexOf('Override')];
                if (override) {
                  row.push(tablerow[vm.columns.indexOf('Override')]);
                } else {
                  row.push(tablerow[vm.columns.indexOf('Retail')]);
                }
                if (override || tablerow[vm.columns.indexOf('Status')] === '1') {
                  newRowData.push(row);
                  var postrow = [tablerow[vm.columns.indexOf('ID')], tablerow[vm.columns.indexOf('Retail')], tablerow[vm.columns.indexOf('Override')]];
                  postrows.push(postrow);
                }
              }
            }

            delete vm.pdfrows;

            docDefinition.content[1].table.body.push(['Product', 'Description', 'Comment', 'UOM', 'SKU', 'Price']);
            newRowData.forEach(function (currentValue) {
              docDefinition.content[1].table.body.push(currentValue);
            });
            doc.content = docDefinition.content;
            doc.pageSize = docDefinition.pageSize;
            doc.pageOrientation = docDefinition.pageOrientation;
            doc.pageMargins = docDefinition.pageMargins;
            doc.styles = docDefinition.styles;
            report(postrows);
          }
        }, {
          text: 'PDF',
          className: 'hide',
          action: function (e, dt, node, config) {
            if (anySelected()) {
              //first make the AJAX call and populate the data because after we trigger the 
              //PDF it will run asynchronously and we no longer have any control over flow.
              var callback = function (err, data) {

                if (err) {

                  screenService.showWarning('PDF Download Failed', 'Please contact IT. ' + stringify(err));
                } else {

                  vm.pdfrows = data.data;
                  api().button(0).trigger();
                }
              };

              searchpdf(callback);

            } else {

              var title = 'No Products Selected';

              var text = [
                'You chose to download a PDF, but you have no ',
                'products selected. Please select at least one ',
                'product before clicking PDF. Thank You'
              ].join('');

              screenService.showWarning(title, text);
            }
          }
        }
      ];
    }

    /**
     * 
     * @returns {undefined}
     */
    function resetVmProps() {

      var vmdeletes = [
        'zff_productname',
        'zff_description',
        'zff_status',
        'zff_comment',
        'zff_option',
        'zff_wholesale',
        'zff_retail',
        'zff_override',
        'zff_uom',
        'zff_sku',
        'zff_saturdayenabled'
      ];

      var vmzeros = [
        'start',
        'page',
        'recordsTotal',
        'recordsFiltered'
      ];

      vmdeletes.forEach(function (val, idx, all) {
        delete vm[val];
        vm[val] = '';
      });

      vmzeros.forEach(function (val, idx, all) {
        vm[val] = 0;
      });

      vm.pageSize = localStorageService.get(vm.productTablePageSize) ?
              localStorageService.get(vm.productTablePageSize) :
              config.pageSize;
    }

    /**
     * 
     * @returns {undefined}
     */
    function activate() {

      resetVmProps();

    }

    /**
     * 
     * @param {String} scenario
     * @param {int} id
     * @param {int} product_id
     * @param {number} overrideprice
     * @param {int} sales_attr_id
     * @param {int} customer_id
     * @param {function} resultHandler
     * @param {function} finalHandler
     * @returns {undefined}
     */
    function addOverridePrice(scenario, id, product_id, overrideprice, sales_attr_id, customer_id,
            resultHandler, finalHandler) {

      screenService.showOverlay();

      var data = {
        id: id,
        product_id: product_id,
        overrideprice: overrideprice,
        sales_attr_id: sales_attr_id,
        customer_id: customer_id,
        scenario: scenario
      };


      $http
              .post(config.urls.productsTableOverride, param(data))

              .then(function (response) {

                if (typeof resultHandler === "function") {

                  resultHandler(response);
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

    /**
     * 
     * @param {int} customer_id
     * @param {int} sales_attr_id
     * @param {String} product
     * @param {String} description
     * @param {String} comment
     * @param {String} overrideprice
     * @param {String} uom
     * @param {String} sku
     * @param {function} resultHandler
     * @param {function} finalHandler
     * @returns {undefined}
     */
    function addProduct(customer_id, sales_attr_id, product, description, comment, overrideprice, uom, sku,
            resultHandler, finalHandler) {

      screenService.showOverlay();

      var data = {
        customer_id: customer_id,
        sales_attr_id: sales_attr_id,
        product: product,
        description: description,
        comment: comment,
        overrideprice: overrideprice,
        uom: uom,
        sku: sku
      };


      $http
              .post(config.urls.productsTableProduct, param(data))

              .then(function (response) {

                if (typeof resultHandler === "function") {

                  resultHandler(response);
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

    /**
     * 
     * @param {array} data
     * @returns {undefined}
     */
    function report(data) {

      var post = {
        sales_attr_id: getSalesAttrId(),
        customer_id: storage('customer_id'),
        rows: data
      };

      var resultHandler = function (response) {

        if (!response.success) {
          screenService.showWarning('Report Error Occurred', 'Please Contact IT.');
        }
      }

      var errorHandler = function () {
        screenService.showWarning('Report Error Occurred', 'Please Contact IT. ' +
                (arguments[0] ? stringify(arguments[0]) : ''));
      };


      $http
              .post(config.urls.productsTableReport, param(post))

              .then(function (response) {

                if (typeof resultHandler === "function") {

                  resultHandler(response.data, data);
                }

              }, function () {

                if (typeof errorHandler === "function") {
                  errorHandler();
                }
              });
    }

    //Public API

    vm.tableTitle = function () {
      var total = vm.recordsTotal;
      var filtered = vm.recordsFiltered;
      if (eq(total, filtered)) {
        return filtered + ' Total Records';
      } else {
        return filtered + ' Filtered Records';
      }
    };

    vm.selectAll = function () {

      var query = vm.selected ? "?myaction=deselectall" : "?myaction=selectall"

      var url = config.urls.productsTableChecked + query;

      api().rows().every(function (rowIdx, tableLoop, rowLoop) {

        vm.selected ? element('#productsTable tbody tr').eq(rowIdx).removeClass('selected') :
                element('#productsTable tbody tr').eq(rowIdx).addClass('selected')

        var checkbox = cell(rowIdx, 0);

        checkbox.data(vm.selected ? '0' : '1');

        checkbox.render();

      });

      vm.selected = !vm.selected;

      var data = {

        'salesperson': getSalesAttrId(),

        'customer': storage('customer_id')

      };

      $http.post(url, param(data))

              .then(function (response) {
                api().draw(false);
              }, function (err) {

                log('Error! ' + stringify(err));

              });

    };

    vm.reloadData = function () {
      $timeout(function () {//$timeout forces async
          vm.dtInstance.rerender();
                    }, 0);
    }

    vm.dtInstanceCallback = function (instance) {
      vm.dtInstance = instance;
    };

    vm.resetFilters = function () {
      resetVmProps();
      vm.reloadData();
    };

    vm.selectSaturdayEnabled = function (saturdayenabled) {

      if (saturdayenabled !== vm.zff_saturdayenabled) {

        vm.zff_saturdayenabled = saturdayenabled;
        
          vm.reloadData();
     
      }
    };

    vm.selectStatus = function (status) {

      if (status !== vm.zff_status) {

        vm.zff_status = status;

          vm.reloadData();

      }
    };

    vm.selectPageSize = function (size) {

      if (size !== vm.pageSize) {

        vm.pageSize = size;

        localStorageService.set(vm.productTablePageSize, size);

        vm.reloadData();
      }
    };

    vm.confirmDeleteAddedProduct = function (productId) {

      var confirmFnc = function () {
        vm.deleteAddedProduct(productId);
      };

      var title = 'Delete Added Product?';
      var text = [].join('');
      var dismissLabel = 'Cancel';
      var confirmLabel = 'Delete';

      screenService.showConfirmation(title, text, dismissLabel, confirmLabel, confirmFnc);

    }

    vm.deleteAddedProduct = function (productId) {

      var sales_attr_id = getSalesAttrId();
      var customer_id = storage('customer_id');
      var params = {
        'product_id': productId,
        'sales_attr_id': sales_attr_id,
        'customer_id': customer_id,
        'scenario': 'delete'
      };

      $http.post(config.urls.productsTableProduct, param(params))

              .then(function (response) {

                vm.reloadData();

              }, function () {

                var title = 'Override Price Error';
                var text = 'Sorry, there was a problem removing the override price from the product. Please inform IT.';
                screenService.showWarning(title, text);
              });

    };

    vm.addProduct = function () {

      var modalInstance;

      modalInstance = $uibModal.open({
        size: 'lg',
        animation: true,
        ariaLabelledBy: 'modal-title',
        ariaDescribedBy: 'modal-body',
        templateUrl: 'addProductModal.html',
        controller: 'AddProductModalController',
        controllerAs: 'vmc',
        resolve: {
          customer_id: function () {

            return storage('customer_id');
          },
          sales_attr_id: function () {

            return getSalesAttrId();
          },
          product: function () {

            return '';
          },
          description: function () {

            return '';
          },
          comment: function () {

            return '';
          },
          overrideprice: function () {

            return '';
          },
          uom: function () {

            return '';
          },
          sku: function () {

            return '';
          }
        }
      });

      modalInstance.result.then(function (vmc) {

        var customer_id = vmc.$value.customer_id,
                sales_attr_id = vmc.$value.sales_attr_id,
                product = vmc.$value.product,
                description = vmc.$value.description,
                comment = vmc.$value.comment,
                overrideprice = vmc.$value.overrideprice,
                uom = vmc.$value.uom,
                sku = vmc.$value.sku;

        modalInstance = null;

        var resultHandler = function (data) {
          vm.reloadData();
        };
        var finalHandler = function () {
          ///hide overlay regardless
          screenService.hideOverlay();
        };

        addProduct(customer_id, sales_attr_id, product, description, comment, overrideprice, uom, sku, resultHandler, finalHandler);


      }, function (dismissed) {

        modalInstance = null;

      });

    };

    vm.addOverridePrice = function (overrideprice, product_id) {

      var modalInstance;

      modalInstance = $uibModal.open({
        animation: true,
        ariaLabelledBy: 'modal-title',
        ariaDescribedBy: 'modal-body',
        templateUrl: 'addOverridePriceModal.html',
        controller: 'AddOverridePriceModalController',
        controllerAs: 'vmc',
        resolve: {
          customer_id: function () {

            return storage('customer_id');
          },
          sales_attr_id: function () {

            return getSalesAttrId();
          },
          product_id: function () {

            return product_id;
          },
          overrideprice: function () {

            return overrideprice ? overrideprice : '';
          }
        }
      });

      modalInstance.result.then(function (vmc) {

        var customer_id = vmc.$value.customer_id,
                scenario = vmc.$value.scenario,
                sales_attr_id = vmc.$value.sales_attr_id,
                id = vmc.$value.id,
                product_id = vmc.$value.product_id,
                overrideprice = vmc.$value.overrideprice;

        var resultHandler = function (data) {

          vm.reloadData();
        };
        var finalHandler = function () {

          screenService.hideOverlay();
        };

        addOverridePrice(scenario, id, product_id, overrideprice, sales_attr_id, customer_id, resultHandler, finalHandler);


      }, function (dismissed) {

        modalInstance = null;

      });

    };

    activate();

  }
})();
