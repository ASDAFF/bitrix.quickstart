<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($arResult["FILE"] <> ''){?>
<div id = "company_info">
<div class = "col_title">
<h2 class = "color1"><?if(strlen($arParams["BLOCK_TITLE"])>0) echo $arParams["BLOCK_TITLE"]; else echo GetMessage("DEFAULT_BLOCK_TITLE");?></h2>
</div>
<div>
	<?include($arResult["FILE"]);?>
</div>
</div>
<?}?>
