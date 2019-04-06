<?if(!IsModuleInstalled("iblock"))
{
	echo CAdminMessage::ShowMessage(GetMessage("ITHIVE_OFFICES_INSTALL_IBLOCK"));
	?>
	<form action="<?echo $APPLICATION->GetCurPage()?>">
	<p>
		<input type="hidden" name="lang" value="<?echo LANG?>">
		<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">	
	</p>
	<form>
	<?
}
else
{
	require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");

	?>
	<form action="<?= $APPLICATION->GetCurPage()?>" name="ithive_offices_install">
	<?=bitrix_sessid_post()?>
		<input type="hidden" name="lang" value="<?= LANG ?>">
		<input type="hidden" name="id" value="ithive.offices">
		<input type="hidden" name="install" value="Y">
		<input type="hidden" name="step" value="2">

		<script language="JavaScript">
		<!--
		function ChangeInstallPublic(val)
		{
			document.ithive_offices_install.public_dir.disabled = !val;
			document.ithive_offices_install.public_rewrite.disabled = !val;
		}
		//-->
		</script>

		<table cellpadding="3" cellspacing="0" border="0" width="0%">
			<tr>
				<td><input type="checkbox" name="install_public" value="Y" id="install_public" OnClick="ChangeInstallPublic(this.checked)" checked="checked" /></td>
				<td><p><label for="install_public"><?=GetMessage("ITHIVE_OFFICES_COPY_PUBLIC_FILES")?></label></p></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<table cellpadding="3" cellspacing="0" border="0" width="0%">
						<tr>
							<td><p><?= GetMessage("ITHIVE_OFFICES_COPY_FOLDER")." - ".CMainPage::GetSiteByHost()."):" ?></p></td>
							<td><input type="input" name="public_dir" value="offices" size="40"></td>
						</tr>
						<tr>
							<td><p><label for="public_rewrite"><?= GetMessage("ITHIVE_OFFICES_REWRITE_ADD") ?>:</label></p></td>
							<td><input type="checkbox" name="public_rewrite" value="Y" id="public_rewrite"></td>
						</tr>
					</table>
				</td>
			</tr>
<?
$db_iblock_type = CIBlockType::GetList();
while($ar_iblock_type = $db_iblock_type->Fetch())
{
	$arIBType[] = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG);
}
if(count($arIBType))
{
?>
			<tr>
				<td><input type="checkbox" name="install_demo_data" value="Y" id="install_demo_data" checked="checked" disabled="disabled" /></td>
				<td><p><label for="install_demo_data"><?= GetMessage("ITHIVE_OFFICES_COPY_DEMO_DATA") ?></label></p></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<table cellpadding="3" cellspacing="0" border="0" width="0%">
						<tr>
							<td><p><?= GetMessage("ITHIVE_OFFICES_COPY_DEMO_DATA_IBLOCK_TYPE") ?></p></td>
							<td><select name="demo_data_iblock_type" size="1">
									<?foreach($arIBType as $type):?><option value="<?=$type["ID"]?>"><?=$type["NAME"]?></option><?endforeach;?>
								</select></td>
						</tr>
						<tr>
							<td><p><label for="demo_data_rewrite"><?= GetMessage("ITHIVE_OFFICES_COPY_DEMO_DATA_REWRITE_ADD") ?>:</label></p></td>
							<td><input type="checkbox" name="demo_data_rewrite" value="N" id="demo_data_rewrite" checked="checked"></td>
						</tr>
					</table>
				</td>
			</tr>
<?
$MAP_KEY = '';
$strMapKeys = COPtion::GetOptionString('fileman', 'map_yandex_keys');

$strDomain = $_SERVER['HTTP_HOST'];
$wwwPos = strpos($strDomian, 'www.');
if ($wwwPos === 0)
	$strDomain = substr($strDomain, 4);

if ($strMapKeys)
{
	$arMapKeys = unserialize($strMapKeys);
	
	if (array_key_exists($strDomain, $arMapKeys))
		$MAP_KEY = $arMapKeys[$strDomain];
}
?>			<tr>
				<td>&nbsp;</td>
				<td>
					<table cellpadding="3" cellspacing="0" border="0" width="0%">
						<tr>
							<td><p><?= GetMessage("ITHIVE_OFFICES_YANDEX_MAP_API_KEY") ?></p></td>
							<td><input type="input" name="yandex_map_api_key" value="<?=$MAP_KEY?>" size="60"><br />
								<a href="http://api.yandex.ru/maps/form.xml" target="_blank"><small><?= GetMessage("ITHIVE_OFFICES_GET_YANDEX_MAP_API_KEY") ?></small></a></td>
						</tr>
					</table>
				</td>
			</tr>
<?
}
else
{
?>
			<tr>
				<td><input type="checkbox" name="install_demo_data_new_iblock_type" value="company" id="install_demo_data_new_iblock_type" checked="checked" disabled="disabled" /></td>
				<td><p><label for="install_demo_data"><?= GetMessage("ITHIVE_OFFICES_COPY_DEMO_DATA_NEW_IBLOCK_TYPE") ?></label></p></td>
			</tr>
<?
}
?>
		</table>		
		<script language="JavaScript">
		<!--
		ChangeInstallPublic(true);
		//-->
		</script>
		<br>
		<input type="submit" name="inst" value="<?= GetMessage("MOD_INSTALL")?>">
	</form>
	<?
}
?>