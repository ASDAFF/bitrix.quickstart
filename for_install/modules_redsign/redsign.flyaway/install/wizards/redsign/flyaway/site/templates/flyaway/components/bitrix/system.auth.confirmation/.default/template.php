<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
?>

<?php if($arResult['SHOW_FORM']): ?>
<div class="row">
    <div class="col col-xs-12">
        <p><?=$arResult["MESSAGE_TEXT"]?></p>
    </div>
    
    <div class="col col-xs-12">
        
        <form class="form form-horizontal" method="post" action="<?=$arResult['FORM_ACTION']?>">
            <input type="hidden" name="<?=$arParams["USER_ID"]?>" value="<?=$arResult['USER_ID']?>">
            
            <div class="form-group">
                <label for="<?=$arParams['LOGIN']?>" class="col-sm-2 control-label"><?=Loc::getMessage("CT_BSAC_LOGIN")?></label>
                <div class="col-sm-10">
                    <input type="text" name="<?=$arParams['LOGIN']?>" id="<?=$arParams['LOGIN']?>" maxlength="50" value="<?=(strlen($arResult['LOGIN']) > 0 ? $arResult['LOGIN']: $arResult['USER']['LOGIN'])?>" size="17">
                </div>
            </div>
            
            <div class="form-group">
                <label for="<?=$arParams['CONFIRM_CODE']?>" class="col-sm-2 control-label"><?=Loc::getMessage("CT_BSAC_CONFIRM_CODE")?></label>
                <div class="col-sm-10">
                    <input type="text" name="<?=$arParams['CONFIRM_CODE']?>" id="<?=$arParams['CONFIRM_CODE']?>" maxlength="50" value="<?=$arResult['CONFIRM_CODE']?>" size="17">
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input class="btn btn-default btn2" type="submit" value="<?=Loc::getMessage('CT_BSAC_CONFIRM')?>">
                </div>
            </div>
            
        </form>
        
    </div>
    
</div>
<?php else: ?>
<?php $APPLICATION->IncludeComponent("bitrix:system.auth.authorize", "", array());?>
<?php endif; ?>

