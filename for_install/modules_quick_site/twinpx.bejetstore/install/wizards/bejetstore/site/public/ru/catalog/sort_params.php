<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
global $arSortParams;
$arSortParams = array();

if(!$_REQUEST["sort"] && $arParams['DEFAULT_SORT'])
{
$_REQUEST["sort"]=$arParams['DEFAULT_SORT'];
}
switch ($_REQUEST["sort"]) {
	case 'cheap':
		$arSortParams["ELEMENT_SORT_FIELD"] = "catalog_PRICE_1";
		$arSortParams["ELEMENT_SORT_ORDER"] = "asc";
		$arSortParams["ELEMENT_SORT_FIELD2"] = "name";
		$arSortParams["ELEMENT_SORT_ORDER2"] = "asc";
		break;
	
	case 'popular':
		$arSortParams["ELEMENT_SORT_FIELD"] = "shows";
		$arSortParams["ELEMENT_SORT_ORDER"] = "desc";
		$arSortParams["ELEMENT_SORT_FIELD2"] = "name";
		$arSortParams["ELEMENT_SORT_ORDER2"] = "asc";
		break;

	case 'new':
		$arSortParams["ELEMENT_SORT_FIELD"] = "active_from";
		$arSortParams["ELEMENT_SORT_ORDER"] = "asc";
		$arSortParams["ELEMENT_SORT_FIELD2"] = "name";
		$arSortParams["ELEMENT_SORT_ORDER2"] = "asc";
		break;

	default:
		$arSortParams["ELEMENT_SORT_FIELD"] = "catalog_PRICE_1";
		$arSortParams["ELEMENT_SORT_ORDER"] = "asc";
		$arSortParams["ELEMENT_SORT_FIELD2"] = "name";
		$arSortParams["ELEMENT_SORT_ORDER2"] = "asc";
		break;
}
?>