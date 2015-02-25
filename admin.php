
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>2015医药企业数字营销交流会</title>

<?php
$link = mysql_connect('localhost','newcdnyxj','193newcdnyx') or die( mysql_error());
 mysql_select_db('newcdnyxj',$link) or die('db error');
 mysql_query("SET NAMES UTF8");
  ?>
  

<link rel="stylesheet" href="./yxj_files/style.css" type="text/css" media="screen">
<link rel="stylesheet" href="./yxj_files/login.css" type="text/css" media="screen">
<script type="text/javascript" src="./yxj_files/zepto.min.js"></script>
<script language="javascript">



</script>


</head>

<body>
<form id="loginform" name="loginform" method="post" action="input.php"  onsubmit="return check();">
<div class="login">
  <div class="login-head">
    <h1>欢迎报名</h1>
  </div>
    <div class="register-meta">
	<div class="f_component">
	<p class="test1">姓名</p>
    <input class="information" value="" name="name" onclick="setFocus(this)" id="register-name">
	</div>	
	<div class="f_component">
	<p class="test1">邮箱</p>
    <input class="information" value="" name="email" onclick="setFocus(this)" id="email">
    </div>
	<div class="f_component">
	<p class="test1">手机</p>
    <input class="information" value="" name="phone" onclick="setFocus(this)" id="register-phone">
	</div>
	<div class="f_component">
	<p class="test1">公司</p>
    <input class="information" value="" name="company" onclick="setFocus(this)" id="company">
	</div>
	<div class="f_component">
	<p class="test1">职务</p>
    <input class="information" value="" name="zhiwu" onclick="setFocus(this)" id="zhiwu">
	</div>
	
  
  <div class="buttons register-buttons">
    <button type="submit" name="wp-submit" id="wp-submit" class="register-button button-primary" value="注册">报<span>名</span></button>
  </div>
<div><strong style="font-weight:bold ;color:rgb(242,151,32)">会议费用：1000元／人</br></strong><p style="
    line-height: 25px;
    font-size: 14px;">
会议时间：2015年1月20日</br> 
会议地点：上海紫金山大酒店金陵厅（上海市浦东新区东方路778号）</br>
会务:杨宇18621918858;洪浩淼13917041003 </br>
银行汇款</br>
收款单位：上海毕马广告有限公司</br>开户行：中信银行上海黄浦支行 </br>
账号：7311610182600058085</br>
支付宝汇款：js@yxj.org.cn</br>
备注：汇款办理后，请将汇款底单传真至021-58545118-25 或将底单截图发送至邮箱jennifer@yxj.org.cn，以尽快确定发票开具信息和其他相关事宜。
</br>

</p></div>
</div>
</form>



<script type="text/javascript">
function setFocus(obj){
    $(obj).val('').focus()
}

$('.register-button').click(function(){
 
  var phone = $('#register-phone').val();
  var login = $('#register-name').val();
  var mail=$('#email').val();
  var company=$('#company').val();
  var zhiwu=$('#zhiwu').val();
  if(login==''){
    alert("请输入用户名");
    return false;   
  }
  if(phone==''){
    alert("请输入手机号码");
    return false;   
  }
  if(mail==''){
    alert("请输入手机号码");
    return false;   
  }
  if(company==''){
    alert("请输入手机号码");
    return false;   
  }
  if(zhiwu==''){
    alert("请输入手机号码");
    return false;   
  }
  var login=login.replace(/(^\s+)|(\s+$)/g, "");
 
  if (!login.match(/^[\u4e00-\u9fa5]{2,4}$/)){
    alert("请输入正确的姓名");
    return false;   
  }
  

  if(!(/^[a-z0-9_@\.-]+@([a-z0-9_-]+\.)+[a-z0-9_-]{2,3}$/).test(mail)){
    alert("请输入正确的邮箱地址");
    return false;   
  }
  
  if(!(/^[\d]{11}|[\d]{13}$/).test(phone)){
    alert("请输入正确的手机号");
    return false;   
  }
})
</script>
</body>
</html>