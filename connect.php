<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://dir-staging.surescripts.net/directory/Directory6dot1/v6_1?id=m1",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_SSLCERT => 'C:\UniServerZ\www\surescripts\certs\ss_2020.pem',
  CURLOPT_SSLCERTPASSWD => 'rivercity#2020',
  CURLOPT_FAILONERROR => 1,  
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_VERBOSE => 1,
  CURLOPT_POSTFIELDS => 'testing',
  CURLOPT_HTTPHEADER, array(
    'Content-length: 10','Content-Type: application/xml'
)
));

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-Apple-Tz: 0',
    'X-Apple-Store-Front: 143444,12'
));

$response = curl_exec($curl);
echo '<pre>';print_r(curl_getinfo($curl));
curl_close($curl);
echo curl_error($curl);
echo $response;


#Invoke-WebRequest -Uri "https://dir-staging.surescripts.net/directory/Directory6dot1/v6_1?id=m1" -Method POST -Certificate (Get-Childitem cert:\LocalMachine\My\05CA0F7FE0C3F245D1B3014AB95F06763F9F7742)