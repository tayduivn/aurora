<?include_once('../common.php')?>
YAHOO.util.Event.addListener(window, "load", function() {
    tables = new function() {

	    this.formatLang=  function(el, oRecord, oColumn, oData) {
		el.innerHTML = '<img src="art/flags/'+oRecord.getData("countrycode")+'.gif" alt="'+oRecord.getData("country")+'"> '+oData;
	    }
		this.userLink=  function(el, oRecord, oColumn, oData) {
		var url="user.php?id="+oRecord.getData("id");
		el.innerHTML = oData.link(url);
	    }	

	     //START OF THE TABLE=========================================================================================================================

	    var tableid=0; // Change if you have more the 1 table
	    var tableDivEL="table"+tableid;



	    var ColumnDefs = [
			         {key:"delete",label:"" ,width:16 ,hidden:true},
				   {key:"active",label:"" ,width:16 , editorOptions:{radioOptions:['<?=_('Activate')?>','<?=_('Desactivate')?>'],disableBtns:true} },
				   {key:"password",label:"" ,width:16 ,hidden:true},
				   {key:"handle", label:"<?=_('Handle')?>",width:60,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}},
				   {key:"name", label:"<?=_('Name')?>",sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}},
				   {key:"email", label:"<?=_('Email')?>",sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}},
				 {key:"lang", label:"<?=_('Language')?>",sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC},},
				   {key:"groups", label:"<?=_('Groups')?>",className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC},   }
				       ];

	    this.dataSource0 = new YAHOO.util.DataSource("ar_users.php?tipo=users&tableid=0");
	    this.dataSource0.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.dataSource0.connXhrMode = "queueRequests";
	    this.dataSource0.responseSchema = {
		resultsList: "resultset.data", 
		metaFields: {
		    rowsPerPage:"resultset.records_perpage",
		    sort_key:"resultset.sort_key",
		    sort_dir:"resultset.sort_dir",
		    tableid:"resultset.tableid",
		    filter_msg:"resultset.filter_msg",
		    totalRecords: "resultset.total_records" // Access to value in the server response
		},
		
		
		fields: [
			 "id","active","handle","name","email","lang","groups","password","delete"
			 ]};

	    this.table0 = new YAHOO.widget.DataTable(tableDivEL, ColumnDefs,
								   this.dataSource0
								 , {
								     // sortedBy: {key:"<?=$_SESSION['tables']['customers_list'][0]?>", dir:"<?=$_SESSION['tables']['customers_list'][1]?>"},
								     renderLoopSize: 50,generateRequest : myRequestBuilder
								    //    ,paginator : new YAHOO.widget.Paginator({
// 									      rowsPerPage    : <?=$_SESSION['state']['users']['user_list']['nr']?>,containers : 'paginator', 
//  									      pageReportTemplate : '(<?=_('Page')?> {currentPage} <?=_('of')?> {totalPages})',
// 									      previousPageLinkLabel : "<",
//  									      nextPageLinkLabel : ">",
//  									      firstPageLinkLabel :"<<",
//  									      lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500]
// 									      ,template : "{FirstPageLink}{PreviousPageLink}<strong>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
// 									  })
								     
								     ,sortedBy : {
									 key: "<?=$_SESSION['state']['users']['user_list']['order']?>",
									 dir: "<?=$_SESSION['state']['users']['user_list']['order_dir']?>"
								     },
								     dynamicData : true

								  }
								   
								 );
	    
	    this.table0.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table0.doBeforeSortColumn = mydoBeforeSortColumn;
	    //this.table0.doBeforePaginatorChange = mydoBeforePaginatorChange;
	    //this.table0.filter={key:'<?=$_SESSION['state']['users']['user_list']['f_field']?>',value:'<?=$_SESSION['state']['users']['user_list']['f_value']?>'};
	    //YAHOO.util.Event.addListener('yui-pg0-0-page-report', "click",myRowsPerPageDropdown);
	



	    
	    var tableid=1; // Change if you have more the 1 table
	    var tableDivEL="table"+tableid;
	    var ColumnDefs = [
			      {key:"id", label:"<?=_('Id')?>", width:40,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}},
			      {key:"name", label:"<?=_('Group')?>", sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}},
			      {key:"users", label:"<?=_('Users')?>", sortable:false,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
			      ];

	    this.dataSource1 = new YAHOO.util.DataSource("ar_users.php?tipo=groups&tableid=1");
	    this.dataSource1.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.dataSource1.connXhrMode = "queueRequests";
	    this.dataSource1.responseSchema = {
		resultsList: "resultset.data", 
		metaFields: {
		    rowsPerPage:"resultset.records_perpage",
		    sort_key:"resultset.sort_key",
		    sort_dir:"resultset.sort_dir",
		    tableid:"resultset.tableid",
		    filter_msg:"resultset.filter_msg",
		    totalRecords: "resultset.total_records" // Access to value in the server response
		},
		
		
		fields: [
		       "id","name","users"
			 ]};

	    this.table1 = new YAHOO.widget.DataTable(tableDivEL, ColumnDefs,
								   this.dataSource1
								 , {
								     // sortedBy: {key:"<?=$_SESSION['tables']['customers_list'][0]?>", dir:"<?=$_SESSION['tables']['customers_list'][1]?>"},
								     renderLoopSize: 50,generateRequest : myRequestBuilder
								    //    ,paginator : new YAHOO.widget.Paginator({
// 									      rowsPerPage    : <?=$_SESSION['state']['users']['groups']['nr']?>,containers : 'paginator', 
//  									      pageReportTemplate : '(<?=_('Page')?> {currentPage} <?=_('of')?> {totalPages})',
// 									      previousPageLinkLabel : "<",
//  									      nextPageLinkLabel : ">",
//  									      firstPageLinkLabel :"<<",
//  									      lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500]
// 									      ,template : "{FirstPageLink}{PreviousPageLink}<strong>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
// 									  })
								     
								     ,sortedBy : {
									 key: "<?=$_SESSION['state']['users']['groups']['order']?>",
									 dir: "<?=$_SESSION['state']['users']['groups']['order_dir']?>"
								     },
								     dynamicData : true

								  }
								   
								 );
	    
	    this.table1.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table1.doBeforeSortColumn = mydoBeforeSortColumn;
	    // this.table1.doBeforePaginatorChange = mydoBeforePaginatorChange;
	    // this.table1.filter={key:'<?=$_SESSION['state']['users']['user_list']['f_field']?>',value:'<?=$_SESSION['state']['users']['user_list']['f_value']?>'};
	    //YAHOO.util.Event.addListener('yui-pg0-0-page-report', "click",myRowsPerPageDropdown)



	};
    });




//  function init(){
//  var Dom   = YAHOO.util.Dom;


//  var oACDS = new YAHOO.util.FunctionDataSource(mygetTerms);
//  oACDS.queryMatchContains = true;
//  var oAutoComp = new YAHOO.widget.AutoComplete("f_input0","f_container", oACDS);
//  oAutoComp.minQueryLength = 0; 


//  }

// YAHOO.util.Event.onDOMReady(init);
