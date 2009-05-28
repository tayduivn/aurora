<?
include_once('../common.php');
?>


YAHOO.namespace ("contacts"); 

   var change_view=function(e){
	var table=tables['table0'];
	var tipo=this.id;

	if(table.view!=tipo){
	    table.hideColumn('id');
	    table.hideColumn('name');
	    table.hideColumn('email');
	    table.hideColumn('location');
	    table.hideColumn('address');
	    table.hideColumn('town');
	    table.hideColumn('postcode');
	    table.hideColumn('region');
	    table.hideColumn('country');
	    table.hideColumn('telephone');
	    table.hideColumn('mobile');
	    table.hideColumn('fax');
	    table.hideColumn('company');
	    if(tipo=='address'){
		table.showColumn('id');
		  table.showColumn('address');
		  table.showColumn('town');
		  table.showColumn('postcode');
		  table.showColumn('region');
		  table.showColumn('country');
		  Dom.get('address').className='selected';
	
	    }
	    if(tipo=='general'){
	
		table.showColumn('id');
		table.showColumn('name');
		table.showColumn('email');
		table.showColumn('location');


	    }
	     if(tipo=='telephone'){
		table.showColumn('id');
		table.showColumn('name');
		table.showColumn('telephone');
		table.showColumn('mobile');
		table.showColumn('fax');

	    }
	    if(tipo=='company'){
		table.showColumn('id');
		table.showColumn('name');
		table.showColumn('company');
	    }

	    

	
	Dom.get(table.view).className="";
	Dom.get(tipo).className="selected";
	table.view=tipo
	YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=contacts-view&value=' + escape(tipo) );
	}
  }



   YAHOO.util.Event.addListener(window, "load", function() {
    tables = new function() {

 
 //START OF THE TABLE=========================================================================================================================

		var tableid=0; // Change if you have more the 1 table
	    var tableDivEL="table"+tableid;



	    var ContactsColumnDefs = [
				       {key:"id", label:"<?=_('ID')?>",width:60,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"name", label:"<?=_('Contact Name')?>",<?=( ( $_SESSION['state']['contacts']['view']=='general' or  $_SESSION['state']['contacts']['view']=='contact' ) ?'':'hidden:true,')?> width:250,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"location", label:"<?=_('Location')?>",<?=($_SESSION['state']['contacts']['view']=='general'?'':'hidden:true,')?> width:230,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       
				       ,{key:"email", label:"<?=_('Email')?>",<?=(($_SESSION['state']['contacts']['view']=='general' )?'':'hidden:true,')?>sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"telephone", label:"<?=_('Telephone')?>",<?=($_SESSION['state']['contacts']['view']=='telephone'?'':'hidden:true,')?>sortable:false,className:"aright"}
				       ,{key:"mobile", label:"<?=_('Mobile')?>",<?=($_SESSION['state']['contacts']['view']=='telephone'?'':'hidden:true,')?>sortable:false,className:"aright"}
				       ,{key:"fax", label:"<?=_('Fax')?>",<?=($_SESSION['state']['contacts']['view']=='telephone'?'':'hidden:true,')?>sortable:false,className:"aright"}

				       ,{key:"address", label:"<?=_('Main Address')?>",<?=($_SESSION['state']['contacts']['view']=='address'?'':'hidden:true,')?>sortable:true,className:"aright"}
				       ,{key:"town", label:"<?=_('Town')?>",<?=($_SESSION['state']['contacts']['view']=='address'?'':'hidden:true,')?>sortable:true,className:"aright"}
				       ,{key:"postcode", label:"<?=_('Postal Code')?>",<?=($_SESSION['state']['contacts']['view']=='address'?'':'hidden:true,')?>sortable:true,className:"aright"}
				       ,{key:"region", label:"<?=_('Region')?>",<?=($_SESSION['state']['contacts']['view']=='address'?'':'hidden:true,')?>sortable:true,className:"aright"}
				       ,{key:"country", label:"<?=_('Country')?>",<?=($_SESSION['state']['contacts']['view']=='address'?'':'hidden:true,')?>sortable:true,className:"aright"}
				       ,{key:"company", label:"<?=_('Company')?>",<?=($_SESSION['state']['contacts']['view']=='company'?'':'hidden:true,')?>sortable:true,className:"aright"}

				     
				       

					 ];
	    //?tipo=contacts&tid=0"
	    this.dataSource0 = new YAHOO.util.DataSource("ar_contacts.php?tipo=contacts");
	    this.dataSource0.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.dataSource0.connXhrMode = "queueRequests";
	    this.dataSource0.responseSchema = {
		resultsList: "resultset.data", 
		metaFields: {
		    rowsPerPage:"resultset.records_perpage",
		    rtext:"resultset.rtext",
		    sort_key:"resultset.sort_key",
		    sort_dir:"resultset.sort_dir",
		    tableid:"resultset.tableid",
		    filter_msg:"resultset.filter_msg",
		    totalRecords: "resultset.total_records" // Access to value in the server response
		},
		
		
		fields: [
			 'id',
			 'name',
			 'location',
			 'email',"telephone","mobile","fax","address","town","postcode","region","country"
			 ]};
	    //__You shouls not change anything from here

	    //this.dataSource.doBeforeCallback = mydoBeforeCallback;



	    this.table0 = new YAHOO.widget.DataTable(tableDivEL, ContactsColumnDefs,
								   this.dataSource0
								 , {
								     // sortedBy: {key:"<?=$_SESSION['tables']['contacts_list'][0]?>", dir:"<?=$_SESSION['tables']['contacts_list'][1]?>"},
								     renderLoopSize: 50,generateRequest : myRequestBuilder
								       ,paginator : new YAHOO.widget.Paginator({
									      rowsPerPage    : <?=$_SESSION['state']['contacts']['table']['nr']?>,containers : 'paginator0', 
 									      pageReportTemplate : '(<?=_('Page')?> {currentPage} <?=_('of')?> {totalPages})',
									      previousPageLinkLabel : "<",
 									      nextPageLinkLabel : ">",
 									      firstPageLinkLabel :"<<",
 									      lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500]
									      ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info0'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"



									  })
								     
								     ,sortedBy : {
									 key: "<?=$_SESSION['state']['contacts']['table']['order']?>",
									 dir: "<?=$_SESSION['state']['contacts']['table']['order_dir']?>"
								     },
								     dynamicData : true

								  }
								   
								 );
	    
	    this.table0.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table0.doBeforeSortColumn = mydoBeforeSortColumn;
	    this.table0.doBeforePaginatorChange = mydoBeforePaginatorChange;

		    
		    
	    this.table0.view='<?=$_SESSION['state']['contacts']['view']?>';

	    this.table0.filter={key:'<?=$_SESSION['state']['contacts']['table']['f_field']?>',value:'<?=$_SESSION['state']['contacts']['table']['f_value']?>'};

	    //   YAHOO.util.Event.addListener('f_input', "keyup",myFilterChangeValue,{table:this.table0,datasource:this.dataSource})
			 
	    
	    //	    var Dom   = YAHOO.util.Dom;
	    //alert(Dom.get('f_input'));

	    YAHOO.util.Event.addListener('yui-pg0-0-page-report', "click",myRowsPerPageDropdown)
	
	};
    });

    
    function init(){

    var Dom = YAHOO.util.Dom;
    var Event = YAHOO.util.Event; 

var ids=['general','company','address','telephone'];
YAHOO.util.Event.addListener(ids, "click",change_view);

 var oACDS = new YAHOO.util.FunctionDataSource(mygetTerms);
 oACDS.queryMatchContains = true;
 var oAutoComp = new YAHOO.widget.AutoComplete("f_input0","f_container", oACDS);
 oAutoComp.minQueryLength = 0; 











 


    
}




YAHOO.util.Event.onDOMReady(init);

YAHOO.util.Event.onContentReady("rppmenu", function () {
	 var oMenu = new YAHOO.widget.Menu("rppmenu", { context:["rtext_rpp0","tl", "tr"]  });
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
	 YAHOO.util.Event.addListener("rtext_rpp0", "click", oMenu.show, null, oMenu);
    });

YAHOO.util.Event.onContentReady("filtermenu", function () {
	 var oMenu = new YAHOO.widget.Menu("filtermenu", { context:["filter_name0","tr", "br"]  });
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
	 YAHOO.util.Event.addListener("filter_name0", "click", oMenu.show, null, oMenu);
    });