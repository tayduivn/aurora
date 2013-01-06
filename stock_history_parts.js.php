<?php
include_once('common.php');

?>
 var Dom   = YAHOO.util.Dom;




YAHOO.util.Event.addListener(window, "load", function() {
    tables = new function() {




	    var tableid=1; // Change if you have more the 1 table
	    var tableDivEL="table"+tableid;
	    var LocationsColumnDefs = [
				     {key:"date", label:"<?php echo _('Date')?>", width:150,sortable:false,className:"aright"}
				      
				      ,{key:"type", label:"<?php echo _('Type')?>", width:50,sortable:false,className:"aleft"}
				       ,{key:"user", label:"<?php echo _('User')?>", width:50,sortable:false,className:"aleft"}
				     ,{key:"location", label:"<?php echo _('Location')?>", width:60,sortable:false,className:"aleft"}

				      ,{key:"note", label:"<?php echo _('Note')?>", width:300,sortable:false,className:"aleft"}
				      ,{key:"change", label:"<?php echo _('Change')?>", width:60,sortable:false,className:"aright"}
					 ];
	    //?tipo=locations&tid=0"
	    this.dataSource1 = new YAHOO.util.DataSource("ar_parts.php?tipo=part_transactions&parent=warehouse&parent_key="+Dom.get('warehouse_id').value+"&sf=0&tableid="+tableid);
	    this.dataSource1.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.dataSource1.connXhrMode = "queueRequests";
	    this.dataSource1.responseSchema = {
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
			"date","change","type","location","note","user"
			 ]};
	    this.table1 = new YAHOO.widget.DataTable(tableDivEL, LocationsColumnDefs,
								   this.dataSource1
								 , {
								     renderLoopSize: 50,generateRequest : myRequestBuilder
								       ,paginator : new YAHOO.widget.Paginator({
									      rowsPerPage    : <?php echo$_SESSION['state']['stock_history']['transactions']['nr']?>,containers : 'paginator1', 
 									      pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',
									      previousPageLinkLabel : "<",
 									      nextPageLinkLabel : ">",
 									      firstPageLinkLabel :"<<",
 									      lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500]
									      ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info1'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
									  })
								     ,sortedBy : {
									 key: "<?php echo$_SESSION['state']['stock_history']['transactions']['order']?>",
									 dir: "<?php echo$_SESSION['state']['stock_history']['transactions']['order_dir']?>"
								     },
								     dynamicData : true
								  }
								 );
	    this.table1.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table1.doBeforeSortColumn = mydoBeforeSortColumn;
	    this.table1.doBeforePaginatorChange = mydoBeforePaginatorChange;
	    this.table1.filter={key:'<?php echo$_SESSION['state']['stock_history']['transactions']['f_field']?>',value:'<?php echo$_SESSION['state']['stock_history']['transactions']['f_value']?>'};
	    
	
	    var tableid=2;
	    var tableDivEL="table"+tableid;
	    var ColumnDefs = [ 
				    {key:"sku", label:"<?php echo _('SKU')?>", width:50,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				    ,{key:"description", label:"<?php echo _('Description')?>",width:300,<?php echo($_SESSION['state']['stock_history']['parts']['view']=='general'?'':'hidden:true,')?> sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
	  ,{key:"locations", label:"<?php echo _('Locations')?>", width:65,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				      ,{key:"stock", label:"<?php echo _('Stock')?>", width:65,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				      ,{key:"value_at_cost", label:"<?php echo _('Cost Value')?>", width:80,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}

				      ,{key:"value_at_end_day", label:"<?php echo _('C Value (ED)')?>", width:85,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				      ,{key:"commercial_value", label:"<?php echo _('Com Value')?>", width:85,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
	
	];
request="ar_parts.php?tipo=parts_at_date&parent=warehouse&parent_key="+Dom.get('warehouse_id').value+"&tableid=2&where=&sf=0&date="+Dom.get('date').value;
alert(request)
	this.dataSource2 = new YAHOO.util.DataSource(request);
	    this.dataSource2.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.dataSource2.connXhrMode = "queueRequests";
	    this.dataSource2.responseSchema = {
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
			 "sku"
			 ,"description","locations","value_at_cost","stock","value_at_end_day","commercial_value"
			 ]};
	    
	    this.table2 = new YAHOO.widget.DataTable(tableDivEL, ColumnDefs,
						     this.dataSource2, {
							 //draggableColumns:true,
							   renderLoopSize: 50,generateRequest : myRequestBuilder
								       ,paginator : new YAHOO.widget.Paginator({
									      rowsPerPage:<?php echo$_SESSION['state']['stock_history']['parts']['nr']?>,containers : 'paginator2', 
 									      pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',
									      previousPageLinkLabel : "<",
 									      nextPageLinkLabel : ">",
 									      firstPageLinkLabel :"<<",
 									      lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500],alwaysVisible:false
									      ,template : "{PreviousPageLink}<span id='paginator_info2'>{CurrentPageReport}</span>{NextPageLink}"
									  })
								     
								     ,sortedBy : {
									 key: "<?php echo $_SESSION['state']['stock_history']['parts']['order']?>",
									 dir: "<?php echo $_SESSION['state']['stock_history']['parts']['order_dir']?>"
								     }
							   ,dynamicData : true

						     }
						     );
	    this.table2.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table2.doBeforeSortColumn = mydoBeforeSortColumn;
	    this.table2.doBeforePaginatorChange = mydoBeforePaginatorChange;


	    
	    this.table2.view='<?php echo $_SESSION['state']['stock_history']['parts']['view']?>';
	    this.table2.filter={key:'<?php echo $_SESSION['state']['stock_history']['parts']['f_field']?>',value:'<?php echo $_SESSION['state']['stock_history']['parts']['f_value']?>'};
		



	};
    });
function hide_stock_history_chart(){
Dom.setStyle(['stock_history_plot_subblock_part','hide_stock_history_chart'],'display','none')
Dom.setStyle('show_stock_history_chart','display','')
YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=warehouse-stock_history-show_chart&value=0',{});
}

function show_stock_history_chart(){
Dom.setStyle(['hide_stock_history_chart','stock_history_plot_subblock_part'],'display','')
Dom.setStyle(['show_stock_history_chart'],'display','none')
YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=warehouse-stock_history-show_chart&value=1' ,{});
}

function change_block(){
ids=['history','movements','parts']
block_ids=['block_history','block_movements','block_parts']
Dom.setStyle(block_ids,'display','none');
Dom.setStyle('block_'+this.id,'display','');
Dom.removeClass(ids,'selected');
Dom.addClass(this,'selected');

YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=warehouse-parts_view&value='+this.id ,{});
}

function get_warehouse_transaction_numbers(from,to){


var ar_file='ar_parts.php'; 
    	var request='tipo=number_warehouse_transactions_in_interval&warehouse_key='+Dom.get('warehouse_key').value+'&from='+from+'&to='+to;
			
			
			Dom.setStyle(['transactions_all_transactions_wait','transactions_in_transactions_wait','transactions_out_transactions_wait','transactions_audit_transactions_wait','transactions_oip_transactions_wait','transactions_move_transactions_wait'],'display','');
//alert(request)

Dom.get('transactions_all_transactions').innerHTML='';
						Dom.get('transactions_in_transactions').innerHTML='';
						Dom.get('transactions_out_transactions').innerHTML='';
						Dom.get('transactions_audit_transactions').innerHTML='';
						Dom.get('transactions_oip_transactions').innerHTML='';
						Dom.get('transactions_move_transactions').innerHTML='';


	YAHOO.util.Connect.asyncRequest(
					'POST',
					ar_file, {
					    success:function(o) {
						//alert(o.responseText);
						var r = YAHOO.lang.JSON.parse(o.responseText);
						if (r.state == 200) {

						Dom.setStyle(['transactions_all_transactions_wait','transactions_in_transactions_wait','transactions_out_transactions_wait','transactions_audit_transactions_wait','transactions_oip_transactions_wait','transactions_move_transactions_wait'],'display','none');

						Dom.get('transactions_all_transactions').innerHTML=r.transactions.all_transactions
						Dom.get('transactions_in_transactions').innerHTML=r.transactions.in_transactions
						Dom.get('transactions_out_transactions').innerHTML=r.transactions.out_transactions
						Dom.get('transactions_audit_transactions').innerHTML=r.transactions.audit_transactions
						Dom.get('transactions_oip_transactions').innerHTML=r.transactions.oip_transactions
						Dom.get('transactions_move_transactions').innerHTML=r.transactions.move_transactions

						
						
						}
					    },
					failure:function(o) {
					  //  alert(o.statusText);
					    
					},
					scope:this
				    },
				    request
				    
				    );  
}


 var change_transaction_type=function(e){
     var table=tables.table0;
     var datasource=tables.dataSource0;
     Dom.removeClass(Dom.getElementsByClassName('transaction_type','span' , 'transaction_chooser'),'selected');;
     Dom.addClass(this,'selected');     
     var request='&view='+this.getAttribute('table_type');
  
     datasource.sendRequest(request,table.onDataReturnInitializeTable, table);       
 }

function get_warehouse_element_transaction_numbers(element,from,to){


var ar_file='ar_parts.php'; 
    	var request='tipo=number_warehouse_element_transactions_in_interval&element='+element+'&warehouse_key='+Dom.get('warehouse_key').value+'&from='+from+'&to='+to;
			
			
			Dom.setStyle(['transactions_'+element+'_transactions_wait'],'display','');
//alert(request)

Dom.get('transactions_'+element+'_transactions').innerHTML='';
					


	YAHOO.util.Connect.asyncRequest(
					'POST',
					ar_file, {
					    success:function(o) {
						//alert(o.responseText);
						var r = YAHOO.lang.JSON.parse(o.responseText);
						if (r.state == 200) {

						Dom.setStyle(['transactions_'+r.element+'_transactions_wait'],'display','none');

						Dom.get('transactions_'+r.element+'_transactions').innerHTML=r.number
						

						
						
						}
					    },
					failure:function(o) {
					   // alert(o.statusText);
					    
					},
					scope:this
				    },
				    request
				    
				    );  
}


var change_snapshot_granularity=function(e){
     var table=tables.table0;
     var datasource=tables.dataSource0;
     Dom.removeClass(Dom.getElementsByClassName('table_type','span' , 'stock_history_type'),'selected');;
     Dom.addClass(this,'selected');     
     var request='&type='+this.getAttribute('table_type');
     datasource.sendRequest(request,table.onDataReturnInitializeTable, table);       
 }

function change_interval(e,suffix){
  
     from=Dom.get("v_calpop1"+suffix).value;
     to=Dom.get("v_calpop2"+suffix).value;

     if(from=='' && to==''){
	 Dom.get('clear_interval'+suffix).style.display='none';

     }else{
	 Dom.get('clear_interval'+suffix).style.display='';

 }

       Dom.get("v_calpop2"+suffix).value=to;
     Dom.get("v_calpop1"+suffix).value=from;
      var request='&sf=0&from=' +from+'&to='+to;
     if(suffix=='t'){
    get_part_transaction_numbers(from,to)
tables.dataSource1.sendRequest(request,tables.table1.onDataReturnInitializeTable, tables.table1);  
     }else{
     
      tables.dataSource0.sendRequest(request,tables.table0.onDataReturnInitializeTable, tables.table0);  

     }
     
 
   
     
     
 }


function clear_interval(e,suffix){
 
    var request='&sf=0&from=&to=';
   if(suffix=='t'){
      Dom.get("v_calpop1t").value='';
     Dom.get("v_calpop2t").value='';
             Dom.get('clear_intervalt').style.display='none';
 get_part_transaction_numbers('','')
     tables.dataSource1.sendRequest(request,tables.table1.onDataReturnInitializeTable, tables.table1);       

   }else{
   
   Dom.get("v_calpop1").value='';
     Dom.get("v_calpop2").value='';
           Dom.get('clear_interval').style.display='none';
       tables.dataSource0.sendRequest(request,tables.table0.onDataReturnInitializeTable, tables.table0);       
}
   
 }


function change_plot(type){
Dom.setStyle(['change_plot_label_value','change_plot_label_end_day_value','change_plot_label_commercial_value'],'display','none')
Dom.setStyle('change_plot_label_'+type,'display','')


change_plot_menu.hide()



reloadSettings("conf/plot_general_candlestick.xml.php?tipo=part_stock_history&output="+type+"&parent=warehouse&parent_key="+Dom.get('warehouse_key').value);

YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=warehouse-stock_history-chart_output&value='+type ,{});

}
function show_dialog_change_plot(){
region1 = Dom.getRegion('change_plot'); 
    region2 = Dom.getRegion('change_plot_menu'); 
	var pos =[region1.right-region2.width,region1.bottom]
	Dom.setXY('change_plot_menu', pos);

change_plot_menu.show()
}

 function init(){
 
 change_plot_menu = new YAHOO.widget.Dialog("change_plot_menu", {visible : false,close:true,underlay: "none",draggable:false});
change_plot_menu.render();
Event.addListener("change_plot", "click", show_dialog_change_plot);
 
 
  init_search('parts');
  
  ids=['elements_Keeping','elements_NotKeeping','elements_Discontinued','elements_LastStock'];
  Event.addListener(ids, "click",change_parts_elements,2);
var ids=['parts_general','parts_stock','parts_sales','parts_forecast','parts_locations'];
YAHOO.util.Event.addListener(ids, "click",change_parts_view,2);
 YAHOO.util.Event.addListener(parts_period_ids, "click",change_parts_period,2);
 ids=['parts_avg_totals','parts_avg_month','parts_avg_week',"parts_avg_month_eff","parts_avg_week_eff"];
 YAHOO.util.Event.addListener(ids, "click",change_parts_avg,2);
 
  
  
Event.addListener(['history','movements','parts'], "click",change_block);

 
  





Event.addListener('clean_table_filter_show2', "click",show_filter,2);
 Event.addListener('clean_table_filter_hide2', "click",hide_filter,2);

 var oACDS2 = new YAHOO.util.FunctionDataSource(mygetTerms);
 oACDS2.queryMatchContains = true;
  oACDS2.table_id=2;
 var oAutoComp2 = new YAHOO.widget.AutoComplete("f_input2","f_container2", oACDS2);
 oAutoComp2.minQueryLength = 0; 

var ids =Array("restrictions_all_transactions","restrictions_oip_transactions","restrictions_out_transactions","restrictions_in_transactions","restrictions_audit_transactions","restrictions_move_transactions") ;
Event.addListener(ids, "click", change_transaction_type);

//get_warehouse_transaction_numbers(Dom.get('v_calpop1t').value,Dom.get('v_calpop2t').value)
get_warehouse_element_transaction_numbers('all',Dom.get('v_calpop1t').value,Dom.get('v_calpop2t').value)
get_warehouse_element_transaction_numbers('out',Dom.get('v_calpop1t').value,Dom.get('v_calpop2t').value)
get_warehouse_element_transaction_numbers('in',Dom.get('v_calpop1t').value,Dom.get('v_calpop2t').value)
get_warehouse_element_transaction_numbers('move',Dom.get('v_calpop1t').value,Dom.get('v_calpop2t').value)
get_warehouse_element_transaction_numbers('audit',Dom.get('v_calpop1t').value,Dom.get('v_calpop2t').value)
get_warehouse_element_transaction_numbers('oip',Dom.get('v_calpop1t').value,Dom.get('v_calpop2t').value)


cal2 = new YAHOO.widget.Calendar("cal2","cal2Container", { title:"<?php echo _('Choose a date')?>:", close:true } );
 
 cal2.update=updateCal;
 cal2.id='2';
 cal2.render();
 cal2.update();
 cal2.selectEvent.subscribe(handleSelect, cal2, true); 
 cal1 = new YAHOO.widget.Calendar("cal1","cal1Container", { title:"<?php echo _('Choose a date')?>:", close:true } );
 cal1.update=updateCal;
 cal1.id='1';
 cal1.render();
 cal1.update();
 cal1.selectEvent.subscribe(handleSelect, cal1, true); 


cal2t = new YAHOO.widget.Calendar("cal2t","cal2tContainer", { title:"<?php echo _('Choose a date')?>:", close:true } );
 cal2t.update=updateCal;
 cal2t.id='2t';
 cal2t.render();
 cal2t.update();
 cal2t.selectEvent.subscribe(handleSelect, cal2t, true); 
 cal1t = new YAHOO.widget.Calendar("cal1t","cal1tContainer", { title:"<?php echo _('Choose a date')?>:", close:true } );
 cal1t.update=updateCal;
 cal1t.id='1t';
 cal1t.render();
 cal1t.update();
 cal1t.selectEvent.subscribe(handleSelect, cal1t, true);  




 
 Event.addListener("calpop1", "click", cal1.show, cal1, true);
 Event.addListener("calpop2", "click", cal2.show, cal2, true);
Event.addListener("calpop1t", "click", cal1t.show, cal1t, true);
 Event.addListener("calpop2t", "click", cal2t.show, cal2t, true);

 Event.addListener("submit_interval", "click", change_interval,'');
 Event.addListener("clear_interval", "click", clear_interval,'');
Event.addListener("submit_intervalt", "click", change_interval,'t');
 Event.addListener("clear_intervalt", "click", clear_interval,'t');
 
 
  YAHOO.util.Event.addListener('hide_stock_history_chart', "click",hide_stock_history_chart);
   YAHOO.util.Event.addListener('show_stock_history_chart', "click",show_stock_history_chart);

var ids =Array("stock_history_type_month","stock_history_type_week","stock_history_type_day") ;
Event.addListener(ids, "click", change_snapshot_granularity);



 }

YAHOO.util.Event.onDOMReady(init);


YAHOO.util.Event.onContentReady("rppmenu2", function () {
	 var oMenu = new YAHOO.widget.ContextMenu("rppmenu2", {trigger:"rtext_rpp2" });
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
    });

YAHOO.util.Event.onContentReady("filtermenu2", function () {
	 var oMenu = new YAHOO.widget.ContextMenu("filtermenu2", {trigger:"filter_name2"});
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
    });
    
YAHOO.util.Event.onContentReady("rppmenu1", function () {
	 var oMenu = new YAHOO.widget.ContextMenu("rppmenu1", {trigger:"rtext_rpp1" });
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
    });

YAHOO.util.Event.onContentReady("filtermenu1", function () {
	 var oMenu = new YAHOO.widget.ContextMenu("filtermenu1", {  trigger: "filter_name1"  });
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
    });  

 YAHOO.util.Event.onContentReady("rppmenu0", function () {
	 var oMenu = new YAHOO.widget.ContextMenu("rppmenu0", {trigger:"rtext_rpp0" });
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
    });

YAHOO.util.Event.onContentReady("filtermenu0", function () {
	 var oMenu = new YAHOO.widget.ContextMenu("filtermenu0", {  trigger: "filter_name0"  });
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
    });  
    
