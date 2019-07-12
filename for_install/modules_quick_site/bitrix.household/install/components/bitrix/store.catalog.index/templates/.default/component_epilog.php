<?
__IncludeLang(dirname(__FILE__).'/lang/'.LANGUAGE_ID.'/template.php');
if (is_array($arResult['IDS']))
{
	foreach ($arResult['IDS'] as $ID)
	{
		$APPLICATION->SetEditArea($this->GetEditAreaId($ID), array(
			array(
				'URL' => "/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&site_id=".SITE_ID."&wizardName=bitrix:store.catalog&".bitrix_sessid_get().'&editCBlock=Y&IBLOCK_ID='.$ID,
				'TITLE' => GetMessage("STORE_EDIT_CATALOG"),
				'ICON' => 'bx-context-toolbar-edit-icon'
			)
		));
	}
}	
?>