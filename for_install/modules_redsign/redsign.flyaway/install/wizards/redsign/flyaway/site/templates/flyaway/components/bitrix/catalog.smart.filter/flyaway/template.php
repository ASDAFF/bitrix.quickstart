<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

//if($arParams['RS_FILTER_TYPE']!='ftype2'){
	//$this->SetViewTarget("smartfilter");
//}
?><div class="filter_mobile-button js_mobile-button hidden-lg hidden-md hidden-sm">
	<?=Loc::getMessage('FILTER_MOBILE_TITLE')?>
	<i class="fa fa-angle-down js-down icon-angle-down"></i>
	<i class="fa fa-angle-up js-up icon-angle-up"></i>
</div><?
?><div class="aroundfilter"><div class="smartfilter <?=$arParams['RS_FILTER_TYPE']?>"><?
	?><form name="<?=$arResult["FILTER_NAME"]."_form"?>" action="<?=$arResult["FORM_ACTION"]?>" method="get"><?
        if (
            $arParams['RS_FILTER_TYPE']!='ftype2' &&
            $arParams['USE_AJAX'] == "Y" &&
            $arParams['INSTANT_RELOAD'] == "Y"
        ) {
            $bxajaxid = $arParams['BXAJAXID'];
           ?><input type="hidden"
                    name="bxajaxid"
                    id="<?="bxajaxid_".$bxajaxid."_3121312"?>"
                    value="<?=$bxajaxid?>"
            ><?
            ?><input type="hidden" name="AJAX_CALL" value="Y" /><?
        }

		foreach ($arResult["HIDDEN"] as $arItem){
			?><input type="hidden" name="<?=$arItem["CONTROL_NAME"]?>" id="<?=$arItem["CONTROL_ID"]?>" value="<?=$arItem["HTML_VALUE"]?>" /><?
		}

		?><ul class="list-unstyled<?if($arParams['RS_FILTER_TYPE']=='ftype2'):?> row<?endif;?>"><?

			//prices
			foreach ($arResult["ITEMS"] as $key=>$arItem) {
				$key = $arItem["ENCODED_ID"];
				if (isset($arItem["PRICE"])) {
					if ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0)
						continue;

					$precision = 2;
					if (Bitrix\Main\Loader::includeModule("currency")):
						$res = CCurrencyLang::GetFormatDescription($arItem["VALUES"]["MIN"]["CURRENCY"]);
						$precision = $res['DECIMALS'];
					endif;

					?><li class="filter__item <?if($arParams['RS_FILTER_TYPE']=='ftype2'):?> col col-sm-12 col-md-4 col-lg-3<?endif;?>"><?
						?><div class="bx_filter_prop <?if($arParams['RS_FILTER_TYPE']!='ftype2'):?>active<?endif;?>"><?
							?><div class="name element bx_filter_name" onclick="smartFilter.hideFilterProps(this)"><?
								if($arItem["FILTER_HINT"] <> "") {
									?><span class="name__icon JS_tip"></span><?
									?><div class="bx_tip_text">
										<i class="fa fa-close"></i>
										<?=$arItem["FILTER_HINT"]?>
									</div><?
								}
								?><?=$arItem["NAME"]?>
							</div><?
							?><div class="body element bx_filter_parameters_box bx_filter_block clearfix"><?
								?>
								<?
								$precision = $arItem["DECIMALS"]? $arItem["DECIMALS"]: 0;
								$step = ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"]) / 4;
								$price1 = number_format($arItem["VALUES"]["MIN"]["VALUE"], $precision, ".", "");
								$price2 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step, $precision, ".", "");
								$price3 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 2, $precision, ".", "");
								$price4 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 3, $precision, ".", "");
								$price5 = number_format($arItem["VALUES"]["MAX"]["VALUE"], $precision, ".", "");
								?>
								<span class="bx_filter_container_modef"></span>
								<div class="bx_filter_parameters_box_container">
									<div class="polovinka">
										<div class="bx_filter_parameters_box_container_block">
											<div class="bx_filter_input_container">
												<div class="input-group">
													<span class="input-group-addon"><?=Loc::getMessage('CT_BCSF_FILTER_FROM')?></span>
													<input
														class="form-control min-price"
														type="text"
														name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
														id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
														value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
														size="5"
														onkeyup="smartFilter.keyup(this)"
														placeholder="<?=$price1?>"
													/>
												</div>
											</div>
										</div>
									</div>
									<div class="polovinka_pad"></div>
									<div class="polovinka">
										<div class="bx_filter_parameters_box_container_block">
											<div class="bx_filter_input_container">
												<div class="input-group">
													<span class="input-group-addon right"><?=Loc::getMessage('CT_BCSF_FILTER_TO')?></span>
													<input
														class="form-control max-price"
														type="text"
														name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
														id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
														value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
														size="5"
														onkeyup="smartFilter.keyup(this)"
														placeholder="<?=$price5?>"
													/>
												</div>
											</div>
										</div>
									</div>

									<div class="clearfix"></div>
									<div class="bx_ui_slider_track" id="drag_track_<?=$key?>">
										<div class="bx_ui_slider_part p1"><span><?=$price1?></span></div>
										<div class="bx_ui_slider_part p2"><span><?=$price2?></span></div>
										<div class="bx_ui_slider_part p3"><span><?=$price3?></span></div>
										<div class="bx_ui_slider_part p4"><span><?=$price4?></span></div>
										<div class="bx_ui_slider_part p5"><span><?=$price5?></span></div>

										<div class="bx_ui_slider_pricebar_VD" style="left: 0;right: 0;" id="colorUnavailableActive_<?=$key?>"></div>
										<div class="bx_ui_slider_pricebar_VN" style="left: 0;right: 0;" id="colorAvailableInactive_<?=$key?>"></div>
										<div class="bx_ui_slider_pricebar_V"  style="left: 0;right: 0;" id="colorAvailableActive_<?=$key?>"></div>
										<div class="bx_ui_slider_range" id="drag_tracker_<?=$key?>"  style="left: 0%; right: 0%;">
											<a class="bx_ui_slider_handle left"  style="left:0;" href="javascript:void(0)" id="left_slider_<?=$key?>"><span></span></a>
											<a class="bx_ui_slider_handle right" style="right:0;" href="javascript:void(0)" id="right_slider_<?=$key?>"><span></span></a>
										</div>
									</div>

									<input
										class="dubl-min-price"
										type="text"
										name="dubl_<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
										id="dubl_<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
										value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
										size="5"
										onkeyup="smartFilter.keyup(this)"
										placeholder="<?=$price1?>"
									/>
									<input
										class="dubl-max-price"
										type="text"
										name="dubl_<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
										id="dubl_<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
										value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
										size="5"
										onkeyup="smartFilter.keyup(this)"
										placeholder="<?=$price5?>"
									/>

								</div>


								<?
								?><a href="#" class="podrob_link"><?echo Loc::getMessage("PODROB_LINK")?></a><?
							?></div><?
						?></div><?
					?></li><?
					$arJsParams = array(
						"leftSlider" => 'left_slider_'.$key,
						"rightSlider" => 'right_slider_'.$key,
						"tracker" => "drag_tracker_".$key,
						"trackerWrap" => "drag_track_".$key,
						"minInputId" => $arItem["VALUES"]["MIN"]["CONTROL_ID"],
						"maxInputId" => $arItem["VALUES"]["MAX"]["CONTROL_ID"],
						"minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
						"maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
						"curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
						"curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
						"fltMinPrice" => intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"] ,
						"fltMaxPrice" => intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"],
						"precision" => $precision,
						"colorUnavailableActive" => 'colorUnavailableActive_'.$key,
						"colorAvailableActive" => 'colorAvailableActive_'.$key,
						"colorAvailableInactive" => 'colorAvailableInactive_'.$key,
					);

					?><script type="text/javascript">
						BX.ready(function(){
							window['trackBar<?=$key?>'] = new BX.Iblock.SmartFilter(<?=CUtil::PhpToJSObject($arJsParams)?>);
						});
					</script><?
				}

			}
			//not prices
			foreach($arResult["ITEMS"] as $key=>$arItem){
				if (
					empty($arItem["VALUES"])
					|| isset($arItem["PRICE"])
				)
					continue;

				if (
					$arItem["DISPLAY_TYPE"] == "A"
					&& (
						$arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0
					)
				)
					continue;

					// search
					if (is_array($arParams['FILTER_SKU_PROP_SEARCH']) && count($arParams['FILTER_SKU_PROP_SEARCH']) > 0 ||
              (in_array($arItem['CODE'], $arParams['FILTER_PROP_SEARCH']) || in_array($arItem['CODE'], $arParams['FILTER_SKU_PROP_SEARCH']))):
						$IS_SEARCHABLE = true;
					else:
						$IS_SEARCHABLE = false;
					endif;
				?><li class="filter__item <?if($arParams['RS_FILTER_TYPE']=='ftype2'):?> col col-sm-12 col-md-4 col-lg-3<?endif;?>"><?
					?><div class="bx_filter_prop <?if($arItem["DISPLAY_EXPANDED"]=="Y" && $arParams['RS_FILTER_TYPE']!='ftype2'):?>active<?endif?>"><?
						?><div class="name element bx_filter_name" onclick="smartFilter.hideFilterProps(this)"><?
							if($arItem["FILTER_HINT"] <> "") {
								?><span class="name__icon JS_tip"></span><?
								?><div class="bx_tip_text">
									<i class="fa fa-close"></i>
									<?=$arItem["FILTER_HINT"]?>
								</div><?
							}
							?><?=$arItem["NAME"]?><?
							/*if($arItem["FILTER_HINT"] <> "") {
								?><span class="hint" id="item_title_hint_<?=$arItem["ID"]?>"><?
									?><i class="fa"></i><?
									?><div class="text"><?=$arItem["FILTER_HINT"]?></div><?
								?></span><?
							}*/
						?></div><?
						?><div class="body element bx_filter_parameters_box bx_filter_block clearfix"><?
							?><span class="bx_filter_container_modef"></span><?
							$arCur = current($arItem["VALUES"]);
							switch ($arItem["DISPLAY_TYPE"])
							{
								case "A"://NUMBERS_WITH_SLIDER
									?>
									<?
									$precision = $arItem["DECIMALS"]? $arItem["DECIMALS"]: 0;
									$step = ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"]) / 4;
									$value1 = number_format($arItem["VALUES"]["MIN"]["VALUE"], $precision, ".", "");
									$value2 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step, $precision, ".", "");
									$value3 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 2, $precision, ".", "");
									$value4 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 3, $precision, ".", "");
									$value5 = number_format($arItem["VALUES"]["MAX"]["VALUE"], $precision, ".", "");
									?>
									<div class="polovinka">
										<div class="bx_filter_parameters_box_container_block">
											<div class="bx_filter_input_container">
												<div class="input-group">
													<span class="input-group-addon"><?=Loc::getMessage('CT_BCSF_FILTER_FROM')?></span>
													<input
														class="form-control min-price"
														type="text"
														name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
														id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
														value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
														size="5"
														onkeyup="smartFilter.keyup(this)"
														placeholder="<?=$value1?>"
													/>
												</div>
											</div>
										</div>
									</div>
									<div class="polovinka_pad"></div>
									<div class="polovinka">
										<div class="bx_filter_parameters_box_container_block">
											<div class="bx_filter_input_container">
												<div class="input-group">
													<span class="input-group-addon"><?=Loc::getMessage('CT_BCSF_FILTER_TO')?></span>
													<input
														class="form-control max-price"
														type="text"
														name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
														id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
														value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
														size="5"
														onkeyup="smartFilter.keyup(this)"
														placeholder="<?=$value5?>"
													/>
												</div>
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="bx_ui_slider_track" id="drag_track_<?=$key?>">
										<div class="bx_ui_slider_part p1"><span><?=$value1?></span></div>
										<div class="bx_ui_slider_part p2"><span><?=$value2?></span></div>
										<div class="bx_ui_slider_part p3"><span><?=$value3?></span></div>
										<div class="bx_ui_slider_part p4"><span><?=$value4?></span></div>
										<div class="bx_ui_slider_part p5"><span><?=$value5?></span></div>

										<div class="bx_ui_slider_pricebar_VD" style="left: 0;right: 0;" id="colorUnavailableActive_<?=$key?>"></div>
										<div class="bx_ui_slider_pricebar_VN" style="left: 0;right: 0;" id="colorAvailableInactive_<?=$key?>"></div>
										<div class="bx_ui_slider_pricebar_V"  style="left: 0;right: 0;" id="colorAvailableActive_<?=$key?>"></div>
										<div class="bx_ui_slider_range" 	id="drag_tracker_<?=$key?>"  style="left: 0;right: 0;">
											<a class="bx_ui_slider_handle left"  style="left:0;" href="javascript:void(0)" id="left_slider_<?=$key?>"><span></span></a>
											<a class="bx_ui_slider_handle right" style="right:0;" href="javascript:void(0)" id="right_slider_<?=$key?>"><span></span></a>
										</div>
									</div>

									<input
										class="dubl-min-price"
										type="text"
										name="dubl_<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
										id="dubl_<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
										value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
										size="5"
										onkeyup="smartFilter.keyup(this)"
										placeholder="<?=$price1?>"
									/>
									<input
										class="dubl-max-price"
										type="text"
										name="dubl_<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
										id="dubl_<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
										value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
										size="5"
										onkeyup="smartFilter.keyup(this)"
										placeholder="<?=$price5?>"
									/>

									<?
									$arJsParams = array(
										"leftSlider" => 'left_slider_'.$key,
										"rightSlider" => 'right_slider_'.$key,
										"tracker" => "drag_tracker_".$key,
										"trackerWrap" => "drag_track_".$key,
										"minInputId" => $arItem["VALUES"]["MIN"]["CONTROL_ID"],
										"maxInputId" => $arItem["VALUES"]["MAX"]["CONTROL_ID"],
										"minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
										"maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
										"curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
										"curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
										"fltMinPrice" => intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"] ,
										"fltMaxPrice" => intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"],
										"precision" => $arItem["DECIMALS"]? $arItem["DECIMALS"]: 0,
										"colorUnavailableActive" => 'colorUnavailableActive_'.$key,
										"colorAvailableActive" => 'colorAvailableActive_'.$key,
										"colorAvailableInactive" => 'colorAvailableInactive_'.$key,
									);
									?>
									<script type="text/javascript">
										BX.ready(function(){
											window['trackBar<?=$key?>'] = new BX.Iblock.SmartFilter(<?=CUtil::PhpToJSObject($arJsParams)?>);
										});
									</script>
									<?
									break;
								case "B"://NUMBERS
									?>
									<div class="polovinka">
										<div class="bx_filter_parameters_box_container_block">
											<div class="bx_filter_input_container">
												<div class="input-group">
													<span class="input-group-addon"><?=Loc::getMessage('CT_BCSF_FILTER_FROM')?></span>
													<input
														class="form-control min-price"
														type="text"
														name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
														id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
														value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
														size="5"
														onkeyup="smartFilter.keyup(this)"
														/>
												</div>
											</div>
										</div>
									</div>
									<div class="polovinka_pad"></div>
									<div class="polovinka">
										<div class="bx_filter_parameters_box_container_block">
											<div class="bx_filter_input_container">
												<div class="input-group">
													<span class="input-group-addon"><?=Loc::getMessage('CT_BCSF_FILTER_TO')?></span>
													<input
														class="form-control max-price"
														type="text"
														name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
														id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
														value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
														size="5"
														onkeyup="smartFilter.keyup(this)"
														/>
													</div>
											</div>
										</div>
									</div>
									<?
									break;
								case "G"://CHECKBOXES_WITH_PICTURES
									?>
									<?foreach ($arItem["VALUES"] as $val => $ar):?>
										<input
											style="display: none"
											type="checkbox"
											name="<?=$ar["CONTROL_NAME"]?>"
											id="<?=$ar["CONTROL_ID"]?>"
											value="<?=$ar["HTML_VALUE"]?>"
											<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
											<? echo $ar["DISABLED"]? 'disabled="disabled"': '' ?>
										/>
										<?
										$class = "";
										if ($ar["CHECKED"])
											$class.= " active";
										if ($ar["DISABLED"])
											$class.= " disabled";
										?>
										<label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="cwp bx_filter_param_label dib<?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'active');">
											<span class="bx_filter_param_btn bx_color_sl">
												<?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
												<span class="bx_filter_btn_color_icon" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
												<?endif?>
											</span>
										</label>
									<?endforeach?>
									<?
									break;
								case "H"://CHECKBOXES_WITH_PICTURES_AND_LABELS
									?>
									<?foreach ($arItem["VALUES"] as $val => $ar):?>
										<input
											style="display: none"
											type="checkbox"
											name="<?=$ar["CONTROL_NAME"]?>"
											id="<?=$ar["CONTROL_ID"]?>"
											value="<?=$ar["HTML_VALUE"]?>"
											<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
											<? echo $ar["DISABLED"]? 'disabled="disabled"': '' ?>
										/>
										<?
										$class = "";
										if ($ar["CHECKED"])
											$class.= " active";
										if ($ar["DISABLED"])
											$class.= " disabled";
										?>
										<label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="cwpal bx_filter_param_label<?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'active');">
											<span class="bx_filter_param_btn bx_color_sl">
												<?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
													<span class="bx_filter_btn_color_icon" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
												<?endif?>
											</span>
											<span class="bx_filter_param_text" title="<?=$ar["VALUE"];?>"><?=$ar["VALUE"];?><?
												if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
													?> <span class="role_count">(<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)</span><?
												endif;
											?></span>
										</label>
									<?endforeach?>
									<?
									break;
								case "P"://DROPDOWN
									$checkedItemExist = false;
									?>
									<div class="bx_filter_select_container">
										<div class="bx_filter_select_block" onclick="smartFilter.showDropDownPopup(this, '<?=CUtil::JSEscape($key)?>')">
											<div class="bx_filter_select_text btn btn-default dropdown-toggle" data-role="currentOption">
												<?
												foreach ($arItem["VALUES"] as $val => $ar)
												{
													if ($ar["CHECKED"]):
														echo $ar["VALUE"];
														$checkedItemExist = true;
													endif;
												}
												if (!$checkedItemExist):
													echo Loc::getMessage("CT_BCSF_FILTER_ALL");
												endif;
												?>
												<i class="fa fa-angle-down hidden-xs icon-angle-down"></i>
											</div>
											<div class="bx_filter_select_arrow"></div>
											<input
												style="display: none"
												type="radio"
												name="<?=$arCur["CONTROL_NAME_ALT"]?>"
												id="<? echo "all_".$arCur["CONTROL_ID"] ?>"
												value=""
											/>
											<?foreach ($arItem["VALUES"] as $val => $ar):?>
												<input
													style="display: none"
													type="radio"
													name="<?=$ar["CONTROL_NAME_ALT"]?>"
													id="<?=$ar["CONTROL_ID"]?>"
													value="<? echo $ar["HTML_VALUE_ALT"] ?>"
													<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
												/>
											<?endforeach?>
											<div class="bx_filter_select_popup" data-role="dropdownContent" style="display: none;">
												<ul class="list-unstyled">
													<li>
														<label for="<?="all_".$arCur["CONTROL_ID"]?>" class="d bx_filter_param_label" data-role="label_<?="all_".$arCur["CONTROL_ID"]?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape("all_".$arCur["CONTROL_ID"])?>')">
															<? echo Loc::getMessage("CT_BCSF_FILTER_ALL"); ?>
														</label>
													</li>
												<?
												foreach ($arItem["VALUES"] as $val => $ar):
													$class = "";
													if ($ar["CHECKED"])
														$class.= " selected";
													if ($ar["DISABLED"])
														$class.= " disabled";
												?>
													<li>
														<label for="<?=$ar["CONTROL_ID"]?>" class="d bx_filter_param_label<?=$class?>" data-role="label_<?=$ar["CONTROL_ID"]?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')"><?=$ar["VALUE"]?></label>
													</li>
												<?endforeach?>
												</ul>
											</div>
										</div>
									</div>
									<?
									break;
								case "R"://DROPDOWN_WITH_PICTURES_AND_LABELS
									?>

									<div class="bx_filter_select_container">
										<div class="bx_filter_select_block" onclick="smartFilter.showDropDownPopup(this, '<?=CUtil::JSEscape($key)?>')">
											<div class="bx_filter_select_text btn btn-default dropdown-toggle" data-role="currentOption">
												<?
												$checkedItemExist = false;
												foreach ($arItem["VALUES"] as $val => $ar):
													if ($ar["CHECKED"]):
													?>
														<?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
															<span class="bx_filter_btn_color_icon" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
														<?endif?>
														<span class="bx_filter_param_text">
															<?=$ar["VALUE"]?>
														</span>
													<?
														$checkedItemExist = true;
													endif;
												endforeach;
												if (!$checkedItemExist):
													?><span class="bx_filter_btn_color_icon all"></span> <?
													echo Loc::getMessage("CT_BCSF_FILTER_ALL");
												endif;
												?>
												<i class="fa fa-angle-down hidden-xs icon-angle-down"></i>
												</div>
											<div class="bx_filter_select_arrow"></div>
											<input
												style="display: none"
												type="radio"
												name="<?=$arCur["CONTROL_NAME_ALT"]?>"
												id="<? echo "all_".$arCur["CONTROL_ID"] ?>"
												value=""
											/>
											<?foreach ($arItem["VALUES"] as $val => $ar):?>
												<input
													style="display: none"
													type="radio"
													name="<?=$ar["CONTROL_NAME_ALT"]?>"
													id="<?=$ar["CONTROL_ID"]?>"
													value="<?=$ar["HTML_VALUE_ALT"]?>"
													<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
												/>
											<?endforeach?>
											<div class="bx_filter_select_popup" data-role="dropdownContent" style="display: none">
												<ul class="list-unstyled">
													<li>
														<label for="<?="all_".$arCur["CONTROL_ID"]?>" class="dwpal bx_filter_param_label" data-role="label_<?="all_".$arCur["CONTROL_ID"]?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape("all_".$arCur["CONTROL_ID"])?>')">
															<span class="bx_filter_btn_color_icon all"></span>
															<? echo Loc::getMessage("CT_BCSF_FILTER_ALL"); ?>
														</label>
													</li>
												<?
												foreach ($arItem["VALUES"] as $val => $ar):
													$class = "";
													if ($ar["CHECKED"])
														$class.= " selected";
													if ($ar["DISABLED"])
														$class.= " disabled";
												?>
													<li>
														<label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="dwpal bx_filter_param_label<?=$class?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')">
															<?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
																<span class="bx_filter_btn_color_icon" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
															<?endif?>
															<span class="bx_filter_param_text">
																<?=$ar["VALUE"]?>
															</span>
														</label>
													</li>
												<?endforeach?>
												</ul>
											</div>
										</div>
									</div>
									<?
									break;
								case "K"://RADIO_BUTTONS
									?>
									<?if($IS_SEARCHABLE):
										?><div class="around_f_search">
											<input type="text" class="f_search gui-input search__input form-control" name="f_search" id="f_search" value="" placeholder="<?=GetMessage('FILTR_SEARHC')?>">
										</div><?
										endif;
									?>
									<div class="js-box-filter">
										<div class="gui-box js-item-filter">
											<label class="gui-radiobox" for="<? echo "all_".$arCur["CONTROL_ID"] ?>">
												<input class="gui-radiobox-item" type="radio" value="" name="<? echo $arCur["CONTROL_NAME_ALT"] ?>" id="<? echo "all_".$arCur["CONTROL_ID"] ?>" onclick="smartFilter.click(this)">
												<span class="gui-out">
													<span class="gui-inside"></span>
												</span>
												<span class="js-name-filter"><? echo Loc::getMessage("CT_BCSF_FILTER_ALL"); ?></span>
											</label>
										</div>
										<?foreach($arItem["VALUES"] as $val => $ar):?>
											<div class="gui-box js-item-filter">
												<label data-role="label_<?=$ar["CONTROL_ID"]?>" class="gui-radiobox<? echo $ar["DISABLED"]? ' disabled': '' ?>" for="<? echo $ar["CONTROL_ID"] ?>" >
													<input class="gui-radiobox-item" type="radio" value="<? echo $ar["HTML_VALUE_ALT"] ?>" name="<? echo $ar["CONTROL_NAME_ALT"] ?>" id="<? echo $ar["CONTROL_ID"] ?>" <? echo $ar["CHECKED"]? 'checked="checked"': '' ?> onclick="smartFilter.click(this)" <?=$ar["DISABLED"]? 'disabled="disabled"': ''; ?>>
													<span class="gui-out">
														<span class="gui-inside"></span>
													</span>
													<span class="js-name-filter"><?=$ar["VALUE"];?></span>
												</label>
											</div>

										<?endforeach;?>
									</div>
									<?
									break;
								case "U"://CALENDAR
									?>
									<div class="polovinka polovinka_calendar left">
										<div class="bx_filter_parameters_box_container_block"><div class="bx_filter_input_container bx_filter_calendar_container from">
											<?$APPLICATION->IncludeComponent(
												'bitrix:main.calendar',
												'filter',
												array(
													'FORM_NAME' => $arResult["FILTER_NAME"]."_form",
													'SHOW_INPUT' => 'Y',
													'INPUT_ADDITIONAL_ATTR' => 'class="form-control calendar" placeholder="'.FormatDate("SHORT", $arItem["VALUES"]["MIN"]["VALUE"]).'" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
													'INPUT_NAME' => $arItem["VALUES"]["MIN"]["CONTROL_NAME"],
													'INPUT_VALUE' => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
													'SHOW_TIME' => 'N',
													'HIDE_TIMEBAR' => 'Y',
												),
												null,
												array('HIDE_ICONS' => 'Y')
											);?>
										</div></div>
									</div>
									<div class="polovinka polovinka_calendar">
										<div class="bx_filter_parameters_box_container_block"><div class="bx_filter_input_container bx_filter_calendar_container to">
											<?$APPLICATION->IncludeComponent(
												'bitrix:main.calendar',
												'filter',
												array(
													'FORM_NAME' => $arResult["FILTER_NAME"]."_form",
													'SHOW_INPUT' => 'Y',
													'INPUT_ADDITIONAL_ATTR' => 'class="form-control calendar" placeholder="'.FormatDate("SHORT", $arItem["VALUES"]["MAX"]["VALUE"]).'" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
													'INPUT_NAME' => $arItem["VALUES"]["MAX"]["CONTROL_NAME"],
													'INPUT_VALUE' => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
													'SHOW_TIME' => 'N',
													'HIDE_TIMEBAR' => 'Y',
												),
												null,
												array('HIDE_ICONS' => 'Y')
											);?>
										</div></div>
									</div>
									<?
									break;
								default://CHECKBOXES
									?>
									<?if ($IS_SEARCHABLE):
                    ?><div class="around_f_search">
                      <input type="text" class="f_search gui-input search__input form-control" name="f_search" id="f_search" value="" placeholder="<?=GetMessage('FILTR_SEARHC')?>">
                    </div><?
                    endif;
                  ?>
                  <div class="js-box-filter">
										<?foreach($arItem["VALUES"] as $val => $ar):?>
											<div class="js-item-filter">
												<label data-role="label_<?=$ar["CONTROL_ID"]?>" class="gui-checkbox <? echo $ar["DISABLED"] ? 'disabled': '' ?>" for="<? echo $ar["CONTROL_ID"] ?>">
													<input
															class="gui-checkbox-input"
															type="checkbox"
															value="<? echo $ar["HTML_VALUE"] ?>"
															name="<? echo $ar["CONTROL_NAME"] ?>"
															id="<? echo $ar["CONTROL_ID"] ?>"
															<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
															onclick="smartFilter.click(this)"
															<? echo $ar["DISABLED"] ? 'disabled': '' ?>
														/>
														<span class="gui-checkbox-icon"></span>
														<span class="js-name-filter"><?=$ar["VALUE"];?></span>
												</label>
											</div>
										<?endforeach;?>
									</div>
							<?
							}
							?><a href="javascript:;" class="podrob_link"><?echo Loc::getMessage("PODROB_LINK")?></a><?
						?></div><?
					?></div><?
				?></li><?
			}

			?><li class="<?if($arParams['RS_FILTER_TYPE']=='ftype2'):?> col col-xs-12<?endif;?>"><?
				?><div class="buttons text-center bx_filter_prop"><?
					?><a href="<?echo $arResult["FILTER_URL"]?>" class="bx_filter_popup_result <?=$arParams["POPUP_POSITION"]?>" id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])) echo 'style="display:none"';?> style="display: inline-block;"><?
						?><?echo Loc::getMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?><?
						?><span class="arrow"><span></span></span><?
						?><?echo Loc::getMessage("CT_BCSF_FILTER_SHOW")?><?
					?></a><?
					?><input type="submit" class="btn btn-default btn-button bx_filter_search_reset" id="del_filter" name="del_filter" value="<?=Loc::getMessage("CT_BCSF_DEL_FILTER")?>"/>&nbsp;<?
					?><input class="btn btn2 bx_filter_search_button" type="submit" id="set_filter" name="set_filter" value="<?=Loc::getMessage("CT_BCSF_SET_FILTER")?>" /><?
				?></div><?
			?></li><?

		?></ul><?
	?></form><?
?></div></div><?
?><script>
	var smartFilter = new JCSmartFilter('<?=CUtil::JSEscape($arResult["FORM_ACTION"])?>', '<?=CUtil::JSEscape($arParams["RS_FILTER_TYPE"])?>', <?=CUtil::PhpToJSObject($arResult["JS_FILTER_PARAMS"])?>);
</script><?

/*if($arParams['RS_FILTER_TYPE']!='ftype2'){
	$this->EndViewTarget();
}*/
