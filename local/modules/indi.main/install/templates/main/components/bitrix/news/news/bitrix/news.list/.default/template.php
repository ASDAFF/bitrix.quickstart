<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
use Indi\Main\Util;
if (!$arResult['ITEMS']) {
	return;
}

?>
<!-- news-list -->

<div class="news-list row">
	<?foreach($arResult['ITEMS'] as $key=>$arItem):?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
		<div class="col-sm-6 col-md-4" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
			<div class="news-item">
				<div class="pic">
					<a href="<?=$arItem['~DETAIL_PAGE_URL'];?>">
						<?if(isset($arItem["PREVIEW_PICTURE"]["SRC"]) && !empty($arItem["PREVIEW_PICTURE"]["SRC"])):?>
							<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"];?>"  alt="<?=$arItem['~NAME'];?>">
						<?else:?>
							<img src="<?=SITE_TEMPLATE_PATH?>/kaluga.kuzov-auto.ru/images/cap.png" alt="<?=$arItem['~NAME'];?>" title="<?=$arItem['~NAME'];?>">
						<?endif;?>
					</a>
				</div>
				<h4><a href="<?=$arItem['~DETAIL_PAGE_URL'];?>"><?=$arItem['~NAME'];?></a></h4>
				<?if(isset($arItem["~PREVIEW_TEXT"]) && !empty($arItem["~PREVIEW_TEXT"])):?>
					<div class="text"><?=$arItem["~PREVIEW_TEXT"];?></div>
				<?endif;?>
				<?if(isset($arItem["DISPLAY_ACTIVE_FROM"]) && !empty($arItem["DISPLAY_ACTIVE_FROM"])):?>
					<div class="date"><?=$arItem["DISPLAY_ACTIVE_FROM"];?></div>
				<?endif;?>
			</div>
		</div>
	<?endforeach;?>
</div>
<!-- NEWS ITEMS END -->

<? if ($arParams["DISPLAY_BOTTOM_PAGER"] && $arResult["NAV_STRING"]): ?>
		<?=$arResult["NAV_STRING"];?>
<? endif; ?>


