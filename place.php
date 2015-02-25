<?php
	$q=$_GET["q"];
	$upid=$q;

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
	
		
		$str=JSON($place);
		$response=$str;
		
		echo $response;
	
?>