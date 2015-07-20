var Dom = YAHOO.util.Dom;

var validate_scope_data;
var validate_scope_metadata;


function change_block(e) {

    var ids = ["description", "state", "allowances"];
    var block_ids = ["d_description", "d_state", "d_allowances"];

    Dom.setStyle(block_ids, 'display', 'none');
    Dom.setStyle('d_' + this.id, 'display', '');

    Dom.removeClass(ids, 'selected');
    Dom.addClass(this, 'selected');

    YAHOO.util.Connect.asyncRequest('POST', 'ar_sessions.php?tipo=update&keys=deal-edit_block_view&value=' + this.id, {});


}



YAHOO.util.Event.addListener(window, "load", function() {
    tables = new function() {






    };
});





YAHOO.util.Event.addListener(window, "load", function() {
    session_data = YAHOO.lang.JSON.parse(base64_decode(Dom.get('session_data').value));
    labels = session_data.label;
    state = session_data.state;


    tables = new function() {


        var tableid = 2; // Change if you have more the 1 table
        var tableDivEL = "table" + tableid;
        var productsColumnDefs = [

        {
            key: "deal_component_key",
            label: "",
            width: 20,
            sortable: false,
            isPrimaryKey: true,
            hidden: true
        }, {
            key: "status",
            label: "",
            width: 10,
            sortable: false
        },
/*
        {
            key: "name",
            label: labels.Name,
            width: 150,
            sortable: true,
            className: "aleft",
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_ASC
            }
        },
*/
        {
            key: "allowances",
            label: labels.Allowance,
            width: 150,
            sortable: true,
            className: "aright",
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_ASC
            }
        },

        {
            key: "label",
            label: labels.Label,
            width: 150,
            sortable: true,
            className: "aright",
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_ASC
            },
            sortOptions: {
                defaultDir: YAHOO.widget.DataTable.CLASS_ASC
            },
            editor: new YAHOO.widget.TextboxCellEditor({
                asyncSubmitter: CellEdit
            }),
            object: 'deal_component_field'
        }, {
            key: "edit_status",
            label: labels.Status,
            width: 150,
            className: "aright",
            sortable: false
        }, {
            key: "edit_dates",
            className: "aright",
            label: labels.Finish,
            width: 150,
            sortable: false
        }

        ];

        request = "ar_edit_deals.php?tipo=deal_components&parent=deal&parent_key=" + Dom.get('deal_key').value + '&tableid=' + tableid
        // alert(request)
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

            fields: ["name", "deal_component_key", "allowances", "duration", "orders", "code", "customers", "target", "terms", "edit_status", "status", "edit_dates", "label"]
        };


        this.table2 = new YAHOO.widget.DataTable(tableDivEL, productsColumnDefs, this.dataSource2, {
            renderLoopSize: 50,
            generateRequest: myRequestBuilder
            //,initialLoad:false
            ,
            paginator: new YAHOO.widget.Paginator({
                rowsPerPage: state.deal.edit_components.nr,
                containers: 'paginator2',
                pageReportTemplate: '(' + labels.Page + ' {currentPage} ' + labels.of + ' {totalPages})',
                previousPageLinkLabel: "<",
                nextPageLinkLabel: ">",
                firstPageLinkLabel: "<<",
                lastPageLinkLabel: ">>",
                rowsPerPageOptions: [10, 25, 50, 100, 250, 500],
                template: "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info2'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"



            })

            ,
            sortedBy: {
                key: state.deal.edit_components.order,
                dir: state.deal.edit_components.order_dir
            },
            dynamicData: true

        }

        );



        this.table2.handleDataReturnPayload = myhandleDataReturnPayload;
        this.table2.doBeforeSortColumn = mydoBeforeSortColumn;
        this.table2.doBeforePaginatorChange = mydoBeforePaginatorChange;
        this.table2.request = request;
        this.table2.table_id = tableid;
        this.table2.subscribe("renderEvent", myrenderEvent);
        this.table2.getDataSource().sendRequest(null, {
            success: function(request, response, payload) {
                if (response.results.length == 0) {
                    get_deal_component_elements_numbers()
                } else {
                    this.onDataReturnInitializeTable(request, response, payload);
                }
            },
            scope: this.table2,
            argument: this.table2.getState()
        });


        this.table2.subscribe("cellMouseoverEvent", highlightEditableCell);
        this.table2.subscribe("cellMouseoutEvent", unhighlightEditableCell);
        this.table2.subscribe("cellClickEvent", onCellClick);


        this.table2.filter = {
            key: state.deal.edit_components.f_field,
            value: state.deal.edit_components.f_value
        };


    };
});







function validate_deal_terms_label(query) {

    validate_general('deal_description', 'terms_label', unescape(query));
}

function validate_deal_name(query) {
    validate_general('deal_description', 'name', unescape(query));
}

function validate_deal_description(query) {

    validate_general('deal_description', 'description', unescape(query));
}

function date_changed() {

    if (this.id == 'v_calpop1') {

        validate_general('deal_dates', 'deal_from', this.value);
    } else if (this.id == 'v_calpop2') {
        validate_general('deal_dates', 'deal_to', this.value);

    }
}

function handleSelect(type, args, obj) {

    var dates = args[0];
    var date = dates[0];
    var year = date[0],
        month = date[1],
        day = date[2];


    if (month < 10) month = '0' + month;
    if (day < 10) day = '0' + day;
    var txtDate1 = document.getElementById("v_calpop" + this.id);
    txtDate1.value = day + "-" + month + "-" + year;
    this.hide();

    if (this.id == 1) {

        validate_general('deal_dates', 'deal_from', txtDate1.value);



    } else if (this.id == 2) {

        validate_general('deal_dates', 'deal_to', txtDate1.value);


    }
}

function start_now() {

    if (Dom.hasClass("start_now", "selected")) {
        Dom.removeClass("start_now", "selected")
        Dom.setStyle(['v_calpop1', 'calpop1'], 'display', '');
    } else {
        Dom.addClass("start_now", "selected")
        Dom.setStyle(['v_calpop1', 'calpop1'], 'display', 'none');
        var d = new Date()
        year = d.getFullYear(),
            month = d.getMonth(),
            day = d.getDate();
        if (month < 10) month = '0' + month;
        if (day < 10) day = '0' + day;
        var date = day + "-" + month + "-" + year;
        Dom.get("v_calpop1").value = date


    }

    validate_general('deal_dates', 'deal_from', Dom.get('v_calpop1').value);
}

function change_to_date() {
    Dom.removeClass("to_permanent", "selected")
    Dom.setStyle(['change_to_date', 'permanent_tag'], 'display', 'none');

    Dom.setStyle(['v_calpop2', 'calpop2'], 'display', '');
    validate_scope_data.deal_dates.deal_to.required = true;


    Dom.get("v_calpop2").value = Dom.get("v_calpop2").getAttribute('ovalue');


}

function permanent_campaign() {

    if (!Dom.hasClass("to_permanent", "selected")) {

        Dom.removeClass("finish_now", "selected")

        Dom.addClass("to_permanent", "selected")
        Dom.setStyle(['v_calpop2', 'calpop2'], 'display', 'none');
        Dom.setStyle(['change_to_date'], 'display', '');

        validate_scope_data.deal_dates.deal_to.changed = true;
        validate_scope_data.deal_dates.deal_to.validated = true;

        Dom.get("v_calpop2").value = '';
        //validate_general('deal_dates', 'deal_to', Dom.get('v_calpop2').value);
        validate_scope('deal_dates')

    }

}

function finish_now() {

    Dom.removeClass("to_permanent", "selected")
    Dom.addClass("finish_now", "selected")

    Dom.setStyle(['v_calpop2', 'calpop2'], 'display', 'none');
    Dom.setStyle(['change_to_date'], 'display', '');

    validate_scope_data.deal_dates.deal_to.required = false;

    Dom.get("v_calpop2").value = 'NOW';
    validate_scope_data.deal_dates.deal_to.changed = true;

    validate_scope('deal_dates')



}




function save_edit_dates() {
    save_edit_general('deal_dates')
}

function reset_edit_dates() {
    reset_edit_general('deal_dates')
}

function save_edit_description() {
    save_edit_general('deal_description')
}

function reset_edit_description() {
    reset_edit_general('deal_description')
}

function post_save_actions(r) {

    if (r.key == 'name') {
        Dom.get('title_name_code_bis').innerHTML = r.newvalue
        Dom.get('title_name_code').innerHTML = r.newvalue

    } else if (r.key == 'deal_to') {

        if (r.deal_status == 'Finish') {
            location.reload()
        }

    }
}

function delete_deal() {
    var request = 'ar_edit_deals.php?tipo=delete_deal&deal_key=' + Dom.get('deal_key').value

    Dom.get('deal_delete_wait').src = 'art/loading.gif';


    YAHOO.util.Connect.asyncRequest('POST', request, {

        success: function(o) {

            //alert(o.responseText)
            var r = YAHOO.lang.JSON.parse(o.responseText);
            if (r.state == 200) {
                location.href = "campaign.php?id=" + r.campaign_key


            } else {
                Dom.get('deal_delete_wait').src = 'art/icons/cross.png';
                Dom.get('deal_delete_msg').innerHTML = r.msg
            }

        }
    });

}

function edit_component_status(deal_component_key, status) {
    var request = 'ar_edit_deals.php?tipo=update_deal_component_status&value=' + status + '&deal_component_key=' + deal_component_key

    Dom.get('component_status_edit_wait_' + deal_component_key).src = 'art/loading.gif'

    // alert(request)  
    YAHOO.util.Connect.asyncRequest('POST', request, {

        success: function(o) {

            var r = YAHOO.lang.JSON.parse(o.responseText);
            if (r.status == 200) {

                Dom.get('component_status_edit_' + r.key).innerHTML = r.button_edit_status
                Dom.get('component_status_' + r.key).innerHTML = r.status_icon

            } else {

            }

        }
    });
}

function edit_component_finish(deal_component_key) {
    var request = 'ar_edit_deals.php?tipo=update_deal_component_finish&deal_component_key=' + deal_component_key + '&deal_key=' + Dom.get('deal_key').value
    //alert(request) 
    Dom.get('component_finish_edit_wait_' + deal_component_key).src = 'art/loading.gif'

    // alert(request)  
    YAHOO.util.Connect.asyncRequest('POST', request, {

        success: function(o) {


            var r = YAHOO.lang.JSON.parse(o.responseText);

            if (r.state == 200) {

                if (r.deal_status == 'Finish') {
                    location.reload();

                } else {

                    Dom.get('component_status_edit_' + r.deal_component_key).innerHTML = ''
                    Dom.get('component_status_' + r.deal_component_key).innerHTML = r.deal_component_status_icon
                    Dom.get('component_finish_edit_' + r.deal_component_key).innerHTML = ''
                    get_deal_component_elements_numbers()
                }



            } else {

            }

        }
    });
}


function edit_status(state) {
    var request = 'ar_edit_deals.php?tipo=update_deal_status&value=' + state + '&deal_key=' + Dom.get('deal_key').value

    Dom.get('deal_status_' + state + '_wait').src = "art/loading.gif"

    // alert(request)  
    YAHOO.util.Connect.asyncRequest('POST', request, {

        success: function(o) {

            var r = YAHOO.lang.JSON.parse(o.responseText);
            Dom.get('deal_status_Active_wait').src = "art/icons/tick.png"
            Dom.get('deal_status_Suspended_wait').src = "art/icons/stop.png"
            if (r.state == 200) {





                if (r.new_value == 'Suspended') {
                    Dom.setStyle('deal_status_Suspended', 'display', 'none')
                    Dom.setStyle(['deal_status_Active', 'suspended_tag'], 'display', '')

                } else if (r.new_value == 'Finish') {
                    Dom.setStyle(['deal_status_Suspended', 'suspended_tag'], 'display', 'none')
                    Dom.setStyle('deal_status_Active', 'display', 'none')


                } else {
                    Dom.setStyle('deal_status_Suspended', 'display', '')
                    Dom.setStyle(['deal_status_Active', 'suspended_tag'], 'display', 'none')
                }

                //	Dom.get('component_state_edit_'+r.key).innerHTML=r.button_edit_status
                //	Dom.get('component_state_'+r.key).innerHTML=r.status_icon
            } else {

            }

        }
    });
}




function new_deal_component() {
    location.href = "new_deal_component.php?deal_key=" + Dom.get('deal_key').value + '&referer=edit_deal';;
}




function init() {
    validate_scope_data = {
        'deal_description': {
            'description': {
                'changed': false,
                'validated': true,
                'required': false,
                'group': 1,
                'type': 'item',
                'name': 'deal_description',
                'dbname': 'Deal Description',

                'ar': false
            },
            'terms_label': {
                'changed': false,
                'validated': true,
                'required': true,
                'group': 1,
                'type': 'item',
                'name': 'deal_terms_label',
                'dbname': 'Deal XHTML Terms Description Label',

                'ar': false,
                'validation': [{
                    'regexp': "[a-z\d]+",
                    'invalid_msg': Dom.get('label_invalid_label').value
                }]
            },
            'name': {
                'changed': false,
                'validated': true,
                'required': true,
                'group': 1,
                'type': 'item',
                'name': 'deal_name',
                'dbname': 'Deal Name',
                'ar': 'find',
                'ar_request': 'ar_deals.php?tipo=name_in_other_deal&deal_key=' + Dom.get('deal_key').value + '&store_key=' + Dom.get('store_key').value + '&query=',

                'validation': false

            },

        },


        'deal_dates': {

            'deal_from': {
                'changed': false,
                'validated': true,
                'required': true,
                'group': 1,
                'type': 'item',
                'name': 'v_calpop1',
                'ar': false,
                'dbname': 'Deal Begin Date',
                'validation': [{
                    'regexp': "\d{2}-\d{2}-\d{4}",
                    'invalid_msg': Dom.get('label_invalid_date').value
                }]
            },
            'deal_to': {
                'changed': false,
                'validated': true,
                'required': false,
                'group': 1,
                'type': 'item',
                'name': 'v_calpop2',
                'ar': false,
                'dbname': 'Deal Expiration Date',
                'validation': [{
                    'regexp': "\d{2}-\d{2}-\d{4}",
                    'invalid_msg': Dom.get('label_invalid_date').value
                }]
            },
        }


    };
    validate_scope_metadata = {
        'deal_description': {
            'type': 'edit',
            'ar_file': 'ar_edit_deals.php',
            'key_name': 'deal_key',
            'key': Dom.get('deal_key').value
        },

        'deal_dates': {
            'type': 'edit',
            'ar_file': 'ar_edit_deals.php',
            'key_name': 'deal_key',
            'key': Dom.get('deal_key').value
        }

    };

    var deal_name_oACDS = new YAHOO.util.FunctionDataSource(validate_deal_name);
    deal_name_oACDS.queryMatchContains = true;
    var deal_name_oAutoComp = new YAHOO.widget.AutoComplete("deal_name", "deal_name_Container", deal_name_oACDS);
    deal_name_oAutoComp.minQueryLength = 0;
    deal_name_oAutoComp.queryDelay = 0.1;

    var deal_description_oACDS = new YAHOO.util.FunctionDataSource(validate_deal_description);
    deal_description_oACDS.queryMatchContains = true;
    var deal_description_oAutoComp = new YAHOO.widget.AutoComplete("deal_description", "deal_description_Container", deal_description_oACDS);
    deal_description_oAutoComp.minQueryLength = 0;
    deal_description_oAutoComp.queryDelay = 0.1;

    var deal_terms_label_oACDS = new YAHOO.util.FunctionDataSource(validate_deal_terms_label);
    deal_terms_label_oACDS.queryMatchContains = true;
    var deal_terms_label_oAutoComp = new YAHOO.widget.AutoComplete("deal_terms_label", "deal_terms_label_Container", deal_terms_label_oACDS);
    deal_terms_label_oAutoComp.minQueryLength = 0;
    deal_terms_label_oAutoComp.queryDelay = 0.1;



    init_search('marketing_store');

    var ids = ["description", "state", "allowances"];
    Event.addListener(ids, "click", change_block);



    cal1 = new YAHOO.widget.Calendar("calpop1", "deal_from_Container", {
        title: "Start:",
        mindate: new Date(),
        close: true
    });
    cal1.update = updateCal;
    cal1.id = '1';
    cal1.render();
    cal1.update();
    cal1.selectEvent.subscribe(handleSelect, cal1, true);

    YAHOO.util.Event.addListener("calpop1", "click", cal1.show, cal1, true);


    cal2 = new YAHOO.widget.Calendar("calpop2", "deal_to_Container", {
        title: "Until:",
        mindate: new Date(),
        close: true
    });
    cal2.update = updateCal;
    cal2.id = '2';
    cal2.render();
    cal2.update();
    cal2.selectEvent.subscribe(handleSelect, cal2, true);

    YAHOO.util.Event.addListener("calpop2", "click", cal2.show, cal2, true);
    Event.addListener(['v_calpop1', 'v_calpop2'], "keyup", date_changed);
    YAHOO.util.Event.addListener('to_permanent', "click", permanent_campaign)
    YAHOO.util.Event.addListener('start_now', "click", start_now)
    YAHOO.util.Event.addListener('change_to_date', "click", change_to_date)
    YAHOO.util.Event.addListener('finish_now', "click", finish_now)



    YAHOO.util.Event.addListener('save_edit_deal_dates', "click", save_edit_dates)

    YAHOO.util.Event.addListener('reset_edit_deal_dates', "click", reset_edit_dates)

    YAHOO.util.Event.addListener('save_edit_deal_description', "click", save_edit_description)
    YAHOO.util.Event.addListener('reset_edit_deal_description', "click", reset_edit_description)
    get_deal_component_elements_numbers()

    ids = ['deal_component_status_elements_Waiting', 'deal_component_status_elements_Active', 'deal_component_status_elements_Suspended', 'deal_component_status_elements_Finish']
    Event.addListener(ids, "click", change_elements_deal_component, {
        table_id: 2
    });


}

YAHOO.util.Event.onDOMReady(init);



YAHOO.util.Event.onContentReady("rppmenu0", function() {
    rppmenu = new YAHOO.widget.ContextMenu("rppmenu0", {
        trigger: "rtext_rpp0"
    });
    rppmenu.render();
    rppmenu.subscribe("show", rppmenu.focus);
});
