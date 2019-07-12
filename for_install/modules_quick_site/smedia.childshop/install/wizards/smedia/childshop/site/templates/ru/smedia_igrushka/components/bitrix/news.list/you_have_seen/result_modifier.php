<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
global $APPLICATION;
$YOU_HAVE_SEEN = $APPLICATION->get_cookie("YOU_HAVE_SEEN");		
$arYOU_HAVE_SEEN=unserialize($YOU_HAVE_SEEN);
$transYOU_HAVE_SEEN = array_flip($arYOU_HAVE_SEEN);
$sortedItems=array();	
foreach ($arResult['ITEMS'] as $key => $arElement)
{	
	if(is_array($arElement["PREVIEW_PICTURE"]))
	{
		$arFilter = '';
		if($arParams["SHARPEN"] != 0)
		{
			$arFilter = array(array("name" => "sharpen", "precision" => $arParams["SHARPEN"]));
		}
		
		$arFileTmp = CFile::ResizeImageGet(
			$arElement['PREVIEW_PICTURE'],
			array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true, $arFilter
		);
		
		$arElement["PREVIEW_PICTURE"] = array(
			"SRC" => $arFileTmp["src"],
			'WIDTH' => $arFileTmp["width"],
			'HEIGHT' => $arFileTmp["height"],
		);
	}
	$newKey=$transYOU_HAVE_SEEN[$arElement['ID']];
	$sortedItems[$newKey]=$arElement;
}
ksort($sortedItems);
$arResult['ITEMS']=$sortedItems;
?>
