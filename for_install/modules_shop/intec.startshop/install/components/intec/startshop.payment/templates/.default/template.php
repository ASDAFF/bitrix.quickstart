<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="startshop-payment default">
    <div class="startshop-aligner-vertical"></div>
    <?if ($arResult['STATUS'] == 'SUCCESS'):?>
        <div class="startshop-payment-message startshop-payment-message-success">
            <div class="startshop-payment-icon"></div>
            <div class="startshop-payment-text"><?=GetMessage('SP_DEFAULT_SUCCESS_TEXT', $arResult['ORDER'])?></div>
        </div>
    <?else:?>
        <div class="startshop-payment-message startshop-payment-message-fail">
            <div class="startshop-payment-icon"></div>
            <?if (!empty($arResult['ORDER'])):?>
                <div class="startshop-payment-text"><?=GetMessage('SP_DEFAULT_FAIL_TEXT_WITH_ORDER', $arResult['ORDER'])?></div>
            <?else:?>
                <div class="startshop-payment-text"><?=GetMessage('SP_DEFAULT_FAIL_TEXT_WITHOUT_ORDER')?></div>
            <?endif;?>
        </div>
    <?endif;?>
</div>
