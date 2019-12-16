<?php

use \Bitrix\Main\Localization\Loc;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();


$emailId = $this->getEditAreaId('email');
?>
<form class="subscribe" action="<?=$arResult['FORM_ACTION']?>">
    <div class="subscribe__title"><?=Loc::getMessage('BLOCK_TITLE')?></div>
    
    <?php foreach($arResult['RUBRICS'] as $itemID => $itemValue): ?>
        <input type="hidden" name="sf_RUB_ID[]" value="<?=$itemValue['ID']?>">
    <?php endforeach; ?>
    
    <div class="form-group" id="<?=$emailId?>">
        <?php
        $frame = $this->createFrame($emailId, false)->begin();
        $frame->setBrowserStorage(true);
        ?>
            <input class="form-control" type="text" name="sf_EMAIL" value="<?=$arResult['EMAIL']?>" title="<?=Loc::getMessage('subscr_form_email_title')?>" placeholder="E-mail">
        <?php $frame->beginStub(); ?>
            <input class="form-control" type="text" name="sf_EMAIL" value="" title="<?=Loc::getMessage('subscr_form_email_title')?>" placeholder="E-mail">
        <?php $frame->end(); ?>
    </div>
    <div class="input-group">
        <input class="btn btn1" type="submit" name="OK" value="<?=Loc::getMessage('subscr_form_button')?>">
    </div>
</form>