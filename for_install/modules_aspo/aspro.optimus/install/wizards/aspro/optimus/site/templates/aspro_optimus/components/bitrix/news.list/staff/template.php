<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if($arResult["ITEMS"] && $arResult['SECTIONS']):?>
	<div class="staff list">
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
						<div class="staff_section" id="<?=$this->GetEditAreaId($SID)?>">
							<div class="staff_section_title"><h4><a rel="nofollow" href=""><?=$SName?></a><span class="slide opener_icon no_bg"><i></i></span></h4></div>
					<?endif;?>
							<div class="staff_section_items">
								<?foreach($arResult['ITEMS_BY_SECTIONS'][$SID] as $arItem):?>
									<?
									$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
									$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
									if($bShowName = in_array('NAME', $arParams['FIELD_CODE'])){
										$arItem["NAME"] = trim($arItem["NAME"]);
										$arName = explode(' ', $arItem["NAME"]);
										$firstName = $arName[0];
										if($firstName != $arItem["NAME"]){
											unset($arName[0]);
											$secondName = implode(' ', $arName);
										}
										else{
											$secondName = '';
										}
									}
									$bShowImage = (in_array('PREVIEW_PICTURE', $arParams['FIELD_CODE']) || in_array('DETAIL_PICTURE', $arParams['FIELD_CODE']));
									?>
									<div class="item <?=($bShowImage ? '' : 'wi')?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
										<?if($bShowImage):?>
											<div class="image">
												<?if(!empty($arItem["PREVIEW_PICTURE"]) && in_array('PREVIEW_PICTURE', $arParams['FIELD_CODE'])):?>
													<?$arImage = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"]["ID"], array('width' => 170, 'height' => 170), BX_RESIZE_IMAGE_EXACT);?>
													<img src="<?=$arImage['src']?>"  alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"] ? $arItem["PREVIEW_PICTURE"]["ALT"] : ($arItem["DETAIL_PICTURE"]["ALT"] ? $arItem["DETAIL_PICTURE"]["ALT"] : $arItem["NAME"]))?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"] ? $arItem["PREVIEW_PICTURE"]["TITLE"] : $arItem["NAME"])?>" />
												<?elseif(!empty($arItem["DETAIL_PICTURE"]) || in_array('DETAIL_PICTURE', $arParams['FIELD_CODE'])):?>
													<?$arImage = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"]["ID"], array('width' => 170, 'height' => 170), BX_RESIZE_IMAGE_EXACT);?>
													<img src="<?=$arImage['src']?>" alt="<?=($arItem["DETAIL_PICTURE"]["ALT"] ? $arItem["DETAIL_PICTURE"]["ALT"] : $arItem["NAME"])?>" title="<?=($arItem["DETAIL_PICTURE"]["TITLE"] ? $arItem["DETAIL_PICTURE"]["TITLE"] : $arItem["NAME"])?>" />
												<?else:?>
													<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" />
												<?endif;?>
											</div>
										<?endif;?>
										<div class="info">
											<?if($bShowName):?>
												<div class="name">
													<?=$firstName?><?if(strlen($secondName)):?>&nbsp;<br /><?=$secondName?><?endif;?>
												</div>
											<?endif;?>
											<?if(strlen($arItem["DISPLAY_PROPERTIES"]["POST"]["VALUE"])):?>
												<div class="post"><?=$arItem["DISPLAY_PROPERTIES"]["POST"]["VALUE"]?></div>
											<?endif;?>
											<?if(strlen($arItem["DISPLAY_PROPERTIES"]["PHONE"]["VALUE"])):?>
												<div class="phone"><div><?=GetMessage('PHONE')?></div><?=$arItem["DISPLAY_PROPERTIES"]["PHONE"]["VALUE"]?></div>
											<?endif;?>
											<?if(strlen($arItem["DISPLAY_PROPERTIES"]["EMAIL"]["VALUE"])):?>
												<div class="email"><div><?=GetMessage('EMAIL')?></div><a rel="nofollow" href="mailto:<?=$arItem["DISPLAY_PROPERTIES"]["EMAIL"]["VALUE"]?>"><?=$arItem["DISPLAY_PROPERTIES"]["EMAIL"]["VALUE"]?></a></div>
											<?endif;?>
										</div>						
									</div>
								<?endforeach;?>
							</div>
					<?if(count($arResult['SECTIONS']) > 1):?>
						</div>
					<?endif;?>
				<?endif;?>
			<?endforeach;?>	
		</div>
	</div>
	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<?=$arResult["NAV_STRING"]?>
	<?endif;?>
<?else:?>
	<p class="no_items"><?=GetMessage("NO_STAFF");?></p>
<?endif;?>
<script type="text/javascript">
$(document).ready(function() {
	setTimeout(function() {
		$('.staff.list .staff_section:first .staff_section_title a').trigger('click');
	}, 300);
});
</script>