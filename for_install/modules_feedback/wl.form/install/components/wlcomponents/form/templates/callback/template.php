<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
global $APPLICATION;
?>
<form class="callback_form" id="FORM_<?= $arParams["ID_FORM"]; ?>" action="<?= $APPLICATION->GetCurPage(true); ?>" onsubmit="return CallBackForm(this);">
    <input type="hidden" name="FORM_<?= $arParams["ID_FORM"]; ?>" value="1" />
    <?= bitrix_sessid_post(); ?>
    <div id="callBack" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?= GetMessage("CALLBACK_TITLE"); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="input_block">
                        <div class="form-group">
                            <label for="<?= $arParams["ID_FORM"]; ?>_<?= $arParams["FIELD_CODE_1"]; ?>"><?= $arParams["FIELD_NAME_1"]; ?></label>
                            <input type="<?= $arParams["FIELD_TYPE_1"]; ?>" name="<?= $arParams["FIELD_CODE_1"]; ?>" class="form-control" id="<?= $arParams["ID_FORM"]; ?>_<?= $arParams["FIELD_CODE_1"]; ?>">
                        </div>
                        <div class="form-group">
                            <label for="<?= $arParams["ID_FORM"]; ?>_<?= $arParams["FIELD_CODE_2"]; ?>"><?= $arParams["FIELD_NAME_2"]; ?></label>
                            <input type="<?= $arParams["FIELD_TYPE_2"]; ?>" name="<?= $arParams["FIELD_CODE_2"]; ?>" class="form-control phone_masked" id="<?= $arParams["ID_FORM"]; ?>_<?= $arParams["FIELD_CODE_2"]; ?>">
                        </div>
                    </div>
                    <div class="alert alert-success" role="alert">
                        <ul>
                            <li><?= GetMessage("CALLBACK_FORM_SUCCESS"); ?></li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-group text-center loader hidden">
                        <img src="<?= $this->GetFolder(); ?>/images/loader.gif" />
                    </div>
                    <div>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= GetMessage("CALLBACK_CLOSE"); ?></button>
                        <button type="submit" class="btn btn-primary"><?= GetMessage("CALLBACK_SEND"); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>