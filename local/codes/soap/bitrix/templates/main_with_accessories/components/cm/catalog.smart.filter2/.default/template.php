<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.ui.widget.js');?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.ui.mouse.js');?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.ui.slider.js');?>
<?$IsIe = (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")) ? true : false;?>
<script type="text/javascript">

    /*function activeProp(element, propID)
    {
    $(".horizontalfilter li").removeClass("active");
    $(element).addClass("active");
    $(".cnt").removeClass("active");
    $("#propBlockValues_"+propID).addClass("active");
    }                            */
</script>
<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" id="form_filtering" action="<?echo $arResult["FORM_ACTION"]?>" method="get" class="smartfilter">
<input type="hidden" name="section_id" value="<?echo $arResult["SECTIONID"]?>"/>
    <?
    foreach($arResult["HIDDEN"] as $arItem):?>
        <input type="hidden" name="<?echo $arItem["CONTROL_NAME"]?>" id="<?echo $arItem["CONTROL_ID"]?>" value="<?echo $arItem["HTML_VALUE"]?>"/>
        <?endforeach;?>
    <div class="b-sidebar-filter m-sidebar">
        <div class="b-sidebar__section">
            <?foreach($arResult["ITEMS"] as $key => $arItem):?>
                <?if(isset($arItem["PRICE"])):?>
                    <?
                        if (empty($arItem["VALUES"]["MIN"]["VALUE"])) $arItem["VALUES"]["MIN"]["VALUE"] = 0;
                        if (empty($arItem["VALUES"]["MAX"]["VALUE"])) $arItem["VALUES"]["MAX"]["VALUE"] = 100000;
                        
                    ?>
                    <h2 class="b-sidebar__h2"><?=$arItem["NAME"]?> <span class="b-sidebar__hint">*</span></h2>

                    <div class="b-filter-slider" id="slider-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" style="margin:7px auto 8px"></div>
                    <div class="b-filter-slider__fields clearfix">
                        <div class="m-price__field m-left">
                            <label class="b-filter-slider__label">от</label>
                            <input type="text" class="b-filter-slider__text m-text__max"  name="arrFilter_cf[<?=$arItem["ID"]?>][LEFT]" placeholder="<?echo GetMessage("CT_BCSF_FILTER_FROM")?>" id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" value="<?echo $arItem["VALUES"]["MIN"]["VALUE"]?>" size="5" onkeyup="smartFilter.keyup(this)"/>
                        </div>
                        <div class="m-price__field m-right">
                            <label class="b-filter-slider__label">до</label>
                            <input type="text" class="b-filter-slider__text m-text__max"  name="arrFilter_cf[<?=$arItem["ID"]?>][RIGHT]" placeholder="<?echo GetMessage("CT_BCSF_FILTER_TO")?>" id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>" value="<?echo $arItem["VALUES"]["MAX"]["VALUE"]?>" size="5" onkeyup="smartFilter.keyup(this)" />
                        </div>
                    </div>

                    <script>
                        $(function() {
                                var minprice = <?=CUtil::JSEscape($arItem["VALUES"]["MIN"]["VALUE"])?>;
                                var maxprice = <?=CUtil::JSEscape($arItem["VALUES"]["MAX"]["VALUE"])?>;
                                $("#slider-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").slider({
                                        range: true,
                                        min: minprice,
                                        max: maxprice,
                                        values: [ <?=(empty($arItem["VALUES"]["MIN"]["HTML_VALUE"])) ? CUtil::JSEscape($arItem["VALUES"]["MIN"]["VALUE"]) : CUtil::JSEscape($arItem["VALUES"]["MIN"]["HTML_VALUE"])?>, <?=(empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) ? CUtil::JSEscape($arItem["VALUES"]["MAX"]["VALUE"]) : CUtil::JSEscape($arItem["VALUES"]["MAX"]["HTML_VALUE"])?> ],
                                        slide: function( event, ui ) {
                                            $("#<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").val(ui.values[0]);
                                            $("#<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>").val(ui.values[1]);
                                            smartFilter.keyup(BX("<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"));
                                        }
                                });
                                $("#max-price-<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>").text(maxprice);
                                $("#min-price-<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>").text(minprice);
                                //$(".min-price").val($(".slider-range").slider("values", 0));
                                //$(".max-price").val($(".slider-range").slider("values", 1));
                        });
                    </script>
                    <?unset($arResult["ITEMS"][$key]);?>
                    <?endif;?>
                <?endforeach;?>
        </div>
        <?if (count($arResult["ITEMS"]) > 0):?>



            <?$flag = 0;
		$count=0;?>
            <?foreach($arResult["ITEMS"] as $arItem):?>
                <div class="b-sidebar__section">
                <?$count++;?>
                    <?if(!empty($arItem["VALUES"]) && !isset($arItem["PRICE"])):?>
                        <h2 class="b-sidebar__h2 <?if($arItem["PROPERTY_TYPE"]!="N"){?>b-toggle__btn<?}?><?if($count<=2){?>active<?}?>" id="b-producer<?=$flag;?>"><?=$arItem["NAME"]?> <?if($arItem["HINT"]!=""){?><span class="b-toggle__hint" title="<?=$arItem["HINT"]?>"></span><?}?></h2>
                        <!-- <div  class="<?//=$arItem["CODE"]?><?//if ($flag == 0) echo " active"?>"><span><span><?//=$arItem["NAME"]?></span></span></div>-->
                        <!--<div class="cnt<?//if ($flag == 0) echo " active"?>" id="<?//=$arItem["CODE"]?>">-->
                        <?if($arItem["PROPERTY_TYPE"] == "N" && !isset($arItem["PRICE"])):?>
                            <?
                                //$arItem["VALUES"]["MIN"]["VALUE"];
                                //$arItem["VALUES"]["MAX"]["VALUE"];
                            ?>
                            <div class="b-filter-slider" id="slider-<?echo $arItem["CODE"]?>" style="margin:7px auto 8px"></div>
                            <div class="b-filter-slider__fields clearfix">
                                <div class="m-price__field m-left">
                                    <label class="b-filter-slider__label">от</label>
                                    <input type="text" class="b-filter-slider__text m-text__max"  name="arrFilter_pf[<?=$arItem["CODE"]?>][LEFT]" placeholder="<?echo GetMessage("CT_BCSF_FILTER_FROM")?>" id="<?=$arItem["CODE"]?>MIN" value="<?echo $arItem["VALUES"]["MIN"]["VALUE"]?>" size="5" onkeyup="smartFilter.keyup(this)"/>
                                </div>
                                <div class="m-price__field m-right">
                                    <label class="b-filter-slider__label">до</label>
                                    <input type="text" class="b-filter-slider__text m-text__max"  name="arrFilter_pf[<?=$arItem["CODE"]?>][RIGHT]" placeholder="<?echo GetMessage("CT_BCSF_FILTER_TO")?>" id="<?=$arItem["CODE"]?>MAX" value="<?echo $arItem["VALUES"]["MAX"]["VALUE"]?>" size="5" onkeyup="smartFilter.keyup(this)" />
                                </div>
                            </div>
                            <?if ($arItem["VALUES"]["MIN"]["VALUE"] > 0 && $arItem["VALUES"]["MAX"]["VALUE"] > 0 && $arItem["VALUES"]["MIN"]["VALUE"] < $arItem["VALUES"]["MAX"]["VALUE"]):?>
                                <script>
                                    var minprice2 = <?=CUtil::JSEscape($arItem["VALUES"]["MIN"]["VALUE"])?>;
                                    var maxprice2 = <?=CUtil::JSEscape($arItem["VALUES"]["MAX"]["VALUE"])?>;
                                    $("#slider-<?=$arItem["CODE"]?>").slider({
                                            range: true,
                                            min: minprice2,
                                            max: maxprice2,
                                            values: [ <?=(empty($arItem["VALUES"]["MIN"]["HTML_VALUE"])) ? CUtil::JSEscape($arItem["VALUES"]["MIN"]["VALUE"]) : CUtil::JSEscape($arItem["VALUES"]["MIN"]["HTML_VALUE"])?>, <?=(empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) ? CUtil::JSEscape($arItem["VALUES"]["MAX"]["VALUE"]) : CUtil::JSEscape($arItem["VALUES"]["MAX"]["HTML_VALUE"])?> ],
                                            slide: function( event, ui ) {
                                                $("#<?=$arItem["CODE"]?>MIN").val(ui.values[0]);
                                                $("#<?=$arItem["CODE"]?>MAX").val(ui.values[1]);
                                                smartFilter.keyup(BX("<?echo $arItem["CODE"]?>"));
                                            }
                                    });
                                    $("#max-price-<?echo $arItem["CODE"]?>").text(maxprice2);
                                    $("#min-price-<?echo $arItem["CODE"]?>").text(minprice2);
                                </script>
                                <?endif?>
                            <?elseif(!empty($arItem["VALUES"]) && !isset($arItem["PRICE"])):?>
                            <script>
                                $(document).ready(function() {
                                        $("#b-producer<?=$flag;?>__toggle").tinyscrollbar();
                                });
                            </script>

                            <div id="b-producer<?=$flag;?>__toggle" class="b-toggle__wrapper b-scrollbar">
                                <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
                                <div class="viewport">
                                    <div class="overview">
                                        <?foreach($arItem["VALUES"] as $val => $ar):?>
                                            <div class="b-toggle__field"><label for="<?echo $ar["CONTROL_CODE"]?>" class="<?if ($ar["DISABLED"]): echo 'b-checkbox2 b-checked'; elseif ($ar["CHECKED"]): echo 'b-checkbox b-checked'; else: echo 'b-checkbox';endif;?>"><input type="checkbox" class="filterElem" value="<?=$ar["VALUE"];?>" name="arrFilter_pf[<?=$ar["CONTROL_CODE"];?>][]" id="<?=$ar["CONTROL_CODE"];?>" /><?if(!empty($ar["VALUE_LIST"])):echo $ar["VALUE_LIST"];else:echo $ar["VALUE"];endif;?></label></div>
                                            <?endforeach;?>
                                    </div>
                                </div>
                            </div>

                            <?endif;?>

                        <!--</div>-->
                        <?$flag++;;?>
                        <?endif?>
                </div>
                <?endforeach;?>
        </div>
        <?endif?>
</form>
<script>
    var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($APPLICATION->GetCurPageParam())?>');
    //document._form.submit();
</script>