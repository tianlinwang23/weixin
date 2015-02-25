<?
require("Inc/config.php");
require("Inc/smtp.php");
//邮件内容
$con=mysql_connect($dbserver,$dbusername,$dbpassword) or http_log("Couldn't connect to SQL Server");// on $server
$db=mysql_select_db($dbname,$con) or http_log("Couldn't open database");// $database
mysql_query("set names gbk");

$result2 = mysql_query("select * from wx_user_messages where UNIX_TIMESTAMP(NOW())-date<=86400 and catid='医学界产业报道' order by catid, date");

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
$smtp2->debug = true;//是否显示发送的调试信息
$smtp2->sendmail($smtpemailto2, $smtpusermail2, $mailsubject2, $mailbody2, $mailtype2);


mysql_close($con);
?>