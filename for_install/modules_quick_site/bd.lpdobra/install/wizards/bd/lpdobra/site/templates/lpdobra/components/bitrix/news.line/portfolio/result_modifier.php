<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?foreach($arResult["ITEMS"] as $key=>$arItem):?>
<?	
  $arFilter = Array('IBLOCK_ID'=>$arItem["IBLOCK_ID"], "ID" => $arItem["IBLOCK_SECTION_ID"]);
  $arSelect = Array('CODE');
  $db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, false, $arSelect);
    $code=array();
	while($ar_result = $db_list->GetNext())
  {
	$arItem["SECTION_CODE"]=$ar_result["CODE"];
  }
  if($arItem["PREVIEW_PICTURE"])
{
$file = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"], array('width'=>560, 'height'=>335), BX_RESIZE_IMAGE_PROPORTIONAL, true); 
 $arItem['PICTURE']=Array('ID'=>$img, 'SRC'=>$file['src'], 'WIDTH'=>$file['width'], 'HEIGHT'=>$file['height']);     
}
 $arResult['ITEMS'][$key]= $arItem;     
?>
<?endforeach;?>
