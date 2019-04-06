<?
$module_id="ipol.sdek";
CModule::IncludeModule($module_id);

// установим метод CDeliverySDEK::Init в качестве обработчика события
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id.'/classes/general/sdekdelivery.php'))
	AddEventHandler("sale", "onSaleDeliveryHandlersBuildList", array('CDeliverySDEK', 'Init')); 
?>