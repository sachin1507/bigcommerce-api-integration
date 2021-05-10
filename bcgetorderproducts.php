<?php
$shash = 'XXXXXXXXX'; //Replace your BIG C Hash Key
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

//$url = "https://api.bigcommerce.com/stores/".$shash."/v2/orders?min_date_created=".$start."&max_date_created=".$today."&limit=250";
$url = "https://api.bigcommerce.com/stores/".$shash."/v2/orders/".$request->id."/products";

// Get all the products
$products = curl_hit($url);

/*$orders = array();
for($i=1; $i<=100; $i++){
$furl = $url."&page=".$i;
$fdata = curl_hit($furl);
if ( $fdata ) {  
 foreach ($fdata as $data) {
     if(isset($data->status) && ($data->status == 'Awaiting Fulfillment' || $data->status == 'Awaiting Shipment')){
     	$data->date_created = date('m/d/Y', strtotime($data->date_created));
     	$products = curl_hit($data->products->url);
     	$data->products = array();
     	foreach ($products as $key => $product) {
     		//$data->products = $product->sku;
     		array_push($data->products, $product->sku);
     		//print_r($product->sku);
     	}     	     	
     	array_push($orders, $data);
     }
 }
} else {
 break; 
}  
}*/

if(count($products) === 0): 
	echo json_encode("No Orders Found.");
else:

    //$json = $result->fetch_all(MYSQLI_ASSOC); 
    echo json_encode($products);

endif;


function curl_hit($url){
  $headers = array(
    "Accept: application/json",
    "X-Auth-Client: XXXXXXXXX", // Replace with your BIG C X-Auth-Client
    "X-Auth-Token: XXXXXXXXX", // Replace with your BIG C X-Auth-Token
    "Content-Type: application/json"
  );

$curl = curl_init($url);

if($curl){
    $info = curl_getinfo($curl);
}else{
    echo curl_error($curl);
}

curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');    
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0 );
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0 );
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1 );  

$result = curl_exec($curl);

curl_close($curl);

//echo($result);
$data = json_decode($result);

return $data;

}

?>