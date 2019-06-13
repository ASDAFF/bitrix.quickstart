<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
global $APPLICATION;
?>
<form class="wl-mclb" id="FORM_<?= $arParams["ID_FORM"]; ?>" name="FORM_<?= $arParams["ID_FORM"]; ?>" action="<?= $APPLICATION->GetCurPage(true); ?>" onsubmit="return ModernCheck(this);">
    <input type="hidden" name="FORM_<?= $arParams["ID_FORM"]; ?>" value="1" />
    <?= bitrix_sessid_post(); ?>
    <a class="wl-mclb__close" href="#"><i class="fa fa-times" aria-hidden="true"></i></a>
    <div class="wl-mclb__wrap">
        <div class="wl-mclb__pulse"></div>
        <div class="wl-mclb__title"><?= GetMessage("MCLB_TITLE"); ?></div>
        <input class="wl-mclb__input" type="text" name="<?= $arParams["FIELD_CODE_1"]; ?>" placeholder="<?= $arParams["FIELD_NAME_1"]; ?>" />
        <div class="wl-mclb__success">∆дите <strong>00:<span class="wl-mclb__timer">28</span> сек.</strong></div>
        <a class="wl-mclb__btn" data-success="<?= GetMessage("MCLB_CLOSE"); ?>" data-order="<?= GetMessage("MCLB_ORDER"); ?>" href="#">
            <i class="fa fa-phone" aria-hidden="true"></i><span><?= GetMessage("MCLB_ORDER"); ?></span>
        </a>
    </div>
</form>