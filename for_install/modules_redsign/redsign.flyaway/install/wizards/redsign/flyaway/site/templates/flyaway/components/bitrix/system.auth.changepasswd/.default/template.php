<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
?>

<div class="row">
    <div class="col col-xs-12">
        <?=ShowMessage($arParams['~AUTH_RESULT']);?>
    </div>
    
    <div class="col col-xs-12">
        <form class="form form-horizontal" method="post" action="<?=$arResult['AUTH_FORM']?>" name="bform">
            
            <?php if(strlen($arResult["BACKURL"])>0): ?>
            <input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>">
            <?php endif; ?>
            
            <input type="hidden" name="AUTH_FORM" value="Y">
            <input type="hidden" name="TYPE" value="CHANGE_PWD">
            
            <div class="form-group">
                <label for="USER_LOGIN" class="col-sm-2 control-label"><?=Loc::getMessage('AUTH_LOGIN')?></label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="USER_LOGIN" id="USER_LOGIN" maxlength="50" value="<?=$arResult['LAST_LOGIN']?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="USER_CHECKWORD" class="col-sm-2 control-label"><?=Loc::getMessage('AUTH_CHECKWORD')?></label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="USER_CHECKWORD" id="USER_CHECKWORD" maxlength="50" value="<?=$arResult['USER_CHECKWORD']?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="USER_PASSWORD" class="col-sm-2 control-label"><?=Loc::getMessage('AUTH_NEW_PASSWORD_REQ')?></label>
                <div class="col-sm-10">
                    <input class="form-control" type="password" name="USER_PASSWORD" id="USER_PASSWORD" maxlength="50" value="<?=$arResult['USER_PASSWORD']?>">
                </div>
            </div>
            
            <?php if($arResult['SECURE_AUTH']): ?>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <noscript>
                    ShowError( Loc::getMessage('AUTH_NONSECURE_NOTE') );
                    </noscript>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="USER_CONFIRM_PASSWORD" class="col-sm-2 control-label"><?=Loc::getMessage('AUTH_NEW_PASSWORD_CONFIRM')?></label>
                <div class="col-sm-10">
                    <input class="form-control" type="password" name="USER_CONFIRM_PASSWORD" id="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult['USER_CONFIRM_PASSWORD']?>">
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input class="btn btn-primary" type="submit" name="change_pwd" value="<?=Loc::getMessage('AUTH_CHANGE')?>" />
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <div><?=$arResult['GROUP_POLICY']['PASSWORD_REQUIREMENTS'];?></div>
                    <div>* <?=GetMessage('AUTH_REQ')?></div>
                    <a href="<?=$arResult['AUTH_AUTH_URL']?>"><?=GetMessage('AUTH_AUTH')?></a>
                </div>
            </div>
            
        </form>
    </div>
    
</div>
<script type="text/javascript">
document.bform.USER_LOGIN.focus();
</script>