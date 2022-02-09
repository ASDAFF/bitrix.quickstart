<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


function PrintPropsForm($arSource=Array(), $PRINT_TITLE = "", $arParams)
{
	if (!empty($arSource))
	{
	 
		
		foreach($arSource as $arProperties)
		{
			 ?>

     <label class="b-cart-field__label"><?= $arProperties["NAME"] ?></label>
     
          
					<?
					if($arProperties["TYPE"] == "TEXT")
					{
						?>
      
						<input type="text"  class="b-cart-field__input" maxlength="250" size="<?=$arProperties["SIZE1"]?>" value="<?=$arProperties["VALUE"]?>" name="<?=$arProperties["FIELD_NAME"]?>">
						<?
					}
					  
					elseif ($arProperties["TYPE"] == "TEXTAREA")
					{
						?>
                    
        
						<textarea class="b-cart-field__input" rows="<?=$arProperties["SIZE2"]?>" cols="<?=$arProperties["SIZE1"]?>" name="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["VALUE"]?></textarea>
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
									"AJAX_CALL" => "N",
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
						<select name="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>">
						<?
						foreach($arProperties["VARIANTS"] as $arVariants)
						{
							?>
							<option value="<?=$arVariants["ID"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
							<?
						}
						?>
						</select>
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
 
					if (strlen($arProperties["DESCRIPTION"]) > 0)
					{
						?><br /><small><?echo $arProperties["DESCRIPTION"] ?></small><?
					}
				 
		}
		
		return true;
	}
	return false;
}
?>
 
<div class="b-cart__list clearfix">
     <div class="b-cart-field__info">
	  <?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket.line",
	"insale",
	Array( "SHOW_PERSONAL_LINK" => "N"  )
        );?> 
  
        <br><br>
        Информация о покупателе:<br>
        <b><?=$arResult['USER']['PERSON_TYPE']['NAME']?></b><br>
        <a href="mailto:<?=$arResult['USER']["EMAIL"];?>"><?=$arResult['USER']["EMAIL"];?></a><br>
 
        <b><?=$arResult['USER']["PERSONAL_PHONE"];?></b>
        
        <br><br> 
          
                                                
    <? 
   
    if($_POST["DELIVERY_ID"]== 4 || $_POST["DELIVERY_ID"]== 2) {
        ?>
          Адрес доставки:<br> 
            <b><?=$arResult["POST"]["ORDER_PROP_5"]; ?> 
               <?=$arResult["POST"]["ORDER_PROP_4"]; ?></b><br>
     
    <? }  elseif($_POST["DELIVERY_ID"]== 1){?>
               Самовывоз:<br> 
            <b>  <?=$_POST["ORDER_PROP_11"];?>    <?=$_POST["ORDER_PROP_12"];?> </b>
 
        <?}  
        ?> 
               
               
               
               
 </div>


  

    <div class="b-cart-field m-cart-field__first" style="min-height: 10px;">
        <div style="display: none;"><?
                PrintPropsForm(
                         $arResult["PRINT_PROPS_FORM"]
                                                           ); 
        ?>
       </div>
    </div>
 
					<div class="b-cart-field">
                                            	<?
				foreach($arResult["PAY_SYSTEM"] as $arPaySystem)
				{
                                    ?>
<label class="b-cart-field__label"><input type="radio" id="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>" name="PAY_SYSTEM_ID" value="<?= $arPaySystem["ID"] ?>"<?if ($arPaySystem["CHECKED"]=="Y") echo " checked";?>> <?= $arPaySystem["NAME"] ?></label>
						                                            
                                    
<?if($arPaySystem["ID"]==4){?> 
 <div class="b-payment-image">
        <img alt="" class="b-payment__image" src="/upload/ban1.png">
        <img alt="" class="b-payment__image" src="/upload/ban2.png">
</div>
<?}?>

                     <?
                                }?>
						
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
<?//prent($arResult);?>
