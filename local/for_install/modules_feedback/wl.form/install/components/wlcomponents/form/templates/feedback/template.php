<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
global $APPLICATION;
?>
<form class="feedback_form" id="FORM_<?= $arParams["ID_FORM"]; ?>" action="<?= $APPLICATION->GetCurPage(true); ?>" onsubmit="return PlFormCheck(this);">
    <input type="hidden" name="FORM_<?= $arParams["ID_FORM"]; ?>" value="1" />
    <?= bitrix_sessid_post(); ?>
    <div class="row">
        <div class="col-xs-12"><h2><?= GetMessage("FEEDBACK_TITLE"); ?></h2></div>
        <div class="col-xs-6">
            <div class="form-group">
                <label for="<?= $arParams["ID_FORM"]; ?>_<?= $arParams["FIELD_CODE_1"]; ?>"><?= $arParams["FIELD_NAME_1"]; ?></label>
                <input type="<?= $arParams["FIELD_TYPE_1"]; ?>" name="<?= $arParams["FIELD_CODE_1"]; ?>" class="form-control" id="<?= $arParams["ID_FORM"]; ?>_<?= $arParams["FIELD_CODE_1"]; ?>">
            </div>
            <div class="form-group">
                <label for="<?= $arParams["ID_FORM"]; ?>_<?= $arParams["FIELD_CODE_2"]; ?>"><?= $arParams["FIELD_NAME_2"]; ?></label>
                <input type="<?= $arParams["FIELD_TYPE_2"]; ?>" name="<?= $arParams["FIELD_CODE_2"]; ?>" class="form-control phone_masked" id="<?= $arParams["ID_FORM"]; ?>_<?= $arParams["FIELD_CODE_2"]; ?>">
            </div>
            <div class="form-group">
                <label for="<?= $arParams["ID_FORM"]; ?>_<?= $arParams["FIELD_CODE_3"]; ?>"><?= $arParams["FIELD_NAME_3"]; ?></label>
                <input type="<?= $arParams["FIELD_TYPE_3"]; ?>" name="<?= $arParams["FIELD_CODE_3"]; ?>" class="form-control" id="<?= $arParams["ID_FORM"]; ?>_<?= $arParams["FIELD_CODE_3"]; ?>">
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label for="<?= $arParams["ID_FORM"]; ?>_<?= $arParams["FIELD_CODE_4"]; ?>"><?= $arParams["FIELD_NAME_4"]; ?></label>
                <textarea name="<?= $arParams["FIELD_CODE_4"]; ?>" class="form-control" id="<?= $arParams["ID_FORM"]; ?>_<?= $arParams["FIELD_CODE_4"]; ?>" rows="8"></textarea>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="alert alert-success" role="alert">
                <ul>
                    <li><?= GetMessage("FEEDBACK_SUCCESS"); ?></li>
                </ul>
            </div>
            <div class="alert alert-danger" role="alert">
                <ul></ul>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group text-right">
                <img class="loader hidden" src="<?=$this->GetFolder();?>/images/loader.gif" /> <button type="submit" class="btn btn-default"><?=GetMessage("FEEDBACK_SUBMIT");?></button>
            </div>
        </div>
    </div>
</form>