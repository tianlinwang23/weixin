<?php

/**
 * PHP����strpos�������˹ؼ���
 * �ű�֮��
 */
// �ؼ��ֹ��˺���

function keyWordCheck($content,$txt){
        // ȥ���հ�
    $content = trim($content);
        // ��ȡ�ؼ����ı�
    $badword= @file_get_contents($txt);
        // ת��������
    //$arr = explode("\n", $content);
        // ֱ���ڳ����������ؼ�����
		$arr = split(',',$badword); 
         
        // �������
    for($i=0,$k=count($arr);$i<$k;$i++){
                // ���������Ԫ��Ϊ���������˴�ѭ��
        if($arr[$i]==''){
              continue;   
        }
 
                // �����⵽�ؼ��֣��򷵻�ƥ��Ĺؼ���,����ֹ����
		//echo $content."----".$arr[$i].mb_strpos($content,$arr[$i])."<br>";
		
        if(mb_strpos($content,$arr[$i])!==false){
            //$i=$k;   
            return $arr[$i];
        }   
    }
        // ���û�м�⵽�ؼ����򷵻�false   
    return false;
}
 

// ���˹ؼ���

 $content="�ؽ���";
// �ж��Ƿ���ڹؼ���
if(keyWordCheck($content,'guke.txt')){
        echo '�㷢�������ݴ��ڹؼ��֡�'.$content.'��';
}else{
        echo '��ϲ��ͨ���ؼ��ּ��';
        // ���¿��Խ���д�������ɷ���������
}

?>