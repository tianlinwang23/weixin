<?php
$data = $_GET["url"];
$logo = 'wxlogo.jpg'; // �м���logoͼ  

if(!$data) {$data='http://www.yxj.org.cn';}

// ͨ��google api����δ��logoǰ��QRͼ��Ҳ�����Լ�ʹ��RQcode������
//$size = '210x210';
//$png = 'http://chart.googleapis.com/chart?chs=' . $size . '&cht=qr&chl=' . urlencode($data) . '&chld=h|0&choe=UTF-8';
//$QR = imagecreatefrompng($png); 


//���ÿ�Դ php���PHP QR Code
include "phpqrcode.php"; 
$errorCorrectionLevel = 'H';// ������L��M��Q��H  
$matrixPointSize = 5;//��Ĵ�С��1��10 ����ͼƬ��С
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
unlink($png);//ɾ������ͼƬ
?>	
