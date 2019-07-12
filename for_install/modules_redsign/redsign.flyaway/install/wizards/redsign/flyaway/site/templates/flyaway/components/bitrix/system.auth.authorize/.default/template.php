<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
?>

<div class="row">

    <?php if(isset($arParams['~AUTH_RESULT']) && is_array($arParams['~AUTH_RESULT']) && $arParams['~AUTH_RESULT']['TYPE']=='ERROR'): ?>
    <div class="col col-xs-12">
        <div class="alert alert-danger" role="alert"><?= $arParams['~AUTH_RESULT']['MESSAGE'] ?></div>
    </div>
    <?php elseif(!is_array($arParams['~AUTH_RESULT']) && $arParams['~AUTH_RESULT']!=''): ?>
    <div class="alert alert-danger" role="alert"><?= $arParams['~AUTH_RESULT'] ?></div>
    <?php else: ?>
    <?=ShowMessage($arParams["~AUTH_RESULT"]);?>
    <?php endif; ?>

    <?php if($arResult['ERROR_MESSAGE']!=''): ?>
    <div class="alert alert-danger" role="alert"><?=$arResult['ERROR_MESSAGE']?></div>
    <?php endif; ?>

    <div class="col col-xs-12">
        <form class="form form-horizontal"  name="form_auth" method="post" target="_top" action="<?=$arResult['AUTH_URL']?>">
            <input type="hidden" name="AUTH_FORM" value="Y">
            <input type="hidden" name="TYPE" value="AUTH">

            <?php if(strlen($arResult['BACKURL'])>0): ?>
            <input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>">
            <?php endif; ?>

            <?php foreach($arResult['POST'] as $key => $value): ?>
            <input type="hidden" name="<?=$key?>" value="<?=$value?>">
            <?php endforeach; ?>

            <div class="form-group">
                <label for="authFormLogin" class="col-sm-2 control-label"><?=Loc::getMessage("AUTH_LOGIN")?></label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="USER_LOGIN" id="authFormLogin" maxlength="255" value="<?=$arResult['LAST_LOGIN']?>" placeholder="">
                </div>
            </div>

            <div class="form-group">
                <label for="authFormPassword" class="col-sm-2 control-label"><?=Loc::getMessage("AUTH_PASSWORD")?></label>
                <div class="col-sm-10">
                    <input class="form-control" class="text" type="password" id="authFormPassword" name="USER_PASSWORD" maxlength="255" placeholder="">
                </div>
            </div>

            <?php if($arResult['SECURE_AUTH']): ?>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <noscript>
                    <?=ShowError( Loc::getMessage('AUTH_NONSECURE_NOTE') );?>
                    </noscript>
                </div>
            </div>
            <?php endif; ?>

            <?php if($arResult['CAPTCHA_CODE']): ?>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="hidden" name="captcha_sid" value="<?=$arResult['CAPTCHA_CODE']?>">
                    <div class="col col-md-6 form-capcha__img">
                        <img class="captchaImg" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CATPCHA_CODE"]?>" alt="CAPTCHA">
                    </div>
                    <div class="col col-md-6">
                        <input class="form-control form-item req-input" id="captcha_<?=$arResult['WEB_FORM_NAME']?>" type="text" name="captcha_word" size="30" maxlength="50" value="" placeholder="<?=Loc::getMessage('AUTH_CAPTCHA_PROMT')?>">
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($arResult['STORE_PASSWORD']=='Y'): ?>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <div class="gui-box">
                        <label class="gui-checkbox" for="USER_REMEMBER">
                            <input id="USER_REMEMBER" class="gui-checkbox-input" type="checkbox" name="USER_REMEMBER" value="Y">
                            <span class="gui-checkbox-icon"></span>
                            <?=Loc::getMessage('AUTH_REMEMBER_ME')?>
                        </label>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input class="btn btn-default btn2" type="submit" name="Login" value="<?=Loc::getMessage('AUTH_AUTHORIZE')?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <a  href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><?=Loc::getMessage('AUTH_REGISTER')?></a><br>
                    <a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=Loc::getMessage("AUTH_FORGOT_PASSWORD_2")?></a>
                </div>
            </div>

        </form>
    </div>

    <div class="col col-xs-12">
        <?php if($arResult["AUTH_SERVICES"]): ?>
        <?php $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
            array(
                "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
                "CURRENT_SERVICE" => $arResult["CURRENT_SERVICE"],
                "AUTH_URL" => $arResult["AUTH_URL"],
                "POST" => $arResult["POST"],
                "SHOW_TITLES" => $arResult["FOR_INTRANET"]?'N':'Y',
                "FOR_SPLIT" => $arResult["FOR_INTRANET"]?'Y':'N',
                "AUTH_LINE" => $arResult["FOR_INTRANET"]?'N':'Y',
            ),
            $component,
            array("HIDE_ICONS"=>"Y")
        );?>
        <?php endif; ?>
    </div>
</div>

<script>
    <?php if(strlen($arResult["LAST_LOGIN"])>0): ?>
        try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
    <?php else: ?>
        try{document.form_auth.USER_LOGIN.focus();}catch(e){}
    <?php endif; ?>
</script>
