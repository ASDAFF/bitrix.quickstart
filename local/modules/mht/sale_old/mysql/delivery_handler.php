<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/delivery_handler.php");

class CSaleDeliveryHandler extends CAllSaleDeliveryHandler
{
	function err_mess()
	{
		$module_id = "sale";
		@include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/install/version.php");
		return "<br>Module: ".$module_id." (".$arModuleVersion["VERSION"].")<br>Class: CSaleDeliveryHandler<br>File: ".__FILE__;
	}	
	
	function __spreadHandlerData($SID)
	{
		global $DB;
		
		$siteList = array();
		$rsSites = CSite::GetList($by="sort", $order="asc", Array());
		while($arRes = $rsSites->Fetch())
		{
			$siteList[] = $arRes["ID"];
		}

		foreach ($siteList as $SITE_ID)
		{
			$query = "INSERT INTO b_sale_delivery_handler (SELECT '','".$DB->ForSql($SITE_ID)."',ACTIVE,HID,NAME,SORT,DESCRIPTION,HANDLER,SETTINGS,PROFILES,TAX_RATE,LOGOTIP, BASE_CURRENCY FROM b_sale_delivery_handler WHERE HID='".$DB->ForSql($SID)."' AND LID='')";
			$DB->Query($query);
		}
		
		$DB->Query("DELETE FROM b_sale_delivery_handler WHERE HID='".$DB->ForSql($SID)."' AND LID=''");
		
		return;
	}
}
?>