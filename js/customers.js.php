<?include_once('../common.php');?>


YAHOO.util.Event.addListener(window, "load", function() {
    tables = new function() {


	    this.customerLink=  function(el, oRecord, oColumn, oData) {
		    var url="customer.php?id="+oRecord.getData("id");
		    el.innerHTML = oData.link(url);

	    };

	    this.customer_name=  function(el, oRecord, oColumn, oData) {
		if(oData!= null){
		    el.style.color='#000';
		    el.innerHTML = oData;
		}else{
		    el.style.color='#ccc';
		    el.innerHTML = "<?=_('Unknown')?>";
		}

		};


	    this.date=  function(el, oRecord, oColumn, oData) {
		el.innerHTML =oRecord.getData("flast_order") ;
	    };
	    this.total=  function(el, oRecord, oColumn, oData) {
		el.innerHTML =oRecord.getData("ftotal") ;
	    };	
	    this.location=  function(el, oRecord, oColumn, oData) {
		el.innerHTML =oRecord.getData("flocation") ;
	    };	


	     //START OF THE TABLE=========================================================================================================================

		var tableid=0; // Change if you have more the 1 table
	    var tableDivEL="table"+tableid;



	    var CustomersColumnDefs = [
				       {key:"id", label:"<?=$customers_ids[0]?>", formatter:this.customerLink,width:60,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"name", label:"<?=_('Name')?>", width:250,sortable:true,formatter:this.customer_name,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"location", label:"<?=_('Location')?>",<?=($_SESSION['state']['customers']['view']=='general'?'':'hidden:true,')?> width:230,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"last_order", label:"<?=_('Last Order')?>",<?=($_SESSION['state']['customers']['view']=='general'?'':'hidden:true,')?>width:100,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"orders", label:"<?=_('Orders')?>",<?=($_SESSION['state']['customers']['view']=='general'?'':'hidden:true,')?>sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"super_total", label:"<?=_('Total')?>",<?=($_SESSION['state']['customers']['view']=='general'?'':'hidden:true,')?>sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"email", label:"<?=_('Email')?>",<?=($_SESSION['state']['customers']['view']=='contact'?'':'hidden:true,')?>sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"tel", label:"<?=_('Telephone')?>",<?=($_SESSION['state']['customers']['view']=='contact'?'':'hidden:true,')?>sortable:false,className:"aright"}
				       //					 {key:"families", label:"<?=_('Customers')?>", sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}},
				      //{key:"active", label:"<?=_('Customers')?>", sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				      

					 ];
	    //?tipo=customers&tid=0"
	    this.dataSource0 = new YAHOO.util.DataSource("ar_contacts.php?tipo=customers");
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
			 "id"
			 ,"name"
			 ,'location'
			 ,'orders'
			 ,'last_order'
			 ,'super_total','email','tel'
			 //,{key:"families",parser:YAHOO.util.DataSource.parseNumber},
			 //	    {key:"active",parser:YAHOO.util.DataSource.parseNumber}
			 ]};
	    //__You shouls not change anything from here

	    //this.dataSource.doBeforeCallback = mydoBeforeCallback;



	    this.table0 = new YAHOO.widget.DataTable(tableDivEL, CustomersColumnDefs,
								   this.dataSource0
								 , {
								     // sortedBy: {key:"<?=$_SESSION['tables']['customers_list'][0]?>", dir:"<?=$_SESSION['tables']['customers_list'][1]?>"},
								     renderLoopSize: 50,generateRequest : myRequestBuilder
								       ,paginator : new YAHOO.widget.Paginator({
									      rowsPerPage    : <?=$_SESSION['state']['customers']['table']['nr']?>,containers : 'paginator', 
 									      pageReportTemplate : '(<?=_('Page')?> {currentPage} <?=_('of')?> {totalPages})',
									      previousPageLinkLabel : "<",
 									      nextPageLinkLabel : ">",
 									      firstPageLinkLabel :"<<",
 									      lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500]
									      ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info0'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"



									  })
								     
								     ,sortedBy : {
									 key: "<?=$_SESSION['state']['customers']['table']['order']?>",
									 dir: "<?=$_SESSION['state']['customers']['table']['order_dir']?>"
								     },
								     dynamicData : true

								  }
								   
								 );
	    
	    this.table0.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table0.doBeforeSortColumn = mydoBeforeSortColumn;
	    this.table0.doBeforePaginatorChange = mydoBeforePaginatorChange;

		    
		    
	    this.table0.view='<?=$_SESSION['state']['customers']['view']?>';

	    this.table0.filter={key:'<?=$_SESSION['state']['customers']['table']['f_field']?>',value:'<?=$_SESSION['state']['customers']['table']['f_value']?>'};

	    //   YAHOO.util.Event.addListener('f_input', "keyup",myFilterChangeValue,{table:this.table0,datasource:this.dataSource})
			 
	    
	    //	    var Dom   = YAHOO.util.Dom;
	    //alert(Dom.get('f_input'));

	    YAHOO.util.Event.addListener('yui-pg0-0-page-report', "click",myRowsPerPageDropdown)
	
	};
    });




 function init(){
 var Dom   = YAHOO.util.Dom;


 var oACDS = new YAHOO.util.FunctionDataSource(mygetTerms);
 oACDS.queryMatchContains = true;
 var oAutoComp = new YAHOO.widget.AutoComplete("f_input0","f_container", oACDS);
 oAutoComp.minQueryLength = 0; 



YAHOO.util.Event.onContentReady("filtermenu", function () {
	 var oMenu = new YAHOO.widget.Menu("filtermenu", { context:["filter_name0","tr", "br"]  });
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
	 YAHOO.util.Event.addListener("filter_name0", "click", oMenu.show, null, oMenu);
    });


YAHOO.util.Event.onContentReady("rppmenu", function () {
	 var oMenu = new YAHOO.widget.Menu("rppmenu", { context:["filter_name0","tr", "bl"]  });
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
	 YAHOO.util.Event.addListener("paginator_info0", "click", oMenu.show, null, oMenu);
    });





  var change_view=function(e){
      var tipo=this.id;
      var table=tables['table0'];
      old_view=table.view;
      
      if(tipo=='general'){
	  table.hideColumn('email');
	  table.hideColumn('tel');
	  table.showColumn('location');
	  table.showColumn('last_order');
	  table.showColumn('orders');
	  table.showColumn('super_total');
	  Dom.get('contact').className='';
	  Dom.get('general').className='selected';
      }else{
	  table.showColumn('email');
	  table.showColumn('tel');
	  table.hideColumn('location');
	  table.hideColumn('last_order');
	  table.hideColumn('orders');
	  table.hideColumn('super_total');


      }


      YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=customers-view&value='+escape(tipo));
  }



YAHOO.util.Event.addListener('but_show_details', "click",show_details,'customers');
var ids=['general','contact'];
YAHOO.util.Event.addListener(ids, "click",change_view);
//YAHOO.util.Event.addListener('submit_advanced_search', "click",submit_advanced_search);



 }

YAHOO.util.Event.onDOMReady(init);
