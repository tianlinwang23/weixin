<?php

include_once "wxBizMsgCrypt.php";
$Conf_TextMsg = "
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
<FuncFlag>0</FuncFlag>
</xml>"; //文本信息


// 第三方发送消息给公众平台
$encodingAesKey = "abcdefghijklmnopqrstuvwxyz0123456789ABcDEFG";
$token = "pamtest";
$timeStamp = "1409304348";
$nonce = "xxxxxx";
$appId = "wxb11529c136998cb6";
$text = "<xml><ToUserName><![CDATA[jewbmiOUlr6X-1crbLOvLw]]></ToUserName><FromUserName><![CDATA[gh_7f083739789a]]></FromUserName><CreateTime>1407743423</CreateTime><MsgType><![CDATA[video]]></MsgType><Video><MediaId><![CDATA[eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0]]></MediaId><Title><![CDATA[testCallBackReplyVideo]]></Title><Description><![CDATA[testCallBackReplyVideo]]></Description></Video></xml>";

$retMsg = "貌似没找到相关的资讯，可以试下回复当天日期，可查收到当天的全部资讯。谢谢您的支持！";
$resultStr = sprintf($Conf_TextMsg, 'gh_7f083739789a', 'jewbmiOUlr6X-1crbLOvLw', time(), $retMsg);
$pc = new WXBizMsgCrypt($token, $encodingAesKey, $appId);
$encryptMsg = '';
$errCode = $pc->encryptMsg($text,$timeStamp, $nonce, $encryptMsg);
if ($errCode == 0) {
	print("加密后: " . $encryptMsg . "\n");
} else {
	print($errCode . "\n");
}



$format = "<xml><ToUserName><![CDATA[tlw]]></ToUserName>  <Encrypt><![CDATA[PpYENDNGwJ22PnJOPpYob6/oDKB3bQIkxFLc82yxvjdbcjNgkSs9zRxPKfHdcMA460K/AyW2LPV7fK8m9y4J8z+QB3d0/egp72bfBT5M9pzYfb1yCkepDw1i6fYa2yAQSF0BR1DizAKonGjuNXn3XeOezwPxTH9mXA/SHjEBjXqKtpDV2n/U/8EdxoZNn3czO6yTO4cozkKvOhV8S9S2Dz6RydzNWLYQ5kXoZP3H9KpNTMa9JMSFD7veRVIzPSYplIqx95JepwGMr333PzO0QDsMM57vf5d765SmfiacVsDjGJdvpn2fMYv4+s+cmAniOSDIjDvGd5xyTUuIOpy+Orpyzpj6AtRlunvKVOpQ+ZStghyyXLbYwgDB0AYBaudWPvWD6T3r9noNqGwfMmsRLcrA+qFSBOmR1hEFVIsPlLo=]]></Encrypt></xml>";


// 第三方收到公众号平台发送的消息
$msg = '';
$errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $fromat, $msg);
if ($errCode == 0) {
	print("解密后: " . $msg . "\n");
} else {
	print($errCode . "\n");
}
*/
?>