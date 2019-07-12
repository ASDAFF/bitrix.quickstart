<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="bx-system-auth-form">
<?if($arResult["FORM_TYPE"] == "login"):?>
	<a href="<?=$arParams["PROFILE_URL"]?>" title="<?=GetMessage("AUTH_PROFILE")?>"><i class="fa fa-lock"></i><?=GetMessage("AUTH_PROFILE")?></a>
<?else:?>
	<table cellpadding="0" cellspacing="0" border="0"><tr>
	<td><a href="<?=$arResult["PROFILE_URL"]?>" title="<?=GetMessage("AUTH_PROFILE")?>"><i class="fa fa-user"></i><?=$arResult["USER_NAME"]?></a></td>
	<td style="border-left:1px solid #E7E7E7">
		<form action="<?=$arResult["AUTH_URL"]?>">
			<?foreach ($arResult["GET"] as $key => $value):?>
				<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
			<?endforeach?>
			<input type="hidden" name="logout" value="yes" />
			<input type="submit" name="logout_butt" value="<?=GetMessage("AUTH_LOGOUT_BUTTON")?>" />
		</form>
	</td>
	</tr></table>
<?endif?>
</div>