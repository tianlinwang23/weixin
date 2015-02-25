<?php
require 'Inc/config.php';
require 'Inc/function.php';
require_once "encry/wxBizMsgCrypt.php";//解密类
define("AppID", "wx9f6d8bdd5b18f859");
define("EncodingAESKey", "LpBGioj9PxlamotDSQ2SJtfQuc2EzSOFEVBepW877Ld");

if(false == checkSignature()) {
	exit(0);
}
//获取echostr
$echostr = $_GET ['echostr'];
if($echostr) {
	echo $echostr;
	exit(0);
}
//TODO分析用户输入并输出
$timestamp  = $_GET['timestamp'];
$nonce = $_GET["nonce"];
$msg_signature  = $_GET['msg_signature'];
$encrypt_type = $_GET['encrypt_type'];

$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
//解密
if ($encrypt_type == 'aes'){
    $pc = new WXBizMsgCrypt(TOKEN, EncodingAESKey, AppID);                
    $decryptMsg = "";  //解密后的明文
    $errCode = $pc->DecryptMsg($msg_signature, $timestamp, $nonce, $postStr, $decryptMsg);
    $postStr = $decryptMsg;
	
}
$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
http_log($postObj);
if(!$postObj) {
	http_log("接收到数据为空!\n");
	echo "wrong input!";
	exit(0);
}
$fromUserName = $postObj->FromUserName;//发送方帐号（一个OpenID）
$toUserName = $postObj->ToUserName;//微信公众平台 原始ID
$createTime = $postObj->CreateTime; //消息创建时间 （整型） 
$msgType = $postObj->MsgType;//消息类型 文本:text 图片:image 语音:voice  视频:video 地理位置:location 链接:link 事件:event 
$msgId = $postObj->MsgId;//消息id，64位整型
$content = $postObj->Content; $content = htmlentities(trim($content),ENT_QUOTES,'UTF-8');//文本消息内容 + 解决腾讯表情带单引号问题
$picUrl = $postObj->PicUrl;//图片链接
$format = $postObj->Format;//语音格式，如amr，speex等 
$thumbMediaId = $postObj->ThumbMediaId;//视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
$mediaId = $postObj->MediaId;//图片消息媒体id，可以调用多媒体文件下载接口拉取数据。 
$location_X = $postObj->Location_X;//地理位置维度
$location_Y = $postObj->Location_Y;//地理位置经度
$scale = $postObj->Scale;//地图缩放大小
$label = $postObj->Label;//地理位置信息
$title = $postObj->Title;//消息标题
$description = $postObj->Description;//消息描述
$url = $postObj->Url;//消息链接
$recongnition = $postObj->Recongnition;//语音识别结果，UTF8编码
$event = $postObj->Event;//事件类型，subscribe(订阅)、unsubscribe(取消订阅) 
$eventKey = $postObj->EventKey;//事件KEY值，qrscene_为前缀，后面为二维码的参数值
$ticket = $postObj->Ticket;// 二维码的ticket，可用来换取二维码图片 
$latitude = $postObj->Latitude;//地理位置纬度
$longitude = $postObj->Longitude;//地理位置经度
$precision = $postObj->Precision;//地理位置精度
$content = trim($content);//去掉前后空格
$NowDate=substr(date("Ymd", $time),2,6);
$con=mysql_connect($dbserver,$dbusername,$dbpassword) or http_log("Couldn't connect to SQL Server");// on $server
$db=mysql_select_db($dbname,$con) or http_log("Couldn't open database");// $database
mysql_query("set names utf8");

	
  //$p1 = new guke("医学界骨科频道", "6",$fromUserName ,$toUserName );
 $p1=classify($toUserName,$fromUserName);
 switch($msgType){
	case "text"://1.用户发送文本消息
	if(preg_match("/^\d{6}$/",$content)){//1.1.a符合6位数字情况下 则为日期查询
	
		$resultStr=$p1->datesearch($content);
		
	}
	elseif(preg_match("/^\d{8}$/",$content)){
	$resultStr=$p1->datesearch($content);
	
	}
	elseif((preg_match("/^[\x7f-\xff]+$/", $content)) || (!strpos('X'.$content, '/:'))){//1.1.b无符合规则情况下 则进行关键词匹配 正则检查是否含有中文
	
		$resultStr=$p1->keywordsearch($content);
		$p1->saveword($content);
	}
	else{
	$p1->saveword($content);
	$retMsg = "貌似没找到相关的资讯，可以试下回复当天日期（如“".$NowDate."”），可查收到当天的全部资讯。谢谢您的支持！";
	$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
	}
	break;
	case "event"://3.用户操作事件
		switch($event){
		case "subscribe"://3.1关注事件
            
			$resultStr=$p1->subscribe();
		break;
		case "unsubscribe"://3.2取消关注事件
			$p1->unsubscribe();
		break;
		case "CLICK":
            
			$resultStr=$p1->eventKey($eventKey);	
		break;
      
        }
	break;
	
    case "image":
   // $resultStr=$p1->acceptimage($picUrl);
    break;
	default:
	$retMsg = '你的消息未能识别...欢迎关注医学界,微信系统正在完善中.../:--b';
	$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
}
//http_log($resultStr);
//关闭连接
mysql_close($con);
if($toUserName=="gh_9352cb95ad49"){
$encryptMsg = '';
$errCode = $pc->encryptMsg($resultStr,$timeStamp, $nonce, $encryptMsg);
if ($errCode == 0) {
	$resultStr=$encryptMsg;
} else {
	http_log($errCode . "\n");
}

echo $resultStr;
}
else{
echo $resultStr;
}
?>