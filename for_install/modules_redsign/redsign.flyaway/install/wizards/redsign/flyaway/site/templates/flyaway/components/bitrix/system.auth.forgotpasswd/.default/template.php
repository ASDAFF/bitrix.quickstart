<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
?>

<div class="row">
    <div class="col col-xs-12">
        <?php if(isset($arParams['~AUTH_RESULT']) && is_array($arParams['~AUTH_RESULT']) && $arParams['~AUTH_RESULT']['TYPE']=='ERROR'): ?>
        <div class="alert alert-danger" role="alert"><?=$arParams['~AUTH_RESULT']['MESSAGE']?></div>
        <?php else: ?>
        <?=ShowMessage($arParams["~AUTH_RESULT"]); ?>
        <?php endif; ?>
    </div>
    
    <div class="col col-xs-12">
        <form class="form form-horizontal" name="bform" method="post" target="_top" action="<?=$arResult['AUTH_URL']?>">
            <?php if(strlen($arResult["BACKURL"])>0): ?>
            <input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>">
            <?php endif; ?>
            <input type="hidden" name="AUTH_FORM" value="Y">
            <input type="hidden" name="TYPE" value="SEND_PWD">
            
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <?=Loc::getMessage('AUTH_FORGOT_PASSWORD_1')?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="USER_LOGIN" class="col-sm-2 control-label"><?=Loc::getMessage('AUTH_LOGIN')?></label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="USER_LOGIN" id="USER_LOGIN" maxlength="50" value="<?=$arResult['LAST_LOGIN']?>">
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <?=Loc::getMessage('AUTH_OR')?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="USER_EMAIL" class="col-sm-2 control-label"><?=Loc::getMessage('AUTH_EMAIL')?></label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="USER_EMAIL" id="USER_EMAIL" maxlength="255">
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input class="btn btn-default btn2" type="submit" name="send_account_info" value="<?=Loc::getMessage('AUTH_SEND')?>">
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <div><a href="<?=$arResult['AUTH_AUTH_URL']?>"><?=Loc::getMessage('AUTH_AUTH')?></a></div>
                </div>
            </div>
            
        </form>
    </div>
</div>
<script type="text/javascript">
document.bform.USER_LOGIN.focus();
</script>