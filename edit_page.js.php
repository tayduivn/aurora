<?php  include_once('common.php'); ?>
var Event = YAHOO.util.Event;
var Dom   = YAHOO.util.Dom;
var select_page_operation=false;
var dialog_upload_page_content;
function save_see_also_type(value){



var request='ar_edit_sites.php?tipo=edit_page_header&newvalue='+value+"&id="+Dom.get('page_key').value+'&key=Page Store See Also Type'


//var request='ar_edit_contacts.php?tipo=edit_customer&key=' + key+ '&newvalue=' + value +'&customer_key=' + customer_id
//	alert(request);
		    YAHOO.util.Connect.asyncRequest('POST',request ,{
			    success:function(o) {
//alert(o.responseText)
				var r =  YAHOO.lang.JSON.parse(o.responseText);
				
				if(r.state==200){
			
  
 
            if (r.newvalue=='Auto' || r.newvalue=='Manual') {
                           Dom.removeClass(['see_also_type_Auto','see_also_type_Manual'],'selected');

               Dom.addClass('see_also_type_'+r.newvalue,'selected');
                          location.href='edit_page.php?id='+r.page_key;
            }else{
                alert(r.msg)
            }
            }
        
    }
    });

}




function delete_found_in_page( page_key ) {
	
	var request='ar_edit_sites.php?tipo=delete_found_in_page&id=' + Dom.get('page_key').value +'&found_in_key='+page_key
	           
	          
//alert(request);
		    YAHOO.util.Connect.asyncRequest('POST',request ,{
	            success:function(o){
	      //     alert(o.responseText);	
			var r =  YAHOO.lang.JSON.parse(o.responseText);
			if(r.state==200){
        location.href='edit_page.php?id='+r.page_key;
                                  }else{
                                  
                                  
                                  
                                  }
   			}
    });

	
}


function delete_see_also_page( page_key ) {
	
	var request='ar_edit_sites.php?tipo=delete_see_also_page&id=' + Dom.get('page_key').value +'&see_also_key='+page_key
	           
	          
//alert(request);
		    YAHOO.util.Connect.asyncRequest('POST',request ,{
	            success:function(o){
	      //     alert(o.responseText);	
			var r =  YAHOO.lang.JSON.parse(o.responseText);
			if(r.state==200){
        location.href='edit_page.php?id='+r.page_key;
                                  }else{
                                  
                                  
                                  
                                  }
   			}
    });

	
}



function show_dialog_page_list(e,type){
select_page_operation=type;
dialog_page_list.show();
}

function select_page(oArgs){
if(select_page_operation=='found_in'){
select_found_in_page(oArgs)
}else if(select_page_operation=='see_also'){
select_see_also_page(oArgs)
}else{
 dialog_page_list.hide();
}

}



function select_see_also_page(oArgs){


see_also_key=tables.table7.getRecord(oArgs.target).getData('key');

 dialog_page_list.hide();


	var request = 'ar_edit_sites.php?tipo=add_see_also_page&id=' + Dom.get('page_key').value + '&see_also_key=' + see_also_key;
	 //alert(request);

	YAHOO.util.Connect.asyncRequest('POST', request, {
		success: function(o) {
			//salert(o.responseText);
			var r = YAHOO.lang.JSON.parse(o.responseText);
			if (r.state == 200) {
				
                    location.href='edit_page.php?id='+r.page_key;
			} else {


				}	
		}		
	});
 

}


function select_found_in_page(oArgs){


found_in_key=tables.table7.getRecord(oArgs.target).getData('key');

 dialog_page_list.hide();


	var request = 'ar_edit_sites.php?tipo=add_found_in_page&id=' + Dom.get('page_key').value + '&found_in_key=' + found_in_key;
	 

	YAHOO.util.Connect.asyncRequest('POST', request, {
		success: function(o) {
			//alert(o.responseText);
			var r = YAHOO.lang.JSON.parse(o.responseText);
			if (r.state == 200) {
				
                    location.href='edit_page.php?id='+r.page_key;
			} else {


				}	
		}		
	});
 

}


YAHOO.util.Event.addListener(window, "load", function() {
    tables = new function() {

   

   var tableid=7; // Change if you have more the 1 table
	    var tableDivEL="table"+tableid;

	    var CustomersColumnDefs = [
				       {key:"key", label:"", hidden:true,action:"none",isPrimaryKey:true}
				     
				       ,{key:"code",label:"<?php echo _('Code')?>", width:80,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}}
				    		,{key:"type",label:"<?php echo _('Type')?>", width:60,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}, editor: new YAHOO.widget.TextboxCellEditor({asyncSubmitter: CellEdit}),object:'family_page_properties'}
  
				      ,{key:"store_title",label:"<?php echo _('Header Title')?>", width:250,sortable:true,className:"aleft",sortOptions:{defaultDir:YAHOO.widget.DataTable.CLASS_ASC}, editor: new YAHOO.widget.TextboxCellEditor({asyncSubmitter: CellEdit}),object:'family_page_properties'}
				    			        
				       ];
				       
	 
				       
				       
	    
	        this.dataSource7 = new YAHOO.util.DataSource("ar_quick_tables.php?tipo=page_list&site_key="+Dom.get('site_key').value+"&sf=0&tableid=7");

//alert("ar_edit_sites.php?tipo=family_page_list&site_key="+Dom.get('site_key').value+"&parent=family&parent_key="+Dom.get('family_key').value+"&tableid=7")
	    this.dataSource7.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.dataSource7.connXhrMode = "queueRequests";
	    this.dataSource7.responseSchema = {
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
			 "key",
			 "code","store_title","type"

			 ]};

        this.table7 = new YAHOO.widget.DataTable(tableDivEL, CustomersColumnDefs,
						     this.dataSource7
						     , {
							 renderLoopSize: 50,generateRequest : myRequestBuilder
							 ,paginator : new YAHOO.widget.Paginator({
								 rowsPerPage    :20 ,containers : 'paginator7', alpartysVisible:false,
								 pageReportTemplate : '(<?php echo _('Page')?> {currentPage} <?php echo _('of')?> {totalPages})',
								 previousPageLinkLabel : "<",
								 nextPageLinkLabel : ">",
								 firstPageLinkLabel :"<<",
								 lastPageLinkLabel :">>",rowsPerPageOptions : [10,25,50,100,250,500]
								 ,template : "{FirstPageLink}{PreviousPageLink}<strong id='paginator_info7'>{CurrentPageReport}</strong>{NextPageLink}{LastPageLink}"
							     })
							 
							 ,sortedBy : {
							   key: "code",
									 dir: ""
							 },
							 dynamicData : true
						     }
						     );
	    
	      this.table7.handleDataReturnPayload =myhandleDataReturnPayload;
	    this.table7.doBeforeSortColumn = mydoBeforeSortColumn;
	    //this.table7.subscribe("cellClickEvent", this.table7.onEventShowCellEditor);

 this.table7.subscribe("rowMouseoverEvent", this.table7.onEventHighlightRow);
       this.table7.subscribe("rowMouseoutEvent", this.table7.onEventUnhighlightRow);
      this.table7.subscribe("rowClickEvent", select_page);
        this.table7.table_id=tableid;
           this.table7.subscribe("renderEvent", myrenderEvent);


	    this.table7.doBeforePaginatorChange = mydoBeforePaginatorChange;
	    this.table7.filter={key:'code',value:''};
	    
	    
	    
	    
	    
	};
    });



function change_block(){
  var ids = ['properties','page_header','page_footer','content','style','media','setup']; 
block_ids=['d_properties','d_page_header','d_page_footer','d_content','d_style','d_media','d_setup'];


if(this.id=='content'){
Dom.setStyle('tabbed_container','margin','0px 0px')
}else{
Dom.setStyle('tabbed_container','margin','0px 20px')

}

Dom.setStyle(block_ids,'display','none');
Dom.setStyle('d_'+this.id,'display','');
Dom.removeClass(ids,'selected');
Dom.addClass(this,'selected');
YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=page-editing&value='+this.id ,{});
}


function reset_edit_page_header(){ reset_edit_general('page_header');}
function save_edit_page_header(){save_edit_general('page_header');}
function reset_edit_page_html_head(){ reset_edit_general('page_html_head');}
function save_edit_page_html_head(){save_edit_general('page_html_head');}
function reset_edit_page_content(){ reset_edit_general('page_content');}
function save_edit_page_content(){


EmailHTMLEditor.saveHTML();



save_edit_general('page_content');
}
function reset_edit_page_properties(){ reset_edit_general('page_properties');}
function save_edit_page_properties(){save_edit_general('page_properties');}

function validate_page_content_presentation_template_data(query){validate_general('page_content','presentation_template_data',unescape(query));}
function validate_page_header_store_title(query){validate_general('page_header','store_title',unescape(query));}
function validate_page_header_subtitle(query){validate_general('page_header','subtitle',unescape(query));}
function validate_page_header_slogan(query){validate_general('page_header','slogan',unescape(query));}
function validate_page_header_resume(query){validate_general('page_header','resume',unescape(query));}
function validate_page_properties_url(query){validate_general('page_properties','url',unescape(query));}
function validate_page_properties_link_title(query){validate_general('page_properties','link_title',unescape(query));}
function validate_page_properties_page_code(query){validate_general('page_properties','page_code',unescape(query));}
function validate_page_html_head_title(query){validate_general('page_html_head','title',unescape(query));}
function validate_page_html_head_keywords(query){validate_general('page_html_head','keywords',unescape(query));}

function html_editor_changed(){
    validate_scope_data['page_content']['source']['changed']=true;
  
    
    validate_scope('page_content');
}

function show_dialog_upload_page_content(){
dialog_upload_page_content.show()

}
function close_upload_page_content(){
dialog_upload_page_content.hide();
}


function upload_page_content(e){
    YAHOO.util.Connect.setForm('upload_page_content_form', true,true);
    var request='ar_upload_page_content.php?tipo=upload_page_content';
   var uploadHandler = {
      upload: function(o) {
	   alert(o.responseText)
	    var r =  YAHOO.lang.JSON.parse(o.responseText);
	   
	    if(r.state==200){
	     
         window.location.reload()
                
	    }else
		alert(r.msg);
	    
	    

	}
    };

    YAHOO.util.Connect.asyncRequest('POST',request, uploadHandler);



  };
  
  
  function post_item_updated_actions(branch,r){
  
  //alert(branch)
  
  }

function show_page_preview(){
     window.location = "page_preview.php?id="+Dom.get('page_key').value;
}

function init(){




  Event.addListener('show_page_preview', "click", show_page_preview);


  Event.addListener('show_upload_page_content', "click", show_dialog_upload_page_content);
Event.addListener("cancel_upload_page_content", "click", close_upload_page_content);
  Event.addListener('upload_page_content', "click", upload_page_content);
 dialog_upload_page_content = new YAHOO.widget.Dialog("dialog_upload_page_content", {context:["show_upload_page_content","tl","bl"] ,visible : false,close:true,underlay: "none",draggable:false});
    dialog_upload_page_content.render();


  init_search('products_store');

    var ids = ['properties','page_header','page_footer','content','style','media','setup']; 
    Event.addListener(ids, "click", change_block);

 validate_scope_metadata={
        'page_properties':{'type':'edit','ar_file':'ar_edit_sites.php','key_name':'id','key':Dom.get('page_key').value}
        ,'page_html_head':{'type':'edit','ar_file':'ar_edit_sites.php','key_name':'id','key':Dom.get('page_key').value}
        ,'page_header':{'type':'edit','ar_file':'ar_edit_sites.php','key_name':'id','key':Dom.get('page_key').value}
        ,'page_content':{'type':'edit','ar_file':'ar_edit_sites.php','key_name':'id','key':Dom.get('page_key').value}
    };


 validate_scope_data={
 
 
    'page_properties':{
	'url':{'changed':false,'validated':true,'required':false,'group':1,'type':'item'
		 ,'validation':[{'regexp':"[a-z\\d]+",'invalid_msg':'<?php echo _('Invalid URL')?>'}]
		 ,'name':'page_properties_url','ar':false
		 
	}
	,'page_code':{'changed':false,'validated':true,'required':false,'group':1,'type':'item'
		 ,'validation':[{'regexp':"[a-z\\d]+",'invalid_msg':'<?php echo _('Invalid Code')?>'}]
		 ,'name':'page_properties_page_code'
		 ,'ar':'find','ar_request':'ar_sites.php?tipo=is_page_store_code&site_key='+Dom.get('site_key').value+'&query='
	},
	'link_title':{'changed':false,'validated':true,'required':false,'group':1,'type':'item'
		 ,'validation':[{'regexp':"[a-z\\d]+",'invalid_msg':'<?php echo _('Invalid Link Title')?>'}]
		 ,'name':'page_properties_link_title','ar':false
		 
	}
    }
     ,'page_html_head':{

	'title':{'changed':false,'validated':true,'required':false,'group':1,'type':'item'
		 ,'validation':[{'regexp':"[a-z\\d]+",'invalid_msg':'<?php echo _('Invalid Title')?>'}]
		 ,'name':'page_html_head_title','ar':false
		 
	}
	
	,'keywords':{'changed':false,'validated':true,'required':false,'group':1,'type':'item','validation':[],'name':'page_html_head_keywords','ar':false}
    }
,'page_header':{
	'store_title':{'changed':false,'validated':true,'required':false,'group':1,'type':'item'
		 ,'validation':[{'regexp':"[a-z\\d]+",'invalid_msg':'<?php echo _('Invalid Title')?>'}]
		 ,'name':'page_header_store_title','ar':false
		 
	}
	,'subtitle':{'changed':false,'validated':true,'required':false,'group':1,'type':'item','validation':[],'name':'page_header_subtitle','ar':false}
	,'slogan':{'changed':false,'validated':true,'required':false,'group':1,'type':'item','validation':[],'name':'page_header_slogan','ar':false}
	,'resume':{'changed':false,'validated':true,'required':false,'group':1,'type':'item','validation':[],'name':'page_header_resume','ar':false}
	
    }
,'page_content':{
	
	'source':{'changed':false,'validated':true,'required':false,'group':1,'type':'item','validation':[],'name':'html_editor','dbname':'Page Store Source','ar':false}
	
    }


};



 YAHOO.util.Event.addListener('reset_edit_page_html_head', "click", reset_edit_page_html_head);
    YAHOO.util.Event.addListener('save_edit_page_html_head', "click", save_edit_page_html_head);

 YAHOO.util.Event.addListener('reset_edit_page_header', "click", reset_edit_page_header);
    YAHOO.util.Event.addListener('save_edit_page_header', "click", save_edit_page_header);

YAHOO.util.Event.addListener('reset_edit_page_content', "click", reset_edit_page_content);
    YAHOO.util.Event.addListener('save_edit_page_content', "click", save_edit_page_content);
    
    YAHOO.util.Event.addListener('reset_edit_page_properties', "click", reset_edit_page_properties);
    YAHOO.util.Event.addListener('save_edit_page_properties', "click", save_edit_page_properties);



 var page_properties_page_code_oACDS = new YAHOO.util.FunctionDataSource(validate_page_properties_page_code);
    page_properties_page_code_oACDS.queryMatchContains = true;
    var page_properties_page_code_oAutoComp = new YAHOO.widget.AutoComplete("page_properties_page_code","page_properties_page_code_Container", page_properties_page_code_oACDS);
    page_properties_page_code_oAutoComp.minQueryLength = 0; 
    page_properties_page_code_oAutoComp.queryDelay = 0.1;


 var page_properties_url_oACDS = new YAHOO.util.FunctionDataSource(validate_page_properties_url);
    page_properties_url_oACDS.queryMatchContains = true;
    var page_properties_url_oAutoComp = new YAHOO.widget.AutoComplete("page_properties_url","page_properties_url_Container", page_properties_url_oACDS);
    page_properties_url_oAutoComp.minQueryLength = 0; 
    page_properties_url_oAutoComp.queryDelay = 0.1;
    
    
     var page_properties_link_title_oACDS = new YAHOO.util.FunctionDataSource(validate_page_properties_link_title);
    page_properties_link_title_oACDS.queryMatchContains = true;
    var page_properties_link_title_oAutoComp = new YAHOO.widget.AutoComplete("page_properties_link_title","page_properties_link_title_Container", page_properties_link_title_oACDS);
    page_properties_link_title_oAutoComp.minQueryLength = 0; 
    page_properties_link_title_oAutoComp.queryDelay = 0.1;
    
    var page_html_head_title_oACDS = new YAHOO.util.FunctionDataSource(validate_page_html_head_title);
    page_html_head_title_oACDS.queryMatchContains = true;
    var page_html_head_title_oAutoComp = new YAHOO.widget.AutoComplete("page_html_head_title","page_html_head_title_Container", page_html_head_title_oACDS);
    page_html_head_title_oAutoComp.minQueryLength = 0; 
    page_html_head_title_oAutoComp.queryDelay = 0.1;

    var page_html_head_keywords_oACDS = new YAHOO.util.FunctionDataSource(validate_page_html_head_keywords);
    page_html_head_keywords_oACDS.queryMatchContains = true;
    var page_html_head_keywords_oAutoComp = new YAHOO.widget.AutoComplete("page_html_head_keywords","page_html_head_keywords_Container", page_html_head_keywords_oACDS);
    page_html_head_keywords_oAutoComp.minQueryLength = 0; 
    page_html_head_keywords_oAutoComp.queryDelay = 0.1;


 var page_header_store_title_oACDS = new YAHOO.util.FunctionDataSource(validate_page_header_store_title);
    page_header_store_title_oACDS.queryMatchContains = true;
    var page_header_store_title_oAutoComp = new YAHOO.widget.AutoComplete("page_header_store_title","page_header_store_title_Container", page_header_store_title_oACDS);
    page_header_store_title_oAutoComp.minQueryLength = 0; 
    page_header_store_title_oAutoComp.queryDelay = 0.1;
    
 var page_header_subtitle_oACDS = new YAHOO.util.FunctionDataSource(validate_page_header_subtitle);
    page_header_subtitle_oACDS.queryMatchContains = true;
    var page_header_subtitle_oAutoComp = new YAHOO.widget.AutoComplete("page_header_subtitle","page_header_subtitle_Container", page_header_subtitle_oACDS);
    page_header_subtitle_oAutoComp.minQueryLength = 0; 
    page_header_subtitle_oAutoComp.queryDelay = 0.1;
var page_header_slogan_oACDS = new YAHOO.util.FunctionDataSource(validate_page_header_slogan);
    page_header_slogan_oACDS.queryMatchContains = true;
    var page_header_slogan_oAutoComp = new YAHOO.widget.AutoComplete("page_header_slogan","page_header_slogan_Container", page_header_slogan_oACDS);
    page_header_slogan_oAutoComp.minQueryLength = 0; 
    page_header_slogan_oAutoComp.queryDelay = 0.1;

    var page_header_resume_oACDS = new YAHOO.util.FunctionDataSource(validate_page_header_resume);
    page_header_resume_oACDS.queryMatchContains = true;
    var page_header_resume_oAutoComp = new YAHOO.widget.AutoComplete("page_header_resume","page_header_resume_Container", page_header_resume_oACDS);
    page_header_resume_oAutoComp.minQueryLength = 0; 
    page_header_resume_oAutoComp.queryDelay = 0.1;
  
dialog_page_list = new YAHOO.widget.Dialog("dialog_page_list", {context:["add_other_found_in_page","tr","tl"]  ,visible : false,close:true,underlay: "none",draggable:false});
    dialog_page_list.render();
	

     Event.addListener("add_other_found_in_page", "click", show_dialog_page_list,'found_in', true);
  Event.addListener("add_other_see_also_page", "click", show_dialog_page_list,'see_also' , true);
    var oACDS7 = new YAHOO.util.FunctionDataSource(mygetTerms);
 oACDS7.queryMatchContains = true;
 oACDS7.table_id=7;
 var oAutoComp7 = new YAHOO.widget.AutoComplete("f_input7","f_container7", oACDS7);
 oAutoComp7.minQueryLength = 0; 
    
    YAHOO.util.Event.addListener('clean_table_filter_show7', "click",show_filter,7);
 YAHOO.util.Event.addListener('clean_table_filter_hide7', "click",hide_filter,7);


    
       var myConfig = {
        height: '2000px',
        width: '972px',
        animate: true,
        dompath: true,
        focusAtStart: true,
         autoHeight: true
    };
    
    var state = 'off';
    

    
        EmailHTMLEditor = new YAHOO.widget.Editor('html_editor', myConfig);
   
   
   EmailHTMLEditor.on('toolbarLoaded', function() {
    
        var codeConfig = {
            type: 'push', label: 'Edit HTML Code', value: 'editcode'
        };
        this.toolbar.addButtonToGroup(codeConfig, 'insertitem');
        
        this.toolbar.on('editcodeClick', function() {
        

        
            var ta = this.get('element'),iframe = this.get('iframe').get('element');

            if (state == 'on') {
                state = 'off';
                this.toolbar.set('disabled', false);
                          this.setEditorHTML(ta.value);
                if (!this.browser.ie) {
                    this._setDesignMode('on');
                }

                Dom.removeClass(iframe, 'editor-hidden');
                Dom.addClass(ta, 'editor-hidden');
                this.show();
                this._focusWindow();
            } else {
                state = 'on';
                
                this.cleanHTML();
               
                Dom.addClass(iframe, 'editor-hidden');
                Dom.removeClass(ta, 'editor-hidden');
                this.toolbar.set('disabled', true);
                this.toolbar.getButtonByValue('editcode').set('disabled', false);
                this.toolbar.selectButton('editcode');
                this.dompath.innerHTML = 'Editing HTML Code';
                this.hide();
            
            }
            return false;
        }, this, true);

        this.on('cleanHTML', function(ev) {
            this.get('element').value = ev.html;
        }, this, true);
        
        
        
        this.on('editorKeyUp', html_editor_changed, this, true);
                this.on('editorDoubleClick', html_editor_changed, this, true);
                this.on('editorMouseDown', html_editor_changed, this, true);
                this.on('buttonClick', html_editor_changed, this, true);

        this.on('afterRender', function() {
            var wrapper = this.get('editor_wrapper');
            wrapper.appendChild(this.get('element'));
            this.setStyle('width', '100%');
            this.setStyle('height', '100%');
            this.setStyle('visibility', '');
            this.setStyle('top', '');
            this.setStyle('left', '');
            this.setStyle('position', '');

            this.addClass('editor-hidden');
        }, this, true);
    }, EmailHTMLEditor, true);
        yuiImgUploader(EmailHTMLEditor, 'html_editor', 'ar_upload_file_from_editor.php','image');

    
    EmailHTMLEditor.render();


}




YAHOO.util.Event.onDOMReady(init);




