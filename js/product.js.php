<?include_once('../common.php');?>
    var plot='<?=$_SESSION['state']['product']['plot']?>';
  var Dom   = YAHOO.util.Dom;
var change_plot_sigma=function(o){

    max_sigma=o.value;
      Dom.get("the_plot").src='plot.php?tipo='+plot+'&max_sigma='+escape(max_sigma);
      YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=product-plot_data-max_sigma&value='+escape(max_sigma) );
}
  var change_plot_months=function(o){
	 
      months=Dom.get('plot_months').value;
      Dom.get("the_plot").src='plot.php?tipo='+plot+'&months='+escape(months);
      YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=product-plot_data-months&value='+escape(months) );
     }

      YAHOO.util.Event.addListener(window, "load", function() {
	      tables = new function() {
		      <?if($LU->checkRight(ORDER_VIEW)){?>
		      
		      
		      var tableid=0;
		      var tableDivEL="table"+tableid;
		      var ColumnDefs = [
					{key:"order", label:"<?=_('Number')?>", width:90,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
					
				      ,{key:"customer_name", label:"<?=_('Customer')?>", width:220,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
					,{key:"date", label:"<?=_('Date')?>", sortable:true, width:100,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
					,{key:"dispached", label:"<?=_('Dispached')?>",width:80,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					,{key:"undispached", label:"<?='&Delta;'._('Ordered')?>", width:80, sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					];
		      
		      
		      this.dataSource0 = new YAHOO.util.DataSource("ar_orders.php?tipo=withproduct&tableid="+tableid);
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
			      totalRecords: "resultset.total_records"
			  },
			
			  fields: [
				   "id","order","customer_name","date","dispached","undispached"
				   ]};
		      
		      this.table0 = new YAHOO.widget.DataTable(tableDivEL, ColumnDefs,
							       this.dataSource0, {
								   //draggableColumns:true,
								   renderLoopSize: 50,generateRequest : myRequestBuilder
								   ,paginator : new YAHOO.widget.Paginator({
									 rowsPerPage:<?=$_SESSION['state']['product']['orders']['nr']?>,containers : 'paginator0', 
									 pageReportTemplate : '(<?=_('Page')?> {currentPage} <?=_('of')?> {totalPages})',
									 previousPageLinkLabel : "<",
									 nextPageLinkLabel : ">",
									 firstPageLinkLabel :"<<",
									 lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500],alwaysVisible:false
									 ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info0'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
								       })
								   
								   ,sortedBy : {
								       key: "<?=$_SESSION['state']['product']['orders']['order']?>",
								       dir: "<?=$_SESSION['state']['product']['orders']['order_dir']?>"
								   }
								   ,dynamicData : true
								   
							     }
							       );
		      this.table0.handleDataReturnPayload =myhandleDataReturnPayload;
		      this.table0.doBeforeSortColumn = mydoBeforeSortColumn;
		      this.table0.doBeforePaginatorChange = mydoBeforePaginatorChange;
		      
		    
		      
		      <?}?>
			  <?if($LU->checkRight(CUST_VIEW) ){?>
		      var tableid=1;
		      var tableDivEL="table"+tableid;
		      
		      var ColumnDefs = [
					{key:"customer", label:"<?=_('Customer')?>",width:320, sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
					,{key:"orders", label:"<?=_('Orders')?>",width:70, sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
				      ,{key:"dispached", label:"<?=_('Disp')?>",width:65, sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					,{key:"todispach", label:"<?=_('To Disp')?>",width:65, sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					,{key:"nodispached", label:"<?=_('No Disp')?>", width:65, sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					,{key:"charged", label:"<?=_('Charged')?>", width:80, sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_DESC}}
					];
		    
		      
		      this.dataSource1 = new YAHOO.util.DataSource("ar_orders.php?tipo=withcustomerproduct&tableid="+tableid);
		      this.dataSource1.responseType = YAHOO.util.DataSource.TYPE_JSON;
		      this.dataSource1.connXhrMode = "queueRequests";
		      this.dataSource1.responseSchema = {
			  resultsList: "resultset.data", 
			  metaFields: {
			    rowsPerPage:"resultset.records_perpage",
			    rtext:"resultset.rtext",
			    sort_key:"resultset.sort_key",
			    sort_dir:"resultset.sort_dir",
			    tableid:"resultset.tableid",
			    filter_msg:"resultset.filter_msg",
			    totalRecords: "resultset.total_records"
			  },
			  
			  fields: [
				  "customer","dispached","nodispached","charged","todispach","orders"
				   ]};
		      
		      this.table1 = new YAHOO.widget.DataTable(tableDivEL, ColumnDefs,
							       this.dataSource1, {
								   //draggableColumns:true,
								   renderLoopSize: 50,generateRequest : myRequestBuilder
								 ,paginator : new YAHOO.widget.Paginator({
									 rowsPerPage:<?=$_SESSION['state']['product']['customers']['nr']?>,containers : 'paginator1', 
									 pageReportTemplate : '(<?=_('Page')?> {currentPage} <?=_('of')?> {totalPages})',
									 previousPageLinkLabel : "<",
									 nextPageLinkLabel : ">",
									 firstPageLinkLabel :"<<",
									 lastPageLinkLabel :">>",alwaysVisible:false
									 ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info1'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
								     })
								   
								   ,sortedBy : {
								       key: "<?=$_SESSION['state']['product']['customers']['order']?>",
								       dir: "<?=$_SESSION['state']['product']['customers']['order_dir']?>"
								   }
								   ,dynamicData : true
								 
							       }
							       );
		      this.table1.handleDataReturnPayload =myhandleDataReturnPayload;
		      this.table1.doBeforeSortColumn = mydoBeforeSortColumn;
		      this.table1.doBeforePaginatorChange = mydoBeforePaginatorChange;

		    <?}?>



	    
	    };
    });

var manual_check=function(){
    var request='ar_assets.php?tipo=sincro_pages';
    YAHOO.util.Connect.asyncRequest('POST',request ,{
	    success:function(o) {
		//alert(o.responseText)
		var r =  YAHOO.lang.JSON.parse(o.responseText);	
		if(r.ok){
		    Dom.get('no_sincro_pages').style.visibility='hidden';
		    Dom.get('no_sincro_pages').setAttribute('title','');
		    Dom.get('edit_web_messages').innerHTML=r.msg;
	
		}else
		    Dom.get('edit_web_messages').innerHTML='<span class="error">'+r.msg+'</span>';
	    }
	    
	});
}

var  change_web_status =function(tipo){
    var request='ar_assets.php?tipo=ep_update&key=web_status'+'&value='+escape(tipo);
    YAHOO.util.Connect.asyncRequest('POST',request ,{
	    success:function(o) {
		//	alert(o.responseText)
		var r =  YAHOO.lang.JSON.parse(o.responseText);
		if(r.ok){
		    Dom.get('web_status').innerHTML=r.web_status;
		    if(r.web_status_error==1){
			Dom.get('web_status_error').style.visibility='visible';
			Dom.get('web_status_error').setAttribute('title',r.web_status_error);
		    }else
			Dom.get('web_status_error').style.visibility='hidden';

		     if(!r.same){
			 Dom.get('no_sincro_pages').style.visibility='visible';
			 Dom.get('no_sincro_db').style.visibility='visible';
		     }
		     Dom.get('edit_web_messages').innerHTML='<?=_('Syncronizing product')?>';
		}

		Dom.get('edit_web_messages').innerHTML='<?=_('Syncronizing product')?>';
		var request='ar_xml.php?tipo=sincronize';
		YAHOO.util.Connect.asyncRequest('POST',request ,{
			success:function(o) {
			    				 alert(o.responseText)
			    var r =  YAHOO.lang.JSON.parse(o.responseText);
			    if(r.ok){
				Dom.get('no_sincro_db').style.visibility='hidden';
				Dom.get('edit_web_messages').innerHTML=r.msg;
				
			    }else
				Dom.get('edit_web_messages').innerHTML='<span class="error">'+r.msg+'</span>';
			}
			
		    });








	    }
	    
	    });

      }

function init(){
     var Event = YAHOO.util.Event;
     var Dom   = YAHOO.util.Dom;




     YAHOO.util.Event.onContentReady("web_status_menu", function () {
	     var oMenu = new YAHOO.widget.Menu("web_status_menu", { context:["web_status","tl", "bl"]  });
	     oMenu.render();
	     oMenu.subscribe("show", oMenu.focus);
	     YAHOO.util.Event.addListener("web_status", "click", oMenu.show, null, oMenu);
    });

     var change_view = function (e){
	 block=this.getAttribute('block');
	 state=this.getAttribute('state');
	 new_title=this.getAttribute('atitle');
	 old_title=this.getAttribute('title');

	 this.setAttribute('title',new_title);
	 this.setAttribute('atitle',old_title);
	 
	 if(state==1){
	     Dom.get('block_'+block).style.display='none';
	     this.setAttribute('state',0);
	     YAHOO.util.Dom.setStyle('but_logo_'+block, 'opacity', .2);
	     YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=product-display-'+block+'&value=0');
	 }else{
	     Dom.get('block_'+block).style.display='';
	     this.setAttribute('state',1);
	     YAHOO.util.Dom.setStyle('but_logo_'+block, 'opacity', 1);
	     YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=product-display-'+block+'&value=1');
	     
	 }


     }




  var change_plot = function (e){

      //      alert(plot)
      Dom.get("the_plot").src='plot.php?tipo='+this.id;
      YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=product-plot&value='+this.id );

      this.className='selected';
      Dom.get(plot).className='opaque';
      plot=this.id;
     }

     var ids = ["change_view_details","change_view_plot","change_view_orders","change_view_customers","change_view_stock_history"]; 
     Event.addListener(ids,"click",change_view);
     var ids = ["product_week_sales","product_month_sales","product_quarter_sales","product_year_sales","product_week_outers","product_week_outers" ,"product_week_outers","product_week_outers","product_stock_history"]; 
     Event.addListener(ids,"click",change_plot);

     Event.addListener('product_submit_search', "click",submit_search,'product');
     Event.addListener('product_search', "keydown", submit_search_on_enter,'product');



}
 YAHOO.util.Event.onDOMReady(init);