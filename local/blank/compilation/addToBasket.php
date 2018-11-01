<?php
if (CModule::IncludeModule("sale"))
{
	$ar_res = CCatalogProduct::GetByIDEx($pid);
	if($ar_res){
		$result =  Add2BasketByProductID($pid,$quantity,
			 array(
					array("NAME" => $ar_res['PROPERTIES']['COLOR']['NAME'], "VALUE" => $ar_res['PROPERTIES']['COLOR']['VALUE']),
					array("NAME" => $ar_res['PROPERTIES']['SIZE']['NAME'], "VALUE" => $ar_res['PROPERTIES']['SIZE']['VALUE'])
				)); 
		if($result) echo 'OK';
		else echo "Error";
	}else{
		$result =  Add2BasketByProductID($pid,$quantity); 
		echo "OK";
	}
}
?>
