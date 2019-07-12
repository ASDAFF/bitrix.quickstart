<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($arResult["FILE"] <> ''){?>
<div class = "right_block">
<div class = "right_image <?echo $arParams["BLOCK_TYPE"];?>"></div>
<div class = "right_block_title"><a href = "<?echo $arParams["BLOCK_LINK"];?>"><?if(strlen($arParams["BLOCK_TITLE"])>0) echo $arParams["BLOCK_TITLE"]; else echo GetMessage("DEFAULT_BLOCK_TITLE");?></a></div>
<div class = "right_block_content">
	<?include($arResult["FILE"]);?>
</div>
</div>
<?}?>
