<?define("NOT_CHECK_PERMISSIONS", true);?>
<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");

if(\Bitrix\Main\Loader::IncludeModule("mlife.asz")){
	
	$orderId = false;
	
	if(isset($_REQUEST["WMI_PAYMENT_NO"]) && intval($_REQUEST["WMI_PAYMENT_NO"])>0){
		$orderId = intval($_REQUEST["WMI_PAYMENT_NO"]);
	}
	
	if(!$orderId) {
		echo 'Params error';
		die();
	}
	
	$res = \Mlife\Asz\OrderTable::getList(array("select"=>array("*","PAYN_"=>"ADDPAY.*"),"filter"=>array("ID"=>$orderId)));
	if($dataAr = $res->Fetch()){
		//echo '<pre>';print_r($dataAr);echo '</pre>';
		$cl = "\Mlife\\Asz\\Payment\\".$dataAr["PAYN_ACTIONFILE"];
		if($dataAr["PAYN_ACTIONFILE"] && class_exists($cl)){
			$cl::checkPay($dataAr);
		}else{
			echo 'payment class not found';
		}
	}else{
		echo 'Order not found';
	}
}

require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/epilog_after.php");
?>