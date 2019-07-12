<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><? ?>
<?= ShowError($arResult["strProfileError"]); ?>
<?
if ($arResult['DATA_SAVED'] == 'Y')
    echo ShowNote(GetMessage('PROFILE_DATA_SAVED'));
?>
<div class="bx_profile">
    <form method="post" name="form1" action="<?= $arResult["FORM_TARGET"] ?>?" enctype="multipart/form-data" class="b-form b-profile__form">
        <?= $arResult["BX_SESSION_CHECK"] ?>
        <input type="hidden" name="lang" value="<?= LANG ?>" />
        <input type="hidden" name="ID" value=<?= $arResult["ID"] ?> />
        <input type="hidden" name="LOGIN" value=<?= $arResult["arUser"]["LOGIN"] ?> />
        <input type="hidden" name="EMAIL" value=<?= $arResult["arUser"]["EMAIL"] ?> />

        <div class="b-form__fieldset">
            <div class="b-form__fieldset__caption"><?= GetMessage("LEGEND_PROFILE") ?></div>

            <div class="b-form__field">
                <div class="b-form__field__label"><?= GetMessage('NAME') ?></div>
                <div class="b-form__field__input">
                    <input type="text" name="NAME" maxlength="50" value="<?= $arResult["arUser"]["NAME"] ?>" />
                </div>
            </div>
            <div class="b-form__field">
                <div class="b-form__field__label"><?= GetMessage('LAST_NAME') ?></div>
                <div class="b-form__field__input">
                    <input type="text" name="LAST_NAME" maxlength="50" value="<?= $arResult["arUser"]["LAST_NAME"] ?>" />
                </div>
            </div>
            <div class="b-form__field">
                <div class="b-form__field__label"><?= GetMessage('SECOND_NAME') ?></div>
                <div class="b-form__field__input">
                    <input type="text" name="SECOND_NAME" maxlength="50" value="<?= $arResult["arUser"]["SECOND_NAME"] ?>" />
                </div>
            </div>
            <div class="b-form__fieldset__caption"><?= GetMessage("MAIN_PSWD") ?></div>
            <div class="b-form__field">
                <div class="b-form__field__label"><?= GetMessage('NEW_PASSWORD_REQ') ?></div>
                <div class="b-form__field__input">
                    <input type="password" name="NEW_PASSWORD_REQ" maxlength="50" value="" autocomplete="off" />
                </div>
            </div>
            <div class="b-form__field">
                <div class="b-form__field__label"><?= GetMessage('NEW_PASSWORD_CONFIRM') ?></div>
                <div class="b-form__field__input">
                    <input type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" />
                </div>
            </div>
            <div class="b-form__submit">
                <input name="save" value="<?= GetMessage("MAIN_SAVE") ?>" class="b-button" type="submit">
            </div>
        </div>
    </form>
</div>
<br>
<?
if ($arResult["SOCSERV_ENABLED"]) {
    $APPLICATION->IncludeComponent("bitrix:socserv.auth.split", ".default", array(
        "SHOW_PROFILES" => "Y",
        "ALLOW_DELETE" => "Y"
            ), false
    );
}
?>
