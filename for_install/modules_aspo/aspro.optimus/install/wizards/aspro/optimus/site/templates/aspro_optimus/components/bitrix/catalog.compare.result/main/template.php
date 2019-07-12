<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$isAjax = ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["ajax_action"]) && $_POST["ajax_action"] == "Y");?>
<div class="bx_compare" id="bx_catalog_compare_block">
<?if ($isAjax){
	$APPLICATION->RestartBuffer();
}?>
<div class="bx_sort_container">
	<ul class="tabs-head">
		<li <?=(!$arResult["DIFFERENT"] ? 'class="current"' : '');?>>
			<span class="sortbutton<? echo (!$arResult["DIFFERENT"] ? ' current' : ''); ?>" data-href="?DIFFERENT=N" rel="nofollow"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></span>
		</li>
		<li <?=($arResult["DIFFERENT"] ? 'class="current"' : '');?>>
			<span class="sortbutton diff <? echo ($arResult["DIFFERENT"] ? ' current' : ''); ?>" data-href="?DIFFERENT=Y" rel="nofollow"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></span>
		</li>
	</ul>
	<span class="wrap_remove_button">
		<?$arStr=$arCompareIDs=array();
		if($arResult["ITEMS"]){
			foreach($arResult["ITEMS"] as $arItem){
				$arCompareIDs[]=$arItem["ID"];
			}
		}
		$arStr=implode("&ID[]=", $arCompareIDs)?>
		<span class="button grey_br transparent remove_all_compare icon_close" onclick="CatalogCompareObj.MakeAjaxAction('/catalog/compare.php?action=DELETE_FROM_COMPARE_RESULT&ID[]=<?=$arStr?>', 'Y');"><?=GetMessage("CLEAR_ALL_COMPARE")?></span>
	</span>
</div>
<div class="table_compare wrap_sliders tabs-body">
	<?if (!empty($arResult["SHOW_FIELDS"])){?>
		<div class="frame top">
			<div class="wraps">
				<table class="compare_view top">
					<tr>
						<?foreach($arResult["ITEMS"] as &$arElement){?>
							<td>
								<div class="item_block">
									<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arElement['~DELETE_URL'])?>', 'Y');" class="remove" title="<?=GetMessage("CATALOG_REMOVE_PRODUCT")?>"><i></i></span>
									<div class="image_wrapper_block">
										<?if($arElement["OFFER_FIELDS"]["PREVIEW_PICTURE"]){
												$img=CFile::GetFileArray($arElement["OFFER_FIELDS"]["PREVIEW_PICTURE"]);?>
												<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img src="<?=$img["SRC"]?>" alt="<?=$img["ALT"]?>" title="<?=$img["TITLE"]?>" /></a>
										<?}elseif($arElement["FIELDS"]["PREVIEW_PICTURE"]){
											if(is_array($arElement["FIELDS"]["PREVIEW_PICTURE"])):?>
												<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img src="<?=$arElement["FIELDS"]["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arElement["FIELDS"]["PREVIEW_PICTURE"]["ALT"]?>" title="<?=$arElement["FIELDS"]["PREVIEW_PICTURE"]["TITLE"]?>" /></a>
											<?endif;
										}else{?>
												<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></a>
										<?}?>
									</div>
									<a href="<?=$arElement["DETAIL_PAGE_URL"]?>" class="title"><?=$arElement["NAME"]?></a>
									<div class="cost prices clearfix">
										<?
										$frame = $this->createFrame()->begin('');
										$frame->setBrowserStorage(true);
										?>
										<?if (isset($arElement['MIN_PRICE']) && is_array($arElement['MIN_PRICE'])){?>
											<div class="price"><?=$arElement['MIN_PRICE']['PRINT_DISCOUNT_VALUE'];?></div>
										<?}?>
										<?$frame->end();?>
									</div>
								</div>
							</td>
						<?}?>
					</tr>
				</table>
			</div>
		</div>
		<div class="wrapp_scrollbar">
			<div class="wr_scrollbar">
				<div class="scrollbar">
					<div class="handle">
						<div class="mousearea"></div>
					</div>
				</div>
			</div>
			<ul class="slider_navigation compare custom_flex">
				<ul class="flex-direction-nav">
					<li class="flex-nav-prev backward"><span class="flex-prev">Previous</span></li>
					<li class="flex-nav-next forward"><span class="flex-next">Next</span></li>
				</ul>
			</ul>
		</div>
	<?}?>
	<?if (!empty($arResult["ALL_FIELDS"]) || !empty($arResult["ALL_PROPERTIES"]) || !empty($arResult["ALL_OFFER_FIELDS"]) || !empty($arResult["ALL_OFFER_PROPERTIES"])){?>
		<div class="bx_filtren_container">
			<ul>
				<?if(!empty($arResult["ALL_FIELDS"])){
					foreach ($arResult["ALL_FIELDS"] as $propCode => $arProp){
						if (!isset($arResult['FIELDS_REQUIRED'][$propCode])){?>
							<li class="button vsmall transparent <?=($arProp["IS_DELETED"] != "N" ? 'visible' : '');?>">
								<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arProp["ACTION_LINK"])?>')">+<?=GetMessage("IBLOCK_FIELD_".$propCode)?></span>
							</li>
						<?}
					}
				}
				if(!empty($arResult["ALL_OFFER_FIELDS"])){
					foreach($arResult["ALL_OFFER_FIELDS"] as $propCode => $arProp){?>
						<li class="button vsmall transparent <?=($arProp["IS_DELETED"] != "N" ? 'visible' : '');?>">
							<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arProp["ACTION_LINK"])?>')">+<?=GetMessage("IBLOCK_FIELD_".$propCode)?></span>
						</li>
					<?}
				}
				if (!empty($arResult["ALL_PROPERTIES"])){
					foreach($arResult["ALL_PROPERTIES"] as $propCode => $arProp){?>
						<li class="button vsmall transparent <?=($arProp["IS_DELETED"] != "N" ? 'visible' : '');?>">
							<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arProp["ACTION_LINK"])?>')">+<?=$arProp["NAME"]?></span>
						</li>
					<?}
				}
				if (!empty($arResult["ALL_OFFER_PROPERTIES"])){
					foreach($arResult["ALL_OFFER_PROPERTIES"] as $propCode => $arProp){?>
						<li class="button vsmall transparent <?=($arProp["IS_DELETED"] != "N" ? 'visible' : '');?>">
							<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arProp["ACTION_LINK"])?>')">+<?=$arProp["NAME"]?></span>
						</li>
					<?}
				}?>
			</ul>
		</div>
	<?}?>
	<?$arUnvisible=array("NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE");?>
		<div class="prop_title_table"></div>
		
		<div class="frame props">
			<div class="wraps">
				<table class="data_table_props compare_view">
					<?if (!empty($arResult["SHOW_FIELDS"])){
						foreach ($arResult["SHOW_FIELDS"] as $code => $arProp){
							if(!in_array($code, $arUnvisible)){
								$showRow = true;
								if (!isset($arResult['FIELDS_REQUIRED'][$code]) || $arResult['DIFFERENT']){
									$arCompare = array();
									foreach($arResult["ITEMS"] as &$arElement){
										$arPropertyValue = $arElement["FIELDS"][$code];
										if (is_array($arPropertyValue)){
											sort($arPropertyValue);
											$arPropertyValue = implode(" / ", $arPropertyValue);
										}
										$arCompare[] = $arPropertyValue;
									}
									unset($arElement);
									$showRow = (count(array_unique($arCompare)) > 1);
								}
								if ($showRow){?>
									<tr>
										<td>
											<?=GetMessage("IBLOCK_FIELD_".$code);?>
											<?if($arResult["ALL_FIELDS"][$code]){?>
												<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arResult["ALL_FIELDS"][$code]["ACTION_LINK"])?>')" class="remove"><i></i></span>
											<?}?>
										</td>
										<?foreach($arResult["ITEMS"] as $arElement){?>
											<td valign="top">
												<?=$arElement["FIELDS"][$code];?>
												
											</td>
										<?}
										unset($arElement);?>
									</tr>
								<?}?>
							<?}?>
						<?}
					}
					if (!empty($arResult["SHOW_OFFER_FIELDS"])){
						foreach ($arResult["SHOW_OFFER_FIELDS"] as $code => $arProp){
							$showRow = true;
							if ($arResult['DIFFERENT']){
								$arCompare = array();
								foreach($arResult["ITEMS"] as &$arElement){
									$Value = $arElement["OFFER_FIELDS"][$code];
									if(is_array($Value)){
										sort($Value);
										$Value = implode(" / ", $Value);
									}
									$arCompare[] = $Value;
								}
								unset($arElement);
								$showRow = (count(array_unique($arCompare)) > 1);
							}
							if ($showRow){?>
								<tr>
									<td>
										<?=GetMessage("IBLOCK_OFFER_FIELD_".$code)?>
										<?if($arResult["ALL_OFFER_FIELDS"][$code]){?>
											<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arResult["ALL_OFFER_FIELDS"][$code]["ACTION_LINK"])?>')" class="remove" title="<?=GetMessage("CATALOG_REMOVE_PRODUCT")?>"><i></i></span>
										<?}?>
									</td>
									<?foreach($arResult["ITEMS"] as &$arElement){?>
										<td>
											<?=(is_array($arElement["OFFER_FIELDS"][$code])? implode("/ ", $arElement["OFFER_FIELDS"][$code]): $arElement["OFFER_FIELDS"][$code])?>
										</td>
									<?}
									unset($arElement);
									?>
								</tr>
							<?}
						}
					}?>
					<?
					if (!empty($arResult["SHOW_PROPERTIES"])){
						foreach ($arResult["SHOW_PROPERTIES"] as $code => $arProperty){
							$showRow = true;
							if ($arResult['DIFFERENT']){
								$arCompare = array();
								foreach($arResult["ITEMS"] as &$arElement){
									$arPropertyValue = $arElement["DISPLAY_PROPERTIES"][$code]["VALUE"];
									if (is_array($arPropertyValue)){
										sort($arPropertyValue);
										$arPropertyValue = implode(" / ", $arPropertyValue);
									}
									$arCompare[] = $arPropertyValue;
								}
								unset($arElement);
								$showRow = (count(array_unique($arCompare)) > 1);
							}
							if ($showRow){?>
								<tr>
									<td>
									<?=$arProperty["NAME"]?>
									<?if($arResult["ALL_PROPERTIES"][$code]){?>
										<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arResult["ALL_PROPERTIES"][$code]["ACTION_LINK"])?>')" class="remove" title="<?=GetMessage("CATALOG_REMOVE_PRODUCT")?>"><i></i></span>
									<?}?>
									</td>
									<?foreach($arResult["ITEMS"] as &$arElement){?>
										<td>
											<?=(is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
										</td>
									<?}
									unset($arElement);
									?>
								</tr>
							<?}
						}
					}
					if (!empty($arResult["SHOW_OFFER_PROPERTIES"])){
						foreach($arResult["SHOW_OFFER_PROPERTIES"] as $code=>$arProperty){
							$showRow = true;
							if ($arResult['DIFFERENT']){
								$arCompare = array();
								foreach($arResult["ITEMS"] as &$arElement){
									$arPropertyValue = $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["VALUE"];
									if(is_array($arPropertyValue)){
										sort($arPropertyValue);
										$arPropertyValue = implode(" / ", $arPropertyValue);
									}
									$arCompare[] = $arPropertyValue;
								}
								unset($arElement);
								$showRow = (count(array_unique($arCompare)) > 1);
							}
							if ($showRow){?>
								<tr>
									<td>
										<?=$arProperty["NAME"]?>
										<?if($arResult["ALL_OFFER_PROPERTIES"][$code]){?>
											<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arResult["ALL_OFFER_PROPERTIES"][$code]["ACTION_LINK"])?>')" class="remove" title="<?=GetMessage("CATALOG_REMOVE_PRODUCT")?>"><i></i></span>
										<?}?>
									</td>
									<?foreach($arResult["ITEMS"] as &$arElement){?>
										<td>
											<?=(is_array($arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
										</td>
									<?}
									unset($arElement);
									?>
								</tr>
							<?}
						}
					}?>
				</table>
			</div>
		</div>
	<?//}?>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$(window).on('resize', function(){
			initSly();
			createTableCompare($('.data_table_props:not(.clone)'), $('.prop_title_table'), $('.data_table_props.clone'));
		});
		// createTableCompare($('.data_table_props'), $('.prop_title_table'), $('.data_table_props.clone'));
		$(window).resize();
	})
</script>
<?if ($isAjax){
	die();
}?>
</div>
<script type="text/javascript">
	var CatalogCompareObj = new BX.Iblock.Catalog.CompareClass("bx_catalog_compare_block");
</script>