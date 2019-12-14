
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

$arPropHaveChecked = array();

$arHiddenProps = array($arParams['BRAND_PROP']);
?>
<div class="bx-filter">
    <div class="bx-filter-section container-fluid">
        <form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get" class="smartfilter" onsubmit="smartFilter.submit(this)">
            <?foreach($arResult["HIDDEN"] as $arItem):?>
            <input type="hidden" name="<?echo $arItem["CONTROL_NAME"]?>" id="<?echo $arItem["CONTROL_ID"]?>" value="<?echo $arItem["HTML_VALUE"]?>" />
            <?endforeach;?>
            <div class="row">
                <?foreach($arResult["ITEMS"] as $key=>$arItem)//prices
                {
                    $arrayKey = $key;
                    $key = $arItem["ENCODED_ID"];
                    if(isset($arItem["PRICE"])):
                        if ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0)
                            continue;
                ?>
                        <?php if (in_array($arItem["CODE"], $arParams["PRICES_GROUPED"])): ?>
                            <div class="bx-filter-parameters-box bx-active">
                                <span class="bx-filter-container-modef"></span>
                                <div class="bx-filter-parameters-box-title" onclick="smartFilter.hideFilterProps(this)"><span><?=$arItem["NAME"]?> <i data-role="prop_angle" class="bx-filter-parameters-box-angle"></i></span></div>
                                <div class="bx-filter-block" data-role="bx_filter_block">
                                    <div class="bx-filter-parameters-box-container">
                                        <div class="bx-filter-param-btn-prices offer_prop-btn clearfix">

                                    <?php foreach ($arItem["GROUP_VALUES"]["FOR_TEMPLATE"] as $keyD => $groupValue): ?>
                                        <?
                                        $class = "offer_prop__value";
                                        if ($groupValue["SELECTED"] == 'Y')
                                            $class.= " checked";
                                        if ($groupValue["DISABLED"] == 'Y')
                                            $class.= " disabled";
                                        $sControlID = $arItem["GROUP_VALUES"]["PRICE_GROUP_DIAPAZONS"][$keyD]["CONTROL_ID"];
                                        ?>
                                        <label for="<?=$sControlID?>" data-role="label_<?=$sControlID?>" class="bx-filter-param-label <?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($sControlID)?>')); BX.toggleClass(this, 'checked');">
                                            <input
                                                style="display: none"
                                                type="checkbox"
                                                value="Y"
                                                name="<?=$arItem["GROUP_VALUES"]["PRICE_GROUP_DIAPAZONS"][$keyD]["CONTROL_NAME"]?>"
                                                id="<?=$sControlID?>"
                                                <?/*onclick="smartFilter.click(this)"*/?>
                                            >
                                            <?=$groupValue["NAME1"]?>
                                        </label>
                                        <?php
                                        if ($groupValue['SELECTED'] == 'Y') {
                                            $arPropHaveChecked[$key][$keyD] = $groupValue['NAME1'];
                                        }
                                        ?>
                                    <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>

                        <?php
                        $precision = 2;
                        if (Bitrix\Main\Loader::includeModule("currency"))
                        {
                            $res = CCurrencyLang::GetFormatDescription($arItem["VALUES"]["MIN"]["CURRENCY"]);
                            $precision = $res['DECIMALS'];
                        }
                        ?>
                        <div class="bx-filter-parameters-box bx-active">
                            <span class="bx-filter-container-modef"></span>
                            <div class="bx-filter-parameters-box-title" onclick="smartFilter.hideFilterProps(this)"><span><?=$arItem["NAME"]?> <i data-role="prop_angle" class="bx-filter-parameters-box-angle"></i></span></div>
                            <div class="bx-filter-block" data-role="bx_filter_block">
                                <div class="row bx-filter-parameters-box-container">
                                    <div class="col-xs-6 bx-filter-parameters-box-container-block bx-left">
                                        <i class="bx-ft-sub"><?=getMessage('CT_BCSF_FILTER_FROM')?></i>
                                        <div class="bx-filter-input-container">
                                            <input
                                                class="min-price form-control quantity__input"
                                                type="number"
                                                name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
                                                id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
                                                <?/*?> value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"<?*/?>
                                                value="<?=('' != $arItem["VALUES"]["MIN"]["HTML_VALUE"]) ? $arItem["VALUES"]["MIN"]["HTML_VALUE"] : (float) $arItem["VALUES"]["MIN"]["VALUE"]?>"
                                                min="<?=(float)$arItem["VALUES"]["MIN"]["VALUE"];?>"
                                                max="<?=(float)$arItem["VALUES"]["MAX"]["VALUE"];?>"
                                                step="<?echo pow(10, -$precision)?>"
                                                size="5"
                                                onkeyup="smartFilter.keyup(this)"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-xs-6 bx-filter-parameters-box-container-block bx-right">
                                        <i class="bx-ft-sub"><?=getMessage('CT_BCSF_FILTER_TO')?></i>
                                        <div class="bx-filter-input-container">
                                            <input
                                                class="max-price form-control quantity__input"
                                                type="number"
                                                name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
                                                id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
                                                <?/*?> value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"<?*/?>
                                                value="<?=('' != $arItem["VALUES"]["MAX"]["HTML_VALUE"]) ? $arItem["VALUES"]["MAX"]["HTML_VALUE"] : (float) $arItem["VALUES"]["MAX"]["VALUE"]?>"
                                                min="<?=(float)$arItem["VALUES"]["MIN"]["VALUE"];?>"
                                                max="<?=(float)$arItem["VALUES"]["MAX"]["VALUE"];?>"
                                                step="<?echo pow(0.1, $precision)?>"
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
                        <?
                        if ($arItem["VALUES"]["MIN"]["HTML_VALUE"]){
                            $arPropHaveChecked[$arrayKey]["MIN"] = $arItem["VALUES"]["MIN"]["HTML_VALUE"];
                        }
                        if ($arItem["VALUES"]["MAX"]["HTML_VALUE"]){
                            $arPropHaveChecked[$arrayKey]["MAX"] = $arItem["VALUES"]["MAX"]["HTML_VALUE"];
                        }
                        endif;
                    endif;
                }

                //not prices
                foreach($arResult["ITEMS"] as $key=>$arItem)
                {
					$key = !empty($arItem["ENCODED_ID"]) ? $arItem["ENCODED_ID"] : $key;
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
                    <div class="bx-filter-parameters-box <?if ($arItem["DISPLAY_EXPANDED"]== "Y"):?>bx-active<?endif?>"<?if(in_array($arItem["CODE"], $arHiddenProps)):?> style="display:none"<?endif?>>
                        <span class="bx-filter-container-modef"></span>
                        <div class="bx-filter-parameters-box-title" onclick="smartFilter.hideFilterProps(this)">
                            <span class="bx-filter-parameters-box-hint"><?=$arItem["NAME"]?>
                                <?if ($arItem["FILTER_HINT"] <> ""):?>
                                    <i id="item_title_hint_<?echo $arItem["ID"]?>" class="filter__hint hint">?</i>
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
                                <i data-role="prop_angle" class="bx-filter-parameters-box-angle"></i>
                            </span>
                        </div>

                        <div class="bx-filter-block" data-role="bx_filter_block">
                            <?
                            $IS_SEARCHABLE = $IS_SCROLABLE = $IS_COLOR = $IS_BUTTON = false;

                            if (
                                is_array($arParams["SEARCH_PROPS"]) && in_array($arItem["CODE"], $arParams["SEARCH_PROPS"]) ||
                                is_array($arParams["OFFER_SEARCH_PROPS"]) && in_array($arItem["CODE"], $arParams["OFFER_SEARCH_PROPS"])
                            ) {
                                $IS_SEARCHABLE = $IS_SCROLABLE = true;
                            } elseif (
                                is_array($arParams["OFFER_SEARCH_PROPS"]) && in_array($arItem["CODE"], $arParams["OFFER_SEARCH_PROPS"]) ||
                                is_array($arParams["OFFER_SCROLL_PROPS"]) && in_array($arItem["CODE"], $arParams["OFFER_SCROLL_PROPS"])
                            ) {
                                $IS_SCROLABLE = true;
                            }

                            if (
                                is_array($arParams["OFFER_TREE_COLOR_PROPS"]) &&
                                in_array($arItem["CODE"], $arParams["OFFER_TREE_COLOR_PROPS"])
                            ) {
                                $IS_COLOR = true;
                            } elseif (
                                is_array($arParams["OFFER_TREE_BTN_PROPS"]) &&
                                in_array($arItem["CODE"], $arParams["OFFER_TREE_BTN_PROPS"])
                            ) {
                                $IS_BUTTON = true;
                            }
                            ?>
                            <?php if (count($arItem["VALUES"]) > 7): ?>
                                <?php if ($IS_SEARCHABLE): ?>
                                    <div class="bx-filter-search form-group">
                                        <input type="text" class="form-control" placeholder="<?=getMessage('RS_SLINE.BCSF_AL.SEARCH')?>">
                                    </div>
                                <?php endif; ?>

                                <?php if ($IS_SCROLABLE): ?>
                                    <div class="bx-filter-scroll">
                                <?php endif; ?>
                            <?php endif; ?>
                            <div class="bx-filter-parameters-box-container<?php if (in_array($arItem["DISPLAY_TYPE"], array('A', 'B', 'U'))): ?> row<?php endif; ?>">
                            <?
                            $arCur = current($arItem["VALUES"]);
                            switch ($arItem["DISPLAY_TYPE"])
                            {
                                case "A"://NUMBERS_WITH_SLIDER
                                    $arValueMax = explode('.', (float) $arItem["VALUES"]["MAX"]["VALUE"]);
                                    $arValueMin = explode('.', (float) $arItem["VALUES"]["MIN"]["VALUE"]);
                                    $precision = pow(0.1, strlen(max($arValueMax[1], $arValueMin[1])));
                                    ?>
                                    <div class="col-xs-6 bx-filter-parameters-box-container-block bx-left">
                                        <i class="bx-ft-sub"><?=getMessage('CT_BCSF_FILTER_FROM')?></i>
                                        <div class="bx-filter-input-container">
                                            <input
                                                class="min-price form-control quantity__input"
                                                type="number"
                                                name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
                                                id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
                                                <?/*?> value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"<?*/?>
                                                value="<?=('' != $arItem["VALUES"]["MIN"]["HTML_VALUE"]) ? $arItem["VALUES"]["MIN"]["HTML_VALUE"] : (float) $arItem["VALUES"]["MIN"]["VALUE"]?>"
                                                min="<?=(float)$arItem["VALUES"]["MIN"]["VALUE"];?>"
                                                max="<?=(float)$arItem["VALUES"]["MAX"]["VALUE"];?>"
                                                step="<?echo pow(10, -$precision)?>"
                                                size="5"
                                                onkeyup="smartFilter.keyup(this)"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-xs-6 bx-filter-parameters-box-container-block bx-right">
                                        <i class="bx-ft-sub"><?=getMessage('CT_BCSF_FILTER_TO')?></i>
                                        <div class="bx-filter-input-container">
                                            <input
                                                class="max-price form-control quantity__input"
                                                type="number"
                                                name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
                                                id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
                                                <?/*?> value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"<?*/?>
                                                value="<?=('' != $arItem["VALUES"]["MAX"]["HTML_VALUE"]) ? $arItem["VALUES"]["MAX"]["HTML_VALUE"] : (float) $arItem["VALUES"]["MAX"]["VALUE"]?>"
                                                min="<?=(float)$arItem["VALUES"]["MIN"]["VALUE"];?>"
                                                max="<?=(float)$arItem["VALUES"]["MAX"]["VALUE"];?>"
                                                step="<?echo pow(10, -$precision)?>"
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
                                            <div class="bx-ui-slider-range"     id="drag_tracker_<?=$key?>"  style="left: 0;right: 0;">
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
                                    if (!in_array($arItem["CODE"], $arHiddenProps)) {
                                        if ($arItem["VALUES"]["MIN"]["HTML_VALUE"]){
                                            $arPropHaveChecked[$key]["MIN"] = $arItem["VALUES"]["MIN"]["HTML_VALUE"];
                                        }
                                        if ($arItem["VALUES"]["MAX"]["HTML_VALUE"]){
                                            $arPropHaveChecked[$key]["MAX"] = $arItem["VALUES"]["MAX"]["HTML_VALUE"];
                                        }
                                    }
                                    break;
                                case "B"://NUMBERS
                                    ?>
                                    <div class="col-xs-6 bx-filter-parameters-box-container-block bx-left">
                                        <i class="bx-ft-sub"><?=getMessage('CT_BCSF_FILTER_FROM')?></i>
                                        <div class="bx-filter-input-container">
                                            <input
                                                class="min-price form-control quantity__input"
                                                type="number"
                                                name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
                                                id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
                                                <?/*?> value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"<?*/?>
                                                value="<?=('' != $arItem["VALUES"]["MIN"]["HTML_VALUE"]) ? $arItem["VALUES"]["MIN"]["HTML_VALUE"] : (float) $arItem["VALUES"]["MIN"]["VALUE"]?>"
                                                min="<?=(float)$arItem["VALUES"]["MIN"]["VALUE"];?>"
                                                max="<?=(float)$arItem["VALUES"]["MAX"]["VALUE"];?>"
                                                size="5"
                                                onkeyup="smartFilter.keyup(this)"
                                                />
                                        </div>
                                    </div>
                                    <div class="col-xs-6 bx-filter-parameters-box-container-block bx-right">
                                        <i class="bx-ft-sub"><?=getMessage('CT_BCSF_FILTER_TO')?></i>
                                        <div class="bx-filter-input-container">
                                            <input
                                                class="max-price form-control quantity__input"
                                                type="number"
                                                name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
                                                id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
                                                <?/*?> value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"<?*/?>
                                                value="<?=('' != $arItem["VALUES"]["MAX"]["HTML_VALUE"]) ? $arItem["VALUES"]["MAX"]["HTML_VALUE"] : (float) $arItem["VALUES"]["MAX"]["VALUE"]?>"
                                                min="<?=(float)$arItem["VALUES"]["MIN"]["VALUE"];?>"
                                                max="<?=(float)$arItem["VALUES"]["MAX"]["VALUE"];?>"
                                                size="5"
                                                onkeyup="smartFilter.keyup(this)"
                                                />
                                        </div>
                                    </div>
                                    <?
                                    break;
                                case "G"://CHECKBOXES_WITH_PICTURES
                                    ?>
                                    <div class="bx-filter-param-btn-inline offer_prop-color">
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
                                        $class = "offer_prop__value";
                                        if ($ar["CHECKED"])
                                            $class.= " checked";
                                        if ($ar["DISABLED"])
                                            $class.= " disabled";
                                        ?>
                                        <label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label <?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'checked');">
                                            <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                <span class="offer_prop__icon">
                                                    <span class="offer_prop__img" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
                                                </span>
                                            <?endif?>
                                        </label>
                                    <?endforeach?>
                                    </div>
                                    <?
                                    if (!in_array($arItem["CODE"], $arHiddenProps)) {
                                        if ($arItem["VALUES"]["MIN"]["HTML_VALUE"]){
                                            $arPropHaveChecked[$key]["MIN"] = $arItem["VALUES"]["MIN"]["HTML_VALUE"];
                                        }
                                        if ($arItem["VALUES"]["MAX"]["HTML_VALUE"]){
                                            $arPropHaveChecked[$key]["MAX"] = $arItem["VALUES"]["MIN"]["HTML_VALUE"];
                                        }
                                    }
                                    break;
                                case "H"://CHECKBOXES_WITH_PICTURES_AND_LABELS
                                    ?>
                                    <div class="bx-filter-param-btn-block offer_prop-color">
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
                                        $class = "offer_prop__value";
                                        if ($ar["CHECKED"])
                                            $class.= " checked";
                                        if ($ar["DISABLED"])
                                            $class.= " disabled";
                                        ?>
                                        <label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label <?=$class?> clearfix" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'checked');">
                                            <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                <span class="offer_prop__icon bx-filter-btn-color-icon">
                                                    <span class="offer_prop__img" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
                                                </span>
                                            <?endif?>
                                            <span class="bx-filter-param-text" title="<?=$ar["VALUE"];?>"><?=$ar["VALUE"];?><?
                                            if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
                                                ?> (<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)<?
                                            endif;?></span>
                                        </label>
                                        <?php
                                        if (!in_array($arItem["CODE"], $arHiddenProps) && $ar["CHECKED"]) {
                                            $arPropHaveChecked[$key][$val] = $ar["VALUE"];
                                        }
                                        ?>
                                    <?endforeach?>
                                    </div>
                                    <?
                                    break;
                                case "P"://DROPDOWN
                                    $checkedItemExist = false;
                                    $dropdownId = $this->getEditAreaId('dd');
                                    ?>
                                    <div class="bx-filter-select-container dropdown select">
                                        <div class="bx-filter-select-block dropdown-toggle select__btn" id="<?=$dropdownId;?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <svg class="select__icon icon-svg"><use xlink:href="#svg-down-round"></use></svg>
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
                                                    echo getMessage("CT_BCSF_FILTER_ALL");
                                                }
                                                ?>
                                            </div>
                                            <?/*<div class="bx-filter-select-arrow"></div>*/?>
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
                                                <?php
                                                if (!in_array($arItem["CODE"], $arHiddenProps) && !$checkedItemExist && $ar["CHECKED"]) {
                                                    $checkedItemExist = $ar;
                                                    $arPropHaveChecked[$key][$val] = $ar["VALUE"];
                                                }
                                                ?>
                                            <?endforeach?>
                                        </div>
                                            <ul class="bx-filter-select-popup dropdown-menu" aria-labelledby="<?=$dropdownId;?>"<?/*" data-role="dropdownContent" style="display: none;"*/?>>
                                                    <li>
                                                        <label for="<?="all_".$arCur["CONTROL_ID"]?>" class="bx-filter-param-label" data-role="label_<?="all_".$arCur["CONTROL_ID"]?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape("all_".$arCur["CONTROL_ID"])?>')">
                                                            <? echo getMessage("CT_BCSF_FILTER_ALL"); ?>
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
                                    <?
                                    break;
                                case "R"://DROPDOWN_WITH_PICTURES_AND_LABELS
                                    $dropdownId = $this->getEditAreaId('dd');
                                    ?>
                                    <div class="bx-filter-select-container dropdown select offer_prop-color">
                                        <div class="dropdown-toggle select__btn" id="<?=$dropdownId;?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <svg class="select__icon icon-svg"><use xlink:href="#svg-down-round"></use></svg>
                                            <div class="bx-filter-select-text fix" data-role="currentOption">
                                                <?
                                                $checkedItemExist = false;
                                                foreach ($arItem["VALUES"] as $val => $ar):
                                                    if ($ar["CHECKED"])
                                                    {
                                                    ?>
                                                        <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                            <span class="offer_prop__icon bx-filter-btn-color-icon">
                                                                <span class="offer_prop__img" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
                                                            </span>
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
                                                    echo getMessage("CT_BCSF_FILTER_ALL");
                                                }
                                                ?>
                                            </div>
                                            <?/*<div class="bx-filter-select-arrow"></div>*/?>
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
                                                <?php
                                                if (!in_array($arItem["CODE"], $arHiddenProps) && !$checkedItemExist && $ar["CHECKED"]){
                                                    $checkedItemExist = $ar;
                                                    $arPropHaveChecked[$key][$val] = $ar["VALUE"];
                                                }
                                                ?>
                                            <?endforeach?>
                                        </div>
                                            <ul class="dropdown-menu" aria-labelledby="<?=$dropdownId;?>"<?/*data-role="dropdownContent" style="display: none"*/?>>
                                                    <li style="border-bottom: 1px solid #e5e5e5;padding-bottom: 5px;margin-bottom: 5px;">
                                                        <label for="<?="all_".$arCur["CONTROL_ID"]?>" class="bx-filter-param-label" data-role="label_<?="all_".$arCur["CONTROL_ID"]?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape("all_".$arCur["CONTROL_ID"])?>')">
                                                            <span class="bx-filter-btn-color-icon all"></span>
                                                            <? echo getMessage("CT_BCSF_FILTER_ALL"); ?>
                                                        </label>
                                                    </li>
                                                <?
                                                foreach ($arItem["VALUES"] as $val => $ar):
                                                    $class = "offer_prop__value";
                                                    if ($ar["CHECKED"])
                                                        $class.= " selected";
                                                    if ($ar["DISABLED"])
                                                        $class.= " disabled";
                                                ?>
                                                    <li>
                                                        <label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label <?=$class?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')">
                                                            <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                                <span class="offer_prop__icon bx-filter-btn-color-icon">
                                                                    <span class="offer_prop__img" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
                                                                </span>
                                                            <?endif?>
                                                            <span class="bx-filter-param-text">
                                                                <?=$ar["VALUE"]?>
                                                            </span>
                                                        </label>
                                                    </li>
                                                <?endforeach?>
                                            </ul>
                                    </div>
                                    <?
                                    break;
                                case "K"://RADIO_BUTTONS
                                    ?>
                                    <div class="bx-filter-param radio">
                                        <label class="bx-filter-param-label" for="<? echo "all_".$arCur["CONTROL_ID"] ?>">
                                            <span class="bx-filter-input-checkbox">
                                                <input
                                                    type="radio"
                                                    value=""
                                                    name="<? echo $arCur["CONTROL_NAME_ALT"] ?>"
                                                    id="<? echo "all_".$arCur["CONTROL_ID"] ?>"
                                                    onclick="smartFilter.click(this)"
                                                />
                                                <i class="radio__icon"></i>
                                                <span class="bx-filter-param-text"><? echo getMessage("CT_BCSF_FILTER_ALL"); ?></span>
                                            </span>
                                        </label>
                                    </div>
                                    <?foreach($arItem["VALUES"] as $val => $ar):?>
                                        <div class="bx-filter-param radio">
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
                                                    <i class="radio__icon"></i>
                                                    <span class="bx-filter-param-text" title="<?=$ar["VALUE"];?>"><?=$ar["VALUE"];?><?
                                                    if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
                                                        ?>&nbsp;(<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)<?
                                                    endif;?></span>
                                                </span>
                                            </label>
                                        <?php
                                        if (!in_array($arItem["CODE"], $arHiddenProps) && $ar["CHECKED"]) {
                                            $arPropHaveChecked[$key][$val] = $ar["VALUE"];
                                        }
                                        ?>
                                        </div>
                                    <?endforeach;?>
                                    <?
                                    break;
                                case "U"://CALENDAR
                                    ?>
                                    <div class="col-xs-6 bx-filter-parameters-box-container-block bx-left"><div class="bx-filter-input-container bx-filter-calendar-container">
                                        <?$APPLICATION->IncludeComponent(
                                            'bitrix:main.calendar',
                                            'al',
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
                                    <div class="col-xs-6 bx-filter-parameters-box-container-block bx-right"><div class="bx-filter-input-container bx-filter-calendar-container">
                                        <?$APPLICATION->IncludeComponent(
                                            'bitrix:main.calendar',
                                            'al',
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
                                    <?
                                    break;
                                default://CHECKBOXES
                                    ?>
                                    <?php if ($IS_COLOR || $IS_BUTTON): ?>
                                        <div class="bx-filter-param-btn-inline offer_prop<?php if ($IS_COLOR): ?>-color<?php elseif ($IS_BUTTON): ?>-btn <?php endif; ?> clearfix">

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
                                            $class = "offer_prop__value";
                                            if ($ar["CHECKED"])
                                                $class.= " checked";
                                            if ($ar["DISABLED"])
                                                $class.= " disabled";
                                            ?>
                                            <label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label <?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'checked');">

                                                <?php if ($IS_COLOR): ?>
                                                    <span class="offer_prop__icon bx-filter-btn-color-icon">
                                                        <span class="offer_prop__img" style="background-color:<?=$ar["RGB"];?>;" title="<?=$ar["VALUE"];?>"></span>
                                                    </span>
                                                <?php elseif ($IS_BUTTON): ?>
                                                    <?php
                                                    echo $ar["VALUE"];
                                                    if ($arParams["DISPLAY_ELEMENT_COUNT"] !== 'N' && isset($ar["ELEMENT_COUNT"])): ?>
                                                        (<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                            </label>
                                            <?php
                                            if (!in_array($arItem["CODE"], $arHiddenProps) && $ar['CHECKED']) {
                                                $arPropHaveChecked[$key][$val] = $ar["VALUE"];
                                            }
                                            ?>
                                        <?endforeach?>
                                        </div>

                                    <?php else: ?>

                                        <?foreach($arItem["VALUES"] as $val => $ar):?>
                                            <div class="bx-filter-param checkbox<?php if ($IS_COLOR || $IS_BUTTON): ?> offer_prop__value<?php endif; ?>">
                                                <label data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label <? echo $ar["DISABLED"] ? 'disabled': '' ?>" for="<? echo $ar["CONTROL_ID"] ?>">
                                                    <input
                                                        type="checkbox"
                                                        value="<? echo $ar["HTML_VALUE"] ?>"
                                                        name="<? echo $ar["CONTROL_NAME"] ?>"
                                                        id="<? echo $ar["CONTROL_ID"] ?>"
                                                        <? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
                                                        onclick="smartFilter.click(this)"
                                                    />
                                                    <svg class="checkbox__icon icon-check icon-svg"><use xlink:href="#svg-check"></use></svg>
                                                    <span class="bx-filter-param-text" title="<?=$ar["VALUE"];?>"><?=$ar["VALUE"];?><?
                                                    if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
                                                        ?>&nbsp;(<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)<?
                                                    endif;?></span>
                                                <?php
                                                if (!in_array($arItem["CODE"], $arHiddenProps) && $ar['CHECKED']) {
                                                    $arPropHaveChecked[$key][$val] = $ar["VALUE"];
                                                }
                                                ?>
                                                </label>
                                            </div>
                                        <?endforeach;?>

                                    <?php endif; ?>
                            <?
                            }
                            ?>
                            </div>
                            <div style="clear: both"></div>
                            <?php if (($IS_SCROLABLE) && count($arItem["VALUES"]) > 7): ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?
                }
                ?>
            </div><!--//row-->
            <div class="row">
                <div class="col-xs-12 bx-filter-button-box">
                    <div class="bx-filter-block">
                        <div class="bx-filter-parameters-box-container">
                            <input
                                class="bx-filter-submit btn btn1"
                                type="submit"
                                id="set_filter"
                                name="set_filter"
                                value="<?=getMessage("CT_BCSF_SET_FILTER")?>"
                            />
                            <input
                                class="bx-filter-reset"
                                type="submit"
                                id="del_filter"
                                name="del_filter"
                                value="<?=getMessage("CT_BCSF_DEL_FILTER")?>"
                            />
                            <div class="bx-filter-popup-result <?=$arParams["POPUP_POSITION"]?>" id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])) echo 'style="display:none"'; else echo 'style="display:inline-block;"';?>>
                                <?echo getMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>
                                <a class="modef__link" href="<?echo $arResult["FILTER_URL"]?>"><?echo getMessage("CT_BCSF_FILTER_SHOW")?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clb"></div>
        </form>
    </div>
</div>

<script>
    var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>', '<?=CUtil::JSEscape($arParams["FILTER_VIEW_MODE"])?>', <?=CUtil::PhpToJSObject($arResult["JS_FILTER_PARAMS"])?>);
    smartFilter.templateFolder = '<?=$templateFolder?>';
</script>

<?php $this->SetViewTarget('catalog_filterin'); ?>
    <div class="filterin"  id="<?=$arParams['TEMPLATE_AJAXID']?>_filterin">
    <?php if (!empty($arPropHaveChecked)): ?>
        <?php foreach($arPropHaveChecked as $key => $arValues): ?>
            <span class="filterin__prop">
                <span class="filterin__name"><?=$arResult["ITEMS"][$key]["NAME"]?>:</span>

                <?php foreach($arValues as $val => $sValue): ?>
                    <?php
                    $CONTROL_ID = $arResult["ITEMS"][$key]["VALUES"][$val]["CONTROL_ID"];

                    if (in_array($arResult["ITEMS"][$key]["CODE"], $arParams["PRICES_GROUPED"])) {
                        $CONTROL_ID = $arResult["ITEMS"][$key]["GROUP_VALUES"]["PRICE_GROUP_DIAPAZONS"][$val]["CONTROL_ID"];
                    }

                    ?>
                    <label for="<?=$CONTROL_ID;?>" data-role="label_<?=$CONTROL_ID;?>" class="filterin__val" onClick="smartFilter.uncheck(this, '<?=(isset($arResult['ITEMS'][$key]['PRICE']) ? $arResult['ITEMS'][$key]['ENCODED_ID'] : $key)?>');"><?
                        if ('MIN' == $val){
                            echo getMessage('CT_BCSF_FILTER_FROM');
                        } elseif ('MAX' == $val) {
                            echo getMessage('CT_BCSF_FILTER_TO');
                        }
                        echo ' '.$sValue;
                    ?><i class="filterin__hint hint del"><svg class="icon-close icon-svg"><use xlink:href="#svg-close"></use></svg></i></label>
                <?php endforeach; ?>
            </span>
        <?php endforeach; ?>
        <span onclick="var delFilter; (delFilter = BX('del_filter')) ?  delFilter.click() : 0;" class="filterin__val" href=" javascript:void(0)">
            <?=getMessage('CT_BCSF_DEL_FILTER')?><i class="filterin__hint hint reset"><svg class="icon-close icon-svg"><use xlink:href="#svg-close"></use></svg></i>
        </span>
    <?php endif; ?>
    </div>
<?php
$this->EndViewTarget();