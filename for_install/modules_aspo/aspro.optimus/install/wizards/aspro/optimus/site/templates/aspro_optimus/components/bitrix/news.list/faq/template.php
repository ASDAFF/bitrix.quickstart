<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if($arResult["ITEMS"] && $arResult['SECTIONS']):?>
	<div class="faq list">
		<div class="items">
			<?foreach($arResult['SECTIONS'] as $SID => $SName):?>
				<?if($arResult['ITEMS_BY_SECTIONS'][$SID]):?>
					<?if(count($arResult['SECTIONS']) > 1):?>
						<?
						// edit/add/delete buttons for edit mode
						$arSectionButtons = CIBlock::GetPanelButtons($arParams['IBLOCK_ID'], 0 , $SID, array('SESSID' => false, 'CATALOG' => true));
						$this->AddEditAction($SID, $arSectionButtons['edit']['edit_section']['ACTION_URL'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_EDIT'));
						$this->AddDeleteAction($SID, $arSectionButtons['edit']['delete_section']['ACTION_URL'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
						?>
						<h3 id="<?=$this->GetEditAreaId($SID)?>"><?=$SName?></h3>
					<?endif;?>
					<div class="faq_section">
						<?foreach($arResult['ITEMS_BY_SECTIONS'][$SID] as $arItem):?>
							<?
							$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
							$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
							?>
							<div class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
								<div class="q"><a href="javascript:;" rel="nofollow"><?=$arItem["NAME"]?></a><span class="slide opener_icon no_bg"><i></i></span></div>
								<div class="a"><?=$arItem["DETAIL_TEXT"]?></div>						
							</div>
						<?endforeach;?>
					</div>
				<?endif;?>
			<?endforeach;?>	
		</div>
	</div>
	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<?=$arResult["NAV_STRING"]?>
	<?endif;?>
<?endif;?>
<script type="text/javascript">
$(document).ready(function() {
	setTimeout(function() {
		$('.faq.list .item:first .q a').trigger('click');
		if($('.form.ASK .form_result.error').length || $('.form.ASK .form_result.success').length){
			$('.button.faq_button').trigger('click');
		}
	}, 300);
});
</script>
