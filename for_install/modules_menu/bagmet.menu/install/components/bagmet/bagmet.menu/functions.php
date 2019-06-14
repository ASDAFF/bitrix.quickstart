<?
if (!function_exists("_GetChildMenuRecursive"))
{
	function _GetChildMenuRecursive(&$arMenu, &$arResult, $menuType, $use_ext, $menuTemplate, $currentLevel, $maxLevel, $bMultiSelect)
	{
		if ($currentLevel > $maxLevel)
			return;

		for ($menuIndex = 0, $menuCount = count($arMenu); $menuIndex < $menuCount; $menuIndex++)
		{
			//Menu from iblock (bitrix:menu.sections)
			if (is_array($arMenu[$menuIndex]["PARAMS"]) && isset($arMenu[$menuIndex]["PARAMS"]["FROM_IBLOCK"]))
			{
				$iblockSectionLevel = intval($arMenu[$menuIndex]["PARAMS"]["DEPTH_LEVEL"]);
				if ($currentLevel > 1)
					$iblockSectionLevel = $iblockSectionLevel + $currentLevel - 1;

				$arResult[] = $arMenu[$menuIndex] + Array("DEPTH_LEVEL" => $iblockSectionLevel, "IS_PARENT" => $arMenu[$menuIndex]["PARAMS"]["IS_PARENT"]);
				continue;
			}

			//Menu from files
			$subMenuExists = false;
			if ($currentLevel < $maxLevel)
			{
				//directory link only
				$bDir = false;
				if(!preg_match("'^(([a-z]+://)|mailto:|javascript:)'i", $arMenu[$menuIndex]["LINK"]))
				{
					if(substr($arMenu[$menuIndex]["LINK"], -1) == "/")
						$bDir = true;
				}
				if($bDir)
				{
					$menu = new CMenu($menuType);
					$success = $menu->Init($arMenu[$menuIndex]["LINK"], $use_ext, $menuTemplate, $onlyCurrentDir = true);
					$subMenuExists = ($success && count($menu->arMenu) > 0);

					if ($subMenuExists)
					{
						$menu->RecalcMenu($bMultiSelect);

						$arResult[] = $arMenu[$menuIndex] + Array("DEPTH_LEVEL" => $currentLevel, "IS_PARENT" => (count($menu->arMenu) > 0));
	
						if($arMenu[$menuIndex]["SELECTED"])
						{
							$arResult["menuType"] = $menuType;
							$arResult["menuDir"] = $arMenu[$menuIndex]["LINK"];
						}

						if(count($menu->arMenu) > 0)
							_GetChildMenuRecursive($menu->arMenu, $arResult, $menuType, $use_ext, $menuTemplate, $currentLevel+1, $maxLevel, $bMultiSelect);
					}
				}
			}

			if(!$subMenuExists)
				$arResult[] = $arMenu[$menuIndex] + Array("DEPTH_LEVEL" => $currentLevel, "IS_PARENT" => false);
		}
	}
}
?>