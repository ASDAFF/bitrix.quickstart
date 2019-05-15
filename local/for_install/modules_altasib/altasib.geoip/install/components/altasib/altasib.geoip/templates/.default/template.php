<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if($arResult["region"] && $arResult["city"]){?>
<span class="notetext"><?echo $arResult["region"]?><?if($arResult["city"] != $arResult["region"]) echo ", ".$arResult["city"]?></span>
<?}?>