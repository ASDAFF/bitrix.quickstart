<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?

    $arResult["PERSON_TYPE_INFO"] = Array();
    $dbPersonType = CSalePersonType::GetList(
        array("SORT" => "ASC", "NAME" => "ASC"),
        array("LID" => SITE_ID, "ACTIVE" => "Y")
    );
    $bFirst = True;
    while ($arPersonType = $dbPersonType->GetNext())
    {
        if (IntVal($arResult["POST"]["PERSON_TYPE"]) == IntVal($arPersonType["ID"]) || IntVal($arResult["POST"]["PERSON_TYPE"]) <= 0 && $bFirst)
            $arPersonType["CHECKED"] = "Y";
        $arResult["PERSON_TYPE_INFO"][] = $arPersonType;
        $bFirst = False;
    }

?>

<div class="b-cart__list clearfix">
    <div class="b-cart-field__info">Вы заказали:<br><b>4 товара на сумму 34 800.–</b></div>
    <div class="b-cart-field m-cart-field__first">
        <div class="b-cart-field__text">Чтобы сохранить данные для повторных покупок в нашем магазине, пожалуйста зарегистрируйтесь</div>
        <form method="post" action="<?= $arParams["PATH_TO_ORDER"]?>" name="order_reg_form">
        <input type="hidden" id="NEW_GENERATE_N" name="NEW_GENERATE" value="N">
        <label class="b-cart-field__label">Пароль <span class="b-star">*</span></label>
        <input type="password" name="USER_PASSWORD" class="b-cart-field__input">
        <label class="b-cart-field__label">Повтор пароля <span class="b-star">*</span></label>
        <input type="password" name="USER_PASSWORD" class="b-cart-field__input">
    </div>
    <div class="b-cart-field">
        <label class="b-cart-field__label">Тип плательщика <span class="b-star">*</span></label>
        <select name="PERSON_TYPE" id="PERSON_TYPE?>" class="b-cart-field__select">
            <?
                foreach($arResult["PERSON_TYPE_INFO"] as $v)
                {
                ?>
                <option <?if ($v["CHECKED"]=="Y") echo " selected='select'";?> value="<?= $v["ID"] ?>"><?= $v["NAME"] ?></option><?
                }
            ?>
        </select>
        <label class="b-cart-field__label">E-mail  (логин) <span class="b-star">*</span></label>
        <input type="text" name="USER_LOGIN" class="b-cart-field__input" value="<?=$arResult["POST"]["USER_LOGIN"]?>">
        <label class="b-cart-field__label">Имя <span class="b-star">*</span></label>
        <input type="text" class="b-cart-field__input" name="USER_NAME" class="b-cart-field__input" value="<?=$arResult["POST"]["NEW_NAME"]?>">
        <label class="b-cart-field__label">Фамилия <span class="b-star">*</span></label>
        <input type="text" class="b-cart-field__input" name="USER_LAST_NAME" class="b-cart-field__input" value="<?=$arResult["POST"]["NEW_LAST_NAME"]?>">

        <label class="b-cart-field__label">Телефон <span class="b-star">*</span></label>
        <input type="text" class="b-cart-field__input">
        <input type="hidden" name="do_authorize" value="Y">

    </div>
</div>
<div class="b-cart__list">
    <div class="b-cart__btn clearfix">
        <div class="b-cart__btn_right m-right">
            <button class="b-button">Назад</button>
            <input type="submit" class="b-button m-orange" name="contButton" value="Продолжить оформление">
        </div>
    </div>
                </div>
                </form>
