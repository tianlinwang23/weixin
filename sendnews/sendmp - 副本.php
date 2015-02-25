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
	//����access_token.json�ļ�ʱ
	//��ȡaccess_token
	
	$jsondata = file_get_contents($filename);
	$arr = json_decode($jsondata);
	$expirestime=$arr->expires_in;
	
	if($expirestime>time()){
		//access_tokenƾ֤��Чʱ�仹����Ч��
		
		$access_token=$arr->access_token;
		
		}
		
		else{
	//΢�ŷ�������ȡaccess_token
	
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
//�ϴ�����ͼƬ
function uploadimage($picurl){
$filepath="/www/web/newcdn_yxj_org_cn/public_html/data/attachment/".$picurl;//�ϴ�ͼƬ�ľ���·��
$filedata=array("media" => "@".$filepath);
$access_token=getaccess_token();
$url="http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=image";
$result=https_request($url,$filedata);
$jsoninfo = json_decode($result, true);
return $jsoninfo['media_id'];
}
//�ϴ�ͼ����Ϣ
function sendnews($new){
$access_token=getaccess_token();
$url="https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=$access_token";
$result1=https_request($url,$new);
$jsoninfo = json_decode($result1, true);
return $jsoninfo['media_id'];
}
//Ⱥ��ͼ����Ϣ
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
$result=https_request($url,$jsondata);
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
//�ж��Ƿ�Ϊ��ӦƵ������Ѷ
for($i=0;$i<$total;$i++){

	if($cat[$i]=='12'){
	$num=$num+1;
	$picurl=$row['pic'];
	$title=$row['title'];
	$wxurl=$row['wxurl'];
	$content=$row['content'];
	$content=str_replace("data/attachment","http://www.yxj.org.cn/data/attachment",$content);//ͼƬ��ַ�޸�
	$author=$row['author'];
	if($author!=''){
	$author="���ߣ�".$author;
	
	}
	
	$from=$row['from'];
	if($from!=''){
	$from="��Դ��".$from;
	
	}
	$wxfooter=$row['wxfooter'];
	$wxfooter='<p>'.$wxfooter.'</p>';
	$title=urlencode($title);//����ת��
	$topimage='<p style="font-size: 15.55px; white-space: normal;"><span style="color: rgb(127, 127, 127);"><img src="http://mmbiz.qpic.cn/mmbiz/EibuWOJekf3dl8vr1YxGPswyk89ic6HojMObJUp78XTvPlLq6dkNT72cNYlFDpFK7hicRSXE5y8BR7ia2amcdojp2Q/0" style="width: auto ! important; visibility: visible ! important; height: auto ! important;" data-src="http://mmbiz.qpic.cn/mmbiz/EibuWOJekf3dl8vr1YxGPswyk89ic6HojMObJUp78XTvPlLq6dkNT72cNYlFDpFK7hicRSXE5y8BR7ia2amcdojp2Q/0" data-w="" data-ratio="0.17357001972386588"></span></p>';
	$bottomimage='<p style="font-size: 15.55px; white-space: normal;"><img src="http://mmbiz.qpic.cn/mmbiz/EibuWOJekf3dl8vr1YxGPswyk89ic6HojMrlce2FH8vMG1kq5yRUjgh1TPRAV5LZRgYXfgJkamJyNUydicx8qxTjA/640" style="width: auto ! important; visibility: visible ! important; height: auto ! important;" data-src="http://mmbiz.qpic.cn/mmbiz/EibuWOJekf3dl8vr1YxGPswyk89ic6HojMrlce2FH8vMG1kq5yRUjgh1TPRAV5LZRgYXfgJkamJyNUydicx8qxTjA/0" data-w="" data-ratio="0.1834319526627219" data-s="300,640"><br></p>';
	$af='<p style="font-size: 15.55px; white-space: normal;"><span style="color: rgb(127, 127, 127); font-size: 16px;">'.$author.'&nbsp;&nbsp;'.$from.'</span><br></p>
	<p></p>';
	$content=$topimage.$af.$content.$wxfooter.$bottomimage;
	$content=addslashes($content);//΢�Ž�����Ϣ��Ҫ����ת�壬��Ȼ��������
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
$newid=sendnews($new);
sendmp($newid);
?>