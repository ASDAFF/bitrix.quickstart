<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization\Loc;

//ob_start();
?>
<div class="rsform rsform-auth row">
    <div class="col col-md-4">
        <?/*<div class="panel__head"><?=Loc::getMessage('AUTH_GET_CHECK_STRING');?></div>*/?>
        <?php
        if(!empty($arParams["~AUTH_RESULT"])):
           $text = str_replace(array("<br>", "<br>"), "\n", $arParams["~AUTH_RESULT"]["MESSAGE"]);
        ?>
            <div class="alert <?=($arParams["~AUTH_RESULT"]["TYPE"] == "OK"? "alert-success":"alert-danger")?>"><?=nl2br(htmlspecialcharsbx($text))?></div>
        <?php endif; ?>

        <form class="js-ajax_form" id="send_account_info" name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>" data-fancybox-title="<?=Loc::getMessage('FORGOT_TITLE')?>">
        <?php $frame = $this->createFrame('send_account_info', false)->begin(''); ?>
            <input type="hidden" name="AUTH_FORM" value="Y">
            <input type="hidden" name="TYPE" value="SEND_PWD">

            <?php if(strlen($arResult['BACKURL']) > 0): ?>
                <input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>">
            <?php endif; ?>

            <p><?=Loc::getMessage('AUTH_FORGOT_PASSWORD_1')?></p>

            <div class="form-group">
                <input class="form-control" type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" placeholder="<?=Loc::getMessage('AUTH_LOGIN');?>">
            </div>

            <p><?=Loc::getMessage("AUTH_OR")?></p>

            <div class="form-group">
                <input class="form-control" type="text" name="USER_EMAIL" maxlength="255" placeholder="<?=Loc::getMessage('AUTH_EMAIL');?>">
            </div>

            <?if($arResult["USE_CAPTCHA"]):?>
                <div class="form-group clearfix">
                    <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>">
                    <img class="captcha-img pull-right" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" alt="CAPTCHA">
                    <div class="l-overflow">
                        <input class="form-control" placeholder="<?=Loc::getMessage('system_auth_captcha');?>" type="text" name="captcha_word" maxlength="50" value="" size="15">
                    </div>
                </div>
            <?endif?>

            <div class="form-group">
                <input class="btn" type="submit" name="send_account_info" value="<?=Loc::getMessage("AUTH_SEND")?>">
            </div>

        <?php $frame->end(); ?>
        </form>

        <div class="fancybox-footer">
            <!--noindex-->
            <a class="js-ajax_fancy" href="<?=$arResult["AUTH_AUTH_URL"]?>" title="<?=Loc::getMessage('AUTH_TITLE')?>" rel="nofollow"><?=Loc::getMessage('AUTH_AUTH')?></a>
            <!--/noindex-->
        </div>

    </div>
    <script>document.bform.USER_LOGIN.focus();</script>
</div>
<?php //$templateData['TEMPLATE_HTML'] = ob_get_flush(); ?>
