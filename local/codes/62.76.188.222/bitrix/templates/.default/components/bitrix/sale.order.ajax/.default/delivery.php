<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); 
 
        if (!empty($arResult["DELIVERY"])) {
            ?> <div class="b-checkout">
            <h3 class="b-h3 m-checkout__h3">Служба доставки</h3>
            <?
            foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery) { 
                if ($delivery_id !== 0 && intval($delivery_id) <= 0) {    
        ?> 		 
    <b><?=$arDelivery["TITLE"]?></b><?if (strlen($arDelivery["DESCRIPTION"]) > 0):?><br />
    <?=nl2br($arDelivery["DESCRIPTION"])?><br /><?endif;?>
    <table border="0" cellspacing="0" cellpadding="3">
    <?
    foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile)
    {
            ?>
            <tr>
                    <td width="20" nowrap="nowrap">&nbsp;</td>
            
                    <td width="0%" valign="top">
                        
      <div class="b-checkout__label">
	 <label class="b-radio m-radio_gp_2<?if($arProfile["CHECKED"] == "Y"){?> b-checked<?}?>">
            <input type="radio" id="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>" name="<?=$arProfile["FIELD_NAME"]?>" value="<?=$delivery_id.":".$profile_id;?>" <?=$arProfile["CHECKED"] == "Y" ? "checked=\"checked\"" : "";?> onClick="submitForm();" />
             <b><?=$arProfile["TITLE"]?></b></label>
	 </div>                  
 

<div class="b-checkout__hint"><?=nl2br($arProfile["DESCRIPTION"])?></div>   
                   
                    
                    
                    
                    </td>
     
                    <td width="50%" valign="top" align="right">
                    <? 
                            $APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
                                    "NO_AJAX" => $arParams["DELIVERY_NO_AJAX"],
                                    "DELIVERY" => $delivery_id,
                                    "PROFILE" => $profile_id,
                                    "ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
                                    "ORDER_PRICE" => $arResult["ORDER_PRICE"],
                                    "LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
                                    "LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
                                    "CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
                            ), null, array('HIDE_ICONS' => 'Y'));
                    ?>

                    </td>
            </tr>
            <?
    } // endforeach
    ?>
    </table>
 <?
                    }
                    else {
                        ?>
   
                
      <div class="b-checkout__label">
                <label class="b-radio m-radio_gp_2 <?if($arDelivery["CHECKED"] == "Y"){?> b-checked<?}?>">
            <input  type="radio" id="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>" name="<?= $arDelivery["FIELD_NAME"] ?>" value="<?= $arDelivery["ID"] ?>"<? if ($arDelivery["CHECKED"] == "Y") echo " checked"; ?> onclick="submitForm();"><b><?= $arDelivery["NAME"] ?></b></label>
        </div>
        <div class="b-checkout__hint"><?if($arDelivery["PRICE_FORMATED"]){ ?>Стоимость <?=$arDelivery["PRICE_FORMATED"] ?> руб<br><?}?>
           <?= $arDelivery["DESCRIPTION"] ?></div>
       
                            <?
                        }
                    }?>
            
          </div>              
                        <? 
                }
 