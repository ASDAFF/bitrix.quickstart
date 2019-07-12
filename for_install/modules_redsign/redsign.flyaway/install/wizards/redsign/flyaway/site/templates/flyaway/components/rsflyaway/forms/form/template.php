<?php
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
?>

<div class="rsform">
    <?php if(!empty($arParams['FORM_DESCRIPTION'])): ?>
        <div class= "well rsform__description"><?=$arParams['FORM_DESCRIPTION']?></div>
    <?php endif; ?>

    <?php if($arResult["LAST_ERROR"]!=''): ?>
        <div class="alert alert-danger" role="alert"><?=$arResult['LAST_ERROR']?></div>
    <?php endif; ?>

    <?php if($arResult['GOOD_SEND']=='Y'): ?>
        <div class="alert alert-success" role="alert"><?=$arResult['MESSAGE_AGREE']?></div>
        <script>$(document).trigger("closeFancy");</script>
    <?php endif; ?>

    <form action="<?=$arResult["ACTION_URL"]?>" method="POST" class="form rsfrom__form">
            <?=bitrix_sessid_post()?>
            <input type="hidden" name="<?=$arParams["REQUEST_PARAM_NAME"]?>" value="Y">
            <?php foreach ($arResult['FIELDS'] as $key => $arField): if($arField['SHOW'] !== 'Y') continue; ?>
                <div class="form-group">
                    <label for="<?=$arField['CONTROL_NAME']?>">
                        <?php if($arField['EXT'] == "Y"): ?>
                            <?=$arParams['RS_FLYAWAY_FIELD_'.$arField['INDEX'].'_NAME']?>
                        <?php elseif(!empty($arParams["INPUT_NAME_".$arField["CONTROL_NAME"]])): ?>
                            <?=$arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]?>
                        <?php else: ?>
                            <?=Loc::getMessage("MSG_".$arField["CONTROL_NAME"]);?>
                        <?php endif; ?>
                        <?php if(in_array($arField['CONTROL_NAME'], $arParams['REQUIRED_FIELDS'])): ?>
                            <span class="required"> *</span>
                        <?php endif; ?>
                    </label>
                    <?php if($arField['CONTROL_NAME'] == 'RS_TEXTAREA'): ?>
                        <textarea name="<?=$arField["CONTROL_NAME"]?>" class="form-item form-control"><?=$arField["HTML_VALUE"]?></textarea>
                    <?php else: ?>
                        <input type="text" value="<?=$arField["HTML_VALUE"]?>" name="<?=$arField["CONTROL_NAME"]?>" class="<?php if(in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])) echo 'req-input';?> form-item form-control"<?php if(in_array($arField["CONTROL_NAME"], $arParams["DISABLED_FIELDS"])) echo ' readonly';?>>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <?php if($arParams['USE_CAPTCHA'] == 'Y'): ?>
                <div class="rsform__captcha clearfix">
                    <div class="captcha_wrap">
                        <div class="rsform__captcha-label"><label for="captcha_sid"><?=Loc::getMessage("MSG_CAPTHA");?></label></div>
                        <div class="rsform__captcha-input">
                            <input type="hidden" name="captcha_sid" value="<?=$arResult["CATPCHA_CODE"]?>">
                            <input class="form-control req-input form-item" id="captcha_<?=$arResult['WEB_FORM_NAME']?>" type="text" name="captcha_word" size="30" maxlength="50" value="">
                        </div>
                        <div class="rsform__captcha-image" >
                            <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CATPCHA_CODE"]?>" alt="CAPTCHA">
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="separator rsform__separator"></div>

            <div class="rsform__bottom">
                <div class="rsform__bottom-ps"><?=Loc::getMessage('MSG_REQUIRED_FIELDS')?></div>
                <div class="rsform__bottom-button">
                    <input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
                    <input type="submit" class="btn btn-group-lg btn-default btn2" name="submit" value="<?=Loc::getMessage("MSG_SUBMIT")?>">
                </div>
            </div>
    </form>
</div>
<script>
    $(function() {
        'use strict';
		
		if(!BX || !BX.localStorage) return;
		
		
        var ajaxData = BX.localStorage.get('ajax_data'),
            key;

        if(ajaxData) {

            if(typeof ajaxData === 'string' || ajaxData instanceof String) {
                ajaxData = JSON.parse(ajaxData);
            }

            for(key in ajaxData) {
                $(".rsform [name=" + key +"]").val(ajaxData[key]);
            }

        }
    });
</script>
