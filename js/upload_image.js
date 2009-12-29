function edit_caption(o){

image_key=o.parentNode.parentNode.getAttribute('image_id');
Dom.get('caption'+image_key).style.display='none';
Dom.get('edit_caption'+image_key).style.display='';
Dom.get('img_reset_caption'+image_key).style.display='';
Dom.get('img_save_caption'+image_key).style.display='';
Dom.get('img_edit_caption'+image_key).style.display='none';
Dom.get('img_set_principal'+image_key).style.display='none';
Dom.get('img_principal'+image_key).style.display='none';
}

function reset_caption(o){

image_key=o.parentNode.parentNode.getAttribute('image_id');
Dom.get('edit_caption'+image_key).value=Dom.get('edit_caption'+image_key).getAttribute('ovalue');

Dom.get('caption'+image_key).style.display='';
Dom.get('edit_caption'+image_key).style.display='none';
Dom.get('img_reset_caption'+image_key).style.display='none';
Dom.get('img_save_caption'+image_key).style.display='none';
Dom.get('img_edit_caption'+image_key).style.display='';
Dom.get('img_save_caption'+image_key).src='art/icons/bullet_gray_disk.png';

if(o.parentNode.parentNode.getAttribute('is_principal')=='Yes')
Dom.get('img_principal'+image_key).style.display='';
else
Dom.get('img_set_principal'+image_key).style.display='';
}


function caption_changed(o){
image_key=o.parentNode.getAttribute('image_id');
if(o.value!=o.getAttribute('ovalue')){
Dom.get('img_save_caption'+image_key).src='art/icons/bullet_disk.png';
}else{
Dom.get('img_save_caption'+image_key).src='art/icons/bullet_gray_disk.png';
}


}


function save_caption(o){
image_key=o.parentNode.o.parentNode.getAttribute('image_id');
if(Dom.get('img_save_caption'+image_key).src=='art/icons/bullet_gray_disk.png')
    return;

  var request='ar_edit_assets.php?tipo=update_image&key=caption'+'&image_id='+escape(image_key)+'&scope='+scope+'&scope_key='+scope_key;
    YAHOO.util.Connect.asyncRequest('POST',request ,{
	    success:function(o) {
		var r =  YAHOO.lang.JSON.parse(o.responseText);
		    if(r.state==200){
		       Dom.get('caption'+image_key).innerHTML=r.new_value;
		       Dom.get('edit_caption'+image_key).value=r.new_value;
		       Dom.get('edit_caption'+image_key).setAttribute('ovalue',r.new_value);
			   reset_caption(o);
		    }else
			alert(r.msg);
		}
		 
	    });

    
}

function set_image_as_principal(o){

image_key=o.parentNode.parentNode.getAttribute('image_id');
if(o.parentNode.parentNode.getAttribute('is_principal')=='Yes'){
return;
}



   

    var request='ar_edit_assets.php?tipo=update_image&key=principal&new_value=Yes&image_key='+escape(image_key)+'&scope='+scope+'&scope_key='+scope_key;
    YAHOO.util.Connect.asyncRequest('POST',request ,{
	    success:function(o) {
		var r =  YAHOO.lang.JSON.parse(o.responseText);
		    if(r.state=200){
			var old_principal=Dom.get('images').getAttribute('principal');
			var new_principal=image_key;
			Dom.get('images').setAttribute('principal',new_principal);
			
			
			Dom.get('img_principal'+old_principal).style.display='none';
		    Dom.get('img_set_principal'+old_principal).style.display='';
            Dom.get('img_principal'+new_principal).style.display='';
		    Dom.get('img_set_principal'+new_principal).style.display='none';
			
		    }else
			alert(r.msg);
		}
		 
	    });

}



function delete_image(o){

    
    image_key=o.parentNode.getAttribute('image_id');
    var answer = confirm('Delete');
    if (answer){

	

	var request='ar_edit_assets.php?tipo=delete_image&scope='+scope+'&scope_key='+scope_key+'&image_key='+escape(image_key);
	//	alert(request)
	YAHOO.util.Connect.asyncRequest('POST',request ,{
		success:function(o) {
		    // alert(o.responseText);
		    var r =  YAHOO.lang.JSON.parse(o.responseText);
		    if(r.state==200){
			Dom.get('image_container'+image_key).style.display='none';
			

		    }else
			alert(r.msg);
		}
		
	    });
    }


}



var onUploadButtonClick = function(e){
    //the second argument of setForm is crucial,
    //which tells Connection Manager this is a file upload form
    YAHOO.util.Connect.setForm('testForm', true);
    var request='ar_edit_assets.php?tipo=upload_product_image&scope='+scope+'&id='+scope_key;
    var uploadHandler = {
      upload: function(o) {
	   // alert(o.responseText)
	    var r =  YAHOO.lang.JSON.parse(o.responseText);
	   
	    if(r.state==200){

		var images=Dom.get('images');
		
		var image_div=document.createElement("div");
		image_div.setAttribute("id", "image_container"+r.data.id);
		image_div.setAttribute("class",'image');
		image_div.setAttribute("image_id", r.data.id);
		image_div.setAttribute("is_principal", r.data.is_principal);
				
		var name_div=document.createElement("div");
		name_div.setAttribute("id", "image_name"+r.data.id);
		name_div.innerHTML=r.data.name;		
		
		var delete_img=document.createElement("img");
		delete_img.setAttribute("class",'delete');
		delete_img.setAttribute("src",'art/icons/delete.png');
		delete_img.setAttribute("alt",r.msg.delete);
		delete_img.setAttribute("title",r.msg.delete);
		delete_img.setAttribute("onClick", 'delete(this)');
		
        var picture_img=document.createElement("img");
		picture_img.setAttribute("class",'picture');
		picture_img.setAttribute("src",r.data.small_url);
		
		var operations_div=document.createElement("div");
		operations_div.setAttribute("class",'operations');

		var principal_img=document.createElement("img");
		principal_img.setAttribute("id", "img_principal"+r.data.id);
		principal_img.setAttribute("image_id", r.data.id);
        principal_img.setAttribute("alt",r.msg.principal);
		principal_img.setAttribute("title",r.msg.principal);
		principal_img.setAttribute("src",'art/icons/bullet_star.png');
	
		var set_principal_img=document.createElement("img");
		set_principal_img.setAttribute("id", "img_set_principal"+r.data.id);
		set_principal_img.setAttribute("image_id", r.data.id);
        set_principal_img.setAttribute("onClick", 'set_image_as_principal(this)');
        set_principal_img.setAttribute("alt",r.msg.set_principal);
		set_principal_img.setAttribute("title",r.msg.set_principal);		
		set_principal_img.setAttribute("src",'art/icons/bullet_gray_star.png');

		if(r.data.is_principal!='Yes'){
		 principal_img.setAttribute("style",'display:none');
		}else{
		   set_principal_img.setAttribute("style",'display:none');
		}	

      var edit_caption_img=document.createElement("img");
		edit_caption_img.setAttribute("src",'art/icons/caption.gif');
	    edit_caption_img.setAttribute("alt",r.msg.edit_caption);
		edit_caption_img.setAttribute("title",r.msg.edit_caption);
		edit_caption_img.setAttribute("id",'img_edit_caption'+r.data.id);
		edit_caption_img.setAttribute("onClick",'edit_caption(this)');



        var save_caption_img=document.createElement("img");
		save_caption_img.setAttribute("src",'art/icons/bullet_gray_disk.png');
	    save_caption_img.setAttribute("alt",r.msg.save_caption);
		save_caption_img.setAttribute("title",r.msg.save_caption);
		save_caption_img.setAttribute("id",'img_save_caption'+r.data.id);
		save_caption_img.setAttribute("onClick",'save_caption(this)');
	    save_caption_img.setAttribute("style",'display:none');

        var reset_caption_img=document.createElement("img");
		reset_caption_img.setAttribute("src",'art/icons/bullet_gray_disk.png');
	    reset_caption_img.setAttribute("alt",r.msg.reset_caption);
		reset_caption_img.setAttribute("title",r.msg.reset_caption);
		reset_caption_img.setAttribute("id",'img_reset_caption'+r.data.id);
		reset_caption_img.setAttribute("onClick",'reset_caption(this)');
	    reset_caption_img.setAttribute("style",'display:none');


		var caption_span=document.createElement("span");
		caption_span.setAttribute("class",'caption');
		caption_span.setAttribute("id",'caption'+r.data.id);
		caption_span.innerHTML=r.data.caption;
		
		
		var caption_textarea=document.createElement("textarea");
		caption_textarea.setAttribute("id",'edit_caption'+r.data.id);
		caption_textarea.setAttribute("ovalue",'');
	    caption_textarea.setAttribute("value",'');
	    caption_textarea.setAttribute("class",'edit_caption');

		caption_textarea.setAttribute("onkeyup",'caption_changed(this)');
		caption_textarea.setAttribute("class",'edit_caption');
		caption_textarea.setAttribute("style",'display:none');

		operations_div.appendChild(principal_img);
		operations_div.appendChild(set_principal_img);
		operations_div.appendChild(edit_caption_img);
		operations_div.appendChild(save_caption_img);
		operations_div.appendChild(reset_caption_img);

		image_div.appendChild(name_div);
		image_div.appendChild(delete_img);
		image_div.appendChild(picture_img);
		image_div.appendChild(operations_div);
		image_div.appendChild(caption_span);
		image_div.appendChild(caption_textarea);

		images.insertBefore(image_div,Dom.get('image_footer'));


	    }else
		alert(r.msg);
	    
	    

	}
    };

    YAHOO.util.Connect.asyncRequest('POST',request, uploadHandler);



  };




