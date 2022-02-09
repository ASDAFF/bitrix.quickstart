<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
    function PrintPropsForm($arSource=Array(), $PRINT_TITLE = "", $arParams)
    {
    ?>

    <?
        foreach($arSource as $arProperties)
        {
         if($arProperties["GROUP_NAME"]=="Для платежа"){continue;}?>

        <label class="b-cart-field__label"><?= $arProperties["NAME"] ?><?
                if($arProperties["REQUIED_FORMATED"]=="Y")
                {
                ?><span class="b-star">*</span><?
                }
            ?>
        </label>
        <?
            if($arProperties["TYPE"] == "CHECKBOX")
            {
            ?>
            <input type="checkbox" name="<?=$arProperties["FIELD_NAME"]?>" value="Y"<?if ($arProperties["CHECKED"]=="Y") echo " checked";?>>
            <?
            }
            elseif($arProperties["TYPE"] == "TEXT")
            {
            ?>
            <input type="text" maxlength="250" size="<?=$arProperties["SIZE1"]?>" value="<?=$arProperties["VALUE"]?>" name="<?=$arProperties["FIELD_NAME"]?>">
            <?
            }
            elseif($arProperties["TYPE"] == "SELECT")
            {
            
            ?>
            <select class="b-cart-field__select" name="<?=$arProperties["FIELD_NAME"]?>" size="1">
                <?
                    foreach($arProperties["VARIANTS"] as $arVariants)
                    {
                        
                    ?>
                    <option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
                    <?
                    }
                ?>
            </select>
            <?
            }
            elseif ($arProperties["TYPE"] == "MULTISELECT")
            {
            ?>
            <select multiple name="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>">
                <?
                    foreach($arProperties["VARIANTS"] as $arVariants)
                    {
                    ?>
                    <option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
                    <?
                    }
                ?>
            </select>
            
            <?
            }
            elseif ($arProperties["TYPE"] == "TEXTAREA")
            {
            ?>
            <textarea rows="3" class="b-cart-field__input" cols="<?=$arProperties["SIZE1"]?>" name="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["VALUE"]?></textarea>
            <?
            }
            elseif ($arProperties["TYPE"] == "LOCATION")
            {
                $value = 0;
                foreach ($arProperties["VARIANTS"] as $arVariant) 
                {
                    if ($arVariant["SELECTED"] == "Y") 
                    {
                        $value = $arVariant["ID"]; 
                        break;
                    }
                }

                if ($arParams["USE_AJAX_LOCATIONS"] == "Y"):
                    $GLOBALS["APPLICATION"]->IncludeComponent(
                        "bitrix:sale.ajax.locations",
                        ".default",
                        array(
                            "AJAX_CALL" => "Y",
                            "COUNTRY_INPUT_NAME" => "COUNTRY_".$arProperties["FIELD_NAME"],
                            "REGION_INPUT_NAME" => "REGION_".$arProperties["FIELD_NAME"],
                            "CITY_INPUT_NAME" => $arProperties["FIELD_NAME"],
                            "CITY_OUT_LOCATION" => "Y",
                            "LOCATION_VALUE" => $value,
                            "ORDER_PROPS_ID" => $arProperties["ID"],
                            "ONCITYCHANGE" => "",
                        ),
                        null,
                        array('HIDE_ICONS' => 'Y')
                    );                        
                    else:
                ?>
                <select class="b-cart-field__select" name="<?=$arProperties["FIELD_NAME"]?>" size="1">
                    <?
                        foreach($arProperties["VARIANTS"] as $arVariants)
                        {
                        ?>
                        <option value="<?=$arVariants["ID"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
                    <?
                    }
                ?>
            </select>
            <input type="submit" name="reload" value="">
            <?
                endif;
            }
            elseif ($arProperties["TYPE"] == "RADIO")
            {
                foreach($arProperties["VARIANTS"] as $arVariants)
                {
                ?>
                <input type="radio" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["ID"]?>" value="<?=$arVariants["VALUE"]?>"<?if($arVariants["CHECKED"] == "Y") echo " checked";?>> <label for="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["ID"]?>"><?=$arVariants["NAME"]?></label><br />
                <?
                }
            }


        ?><br /><?echo $arProperties["DESCRIPTION"] ?><?

        ?>

        <?
        }
    ?>

    <?
        return true;
    }

?>
<br />


<?
if ($arPersType = CSalePersonType::GetByID($_POST["PERSON_TYPE"]))
{
   
   $_POST["person"] = $arPersType['NAME'];
   
}
?>



<div class="b-cart__list clearfix">
    <div class="b-cart-field__info">
        Вы заказали:<br /><b><?=$_POST["cnt"]?> товара на сумму <?=$_POST["price"]?>.–</b>
        <br /><br />
        Информация о покупателе:<br />
        <b><?=$_POST["person"]?></b><br />
        <a href="mailto:<?=$_POST["USER_LOGIN"]?>"><?=$_POST["USER_LOGIN"]?></a><br />
        <b><?=$_POST["USER_NAME"]?>&nbsp;<?=$_POST["USER_LAST_NAME"]?></b><br />
        <b><?=$_POST["phone"]?></b>
    </div>
    <div class="b-cart-field m-cart-field__first">

        <?
            PrintPropsForm($arResult["PRINT_PROPS_FORM"]["USER_PROPS_Y"], GetMessage("SALE_NEW_PROFILE_TITLE"), $arParams);
            PrintPropsForm($arResult["PRINT_PROPS_FORM"]["USER_PROPS_N"], GetMessage("SALE_NEW_PROFILE_TITLE"), $arParams);
        ?>
    </div>
    <div class="b-cart-field">

        <?
            foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
            {
                if ($delivery_id !== 0 && intval($delivery_id) <= 0):
                ?>

                <?
                    foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile)
                    {
                    ?>
                    <label for="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>" class="b-cart-field__label">
                    <input type="radio" id="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>" name="<?=$arProfile["FIELD_NAME"]?>" value="<?=$delivery_id.":".$profile_id;?>" <?=$arProfile["CHECKED"] == "Y" ? "checked=\"checked\"" : "";?> />
                    
                        <?=$arProfile["TITLE"]?><?if (strlen($arProfile["DESCRIPTION"]) > 0):?>
                            <?=nl2br($arProfile["DESCRIPTION"])?><?endif;?>
                    </label>
                    <?
                        $APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
                                "NO_AJAX" => $arParams["SHOW_AJAX_DELIVERY_LINK"] == 'S' ? 'Y' : 'N',
                                "DELIVERY" => $delivery_id,
                                "PROFILE" => $profile_id,
                                "ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
                                "ORDER_PRICE" => $arResult["ORDER_PRICE"],
                                "LOCATION_TO" => $arResult["DELIVERY_LOCATION"],
                                "LOCATION_ZIP" => $arResult['DELIVERY_LOCATION_ZIP'],
                                "CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
                            ));
                    ?>
                    <?if ($arParams["SHOW_AJAX_DELIVERY_LINK"] == 'N'):?>
                        <script type="text/javascript">deliveryCalcProceed({STEP:1,DELIVERY:'<?=CUtil::JSEscape($delivery_id)?>',PROFILE:'<?=CUtil::JSEscape($profile_id)?>',WEIGHT:'<?=CUtil::JSEscape($arResult["ORDER_WEIGHT"])?>',PRICE:'<?=CUtil::JSEscape($arResult["ORDER_PRICE"])?>',LOCATION:'<?=intval($arResult["DELIVERY_LOCATION"])?>',CURRENCY:'<?=CUtil::JSEscape($arResult["BASE_LANG_CURRENCY"])?>'})</script>
                        <?endif;?>

                    <?
                    } // endforeach
                ?>
                <?
                    else:
                ?>
                <label for="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>" class="b-cart-field__label">
                <input type="radio" id="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>" name="<?=$arDelivery["FIELD_NAME"]?>" value="<?= $arDelivery["ID"] ?>"<?if ($arDelivery["CHECKED"]=="Y") echo " checked";?>>
                
                    <?= $arDelivery["NAME"] ?>
                    <?
                        if (strlen($arDelivery["PERIOD_TEXT"])>0)
                        {
                            echo $arDelivery["PERIOD_TEXT"];
                        ?><?
                        }
                    ?>
                    <b><?=$arDelivery["PRICE_FORMATED"]?></b><br />
                    <?
                        if (strlen($arDelivery["DESCRIPTION"])>0)
                        {
                        ?>
                        <?=$arDelivery["DESCRIPTION"]?><br />
                        <?
                        }
                    ?>
                </label>

            <?
                endif;

            } // endforeach
        ?>
    </div>
</div>
<div class="b-cart__list">
    <div class="b-cart__btn clearfix">
        <div class="b-cart__btn_right m-right">
            <input type="submit" name="backButton" value="Назад" class="b-button">
            <input type="submit" name="contButton" value="Продолжить оформление" class="b-button m-orange">
        </div>
    </div>
</div>

