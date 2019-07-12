<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if($arResult["ALLOW_ANONYMOUS"]=="Y" && $_REQUEST["authorize"]<>"YES" && $_REQUEST["register"]<>"YES"):?>

    <div class="col-xs-12">
        <h2><?echo GetMessage("subscr_title_auth2")?></h2>
    </div>
    <div class="col-xs-12 col-sm-6">
        <p><?echo GetMessage("adm_auth1")?> <a href="<?echo $arResult["FORM_ACTION"]?>?authorize=YES&amp;sf_EMAIL=<?echo $arResult["REQUEST"]["EMAIL"]?><?echo $arResult["REQUEST"]["RUBRICS_PARAM"]?>"><?echo GetMessage("adm_auth2")?></a>.</p>
        <?if($arResult["ALLOW_REGISTER"]=="Y"):?>
            <p><?echo GetMessage("adm_reg1")?> <a href="<?echo $arResult["FORM_ACTION"]?>?register=YES&amp;sf_EMAIL=<?echo $arResult["REQUEST"]["EMAIL"]?><?echo $arResult["REQUEST"]["RUBRICS_PARAM"]?>"><?echo GetMessage("adm_reg2")?></a>.</p>
        <?endif;?>
    </div>
    <div class="col-xs-12 col-sm-6"><?echo GetMessage("adm_reg_text")?></div>

<?elseif($arResult["ALLOW_ANONYMOUS"]=="N" || $_REQUEST["authorize"]=="YES" || $_REQUEST["register"]=="YES"):?>

	<form class="col-xs-12 col-sm-6" action="<?=$arResult["FORM_ACTION"]?>" method="post">
	<?echo bitrix_sessid_post();?>
	<h2><?echo GetMessage("adm_auth_exist")?></h2>
    <p>
        <?if($arResult["ALLOW_ANONYMOUS"]=="Y"):?>
            <?echo GetMessage("subscr_auth_note")?>
        <?else:?>
            <?echo GetMessage("adm_must_auth")?>
        <?endif;?>
    </p>
    <div class="form-group">
		<input class="form-control" type="text" name="LOGIN" value="<?echo $arResult["REQUEST"]["LOGIN"]?>" size="20" placeholder="<?echo GetMessage("adm_auth_login")?>*">
    </div>
    <div class="form-group">
		<input class="form-control" type="password" name="PASSWORD" size="20" value="<?echo $arResult["REQUEST"]["PASSWORD"]?>" placeholder="<?echo GetMessage("adm_auth_pass")?>*">
    </div>
	<input class="btn btn1" type="submit" name="Save" value="<?echo GetMessage("adm_auth_butt")?>" />

	<?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
		<input type="hidden" name="RUB_ID[]" value="<?=$itemValue["ID"]?>">
	<?endforeach;?>
	<input type="hidden" name="PostAction" value="<?echo ($arResult["ID"]>0? "Update":"Add")?>" />
	<input type="hidden" name="ID" value="<?echo $arResult["SUBSCRIPTION"]["ID"];?>" />
	<?if($_REQUEST["register"] == "YES"):?>
		<input type="hidden" name="register" value="YES" />
	<?endif;?>
	<?if($_REQUEST["authorize"]=="YES"):?>
		<input type="hidden" name="authorize" value="YES" />
	<?endif;?>
	</form>

	<?if($arResult["ALLOW_REGISTER"]=="Y"):
		?>
		<form class="col-xs-12 col-sm-6" action="<?=$arResult["FORM_ACTION"]?>" method="post">
		<?echo bitrix_sessid_post();?>
        <h2><?echo GetMessage("adm_reg_new")?></h2>
        <p>
            <?if($arResult["ALLOW_ANONYMOUS"]=="Y"):?>
                <?echo GetMessage("subscr_auth_note")?>
            <?else:?>
                <?echo GetMessage("adm_must_auth")?>
            <?endif;?>
        </p>
        <div class="form-group">
            <input class="form-control" type="text" name="NEW_LOGIN" value="<?echo $arResult["REQUEST"]["NEW_LOGIN"]?>" size="20" placeholder="<?echo GetMessage("adm_reg_login")?>*"></p>
        </div>
        <div class="form-group">
            <input class="form-control" type="password" name="NEW_PASSWORD" size="20" value="<?echo $arResult["REQUEST"]["NEW_PASSWORD"]?>" placeholder="<?echo GetMessage("adm_reg_pass")?>*">
        </div>
        <div class="form-group">
            <input class="form-control" type="password" name="CONFIRM_PASSWORD" size="20" value="<?echo $arResult["REQUEST"]["CONFIRM_PASSWORD"]?>" placeholder="<?echo GetMessage("adm_reg_pass_conf")?>*">
        </div>
        <div class="form-group">
            <input class="form-control" type="text" name="EMAIL" value="<?=$arResult["SUBSCRIPTION"]["EMAIL"]!=""?$arResult["SUBSCRIPTION"]["EMAIL"]:$arResult["REQUEST"]["EMAIL"];?>" size="30" maxlength="255" placeholder="<?echo GetMessage("subscr_email")?>*">
        </div>
		<?
        /* CAPTCHA */
        if (COption::GetOptionString("main", "captcha_registration", "N") == "Y"):
            $capCode = $GLOBALS["APPLICATION"]->CaptchaGetCode();
        ?>
            <div class="form-group clearfix">
				<input type="hidden" name="captcha_sid" value="<?= htmlspecialcharsbx($capCode) ?>" />
				<img class="captcha-img pull-right" src="/bitrix/tools/captcha.php?captcha_sid=<?= htmlspecialcharsbx($capCode) ?>" alt="CAPTCHA" title="<?=GetMessage("subscr_CAPTCHA_REGF_TITLE")?>">
                <div class="l-overflow">
                    <input class="form-control" type="text" name="captcha_word" size="30" maxlength="50" value="" placeholder="<?=GetMessage("subscr_CAPTCHA_REGF_PROMT")?>">
                </div>
            </div>
        <?endif;?>
		
        <input class="btn btn1" type="submit" name="Save" value="<?echo GetMessage("adm_reg_butt")?>" />

		<?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
			<input type="hidden" name="RUB_ID[]" value="<?=$itemValue["ID"]?>">
		<?endforeach;?>
		<input type="hidden" name="PostAction" value="<?echo ($arResult["ID"]>0? "Update":"Add")?>" />
		<input type="hidden" name="ID" value="<?echo $arResult["SUBSCRIPTION"]["ID"];?>" />
		<?if($_REQUEST["register"] == "YES"):?>
			<input type="hidden" name="register" value="YES" />
		<?endif;?>
		<?if($_REQUEST["authorize"]=="YES"):?>
			<input type="hidden" name="authorize" value="YES" />
		<?endif;?>
		</form>

	<?endif;?>

<?endif; //$arResult["ALLOW_ANONYMOUS"]=="Y" && $authorize<>"YES"?>
