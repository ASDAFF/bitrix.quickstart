<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);
?><form name="<?=$arResult['FILTER_NAME'].'_form'?>" action="<?=$arResult['FORM_ACTION']?>" method="get" class="smartfilter" onsubmit="return RSGoPro_FilterOnSubmitForm();"><?
	foreach($arResult['HIDDEN'] as $arItem) {
		?><input <?
			?>type="hidden" <?
			?>name="<?=$arItem['CONTROL_NAME']?>" <?
			?>id="<?=$arItem['CONTROL_ID']?>" <?
			?>value="<?=$arItem['HTML_VALUE']?>" <?
		?>/> <?
	}
	?><div class="around_filtren"><?
		?><div class="filtren clearfix<?
			if($arParams['FILTER_FIXED']=='Y'):?> filterfixed<?endif;?><?
			if($arParams['FILTER_USE_AJAX']=='Y'):?> ajaxfilter<?endif;?><?
			echo ' '.$arParams['FILTER_DISABLED_PIC_EFFECT'];
			?>"><?
			?><div class="title"><?
				?><a class="shhi" href="#"><span class="show"><?=GetMessage('CT_BCSF_FILTER_TITLE_SHOW')?></span><span class="hide"><?=GetMessage('CT_BCSF_FILTER_TITLE_HIDE')?></span></a><?
				if($arParams['USE_COMPARE']=='Y') {
					?><span class="filtercompare"><?=GetMessage('FILTER_COMPARE')?>: <a href="#"></a></span><?
				}
			?></div><?
			?><div class="body"><?
				?><ul class="clearfix"><?

				// prices
				foreach($arResult['ITEMS'] as $key=>$arItem) {
					$key = $arItem["ENCODED_ID"];
					if(isset($arItem["PRICE"])) {
						if (!$arItem["VALUES"]["MIN"]["VALUE"] || !$arItem["VALUES"]["MAX"]["VALUE"] || $arItem["VALUES"]["MIN"]["VALUE"] == $arItem["VALUES"]["MAX"]["VALUE"]) {
							continue;
						}
						$precision = 2;
						if (Bitrix\Main\Loader::includeModule("currency"))
						{
							$res = CCurrencyLang::GetFormatDescription($arItem["VALUES"]["MIN"]["CURRENCY"]);
							$precision = $res['DECIMALS'];
						}
						?><li class="lvl1<?if($IS_SCROLABLE):?> scrolable<?endif;?><?if($IS_SEARCHABLE):?> searcheble<?endif;?>" data-propid="<?=$arItem['ID']?>" data-propcode="<?=$arItem['CODE']?>"><?
							?><a href="#" class="showchild"><i class="icon pngicons"></i><?=$arItem['NAME']?></a><?
							?><ul class="property number"><?
								?><div class="inputs"><?
									?><span class="from"><?=GetMessage('CT_BCSF_FILTER_FROM')?></span><?
									?><input
										class="min-price min"
										type="text"
										name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
										id="<?=$arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
										value="<?=$arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
										size="9"
										onkeyup="smartFilter.keyup(this)"
									/><?
									?><span class="separator"></span><?
									?><span class="to"><?=GetMessage("CT_BCSF_FILTER_TO")?></span><?
									?><input
										class="max-price max"
										type="text"
										name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
										id="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
										value="<?=$arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
										size="9"
										onkeyup="smartFilter.keyup(this)"
									/><?
								?></div><?
								?><div class="aroundslider"><?
									?><div style="clear: both;"></div><?
									?><div class="bx_ui_slider_track" id="drag_track_<?=$key?>"><?
										$precision = $arItem["DECIMALS"]? $arItem["DECIMALS"]: 0;
										$step = ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"]) / 4;
										$price1 = number_format($arItem["VALUES"]["MIN"]["VALUE"], $precision, ".", "");
										$price2 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step, $precision, ".", "");
										$price3 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 2, $precision, ".", "");
										$price4 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 3, $precision, ".", "");
										$price5 = number_format($arItem["VALUES"]["MAX"]["VALUE"], $precision, ".", "");
										?><div class="bx_ui_slider_part p1"><span><?=$price1?></span></div><?
										?><div class="bx_ui_slider_part p2"><span><?=$price2?></span></div><?
										?><div class="bx_ui_slider_part p3"><span><?=$price3?></span></div><?
										?><div class="bx_ui_slider_part p4"><span><?=$price4?></span></div><?
										?><div class="bx_ui_slider_part p5"><span><?=$price5?></span></div><?
										?><div class="bx_ui_slider_pricebar_VD" style="left: 0;right: 0;" id="colorUnavailableActive_<?=$key?>"></div><?
										?><div class="bx_ui_slider_pricebar_VN" style="left: 0;right: 0;" id="colorAvailableInactive_<?=$key?>"></div><?
										?><div class="bx_ui_slider_pricebar_V"  style="left: 0;right: 0;" id="colorAvailableActive_<?=$key?>"></div><?
										?><div class="bx_ui_slider_range" 	id="drag_tracker_<?=$key?>"  style="left: 0;right: 0;"><?
											?><a class="bx_ui_slider_handle left pngicons"  style="left:0;" href="javascript:void(0)" id="left_slider_<?=$key?>"></a><?
											?><a class="bx_ui_slider_handle right pngicons" style="right:0;" href="javascript:void(0)" id="right_slider_<?=$key?>"></a><?
										?></div><?
									?></div><?
								?></div><?
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
							?></ul><?
							?><script type="text/javascript">
								BX.ready(function(){
									window['trackBar<?=$key?>'] = new BX.Iblock.SmartFilter(<?=CUtil::PhpToJSObject($arJsParams)?>);
								});
							</script><?
						?></li><?
					}
				}

				// simple
				foreach($arResult['ITEMS'] as $key=>$arItem) {
					if( empty($arItem['VALUES']) || isset($arItem['PRICE']) ) {
						continue;
					}

					if(
						$arItem["DISPLAY_TYPE"] == "A"
						&& (
							!$arItem["VALUES"]["MIN"]["VALUE"]
							|| !$arItem["VALUES"]["MAX"]["VALUE"]
							|| $arItem["VALUES"]["MIN"]["VALUE"] == $arItem["VALUES"]["MAX"]["VALUE"]
						)
					) {
						continue;
					}
					// scroll
					if(in_array($arItem['CODE'],$arParams['FILTER_PROP_SCROLL']) || in_array($arItem['CODE'],$arParams['FILTER_SKU_PROP_SCROLL'])) {
						$IS_SCROLABLE = true;
					} else {
						$IS_SCROLABLE = false;
					}
					// search
					if(in_array($arItem['CODE'],$arParams['FILTER_PROP_SEARCH']) || in_array($arItem['CODE'],$arParams['FILTER_SKU_PROP_SEARCH'])) {
						$IS_SEARCHABLE = true;
					} else {
						$IS_SEARCHABLE = false;
					}
					?><li class="lvl1<?if($IS_SCROLABLE):?> scrolable<?endif;?><?if($IS_SEARCHABLE):?> searcheble<?endif;?><?if($arItem["DISPLAY_EXPANDED"]!="Y"):?> closed<?endif?>" data-propid="<?=$arItem['ID']?>" data-propcode="<?=$arItem['CODE']?>"><?
						?><a href="#" class="showchild"><i class="icon pngicons"></i><?=$arItem['NAME']?><?if($arItem['FILTER_HINT']<>''):?><span class="hint">?<div><?=$arItem['FILTER_HINT']?></div></span><?endif;?></a><?
						$arCur = current($arItem["VALUES"]);
						switch ($arItem["DISPLAY_TYPE"]) {
							case "A"://NUMBERS_WITH_SLIDER
								?><ul class="property number"><?
									?><div class="inputs"><?
										?><span class="from"><?=GetMessage('CT_BCSF_FILTER_FROM')?></span><?
										?><input
											class="min-price min"
											type="text"
											name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
											id="<?=$arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
											value="<?=$arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
											size="9"
											onkeyup="smartFilter.keyup(this)"
										/><?
										?><span class="separator"></span><?
										?><span class="to"><?=GetMessage("CT_BCSF_FILTER_TO")?></span><?
										?><input
											class="max-price max"
											type="text"
											name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
											id="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
											value="<?=$arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
											size="9"
											onkeyup="smartFilter.keyup(this)"
										/><?
									?></div><?
									?><div class="aroundslider"><?
										?><div style="clear: both;"></div><?
										?><div class="bx_ui_slider_track" id="drag_track_<?=$key?>"><?
											$precision = $arItem["DECIMALS"]? $arItem["DECIMALS"]: 0;
											$step = ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"]) / 4;
											$value1 = number_format($arItem["VALUES"]["MIN"]["VALUE"], $precision, ".", "");
											$value2 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step, $precision, ".", "");
											$value3 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 2, $precision, ".", "");
											$value4 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 3, $precision, ".", "");
											$value5 = number_format($arItem["VALUES"]["MAX"]["VALUE"], $precision, ".", "");
											?><div class="bx_ui_slider_part p1"><span><?=$value1?></span></div><?
											?><div class="bx_ui_slider_part p2"><span><?=$value2?></span></div><?
											?><div class="bx_ui_slider_part p3"><span><?=$value3?></span></div><?
											?><div class="bx_ui_slider_part p4"><span><?=$value4?></span></div><?
											?><div class="bx_ui_slider_part p5"><span><?=$value5?></span></div><?
											?><div class="bx_ui_slider_pricebar_VD" style="left: 0;right: 0;" id="colorUnavailableActive_<?=$key?>"></div><?
											?><div class="bx_ui_slider_pricebar_VN" style="left: 0;right: 0;" id="colorAvailableInactive_<?=$key?>"></div><?
											?><div class="bx_ui_slider_pricebar_V"  style="left: 0;right: 0;" id="colorAvailableActive_<?=$key?>"></div><?
											?><div class="bx_ui_slider_range" 	id="drag_tracker_<?=$key?>"  style="left: 0;right: 0;"><?
												?><a class="bx_ui_slider_handle left pngicons"  style="left:0;" href="javascript:void(0)" id="left_slider_<?=$key?>"></a><?
												?><a class="bx_ui_slider_handle right pngicons" style="right:0;" href="javascript:void(0)" id="right_slider_<?=$key?>"></a><?
											?></div><?
										?></div><?
									?></div><?
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
								?></ul><?
								?><script type="text/javascript">
									BX.ready(function(){
										window['trackBar<?=$key?>'] = new BX.Iblock.SmartFilter(<?=CUtil::PhpToJSObject($arJsParams)?>);
									});
								</script><?
								break;
							case "B"://NUMBERS
								?><ul class="property number"><?
									?><div class="inputs"><?
										?><span class="from"><?=GetMessage('CT_BCSF_FILTER_FROM')?></span><?
										?><input <?
											?>class="min-price min" <?
											?>type="text" <?
											?>name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>" <?
											?>id="<?=$arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" <?
											?>value="<?=$arItem["VALUES"]["MIN"]["HTML_VALUE"]?>" <?
											?>size="9" <?
											?>onkeyup="smartFilter.keyup(this)" <?
										?>/><?
										?><span class="separator"></span><?
										?><span class="to"><?=GetMessage("CT_BCSF_FILTER_TO")?></span><?
										?><input <?
											?>class="max-price max" <?
											?>type="text" <?
											?>name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>" <?
											?>id="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>" <?
											?>value="<?=$arItem["VALUES"]["MAX"]["HTML_VALUE"]?>" <?
											?>size="9" <?
											?>onkeyup="smartFilter.keyup(this)" <?
										?>/><?
									?></div><?
								?></ul><?
								break;
							case "G"://CHECKBOXES_WITH_PICTURES
								?><ul class="property cwp"><?
									foreach ($arItem["VALUES"] as $val => $ar) {
										$class = "";
										if($ar["CHECKED"]) {
											$class.= " active";
										}
										if($ar["DISABLED"]) {
											$class.= " disabled";
										}
										$onclick = "smartFilter.keyup(BX('".CUtil::JSEscape($ar["CONTROL_ID"])."'));";
										?><li class="lvl2"><?
											?><div class="<?=$class?>"><?
												?><span><input <?
													?>type="checkbox" <?
													?>name="<?=$ar["CONTROL_NAME"]?>" <?
													?>id="<?=$ar["CONTROL_ID"]?>" <?
													?>value="<?=$ar["HTML_VALUE"]?>" <?
													?><? echo $ar["CHECKED"]? 'checked="checked"': '' ?> <?
													?>onclick="<?=$onclick?>" <?
												?>/></span><?
												?><label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="pic <?=$class?>"><?
													if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])) {
														?><span style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span><?
													}
												?></label><?
											?></div><?
										?></li><?
									}
								?></ul><?
								break;
							case "H"://CHECKBOXES_WITH_PICTURES_AND_LABELS
								?><ul class="property cwpal"><?
									// search
									if($IS_SEARCHABLE && !$IS_COLOR) {
										?><div class="around_f_search"><input type="text" class="f_search" name="f_search" id="f_search" value="" placeholder="<?=GetMessage('FILTR_SEARHC')?>"></div><?
									}
									// scroll
									if($IS_SCROLABLE && count($arItem['VALUES'])>$arParams['FILTER_PROP_SCROLL_CNT']) {
										$IS_SCROLABLE = true;
									} else {
										$IS_SCROLABLE = false;
									}
									if($IS_SCROLABLE) {
										?><div class="f_jscrollpane" id="f_scroll_<?=$arItem['ID']?>"><?
									}
									foreach ($arItem["VALUES"] as $val => $ar) {
										$class = "";
										if($ar["CHECKED"]) {
											$class.= " active";
										}
										if($ar["DISABLED"]) {
											$class.= " disabled";
										}
										$onclick = "smartFilter.keyup(BX('".CUtil::JSEscape($ar["CONTROL_ID"])."'));";
										?><li class="lvl2"><?
											?><div class="clearfix <?=$class?>"><?
												?><span><input <?
													?>type="checkbox" <?
													?>name="<?=$ar["CONTROL_NAME"]?>" <?
													?>id="<?=$ar["CONTROL_ID"]?>" <?
													?>value="<?=$ar["HTML_VALUE"]?>" <?
													?><? echo $ar["CHECKED"]? 'checked="checked"': '' ?> <?
													?>onclick="<?=$onclick?>" <?
												?>/></span><?
												?><label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="pic <?=$class?>"><?
													if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])) {
														?><span style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span><?
													}
												?></label><?
												?><label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="name <?=$class?>"><?
													?><span class="val"><?=$ar["VALUE"]?></span><?
												?></label><?
											?></div><?
										?></li><?
									}
									// scroll
									if($IS_SCROLABLE) {
										?></div><?
									}
								?></ul><?
								break;
							case "P"://DROPDOWN
								?><ul class="property dd"><?
									?><li class="dropdown" id="fltr_<?=$arItem['CODE']?>"><ul class="dropdown"><?
										$arChecked = array();
										$ar = current($arItem["VALUES"]);
										?><li class="lvl2"><?
											?><div><?
												?><span><input <?
													?>type="radio" <?
													?>value="" <?
													?>name="<?=$ar["CONTROL_NAME_ALT"]?>" <?
													?>id="all_<?=$ar["CONTROL_NAME_ALT"]?>" <?
													?><? echo $ar["CHECKED"]? 'checked="checked"': '' ?><?
													?>onclick="smartFilter.click(this)" <?
												?>/></span><?
												?><label for="all_<?=$ar["CONTROL_NAME_ALT"]?>" data-role="label_<?=$arCur["CONTROL_ID"]?>" class="name"><?
													?><span class="val"><?=GetMessage("CT_BCSF_FILTER_ALL")?></span><?
													?><i class="icon pngicons"></i><?
												?></label><?
											?></div><?
										?></li><?
										foreach($arItem["VALUES"] as $val => $ar) {
											$class = "";
											if($ar["CHECKED"]) {
												$class.= " active";
											}
											if($ar["DISABLED"]) {
												$class.= " disabled";
											}
											?><li class="lvl2"><?
												?><div class="<?=$class?>"><?
													?><span><input <?
														?>type="radio" <?
														?>value="<?=$ar["HTML_VALUE_ALT"]?>" <?
														?>name="<?=$ar["CONTROL_NAME_ALT"]?>" <?
														?>id="<?=$ar["CONTROL_ID"]?>" <?
														?><? echo $ar["CHECKED"]? 'checked="checked"': '' ?><?
														?>onclick="smartFilter.click(this)" <?
													?>/></span><?
													?><label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="name <?=$class?>"><?
														?><span class="val"><?=$ar["VALUE"]?></span><?
													?></label><?
												?></div><?
											?></li><?
											if($ar["CHECKED"]) {
												$arChecked = $ar;
											}
										}
									?></ul></li><?
									?><li class="lvl2 selected"><?
										?><div><?
											?><label data-role="label_<?=$arCur["CONTROL_ID"]?>" class="name select"><?
												?><span><?=( isset($arChecked['VALUE']) ? $arChecked['VALUE'] : GetMessage("CT_BCSF_FILTER_ALL") )?></span><?
												?><i class="icon pngicons"></i><?
											?></label><?
										?></div><?
									?></li><?
								?></ul><?
								break;
							case "R"://DROPDOWN_WITH_PICTURES_AND_LABELS
								?><ul class="property dd wpal"><?
									?><li class="dropdown" id="fltr_<?=$arItem['CODE']?>"><ul class="dropdown"><?
										$arChecked = array();
										$ar = current($arItem["VALUES"]);
										?><li class="lvl2 clearfix"><?
											?><div><?
												?><span><input <?
													?>type="radio" <?
													?>value="" <?
													?>name="<?=$ar["CONTROL_NAME_ALT"]?>" <?
													?>id="all_<?=$ar["CONTROL_NAME_ALT"]?>" <?
													?><? echo $ar["CHECKED"]? 'checked="checked"': '' ?><?
													?>onclick="smartFilter.click(this)" <?
												?>/></span><?
												?><label for="all_<?=$ar["CONTROL_NAME_ALT"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="pic <?=$class?>"><?
													if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])) {
														?><span class="nopic"></span><?
													}
												?></label><?
												?><label for="all_<?=$ar["CONTROL_NAME_ALT"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="name <?=$class?>"><?
													?><span class="val"><?=GetMessage("CT_BCSF_FILTER_ALL")?></span><?
												?></label><?
											?></div><?
										?></li><?
										foreach($arItem["VALUES"] as $val => $ar) {
											$class = "";
											if($ar["CHECKED"]) {
												$class.= " active";
											}
											if($ar["DISABLED"]) {
												$class.= " disabled";
											}
											?><li class="lvl2 clearfix"><?
												?><div class="<?=$class?>"><?
													?><span><input <?
														?>type="radio" <?
														?>value="<?=$ar["HTML_VALUE_ALT"]?>" <?
														?>name="<?=$ar["CONTROL_NAME_ALT"]?>" <?
														?>id="<?=$ar["CONTROL_ID"]?>" <?
														?><? echo $ar["CHECKED"]? 'checked="checked"': '' ?><?
														?>onclick="smartFilter.click(this)" <?
													?>/></span><?
													?><label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="pic <?=$class?>"><?
														if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])) {
															?><span style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span><?
														}
													?></label><?
													?><label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="name <?=$class?>"><?
														?><span class="val"><?=$ar["VALUE"]?></span><?
													?></label><?
												?></div><?
											?></li><?
											if($ar["CHECKED"]) {
												$arChecked = $ar;
											}
										}
									?></ul></li><?
									?><li class="lvl2 selected"><?
										?><div><?
											?><label data-role="label_<?=$arChecked["CONTROL_ID"]?>" class="pic select"><?
												if (isset($arChecked["FILE"]) && !empty($arChecked["FILE"]["SRC"])) {
													?><span style="background-image:url('<?=$arChecked["FILE"]["SRC"]?>');"></span><?
												} else {
													?><span class="nopic"></span><?
												}
											?></label><?
											?><label data-role="label_<?=$arChecked["CONTROL_ID"]?>" class="name select"><?
												?><span><?
													?><?=( isset($arChecked['VALUE']) ? $arChecked['VALUE'] : GetMessage("CT_BCSF_FILTER_ALL") )?><?
												?></span><?
												?><i class="icon pngicons"></i><?
											?></label><?
										?></div><?
									?></li><?
								?></ul><?
								break;
							case "K"://RADIO_BUTTONS
								?><ul class="property rb"><?
									// search
									if($IS_SEARCHABLE && !$IS_COLOR) {
										?><div class="around_f_search"><input type="text" class="f_search" name="f_search" id="f_search" value="" placeholder="<?=GetMessage('FILTR_SEARHC')?>"></div><?
									}
									// scroll
									if($IS_SCROLABLE && count($arItem['VALUES'])>$arParams['FILTER_PROP_SCROLL_CNT']) {
										$IS_SCROLABLE = true;
									} else {
										$IS_SCROLABLE = false;
									}
									if($IS_SCROLABLE) {
										?><div class="f_jscrollpane" id="f_scroll_<?=$arItem['ID']?>"><?
									}
									?><li class="lvl2"><?
										?><div><?
											?><input <?
												?>type="radio" <?
												?>value="" <?
												?>name="<?=$arCur["CONTROL_NAME_ALT"]?>" <?
												?>id="<?="all_".$arCur["CONTROL_ID"]?>" <?
												?>onclick="smartFilter.click(this)" <?
											?>/><?
											?><label for="all_<?=$arCur["CONTROL_ID"]?>" data-role="label_<?=$arCur["CONTROL_ID"]?>" class="name <?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'active');"><?
												?><span><?=GetMessage("CT_BCSF_FILTER_ALL")?></span><?
											?></label><?
										?></div><?
									?></li><?
									foreach($arItem["VALUES"] as $val => $ar) {
										$class = "";
										if($ar["CHECKED"]) {
											$class.= " active";
										}
										if($ar["DISABLED"]) {
											$class.= " disabled";
										}
										?><li class="lvl2"><?
											?><div class="<?=$class?>"><?
												?><input <?
													?>type="radio" <?
													?>value="<?=$ar["HTML_VALUE_ALT"]?>" <?
													?>name="<?=$ar["CONTROL_NAME_ALT"]?>" <?
													?>id="<?=$ar["CONTROL_ID"]?>" <?
													?><? echo $ar["CHECKED"]? 'checked="checked"': '' ?><?
													?>onclick="smartFilter.click(this)" <?
												?>/><?
												?><label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="name <?=$class?>"><?
													?><span class="val"><?=$ar["VALUE"]?></span><?
												?></label><?
											?></div><?
										?></li><?
									}
									// scroll
									if($IS_SCROLABLE) {
										?></div><?
									}
								?></ul><?
								break;
							case "U"://CALENDAR
								?><ul class="property c"><?
									?><div class="inputs"><?
										$APPLICATION->IncludeComponent(
											'bitrix:main.calendar',
											'',
											array(
												'FORM_NAME' => $arResult["FILTER_NAME"]."_form",
												'SHOW_INPUT' => 'Y',
												'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="'.FormatDate("SHORT", $arItem["VALUES"]["MIN"]["VALUE"]).'" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
												'INPUT_NAME' => $arItem["VALUES"]["MIN"]["CONTROL_NAME"],
												'INPUT_VALUE' => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
												'SHOW_TIME' => 'N',
												'HIDE_TIMEBAR' => 'Y',
											),
											null,
											array('HIDE_ICONS' => 'Y')
										);
										$APPLICATION->IncludeComponent(
											'bitrix:main.calendar',
											'',
											array(
												'FORM_NAME' => $arResult["FILTER_NAME"]."_form",
												'SHOW_INPUT' => 'Y',
												'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="'.FormatDate("SHORT", $arItem["VALUES"]["MAX"]["VALUE"]).'" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
												'INPUT_NAME' => $arItem["VALUES"]["MAX"]["CONTROL_NAME"],
												'INPUT_VALUE' => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
												'SHOW_TIME' => 'N',
												'HIDE_TIMEBAR' => 'Y',
											),
											null,
											array('HIDE_ICONS' => 'Y')
										);
									?></div><?
								?></ul><?
								break;
							default://CHECKBOXES
								?><ul class="property"><?
									// search
									if($IS_SEARCHABLE && !$IS_COLOR) {
										?><div class="around_f_search"><input type="text" class="f_search" name="f_search" id="f_search" value="" placeholder="<?=GetMessage('FILTR_SEARHC')?>"></div><?
									}
									// scroll
									if($IS_SCROLABLE && count($arItem['VALUES'])>$arParams['FILTER_PROP_SCROLL_CNT']) {
										$IS_SCROLABLE = true;
									} else {
										$IS_SCROLABLE = false;
									}
									if($IS_SCROLABLE) {
										?><div class="f_jscrollpane" id="f_scroll_<?=$arItem['ID']?>"><?
									}
									foreach ($arItem["VALUES"] as $val => $ar) {
										$class = "";
										if($ar["CHECKED"]) {
											$class.= " active";
										}
										if($ar["DISABLED"]) {
											$class.= " disabled";
										}
										?><li class="lvl2"><?
											?><div class="<?=$class?>"><?
												?><input <?
													?>type="checkbox" <?
													?>name="<?=$ar["CONTROL_NAME"]?>" <?
													?>id="<?=$ar["CONTROL_ID"]?>" <?
													?>value="<?=$ar["HTML_VALUE"]?>" <?
													?><? echo $ar["CHECKED"]? 'checked="checked"': '' ?> <?
												?>/><?
												?><label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="name <?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'active');"><?
													?><span class="val"><?=$ar["VALUE"]?></span><?
												?></label><?
											?></div><?
										?></li><?
									}
									// scroll
									if($IS_SCROLABLE) {
										?></div><?
									}
								?></ul><?
						}
					?></li><?
				}
				?></ul><?
				
				?><div class="buttons"><?
					?><a rel="nofollow" class="btn1 set_filter" href="#"><?=GetMessage('CT_BCSF_SET_FILTER')?></a><?
					?><span class="separator"></span><?
					?><a rel="nofollow" class="btn3 del_filter" href="#"><?=GetMessage('CT_BCSF_DEL_FILTER')?></a><?
					?><input class="nonep" type="submit" id="set_filter" name="set_filter" value="<?=GetMessage('CT_BCSF_SET_FILTER')?>" /><?
					?><input class="nonep" type="submit" id="del_filter" name="del_filter" value="<?=GetMessage('CT_BCSF_DEL_FILTER')?>" /><?
				?></div><?
			?></div><?
			
			?><div class="modef" id="modef" <?if(!isset($arResult['ELEMENT_COUNT'])) echo 'style="display:none"';?>><?
				?><span class="arrow">&nbsp;</span><?
				?><span class="data"><?
					?><?=GetMessage('CT_BCSF_FILTER_COUNT', array('#ELEMENT_COUNT#' => '<span id="modef_num">'.intval($arResult['ELEMENT_COUNT']).'</span>'));?><?
					?> <a href="<?=$arResult['FILTER_URL']?>"><?=GetMessage('CT_BCSF_FILTER_SHOW')?></a><?
				?></span><?
			?></div><?
		?></div><?
	?></div><?
?></form><?
?><script>
	var smartFilter = new JCSmartFilter('<?=CUtil::JSEscape($arResult['FORM_ACTION'])?>');
</script>