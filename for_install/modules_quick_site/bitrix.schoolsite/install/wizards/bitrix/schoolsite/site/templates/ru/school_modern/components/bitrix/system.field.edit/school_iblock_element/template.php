<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$fieldName = $arParams["arUserField"]["FIELD_NAME"];
if(!intVal($arParams["arUserField"]["SETTINGS"]["IBLOCK_ID"]) || !CModule::IncludeModule("iblock"))
{
 ShowError(GetMessage("UF_IBLOCK_ELEMENT_IBLOCK_NOT_DEFINED"));
 return false;
}

if($arParams["arUserField"]["MULTIPLE"] == "Y")
{
 ?><select multiple="multiple" name="<?=$fieldName?>" size="5"><?
 foreach ($arParams["arUserField"]["USER_TYPE"]["FIELDS"] as $sec)
 {
  ?><option value="" style="font-weight: bold; font-style: italic;"><?=str_repeat(".", $sec["DEPTH_LEVEL"])?>[<?=$sec["NAME"]?>]</option><?
  foreach($sec["ITEMS"] as $key=>$val)
  {
   $bSelected = in_array($key, $arResult["VALUE"]);
   ?><option value="<?echo $key?>" <?echo ($bSelected? "selected" : "")?> title="<?=htmlspecialchars($val)?>"><?=str_repeat("&nbsp;", $sec["DEPTH_LEVEL"]+1)?><?=$val?></option><?
  }
 }
 ?></select><?
}
else
{
 ?><select name="<?=$fieldName?>" size="5"><?
 foreach ($arParams["arUserField"]["USER_TYPE"]["FIELDS"] as $sec)
 {
  ?><option value="" style="font-weight: bold; font-style: italic;"><?=str_repeat(".", $sec["DEPTH_LEVEL"])?>[<?=$sec["NAME"]?>]</option><?
  foreach($sec["ITEMS"] as $key=>$val)
  {
   $bSelected = in_array($key, $arResult["VALUE"]);
   ?><option value="<?echo $key?>" <?echo ($bSelected? "selected" : "")?> title="<?=htmlspecialchars($val)?>"><?=str_repeat("&nbsp;", $sec["DEPTH_LEVEL"]+1)?><?=$val?></option><?
  }
 }
 ?></select><?
}
?>