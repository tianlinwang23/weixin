<?
require("Inc/config.php");
require("Inc/smtp.php");
//�ʼ�����
$con=mysql_connect($dbserver,$dbusername,$dbpassword) or http_log("Couldn't connect to SQL Server");// on $server
$db=mysql_select_db($dbname,$con) or http_log("Couldn't open database");// $database
mysql_query("set names gbk");

$result2 = mysql_query("select * from wx_user_messages where UNIX_TIMESTAMP(NOW())-date<=86400 and catid='ҽѧ���ҵ����' order by catid, date");

##########################################
$smtpserver2 = "smtp.qq.com";//SMTP������
$smtpserverport2 = 25;//SMTP�������˿�
$smtpusermail2 = "js@yxj.org.cn";//SMTP���������û�����
$smtpemailto2 = "wangshixiao@yxj.org.cn";//���͸�˭


$smtpuser2 = "js@yxj.org.cn";//SMTP���������û��ʺ�
$smtppass2= "ptmkt58545118";//SMTP���������û�����
$mailsubject2 = "΢�ź�̨ÿ����Ϣ";//�ʼ�����
$mailbody2 = "<h1>΢����Ϣÿ�շ���</h1><table border='1'><tr>
    <th>Ƶ��</th>
    <th>����</th>
	 <th>ʱ��</th>
  </tr>";
	while($row2 = mysql_fetch_array($result2))
	{
		$time2=date('Y-m-d H:i:s', $row2['date']); 
		$mailbody2=$mailbody2."<tr><td>{$row2['catid']}</td><td >{$row2['message']}</td><td >{$time2}</td>
				</tr>";
	}
	$mailbody2=$mailbody2."</table>";
	

;//�ʼ�����
$mailtype2 = "HTML";//�ʼ���ʽ��HTML/TXT��,TXTΪ�ı��ʼ�
##########################################
$smtp2 = new smtp($smtpserver2,$smtpserverport2,true,$smtpuser2,$smtppass2);//�������һ��true�Ǳ�ʾʹ�������֤,����ʹ�������֤.
$smtp2->debug = true;//�Ƿ���ʾ���͵ĵ�����Ϣ
$smtp2->sendmail($smtpemailto2, $smtpusermail2, $mailsubject2, $mailbody2, $mailtype2);


mysql_close($con);
?>