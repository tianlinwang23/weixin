<?php
require 'Inc/config.php';
require 'Inc/function.php';
if(false == checkSignature()) {
	exit(0);
}
//获取echostr
$echostr = $_GET ['echostr'];
if($echostr) {
	echo $echostr;
	exit(0);
}
//TODO分析用户输入并输出
$PostData = $GLOBALS["HTTP_RAW_POST_DATA"];
if(!$PostData){
	http_log("接收到数据为空!\n");
	echo "wrong input!";
	exit(0);
}
//http_log($PostData);  //txt文档记录用户发送的消息
$postObj = simplexml_load_string($PostData, 'SimpleXMLElement', LIBXML_NOCDATA);
if(!$postObj) {
	http_log("接收到数据为空!\n");
	echo "wrong input!";
	exit(0);
}
$fromUserName = $postObj->FromUserName;//发送方帐号（一个OpenID）
$toUserName = $postObj->ToUserName;//微信公众平台 原始ID
$createTime = $postObj->CreateTime; //消息创建时间 （整型） 
$msgType = $postObj->MsgType;//消息类型 文本:text 图片:image 语音:voice  视频:video 地理位置:location 链接:link 事件:event 
$msgId = $postObj->MsgId;//消息id，64位整型
$content = $postObj->Content; $content = htmlentities(trim($content),ENT_QUOTES,'UTF-8');//文本消息内容 + 解决腾讯表情带单引号问题
$picUrl = $postObj->PicUrl;//图片链接
$format = $postObj->Format;//语音格式，如amr，speex等 
$thumbMediaId = $postObj->ThumbMediaId;//视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
$mediaId = $postObj->MediaId;//图片消息媒体id，可以调用多媒体文件下载接口拉取数据。 
$location_X = $postObj->Location_X;//地理位置维度
$location_Y = $postObj->Location_Y;//地理位置经度
$scale = $postObj->Scale;//地图缩放大小
$label = $postObj->Label;//地理位置信息
$title = $postObj->Title;//消息标题
$description = $postObj->Description;//消息描述
$url = $postObj->Url;//消息链接
$recongnition = $postObj->Recongnition;//语音识别结果，UTF8编码
$event = $postObj->Event;//事件类型，subscribe(订阅)、unsubscribe(取消订阅) 
$eventKey = $postObj->EventKey;//事件KEY值，qrscene_为前缀，后面为二维码的参数值
$ticket = $postObj->Ticket;// 二维码的ticket，可用来换取二维码图片 
$latitude = $postObj->Latitude;//地理位置纬度
$longitude = $postObj->Longitude;//地理位置经度
$precision = $postObj->Precision;//地理位置精度
$content = trim($content);//去掉前后空格
$NowDate=substr(date("Ymd", $time),2,6);
$con=mssql_connect($dbserver,$dbusername,$dbpassword) or http_log("Couldn't connect to SQL Server");// on $server
$db=mssql_select_db($dbname,$con) or http_log("Couldn't open database");// $database
 switch($msgType){
	case "text"://1.用户发送文本消息
	if(preg_match("/^\d{6}$/",$content)){//1.1.a符合6位数字情况下 则为日期查询
		if($content>='120406' && $content<=$NowDate){//2012.4.6之前系统无信息
			$QueryDate = "20".substr($content,0,2)."-".substr($content,2,2)."-".substr($content,4,2);
			$Sql_S_T_N="SELECT TOP 10 ID,TITLE,Introduction,ThumPic,convert(char,InputTime,120) as InputTime FROM tbl_News where IsWx='1' and IsShow = '1' and AuditStatus = '1' and ThumPic<>'nopic.gif' ";
			$Sql_S_T_N=$Sql_S_T_N."and DATEDIFF(dd, InputTime, '$QueryDate') = 0";
			$Sql_S_T_N=$Sql_S_T_N."order by WxOrder desc, Hits desc";
			$Result_S_T_N=mssql_query($Sql_S_T_N);
			$Num_S_T_N=mssql_num_rows($Result_S_T_N);
			if($Num_S_T_N==1){//1.1.a.1当日只有一条资讯
				//回复单图文
				$Row=mssql_fetch_assoc($Result_S_T_N);
				$Title=iconv("gb2312","utf-8",$Row['TITLE']);
				$Description=iconv("gb2312","utf-8",$Row['Introduction']);
				$PicUrl="http://www.yxj.org.cn/UploadFile/News/{$Row['ThumPic']}";
				$InputTime=strtotime($Row['InputTime']);
				$Url=$SiteUrl."/WX/{$Row['ID']}.html?OID=".$fromUserName."&idx={$X}#top";
				$resultStr = sprintf($Conf_NewsMsg, $fromUserName, $toUserName, $InputTime, $Title, $Description, $PicUrl,$Url);
			}elseif($Num_S_T_N>1){//1.1.a.2当日有多条资讯
				//回复多图文
				$X=1;
				$resultStr = "
		<xml>
		<ToUserName><![CDATA[{$fromUserName}]]></ToUserName>
		<FromUserName><![CDATA[{$toUserName}]]></FromUserName>
		<CreateTime>{$time}</CreateTime>
		<MsgType><![CDATA[news]]></MsgType>
		<ArticleCount>{$Num_S_T_N}</ArticleCount>
		<Articles>";
						while($Row = mssql_fetch_array($Result_S_T_N)){
		$Title=iconv("gb2312","utf-8",$Row['TITLE']);
		$Description=iconv("gb2312","utf-8",$Row['Introduction']);
		$PicUrl="http://www.yxj.org.cn/UploadFile/News/{$Row['ThumPic']}";
		$Url=$SiteUrl."/WX/{$Row['ID']}.html?OID=".$fromUserName."&idx={$X}#top";
		$resultStr = $resultStr."
		<item>
		<Title><![CDATA[{$Title}]]></Title> 
		<Description><![CDATA[{$Description}]]></Description>
		<PicUrl><![CDATA[{$PicUrl}]]></PicUrl>
		<Url><![CDATA[{$Url}]]></Url>
		</item>";
		$X=$X+1;
						}
		$resultStr = $resultStr."
		</Articles>
		</xml>";
			}else{//1.1.a.3当日无资讯
				$QueryDate = "20".substr($content,0,2)."年".substr($content,2,2)."月".substr($content,4,2)."日";
				$retMsg = "貌似没找到{$QueryDate}的资讯，你可以尝试直接回复关键词（如“内科”“医改”“贾永青”），可查收到最新相关资讯。谢谢你的支持！";
				$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
			}
		}else{
			if($content>$NowDate){
				$retMsg = "/:wipe今天才几号哦？哥不是神算,那天的事我也不知道！\n你可以尝试直接回复关键词（如“内科”“医改”“贾永青”），可查收到最新相关资讯。";
				
			}else{
				$retMsg = "/:8*两年前的资讯暂时真没空整理了...\n你可以尝试直接回复关键词（如“内科”“医改”“贾永青”），可查收到最新相关资讯。";
			}
			$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
		}
	}
	elseif($content=="58"){
	$siteadd="http://newcdn.yxj.org.cn/xiaohua/admin.php";
	$retMsg = "<a href=\"$siteadd\">完善个人资料（点击进入）。</a>";
	$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
	}
	elseif((preg_match("/^[\x7f-\xff]+$/", $content)) || (!strpos('X'.$content, '/:'))){//1.1.b无符合规则情况下 则进行关键词匹配 正则检查是否含有中文
		if($content=="我要脱单"){
					$retMsg ="光棍节过厌了，不想单身了？快来参与《医学界》“我要脱单”活动，发来照片填写资料，迅速找到心仪的另一半！
";
					$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
				}
		else{
					$newcontent=iconv("utf-8","gb2312",$content);//mssql 字段问题
					$Sql_S_T_N="SELECT TOP 10 ID,TITLE,Introduction,ThumPic,convert(char,InputTime,120) as InputTime FROM tbl_News where IsWx='1' and (Title like '%".$newcontent."%' or KeyWord like '%".$newcontent."%') and IsShow = '1' and AuditStatus = '1' ";// and ThumPic<>'nopic.gif' ";
					$Sql_S_T_N=$Sql_S_T_N."order by ID desc, Hits desc";
					$Result_S_T_N=mssql_query($Sql_S_T_N);
					$Num_S_T_N=mssql_num_rows($Result_S_T_N);
					if($Num_S_T_N==1){//1.1.b.1搜索只有一条资讯
						//回复单图文
						$Row=mssql_fetch_assoc($Result_S_T_N);
						$Title=iconv("gb2312","utf-8",$Row['TITLE']);
						$Description=iconv("gb2312","utf-8",$Row['Introduction']);
						$PicUrl="http://www.yxj.org.cn/UploadFile/News/{$Row['ThumPic']}";
						$InputTime=strtotime($Row['InputTime']);
						$Url=$SiteUrl."/WX/{$Row['ID']}.html?OID=".$fromUserName."&idx={$X}#top";
						$resultStr = sprintf($Conf_NewsMsg, $fromUserName, $toUserName, $InputTime, $Title, $Description, $PicUrl,$Url);
					}elseif($Num_S_T_N>1){//1.1.b.2当日有多条资讯
						//回复多图文
						$X=1;
						$resultStr = "
				<xml>
				<ToUserName><![CDATA[{$fromUserName}]]></ToUserName>
				<FromUserName><![CDATA[{$toUserName}]]></FromUserName>
				<CreateTime>{$time}</CreateTime>
				<MsgType><![CDATA[news]]></MsgType>
				<ArticleCount>{$Num_S_T_N}</ArticleCount>
				<Articles>";
								while($Row = mssql_fetch_array($Result_S_T_N)){
				$Title=iconv("gb2312","utf-8",$Row['TITLE']);
				$Description=iconv("gb2312","utf-8",$Row['Introduction']);
				$PicUrl="http://www.yxj.org.cn/UploadFile/News/{$Row['ThumPic']}";
				$Url=$SiteUrl."/WX/{$Row['ID']}.html?OID=".$fromUserName."&idx={$X}#top";
				$resultStr = $resultStr."
				<item>
				<Title><![CDATA[{$Title}]]></Title> 
				<Description><![CDATA[{$Description}]]></Description>
				<PicUrl><![CDATA[{$PicUrl}]]></PicUrl>
				<Url><![CDATA[{$Url}]]></Url>
				</item>";
				$X=$X+1;
								}
				$resultStr = $resultStr."
				</Articles>
				</xml>";
					}else{//1.1.b.3查询无果
						$retMsg = "貌似没找到 {$content} 相关的资讯，可以试下回复当天日期（如“".$NowDate."”），可查收到当天的全部资讯。谢谢您的支持！";
						$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
					}
	}
	}else{//1.1.c无符合规则情况下
		$retMsg = "/::)这里是《医学界》杂志，回复日期（如“".$NowDate."”）,可查收到当天的全部资讯;\n回复关键词（如“医改”“内科”）,可查收到最新相关资讯。谢谢您的支持！";
		$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
	}
	break;

	case "location"://2.用户发送地理位置消息
		$Sql_S_T_M="SELECT * FROM tbl_members where OpenID='{$fromUserName}' ";
		$Result_S_T_M=mssql_query($Sql_S_T_M);
		$Num_S_T_M=mssql_num_rows($Result_S_T_M);
		if($Num_S_T_M){//对绑定的用户才做验证
			$location=$location_X.",".$location_Y;
			if(!$location) $location="31.242139,121.533922";
			$url="http://api.map.baidu.com/geocoder/v2/?coordtype=gcj02ll&ak=9ceea666f9135a2c440c20cee4600e0f&callback=renderReverse&location=".$location."&output=json&pois=0";
			$json=file_get_contents($url);
			$json=str_replace('renderReverse&&renderReverse({','{',$json);
			$json=str_replace('}})','}}',$json);
			$J = json_decode($json);
			$province=$J->result->addressComponent->province;//根据经纬度获取到城市
			$city=$J->result->addressComponent->city;
			if($province){//当获取到城市时
				$province=str_replace('省','',$province);//过滤 省
				$province=str_replace('市','',$province);//过滤 市
				$city=str_replace('市','',$city);//过滤 市
				$province=iconv("utf-8","gb2312",$province);//mssql 字段问题
				$city=iconv("utf-8","gb2312",$city);//mssql 字段问题
				$Sql_S_T_N="SELECT TOP 10 ID,TITLE,Introduction,ThumPic,convert(char,InputTime,120) as InputTime FROM tbl_News where IsWx='1' and (Title like '%".$province."%' or KeyWord like '%".$province."%' or Title like '%".$city."%' or KeyWord like '%".$city."%') and IsShow = '1' and AuditStatus = '1' ";// and ThumPic<>'nopic.gif' ";
				$Sql_S_T_N=$Sql_S_T_N."order by ID desc, Hits desc";
				$Result_S_T_N=mssql_query($Sql_S_T_N);
				$Num_S_T_N=mssql_num_rows($Result_S_T_N);
				if($Num_S_T_N==1){//1.1.b.1搜索只有一条资讯
					//回复单图文
					$Row=mssql_fetch_assoc($Result_S_T_N);
					$Title=iconv("gb2312","utf-8",$Row['TITLE']);
					$Description=iconv("gb2312","utf-8",$Row['Introduction']);
					$PicUrl="http://www.yxj.org.cn/UploadFile/News/{$Row['ThumPic']}";
					$InputTime=strtotime($Row['InputTime']);
					$Url=$SiteUrl."/WX/{$Row['ID']}.html?OID=".$fromUserName."&idx={$X}#top";
					$resultStr = sprintf($Conf_NewsMsg, $fromUserName, $toUserName, $InputTime, $Title, $Description, $PicUrl,$Url);
				}elseif($Num_S_T_N>1){//1.1.b.2当日有多条资讯
					//回复多图文
					$X=1;
					$resultStr = "
			<xml>
			<ToUserName><![CDATA[{$fromUserName}]]></ToUserName>
			<FromUserName><![CDATA[{$toUserName}]]></FromUserName>
			<CreateTime>{$time}</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<ArticleCount>{$Num_S_T_N}</ArticleCount>
			<Articles>";
							while($Row = mssql_fetch_array($Result_S_T_N)){
			$Title=iconv("gb2312","utf-8",$Row['TITLE']);
			$Description=iconv("gb2312","utf-8",$Row['Introduction']);
			$PicUrl="http://www.yxj.org.cn/UploadFile/News/{$Row['ThumPic']}";
			$Url=$SiteUrl."/WX/{$Row['ID']}.html?OID=".$fromUserName."&idx={$X}#top";
			$resultStr = $resultStr."
			<item>
			<Title><![CDATA[{$Title}]]></Title> 
			<Description><![CDATA[{$Description}]]></Description>
			<PicUrl><![CDATA[{$PicUrl}]]></PicUrl>
			<Url><![CDATA[{$Url}]]></Url>
			</item>";
			$X=$X+1;
							}
			$resultStr = $resultStr."
			</Articles>
			</xml>";
				}else{//1.1.b.3查询无果
					$retMsg = "貌似没找到本地相关的资讯，可以试下回复当天日期（如“".$NowDate."”），可查收到当天的全部资讯\n回复关键词（如“医改”“内科”）,可查收到最新相关资讯。谢谢您的支持！";
					$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
				}
			}
		}else{
			$retMsg = "此项服务需要你先完善下资料,或尝试先取消关注后再关注";
			$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
		}
	break;

	case "event"://3.用户操作事件
		switch($event){
		case "subscribe"://3.1关注事件
			mssql_query("insert into tbl_members (OpenID,UserStatus,registerTime) values('{$fromUserName}',1,GETDATE())");//会员系统加入记录
			$retMsg = "/::)这里是《医学界杂志》，每天为您推送最新的医学咨询"; 
			$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
		break;
		case "unsubscribe"://3.2取消关注事件
			mssql_query("delete from tbl_members where OpenID = '{$fromUserName}' ");//会员系统删除记录
		break;
		case "CLICK"://3.3自定义菜单事件
			switch($eventKey){
				case "yxj-jj"://医学界简介
					$resultStr = "
<xml>
<ToUserName><![CDATA[{$fromUserName}]]></ToUserName>
<FromUserName><![CDATA[{$toUserName}]]></FromUserName>
<CreateTime>{$time}</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>5</ArticleCount>
<Articles>

<item>
<Title><![CDATA[《医学界》 ：医路幸福，有我相伴！]]></Title> 
<PicUrl><![CDATA[http://mmbiz.qpic.cn/mmbiz/a1xwNbPmyoqb1MZ0IUvQPmfVDIykJGXK9wmna2HJQlVzYveZZNyPYUfzUhKMzXiaNNnVzTDL3jAuVZeeJlCjBfQ/0]]></PicUrl>
<Url><![CDATA[http://mp.weixin.qq.com/s?__biz=MjM5MDc3NjQwMA==&mid=100196245&idx=1&sn=f587bd560a01be4572778aec6f1cd572#rd]]></Url>
</item>
<item>
<Title><![CDATA[《医学界》杂志：给医者的职业幸福杂志]]></Title> 
<PicUrl><![CDATA[http://mmbiz.qpic.cn/mmbiz/a1xwNbPmyoqb1MZ0IUvQPmfVDIykJGXKgqlxice7vcHu9vcd2z2Qs7zu4YiaicDtsf7tW6fPT9V4Nk9pc68SEuVpA/0]]></PicUrl>
<Url><![CDATA[http://mp.weixin.qq.com/s?__biz=MjM5MDc3NjQwMA==&mid=100196245&idx=2&sn=717f1627ff84fed7523e82c902ef9a4e#rd]]></Url>
</item>
<item>
<Title><![CDATA[“医学界杂志”微信: 每日一次，推送优质资讯]]></Title> 
<PicUrl><![CDATA[http://mmbiz.qpic.cn/mmbiz/a1xwNbPmyoqb1MZ0IUvQPmfVDIykJGXKra2XtFqLSiaqTbDpHNL8STYYAv6kd2epfWIS1vJSrqzSTJudZCJS2qw/0]]></PicUrl>
<Url><![CDATA[http://mp.weixin.qq.com/s?__biz=MjM5MDc3NjQwMA==&mid=100196245&idx=3&sn=ad78a892a03058417e99d5f51a5019da#rd]]></Url>
</item>
<item>
<Title><![CDATA[@医学界网站 官方微博：互动最好的医者社区官微]]></Title> 
<PicUrl><![CDATA[http://mmbiz.qpic.cn/mmbiz/a1xwNbPmyoqb1MZ0IUvQPmfVDIykJGXK717Ug1icZiaaicbOI5ic6icOLYFTEJvic0vpwTMELtAOeaicUFAQ5ib3Yh6oEQ/0]]></PicUrl>
<Url><![CDATA[http://mp.weixin.qq.com/s?__biz=MjM5MDc3NjQwMA==&mid=100196245&idx=4&sn=46480df9ec0c17f0456c907789869450#rd]]></Url>
</item>
<item>
<Title><![CDATA[医学界手机报：浓缩出精华，订阅送杂志]]></Title> 
<PicUrl><![CDATA[http://mmbiz.qpic.cn/mmbiz/a1xwNbPmyoqb1MZ0IUvQPmfVDIykJGXKFQdRonS370eLRx25oHCw6WyK5cM622UtE7gFZJ0kqVpgsu04A3ia9Jg/0]]></PicUrl>
<Url><![CDATA[http://mp.weixin.qq.com/s?__biz=MjM5MDc3NjQwMA==&mid=100196245&idx=5&sn=ed7439f374e50547490ab6464211af82#rd]]></Url>
</item>

</Articles>
</xml>";
				break;
					case "yxj-xz"://写真
					$retMsg = "界哥，男（根据我们的用户群分析，医学界杂志的订户中，女性比男性多约4个百分点），今年38岁，身高171CM，体重75公斤，大嗓门，喜欢表达观点，精心钻研临床，经常学习或工作到夜里两点，生活不太规律，喜欢喝点酒，没量；喜欢运动，不多；从不抽烟，为了不毒害别人；喜欢买有品味的书，但看得不多；看电影流泪，不过不让别人看见；喜欢抨击时政，但在领导面前总是很恭顺的样子；每年会献血1次，签下了捐献遗体的志愿书，目前职称，刚刚升上副主任医师……总体来说，他是个有爱心、有追求、向往品位、努力提高水平的有志青年一枚（界哥是个虚拟品牌代言人）";
					$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
					break;	
					case "yxj-zp"://招聘
				$retMsg = "《医学界》是目前影响力最大的医疗新媒体，微信拥有近100万用户；微博影响力为行业第一名；网站位居医生类网站前三名。由于业务快速发展，我们正在招聘大量的新人加入，如果您想寻找一个有意义、能发挥个人创造力的工作，请给我们投简历：zhaopin@yxj.org.cn.我们正在招聘的岗位包括：大客户销售，10名，2年以上销售经验，北京、上海、广州、深圳；医学编辑，10名，临床医学硕士或有3年以上医生或编辑经验者，上海或北京；产经编辑，2名，北京、上海。岗位均要求：1、愿意通过奋斗成长；2、工作能力突出；3、价值观良性。我们将向您提供：1、好的薪酬；2、大发展空间；3、期权+提成+职务晋升的综合激励.";
				$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
				break;
				case "yxj-tg"://投稿
				$retMsg = "如果您对医疗政策、新闻热点、临床热点、医学学术、海外医疗、医院管理、医疗产业等方面有独到见解或观点； 如果您对自己的行医生涯有话想说，想与更多医界同行分享交流； 欢迎您和我们联系，成为《医学界》杂志、微信、网站的专栏作者； 将自己的发现、观点，或是经历，与全国《医学界》的读者分享； 稿子一经录用，稿酬从优（网络稿按篇计稿酬）。 投稿信箱：yxjtougao@126.com 投完稿后，请您在微信上给我们留言。 无论投稿录用与否，我们都会给您在三个工作日内回复。 如果您发现有临床学术高价值文章、医疗医学相关的美文、值得医务工作者阅读的其他文章，请推荐给我们。请直接将文章链接通过微信发送给“医学界杂志”。";
				$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
				break;
				case "yxj-bh";//爆黑
				$retMsg = "看到了值得爆料的现象？或者有黑要打？心有不平事儿，找找《医学界》。界哥作为有志青年，喜打抱不平，以推动医疗进步、提升医者幸福为宗旨，只要确有其事儿、值得报道、合乎法规，就找找界哥吧。因为每日读者发来的消息太多，请按照这个格式发消息“爆料+名称+简要描述”，如“爆料+X医院取消二线值班+XX地区XX医院竟然取消了二线值班医生”。爆料被采纳后，您有可能获得赠送的医学界杂志。";
				$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
				break;
				case "yxj-gg";//广告
				$retMsg = "《医学界》目前拥有手机端最多的医生读者群，目前医生用户数、阅读量均位居医疗新媒体第一名。我们面向医院、学会、医药企业、医疗器械企业等各机构，在合法合规、内容可靠的前提下，提供广告合作、会议及活动协办承办、在线视频会议直播、医学视频讲座、订制专刊等多项服务。如果您有需求，请联系我们的客户部，联系人：杨小姐，手机号：18621918858。";
				$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
				break;
				case "yxj-tuodan";//广告
				$retMsg = "光棍节过厌了，不想单身了？快来参与《医学界》“我要脱单”活动，发来照片填写资料，迅速找到心仪的另一半！
";
				$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
				break;
							
			}
		break;
		case "LOCATION"://3.4打开微信对应公众账号上报地理位置事件
		//订阅号无此功能 暂无开发 服务号可以考虑 打开后立即发送本地新闻
		http_log($PostData."位置消息");
		break;
		default;
			$retMsg = "医学界微信系统正在完善中.../:,@-D";

			$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
		}
	break;
	
	case "image"://4.用户发送图片消息 暂停此功能
	$siteadd="http://newcdn.yxj.org.cn/tuodan/admin.php?openid={$fromUserName}&url={$picUrl}";
	$retMsg = "想找到自己的另一半吗？快来完善:--><a href=\"$siteadd\">个人资料（点击进入）</a>吧，真实信息有助于您更快找到身边的单身朋友哦！！";

	$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
	
	break;
	break;
	default:
	$retMsg = '你的消息未能识别...欢迎关注医学界,微信系统正在完善中.../:--b';
	$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
}
//http_log($resultStr);
//关闭连接
mssql_close($con);
echo $resultStr;
?>