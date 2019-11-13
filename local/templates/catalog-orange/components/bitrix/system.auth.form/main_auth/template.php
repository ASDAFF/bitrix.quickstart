<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="bx-system-auth-form">
<?if($arResult["FORM_TYPE"] == "login"):?>

<div style="margin:0 5px 10px 5px;">
  <img src="<?=$templateFolder?>/images/key.png" border="0" width="18px" align="left">&nbsp;
  <span style="border-bottom: dashed 1px #000;"><?=GetMessage("AUTH_LOGIN_TITLE")?></span>
</div>

<?
if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR'])
	ShowMessage($arResult['ERROR_MESSAGE']);
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
	<input type="hidden" id="USER_REMEMBER_frm" name="USER_REMEMBER" value="Y" />
	<table width="95%" border="0" cellpadding="0" cellspacing="5">
		<tr><td>
			<table cellpadding="3" cellspacing="0" border="0"><tr>
				<td width="70px" valign="top" style="padding-top:3px;"><span class="input_title"><?=GetMessage("AUTH_LOGIN")?>:</span></td>
				<td><input type="text" class="input_bg" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" size="17" /></td>
			</tr></table>
		</td></tr>
		<tr><td>
			<table cellpadding="3" cellspacing="0" border="0"><tr>
				<td width="70px" valign="top" style="padding-top:3px;"><span class="input_title"><?=GetMessage("AUTH_PASSWORD")?>:</span>
				<td><input type="password" class="input_bg" name="USER_PASSWORD" maxlength="50" size="17" /></td>
				<td>
				<?if($arResult["SECURE_AUTH"]):?>
					<span class="bx-auth-secure" id="bx_auth_secure<?=$arResult["RND"]?>" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
						<div class="bx-auth-secure-icon"></div>
					</span>
					<noscript>
					<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
						<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
					</span>
					</noscript>
				<script type="text/javascript">
					document.getElementById('bx_auth_secure<?=$arResult["RND"]?>').style.display = 'inline-block';
				</script>
				<?endif?>
				</td>
			</tr></table>
		</td></tr>
<?if ($arResult["CAPTCHA_CODE"]):?>
		<tr>
			<td>
			<?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:<br />
			<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
			<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /><br /><br />
			<input type="text" name="captcha_word" maxlength="50" value="" /></td>
		</tr>
<?endif?>
		<tr>
			<td align="center"><input type="submit" style="padding:4px; width:50px; cursor:pointer;" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" /></td>
		</tr>
		<tr><td style="font-size:6px;">&nbsp;</td></tr>
		<tr>
			<td align="right" class="links">
				<table width="100%"><tr>
					<td align="left">
						<noindex><a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_REGISTER")?></a></noindex>
					</td>
				<td align="right"><noindex><a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a></noindex></td>
				</tr></table>
			</td>
		</tr>
		<tr>
			<td style="padding-top:10px;">
				<div class="bx-auth-lbl"><?=GetMessage("socserv_as_user_form")?></div>
<?
$APPLICATION->IncludeComponent("infospice.loginza:loginza.auth", ".default", array(
	"GROUP_ID" => array(
		0 => "5",
	),
	"PROVIDERS_SET" => array(
		0 => "yandex",
		1 => "google",
		2 => "odnoklassniki",
		3 => "mailru",
		4 => "vkontakte",
		5 => "facebook",
		6 => "twitter",
		7 => "loginza",
		8 => "mailruapi",
		9 => "myopenid",
		10 => "webmoney",
		11 => "rambler",
		12 => "flickr",
		13 => "lastfm",
		14 => "verisign",
		15 => "aol",
		16 => "steam",
		17 => "openid",
	),
	"PROVIDER" => "",
	"REDIRECT_PAGE" => "",
	"LANG" => "ru",
	"TEXT_LINK" => "",
	"IMAGE_LINK" => $templateFolder."/images/loginza_btn.png"
	),
	false
);
?>
			</td>
		</tr>
	</table>
</form>

<?
//if($arResult["FORM_TYPE"] == "login")
else:
?>

<div style="margin:0 5px 10px 5px;">
  <img src="<?=$templateFolder?>/images/key.png" border="0" width="18px" align="left">&nbsp;
  <span style="border-bottom: dashed 1px #000;"><?=GetMessage("AUTH_PERSONAL_LINK")?></span>
</div>

<table cellspacing="5" style="margin-left:20px;">
  <tr>
    <td width="16px"><img src="<?=$templateFolder?>/images/arrow.png"></td>
    <td><a href="/personal/cart/" class="personal_link"><?=GetMessage("AUTH_BASKET_LINK")?></a></td>
  </tr>
  <tr>
    <td width="16px"><img src="<?=$templateFolder?>/images/arrow.png"></td>
    <td><a href="/personal/orders/" class="personal_link"><?=GetMessage("AUTH_ORDERS_LINK")?></a></td>
  </tr>
  <tr>
    <td width="16px"><img src="<?=$templateFolder?>/images/arrow.png"></td>
    <td><a href="<?=$arResult["PROFILE_URL"]?>" class="personal_link"><?=GetMessage("AUTH_PROFILE_LINK")?></a></td>
  </tr>
</table>

<table align="right" style="padding-bottom:5px;"><tr>
<td style="font-size:10px;"><?=$arResult["USER_NAME"]?>
<td style="font-size:10px;">
  [<a href="<?=$arResult["PROFILE_URL"]?>" title="<?=GetMessage("AUTH_PROFILE")?>"><?=$arResult["USER_LOGIN"]?></a>]
</td>
<td style="font-size:10px;">
<form action="<?=$arResult["AUTH_URL"]?>">
  <input type="hidden" name="logout" value="yes" />
  <input type="submit" style="border:none; background:#fff; text-decoration:underline; font-size:10px; color:#666; cursor:pointer;" name="logout_butt" value="<?=GetMessage("AUTH_LOGOUT_BUTTON")?>" />
</form>
</td>
</tr></table>

<?endif?>
</div>