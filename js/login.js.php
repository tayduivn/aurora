
function init() {


    var onmySubmit =function(){
	var input_login=document.getElementById("_login_");
	var input_pwd=document.getElementById("_passwd_");
	var input_epwd=document.getElementById("ep");
	var theform=document.getElementById("loginform");

    
     var epwd=sha256_digest(input_pwd.value);
     input_pwd.value='secret';
	input_epwd.value=epwd;
	theform.submit();
    

}

    var log_in=document.getElementById("_login_");
    log_in.focus();
    
    var oPushButton1 = new YAHOO.widget.Button("login_go"); 
    oPushButton1.on("click", onmySubmit); 
    


 }


 YAHOO.util.Event.onDOMReady(init);

