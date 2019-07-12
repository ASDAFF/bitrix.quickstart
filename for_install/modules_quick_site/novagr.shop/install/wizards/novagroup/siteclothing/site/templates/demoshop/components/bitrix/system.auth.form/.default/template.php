<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>

<div class="bx-system-auth-form">
<?
if($arResult["FORM_TYPE"] == "login"):
	if ($_REQUEST["wrong_pass"] == 1) {
		$arResult['ERROR'] = 1;
		$arResult['ERROR_MESSAGE'] = GetMessage("AUTH_WRONG_PASS");
		
		if (!empty($_SESSION["REFFERER_FOR_AUTH"])) $arResult["BACKURL"] = $_SESSION["REFFERER_FOR_AUTH"];
	}

    if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR']==1 and $_REQUEST["wrong_pass"] == 1)
	ShowMessage($arResult['ERROR_MESSAGE']);


$arResult["AUTH_URL"] = SITE_DIR.'auth/ajax/forms.php?login=yes';
?>


<form name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
<?if($arResult["BACKURL"] <> ''):?>
	<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?endif?>
<?foreach ($arResult["POST"] as $key => $value):?>
	<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
<?endforeach?>
	<input type="hidden" name="AUTH_FORM" value="Y" />
	<input type="hidden" name="TYPE" value="AUTH" />
	<h3><?=GetMessage("AUTH_MESSAGE")?>:</h3>
	<div>
		<div class="login">
			<div class="name"><?=GetMessage("AUTH_EMAIL")?></div>
			<div class="value">
				<input type="text" autocomplete="on" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" />
				
			</div>
		</div>
		<div class="pass">
			<div class="name"><?=GetMessage("AUTH_PASSWORD")?></div>
			<div class="value">
			<input type="password" name="USER_PASSWORD" autocomplete="on"  maxlength="50" />
			</div>
		</div>
	</div>
	<div class="clear"></div>
	<div id="autorize_button" class="autorize_button">
		<a onClick="$('#forgot_link').trigger('click');return false;" class="forgot-link" href="#" data-toggle="modal"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a>
		<a href="#" onClick="$('#reg_link').trigger('click');return false;" data-toggle="modal" class="reg_link"><?=GetMessage("AUTH_REGISTER")?></a>
	</div>
	<div class="bas-sub-div">
	<input class="btn" type="submit" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" />
	</div>
	<p><?=GetMessage("AUTH_MESSAGE2")?></p>
	
</form>

<?if($arResult["AUTH_SERVICES"]):?>
<?
$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "", 
	array(
		"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
		"AUTH_URL"=>$arResult["AUTH_URL"],
		"POST"=>$arResult["POST"],
		"POPUP"=>"Y",
		"SUFFIX"=>"form",
	), 
	$component, 
	array("HIDE_ICONS"=>"Y")
);
?>
<?endif?>

<?
//if($arResult["FORM_TYPE"] == "login")
else:
?>

<form action="<?=$arResult["AUTH_URL"]?>">
	<table width="95%">
		<tr>
			<td align="center">
				<?=$arResult["USER_NAME"]?><br />
				[<?=$arResult["USER_LOGIN"]?>]<br />
				<a href="<?=SITE_DIR?>cabinet/userinfo/" title="<?=GetMessage("AUTH_PROFILE")?>"><?=GetMessage("AUTH_PROFILE")?></a><br />
			</td>
		</tr>
		<tr>
			<td align="center">
			<?foreach ($arResult["GET"] as $key => $value):?>
				<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
			<?endforeach?>
			<input type="hidden" name="logout" value="yes" />
			<input type="submit" class="btn" name="logout_butt" value="<?=GetMessage("AUTH_LOGOUT_BUTTON")?>" />
			</td>
		</tr>
	</table>
</form>
<?endif?>
</div><??>