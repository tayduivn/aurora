<?include_once('../common.php');?>
var Dom   = YAHOO.util.Dom;
YAHOO.util.Event.addListener(window, "load", function() {
    tables = new function() {
    var tableid=0; // Change if you have more the 1 table
	    var tableDivEL="table"+tableid;



	    var SuppliersColumnDefs = [
				         {key:"id", label:"<?=_('Id')?>",  width:60,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
					 ,{key:"code", label:"<?=_('Code')?>",width:100, sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"name", label:"<?=_('Name')?>", width:190,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"location", label:"<?=_('Location')?>", width:190,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"email", label:"<?=_('Email')?>", width:190,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"tel",<?=($_SESSION['state']['suppliers']['view']!='contact'?'hidden:true,':'')?> label:"<?=_('Tel')?>", width:190,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				       ,{key:"for_sale", <?=($_SESSION['state']['suppliers']['view']!='products'?'hidden:true,':'')?> label:"<?=_('For sale')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"tobedicontinued", <?=($_SESSION['state']['suppliers']['view']!='products'?'hidden:true,':'')?> label:"<?=_('To be disc')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"discontinued",<?=($_SESSION['state']['suppliers']['view']!='products'?'hidden:true,':'')?>  label:"<?=_('Disc')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"nosale", <?=($_SESSION['state']['suppliers']['view']!='products'?'hidden:true,':'')?> label:"<?=_('Not for sale')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"stock_value",<?=($_SESSION['state']['suppliers']['view']!='money'?'hidden:true,':'')?>  label:"<?=_('Stock Value')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"high",<?=($_SESSION['state']['suppliers']['view']!='product_availability'?'hidden:true,':'')?>  label:"<?=_('High')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"normal",<?=($_SESSION['state']['suppliers']['view']!='product_availability'?'hidden:true,':'')?>  label:"<?=_('Normal')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"low", <?=($_SESSION['state']['suppliers']['view']!='product_availability'?'hidden:true,':'')?> label:"<?=_('Low')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"crical", <?=($_SESSION['state']['suppliers']['view']!='product_availability'?'hidden:true,':'')?> label:"<?=_('Critical')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				       ,{key:"outofstock", <?=($_SESSION['state']['suppliers']['view']!='product_availability'?'hidden:true,':'')?> label:"<?=_('Out of Stock')?>", width:90,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}

				       
				       
				       ];

	      this.dataSource0 = new YAHOO.util.DataSource("ar_suppliers.php?tipo=suppliers");
  this.dataSource0.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.dataSource0.connXhrMode = "queueRequests";
	    this.dataSource0.responseSchema = {
		resultsList: "resultset.data", 
		metaFields: {
		    rtext:"resultset.rtext",
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
			 ,"forsale"
			 ,"outofstock"
			 ,"low"
	 ]};

	    this.table0 = new YAHOO.widget.DataTable(tableDivEL, SuppliersColumnDefs,
						     this.dataSource0, {draggableColumns:true,
							   renderLoopSize: 50,generateRequest : myRequestBuilder
								       ,paginator : new YAHOO.widget.Paginator({
									       rowsPerPage    : <?=$_SESSION['state']['suppliers']['table']['nr']?>,containers : 'paginator', 
 									      pageReportTemplate : '(<?=_('Page')?> {currentPage} <?=_('of')?> {totalPages})',
									      previousPageLinkLabel : "<",
 									      nextPageLinkLabel : ">",
 									      firstPageLinkLabel :"<<",
 									      lastPageLinkLabel :">>"
									      ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info0'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
									  })
								     
								     ,sortedBy : {
									 key: "<?=$_SESSION['state']['suppliers']['table']['order']?>",
									 dir: "<?=$_SESSION['state']['suppliers']['table']['order_dir']?>"
								     }
							   ,dynamicData : true

						     }
						     );
	    this.table0.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table0.doBeforeSortColumn = mydoBeforeSortColumn;
	    this.table0.doBeforePaginatorChange = mydoBeforePaginatorChange;
	    this.table0.filter={key:'<?=$_SESSION['state']['suppliers']['table']['f_field']?>',value:'<?=$_SESSION['state']['suppliers']['table']['f_value']?>'};
	    this.table0.view='<?=$_SESSION['state']['suppliers']['view']?>';
	    



	};
    });


 var change_view=function(e){
	
	var table=tables['table0'];
	var tipo=this.id;

	table.hideColumn('location');
	table.hideColumn('email');
	table.hideColumn('for_sale');
	table.hideColumn('tobediscontinued');
	table.hideColumn('nosale');
	table.hideColumn('high');
	table.hideColumn('normal');
	table.hideColumn('low');
	table.hideColumn('critical');
	table.hideColumn('outofstock');
	if(tipo=='general'){
	    table.showColumn('name');
	    table.showColumn('location');
	    table.showColumn('email');
	}else if(tipo=='stock'){
	    table.showColumn('high');
	    table.showColumn('normal');
	    table.showColumn('low');
	    table.showColumn('critical');
	    table.showColumn('outofstock');


	}else if(tipo=='sale'){
	    
	}else if(tipo=='products'){
	    table.showColumn('for_sale');
	    table.showColumn('tobediscontinued');
	    table.showColumn('nosale');
	}
	

	Dom.get(table.view).className="";
	Dom.get(tipo).className="selected";
	table.view=tipo
	YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=suppliers-view&value=' + escape(tipo) );
    }


function init(){
    var oACDS = new YAHOO.util.FunctionDataSource(mygetTerms);
    oACDS.queryMatchContains = true;
    var oAutoComp = new YAHOO.widget.AutoComplete("f_input0","f_container", oACDS);
    oAutoComp.minQueryLength = 0; 

    
    ids=['general','sales','stock','products'];
    YAHOO.util.Event.addListener(ids, "click",change_view)


}
YAHOO.util.Event.onDOMReady(init);

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
