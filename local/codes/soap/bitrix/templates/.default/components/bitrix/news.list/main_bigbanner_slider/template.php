<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="b-tab-head">
<?foreach($arResult["ITEMS"] as $arItem):?>
<a href="#<?=$this->GetEditAreaId($arItem['ID']);?>" class="b-tab-head__link"><?=$arItem["NAME"]?> <?=$arItem['ID']?></a>
<?endforeach;?>
</div>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
<div id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="b-tab__body">
    <div class="b-tab">
        <div class="b-tab-promo__image"><a href="$arItem['PROPERTIES']['link']['VALUE']"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="" /></a></div>
		<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "/includes/big_banner_footer.php",
		"EDIT_TEMPLATE" => ""
	),
false
);?> 
    </div>
</div>
<?endforeach;?>
