<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="b-cart__list clearfix">
    <div class="b-cart-field__info">
        
               <?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket.line",
	"insale",
	Array( "SHOW_PERSONAL_LINK" => "N"  )
        );?> 
        
        
    </div>
    <div class="b-cart-field m-cart-field__first">
   
    </div>
    <div class="b-cart-field">
        <label class="b-cart-field__label">Тип плательщика <span class="b-star">*</span></label>
        <select name="PERSON_TYPE" id="PERSON_TYPE" class="b-cart-field__select">
            <?
                foreach($arResult["PERSON_TYPE_INFO"] as $v)
                {
                ?>
                <option <?if ($v["CHECKED"]=="Y") echo " selected='selected'";?> value="<?= $v["ID"] ?>"><?= $v["NAME"] ?></option><?
                }
            ?>
        </select>


    </div>
</div>
<div class="b-cart__list">
    <div class="b-cart__btn clearfix">
        <div class="b-cart__btn_right m-right">   
             <button name="backButton" id="goCartBtn" class="b-button">Назад</button>
            <input type="submit" class="b-button m-orange" name="contButton" value="Продолжить оформление">
        </div>
    </div>
                </div>
                </form>

 