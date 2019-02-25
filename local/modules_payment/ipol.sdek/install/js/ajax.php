<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("ipol.sdek");

$action = $_POST['isdek_action'];
if(method_exists('sdekHelper',$action))
	sdekHelper::$action($_POST);
elseif(method_exists('sdekdriver',$action))
	sdekdriver::$action($_POST);
elseif(method_exists('CDeliverySDEK',$action))
	CDeliverySDEK::$action($_POST);
elseif(method_exists('sdekExport',$action))
	sdekExport::$action($_POST);
elseif(method_exists('sdekOption',$action))
	sdekOption::$action($_POST);
else{
	$action = $_POST['action'];
	if(method_exists('sdekHelper',$action))
		sdekHelper::$action($_POST);
	elseif(method_exists('CDeliverySDEK',$action))
		CDeliverySDEK::$action($_POST);
}
?>