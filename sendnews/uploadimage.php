<?php
define("appid","wx9f6d8bdd5b18f859");
define("appsecret","e31d3e70596fdd9623d3257aa4350a7d");
function getaccess_token(){
$access_tokenpath="Cache";
$access_tokenfilename = "access_token.json";
$filename=$access_tokenpath.'/'.$access_tokenfilename;
if (!file_exists($filename)) {
	$url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.appid.'&secret='.appsecret.'';
	$jsondata = file_get_contents($url);
	$arr = json_decode($jsondata);
	$arr->expires_in=($arr->expires_in)+time();
	$jsondata= json_encode($arr);
	file_put_contents($filename,$jsondata);
	$access_token=$arr->access_token;
}
else{
	//存在access_token.json文件时
	//读取access_token
	
	$jsondata = file_get_contents($filename);
	$arr = json_decode($jsondata);
	$expirestime=$arr->expires_in;
	
	if($expirestime>time()){
		//access_token凭证有效时间还在有效期
		
		$access_token=$arr->access_token;
		
		}
		
		else{
	//微信服务器获取access_token
	
	$url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.appid.'&secret='.appsecret.'';
	$jsondata = file_get_contents($url);
	$arr = json_decode($jsondata);
	$access_token=$arr->access_token;
	$arr->expires_in=($arr->expires_in)+time();
	$jsondata= json_encode($arr);
	file_put_contents($filename,$jsondata);
	
		}

}
return $access_token;
}
function postData($postUrl,$data){	
	$timeout = 120; // set to zero for no timeout 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $postUrl);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	
	$post_data = $data;
	curl_setopt($ch, CURLOPT_POST, true);// post数据
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);// post的变量

	$postData=curl_exec($ch);
	curl_close($ch);
	if(!empty($postData)){
		$resultData =  $postData;
	}else{
		$resultData = "貌似服务器故障";
	}
	return $resultData;
}
function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, TRUE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}


function uploadimage(){
$filepath="/www/web/newcdn_yxj_org_cn/public_html/data/attachment/portal/201412/18/115424iz36dzswnu6u63s3.jpg";//上传图片的绝对路径
$filedata=array("media" => "@".$filepath);
echo $filedata['media'];
$access_token=getaccess_token();
$url="http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=image";
echo $result=postData($url,$filedata);
$jsoninfo = json_decode($result, true);
return $jsoninfo['media_id'];
}
echo $thumb_media_id=uploadimage();
?>