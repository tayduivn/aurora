<?php
//@author Raul Perusquia <rulovico@gmail.com>
//Copyright (c) 2009 LW
include_once('common.php');
?>
    var Dom   = YAHOO.util.Dom;
var Event = YAHOO.util.Event;
var dialog_note;


function save(tipo){
    switch(tipo){
    case('note'):
	var value=escape(Dom.get(tipo+"_input").value);
	var request="ar_contacts.php?tipo=update_customer&key=new_note&value="+value;
	YAHOO.util.Connect.asyncRequest('POST',request ,{
		success:function(o) {
		    //	alert(o.responseText);

		    var r =  YAHOO.lang.JSON.parse(o.responseText);
		    if (r.state==200) {
			close_dialog(tipo)
			var table=tables['table0'];
			var datasource=tables['dataSource0'];
			var request='';
			datasource.sendRequest(request,table.onDataReturnInitializeTable, table);    

		    }else
			Dom.get(tipo+'_msg').innerHTML=r.msg;
		}
	    });        
	

	break;
    }
};

function change(e,o,tipo){
    switch(tipo){
    case('note'):
	if(o.value!=''){
	    enable_save(tipo);

	    if(window.event)
		key = window.event.keyCode; //IE
	    else
		key = e.which; //firefox     
	    
	    if (key == 13)
		save(tipo);


	}else
	    disable_save(tipo);
	break;
    }
};


function enable_save(tipo){
    switch(tipo){
    case('note'):
	Dom.get(tipo+'_save').style.visibility='visible';
	break;
    }
};

function disable_save(tipo){
    switch(tipo){
    case('note'):
	Dom.get(tipo+'_save').style.visibility='hidden';
	break;
    }
};


function close_dialog(tipo){
    switch(tipo){
  //   case('long_note'):
// 	//Dom.get(tipo+"_input").value='';
// 	dialog_note.hide();

// 	break;

    case('note'):

	Dom.get(tipo+"_input").value='';
	Dom.get(tipo+'_save').style.visibility='hidden';
	dialog_note.hide();

	break;
    }
};

 
YAHOO.util.Event.addListener(window, "load", function() {
	    tables = new function() {
		    
		    var tableid=0; // Change if you have more the 1 table
		    var tableDivEL="table"+tableid;  
		    
		    var ColumnDefs = [
				      {key:"date",label:"<?php echo _('Date')?>", width:200,sortable:true,className:"aright",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				      ,{key:"author",label:"<?php echo _('Author')?>", width:70,sortable:true,formatter:this.customer_name,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				      ,{key:"abstract", label:"<?php echo _('Description')?>", width:370,sortable:true,formatter:this.customer_name,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				      ];
		
		    this.dataSource0  = new YAHOO.util.DataSource("ar_history.php?tipo=history&&type=company&tid="+tableid);
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
		    totalRecords: "resultset.total_records" // Access to value in the server response
		},
		fields: [
			 "id","note",'author','date','tipo','abstract','details'
			 ]};
		    this.table0 = new YAHOO.widget.DataTable(tableDivEL, ColumnDefs,
								   this.dataSource0
								 , {
								     renderLoopSize: 5,generateRequest : myRequestBuilder
								       ,paginator : new YAHOO.widget.Paginator({
									      rowsPerPage    : <?php echo$_SESSION['state']['company']['history']['nr']?>,containers : 'paginator', 
 									      pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',alwaysVisible:false,
									      previousPageLinkLabel : "<",
 									      nextPageLinkLabel : ">",
 									      firstPageLinkLabel :"<<",
 									      lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500]
									      ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info0'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"



									  })
								     
								     ,sortedBy : {
									 key: "<?php echo$_SESSION['state']['company']['history']['order']?>",
									 dir: "<?php echo$_SESSION['state']['company']['history']['order_dir']?>"
								     },
								     dynamicData : true

								  }
								   
								 );
	    	    this.table0.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table0.doBeforeSortColumn = mydoBeforeSortColumn;
	    this.table0.doBeforePaginatorChange = mydoBeforePaginatorChange;
		    this.table0.filter={key:'<?php echo$_SESSION['state']['company']['history']['f_field']?>',value:'<?php echo$_SESSION['state']['company']['history']['f_value']?>'};

	    //   YAHOO.util.Event.addListener('f_input', "keyup",myFilterChangeValue,{table:this.table0,datasource:this.dataSource})
			 
	    
	    //	    var Dom   = YAHOO.util.Dom;
	    //alert(Dom.get('f_input'));

	    YAHOO.util.Event.addListener('yui-pg0-0-page-report', "click",myRowsPerPageDropdown)
	
	};
    });

var oMenu;
function init(){

//     var shortcut_next = new YAHOO.util.KeyListener(document, {keys:89 },  { fn:key_press });
//         alert("cac");
//     shortcut_next.enable();
//     var key_press=function(type, args, obj){
// 	alert("caca");
// 	//	window.location=Dom.get("next").href;
//     }

//     document.documentElement.focus();
//     document.body.focus();

    var alt_shortcuts = function(type, args, obj) {
	if(args[0]==78){
	    window.location=Dom.get("next").href;
	}else if(args[0]==80){
	    window.location=Dom.get("next").href;
	}

    }

    kpl1 = new YAHOO.util.KeyListener(document, { alt:true ,keys:[78,80] }, { fn:alt_shortcuts } );
    kpl1.enable();

   var search_data={tipo:'customer_name',container:'customer'};
Event.addListener('customer_submit_search', "click",submit_search,search_data);
Event.addListener('customer_search', "keydown", submit_search_on_enter,search_data); 


	//Details textarea editor ---------------------------------------------------------------------
	var texteditorConfig = {
	    height: '270px',
	    width: '750px',
	    dompath: true,
	    focusAtStart: true
	};     

 	editor = new YAHOO.widget.Editor('long_note_input', texteditorConfig);

	editor._defaultToolbar.buttonType = 'basic';
 	editor.render();

	//	editor.on('editorKeyUp',change_textarea,'details' );
	//-------------------------------------------------------------


dialog_note = new YAHOO.widget.Dialog("dialog_note", {context:["note","tr","tl"]  ,visible : false,close:false,underlay: "none",draggable:false});
dialog_note.render();
Event.addListener("note", "click", dialog_note.show,dialog_note , true);
dialog_long_note = new YAHOO.widget.Dialog("dialog_long_note", {context:["customer_data","tl","tl"] ,visible : false,close:false,underlay: "none",draggable:false});
dialog_long_note.render();
Event.addListener("long_note", "click", dialog_long_note.show,dialog_long_note , true);

//Event.addListener("note", "click", dialog_note.hide,dialog_note , true);



 var oACDS = new YAHOO.util.FunctionDataSource(mygetTerms);
 oACDS.queryMatchContains = true;
 var oAutoComp = new YAHOO.widget.AutoComplete("f_input0","f_container", oACDS);
 oAutoComp.minQueryLength = 0; 



YAHOO.util.Event.onContentReady("filtermenu0", function () {
	 var oMenu = new YAHOO.widget.ContextMenu("filtermenu0", {trigger:"filter_name0"});
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
	 
    });


YAHOO.util.Event.onContentReady("rppmenu0", function () {
	 rppmenu = new YAHOO.widget.Menu("rppmenu", { context:["rtext_rpp0","bl", "bl"]  });
	 rppmenu.render();
	 rppmenu.subscribe("show", rppmenu.focus);
	 


    });


}

YAHOO.util.Event.onDOMReady(init);
