<?php

/** @var CMain $APPLICATION */

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!check_bitrix_sessid()) {
    return;
}

if (isset($arResult['errCode']) && $arResult['errCode']) {
    echo CAdminMessage::ShowMessage(Loc::getMessage($arResult['errCode']));
}

?>

<style>
    .sc_help_link {
        background: url("/bitrix/themes/.default/icons/status_icons.png") no-repeat scroll -12px -235px transparent;
        cursor: pointer;
        float: right;
        width: 25px;
        height: 25px;
        margin-left: 10px;
    }

    .sc_icon {
        display: inline-block;
        height: 25px;
        margin-right: 10px;
        vertical-align: middle;
        width: 25px;
    }

    .sc_icon_success {
        background: url("/bitrix/themes/.default/icons/status_icons.png") no-repeat scroll -14px -19px transparent;
    }

    .sc_icon_warning {
        background: url("/bitrix/themes/.default/icons/status_icons.png") no-repeat scroll -12px -212px transparent;
    }

    .sc_icon_error {
        background: url("/bitrix/themes/.default/icons/status_icons.png") no-repeat scroll -12px -73px transparent;
    }

    .sc_success {
        color: #408218 !important;
        vertical-align: middle;
    }

    .sc_warning {
        color: #000000;
        vertical-align: middle;
    }

    .sc_error {
        color: #DD0000 !important;
        vertical-align: middle;
    }

    .sc_code {
        border: 1px solid #CCC;
        margin: 10px;
        padding: 10px;
        font-family: monospace;
        background-color: #FEFEFA;
    }

    .sc_progress {
        text-align: center !important;
        font-weight: bold !important;
        background-color: #b9cbdf;
        padding: 2px;
        margin: 10px;
    }
</style>

<div class="adm-detail-block" id="tabControl_layout">
    <div class="adm-detail-content-wrap">
        <div class="adm-detail-content">
            <div class="adm-detail-title"><?= Loc::getMessage('ADMITAD_TRACKING_MASTER_TITLE') ?></div>
            <div class="adm-detail-content-item-block">
                <form action="<?= $APPLICATION->GetCurPage() ?>" method="POST">
                    <?= bitrix_sessid_post(); ?>
                    <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
                    <input type="hidden" name="id" value="admitad.tracking">
                    <input type="hidden" name="install" value="Y">
                    <input type="hidden" name="step" value="2">
                    <table class="adm-detail-content-table edit-table">
                        <tbody>
                        <tr>
                            <td colspan="2"><?= Loc::getMessage('INFO_1'); ?></td>
                        </tr>
                        <tr>
                            <td colspan="2"><?= Loc::getMessage('INFO_2'); ?></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div id="result" style="padding-top:10px">
                                    <table id="result_table" width="100%" class="internal">
                                        <tbody>
                                        <tr class="heading">
                                            <td class="align-left" colspan="2"><?= Loc::getMessage('ADMITAD_TRACKING_MASTER_STEP1_TITLE'); ?></td>
                                        </tr>
                                        <?php foreach ($arResult['checks'] as $check): ?>
                                            <tr id="">
                                                <td style="width: 40%;"><?= $check["title"] ?></td>
                                                <td>
                                                    <div class="sc_icon sc_icon_<?= strtolower($check['status']) ?>"></div>
                                                    <span class="sc_<?= strtolower($check['status']) ?>"><?= $check['text'] ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div style="padding: 11px 0 2px; height:28px;">
                        <div align="right" style="float:right; position:relative;">
                            <input type="submit" name="inst" value="<?= Loc::getMessage("ADMITAD_TRACKING_MASTER_NEXT_STEP"); ?>" class="adm-btn-save">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="adm-detail-content-btns-wrap">
            <div class="adm-detail-content-btns adm-detail-content-btns-empty"></div>
        </div>
    </div>
</div>