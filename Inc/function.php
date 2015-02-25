<?php
function http_log($log){
	file_put_contents(date('Y-m-d').'.txt', $log."\r\n", FILE_APPEND);
}
function http_log2($log){
	file_put_contents(date('Y-m-d').'.new', $log."\r\n", FILE_APPEND);
}
function checkSignature() {
	$signature = $_GET ['signature'];
	$timestamp = $_GET ['timestamp'];
	$nonce = $_GET ['nonce'];
	$tmpArr = array (TOKEN,$timestamp,$nonce);
	sort ($tmpArr,SORT_STRING );
	$tmpStr = implode ( $tmpArr );
	$tmpStr = sha1 ( $tmpStr );
	if ($tmpStr == $signature) {
		return true;
	}
	return false;
}
function isMobile(){
$agent = $_SERVER['HTTP_USER_AGENT'];
if(strpos($agent,"NetFront") || strpos($agent,"iPhone") || strpos($agent,"MIDP-2.0") || strpos($agent,"Opera Mini") || strpos($agent,"UCWEB") || strpos($agent,"Android") || strpos($agent,"Windows CE") || strpos($agent,"SymbianOS")) {
return true;
} else{
return false;
}
}



?>