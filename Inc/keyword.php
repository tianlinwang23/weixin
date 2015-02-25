<?php

/**
 * PHP中用strpos函数过滤关键字
 * 脚本之家
 */
// 关键字过滤函数

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
 

// 过滤关键字

 $content="关节炎";
// 判断是否存在关键字
if(keyWordCheck($content,'guke.txt')){
        echo '你发布的内容存在关键字【'.$content.'】';
}else{
        echo '恭喜！通过关键字检测';
        // 往下可以进行写库操作完成发布动作。
}

?>