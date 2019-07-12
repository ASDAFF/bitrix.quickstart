<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

use \Bitrix\Main\Localization\Loc;
?>

<div class="bx-auth-profile">
    <?php if(!empty($arResul['strProfileError'])): ?>
        <div class="alert alert-danger" role="alert">
            <?=$arResult['strProfileError']?>
        </div>
    <?php endif; ?>

    <?php if($arResult['DATA_SAVED'] == 'Y'): ?>
		<div class="alert alert-success" role="alert">
			<?=Loc::getMessage('PROFILE_DATA_SAVED'); ?>
        </div>
    <?php endif; ?>

    <form
        class="form-horizontal form"
        method="POST"
        name="form1"
        action="<?=$arResult["FORM_TARGET"]?>"
        enctype="multipart/form-data"
    >
        <?=$arResult['BX_SESSION_CHECK']?>
        <input type="hidden" name="lang" value="<?=LANG?>">
        <input type="hidden" name="ID" value=<?=$arResult["ID"]?>>

        <div class="form-group">
            <label for="TITLE" class="col-md-4 col-lg-3 control-label text-nowrap">
                <?=Loc::getMessage("main_profile_title")?>
            </label>
            <div class="col-md-8 col-lg-9">
                <input class="form-control" type="text" name="TITLE" id="TITLE" value="<?=$arResult["arUser"]["TITLE"]?>">
            </div>
        </div>

        <div class="form-group">
            <label for="NAME" class="col-md-4 col-lg-3 control-label text-nowrap">
                <?=Loc::getMessage("NAME")?>
            </label>
            <div class="col-md-8 col-lg-9">
                <input class="form-control" type="text" name="NAME" id="NAME" value="<?=$arResult["arUser"]["NAME"]?>">
            </div>
        </div>

        <div class="form-group">
            <label for="LAST_NAME" class="col-md-4 col-lg-3 control-label text-nowrap">
                <?=Loc::getMessage("LAST_NAME")?>
            </label>
            <div class="col-md-8 col-lg-9">
                <input class="form-control" type="text" name="LAST_NAME" id="LAST_NAME" value="<?=$arResult["arUser"]["LAST_NAME"]?>">
            </div>
        </div>

        <div class="form-group">
            <label for="SECOND_NAME" class="col-md-4 col-lg-3 control-label text-nowrap">
                <?=Loc::getMessage("SECOND_NAME")?>
            </label>
            <div class="col-md-8 col-lg-9">
                <input class="form-control" type="text" name="SECOND_NAME" id="SECOND_NAME" value="<?=$arResult["arUser"]["SECOND_NAME"]?>">
            </div>
        </div>

        <div class="form-group">
            <label for="EMAIL" class="col-md-4 col-lg-3 control-label text-nowrap">
                <?=Loc::getMessage("EMAIL")?>
                <span class="required">*</span>
            </label>
            <div class="col-md-8 col-lg-9">
                <input class="form-control" type="text" name="EMAIL" id="EMAIL" value="<?=$arResult["arUser"]["EMAIL"]?>">
            </div>
        </div>

        <div class="form-group">
            <label for="LOGIN" class="col-md-4 col-lg-3 control-label text-nowrap">
                <?=Loc::getMessage("LOGIN")?>
                <span class="required">*</span>
            </label>
            <div class="col-md-8 col-lg-9">
                <input class="form-control" type="text" name="LOGIN" id="LOGIN" value="<?=$arResult["arUser"]["LOGIN"]?>">
            </div>
        </div>

        <?php if($arResult["arUser"]["EXTERNAL_AUTH_ID"] == ''): ?>
             <div class="form-group">
                <label for="NEW_PASSWORD" class="col-md-4 col-lg-3 control-label text-nowrap">
                    <?=Loc::getMessage("NEW_PASSWORD")?>
                    <span class="required">*</span>
                </label>
                <div class="col-md-8 col-lg-9">
                    <input class="form-control" type="text" name="NEW_PASSWORD" id="NEW_PASSWORD" value="">
                </div>
            </div>

            <div class="form-group">
                <label for="NEW_PASSWORD_CONFIRM" class="col-md-4 col-lg-3 control-label text-nowrap">
                    <?=Loc::getMessage("NEW_PASSWORD_CONFIRM")?>
                    <span class="required">*</span>
                </label>
                <div class="col-md-8 col-lg-9">
                    <input class="form-control" type="text" name="NEW_PASSWORD_CONFIRM" id="NEW_PASSWORD_CONFIRM" value="">
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group">
			<div class="col-sm-offset-3 col-md-8 col-lg-9">
				<input class="btn btn-default btn2" type="submit" name="save" value="<?=(($arResult["ID"]>0) ? Loc::getMessage("MAIN_SAVE") : Loc::getMessage("MAIN_ADD"))?>">
				<input class="btn btn-default btn-button" type="reset" value="<?=Loc::getMessage('MAIN_RESET');?>">
			</div>
		</div>

    </form>

    <div>
        <?=Loc::getMessage('REQUIED_FIELDS_NOTE');?>
    </div>
</div>
