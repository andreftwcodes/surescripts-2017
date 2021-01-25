<?php

$curl = curl_init();
$strCipherList = 'ECDHE-RSA-AES256-GCM-SHA384, ECDHE-RSA-AES256-SHA384,ECDHE-RSA-AES256-CBC-SHA,AES256-GCM-SHA384,AES256-SHA256,AES256-SHA,ECDHE-RSA-AES128-GCM-SHA256,ECDHE-RSA-AES128-SHA256,ECDHE-RSA-AES128-CBC-SHA,AES128-GCM-SHA256,AES128-SHA256,AES128-SHA';
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://switch.surescripts.net/checktls",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => 1,
  CURLOPT_SSLVERSION => 'CURL_SSLVERSION_TLSv1_2',
  CURLOPT_SSL_CIPHER_LIST => $strCipherList
));


curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		
$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
die;