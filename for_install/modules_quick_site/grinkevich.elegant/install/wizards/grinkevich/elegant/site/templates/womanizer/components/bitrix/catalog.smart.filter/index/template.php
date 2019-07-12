<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>




<div class="right-block">
<div class="rb-wrap">
<div class="rb-wrap">


		<h2><?= GetMessage("CT_BCSF_FILTER_TITLE")?></h2>

		<form action="" method="get" id="smart-filter-form">
			<?foreach($arResult["HIDDEN"] as $arItem):?>
				<input type="hidden" name="<?echo $arItem["CONTROL_NAME"]?>" id="<?echo $arItem["CONTROL_ID"]?>" value="<?echo $arItem["HTML_VALUE"]?>"/>
			<?endforeach;?>
									<div id="right-form">
										<div class="item">
											<span class="lb"><?= GetMessage("CT_BCSF_FILTER_TOV")?></span>

											<?


											$aMenuLinksExt = $APPLICATION->IncludeComponent("bitrix:menu.sections","",Array(
														"IS_SEF" => "Y",
														"SEF_BASE_URL" => SITE_DIR . "catalog/",
														"SECTION_PAGE_URL" => "#SECTION_CODE#/",
														"DETAIL_PAGE_URL" => "#SECTION_CODE#/#ELEMENT_ID#",
														"IBLOCK_TYPE" => "catalog",
														"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
														"DEPTH_LEVEL" => "2",
														"CACHE_TYPE" => "A",
														"CACHE_TIME" => "3600"
													)
												);


											?>

											<select id="filter-catalog-sections-select">
												<? $ru = getEnv('REQUEST_URI'); foreach($aMenuLinksExt as $iInd=>$arItem): $class = '';
													if (strpos($ru, $arItem[1]) !== false)
														$class = ' selected="selected"';
													$sep = ' ' . str_repeat('-', $arItem[3]['DEPTH_LEVEL'] - 1) . ' ';
												 ?>
												 <option value="<?=$arItem[1]?>"<?=$class;?>><?=$sep.$arItem[0]?></option>
											   	<? endforeach; ?>
											</select>


										</div>



		<?foreach($arResult["ITEMS"] as $key => $arItem): ?>
			<?if (isset($arItem["PRICE"])):?>
				<?
				if (empty($arItem["VALUES"]["MIN"]["VALUE"]))
					$arItem["VALUES"]["MIN"]["VALUE"] = 0;
				if (empty($arItem["VALUES"]["MAX"]["VALUE"]))
					$arItem["VALUES"]["MAX"]["VALUE"] = 100000;
				$arItem["VALUES"]["MAX"]["VALUE"] = number_format($arItem["VALUES"]["MAX"]["VALUE"], 0, '', '');
				$arItem["VALUES"]["MIN"]["VALUE"] = number_format($arItem["VALUES"]["MIN"]["VALUE"], 0, '', '');
				if( empty( $arItem["VALUES"]["MIN"]["HTML_VALUE"] ) )
					$arItem["VALUES"]["MIN"]["HTML_VALUE"] = $arItem["VALUES"]["MIN"]["VALUE"];
				if( empty( $arItem["VALUES"]["MAX"]["HTML_VALUE"] ) )
					$arItem["VALUES"]["MAX"]["HTML_VALUE"] = $arItem["VALUES"]["MAX"]["VALUE"];
				?>
				<div class="item">
											<span class="lb"><?= $arItem["NAME"]; ?></span>
											<div class="s-wrap">
												<div class="sliders">
													<span id="min-count"></span>
													<span id="max-count"></span>
												</div>
												<?= GetMessage("CT_BCSF_FILTER_FROM")?>
												<input type="text" name="<?= $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>" id="min-inp" value="<?=$arItem["VALUES"]["MIN"]["HTML_VALUE"];?>" />
												<?= GetMessage("CT_BCSF_FILTER_TO")?>
												<input type="text" name="<?= $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>" id="max-inp" value="<?=$arItem["VALUES"]["MAX"]["HTML_VALUE"];?>" />
												<span class="rubl">A</span>

												<input type="hidden" id="min-val" value="<?=$arItem["VALUES"]["MIN"]["VALUE"];?>" />
												<input type="hidden" id="max-val" value="<?=$arItem["VALUES"]["MAX"]["VALUE"];?>" />
												<input type="hidden" id="m-step" value="5" />

											</div>
				</div>
			<?endif;?>
		<?endforeach;?>


		<?foreach($arResult["ITEMS"] as $key => $arItem): ?>
			<?if(!empty($arItem["PROPERTY_TYPE"]) && ($arItem['PROPERTY_TYPE'] == 'L' || $arItem['PROPERTY_TYPE'] == 'E') && sizeof($arItem["VALUES"]) > 1):  ?>
				<div class="item">
					<span class="lb"><?= $arItem["NAME"]; ?></span>
					<select data-inpid="<?= $arItem["CODE"]?>" class="chzn-select">
						<option data-name="" value=""></option>
						<? $arCName = $arCValue = ''; ?>
						<?foreach($arItem["VALUES"] as $val => $ar):?>
							<?  if ($ar["CHECKED"]) {$arCName =  $ar["CONTROL_NAME"]; $arCValue = $ar["HTML_VALUE"]; } ?>
							<option data-name="<?echo $ar["CONTROL_NAME"]?>" value="<?= $ar["VALUE"];?>" <?= ($ar["CHECKED"] ? 'selected="selected"': '') ?>><?= $ar["VALUE"];?></option>
						<?endforeach;?>
					</select>
					<input type="hidden" name="" id="in<?=$arItem["CODE"]?>" value="" />
				</div>
			<?endif;?>
		<?endforeach;?>


										<div class="but">
											<a class="button orange" rel="smart-filter-form"><span><img src="<?=SITE_TEMPLATE_PATH?>/images/s_but_ico.png" alt="" /><?= GetMessage("CT_BCSF_SEARCH2")?></span></a>
											<input type="hidden" name="set_filter" value="1" />
											<input type="image" src="<?=SITE_TEMPLATE_PATH?>/images/s_but_ico.png" width="1" height="1" />
										</div>
									</div>
								</form>


</div>
</div>
</div>