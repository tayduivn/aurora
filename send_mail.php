<?php
	include('common.php');
	if(!isset($_REQUEST['send_mail']))
	{
	    header('Location: index.php');
	    exit();
	}	
	
	$mail_list = array();		//select the list of members to send mail
	$mail_list = $_SESSION['check_email'];

	if(isset($_REQUEST['template']))
	{
		$template = $_REQUEST['template'];

		if($template=='basic'){
			$template = "include('basic_template_design.php')";
		}

		if($template=='newsletter1'){
			$template = "include('newsletter_template1_design.php')";
		}	

		if($template=='newsletter2'){
			$template = "include('newsletter_template2_design.php')";
		}	

		if($template=='postcard'){
			$template = "include('postcard_template_design.php')";
		}
	}

if($mail_list != '')
{
	foreach($mail_list as $key=>$mail)
	{	
		
		$sql = "select * from `Email Campaign Dimension` where `Email Campaign Key` = '".$mail."'";
		$query = mysql_query($sql);
		$row = mysql_fetch_assoc($query);
		
		$name = $row['Email Campaign Name'];
	
		$objective = $row['Email Campaign Objective'];

		$content = $row['Email Campaign Content'];		

		//pick up the customer mail to send
		$runSql = "select `Customer Key` from `Email Campaign Mailing List` where `Email Campaign Key` = '".$mail."'";
		$runQuery = mysql_query($runSql);
		$fetchRow = mysql_fetch_assoc($runQuery);

		
		
		//fetch the customer base from Customer List Customer Bridge
		$exeSql = "select `Customer Key` from `Customer List Customer Bridge` where `Customer List Key` = '".$fetchRow['Customer Key']."'";
		$exeQuery = mysql_query($exeSql);
		

		while($exeRow = mysql_fetch_assoc($exeQuery))
		{
			$printSql = "select `Customer Main Plain Email` from `Customer Dimension` where `Customer Key` = '".$exeRow['Customer Key']."'";
			$resultSet = mysql_query($printSql);
			$execute = mysql_fetch_assoc($resultSet);

			
			$to = $execute['Customer Main Plain Email'];
			$from = "rulovico@gmail.com";
			$subject = "Hello! This is HTML email";
			
		
			//end of message
			$headers  = "From: $from\r\n";
			$headers .= "Content-type: text/html\r\n";

			//options to send to cc+bcc
			//$headers .= "Cc: [email]satyajit@primediart.com[/email]";
			//$headers .= "Bcc: [email]satyajit@primediart.com[/email]";

			
			// now lets send the email.
			mail($to, $subject, $template, $headers);

			

		}	
			
	}
			$_SESSION['msg'] = "Message has been sent....!";			
			@header('location:campaign_builder.php');		

}
else
{
			$_SESSION['msg'] = "Please select the campaign";
			@header('location:campaign_builder.php');		
}
?>
