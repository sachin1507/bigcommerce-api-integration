<?php
include('connect.php');
require('phpmailer/mailfunctions.php');
$shash = 'XXXXXXXXX'; //Replace your BIG C Hash Key

$carturl = "https://api.bigcommerce.com/stores/".$shash."/v3/carts?include=redirect_urls,line_items.physical_items.options";

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

//Products Array
$line_items = array();
foreach ($request->products as $product) {
  $push_line_items['quantity'] = $product->p_qty;
  $push_line_items['product_id'] = $product->p_productid; //12107 $product->p_productid
  $i = 0;

  //Get Variants by productid
  if($product->p_variantid > 0){    
    $variantsurl = "https://api.bigcommerce.com/stores/".$shash."/v3/catalog/products/".$product->p_productid."/variants/".$product->p_variantid; 
  } else {
    $variantsurl = "https://api.bigcommerce.com/stores/".$shash."/v3/catalog/products/".$product->p_productid."/variants";
  }

  $variants = bcCurlRequest($variantsurl, NULL, 'GET');

  if(isset($variants->data) && count($variants->data->option_values)>0){
    if(count($variants->data) == 1){
      if(isset($variants->data->option_values)){    
        $variantoptionids = array();
        foreach ($variants->data->option_values as $key => $option_values) {
        array_push($variantoptionids, $option_values->option_id);          
        $push_line_items['option_selections'][$key]['option_id'] = $option_values->option_id;
        $push_line_items['option_selections'][$key]['option_value'] = $option_values->id;
        $i++;
        }  
      } 
    } else {
      foreach ($variants->data as $key => $variant) {          
        if($variant->sku == $product->p_sku){
          if($product->p_variantid == null || $product->p_variantid ==0){
            $variant_id = $variant->id;
          }       
          if(isset($variant->option_values)){    
            $variantoptionids = array();
            foreach ($variant->option_values as $key => $option_values) {     
            array_push($variantoptionids, $option_values->option_id);          
            $push_line_items['option_selections'][$key]['option_id'] = $option_values->option_id;
            $push_line_items['option_selections'][$key]['option_value'] = $option_values->id;
            $i++;
            }  
          }       
        }      
      } 
    } 
  } else {
    // Remove option_selections from array when there is no options for product.
    unset($push_line_items['option_selections']); 
  }

  //Get Options
  $optionsurl = "https://api.bigcommerce.com/stores/".$shash."/v3/catalog/products/".$product->p_productid."/options";

  $options = bcCurlRequest($optionsurl, NULL, 'GET');
  if(isset($options->data)){    
    foreach ($options->data as $key => $option) {    
      if(isset($option->option_values)){   
        if (!in_array($option->id, $variantoptionids)) {                
          foreach ($option->option_values as $key => $option_values) {            
            if($option_values->is_default == true){ 
            $push_line_items['option_selections'][$i]['option_id'] = $option->id;
            $push_line_items['option_selections'][$i]['option_value'] = $option_values->id;
            $opt = true;
            $i++;
            }           
          }
          if($opt == false){
              $push_line_items['option_selections'][$i]['option_id'] = $option->id;
              $push_line_items['option_selections'][$i]['option_value'] = $option->option_values[0]->id; 
              $i++;
          }
        }
      }      
    }  
  } 

  //Get Modifiers
  $modifierurl = "https://api.bigcommerce.com/stores/".$shash."/v3/catalog/products/".$product->p_productid."/modifiers";

  $modifiers = bcCurlRequest($modifierurl, NULL, 'GET');
  if(isset($modifiers->data)){
    foreach ($modifiers->data as $key => $modifier) {      
      if($modifier->required == true){         
        foreach ($modifier->option_values as $key => $option_values) {
          # code...              
          if($option_values->is_default == true){            
            $push_line_items['option_selections'][$i]['option_id'] = $modifier->id;
            $push_line_items['option_selections'][$i]['option_value'] = $option_values->id;
            $mod = true;
            $i++;          
          } 
        }   
        if($mod == false){
          $push_line_items['option_selections'][$i]['option_id'] = $modifier->id;
          $push_line_items['option_selections'][$i]['option_value'] = $modifier->option_values[0]->id;  
              $i++;
        }   
      }
    }  
  }

  //If you are adding a product to the cart that has a single modifier associated with it (like a text field) try the POST to the cart API without including the "variant_id" field:
  if(!isset($modifiers->data)){  
    if(($product->p_variantid == null || $product->p_variantid == 0) && isset($variant_id)){
      $push_line_items['variant_id'] = $variant_id;
    } else {
      $push_line_items['variant_id'] = $product->p_variantid; 
    }
  }  
  
  array_push($line_items, $push_line_items);

}

if($request->id){
  $params = array(
          "customer_id" => $request->id,         
          "line_items" => $line_items
  );
} else {
  $params = array(          
          "line_items" => $line_items
  );
}


//print_r(json_encode($params));die;

$bcresult = bcCurlRequest($carturl,$params,'POST');

if(isset($bcresult->data)){         
      
      $stmt = $conn->prepare("INSERT INTO cart (cart_id, created_time, customer, url, total, jsondata) VALUES (?,?,?,?,?,?)");      
      $stmt->bind_param("ssssss", $bcresult->data->id, $bcresult->data->created_time, $bcresult->data->email, $bcresult->data->redirect_urls->cart_url, $bcresult->data->cart_amount, $postdata);     
      if($stmt->execute()){
          //echo "true";
      }else{
          //echo "false";
      }
      $stmt->close();
      $conn->close();

      echo(json_encode($bcresult, JSON_UNESCAPED_SLASHES));
      exit;
      
} else {

    //If there is some error to submit data into the BIG C then save into the Elev8 DB
    $stmt = $conn->prepare("INSERT INTO orders (orderid, customerid, jsondata) VALUES (?,?,?)"); 
    $order_id = 'WHO'.date('dmY-his');  
    $stmt->bind_param("sss", $order_id, $request->id, $postdata);
    $stmt->execute();
    $stmt->close();
    foreach ($request->products as $product) {
      $stmt = $conn->prepare("INSERT INTO order_detail (orderid, sku, qty) VALUES (?,?,?)");
      $stmt->bind_param("sss", $order_id, $product->p_sku, $product->p_qty);
      $stmt->execute();
      $stmt->close();
    }

    $bcresult->data->failed_status = "Your order has been recieved.";
    echo(json_encode($bcresult, JSON_UNESCAPED_SLASHES));

    $customer_data = getbccustomer($shash,$request->id);
    $Subject = 'Elev8 Wholesale Order Failed - Order ID : '.$order_id;
    $Content = '<b>Order ID : </b>'.$order_id.'<br/>';
    $Content .= '<b>Customer : </b>'.$customer_data->email.' ('.$customer_data->id.')'.'<br/>';
    $Content .= '<table style="border: 1px solid black;width:50%" border="1">
                  <tr>
                    <th>Sr. No.</th>
                    <th>SKU</th>
                    <th>Quantity</th>
                  </tr>';
    foreach ($request->products as $key => $product) {
    $srno =   $key+1;
    $Content .= '<tr>
                    <td>'.$srno.'</td>
                    <td>'.$product->p_sku.'</td>
                    <td>'.$product->p_qty.'</td>
                  </tr>'; 
    }
    $Content .= '</table>';    
    $ToEmail = "www.webexpert@gmail.com";
    $CcEmail = "webb.expert1@gmail.com";
    $BCcEmail = "www.webexpert@gmail.com";
    ElevSendMail($ToEmail,$CcEmail,$BCcEmail,$Subject,$Content);
    exit;  
}

function getbccustomer($shash,$bigcid){
    $url = "https://api.bigcommerce.com/stores/".$shash."/v2/customers/".$bigcid;    
    $cdata = bcCurlRequest($url, NULL, 'GET');
    if( isset($cdata) && (!is_array($cdata))){ 
        return $cdata;
    }
    return null;
}

function bcCurlRequest($url,$params, $method){
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
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);    
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0 );
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0 );
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1 );    


    $result = curl_exec($curl);
    
    curl_close($curl);    

    //echo($result);
    $bcresult = json_decode($result);
    return $bcresult;
}

// function getbigcid(){

// }

?>