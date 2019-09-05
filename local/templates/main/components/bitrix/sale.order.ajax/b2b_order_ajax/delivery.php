<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript">
    function calculateDeliveryCount(n)
    {
            $("input[name=delivery_count]").val(n);
    }
</script><?
if(!empty($arResult["DELIVERY"]))
{

$showCount = $arParams["ORDER_ITEM_SHOW_COUNT"];
$count = 0;
$bool = false;

if(isset($_REQUEST["delivery_count"]) && $_REQUEST["delivery_count"]>0 && $showCount<$_REQUEST["delivery_count"])
    $showCount = $_REQUEST["delivery_count"];
?>
<input name="delivery_count" value="<?=isset($_REQUEST["delivery_count"])?$_REQUEST["delivery_count"]:0?>" type="hidden" />
<div class="col-sm-12 <?if(isset($d2p)):?>sm-padding-left-no<?else:?>sm-padding-right-no<?endif?>">
    <div class="section block_delivery js_radio">
        <div class="section_title">
            <div class="section_title_in">
                <span><?=GetMessage("MS_ORDER_DELIVERY")?></span>
            </div>
        </div>
        <?
        foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
        {
            if ($delivery_id !== 0 && intval($delivery_id) <= 0)
            {
                foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile)
                {
                    $count++;
                    if($count>$showCount && !$bool)
                    {
                        $bool = true;
                    ?>
                        <div id="close_delivery" class="close_block">
                    <?
                    }
                    ?>
                    <div class="wrap_item_delivery">
                        <input
                                type="radio"
                                id="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>"
                                name="<?=htmlspecialcharsbx($arProfile["FIELD_NAME"])?>"
                                value="<?=$delivery_id.":".$profile_id;?>"
                                <?=$arProfile["CHECKED"] == "Y" ? "checked=\"checked\"" : "";?>
                                onclick="calculateDeliveryCount(<?=$count?>);submitForm();"
                        />
                        <label class="item_delivery <?if($arProfile["CHECKED"] == "Y"):?>label-active<?endif;?>" for="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>"  onclick="BX('ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>').checked=true;calculateDeliveryCount(<?=$count?>);submitForm();">
                            <?
                                if (count($arDelivery["LOGOTIP"]) > 0):

                                    $arFileTmp = CFile::ResizeImageGet(
                                        $arDelivery["LOGOTIP"]["ID"],
                                        array("width" => "85", "height" =>"45"),
                                        BX_RESIZE_IMAGE_PROPORTIONAL,
                                        true
                                    );

                                    $deliveryImgURL = $arFileTmp["src"];
                                else:
                                    $deliveryImgURL = $templateFolder."/images/logo-default-d.gif";
                                endif;
                                if($arDelivery["ISNEEDEXTRAINFO"] == "Y")
                                    $extraParams = "showExtraParamsDialog('".$delivery_id.":".$profile_id."');";
                                else
                                    $extraParams = "";

                            ?>
                            <div class="">
                                <div class="">
                                    <div class="block_img">
                                        <img class="img-responsive" src="<?=$deliveryImgURL?>" width="23px" height="auto"  alt=""/>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="block_text">
                                        <h3 class="title"><?=htmlspecialcharsbx($arProfile["TITLE"])//htmlspecialcharsbx($arDelivery["TITLE"])." (".htmlspecialcharsbx($arProfile["TITLE"]).")";?></h3>
                                        <div class="">
                                            <div class="">
                                                <p class="delivery_text">
                                                <?if (strlen($arProfile["DESCRIPTION"]) > 0):?>
                                                    <?=nl2br($arProfile["DESCRIPTION"])?>
                                                <?else:?>
                                                    <?=nl2br($arDelivery["DESCRIPTION"])?>
                                                <?endif;?>
                                                </p>
                                            </div>
                                            <div class="">
                                                <div class="block_cost">
                                                     <?
                                                     if($arProfile["CHECKED"] == "Y" && doubleval($arResult["DELIVERY_PRICE"]) > 0):
                                                     ?>
                                                        <div><?=$arResult["DELIVERY_PRICE_FORMATED"]?></div>
                                                        <?
                                                        if ((isset($arResult["PACKS_COUNT"]) && $arResult["PACKS_COUNT"]) > 1):
                                                            echo GetMessage('SALE_PACKS_COUNT').': '.$arResult["PACKS_COUNT"].'';
                                                        endif;

                                                    else:
                                                    $APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', 'ms_delivery_calculator', array(
                                                        "NO_AJAX" => $arParams["DELIVERY_NO_AJAX"],
                                                        "DELIVERY" => $delivery_id,
                                                        "PROFILE" => $profile_id,
                                                        "ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
                                                        "ORDER_PRICE" => $arResult["ORDER_PRICE"],
                                                        "LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
                                                        "LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
                                                        "CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
                                                        "ITEMS" => $arResult["BASKET_ITEMS"],
                                                        "EXTRA_PARAMS_CALLBACK" => $extraParams
                                                    ), null, array('HIDE_ICONS' => 'Y'));
                                                    endif;
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    <?
                }
            }else{
            $count++;
            if($count>$showCount && !$bool)
            {
                $bool = true;
                ?>
                <div id="close_delivery" class="close_block">
                <?
            }

            if (count($arDelivery["STORE"]) > 0)
                $clickHandler = "onClick = \"fShowStore('".$arDelivery["ID"]."','".$arParams["SHOW_STORES_IMAGES"]."','".$width."','".SITE_ID."')\";";
            else
                $clickHandler = "onClick = \"BX('ID_DELIVERY_ID_".$arDelivery["ID"]."').checked=true;submitForm();\"";
            ?>
                <div class="wrap_item_delivery">
					<?if ($arDelivery["ID"] == $arResult['SDEK']):?>
		                <input type="radio"
		                       id="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>"
		                       name="<?=htmlspecialcharsbx($arDelivery["FIELD_NAME"])?>"
		                       value="<?= $arDelivery["ID"] ?>"<?if ($arDelivery["CHECKED"]=="Y") echo " checked";?>
		                       onclick="calculateDeliveryCount(<?=$count?>);IPOLSDEK_pvz.selectPVZ('<?=$arDelivery["ID"]?>','PVZ');"
		                />
					<?else:?>
                    <input type="radio"
                                id="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>"
                                name="<?=htmlspecialcharsbx($arDelivery["FIELD_NAME"])?>"
                                value="<?= $arDelivery["ID"] ?>"<?if ($arDelivery["CHECKED"]=="Y") echo " checked";?>
                                onclick="calculateDeliveryCount(<?=$count?>);submitForm();"
                                />
					<?endif;?>
                    <label class="item_delivery <?if($arDelivery["CHECKED"] == "Y"):?>label-active<?endif;?>" for="ID_DELIVERY_ID_<?=$arDelivery["ID"]?>" <?=$clickHandler?>>
                        <?
                        if (count($arDelivery["LOGOTIP"]) > 0):

                            $arFileTmp = CFile::ResizeImageGet(
                                        $arDelivery["LOGOTIP"]["ID"],
                                        array("width" => "85", "height" =>"45"),
                                        BX_RESIZE_IMAGE_PROPORTIONAL,
                                        true
                            );

                            $deliveryImgURL = $arFileTmp["src"];
                        else:
                            $deliveryImgURL = $templateFolder."/images/logo-default-d.gif";
                        endif;

                        if($arDelivery["ISNEEDEXTRAINFO"] == "Y")
                            $extraParams = "showExtraParamsDialog('".$delivery_id.":".$profile_id."');";
                        else
                            $extraParams = "";
                        ?>
                        <div class="label_content">
                            <div class="block_img_wrap">
                                <div class="block_img">
                                    <img class="img-responsive" src="<?=$deliveryImgURL?>" width="23px" height="auto"  alt=""/>
                                </div>
                            </div>
                            <div class="block_text_wrap">
                                <div class="block_text">
                                    <h3 class="title"><?= htmlspecialcharsbx($arDelivery["OWN_NAME"])//htmlspecialcharsbx($arDelivery["NAME"])?></h3>
                                    <div class="block_delivery_cost">
                                        <div class="block_delivery_text">
                                            <p class="delivery_text">
                                            <?
                                            if (strlen($arDelivery["DESCRIPTION"])>0)
                                                echo $arDelivery["DESCRIPTION"]."<br />"
                                            ?>
                                            </p>
                                        </div>
                                        <div class="block_cost">
                                            <?if(Bitrix\Main\Config\Option::get("main", "~sale_converted_15", 'N') == 'Y' && empty($arDelivery["PRICE"])):?>
                                                <?$APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', 'ms_delivery_calculator', array(
                                                    "NO_AJAX" => $arParams["DELIVERY_NO_AJAX"],
                                                    "DELIVERY_ID" => $delivery_id,
                                                    "ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
                                                    "ORDER_PRICE" => $arResult["ORDER_PRICE"],
                                                    "LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
                                                    "LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
                                                    "CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
                                                    "ITEMS" => $arResult["BASKET_ITEMS"],
                                                    "EXTRA_PARAMS_CALLBACK" => $extraParams
                                                ), null, array('HIDE_ICONS' => 'Y'));?>

                                            <?else:?>
                                                <p>&nbsp;&ndash;&nbsp;<?=(isset($arDelivery['DELIVERY_DISCOUNT_PRICE_FORMATED']) && !empty($arDelivery['DELIVERY_DISCOUNT_PRICE_FORMATED']))?$arDelivery['DELIVERY_DISCOUNT_PRICE_FORMATED']:$arDelivery["PRICE_FORMATED"]?>
                                                    <?if (strlen($arDelivery["PERIOD_TEXT"])>0):?>
                                                        <br />
                                                        <span class="delivery__period"><?=GetMessage('SALE_SADC_TRANSIT')?></span>
                                                        <?=$arDelivery["PERIOD_TEXT"];?>
                                                    <?endif;?>
                                                </p>
                                            <?endif;?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
                <?
            }
        }

        if($count>$showCount)
        {
            ?>
            </div>
            <div class="wrap_block_btn">
                <span class="display_close_block close" onclick="open_close_block(this, '#close_delivery')">
                    <span class="first"><?=GetMessage("MS_ORDER_SHOW")?></span>
                    <span class="second"><?=GetMessage("MS_ORDER_HIDE")?></span>
                </span>
            </div>
            <?
        }
        ?>
    </div>
</div>
<?}?>
<script type="text/javascript">
	function IPOLSDEK_DeliveryChangeEvent(id)
	{
		$('#'+id).prop('checked', 'Y');
		submitForm();
	}
</script>