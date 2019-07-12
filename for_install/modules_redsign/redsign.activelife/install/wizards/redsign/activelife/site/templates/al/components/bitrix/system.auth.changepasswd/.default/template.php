<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

//ob_start();
?>
<div class="rsform rsform-auth row">
    <div class="col col-md-4">
        <?/*<div class="panel__head"><?=Loc::getMessage('AUTH_CHANGE_PASSWORD');?></div>*/?>
        <?php
        if(!empty($arParams["~AUTH_RESULT"])):
           $text = str_replace(array("<br>", "<br />"), "\n", $arParams["~AUTH_RESULT"]["MESSAGE"]);
        ?>
            <div class="alert <?=($arParams["~AUTH_RESULT"]["TYPE"] == "OK"? "alert-success":"alert-danger")?>"><?=nl2br(htmlspecialcharsbx($text))?></div>
        <?php endif; ?>
        
        <form class="js-ajax_form" id="change_pwd" method="post" action="<?=$arResult['AUTH_FORM']?>" name="bform" class="profile_block_body" data-fancybox-title="<?=Loc::getMessage('AUTH_CHANGE_PASSWORD')?>">
        <?php $frame = $this->createFrame('change_pwd', false)->begin(''); ?>
            <input type="hidden" name="AUTH_FORM" value="Y">
            <input type="hidden" name="TYPE" value="CHANGE_PWD">

            <?php if(strlen($arResult['BACKURL']) > 0): ?>
                <input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>" />
            <?php endif; ?>

            <div class="form-group">
                <input class="form-control" type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult['LAST_LOGIN']?>" placeholder="<?=Loc::getMessage('AUTH_LOGIN')?>*">
            </div>
            <div class="form-group">
                <input class="form-control" type="text" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult['USER_CHECKWORD']?>" placeholder="<?=Loc::getMessage('AUTH_CHECKWORD')?>*">
            </div>
            <div class="form-group">
                <input class="form-control" type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" class="bx-auth-input" autocomplete="off" placeholder="<?=Loc::getMessage('AUTH_NEW_PASSWORD_REQ')?>*">
                <?/*if($arResult["SECURE_AUTH"]):?>
                                <span class="bx-auth-secure" id="bx_auth_secure" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
                                    <div class="bx-auth-secure-icon"></div>
                                </span>
                                <noscript>
                                <span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
                                    <div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
                                </span>
                                </noscript>
                <script type="text/javascript">
                document.getElementById('bx_auth_secure').style.display = 'inline-block';
                </script>
                <?endif*/?>
            </div>
            <div class="form-group">
                <input class="form-control" type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult['USER_CONFIRM_PASSWORD']?>" placeholder="<?=Loc::getMessage('AUTH_NEW_PASSWORD_CONFIRM')?>*">
            </div>
            
            <?if($arResult["USE_CAPTCHA"]):?>
                <div class="form-group clearfix">
                    <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>">
                    <img class="captcha-img pull-right" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" alt="CAPTCHA">
                    <div class="l-overflow">
                        <input class="form-control" type="text" name="captcha_word" maxlength="50" value="" placeholder="<?=Loc::getMessage('system_auth_captcha');?>">
                    </div>
                </div>
            <?endif?>
        
            <div class="form-group">
                <input class="btn" type="submit" name="change_pwd" value="<?=Loc::getMessage('AUTH_CHANGE')?>">
            </div>

            <p>
                <?=$arResult['GROUP_POLICY']['PASSWORD_REQUIREMENTS'];?><br>
                <span class="required">*</span> - <?=GetMessage('AUTH_REQ')?>
            </p>
        <?php $frame->end(); ?>
        </form>
        <div class="fancybox-footer">
            <!--noindex-->
            <a class="js-ajax_fancy" href="<?=$arResult["AUTH_AUTH_URL"]?>" title="<?=Loc::getMessage('AUTH_TITLE')?>" rel="nofollow"><?=Loc::getMessage('AUTH_AUTH')?></a>
            <!--/noindex-->
        </div>
    </div>
</div>
<script>document.bform.USER_LOGIN.focus();</script>

<?php //$templateData['TEMPLATE_HTML'] = ob_get_flush(); ?>
