<?php

$shash = 'XXXXXXXXX'; //Replace your BIG C Hash Key
$url = "https://api.bigcommerce.com/stores/".$shash."/v3/catalog/products";
$params = array(
          "name" => "string",
          "type" => "physical",
          "sku" => "",
          "description" => "PLEASE EDIT DESCRIPTION",
          "weight" => 0,
          "width" => 0,
          "depth" => 0,
          "height" => 0,
          "price" => 0,
          "cost_price" => 0,
          "retail_price" => 0,
          "sale_price" => 0,
          "tax_class_id" => 0,
          // "product_tax_code" => "string",
          "categories" => [
            27
          ],
          // "brand_id" => 0,
          "inventory_level" => 0,
          "inventory_warning_level" => 0,
          "inventory_tracking" => "none",
          "fixed_cost_shipping_price" => 0,
          "is_free_shipping" => true,
          // "is_visible" => true,
          // "is_featured" => true
          // "related_products" => [
          //   0
          // ],
          // "warranty" => "string",
          // "bin_picking_number" => "string",
          // "layout_file" => "string",
          // "upc" => "string",
          // "search_keywords" => "string",
          // "availability" => "available",
          // "availability_description" => "string",
          // "gift_wrapping_options_type" => "any",
          // "gift_wrapping_options_list" => [
          //   0
          // ],
          // "sort_order" => -2147483648,
          // "condition" => "New",
          // "is_condition_shown" => true,
          // "order_quantity_minimum" => 0,
          // "order_quantity_maximum" => 0,
          // "page_title" => "string",
          // "meta_keywords" => [
          //   "string"
          // ],
          // "meta_description" => "string",
          // "view_count" => 0,
          // "preorder_release_date" => "2017-03-24T23:49:18Z",
          // "preorder_message" => "string",
          // "is_preorder_only" => true,
          // "is_price_hidden" => true,
          // "price_hidden_label" => "string",
          // "custom_url" => [
          //   "url" => "string",
          //   "is_customized" => true
          //   ]
          // ,
          // "custom_fields" => [
            
          //     "name" => "string",
          //     "value" => "string"
            
          // ],
          // "bulk_pricing_rules" => [
            
          //     "quantity_min" => 0,
          //     "quantity_max" => 0,
          //     "type" => "price",
          //     "amount" => 0
            
          // ],
          // "variants" => [
            
          //     "cost_price" => 0,
          //     "price" => 0,
          //     "weight" => 0,
          //     "purchasing_disabled" => true,
          //     "purchasing_disabled_message" => "string",
          //     "image_url" => "string",
          //     "upc" => "string",
          //     "inventory_level" => 0,
          //     "inventory_warning_level" => 0,
          //     "bin_picking_number" => "string",
          //     "product_id" => 0,
          //     "sku" => "string",
          //     "option_values" => [
                
          //         "option_display_name" => "string",
          //         "label" => "string"
                
          //     ]
            
          // ]
        
);


function bcCurlRequest($url,$params){
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
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0 );
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0 );
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1 );    


    $result = curl_exec($curl);
    
    curl_close($curl);

    // $bcresult = json_decode($result, true);
    logger($result,"CREATE");
    //echo($result);
}

?>