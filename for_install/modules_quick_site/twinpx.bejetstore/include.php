<?
IncludeModuleLangFile(__FILE__);
class CBejetstore
{
	function ShowPanel()
	{
		if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "bejetstore")
		{
			$GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/wizards/bitrix/bejetstore/css/panel.css"); 

			$arMenu = Array(
				Array(		
					"ACTION" => "jsUtils.Redirect([], '".CUtil::JSEscape("/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardSiteID=".SITE_ID."&wizardName=bitrix:bejetstore&".bitrix_sessid_get())."')",
					"ICON" => "bx-popup-item-wizard-icon",
					"TITLE" => GetMessage("STOM_BUTTON_TITLE_W1"),
					"TEXT" => GetMessage("STOM_BUTTON_NAME_W1"),
				)
			);

			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF" => "/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardName=bitrix:bejetstore&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
				"ID" => "bejetstore_wizard",
				"ICON" => "bx-panel-site-wizard-icon",
				"MAIN_SORT" => 2500,
				"TYPE" => "BIG",
				"SORT" => 10,	
				"ALT" => GetMessage("SCOM_BUTTON_DESCRIPTION"),
				"TEXT" => GetMessage("SCOM_BUTTON_NAME"),
				"MENU" => $arMenu,
			));
		}
	}

	function BeforeIndexHandler($arFields)
    {
        // элемент инфоблока 180 (не раздел)
    	if($arFields["MODULE_ID"] == "iblock" && substr($arFields["ITEM_ID"], 0, 1) != "S"){
    		$rsCatalogIblock = CIBlock::GetList(array(),array("CODE" => "clothes"));
    		while($arCatalogIblock = $rsCatalogIblock->Fetch()){
    			$arCatalogID[] = $arCatalogIblock["ID"];
    		}
    		if(!empty($arCatalogID) && in_array($arFields["PARAM2"], $arCatalogID)){
	        	$arFields["PARAMS"]["iblock_section"] = array();
	        	//Получаем разделы привязки элемента (их может быть несколько)
	        	$rsSections = CIBlockElement::GetElementGroups($arFields["ITEM_ID"], true);
	        	while($arSection = $rsSections->Fetch())
	        	{
		         	$arFields["PARAMS"]["iblock_section"][] = $arSection["ID"];
		         	$rsParentSection = CIBlockSection::GetByID($arSection["ID"]);
					if ($arParentSection = $rsParentSection->GetNext())
					{
						$rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'), array(
							'IBLOCK_ID' => $arCatalogID,
							"<=LEFT_BORDER" => $arParentSection["LEFT_MARGIN"],
							">=RIGHT_BORDER" => $arParentSection["RIGHT_MARGIN"],
							"<DEPTH_LEVEL" => $arParentSection["DEPTH_LEVEL"]
						));
						while ($arSect = $rsSect->GetNext())
						{
							if(!in_array($arSect["ID"], $arFields["PARAMS"]["iblock_section"]))
					   			$arFields["PARAMS"]["iblock_section"][] = $arSect["ID"];
						}
					}
	         	}
	        }
        }
      	//Всегда возвращаем arFields
       	return $arFields;
    }
}
?>