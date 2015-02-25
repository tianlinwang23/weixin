<?php
require("Inc/config.php");
require("Inc/smtp.php");
//邮件内容
$con=mysql_connect($dbserver,$dbusername,$dbpassword) or http_log("Couldn't connect to SQL Server");// on $server
$db=mysql_select_db($dbname,$con) or http_log("Couldn't open database");// $database
mysql_query("set names gbk");

//推送所有消息1
 $result = mysql_query("select * from wx_user_messages where UNIX_TIMESTAMP(NOW())-date<=86400 order by catid, date");

##########################################
$smtpserver = "smtp.qq.com";//SMTP服务器
$smtpserverport = 25;//SMTP服务器端口
$smtpusermail = "js@yxj.org.cn";//SMTP服务器的用户邮箱
$smtpemailto = "ptmkt007@126.com";//发送给谁
$smtpemailcc="guojingtao001@126.com";//抄送
$smtpemailbcc="chenyifu@yxj.org.cn";
$smtpuser = "js@yxj.org.cn";//SMTP服务器的用户帐号
$smtppass = "ptmkt58545118";//SMTP服务器的用户密码
$mailsubject = "微信后台每日消息";//邮件主题
$mailbody = "<h1>微信消息每日发送</h1><table border='1'><tr>
    <th>频道</th>
    <th>内容</th>
	 <th>时间</th>
  </tr>";
	while($row = mysql_fetch_array($result))
	{
		$time=date('Y-m-d H:i:s', $row['date']); 
		$mailbody=$mailbody."<tr><td>{$row['catid']}</td><td >{$row['message']}</td><td >{$time}</td>
				</tr>";
	}
	$mailbody=$mailbody."</table>";
	

;//邮件内容
$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
##########################################
$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
$smtp->debug = false;//是否显示发送的调试信息
$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype,$smtpemailcc,$smtpemailbcc);

//推送所有消息2

$result1 = mysql_query("select * from wx_user_messages where UNIX_TIMESTAMP(NOW())-date<=86400 order by catid, date");
$smtpserver1 = "smtp.qq.com";//SMTP服务器
$smtpserverport1 = 25;//SMTP服务器端口
$smtpusermail1 = "js@yxj.org.cn";//SMTP服务器的用户邮箱
$smtpemailto1 = "gf@yxj.org.cn";//发送给谁
$smtpemailcc1="hufang1023@126.com";//抄送
$smtpemailbcc1="tskyangyu@126.com";//密送
$smtpuser1 = "js@yxj.org.cn";//SMTP服务器的用户帐号
$smtppass1 = "ptmkt58545118";//SMTP服务器的用户密码
$mailsubject1 = "微信后台每日消息";//邮件主题

$mailbody1 = "<h1>微信消息每日发送</h1><table border='1'><tr>
    <th>频道</th>
    <th>内容</th>
	 <th>时间</th>
  </tr>";
	while($row1 = mysql_fetch_array($result1))
	{
		$time1=date('Y-m-d H:i:s', $row1['date']); 
		$mailbody1=$mailbody1."<tr><td>{$row1['catid']}</td><td >{$row1['message']}</td><td >{$time1}</td>
				</tr>";
	}
	$mailbody1=$mailbody1."</table>";
$mailtype1 = "HTML";
$smtp1 = new smtp($smtpserver1,$smtpserverport1,true,$smtpuser1,$smtppass1);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
$smtp1->debug = false;//是否显示发送的调试信息
$smtp1->sendmail($smtpemailto1, $smtpusermail1, $mailsubject1, $mailbody1, $mailtype1,$smtpemailcc1,$smtpemailbcc1);	

//推送王世枭产经消息


$result2 = mysql_query("select * from wx_user_messages where UNIX_TIMESTAMP(NOW())-date<=86400 order by catid, date");

##########################################
$smtpserver2 = "smtp.qq.com";//SMTP服务器
$smtpserverport2 = 25;//SMTP服务器端口
$smtpusermail2 = "js@yxj.org.cn";//SMTP服务器的用户邮箱
$smtpemailto2 = "wangshixiao@yxj.org.cn";//发送给谁


$smtpuser2 = "js@yxj.org.cn";//SMTP服务器的用户帐号
$smtppass2= "ptmkt58545118";//SMTP服务器的用户密码
$mailsubject2 = "微信后台每日消息";//邮件主题
$mailbody2 = "<h1>微信消息每日发送</h1><table border='1'><tr>
    <th>频道</th>
    <th>内容</th>
	 <th>时间</th>
  </tr>";
	while($row2 = mysql_fetch_array($result2))
	{
		$time2=date('Y-m-d H:i:s', $row2['date']); 
		$mailbody2=$mailbody2."<tr><td>{$row2['catid']}</td><td >{$row2['message']}</td><td >{$time2}</td>
				</tr>";
	}
	$mailbody2=$mailbody2."</table>";
	

;//邮件内容
$mailtype2 = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
##########################################
$smtp2 = new smtp($smtpserver2,$smtpserverport2,true,$smtpuser2,$smtppass2);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
$smtp2->debug = false;//是否显示发送的调试信息
$smtp2->sendmail($smtpemailto2, $smtpusermail2, $mailsubject2, $mailbody2, $mailtype2);


mysql_close($con);
?>