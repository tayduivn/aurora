var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;
var dialog_cancel, dialog_edit_shipping;
var change_staff_discount;
YAHOO.namespace("invoice");

var edit_delivery_address;



function showdetails(o) {



    var history_id = o.getAttribute('hid');
    var details = o.getAttribute('d');
    tr = Dom.getAncestorByTagName(o, 'tr');
    row_index = tr.rowIndex + 1;
    var table = Dom.getAncestorByTagName(o, 'table');
    //alert(o);
    if (details == 'no') {
        row_class = tr.getAttribute('class');

        var request = "ar_history.php?tipo=history_details&id=" + history_id;
        //alert(request)	
        YAHOO.util.Connect.asyncRequest('POST', request, {
            success: function(o) {
                var r = YAHOO.lang.JSON.parse(o.responseText);
                if (r.state == 200) {
                    var x = table.insertRow(row_index);
                    x.setAttribute('class', row_class);
                    x.setAttribute('id', 'chd' + history_id);

                    var c1 = x.insertCell(0);
                    var c2 = x.insertCell(1);
                    var c3 = x.insertCell(2);
                    var c4 = x.insertCell(3);
                    x.setAttribute('style', 'padding:10px 0 ;border-top:none')
                    c1.innerHTML = "";
                    c2.innerHTML = "";
                    c3.setAttribute('style', 'padding:10px;');
                    c4.innerHTML = "";

                    c3.setAttribute('colspan', 3);
                    c3.innerHTML = r.details;
                    Dom.get('ch' + history_id).src = 'art/icons/showed.png';
                    Dom.get('ch' + history_id).setAttribute('d', 'yes');


                }

            }
        });
    } else {
        Dom.get('ch' + history_id).src = 'art/icons/closed.png';
        Dom.get('ch' + history_id).setAttribute('d', 'no');
        table.deleteRow(row_index);

    }


}




YAHOO.util.Event.addListener(window, "load", function() {
    session_data = YAHOO.lang.JSON.parse(base64_decode(Dom.get('session_data').value));
    labels = session_data.label;
    state = session_data.state;

    tables = new function() {

        var tableid = 0;
        var tableDivEL = "table" + tableid;

        var formater_qty = function(el, oRecord, oColumn, oData) {
                el.innerHTML = oRecord.getData("quantity");
            };

        var myRowFormatter = function(elTr, oRecord) {
                if (oRecord.getData('class') == 'first') {

                    Dom.addClass(elTr, 'first');
                } else if (oRecord.getData('class') == 'out_of_stock') {

                    Dom.addClass(elTr, 'out_of_stock');
                }
                return true;
            };

        var InvoiceColumnDefs = [{
            key: "pid",
            width: 20,
            sortable: false,
            isPrimaryKey: true,
            hidden: true
        }, {
            key: "pkey",
            width: 20,
            sortable: false,
            isPrimaryKey: true,
            hidden: true
        }, {
            key: "code",
            label: labels.Code,
            width: 80,
            sortable: false,
            className: "aleft",
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_ASC
            }
        }, {
            key: "description",
            label: labels.Description,
            width: 440,
            sortable: false,
            className: "aleft",
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_ASC
            }
        }, {
            key: "ordered_quantity",
            label: labels.Qty,
            formatter: formater_qty,
            width: 80,
            sortable: false,
            className: "aright",
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_DESC
            },
            editor: new YAHOO.widget.TextboxCellEditor({
                asyncSubmitter: CellEdit
            }),
            object: 'new_order'
        }, {
            key: "quantity",
            label: "",
            hidden: true,
            width: 80,
            sortable: false
        }, {
            key: "add",
            label: "",
            width: 5,
            sortable: false,
            action: 'add_object',
            object: 'new_order'
        }, {
            key: "remove",
            label: "",
            width: 5,
            sortable: false,
            action: 'remove_object',
            object: 'new_order'
        }, {
            key: "to_charge",
            label: labels.Net,
            width: 75,
            sortable: false,
            className: "aright",
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_DESC
            },
            action: 'edit_object',
            object: 'transaction_discount_percentage'
        }, {
            key: "otf_key",
            label: "",
            hidden: true,
            width: 1
        }, {
            key: "discount_percentage",
            label: "",
            hidden: true,
            width: 1
        }];

        request = "ar_edit_orders.php?tipo=transactions_to_process&tid=0&sf=0&f_value=&display=items&order_key=" + Dom.get('order_key').value + "&store_key=" + Dom.get('store_key').value;
       // alert(request)
        this.dataSource0 = new YAHOO.util.DataSource(request);
        this.dataSource0.responseType = YAHOO.util.DataSource.TYPE_JSON;
        this.dataSource0.connXhrMode = "queueRequests";
        this.dataSource0.responseSchema = {
            resultsList: "resultset.data",
            metaFields: {
                rtext: "resultset.rtext",
                rtext_rpp: "resultset.rtext_rpp",
                rowsPerPage: "resultset.records_perpage",
                sort_key: "resultset.sort_key",
                sort_dir: "resultset.sort_dir",
                tableid: "resultset.tableid",
                filter_msg: "resultset.filter_msg",
                totalRecords: "resultset.total_records"
            },
            fields: ["code", "pkey", "description", 'ordered_quantity', "quantity", "discount", "to_charge", "gross", "tariff_code", "stock", "add", "remove", "pid", 'dispatching_status', 'otf_key', 'discount_percentage', 'tax','class']
        };
        this.table0 = new YAHOO.widget.DataTable(tableDivEL, InvoiceColumnDefs, this.dataSource0, {
            formatRow: myRowFormatter,
            renderLoopSize: 50,
            generateRequest: myRequestBuilder,
            paginator: new YAHOO.widget.Paginator({
                rowsPerPage: state.order.items.nr,
                containers: 'paginator0',
                pageReportTemplate: '(' + labels.Page + ' {currentPage} ' + labels.of + ' {totalPages})',
                previousPageLinkLabel: "<",
                nextPageLinkLabel: ">",
                firstPageLinkLabel: "<<",
                lastPageLinkLabel: ">>",
                rowsPerPageOptions: [10, 25, 50, 100, 250, 500],
                alwaysVisible: false,
                template: "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info0'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
            })

            ,
            sortedBy: {
                key: state.order.items.order,
                dir: state.order.items.order_dir

            },
            dynamicData: true
        }

        );


        this.table0.handleDataReturnPayload = myhandleDataReturnPayload;
        this.table0.doBeforeSortColumn = mydoBeforeSortColumn;
        this.table0.doBeforePaginator = mydoBeforePaginatorChange;
        this.table0.subscribe("cellMouseoverEvent", highlightEditableCell);
        this.table0.subscribe("cellMouseoutEvent", unhighlightEditableCell);
        this.table0.subscribe("cellClickEvent", myonCellClick);
        this.table0.table_id = tableid;
        this.table0.subscribe("renderEvent", myrenderEvent);
        this.table0.filter = {
            key: state.order.items.f_field,
            value: ''
        };





        var tableid = 1;
        var tableDivEL = "table" + tableid;

        var InvoiceColumnDefs = [{
            key: "pid",
            label: "Product ID",
            width: 20,
            sortable: false,
            isPrimaryKey: true,
            hidden: true
        }, {
            key: "pkey",
            label: "Product KEY",
            width: 20,
            sortable: false,
            isPrimaryKey: true,
            hidden: true
        }, {
            key: "code",
            label: labels.Code,
            width: 80,
            sortable: false,
            className: "aleft",
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_ASC
            }
        }, {
            key: "description",
            label: labels.Description,
            width: 480,
            sortable: false,
            className: "aleft",
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_ASC
            }
        }, {
            key: "quantity",
            label: labels.Qty,
            width: 40,
            sortable: false,
            className: "aright",
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_DESC
            },
            editor: new YAHOO.widget.TextboxCellEditor({
                asyncSubmitter: CellEdit
            }),
            object: 'new_order'
        }, {
            key: "add",
            label: "",
            width: 5,
            sortable: false,
            action: 'add_object',
            object: 'new_order'
        }, {
            key: "remove",
            label: "",
            width: 5,
            sortable: false,
            action: 'remove_object',
            object: 'new_order'
        }, {
            key: "to_charge",
            label: labels.Net,
            width: 75,
            sortable: false,
            className: "aright",
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_DESC
            },
            action: 'edit_object',
            object: 'transaction_discount_percentage'
        }, {
            key: "otf_key",
            label: "",
            hidden: true,
            width: 1
        }, {
            key: "discount_percentage",
            label: "",
            hidden: true,
            width: 1
        }];

        request = "ar_edit_orders.php?tipo=transactions_to_process&tid=0&sf=0&f_value=&display=products&order_key=" + Dom.get('order_key').value + "&store_key=" + Dom.get('store_key').value + '&tableid=' + tableid + '&lookup_family=' + Dom.get('lookup_family').value
        //alert(request)
        this.dataSource1 = new YAHOO.util.DataSource(request);
        this.dataSource1.responseType = YAHOO.util.DataSource.TYPE_JSON;
        this.dataSource1.connXhrMode = "queueRequests";
        this.dataSource1.responseSchema = {
            resultsList: "resultset.data",
            metaFields: {
                rtext: "resultset.rtext",
                rtext_rpp: "resultset.rtext_rpp",
                rowsPerPage: "resultset.records_perpage",
                sort_key: "resultset.sort_key",
                sort_dir: "resultset.sort_dir",
                tableid: "resultset.tableid",
                filter_msg: "resultset.filter_msg",
                totalRecords: "resultset.total_records"
            },
            fields: ["code", "pkey", "description", "quantity", "discount", "to_charge", "gross", "tariff_code", "stock", "add", "remove", "pid", 'dispatching_status', 'otf_key', 'discount_percentage', 'tax','class']
        };

        this.table1 = new YAHOO.widget.DataTable(tableDivEL, InvoiceColumnDefs, this.dataSource1, {
         formatRow: myRowFormatter,
            renderLoopSize: 50,
            generateRequest: myRequestBuilder,
            paginator: new YAHOO.widget.Paginator({
             formatRow: myRowFormatter,
                rowsPerPage: state.order.products.nr,
                containers: 'paginator1',
                pageReportTemplate: '(' + labels.Page + ' {currentPage} ' + labels.of + ' {totalPages})',
                previousPageLinkLabel: "<",
                nextPageLinkLabel: ">",
                firstPageLinkLabel: "<<",
                lastPageLinkLabel: ">>",
                rowsPerPageOptions: [10, 25, 50, 100, 250, 500],
                alwaysVisible: false,
                template: "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info1'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
            })

            ,
            sortedBy: {
                key: state.order.products.order,
                dir: state.order.products.order_dir
            },
            dynamicData: true
        }

        );


        this.table1.handleDataReturnPayload = myhandleDataReturnPayload;
        this.table1.doBeforeSortColumn = mydoBeforeSortColumn;
        this.table1.doBeforePaginator = mydoBeforePaginatorChange;
        this.table1.subscribe("cellMouseoverEvent", highlightEditableCell);
        this.table1.subscribe("cellMouseoutEvent", unhighlightEditableCell);
        this.table1.subscribe("cellClickEvent", myonCellClick);
        this.table1.table_id = tableid;
        this.table1.subscribe("renderEvent", myrenderEvent);
        this.table1.filter = {
            key: state.order.products.f_field,
            value: ''
        };





        var tableid = 2;
        var tableDivEL = "table" + tableid;


        var myRowFormatter = function(elTr, oRecord) {
                if (oRecord.getData('type') == 'Orders') {
                    Dom.addClass(elTr, 'customer_history_orders');
                } else if (oRecord.getData('type') == 'Notes') {
                    Dom.addClass(elTr, 'customer_history_notes');
                } else if (oRecord.getData('type') == 'Changes') {
                    Dom.addClass(elTr, 'customer_history_changes');
                }
                return true;
            };


        this.prepare_note = function(elLiner, oRecord, oColumn, oData) {

            if (oRecord.getData("strikethrough") == "Yes") {
                Dom.setStyle(elLiner, 'text-decoration', 'line-through');
                Dom.setStyle(elLiner, 'color', '#777');

            }
            elLiner.innerHTML = oData
        };

        var ColumnDefs = [{
            key: "key",
            label: "",
            width: 20,
            sortable: false,
            isPrimaryKey: true,
            hidden: true
        }, {
            key: "type",
            label: "",
            width: 0,
            sortable: false,
            hidden: true
        }, {
            key: "date",
            label: labels.Date,
            className: "aright",
            width: 120,
            sortable: true,
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_DESC
            }
        }, {
            key: "time",
            label: labels.Time,
            className: "aleft",
            width: 70
        }, {
            key: "handle",
            label: labels.Author,
            className: "aleft",
            width: 100,
            sortable: true,
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_ASC
            }
        }, {
            key: "note",
            formatter: this.prepare_note,
            label: labels.Notes,
            className: "aleft",
            width: 420
        }

        ];
        request = "ar_history.php?tipo=customer_history&parent=order_customer&parent_key=" + Dom.get('customer_key').value + "&sf=0&tableid=" + tableid

        this.dataSource2 = new YAHOO.util.DataSource(request);
        this.dataSource2.responseType = YAHOO.util.DataSource.TYPE_JSON;
        this.dataSource2.connXhrMode = "queueRequests";
        this.dataSource2.responseSchema = {
            resultsList: "resultset.data",
            metaFields: {
                rowsPerPage: "resultset.records_perpage",
                rtext: "resultset.rtext",
                rtext_rpp: "resultset.rtext_rpp",
                sort_key: "resultset.sort_key",
                sort_dir: "resultset.sort_dir",
                tableid: "resultset.tableid",
                filter_msg: "resultset.filter_msg",
                totalRecords: "resultset.total_records"
            },
            fields: ["note", "date", "time", "handle", "delete", "can_delete", "delete_type", "key", "edit", "type", "strikethrough"]
        };

        this.table2 = new YAHOO.widget.DataTable(tableDivEL, ColumnDefs, this.dataSource2, {
            formatRow: myRowFormatter,
            renderLoopSize: 5,
            generateRequest: myRequestBuilder,
            paginator: new YAHOO.widget.Paginator({
                rowsPerPage: state.order.customer_history.nr,

                containers: 'paginator2',
                pageReportTemplate: '(' + labels.Page + ' {currentPage} ' + labels.of + ' {totalPages})',
                alwaysVisible: false,
                previousPageLinkLabel: "<",
                nextPageLinkLabel: ">",
                firstPageLinkLabel: "<<",
                lastPageLinkLabel: ">>",
                rowsPerPageOptions: [10, 25, 50, 100, 250, 500],
                template: "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info0'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"



            })

            ,
            sortedBy: {
                key: state.order.customer_history.order,
                dir: state.order.customer_history.order_dir
            },
            dynamicData: true

        }

        );
        this.table2.handleDataReturnPayload = myhandleDataReturnPayload;
        this.table2.doBeforeSortColumn = mydoBeforeSortColumn;
        this.table2.doBeforePaginatorChange = mydoBeforePaginatorChange;
        this.table2.filter = {
            key: state.order.customer_history.f_field,
            value: state.order.customer_history.f_value
        };

        this.table2.subscribe("cellMouseoverEvent", highlightEditableCell);
        this.table2.subscribe("cellMouseoutEvent", unhighlightEditableCell);



        this.table2.subscribe("cellClickEvent", onCellClick);
        this.table2.table_id = tableid;
        this.table2.subscribe("renderEvent", myrenderEvent);


        this.table2.getDataSource().sendRequest(null, {
            success: function(request, response, payload) {



                if (response.results.length == 0) {
                    //alert("caca")
                    get_history_numbers();

                } else {
                    // this.onDataReturnInitializeTable(request, response, payload);
                }
            },
            scope: this.table2,
            argument: this.table2.getState()
        });





        var tableid = 3;
        var tableDivEL = "table" + tableid;


        var myRowFormatter = function(elTr, oRecord) {
                if (oRecord.getData('type') == 'Orders') {
                    Dom.addClass(elTr, 'store_history_orders');
                } else if (oRecord.getData('type') == 'Notes') {
                    Dom.addClass(elTr, 'store_history_notes');
                } else if (oRecord.getData('type') == 'Changes') {
                    Dom.addClass(elTr, 'store_history_changes');
                }
                return true;
            };

        this.prepare_note = function(elLiner, oRecord, oColumn, oData) {

            if (oRecord.getData("strikethrough") == "Yes") {
                Dom.setStyle(elLiner, 'text-decoration', 'line-through');
                Dom.setStyle(elLiner, 'color', '#777');

            }
            elLiner.innerHTML = oData
        };

        var ColumnDefs = [{
            key: "key",
            label: "",
            width: 20,
            sortable: false,
            isPrimaryKey: true,
            hidden: true
        }, {
            key: "date",
            label: labels.Date,
            className: "aright",
            width: 120,
            sortable: true,
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_DESC
            }
        }, {
            key: "time",
            label: labels.Time,
            className: "aleft",
            width: 70
        }, {
            key: "handle",
            label: labels.Author,
            className: "aleft",
            width: 120,
            sortable: true,
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_ASC
            }
        }, {
            key: "note",
            formatter: this.prepare_note,
            label: labels.Notes,
            className: "aleft",
            width: 420
        }, {
            key: "delete",
            label: "",
            width: 12,
            sortable: false,
            action: 'dialog',
            object: 'delete_note'
        }, {
            key: "edit",
            label: "",
            width: 12,
            sortable: false,
            action: 'edit',
            object: 'supplier_product_history'
        }

        ];
        request = "ar_history.php?tipo=order_history&parent=order&parent_key=" + Dom.get('order_key').value + "&sf=0&tableid=" + tableid

        this.dataSource3 = new YAHOO.util.DataSource(request);
        this.dataSource3.responseType = YAHOO.util.DataSource.TYPE_JSON;
        this.dataSource3.connXhrMode = "queueRequests";
        this.dataSource3.responseSchema = {
            resultsList: "resultset.data",
            metaFields: {
                rowsPerPage: "resultset.records_perpage",
                rtext: "resultset.rtext",
                rtext_rpp: "resultset.rtext_rpp",
                sort_key: "resultset.sort_key",
                sort_dir: "resultset.sort_dir",
                tableid: "resultset.tableid",
                filter_msg: "resultset.filter_msg",
                totalRecords: "resultset.total_records"
            },
            fields: ["note", "date", "time", "handle", "delete", "can_delete", "delete_type", "key", "edit", "type", "strikethrough"]
        };
        this.table3 = new YAHOO.widget.DataTable(tableDivEL, ColumnDefs, this.dataSource3, {
            formatRow: myRowFormatter,
            renderLoopSize: 5,
            generateRequest: myRequestBuilder,
            paginator: new YAHOO.widget.Paginator({
                rowsPerPage: state.order.history.nr,
                containers: 'paginator3',
                pageReportTemplate: '(' + labels.Page + ' {currentPage} ' + labels.of + ' {totalPages})',
                alwaysVisible: false,
                previousPageLinkLabel: "<",
                nextPageLinkLabel: ">",
                firstPageLinkLabel: "<<",
                lastPageLinkLabel: ">>",
                rowsPerPageOptions: [10, 25, 50, 100, 250, 500],
                template: "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info3'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"



            })

            ,
            sortedBy: {
                key: state.order.history.order,
                dir: state.order.history.order_dir
            },
            dynamicData: true

        }

        );

        this.table3.handleDataReturnPayload = myhandleDataReturnPayload;
        this.table3.doBeforeSortColumn = mydoBeforeSortColumn;
        this.table3.doBeforePaginatorChange = mydoBeforePaginatorChange;

        this.table3.filter = {
            key: state.order.history.f_field,
            value: state.order.history.f_value
        };
        this.table3.subscribe("cellMouseoverEvent", highlightEditableCell);
        this.table3.subscribe("cellMouseoutEvent", unhighlightEditableCell);
        this.table3.subscribe("cellClickEvent", onNotesCellClick);
        this.table3.table_id = tableid;
        this.table3.subscribe("renderEvent", myrenderEvent);



    };
});



function init() {



    init_search('orders_store');


    Event.addListener('clean_table_filter_show0', "click", show_filter, 0);
    Event.addListener('clean_table_filter_hide0', "click", hide_filter, 0);


    Event.addListener('clean_table_filter_show1', "click", show_filter, 1);
    Event.addListener('clean_table_filter_hide1', "click", hide_filter, 1);



    var oACDS = new YAHOO.util.FunctionDataSource(mygetTerms);
    oACDS.queryMatchContains = true;
    oACDS.table_id = 0;
    var oAutoComp = new YAHOO.widget.AutoComplete("f_input0", "f_container0", oACDS);
    oAutoComp.minQueryLength = 0;

    var oACDS1 = new YAHOO.util.FunctionDataSource(mygetTerms);
    oACDS1.queryMatchContains = true;
    oACDS1.table_id = 1;
    var oAutoComp1 = new YAHOO.widget.AutoComplete("f_input1", "f_container1", oACDS1);
    oAutoComp1.minQueryLength = 0;



    // YAHOO.util.Event.addListener("done", "click", create_delivery_note);
    YAHOO.util.Event.addListener("done", "click", open_send_to_warehouse_dialog);



    myTooltip = new YAHOO.widget.Tooltip("order_paid_info_Tooltip", {
        context: "order_paid_info",

        showDelay: 500
    });


}


YAHOO.util.Event.onDOMReady(init);

YAHOO.util.Event.onContentReady("rppmenu0", function() {
    var oMenu = new YAHOO.widget.ContextMenu("rppmenu0", {
        trigger: "rtext_rpp0"
    });
    oMenu.render();
    oMenu.subscribe("show", oMenu.focus);
});

YAHOO.util.Event.onContentReady("filtermenu0", function() {
    var oMenu = new YAHOO.widget.ContextMenu("filtermenu0", {
        trigger: "filter_name0"
    });
    oMenu.render();
    oMenu.subscribe("show", oMenu.focus);

});

YAHOO.util.Event.onContentReady("rppmenu1", function() {
    var oMenu = new YAHOO.widget.ContextMenu("rppmenu1", {
        trigger: "rtext_rpp1"
    });
    oMenu.render();
    oMenu.subscribe("show", oMenu.focus);
});

YAHOO.util.Event.onContentReady("filtermenu1", function() {
    var oMenu = new YAHOO.widget.ContextMenu("filtermenu1", {
        trigger: "filter_name1"
    });
    oMenu.render();
    oMenu.subscribe("show", oMenu.focus);

});
