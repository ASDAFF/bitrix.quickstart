<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?> 
<h3 class="b-h3 m-checkout__h3">Платежная система</h3> 
<?
foreach ($arResult["PAY_SYSTEM"] as $arPaySystem) {
    if (count($arResult["PAY_SYSTEM"]) == 1) {
        ?> 
        <div class="b-checkout__label">
            <label class="b-radio m-radio_gp_3 b-checked">
                <input type="radio" id="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>" name="PAY_SYSTEM_ID" value="<?= $arPaySystem["ID"] ?>"><b><?= $arPaySystem["PSA_NAME"] ?></b></label>
        </div> 
        <? 
        if (strlen($arPaySystem["DESCRIPTION"]) > 0) {
             ?><div class="b-checkout__hint"><?= $arPaySystem["DESCRIPTION"] ?></div>  <?
        }
    } else {
        if (!isset($_POST['PAY_CURRENT_ACCOUNT']) OR $_POST['PAY_CURRENT_ACCOUNT'] == "N") {
            ?> 
            <div class="b-checkout__label">
                <label class="b-radio m-radio_gp_3<? if ($arPaySystem["CHECKED"] == "Y") 
                    echo " b-checked";?>"><input type="radio"  <?if($arPaySystem["CHECKED"] == "Y") echo " checked='checked' "?> id="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] 
                ?>" name="PAY_SYSTEM_ID" value="<?= $arPaySystem["ID"] ?>"><b><?=$arPaySystem["PSA_NAME"]
                ?></b></label>
            </div> 
          <?
            if (strlen($arPaySystem["DESCRIPTION"]) > 0) {
                ?><div class="b-checkout__hint"><?= $arPaySystem["DESCRIPTION"] ?></div>  <?
                }
            }
        }
    }
 