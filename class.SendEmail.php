<?php

include_once('class.DB_Table.php');
require_once("external_libs/mail/email_message.php");
require_once("external_libs/mail/smtp_message.php");
require_once("external_libs/mail/smtp.php");
/* Uncomment when using SASL authentication mechanisms */
require("external_libs/mail/sasl.php");
require_once("class.EmailSend.php");


class SendEmail extends DB_Table {

    function SendEmail($data=false) {


    }

    function set_method($method) {
        $this->method=$method;
    }

    function smtp($data) {

        //$this->email_type=$data['email_type'];
        //$this->recipient_key=$data['recipient_key'];
        //$this->recipient_type=$data['recipient_type'];

  

        $this->method=$data['method'];
 
 

        switch ($this->method) {
        case 'smtp':
        case 'SMTP':
            if (!isset($data['attachement']))
                $data['attachement']=array();

            $this->message_object=new smtp_message_class;
            $this->message_object->localhost="localhost";   /* This computer address */
            $this->message_object->smtp_host="localhost";   /* SMTP server address */
            $this->message_object->smtp_direct_delivery=0;  /* Deliver directly to the recipients destination SMTP server */
            $this->message_object->smtp_exclude_address=""; /* In directly deliver mode, the DNS may return the IP of a sub-domain of the default domain for domains that do not exist. If that is your case, set this variable with that sub-domain address. */
            /*
             * If you use the direct delivery mode and the GetMXRR is not functional, you need to use a replacement function.
             */
            /*
            $_NAMESERVERS=array();
            include("rrcompat.php");
            $this->message_object->smtp_getmxrr="_getmxrr";
            */
            $this->message_object->smtp_user="";            /* authentication user name */
            $this->message_object->smtp_realm="";           /* authentication realm or Windows domain when using NTLM authentication */
            $this->message_object->smtp_workstation="";     /* authentication workstation name when using NTLM authentication */
            $this->message_object->smtp_password="";        /* authentication password */
            $this->message_object->smtp_pop3_auth_host="";  /* if you need POP3 authetntication before SMTP delivery, specify the host name here. The smtp_user and smtp_password above should set to the POP3 user and password */
            $this->message_object->smtp_debug=0;            /* Output dialog with SMTP server */
            $this->message_object->smtp_html_debug=1;       /* If smtp_debug is 1, set this to 1 to output debug information in HTML */


            $this->to='';
            $this->subject='';
            $this->message='';
            $this->additional_headers='';
            $this->additional_parameters='';
 

            switch ($data['type']) {
            case 'Plain':
            case 'plain':
                $this->type='Plain';
                $sql=sprintf("select * from `Email Credentials Dimension` where `Email Credentials Key`=%d", $data['email_credentials_key']);
                $result=mysql_query($sql);
                if ($row=mysql_fetch_array($result)) {

                    $this->from_name=getenv("USERNAME");
                    $this->from_address=$row['Email Address'];
                    $this->sender_line=__LINE__;

                    $this->reply_name=$this->from_name;
                    $this->reply_address=$this->from_address;
                    $this->reply_address=$this->from_address;
                    $this->error_delivery_name=$this->from_name;
                    $this->error_delivery_address=$this->from_address;
                    $this->to_name=$data['to'];
                    $this->to_address=$data['to'];
                    $this->recipient_line=__LINE__;
                    $this->subject=$data['subject'];
                    $this->message=$data['plain'];

                    if (strlen($this->from_address)==0)
                        die("Please set the messages sender address in line ".$this->sender_line." of the script ".basename(__FILE__)."\n");
                    if (strlen($this->to_address)==0)
                        die("Please set the messages recipient address in line ".$this->recipient_line." of the script ".basename(__FILE__)."\n");

                    //$this->message_object=new smtp_message_class;

                    /* This computer address */
                    $this->message_object->localhost="localhost";

                    /* SMTP server address, probably your ISP address,
                     * or smtp.gmail.com for Gmail
                     * or smtp.live.com for Hotmail */
                    $this->message_object->smtp_host=$row['Outgoing Mail Sever'];

                    /* SMTP server port, usually 25 but can be 465 for Gmail */
                    $this->message_object->smtp_port=465;

                    /* Use SSL to connect to the SMTP server. Gmail requires SSL */
                    $this->message_object->smtp_ssl=1;

                    /* Use TLS after connecting to the SMTP server. Hotmail requires TLS */
                    $this->message_object->smtp_start_tls=0;

                    /* Change this variable if you need to connect to SMTP server via an HTTP proxy */
                    $this->message_object->smtp_http_proxy_host_name='';
                    /* Change this variable if you need to connect to SMTP server via an HTTP proxy */
                    $this->message_object->smtp_http_proxy_host_port=3128;

                    /* Change this variable if you need to connect to SMTP server via an SOCKS server */
                    $this->message_object->smtp_socks_host_name = '';
                    /* Change this variable if you need to connect to SMTP server via an SOCKS server */
                    $this->message_object->smtp_socks_host_port = 1080;
                    /* Change this variable if you need to connect to SMTP server via an SOCKS server */
                    $this->message_object->smtp_socks_version = '5';


                    /* Deliver directly to the recipients destination SMTP server */
                    $this->message_object->smtp_direct_delivery=0;

                    /* In directly deliver mode, the DNS may return the IP of a sub-domain of
                     * the default domain for domains that do not exist. If that is your
                     * case, set this variable with that sub-domain address. */
                    $this->message_object->smtp_exclude_address="";

                    /* If you use the direct delivery mode and the GetMXRR is not functional,
                     * you need to use a replacement function. */
                    /*
                    $_NAMESERVERS=array();
                    include("rrcompat.php");
                    $message_object->smtp_getmxrr="_getmxrr";
                    */

                    /* authentication user name */
                    $this->message_object->smtp_user=$row['Login'];

                    /* authentication password */
                    $this->message_object->smtp_password=$row['Password'];

                    /* if you need POP3 authetntication before SMTP delivery,
                     * specify the host name here. The smtp_user and smtp_password above
                     * should set to the POP3 user and password*/
                    $this->message_object->smtp_pop3_auth_host="";

                    /* authentication realm or Windows domain when using NTLM authentication */
                    $this->message_object->smtp_realm="";

                    /* authentication workstation name when using NTLM authentication */
                    $this->message_object->smtp_workstation="";

                    /* force the use of a specific authentication mechanism */
                    $this->message_object->smtp_authentication_mechanism="";

                    /* Output dialog with SMTP server */
                    $this->message_object->smtp_debug=0;

                    /* if smtp_debug is 1,
                     * set this to 1 to make the debug output appear in HTML */
                    $this->message_object->smtp_html_debug=1;

                    /* If you use the SetBulkMail function to send messages to many users,
                     * change this value if your SMTP server does not accept sending
                     * so many messages within the same SMTP connection */
                    $this->message_object->maximum_bulk_deliveries=100;

                    $this->message_object->SetEncodedEmailHeader("To",$this->to_address,$this->to_name);
                    $this->message_object->SetEncodedEmailHeader("From",$this->from_address,$this->from_name);
                    $this->message_object->SetEncodedEmailHeader("Reply-To",$this->reply_address,$this->reply_name);
                    $this->message_object->SetHeader("Return-Path",$this->error_delivery_address);
                    $this->message_object->SetEncodedEmailHeader("Errors-To",$this->error_delivery_address,$this->error_delivery_name);


                    /*
                    	$message_object->SetEncodedHeader("Subject",$subject);
                    	$message_object->AddQuotedPrintableTextPart($message_object->WrapText($message));

                    */

                    /*
                     *  Set the Return-Path header to define the envelope sender address to which bounced messages are delivered.
                     *  If you are using Windows, you need to use the smtp_message_class to set the return-path address.
                     */
                    if (defined("PHP_OS")
                            && strcmp(substr(PHP_OS,0,3),"WIN"))
                        $this->message_object->SetHeader("Return-Path",$this->error_delivery_address);

                    $this->message_object->SetEncodedHeader("Subject",$this->subject);


                    /*
                     *  It is strongly recommended that when you send HTML messages,
                     *  also provide an alternative text version of HTML page,
                     *  even if it is just to say that the message is in HTML,
                     *  because more and more people tend to delete HTML only
                     *  messages assuming that HTML messages are spam.
                     */
                    $this->text_message=$this->message;
                    $this->message_object->CreateQuotedPrintableTextPart($this->message_object->WrapText($this->text_message),"",$this->text_part);

                    /*
                     *  Multiple alternative parts are gathered in multipart/alternative parts.
                     *  It is important that the fanciest part, in this case the HTML part,
                     *  is specified as the last part because that is the way that HTML capable
                     *  mail programs will show that part and not the text version part.
                     */
                    $this->alternative_parts=array(
                                                 $this->text_part
                                             );
                    $this->message_object->AddAlternativeMultipart($this->alternative_parts);


                    //Attachements
                    $text_attachment=array();
                    $image_attachment=array();
                    foreach($data['attachement'] as $value) {
                        if ($value['attachement_type']=='Text') {
                            $text_attachment[]=array(
                                                   'Data'=>$value['Data'],
                                                   'Name'=>$value['Name'],
                                                   'Content-Type'=>$value['Content-Type'],
                                                   'Disposition'=>$value['Disposition']
                                               );
                        }

                        else if ($value['attachement_type']=='Image') {
                            $image_attachment[]=array(
                                                    'FileName'=>$value['FileName'],
                                                    'Content-Type'=>$value['Content-Type'],
                                                    'Disposition'=>$value['Disposition']
                                                );
                        }

                    }


                    foreach($text_attachment as $single_text)
                    $this->message_object->AddFilePart($single_text);

                    foreach($image_attachment as $single_image)
                    $this->message_object->AddFilePart($single_image);





                } else {
                    $this->error=true;
                    $this->msg="Cretentials not found";
                    return array('state'=>400,'msg'=>"Credentials not found");
                }
                break;
            case 'HTML':
            case 'html':
            case 'HTML Template':
            
            
         
            
                $this->type='HTML';

                /*
                	$data=array(
                		'subject'=>	'',
                		'plain'=>'',
                		'html'=>'',
                		'email_credentials_key'=>'',
                		'to'=>'',
                		'bcc'=>''
                	);
                */

                $sql=sprintf("select * from `Email Credentials Dimension` where `Email Credentials Key`=%d", $data['email_credentials_key']);

                $result=mysql_query($sql);
                if ($row=mysql_fetch_array($result)) {

                    $this->from_name=getenv("USERNAME");
                    $this->from_address=$row['Email Address'];
                    $this->sender_line=__LINE__;

                    $this->reply_name=$this->from_name;
                    $this->reply_address=$this->from_address;
                    $this->reply_address=$this->from_address;
                    $this->error_delivery_name=$this->from_name;
                    $this->error_delivery_address=$this->from_address;
                    $this->to_name=$data['to'];
                    $this->to_address=$data['to'];
                    $this->recipient_line=__LINE__;
                    $this->subject=$data['subject'];
                    $this->message=$data['plain'];

                    if (strlen($this->from_address)==0)
                        die("Please set the messages sender address in line ".$this->sender_line." of the script ".basename(__FILE__)."\n");
                    if (strlen($this->to_address)==0)
                        die("Please set the messages recipient address in line ".$this->recipient_line." of the script ".basename(__FILE__)."\n");

                    //$this->message_object=new smtp_message_class;

                    /* This computer address */
                    $this->message_object->localhost="localhost";

                    /* SMTP server address, probably your ISP address,
                     * or smtp.gmail.com for Gmail
                     * or smtp.live.com for Hotmail */
                    $this->message_object->smtp_host=$row['Outgoing Mail Sever'];

                    /* SMTP server port, usually 25 but can be 465 for Gmail */
                    $this->message_object->smtp_port=465;

                    /* Use SSL to connect to the SMTP server. Gmail requires SSL */
                    $this->message_object->smtp_ssl=1;

                    /* Use TLS after connecting to the SMTP server. Hotmail requires TLS */
                    $this->message_object->smtp_start_tls=0;

                    /* Change this variable if you need to connect to SMTP server via an HTTP proxy */
                    $this->message_object->smtp_http_proxy_host_name='';
                    /* Change this variable if you need to connect to SMTP server via an HTTP proxy */
                    $this->message_object->smtp_http_proxy_host_port=3128;

                    /* Change this variable if you need to connect to SMTP server via an SOCKS server */
                    $this->message_object->smtp_socks_host_name = '';
                    /* Change this variable if you need to connect to SMTP server via an SOCKS server */
                    $this->message_object->smtp_socks_host_port = 1080;
                    /* Change this variable if you need to connect to SMTP server via an SOCKS server */
                    $this->message_object->smtp_socks_version = '5';


                    /* Deliver directly to the recipients destination SMTP server */
                    $this->message_object->smtp_direct_delivery=0;

                    /* In directly deliver mode, the DNS may return the IP of a sub-domain of
                     * the default domain for domains that do not exist. If that is your
                     * case, set this variable with that sub-domain address. */
                    $this->message_object->smtp_exclude_address="";

                    /* If you use the direct delivery mode and the GetMXRR is not functional,
                     * you need to use a replacement function. */
                    /*
                    $_NAMESERVERS=array();
                    include("rrcompat.php");
                    $message_object->smtp_getmxrr="_getmxrr";
                    */

                    /* authentication user name */
                    $this->message_object->smtp_user=$row['Login'];

                    /* authentication password */
                    $this->message_object->smtp_password=$row['Password'];

                    /* if you need POP3 authetntication before SMTP delivery,
                     * specify the host name here. The smtp_user and smtp_password above
                     * should set to the POP3 user and password*/
                    $this->message_object->smtp_pop3_auth_host="";

                    /* authentication realm or Windows domain when using NTLM authentication */
                    $this->message_object->smtp_realm="";

                    /* authentication workstation name when using NTLM authentication */
                    $this->message_object->smtp_workstation="";

                    /* force the use of a specific authentication mechanism */
                    $this->message_object->smtp_authentication_mechanism="";

                    /* Output dialog with SMTP server */
                    $this->message_object->smtp_debug=0;

                    /* if smtp_debug is 1,
                     * set this to 1 to make the debug output appear in HTML */
                    $this->message_object->smtp_html_debug=1;

                    /* If you use the SetBulkMail function to send messages to many users,
                     * change this value if your SMTP server does not accept sending
                     * so many messages within the same SMTP connection */
                    $this->message_object->maximum_bulk_deliveries=100;

                    $this->message_object->SetEncodedEmailHeader("To",$this->to_address,$this->to_name);
                    $this->message_object->SetEncodedEmailHeader("From",$this->from_address,$this->from_name);
                    $this->message_object->SetEncodedEmailHeader("Reply-To",$this->reply_address,$this->reply_name);
                    $this->message_object->SetHeader("Return-Path",$this->error_delivery_address);
                    $this->message_object->SetEncodedEmailHeader("Errors-To",$this->error_delivery_address,$this->error_delivery_name);


                    /*
                    	$message_object->SetEncodedHeader("Subject",$subject);
                    	$message_object->AddQuotedPrintableTextPart($message_object->WrapText($message));

                    */

                    /*
                     *  Set the Return-Path header to define the envelope sender address to which bounced messages are delivered.
                     *  If you are using Windows, you need to use the smtp_message_class to set the return-path address.
                     */
                    if (defined("PHP_OS")
                            && strcmp(substr(PHP_OS,0,3),"WIN"))
                        $this->message_object->SetHeader("Return-Path",$this->error_delivery_address);

                    $this->message_object->SetEncodedHeader("Subject",$this->subject);


                    if (isset($data['html']) and $data['html'])
                        $html_msg=$data['html'];
                    else
                        $html_msg='';



                    $this->html_message=$html_msg.$this->get_track_code();
                    $this->message_object->CreateQuotedPrintableHTMLPart($this->html_message,"",$this->html_part);


//print  $this->html_message;

                    /*
                     *  It is strongly recommended that when you send HTML messages,
                     *  also provide an alternative text version of HTML page,
                     *  even if it is just to say that the message is in HTML,
                     *  because more and more people tend to delete HTML only
                     *  messages assuming that HTML messages are spam.
                     */
                    $this->text_message=$this->message;

                    $this->message_object->CreateQuotedPrintableTextPart($this->message_object->WrapText($this->text_message),"",$this->text_part);

                    /*
                     *  Multiple alternative parts are gathered in multipart/alternative parts.
                     *  It is important that the fanciest part, in this case the HTML part,
                     *  is specified as the last part because that is the way that HTML capable
                     *  mail programs will show that part and not the text version part.
                     */
                    $this->alternative_parts=array(

                                                 $this->text_part,
                                                 $this->html_part

                                             );


                    $this->message_object->AddAlternativeMultipart($this->alternative_parts);


                    //Attachements
                    $text_attachment=array();
                    $image_attachment=array();
                    foreach($data['attachement'] as $value) {
                        if ($value['attachement_type']=='Text') {
                            $text_attachment[]=array('Data'=>$value['Data'],
                                                     'Name'=>$value['Name'],
                                                     'Content-Type'=>$value['Content-Type'],
                                                     'Disposition'=>$value['Disposition']
                                                    );
                        }

                        else if ($value['attachement_type']=='Image') {
                            $image_attachment[]=array('FileName'=>$value['FileName'],
                                                      'Content-Type'=>$value['Content-Type'],
                                                      'Disposition'=>$value['Disposition']
                                                     );
                        }

                    }


                    foreach($text_attachment as $single_text)
                    $this->message_object->AddFilePart($single_text);

                    foreach($image_attachment as $single_image)
                    $this->message_object->AddFilePart($single_image);


                } else {

                    $this->error=true;
                    $this->msg="Credentials not found";
                    return array('state'=>400,'msg'=>"Credentials not found");
                }

                break;
            }


            $error=$this->message_object->Send();
            for ($recipient=0,Reset($this->message_object->invalid_recipients); $recipient<count($this->message_object->invalid_recipients); Next($this->message_object->invalid_recipients),$recipient++)
                $response= "Invalid recipient: ".Key($this->message_object->invalid_recipients)." Error: ".$this->message_object->invalid_recipients[Key($this->message_object->invalid_recipients)]."\n";
            if (strcmp($error,"")) {
                $response=  array('state'=>400,'msg'=>$error);

            } else
                $response=  array('state'=>200,'msg'=>'ok');

            return $response;

            break;
        case 'Amazon':
        case 'amazon':
        case 'AMAZON':
            $access_key=$data['access_key'];
            $secret_key=$data['secret_key'];
            if ($access_key==null || $secret_key==null) {
                print 'No access key/ secret key set';
                exit;
            }
            $this->ses = new SimpleEmailService($access_key, $secret_key);
            $this->m = new SimpleEmailServiceMessage();



            $this->m->addTo($data['to']);
            $this->m->setFrom($row['Email Address']);
            $this->m->setSubject($data['subject']);
            $this->m->setReturnPath($data['return_path']);

            switch ($data['type']) {
            case 'plain':
            case 'PLAIN':
                $this->type='Plain';
                $this->m->setMessageFromString($data['plain']);
                break;

            case 'html':
            case 'HTML':
            case 'HTML Template':
                $this->type='HTML';



                $this->m->setMessageFromString($data['plain'], $data['html'].$this->get_track_code());
                break;

            }
            $response=$this->ses->sendEmail($this->m);
            break;
        }

        return $response;
    }




    function send($data) {




        $email_send_data=array(
                             'Email Send Type'=>$data['email_matter'],
                             'Email Send Type Key'=>$data['email_matter_key'],
                             'Email Send Type Parent Key'=>$data['email_matter_parent_key'],
                             'Email Send Recipient Type'=>$data['recipient_type'],
                             'Email Send Recipient Key'=>$data['recipient_key'],
                             'Email Key'=>$data['email_key'],
                             'Email Send Creation Date'=>date('Y-m-d H:i:s',strtotime('now +0:00'))

                         );
        $email_send=new EmailSend();



        $email_send->create($email_send_data);
        $this->send_key=$email_send->id;


        switch ($data['email_matter']) {
        case 'Marketing':
            $sql=sprintf("update `Email Campaign Mailing List`  set `Email Send Key`=%d where `Email Campaign Mailing List Key`=%d ",
                         $this->send_key,
                         $data['email_matter_key']
                        );
            // mysql_query($sql);
            break;
        default:

            break;
        }



        $send_result=$this->smtp($data);

        //print_r($send_result);
        if ($send_result['state']==200) {
            $sql=sprintf("update `Email Send Dimension` set `Email Send Date`=%s where `Email Send Key`=%d",
                         prepare_mysql(date('Y-m-d H:i:s',strtotime('now +0:00'))),
                         $this->send_key);
            mysql_query($sql);

            switch ($data['email_matter']) {
            case 'Marketing':
                $email_campaign=new EmailCampaign($email_send->data['Email Send Type Parent Key']);
                $email_campaign->update_send_emails();
                break;
            default:

                break;
            }




        }


        return $send_result;


    }


    function retry($type) {
        $success=0;
        $fail=0;
        $result='';

        $files=array();
        switch ($type) {
        case 'plain':
            $sql=sprintf("select * from `Email Queue Dimension` where `Status`='No' and `Type`='Plain'");


            $result=mysql_query($sql);
            while ($row=mysql_fetch_array($result)) {
                $sql=sprintf("select * from `Email Queue Attachement Dimension` where `Email Queue Key` = %d", $row['Email Queue Key']);
                $res=mysql_query($sql);
                while ($r=mysql_fetch_array($res)) {
                    $files[]=array('Data'=>$r['Data'],
                                   'Name'=>$r['Name'],
                                   'Content-Type'=>$r['Content-Type'],
                                   'Disposition'=>$r['Disposition'],
                                   'FileName'=>$r['FileName'],
                                   'attachement_type'=>$r['Type']
                                  );
                }


                $data=array(
                          'subject'=>	$row['Subject'],
                          'plain'=>$row['Plain'],
                          'email_credentials_key'=>$row['Email Credentials Key'],
                          'to'=>$row['To'],
                          'bcc'=>$row['BCC'],
                          'attachement'=>$files
                      );

                $this->smtp('plain', $data);
                $res=$this->send();

                if ($res['msg']=='ok') {
                    $sql=sprintf("update `Email Queue Dimension` set `Status`='Yes' where `Email Queue Key`=%d", $row['Email Queue Key']);
                    if (mysql_query($sql))
                        $success++;
                } else
                    $fail++;

            }

            $response=sprintf("%d emails sent. %d has failed", $success, $fail);
            break;

        case 'html':
            $sql=sprintf("select * from `Email Queue Dimension` where `Status`='No' and `Type`='HTML'");
            //$sql=sprintf("select * from `Email Queue Dimension` ");
            //print $sql;
            //	print $sql;
            $result=mysql_query($sql);
            //print "$result";
            while ($row=mysql_fetch_assoc($result)) {
                //print mysql_error();

                $sql=sprintf("select * from `Email Queue Attachement Dimension` where `Email Queue Key` = %d", $row['Email Queue Key']);
                $res=mysql_query($sql);
                while ($r=mysql_fetch_array($res)) {
                    $files[]=array('Data'=>$r['Data'],
                                   'Name'=>$r['Name'],
                                   'Content-Type'=>$r['Content-Type'],
                                   'Disposition'=>$r['Disposition'],
                                   'FileName'=>$r['FileName'],
                                   'attachement_type'=>$r['Type']
                                  );
                }

                $data=array(
                          'subject'=>	$row['Subject'],
                          'plain'=>$row['Plain'],
                          'html'=>$row['HTML'],
                          'email_credentials_key'=>$row['Email Credentials Key'],
                          'to'=>$row['To'],
                          'bcc'=>$row['BCC'],
                          'attachement'=>$files
                      );
                $this->smtp('html', $data);
                $res=$this->send();

                if ($res['msg']=='ok') {
                    $sql=sprintf("update `Email Queue Dimension` set `Status`='Yes' where `Email Queue Key`=%d", $row['Email Queue Key']);
                    if (mysql_query($sql))
                        $success++;
                } else
                    $fail++;

            }
            $response=sprintf("%d emails sent. %d has failed", $success, $fail);
            break;
        }

        return $response;
    }

    function store_in_queue($result, $files=false, $data) {
        if (preg_match('/^could not resolve the host domain/',$result['msg'])) {
            if (isset($data['html']) && $data['html']) {
                $html_msg=$data['html'];
            } else
                $html_msg=null;


            if (isset($data['bcc']) && $data['bcc']) {
                $bcc=$data['bcc'];
            } else
                $bcc=null;

            $sql=sprintf("insert into `Email Queue Dimension` (`To`, `Type`, `Subject`, `Plain`, `HTML`, `Email Credentials Key`, `BCC`) values (%s, %s, %s, %s, %s, %d, %s)	"
                         ,prepare_mysql($data['to'])
                         ,prepare_mysql($data['type'])
                         ,prepare_mysql($data['subject'])
                         ,prepare_mysql($data['plain'])
                         ,prepare_mysql($html_msg)
                         ,$data['email_credentials_key']
                         ,prepare_mysql($bcc)
                        );

            //print $sql;
            $stat=mysql_query($sql);

            $email_queue_key=mysql_insert_id();

            if (isset($files)) {
                foreach($files as $value) {
                    if (isset($value['Data']) && $value['Data']) {
                        $data_temp=$value['Data'];
                    } else
                        $data_temp=null;

                    if (isset($value['FileName']) && $value['FileName']) {
                        $file_name=$value['FileName'];
                    } else
                        $file_name=null;

                    if (isset($value['Name']) && $value['Name']) {
                        $name=$value['Name'];
                    } else
                        $name=null;

                    $sql=sprintf("insert into `Email Queue Attachement Dimension` (`Email Queue Key`, `Data`, `FileName`, `Name`, `Content-Type`, `Disposition`, `Type`) values (%d, %s, %s, %s, %s, %s, %s)"
                                 ,$email_queue_key
                                 ,prepare_mysql($data_temp)
                                 ,prepare_mysql($file_name)
                                 ,prepare_mysql($name)
                                 ,prepare_mysql($value['Content-Type'])
                                 ,prepare_mysql($value['Disposition'])
                                 ,prepare_mysql($value['attachement_type'])
                                );

                    //print prepare_mysql($file_name);
                    $stat = $stat & mysql_query($sql);
                }

            }

            if ($stat)
                $result=array('state'=>400,'msg'=>_('Message will send shortly'));
            else
                $result=array('state'=>400,'msg'=>'Error: Message could not be sent');

        }
        return $result;
    }







    function get_track_code() {

        if (!$this->track or $this->type=='Plain')
            return '';

        $public_path='';

        $sql=sprintf("select * from `Configuration Dimension`");
        $result=mysql_query($sql);
        if ($row=mysql_fetch_array($result)) {
            $public_path=$row['Public Path'];
        }

        $public_path='http://localhost/dw/';

        $code=sprintf('<img src="%s/track.php?s=%s" />', $public_path, $this->send_key);

        return $code;
    }


    function generate_unsubscribe_email($type='Newsletter', $customer_id, $generate=true) {
        if (!$generate) {
            return;
        }

        $link='<a href="localhost/unsubscribe.php?s=$customer_id&type=$type"';
        return $link;
    }

}


class SimpleEmailService {
    protected $__accessKey; // AWS Access key
    protected $__secretKey; // AWS Secret key
    protected $__host;

    public function getAccessKey() {
        return $this->__accessKey;
    }
    public function getSecretKey() {
        return $this->__secretKey;
    }
    public function getHost() {
        return $this->__host;
    }

    protected $__verifyHost = 1;
    protected $__verifyPeer = 1;

    // verifyHost and verifyPeer determine whether curl verifies ssl certificates.
    // It may be necessary to disable these checks on certain systems.
    // These only have an effect if SSL is enabled.
    public function verifyHost() {
        return $this->__verifyHost;
    }
    public function enableVerifyHost($enable = true) {
        $this->__verifyHost = $enable;
    }

    public function verifyPeer() {
        return $this->__verifyPeer;
    }
    public function enableVerifyPeer($enable = true) {
        $this->__verifyPeer = $enable;
    }

    /**
    * Constructor
    *
    * @param string $accessKey Access key
    * @param string $secretKey Secret key
    * @return void
    */
    public function __construct($accessKey = null, $secretKey = null, $host = 'email.us-east-1.amazonaws.com') {

        if ($accessKey !== null && $secretKey !== null) {
            $this->setAuth($accessKey, $secretKey);
        }
        $this->__host = $host;
    }

    /**
    * Set AWS access key and secret key
    *
    * @param string $accessKey Access key
    * @param string $secretKey Secret key
    * @return void
    */
    public function setAuth($accessKey, $secretKey) {
        $this->__accessKey = $accessKey;
        $this->__secretKey = $secretKey;
    }

    /**
    * Lists the email addresses that have been verified and can be used as the 'From' address
    *
    * @return An array containing two items: a list of verified email addresses, and the request id.
    */
    public function listVerifiedEmailAddresses() {
        $rest = new SimpleEmailServiceRequest($this, 'GET');
        $rest->setParameter('Action', 'ListVerifiedEmailAddresses');

        $rest = $rest->getResponse();
        if ($rest->error === false && $rest->code !== 200) {
            $rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
        }
        if ($rest->error !== false) {
            $this->__triggerError('listVerifiedEmailAddresses', $rest->error);
            return false;
        }

        $response = array();
        if (!isset($rest->body)) {
            return $response;
        }

        $addresses = array();
        foreach($rest->body->ListVerifiedEmailAddressesResult->VerifiedEmailAddresses->member as $address) {
            $addresses[] = (string)$address;
        }

        $response['Addresses'] = $addresses;
        $response['RequestId'] = (string)$rest->body->ResponseMetadata->RequestId;

        return $response;
    }

    /**
    * Requests verification of the provided email address, so it can be used
    * as the 'From' address when sending emails through SimpleEmailService.
    *
    * After submitting this request, you should receive a verification email
    * from Amazon at the specified address containing instructions to follow.
    *
    * @param string email The email address to get verified
    * @return The request id for this request.
    */
    public function verifyEmailAddress($email) {
        $rest = new SimpleEmailServiceRequest($this, 'POST');
        $rest->setParameter('Action', 'VerifyEmailAddress');
        $rest->setParameter('EmailAddress', $email);

        $rest = $rest->getResponse();
        if ($rest->error === false && $rest->code !== 200) {
            $rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
        }
        if ($rest->error !== false) {
            $this->__triggerError('verifyEmailAddress', $rest->error);
            return false;
        }

        $response['RequestId'] = (string)$rest->body->ResponseMetadata->RequestId;
        return $response;
    }

    /**
    * Removes the specified email address from the list of verified addresses.
    *
    * @param string email The email address to remove
    * @return The request id for this request.
    */
    public function deleteVerifiedEmailAddress($email) {
        $rest = new SimpleEmailServiceRequest($this, 'DELETE');
        $rest->setParameter('Action', 'DeleteVerifiedEmailAddress');
        $rest->setParameter('EmailAddress', $email);

        $rest = $rest->getResponse();
        if ($rest->error === false && $rest->code !== 200) {
            $rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
        }
        if ($rest->error !== false) {
            $this->__triggerError('deleteVerifiedEmailAddress', $rest->error);
            return false;
        }

        $response['RequestId'] = (string)$rest->body->ResponseMetadata->RequestId;
        return $response;
    }

    /**
    * Retrieves information on the current activity limits for this account.
    * See http://docs.amazonwebservices.com/ses/latest/APIReference/API_GetSendQuota.html
    *
    * @return An array containing information on this account's activity limits.
    */
    public function getSendQuota() {
        $rest = new SimpleEmailServiceRequest($this, 'GET');
        $rest->setParameter('Action', 'GetSendQuota');

        $rest = $rest->getResponse();
        if ($rest->error === false && $rest->code !== 200) {
            $rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
        }
        if ($rest->error !== false) {
            $this->__triggerError('getSendQuota', $rest->error);
            return false;
        }

        $response = array();
        if (!isset($rest->body)) {
            return $response;
        }

        $response['Max24HourSend'] = (string)$rest->body->GetSendQuotaResult->Max24HourSend;
        $response['MaxSendRate'] = (string)$rest->body->GetSendQuotaResult->MaxSendRate;
        $response['SentLast24Hours'] = (string)$rest->body->GetSendQuotaResult->SentLast24Hours;
        $response['RequestId'] = (string)$rest->body->ResponseMetadata->RequestId;

        return $response;
    }

    /**
    * Retrieves statistics for the last two weeks of activity on this account.
    * See http://docs.amazonwebservices.com/ses/latest/APIReference/API_GetSendStatistics.html
    *
    * @return An array of activity statistics.  Each array item covers a 15-minute period.
    */
    public function getSendStatistics() {
        $rest = new SimpleEmailServiceRequest($this, 'GET');
        $rest->setParameter('Action', 'GetSendStatistics');

        $rest = $rest->getResponse();
        if ($rest->error === false && $rest->code !== 200) {
            $rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
        }
        if ($rest->error !== false) {
            $this->__triggerError('getSendStatistics', $rest->error);
            return false;
        }

        $response = array();
        if (!isset($rest->body)) {
            return $response;
        }

        $datapoints = array();
        foreach($rest->body->GetSendStatisticsResult->SendDataPoints->member as $datapoint) {
            $p = array();
            $p['Bounces'] = (string)$datapoint->Bounces;
            $p['Complaints'] = (string)$datapoint->Complaints;
            $p['DeliveryAttempts'] = (string)$datapoint->DeliveryAttempts;
            $p['Rejects'] = (string)$datapoint->Rejects;
            $p['Timestamp'] = (string)$datapoint->Timestamp;

            $datapoints[] = $p;
        }

        $response['SendDataPoints'] = $datapoints;
        $response['RequestId'] = (string)$rest->body->ResponseMetadata->RequestId;

        return $response;
    }


    /**
    * Given a SimpleEmailServiceMessage object, submits the message to the service for sending.
    *
    * @return An array containing the unique identifier for this message and a separate request id.
    *         Returns false if the provided message is missing any required fields.
    */
    public function sendEmail($sesMessage) {
        if (!$sesMessage->validate()) {
            $this->__triggerError('sendEmail', 'Message failed validation.');
            return false;
        }

        $rest = new SimpleEmailServiceRequest($this, 'POST');
        $rest->setParameter('Action', 'SendEmail');

        $i = 1;
        foreach($sesMessage->to as $to) {
            $rest->setParameter('Destination.ToAddresses.member.'.$i, $to);
            $i++;
        }

        if (is_array($sesMessage->cc)) {
            $i = 1;
            foreach($sesMessage->cc as $cc) {
                $rest->setParameter('Destination.CcAddresses.member.'.$i, $cc);
                $i++;
            }
        }

        if (is_array($sesMessage->bcc)) {
            $i = 1;
            foreach($sesMessage->bcc as $bcc) {
                $rest->setParameter('Destination.BccAddresses.member.'.$i, $bcc);
                $i++;
            }
        }

        if (is_array($sesMessage->replyto)) {
            $i = 1;
            foreach($sesMessage->replyto as $replyto) {
                $rest->setParameter('ReplyToAddresses.member.'.$i, $replyto);
                $i++;
            }
        }

        $rest->setParameter('Source', $sesMessage->from);

        if ($sesMessage->returnpath != null) {
            $rest->setParameter('ReturnPath', $sesMessage->returnpath);
        }

        if ($sesMessage->subject != null && strlen($sesMessage->subject) > 0) {
            $rest->setParameter('Message.Subject.Data', $sesMessage->subject);
            if ($sesMessage->subjectCharset != null && strlen($sesMessage->subjectCharset) > 0) {
                $rest->setParameter('Message.Subject.Charset', $sesMessage->subjectCharset);
            }
        }


        if ($sesMessage->messagetext != null && strlen($sesMessage->messagetext) > 0) {
            $rest->setParameter('Message.Body.Text.Data', $sesMessage->messagetext);
            if ($sesMessage->messageTextCharset != null && strlen($sesMessage->messageTextCharset) > 0) {
                $rest->setParameter('Message.Body.Text.Charset', $sesMessage->messageTextCharset);
            }
        }

        if ($sesMessage->messagehtml != null && strlen($sesMessage->messagehtml) > 0) {
            $rest->setParameter('Message.Body.Html.Data', $sesMessage->messagehtml);
            if ($sesMessage->messageHtmlCharset != null && strlen($sesMessage->messageHtmlCharset) > 0) {
                $rest->setParameter('Message.Body.Html.Charset', $sesMessage->messageHtmlCharset);
            }
        }

        $rest = $rest->getResponse();
        if ($rest->error === false && $rest->code !== 200) {
            $rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
        }
        if ($rest->error !== false) {
            $this->__triggerError('sendEmail', $rest->error);
            //return false;
        }

        //$response['MessageId'] = (string)$rest->body->SendEmailResult->MessageId;
        //$response['RequestId'] = (string)$rest->body->ResponseMetadata->RequestId;


        if ($rest->error !== false)
            $response=  array('state'=>400,'msg'=>$rest->error);
        else
            $response=  array('state'=>200,'msg'=>'ok');

        //print_r($response);
        return $response;

    }

    /**
    * Trigger an error message
    *
    * @internal Used by member functions to output errors
    * @param array $error Array containing error information
    * @return string
    */
    public function __triggerError($functionname, $error) {
        if ($error == false) {
            trigger_error(sprintf("SimpleEmailService::%s(): Encountered an error, but no description given", $functionname), E_USER_WARNING);
        } else if (isset($error['curl']) && $error['curl']) {
            trigger_error(sprintf("SimpleEmailService::%s(): %s %s", $functionname, $error['code'], $error['message']), E_USER_WARNING);
        } else if (isset($error['Error'])) {
            $e = $error['Error'];
            $message = sprintf("SimpleEmailService::%s(): %s - %s: %s\nRequest Id: %s\n", $functionname, $e['Type'], $e['Code'], $e['Message'], $error['RequestId']);
            trigger_error($message, E_USER_WARNING);
        } else {
            trigger_error(sprintf("SimpleEmailService::%s(): Encountered an error: %s", $functionname, $error), E_USER_WARNING);
        }
    }
}

final class SimpleEmailServiceRequest {
    private $ses, $verb, $parameters = array();
    public $response;

    /**
    * Constructor
    *
    * @param string $ses The SimpleEmailService object making this request
    * @param string $action action
    * @param string $verb HTTP verb
    * @return mixed
    */
    function __construct($ses, $verb) {
        $this->ses = $ses;
        $this->verb = $verb;
        $this->response = new STDClass;
        $this->response->error = false;
    }

    /**
    * Set request parameter
    *
    * @param string  $key Key
    * @param string  $value Value
    * @param boolean $replace Whether to replace the key if it already exists (default true)
    * @return void
    */
    public function setParameter($key, $value, $replace = true) {
        if (!$replace && isset($this->parameters[$key])) {
            $temp = (array)($this->parameters[$key]);
            $temp[] = $value;
            $this->parameters[$key] = $temp;
        } else {
            $this->parameters[$key] = $value;
        }
    }

    /**
    * Get the response
    *
    * @return object | false
    */
    public function getResponse() {

        $params = array();
        foreach ($this->parameters as $var => $value) {
            if (is_array($value)) {
                foreach($value as $v) {
                    $params[] = $var.'='.$this->__customUrlEncode($v);
                }
            } else {
                $params[] = $var.'='.$this->__customUrlEncode($value);
            }
        }

        sort($params, SORT_STRING);

        // must be in format 'Sun, 06 Nov 1994 08:49:37 GMT'
        $date = gmdate('D, d M Y H:i:s e');

        $query = implode('&', $params);

        $headers = array();
        $headers[] = 'Date: '.$date;
        $headers[] = 'Host: '.$this->ses->getHost();

        $auth = 'AWS3-HTTPS AWSAccessKeyId='.$this->ses->getAccessKey();
        $auth .= ',Algorithm=HmacSHA256,Signature='.$this->__getSignature($date);
        $headers[] = 'X-Amzn-Authorization: '.$auth;

        $url = 'https://'.$this->ses->getHost().'/';

        // Basic setup
        $curl = curl_init();
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'SimpleEmailService/php');

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, ($this->ses->verifyHost() ? 0 : 0));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, ($this->ses->verifyPeer() ? 0 : 0));

        // Request types
        switch ($this->verb) {
        case 'GET':
            $url .= '?'.$query;
            break;
        case 'POST':
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->verb);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            break;
        case 'DELETE':
            $url .= '?'.$query;
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
            break;
        default:
            break;
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curl, CURLOPT_WRITEFUNCTION, array(&$this, '__responseWriteCallback'));
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        // Execute, grab errors
        if (curl_exec($curl)) {
            $this->response->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        } else {
            $this->response->error = array(
                                         'curl' => true,
                                         'code' => curl_errno($curl),
                                         'message' => curl_error($curl),
                                         'resource' => $this->resource
                                     );
        }

        @curl_close($curl);

        // Parse body into XML
        if ($this->response->error === false && isset($this->response->body)) {
            $this->response->body = simplexml_load_string($this->response->body);

            // Grab SES errors
            if (!in_array($this->response->code, array(200, 201, 202, 204))
                    && isset($this->response->body->Error)) {
                $error = $this->response->body->Error;
                $output = array();
                $output['curl'] = false;
                $output['Error'] = array();
                $output['Error']['Type'] = (string)$error->Type;
                $output['Error']['Code'] = (string)$error->Code;
                $output['Error']['Message'] = (string)$error->Message;
                $output['RequestId'] = (string)$this->response->body->RequestId;

                $this->response->error = $output;
                unset($this->response->body);
            }
        }

        return $this->response;
    }

    /**
    * CURL write callback
    *
    * @param resource &$curl CURL resource
    * @param string &$data Data
    * @return integer
    */
    private function __responseWriteCallback(&$curl, &$data) {
        $this->response->body .= $data;
        return strlen($data);
    }

    /**
    * Contributed by afx114
    * URL encode the parameters as per http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/index.html?Query_QueryAuth.html
    * PHP's rawurlencode() follows RFC 1738, not RFC 3986 as required by Amazon. The only difference is the tilde (~), so convert it back after rawurlencode
    * See: http://www.morganney.com/blog/API/AWS-Product-Advertising-API-Requires-a-Signed-Request.php
    *
    * @param string $var String to encode
    * @return string
    */
    private function __customUrlEncode($var) {
        return str_replace('%7E', '~', rawurlencode($var));
    }

    /**
    * Generate the auth string using Hmac-SHA256
    *
    * @internal Used by SimpleDBRequest::getResponse()
    * @param string $string String to sign
    * @return string
    */
    private function __getSignature($string) {
        return base64_encode(hash_hmac('sha256', $string, $this->ses->getSecretKey(), true));
    }
}


final class SimpleEmailServiceMessage {

    // these are public for convenience only
    // these are not to be used outside of the SimpleEmailService class!
    public $to, $cc, $bcc, $replyto;
    public $from, $returnpath;
    public $subject, $messagetext, $messagehtml;
    public $subjectCharset, $messageTextCharset, $messageHtmlCharset;

    function __construct() {
        $this->to = array();
        $this->cc = array();
        $this->bcc = array();
        $this->replyto = array();

        $this->from = null;
        $this->returnpath = null;

        $this->subject = null;
        $this->messagetext = null;
        $this->messagehtml = null;

        $this->subjectCharset = null;
        $this->messageTextCharset = null;
        $this->messageHtmlCharset = null;
    }


    /**
    * addTo, addCC, addBCC, and addReplyTo have the following behavior:
    * If a single address is passed, it is appended to the current list of addresses.
    * If an array of addresses is passed, that array is merged into the current list.
    */
    function addTo($to) {
        if (!is_array($to)) {
            $this->to[] = $to;
        } else {
            $this->to = array_merge($this->to, $to);
        }
    }

    function addCC($cc) {
        if (!is_array($cc)) {
            $this->cc[] = $cc;
        } else {
            $this->cc = array_merge($this->cc, $cc);
        }
    }

    function addBCC($bcc) {
        if (!is_array($bcc)) {
            $this->bcc[] = $bcc;
        } else {
            $this->bcc = array_merge($this->bcc, $bcc);
        }
    }

    function addReplyTo($replyto) {
        if (!is_array($replyto)) {
            $this->replyto[] = $replyto;
        } else {
            $this->replyto = array_merge($this->replyto, $replyto);
        }
    }

    function setFrom($from) {
        $this->from = $from;
    }

    function setReturnPath($returnpath) {
        $this->returnpath = $returnpath;
    }

    function setSubject($subject) {
        $this->subject = $subject;
    }

    function setSubjectCharset($charset) {
        $this->subjectCharset = $charset;
    }

    function setMessageFromString($text, $html = null) {
        $this->messagetext = $text;
        $this->messagehtml = $html;
    }

    function setMessageFromFile($textfile, $htmlfile = null) {
        if (file_exists($textfile) && is_file($textfile) && is_readable($textfile)) {
            $this->messagetext = file_get_contents($textfile);
        } else {
            $this->messagetext = null;
        }
        if (file_exists($htmlfile) && is_file($htmlfile) && is_readable($htmlfile)) {
            $this->messagehtml = file_get_contents($htmlfile);
        } else {
            $this->messagehtml = null;
        }
    }

    function setMessageFromURL($texturl, $htmlurl = null) {
        if ($texturl !== null) {
            $this->messagetext = file_get_contents($texturl);
        } else {
            $this->messagetext = null;
        }
        if ($htmlurl !== null) {
            $this->messagehtml = file_get_contents($htmlurl);
        } else {
            $this->messagehtml = null;
        }
    }

    function setMessageCharset($textCharset, $htmlCharset = null) {
        $this->messageTextCharset = $textCharset;
        $this->messageHtmlCharset = $htmlCharset;
    }

    /**
    * Validates whether the message object has sufficient information to submit a request to SES.
    * This does not guarantee the message will arrive, nor that the request will succeed;
    * instead, it makes sure that no required fields are missing.
    *
    * This is used internally before attempting a SendEmail or SendRawEmail request,
    * but it can be used outside of this file if verification is desired.
    * May be useful if e.g. the data is being populated from a form; developers can generally
    * use this function to verify completeness instead of writing custom logic.
    *
    * @return boolean
    */
    public function validate() {
        if (count($this->to) == 0)
            return false;
        if ($this->from == null || strlen($this->from) == 0)
            return false;
        // messages require at least one of: subject, messagetext, messagehtml.
        if (($this->subject == null || strlen($this->subject) == 0)
                && ($this->messagetext == null || strlen($this->messagetext) == 0)
                && ($this->messagehtml == null || strlen($this->messagehtml) == 0)) {
            return false;
        }

        return true;
    }
}


?>
