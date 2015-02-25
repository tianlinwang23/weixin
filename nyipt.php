<?php 
$openid=$_POST['openid'];
$imgurl=$_POST['picurl'];
$name=$_POST['name'];
$hospital=$_POST['hospital'];
$wish=$_POST['wish'];

	$link = mysql_connect('localhost', 'newcdnyxj','193newcdnyx') or die( mysql_error());
	mysql_select_db('newcdnyxj',$link) or die('db error');
	mysql_query("SET NAMES UTF8");
	mysql_query("insert into member_profile (openid, imageurl, name,hospital,wish,type) 
VALUES ('$openid', '$imgurl', '$name','$hospital','$wish','newyear')");
echo "<script language=\"javascript\">alert('提交成功');window.location.href='http://www.yxj.org.cn';</script>"; 


?>