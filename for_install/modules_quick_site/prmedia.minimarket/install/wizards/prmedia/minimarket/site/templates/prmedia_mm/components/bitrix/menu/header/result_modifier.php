<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CCacheManager $CACHE_MANAGER
 * @global CDatabase $DB
 * @param array $arParams
 * @param array $arResult
 */

// catalog section
$catalogItem = $arParams['CATALOG_MENU_ITEM'];
$siteCatalogItem = SITE_DIR . $catalogItem;
if ($arParams['SHOW_CATALOG'] === 'Y')
{
	foreach ($arResult as $key => $menuItem)
	{
		if ($menuItem['LINK'] === $catalogItem || $menuItem['LINK'] === $siteCatalogItem)
		{
			$arResult[$key]['IS_CATALOG'] = 'Y';
			break;
		}
	}
}

// result rebuild
$parentKey = 0;
foreach ($arResult as $key => $menuItem)
{
	if ($menuItem['DEPTH_LEVEL'] == 2)
	{
		$arResult[$parentKey]['ITEMS'][] = $menuItem;
		unset($arResult[$key]);
		continue;
	}
	else
	{
		$parentKey = $key;
	}
}

function PrmediaMinimarketMenuHeaderShowLink($menuItem)
{
	$class = intval($menuItem['SELECTED']) > 0 ? 'class="active"' : '';
	$href = 'href="' . $menuItem['LINK'] . '"';
	$link = '<a ' . $href . $class . '>' . $menuItem['TEXT'] . '</a>';
	echo $link;
}