<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="sonet-add-group-button">
	<a onclick="AddPopupGroup(event);" class="sonet-add-group-button-left" href="<?=$arParams["~HREF"]?>" title="<?= GetMessage("SONET_C36_T_CREATE") ?>"></a>
	<div class="sonet-add-group-button-fill"><a onclick="AddPopupGroup(event);" href="<?=$arParams["~HREF"]?>" class="sonet-add-group-button-fill-text"><?= GetMessage("SONET_C36_T_CREATE") ?></a></div>
	<a onclick="AddPopupGroup(event);" class="sonet-add-group-button-right" href="<?=$arParams["~HREF"]?>" title="<?= GetMessage("SONET_C36_T_CREATE") ?>"></a>
	<div class="sonet-add-group-button-clear"></div>
</div>