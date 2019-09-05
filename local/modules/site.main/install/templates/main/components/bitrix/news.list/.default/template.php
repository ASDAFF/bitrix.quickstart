<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (!$arResult['ITEMS']) {
	return;
}
?>

<section class="news-list news-list-default">
	<?
	if ($arParams['DISPLAY_TOP_PAGER']) {
		print $arResult['NAV_STRING'];
	}
	
	foreach($arResult['ITEMS'] as $arItem) {
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		$showLink = !$arParams['HIDE_LINK_WHEN_NO_DETAIL'] || ($arItem['DETAIL_TEXT'] && $arResult['USER_HAVE_ACCESS']);
		?>
		<article class="news-list-item" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
			<?if ($arItem['DISPLAY_ACTIVE_FROM']) {
				?><span class="news-list-date"><?=$arItem['DISPLAY_ACTIVE_FROM']?></span><?
			}?>
			
			<?if ($arParams['DISPLAY_NAME'] != 'N' && $arItem['NAME']) {
				?><h2><?
				if ($showLink) {
					?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a><?
				} else {
					?><?=$arItem['NAME']?><?
				}
				?></h2><?
			}?>
			<div class="row">
				<div class="col-sm-3">
					<?
					if ($showLink) {
						?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
					}
					
					if (is_array($arItem['PREVIEW_PICTURE'])) {
						?><img class="news-list-image img-responsive" src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>"/><?
					} else {
						?><img class="news-list-image img-responsive" src="<?=\Site\Main\TEMPLATE_IMG?>/nophoto.png" alt="<?=$arItem['NAME']?>"/><?
					}
					
					if ($showLink) {
						?></a><?
					}
					?>
				</div>
				<div class="col-sm-9">
					<?
					if ($arItem['PREVIEW_TEXT']) {
						?><div class="news-list-preview"><?=$arItem['PREVIEW_TEXT']?></div><?
					}
					
					if ($arItem['FIELDS'] || $arItem['DISPLAY_PROPERTIES']) {
						?>
						<dl class="news-list-propperties">
							<?
							foreach ($arItem['FIELDS'] as $code => $value) {
								?>
								<dt><?=GetMessage('IBLOCK_FIELD_' . $code)?></dt>
								<dd><?=$value?></dd>
								<?
							}
							
							foreach ($arItem['DISPLAY_PROPERTIES'] as $arProperty) {
								?>
								<dt><?=$arProperty['NAME']?></dt>
								<dd><?=implode(', ', (array) $arProperty['DISPLAY_VALUE'])?></dd>
								<?
							}
							?>
						</dl>
						<?
					}
					?>
				</div>
			</div>
		</article>
		<?
	}
	
	if ($arParams['DISPLAY_BOTTOM_PAGER']) {
		print $arResult['NAV_STRING'];
	}
	?>
</section>
