<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

$jsParams = [
    'ajaxUrl' => $componentPath.'/ajax.php',
    'siteId' => SITE_ID
];;
?>
<span class="b-topline-location" id="topline-location">
    <svg class="icon-svg"><use xlink:href="#svg-location-main"></use></svg>Ваш город:
    <a href="<?=(isset($arParams['POPUP_URL']) ? $arParams['POPUP_URL'] : SITE_DIR.'mycity/')?>" title="<?=Loc::getMessage('RS_LOCATION_SELECT');?>" data-type="ajax" class="b-topline-location__link">
        <?php
        $frame = $this->createFrame('topline-cart')->begin();
            $frame->setBrowserStorage(true);
            echo (!empty($arResult['NAME']) ? $arResult['NAME'] : Loc::getMessage('RS_LOCATION_NOT_SELECT'));
        $frame->beginStub();
            echo Loc::getMessage('RS_LOCATION_NOT_SELECT');
        $frame->end();
        ?>
    </a>
</span>
<script>RS.Location = new RSLocation(<?=CUtil::PhpToJSObject($arResult)?>, <?=CUtil::PhpToJSObject($jsParams)?>);</script>
