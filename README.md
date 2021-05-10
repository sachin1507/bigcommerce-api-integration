# bigcommerce-api-integration
bigcommerce api integration

### Tech Stack:
PHP 
BIG Commerce API 
PHP Print QR Code Library


### CodeBase Description:
BIG Commerce API Integration

### Files Description:
* bccreateacart.php --> Create Cart on BIG C
* bccreateaitem.php --> Add a Product on BIG C
* bcgetorderproducts.php --> Get BIG C Cart Products 
* hc.php --> Get BIG C Hooks
* manualqr.php --> Print QR Code


### Replace your BIG C Keys

* $shash = 'XXXXXXXXX'; //Replace your BIG C Hash Key
* $headers = array(
	* "Accept: application/json",
	* "X-Auth-Client: XXXXXXXXX", // Replace with your BIG C X-Auth-Client
	* "X-Auth-Token: XXXXXXXXX", // Replace with your BIG C X-Auth-Token
	* "Content-Type: application/json"
* );