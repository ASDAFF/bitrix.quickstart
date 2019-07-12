<?
IncludeModuleLangFile(__FILE__);

class CRSGrupper
{
	function HandlerOnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		foreach($aModuleMenu as $key1 => $value)
		{
			if($value["module_id"]=="catalog" || $value["items_id"]=="menu_catalog_list")
			{
				$newMenu = array(
					"text" => GetMessage("MENU_PENETRATION_TEXT"),
					"title" => GetMessage("MENU_PENETRATION_TITLE"),
					"url" => "redsign_grupper.php?lang=".LANGUAGE_ID,
					"more_url" => array(
						"redsign_grupper_edit.php",
					),
				);
				$aModuleMenu[$key1]["items"][] = $newMenu;
				break;
			}
		}
	}
	
	function HandlerOnAdminContextMenuShow(&$items)
	{
		global $APPLICATION;
		CUtil::InitJSCore( array('ajax' , 'popup' ));
		$IBLOCK_ID = IntVal($_REQUEST["IBLOCK_ID"]);
		if($APPLICATION->GetCurPage(true) == "/bitrix/admin/iblock_property_admin.php" && $IBLOCK_ID >0)
		{
			$popupLink = $APPLICATION->GetPopupLink(
				array(
					"URL" => "/bitrix/admin/redsign_grupper_popup.php?lang=".LANG."&IBLOCK_ID=".$IBLOCK_ID."&bxpublic=Y",
					"PARAMS" => array(
						"width" => 500,
						"height" => 400,
						"resizable" => true,
						"min_width" => 500,
						"min_height" => 400,
					)
				)
			);
			$items[] = array(
				"TEXT" => GetMessage("MENU_CONTEXT_NAME"),
				"ICON" => "",
				"TITLE" => GetMessage("MENU_CONTEXT_TITLE"),
				"ONCLICK" => $popupLink,
			);
		}
	}
}
?>