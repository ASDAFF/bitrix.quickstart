<?
class WizardServices
{
	function GetTemplates($relativePath,$arWizardTemplates = Array())
	{
		$absolutePath = $_SERVER["DOCUMENT_ROOT"].$relativePath;
		$absolutePath = str_replace("\\", "/", $absolutePath);

		if (!$handle  = @opendir($absolutePath))
			return $arWizardTemplates;

		while(($dirName = @readdir($handle)) !== false)
		{
			if ($dirName == "." || $dirName == ".." || !is_dir($absolutePath."/".$dirName)) 
				continue;

			$arTemplate = Array(
				"DESCRIPTION"=>"",
				"NAME" => $dirName,
			);

			if (file_exists($absolutePath."/".$dirName."/description.php"))
			{
				if (LANGUAGE_ID != "en" && LANGUAGE_ID != "ru")
				{
					if (file_exists($absolutePath."/".$dirName."/lang/en/description.php"))
						__IncludeLang($absolutePath."/".$dirName."/lang/en/description.php");
				}

				if (file_exists($absolutePath."/".$dirName."/lang/".LANGUAGE_ID."/description.php"))
						__IncludeLang($absolutePath."/".$dirName."/lang/".LANGUAGE_ID."/description.php");

				include($absolutePath."/".$dirName."/description.php");
			}

			$arTemplate["ID"] = $dirName;
			if (isset($arTemplate["SORT"]) && intval($arTemplate["SORT"]) > 0)
				$arTemplate["SORT"] = intval($arTemplate["SORT"]);
			else
				$arTemplate["SORT"] = 0;

			if (file_exists($absolutePath."/".$dirName."/screen.png"))
				$arTemplate["SCREENSHOT"] = $relativePath."/".$dirName."/screen.png";
			else
				$arTemplate["SCREENSHOT"] = false;

			if (file_exists($absolutePath."/".$dirName."/preview.png"))
				$arTemplate["PREVIEW"] = $relativePath."/".$dirName."/preview.png";
			elseif (file_exists($absolutePath."/".$dirName."/preview.gif"))
				$arTemplate["PREVIEW"] = $relativePath."/".$dirName."/preview.gif";
			else
				$arTemplate["PREVIEW"] = false;

			if (!isset($arWizardTemplates[$arTemplate["ID"]]))
				$arWizardTemplates[$arTemplate["ID"]] = $arTemplate;
			else {
				$arWizardTemplates[$arTemplate["ID"]]["SORT"] = (isset($arTemplate["SORT"]) && intval($arTemplate["SORT"]) > 0 ? intval($arTemplate["SORT"]) : $arWizardTemplates[$arTemplate["ID"]]["SORT"]);
				$arWizardTemplates[$arTemplate["ID"]]["SCREENSHOT"] = (!empty($arTemplate["SCREENSHOT"]) ? $arTemplate["SCREENSHOT"] : $arWizardTemplates[$arTemplate["ID"]]["SCREENSHOT"]);
				$arWizardTemplates[$arTemplate["ID"]]["PREVIEW"] = (!empty($arTemplate["PREVIEW"]) ? $arTemplate["PREVIEW"] : $arWizardTemplates[$arTemplate["ID"]]["PREVIEW"]);
				$arWizardTemplates[$arTemplate["ID"]]["DESCRIPTION"] = (!empty($arTemplate["DESCRIPTION"]) ? $arTemplate["DESCRIPTION"] : $arWizardTemplates[$arTemplate["ID"]]["DESCRIPTION"]);
				$arWizardTemplates[$arTemplate["ID"]]["NAME"] = ($arTemplate["NAME"]!=$arTemplate["ID"] ? $arTemplate["NAME"] : $arWizardTemplates[$arTemplate["ID"]]["NAME"]);
			}
		}


		closedir($handle);
		uasort($arWizardTemplates, create_function('$a, $b', 'return $a["SORT"] > $b["SORT"];'));
		return $arWizardTemplates;
	}

	function GetTemplatesPath($path)
	{
		$templatesPath = $path."/templates";

		if (file_exists($_SERVER["DOCUMENT_ROOT"].$templatesPath."/".LANGUAGE_ID))
			$templatesPath .= "/".LANGUAGE_ID;

		return $templatesPath;
	}

	function GetServices($wizardPath, $serviceFolder = "", $arFilter = Array())
	{
		$arServices = Array();
		
		$wizardPath = rtrim($wizardPath, "/");
		$serviceFolder = rtrim($serviceFolder, "/");
		$servicePath = $wizardPath."/".$serviceFolder;
		
		if (LANGUAGE_ID != "en" && LANGUAGE_ID != "ru")
		{
			if (file_exists($wizardPath."/lang/en".$serviceFolder."/.services.php"))
				__IncludeLang($wizardPath."/lang/en".$serviceFolder."/.services.php");
		}
		if (file_exists($wizardPath."/lang/".LANGUAGE_ID.$serviceFolder."/.services.php"))
			__IncludeLang($wizardPath."/lang/".LANGUAGE_ID.$serviceFolder."/.services.php");
		
		include($servicePath."/.services.php");
		
		if (empty($arServices))
			return $arServices;
		
		
		$servicePosition = 1;
		foreach ($arServices as $serviceID => $arService)
		{
			if (isset($arFilter["SKIP_INSTALL_ONLY"]) && array_key_exists("INSTALL_ONLY", $arService) && $arService["INSTALL_ONLY"] == $arFilter["SKIP_INSTALL_ONLY"])
			{
				unset($arServices[$serviceID]);
				continue;
			}
						
			if (isset($arFilter["SHOW_IN_FORM"]) && $arService["IN_FORM"] != $arFilter["SHOW_IN_FORM"])
			{				
				unset($arServices[$serviceID]);
				continue;
			}

			if (isset($arFilter["SERVICES"]) && is_array($arFilter["SERVICES"]) && !in_array($serviceID, $arFilter["SERVICES"]) && $arService["IN_FORM"]=="Y")
			{
				unset($arServices[$serviceID]);
				continue;
			}
			
			//Check service dependencies
			$modulesCheck = Array();
			if (array_key_exists("MODULE_ID", $arService))
				$modulesCheck = (is_array($arService["MODULE_ID"]) ? $arService["MODULE_ID"] : Array($arService["MODULE_ID"]));
			
			foreach ($modulesCheck as $moduleID)
			{
				if (!IsModuleInstalled($moduleID))
				{
					unset($arServices[$serviceID]);
					continue 2;
				}
			}
			
			$arServices[$serviceID]["POSITION"] = $servicePosition;
			$servicePosition += (isset($arService["STAGES"]) && !empty($arService["STAGES"]) ? count($arService["STAGES"]) : 1);
		}

		return $arServices;
	}

	function IncludeServiceLang($relativePath, $lang = false, $bReturnArray = false)
	{
		if($lang === false)
			$lang = LANGUAGE_ID;

		$arMessages = Array();
		if ($lang != "en" && $lang != "ru")
		{
			if (file_exists(WIZARD_SERVICE_ABSOLUTE_PATH."/lang/en/".$relativePath))
			{
				if ($bReturnArray)
					$arMessages = __IncludeLang(WIZARD_SERVICE_ABSOLUTE_PATH."/lang/en/".$relativePath, true);
				else
					__IncludeLang(WIZARD_SERVICE_ABSOLUTE_PATH."/lang/en/".$relativePath);
			}
		}

		if (file_exists(WIZARD_SERVICE_ABSOLUTE_PATH."/lang/".$lang."/".$relativePath))
		{
			if ($bReturnArray)
				$arMessages = array_merge($arMessages, __IncludeLang(WIZARD_SERVICE_ABSOLUTE_PATH."/lang/".$lang."/".$relativePath, true));
			else
				__IncludeLang(WIZARD_SERVICE_ABSOLUTE_PATH."/lang/".$lang."/".$relativePath);
		}

		return $arMessages;
	}

	function GetCurrentSiteID($selectedSiteID = null)
	{
		if (strlen($selectedSiteID) > 0)
		{
			$obSite = CSite::GetList($by = "def", $order = "desc", Array("LID" => $selectedSiteID));
			if (!$arSite = $obSite->Fetch())
				$selectedSiteID = null;
		}

		$currentSiteID = $selectedSiteID;
		if ($currentSiteID == null)
		{
			$currentSiteID = SITE_ID;
			if (defined("ADMIN_SECTION"))
			{
				$obSite = CSite::GetList($by = "def", $order = "desc", Array("ACTIVE" => "Y"));
				if ($arSite = $obSite->Fetch())
					$currentSiteID = $arSite["LID"];
			}
		}
		return $currentSiteID;
	}

	function GetThemes($relativePath,$arThemes = Array())
	{
		if (!is_dir($_SERVER["DOCUMENT_ROOT"].$relativePath))
			return $arThemes;

		$themePath = $_SERVER["DOCUMENT_ROOT"].$relativePath;
		$themePath = str_replace("\\", "/", $themePath);

		if ($handle = @opendir($themePath))
		{
			while (($file = readdir($handle)) !== false)
			{
				if ($file == "." || $file == ".." || !is_dir($themePath."/".$file))
					continue;

				$arTemplate = Array();
				if (is_file($themePath."/".$file."/description.php"))
				{
					if (LANGUAGE_ID != "en" && LANGUAGE_ID != "ru")
					{
						if (file_exists($themePath."/".$file."/lang/en/description.php"))
							__IncludeLang($themePath."/".$file."/lang/en/description.php");
					}

					if (file_exists($themePath."/".$file."/lang/".LANGUAGE_ID."/description.php"))
							__IncludeLang($themePath."/".$file."/lang/".LANGUAGE_ID."/description.php");

					@include($themePath."/".$file."/description.php");
				}
				if (!isset ($arThemes[$file])) {
					$arThemes[$file] = $arTemplate + Array(
						"ID" => $file,
						"SORT" => (isset($arTemplate["SORT"]) && intval($arTemplate["SORT"]) > 0 ? intval($arTemplate["SORT"]) : 10),
						"NAME" => (isset($arTemplate["NAME"]) ? $arTemplate["NAME"] : $file),
						"SCREENSHOT" => (file_exists($themePath."/".$file."/screen.png") ? $relativePath."/".$file."/screen.png" : false),
					);
					if (file_exists($themePath."/".$file."/preview.png"))
						$arThemes[$file]["PREVIEW"] = $relativePath."/".$file."/preview.png";
					elseif (file_exists($themePath."/".$file."/preview.gif"))
						$arThemes[$file]["PREVIEW"] = $relativePath."/".$file."/preview.gif";
					else
						$arThemes[$file]["PREVIEW"] = false;
				}
				else {
						$arThemes[$file]["SORT"] = (isset($arTemplate["SORT"]) && intval($arTemplate["SORT"]) > 0 ? intval($arTemplate["SORT"]) : $arThemes[$file]["SORT"]);
						$arThemes[$file]["NAME"] = (isset($arTemplate["NAME"]) ? $arTemplate["NAME"] : $arThemes[$file]["NAME"]);
						$arThemes[$file]["DESCRIPTION"] = (isset($arTemplate["DESCRIPTION"]) ? $arTemplate["DESCRIPTION"] : $arThemes[$file]["DESCRIPTION"]);
						if (file_exists($themePath."/".$file."/preview.png"))
							$arThemes[$file]["PREVIEW"] = $relativePath."/".$file."/preview.png";
						elseif (file_exists($themePath."/".$file."/preview.gif"))
							$arThemes[$file]["PREVIEW"] = $relativePath."/".$file."/preview.gif";
						$arThemes[$file]["SCREENSHOT"] = (file_exists($themePath."/".$file."/screen.png") ? $relativePath."/".$file."/screen.png" : $arThemes[$file]["SCREENSHOT"]);
				}

			}
			@closedir($handle);
		}

		uasort($arThemes, create_function('$a, $b', 'return strcmp($a["SORT"], $b["SORT"]);'));
		return $arThemes;
	}

	function SetFilePermission($path, $permissions)
	{
		$originalPath = $path;

		CMain::InitPathVars($site, $path);
		$documentRoot = CSite::GetSiteDocRoot($site);

		$path = rtrim($path, "/");

		if (strlen($path) <= 0)
			$path = "/";

		if( ($position = strrpos($path, "/")) !== false)
		{
			$pathFile = substr($path, $position+1);
			$pathDir = substr($path, 0, $position);
		}
		else
			return false;

		if ($pathFile == "" && $pathDir == "")
			$pathFile = "/";

		$PERM = Array();
		if(file_exists($documentRoot.$pathDir."/.access.php"))
			@include($documentRoot.$pathDir."/.access.php");

		if (!isset($PERM[$pathFile]) || !is_array($PERM[$pathFile]))
			$arPermisson = $permissions;
		else
			$arPermisson = $permissions + $PERM[$pathFile];

		return $GLOBALS["APPLICATION"]->SetFileAccessPermission($originalPath, $arPermisson);
	}

	function AddMenuItem($menuFile, $menuItem,  $siteID)
	{
		if (CModule::IncludeModule('fileman'))
		{
			$arResult = CFileMan::GetMenuArray($_SERVER["DOCUMENT_ROOT"].$menuFile);
			$arMenuItems = $arResult["aMenuLinks"];
			$menuTemplate = $arResult["sMenuTemplate"];

			$bFound = false;
			foreach($arMenuItems as $item)
				if($item[1] == $menuItem[1])
					$bFound = true;

			if(!$bFound)
			{
				$arMenuItems[] = $menuItem;
				CFileMan::SaveMenu(Array($siteID, $menuFile), $arMenuItems, $menuTemplate);
			}
		}
	}


	function ImportIBlockFromXML($xmlFile, $iblockCode, $iblockType, $siteID, $permissions = Array(),$upd=false)
	{
		if (!CModule::IncludeModule('iblock'))
			return false;

		if (!$upd) {
		$rsIBlock = CIBlock::GetList(array(), array('SITE_ID' => $siteID, 'CODE' => $iblockCode, 'TYPE' => $iblockType));
		if ($arIBlock = $rsIBlock->Fetch())
			return false;
		}

		if (!is_array($siteID))
			$siteID = Array($siteID);

		require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/iblock/classes/'.strtolower($GLOBALS['DB']->type).'/cml2.php');
		$resEr=ImportXMLFile($xmlFile, $iblockType, $siteID, $section_action = 'N', $element_action = 'N',false,false,false,true);
		$iblockID = false;
		$rsIBlock = CIBlock::GetList(array(), array('SITE_ID' => $siteID, 'CODE' => $iblockCode, 'TYPE' => $iblockType));
		if ($arIBlock = $rsIBlock->Fetch())
		{
			$iblockID = $arIBlock['ID'];

			if (empty($permissions))
				$permissions = Array(1 => 'X', 2 => 'R');

			CIBlock::SetPermission($iblockID, $permissions);
		}
		return $iblockID;
	}


	function SetIBlockFormSettings($iblockID, $settings)
	{
		global $DBType;
		require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/classes/".strtolower($DBType)."/favorites.php");

		CUserOptions::SetOption(
			"form", 
			"form_element_".$iblockID,
			$settings,
			$common = true
		);
	}

	function SetUserOption($category, $option, $settings, $common = false, $userID = false)
	{
		global $DBType;
		require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/classes/".strtolower($DBType)."/favorites.php");

		CUserOptions::SetOption(
			$category, 
			$option, 
			$settings, 
			$common, 
			$userID
		);
	}

	function CreateSectionProperty($iblockID, $fieldCode, $arFieldName = Array())
	{
		$entityID = "IBLOCK_".$iblockID."_SECTION";
		
		$dbField = CUserTypeEntity::GetList(Array(), array("ENTITY_ID" => $entityID, "FIELD_NAME" => $fieldCode));
		if ($arField = $dbField->Fetch())
			return $arField["ID"];

		$arFields = Array(
			"ENTITY_ID" => $entityID,
			"FIELD_NAME" => $fieldCode,
			"USER_TYPE_ID" => "string",
			"MULTIPLE" => "N",
			"MANDATORY" => "N",
			"EDIT_FORM_LABEL" => $arFieldName
		);

		$obUserField = new CUserTypeEntity;
		$fieldID = $obUserField->Add($arFields);
		$GLOBALS["USER_FIELD_MANAGER"]->arFieldsCache = array();
		return $fieldID;
	}
}
?>