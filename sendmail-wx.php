<?php
require("Inc/config.php");
require("Inc/smtp.php");
//�ʼ�����
$con=mysql_connect($dbserver,$dbusername,$dbpassword) or http_log("Couldn't connect to SQL Server");// on $server
$db=mysql_select_db($dbname,$con) or http_log("Couldn't open database");// $database
mysql_query("set names gbk");

//����������Ϣ1
 $result = mysql_query("select * from wx_user_messages where UNIX_TIMESTAMP(NOW())-date<=86400 order by catid, date");

##########################################
$smtpserver = "smtp.qq.com";//SMTP������
$smtpserverport = 25;//SMTP�������˿�
$smtpusermail = "js@yxj.org.cn";//SMTP���������û�����
$smtpemailto = "ptmkt007@126.com";//���͸�˭
$smtpemailcc="guojingtao001@126.com";//����
$smtpemailbcc="chenyifu@yxj.org.cn";
$smtpuser = "js@yxj.org.cn";//SMTP���������û��ʺ�
$smtppass = "ptmkt58545118";//SMTP���������û�����
$mailsubject = "΢�ź�̨ÿ����Ϣ";//�ʼ�����
$mailbody = "<h1>΢����Ϣÿ�շ���</h1><table border='1'><tr>
    <th>Ƶ��</th>
    <th>����</th>
	 <th>ʱ��</th>
  </tr>";
	while($row = mysql_fetch_array($result))
	{
		$time=date('Y-m-d H:i:s', $row['date']); 
		$mailbody=$mailbody."<tr><td>{$row['catid']}</td><td >{$row['message']}</td><td >{$time}</td>
				</tr>";
	}
	$mailbody=$mailbody."</table>";
	

;//�ʼ�����
$mailtype = "HTML";//�ʼ���ʽ��HTML/TXT��,TXTΪ�ı��ʼ�
##########################################
$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//�������һ��true�Ǳ�ʾʹ�������֤,����ʹ�������֤.
$smtp->debug = false;//�Ƿ���ʾ���͵ĵ�����Ϣ
$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype,$smtpemailcc,$smtpemailbcc);

//����������Ϣ2

$result1 = mysql_query("select * from wx_user_messages where UNIX_TIMESTAMP(NOW())-date<=86400 order by catid, date");
$smtpserver1 = "smtp.qq.com";//SMTP������
$smtpserverport1 = 25;//SMTP�������˿�
$smtpusermail1 = "js@yxj.org.cn";//SMTP���������û�����
$smtpemailto1 = "gf@yxj.org.cn";//���͸�˭
$smtpemailcc1="hufang1023@126.com";//����
$smtpemailbcc1="tskyangyu@126.com";//����
$smtpuser1 = "js@yxj.org.cn";//SMTP���������û��ʺ�
$smtppass1 = "ptmkt58545118";//SMTP���������û�����
$mailsubject1 = "΢�ź�̨ÿ����Ϣ";//�ʼ�����

$mailbody1 = "<h1>΢����Ϣÿ�շ���</h1><table border='1'><tr>
    <th>Ƶ��</th>
    <th>����</th>
	 <th>ʱ��</th>
  </tr>";
	while($row1 = mysql_fetch_array($result1))
	{
		$time1=date('Y-m-d H:i:s', $row1['date']); 
		$mailbody1=$mailbody1."<tr><td>{$row1['catid']}</td><td >{$row1['message']}</td><td >{$time1}</td>
				</tr>";
	}
	$mailbody1=$mailbody1."</table>";
$mailtype1 = "HTML";
$smtp1 = new smtp($smtpserver1,$smtpserverport1,true,$smtpuser1,$smtppass1);//�������һ��true�Ǳ�ʾʹ�������֤,����ʹ�������֤.
$smtp1->debug = false;//�Ƿ���ʾ���͵ĵ�����Ϣ
$smtp1->sendmail($smtpemailto1, $smtpusermail1, $mailsubject1, $mailbody1, $mailtype1,$smtpemailcc1,$smtpemailbcc1);	

//���������ɲ�����Ϣ


$result2 = mysql_query("select * from wx_user_messages where UNIX_TIMESTAMP(NOW())-date<=86400 order by catid, date");

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
$smtp2->debug = false;//�Ƿ���ʾ���͵ĵ�����Ϣ
$smtp2->sendmail($smtpemailto2, $smtpusermail2, $mailsubject2, $mailbody2, $mailtype2);


mysql_close($con);
?>