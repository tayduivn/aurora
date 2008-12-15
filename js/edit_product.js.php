<?
    include_once('../common.php');
?>
var Event = YAHOO.util.Event;
var Dom   = YAHOO.util.Dom;

var    current_form = 'description';
var    num_changed = 0;
var    num_errors = 0;
var editor;
var editing='<?=$_SESSION['state']['product']['edit']?>';
var cat_list;
jsonString='<?=$_SESSION['state']['product']['shapes'];?>';
try {
    var shapes = YAHOO.lang.JSON.parse(jsonString);
}
catch (e) {
    alert("ERROR:P_PES_JSONDATA");
};
jsonString='<?=$_SESSION['state']['product']['shapes_example'];?>';
try {
    var shapes_example = YAHOO.lang.JSON.parse(jsonString);
}
catch (e) {
    alert("ERROR:P_PES_JSONDATA");
};

var cats=new Object;
var select_cat=function(o,e){
    var tipo=o.getAttribite('cat');
    var cat_id=o.getAttribute('cat_id');
    var cat_name=o.innerHTML;
    
    if(o.className=='selected'){
	delete(cats[cat_id])
	
    }else{

	cats[cat_id]={'tipo':tipo,'name':cat_name};
    }
    display_cats();

}

    var to_save_on_enter=function(e,o){
     var key;     
     if(window.event)
          key = window.event.keyCode; //IE
     else
          key = e.which; //firefox     

     //     alert(key);
     if (key == 13){
	 //	 alert(o.name+'_save');
	
	 o.blur();

     }
 }


var is_diferent = function(v1,v2,tipo){

    if(tipo=='money' || tipo=='number'){
	if(parseFloat(v1)!=parseFloat(v2))
	    return true;
	else
	    return false;
    }else{
	if(v1!=v2)
	    return true;
	else
	    return false;
    }
	

}

    var change_units=function(){
	Dom.get("units_cancel").style.visibility='visible';
	Dom.get("change_units_but").style.display='none';
	Dom.get("units").style.display='none';
	Dom.get("v_units").style.display='';
	Dom.get("change_units_price").style.display='';
	Dom.get("change_units_oweight").style.display='';
	Dom.get("change_units_odim").style.display='';
	Dom.get("change_units_odim_example").style.display='';
	Dom.get("change_units_tipo_but").style.display='none';
    }


var change_element= function(o){

       
	var current_class=o.className;
	var tipo=o.getAttribute('tipo');


	if(is_diferent(o.getAttribute("ovalue"),o.value,tipo)){

	    if(current_class==''){
		num_changed++;
		}

	    val = vadilate(o);

	    if(!val){
		if(current_class!='error'){
		    num_errors++;
		}
		o.className='error';
	    }else{
		if(current_class=='error')
		    num_errors--;
		o.className='ok';

	    }

	}else{

	    if(current_class=='ok')
		num_changed--;
	    if(current_class=='error'){
		num_changed--;
		num_errors--;
	    }
	    o.className='';

	}

	if(editing=='suppliers'){
	    interpet_changes(o.getAttribute('supplier_id'));
	}else
	    interpet_changes(o.id);
	


    }

    function supplier_changed(o,id){
     var code=Dom.get('v_supplier_code'+id);
     var cost=Dom.get('v_supplier_cost'+id);
     name=o.name;
     if(cost.value!=cost.getAttribute('ovalue') || code.value!=code.getAttribute('ovalue')){
	 Dom.get('save_supplier_'+id).style.visibility='visible';
     }else
	 Dom.get('save_supplier_'+id).style.visibility='hidden';
     
 }
    function price_change(old_value,new_value){
	//	alert(old_value+' '+new_value)
	prefix='';
	old_value=FormatNumber(old_value,'.','',2);
	new_value=FormatNumber(new_value,'.','',2);
	var diff=new_value-old_value;
	if(diff>0)
	    prefix='+'+'<?=$myconf['currency_symbol']?>';
	else
	    prefix='-'+'<?=$myconf['currency_symbol']?>';
	

	if(old_value==0)
	    var per='';
	else{

	    var per=FormatNumber((100*(diff/old_value)).toFixed(1),'<?=$myconf['decimal_point']?>','<?=$myconf['thosusand_sep']?>',1)+'%';
	}
	var diff=FormatNumber(Math.abs(diff).toFixed(2),'<?=$myconf['decimal_point']?>','<?=$myconf['thosusand_sep']?>',2);
	return prefix+diff+' '+per;
    }

function format_rrp(o){
  if(o.value=='')
      {
	  price_changed(o);
      }else{
      o.value=FormatNumber(o.value,'<?=$myconf['decimal_point']?>','<?=$myconf['thosusand_sep']?>',2);
      price_changed(o);
  }

}

function return_to_old_value(key){
    Dom.get("v_"+key).value=Dom.get("v_"+key).getAttribute('ovalue');
    
    if(key=='description')
	description_changed(Dom.get("v_"+key));

}

function change_to_dependant(){
    Dom.get("product_tipo_dependant").style.display='';

}

function units_save(){
    var units=Dom.get('v_units').value;
    var price=Dom.get('v_price').value;
    var oweight=Dom.get('v_oweight_fcu').value;
    var name='odim_fcu';
    var odim=Dom.get('v_'+name).getAttribute('tipo')+'_'+Dom.get('v_'+name).value;

    var request='ar_assets.php?tipo=ep_update&key=units'+'&value='+escape(units)+'&price='+escape(price)+'&oweight='+escape(oweight)+'&odim='+escape(odim);
    YAHOO.util.Connect.asyncRequest('POST',request ,{
	    success:function(o) {
		//alert(o.responseText);
		var r =  YAHOO.lang.JSON.parse(o.responseText);
		if(r.ok){
		    Dom.get("units_cancel").style.visibility='hidden';
		    Dom.get("units_save").style.visibility='hidden';

		    Dom.get("change_units_diff").style.display='none';
		    Dom.get("change_units_but").style.display='';
		    Dom.get("units").style.display='';
		    Dom.get("v_units").style.display='none';
		    Dom.get("change_units_price").style.display='none';
		    Dom.get("change_units_oweight").style.display='none';
		    Dom.get("change_units_odim").style.display='none';
		    Dom.get("change_units_odim_example").style.display='none';
		    Dom.get("change_units_tipo_but").style.display='';
		    Dom.get("v_units").setAttribute('ovalue',units);
		    Dom.get("v_oweight_fcu").setAttribute('ovalue',oweight);
		    Dom.get("v_odim_fcu").setAttribute('ovalue',odim);
		    Dom.get("v_price_fcu").setAttribute('ovalue',price);
		    Dom.get("change_units_price_diff").style.display='none';
		    Dom.get("change_units_oweight_diff").style.display='none';
		    Dom.get("change_units_odim_diff").style.display='none';
		    Dom.get('edit_messages').innerHTML='<span>'+r.msg+'</span>';
		}else
		    Dom.get('edit_messages').innerHTML='<span class="error">'+r.msg+'</span>';
	    }
	    
	    });


}

function delete_supplier(id,name){
    var answer = confirm("<?=_('Are you sure you want to desassociate this supplier')?> ("+name+")");
    if (answer){

	var request='ar_assets.php?tipo=ep_update&key=supplier_delete'+'&value='+escape(id);
	YAHOO.util.Connect.asyncRequest('POST',request ,{
		success:function(o) {
		    //alert(o.responseText);
		    var r =  YAHOO.lang.JSON.parse(o.responseText);
		    if(r.ok){
			el=Dom.get("sup_tr1_"+id);
			el.parentNode.removeChild(el);
			el=Dom.get("sup_tr2_"+id);
			el.parentNode.removeChild(el);
			el=Dom.get("sup_tr3_"+id);
			el.parentNode.removeChild(el);
			el=Dom.get("sup_tr4_"+id);
			el.parentNode.removeChild(el);
		    }else
			alert(r.msg);
		}
		
	    });
    }


}


function delete_image(image_id,image_name){
    var answer = confirm("<?=_('Are you sure you want to delete this image')?> ("+image_name+")");
    if (answer){

	

	var request='ar_assets.php?tipo=ep_update&key=img_delete'+'&value='+escape(image_id);
	YAHOO.util.Connect.asyncRequest('POST',request ,{
		success:function(o) {
		    //alert(o.responseText);
		    var r =  YAHOO.lang.JSON.parse(o.responseText);
		    if(r.ok){
			Dom.get('image'+image_id).style.display='none';
			if(r.new_principal!=''){
			    var new_principal=r.new_principal;
			    Dom.get('images').setAttribute('principal',new_principal);
			    var new_but=Dom.get('img_set_principal'+new_principal);
			    new_but.setAttribute('title','<?=_('Main Image')?>');
			    new_but.setAttribute('principal',1);
			    new_but.setAttribute('src',"art/icons/asterisk_orange.png");		
			    new_but.style.cursor="default";
			}

		    }else
			alert(r.msg);
		}
		
	    });
    }


}


function set_image_as_principal(o){

    if(o.getAttribute('principal')==1)
	return;
    image_id=o.getAttribute('image_id');

    var request='ar_assets.php?tipo=ep_update&key=img_set_principal'+'&value='+escape(image_id);
    YAHOO.util.Connect.asyncRequest('POST',request ,{
	    success:function(o) {
		var r =  YAHOO.lang.JSON.parse(o.responseText);
		    if(r.ok){
			var old_principal=Dom.get('images').getAttribute('principal');
			var new_principal=image_id;
			Dom.get('images').setAttribute('principal',new_principal);
			var old_but=Dom.get('img_set_principal'+old_principal);
			var new_but=Dom.get('img_set_principal'+new_principal);
			old_but.setAttribute('title','<?=_('Set as the principal image')?>');
			old_but.setAttribute('principal',0);
			old_but.setAttribute('src',"art/icons/picture_empty.png");
			old_but.style.cursor="pointer";
			new_but.setAttribute('title','<?=_('Main Image')?>');
			new_but.setAttribute('principal',1);
			new_but.setAttribute('src',"art/icons/asterisk_orange.png");		
			new_but.style.cursor="default";
		    }else
			alert(r.msg);
		}
		 
	    });

}


function caption_changed(o){
    if(o.value!=o.getAttribute('ovalue')){
	Dom.get("save_img_caption"+o.getAttribute('image_id')).style.visibility='visible';
    }else
	Dom.get("save_img_caption"+o.getAttribute('image_id')).style.visibility='hidden';

}

  function description_changed(o){
	var ovalue=o.getAttribute('ovalue');
	var name=o.name;

	    
	if(ovalue!=o.value){
	    if(name=='description'){
		if(o.value==''){
		    Dom.get("edit_messages").innerHTML="<?=_("The product description can not be empty")?>";
		    Dom.get(name+"_save").style.visibility='hidden';
		    return;
		}
		if(o.value.lenght>75){
		    Dom.get("edit_messages").innerHTML="<?=_("The product description can not be longer the 75 characters")?>";
		    Dom.get(name+"_save").style.visibility='hidden';
		    return;
		}
	    }else if(name=='sdescription'){
		if(o.value==''){
		    Dom.get("edit_messages").innerHTML="<?=_("The product short description can not be empty")?>";
		    Dom.get(name+"_save").style.visibility='hidden';
		    return;
		}
		if(o.value.lenght>75){
		    Dom.get("edit_messages").innerHTML="<?=_("The product short description can not be longer the 40 characters")?>";
		    Dom.get(name+"_save").style.visibility='hidden';
		    return;
		}
	    }


	    

	    Dom.get("edit_messages").innerHTML='';
	    Dom.get(name+"_save").style.visibility='visible';
	    //Dom.get(name+"_icon").style.visibility='visible';
	    
	}
	else{
	    Dom.get("edit_messages").innerHTML='';
	    Dom.get(name+"_save").style.visibility='hidden';
	    //Dom.get(name+"_icon").style.visibility='hidden';
	}
    
	
	
    }

function percentage(old_value,new_value){
    if(new_value==0)
	return '';
    old_value=FormatNumber(old_value,'.','',4);
    new_value=FormatNumber(new_value,'.','',4);
    var diff=new_value-old_value;
    if(diff>0)
	prefix='+';
    else
	prefix='';
    var txt=prefix+FormatNumber((100*(diff/old_value)).toFixed(1),'<?=$myconf['decimal_point']?>','<?=$myconf['thosusand_sep']?>',1)+'%';

    return txt;

}


function units_changed(o){
	var ovalue=o.getAttribute('ovalue');
	var name=o.name;

	if(ovalue!=o.value){

	    Dom.get(name+"_save").style.visibility='visible';
	    //Dom.get(name+"_cancel").style.display='none';
	    Dom.get("change_units_diff").style.display='';
	    if(o.value==0){
		Dom.get(name+"_save").style.visibility='hidden';
		Dom.get(name+"_cancel").style.display='';
	   
		Dom.get("change_units_diff").innerHTML='<?_('Error')?>';
	    }else{
		Dom.get("change_units_diff").innerHTML=percentage(ovalue,o.value);
		Dom.get("change_units_price_diff").innerHTML=percentage(Dom.get("v_price_fcu").getAttribute('ovalue'),Dom.get("v_price_fcu").value);	
		Dom.get("change_units_oweight_diff").innerHTML=percentage(Dom.get("v_oweight_fcu").getAttribute('ovalue'),Dom.get("v_oweight_fcu").value);	
		Dom.get("change_units_odim_diff").innerHTML=percentage(Dom.get("v_odim_fcu").getAttribute('ovalue'),Dom.get("v_odim_fcu").value);	

	    
	    }	    
	    
	    
	    
	}else{

	    Dom.get(name+"_save").style.visibility='hidden';
	    //Dom.get(name+"_cancel").style.display='';
	    Dom.get("change_units_diff").style.display='none';
	    
	}
}



function weight_changed(o){
	var ovalue=o.getAttribute('ovalue');
	var name=o.name;
	if(ovalue!=o.value){
	    Dom.get(name+"_save").style.visibility='visible';

		
	}else{
	    Dom.get(name+"_save").style.visibility='hidden';
	}
}


function validate_dim(value,tipo){
    switch(tipo){
    case('shape0'):
	return {ok:false,msg:''};
	break;
    case('shape1'):
	if(!value.match(/^[0-9\<?=$myconf['decimal_point']?>]+x[0-9\<?=$myconf['decimal_point']?>]+x[0-9\<?=$myconf['decimal_point']?>]+$/))
	    return {ok:false,msg:''};
	else{
	    var dim=value.split("x",3);
	    var vol=dim[0]*dim[1]*dim[2];
	    if(vol==0)
		return {ok:false,msg:'<?=_('Zero volumen')?>'};
	    else
		return {ok:true,msg:'',vol:vol};

	}
	break;
    case('shape2'):
    case('shape4'):
	if(!value.match(/^[0-9\<?=$myconf['decimal_point']?>\s]+$/))
	    return {ok:false,msg:''};
	else
	    return {ok:true,msg:''};
	break;	
    case('shape3'):
    case('shape5'):
	if(!value.match(/^[0-9\<?=$myconf['decimal_point']?>]+x[0-9\<?=$myconf['decimal_point']?>]+$/))
	    return {ok:false,msg:''};
	else
	    return {ok:true,msg:''};
	break;
    default:
	
    }

    alert(value+" "+tipo);
    return true;

}

function dim_changed(o){


    var tipo=o.getAttribute('tipo');
    var name=o.name;

    if(validate_dim(o.value,tipo).ok){
	var ovalue=o.getAttribute('ovalue');
	if(ovalue!=o.value){
	    Dom.get(name+"_save").style.visibility='visible';
	    
	}else{
	    Dom.get(name+"_save").style.visibility='hidden';
	}
	Dom.get(name+"_alert").style.visibility='hidden';
		
    }else{

	Dom.get(name+"_save").style.visibility='hidden';
	Dom.get(name+"_alert").style.visibility='visible';

    }

}
function price_fcu_changed(o){
    var ovalue=o.getAttribute('ovalue');
    Dom.get('change_units_price_diff').innerHTML=percentage(ovalue,o.value);
}
function oweight_fcu_changed(o){
    var ovalue=o.getAttribute('ovalue');
    Dom.get('change_units_oweight_diff').innerHTML=percentage(ovalue,o.value);
}
function odim_fcu_changed(o){
    
    tipo_shape=o.getAttribute('tipo');
    data_old=validate_dim(o.getAttribute('ovalue'),tipo_shape);
    data_new=validate_dim(o.value,tipo_shape);
    if(!data_new.ok){
	Dom.get('change_units_odim_diff').innerHTML='<?_('Error')?>';
	
    }else{
	if(data_old.ok)
	    Dom.get('change_units_odim_diff').innerHTML=percentage(data_old.vol,data_new.vol);
    }
}
function price_changed(o){
	var ovalue=o.getAttribute('ovalue');
	var name=o.name;

	    
	if(ovalue!=o.value){
	    Dom.get(name+"_save").style.visibility='visible';
	    if(o.value==''){
		Dom.get(name+"_change").innerHTML='<?=_('RRP value unset')?>';
		Dom.get(name+"_ou").innerHTML='';
	    }else if(ovalue==''){
		value=FormatNumber(o.value,'.','',2);
		factor=FormatNumber(o.getAttribute('factor'),'.','',6);
		Dom.get(name+"_change").innerHTML='<?=_('RRP set to')?> '+'<?=$myconf['currency_symbol']?>'+FormatNumber(value,'<?=$myconf['decimal_point']?>','<?=$myconf['thosusand_sep']?>',2);
		Dom.get(name+"_ou").innerHTML='<?=$myconf['currency_symbol']?>'+FormatNumber((value*factor).toFixed(2),'<?=$myconf['decimal_point']?>','<?=$myconf['thosusand_sep']?>',2);
	    }else{
		value=FormatNumber(o.value,'.','',2);
		factor=FormatNumber(o.getAttribute('factor'),'.','',6);
		change=price_change(ovalue,o.value);
		Dom.get(name+"_change").innerHTML=change;
		Dom.get(name+"_ou").innerHTML='<?=$myconf['currency_symbol']?>'+FormatNumber((value*factor).toFixed(2),'<?=$myconf['decimal_point']?>','<?=$myconf['thosusand_sep']?>',2);
	    }
	}else{
	    Dom.get(name+"_save").style.visibility='hidden';
	}
    
	
	
    }


function save_form(){
    if(current_form == 'description')
	editor.saveHTML();
    YAHOO.util.Connect.setForm(document.getElementById(current_form)); 

    var request = YAHOO.util.Connect.asyncRequest('POST', 'ar_assets.php', callback);

}

var interpet_changes = function(id){
    
    if(editing=='suppliers'){
	if(num_changed>0 && num_errors==0){
	    Dom.get('save_supplier_'+id).style.display='';
	}else
	    Dom.get('save_supplier_'+id).style.display='none';

	
    }else{
	if(num_changed>0 && num_errors==0){
	    Dom.get('save_'+id).style.display='';
	    //  Dom.get('save').className='ok';
	    // Dom.get('exit').className='nook';
	    // YAHOO.util.Event.addListener('save', "click", save_form);
	}else{
	    Dom.get('save_'+id).style.display='none';
	    // YAHOO.util.Event.removeListener('save', "click");
	    // Dom.get('save').className='disabled';
	    //Dom.get('exit').className='ok';
	    
	}
    }
};

function simple_save(name){

    if(name=='dim' || name=='odim')
	var value = Dom.get('v_'+name).getAttribute('tipo')+'_'+Dom.get('v_'+name).value;
    else
	var value = Dom.get('v_'+name).value;

    var request='ar_assets.php?tipo=ep_update&key='+ escape(name)+'&value='+ escape(value);
    // alert(request)
    YAHOO.util.Connect.asyncRequest('POST',request ,{
	    success:function(o) {
		//alert(o.responseText);
		
		var r =  YAHOO.lang.JSON.parse(o.responseText);
		if(r.ok){
		Dom.get(name+'_save').style.visibility='hidden';
		Dom.get('v_'+name).setAttribute('ovalue',value);
		Dom.get('edit_messages').innerHTML=r.msg;
		}else{
		    Dom.get('edit_messages').innerHTML='<span class="error">'+r.msg+'</span>';
		}
	    }
	});

}

function delete_list_item (e,id){
    
    cat_td=YAHOO.util.Dom.get('cat_'+id);
    saved=cat_td.getAttribute('saved');
    
    if(cat_td.getAttribute('tipo')==1){
	cat_td.style.textDecoration = 'line-through';
	cat_td.style.color = '#777';
	
	
	YAHOO.util.Dom.get('cat_t_'+id).src='art/icons/arrow_rotate_anticlockwise.png';
	if(saved==1)
	    num_changed++;
	else
	    num_changed--;
	cat_td.setAttribute('tipo',0);
	var new_cat= new Array();
	
	var current_cat=Dom.get('v_cat').value;
	      //	      alert(current_cat);
	current_cat=current_cat.split(',');
	
	
	for (x in current_cat){
	    //		  alert(current_cat[x]+' '+id);
		  if(current_cat[x]!=id)
		      new_cat.push(current_cat[x])
	      }

	      Dom.get('v_cat').value=new_cat.join(',');
	      //alert(Dom.get('v_cat').value)
		  
	      

		  }else{

	      cat_td.style.textDecoration = 'none';
	      cat_td.style.color = '#000';
	      YAHOO.util.Dom.get('cat_t_'+id).src='art/icons/cross.png';
	      if(saved==1)
		  num_changed--;
	      else
		  num_changed++;

	      cat_td.setAttribute('tipo',1);

	      var v_cat=new Array();
	      v_cat=Dom.get('v_cat').value;
	      v_cat=v_cat.split(',');
	      v_cat.push(id);
	      Dom.get('v_cat').value=v_cat.join(',');
	      


	      
	  }
	  //	  alert(num_changed);
    interpet_changes();
}








    var check_number = function(e){
	re=<?=$regex['thousand_sep']?>;
	value=this.value.replace(re,'')
	re=<?=$regex['number']?>;
	re_strict=<?=$regex['strict_number']?>;

	if(!re.test(value)){
	    this.className='text aright error';
	}else if(!re_strict.test(this.value)){
	    this.className='text aright warning';
	}else
	    this.className='text aright ok';
    };

    var check_dimension = function(e,scope){
     
     
	if(typeof(scope)=='undefined')
	    scope=this;
     
	 
	tipo=Dom.get(scope.id+'_shape').selectedIndex;

	if(tipo==0){
	    scope.className='text aright error';
	    return
		}else if(tipo==1)
	    re=<?=$regex['dimension3']?>;
	else if(tipo==3 || tipo==5)
	    re=<?=$regex['dimension2']?>;
	else if(tipo==2 || tipo==4)
	    re=<?=$regex['dimension1']?>;


	re_prepare=<?=$regex['thousand_sep']?>;
	value=scope.value.replace(re_prepare,'')



	if(!re.test(value)){
	    scope.className='text aright error';
	}else
	    scope.className='text aright ok';
    }
    var change_shape= function(e){
	tipo=this.selectedIndex;
	shape_examples=new Array(<?='"'.join('","',$_shape_example).'"'?>)
	Dom.get(this.id+'_ex').innerHTML=shape_examples[tipo];
	check_dimension('',Dom.get(this.id.replace(/_shape/,'')))

    }

    var vadilate = function(o){
	if(o.getAttribute('tipo')=='money' || o.getAttribute('tipo')=='number'  || o.getAttribute('tipo')=='shape2' || o.getAttribute('tipo')=='shape4' ){
	    if(isNaN(o.value))
		 return false;
	    if(o.value.match(/[a-z]/))
		return false;

	}else if(o.getAttribute('tipo')=='shape3' || o.getAttribute('tipo')=='shape5'){
	    if(!o.value.match(/[0-9\.\,]x[0-9\.\,]/))
		return false;
	}else if(o.getAttribute('tipo')=='shape1'){
	    if(!o.value.match(/[0-9\.\,]x[0-9\.\,]x[0-9\.\,]/))
		return false;
	}else if(o.getAttribute('tipo')=='shape0'){
	    return false;
	}else if( o.getAttribute('tipo')=='text_nonull'  ){
	    if(o.value==''){
		return false;
	    }
	}else if(!o.value.match(/[a-z]/))
	    return false;
	
	
	return true;
    }
    var change_textarea=function(e,name){
	//editor.saveHTML(); 
	//html = editor.get('element').value; 

	Dom.get('details_save').style.display='';

    }




var handleSuccess = function(o){
    //    alert(o.responseText);
    var r =  YAHOO.lang.JSON.parse(o.responseText);
    if (r.state == 200) {
	YAHOO.util.Event.removeListener('save', "click");
	Dom.get('save').className='disabled';
	Dom.get('exit').className='ok';
	for (x in r.res){
	    if(r.res[x]['res']==1){
		
		num_changed--;
		Dom.get('c_'+x).style.visibility='visible';
		Dom.get('c_'+x).style.src='art/icons/accept.png';
		var attributes = {opacity: { to: 0 }};
		YAHOO.util.Dom.setStyle('c_'+x, 'opacity', 1);
		var myAnim = new YAHOO.util.Anim('c_'+x, attributes); 
		myAnim.duration = 10; 
		myAnim.animate(); 
		if(x=='details'){
		    Dom.get('i_'+x).style.visibility='hidden';
		}else{
		    Dom.get('v_'+x).className='';
		    Dom.get('v_'+x).setAttribute("ovalue",r.res[x]['new_value']);
		}

	    }else if(r.res[x]['res']==0){
		Dom.get('v_'.x).className='error';
	    }
		
	}

	interpet_changes();

    }
};

var handleFailure = function(o){

};



var callback =
{

    success:handleSuccess,
    failure:handleFailure,
    argument:['foo','bar']
};




var add_list_element=function(e){
    
    var box=Dom.get(this.getAttribute('box'));
    var selected=box.selectedIndex;
    var name=box.options[selected].text;
    var id=box.options[selected].getAttribute('cat_id');
    
    // disable parents 
    var parents=box.options[selected].getAttribute('parents');
    if(parents!=''){
	var _parents = new Array();
	_parents = parents.split(',');
	Dom.get('cat_o_'+id).setAttribute('disabled','disabled');
	for (x in _parents){
	    
	    Dom.get('cat_o_'+_parents[x]).setAttribute('disabled','disabled');
	}
    }
    //add tr to the cat table
    
    table=Dom.get(box.name+'_list');
    var newRow = table.insertRow(0);
    var newCell = newRow.insertCell(0);
    newCell.innerHTML = '<img  src="art/icons/cross.png"  id="cat_t_'+id+'" cat_id="'+id+'" style="cursor:pointer" />';
    YAHOO.util.Event.addListener(newCell, "click", delete_list_item,id);
    var newCell = newRow.insertCell(0);
    newCell.innerHTML = name;
    newCell.id='cat_'+id;
    newCell.setAttribute('tipo','1');
    newCell.setAttribute('saves','0');
    
    YAHOO.util.Event.removeListener('add_cat');
    num_changed++;
    

    
    var v_cat=new Array();
    v_cat=Dom.get('v_cat').value;
    v_cat=v_cat.split(',');
    v_cat.push(id);
    Dom.get('v_cat').value=v_cat.join(',');
    
    
	    
	    

    interpet_changes();
}
var prepare_list_element=function(e){
    selected=this.selectedIndex;
    prev=this.getAttribute('prev')
    if(!(prev==0 || selected==prev))
	alert(prev+' '+selected)
	    };
function change_units_tipo(id,name,sname){
    var current=Dom.get('v_units_tipo').getAttribute('ovalue');
    if(id!=current){
	Dom.get('v_units_tipo').innerHTML=name;
	Dom.get('units_tipo_plural').innerHTML=sname;
	Dom.get('v_units_tipo').setAttribute('value',id);
	Dom.get('units_tipo_save').style.visibility='visible';
    }else{
	Dom.get('units_tipo_save').style.visibility='hidden';

    }
    
}
var change_list_element=function(e){
	    
    selected=this.selectedIndex;
    if(selected==0){
	
    }else{
	item_name=this.options[selected].getAttribute('iname')
	this.options[selected].text=item_name;
	YAHOO.util.Event.addListener('add_cat', "click", add_list_element);
	
	prev=this.getAttribute('prev')
	if(prev>0)
	    this.options[prev].text=this.options[prev].getAttribute('sname');
	
	

	this.setAttribute('prev',selected)
    }
}

    function save_price(key){

	new_value=Dom.get('v_'+key).value;
	//	alert(key+' >'+new_value+'<');
	if(key=='rrp' && new_value=='')
	    value='';
	else
	    value=FormatNumber(Dom.get('v_'+key).value,'.','',2);
	var request='ar_assets.php?tipo=ep_update&key='+escape(key)+'&value='+escape(value);
	//	alert(request);
	YAHOO.util.Connect.asyncRequest('POST',request ,{
		success:function(o) {
		     alert(o.responseText);
		    var r =  YAHOO.lang.JSON.parse(o.responseText);
		    //for(x in r['res'])
		    //	alert(x+' '+r[x])
		    if(r.ok){
			//Dom.get(key+'_change').innerHTML='';
			Dom.get(key+'_save').style.visibility='hidden';
			Dom.get('v_'+key).setAttribute('ovalue',new_value);
		    }
		    Dom.get('edit_messages').innerHTML=r.msg;
		}
		 
	    });
    }


function save_image(key,image_id){
    new_value=Dom.get(key+image_id).value;
    var request='ar_assets.php?tipo=ep_update&key='+escape(key)+'&value='+escape(new_value)+'&image_id='+image_id;
	YAHOO.util.Connect.asyncRequest('POST',request ,{
		success:function(o) {
		    alert(o.responseText);
		    var r =  YAHOO.lang.JSON.parse(o.responseText);
		    //for(x in r['res'])
		    //	alert(x+' '+r[x])
		    if(r.ok){
			
			Dom.get('save_'+key+image_id).style.visibility='hidden';
			Dom.get(key+image_id).setAttribute('ovalue',new_value);
		    }else
			alert(r.msg);
		}
		 
	    });

}

 function save_description(key){

	new_value=Dom.get('v_'+key).value;
	if(key=='rrp' && new_value=='')
	    value='';
	else if(key=='price' && key=='rrp')
	    value=FormatNumber(Dom.get('v_'+key).value,'.','',2);
	else if (key=='details'){
	    	editor.saveHTML(); 
		value = editor.get('element').value;
		new_value=value;
	}else
	    value=Dom.get('v_'+key).value;
	var request='ar_assets.php?tipo=ep_update&key='+escape(key)+'&value='+value;
	//	alert(request);
	YAHOO.util.Connect.asyncRequest('POST',request ,{
		success:function(o) {
		    alert(o.responseText);
		    var r =  YAHOO.lang.JSON.parse(o.responseText);
		    //for(x in r['res'])
		    //	alert(x+' '+r[x])
		    if(r.ok){
			//	Dom.get(key+'_change').innerHTML='';
			Dom.get(key+'_save').style.visibility='hidden';
			//Dom.get(key+'_icon').style.visibility='hidden';
			Dom.get('v_'+key).setAttribute('ovalue',new_value);
			Dom.get("edit_messages").innerHTML=r.msg;
		    }else
			Dom.get("edit_messages").innerHTML=r.msg;
		}
		 
	    });
    }




var change_block = function(e){
    

    Dom.get('d_suppliers').style.display='none';
    Dom.get('d_pictures').style.display='none';
    Dom.get('d_suppliers').style.display='none';
    Dom.get('d_prices').style.display='none';
    Dom.get('d_dimat').style.display='none';
    Dom.get('d_config').style.display='none';
    
    Dom.get('d_description').style.display='none';
    Dom.get('d_'+this.id).style.display='';
    

    Dom.get('config').className='';

    Dom.get('suppliers').className='';
    Dom.get('pictures').className='';
    Dom.get('suppliers').className='';
    Dom.get('prices').className='';
    Dom.get('dimat').className='';
    Dom.get('description').className='';
    Dom.get(this.id).className='selected';
    
    YAHOO.util.Connect.asyncRequest('POST','ar_sessions.php?tipo=update&keys=product-edit&value='+this.id );
    
    editing=this.id;


}

function init(){


    //var ids = ["v_description","v_sdescription"]; 
    //	YAHOO.util.Event.addListener(ids, "keyup", change_element);

	var ids = ["cat_select"]; 
	YAHOO.util.Event.addListener(ids, "change", change_list_element);
	var ids = ["description","pictures","prices","suppliers","dimat","config"]; 
	YAHOO.util.Event.addListener(ids, "click", change_block);
	
	//	YAHOO.util.Event.addListener(ids, "click", prepare_list_element);


	//	var ids = ["v_details"]; 
	//YAHOO.util.Event.addListener(ids, "keyup", change_textarea);




	//Tooltips
	//var myTooltip = new YAHOO.widget.Tooltip("myTooltip", { context:"upo_label,outall_label,awoutall_label,awoutq_label"} ); 


	//Details textarea editor ---------------------------------------------------------------------
	var texteditorConfig = {
	    height: '300px',
	    width: '730px',
	    dompath: true,
	    focusAtStart: true
	};     

 	editor = new YAHOO.widget.Editor('v_details', texteditorConfig);

	editor._defaultToolbar.buttonType = 'basic';
 	editor.render();

	editor.on('editorKeyUp',change_textarea,'details' );
	//-------------------------------------------------------------


	cat_list = new YAHOO.widget.Menu("catlist", {context:["browse_cat","tr", "br","beforeShow"]  });

	cat_list.render();

	cat_list.subscribe("show", cat_list.focus);

	YAHOO.util.Event.addListener("browse_cat", "click", cat_list.show, null, cat_list); 




 var onUploadButtonClick = function(e){
    //the second argument of setForm is crucial,
    //which tells Connection Manager this is a file upload form
    YAHOO.util.Connect.setForm('testForm', true);

    var uploadHandler = {
      upload: function(o) {
	    alert(o.responseText);
	    var r =  YAHOO.lang.JSON.parse(o.responseText);
	    if(r.ok){
		var images=Dom.get('images');
		var image_div=document.createElement("div");
		image_div.setAttribute("id", "image"+r.data.id);
		image_div.setAttribute("class",'image');

		var name_div=document.createElement("div");
		name_div.innerHTML=r.data.name;
		
		
		var picture_img=document.createElement("img");
		picture_img.setAttribute("src", r.data.med);
		picture_img.setAttribute("class", 'picture');

		var operations_div=document.createElement("div");
		operations_div.setAttribute("class",'operations');
		var set_principal_span=document.createElement("span");
		set_principal_span.setAttribute("class",'img_set_principal');
		set_principal_span.style.cursor='pointer';
		
		var set_principal_img=document.createElement("img");
		set_principal_img.setAttribute("id", "img_set_principal"+r.data.id);
		set_principal_img.setAttribute("image_id", r.data.id);


		set_principal_img.setAttribute("onClick", 'set_image_as_principal(this)');
		
		if(r.is_principal==1){
		    Dom.get('images').setAttribute('principal',r.data.id)
		    set_principal_img.setAttribute("principal", 1);
		    set_principal_img.setAttribute("src", 'art/icons/asterisk_orange.png');
		    set_principal_img.setAttribute("title", "<?=_('Main Image')?>");
		}else{
		    set_principal_img.setAttribute("principal", 0);
		    set_principal_img.setAttribute("src", 'art/icons/picture_empty.png');
		    set_principal_img.setAttribute("title", "<?=_('Set as the principal image')?>");
		}	



		set_principal_span.appendChild(set_principal_img);
		var delete_span=document.createElement("span");
		delete_span.style.cursor='pointer';
		delete_span.innerHTML='<?=_('Delete')?> <img src="art/icons/cross.png">';
		delete_span.setAttribute("onClick", 'delete_image('+r.data.id+',"'+r.data.name+'")');

		operations_div.appendChild(set_principal_span);
		operations_div.appendChild(delete_span);


		var caption_div=document.createElement("div");
		caption_div.setAttribute("class",'caption');
		var caption_tag_div=document.createElement("div");
		caption_tag_div.innerHTML='<?=_('Caption')?>:';
		var save_caption_span=document.createElement("span");
		save_caption_span.setAttribute("class",'save');
		var save_caption_img=document.createElement("img");
		save_caption_img.setAttribute("src",'art/icons/disk.png');
		save_caption_img.setAttribute("title",'<?=_('Save caption')?>');
		save_caption_img.setAttribute("id",'save_img_caption'+r.data.id);
		save_caption_img.setAttribute("onClick",'save_image("img_caption",'+r.data.id+')');
		save_caption_img.setAttribute("class",'caption');


		var caption_textarea=document.createElement("textarea");
		caption_textarea.setAttribute("id",'img_caption'+r.data.id);
		caption_textarea.setAttribute("image_id",r.data.id);
		caption_textarea.setAttribute("ovalue",'');
		caption_textarea.setAttribute("onkeydown",'caption_changed(this)');
		caption_textarea.setAttribute("class",'caption');
		//caption_textarea.style.width='150px';

		save_caption_span.appendChild(save_caption_img);
		caption_div.appendChild(caption_tag_div);
		caption_div.appendChild(save_caption_span);
		caption_div.appendChild(caption_textarea);

		image_div.appendChild(name_div);
		image_div.appendChild(picture_img);
		image_div.appendChild(operations_div);
		image_div.appendChild(caption_div);

		images.appendChild(image_div);


	    }else
		alert(r.msg);
	    
	    

      }
    };
    var request='ar_assets.php?tipo=ep_update&key=img_new&value=';
    YAHOO.util.Connect.asyncRequest('POST',request, uploadHandler);
  };
  YAHOO.util.Event.on('uploadButton', 'click', onUploadButtonClick);



}

var change_dim_tipo=function(tipo){
    Dom.get('dim_shape').innerHTML=shapes[tipo];
    Dom.get('dim_shape_example').innerHTML=shapes_example[tipo];

    Dom.get('v_dim').setAttribute('tipo','shape'+tipo);
    dim_changed(Dom.get('v_dim'));
}

YAHOO.util.Event.onContentReady("shapes", function () {

	var oMenu = new YAHOO.widget.Menu("shapes", { context:["dim_shape","tr", "br"]  });
	oMenu.render();
	oMenu.subscribe("show", oMenu.focus);
	YAHOO.util.Event.addListener("dim_shape", "click", oMenu.show, null, oMenu);
    });



    YAHOO.util.Event.onDOMReady(init);


var supplier_selected=function(sType, aArgs){
    var myAC = aArgs[0]; // reference back to the AC instance
    var elLI = aArgs[1]; // reference to the selected LI element
    var oData = aArgs[2]; // object literal of selected item's result data

    Dom.get('new_supplier_form').style.display='';

    Dom.setStyle('current_suppliers_form', 'opacity', .25); 

    Dom.get('new_supplier_name').innerHTML=oData.names;
    Dom.get('new_supplier_form').setAttribute('supplier_id',oData.id);
    Dom.get('new_supplier_input').value='';
    Dom.get('new_supplier_cost').value='';
    Dom.get('new_supplier_code').value='';


};

var save_supplier=function(supplier_id){
    var cost=Dom.get('v_supplier_cost'+supplier_id).value;
    var code=Dom.get('v_supplier_code'+supplier_id).value;
    var request='ar_assets.php?tipo=ep_update&key=supplier&value='+ escape(supplier_id)+'&sup_cost='+ escape(cost)+'&sup_code='+ escape(code);
    YAHOO.util.Connect.asyncRequest('POST',request ,{
	    success:function(o) {
			alert(o.responseText)
		var r =  YAHOO.lang.JSON.parse(o.responseText);
		if (r.ok) {
		    Dom.get('save_supplier_'+supplier_id).style.visibility='hidden';
		}else
		    Dom.get('edit_messages').innerHTML='<span class="error">'+r.msg+'</span>';
	    }
	});
};



var save_new_supplier=function(){
    var cost=Dom.get('new_supplier_cost').value;
    var code=Dom.get('new_supplier_code').value;
    var supplier_id=Dom.get('new_supplier_form').getAttribute('supplier_id');
    var request='ar_assets.php?tipo=ep_update&key=supplier_new&value='+ escape(supplier_id)+'&sup_cost='+ escape(cost)+'&sup_code='+ escape(code);

    YAHOO.util.Connect.asyncRequest('POST',request ,{
	    success:function(o) {
		alert(o.responseText)
		var r =  YAHOO.lang.JSON.parse(o.responseText);

		if (r.ok) {

		    tbody=Dom.get("current_suppliers_form");
		    
		    var tr = document.createElement("tr");
		    tr.setAttribute("id",'sup_tr1_'+supplier_id );
		    tr.setAttribute("class","top title" );
		    var td = document.createElement("td");
		    td.setAttribute("class","label" );
		    td.setAttribute("colspan","2" );
		    var img = document.createElement("img");
		    img.setAttribute("class","icon" );
		    img.setAttribute("id","delete_supplier_"+supplier_id );
		    img.setAttribute("onClick","delete_supplier("+supplier_id+",'"+r.data.code+"')");
		    img.setAttribute("src","art/icons/cross.png");
		    td.appendChild(img);
		    var img = document.createElement("img");
		    img.setAttribute("class","icon" );
		    img.setAttribute("id","save_supplier_"+supplier_id );
		    img.setAttribute("onClick","save_supplier("+supplier_id+")");
		    img.setAttribute("src","art/icons/disk.png");
		    img.setAttribute("style","visibility:hidden");
		    td.appendChild(img);
		    var txt = document.createElement("textNode");
		    txt.innerHTML=r.data.code;
		    td.appendChild(txt);
		    tr.appendChild(td);
		    tbody.appendChild(tr);


		    var tr = document.createElement("tr");
		    tr.setAttribute("id",'sup_tr2_'+supplier_id );
		    var td = document.createElement("td");
		    td.setAttribute("class","label" );
		    td.setAttribute("style","width:15em" );
		    td.innerHTML='<?=_('Suppliers product code')?>:';
		    tr.appendChild(td);
		    var td = document.createElement("td");
		    td.setAttribute("style","text-align:left;" );
		    var input = document.createElement("input");
		    input.setAttribute("id","v_supplier_code"+supplier_id );
		    input.setAttribute("style","padding-left:2px;text-align:left;width:10em" );
		    input.setAttribute("ovalue",r.data.supplier_product_code );
		    input.setAttribute("name",'code' );
		    input.setAttribute("onkeyup","supplier_changed(this,"+supplier_id+")" );
		    input.value=r.data.supplier_product_code;
		    td.appendChild(input);
		    tr.appendChild(td);
		    tbody.appendChild(tr);

		    var tr = document.createElement("tr");
		    tr.setAttribute("id",'sup_tr3_'+supplier_id );
		    var td = document.createElement("td");
		    td.setAttribute("class","label" );
		    td.innerHTML='<?=_('Cost per')?> '+r.units_tipo_name+':';
		    tr.appendChild(td);

		    var td = document.createElement("td");
		    td.setAttribute("style","text-align:left" );

		    var txt = document.createElement("textNode");
		    txt.innerHTML=r.currency;

		    var input = document.createElement("input");
		    input.setAttribute("id","v_supplier_cost"+supplier_id );
		    input.setAttribute("style","text-align:right;width:6em" );
		    input.setAttribute("ovalue",r.data.price );
		    input.setAttribute("name",'price' );
		    input.setAttribute("onblur","this.value=FormatNumber(this.value,'"+r.decimal_point+"','"+r.thosusand_sep+"',4);supplier_changed(this,"+supplier_id+")" );
		    input.value=r.data.price;
		    
		    td.appendChild(txt);
		    td.appendChild(input);
		    tr.appendChild(td);
		    tbody.appendChild(tr);
		    var tr = document.createElement("tr");
		    tr.setAttribute("id",'sup_tr4_'+supplier_id );
		    var td = document.createElement("td");
		    td.setAttribute("colspan","2" );
		    tr.appendChild(td);
		    tbody.appendChild(tr);
		    Dom.get('new_supplier_form').style.display='none';
		    Dom.setStyle('current_suppliers_form', 'opacity', 1); 
		}else
		    Dom.get('edit_messages').innerHTML='<span class="error">'+r.msg+'</span>';
	    }
	});
};
var cancel_new_supplier=function(){
    Dom.setStyle('current_suppliers_form', 'opacity', 1.0); 
    Dom.get('new_supplier_form').style.display='none';
    Dom.get('new_supplier_name').innerHTML='';
    Dom.get('new_supplier_form').setAttribute('supplier_id','');


}	





YAHOO.util.Event.onContentReady("adding_new_supplier", function () {
	var oDS = new YAHOO.util.XHRDataSource("ar_suppliers.php");
 	oDS.responseType = YAHOO.util.XHRDataSource.TYPE_JSON;
 	oDS.responseSchema = {
 	    resultsList : "data",
 	    fields : ["name","code","id","names"]
 	};

 	var oAC = new YAHOO.widget.AutoComplete("new_supplier_input", "new_supplier_container", oDS);
 	oAC.resultTypeList = false; 
	oAC.generateRequest = function(sQuery) {
	    // alert("?tipo=suppliers_name&except_product=<?=$_SESSION['state']['product']['id']?>&query=" + sQuery)
 	    return "?tipo=suppliers_name&except_product=<?=$_SESSION['state']['product']['id']?>&query=" + sQuery ;
 	};
	oAC.forceSelection = true; 
	oAC.itemSelectEvent.subscribe(supplier_selected); 
    });


YAHOO.util.Event.onContentReady("units_tipo_list", function () {
	 var oMenu = new YAHOO.widget.Menu("units_tipo_list", { context:["v_units_tipo","tr", "br"]  });
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
	 YAHOO.util.Event.addListener("v_units_tipo", "click", oMenu.show, null, oMenu);
    });

