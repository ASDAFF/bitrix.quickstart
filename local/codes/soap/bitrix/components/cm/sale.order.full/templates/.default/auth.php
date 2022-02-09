<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?> 

<form method="post" action="<?=$arParams["PATH_TO_ORDER"]?>" name="order_reg_form">
  
<div class="b-cart__list clearfix">
 
    <div class="b-cart-field__info">

        <?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket.line",
	"insale",
	Array( "SHOW_PERSONAL_LINK" => "N"  )
        );?> 
        
        </div>
       <div class="b-cart-field m-cart-field__first">
        <div class="b-cart-field__text">Чтобы сохранить данные для повторных покупок в нашем магазине, пожалуйста зарегистрируйтесь</div>
       
        <input type="hidden" id="NEW_GENERATE_N" name="NEW_GENERATE" value="N">
        <label class="b-cart-field__label">Пароль</label>
        <input type="password" name="NEW_PASSWORD" class="b-cart-field__input" value="">
        <label class="b-cart-field__label">Повтор пароля</label>
        <input type="password" name="NEW_PASSWORD_CONFIRM" class="b-cart-field__input" value="">
    </div>
    <div class="b-cart-field">
        <label class="b-cart-field__label">Тип плательщика <span class="b-star">*</span></label>
        <select name="PERSON_TYPE" id="PERSON_TYPE" class="b-cart-field__select">
            <?
                foreach($arResult["PERSON_TYPE_INFO"] as $v)
                {
                ?>
                <option <?if ($v["CHECKED"]=="Y") echo " selected='select'";?> value="<?=$v["ID"]?>"><?=$v["NAME"]?></option><?
                }
            ?>
        </select>
        <label class="b-cart-field__label">E-mail  (логин) <span class="b-star">*</span></label>
        <input type="text" id="NEW_EMAIL" name="NEW_EMAIL" class="b-cart-field__input" value="<?=$arResult["POST"]["NEW_EMAIL"]?>">
        <label class="b-cart-field__label">ФИО <span class="b-star">*</span></label>
        <input id="NEW_LAST_NAME" type="text" class="b-cart-field__input" name="NEW_LAST_NAME" class="b-cart-field__input" value="<?=$arResult["POST"]["NEW_LAST_NAME"]?>">

        <label class="b-cart-field__label">Телефон <span class="b-star">*</span></label>
        <input type="text" class="b-cart-field__input" id="PERSONAL_PHONE" name="PERSONAL_PHONE" class="b-cart-field__input" value="<?=$arResult["POST"]["PERSONAL_PHONE"]?>">
        <input type="hidden" name="do_register" value="Y">

    </div> 
</div>
<div class="b-cart__list">
    <div class="b-cart__btn clearfix">
        <div class="b-cart__btn_right m-right">
             <button name="backButton" id="goCartBtn" class="b-button">Назад</button>
            <input type="submit" id="first_step_btn"class="b-button m-orange" value="Продолжить оформление">
        </div>
    </div>
 </div>
</form>