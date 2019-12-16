<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Diag\Debug;

$context = \Bitrix\Main\Context::getCurrent();
$request = $context->getRequest();

?>
<div class="container">
  <?php // Debug::dump($arResult); ?>
    <?php if(count($arResult['MESSAGES']['ERRORS']) > 0): ?>
        <div class="alert alert-danger">
        <?php foreach($arResult['MESSAGES']['ERRORS'] as $msg): ?><?=$msg?><br><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?=$arResult['REQUEST_URI']?>">
        <?=bitrix_sessid_post()?>
        <div class="row">
        <?php foreach($arResult['FIELDS'] as $arField): ?>
            <?php if ($arField['PROPERTY_TYPE'] == 'S'): ?>
                <div class="col col-md-12 form-group">
                    <label for="FIELD_<?=$arField['CODE']?>" class="control-label">
                        <?=$arField['NAME']?>:
                        <?php if ($arField['IS_REQUIRED'] == 'Y'): ?><span class="required">*</span><?php endif; ?>
                    </label>
                    <?php if ($arField['USER_TYPE'] == 'HTML'): ?>
                        <textarea style="max-width: 100%;" id="FIELD_<?=$arField['CODE']?>" name="FIELD_<?=$arField['CODE']?>" class="form-control"></textarea>
                    <?php elseif ($arField['USER_TYPE'] == 'Date'): ?>
                        <input type="date" id="FIELD_<?=$arField['CODE']?>" name="FIELD_<?=$arField['CODE']?>" value="<?=$arField['CURRENT_VALUE']?>" class="form-control">
                    <?php elseif ($arField['USER_TYPE'] == 'DateTime'): ?>
                        <input type="datetime-local" id="FIELD_<?=$arField['CODE']?>" name="FIELD_<?=$arField['CODE']?>" value="<?=$arField['CURRENT_VALUE']?>" class="form-control">
                    <?php else: ?>
                        <input type="text" id="FIELD_<?=$arField['CODE']?>" name="FIELD_<?=$arField['CODE']?>" value="<?=$arField['CURRENT_VALUE']?>" class="form-control">
                    <?php endif; ?>
                    </label>
                </div>
            <?php elseif ($arField['PROPERTY_TYPE'] == 'L' && is_array($arField['VALUES'])): ?>
            <div class="col col-md-12 form-group">
                <label for="FIELD_<?=$arField['CODE']?>" class="control-label">
                    <?=$arField['NAME']?>:
                    <?php if ($arField['IS_REQUIRED'] == 'Y'): ?><span class="required">*</span><?php endif; ?>
                </label>
                <select class="form-control" name="FIELD_<?=$arField['CODE']?>" id="FIELD_<?=$arField['CODE']?>">
                <?php foreach ($arField['VALUES'] as $i => $arValue): ?>
                    <option <?php if ((empty($arField['CURRENT_VALUE']) && $i == 0) || $arField['CURRENT_VALUE'] == $arValue['ID']): ?>selected="selected"<?php endif; ?> value="<?=$arValue['ID']?>"><?=$arValue['VALUE']?></option>
                <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
            <?php if($arResult['USE_CAPTCHA'] == 'Y'): ?>
                <div class="col col-md-12 form-group captcha">
                    <label for="FIELD_CAPTCHA" class="control-label">
                        Код с картинки: <span class="required">*</span>
                    </label>
                    <div class="input-group" style="width: 100%;">
                        <span class="input-group-addon" style="width: 180px; height: 40px; padding: 0;" >
                            <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA">
                        </span>
                        <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>">
                        <input type="text"class="form-control"  name="captcha_word" size="30" maxlength="50" value="">
                    </div>
                </div>
            <?php endif; ?>
            <div class="col col-md-12">
                <div class="text-right">
                    <input type="submit" class="btn btn-primary" value="Отправить">
                </div>
            </div>
        </div>
    </form>
</div>
