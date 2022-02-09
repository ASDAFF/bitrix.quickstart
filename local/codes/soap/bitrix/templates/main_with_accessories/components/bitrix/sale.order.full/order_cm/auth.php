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
<?

CModule::IncludeModule("sale");
 
$dbBasketItems = CSaleBasket::GetList(
    array(
        "NAME" => "ASC",
        "ID" => "ASC"
    ),
    array(
        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
        "LID" => SITE_ID,
        "ORDER_ID" => "NULL",
        "CAN_BUY" => "Y",
    ),
    false,
    false,
    array("ID", "PRICE")
);
 
$Cnt = 0;
$Price = 0; 
while ($arItem = $dbBasketItems->Fetch())
{
    $Price += $arItem["PRICE"];
    $Cnt++;
    
}

?>
    <div class="b-cart-field__info">Вы заказали:<br><b><?=$Cnt?> товара на сумму <?=$Price?>.–</b></div>
    <form method="post" action="<?= $arParams["PATH_TO_ORDER"]?>" name="order_reg_form">
        <div class="b-cart-field m-cart-field__first">
        <div class="b-cart-field__text">Чтобы сохранить данные для повторных покупок в нашем магазине, пожалуйста зарегистрируйтесь</div>
       
        <input type="hidden" id="NEW_GENERATE_N" name="NEW_GENERATE" value="N">
        <label class="b-cart-field__label">Пароль <span class="b-star">*</span></label>
        <input type="password" name="NEW_PASSWORD" class="b-cart-field__input" value="">
        <label class="b-cart-field__label">Повтор пароля <span class="b-star">*</span></label>
        <input type="password" name="NEW_PASSWORD_CONFIRM" class="b-cart-field__input" value="">
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
        <input type="text" name="NEW_EMAIL" class="b-cart-field__input" value="<?=$arResult["POST"]["NEW_EMAIL"]?>">
        <label class="b-cart-field__label">ФИО <span class="b-star">*</span></label>
        <input type="text" class="b-cart-field__input" name="NEW_LAST_NAME" class="b-cart-field__input" value="<?=$arResult["POST"]["NEW_LAST_NAME"]?>">

        <label class="b-cart-field__label">Телефон <span class="b-star">*</span></label>
        <input type="text" class="b-cart-field__input" name="PERSONAL_PHONE" class="b-cart-field__input" value="<?=$arResult["POST"]["PERSONAL_PHONE"]?>">
        <input type="hidden" name="do_register" value="Y">

    </div> </form>
</div>
<div class="b-cart__list">
    <div class="b-cart__btn clearfix">
        <div class="b-cart__btn_right m-right">
            <button class="b-button">Назад</button>
            <input type="submit" class="b-button m-orange" value="Продолжить оформление">
        </div>
    </div>
                </div>
               
