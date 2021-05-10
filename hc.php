<?php
$headers = array(
		"Accept: application/json",
		"X-Auth-Client: XXXXXXXXX", // Replace with your BIG C X-Auth-Client
    	"X-Auth-Token: XXXXXXXXX", // Replace with your BIG C X-Auth-Token
		"Content-Type: application/json"
);
// $fields = array(
// 		"scope": "store/order/*",
// 		"headers": {
// 			"X-Custom-Auth-Header": "{secret_auth_password}"
// 		},
// 		"destination": "https://walterburkholder.com/elev8/worker/product.php",
// 		"is_active": true
// );

$shash = 'XXXXXXXXX'; //Replace your BIG C Hash Key
$url = "https://api.bigcommerce.com/stores/".$shash."/v2/hooks";


$curl = curl_init($url);

if($curl){
	$info = curl_getinfo($curl);
}else{
	echo curl_error($curl);
}

curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0 );
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0 );
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1 );    



$result = curl_exec($curl);
curl_close($curl);

print_r($info);
echo("<br>");
print_r($result);
echo("<br>");


?>