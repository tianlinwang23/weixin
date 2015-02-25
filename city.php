<?php
	$q=$_GET["q"];
	$upid=$q;
	


header("Content-type: text/html; charset=utf-8"); 
  function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
	{
    static $recursive_counter = 0;
    if (++$recursive_counter > 1000) {
        die('possible deep recursion attack');
    }
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            arrayRecursive($array[$key], $function, $apply_to_keys_also);
        } else {
            $array[$key] = $function($value);
        }

        if ($apply_to_keys_also && is_string($key)) {
            $new_key = $function($key);
            if ($new_key != $key) {
                $array[$new_key] = $array[$key];
                unset($array[$key]);
            }
        }
    }
    $recursive_counter--;
	}
	function JSON($array) {
	arrayRecursive($array, 'urlencode', true);
	$json = json_encode($array);
	return urldecode($json);
}
	$link = mysql_connect('localhost','newcdnyxj','193newcdnyx') or die( mysql_error());
	mysql_select_db('newcdnyxj',$link) or die('db error');
	mysql_query("SET NAMES UTF8");
	$place = array(); 
	$place[0]=array('place'=>'请选择' ,'id' =>'');
		//区分直辖市和省市
		if($q=="1"){
			$place[1]=array('place'=>'北京市' ,'id' =>'1');
		}
		elseif($q=="2"){
			$place[1]=array('place'=>'天津市' ,'id' =>'2');
		}
		elseif($q=="9"){
			$place[1]=array('place'=>'上海市' ,'id' =>'9');
		}
		elseif($q=="22"){
			$place[1]=array('place'=>'重庆市' ,'id' =>'22');
		}
		else{
			$sql = "Select * from  pre_common_district where upid=$q ";
			$result = mysql_query( $sql );
			
			 $i=1;
			while( $row=mysql_fetch_array( $result ) )
			{
				$name=$row['name'];
				
				$id=$row['id'];
				$place[$i]=array('place'=>$name ,'id' =>$id);
				$i++;
			}
	
		}
		$str=JSON($place);
		$response=$str;
		
		echo $response;
	
?>