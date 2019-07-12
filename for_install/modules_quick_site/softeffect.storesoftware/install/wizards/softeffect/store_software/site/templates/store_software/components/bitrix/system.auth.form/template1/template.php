<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if($arResult["FORM_TYPE"] == "login"):?>


<?
if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR'])
	ShowMessage($arResult['ERROR_MESSAGE']);
?>

<div id="option1" class="loginoption">



	<h2>
	Уже зарегистрированы?
	<span></span>
	</h2>

	<div class="logincontent clearfix">
	
	
	
		
	<form class="form" onsubmit="return validateForm(this)" name="Login" enctype="multipart/form-data" method="post" action="http://softeffect.ru/login/">
	
	<input type="hidden" value="Y" name="AUTH_FORM">
	<input type="hidden" value="AUTH" name="TYPE">
	<input type="hidden" value="/login/index.php" name="backurl">
	
	<div class="loginbuttons">
	
		<input value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>"  name="Login"  id="submit" type="image" alt="Login" src="/images/buttons/login_regular.gif">	
	</div>
	
	
	<table cellspacing="0" cellpadding="0" border="0">
	
	
	<tbody>
	
				
	<th id="lbl_cemail">
	<label for="cemail">
	E-mail:
	</label>
	</th>
	
	<td>
	                       
				<input id="cemail" class="inputfield" type="text" maxlength="60" name="USER_LOGIN" value="<?=$arResult["USER_LOGIN"]?>" size="20" />
				</td>
	</tr>
	
	
	<tr>
	<th id="lbl_cpassword">
	<label for="cpassword">
	Пароль:
	</label>
	</th>
		
	                        <td>
				
				<input id="cpassword" class="inputfield" type="password" autocomplete="off" value="" name="USER_PASSWORD" maxlength="60" size="20" />
	                         </td>
	</tr>
	
	
	
	<tr>
	<td></td>
	<td>
	<a rel="noindex, nofollow" title="Забыли пароль?" href="/login/forgotpass.php">
	<strong>Забыли свой пароль?</strong>
	</a>
	</td>
	
	
	
	</tbody>
	
	
	</table>
	







<?if ($arResult["CAPTCHA_CODE"]):?>
		<tr>
			<td colspan="2">
			<?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:<br />
			<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
			<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /><br /><br />
			<input type="text" name="captcha_word" maxlength="50" value="" /></td>
		</tr>
<?endif?>
		

               

<?php

/**

<td>
<noindex><a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a></noindex>
</td>



<?if($arResult["NEW_USER_REGISTRATION"] == "Y"):?>
		<tr>
			<td colspan="2"><noindex><a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_REGISTER")?></a></noindex><br /></td>
		</tr>
<?endif?>

		






<?if($arResult["AUTH_SERVICES"]):?>
		<tr>
			<td colspan="2">
				<div class="bx-auth-lbl"><?=GetMessage("socserv_as_user_form")?></div>
<?
$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "icons", 
	array(
		"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
		"SUFFIX"=>"form",
	), 
	$component, 
	array("HIDE_ICONS"=>"Y")
);
?>
			</td>
		</tr>

<?endif?>


*/
?>


</form>

</div>
</div>
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
				<a href="<?=$arResult["PROFILE_URL"]?>" title="<?=GetMessage("AUTH_PROFILE")?>"><?=GetMessage("AUTH_PROFILE")?></a><br />
			</td>
		</tr>
		<tr>
			<td align="center">
			<?foreach ($arResult["GET"] as $key => $value):?>
				<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
			<?endforeach?>
			<input type="hidden" name="logout" value="yes" />
			<input type="submit" name="logout_butt" value="<?=GetMessage("AUTH_LOGOUT_BUTTON")?>" />
			</td>
		</tr>
	</table>

</form>
<?endif?>







