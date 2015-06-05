var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;




var checker_list;
var checked_dialog;
var staff_dialog;






var myCellEdit = function(callback, newValue) {
        var record = this.getRecord(),
            column = this.getColumn(),
            oldValue = this.value,
            datatable = this.getDataTable();
        recordIndex = datatable.getRecordIndex(record);
        var data = record.getData();
        var oldCounted = data['counted'];
        ar_file = 'ar_edit_porders.php';
        oldDamaged = data['damaged_quantity'];
        oldCounted = data['counted'];
        
        
        //            request = 'tipo=edit_' + column.object + '&key=' + Key + '&newvalue=' + new_qty + '&old_counted=' + encodeURIComponent(oldCounted) + '&old_quantity=' + encodeURIComponent(oldValue) + '&old_damaged_quantity=' + encodeURIComponent(oldDamaged) + '&potf_key=' + data['id'] + '&supplier_dn_key=' + Dom.get('supplier_deliver_note_key').value + '&sp_key=' + data["sp_key"]

        
        var request = 'tipo=edit_' + column.object + '&key=' + column.key + '&newvalue=' + encodeURIComponent(newValue) + '&old_counted=' + encodeURIComponent(oldCounted) + '&old_damaged_quantity=' + encodeURIComponent(oldDamaged) + '&old_quantity=' + encodeURIComponent(oldValue) + myBuildUrl(datatable, record) + '&potf_key=' + data['id'] + '&supplier_dn_key=' + Dom.get('supplier_deliver_note_key').value + '&sp_key=' + data["sp_key"]
        //  alert(ar_file+'?'+request);
        YAHOO.util.Connect.asyncRequest('POST', ar_file, {
            success: function(o) {
                alert(o.responseText);
                var r = YAHOO.lang.JSON.parse(o.responseText);
                if (r.state == 200) {


                    for (x in r.data) {
                        if (Dom.get(x) != undefined) Dom.get(x).innerHTML = r.data[x];
                    }

                    datatable.updateCell(record, 'counted', r.counted);
                    datatable.updateCell(record, 'damaged_quantity', r.damaged_quantity);

                    if (r.damaged_quantity != 0) {
                        datatable.updateCell(record, 'notes_damaged', '(-' + r.damaged_quantity + ')');

                    } else {
                        datatable.updateCell(record, 'notes_damaged', '');
                    }
                    if (column.key == 'damaged_quantity') callback(true, r.damaged_quantity);
                    else callback(true, r.quantity);


                } else {

                    alert(r.msg);
                    callback();
                }
            },
            failure: function(o) {
                alert(o.statusText);
                callback();



            },
            scope: this
        }, request

        );
    };




var myonCellClick = function(oArgs) {


        var target = oArgs.target,
            column = this.getColumn(target),
            record = this.getRecord(target);



        datatable = this;
        var records = this.getRecordSet();
        //alert(records.getLength())


        //alert(datatable)
        var recordIndex = this.getRecordIndex(record);

        switch (column.action) {

        case ('edit_object'):
        case ('add_object'):
        case ('remove_object'):
            var data = record.getData();

            if (column.action == 'add_object' && column.key == 'add') {
                var new_qty = parseFloat(data['received_quantity']) + 1;
                Key = 'quantity';
            } else if (column.action == 'add_object' && column.key == 'add_damaged') {
                var new_qty = parseFloat(data['damaged_quantity']) + 1;
                if (new_qty > data['received_quantity']) new_qty = data['received_quantity']
                Key = 'damaged_quantity';
            } else if (column.action == 'remove_object' && column.key == 'remove') {
                var new_qty = parseFloat(data['received_quantity']) - 1;
                Key = 'quantity';

            } else if (column.action == 'remove_object' && column.key == 'remove_damaged') {
                var new_qty = parseFloat(data['damaged_quantity']) - 1;
                if (new_qty < 0) new_qty = 0;
                Key = 'damaged_quantity';

            } else {
                Key = 'counted';
                var new_qty = 'Yes';
                if (data['counted'] == 'Yes') new_qty = 'No';
            }
            oldValue = data['received_quantity'];
            oldCounted = data['counted'];
            oldDamaged = data['damaged_quantity'];

            var ar_file = 'ar_edit_porders.php';

            request = 'tipo=edit_' + column.object + '&key=' + Key + '&newvalue=' + new_qty + '&old_counted=' + encodeURIComponent(oldCounted) + '&old_quantity=' + encodeURIComponent(oldValue) + '&old_damaged_quantity=' + encodeURIComponent(oldDamaged) + '&potf_key=' + data['id'] + '&supplier_dn_key=' + Dom.get('supplier_deliver_note_key').value + '&sp_key=' + data["sp_key"]
           
            YAHOO.util.Connect.asyncRequest('POST', ar_file, {
                success: function(o) {
                    //alert(o.responseText);
                    var r = YAHOO.lang.JSON.parse(o.responseText);
                    if (r.state == 200) {
                        for (x in r.data) {
                            if (Dom.get(x) != undefined) Dom.get(x).innerHTML = r.data[x];
                        }

                        datatable.updateCell(record, 'damaged_quantity', r.damaged_quantity);
                        datatable.updateCell(record, 'received_quantity', r.quantity);
                        datatable.updateCell(record, 'counted', r.counted);
                        if (r.damaged_quantity != 0) {
                            datatable.updateCell(record, 'notes_damaged', '(-' + r.damaged_quantity + ')');

                        } else {
                            datatable.updateCell(record, 'notes_damaged', '');
                        }

                    } else {
                        alert(r.msg);
                        //	callback();
                    }
                },
                failure: function(o) {
                    alert(o.statusText);
                    // callback();
                },
                scope: this
            }, request

            );

            break;


        default:

            this.onEventShowCellEditor(oArgs);
            break;
        }
    };









YAHOO.util.Event.addListener(window, "load", function() {

    session_data = YAHOO.lang.JSON.parse(base64_decode(Dom.get('session_data').value));
    labels = session_data.label;
    state = session_data.state;
    tables = new function() {

        var tableid = 0;
        var tableDivEL = "table" + tableid;
        var ColumnDefs = [{
            key: "id",
            label: labels.SPK,
            width: 20,
            sortable: false,
            isPrimaryKey: true,
            hidden: true
        }, {
            key: "sp_key",
            width: 20,
            sortable: false,
            hidden: true
        }

        , {
            key: "code",
            label: labels.Code,
            width: 60,
            sortable: true,
            className: "aleft",
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_ASC
            }
        }

        , {
            key: "description",
            label: labels.Description,
            width: 310,
            sortable: false,
            className: "aleft"
        }

        , {
            key: "dn_quantity",
            label: labels.DN_Qty,
            width: 70,
            sortable: false,
            className: "aright"
        }


        , {
            key: "received_quantity",
            label: labels.Rcvd_Qty,
            width: 60,
            sortable: false,
            className: "aright",
            editor: new YAHOO.widget.TextboxCellEditor({
                asyncSubmitter: myCellEdit
            }),
            object: 'inputted_supplier_dn',
            'action': 'change_received_qty'
        },

        {
            key: "add",
            label: "",
            width: 3,
            sortable: false,
            action: 'add_object',
            object: 'inputted_supplier_dn'
        }, {
            key: "remove",
            label: "",
            width: 3,
            sortable: false,
            action: 'remove_object',
            object: 'inputted_supplier_dn'
        }, {
            key: "add_damaged",
            label: "",
            width: 3,
            sortable: false,
            action: 'add_object',
            object: 'inputted_supplier_dn',
            hidden: true
        }, {
            key: "remove_damaged",
            label: "",
            width: 3,
            sortable: false,
            action: 'remove_object',
            object: 'inputted_supplier_dn',
            hidden: true
        }

        , {
            key: "counted",
            label: labels.Ckd,
            width: 50,
            sortable: false,
            className: 'aright',
            action: 'edit_object',
            object: 'inputted_supplier_dn'
        }, {
            key: "damaged_quantity",
            label: labels.Dmgd_Qty,
            width: 60,
            sortable: false,
            className: "aright",
            editor: new YAHOO.widget.TextboxCellEditor({
                asyncSubmitter: myCellEdit
            }),
            object: 'inputted_supplier_dn',
            'action': 'change_received_qty',
            hidden: false
        }, {
            key: "notes_damaged",
            label: "",
            width: 15,
            sortable: false
        }, {
            key: "notes_received",
            label: "",
            width: 15,
            sortable: false,
            hidden: true
        }



        ];

        request = "ar_edit_porders.php?tipo=dn_transactions_to_count&sf=0&tableid=" + tableid + '&supplier_dn_key=' + Dom.get('supplier_deliver_note_key').value
        //alert(request)
        this.dataSource0 = new YAHOO.util.DataSource(request);

        this.dataSource0.responseType = YAHOO.util.DataSource.TYPE_JSON;
        this.dataSource0.connXhrMode = "queueRequests";
        this.dataSource0.responseSchema = {
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

            fields: ["id", "code", "description", "quantity", "amount", "unit_type", "add", "remove", "used_in", "dn_quantity", "dn_unit_type", "received_quantity", "damaged_quantity", "counted", "add_damaged", 'notes_damaged', 'remove_damaged', 'sp_key']
        };

        this.table0 = new YAHOO.widget.DataTable(tableDivEL, ColumnDefs, this.dataSource0, {
            //draggableColumns:true,
            renderLoopSize: 50,
            generateRequest: myRequestBuilder,
            paginator: new YAHOO.widget.Paginator({
                rowsPerPage: state.supplier_dn.products.nr,
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
                key: state.supplier_dn.products.order,
                dir: state.supplier_dn.products.order_dir
            },
            dynamicData: true

        });
        this.table0.handleDataReturnPayload = myhandleDataReturnPayload;
        this.table0.doBeforeSortColumn = mydoBeforeSortColumn;
        this.table0.doBeforePaginatorChange = mydoBeforePaginatorChange;
        this.table0.subscribe("cellMouseoverEvent", highlightEditableCell);
        this.table0.subscribe("cellMouseoutEvent", unhighlightEditableCell);
        this.table0.subscribe("cellClickEvent", myonCellClick);





        this.table0.filter = {
            key: state.supplier_dn.products.f_field,
            value: state.supplier_dn.products.f_value
        };

    }
});


var select_staff = function(o, e) {
        var staff_id = o.getAttribute('staff_id');
        var staff_name = o.innerHTML;
        o.className = 'selected';

        Dom.get('checked_by').value = staff_id;
        Dom.get('checked_by_alias').innerHTML = staff_name;
        close_dialog('staff');
    };



var checked_order_save = function(o) {
        var staff_key = Dom.get('checked_by').value;
        var request = 'ar_edit_porders.php?tipo=set_dn_as_checked&id=' + escape(Dom.get('dn_key').value) + '&staff_key=' + escape(staff_key);
        // alert(request)
        YAHOO.util.Connect.asyncRequest('POST', request, {

            success: function(o) {
                alert(o.responseText);

                var r = YAHOO.lang.JSON.parse(o.responseText);
                if (r.state == 200) {

                    //	location.href='supplier_dn.php?id='+Dom.get('dn_key').value;


                } else alert(r.msg);
            }
        });
    };


function take_values_from_dn() {

    var ar_file = 'ar_edit_porders.php';
    request = 'tipo=take_values_from_dn&dn_key=' + Dom.get('dn_key').value;
    //	alert(ar_file+'?'+request)
    YAHOO.util.Connect.asyncRequest('POST', ar_file, {
        success: function(o) {
            //	  alert(o.responseText);
            var r = YAHOO.lang.JSON.parse(o.responseText);
            if (r.state == 200) {
                var tableid = 0;
                var table = tables['table' + tableid];

                var datasource = tables['dataSource' + tableid];
                table.filter.value = Dom.get('f_input' + tableid).value;
                var request = '&show_all=no';
                datasource.sendRequest(request, table.onDataReturnInitializeTable, table);

                //	callback(true, r.newvalue);
            } else {
                alert(r.msg);
                //	callback();
            }
        },
        failure: function(o) {
            alert(o.statusText);
            // callback();
        },
        scope: this
    }, request

    );


}

function set_received() {
    Dom.get('take_values_from_dn').style.visibility = 'visible';
    Dom.get('set_damages_bis').style.display = '';
    Dom.get('set_received').style.display = 'none';
    var table = tables['table0'];

    table.showColumn('add');
    table.showColumn('remove');
    table.showColumn('dn_quantity');
    table.hideColumn('add_damaged');
    table.hideColumn('damaged_quantity');
    table.hideColumn('remove_damaged');
    table.showColumn('notes_damaged');
    table.hideColumn('notes_received');

}

function set_damages() {
    Dom.get('take_values_from_dn').style.visibility = 'hidden';
    Dom.get('set_damages_bis').style.display = 'none';
    Dom.get('set_received').style.display = '';


    var table = tables['table0'];
    table.hideColumn('add');
    table.hideColumn('remove');
    table.hideColumn('dn_quantity');

    table.showColumn('remove_damaged');

    table.showColumn('add_damaged');
    table.showColumn('damaged_quantity');
    table.hideColumn('notes_damaged');
    table.showColumn('notes_received');
}

function init() {

    Event.addListener("take_values_from_dn", "click", take_values_from_dn);

    checked_dialog = new YAHOO.widget.Dialog("checked_dialog", {
        context: ["make_dn_as_checked", "tr", "tl"],
        visible: false,
        close: true,
        underlay: "none",
        draggable: false
    });
    checked_dialog.render();

    staff_dialog = new YAHOO.widget.Dialog("staff_dialog", {
        context: ["get_checker", "tr", "tl"],
        visible: false,
        close: false,
        underlay: "none",
        draggable: false
    });
    staff_dialog.render();

    Event.addListener("make_dn_as_checked", "click", checked_dialog.show, checked_dialog, true);
    Event.addListener("get_checker", "click", staff_dialog.show, staff_dialog, true);

    ids = ['set_damages', 'set_damages_bis'];
    YAHOO.util.Event.addListener(ids, "click", set_damages);
    YAHOO.util.Event.addListener('set_received', "click", set_received);
}



YAHOO.util.Event.onDOMReady(init);

YAHOO.util.Event.onContentReady("filtermenu0", function() {
    var oMenu = new YAHOO.widget.ContextMenu("filtermenu0", {
        trigger: "filter_name0"
    });
    oMenu.render();
    oMenu.subscribe("show", oMenu.focus);

});


YAHOO.util.Event.onContentReady("rppmenu0", function() {
    var oMenu = new YAHOO.widget.ContextMenu("rppmenu0", {
        trigger: "rtext_rpp0"
    });
    oMenu.render();
    oMenu.subscribe("show", oMenu.focus);

});
