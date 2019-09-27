<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$optionNameUserMenu = "user_menu_items_".SITE_ID;

if(!function_exists("CheckMenuAdminItems"))
{
	function CheckMenuAdminItems(&$adminFavoriteOption, $arMenuItemsId)
	{
		if (is_array($adminFavoriteOption) && !empty($adminFavoriteOption))
		{
			$isChanged = false;
			foreach($adminFavoriteOption as $itemID)
			{
				if (!in_array($itemID, $arMenuItemsId))
				{
					$key = array_search($itemID, $adminFavoriteOption);
					unset($adminFavoriteOption[$key]);
					$isChanged = true;
				}
			}
			if ($isChanged && $GLOBALS['USER']->CanDoOperation('bitrix24_config'))
			{
				COption::SetOptionString("bitrix24", "admin_menu_items", serialize($adminFavoriteOption), false, SITE_ID);
			}
		}
	}
}
if(!function_exists("CheckMenuUserItems"))
{
	function CheckMenuUserItems(&$userMenuOption, $arMenuItemsId)
	{
		if (is_array($userMenuOption) && !empty($userMenuOption))
		{
			$isChanged = false;
			foreach($userMenuOption as $title => $arIDs)
			{
				if (is_array($arIDs["show"]))
				{
					foreach($arIDs["show"] as $key=>$id)
					{
						if (!in_array($id, $arMenuItemsId))
						{
							$key = array_search($id, $userMenuOption[$title]["show"]);
							unset($userMenuOption[$title]["show"][$key]);
							$isChanged = true;
						}
					}
				}
				if (is_array($arIDs["hide"]))
				{
					foreach($arIDs["hide"] as $key=>$id)
					{
						if (!in_array($id, $arMenuItemsId) || is_array($arIDs["show"]) && in_array($id, $arIDs["show"]))
						{
							$key = array_search($id, $userMenuOption[$title]["hide"]);
							unset($userMenuOption[$title]["hide"][$key]);
							$isChanged = true;
						}
					}
				}
			}
			if ($isChanged)
			{
				CUserOptions::SetOption("bitrix24", "user_menu_items_".SITE_ID, $userMenuOption);
			}
		}
	}
}
if(!function_exists("CheckFavouriteItemsUserAdded"))
{
	function CheckFavouriteItemsUserAdded(&$favoriteItemsUserAdded, $userMenuOption, $arMenuItemsId)
	{
		if (is_array($favoriteItemsUserAdded))
		{
			$isChanged = false;
			foreach($favoriteItemsUserAdded as $key=>$itemID)
			{
				if (!in_array($itemID, $arMenuItemsId)
					|| is_array($userMenuOption["menu-favorites"])
					&& (!is_array($userMenuOption["menu-favorites"]["show"]) || !in_array($itemID, $userMenuOption["menu-favorites"]["show"]))
					&& (!is_array($userMenuOption["menu-favorites"]["hide"]) || !in_array($itemID, $userMenuOption["menu-favorites"]["hide"]))
				)
				{
					unset($favoriteItemsUserAdded[$key]);
					$isChanged = true;
				}
			}
			if ($isChanged)
			{
				CUserOptions::SetOption("bitrix24", "user_added_favorite_items_".SITE_ID, $favoriteItemsUserAdded);
			}
		}
	}
}

$arResultItems = $arResult;
$arResult = array();

//admin option (items were added to favorite by admin)
$adminFavoriteOption = COption::GetOptionString("bitrix24", "admin_menu_items");
if ($adminFavoriteOption)
	$adminFavoriteOption = unserialize($adminFavoriteOption);

//user menu option
$userMenuOption = CUserOptions::GetOption("bitrix24", $optionNameUserMenu);

//user added items to favorite (not admin)
$favoriteItemsUserAdded = CUserOptions::GetOption("bitrix24", "user_added_favorite_items_".SITE_ID);

$isFavouriteBlock = false;
$arFavouriteDefault = array();
$allTitleItemsID = array();
foreach($arResultItems as $index => $arItem)
{
	if (IsModuleInstalled("bitrix24")) :
		if (isset($arItem["PARAMS"]["class"]))
		{
			$arItem["DEPTH_LEVEL"] = 1;
			$arItem["IS_PARENT"]= true;
		}
		else
		{
			$arItem["DEPTH_LEVEL"] = 2;
		}
	endif;

	$arItem["PARAMS"]["can_delete_from_favourite"] = "Y";

//id to item
	if ($arItem["DEPTH_LEVEL"] == 2 && !$arItem["PARAMS"]["menu_item_id"])
	{
		$arItem["PARAMS"]["menu_item_id"] = ($arItem["PARAMS"]["name"] == "live_feed") ? "menu_live_feed" : crc32($arItem["LINK"]);
	}
	elseif ($arItem["DEPTH_LEVEL"] == 1 && !$arItem["PARAMS"]["menu_item_id"])
	{
		if (isset($arItem["PARAMS"]["class"]))
		{
			$arItem["PARAMS"]["menu_item_id"] = (!in_array($arItem["PARAMS"]["class"], $allTitleItemsID)) ? $arItem["PARAMS"]["class"] : $arItem["PARAMS"]["class"]."_".crc32($arItem["LINK"]);
			$allTitleItemsID[] = $arItem["PARAMS"]["menu_item_id"];
		}
		else
			$arItem["PARAMS"]["menu_item_id"] = "title_".crc32($arItem["LINK"]);
	}
//--id to item

//find default favorite items
	if ($isFavouriteBlock && $arItem["DEPTH_LEVEL"] == 1)
		$isFavouriteBlock = false;

	if ($isFavouriteBlock && $arItem["DEPTH_LEVEL"] == 2)
	{
		$arFavouriteDefault[] = $arItem["PARAMS"]["menu_item_id"];
		$arItem["PARAMS"]["can_delete_from_favourite"] = "N";
	}

	if ($arItem["PARAMS"]["class"]=="menu-favorites")
		$isFavouriteBlock = true;
//--find default favorite items

	$arResultItems[$index] = $arItem;
}


//prepare two-dimensional array of items and array of titles
$arResultItemsNew = array();
$arResultTitleItemsNew = array();
$arFlatItemsList = array();
$arMenuItemsId = array();
foreach($arResultItems as $index => $arItem)
{
	if ($arItem["DEPTH_LEVEL"] == 1)
	{
		$arResultTitleItemsNew[$arItem["PARAMS"]["menu_item_id"]] = $arItem;
		$arResultItemsNew[$arItem["PARAMS"]["menu_item_id"]] = array();
		$currentTitle = $arItem["PARAMS"]["menu_item_id"];
	}
	elseif($arItem["DEPTH_LEVEL"] == 2)
	{
		$arResultItemsNew[$currentTitle][$arItem["PARAMS"]["menu_item_id"]] = $arItem;
		$arFlatItemsList[$arItem["PARAMS"]["menu_item_id"]] = $arItem;
		$arMenuItemsId[] = $arItem["PARAMS"]["menu_item_id"];
	}
}
//-- prepare two-dimensional array of items and array of titles

CheckMenuAdminItems($adminFavoriteOption, $arMenuItemsId);//check admin items  for the existence
CheckMenuUserItems($userMenuOption, $arMenuItemsId);  //check user items  for the existence
CheckFavouriteItemsUserAdded($favoriteItemsUserAdded, $userMenuOption, $arMenuItemsId);
if (!is_array($adminFavoriteOption))
	$adminFavoriteOption = array();
if (!is_array($favoriteItemsUserAdded))
	$favoriteItemsUserAdded = array();

//add admin items to default favorite, status
if (is_array($adminFavoriteOption) && !empty($adminFavoriteOption))
{
	foreach($arResultItems as $index => $arItem)
	{
		if ($arItem["DEPTH_LEVEL"] == 1)
		{
			$currentTitle = $arItem["PARAMS"]["menu_item_id"];
		}
		elseif($arItem["DEPTH_LEVEL"] == 2)
		{
			if (in_array($arItem["PARAMS"]["menu_item_id"], $adminFavoriteOption))
			{
				$arItem["PARAMS"]["can_delete_from_favourite"] = "A";//can edit just admin
				$arResultItemsNew[$currentTitle][$arItem["PARAMS"]["menu_item_id"]] = $arItem;
				$arFlatItemsList[$arItem["PARAMS"]["menu_item_id"]] = $arItem;
				$arFavouriteDefault[] = $arItem["PARAMS"]["menu_item_id"];
			}
		}
	}
}
//--add admin items to default favorite, status
$arResult["FAVOURITE_DEFAULT_ITEMS"] = $arFavouriteDefault;   //can't delete from favorite

$allItemsInFavourite = $arFavouriteDefault;
$allShowItemsInFavourite = $arFavouriteDefault;

// merge admin option with user option
if (is_array($adminFavoriteOption) && !empty($adminFavoriteOption))
{
	if (empty($userMenuOption["menu-favorites"]["show"]))
	{
		if (empty($userMenuOption["menu-favorites"]["hide"]))
			$userMenuOption["menu-favorites"]["show"] = $arFavouriteDefault;
		else
		{
			foreach($arFavouriteDefault as $itemID)
			{
				if (!in_array($itemID, $userMenuOption["menu-favorites"]["hide"]))
					$userMenuOption["menu-favorites"]["show"][] = $itemID;
			}
		}
	}
	foreach($adminFavoriteOption as $itemID)
	{
		if (
			(!is_array($userMenuOption["menu-favorites"]["show"]) || !in_array($itemID, $userMenuOption["menu-favorites"]["show"]))
			&& (!is_array($userMenuOption["menu-favorites"]["hide"]) || !in_array($itemID, $userMenuOption["menu-favorites"]["hide"]))
		)
			$userMenuOption["menu-favorites"]["show"][] = $itemID;
	}
}
//-- merge admin option with user option

//find all items in favourite
$userMenuOptionTitleIDs = array();
if ($userMenuOption)
{
	$allItemsInFavourite = array();
	$allShowItemsInFavourite = array();

	$isChanged = false;
	foreach($userMenuOption as $title => $arIDs)
	{
		if ($title == "menu-favorites")
		{
			if (is_array($arIDs["show"]))
			{
				foreach($arIDs["show"] as $key=>$id)
				{
					if (!in_array($id, $arResult["FAVOURITE_DEFAULT_ITEMS"]) && !in_array($id, $favoriteItemsUserAdded) && !in_array($id, $adminFavoriteOption))
					{

						unset($userMenuOption[$title]["show"][$key]);
						$isChanged = true;
					}
					else
					{
						$allItemsInFavourite[] = $id;
						$allShowItemsInFavourite[] = $id;
					}
				}
			}
			if (is_array($arIDs["hide"]))
			{
				foreach($arIDs["hide"] as $key=>$id)
				{
					if (!in_array($id, $arResult["FAVOURITE_DEFAULT_ITEMS"]) && !in_array($id, $favoriteItemsUserAdded) && !in_array($id, $adminFavoriteOption))
					{
						unset($userMenuOption[$title]["hide"][$key]);
						$isChanged = true;
					}
					else
						$allItemsInFavourite[] = $id;
				}
			}
			break;
		}
	}
	$userMenuOptionTitleIDs = array_keys($userMenuOption);
	if ($isChanged)
		CUserOptions::SetOption("bitrix24", $optionNameUserMenu, $userMenuOption);
}

//--find all items in favourite
$arResult["ALL_FAVOURITE_ITEMS_ID"] = $allItemsInFavourite;
$arResult["ALL_SHOW_FAVOURITE_ITEMS_ID"] = $allShowItemsInFavourite;

//sort items
$arResultSortItems = array();
foreach($arResultTitleItemsNew as $title => $arTitle)
{
	$titleItemExists = ($title == "menu-favorites") ? true : false;  //not hidden
	if (in_array($title, $userMenuOptionTitleIDs))  //block is sorted
	{
		if (is_array($userMenuOption[$title]["show"]))   //show items from option
		{
			foreach($userMenuOption[$title]["show"] as $itemID)
			{
				$arResultSortItems[$title]["show"][$itemID] = isset($arResultItemsNew[$title][$itemID]) ? $arResultItemsNew[$title][$itemID] : $arFlatItemsList[$itemID];
				unset($arResultItemsNew[$title][$itemID]);
				if (!in_array($itemID, $allItemsInFavourite))
					$titleItemExists = true;
			}
		}
		if (is_array($userMenuOption[$title]["hide"]))     //hide items from option
		{
			foreach($userMenuOption[$title]["hide"] as $itemID)
			{
				$arResultSortItems[$title]["hide"][$itemID] = isset($arResultItemsNew[$title][$itemID]) ? $arResultItemsNew[$title][$itemID] : $arFlatItemsList[$itemID];
				unset($arResultItemsNew[$title][$itemID]);
				if (!in_array($itemID, $allItemsInFavourite))
					$titleItemExists = true;
			}
		}
		if (is_array($arResultItemsNew[$title]) && !empty($arResultItemsNew[$title]))   //other items, not from option
		{
			foreach($arResultItemsNew[$title] as $itemID => $arItem)
			{
				$arResultSortItems[$title]["show"][$itemID] = isset($arResultItemsNew[$title][$itemID]) ? $arResultItemsNew[$title][$itemID] : $arFlatItemsList[$itemID];
				if (!in_array($itemID, $allItemsInFavourite))
					$titleItemExists = true;
			}
		}
	}
	else //block is not sorted
	{
		$arResultSortItems[$title]["show"] = $arResultItemsNew[$title];
		$arItemIDs = array_keys($arResultItemsNew[$title]);
		foreach($arItemIDs as $id)
		{
			if (!in_array($id, $allItemsInFavourite))
				$titleItemExists = true;
		}
	}

	if (!$titleItemExists)
		$arResultTitleItemsNew[$title]["PARAMS"]["is_empty"] = "Y";
}
//--sort items

$arResult["SORT_ITEMS"] = $arResultSortItems;  //sorted array of items
$arResult["TITLE_ITEMS"] = $arResultTitleItemsNew;
?>