<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
 <div class="section group">
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="grid_1_of_4 team_1_of_4" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
	<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>" />
	<h3><?echo $arItem["NAME"]?></h3>
	<h5><?=$arItem["DISPLAY_PROPERTIES"]["team_position"]["VALUE"];?></h5>
	<p><?echo $arItem["PREVIEW_TEXT"];?></p>
	<div class="social_networks">
		<ul>
		<li><a href="<?=$arItem["DISPLAY_PROPERTIES"]["facebook"]["VALUE"];?>"> <i class="facebook"> </i> </a></li>
		<li><a href="<?=$arItem["DISPLAY_PROPERTIES"]["twitter"]["VALUE"];?>"><i class="twitter"> </i> </a></li>
		<li><a href="<?=$arItem["DISPLAY_PROPERTIES"]["linkedIn"]["VALUE"];?>"><i class="inliked"> </i> </a></li>
		<li><a href="<?=$arItem["DISPLAY_PROPERTIES"]["email"]["VALUE"];?>"><i class="mail"> </i> </a></li>
		<div class="clear"> </div>
		</ul>
	</div></div>
	<?endforeach;?>
	</div>
