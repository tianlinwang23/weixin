<?php
if(isset($_POST['openid'])){
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
echo "<script language=\"javascript\">alert('提交成功');</script>"; 
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>新年寄语</title>

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
<script language="javascript">


</script>

</head>

<body>
<form id="loginform" name="loginform" method="post" action="newyear.php" onsubmit enctype="multipart/form-data">
<div class="login">
  <div class="login-head">
    <h1>新年愿望</h1>
  </div>
    <div class="register-meta">
	<?php
	$openid=$_GET["openid"];
	$picurl=$_GET["picurl"];
	?>
	<div class="f_component" style="display:block">
	<p class="test1">姓名</p>
    <input class="information" value="" name="name" onclick="setFocus(this)" id="register-name">
	</div>
	<div class="f_component" style="display:block">
	<p class="test1">医院</p>
    <input class="information" value="" name="hospital" onclick="setFocus(this)" id="hospital">
	</div>
    <div class="f_component" style="display:block">
	<p class="test1">新年愿望</p>
    <textarea class="information" rows="4" cols="20"  style="height:100px" value="" name="wish" onclick="setFocus(this)" id="wish"></textarea>
	</div>
	<input type="hidden" name="openid" value="<?php echo $openid; ?>" />
	<input type="hidden" name="picurl" value="<?php echo $picurl; ?>" />
  </div>
  <div class="buttons register-buttons" id="submit" style="display:block">
    <button type="submit" name="wp-submit" id="wp-submit" class="register-button button-primary" value="注册">提<span>交</span></button>
  </div>
  

</div>
</form>



<script type="text/javascript">
function setFocus(obj){
    $(obj).val('').focus()
}

$('.register-button').click(function(){
  var login = $('#register-name').val();
  var hospital=$('#hospital').val();
  var wish=$('#wish').val(); 
	
	 if(login==''){
     alert("请输入用户名");
     return false;   
	 }
	 
	 if(hospital==""){
	 alert("请输入您的医院");
     return false; 
	 }

	 if(wish==""){
	 alert("请输入您的新年愿望");
     return false; 
	 }
	/*
  if(!(/^[a-z0-9_@\.-]+@([a-z0-9_-]+\.)+[a-z0-9_-]{2,3}$/).test(mail)){
    alert("请输入正确的邮箱地址");
    return false;   
  }
  */
 
  
})
</script>
</body></html>