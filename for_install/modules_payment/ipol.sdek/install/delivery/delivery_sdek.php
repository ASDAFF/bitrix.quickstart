<?
$module_id="ipol.sdek";
CModule::IncludeModule($module_id);

// ��������� ����� CDeliverySDEK::Init � �������� ����������� �������
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id.'/classes/general/sdekdelivery.php'))
	AddEventHandler("sale", "onSaleDeliveryHandlersBuildList", array('CDeliverySDEK', 'Init')); 
?>