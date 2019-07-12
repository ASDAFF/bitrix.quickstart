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
$bOpen = false;
$count = $arResult["ITEMS"];
?>
<div class="bj-block-group">
<?foreach($arResult["ITEMS"] as $key => $arItem):?>
	<?if($key % 3 == 0):$bOpen = true;?><div class="row"><?endif;?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="col-sm-4 col-xs-12" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<div class="bj-block">
			<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" class="img-responsive bj-block__img" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"></a>
			<?endif;?>
			<div class="bj-block__title bj-table">
				<div class="bj-table-row">
					<div class="bj-table-cell">
						<h2 class="bj-block__title__wrapper">
							<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a>
						</h2>
					</div>
				</div>
			</div>
			<div>
			<?if(strlen($arItem["PREVIEW_TEXT"])):?><p><?echo $arItem["PREVIEW_TEXT"];?></p><?endif;?>
			<?if($arResult["LINKED_ELEMENTS"][ $arItem["ID"] ]["CNT"]):?><p><a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?=$arResult["LINKED_ELEMENTS"][ $arItem["ID"] ]["CNT"]?> <?=$arResult["LINKED_ELEMENTS"][ $arItem["ID"] ]["STRING"]?></a></p><?endif;?>
			</div>
		</div>
	</div>	
	<?if($key % 3 == 2):$bOpen = false;?>
	</div><?if($count != $key + 1):?><hr><?endif;?>
	<?else:?>
	<hr class="clearfix visible-xs-block">
	<?endif;?>
<?endforeach;?>
<?if($bOpen):?></div><?endif;?>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
<hr>
<?=$arResult["NAV_STRING"]?>
<?endif;?>