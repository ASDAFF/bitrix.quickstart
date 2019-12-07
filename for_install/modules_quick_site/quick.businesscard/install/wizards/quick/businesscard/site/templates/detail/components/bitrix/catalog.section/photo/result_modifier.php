<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

if(!empty($arResult['ITEMS']))
{
	foreach ($arResult['ITEMS'] as $key => &$arItem)
	{
		if(!empty($arItem["PROPERTIES"]["MORE_PHOTO"]["VALUE"]))
			foreach($arItem["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $key=>$arPhoto)
			{
				$arItem["PROPERTIES"]["MORE_PHOTO"]["ITEMS"][$key]["PREVIEW"] = CFile::ResizeImageGet(
					$arPhoto,
					array("width" => 800, "height"=>800),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arItem["PROPERTIES"]["MORE_PHOTO"]["ITEMS"][$key]["PREVIEW"]["SRC"] = $arItem["PROPERTIES"]["MORE_PHOTO"]["ITEMS"][$key]["PREVIEW"]["src"];
				$arItem["PROPERTIES"]["MORE_PHOTO"]["ITEMS"][$key]["DETAIL"] = CFile::GetFileArray($arPhoto);		
				$arItem["PROPERTIES"]["MORE_PHOTO"]["ITEMS"][$key]["DESCRIPTION"] = $arItem["PROPERTIES"]["MORE_PHOTO_DESCRIPTION"]["VALUE"][$key];
			}
	}	
}
?>