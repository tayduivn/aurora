<?php
include_once('common.php');

?>
var Event = YAHOO.util.Event;
var Dom   = YAHOO.util.Dom;

//var can_add_department=false;
var description_num_changed=0;
var description_warnings= new Object();
var description_errors= new Object();
var category_show_options=['Yes','No'];
var category_show_name={'Yes':'Yes','No':'No'};

var scope='category';
var scope_edit_ar_file='ar_edit_categories.php';
var scope_key_name='category_key';
var scope_key=0;

var parent='category';
var parent_key_name='id';
//var parent_key=<?php //echo $_REQUEST['category_key']?>;


//var editing='<?php //echo $_SESSION['state']['product_categories']['edit']?>';  


var validate_scope_data={
'category':{

    'name':{'changed':false,'validated':true,'required':true,'group':1,'type':'item'
	    ,'validation':[{'regexp':"[a-z\\d]+",'invalid_msg':'<?php echo _('Invalid Category Name')?>'}],'name':'Category_Name'
	    ,'ar':false,'ar_request':false}
   }
  
};


var validate_scope_metadata={'category':{'type':'edit','ar_file':'ar_edit_categories.php','key_name':'category_key','key':0}};
				

/*function validate_id(query){
 validate_general('company_staff','id',unescape(query));
}*/
function validate_name(query){
//alert("********");alert(query);
 validate_general('category','name',unescape(query));
}
function validate_subcategory_name(query){
 validate_general('subcategory','subcategory_name',unescape(query));
}
function reset_new_category(){
 reset_edit_general('category');
}

function reset_edit_category(){
    reset_edit_general('category')
}
function save_edit_subcategory(){
    save_edit_general('subcategory');
}
function reset_edit_subcategory(){
    reset_edit_general('subcategory')
}
function reset_new_subcategory(){
 reset_edit_general('subcategory');
}

/*function save_new_category(){
 save_edit_general('category');
}
function save_edit_category(){
    save_edit_general('category');
}*/




function post_item_updated_actions(branch,r){
key=r.key;
newvalue=r.newvalue;
if(key=='name'){
     Dom.get('title_name').innerHTML=newvalue;}


 
 var table=tables.table1;
 var datasource=tables.dataSource1;
 var request='';
 datasource.sendRequest(request,table.onDataReturnInitializeTable, table); 
var table_id=1


    var table=tables['table'+table_id];
    var datasource=tables['dataSource'+table_id];

  
    var request='&tableid='+table_id+'&sf=0';
    datasource.sendRequest(request,table.onDataReturnInitializeTable, table);  
 
}



function post_create_actions(branch){

var table=tables.table1;
 var datasource=tables.dataSource1;
 var request='';
 datasource.sendRequest(request,table.onDataReturnInitializeTable, table); 
 
 var table=tables.table0;
 var datasource=tables.dataSource0;
 var request='';
 datasource.sendRequest(request,table.onDataReturnInitializeTable, table); 
}





YAHOO.util.Event.addListener(window, "load", function() {
function category_show_formatter(el, oRecord, oColumn, oData){
el.innerHTML =category_show_name[oData];
}
    tables = new function() {
  var tableid=0; // Change if you have more the 1 table
	    var tableDivEL="table"+tableid;
	    var OrdersColumnDefs = [ 
				    {key:"id", label:"<?php echo _('Key')?>", width:20,sortable:false,isPrimaryKey:true,hidden:true} 
				    ,{key:"go",label:'',width:20,}
				 
				    ,{key:"name", label:"<?php echo _('Name')?>", width:150,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}, editor: new YAHOO.widget.TextboxCellEditor({asyncSubmitter: CellEdit}),object:'subcategory' }
				    ,{key:"label", label:"<?php echo _('Label')?>", width:150,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}, editor: new YAHOO.widget.TextboxCellEditor({asyncSubmitter: CellEdit}),object:'subcategory' }
				,{key:"new_subject",formatter:category_show_formatter,label:"<?php echo _('New Subject')?>",className:"aleft"
				, editor:new YAHOO.widget.RadioCellEditor({radioOptions:category_show_options,disableBtns:true,asyncSubmitter: CellEdit}),object:'subcategory'}
,{key:"public_new_subject",formatter:category_show_formatter,label:"<?php echo _('Public New Subject')?>",className:"aleft"
				, editor:new YAHOO.widget.RadioCellEditor({radioOptions:category_show_options,disableBtns:true,asyncSubmitter: CellEdit}),object:'subcategory'}
,{key:"public_edit",formatter:category_show_formatter,label:"<?php echo _('Public Edit')?>",className:"aleft"
				, editor:new YAHOO.widget.RadioCellEditor({radioOptions:category_show_options,disableBtns:true,asyncSubmitter: CellEdit}),object:'subcategory'}
                                      ,{key:"delete", label:"", width:100,sortable:false,className:"aleft",action:'delete',object:'subcategory'}
				    ,{key:"delete_type", label:"",hidden:true,isTypeKey:true}
				     ];

	    this.dataSource0 = new YAHOO.util.DataSource("ar_edit_categories.php?tipo=edit_customer_category_list");
	    this.dataSource0.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.dataSource0.connXhrMode = "queueRequests";
	    this.dataSource0.responseSchema = {
		resultsList: "resultset.data", 
		metaFields: {
rowsPerPage:"resultset.records_perpage",
		    rtext:"resultset.rtext",
		    rtext_rpp:"resultset.rtext_rpp",
		    sort_key:"resultset.sort_key",
		    sort_dir:"resultset.sort_dir",
		    tableid:"resultset.tableid",
		    filter_msg:"resultset.filter_msg",
		    totalRecords: "resultset.total_records"		},
		
		fields: [
			 'id','name','delete','delete_type','go','label','new_subject','public_edit','public_new_subject'
			 ]};
	    
	    this.table0 = new YAHOO.widget.DataTable(tableDivEL, OrdersColumnDefs,
						     this.dataSource0, {
							 //draggableColumns:true,
							   renderLoopSize: 50,generateRequest : myRequestBuilder
								       ,paginator : new YAHOO.widget.Paginator({
									      rowsPerPage:<?php echo$_SESSION['state']['categories']['table']['nr']?>,containers : 'paginator', 
 									      pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',
									      previousPageLinkLabel : "<",
 									      nextPageLinkLabel : ">",
 									      firstPageLinkLabel :"<<",
 									      lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500],alwaysVisible:false
									      ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info0'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
									  })
								     
							   ,sortedBy : {
							    key: "<?php echo$_SESSION['state']['categories']['table']['order']?>",
							     dir: "<?php echo$_SESSION['state']['categories']['table']['order_dir']?>"
								     }
							   ,dynamicData : true

						     }
						     );
	    this.table0.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table0.doBeforeSortColumn = mydoBeforeSortColumn;
	    this.table0.doBeforePaginatorChange = mydoBeforePaginatorChange;
       this.table0.table_id=tableid;
        this.table0.subscribe("renderEvent", myrenderEvent);


	  
	    this.table0.subscribe("cellMouseoverEvent", highlightEditableCell);
	    this.table0.subscribe("cellMouseoutEvent", unhighlightEditableCell);
	    this.table0.subscribe("cellClickEvent", onCellClick);





		/*
 var tableid=1; // Change if you have more the 1 table
	    var tableDivEL="table"+tableid;

	    var CustomersColumnDefs = [
				       {key:"date",label:"<?php echo _('Date')?>", width:200,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"author",label:"<?php echo _('Author')?>", width:70,sortable:true,formatter:this.customer_name,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       //     ,{key:"tipo", label:"<?php echo _('Type')?>", width:90,sortable:true,formatter:this.customer_name,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       //,{key:"diff_qty",label:"<?php echo _('Qty')?>", width:90,sortable:true,formatter:this.customer_name,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"abstract", label:"<?php echo _('Description')?>", width:370,sortable:true,formatter:this.customer_name,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ];
	    //?tipo=customers&tid=0"
	    
	    this.dataSource1 = new YAHOO.util.DataSource("ar_history.php?tipo=history&type=customer_categories&tableid=1");
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
			 "id"
			 ,"note"
			 ,'author','date','tipo','abstract','details'
			 ]};
	    
	    this.table1 = new YAHOO.widget.DataTable(tableDivEL, CustomersColumnDefs,
						     this.dataSource1
						     , {
							 renderLoopSize: 50,generateRequest : myRequestBuilder
							 ,paginator : new YAHOO.widget.Paginator({
								 rowsPerPage    : <?php echo$_SESSION['state']['categories']['history']['nr']?>,containers : 'paginator1', alwaysVisible:false,
								 pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',
								 previousPageLinkLabel : "<",
								 nextPageLinkLabel : ">",
								 firstPageLinkLabel :"<<",
								 lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500]
								 ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info1'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
							     })
							 
							 ,sortedBy : {
							    key: "<?php echo$_SESSION['state']['categories']['history']['order']?>",
							     dir: "<?php echo$_SESSION['state']['categories']['history']['order_dir']?>"
							 },
							 dynamicData : true
							 
						     }
						     
						     );
	    
	    this.table1.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table1.doBeforeSortColumn = mydoBeforeSortColumn;
	    this.table1.doBeforePaginatorChange = mydoBeforePaginatorChange;
       this.table1.table_id=tableid;
        this.table1.subscribe("renderEvent", myrenderEvent);

		    
		    
	    this.table1.filter={key:'<?php echo$_SESSION['state']['company']['history']['f_field']?>',value:'<?php echo$_SESSION['state']['company']['history']['f_value']?>'};

*/
	};
    });






function change_block(){
  

	Dom.get('d_description').style.display='none';
	Dom.get('d_subcategory').style.display='none';

	Dom.get('d_'+this.id).style.display='';
	
	Dom.removeClass(['description','subcategory'],'selected');
	Dom.addClass(this, 'selected');
	
	YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=categories_view&value='+this.id ,{});
}
function cancel_add_category(){
   reset_new_category();
  }
function cancel_add_subcategory(){
   reset_new_subcategory();
  }

function init(){
init_search('customers_store');
    var ids = ["description","subcategory"]; 
    YAHOO.util.Event.addListener(ids, "click", change_block);

//    YAHOO.util.Event.addListener('add_category', "click", show_add_category_dialog);
    YAHOO.util.Event.addListener('save_edit_category', "click", save_new_category);   
    YAHOO.util.Event.addListener('reset_edit_category', "click", cancel_add_category);

  
   
 /*   var staff_id_oACDS = new YAHOO.util.FunctionDataSource(validate_id);
    staff_id_oACDS.queryMatchContains = true;
    var staff_id_oAutoComp = new YAHOO.widget.AutoComplete("Company_Staff_Id","Company_Staff_Id_Container", staff_id_oACDS);
    staff_id_oAutoComp.minQueryLength = 0; 
    staff_id_oAutoComp.queryDelay = 0.1; */
    
     var category_name_oACDS = new YAHOO.util.FunctionDataSource(validate_name);
    category_name_oACDS.queryMatchContains = true;
    var category_name_oAutoComp = new YAHOO.widget.AutoComplete("Category_Name","Category_Name_Container", category_name_oACDS);
    category_name_oAutoComp.minQueryLength = 0; 
    category_name_oAutoComp.queryDelay = 0.1;


//  YAHOO.util.Event.addListener('add_subcategory', "click", show_add_subcategory_dialog);
    YAHOO.util.Event.addListener('save_edit_subcategory', "click", save_new_subcategory);
    YAHOO.util.Event.addListener('reset_edit_subcategory', "click", cancel_add_subcategory); 

   var subcategory_name_oACDS = new YAHOO.util.FunctionDataSource(validate_subcategory_name);
    subcategory_name_oACDS.queryMatchContains = true;
    var subcategory_name_oAutoComp = new YAHOO.widget.AutoComplete("Subcategory_Name","Subcategory_Name_Container", subcategory_name_oACDS);
    subcategory_name_oAutoComp.minQueryLength = 0; 
    subcategory_name_oAutoComp.queryDelay = 0.1; 


   

}

YAHOO.util.Event.onDOMReady(init);

YAHOO.util.Event.onContentReady("filtermenu0", function () {
	 var oMenu = new YAHOO.widget.ContextMenu("filtermenu0", {trigger:"filter_name0"});
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
	 
    });


YAHOO.util.Event.onContentReady("rppmenu0", function () {
	 rppmenu = new YAHOO.widget.ContextMenu("rppmenu0", {trigger:"rtext_rpp0" });
	 rppmenu.render();
	 rppmenu.subscribe("show", rppmenu.focus);
    });
