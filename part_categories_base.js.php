<?php
//@author Raul Perusquia <rulovico@gmail.com>
//Copyright (c) 2011 Inikooo
include_once('common.php');
?>
var Dom   = YAHOO.util.Dom;
var Event   = YAHOO.util.Event;

var period='period_<?php echo $_SESSION['state']['part_categories']['period']?>';
var avg='avg_<?php echo $_SESSION['state']['part_categories']['avg']?>';


function change_block(){
ids=['subcategories','history'];
block_ids=['block_subcategories','block_history'];
Dom.setStyle(block_ids,'display','none');
Dom.setStyle('block_'+this.id,'display','');
Dom.removeClass(ids,'selected');
Dom.addClass(this,'selected');
YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=part_categories-base_block_view&value='+this.id ,{});
}

var dialog_new_category;

function change_category(){
    if(Dom.hasClass(this,'selected'))
    	return;
    //alert(this)
     Dom.removeClass(Dom.getElementsByClassName('selected','span','cat_chooser'),'selected');
    Dom.addClass(this,'selected');
    var table_id=0
    var table=tables['table'+table_id];
    var datasource=tables['dataSource'+table_id];
    var request='&category=' + this.getAttribute('cat_id');
    datasource.sendRequest(request,table.onDataReturnInitializeTable, table);   


    
}
var change_view=function(e){
	
	var table=tables['table0'];
	var tipo=this.id;
	//	alert(table.view+' '+tipo)
	if(table.view!=tipo){
	    table.hideColumn('active');
	    table.hideColumn('families');
	    table.hideColumn('departments');
	    table.hideColumn('todo');
	    table.hideColumn('discontinued');

	    table.hideColumn('sales');
	    table.hideColumn('profit');
	    //    table.hideColumn('stock_value');
	    table.hideColumn('stock_error');
	    table.hideColumn('outofstock');
	    table.hideColumn('surplus');
	    table.hideColumn('optimal');
	    table.hideColumn('low');
	    table.hideColumn('critcal');

	    if(tipo=='sales'){
		Dom.get('period_options').style.display='';
		Dom.get('avg_options').style.display='';
		table.showColumn('sales');
		table.showColumn('profit');
	    }
	    if(tipo=='general'){
		Dom.get('period_options').style.display='none';
		Dom.get('avg_options').style.display='none';
		table.showColumn('active');
		table.showColumn('families');
		table.showColumn('departments');
		table.showColumn('todo');
		table.showColumn('discontinued');

	    }
	    if(tipo=='stock'){
		Dom.get('period_options').style.display='none';
		Dom.get('avg_options').style.display='none';
		
		table.showColumn('surplus');
		table.showColumn('optimal');
		table.showColumn('low');
		table.showColumn('critcal');
		table.showColumn('stock_error');
		table.showColumn('outofstock');
	    }

	    

	
	Dom.get(table.view).className="";
	Dom.get(tipo).className="selected";
	table.view=tipo
	YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=customer_categories-view&value=' + escape(tipo) );
	}
  }



YAHOO.util.Event.addListener(window, "load", function() {
    tables = new function() {


	
	    var tableid=1; // Change if you have more the 1 table
	    var tableDivEL="table"+tableid;
	    var OrdersColumnDefs = [ 
				
				    {key:"name", label:"<?php echo _('Name')?>", width:360,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				    //	,{key:"subjects", label:"<?php echo _('Customers')?>", width:260,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}

				
				     ];

	    this.dataSource1 = new YAHOO.util.DataSource("ar_warehouse.php?tipo=part_categories&tableid=1");
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
			"name","subjects"
			 ]};
	    
	    this.table1 = new YAHOO.widget.DataTable(tableDivEL, OrdersColumnDefs,
						     this.dataSource1, {
							   renderLoopSize: 50,generateRequest : myRequestBuilder
								       ,paginator : new YAHOO.widget.Paginator({
									      rowsPerPage:<?php echo$_SESSION['state']['part_categories']['subcategories']['nr']?>,containers : 'paginator1', 
 									      pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',
									      previousPageLinkLabel : "<",
 									      nextPageLinkLabel : ">",
 									      firstPageLinkLabel :"<<",
 									      lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500],alwaysVisible:false
									      ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info1'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
									  })
								     
								     ,sortedBy : {
									 key: "<?php echo$_SESSION['state']['part_categories']['subcategories']['order']?>",
									 dir: "<?php echo$_SESSION['state']['part_categories']['subcategories']['order_dir']?>"
								     }
							   ,dynamicData : true

						     }
						     );
	    this.table1.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table1.doBeforeSortColumn = mydoBeforeSortColumn;
	    this.table1.doBeforePaginatorChange = mydoBeforePaginatorChange;


	    
	    this.table1.view='<?php echo$_SESSION['state']['part_categories']['view']?>';
	    this.table1.filter={key:'<?php echo$_SESSION['state']['part_categories']['subcategories']['f_field']?>',value:'<?php echo$_SESSION['state']['part_categories']['subcategories']['f_value']?>'};

		

 var tableid=2; // Change if you have more the 1 table
	    var tableDivEL="table"+tableid;

	    var CustomersColumnDefs = [
				       {key:"date",label:"<?php echo _('Date')?>", width:200,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"author",label:"<?php echo _('Author')?>", width:70,sortable:true,formatter:this.customer_name,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       //     ,{key:"tipo", label:"<?php echo _('Type')?>", width:90,sortable:true,formatter:this.customer_name,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       //,{key:"diff_qty",label:"<?php echo _('Qty')?>", width:90,sortable:true,formatter:this.customer_name,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"abstract", label:"<?php echo _('Description')?>", width:370,sortable:true,formatter:this.customer_name,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ];
	    
	    this.dataSource2 = new YAHOO.util.DataSource("ar_history.php?tipo=history&type=base_part_category&parent_key="+Dom.get('warehouse_id').value+"&tableid=2");
	    this.dataSource2.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.dataSource2.connXhrMode = "queueRequests";
	    this.dataSource2.responseSchema = {
		resultsList: "resultset.data", 
		metaFields: {
		  
		 rowsPerPage:"resultset.records_perpage",
		    RecordOffset : "resultset.records_offset", 
		       rtext:"resultset.rtext",
		    rtext_rpp:"resultset.rtext_rpp",
		    sort_key:"resultset.sort_key",
		    sort_dir:"resultset.sort_dir",
		    tableid:"resultset.tableid",
		    filter_msg:"resultset.filter_msg",
		    totalRecords: "resultset.total_records"
		
		},
		
		
		fields: [
			 "id"
			 ,"note"
			 ,'author','date','tipo','abstract','details'
			 ]};
	    
	    this.table2 = new YAHOO.widget.DataTable(tableDivEL, CustomersColumnDefs,
						     this.dataSource2
						     , {
							 renderLoopSize: 50,generateRequest : myRequestBuilder
							 ,paginator : new YAHOO.widget.Paginator({
								 rowsPerPage    : <?php echo$_SESSION['state']['customer_categories']['history']['nr']?>,containers : 'paginator2', alwaysVisible:false,
								 pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',
								 previousPageLinkLabel : "<",
								 nextPageLinkLabel : ">",
								 firstPageLinkLabel :"<<",
								 lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500]
								 ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info2'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
							     })
							 
							 ,sortedBy : {
							    key: "<?php echo$_SESSION['state']['customer_categories']['history']['order']?>",
							     dir: "<?php echo$_SESSION['state']['customer_categories']['history']['order_dir']?>"
							 },
							 dynamicData : true
							 
						     }
						     
						     );
	    
	    this.table2.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table2.doBeforeSortColumn = mydoBeforeSortColumn;
	    this.table2.doBeforePaginatorChange = mydoBeforePaginatorChange;

		       this.table2.table_id=tableid;
     this.table2.subscribe("renderEvent", myrenderEvent);

		    
	    this.table2.filter={key:'<?php echo$_SESSION['state']['customer_categories']['history']['f_field']?>',value:'<?php echo$_SESSION['state']['customer_categories']['history']['f_value']?>'};







	};
    });


function change_period(e,table_id){

    tipo=this.id;
    Dom.get(period).className="";
    Dom.get(tipo).className="selected";	
    period=tipo;
    var table=tables['table'+table_id];
    var datasource=tables['dataSource'+table_id];
    var request='&period=' + this.getAttribute('period');
    // alert(request);
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



 function init(){
  ids=['subcategories','history'];
  Event.addListener(ids, "click",change_block);


 init_search('parts');
 
var oACDS = new YAHOO.util.FunctionDataSource(mygetTerms);
 oACDS.queryMatchContains = true;
 var oAutoComp = new YAHOO.widget.AutoComplete("f_input0","f_container0", oACDS);
 oAutoComp.minQueryLength = 0; 





 ids=['general','sales','stock'];
 YAHOO.util.Event.addListener(ids, "click",change_view)
 ids=['period_all','period_year','period_quarter','period_month','period_week'];
 YAHOO.util.Event.addListener(ids, "click",change_period,0);
 ids=['avg_totals','avg_month','avg_week',"avg_month_eff","avg_week_eff"];
 YAHOO.util.Event.addListener(ids, "click",change_avg,0);

 var elements = Dom.getElementsByClassName('cat_chooser','span','cat_chooser');

     YAHOO.util.Event.addListener(elements, "click",change_category);

 //YAHOO.util.Event.addListener('show_details', "click",show_details,'customer_categories');
 YAHOO.util.Event.addListener('show_percentages', "click",show_percentages,'customer_categories');




 }

YAHOO.util.Event.onDOMReady(init);

YAHOO.util.Event.onContentReady("rppmenu0", function () {
	 var oMenu = new YAHOO.widget.ContextMenu("rppmenu0", {trigger:"rtext_rpp0" });
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
    });

YAHOO.util.Event.onContentReady("filtermenu0", function () {
	 var oMenu = new YAHOO.widget.ContextMenu("filtermenu0", {trigger:"filter_name0"});
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
	 
    });

YAHOO.util.Event.onContentReady("change_display_menu", function () {
	 var oMenu = new YAHOO.widget.Menu("change_display_menu", { context:["change_display_mode","tr", "br"]  });
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
	 YAHOO.util.Event.addListener("change_display_mode", "click", oMenu.show, null, oMenu);
  
    });
