<?php
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

if(empty($arParams["DISABLED_FIELDS"])) {
    $arParams["DISABLED_FIELDS"] = array();
}
?>
<div class="rsform row">
    <div class="col col-md-4">
        <?php if(!empty($arParams['FORM_DESCRIPTION'])): ?>
            <div class="well"><?=$arParams['FORM_DESCRIPTION']?></div><br>
        <?php endif; ?>

        <?php if($arResult["LAST_ERROR"]!=''): ?>
            <div class="alert alert-danger" role="alert"><?=$arResult['LAST_ERROR']?></div><br>
        <?php endif; ?>

        <?php if($arResult['GOOD_SEND']=='Y'): ?>
            <div class="alert alert-success" role="alert"><?=$arResult['MESSAGE_AGREE']?></div><br>
            <script>$(document).trigger("closeFancy");</script>
        <?php endif; ?>
        <form class="js-ajax_form" action="<?=$arResult["ACTION_URL"]?>" method="POST" data-fancybox-title="<?=$arParams['FORM_TITLE']?>">
            <?=bitrix_sessid_post()?>
            <input type="hidden" name="<?=$arParams["REQUEST_PARAM_NAME"]?>" value="Y">
            <input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
            <?php foreach ($arResult['FIELDS'] as $key => $arField): ?>
                <?php
                if($arField['SHOW'] !== 'Y') {
                    continue;
                }
                ?>
                <div class="form-group">
                    <?php
                    $sInputLabel = '';
                    
                    if($arField['EXT'] == "Y") {
                        $sInputLabel = $arParams['RS_FLYAWAY_FIELD_'.$arField['INDEX'].'_NAME'];
                    } elseif(!empty($arParams["INPUT_NAME_".$arField["CONTROL_NAME"]])) {
                        $sInputLabel = $arParams["INPUT_NAME_".$arField["CONTROL_NAME"]];
                    } else {
                        $sInputLabel = Loc::getMessage("MSG_".$arField["CONTROL_NAME"]);
                    }
                    
                    if(in_array($arField['CONTROL_NAME'], $arParams['REQUIRED_FIELDS'])) {
                        $sInputLabel .= '*';
                    }
                    
                    if ($sInputLabel != '') {
                        $sInputLabel = ' placeholder="'.$sInputLabel.'"';
                    }
                    
                    ?>
                    <?php if($arField['CONTROL_NAME'] == 'RS_TEXTAREA'): ?>
                        <textarea name="<?=$arField["CONTROL_NAME"]?>" class="form-control"<?=$sInputLabel?>><?=$arField["HTML_VALUE"]?></textarea>
                    <?php else: ?>
                        <input type="text" value="<?=$arField["HTML_VALUE"]?>" name="<?=$arField["CONTROL_NAME"]?>" 
                            class="<?php if(in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])) echo 'req-input';?> form-control"
                            <?php if(in_array($arField["CONTROL_NAME"], $arParams["DISABLED_FIELDS"])) echo ' readonly';?>
                            <?=$sInputLabel?>
                        />
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <?php if($arParams['USE_CAPTCHA'] == 'Y'): ?>
            <div class="form-group clearfix">
                <input type="hidden" name="captcha_sid" value="<?=$arResult["CATPCHA_CODE"]?>">
                <img class="captcha-img pull-right" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CATPCHA_CODE"]?>" alt="CAPTCHA">
                <div class="l-overflow">
                    <input class="form-control" id="captcha_<?=$arResult['WEB_FORM_NAME']?>" type="text" name="captcha_word" size="30" maxlength="50" value="" placeholder="<?=Loc::getMessage('MSG_CAPTHA');?>*">
                </div>
            </div>
            <?php endif; ?>
            
            <div class="form-group">
                <input class="btn" type="submit" name="submit" value="<?=Loc::getMessage("MSG_SUBMIT")?>">
            </div>
            
            <div><?=Loc::getMessage('MSG_REQUIRED_FIELDS')?></div>
        </form>
    </div>
</div>
<?=CJSCore::Init(array('ls'), true)?>
<script>
if (window.jQuery) {
  $(function() {
    'use strict';
    BX.ready(function(){
      var ajaxData = BX.localStorage.get('ajax_data'),
          key;
      if (ajaxData) {
        if(typeof ajaxData === 'string' || ajaxData instanceof String) {
          ajaxData = BX.parseJSON(ajaxData);
        }
        for (key in ajaxData) {
          $(".rsform [name=" + key +"]").val(ajaxData[key]);
        }

      }
    });
  });
}
</script>
