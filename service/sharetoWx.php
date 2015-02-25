<?php
$data = $_GET["url"];
$logo = 'wxlogo.jpg'; // 中间那logo图  

if(!$data) {$data='http://www.yxj.org.cn';}

// 通过google api生成未加logo前的QR图，也可以自己使用RQcode类生成
//$size = '210x210';
//$png = 'http://chart.googleapis.com/chart?chs=' . $size . '&cht=qr&chl=' . urlencode($data) . '&chld=h|0&choe=UTF-8';
//$QR = imagecreatefrompng($png); 


//调用开源 php类库PHP QR Code
include "phpqrcode.php"; 
$errorCorrectionLevel = 'H';// 纠错级别：L、M、Q、H  
$matrixPointSize = 5;//点的大小：1到10 生成图片大小
$margin = 2;
$time=time();
$png = $time.rand().".png";
QRcode::png($data, $png, $errorCorrectionLevel, $matrixPointSize, $margin); 
$QR = imagecreatefromstring(file_get_contents($png));  
 
if($logo !== FALSE)  
{  
    $QR_width = imagesx($QR);
    $QR_height = imagesy($QR);  
    $logo = imagecreatefromstring(file_get_contents($logo));  
    $logo_width = imagesx($logo);  
    $logo_height = imagesy($logo);  
    $logo_qr_width = $QR_width / 5;  
    $scale = $logo_width / $logo_qr_width;  
    $logo_qr_height = $logo_height / $scale;  
    $from_width = ($QR_width - $logo_qr_width) / 2;
    imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);  
}  
header('Content-type: image/png');  
imagepng($QR);  
imagedestroy($QR);
unlink($png);//删除生成图片
?>	
