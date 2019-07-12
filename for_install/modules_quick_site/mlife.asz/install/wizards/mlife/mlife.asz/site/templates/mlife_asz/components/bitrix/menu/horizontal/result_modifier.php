<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (empty($arResult))
	return;

if (isset($arParams["MENU_THEME"]) && !empty($arParams["MENU_THEME"]))
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

	if ($arParams["MENU_THEME"] == "site")
	{
		$solution = COption::GetOptionString("main", "wizard_solution", "", SITE_ID);
		if ($solution == "eshop")
		{
			$theme = COption::GetOptionString("main", "wizard_eshop_adapt_theme_id", "blue", SITE_ID);
			$arParams["MENU_THEME"] = (in_array($theme, $arAvailableThemes)) ? $theme : "blue";
		}
	}
	else
	{
		$arParams["MENU_THEME"] = (in_array($arParams["MENU_THEME"], $arAvailableThemes)) ? $arParams["MENU_THEME"] : "blue";
	}
}
else
{
	$arParams["MENU_THEME"] = "blue";
}

$arMenuItemsIDs = array();
$arAllItems = array();
foreach($arResult as $arItem)
{
	$arItem["PARAMS"]["item_id"] = crc32($arItem["LINK"]);

	if ($arItem["DEPTH_LEVEL"] == "1")
	{
		$arMenuItemsIDs[$arItem["PARAMS"]["item_id"]] = array();
		if ($arItem["IS_PARENT"])
		{
			$curItemLevel_1 = $arItem["PARAMS"]["item_id"];
		}
		$arAllItems[$arItem["PARAMS"]["item_id"]] = $arItem;
	}
	elseif($arItem["DEPTH_LEVEL"] == "2")
	{
		$arMenuItemsIDs[$curItemLevel_1][$arItem["PARAMS"]["item_id"]] = array();
		if ($arItem["IS_PARENT"])
		{
			$curItemLevel_2 = $arItem["PARAMS"]["item_id"];
		}
		$arAllItems[$arItem["PARAMS"]["item_id"]] = $arItem;
	}
	elseif($arItem["DEPTH_LEVEL"] == "3")
	{
		$arMenuItemsIDs[$curItemLevel_1][$curItemLevel_2][] = $arItem["PARAMS"]["item_id"];
		$arAllItems[$arItem["PARAMS"]["item_id"]] = $arItem;
	}
}

$arMenuStructure = array();
foreach ($arMenuItemsIDs as $itemIdLevel_1=>$arLevels2)
{
	$countItemsInRow = 3;
	$arMenuStructure[$itemIdLevel_1] = array();
	$countLevels2 = count($arLevels2);

	if ($countLevels2 > 0)
	{
		for ($i=1; $i<=3; $i++)
		{
			$sumElementsInRow = 0;
			foreach($arLevels2 as $itemIdLevel_2=>$arLevels3)
			{
				$sumElementsInRow+= count($arLevels3) + 1;
				$arMenuStructure[$itemIdLevel_1][$i][$itemIdLevel_2] = $arLevels3;
				if ($sumElementsInRow > $countItemsInRow)
					$countItemsInRow = $sumElementsInRow;

				unset($arLevels2[$itemIdLevel_2]);
				$tmpCount = 0;
				foreach($arLevels2 as $tmpItemIdLevel_2=>$arTmpLevels3)
				{
					$tmpCount+= 1 + count($arTmpLevels3);
				}

				if ($tmpCount <= $countItemsInRow*(3-$i) && $countItemsInRow<=$sumElementsInRow)
					break;
			}
		}
	}
}

$arResult = array();
$arResult["ALL_ITEMS"] = $arAllItems;
$arResult["ALL_ITEMS_ID"] = $arMenuItemsIDs;
$arResult["MENU_STRUCTURE"] = $arMenuStructure;
?>

