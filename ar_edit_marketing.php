<?php
require_once 'common.php';
require_once 'ar_edit_common.php';
require_once 'class.EmailCampaign.php';



if (!isset($_REQUEST['tipo'])) {
    $response=array('state'=>405,'msg'=>_('Non acceptable request').' (t)');
    echo json_encode($response);
    exit;
}

$tipo=$_REQUEST['tipo'];
switch ($tipo) {
case('move_email_paragraph'):
 $data=prepare_values($_REQUEST,array(
                             'values'=>array('type'=>'json array')
                            ));
    move_email_paragraph($data);
break;
case('edit_email_paragraph'):
 $data=prepare_values($_REQUEST,array(
                             'values'=>array('type'=>'json array')
                            ));
    edit_email_paragraph($data);
break;
case('edit_email_campaign'):
    $data=prepare_values($_REQUEST,array(
                             'email_campaign_key'=>array('type'=>'key'),
                             'okey'=>array('type'=>'string'),
                             'key'=>array('type'=>'string'),
                             'newvalue'=>array('type'=>'string')

                         ));
    edit_email_campaign($data);
    break;
case('select_html_email_campaign'):
    $data=prepare_values($_REQUEST,array(
                             'email_campaign_key'=>array('type'=>'key')

                         ));
    $data['newvalue']='HTML Template';
    $data['key']='Email Campaign Content Type';
    $data['okey']='email_campaign_content_type';
    edit_email_campaign($data);
    break;    
case('select_plain_email_campaign'):
    $data=prepare_values($_REQUEST,array(
                             'email_campaign_key'=>array('type'=>'key')

                         ));
    $data['newvalue']='Plain';
    $data['key']='Email Campaign Content Type';
    $data['okey']='email_campaign_content_type';
    edit_email_campaign($data);
    break;
case('delete_email_campaign'):
    $data=prepare_values($_REQUEST,array(
                             'email_campaign_key'=>array('type'=>'key'),
                         ));
    delete_email_campaign($data);
    break;

case('add_emails_from_list'):
    $data=prepare_values($_REQUEST,array(
                             'email_campaign_key'=>array('type'=>'key'),
                             'list_key'=>array('type'=>'key')
                         ));
    add_emails_to_email_campaign_from_list($data);
    break;
    break;
case('create_add_email_address_manually'):
    $data=prepare_values($_REQUEST,array(
                             'parent_key'=>array('type'=>'key'),
                             'values'=>array('type'=>'json array')
                         ));
    add_email_address_manually($data);
    break;
case('create_email_campaign'):
    $data=prepare_values($_REQUEST,array(
                             'parent_key'=>array('type'=>'key'),
                             'values'=>array('type'=>'json array'),

                         ));
    create_email_campaign($data);
    break;
default:
    $response=array('state'=>404,'msg'=>_('Operation not found'));
    echo json_encode($response);
}

function add_emails_to_email_campaign_from_list($data) {
    global $user;
    

    $email_campaign=new EmailCampaign($data['email_campaign_key']);

    $sql=sprintf("select * from `List Dimension` where `List Key`=%d",$data['list_key']);

    $res=mysql_query($sql);
    if (!$customer_list_data=mysql_fetch_assoc($res)) {
        $response= array('state'=>400,'msg'=>'List not found');
         echo json_encode($response);
        return;

    }
        

    
    
    
   
    if (!in_array($customer_list_data['List Store Key'],$user->stores)) {
        $response= array('state'=>400,'msg'=>_('Operation forbidden'));
        return;

    }



    $email_campaign->add_emails_from_list($data['list_key']);

    if ($email_campaign->updated) {

        $response= array('state'=>200,
                        'action'=>'created',
                        'number_recipients'=>$email_campaign->data['Number of Emails'],
                        'recipients_preview'=>$email_campaign->data['Email Campaign Recipients Preview'],
                        'msg'=>$email_campaign->msg
                        );
    } else {
        $response= array('state'=>400,'msg'=>$email_campaign->msg);

    }
    echo json_encode($response);
}



function delete_email_campaign($data) {
    

    $email_campaign=new EmailCampaign($data['email_campaign_key']);
    $email_campaign->delete();
    if ($email_campaign->updated) {

        $response= array('state'=>200,'action'=>'deleted'
                        );
    } else {
        $response= array('state'=>400,'msg'=>$email_campaign->msg);

    }
    echo json_encode($response);
}

function add_email_address_manually($data) {
    

    $email_campaign=new EmailCampaign($data['parent_key']);
    $email_campaign->add_email_address_manually($data['values']);

    if ($email_campaign->updated) {

        $response= array('state'=>200,
                            'action'=>'created',
                            'number_recipients'=>$email_campaign->data['Number of Emails'],
                            'recipients_preview'=>$email_campaign->data['Email Campaign Recipients Preview']
                        );
    } else {
        $response= array('state'=>400,'msg'=>$email_campaign->msg);

    }
    echo json_encode($response);
}

function create_email_campaign($data) {
    

    $email_campaign_data=array(
                             'Email Campaign Store Key'=>$data['parent_key'],
                             'Email Campaign Name'=>$data['values']['Email Campaign Name'],
                             'Email Campaign Objective'=>$data['values']['Email Campaign Objective']

                         );
//print_r($email_campaign_data);
    $email_campaign=new EmailCampaign('find',$email_campaign_data,'create');
    if ($email_campaign->new) {

        $response= array(
                       'state'=>200,
                       'action'=>'created',
                       'email_campaign_key'=>$email_campaign->id
                   );
    }
    elseif($email_campaign->found) {
        $response= array(
                       'state'=>200,
                       'action'=>'found',
                       'email_campaign_key'=>$email_campaign->id,
                       'msg'=>_('Another email campaign has that name')

                   );
    }
    else {
        $response= array('state'=>400,'msg'=>$email_campaign->msg);

    }
    echo json_encode($response);

}

function edit_email_campaign($data) {
    

    $email_campaign=new EmailCampaign($data['email_campaign_key']);
    if (!$email_campaign->id) {
        $response= array('state'=>400,'msg'=>'Invalid Email Campaign Key','key'=>$data['okey']);
        echo json_encode($response);
        exit;
    }



    $email_campaign->update(array($data['key']=>$data['newvalue']));

      if ($email_campaign->updated) {

        $response= array(
                       'state'=>200,
                       'action'=>'changed',
                       'email_campaign_key'=>$email_campaign->id,
                       'key'=>$data['okey'],
                       'newvalue'=>$email_campaign->new_value
                   );
    }elseif($email_campaign->error) {
        $response= array(
                       'state'=>400,
            
                       'msg'=>$email_campaign->msg,
                       'key'=>$data['okey']

                   );
    }
    else {
        $response= array('state'=>400,'msg'=>$email_campaign->msg);
 $response= array(
                       'state'=>200,
                       'action'=>'nochange',
                       'email_campaign_key'=>$email_campaign->id,
                       'msg'=>$email_campaign->msg,
                       'key'=>$data['okey']

                   );
    }
 echo json_encode($response);
    

}

function edit_email_paragraph($data){



$email_campaign=new EmailCampaign($data['values']['email_campaign_key']);
$paragrahp_data=array(
'title'=>$data['values']['title'],
'subtitle'=>$data['values']['subtitle'],
'content'=>$data['values']['content'],

);


$email_campaign->update_paragraph($data['values']['email_content_key'],$data['values']['paragraph_key'],$paragrahp_data);
if($email_campaign->updated){
        $response= array('state'=>200);

}else{
        $response= array('state'=>400,'msg'=>$email_campaign->msg);

}
echo json_encode($response);
}


function move_email_paragraph($data){
//print_r($data['values']);

$email_campaign=new EmailCampaign($data['values']['email_campaign_key']);

if(preg_match('/\d+$/',$data['values']['paragraph_key'],$match)){
$paragrahp_key=$match[0];
}else{
$response= array('state'=>400,'msg'=>'invalid paragraph');echo json_encode($response);return;
}
if(preg_match('/\d+$/',$data['values']['target'],$match)){
$target_paragraph_key=$match[0];
}else{
$response= array('state'=>400,'msg'=>'invalid target');echo json_encode($response);return;
}

$email_campaign->move_paragraph_before_target($data['values']['email_content_key'],$paragrahp_key,$target_paragraph_key);

 $response= array('state'=>200);
echo json_encode($response);
}


?>
