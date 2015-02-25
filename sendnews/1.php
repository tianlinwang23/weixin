<?php
//GET https
function getUrl($URL){   
    $timeout = 60; // set to zero for no timeout
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
   // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  // this line makes it work under https
    $getData=curl_exec($ch);
    curl_close($ch);
    return $getData;
}

echo getUrl("https://www.baidu.com");
//phpinfo();
?>
