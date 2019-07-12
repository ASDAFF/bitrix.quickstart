<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

IncludeTemplateLangFile(__FILE__);
?>

	<p><?=$arResult["DETAIL_TEXT"];?></p>

	<p><a href="<?=(isMobile() ? "tel:".str_replace(array(" ", "(", ")", "-"), array("", "", "", ""), $arResult["PREVIEW_TEXT"]) : 'javascript:void(0);')?>" class="tel"><?=$arResult["PREVIEW_TEXT"]?></a></p>