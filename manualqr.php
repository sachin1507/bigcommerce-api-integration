<?php
include("phpqrcode/qrlib.php");

function createqr($id){
	$codeContents = $id;
	$tempDir = "../images/qrcodes/";

	$fileName = 'qr_'.$codeContents.'.png'; 

	$pngAbsoluteFilePath = $tempDir.$fileName; 
	$urlRelativeFilePath = $tempDir.$fileName; 
	 
	// generating 
	if (!file_exists($pngAbsoluteFilePath)) { 
	    QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 10); 
	     
	} else { 
	   
	} 
	 
	
}

for ($x = 0; $x <= 10000; $x++) {
    createqr($x); 
} 
 
?>