<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arParams['ROOT_MENU_TYPE'] = strval($arParams['ROOT_MENU_TYPE']);
$arParams['INNER_MENU_TYPE'] = strval($arParams['INNER_MENU_TYPE']);
$arParams['SHOW_ALL'] = ($arParams['SHOW_ALL'] == 'Y' ? 'Y' : 'N');
$arParams['DIR'] = strval($arParams['DIR']);

if (strlen($arParams['DIR']) <= 0)
{
	$arPath = explode("/", $APPLICATION -> GetCurDir());
	if (strlen($arPath[2]) > 0)
	{
		$arParams['DIR'] =implode("/", array_slice($arPath, 0, 3)) . '/';
	}
	else
		$arParams['DIR'] = SITE_DIR;
}

if (!function_exists('_GetChild'))
{
function _GetChild($dir, $type, $showAll)
{
	$arOut = array();
	$menu = new CMenu($type);
	$menu -> Init($dir, true, '/bitrix/components/bitrix/menu/stub.php');
	if ($menu -> MenuDir == $dir)
	{
		$menu -> RecalcMenu();
		$arItems = $menu -> arMenu;
		for ($i = 0, $size = sizeof($menu -> arMenu); $i < $size; $i++)
		{
			$item = $arItems[$i];
			if ($showAll == 'Y' || ($showAll == 'N' && $item['SELECTED']))
				$item['CHILD'] = _GetChild($item['LINK'], $type, $showAll);
			$arOut[] = $item;
		}
	}
	return $arOut;
}
}

if (!function_exists('_GetMenuTree'))
{
function _GetMenuTree($dir, $top, $inner, $showAll)
{
	$arOut = array();
	$menu = new CMenu($top);
	$menu -> Init($dir, true, '/bitrix/components/bitrix/menu/stub.php');
	//print "<pre>" . print_r($menu, true) . "</pre>";
	if ($menu -> MenuDir == $dir)
	{
		$menu -> RecalcMenu();
		$arItems = $menu -> arMenu;
		for ($i = 0, $size = sizeof($menu -> arMenu); $i < $size; $i++)
		{
			$item = $arItems[$i];
			if ($showAll == 'Y' || ($showAll == 'N' && $item['SELECTED']))
				$item['CHILD'] = _GetChild($item['LINK'], $inner, $showAll);
			$arOut[] = $item;
		}
	}
	return $arOut;
}
}

if($this->StartResultCache(false, array($USER->GetGroups(), $_GET, $APPLICATION -> GetCurUri())))
{
	$arResult['ITEMS'] = _GetMenuTree($arParams['DIR'], $arParams['ROOT_MENU_TYPE'], $arParams['INNER_MENU_TYPE'], $arParams['SHOW_ALL']);
	$this->IncludeComponentTemplate();
}
else
{
	$this->AbortResultCache();
}
?>