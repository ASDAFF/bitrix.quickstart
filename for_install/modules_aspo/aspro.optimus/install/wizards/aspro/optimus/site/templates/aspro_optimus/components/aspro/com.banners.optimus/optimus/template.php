<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if($arResult["ITEMS"]){?>
	
	<div class="start_promo <?=($arResult["OTHER_BANNERS_VIEW"]=="Y" ? "other" : "normal_view");?>">
		<?$i=1;?>
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			$isUrl=(strlen($arItem["PROPERTIES"]["URL_STRING"]["VALUE"]) ? true : false);
			?>
			<?if($arItem["DETAIL_PICTURE"]["SRC"] || $arItem["PREVIEW_PICTURE"]["SRC"]):?>
				<div class="item s_<?=$i;?> <?=($isUrl ? "hover" : "");?> <?=($arItem["PROPERTIES"]["BANNER_SIZE"]["VALUE_XML_ID"] ? $arItem["PROPERTIES"]["BANNER_SIZE"]["VALUE_XML_ID"] : "normal");?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<?$arItem["FORMAT_NAME"]=strip_tags($arItem["~NAME"]);?>
					<?if($isUrl){?>
						<a href="<?=$arItem["PROPERTIES"]["URL_STRING"]["VALUE"]?>" class="opacity_block1 dark_block_animate" title="<?=$arItem["FORMAT_NAME"];?>" <?=($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"] ? "target='".$arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]."'" : "");?>></a>
					<?}
					if($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"] != "image"):?>
						<?$class_position_block = $class_text_block = '';
						if(isset($arItem["PROPERTIES"]["TEXT_POSITION"]) && $arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"])
						{
							$class_position_block = $arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"].'_blocks';
						}
						if(isset($arItem["PROPERTIES"]["TEXTCOLOR"]) && $arItem["PROPERTIES"]["TEXTCOLOR"]["VALUE_XML_ID"])
						{
							$class_text_block = $arItem["PROPERTIES"]["TEXTCOLOR"]["VALUE_XML_ID"].'_text';
						}
						?>
						<div class="wrap_tizer  <?=$class_position_block;?> <?=$class_text_block;?>">
							<div class="wrapper_inner_tizer">
								<div class="wr_block">
									<span class="wrap_outer title">
										<?if($isUrl){?>
											<?if($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]):?>
												<a class="outer_text" href="<?=$arItem["PROPERTIES"]["URL_STRING"]["VALUE"]?>" <?=($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"] ? "target='".$arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]."'" : "");?>>
											<?else:?>
												<a class="outer_text" href="<?=$arItem["PROPERTIES"]["URL_STRING"]["VALUE"]?>">
											<?endif;?>
										<?}else{?>
											<span class="outer_text">
										<?}?>
											<span class="inner_text">
												<?=strip_tags($arItem["~NAME"], "<br><br/>");?>
											</span>
										<?if($isUrl){?>
											</a>
										<?}else{?>
											</span>
										<?}?>
									</span>
								</div>
								<?if($arItem["PREVIEW_TEXT"]){?>
									<div class="wr_block price">
										<span class="wrap_outer_desc">
											<?if($isUrl){?>
												<a class="outer_text_desc" href="<?=$arItem["PROPERTIES"]["URL_STRING"]["VALUE"]?>" <?=($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"] ? "target='".$arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]."'" : "");?>>
											<?}else{?>
												<span class="outer_text_desc">
											<?}?>
												<span class="inner_text_desc">
													<?=trim(strip_tags($arItem["PREVIEW_TEXT"]))?>
												</span>
											<?if($isUrl){?>
												</a>
											<?}else{?>
												</span>
											<?}?>
										</span>
									</div>
								<?}?>
							</div>
						</div>
					<?endif;?>
					<?if($isUrl){?>
						<a href="<?=$arItem["PROPERTIES"]["URL_STRING"]["VALUE"]?>" <?=($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"] ? "target='".$arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]."'" : "");?>>
					<?}?>
						<img class="scale_block_animate" src="<?=($arItem["DETAIL_PICTURE"]["SRC"] ? $arItem["DETAIL_PICTURE"]["SRC"] : $arItem["PREVIEW_PICTURE"]["SRC"])?>" alt="<?=$arItem["FORMAT_NAME"]?>" title="<?=$arItem["FORMAT_NAME"]?>" />
					<?if($isUrl){?>
						</a>
					<?}?>
				</div>
				<?$i++;?>
			<?endif;?>
		<?endforeach;?>
	</div>
<?}?>