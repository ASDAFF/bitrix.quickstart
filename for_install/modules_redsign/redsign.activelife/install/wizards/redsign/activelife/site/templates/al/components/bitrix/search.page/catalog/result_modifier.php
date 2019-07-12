<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(IntVal($arParams["COUNT_RESULT_NOT_CATALOG"])<1)
	$arParams["COUNT_RESULT_NOT_CATALOG"] = 0;

if(CModule::IncludeModule('iblock') && CModule::IncludeModule('catalog'))
{
	$arAllPicParams = array("MORE_PHOTO_CODE"=>$arParams["ADDITIONAL_PICT_PROP"],"SKU_MORE_PHOTO_CODE"=>$arParams["OFFER_ADDITIONAL_PICT_PROP"]);
	$arSizes = array("WIDTH"=>214,"HEIGHT"=>153);
	$arCatalog = array();
	$arIBlocks = array();
	$arOthers = array();
	if(!empty($arResult["SEARCH"]))
	{
		foreach($arResult["SEARCH"] as $key1 => $arItem)
		{
			$dbRes = CIBlockElement::GetByID($arItem["ITEM_ID"]);
			if($arFields = $dbRes->GetNext())
			{
				$arResult["SEARCH"][$key1]["PREVIEW_PICTURE"] = (0 < $arFields["PREVIEW_PICTURE"] ? CFile::GetFileArray($arFields["PREVIEW_PICTURE"]) : false);
				$arResult["SEARCH"][$key1]["DETAIL_PICTURE"] = (0 < $arFields["DETAIL_PICTURE"] ? CFile::GetFileArray($arFields["DETAIL_PICTURE"]) : false);
				// Get all pictures
				$arResult["SEARCH"][$key1]["IMAGES"] = RSDevFuncOffersExtension::GetAllPictures($arSizes,$arResult["SEARCH"][$key1],$arAllPicParams);
				// /Get all pictures
			}
			if($arItem["MODULE_ID"]=="iblock" && $arItem["PARAM2"]!=$arParams["CATALOG_IBLOCK_ID"])
			{
				if(empty($arIBlocks[$arItem["PARAM2"]]))
				{
					$res = CIBlock::GetByID( $arItem["PARAM2"] );
					if($arRes = $res->GetNext())
					{
						$arIBlocks[$arItem["PARAM2"]] = $arRes;
					}
				}
			}
			$arResult["SEARCH"][$key1]["IBLOCK_LINK"] = str_replace('//','/', str_replace($arItem["CODE"],'',$arItem["DETAIL_PAGE_URL"]) );
		}
	}
}
$arResult["IBLOCKS"] = $arIBlocks;
?>