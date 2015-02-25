<?php

date_default_timezone_set('Asia/Shanghai'); //设置正确时区
define("TOKEN", "yxjorgcn");

$SiteUrl = "http://wx.yxj.org.cn";
$SiteName = "医学界";

$time = time();
$Y = date("Y", $time);
$M = date("m", $time);
$D = date("d", $time);


//数据库设置
$dbserver = 'localhost'; //数据库登录地址
$dbname = 'newcdnyxj'; //数据库名
$dbusername = 'newcdnyxj'; //数据库用户名
$dbpassword = '193newcdnyx'; //数据库密码


$Conf_TextMsg = "
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
<FuncFlag>0</FuncFlag>
</xml>"; //文本信息

$Conf_ImageMsg = "
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
<Image>
<MediaId><![CDATA[%s]]></MediaId>
</Image>
</xml>"; //图片消息

$Conf_NewsMsg = "
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>1</ArticleCount>
<Articles>
<item>
<Title><![CDATA[%s]]></Title> 
<Description><![CDATA[%s]]></Description>
<PicUrl><![CDATA[%s]]></PicUrl>
<Url><![CDATA[%s]]></Url>
</item>
</Articles>
</xml>"; //图文消息

$Conf_TextMedia = "
<tr id='MediaId%s'>
<td align='center'>%s</td>
<td align='center'>%s</td>
<td align='left'>%s</td>
<td align='center'></td>
</tr>"; //文本素材


class yxj
{

    public $name; //频道的名字
    public $catid; //频道的id
    public $fromUserName;
    public $toUserName;
    public $SiteUrl = "http://wx.yxj.org.cn";

    public $Conf_NewsMsg = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>1</ArticleCount>
<Articles>
<item>
<Title><![CDATA[%s]]></Title> 
<Description><![CDATA[%s]]></Description>
<PicUrl><![CDATA[%s]]></PicUrl>
<Url><![CDATA[%s]]></Url>
</item>
</Articles>
</xml>"; //图文消息
    public $Conf_TextMsg = "
    <xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[%s]]></Content>
    <FuncFlag>0</FuncFlag>
    </xml>"; //文本信息


    //定义一个构造方法
    function __construct($name, $catid, $fromUserName, $toUserName)
    {

        $this->name = $name;
        $this->catid = $catid;
        $this->fromUserName = $fromUserName;
        $this->toUserName = $toUserName;
    }


    function datesearch($content) //搜索每日资讯
    {
        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
		$length=strlen($content);
		if($length==6){
        $QueryDate = "20" . substr($content, 0, 2) . "-" . substr($content, 2, 2) . "-" .
            substr($content, 4, 2);
		}
		elseif($length==8){
		 $QueryDate = substr($content, 0, 4) . "-" . substr($content, 4, 2) . "-" .
            substr($content, 6, 2);
		}
        $Sql_S_T_N = "SELECT pre_portal_article_title.aid,pre_portal_article_title.title,pre_portal_article_title.summary,pre_portal_article_title.pic,
        date(FROM_UNIXTIME(pre_portal_article_title.dateline))as dateline,pre_portal_article_count.viewnum FROM pre_portal_article_title 
        LEFT JOIN pre_portal_article_count ON pre_portal_article_title.aid=pre_portal_article_count.aid 
        where pre_portal_article_title.catid='$catid'and DATEDIFF(date(FROM_UNIXTIME(pre_portal_article_title.dateline)),'$QueryDate')=0 ";

        $Sql_S_T_N = $Sql_S_T_N .
            "order by pre_portal_article_count.viewnum DESC limit 0,8";
        $Result_S_T_N = mysql_query($Sql_S_T_N);
        $Num_S_T_N = mysql_num_rows($Result_S_T_N);


        if ($Num_S_T_N == 1)
        {
            $Row = mysql_fetch_array($Result_S_T_N);
            $X = 1;
            echo $Row['viewnum'];
            $Title = $Row['title'];
            $Description = $Row['summary'];
            if ($Row['pic'] != "")
            {
                $PicUrl = "http://www.yxj.org.cn/data/attachment/{$Row['pic']}";
            } else
            {
                $PicUrl = "http://wx.yxj.org.cn/nophoto.jpg";
            }
            $InputTime = strtotime($Row['dateline']);
            $Url = $SiteUrl . "/WX/newsdetail-mobile.php?ID=" . $Row['aid'] . "&OID=" . $fromUserName .
                "&idx={$X}#top";
            $resultStr = sprintf($Conf_NewsMsg, $fromUserName, $toUserName, $InputTime, $Title,
                $Description, $PicUrl, $Url);
            return $resultStr;

        }
		elseif ($Num_S_T_N > 1)
        { //1.1.a.2当日有多条资讯
            //回复多图文

            $time = time();
            $X = 1;
            $resultStr = "
        		<xml>
        		<ToUserName><![CDATA[{$fromUserName}]]></ToUserName>
        		<FromUserName><![CDATA[{$toUserName}]]></FromUserName>
        		<CreateTime>{$time}</CreateTime>
        		<MsgType><![CDATA[news]]></MsgType>
        		<ArticleCount>{$Num_S_T_N}</ArticleCount>
        		<Articles>";
            while ($Row = mysql_fetch_array($Result_S_T_N))
            {
                $Title = $Row['title'];
                $Description = $Row['summary'];
                if ($Row['pic'] != "")
                {
                    $PicUrl = "http://www.yxj.org.cn/data/attachment/{$Row['pic']}";
                } else
                {
                    $PicUrl = "http://wx.yxj.org.cn/nophoto.jpg";
                }
                $InputTime = strtotime($Row['dateline']);
                $Url = $SiteUrl . "/WX/newsdetail-mobile.php?ID=" . $Row['aid'] . "&OID=" . $fromUserName .
                    "&idx={$X}#top";
                $resultStr = $resultStr . "
        		<item>
        		<Title><![CDATA[{$Title}]]></Title> 
        		<Description><![CDATA[{$Description}]]></Description>
        		<PicUrl><![CDATA[{$PicUrl}]]></PicUrl>
        		<Url><![CDATA[{$Url}]]></Url>
        		</item>";
                $X = $X + 1;
            }

            $resultStr = $resultStr . "
		</Articles>
		</xml>";
            return $resultStr;
        }
		elseif($Num_S_T_N==0) {
		 //$retMsg = "对不起，当日无资讯。";
         //$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         //return $resultStr;
		}
    }


    function keywordsearch($content) //关键字检索
    {
		
        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
		$str=trim($content);
		function msgtmp($class,$url){
		$msg="亲，想了解更多" . $class . "资讯吗？点击此处关注<a href=\"{$url}\">医学界".$class."频道</a>吧！";
		return $msg;
		}
		function contain($str,$find){
			$k=stripos($str,$find);
			return is_numeric($k);

		}
		function keyWordCheck($content,$txt){
			// 去除空白
		$content = trim($content);
			// 读取关键字文本
		$badword= @file_get_contents($txt);
			// 转换成数组
		//$arr = explode("\n", $content);
			// 直接在程序中声明关键字数
			$arr = split(',',$badword); 
			 
			// 遍历检测
		for($i=0,$k=count($arr);$i<$k;$i++){
					// 如果此数组元素为空则跳过此次循环
			if($arr[$i]==''){
				  continue;   
			}
	 
					// 如果检测到关键字，则返回匹配的关键字,并终止运行
			//echo $content."----".$arr[$i].mb_strpos($content,$arr[$i])."<br>";
			
			if(mb_strpos($content,$arr[$i])!==false){
				//$i=$k;   
				return $arr[$i];
			}   
		}
			// 如果没有检测到关键字则返回false   
		return false;
		}
		if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/guke.txt')){
		 $retMsg = "亲，想了解更多骨科资讯吗？点击此处关注<a href=\"http://mp.weixin.qq.com/s?__biz=MzA5NDIxMzMzNA==&mid=201047896&idx=1&sn=f1f04d5edbb29349ace414b3b4ac342a#rd\">医学界骨科频道</a>吧！";
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		
		elseif(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/neifenmi.txt')){
		 $retMsg = "亲，想了解更多内分泌科资讯吗？点击此处关注<a href=\"http://mp.weixin.qq.com/s?__biz=MjM5MjQyMDMxMg==&mid=200631135&idx=1&sn=86a31e0e4398a51f13f646fb198eecd9#rd\">医学界内分泌频道</a>吧！";
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		
		}
		
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/fengshi.txt')){
		 $retMsg = "亲，想了解更多风湿免疫资讯吗？点击此处关注<a href=\"http://mp.weixin.qq.com/s?__biz=MjM5MzkxODMyNA==&mid=200591122&idx=1&sn=76e383c4beda8b5ecf392ba508f4e4f8#rd\">医学界风湿免疫频道</a>吧！";
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/xinxueguan.txt')){
		 $retMsg = "亲，想了解更多心血管资讯吗？点击此处关注<a href=\"http://mp.weixin.qq.com/s?__biz=MjM5OTc3NTcxMw==&mid=201041591&idx=1&sn=36acc227e64bc7b19bbf3a224649733b#rd\">医学界心血管频道</a>吧！";
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/xiaohua.txt')){
		 $retMsg = "亲，想了解更多消化资讯吗？点击此处关注<a href=\"http://mp.weixin.qq.com/s?__biz=MzA4NjA5NzIwMw==&mid=200889526&idx=1&sn=7334f80593bcf00110e5b1a8e3257c86#rd\">医学界消化频道</a>吧！";
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/yingxiang.txt')){
		 $retMsg = "亲，想了解更多影像诊断与介入的资讯吗？点击此处关注<a href=\"http://mp.weixin.qq.com/s?__biz=MzA4NjA5NzIwMw==&mid=200889526&idx=1&sn=7334f80593bcf00110e5b1a8e3257c86#rd\">医学界影像诊断与介入频道</a>吧！";
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/ganran.txt')){
		 $retMsg = msgtmp("感染","http://mp.weixin.qq.com/s?__biz=MzA3MjI0MzYwOA==&mid=202843468&idx=1&sn=2ba26502cefcfe97f136b5c575a4c550#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/fuke.txt')){
		 $retMsg = msgtmp("妇产","http://mp.weixin.qq.com/s?__biz=MzA3NzE5MDgyMw==&mid=200723184&idx=1&sn=c67f08b7fd3b5a30029e9c9d0256d418#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/erke.txt')){
		 $retMsg = msgtmp("儿科","http://mp.weixin.qq.com/s?__biz=MzA3OTA4MjcwOA==&mid=200996575&idx=1&sn=ba5dc60aff2d6d69b08f0c565a108913#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/huxi.txt')){
		 $retMsg = msgtmp("呼吸","http://mp.weixin.qq.com/s?__biz=MzA4NDE1MTYxNw==&mid=200517884&idx=1&sn=f5e200fbe2228515a44b508f1e31fcfa#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/zhongliu.txt')){
		 $retMsg = msgtmp("肿瘤","http://mp.weixin.qq.com/s?__biz=MjM5NTc4MTMwMw==&mid=200593489&idx=1&sn=ed4c4fcadf57ebbed578f6a35778e4d1#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/shenjing.txt')){
		 $retMsg = msgtmp("神经病学","http://mp.weixin.qq.com/s?__biz=MzA3MzI2NDgxOA==&mid=200951078&idx=1&sn=0494b4b440b6438ce5bdaf07900d59a2#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/jizhen.txt')){
		 $retMsg = msgtmp("急诊与重症","http://mp.weixin.qq.com/s?__biz=MzA4NTUzMDkxMA==&mid=202777980&idx=1&sn=1ba07fe0586365aa63dbbbcf927e7376#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/jianyan.txt')){
		 $retMsg = msgtmp("检验","http://mp.weixin.qq.com/s?__biz=MjM5MTMzNzQxMA==&mid=200593070&idx=1&sn=4da14f358c7f3ad4a9a9a9627ede88b9#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/mazui.txt')){
		 $retMsg = msgtmp("麻醉","http://mp.weixin.qq.com/s?__biz=MjM5MTE3OTcxOQ==&mid=201456227&idx=1&sn=cf30ad3dc4bdfb1e70076051183e5ed1#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/kangfu.txt')){
		 $retMsg = msgtmp("康复","http://mp.weixin.qq.com/s?__biz=MzAxODAzMTg5OA==&mid=203370556&idx=1&sn=5c263e7058de3825f8b173c27d662efc#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/kangfu.txt')){
		 $retMsg = msgtmp("康复","http://mp.weixin.qq.com/s?__biz=MzAxODAzMTg5OA==&mid=203370556&idx=1&sn=5c263e7058de3825f8b173c27d662efc#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/huli.txt')){
		 $retMsg = msgtmp("护理","http://mp.weixin.qq.com/s?__biz=MzA4MjM4MDUwMw==&mid=203543840&idx=1&sn=e52b671d6714cc5ee9461d3255daa826#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/jinsheng.txt')){
		 $retMsg = msgtmp("精神","http://mp.weixin.qq.com/s?__biz=MzAwNDEyNTMzOA==&mid=203859723&idx=4&sn=8f21e8f2ba5d96c0cb93b0f84dd254f8&scene=2&from=timeline&isappinstalled=0#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/yaoxue.txt')){
		 $retMsg = msgtmp("临床药学","http://mp.weixin.qq.com/s?__biz=MzAxMTE3MjkzMg==&mid=202655192&idx=1&sn=08d23caf9c447002f68c0ca056d351b9#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		
		else{
		
        $Sql_S_T_N = "SELECT  pre_portal_article_title.aid,pre_portal_article_title.title,pre_portal_article_title.summary,pre_portal_article_title.pic,
        date(FROM_UNIXTIME(pre_portal_article_title.dateline))as dateline,pre_portal_article_count.viewnum from pre_portal_article_title LEFT JOIN pre_portal_article_count ON pre_portal_article_title.aid=pre_portal_article_count.aid  
         where pre_portal_article_title.catid='$catid' and (pre_portal_article_title.title like '%" .
            $content . "%' or pre_portal_article_title.tags like '%" . $content . "%')";

        $Sql_S_T_N = $Sql_S_T_N . "order by pre_portal_article_title.dateline DESC limit 0,8";
        $Result_S_T_N = mysql_query($Sql_S_T_N);
        $Num_S_T_N = mysql_num_rows($Result_S_T_N);
		
		
		
	
	    
        if ($Num_S_T_N == 1)
        { //1.1.b.1搜索只有一条资讯
            //回复单图文
            $Row = mysql_fetch_array($Result_S_T_N);
            $X = 1;
            $Title = $Row['title'];
            $Description = $Row['summary'];
            if ($Row['pic'] != "")
            {
                $PicUrl = "http://www.yxj.org.cn/data/attachment/{$Row['pic']}";
            } else
            {
                $PicUrl = "http://wx.yxj.org.cn/nophoto.jpg";
            }
            $InputTime = strtotime($Row['dateline']);
            $Url = $SiteUrl . "/WX/newsdetail-mobile.php?ID=" . $Row['aid'] . "&OID=" . $fromUserName .
                "&idx={$X}#top";
            $resultStr = sprintf($Conf_NewsMsg, $fromUserName, $toUserName, $InputTime, $Title,
                $Description, $PicUrl, $Url);
            return $resultStr;
        } elseif ($Num_S_T_N > 1)
        { //1.1.a.2当日有多条资讯
            //回复多图文
            $time = time();
            $X = 1;
            $resultStr = "
        		<xml>
        		<ToUserName><![CDATA[{$fromUserName}]]></ToUserName>
        		<FromUserName><![CDATA[{$toUserName}]]></FromUserName>
        		<CreateTime>{$time}</CreateTime>
        		<MsgType><![CDATA[news]]></MsgType>
        		<ArticleCount>{$Num_S_T_N}</ArticleCount>
        		<Articles>";

            while ($Row = mysql_fetch_array($Result_S_T_N))
            {

                $Title = $Row['title'];
                $Description = $Row['summary'];
                if ($Row['pic'] != "")
                {
                    $PicUrl = "http://www.yxj.org.cn/data/attachment/{$Row['pic']}";
                } else
                {
                    $PicUrl = "http://wx.yxj.org.cn/nophoto.jpg";
                }
                $InputTime = strtotime($Row['dateline']);
                $Url = $SiteUrl . "/WX/newsdetail-mobile.php?ID=" . $Row['aid'] . "&OID=" . $fromUserName .
                    "&idx={$X}#top";
                $resultStr = $resultStr . "
        		<item>
        		<Title><![CDATA[{$Title}]]></Title> 
        		<Description><![CDATA[{$Description}]]></Description>
        		<PicUrl><![CDATA[{$PicUrl}]]></PicUrl>
        		<Url><![CDATA[{$Url}]]></Url>
        		</item>";
                $X = $X + 1;
            }

            $resultStr = $resultStr . "
		</Articles>
		</xml>";
            return $resultStr;
        }
		elseif($Num_S_T_N==0) {
		 //$retMsg = "对不起，找不到相关资讯。";
         //$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         //return $resultStr;
		}
	}
    }
    
    
    
    function saveword($content) //保存用户的会话
    {
        $name = $this->name;
        $time = time();
		$fromUserName=$this->fromUserName;
        mysql_query("INSERT INTO wx_user_messages (message, date, catid,openid) 
        VALUES ('$content', '$time', '$name','$fromUserName')");

    }
    function subscribe() //新用户关注事件
    {
        $name = $this->name;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $catid = $this->catid;
        $Conf_TextMsg = $this->Conf_TextMsg;
        $NowDate = substr(date("Ymd", strtotime("-1 day")), 2, 6);
        $retMsg = "/::)这里是" . $name . "我们每年会举办500场优质的在线视频讲座，为了便于对您提供更优质的服务,希望您能完善资料:--><a href=\"{$SiteUrl}/admin.php?openid={$fromUserName}&wechat={$name}\">快速完善</a>\n回复日期（如“" .
            $NowDate . "”）,可查收到当天的全部资讯;\n回复关键词（如“医学”）,可查收到最新相关资讯。谢谢您的支持！";
        $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
        return $resultStr;
    }

    function unsubscribe() //用户取消关注事件
    {
		 $fromUserName = $this->fromUserName;
		 $Sql="SELECT * from wx_user_information where openid='$fromUserName'";
		 $Result = mysql_query($Sql);
		 $ResultNum = mysql_num_rows($Result);
		 if($ResultNum==1){
		 mysql_query("UPDATE wx_user_information set issubscribe='1' where openid='$fromUserName'");
		 }
    }
	function acceptimage($picUrl){
	 
	}

    function eventKey($eventKey)
    {
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $Conf_TextMsg = $this->Conf_TextMsg;
        switch ($eventKey)
        {
            case "yxj-jj": //医学界简介
                $retMsg = "　医学界是一家面向全国医务工作者的职业生活全媒体，服务对象是有品位、有追求、有爱心、有水平的医务工作者，是目前全国影响力最大、微信用户最多、PV最高的医疗新媒体。医学界以推动医疗产业发展、服务医疗职业生活为宗旨，让行医更幸福是我们的使命。为用户提供可靠、有用、有价值观的资讯是我们的存在方式。
				医学界传媒包括医学界系列微信账号、《医学界杂志》、医学界网站（www.yxj.org.cn）、@医学界网站微博和医学界手机报。医学界系列微信包括了两个面向医生大众的公众号和十余个面向专科领域的临床频道账号。包括：“医学界杂志”、“医学界产业报道”；学术账号包括：“医学界心血管频道”、“医学界内分泌频道”、“医学界儿科频道”、“医学界影像诊断与介入频道”、“医学界妇产科频道”、“医学界肿瘤频道”、“医学界消化频道”、“医学界呼吸频道”、“医学界骨科频道”、“医学界感染病学频道”、“医学界风湿免疫频道”、“医学界急诊与重症频道”、“医学界神经病学频道”、“医学界麻醉频道”、“医学界检验频道”等。<a href=\"http://www.yxj.org.cn/wx/topwx.html\">点击此处</a>选择您想要关注的频道。目前，医学界系列微信账号总粉丝数超过140万，日总PV超过130万；其中“医学界杂志”账号总粉丝数50万，日均PV60万+，稳居医疗新媒体排行榜首位。";
                $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
                return $resultStr;
                break;
            case "yxj-tg": //投稿
                $retMsg = "如果您对医疗政策、新闻热点、临床热点、医学学术、海外医疗、医院管理、医疗产业等方面有独到见解或观点；\n如果您对自己的行医生涯有话想说，想与更多医界同行分享交流；\n欢迎您和我们联系，成为《医学界》杂志、微信、网站的专栏作者；\n 将自己的发现、观点，或是经历，与全国《医学界》的读者分享；\n 稿子一经录用，稿酬100-300元/篇（网络稿按篇计稿酬）。 \n投稿信箱：yxjtougao@126.com \n投完稿后，请您在微信上给我们留言。 \n无论投稿录用与否，我们都会给您在三个工作日内回复。\n如果您发现有临床学术高价值文章、医疗医学相关的美文、值得医务工作者阅读的其他文章，请推荐给我们。请直接将文章链接通过微信发送给“医学界杂志”。";
                $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
                return $resultStr;
                break;
            case "yxj-xz": //写真
                $retMsg = "界哥，男（根据我们的用户群分析，医学界杂志的订户中，女性比男性多约4个百分点），今年38岁，身高171CM，体重75公斤，大嗓门，喜欢表达观点，精心钻研临床，经常学习或工作到夜里两点，生活不太规律，喜欢喝点酒，没量；喜欢运动，不多；从不抽烟，为了不毒害别人；喜欢买有品味的书，但看得不多；看电影流泪，不过不让别人看见；喜欢抨击时政，但在领导面前总是很恭顺的样子；每年会献血1次，签下了捐献遗体的志愿书，目前职称，刚刚升上副主任医师……总体来说，他是个有爱心、有追求、向往品位、努力提高水平的有志青年一枚（界哥是个虚拟品牌代言人）";
                $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
                return $resultStr;
                break;
            case "yxj-zp": //招聘
                $retMsg = "《医学界》是目前影响力最大的医疗新媒体，微信拥有近100万用户；微博影响力为行业第一名；网站位居医生类网站前三名。由于业务快速发展，我们正在招聘大量的新人加入，如果您想寻找一个有意义、能发挥个人创造力的工作，请给我们投简历：zhaopin@yxj.org.cn.我们正在招聘的岗位包括：大客户销售，10名，2年以上销售经验，北京、上海、广州、深圳；医学编辑，10名，临床医学硕士或有3年以上医生或编辑经验者，上海或北京；产经编辑，2名，北京、上海。岗位均要求：1、愿意通过奋斗成长；2、工作能力突出；3、价值观良性。我们将向您提供：1、好的薪酬；2、大发展空间；3、期权+提成+职务晋升的综合激励.";
                $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
                return $resultStr;
                break;
            case "yxj-tg": //投稿
                $retMsg = "如果您对医疗政策、新闻热点、临床热点、医学学术、海外医疗、医院管理、医疗产业等方面有独到见解或观点； 如果您对自己的行医生涯有话想说，想与更多医界同行分享交流； 欢迎您和我们联系，成为《医学界》杂志、微信、网站的专栏作者； 将自己的发现、观点，或是经历，与全国《医学界》的读者分享； 稿子一经录用，稿酬从优（网络稿按篇计稿酬）。 投稿信箱：yxjtougao@126.com 投完稿后，请您在微信上给我们留言。 无论投稿录用与否，我们都会给您在三个工作日内回复。 如果您发现有临床学术高价值文章、医疗医学相关的美文、值得医务工作者阅读的其他文章，请推荐给我们。请直接将文章链接通过微信发送给“医学界杂志”。";
                $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
                return $resultStr;
                break;
            case "yxj-bh"; //爆黑
                $retMsg = "看到了值得爆料的现象？或者有黑要打？心有不平事儿，找找《医学界》。界哥作为有志青年，喜打抱不平，以推动医疗进步、提升医者幸福为宗旨，只要确有其事儿、值得报道、合乎法规，就找找界哥吧。因为每日读者发来的消息太多，请按照这个格式发消息“爆料+名称+简要描述”，如“爆料+X医院取消二线值班+XX地区XX医院竟然取消了二线值班医生”。爆料被采纳后，您有可能获得赠送的医学界杂志。";
                $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
                return $resultStr;
                break;
            case "yxj-gg"; //广告
                $retMsg = "《医学界》目前拥有手机端最多的医生读者群，目前医生用户数、阅读量均位居医疗新媒体第一名。我们面向医院、学会、医药企业、医疗器械企业等各机构，在合法合规、内容可靠的前提下，提供广告合作、会议及活动协办承办、在线视频会议直播、医学视频讲座、订制专刊等多项服务。如果您有需求，请联系我们的客户部，联系人：杨小姐，电话：021-38821378。";
                $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
                return $resultStr;
                break;
        }
    }
}
class guke extends yxj
{
     function datesearch($content) //搜索每日资讯
    {
        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
       
      
		
		 $retMsg = "亲爱的界友，咱们废话不说先拜年，望您喜气羊羊、羊眉吐气，洋房、洋钱堆成羴（shan），掏心感谢您支持医学界！现如羊温顺的小编我也要回家过年啦！也希望界友们留更多时间陪家人，鉴于此，骨科频道将在年三十至初六放假七天！但是如果出现什么特重大的新闻和咨询，小编会光速回归哦！";
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
    }

    function keywordsearch($content) //关键字检索
    {

        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
       
      
		
		 $retMsg = "亲爱的界友，咱们废话不说先拜年，望您喜气羊羊、羊眉吐气，洋房、洋钱堆成羴（shan），掏心感谢您支持医学界！现如羊温顺的小编我也要回家过年啦！也希望界友们留更多时间陪家人，鉴于此，骨科频道将在年三十至初六放假七天！但是如果出现什么特重大的新闻和咨询，小编会光速回归哦！";
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		
    }
    
}

class neifenmi extends yxj
{


}
class fengshimianyi extends yxj{
        function datesearch($content) //搜索每日资讯
    {
        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
       
      
		
		 $retMsg = "亲爱的界友，咱们废话不说先拜年，望您喜气羊羊、羊眉吐气，洋房、洋钱堆成羴（shan），掏心感谢您支持医学界！现如羊温顺的小编我也要回家过年啦！也希望界友们留更多时间陪家人，鉴于此，风湿免疫频道将在年三十至初六放假七天！但是如果出现什么特重大的新闻和咨询，小编会光速回归哦！";
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
    }

    function keywordsearch($content) //关键字检索
    {

        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
       
      
		
		 $retMsg = "亲爱的界友，咱们废话不说先拜年，望您喜气羊羊、羊眉吐气，洋房、洋钱堆成羴（shan），掏心感谢您支持医学界！现如羊温顺的小编我也要回家过年啦！也希望界友们留更多时间陪家人，鉴于此，风湿免疫频道将在年三十至初六放假七天！但是如果出现什么特重大的新闻和咨询，小编会光速回归哦！";
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		
    }
}

class xinxueguan extends yxj{
    
	
	
	
}
class xiaohua extends yxj{
    function datesearch($content) //搜索每日资讯
    {
        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
       
      
		
		 $retMsg = "亲爱的界友，咱们废话不说先拜年，望您喜气羊羊、羊眉吐气，洋房、洋钱堆成羴（shan），掏心感谢您支持医学界！现如羊温顺的小编我也要回家过年啦！也希望界友们留更多时间陪家人，鉴于此，消化频道将在年三十至初六放假七天！但是如果出现什么特重大的新闻和咨询，小编会光速回归哦！";
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
    }

    function keywordsearch($content) //关键字检索
    {

        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
       
      
		
		 $retMsg = "亲爱的界友，咱们废话不说先拜年，望您喜气羊羊、羊眉吐气，洋房、洋钱堆成羴（shan），掏心感谢您支持医学界！现如羊温顺的小编我也要回家过年啦！也希望界友们留更多时间陪家人，鉴于此，消化频道将在年三十至初六放假七天！但是如果出现什么特重大的新闻和咨询，小编会光速回归哦！";
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		
    }
    
}
class yingxiang extends yxj{
    
    
}
class ganran extends yxj{
        function datesearch($content) //搜索每日资讯
    {
        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
       
      
		
		 $retMsg = "亲爱的界友，咱们废话不说先拜年，望您喜气羊羊、羊眉吐气，洋房、洋钱堆成羴（shan），掏心感谢您支持医学界！现如羊温顺的小编我也要回家过年啦！也希望界友们留更多时间陪家人，鉴于此，感染频道将在年三十至初六放假七天！但是如果出现什么特重大的新闻和咨询，小编会光速回归哦！";
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
    }

    function keywordsearch($content) //关键字检索
    {

        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
       
      
		
		 $retMsg = "亲爱的界友，咱们废话不说先拜年，望您喜气羊羊、羊眉吐气，洋房、洋钱堆成羴（shan），掏心感谢您支持医学界！现如羊温顺的小编我也要回家过年啦！也希望界友们留更多时间陪家人，鉴于此，感染频道将在年三十至初六放假七天！但是如果出现什么特重大的新闻和咨询，小编会光速回归哦！";
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		
    }
    
}
class fuchanke  extends yxj{
    
    
}

class erke extends yxj{
   
    
}

class huxi extends yxj{
        function datesearch($content) //搜索每日资讯
    {
        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
       
      
		
		 $retMsg = "亲爱的界友，咱们废话不说先拜年，望您喜气羊羊、羊眉吐气，洋房、洋钱堆成羴（shan），掏心感谢您支持医学界！现如羊温顺的小编我也要回家过年啦！也希望界友们留更多时间陪家人，鉴于此，呼吸频道将在年三十至初六放假七天！但是如果出现什么特重大的新闻和咨询，小编会光速回归哦！";
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
    }

    function keywordsearch($content) //关键字检索
    {

        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
       
      
		
		 $retMsg = "亲爱的界友，咱们废话不说先拜年，望您喜气羊羊、羊眉吐气，洋房、洋钱堆成羴（shan），掏心感谢您支持医学界！现如羊温顺的小编我也要回家过年啦！也希望界友们留更多时间陪家人，鉴于此，呼吸频道将在年三十至初六放假七天！但是如果出现什么特重大的新闻和咨询，小编会光速回归哦！";
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		
    }
    
}
class zhongliu extends yxj{
    
    
} 
class shenjingbing extends yxj{
        function datesearch($content) //搜索每日资讯
    {
        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
       
      
		
		 $retMsg = "亲爱的界友，咱们废话不说先拜年，望您喜气羊羊、羊眉吐气，洋房、洋钱堆成羴（shan），掏心感谢您支持医学界！现如羊温顺的小编我也要回家过年啦！也希望界友们留更多时间陪家人，鉴于此，神经频道将在年三十至初六放假七天！但是如果出现什么特重大的新闻和咨询，小编会光速回归哦！";
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
    }

    function keywordsearch($content) //关键字检索
    {

        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
       
      
		
		 $retMsg = "亲爱的界友，咱们废话不说先拜年，望您喜气羊羊、羊眉吐气，洋房、洋钱堆成羴（shan），掏心感谢您支持医学界！现如羊温顺的小编我也要回家过年啦！也希望界友们留更多时间陪家人，鉴于此，神经频道将在年三十至初六放假七天！但是如果出现什么特重大的新闻和咨询，小编会光速回归哦！";
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		
    }
    
}
class jizheng extends yxj{
    
}
class jianyan extends yxj{
        function datesearch($content) //搜索每日资讯
    {
        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
       
      
		
		 $retMsg = "亲爱的界友，咱们废话不说先拜年，望您喜气羊羊、羊眉吐气，洋房、洋钱堆成羴（shan），掏心感谢您支持医学界！现如羊温顺的小编我也要回家过年啦！也希望界友们留更多时间陪家人，鉴于此，检验频道将在年三十至初六放假七天！但是如果出现什么特重大的新闻和咨询，小编会光速回归哦！";
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
    }

    function keywordsearch($content) //关键字检索
    {

        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
       
      
		
		 $retMsg = "亲爱的界友，咱们废话不说先拜年，望您喜气羊羊、羊眉吐气，洋房、洋钱堆成羴（shan），掏心感谢您支持医学界！现如羊温顺的小编我也要回家过年啦！也希望界友们留更多时间陪家人，鉴于此，检验频道将在年三十至初六放假七天！但是如果出现什么特重大的新闻和咨询，小编会光速回归哦！";
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		
    }
}
class chanjing extends yxj{
    
}
class yxjzazhi extends yxj{



	function datesearch($content) //搜索每日资讯
    {
        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
        $length=strlen($content);
		if($length==6){
        $QueryDate = "20" . substr($content, 0, 2) . "-" . substr($content, 2, 2) . "-" .
            substr($content, 4, 2);
		}
		elseif($length==8){
		 $QueryDate = substr($content, 0, 4) . "-" . substr($content, 4, 2) . "-" .
            substr($content, 6, 2);
		}

        $Sql_S_T_N = "SELECT pre_portal_article_title.aid,pre_portal_article_title.title,pre_portal_article_title.summary,pre_portal_article_title.pic,
        date(FROM_UNIXTIME(pre_portal_article_title.dateline))as dateline,pre_portal_article_count.viewnum FROM pre_portal_article_title 
        LEFT JOIN pre_portal_article_count ON pre_portal_article_title.aid=pre_portal_article_count.aid 
        where DATEDIFF(date(FROM_UNIXTIME(pre_portal_article_title.dateline)),'$QueryDate')=0 ";

        $Sql_S_T_N = $Sql_S_T_N .
            "order by pre_portal_article_count.viewnum DESC limit 0,8";
        $Result_S_T_N = mysql_query($Sql_S_T_N);
        $Num_S_T_N = mysql_num_rows($Result_S_T_N);


        if ($Num_S_T_N == 1)
        {
            $Row = mysql_fetch_array($Result_S_T_N);
            $X = 1;
            echo $Row['viewnum'];
            $Title = $Row['title'];
            $Description = $Row['summary'];
            if ($Row['pic'] != "")
            {
                $PicUrl = "http://www.yxj.org.cn/data/attachment/{$Row['pic']}";
            } else
            {
                $PicUrl = "http://wx.yxj.org.cn/nophoto.jpg";
            }
            $InputTime = strtotime($Row['dateline']);
            $Url = $SiteUrl . "/WX/newsdetail-mobile.php?ID=" . $Row['aid'] . "&OID=" . $fromUserName .
                "&idx={$X}#top";
            $resultStr = sprintf($Conf_NewsMsg, $fromUserName, $toUserName, $InputTime, $Title,
                $Description, $PicUrl, $Url);
            return $resultStr;

        } elseif ($Num_S_T_N > 1)
        { //1.1.a.2当日有多条资讯
            //回复多图文

            $time = time();
            $X = 1;
            $resultStr = "
        		<xml>
        		<ToUserName><![CDATA[{$fromUserName}]]></ToUserName>
        		<FromUserName><![CDATA[{$toUserName}]]></FromUserName>
        		<CreateTime>{$time}</CreateTime>
        		<MsgType><![CDATA[news]]></MsgType>
        		<ArticleCount>{$Num_S_T_N}</ArticleCount>
        		<Articles>";
            while ($Row = mysql_fetch_array($Result_S_T_N))
            {
                $Title = $Row['title'];
                $Description = $Row['summary'];
                if ($Row['pic'] != "")
                {
                    $PicUrl = "http://www.yxj.org.cn/data/attachment/{$Row['pic']}";
                } else
                {
                    $PicUrl = "http://wx.yxj.org.cn/nophoto.jpg";
                }
                $InputTime = strtotime($Row['dateline']);
                $Url = $SiteUrl . "/WX/newsdetail-mobile.php?ID=" . $Row['aid'] . "&OID=" . $fromUserName .
                    "&idx={$X}#top";
                $resultStr = $resultStr . "
        		<item>
        		<Title><![CDATA[{$Title}]]></Title> 
        		<Description><![CDATA[{$Description}]]></Description>
        		<PicUrl><![CDATA[{$PicUrl}]]></PicUrl>
        		<Url><![CDATA[{$Url}]]></Url>
        		</item>";
                $X = $X + 1;
            }

            $resultStr = $resultStr . "
		</Articles>
		</xml>";
            return $resultStr;
        }
		elseif($Num_S_T_N==0) {
		 //$retMsg = "对不起，当日无资讯。";
         //$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         //return $resultStr;
		}
    }
	
	 function subscribe() //新用户关注事件
    {
        $name = $this->name;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $catid = $this->catid;
        $Conf_TextMsg = $this->Conf_TextMsg;
        $NowDate = substr(date("Ymd", strtotime("-1 day")), 2, 6);
        $retMsg ="        欢迎关注《医学界》! 面向医院内工作的医生、护士、药师、医疗管理专业人员，我们还有一大波临床学科类公众账号，<a href=\"http://www.yxj.org.cn/wx/topwx.html\">点击此处</a >选择合适的频道关注，以便获取精准的临床和职业资讯服务。

		

		2015年我们将面向全国的临床医生、护士，邀请医学名家、临床中坚医生，举办300至500场前沿、实用的临床学习类视频直播讲座，请
		<a href=\"{$SiteUrl}/admin.php?openid={$fromUserName}&wechat={$name}\">点击此处</a>注册之后，即可免费收看。";
        $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
        return $resultStr;
    }

	function acceptimage($picUrl){
	 $fromUserName = $this->fromUserName;
     $toUserName = $this->toUserName;
	 $Conf_TextMsg = $this->Conf_TextMsg;
    
     $url='http://wx.yxj.org.cn/newyear.php?openid='.$fromUserName.'&picurl='.$picUrl;
	 $retMsg = "请许下您新年愿望，也许新年有新惊喜哦~
--><a href=\"{$url}\">许愿</a>";
     
	
	 $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
     return $resultStr;
	}
	
	
    function keywordsearch($content) //关键字检索
    {
		$catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
		$str=trim($content);
		
		function contain($str,$find){
			$k=stripos($str,$find);
			return is_numeric($k);

		}
		function isfirst($openid){
		$result="select * from wx_user_member where openid='$openid'";
		$num=mysql_num_rows($result);
			if($num==0){
				return true;
			
			}
			
			else if($num!=0){
				return false;
			
			}
		}
			/*if($content="加入医学界"){
		$retMsg = "为了便于对您提供更优质的服务,希望您能完善资料:--><a href=\"{$SiteUrl}/admin.php?openid={$fromUserName}&wechat={$name}\">加入我们</a>";
		$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
        return $resultStr;
		}
		else if($content="更多频道"){
		$retMsg = "1.<a href=\"http://mp.weixin.qq.com/s?__biz=MzA5NDIxMzMzNA==&mid=201047896&idx=1&sn=f1f04d5edbb29349ace414b3b4ac342a#rd\">骨科</a> 2.<a href=\"http://mp.weixin.qq.com/s?__biz=MjM5MjQyMDMxMg==&mid=200631135&idx=1&sn=86a31e0e4398a51f13f646fb198eecd9#rd\">内分泌</a><br>
		3.<a href=\"http://mp.weixin.qq.com/s?__biz=MjM5MzkxODMyNA==&mid=200591122&idx=1&sn=76e383c4beda8b5ecf392ba508f4e4f8#rd\">风湿免疫</a> 4.<a href=\"http://mp.weixin.qq.com/s?__biz=MjM5OTc3NTcxMw==&mid=201041591&idx=1&sn=36acc227e64bc7b19bbf3a224649733b#rd\">心血管</a><br>";
		
		
		$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
        return $resultStr;		
		}
		*/
		function msgtmp($class,$url){
		$msg="亲，想了解更多" . $class . "资讯吗？点击此处关注<a href=\"{$url}\">医学界".$class."频道</a>吧！";
		return $msg;
		}
		
	
		function keyWordCheck($content,$txt){
			// 去除空白
		$content = trim($content);
			// 读取关键字文本
		$badword= @file_get_contents($txt);
			// 转换成数组
		//$arr = explode("\n", $content);
			// 直接在程序中声明关键字数
			$arr = split(',',$badword); 
			 
			// 遍历检测
		for($i=0,$k=count($arr);$i<$k;$i++){
					// 如果此数组元素为空则跳过此次循环
			if($arr[$i]==''){
				  continue;   
			}
	 
					// 如果检测到关键字，则返回匹配的关键字,并终止运行
			//echo $content."----".$arr[$i].mb_strpos($content,$arr[$i])."<br>";
			
			if(mb_strpos($content,$arr[$i])!==false){
				//$i=$k;   
				return $arr[$i];
			}   
		}
			// 如果没有检测到关键字则返回false   
		return false;
		}
		
		if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/guke.txt')){
		 $retMsg = "亲，想了解更多骨科资讯吗？点击此处关注<a href=\"http://mp.weixin.qq.com/s?__biz=MzA5NDIxMzMzNA==&mid=201047896&idx=1&sn=f1f04d5edbb29349ace414b3b4ac342a#rd\">医学界骨科频道</a>吧";
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		elseif($content=="会员"){
		$retMsg = "因为有你，让小编们越来越强烈地感受到自己的工作价值和意义。<a href=\"http://wx.yxj.org.cn/invitation/huiyuan.php\">点击这里</a>成为《医学界》会员，支持小编们“服务医生，改善医疗”！";
		 
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		
		}
		elseif(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/neifenmi.txt')){
		 $retMsg = "亲，想了解更多内分泌科资讯吗？点击此处关注<a href=\"http://mp.weixin.qq.com/s?__biz=MjM5MjQyMDMxMg==&mid=200631135&idx=1&sn=86a31e0e4398a51f13f646fb198eecd9#rd\">医学界内分泌频道</a>吧！";
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		
		}
		
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/fengshi.txt')){
		 $retMsg = "亲，想了解更多风湿免疫资讯吗？点击此处关注<a href=\"http://mp.weixin.qq.com/s?__biz=MjM5MzkxODMyNA==&mid=200591122&idx=1&sn=76e383c4beda8b5ecf392ba508f4e4f8#rd\">医学界风湿免疫频道</a>吧！";
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/xinxueguan.txt')){
		 $retMsg = "亲，想了解更多心血管资讯吗？点击此处关注<a href=\"http://mp.weixin.qq.com/s?__biz=MjM5OTc3NTcxMw==&mid=201041591&idx=1&sn=36acc227e64bc7b19bbf3a224649733b#rd\">医学界心血管频道</a>吧！";
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/xiaohua.txt')){
		 $retMsg = "亲，想了解更多消化资讯吗？点击此处关注<a href=\"http://mp.weixin.qq.com/s?__biz=MzA4NjA5NzIwMw==&mid=200889526&idx=1&sn=7334f80593bcf00110e5b1a8e3257c86#rd\">医学界消化频道</a>吧！";
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/yingxiang.txt')){
		 $retMsg = "亲，想了解更多影像诊断与介入的资讯吗？点击此处关注<a href=\"http://mp.weixin.qq.com/s?__biz=MzA4NjA5NzIwMw==&mid=200889526&idx=1&sn=7334f80593bcf00110e5b1a8e3257c86#rd\">医学界影像诊断与介入频道</a>吧！";
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/ganran.txt')){
		 $retMsg = msgtmp("感染","http://mp.weixin.qq.com/s?__biz=MzA3MjI0MzYwOA==&mid=202843468&idx=1&sn=2ba26502cefcfe97f136b5c575a4c550#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/fuke.txt')){
		 $retMsg = msgtmp("妇产","http://mp.weixin.qq.com/s?__biz=MzA3NzE5MDgyMw==&mid=200723184&idx=1&sn=c67f08b7fd3b5a30029e9c9d0256d418#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/erke.txt')){
		 $retMsg = msgtmp("儿科","http://mp.weixin.qq.com/s?__biz=MzA3OTA4MjcwOA==&mid=200996575&idx=1&sn=ba5dc60aff2d6d69b08f0c565a108913#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/huxi.txt')){
		 $retMsg = msgtmp("呼吸","http://mp.weixin.qq.com/s?__biz=MzA4NDE1MTYxNw==&mid=200517884&idx=1&sn=f5e200fbe2228515a44b508f1e31fcfa#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/zhongliu.txt')){
		 $retMsg = msgtmp("肿瘤","http://mp.weixin.qq.com/s?__biz=MjM5NTc4MTMwMw==&mid=200593489&idx=1&sn=ed4c4fcadf57ebbed578f6a35778e4d1#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/shenjing.txt')){
		 $retMsg = msgtmp("神经病学","http://mp.weixin.qq.com/s?__biz=MzA3MzI2NDgxOA==&mid=200951078&idx=1&sn=0494b4b440b6438ce5bdaf07900d59a2#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/jizhen.txt')){
		 $retMsg = msgtmp("急诊与重症","http://mp.weixin.qq.com/s?__biz=MzA4NTUzMDkxMA==&mid=202777980&idx=1&sn=1ba07fe0586365aa63dbbbcf927e7376#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/jianyan.txt')){
		 $retMsg = msgtmp("检验","http://mp.weixin.qq.com/s?__biz=MjM5MTMzNzQxMA==&mid=200593070&idx=1&sn=4da14f358c7f3ad4a9a9a9627ede88b9#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/mazui.txt')){
		 $retMsg = msgtmp("麻醉","http://mp.weixin.qq.com/s?__biz=MjM5MTE3OTcxOQ==&mid=201456227&idx=1&sn=cf30ad3dc4bdfb1e70076051183e5ed1#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/kangfu.txt')){
		 $retMsg = msgtmp("康复","http://mp.weixin.qq.com/s?__biz=MzAxODAzMTg5OA==&mid=203370556&idx=1&sn=5c263e7058de3825f8b173c27d662efc#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/kangfu.txt')){
		 $retMsg = msgtmp("康复","http://mp.weixin.qq.com/s?__biz=MzAxODAzMTg5OA==&mid=203370556&idx=1&sn=5c263e7058de3825f8b173c27d662efc#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/huli.txt')){
		 $retMsg = msgtmp("护理","http://mp.weixin.qq.com/s?__biz=MzA4MjM4MDUwMw==&mid=203543840&idx=1&sn=e52b671d6714cc5ee9461d3255daa826#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/jinsheng.txt')){
		 $retMsg = msgtmp("精神","http://mp.weixin.qq.com/s?__biz=MzAwNDEyNTMzOA==&mid=203859723&idx=4&sn=8f21e8f2ba5d96c0cb93b0f84dd254f8&scene=2&from=timeline&isappinstalled=0#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		else if(keyWordCheck($str,'http://wx.yxj.org.cn/keyword/yaoxue.txt')){
		 $retMsg = msgtmp("临床药学","http://mp.weixin.qq.com/s?__biz=MzAxMTE3MjkzMg==&mid=202655192&idx=1&sn=08d23caf9c447002f68c0ca056d351b9#rd");
		 //$retMsg=$str;
         $resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         return $resultStr;
		}
		
		else{
        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
        $Sql_S_T_N = "SELECT  pre_portal_article_title.aid,pre_portal_article_title.title,pre_portal_article_title.summary,pre_portal_article_title.pic,
        date(FROM_UNIXTIME(pre_portal_article_title.dateline))as dateline,pre_portal_article_count.viewnum from pre_portal_article_title LEFT JOIN pre_portal_article_count ON pre_portal_article_title.aid=pre_portal_article_count.aid  
         where  (pre_portal_article_title.title like '%" .
            $content . "%' or pre_portal_article_title.tags like '%" . $content . "%')";

        $Sql_S_T_N = $Sql_S_T_N . "order by pre_portal_article_title.dateline DESC limit 0,8";
        $Result_S_T_N = mysql_query($Sql_S_T_N);
        $Num_S_T_N = mysql_num_rows($Result_S_T_N);

        if ($Num_S_T_N == 1)
        { //1.1.b.1搜索只有一条资讯
            //回复单图文
            $Row = mysql_fetch_array($Result_S_T_N);
            $X = 1;
            $Title = $Row['title'];
            $Description = $Row['summary'];
            if ($Row['pic'] != "")
            {
                $PicUrl = "http://www.yxj.org.cn/data/attachment/{$Row['pic']}";
            } else
            {
                $PicUrl = "http://wx.yxj.org.cn/nophoto.jpg";
            }
            $InputTime = strtotime($Row['dateline']);
            $Url = $SiteUrl . "/WX/newsdetail-mobile.php?ID=" . $Row['aid'] . "&OID=" . $fromUserName .
                "&idx={$X}#top";
            $resultStr = sprintf($Conf_NewsMsg, $fromUserName, $toUserName, $InputTime, $Title,
                $Description, $PicUrl, $Url);
            return $resultStr;
        } 
		elseif ($Num_S_T_N > 1)
        { //1.1.a.2当日有多条资讯
            //回复多图文
            $time = time();
            $X = 1;
            $resultStr = "
        		<xml>
        		<ToUserName><![CDATA[{$fromUserName}]]></ToUserName>
        		<FromUserName><![CDATA[{$toUserName}]]></FromUserName>
        		<CreateTime>{$time}</CreateTime>
        		<MsgType><![CDATA[news]]></MsgType>
        		<ArticleCount>{$Num_S_T_N}</ArticleCount>
        		<Articles>";

            while ($Row = mysql_fetch_array($Result_S_T_N))
            {

                $Title = $Row['title'];
                $Description = $Row['summary'];
                if ($Row['pic'] != "")
                {
                    $PicUrl = "http://www.yxj.org.cn/data/attachment/{$Row['pic']}";
                } else
                {
                    $PicUrl = "http://wx.yxj.org.cn/nophoto.jpg";
                }
                $InputTime = strtotime($Row['dateline']);
                $Url = $SiteUrl . "/WX/newsdetail-mobile.php?ID=" . $Row['aid'] . "&OID=" . $fromUserName .
                    "&idx={$X}#top";
                $resultStr = $resultStr . "
        		<item>
        		<Title><![CDATA[{$Title}]]></Title> 
        		<Description><![CDATA[{$Description}]]></Description>
        		<PicUrl><![CDATA[{$PicUrl}]]></PicUrl>
        		<Url><![CDATA[{$Url}]]></Url>
        		</item>";
                $X = $X + 1;
            }

            $resultStr = $resultStr . "
		</Articles>
		</xml>";
            return $resultStr;
        }
		elseif($Num_S_T_N==0) {
		 //$retMsg = "对不起，找不到相关资讯。";
         //$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         //return $resultStr;
		}
    }
    }
}

class yxjfuwuhao extends yxj{

	 function datesearch($content) //搜索每日资讯
    {
        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
        $length=strlen($content);
		if($length==6){
        $QueryDate = "20" . substr($content, 0, 2) . "-" . substr($content, 2, 2) . "-" .
            substr($content, 4, 2);
		}
		elseif($length==8){
		 $QueryDate = substr($content, 0, 4) . "-" . substr($content, 4, 2) . "-" .
            substr($content, 6, 2);
		}

        $Sql_S_T_N = "SELECT pre_portal_article_title.aid,pre_portal_article_title.title,pre_portal_article_title.summary,pre_portal_article_title.pic,
        date(FROM_UNIXTIME(pre_portal_article_title.dateline))as dateline,pre_portal_article_count.viewnum FROM pre_portal_article_title 
        LEFT JOIN pre_portal_article_count ON pre_portal_article_title.aid=pre_portal_article_count.aid 
        where  DATEDIFF(date(FROM_UNIXTIME(pre_portal_article_title.dateline)),'$QueryDate')=0 ";

        $Sql_S_T_N = $Sql_S_T_N .
            "order by pre_portal_article_count.viewnum DESC limit 0,8";
        $Result_S_T_N = mysql_query($Sql_S_T_N);
        $Num_S_T_N = mysql_num_rows($Result_S_T_N);


        if ($Num_S_T_N == 1)
        {
            $Row = mysql_fetch_array($Result_S_T_N);
            $X = 1;
            echo $Row['viewnum'];
            $Title = $Row['title'];
            $Description = $Row['summary'];
            if ($Row['pic'] != "")
            {
                $PicUrl = "http://www.yxj.org.cn/data/attachment/{$Row['pic']}";
            } else
            {
                $PicUrl = "http://wx.yxj.org.cn/nophoto.jpg";
            }
            $InputTime = strtotime($Row['dateline']);
            $Url = $SiteUrl . "/WX/newsdetail-mobile.php?ID=" . $Row['aid'] . "&OID=" . $fromUserName .
                "&idx={$X}#top";
            $resultStr = sprintf($Conf_NewsMsg, $fromUserName, $toUserName, $InputTime, $Title,
                $Description, $PicUrl, $Url);
            return $resultStr;

        } elseif ($Num_S_T_N > 1)
        { //1.1.a.2当日有多条资讯
            //回复多图文

            $time = time();
            $X = 1;
            $resultStr = "
        		<xml>
        		<ToUserName><![CDATA[{$fromUserName}]]></ToUserName>
        		<FromUserName><![CDATA[{$toUserName}]]></FromUserName>
        		<CreateTime>{$time}</CreateTime>
        		<MsgType><![CDATA[news]]></MsgType>
        		<ArticleCount>{$Num_S_T_N}</ArticleCount>
        		<Articles>";
            while ($Row = mysql_fetch_array($Result_S_T_N))
            {
                $Title = $Row['title'];
                $Description = $Row['summary'];
                if ($Row['pic'] != "")
                {
                    $PicUrl = "http://www.yxj.org.cn/data/attachment/{$Row['pic']}";
                } else
                {
                    $PicUrl = "http://wx.yxj.org.cn/nophoto.jpg";
                }
                $InputTime = strtotime($Row['dateline']);
                $Url = $SiteUrl . "/WX/newsdetail-mobile.php?ID=" . $Row['aid'] . "&OID=" . $fromUserName .
                    "&idx={$X}#top";
                $resultStr = $resultStr . "
        		<item>
        		<Title><![CDATA[{$Title}]]></Title> 
        		<Description><![CDATA[{$Description}]]></Description>
        		<PicUrl><![CDATA[{$PicUrl}]]></PicUrl>
        		<Url><![CDATA[{$Url}]]></Url>
        		</item>";
                $X = $X + 1;
            }

            $resultStr = $resultStr . "
		</Articles>
		</xml>";
            return $resultStr;
        }
		elseif($Num_S_T_N==0) {
		 //$retMsg = "对不起，当日无资讯。";
         //$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         //return $resultStr;
		}
    }

    function keywordsearch($content) //关键字检索
    {

        $catid = $this->catid;
        $fromUserName = $this->fromUserName;
        $toUserName = $this->toUserName;
        $SiteUrl = $this->SiteUrl;
        $Conf_NewsMsg = $this->Conf_NewsMsg;
		$Conf_TextMsg=$this->Conf_TextMsg;
        $Sql_S_T_N = "SELECT  pre_portal_article_title.aid,pre_portal_article_title.title,pre_portal_article_title.summary,pre_portal_article_title.pic,
        date(FROM_UNIXTIME(pre_portal_article_title.dateline))as dateline,pre_portal_article_count.viewnum from pre_portal_article_title LEFT JOIN pre_portal_article_count ON pre_portal_article_title.aid=pre_portal_article_count.aid  
         where (pre_portal_article_title.title like '%" .
            $content . "%' or pre_portal_article_title.tags like '%" . $content . "%')";

        $Sql_S_T_N = $Sql_S_T_N . "order by pre_portal_article_title.dateline DESC limit 0,8";
        $Result_S_T_N = mysql_query($Sql_S_T_N);
        $Num_S_T_N = mysql_num_rows($Result_S_T_N);

        if ($Num_S_T_N == 1)
        { //1.1.b.1搜索只有一条资讯
            //回复单图文
            $Row = mysql_fetch_array($Result_S_T_N);
            $X = 1;
            $Title = $Row['title'];
            $Description = $Row['summary'];
            if ($Row['pic'] != "")
            {
                $PicUrl = "http://www.yxj.org.cn/data/attachment/{$Row['pic']}";
            } else
            {
                $PicUrl = "http://wx.yxj.org.cn/nophoto.jpg";
            }
            $InputTime = strtotime($Row['dateline']);
            $Url = $SiteUrl . "/WX/newsdetail-mobile.php?ID=" . $Row['aid'] . "&OID=" . $fromUserName .
                "&idx={$X}#top";
            $resultStr = sprintf($Conf_NewsMsg, $fromUserName, $toUserName, $InputTime, $Title,
                $Description, $PicUrl, $Url);
            return $resultStr;
        } elseif ($Num_S_T_N > 1)
        { //1.1.a.2当日有多条资讯
            //回复多图文
            $time = time();
            $X = 1;
            $resultStr = "
        		<xml>
        		<ToUserName><![CDATA[{$fromUserName}]]></ToUserName>
        		<FromUserName><![CDATA[{$toUserName}]]></FromUserName>
        		<CreateTime>{$time}</CreateTime>
        		<MsgType><![CDATA[news]]></MsgType>
        		<ArticleCount>{$Num_S_T_N}</ArticleCount>
        		<Articles>";

            while ($Row = mysql_fetch_array($Result_S_T_N))
            {

                $Title = $Row['title'];
                $Description = $Row['summary'];
                if ($Row['pic'] != "")
                {
                    $PicUrl = "http://www.yxj.org.cn/data/attachment/{$Row['pic']}";
                } else
                {
                    $PicUrl = "http://wx.yxj.org.cn/nophoto.jpg";
                }
                $InputTime = strtotime($Row['dateline']);
                $Url = $SiteUrl . "/WX/newsdetail-mobile.php?ID=" . $Row['aid'] . "&OID=" . $fromUserName .
                    "&idx={$X}#top";
                $resultStr = $resultStr . "
        		<item>
        		<Title><![CDATA[{$Title}]]></Title> 
        		<Description><![CDATA[{$Description}]]></Description>
        		<PicUrl><![CDATA[{$PicUrl}]]></PicUrl>
        		<Url><![CDATA[{$Url}]]></Url>
        		</item>";
                $X = $X + 1;
            }

            $resultStr = $resultStr . "
		</Articles>
		</xml>";
            return $resultStr;
        }
		elseif($Num_S_T_N==0) {
		 //$retMsg = "对不起，找不到相关资讯。";
         //$resultStr = sprintf($Conf_TextMsg, $fromUserName, $toUserName, time(), $retMsg);
         //return $resultStr;
		}
    }
}


function classify($toUserName,$fromUserName){
switch ($toUserName)
{
case "gh_2f3ddfbaf9a4":
  $p1 = new guke("医学界骨科频道", "6",$fromUserName ,$toUserName );
  return $p1;
  break;
case "gh_d4d0d2298e86":
  $p1 = new neifenmi("医学界内分泌频道", "10",$fromUserName ,$toUserName );
  return $p1;
  break;
case "gh_17f021ebef47":
  $p1 = new fengshimianyi("医学界风湿免疫频道", "13",$fromUserName ,$toUserName );
  return $p1;
  break;
case "gh_26c1695b3346":
  $p1 = new xinxueguan("医学界心血管频道", "8",$fromUserName ,$toUserName );
  return $p1;
  break;
case "gh_a7f10f2d818c":
  $p1 = new xiaohua("医学界消化频道", "5",$fromUserName ,$toUserName );
  return $p1;
  break;
case "gh_3878b4baa31b":
  $p1 = new yingxiang("医学界影像诊断与介入频道", "15",$fromUserName ,$toUserName );
  return $p1;
  break;
case "gh_2b6cbdbe2234":
  $p1 = new ganran("医学界感染频道", "12",$fromUserName ,$toUserName );
  return $p1;
  break;
case "gh_eda24d89ae50":
  $p1 = new fuchanke("医学界妇产科频道", "11",$fromUserName ,$toUserName );
  return $p1;
  break;
case "gh_f370ef82dd1f":
  $p1 = new erke("医学界儿科频道", "14",$fromUserName ,$toUserName );
  return $p1;
  break;  
case "gh_a6e743c2ef55":
  $p1 = new huxi("医学界呼吸频道", "7",$fromUserName ,$toUserName );
  return $p1;
  break;  
case "gh_8d13966516fc":
  $p1 = new zhongliu("医学界肿瘤频道", "9",$fromUserName ,$toUserName );
  return $p1;
  break;  
case "gh_5d31c121a270":
  $p1 = new shenjingbing("医学界神经病学频道", "16",$fromUserName ,$toUserName );
  return $p1;
  break;  
case "gh_ddb5867a302a":
  $p1 = new jizheng("医学界急诊与重症频道", "18",$fromUserName ,$toUserName );
  return $p1;
  break;  
case "gh_114d5b84e67c":
  $p1 = new jianyan("医学界检验频道", "19",$fromUserName ,$toUserName );
  return $p1;
  break;  
  
case "gh_20727b5ef868":
  $p1 = new chanjing("医学界产业报道", "2",$fromUserName ,$toUserName );
  return $p1;
  break;         
case "gh_9352cb95ad49":
  $p1 = new yxjzazhi("医学界杂志", "2",$fromUserName ,$toUserName );
  return $p1;
  break;   
  
case "gh_c1c4ee007b6b":
  $p1 = new yxjfuwuhao("医学界服务号", "2",$fromUserName ,$toUserName );
  return $p1;
  break;

  
}


}
?>