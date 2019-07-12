<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult["ITEMS"])){
	if(CModule::IncludeModule("iblock")){
		$rsIBlockProps = CIBlockProperty::GetList(array(), array("CODE" => "COLOR_REF"));
		while($arIblockProp = $rsIBlockProps->GetNext()){
			if(!empty($arResult["ITEMS"][ $arIblockProp["ID"] ])){
				$hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList(array("filter" => array("TABLE_NAME" => $arResult["ITEMS"][ $arIblockProp["ID"] ]["USER_TYPE_SETTINGS"]["TABLE_NAME"])))->fetch();
				$entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
				$entityDataClass = $entity->getDataClass();
				$rsData = $entityDataClass::getList(array());
				while($arData = $rsData->fetch())
				{
					if(!empty($arResult["ITEMS"][ $arIblockProp["ID"] ]["VALUES"][ $arData["UF_XML_ID"] ])){
						if($arData["UF_FILE"]){
							$rsFile = CFile::GetByID($arData["UF_FILE"]);
							$arFile = $rsFile->Fetch();
							$arResult["ITEMS"][ $arIblockProp["ID"] ]["VALUES"][ $arData["UF_XML_ID"] ]["IMAGE"] = $arFile;
						}						
					}
				}
			}	
		}
	}
}

if (isset($arParams["TEMPLATE_THEME"]) && !empty($arParams["TEMPLATE_THEME"]))
{
	$arAvailableThemes = array();
	$dir = trim(preg_replace("'[\\\\/]+'", "/", dirname(__FILE__)."/themes/"));
	if (is_dir($dir) && $directory = opendir($dir))
	{
		while (($file = readdir($directory)) !== false)
		{
			if ($file != "." && $file != ".." && is_dir($dir.$file))
				$arAvailableThemes[] = $file;
		}
		closedir($directory);
	}

	if ($arParams["TEMPLATE_THEME"] == "site")
	{
		$solution = COption::GetOptionString("main", "wizard_solution", "", SITE_ID);
		if ($solution == "eshop")
		{
			$theme = COption::GetOptionString("main", "wizard_eshop_adapt_theme_id", "blue", SITE_ID);
			$arParams["TEMPLATE_THEME"] = (in_array($theme, $arAvailableThemes)) ? $theme : "blue";
		}
	}
	else
	{
		$arParams["TEMPLATE_THEME"] = (in_array($arParams["TEMPLATE_THEME"], $arAvailableThemes)) ? $arParams["TEMPLATE_THEME"] : "blue";
	}
}
else
{
	$arParams["TEMPLATE_THEME"] = "blue";
}
