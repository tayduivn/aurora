
if(!Array.indexOf){
	    Array.prototype.indexOf = function(obj){
	        for(var i=0; i<this.length; i++){
	            if(this[i]==obj){
	                return i;
	            }
	        }
	        return -1;
	    }
	}




 YAHOO.util.Event.onContentReady("langmenu", function () {
	 var oMenu = new YAHOO.widget.Menu("langmenu", { context:["language_flag","tr", "br"]  });
	 oMenu.render();
	 oMenu.subscribe("show", oMenu.focus);
	 YAHOO.util.Event.addListener("language_flag", "click", oMenu.show, null, oMenu);
    
    });


function gup( name )
{
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( window.location.href );
  if( results == null )
    return "";
  else
    return results[1];
}

function gup_str( name,str )
{
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( str );
  if( results == null )
    return "";
  else
    return results[1];
}


// function validate_email(email){
//     if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email))
// 	return (true);
//     else
// 	return (false);
// }

function isValidURL(url){
    var RegExp = /^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/;
    if(RegExp.test(url)){
        return true;
    }else{
        return false;
    } 
}


function isValidEmail(email){
    var RegExp = /^((([a-z]|[0-9]|!|#|$|%|&|'|\*|\+|\-|\/|=|\?|\^|_|`|\{|\||\}|~)+(\.([a-z]|[0-9]|!|#|$|%|&|'|\*|\+|\-|\/|=|\?|\^|_|`|\{|\||\}|~)+)*)@((((([a-z]|[0-9])([a-z]|[0-9]|\-){0,61}([a-z]|[0-9])\.))*([a-z]|[0-9])([a-z]|[0-9]|\-){0,61}([a-z]|[0-9])\.)[\w]{2,4}|(((([0-9]){1,3}\.){3}([0-9]){1,3}))|(\[((([0-9]){1,3}\.){3}([0-9]){1,3})\])))$/
	if(RegExp.test(email)){
	    return true;
	}else{
	    return false;
	}
}


function isValidNumber(number,ok_null){
    if(ok_null){
	if(number=='')
	    return true

    }
    
    var RegExp = /^[0-9\s]+$/
	if(RegExp.test(number)){
	    return true;
	}else{
	    return false;
	}
}


function xemailcheck(str) {

		var at="@"
		var dot="."
		var lat=str.indexOf(at)
		var lstr=str.length
		var ldot=str.indexOf(dot)
		if (str.indexOf(at)==-1){
		   
		   return false
		}

		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		   
		   return false
		}

		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		    
		    return false
		}

		 if (str.indexOf(at,(lat+1))!=-1){
		    
		    return false
		 }

		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		    
		    return false
		 }

		 if (str.indexOf(dot,(lat+2))==-1){
		    
		    return false
		 }
		
		 if (str.indexOf(" ")!=-1){
		    
		    return false
		 }

 		 return true					
	}



function number_format( number, decimals, dec_point, thousands_sep ) {
 
    var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
    var d = dec_point == undefined ? "," : dec_point;
    var t = thousands_sep == undefined ? "." : thousands_sep, s = n < 0 ? "-" : "";
    var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
    
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}


function key_filter(e,filter)
{
var keynum;
var keychar;
var numcheck;

if(window.event) // IE
  {
  keynum = e.keyCode;
  }
else if(e.which) // Netscape/Firefox/Opera
  {
  keynum = e.which;
  }


 if(typeof(keynum)=='undefined')
   return
keychar = String.fromCharCode(keynum);

 return filter.test(keychar);
 

}
function appendRow(tblId)
{
	var tbl = document.getElementById(tblId);
	var newRow = tbl.insertRow(tbl.rows.length);
	var newCell = newRow.insertCell(0);
	newCell.innerHTML = 'Hello World!';
}
function deleteLastRow(tblId)
{
	var tbl = document.getElementById(tblId);
	if (tbl.rows.length > 0) tbl.deleteRow(tbl.rows.length - 1);
}
function insertRow(tblId, txtIndex, txtError)
{
	var tbl = document.getElementById(tblId);
	var rowIndex = document.getElementById(txtIndex).value;
	try {
		var newRow = tbl.insertRow(rowIndex);
		var newCell = newRow.insertCell(0);
		newCell.innerHTML = 'Hello World! insert';
	} catch (ex) {
		document.getElementById(txtError).value = ex;
	}
}
function deleteRow(tblId, txtIndex, txtError)
{
	var tbl = document.getElementById(tblId);
	var rowIndex = document.getElementById(txtIndex).value;
	try {
		tbl.deleteRow(rowIndex);
	} catch (ex) {
		document.getElementById(txtError).value = ex;
	}
}


 function updateCal() {
	

     var txtDate1 = document.getElementById("v_calpop"+this.id);
     
     if (txtDate1.value != "") {
	 temp = txtDate1.value.split('-');
	 var date=temp[1]+'/'+temp[0]+'/'+temp[2];
	 
	    this.select(date);
	    
	    var selectedDates = this.getSelectedDates();

	    if (selectedDates.length > 0) {
		var firstDate = selectedDates[0];
		this.cfg.setProperty("pagedate", (firstDate.getMonth()+1) + "/" + firstDate.getFullYear());
		this.render();
	    } else {
		alert("<?=_("Cannot select a date before 1/1/2006 or after 12/31/2008")?>");
	    }
	    
	}
    }

 function handleSelect(type,args,obj) {
		var dates = args[0];
		var date = dates[0];
		var year = date[0], month = date[1], day = date[2];


		if(month<10)
		    month='0'+month;
		if(day<10)
		    day='0'+day;

		var txtDate1 = document.getElementById("v_calpop"+this.id);
		txtDate1.value = day + "-" + month + "-" + year;
		this.hide();
    }

    