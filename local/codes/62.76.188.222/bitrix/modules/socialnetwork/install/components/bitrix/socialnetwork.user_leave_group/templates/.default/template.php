<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if ($arResult["NEED_AUTH"] == "Y")
{
	$APPLICATION->AuthForm("");
}
elseif (strlen($arResult["FatalError"])>0)
{
	?>
	<span class='errortext'><?=$arResult["FatalError"]?></span><br /><br />
	<?
}
else
{
	if(strlen($arResult["ErrorMessage"])>0)
	{
		?>
		<span class='errortext'><?=$arResult["ErrorMessage"]?></span><br /><br />
		<?
	}

	if ($arResult["ShowForm"] == "Input")
	{
		?>
		<form method="post" name="form1" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data">
			<table class="sonet-message-form data-table" cellspacing="0" cellpadding="0">
				<tr>
					<th colspan="2"><?= GetMessage("SONET_C37_T_PROMT") ?></th>
				</tr>
				<tr>
					<td valign="top" width="10%" nowrap><?= GetMessage("SONET_C37_T_GROUP") ?>:</td>
					<td valign="top">
						<b><?
						if ($arResult["CurrentUserPerms"]["UserCanSeeGroup"])
							echo "<a href=\"".$arResult["Urls"]["Group"]."\">";
						echo $arResult["Group"]["NAME"];
						if ($arResult["CurrentUserPerms"]["UserCanSeeGroup"])
							echo "</a>";
						?></b>
					</td>
				</tr>
			</table>
			<input type="hidden" name="SONET_GROUP_ID" value="<?= $arResult["Group"]["ID"] ?>">
			<?=bitrix_sessid_post()?>
			<br />
			<input type="submit" name="save" value="<?= GetMessage("SONET_C37_T_SAVE") ?>">
			<input type="reset" name="cancel" value="<?= GetMessage("SONET_C37_T_CANCEL") ?>" OnClick="window.location='<?= $arResult["Urls"]["Group"] ?>'">
		</form>
		<?
	}
	else
	{
		?>
		<?= GetMessage("SONET_C37_T_SUCCESS") ?><br><br>
		<?if ($arResult["CurrentUserPerms"]["UserCanSeeGroup"]):?>
			<a href="<?= $arResult["Urls"]["Group"] ?>"><?= $arResult["Group"]["NAME"]; ?></a>
		<?endif;?>
		<?
	}
}
?>