<?php

require ('../config/config.php');
define("appid","wx3d65cc3f9d666dbd");
define("appsecret","ab35f0785c5b425e4e6dd293281f9281");
set_time_limit(0);

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

function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
//上传封面图片
function uploadimage($picurl){
$filepath="/www/web/newcdn_yxj_org_cn/public_html/data/attachment/".$picurl;//上传图片的绝对路径
$filedata=array("media" => "@".$filepath);
$access_token=getaccess_token();
$url="http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=image";
$result=https_request($url,$filedata);
$jsoninfo = json_decode($result, true);
return $jsoninfo['media_id'];
}
//上传图文消息
function sendnews($new){
$access_token=getaccess_token();
$url="https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=$access_token";
$result1=https_request($url,$new);
$jsoninfo = json_decode($result1, true);
return $jsoninfo['media_id'];
}
//群发图文消息
function sendmp($media_id){
$filter=array(
"group_id"=>'0',

);
$media_news=array("media_id"=>$media_id,
);
$sendmp= array(  
             "filter"=>$filter,
             "mpnews"=>$media_news,
			 "msgtype"=>"mpnews",
			
        ); 

echo $jsondata=json_encode($sendmp); 
$access_token=getaccess_token();
$url="https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=$access_token";
echo $result=https_request($url,$jsondata);
$jsoninfo = json_decode($result1, true);
echo $jsoninfo['errcode'];
}

$con=mysql_connect($dbserver,$dbusername,$dbpassword);
$db=mysql_select_db($dbname,$con);// $database
mysql_query("set names utf8");
$result=mysql_query("SELECT pre_portal_article_title.aid,pre_portal_article_title.wxfooter,pre_portal_article_title.dateline,pre_portal_article_title.wxorder,pre_portal_article_title.wxurl,pre_portal_article_title.title,pre_portal_article_title.from,pre_portal_article_title.summary,pre_portal_article_title.author,pre_portal_article_title.pic,pre_portal_article_title.isWx,pre_portal_article_content.content FROM pre_portal_article_title LEFT JOIN pre_portal_article_content ON pre_portal_article_title.aid=pre_portal_article_content.aid where pre_portal_article_title.isWx !='' and  date(FROM_UNIXTIME(pre_portal_article_title.dateline))=curdate()order by wxorder  ");
$num=0;
while($row=mysql_fetch_array($result)){
$isWx=$row['isWx'];
$cat=explode(",",$isWx);
$total=count($cat);
//判断是否为对应频道的资讯
for($i=0;$i<$total;$i++){

	if($cat[$i]=='12'){
	$num=$num+1;
	$picurl=$row['pic'];
	$title=$row['title'];
	$wxurl=$row['wxurl'];
	$content=$row['content'];
	$content=str_replace("data/attachment","http://www.yxj.org.cn/data/attachment",$content);//图片地址修改
	$author=$row['author'];
	if($author!=''){
	$author="作者：".$author;
	
	}
	
	$from=$row['from'];
	if($from!=''){
	$from="来源：".$from;
	
	}
	$wxfooter=$row['wxfooter'];
	$wxfooter='<p></p><p style="color: rgb(127, 127, 127); font-size: 16px;">'.$wxfooter.'</p>';
	$title=urlencode($title);//进行转码
	$topimage='<p><img src="http://mmbiz.qpic.cn/mmbiz/QbYRrFicefiboGicTJiblU8gq0zEzvbd7Vzkc8UYFbibwFiacZQ2VyeD2l2ULHxHDbbibhg4EoLBRbI61WVndic5iby5odw/0" style="width: auto ! important; visibility: visible ! important; height: auto ! important;" data-src="http://mmbiz.qpic.cn/mmbiz/QbYRrFicefiboGicTJiblU8gq0zEzvbd7Vzkc8UYFbibwFiacZQ2VyeD2l2ULHxHDbbibhg4EoLBRbI61WVndic5iby5odw/0" data-ratio="0.17193675889328064" data-w=""></p>';
	$bottomimage='<p><img src="http://mmbiz.qpic.cn/mmbiz/QbYRrFicefiboGicTJiblU8gq0zEzvbd7Vzkc1Xjs0xJFWTPriaeeYpfgZl1D7SOWYfnOxGBicsibegzJQyKV9ib63QYtA/640" style="width: auto ! important; visibility: visible ! important; height: auto ! important;" data-s="300,640" data-src="http://mmbiz.qpic.cn/mmbiz/QbYRrFicefiboGicTJiblU8gq0zEzvbd7Vzkc1Xjs0xJFWTPriaeeYpfgZl1D7SOWYfnOxGBicsibegzJQyKV9ib63QYtA/0" data-ratio="0.18181818181818182" data-w=""></p>';
	$af='<p style="font-size: 15.55px; white-space: normal;"><span style="color: rgb(127, 127, 127); font-size: 16px;">'.$author.'&nbsp;&nbsp;'.$from.'</span><br></p>
	<p></p>';
	$content=$topimage.$af.$content.$wxfooter.$bottomimage;
	$content=addslashes($content);//微信接受消息需要进行转义，不然会有问题
	$content=urlencode($content);
	$thumb_media_id=uploadimage($picurl);
	$a[]=array(  
				"thumb_media_id"=>$thumb_media_id,
				 "author"=>"",
				 "title"=>$title,
				 "content_source_url"=>$wxurl,
				 "content"=>$content,
				 "digest"=>"",
				 "show_cover_pic"=>"0"
			); 
	}
}
}

$b=array();
echo $num;
for($i=0;$i<$num;$i++){
array_push($b,$a[$i]);
}
$arr = array(  
   
    'articles' =>$b,
		
        
    
); 

$new=json_encode($arr); 

$new=urldecode($new);
echo $new;
$newid=sendnews($new);
sendmp($newid);
?>