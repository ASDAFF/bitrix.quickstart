<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id = "services_block">
<?foreach($arResult["SECTIONS"] as $key=>$arSection){?>
<div class = "service" style = "width: <?if($key == count($arResult["SECTIONS"])-1 && intval($arResult["REST"])>0) echo($arResult["BLOCK_WIDTH"]+$arResult["REST"]); else echo $arResult["BLOCK_WIDTH"];?>px;">
<div class = "service_img">
<a href = "<?=$arSection["SECTION_PAGE_URL"]?>" title = "<?=$arSection["NAME"]?>" style = "background-image: url('<?echo $arSection["PICTURE"]["SRC"]?>');"></a>
</div>
<div class = "service_title background_color" style = "width: <?if($key == count($arResult["SECTIONS"])-1 && intval($arResult["REST"])>0) echo($arResult["BLOCK_WIDTH"]+$arResult["REST"]); else echo($arResult["BLOCK_WIDTH"]-1);?>px;">
<span><a href = "<?=$arSection["SECTION_PAGE_URL"]?>"><?=$arSection["NAME"]?></a></span>
</div>
</div>
<?}?>
</div>