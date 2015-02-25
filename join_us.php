<?php
	
	require('../Inc/smtp.php');
	
	$huiyuan=$_POST['radiobutton'];
	$name=$_POST['name'];
	$contact=$_POST['contact'];
	$one=$_POST['one'];
	$company=$_POST['company'];
	$sex=$_POST['sex'];
	$email=$_POST['mail'];
	$phone=$_POST['phone'];
	
	if($huiyuan=="true"){
	
	$wechat="普通会员";
	
	}
	else if($huiyuan=="false"){
	$wechat="高级会员";
	}
	if($contact!=""||$name!=""){
	
	$link = mysql_connect('localhost', 'newcdnyxj','193newcdnyx') or die( mysql_error());
	mysql_select_db('newcdnyxj',$link) or die('db error');
	mysql_query("SET NAMES UTF8");
	//对应城市和医院
	$result1=mysql_query("select id,name from pre_common_district where id=$one");
	$row=mysql_fetch_array($result1);
	$province=$row['name'];
	if($name==""){
	$names=$contact;
	}
	else if($contact==""){
	$names=$name;
	
	}
	
	$time=time();
	mysql_query("INSERT INTO wx_user_information (name,sex, province,company,phone,email,registertime,wechat) 
VALUES ('$names','$sex','$province','$company','$phone','$email','$time','$wechat')");

//发邮件
$smtpserver = "smtp.qq.com";//SMTP服务器
$smtpserverport = 25;//SMTP服务器端口
$smtpusermail = "js@yxj.org.cn";//SMTP服务器的用户邮箱
$smtpemailto =$email;//发送给谁

$smtpuser = "js@yxj.org.cn";//SMTP服务器的用户帐号
$smtppass = "ptmkt58545118";//SMTP服务器的用户密码
$mailsubject = "欢迎加入《医学界》会员俱乐部！";//邮件主题
$mailsubject = "=?UTF-8?B?".base64_encode($mailsubject)."?=";
$mailbody = "
<p>亲爱的界友，您好，我们已经收到您加入会员俱乐部的申请。假期过后（25日后）我们会第一时间回复您，安排寄送会刊事宜。如果您还未完成付款，<a href='http://mp.weixin.qq.com/bizmall/mallshelf?id=&t=mall/list&biz=MzA3NjIzNTkwNQ==&shelf_id=4&showwxpaytitle=1#wechat_redirect'>点击（链接）</a>，打赏一下界哥界妹吧～
祝您新年快乐，2015年“羊”眉吐气，“羊”光灿烂一整年！
</p>";
    
  
	

;//邮件内容
$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
##########################################
$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
$smtp->debug = false;//是否显示发送的调试信息
$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype,$smtpemailcc,$smtpemailbcc);



echo "<script language=\"javascript\">alert('会员报名成功');window.location.href='http://mp.weixin.qq.com/bizmall/mallshelf?id=&t=mall/list&biz=MzA3NjIzNTkwNQ==&shelf_id=4&showwxpaytitle=1#wechat_redirect';</script>"; 

mysql_close($link);
}

?>