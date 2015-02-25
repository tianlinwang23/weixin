<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>脱单检查</title>
<script language="javascript">
function jump(x){
 var page=x;

 window.location.href="page-search.php?page="+page;
}


function isshow(y){
var id=y;
var url="isshow.php";
	url=url+"?q="+id;
	url=url+"&sid="+Math.random();
	
	
	
	
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
		alert(xmlhttp.responseText);
		
    }
  }
xmlhttp.open("GET",url,true);
xmlhttp.send(null);

		
  
}

</script>
</head>

<body>
<!--分页开始-->
<?php
require "config.php";
if(isset($_GET['page']))     //由GET方法获得页面传入当前页数的参数
{
    $page = $_GET['page'];
}
else
{
    $page = 1;
}
$page_size = 20;              //每页显示两条数据





$sql = "select * from  member_profile order by id ";


$result=mysql_query($sql);
$total = mysql_num_rows($result);
$start=($page-1)*$page_size;
 

//echo $total;

//开始计算总页数
if($total)
{
    if($total < $page_size)
        $page_count = 1;
    if($total % $page_size)
    {
        $page_count = (int)($total/$page_size) + 1;
    }
    else
    {
        $page_count = $total/$page_size;
    }
}
else
{
    $page_count = 0;
}
//翻页链接
$turn_page = '';

if($page == 1)
{
    $turn_page .= '首页 |  上一页  |  ';
}
else
{
    $turn_page .= '<a href=page-search.php?page=1> 首页</a>  |  <a href=page-search.php?page='.($page-1).'>上一页</a>  |  ';
}
if($page == $page_count || $page_count == 0)
{
    $turn_page .= '下一页  |  尾页';
}
else
{
    $turn_page .= '<a href=page-search.php?page='.($page+1).'>下一页</a>  |  <a href=page-search.php?page='.$page_count.'>尾页</a>';
}


$sql = $sql." limit ". ($page-1)*$page_size .", ".$page_size;

$result = mysql_query($sql);

?>
<table  border="1" >
<tbody>
<tr>
<th>
用户id
</th>
<th>
姓名
</th>
<th>
性别
</th>
<th>
图片
</th>
<th>
是否显示
</th>
</tr>
<?php
while($row = mysql_fetch_array($result))
{
echo "<tr>
			<td >{$row['id']}
				</td>
				<td >{$row['name']}
				</td>
				<td >{$row['sex']}
				</td>
			<td ><img src='{$row['imageurl']}' width='150px' height='200px'>
				</td>
				<td ><button value='{$row['id']} class='button' onclick='isshow(this.value)'>屏蔽</button>
				</td>
				</tr>";


}

echo "<tbody></table>";

 echo "<div class='turn page'>{$turn_page}</div>";
?>
<select class="page" id="page" name="page" size="1"  onchange="jump(options[this.selectedIndex].value)">
<option value="" >请选择页数</option> 
	
	  <?php
		
	 FOR ($i = 1; $i <= $page_count; $i++) {
	   echo "<option value=$i>$i</option> ";
	  }
	  ?>  

</select>
<?php 
echo "这是第".$page."页";
?>
</body>
</html>