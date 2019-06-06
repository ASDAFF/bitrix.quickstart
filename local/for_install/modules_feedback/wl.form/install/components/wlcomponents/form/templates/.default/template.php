<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
global $APPLICATION;
?>
<form class="tb_form" id="FORM_<?= $arParams["ID_FORM"]; ?>" action="<?=$APPLICATION->GetCurPage(true);?>" onsubmit="return PlFormCheck(this);">
    <input type="hidden" name="FORM_<?= $arParams["ID_FORM"]; ?>" value="1" />
    <?= bitrix_sessid_post(); ?>
    <? for ($i = 1; $i <= intval($arParams["CTN_FIELDS"]); $i++) { ?>
        <? if (in_array($arParams["FIELD_TYPE_" . $i], Array("text", "phone", "email"))) { ?>
            <div class="form-group">
                <label for="<?= $arParams["ID_FORM"]; ?>_<?= $arParams["FIELD_CODE_" . $i]; ?>"><?= $arParams["FIELD_NAME_" . $i]; ?></label>
                <input type="<?= $arParams["FIELD_TYPE_" . $i]; ?>" name="<?= $arParams["FIELD_CODE_" . $i]; ?>" class="form-control<? if($arParams["FIELD_TYPE_" . $i] == "phone") echo " phone_masked"; ?>" id="<?= $arParams["ID_FORM"]; ?>_<?= $arParams["FIELD_CODE_" . $i]; ?>">
            </div>
        <? } ?>
    <? } ?>
    <div class="alert alert-success" role="alert">
        <ul>
            <li><?=GetMessage("TB_FORM_SUCCESS");?></li>
        </ul>
    </div>
    <div class="alert alert-danger" role="alert">
        <ul></ul>
    </div>
    <button type="submit" class="btn btn-default"><?=GetMessage("TB_FORM_SUBMIT");?></button> <img class="loader hidden" src="<?=$this->GetFolder();?>/images/loader.gif" />
</form>