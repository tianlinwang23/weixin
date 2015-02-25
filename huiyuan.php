
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>医学界会员注册</title>

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


function ishospital(){
	var job=$('input[name="radiobutton"]:checked').val();
	var other=document.getElementById("other");
	if (job=="true"){
		$(".f_component").css('display','block');
		$("#checkbox").css('display','none');
		$("#checkbox2").css('display','none');
		$("#submit").css('display','block');
		$("#company_ranks").css('display','none');
		$("#sex").css('display','block');
		$("#company-contact").css('display','none');
		$("#login-head-geren").css('display','block');
		$(".geren").css('display','block');
		$(".summary").css('display','none');
		$("#login-head").css('display','none');
		$(".login").css('width','280px');
		$("#backTop").css('display','inline');
	}
	else{
		/*$(".f_component").css('display','block');
		$("#checkbox").css('display','none');
		$("#checkbox2").css('display','none');
		$("#hospital0").css('display','none');
		$("#hospital1").css('display','none');
		$("#office0").css('display','none');
		$("#office1").css('display','none');
		$("#hospital_ranks").css('display','none');
		$("#submit").css('display','block');
		$("#name").css('display','none');
		$("#sex").css('display','none');
		$("#company-contact").css('display','block');
		$(".qiye").css('display','block');
		$("#login-head-qiye").css('display','block');
		$("#login-head").css('display','none');
		$(".summary").css('display','none');
		$(".login").css('width','280px');
		*/
		$(".f_component").css('display','block');
		$("#checkbox").css('display','none');
		$("#checkbox2").css('display','none');
		$("#submit").css('display','block');
		$("#company_ranks").css('display','none');
		$("#sex").css('display','block');
		$("#company-contact").css('display','none');
		$("#login-head-qiye").css('display','block');
		$(".qiye").css('display','block');
		$(".summary").css('display','none');
		$("#login-head").css('display','none');
		$(".login").css('width','280px');
		$("#backTop").css('display','inline');
	}
}

</script>

</head>

<body>
<div class="bg">
<form id="loginform" name="loginform" method="post" action="join_us.php" onsubmit enctype="multipart/form-data">
<div class="login">
  <div class="login-head" id="login-head">
    <h1>会员类别</h1>
  </div>
 
   <div class="login-head" id="login-head-geren" style="display:none">
    <h1>普通会员</h1>
  </div>
  <div class="login-head" id="login-head-qiye" style="display:none"> 
    <h1>高级会员</h1>
  </div>
    <div class="register-meta">
	<?php
	$wechat=$_GET["wechat"];
	?>
	<div class="f_component" id="checkbox">
	<p class="huiyuan_p" >普通会员</p>  <input type="radio" name="radiobutton" value="true" onclick="ishospital()" class="huiyuan_i" id="inhos">
	</div>
	<div class="f_component" id="checkbox2">
	<p class="huiyuan_p">高级会员</p> <input type="radio" name="radiobutton" value="false" onclick="ishospital()" class="huiyuan_i" id="outhos"> 
	</div> 
	<div class="summary" style="height:150px">
	<image width="100%" height="130px" src="yxj_files/zhaomu.jpg">
	</div>
	<div class="f_component"  id="name" style="display:none">
	<p class="test1">姓名</p>
    <input class="information" value="" name="name" onclick="setFocus(this)" id="register-name">
	</div>
	<div class="f_component"  id="sex" style="display:none">
	<p class="test1">性别</p>
	<div class="where"><p>男</p><input type="radio"  name="sex" value="男"></input></div>
    <div class="where"><p>女</p><input type="radio"  name="sex" value="女"></input></div>
	</div>

	<div class="f_component" style="display:none">
	<p class="test1">省份</p>
    <select class="information" id="one" name="one" size="1"  ">
    <option value="" >请选择省份</option>
	  <?php
	  $sql_pr = "Select * from  pre_common_district where upid=0";
	  $result_pr = mysql_query( $sql_pr );
	  while( $rs_pr = mysql_fetch_array( $result_pr ) )
	  {
	   echo "<option value=$rs_pr[id]>$rs_pr[name]</option>";
	  }
	  ?>  
	</select>
	</div>
	
	<div class="f_component"  id="company"  style="display:none">
	<p class="test1">单位</p>
    <input class="information" value="" name="company" onclick="setFocus(this)" id="reg-company">
	</div>
	<div class="f_component"  id="company-contact"  style="display:none">
	<p class="test1">联系人</p>
    <input class="information" value="" name="contact" onclick="setFocus(this)" id="contact">
	</div>
	

	
	<div class="f_component" style="display:none">
	<p class="test1">邮箱</p>
    <input class="information" value="" name="mail" onclick="setFocus(this)" id="register-mail">
	</div>
	<div class="f_component" style="display:none">
	<p class="test1">电话</p>
    <input class="information" value="" name="phone" onclick="setFocus(this)" id="register-phone">
	</div>
	
	<input type="hidden" name="openid" value="<?php echo $openid; ?>" />
	<input type="hidden" name="wechat" value="<?php echo $wechat; ?>" />
  </div>
  <div class="buttons register-buttons" id="submit" style="display:none">
    <button type="submit" name="wp-submit" id="wp-submit" class="register-button button-primary" value="注册">提<span>交</span></button>
  </div>
  <div class="geren" style="display:none"><strong style="font-weight:bold ;color:rgb(242,151,32)">普通会员：
300元/年，尊享：</br></strong><p style="
    line-height: 25px;
    font-size: 14px;">
（1）.全年《医学界》会刊，共12期，价值360元/年，留住一年的精彩；
</br> 
（2）.八折参与《医学界》线下会议，内容涉及产经、管理、投资、人文等，与大咖面对面，就这么简单；</br>
（3）免费参与《医学界》“医堂课”继续教育课程，300场课程，充电、培训机会多多;
</br>
（4）成为会员后，更尊享线上线下会议一对一短信/邮件通知，精彩绝不能错过！
</br>

</p></div>




  <div class="qiye" style="display:none"><strong style="font-weight:bold ;color:rgb(242,151,32)">1000元/年，您可以获得：
</br></strong><p style="
    line-height: 25px;
    font-size: 14px;">

（1）4套全年《医学界》会刊，每套12期，价值1440元/年，是收藏细读、馈赠亲友的文化礼品；
</br> 
（2）7.5折参与《医学界》线下会议，内容涉及产经、管理、投资、人文等；
</br>

（3）提供4个名额免费参与“医堂课”名额，全年300场继续教育课程；</br>

</p></div>

</div>
</form>

</div>
<a style="display: none;" id="backTop" href="http://wx.yxj.org.cn/invitation/huiyuan.php" data-title="返回" title="返回"><div class="btn-inner-text"></div></a>
<script type="text/javascript">
function setFocus(obj){
    $(obj).val('').focus()
}

$('.register-button').click(function(){
  var login = $('#register-name').val();
  var phone = $('#register-phone').val();
  var contact = $('#contact').val();
  var company=$('#reg-company').val();
  var email=$('#register-mail').val();
  var t=document.getElementById("one");
  var one =t.options[t.selectedIndex].value;
  var checkbox=$('input[name="radiobutton"]:checked').val();
  var sex=$('input[name="sex"]:checked').val();
  
  
  if(checkbox=='true'){
	 if(login==''){
     alert("请输入用户名");
     return false;   
	 }
	 if(sex==undefined){
	 alert("请输入性别");
	  return false; 
	 }
	if(one==''){
    alert("请输入省份");
    return false;   
	}
   if(company==''){
    alert("请输入单位");
    return false;   
   }
   if(email==''){
    alert("请输入邮箱");
    return false;   
   }
	if(phone==''){
    alert("请输入电话");
    return false;   
   } 
	 if(!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(email)) 
   { alert("请输入正确的邮箱地址");
    return false;   }
  }
  else if(checkbox=='false'){
		 if(login==''){
     alert("请输入用户名");
     return false;   
	 }
	 if(sex==undefined){
	 alert("请输入性别");
	  return false; 
	 }
	if(one==''){
    alert("请输入省份");
    return false;   
	}
   if(company==''){
    alert("请输入单位");
    return false;   
   }
   if(email==''){
    alert("请输入邮箱");
    return false;   
   }
	if(phone==''){
    alert("请输入电话");
    return false;   
   } 
	 if(!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(email)) 
   { alert("请输入正确的邮箱地址");
    return false;   }
  }
  /*else if(checkbox=='false'){
  if(one==''){
    alert("请输入省份");
    return false;   
	}
	if(company==''){
    alert("请输入单位");
    return false;   
   }
	if(contact==''){
    alert("请输入联系人");
    return false;   
   }
   if(email==''){
    alert("请输入邮箱");
    return false;   
   }
	if(phone==''){
    alert("请输入电话");
    return false;   
   } 
	 if(!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(email)) 
   { alert("请输入正确的邮箱地址");
    return false;   }
  }
	*/
	 /*if(login==''){
     alert("请输入用户名");
     return false;   
	 }
	if(sex==undefined){
	 alert("请输入性别");
	  return false; 
	 }
	if(one==''){
    alert("请输入省份");
    return false;   
   }
  
  
  if(checkbox=="false"){
		
	if(company==''){
	  alert("请输入单位");
	  return false;   
    }
	if(zhiwu==''){
	  alert("zhiwu");
	  return false;   
    }
  
  }
  else if (checkbox=="true"){
	if(place_c==""){
		alert("请输入医院");
		return false;
	}
	if(other.type=='text'){
		if(other.value==''||other.value=='其他'){
				alert("请输入医院");
				return false;   
		}
  }
  if(bigoffice==""){
	alert("请输入大类");
	return false;   
  }
  if(smalloffice==""){
	alert("请输入小类");
	return false;   
  }
   if(zhicheng==""){
	alert("请输入职称");
	return false;   
  }
  }
 
  if(phone==''){
    alert("请输入手机号码");
    return false;   
  }
  
  var login=login.replace(/(^\s+)|(\s+$)/g, "");
 
  if (!login.match(/^[\u4e00-\u9fa5]{2,4}$/)){
    alert("请输入正确的姓名");
    return false;   
  }*/
  
	/*
  if(!(/^[a-z0-9_@\.-]+@([a-z0-9_-]+\.)+[a-z0-9_-]{2,3}$/).test(mail)){
    alert("请输入正确的邮箱地址");
    return false;   
  }
  */
  /*if(!(/^[\d]{11}|[\d]{13}$/).test(phone)){
    alert("请输入正确的手机号");
    return false;   
  }
  */
  
})
</script>
</body></html>