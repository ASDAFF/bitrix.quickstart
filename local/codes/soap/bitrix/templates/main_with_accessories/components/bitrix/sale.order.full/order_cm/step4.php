<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
    function PrintPropsForm($arSource=Array(), $PRINT_TITLE = "", $arParams)
    {
    ?>

    <?
        foreach($arSource as $arProperties)
        {
        ?>

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






<div class="b-cart__list clearfix">
    <div class="b-cart-field__info">
        Вы заказали:<br /><b>4 товара на сумму 34 800.–</b>
        <br /><br />
        Информация о покупателе:<br />
        <b>Физическое лицо</b><br />
        <a href="mailto:email@mail.ru">email@mail.ru</a><br />
        <b>Иванов Иван Иванович</b><br />
        <b>+7 495 9873947</b>
        <br /><br />
        Адрес доставки:<br />
        <b>Курьером по Москве</b><br />
        <b>г. Москва, м. Белорусская, ул. </b><br />
        <b>Вавилова, д.19 кв. 3</b><br />
        <b>С 19:00 до 23:00</b><br />
    </div>
    
            <div class="b-cart-field m-cart-field__first">

            <?
                if ($arResult["PERSON_TYPE"] == 2){
                //echo "<pre>", print_r($arResult["PRINT_PROPS_FORM"],1), "</pre>";
                //PrintPropsForm($arResult["PRINT_PROPS_FORM"]["USER_PROPS_Y"], GetMessage("SALE_NEW_PROFILE_TITLE"), $arParams);
                
                PrintPropsForm($arResult["UR"], GetMessage("SALE_NEW_PROFILE_TITLE"), $arParams);
                }?>
        </div>
    
        <!--    <div class="b-cart-field m-cart-field__first">
        <label class="b-cart-field__label">Наименование организации:</label>
        <input type="text" class="b-cart-field__input" />
        <label class="b-cart-field__label">Юридический адрес:</label>
        <textarea name="" id="" rows="3" class="b-cart-field__input"></textarea>
        <label class="b-cart-field__label">Фактический адрес:</label>
        <textarea name="" id="" rows="3" class="b-cart-field__input"></textarea>
        <label class="b-cart-field__label">ИНН:</label>
        <input type="text" class="b-cart-field__input" />
        <label class="b-cart-field__label">КПП:</label>
        <input type="text" class="b-cart-field__input" />
        <label class="b-cart-field__label">Банк:</label>
        <input type="text" class="b-cart-field__input" />
        <label class="b-cart-field__label">БИК:</label>
        <input type="text" class="b-cart-field__input" />
        </div>-->
    <div class="b-cart-field">
                <?
                if(count($arResult["PAY_SYSTEM"])>0)
                {
                ?>

                    <?
                        foreach($arResult["PAY_SYSTEM"] as $arPaySystem)
                        {
                        ?>

                                <label for="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>" class="b-cart-field__label"><input type="radio" id="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>" name="PAY_SYSTEM_ID" value="<?= $arPaySystem["ID"] ?>"<?if ($arPaySystem["CHECKED"]=="Y") echo " checked";?>>
                                
                                    <?= $arPaySystem["PSA_NAME"] ?><br />
                                    <?
                                        if (strlen($arPaySystem["DESCRIPTION"])>0)
                                        {
                                        ?>
                                        <?=$arPaySystem["DESCRIPTION"]?>
                                        <br />
                                        <?
                                        }
                                    ?>
                                </label>
                        <?
                        }
                    ?>

                <?
                }?>
        <div class="b-payment-image">
            <img src="/upload/ban1.png" class="b-payment__image" alt="" />
            <img src="/upload/ban2.png" class="b-payment__image" alt="" />
        </div>
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





