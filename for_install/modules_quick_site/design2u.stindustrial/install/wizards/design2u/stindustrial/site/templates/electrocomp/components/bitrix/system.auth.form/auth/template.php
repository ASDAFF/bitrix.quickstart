<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if ($arResult["FORM_TYPE"] == "login"):?>


<div id="login-form-window">

<a href="" onclick="return CloseLoginForm()" style="float:right;"><?=GetMessage("AUTH_CLOSE_WINDOW")?></a>

<form method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
	<?
	if (strlen($arResult["BACKURL"]) > 0)
	{
	?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
	<?
	}
	?>
	<?
	foreach ($arResult["POST"] as $key => $value)
	{
	?>
	<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
	<?
	}
	?>
	<input type="hidden" name="AUTH_FORM" value="Y" />
	<input type="hidden" name="TYPE" value="AUTH" />

	<table width="95%">
			<tr>
				<td colspan="2">
				<?=GetMessage("AUTH_LOGIN")?>:<br />
				<input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" size="17" /></td>
			</tr>
			<tr>
				<td colspan="2">
				<?=GetMessage("AUTH_PASSWORD")?>:<br />
				<input type="password" name="USER_PASSWORD" maxlength="50" size="17" /></td>
			</tr>
		<?
		if ($arResult["STORE_PASSWORD"] == "Y") 
		{
		?>
			<tr>
				<td valign="top"><input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" /></td>
				<td width="100%"><label for="USER_REMEMBER"><?=GetMessage("AUTH_REMEMBER_ME")?></label></td>
			</tr>
		<?
		}
		?>
			<tr>
				<td colspan="2"><input type="submit" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" /></td>
			</tr>

			<tr>
				<td colspan="2"><a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a></td>
			</tr>
		<?
		if($arResult["NEW_USER_REGISTRATION"] == "Y")
		{
		?>
			<tr>
				<td colspan="2"><a href="<?=$arResult["AUTH_REGISTER_URL"]?>"><?=GetMessage("AUTH_REGISTER")?></a><br /></td>
			</tr>
		<?
		}
		?>
	</table>	
</form>
</div>

<img src="<?=$templateFolder?>/images/login.gif" width="10" height="11" border="0" alt="">&nbsp;&nbsp;<a href="<?=$arResult["AUTH_REGISTER_URL"]?>" onclick="return ShowLoginForm();"><?=GetMessage("AUTH_LOGIN_BUTTON")?></a>&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?=$templateFolder?>/images/register.gif" width="8" height="11" border="0" alt="">&nbsp;&nbsp;<a href="<?=$arResult["AUTH_REGISTER_URL"]?>"><?=GetMessage("AUTH_REGISTER")?></a>

<?else:?>

<form action="<?=$arResult["AUTH_URL"]?>">

<?=$arResult["USER_NAME"]?> [<a href="<?=$arResult["PROFILE_URL"]?>" class="profile-link" title="<?=GetMessage("AUTH_PROFILE")?>"><?=$arResult["USER_LOGIN"]?></a>]

<?foreach ($arResult["GET"] as $key => $value):?>
	<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
<?endforeach?>
	<input type="hidden" name="logout" value="yes" />
	<input type="image" src="<?=$templateFolder?>/images/login.gif" alt="<?=GetMessage("AUTH_LOGOUT_BUTTON")?>">
</form>
<?endif?>