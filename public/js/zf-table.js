(function(jQuery) {
    jQuery.fn.animateGreenHighlight = function (highlightColor, duration) {
        var highlightBg = highlightColor || "#68e372";
        var animateMs = duration || "1000"; // edit is here
        var originalBg = this.css("background-color");

        if (!originalBg || originalBg == highlightBg)
            originalBg = "#FFFFFF"; // default to white

        jQuery(this)
            .css("backgroundColor", highlightBg)
            .animate({ backgroundColor: originalBg }, animateMs, null, function () {
                jQuery(this).css("backgroundColor", originalBg);
            });
    };

    jQuery.fn.zfTable = function(url , options) {

        var initialized = false;

        var table = false;

        var defaults = {

            beforeSend: function(){},
            success: function(){},
            error: function(){},
            complete: function(){},

            onInit: function(){},
            sendAdditionalParams: function(){ return '';},
            tableDiv: false
        };

        var options = $.extend(defaults, options);

        function strip(html){
            var tmp = document.createElement("DIV");
            tmp.innerHTML = html;
            return tmp.textContent || tmp.innerText || "";
        }

        function init($obj) {
            options.onInit();
            ajax($obj, true);

            table = $obj;
        }

        function ajax($obj, init, column, row) {
            var result = "";
            var data = $obj.find(':input').serialize();
            data = data.split("&");
            var items = [];

            $.each(data, function (key, value) {
                var item = value.split('=');
                if (typeof (items[item[0]]) == 'undefined') {
                    result = result+value+"&";
                    items[item[0]] = value;
                }
            });

            result = result.substring(0, result.length-1);

            var additional = '';
            if (window.customPackingGrid !== false) {
                additional = '&zff_pkgbarcode=' + window.barcode;
            }

            jQuery.ajax({
                url: url,
                data: result + options.sendAdditionalParams()+additional,
                type: 'POST',

                beforeSend: function( e ) {
                    options.beforeSend( e );
                },
                success: function(data, xhr) {
                    hideLoader();
                    $obj.empty();
                    $obj.html(data);
                    initNavigation($obj);

                    options.success();
                    var $pendingGrid = $('#pendingOrdersGrid');
                    if (window.barcodeAction == 'add') {
                        $pendingGrid.find('.addBarcode').removeClass('hidden');
                    } else if (window.barcodeAction == 'subtract') {
                        $pendingGrid.find('.subtractBarcode').removeClass('hidden');
                    }

                    var $btn = $('#searchBox');
                    if (window.searchBox == true) {
                        $btn.find('span').text('On');
                    } else {
                        $btn.find('span').text('Off');
                    }

                    if (typeof (column) !== 'undefined' && typeof (row) !== 'undefined') {
                        openNextModal(column, row);
                    }

                    if (typeof (column) !== 'undefined' && column == 'PackageWtQty' && init != false) {
                        var pkgTd = $(document).find('tr[data-barcode="'+init+'"] td[data-column="PackageWtQty"]');
                        showEditModal(pkgTd, false);
                    }
                },

                error : function(e){ options.error( e );},
                complete : function(e) {
                    if (checkLogin(e) != false) {
                        options.complete(e);

                        hideLoader();
                        if (typeof (init) !== 'undefined' && init === true && $('#lazyLoadingOverlay:visible')) {
                            $('.progress-bar').css('width', '100%');
                            $('#lazyLoadingOverlay').fadeOut(800);
                            $('[data-toggle="tooltip"]').tooltip();
                        }
                    }
                },

                dataType: 'html'
            });

        }
        function initNavigation($obj){
            var _this = this;

            $(".btn-group.bootstrap-select.open").filter(function(){
                return $(this).parents('.modal').length === 0;
            }).remove();

            $('.selectpicker').selectpicker();
            $('[data-toggle="tooltip"]').tooltip();

            $obj.find('table th.sortable').on('click',function(e){
                $obj.find('input[name="zfTableColumn"]').val(jQuery(this).data('column'));
                $obj.find('input[name="zfTableOrder"]').val(jQuery(this).data('order'));
                ajax($obj);
            });
            $obj.find('.pagination').find('a').on('click',function(e){
                $obj.find('input[name="zfTablePage"]').val(jQuery(this).data('page'));
                e.preventDefault();
                ajax($obj);
            });
            $obj.find('.itemPerPage').on('change',function(e){
                $obj.find('input[name="zfTableItemPerPage"]').val(jQuery(this).val());
                ajax($obj);
            });
            $obj.find('input.filter').on('keypress',function(e){
               if(e.which === 13) {
                   e.preventDefault();
                   ajax($obj);
               }
            });
            $obj.find('select.filter').on('change',function(e){
                   e.preventDefault();
                   ajax($obj);
            });
            $obj.find('.quick-search').on('keypress',function(e){
               if(e.which === 13) {
                   e.preventDefault();
                   $obj.find('input[name="zfTableQuickSearch"]').val(jQuery(this).val());
                   ajax($obj);
               }
            });

            $obj.find('.export-csv').on('click',function(e){
                exportToCSV(jQuery(this), $obj);
            });

            if (options.tableDiv !== false) {
                var $date = $("#"+options.tableDiv+" input[name='zff_creationDate']");
                if (typeof ($date) !== 'undefined') {
                    $date.datepicker({
                        format: 'mm/dd/yyyy', todayHighlight: true, todayBtn: true, clearBtn: true, autoclose : true
                    }).on('changeDate', function(e) {
                        ajax($obj);
                    }).data('datepicker');
                }

                $date = $("#"+options.tableDiv+" input[name='zff_lastLoginDate']");
                if (typeof ($date) !== 'undefined') {
                    $date.datepicker({
                        format: 'mm/dd/yyyy', todayHighlight: true, todayBtn: true, clearBtn: true, autoclose : true
                    }).on('changeDate', function(e) {
                        ajax($obj);
                    }).data('datepicker');
                }

                $date = $("#"+options.tableDiv+" input[name='zff_DeliveryDate']");
                if (typeof ($date) !== 'undefined') {
                    $date.datepicker({
                        format: 'mm/dd/yyyy', todayHighlight: true, todayBtn: true, autoclose : true
                    }).on('changeDate', function(e) {
                        e.preventDefault();
                        showLoader();
                        $('.reset-filters-btn').trigger('click');
                    }).data('datepicker');
                }

                $date = $("#"+options.tableDiv+" input[name='zff_creationTime']");
                if (typeof ($date) !== 'undefined') {
                    $date.datepicker({
                        format: 'mm/dd/yyyy', todayHighlight: true, todayBtn: true, clearBtn: true, autoclose : true
                    }).on('changeDate', function(e) {
                        ajax($obj);
                    }).data('datepicker');
                }

                $date = $("#"+options.tableDiv+" input[name='zff_datetime']");
                if (typeof ($date) !== 'undefined') {
                    $date.datepicker({
                        format: 'mm/dd/yyyy', todayHighlight: true, todayBtn: true, clearBtn: true, autoclose : true
                    }).on('changeDate', function(e) {
                        ajax($obj);
                    }).data('datepicker');
                }
            }
        }

        function saveData(link, column, value, row, orderId, $obj, table2) {
            var column1 = column;
            var order_id = 0;
            if (typeof (orderId) !== "undefined" && orderId != false) {
                order_id = orderId;
            }

            var boxModal = $('#boxWarningModal');
            if (column1 == 'BoxNumber' && order_id == 'forceSave') {
                boxModal.modal('hide');
            }

            var weightModal = $('#weightWarningModal');
            if (column1 == 'fkItemStatusID' && order_id == 'forceSave') {
                weightModal.modal('hide');
            }

            jQuery.ajax({
                url:  link,
                data: {column: column , value : value , row: row, orderId: order_id},
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    hideLoader();
                    if (typeof (data.result) !== 'undefined' && data.result == 'box_warning' && column1 == 'BoxNumber') {
                        boxModal.modal('hide');
                        boxModal.find('input[name="boxNr"]').val(value);
                        boxModal.find('input[name="pkgId"]').val(row);
                        boxModal.modal('show');
                        return true;
                    }

                    if (typeof (data.result) !== 'undefined' && data.result == 'weight_warning' && column1 == 'fkItemStatusID') {
                        weightModal.modal('hide');
                        ajax($obj);
                        weightModal.find('div.message').html(data.error);
                        weightModal.find('input[name="statusId"]').val(value);
                        weightModal.find('input[name="orderItemId"]').val(row);
                        if (options.tableDiv !== false) {
                            weightModal.find('input[name="tableDiv"]').val(options.tableDiv);
                        }
                        weightModal.modal('show');
                        return true;
                    }

                    if (typeof (data.error) !== 'undefined') {
                        ajax($obj);
                        showNotification(data.error, 'danger');
                        return false;
                    } else if (typeof (data.pkg_error) !== 'undefined') {
                        ajax($obj);
                        showWarningModal('label-pkg', data.pkg_error);
                        return false;
                    }

                    if (column1 == 'fkItemStatusID') {
                        if (typeof(data.result) !== "undefined") {
                            if (data.result == 'ok' && typeof(data.barcode) !== 'undefined') {
                                window.barcode = data.barcode;
                            }
                        }

                        if (typeof(data.message) !== "undefined") {
                            showNotification(data.message, 'danger');
                        }

                        ajax($obj);
                        if (typeof(table2) !== "undefined") {
                            table2.refresh();
                        }
                    } else {
                        if (typeof(data.message) !== "undefined") {
                            if (column1 == 'BoxWt' && data.message.toLowerCase().indexOf("box number") >= 0) {
                                $('#editableModal').modal('hide');
                                showNotification(data.message, 'danger');
                                return false;
                            }

                            if ($('#editableModal').is(':visible')) {}
                            else {
                                var selectedRow = $('.firstTable .selected-tr [data-column="'+column1+'"]');
                                showEditModal(selectedRow, value);
                            }
                            $('#editableModal').find('.modal-body .form-group').addClass('has-error');
                            $('#editableModal').find('.modal-body .help-block').text(data.message);

                            return false;
                        }

                        if (typeof(data.result) !== "undefined") {
                            if (data.result == 'ok' && typeof(data.barcode) !== 'undefined') {
                                window.barcode = data.barcode;
                                $('#editableModal').modal('hide');
                            }

                            ajax($obj, false, column1, row);
                        }
                    }

                    return true;
                },
                complete: function () {
                    hideLoader();
                }
            });
        }

        function exportToCSV(link, $table){
            var data = new Array();
            $table.find("tr.zf-title , tr.zf-data-row").each(function(i,el){
                var row = new Array();
                $(this).find('th, td').each(function(j, el2){
                    row[j] = strip($(this).html());
                });
                data[i] = row;
            });

            var csvHeader= "data:application/csv;charset=utf-8,";
            var csvData = '';
            data.forEach(function(infoArray, index){
               dataString = infoArray.join(";");
               csvData += dataString + '\r\n';

            });
            link.attr({
                 'download': 'export-table.csv',
                 'href': csvHeader + encodeURIComponent(csvData),
                 'target': '_blank'
            });
        }

        init(this);

        return {
            refresh: function (resetPaging, pkgid, column1) {
                if (typeof (resetPaging) !== 'undefined' && resetPaging == true) {
                    table.find('input[name="zfTablePage"]').val(1);
                }
                ajax(table, pkgid, column1);
            },
            save: function (link1, column1, value1, row1, td1, table2) {
                showLoader();
                saveData(link1, column1, value1, row1, td1, table, table2);
            },
            exportToCSV: function (link) {
                exportToCSV(link, table);
            },
            divId: function() {
                return options.tableDiv;
            }
        };
    };

})(jQuery);

function openNextModal(column1, dataRow) {
    if (typeof(autoOpenModal) !== "undefined" && autoOpenModal == true) {
        var td = false;
        if (column1 == 'WholeWtQty' || column1 == 'FilletWt') {
            td = $(document).find('tr[data-row="'+dataRow+'"] td[data-column="Temp"]');
        } else if (column1 == 'Temp') {
            td = $(document).find('tr[data-row="'+dataRow+'"] td[data-column="VendorWtQty"]');
        } else if (column1 == 'VendorWtQty') {
            td = $(document).find('tr[data-row="'+dataRow+'"] td[data-column="VendorClaimedPrice"]');
        }

        if (td != false && td.length > 0) {
            setTimeout(function(){
                showEditModal(td, false);
            }, 1000);
        }
    }
}
