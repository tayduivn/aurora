<?php
/*

 About:
 Autor: Raul Perusquia <raul@inikoo.com>

 Copyright (c) 2011, Inikoo

 Version 2.0
*/
include_once('class.DB_Table.php');
include_once('class.Email.php');
include_once('class.Customer.php');

class ReadEmail extends DB_Table {

    var $emails=array();

    function ReadEmail($ServerName, $UserName,$PassWord) {
        $this->ServerName=$ServerName;
        $this->UserName=$UserName;
        $this->PassWord=$PassWord;

    }
    function getdecodevalue($message,$coding) {
        if ($coding == 0) {
            $message = imap_8bit($message);
        }
        elseif ($coding == 1) {
            $message = imap_8bit($message);
        }
        elseif ($coding == 2) {
            $message = imap_binary($message);
        }
        elseif ($coding == 3) {
            $message=imap_base64($message);
        }
        elseif ($coding == 4) {
            $message = imap_qprint($message);
        }
        elseif ($coding == 5) {
            $message = imap_base64($message);
        }
        return $message;
    }
    function transformHTML($str) {
        if ((strpos($str,"<HTML") < 0) || (strpos($str,"<html")    < 0)) {
            $makeHeader = "<html><head><meta http-equiv=\"Content-Type\"    content=\"text/html; charset=iso-8859-1\"></head>\n";
            if ((strpos($str,"<BODY") < 0) || (strpos($str,"<body")    < 0)) {
                $makeBody = "\n<body>\n";
                $str = $makeHeader . $makeBody . $str ."\n</body></html>";
            } else {
                $str = $makeHeader . $str ."\n</html>";
            }
        } else {
            $str = "<meta http-equiv=\"Content-Type\" content=\"text/html;    charset=iso-8859-1\">\n". $str;
        }
        return $str;
    }
    function get_mime_type(&$structure) {
        $primary_mime_type = array("TEXT", "MULTIPART","MESSAGE", "APPLICATION", "AUDIO","IMAGE", "VIDEO", "OTHER");
        if ($structure->subtype) {
            return $primary_mime_type[(int) $structure->type] . '/' .$structure->subtype;
        }
        return "TEXT/PLAIN";
    }
    function get_part($stream, $msg_number, $mime_type, $structure = false,$part_number    = false) {

        //  print "+++ $part_number \n";


        if (!$structure) {
            $structure = imap_fetchstructure($stream, $msg_number);
        }
        if ($structure) {
            if ($mime_type == $this->get_mime_type($structure)) {
                if (!$part_number) {
                    $part_number = "1";
                }
                $text = imap_fetchbody($stream, $msg_number, $part_number);

                return $this->getdecodevalue($text,$structure->encoding);


            } else {
                //  print "---++---  ".$this->get_mime_type($structure)."\n";
            }

            if ($structure->type == 1) { /* multipart */
                while (list($index, $sub_structure) = each($structure->parts)) {


                    if ($part_number) {
                        $prefix = $part_number . '.';
                    } else {
                        $prefix='';
                    }



                    $data = $this->get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix.($index + 1));
                    if ($data) {
                        return $data;
                    }
                }
            }
        }
        return false;
    }
    function process_customer_communication($mbox,$message_number) {

        $this->get_email_message_body($mbox,$message_number);
    }
    function get_email_message_body($mbox,$msgno) {

        $dataTxt = $this->get_part($mbox, $msgno, "TEXT/PLAIN");
        $dataHtml = $this->get_part($mbox, $msgno, "TEXT/HTML");


        if ($dataHtml != "") {
            $msgBody = $this->transformHTML($dataHtml);
        } else {
            $msgBody = nl2br($dataTxt);
            $msgBody = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i","$1http://$2",    $msgBody);
            $msgBody = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<A    TARGET=\"_blank\" HREF=\"$1\">$1</A>", $msgBody);
            $msgBody = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i","<A    HREF=\"mailto:$1\">$1</A>",$msgBody);


        }



//print "$msgBody\n\n";

        $struct = imap_fetchstructure($mbox,$msgno);
        $selectBoxDisplay=array();





        if (isset($struct->parts)) {



            $contentParts = count($struct->parts);

            $message_mime_type=$this->get_mime_type($struct);




            if ($message_mime_type=='MULTIPART/ALTERNATIVE' and $contentParts==2) {
                
            }elseif ($contentParts >= 2) {
                for ($i=2; $i<=$contentParts; $i++) {
                    $att[$i-2] = imap_bodystruct($mbox,$msgno,$i);
                }
                for ($k=0; $k<sizeof($att); $k++) {
                    if ($att[$k]->parameters[0]->value == "us-ascii" || $att[$k]->parameters[0]->value    == "US-ASCII") {
                        if (    isset($att[$k]->parameters[1])    and   $att[$k]->parameters[1]->value != "") {
                            $selectBoxDisplay[$k] = $att[$k]->parameters[1]->value;


                        }
                    }
                    elseif ($att[$k]->parameters[0]->value != "iso-8859-1" &&    $att[$k]->parameters[0]->value != "ISO-8859-1") {
                        $selectBoxDisplay[$k] = $att[$k]->parameters[0]->value;
                    }
                }
            }

        }
       
       foreach($selectBoxDisplay  as $attchment){
       
       }

    }
    
    
    
    function read_mailbox($process_type,$mail_box='') {
        $mbox = imap_open($this->ServerName.$mail_box, $this->UserName,$this->PassWord);


       $list = imap_list($mbox,$this->ServerName, "*");

print_r($list);

  
        if ($hdr = imap_check($mbox)) {

            $msgCount = $hdr->Nmsgs;
        } else {
            return 0;
        }
        
        
        
        
        
        
        $MN=$msgCount;
        $overview=imap_fetch_overview($mbox,"1:$MN",0);
        $size=sizeof($overview);



        for ($i=$size-1; $i>=0; $i--) {
        
            print_r($overview[$i]);
        
            switch ($process_type) {
            case 'customer_communication':
                $this->process_customer_communication($mbox,$i);
                break;
            default:
                break(2);

            }

        }

    }
}
?>