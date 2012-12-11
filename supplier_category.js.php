<?php
//@author Raul Perusquia <rulovico@gmail.com>
//Copyright (c) 2012 LW
include_once('common.php');
?>
var Dom   = YAHOO.util.Dom;
var Event   = YAHOO.util.Event;






var period='period_<?php echo $_SESSION['state']['supplier_categories']['period']?>';
var avg='avg_<?php echo $_SESSION['state']['supplier_categories']['avg']?>';

var  subcategories_period_ids=['subcategories_period_all',
 'subcategories_period_yesterday',
 'subcategories_period_last_w',
 'subcategories_period_last_m','subcategories_period_three_year','subcategories_period_year','subcategories_period_yeartoday','subcategories_period_six_month','subcategories_period_quarter','subcategories_period_month','subcategories_period_ten_day','subcategories_period_week','subcategories_period_monthtoday','subcategories_period_weektoday','subcategories_period_today'];



var dialog_new_category;

function change_history_elements(e, table_id) {
    ids = ['elements_Change', 'elements_Assign'];
    if (Dom.hasClass(this, 'selected')) {

        var number_selected_elements = 0;
        for (i in ids) {
            if (Dom.hasClass(ids[i], 'selected')) {
                number_selected_elements++;
            }
        }

        if (number_selected_elements > 1) {
            Dom.removeClass(this, 'selected')

        }

    } else {
        Dom.addClass(this, 'selected')

    }
    var table = tables['table' + table_id];
    var datasource = tables['dataSource' + table_id];
    var request = '';
    for (i in ids) {
        if (Dom.hasClass(ids[i], 'selected')) {
            request = request + '&' + ids[i] + '=1'
        } else {
            request = request + '&' + ids[i] + '=0'

        }
    }
    datasource.sendRequest(request, table.onDataReturnInitializeTable, table);
}


function change_suppliers_view_save(tipo){
    YAHOO.util.Connect.asyncRequest('POST', 'ar_sessions.php?tipo=update&keys=supplier_categories-suppliers-view&value=' + escape(tipo), {});

}



function change_block(){
ids=['subcategories','subjects','overview','history','sales','no_assigned'];
block_ids=['block_subcategories','block_subjects','block_overview','block_history','block_sales','block_no_assigned'];
Dom.setStyle(block_ids,'display','none');
Dom.setStyle('block_'+this.id,'display','');
Dom.removeClass(ids,'selected');
Dom.addClass(this,'selected');
YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=supplier_categories-'+Dom.get('state_type').value+'_block_view&value='+this.id ,{});
}


YAHOO.util.Event.addListener(window, "load", function() {
    tables = new function() {


	    var tableid=0;
	    var tableDivEL="table"+tableid;
	    var ColumnDefs = [ 
	       {key:"id", label:"<?php echo _('Id')?>", hidden:true, width:60,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"code", label:"<?php echo _('Code')?>",width:80, sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"name", label:"<?php echo _('Name')?>",<?php echo(($_SESSION['state']['supplier_categories']['suppliers']['view']=='general' or $_SESSION['state']['supplier_categories']['suppliers']['view']=='contact' or $_SESSION['state']['supplier_categories']['suppliers']['view']=='products' or $_SESSION['state']['supplier_categories']['suppliers']['view']=='sales')?'':'hidden:true,')?> width:190,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"contact",<?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='contact'?'hidden:true,':'')?> label:"<?php echo _('Contact')?>", width:190,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}

				       ,{key:"email", label:"<?php echo _('Email')?>",<?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='contact'?'hidden:true,':'')?> width:190,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"location", label:"<?php echo _('Location')?>",<?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='general'?'hidden:true,':'')?> width:190,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"tel",<?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='contact'?'hidden:true,':'')?> label:"<?php echo _('Tel')?>", width:190,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}

				       ,{key:"pending_pos", <?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='general'?'hidden:true,':'')?> label:"<?php echo _('P POs')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"for_sale", <?php echo(($_SESSION['state']['supplier_categories']['suppliers']['view']=='products' or  $_SESSION['state']['supplier_categories']['suppliers']['view']=='general') ?'':'hidden:true,')?> label:"<?php echo _('Products')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"discontinued",<?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='products'?'hidden:true,':'')?>  label:"<?php echo _('Discontinued')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"stock_value",<?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='money'?'hidden:true,':'')?>  label:"<?php echo _('Stock Value')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					
					,{key:"high",<?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='stock'?'hidden:true,':'')?>  label:"<?php echo _('High')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					 ,{key:"normal",<?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='stock'?'hidden:true,':'')?>  label:"<?php echo _('Normal')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					 ,{key:"low", <?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='stock'?'hidden:true,':'')?> label:"<?php echo _('Low')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					 ,{key:"critical", <?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='stock'?'hidden:true,':'')?> label:"<?php echo _('Critical')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					 ,{key:"outofstock", <?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='stock'?'hidden:true,':'')?> label:"<?php echo _('Out of Stock')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					
					,{key:"sales", <?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='sales'?'hidden:true,':'')?> label:"<?php echo _('Sales')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
			
			,{key:"profit", <?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='profit'?'hidden:true,':'')?> label:"<?php echo _('Profit')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       	 ,{key:"profit_after_storing", <?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='profit'?'hidden:true,':'')?> label:"<?php echo _('PaS')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					 ,{key:"cost", <?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='profit'?'hidden:true,':'')?> label:"<?php echo _('Cost')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					 					 ,{key:"margin", <?php echo($_SESSION['state']['supplier_categories']['suppliers']['view']!='profit'?'hidden:true,':'')?> label:"<?php echo _('Margin')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}

			     ];
				     
				     request="ar_suppliers.php?tipo=suppliers&tableid=0&where=&parent=category&sf=0&parent_key="+Dom.get('category_key').value
				    // alert(request)
	    this.dataSource0 = new YAHOO.util.DataSource(request);
	    this.dataSource0.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.dataSource0.connXhrMode = "queueRequests";
	    this.dataSource0.responseSchema = {
		resultsList: "resultset.data", 
		metaFields: {
		    rtext:"resultset.rtext",
		    rtext_rpp:"resultset.rtext_rpp",
		    rowsPerPage:"resultset.records_perpage",
		    sort_key:"resultset.sort_key",
		    sort_dir:"resultset.sort_dir",
		    tableid:"resultset.tableid",
		    filter_msg:"resultset.filter_msg",
		    totalRecords: "resultset.total_records"
		},
		
		fields: [
			 "id"
			 ,"name"
			 ,"code"
			 ,"for_sale"
			 ,"outofstock"
			 ,"low","location","email","profit",'profit_after_storing','cost',"pending_pos","sales","contact","critical","margin"
			 ]};
	    
	    this.table0 = new YAHOO.widget.DataTable(tableDivEL, ColumnDefs,
						     this.dataSource0, {
							 //draggableColumns:true,
							   renderLoopSize: 50,generateRequest : myRequestBuilder
								       ,paginator : new YAHOO.widget.Paginator({
									      rowsPerPage:<?php echo$_SESSION['state']['supplier_categories']['suppliers']['nr']?>,containers : 'paginator0', 
 									      pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',
									      previousPageLinkLabel : "<",
 									      nextPageLinkLabel : ">",
 									      firstPageLinkLabel :"<<",
 									      lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500],alwaysVisible:false
									      ,template : "{PreviousPageLink}<span id='paginator_info0'>{CurrentPageReport}</span>{NextPageLink}"
									  })
								     
								     ,sortedBy : {
									 key: "<?php echo $_SESSION['state']['supplier_categories']['suppliers']['order']?>",
									 dir: "<?php echo $_SESSION['state']['supplier_categories']['suppliers']['order_dir']?>"
								     }
							   ,dynamicData : true

						     }
						     );
	    this.table0.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table0.doBeforeSortColumn = mydoBeforeSortColumn;
	    this.table0.doBeforePaginatorChange = mydoBeforePaginatorChange;
  		this.table0.table_id=tableid;
        this.table0.subscribe("renderEvent", myrenderEvent);

	    
	    this.table0.view='<?php echo $_SESSION['state']['supplier_categories']['suppliers']['view']?>';
	    this.table0.filter={key:'<?php echo $_SESSION['state']['supplier_categories']['suppliers']['f_field']?>',value:'<?php echo $_SESSION['state']['supplier_categories']['suppliers']['f_value']?>'};
		


	    var tableid=1; // Change if you have more the 1 table
	    var tableDivEL="table"+tableid;
	    var OrdersColumnDefs = [ 
				
				    {key:"code", label:"<?php echo _('Code')?>", width:90,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				    ,{key:"label", label:"<?php echo _('Label')?>", width:360,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
					,{key:"subjects", label:"<?php echo _('Suppliers')?>", width:100,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					,{key:"sales", label:"<?php echo _('Sales')?>", width:100,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					,{key:"delta_sales", label:"<?php echo '&Delta;'._('Sales')?>", width:100,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}

				
				     ];
//alert("ar_suppliers.php?tipo=supplier_categories&sf=0&tableid=1&parent_key="+Dom.get('category_key').value)
	    request="ar_suppliers.php?tipo=supplier_categories&sf=0&tableid=1&parent_key="+Dom.get('category_key').value;
	   // alert(request)
	    this.dataSource1 = new YAHOO.util.DataSource(request);
	    this.dataSource1.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.dataSource1.connXhrMode = "queueRequests";
	    this.dataSource1.responseSchema = {
		resultsList: "resultset.data", 
		metaFields: {
		    rtext:"resultset.rtext",
		    rtext_rpp:"resultset.rtext_rpp",
		    rowsPerPage:"resultset.records_perpage",
		    sort_key:"resultset.sort_key",
		    sort_dir:"resultset.sort_dir",
		    tableid:"resultset.tableid",
		    filter_msg:"resultset.filter_msg",
		    totalRecords: "resultset.total_records"
		},
		
		fields: [
			"code","subjects","sold","sales","label","delta_sales"
			 ]};
	    
	    this.table1 = new YAHOO.widget.DataTable(tableDivEL, OrdersColumnDefs,
						     this.dataSource1, {
							   renderLoopSize: 50,generateRequest : myRequestBuilder
								       ,paginator : new YAHOO.widget.Paginator({
									      rowsPerPage:<?php echo$_SESSION['state']['supplier_categories']['subcategories']['nr']?>,containers : 'paginator1', 
 									      pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',
									      previousPageLinkLabel : "<",
 									      nextPageLinkLabel : ">",
 									      firstPageLinkLabel :"<<",
 									      lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500],alwaysVisible:false
									      ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info1'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
									  })
								     
								     ,sortedBy : {
									 key: "<?php echo$_SESSION['state']['supplier_categories']['subcategories']['order']?>",
									 dir: "<?php echo$_SESSION['state']['supplier_categories']['subcategories']['order_dir']?>"
								     }
							   ,dynamicData : true

						     }
						     );
	    this.table1.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table1.doBeforeSortColumn = mydoBeforeSortColumn;
	    this.table1.doBeforePaginatorChange = mydoBeforePaginatorChange;


	    
	    this.table1.view='<?php echo$_SESSION['state']['supplier_categories']['view']?>';
	    this.table1.filter={key:'<?php echo$_SESSION['state']['supplier_categories']['subcategories']['f_field']?>',value:'<?php echo$_SESSION['state']['supplier_categories']['subcategories']['f_value']?>'};
		this.table1.table_id=tableid;
     	this.table1.subscribe("renderEvent", myrenderEvent);
		
 var tableid=2; // Change if you have more the 1 table
	    var tableDivEL="table"+tableid;

	  	  	    var SuppliersColumnDefs = [
				       {key:"key", label:"", width:20,sortable:false,isPrimaryKey:true,hidden:true} 
				      ,{key:"date", label:"<?php echo _('Date')?>",className:"aright",width:120,sortable:true,sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				      ,{key:"time", label:"<?php echo _('Time')?>",className:"aleft",width:50}
				      ,{key:"handle", label:"<?php echo _('Author')?>",className:"aleft",width:100,sortable:true,sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				      ,{key:"note", formatter:this.prepare_note,label:"<?php echo _('Notes')?>",className:"aleft",width:520}
				       ];
		request="ar_history.php?tipo=supplier_categories&parent=category&parent_key="+Dom.get('category_key').value+"&tableid=2";
//	   	alert(request)
	   	this.dataSource2 = new YAHOO.util.DataSource(request);

	   this.dataSource2.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.dataSource2.connXhrMode = "queueRequests";
	    this.dataSource2.responseSchema = {
		resultsList: "resultset.data", 
		metaFields: {
		    rowsPerPage:"resultset.records_perpage",
		    rtext:"resultset.rtext",
		    rtext_rpp:"resultset.rtext_rpp",
		    sort_key:"resultset.sort_key",
		    sort_dir:"resultset.sort_dir",
		    tableid:"resultset.tableid",
		    filter_msg:"resultset.filter_msg",
		    totalRecords: "resultset.total_records"
		},
		
		
		fields: [
			 "key"
			 ,"date"
			 ,'time','handle','note'
			 ]};
	    
	    this.table2 = new YAHOO.widget.DataTable(tableDivEL, SuppliersColumnDefs,
						     this.dataSource2
						     , {
							 renderLoopSize: 50,generateRequest : myRequestBuilder
							 ,paginator : new YAHOO.widget.Paginator({
								 rowsPerPage    : <?php echo$_SESSION['state']['supplier_categories']['history']['nr']?>,containers : 'paginator2', alwaysVisible:false,
								 pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',
								 previousPageLinkLabel : "<",
								 nextPageLinkLabel : ">",
								 firstPageLinkLabel :"<<",
								 lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500]
								 ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info2'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
							     })
							 
							 ,sortedBy : {
							    key: "<?php echo$_SESSION['state']['supplier_categories']['history']['order']?>",
							     dir: "<?php echo$_SESSION['state']['supplier_categories']['history']['order_dir']?>"
							 },
							 dynamicData : true
							 
						     }
						     
						     );
	    
	    this.table2.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table2.doBeforeSortColumn = mydoBeforeSortColumn;
	    this.table2.doBeforePaginatorChange = mydoBeforePaginatorChange;

		this.table2.table_id=tableid;
     	this.table2.subscribe("renderEvent", myrenderEvent);

		    
	    this.table2.filter={key:'<?php echo$_SESSION['state']['supplier_categories']['history']['f_field']?>',value:'<?php echo$_SESSION['state']['supplier_categories']['history']['f_value']?>'};







	};
    });


function change_suppliers_view_save(tipo) {
    YAHOO.util.Connect.asyncRequest('POST', 'ar_sessions.php?tipo=update&keys=supplier_categories-suppliers-view&value=' + escape(tipo), {});

}

function change_sales_period(){
  tipo=this.id;
 
  ids=['category_period_yesterday','category_period_last_m','category_period_last_w','category_period_all','category_period_three_year','category_period_year','category_period_six_month','category_period_quarter','category_period_month','category_period_ten_day','category_period_week','category_period_yeartoday','category_period_monthtoday','category_period_weektoday','category_period_today'];

 Dom.removeClass(ids,"selected")
 Dom.addClass(this,"selected")
   period=this.getAttribute('period');
 YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=supplier_categories-period&value='+period ,{});



Dom.setStyle(['info_yesterday','info_last_m','info_last_w','info_all','info_three_year','info_year','info_six_month','info_quarter','info_month','info_ten_day','info_week','info_yeartoday','info_monthtoday','info_weektoday','info_today'],'display','none')


Dom.setStyle(['info2_yesterday','info2_last_m','info2_last_w','info2_all','info2_three_year','info2_year','info2_six_month','info2_quarter','info2_month','info2_ten_day','info2_week','info2_yeartoday','info2_monthtoday','info2_weektoday','info2_today'],'display','none')
Dom.setStyle(['info_'+period,'info2_'+period],'display','')

}



function change_subcategories_period(e,table_id){

  tipo=this.id;

 Dom.removeClass(subcategories_period_ids,"selected")
 Dom.addClass(this,"selected")
   
    var table=tables['table'+table_id];
    var datasource=tables['dataSource'+table_id];
    var request='&period=' + this.getAttribute('period');
    datasource.sendRequest(request,table.onDataReturnInitializeTable, table);       

}
function change_avg(e,table_id){

    //  alert(avg);
    tipo=this.id;
    Dom.get(avg).className="";
    Dom.get(tipo).className="selected";	
    avg=tipo;
    var table=tables['table'+table_id];
    var datasource=tables['dataSource'+table_id];
    var request='&avg=' + this.getAttribute('avg');
    datasource.sendRequest(request,table.onDataReturnInitializeTable, table);       
}


function change_display_mode(name,label){
    if(name=='percentage'){
	var request='&percentages=1';
    }if(name=='value'){
	var request='&percentages=0&show_default_currency=0';
    }if(name=='value_default_d2d'){
	var request='&percentages=0&show_default_currency=1';
    }

    Dom.get('change_display_mode').innerHTML=label;
    var table=tables['table0'];
    var datasource=tables.dataSource0;
    
    datasource.sendRequest(request,table.onDataReturnInitializeTable, table);   

}

function update_supplier_category_history_elements() {

    var ar_file = 'ar_history.php';
    var request = 'tipo=get_supplier_category_history_elements&parent=category&parent_key=' + Dom.get('category_key').value;
    YAHOO.util.Connect.asyncRequest('POST', ar_file, {
        success: function(o) {
            var r = YAHOO.lang.JSON.parse(o.responseText);
            for (key in r.elements_number) {
                Dom.get('elements_' + key + '_number').innerHTML = r.elements_number[key]
            }
        },
        failure: function(o) {},
        scope: this
    }, request

    );
}


function init() {

    ids = ['subcategories', 'subjects', 'overview', 'history', 'sales', 'no_assigned'];
    Event.addListener(ids, "click", change_block);

    init_search('suppliers');

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


    var oACDS2 = new YAHOO.util.FunctionDataSource(mygetTerms);
    oACDS2.queryMatchContains = true;
    oACDS2.table_id = 2;
    var oAutoComp2 = new YAHOO.widget.AutoComplete("f_input2", "f_container2", oACDS2);
    oAutoComp2.minQueryLength = 0;

    Event.addListener('clean_table_filter_show0', "click", show_filter, 0);
    Event.addListener('clean_table_filter_hide0', "click", hide_filter, 0);
    Event.addListener('clean_table_filter_show1', "click", show_filter, 1);
    Event.addListener('clean_table_filter_hide1', "click", hide_filter, 1);
    Event.addListener('clean_table_filter_show2', "click", show_filter, 2);
    Event.addListener('clean_table_filter_hide2', "click", hide_filter, 2);

    ids=['suppliers_general','suppliers_sales','suppliers_stock','suppliers_products','suppliers_contact','suppliers_profit'];
 YAHOO.util.Event.addListener(ids, "click",change_suppliers_view,{'table_id':0,'parent':'suppliers'})
 YAHOO.util.Event.addListener(suppliers_period_ids, "click",change_period,{'table_id':0,'subject':'suppliers'});

    YAHOO.util.Event.addListener(subcategories_period_ids, "click", change_subcategories_period, 1);
    ids = ['category_period_all', 'category_period_three_year', 'category_period_year', 'category_period_yeartoday', 'category_period_six_month', 'category_period_quarter', 'category_period_month', 'category_period_ten_day', 'category_period_week', 'category_period_monthtoday', 'category_period_weektoday', 'category_period_today', 'category_period_yesterday', 'category_period_last_m', 'category_period_last_w'];
    YAHOO.util.Event.addListener(ids, "click", change_sales_period);

    ids = ['elements_Change', 'elements_Assign'];
    Event.addListener(ids, "click", change_history_elements, 2);


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
YAHOO.util.Event.onContentReady("rppmenu2", function() {
    var oMenu = new YAHOO.widget.ContextMenu("rppmenu2", {
        trigger: "rtext_rpp2"
    });
    oMenu.render();
    oMenu.subscribe("show", oMenu.focus);
});

YAHOO.util.Event.onContentReady("filtermenu2", function() {
    var oMenu = new YAHOO.widget.ContextMenu("filtermenu2", {
        trigger: "filter_name2"
    });
    oMenu.render();
    oMenu.subscribe("show", oMenu.focus);

});

YAHOO.util.Event.onContentReady("change_display_menu", function() {
    var oMenu = new YAHOO.widget.Menu("change_display_menu", {
        context: ["change_display_mode", "tr", "br"]
    });
    oMenu.render();
    oMenu.subscribe("show", oMenu.focus);
    YAHOO.util.Event.addListener("change_display_mode", "click", oMenu.show, null, oMenu);

});
