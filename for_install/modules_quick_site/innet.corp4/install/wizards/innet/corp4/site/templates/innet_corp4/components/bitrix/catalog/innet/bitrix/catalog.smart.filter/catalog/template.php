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

$templateData = array(
	'TEMPLATE_THEME' => $this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/colors.css',
	'TEMPLATE_CLASS' => 'bx-'.$arParams['TEMPLATE_THEME']
);

if (isset($templateData['TEMPLATE_THEME'])) {
    $this->addExternalCss($templateData['TEMPLATE_THEME']);
}
//$this->addExternalCss("/bitrix/css/main/bootstrap.css");
//$this->addExternalCss("/bitrix/css/main/font-awesome.css");
?>

<a class="open-toogle2"><?=GetMessage('CT_BCSF_FILTER_SHOW_FILTERS')?></a>

<div class="border2">
    <div>
        <div class="toogle toogle2">
            <div class="bx-filter <?=$templateData["TEMPLATE_CLASS"]?> <?if ($arParams["FILTER_VIEW_MODE"] == "HORIZONTAL") echo "bx-filter-horizontal"?>">
                <div class="bx-filter-section container-fluid" style="padding-top: 0px;">
                    <form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get" class="smartfilter">
                        <?foreach($arResult["HIDDEN"] as $arItem):?>
                            <input type="hidden" name="<?echo $arItem["CONTROL_NAME"]?>" id="<?echo $arItem["CONTROL_ID"]?>" value="<?echo $arItem["HTML_VALUE"]?>" />
                        <?endforeach;?>

                        <div class="row">
							<ul>
							
                            <?foreach($arResult["ITEMS"] as $key=>$arItem)//prices
                            {
                                $key = $arItem["ENCODED_ID"];
                                if(isset($arItem["PRICE"])):
                                    if ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0)
                                        continue;

                                    $precision = 2;
                                    if (Bitrix\Main\Loader::includeModule("currency"))
                                    {
                                        $res = CCurrencyLang::GetFormatDescription($arItem["VALUES"]["MIN"]["CURRENCY"]);
                                        $precision = $res['DECIMALS'];
                                    }
                                    ?>
									<li class="opened">
										<a class="toogle-title" data-action="slide-toggler" data-target=".toogle-main.toogle-content">Цена<span></span></a>
										<div class="toogle-main toogle-content">
											<div class="<?if ($arParams["FILTER_VIEW_MODE"] == "HORIZONTAL"):?> <?else:?> <?endif?> bx-filter-parameters-box bx-active">
												<span class="bx-filter-container-modef"></span>
												<div class="bx-filter-block" data-role="bx_filter_block">
													<div class="row bx-filter-parameters-box-container">
														<div class="col-xs-6 bx-filter-parameters-box-container-block bx-left">
															<i class="bx-ft-sub"><?=GetMessage("CT_BCSF_FILTER_FROM")?></i>
															<div class="bx-filter-input-container">
																<input
																	class="min-price"
																	type="text"
																	name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
																	id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
																	value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
																	size="5"
																	onkeyup="smartFilter.keyup(this)"
																/>
															</div>
														</div>
														<div class="col-xs-6 bx-filter-parameters-box-container-block bx-right">
															<i class="bx-ft-sub"><?=GetMessage("CT_BCSF_FILTER_TO")?></i>
															<div class="bx-filter-input-container">
																<input
																	class="max-price"
																	type="text"
																	name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
																	id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
																	value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
																	size="5"
																	onkeyup="smartFilter.keyup(this)"
																/>
															</div>
														</div>

														<div class="col-xs-10 col-xs-offset-1 bx-ui-slider-track-container">
															<div class="bx-ui-slider-track" id="drag_track_<?=$key?>">
																<?
																$precision = $arItem["DECIMALS"]? $arItem["DECIMALS"]: 0;
																$step = ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"]) / 4;
																$price1 = number_format($arItem["VALUES"]["MIN"]["VALUE"], $precision, ".", "");
																$price2 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step, $precision, ".", "");
																$price3 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 2, $precision, ".", "");
																$price4 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 3, $precision, ".", "");
																$price5 = number_format($arItem["VALUES"]["MAX"]["VALUE"], $precision, ".", "");
																?>
																<div class="bx-ui-slider-part p1"><span><?=$price1?></span></div>
																<div class="bx-ui-slider-part p2"><span><?=$price2?></span></div>
																<div class="bx-ui-slider-part p3"><span><?=$price3?></span></div>
																<div class="bx-ui-slider-part p4"><span><?=$price4?></span></div>
																<div class="bx-ui-slider-part p5"><span><?=$price5?></span></div>

																<div class="bx-ui-slider-pricebar-vd" style="left: 0;right: 0;" id="colorUnavailableActive_<?=$key?>"></div>
																<div class="bx-ui-slider-pricebar-vn" style="left: 0;right: 0;" id="colorAvailableInactive_<?=$key?>"></div>
																<div class="bx-ui-slider-pricebar-v"  style="left: 0;right: 0;" id="colorAvailableActive_<?=$key?>"></div>
																<div class="bx-ui-slider-range" id="drag_tracker_<?=$key?>"  style="left: 0%; right: 0%;">
																	<a class="bx-ui-slider-handle left"  style="left:0;" href="javascript:void(0)" id="left_slider_<?=$key?>"></a>
																	<a class="bx-ui-slider-handle right" style="right:0;" href="javascript:void(0)" id="right_slider_<?=$key?>"></a>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</li>
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
                                        "precision" => $precision,
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
                                <?endif;
                            }
							?>
					
                            <?//not prices
                            foreach($arResult["ITEMS"] as $key=>$arItem)
                            {
                                if(
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
                                ?>
								
								
								            	
								            
								<li class="opened">
								
								<a class="toogle-title" data-action="slide-toggler" data-target=".toogle-main.toogle-content"><?=$arItem["NAME"]?>
									<?if ($arItem["FILTER_HINT"] <> ""):?>
										<i id="item_title_hint_<?echo $arItem["ID"]?>" class="fa fa-question-circle"></i>
										<script type="text/javascript">
											new top.BX.CHint({
												parent: top.BX("item_title_hint_<?echo $arItem["ID"]?>"),
												show_timeout: 10,
												hide_timeout: 200,
												dx: 2,
												preventHide: true,
												min_width: 250,
												hint: '<?= CUtil::JSEscape($arItem["FILTER_HINT"])?>'
											});
										</script>
									<?endif?>
									<span></span>
								</a>
								<div class="toogle-main toogle-content">
									
                                <div class="<?if ($arParams["FILTER_VIEW_MODE"] == "HORIZONTAL"):?><?else:?><?endif?> bx-filter-parameters-box <?if ($arItem["DISPLAY_EXPANDED"]== "Y"):?>bx-active<?endif?>">
                                    <span class="bx-filter-container-modef"></span>
                                    
									


                                    <div class="bx-filter-block" data-role="bx_filter_block">
                                        <div class="bx-filter-parameters-box-container">
                                        <?
                                        $arCur = current($arItem["VALUES"]);
                                        switch ($arItem["DISPLAY_TYPE"])
                                        {
                                            case "A"://NUMBERS_WITH_SLIDER
                                                ?>
                                                <div class="col-xs-6 bx-filter-parameters-box-container-block bx-left">
                                                    <i class="bx-ft-sub"><?=GetMessage("CT_BCSF_FILTER_FROM")?></i>
                                                    <div class="bx-filter-input-container">
                                                        <input
                                                            class="min-price"
                                                            type="text"
                                                            name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
                                                            id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
                                                            value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
                                                            size="5"
                                                            onkeyup="smartFilter.keyup(this)"
                                                        />
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 bx-filter-parameters-box-container-block bx-right">
                                                    <i class="bx-ft-sub"><?=GetMessage("CT_BCSF_FILTER_TO")?></i>
                                                    <div class="bx-filter-input-container">
                                                        <input
                                                            class="max-price"
                                                            type="text"
                                                            name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
                                                            id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
                                                            value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
                                                            size="5"
                                                            onkeyup="smartFilter.keyup(this)"
                                                        />
                                                    </div>
                                                </div>

                                                <div class="col-xs-10 col-xs-offset-1 bx-ui-slider-track-container">
                                                    <div class="bx-ui-slider-track" id="drag_track_<?=$key?>">
                                                        <?
                                                        $precision = $arItem["DECIMALS"]? $arItem["DECIMALS"]: 0;
                                                        $step = ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"]) / 4;
                                                        $value1 = number_format($arItem["VALUES"]["MIN"]["VALUE"], $precision, ".", "");
                                                        $value2 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step, $precision, ".", "");
                                                        $value3 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 2, $precision, ".", "");
                                                        $value4 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 3, $precision, ".", "");
                                                        $value5 = number_format($arItem["VALUES"]["MAX"]["VALUE"], $precision, ".", "");
                                                        ?>
                                                        <div class="bx-ui-slider-part p1"><span><?=$value1?></span></div>
                                                        <div class="bx-ui-slider-part p2"><span><?=$value2?></span></div>
                                                        <div class="bx-ui-slider-part p3"><span><?=$value3?></span></div>
                                                        <div class="bx-ui-slider-part p4"><span><?=$value4?></span></div>
                                                        <div class="bx-ui-slider-part p5"><span><?=$value5?></span></div>

                                                        <div class="bx-ui-slider-pricebar-vd" style="left: 0;right: 0;" id="colorUnavailableActive_<?=$key?>"></div>
                                                        <div class="bx-ui-slider-pricebar-vn" style="left: 0;right: 0;" id="colorAvailableInactive_<?=$key?>"></div>
                                                        <div class="bx-ui-slider-pricebar-v"  style="left: 0;right: 0;" id="colorAvailableActive_<?=$key?>"></div>
                                                        <div class="bx-ui-slider-range" 	id="drag_tracker_<?=$key?>"  style="left: 0;right: 0;">
                                                            <a class="bx-ui-slider-handle left"  style="left:0;" href="javascript:void(0)" id="left_slider_<?=$key?>"></a>
                                                            <a class="bx-ui-slider-handle right" style="right:0;" href="javascript:void(0)" id="right_slider_<?=$key?>"></a>
                                                        </div>
                                                    </div>
                                                </div>
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
                                                <div class="col-xs-6 bx-filter-parameters-box-container-block bx-left">
                                                    <i class="bx-ft-sub"><?=GetMessage("CT_BCSF_FILTER_FROM")?></i>
                                                    <div class="bx-filter-input-container">
                                                        <input
                                                            class="min-price"
                                                            type="text"
                                                            name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
                                                            id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
                                                            value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
                                                            size="5"
                                                            onkeyup="smartFilter.keyup(this)"
                                                            />
                                                    </div>
                                                </div>
                                                <div class="col-xs-6 bx-filter-parameters-box-container-block bx-right">
                                                    <i class="bx-ft-sub"><?=GetMessage("CT_BCSF_FILTER_TO")?></i>
                                                    <div class="bx-filter-input-container">
                                                        <input
                                                            class="max-price"
                                                            type="text"
                                                            name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
                                                            id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
                                                            value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
                                                            size="5"
                                                            onkeyup="smartFilter.keyup(this)"
                                                            />
                                                    </div>
                                                </div>
                                                <?
                                                break;
                                            case "G"://CHECKBOXES_WITH_PICTURES
                                                ?>
                                                <div class="bx-filter-param-btn-inline">
													<?foreach ($arItem["VALUES"] as $val => $ar):?>
														<input
															style="display: none"
															type="checkbox"
															name="<?=$ar["CONTROL_NAME"]?>"
															id="<?=$ar["CONTROL_ID"]?>"
															value="<?=$ar["HTML_VALUE"]?>"
															<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
														/>
														<?
														$class = "";
														if ($ar["CHECKED"])
															$class.= " bx-active";
														if ($ar["DISABLED"])
															$class.= " disabled";
														?>
														<label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label <?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'bx-active');">
															<span class="bx-filter-param-btn bx-color-sl">
																<?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
																<span class="bx-filter-btn-color-icon" style="background:url('<?=$ar["FILE"]["SRC"]?>');"></span>
																<?endif?>
															</span>
														</label>
													<?endforeach?>
                                                </div>
                                                <?
                                                break;
                                            case "H"://CHECKBOXES_WITH_PICTURES_AND_LABELS
                                                ?>
                                                <div class="bx-filter-param-btn-block">
                                                <?foreach ($arItem["VALUES"] as $val => $ar):?>
                                                    <input
                                                        style="display: none"
                                                        type="checkbox"
                                                        name="<?=$ar["CONTROL_NAME"]?>"
                                                        id="<?=$ar["CONTROL_ID"]?>"
                                                        value="<?=$ar["HTML_VALUE"]?>"
                                                        <? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
                                                    />
                                                    <?
                                                    $class = "";
                                                    if ($ar["CHECKED"])
                                                        $class.= " bx-active";
                                                    if ($ar["DISABLED"])
                                                        $class.= " disabled";
                                                    ?>
                                                    <label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label<?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'bx-active');">
                                                        <span class="bx-filter-param-btn bx-color-sl">
                                                            <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                                <span class="bx-filter-btn-color-icon" style="background:url('<?=$ar["FILE"]["SRC"]?>');"></span>
                                                            <?endif?>
                                                        </span>
                                                        <!--span class="bx-filter-param-text" title="<?=$ar["VALUE"];?>"><?=$ar["VALUE"];?><?
                                                        if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
                                                            ?> (<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)<?
                                                        endif;?></span-->
                                                    </label>
                                                <?endforeach?>
                                                </div>
                                                <?
                                                break;
                                            case "P"://DROPDOWN
                                                $checkedItemExist = false;
                                                ?>
                                                <div class="bx-filter-select-container">
                                                    <div class="bx-filter-select-block" onclick="smartFilter.showDropDownPopup(this, '<?=CUtil::JSEscape($key)?>')">
                                                        <div class="bx-filter-select-text" data-role="currentOption">
                                                            <?
                                                            foreach ($arItem["VALUES"] as $val => $ar)
                                                            {
                                                                if ($ar["CHECKED"])
                                                                {
                                                                    echo $ar["VALUE"];
                                                                    $checkedItemExist = true;
                                                                }
                                                            }
                                                            if (!$checkedItemExist)
                                                            {
                                                                echo GetMessage("CT_BCSF_FILTER_ALL");
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="bx-filter-select-arrow"></div>
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
                                                        <div class="bx-filter-select-popup" data-role="dropdownContent" style="display: none;">
                                                            <ul>
                                                                <li>
                                                                    <label for="<?="all_".$arCur["CONTROL_ID"]?>" class="bx-filter-param-label" data-role="label_<?="all_".$arCur["CONTROL_ID"]?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape("all_".$arCur["CONTROL_ID"])?>')">
                                                                        <? echo GetMessage("CT_BCSF_FILTER_ALL"); ?>
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
                                                                    <label for="<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label<?=$class?>" data-role="label_<?=$ar["CONTROL_ID"]?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')"><?=$ar["VALUE"]?></label>
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
                                                <div class="bx-filter-select-container">
                                                    <div class="bx-filter-select-block" onclick="smartFilter.showDropDownPopup(this, '<?=CUtil::JSEscape($key)?>')">
                                                        <div class="bx-filter-select-text fix" data-role="currentOption">
                                                            <?
                                                            $checkedItemExist = false;
                                                            foreach ($arItem["VALUES"] as $val => $ar):
                                                                if ($ar["CHECKED"])
                                                                {
                                                                ?>
                                                                    <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                                        <span class="bx-filter-btn-color-icon" style="background:url('<?=$ar["FILE"]["SRC"]?>');"></span>
                                                                    <?endif?>
                                                                    <span class="bx-filter-param-text">
                                                                        <?=$ar["VALUE"]?>
                                                                    </span>
                                                                <?
                                                                    $checkedItemExist = true;
                                                                }
                                                            endforeach;
                                                            if (!$checkedItemExist)
                                                            {
                                                                ?><span class="bx-filter-btn-color-icon all"></span> <?
                                                                echo GetMessage("CT_BCSF_FILTER_ALL");
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="bx-filter-select-arrow"></div>
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
                                                        <div class="bx-filter-select-popup" data-role="dropdownContent" style="display: none">
                                                            <ul>
                                                                <li style="border-bottom: 1px solid #e5e5e5;padding-bottom: 5px;margin-bottom: 5px;">
                                                                    <label for="<?="all_".$arCur["CONTROL_ID"]?>" class="bx-filter-param-label" data-role="label_<?="all_".$arCur["CONTROL_ID"]?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape("all_".$arCur["CONTROL_ID"])?>')">
                                                                        <span class="bx-filter-btn-color-icon all"></span>
                                                                        <? echo GetMessage("CT_BCSF_FILTER_ALL"); ?>
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
                                                                    <label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label<?=$class?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')">
                                                                        <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                                            <span class="bx-filter-btn-color-icon" style="background:url('<?=$ar["FILE"]["SRC"]?>');"></span>
                                                                        <?endif?>
                                                                        <span class="bx-filter-param-text">
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
                                                <div class="radio">
                                                    <label class="bx-filter-param-label" for="<? echo "all_".$arCur["CONTROL_ID"] ?>">
                                                        <span class="bx-filter-input-checkbox">
                                                            <input
                                                                type="radio"
                                                                value=""
                                                                name="<? echo $arCur["CONTROL_NAME_ALT"] ?>"
                                                                id="<? echo "all_".$arCur["CONTROL_ID"] ?>"
                                                                onclick="smartFilter.click(this)"
                                                            />
                                                            <span class="bx-filter-param-text"><? echo GetMessage("CT_BCSF_FILTER_ALL"); ?></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <?foreach($arItem["VALUES"] as $val => $ar):?>
                                                    <div class="radio">
                                                        <label data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label" for="<? echo $ar["CONTROL_ID"] ?>">
                                                            <span class="bx-filter-input-checkbox <? echo $ar["DISABLED"] ? 'disabled': '' ?>">
                                                                <input
                                                                    type="radio"
                                                                    value="<? echo $ar["HTML_VALUE_ALT"] ?>"
                                                                    name="<? echo $ar["CONTROL_NAME_ALT"] ?>"
                                                                    id="<? echo $ar["CONTROL_ID"] ?>"
                                                                    <? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
                                                                    onclick="smartFilter.click(this)"
                                                                />
                                                                <span class="bx-filter-param-text" title="<?=$ar["VALUE"];?>"><?=$ar["VALUE"];?><?
                                                                if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
                                                                    ?>&nbsp;(<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)<?
                                                                endif;?></span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                <?endforeach;?>
                                                <?
                                                break;
                                            case "U"://CALENDAR
                                                ?>
                                                <div class="bx-filter-parameters-box-container-block"><div class="bx-filter-input-container bx-filter-calendar-container">
                                                    <?$APPLICATION->IncludeComponent(
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
                                                    );?>
                                                </div></div>
                                                <div class="bx-filter-parameters-box-container-block"><div class="bx-filter-input-container bx-filter-calendar-container">
                                                    <?$APPLICATION->IncludeComponent(
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
                                                    );?>
                                                </div></div>
                                                <?
                                                break;
                                            default://CHECKBOXES
                                                ?>
                                                <?foreach($arItem["VALUES"] as $val => $ar):?>
                                                    <div class="checkbox">
                                                        <label data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label <? echo $ar["DISABLED"] ? 'disabled': '' ?>" for="<? echo $ar["CONTROL_ID"] ?>">
                                                            <span class="bx-filter-input-checkbox">
                                                                <div>
																	<input
																		type="checkbox"
																		value="<? echo $ar["HTML_VALUE"] ?>"
																		name="<? echo $ar["CONTROL_NAME"] ?>"
																		id="<? echo $ar["CONTROL_ID"] ?>"
																		<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
																		onclick="smartFilter.click(this)"
																	/>
																	<label class="bx-filter-param-text" for="<?=$ar["CONTROL_ID"]?>"><span></span><?=$ar["VALUE"];?></label>
																</div>
																
                                                                <!--span class="bx-filter-param-text" title="<?=$ar["VALUE"];?>"><?=$ar["VALUE"];?><?
                                                                if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
                                                                    ?>&nbsp;(<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)<?
                                                                endif;?></span-->
                                                            </span>
                                                        </label>
                                                    </div>
                                                <?endforeach;?>
                                        <?
                                        }
                                        ?>
                                        </div>
                                        <div style="clear: both"></div>
                                    </div>
                                </div>
                            <?
                            }
                            ?>
							</div>
                        </div><!--//row-->
						</li>
						</ul>
                        <div class="row">
                            <div class="col-xs-12 bx-filter-button-box filter-bottom">
                                <div class="bx-filter-block ">
                                    <div class="bx-filter-parameters-box-container ">
                                        <?/*<input
                                            class="btn"
                                            type="submit"
                                            id="set_filter"
                                            name="set_filter"
                                            value="<?=GetMessage("CT_BCSF_SET_FILTER")?>"
                                        />*/?>
                                        <button
                                            class="btn"
                                                type="submit"
                                                id="set_filter"
                                                name="set_filter"
                                                value="y">
                                            <?=GetMessage("CT_BCSF_SET_FILTER")?>
                                        </button>
                                        <input
                                            class="btn4"
                                            type="submit"
                                            id="del_filter"
                                            name="del_filter"
                                            value="<?=GetMessage("CT_BCSF_DEL_FILTER")?>"
                                        />
                                        <div class="bx-filter-popup-result <?if ($arParams["FILTER_VIEW_MODE"] == "VERTICAL") echo $arParams["POPUP_POSITION"]?>" id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])) echo 'style="display:none"';?> style="display: inline-block;">
                                            <?echo GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>
                                            <span class="arrow"></span>
                                            <br/>
                                            <a href="<?echo $arResult["FILTER_URL"]?>" target=""><?echo GetMessage("CT_BCSF_FILTER_SHOW")?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clb"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
	var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>', '<?=CUtil::JSEscape($arParams["FILTER_VIEW_MODE"])?>', <?=CUtil::PhpToJSObject($arResult["JS_FILTER_PARAMS"])?>);
</script>