<?php
include_once('common.php');
?>
    var Dom   = YAHOO.util.Dom;
var dialog_new_list;
    
function change_block(){
ids=['details','campaigns','orders','customers','email_remainder'];
block_ids=['block_details','block_campaigns','block_orders','block_customers','block_email_remainder'];
Dom.setStyle(block_ids,'display','none');
Dom.setStyle('block_'+this.id,'display','');
Dom.removeClass(ids,'selected');
Dom.addClass(this,'selected');

	YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=deal-view&value='+this.id ,{});

}

YAHOO.util.Event.addListener(window, "load", function() {
    tables = new function() {



	      
		      var tableid=0;
		      var tableDivEL="table"+tableid;
		      var ColumnDefs = [
					{key:"order", label:"<?php echo _('Number')?>", width:90,className:"aleft", sortable:true,sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
					
				      ,{key:"customer_name", label:"<?php echo _('Customer')?>", width:220, sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
					,{key:"date", label:"<?php echo _('Date')?>", sortable:true, width:100,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
					//,{key:"dispatched", label:"<?php echo _('Dispatched')?>",width:80,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				//	,{key:"undispatched", label:"<?php echo _('No Send')?>", width:80, sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					];
		      
		      
		      this.dataSource0 = new YAHOO.util.DataSource("ar_orders.php?tipo=withdeal&deal_key="+Dom.get('deal_key').value+"&tableid="+tableid);
		     // alert("ar_orders.php?tipo=withproduct&product_pid="+Dom.get('product_pid').value+"&tableid="+tableid)
		      
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
				   "id","order","customer_name","date","dispatched","undispatched"
				   ]};
		      
		      this.table0 = new YAHOO.widget.DataTable(tableDivEL, ColumnDefs,
							       this.dataSource0, {
								   //draggableColumns:true,
								   renderLoopSize: 50,generateRequest : myRequestBuilder
								   ,paginator : new YAHOO.widget.Paginator({
									   rowsPerPage:<?php echo (!$_SESSION['state']['deal']['orders']['nr']?25:$_SESSION['state']['deal']['orders']['nr'] )?>,containers : 'paginator0', 
									 pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',
									 previousPageLinkLabel : "<",
									 nextPageLinkLabel : ">",
									 firstPageLinkLabel :"<<",
									 lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500],alwaysVisible:false
									 ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info0'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
								       })
								   
								   ,sortedBy : {
								      key: "<?php echo $_SESSION['state']['deal']['orders']['order']?>",
								       dir: "<?php echo $_SESSION['state']['deal']['orders']['order_dir']?>"
								   }
								   ,dynamicData : true
								   
							     }
							       );
		      this.table0.handleDataReturnPayload =myhandleDataReturnPayload;
		      this.table0.doBeforeSortColumn = mydoBeforeSortColumn;
		      this.table0.doBeforePaginatorChange = mydoBeforePaginatorChange;
		       this.table0.table_id=tableid;
     	this.table0.subscribe("renderEvent", myrenderEvent);
		    	    this.table0.filter={key:'<?php echo$_SESSION['state']['deal']['orders']['f_field']?>',value:'<?php echo$_SESSION['state']['deal']['orders']['f_value']?>'};

		      
		   
		      var tableid=1;
		      var tableDivEL="table"+tableid;
		      
		      var ColumnDefs = [
									       {key:"id", label:"<?php echo _('Id')?>",width:45,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}

					,{key:"name", label:"<?php echo _('Customer')?>",width:270, sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
						,{key:"location", label:"<?php echo _('Location')?>",width:250, sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}

					,{key:"orders", label:"<?php echo _('Orders')?>",width:70, sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				//      ,{key:"dispatched", label:"<?php echo _('Disp')?>",width:65, sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				//	,{key:"to_dispatch", label:"<?php echo _('To Disp')?>",width:65, sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
			//		,{key:"nodispatched", label:"<?php echo _('No Disp')?>", width:65, sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
			//		,{key:"charged", label:"<?php echo _('Charged')?>", width:80, sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					];
		    
		      
		      this.dataSource1 = new YAHOO.util.DataSource("ar_assets.php?tipo=customers_who_use_deal&deal_key="+Dom.get('deal_key').value+"&tableid="+tableid);
	//alert("ar_assets.php?tipo=customers_who_order_product&product_pid="+Dom.get('product_pid').value+"&tableid="+tableid)
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
				  "name","dispatched","nodispatched","charged","to_dispatch","orders","location","id"
				   ]};
		      
		     
		      
		      this.table1 = new YAHOO.widget.DataTable(tableDivEL, ColumnDefs,
							       this.dataSource1, {
								   //draggableColumns:true,
								   renderLoopSize: 50,generateRequest : myRequestBuilder
								 ,paginator : new YAHOO.widget.Paginator({
									 rowsPerPage:<?php echo (!$_SESSION['state']['deal']['orders']['nr']?25:$_SESSION['state']['deal']['customers']['nr'] )?>,containers : 'paginator1', 
									 pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',
									 previousPageLinkLabel : "<",
									 nextPageLinkLabel : ">",
									 firstPageLinkLabel :"<<",
									 lastPageLinkLabel :">>",alwaysVisible:false
									 ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info1'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
								     })
								   
								   ,sortedBy : {
								      key: "<?php echo $_SESSION['state']['deal']['customers']['order']?>",
								       dir: "<?php echo $_SESSION['state']['deal']['customers']['order_dir']?>"
								   }
								   ,dynamicData : true
								 
							       }
							       );
		      this.table1.handleDataReturnPayload =myhandleDataReturnPayload;
		      this.table1.doBeforeSortColumn = mydoBeforeSortColumn;
		      this.table1.doBeforePaginatorChange = mydoBeforePaginatorChange;
 			this.table1.table_id=tableid;
     	this.table1.subscribe("renderEvent", myrenderEvent);
		  		this.table1.filter={key:'<?php echo$_SESSION['state']['deal']['customers']['f_field']?>',value:'<?php echo$_SESSION['state']['deal']['customers']['f_value']?>'};



	
	
	
	
	
	
	
	
	
        var tableid=10; 
	    var tableDivEL="table"+tableid;

	   
	    var ColumnDefs = [
	    				    {key:"id", label:"",hidden:true,action:"none",isPrimaryKey:true}
	    				    ,{key:"name", label:"<?php echo _('Name')?>",width:170,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
	                        ,{key:"palette", label:"<?php echo _('Palette')?>",width:300,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
	                        ,{key:"used",label:"<?php echo _('In Use')?>", width:40,className:"aright"}

			                ,{key:"delete",label:"", width:20,className:"aleft",action:'delete',object:'color_scheme'}
			  			];
			       
		request="ar_edit_marketing.php?tipo=color_schemes&store_key="+Dom.get('store_key').value+"&tableid="+tableid+"&sf=0&email_content_key="+Dom.get('email_content_key').value	       


		this.dataSource10 = new YAHOO.util.DataSource(request);
	 this.dataSource10.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.dataSource10.connXhrMode = "queueRequests";
	    	    this.dataSource10.table_id=tableid;

	    this.dataSource10.responseSchema = {
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
			 "id","name","palette","delete","used"
			 ]};

	    this.table10 = new YAHOO.widget.DataTable(tableDivEL, ColumnDefs,
								   this.dataSource10
								 , {
								     renderLoopSize: 50,generateRequest : myRequestBuilder
								      ,paginator : new YAHOO.widget.Paginator({
									      rowsPerPage:20,containers : 'paginator10', 
 									      pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',
									      previousPageLinkLabel : "<",
 									      nextPageLinkLabel : ">",
 									      firstPageLinkLabel :"<<",
 									      lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500],alwaysVisible:false
									      ,template : "{PreviousPageLink}<strong id='paginator_info10'>{CurrentPageReport}</strong>{NextPageLink}"
									  })
								     
								     ,sortedBy : {
									 key: "name",
									 dir: ""
								     },
								     dynamicData : true

								  }
								   
								 );
	    
	    this.table10.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table10.doBeforeSortColumn = mydoBeforeSortColumn;
	  this.table10.subscribe("cellMouseoverEvent", highlightEditableCell);
	    this.table10.subscribe("cellMouseoutEvent", unhighlightEditableCell);
	        this.table10.subscribe("cellClickEvent", onCellClick);      	    
     
 this.table10.table_id=tableid;
     this.table10.subscribe("renderEvent", myrenderEvent);

	    this.table10.doBeforePaginatorChange = mydoBeforePaginatorChange;
	    this.table10.filter={key:'name',value:''};



  var tableid=11; 
	    var tableDivEL="table"+tableid;

	   
	    var ColumnDefs = [
	    				    {key:"id", label:"",hidden:true,action:"none",isPrimaryKey:true}
	    				    ,{key:"name", label:"<?php echo _('Name')?>",width:120,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
	                        ,{key:"image", label:"<?php echo _('Image')?>",width:610,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
	                        ,{key:"used",label:"<?php echo _('In Use')?>", width:40,className:"aright"}

			                ,{key:"delete",label:"", width:20,className:"aleft",action:'delete',object:'template_header_image'}
			  			];
			
request="ar_edit_marketing.php?tipo=email_template_header_images&store_key="+Dom.get('store_key').value+"&tableid="+tableid+"&sf=0&email_content_key="+Dom.get('email_content_key').value

		this.dataSource11 = new YAHOO.util.DataSource(request);
	 this.dataSource11.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.dataSource11.connXhrMode = "queueRequests";
	    	    this.dataSource11.table_id=tableid;

	    this.dataSource11.responseSchema = {
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
			 "id","name","image","delete","used"
			 ]};

	    this.table11 = new YAHOO.widget.DataTable(tableDivEL, ColumnDefs,
								   this.dataSource11
								 , {
								     renderLoopSize: 50,generateRequest : myRequestBuilder
								      ,paginator : new YAHOO.widget.Paginator({
									      rowsPerPage:20,containers : 'paginator11', 
 									      pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',
									      previousPageLinkLabel : "<",
 									      nextPageLinkLabel : ">",
 									      firstPageLinkLabel :"<<",
 									      lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500],alwaysVisible:false
									      ,template : "{PreviousPageLink}<strong id='paginator_info11'>{CurrentPageReport}</strong>{NextPageLink}"
									  })
								     
								     ,sortedBy : {
									 key: "name",
									 dir: ""
								     },
								     dynamicData : true

								  }
								   
								 );
	    
	    this.table11.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table11.doBeforeSortColumn = mydoBeforeSortColumn;
	  this.table11.subscribe("cellMouseoverEvent", highlightEditableCell);
	    this.table11.subscribe("cellMouseoutEvent", unhighlightEditableCell);
	        this.table11.subscribe("cellClickEvent", onCellClick);      	    
     

 this.table11.table_id=tableid;
     this.table11.subscribe("renderEvent", myrenderEvent);
	    this.table11.doBeforePaginatorChange = mydoBeforePaginatorChange;
	    this.table11.filter={key:'name',value:''};





  var tableid=12; 
	    var tableDivEL="table"+tableid;

	   
	    var ColumnDefs = [
	    				    {key:"id", label:"",hidden:true,action:"none",isPrimaryKey:true}
	    				    ,{key:"name", label:"<?php echo _('Name')?>",width:120,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
	                        ,{key:"image", label:"<?php echo _('Image')?>",width:610,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
	                        ,{key:"used",label:"<?php echo _('In Use')?>", width:40,className:"aright"}

			                ,{key:"delete",label:"", width:20,className:"aleft",action:'delete',object:'template_postcard'}
			  			];
			       
		this.dataSource12 = new YAHOO.util.DataSource("ar_edit_marketing.php?tipo=email_template_postcards&store_key="+Dom.get('store_key').value+"&tableid="+tableid+"&sf=0&email_content_key="+Dom.get('email_content_key').value);
	 this.dataSource12.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.dataSource12.connXhrMode = "queueRequests";
	    	    this.dataSource12.table_id=tableid;

	    this.dataSource12.responseSchema = {
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
			 "id","name","image","delete","used"
			 ]};

	    this.table12 = new YAHOO.widget.DataTable(tableDivEL, ColumnDefs,
								   this.dataSource12
								 , {
								     renderLoopSize: 50,generateRequest : myRequestBuilder
								      ,paginator : new YAHOO.widget.Paginator({
									      rowsPerPage:20,containers : 'paginator12', 
 									      pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',
									      previousPageLinkLabel : "<",
 									      nextPageLinkLabel : ">",
 									      firstPageLinkLabel :"<<",
 									      lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500],alwaysVisible:false
									      ,template : "{PreviousPageLink}<strong id='paginator_info12'>{CurrentPageReport}</strong>{NextPageLink}"
									  })
								     
								     ,sortedBy : {
									 key: "name",
									 dir: ""
								     },
								     dynamicData : true

								  }
								   
								 );
	    
	    this.table12.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table12.doBeforeSortColumn = mydoBeforeSortColumn;
	  this.table12.subscribe("cellMouseoverEvent", highlightEditableCell);
	    this.table12.subscribe("cellMouseoutEvent", unhighlightEditableCell);
	        this.table12.subscribe("cellClickEvent", onCellClick);      	    
     
 this.table12.table_id=tableid;
     this.table12.subscribe("renderEvent", myrenderEvent);


	    this.table12.doBeforePaginatorChange = mydoBeforePaginatorChange;
	    this.table12.filter={key:'name',value:''};

	
	
	
	
	
	    
	    

	
	};
    });




function update_objects_table(){

}


function change_new_email_campaign_type(){
types=Dom.getElementsByClassName('email_campaign_type', 'button', 'email_campaign_type_buttons')
Dom.removeClass(types,'selected');
Dom.addClass(this,'selected');
Dom.get('email_campaign_type').value=this.id

}

function save_new_email_campaign(){


var store_key=Dom.get('store_key').value;
var email_campaign_name=Dom.get('email_campaign_name').value;
var email_campaign_type=Dom.get('email_campaign_type').value;
	
	switch ( Dom.get('email_campaign_type').value ) {
		case 'select_text_email':
			email_campaign_content_type='Plain';
			break;
		
		case'select_html_email':
		email_campaign_content_type='HTML';
			break;
		
		default:
				email_campaign_content_type='HTML Template';
			break;
	}
	
	
	var data=new Object;
	data={'email_campaign_name':email_campaign_name,'email_campaign_content_type':email_campaign_content_type,'store_key':store_key,'email_campaign_type':'Reminder','deal_key':Dom.get('deal_key').value}
	 var json_value = my_encodeURIComponent(YAHOO.lang.JSON.stringify(data));
	
var request='ar_edit_marketing.php?tipo=create_email_campaign&values='+json_value

 YAHOO.util.Connect.asyncRequest('POST',request ,{
	    success:function(o) {
	  //alert(o.responseText);
		var r =  YAHOO.lang.JSON.parse(o.responseText);

		if(r.state==200){
                 location.href='deal.php.php?id='+Dom.get('deal_key').value
	
		}else{
            Dom.setStyle('new_email_campaign_msg_tr','display','');
            Dom.get('new_email_campaign_msg').innerHTML=r.msg;
	    }
	    }
	    });


}


function cancel_new_email_campaign(){
Dom.get('email_campaign_name').value='';
Dom.get('email_campaign_type').value='select_html_from_template_email';
types=Dom.getElementsByClassName('email_campaign_type', 'button', 'email_campaign_type_buttons')
Dom.removeClass(types,'selected');
Dom.addClass('select_html_from_template_email','selected');
Dom.setStyle('new_email_campaign_msg_tr','display','none');
            Dom.get('new_email_campaign_msg').innerHTML='';
dialog_new_email_campaign.hide();
}


function show_dialog_new_email_campaign(){


	region1 = Dom.getRegion(this); 
    region2 = Dom.getRegion('dialog_new_email_campaign'); 
	var pos =[region1.right-region1.width,region1.bottom]
	Dom.setXY('dialog_new_email_campaign', pos);


dialog_new_email_campaign.show()
}


function init(){
ids=['details','campaigns','orders','customers','email_remainder'];

 Event.addListener(ids, "click",change_block);




 dialog_new_email_campaign = new YAHOO.widget.Dialog("dialog_new_email_campaign", {  visible : false,close:true,underlay: "none",draggable:false});
dialog_new_email_campaign.render();

Event.addListener('show_create_email_remainder', "click", show_dialog_new_email_campaign);
 
Event.addListener("save_new_email_campaign", "click", save_new_email_campaign);
Event.addListener("cancel_new_email_campaign", "click", cancel_new_email_campaign);

Event.addListener(["select_text_email","select_html_from_template_email","select_html_email"], "click", change_new_email_campaign_type);


init_search('products_store');

 var oACDS = new YAHOO.util.FunctionDataSource(mygetTerms);
 oACDS.queryMatchContains = true;
   oACDS.table_id=0;
 var oAutoComp = new YAHOO.widget.AutoComplete("f_input0","f_container0", oACDS);
 oAutoComp.minQueryLength = 0; 


 var oACDS1 = new YAHOO.util.FunctionDataSource(mygetTerms);
 oACDS1.queryMatchContains = true;
   oACDS1.table_id=1;
 var oAutoComp1 = new YAHOO.widget.AutoComplete("f_input1","f_container1", oACDS1);
 oAutoComp1.minQueryLength = 0; 


    Event.addListener(['details','customers','orders','timeline','sales', 'web_site'], "click",change_block);


 YAHOO.util.Event.addListener('clean_table_filter_show0', "click",show_filter,0);
 YAHOO.util.Event.addListener('clean_table_filter_hide0', "click",hide_filter,0);
  YAHOO.util.Event.addListener('clean_table_filter_show1', "click",show_filter,1);
 YAHOO.util.Event.addListener('clean_table_filter_hide1', "click",hide_filter,1);
  YAHOO.util.Event.addListener('clean_table_filter_show2', "click",show_filter,2);
 YAHOO.util.Event.addListener('clean_table_filter_hide2', "click",hide_filter,2);

}

YAHOO.util.Event.onDOMReady(init);

 YAHOO.util.Event.onContentReady("rppmenu2", function () {
	 var oMenu = new YAHOO.widget.ContextMenu("rppmenu2", {trigger:"rtext_rpp2" });
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
    });

YAHOO.util.Event.onContentReady("filtermenu2", function () {
	 var oMenu = new YAHOO.widget.ContextMenu("filtermenu2", {  trigger: "filter_name2"  });
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

