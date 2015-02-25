<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport" >
<title>我的资料</title>
<link rel="stylesheet" type="text/css" href="http://wx.virgobeauty.com/CSS/css.css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
//隐藏微信中网页右上角按钮
document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
WeixinJSBridge.call('hideOptionMenu');
//WeixinJSBridge.call('hideToolbar');
});
</script>
</head>
<body>
<header>
	<img src="/service/wxlogo.jpg" width="60">
</header>
<?php
header("Content-type: text/html; charset=utf-8"); 
date_default_timezone_set('Asia/Shanghai');
require 'Inc/config.php';
$con=mssql_connect($dbserver,$dbusername,$dbpassword) or http_log("Couldn't connect to SQL Server");// on $server
$db=mssql_select_db($dbname,$con) or http_log("Couldn't open database");// $database
mssql_query("SET NAMES UTF8"); //解决乱码问题
$OpenID=$_GET["U"];
$Sql="SELECT realname,Mobil,Department,Hospital,convert(char,registerTime,120) as registerTime from tbl_members where OpenID='$OpenID' ";
$Result = mssql_query($Sql);
?>
<div class="ind_box">
   <div class="myinfo">
<?

	if(mssql_num_rows($Result)<1){//当数据表中无此用户时
		echo "出错了,请微信告知我们,谢谢!";
	}else{
		$row = mssql_fetch_assoc($Result);
?>
        <div class="tbox">
        	<div class="firsttr">
            	<div class="firsttd">姓名</div>
                <div class="secondtd"><? echo iconv("gb2312","utf-8",$row['realname']); ?></div>
            </div>
            <div class="secondtr">
            	<div class="firsttd">电话</div>
                <div class="secondtd"><? echo $row['Mobil']; ?></div>
            </div>
            <div class="thirdtr">
            	<div class="firsttd">科室</div>
                <div class="secondtd"><? echo iconv("gb2312","utf-8",$row['Department']); ?></div>
            </div>
            <?
			if (strlen(iconv("gb2312","utf-8",$row['Hospital']))>1){
			?>
            <div class="secondtr">
            	<div class="firsttd">单位</div>
                <div class="secondtd"><? echo iconv("gb2312","utf-8",$row['Hospital']); ?></div>
            </div>
            <?
			}
			?>
            <div class="thirdtr">
            	<div class="firsttd">绑定日期</div>
                <div class="secondtd"><? echo date("Y-m-d",strtotime(iconv("gb2312","utf-8",$row['registerTime']))); ?></div>
            </div>
        </div>  
<?
	}
mssql_close($con);
?>
   </div>
</div>
</body>
</html>