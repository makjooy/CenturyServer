<?php
include_once '../../../define.php';
include_once '../model/PushBase.php';
$push=new PushBase();
$ret=$push->getNeedSend();

$rediska = new Rediska();
$list = new Rediska_Key_List('Redis_push_'.$ret['_id']);
set_time_limit(3600);
$temcount=0;
while($list->count()>0){
	$tem=$list->pop();
	require_once FRAMEWORK . 'jpush/jpush.php';
	$obj = new jpush ( masterSecret, appkeys );
	$msg_content = json_encode ( array (
			'n_builder_id' => 0,
			'n_title' => "爱上聚会",
			'n_content' => $ret['content']
	) );
	$res = $obj->send ( rand ( 100000, 999999 ), 3,$tem,1, $msg_content, strtolower ( 'android') );
	$temcount++;
}

$push->hasSend($ret['_id'],$temcount);
print_R($ret);