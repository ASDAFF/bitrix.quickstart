<?
//<title>Yandex simple</title>
__IncludeLang(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/lang/", "/export_setup_templ.php"));

global $APPLICATION;

$arSetupErrors = array();
if (($ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY') && $STEP == 1)
{
	if (isset($arOldSetupVars['YANDEX_EXPORT']))
		$YANDEX_EXPORT = $arOldSetupVars['YANDEX_EXPORT'];
	if (isset($arOldSetupVars['SETUP_FILE_NAME']))
		$SETUP_FILE_NAME = $arOldSetupVars['SETUP_FILE_NAME'];
	if (isset($arOldSetupVars['SETUP_PROFILE_NAME']))
		$SETUP_PROFILE_NAME = $arOldSetupVars['SETUP_PROFILE_NAME'];
	if (isset($arOldSetupVars['SETUP_SERVER_NAME']))
		$SETUP_SERVER_NAME = $arOldSetupVars['SETUP_SERVER_NAME'];
}

if ($STEP>1)
{
	if (!is_array($YANDEX_EXPORT) || count($YANDEX_EXPORT)<=0)
	{
		$arSetupErrors[] = GetMessage("CET_ERROR_NO_IBLOCKS");
	}

	if (strlen($SETUP_FILE_NAME)<=0)
	{
		$arSetupErrors[] = GetMessage("CET_ERROR_NO_FILENAME");
	}
	elseif (preg_match(BX_CATALOG_FILENAME_REG,$SETUP_FILE_NAME))
	{
		$arSetupErrors[] = GetMessage("CES_ERROR_BAD_EXPORT_FILENAME");
	}
	elseif ($APPLICATION->GetFileAccessPermission($SETUP_FILE_NAME) < "W")
	{
		$arSetupErrors[] = str_replace("#FILE#", $SETUP_FILE_NAME, GetMessage('CET_YAND_RUN_ERR_SETUP_FILE_ACCESS_DENIED'));
	}

	if (($ACTION=="EXPORT_SETUP" || $ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY') && strlen($SETUP_PROFILE_NAME)<=0)
	{
		$arSetupErrors[] = GetMessage("CET_ERROR_NO_PROFILE_NAME");
	}

	if (!empty($arSetupErrors))
	{
		$STEP = 1;
	}
}

if (!empty($arSetupErrors))
	echo ShowError(implode('<br />', $arSetupErrors));

if ($STEP==1)
{
	if (CModule::IncludeModule("iblock"))
	{
		// Get IBlock list
		?>
		<form method="POST" action="<? echo $APPLICATION->GetCurPage(); ?>" enctype="multipart/form-data" name="dataload">
		<? echo bitrix_sessid_post(); ?>
		<?if ($ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY')
		{
			?><input type="hidden" name="PROFILE_ID" value="<? echo intval($PROFILE_ID); ?>"><?
		}
		?>
		<table width="100%">
			<tr>
				<td width="0%" valign="top">
					<font class="text" style="font-size: 20px;">1.&nbsp;&nbsp;&nbsp;</font>
				</td>
				<td valign="top">
					<font class="text"><?echo GetMessage("CET_EXPORT_CATALOGS");?><br><br></font>
					<?
					if (!isset($YANDEX_EXPORT) || !is_array($YANDEX_EXPORT))
					{
						$YANDEX_EXPORT = array();
					}
					$boolAll = false;
					$intCountChecked = 0;
					$intCountAvailIBlock = 0;
					$arIBlockList = array();
					$db_res = CIBlock::GetList(Array("IBLOCK_TYPE"=>"ASC", "NAME"=>"ASC"),array('CHECK_PERMISSIONS' => 'Y','MIN_PERMISSION' => 'W'));
					while ($res = $db_res->Fetch())
					{
						if ($ar_res1 = CCatalog::GetByID($res["ID"]))
						{
							$arSiteList = array();
							$rsSites = CIBlock::GetSite($res["ID"]);
							while ($arSite = $rsSites->Fetch())
							{
								$arSiteList[] = $arSite["SITE_ID"];
							}

							$boolYandex = (in_array($res['ID'],$YANDEX_EXPORT));
							$arIBlockList[] = array(
								'ID' => $res['ID'],
								'NAME' => $res['NAME'],
								'IBLOCK_TYPE_ID' => $res['IBLOCK_TYPE_ID'],
								'YANDEX_EXPORT' => $boolYandex,
								'SITE_LIST' => '('.implode(' ',$arSiteList).')',
							);
							if ($boolYandex)
								$intCountChecked++;
							$intCountAvailIBlock++;
						}
					}
					if ($intCountChecked == $intCountAvailIBlock)
						$boolAll = true;
					?>
					<table width="100%" cellspacing="1" cellpadding="2" border="0">
						<tr>
							<td valign="top" class="tablehead1">
								<font class="tableheadtext"><?echo GetMessage("CET_CATALOG");?></font>
							</td>
							<td valign="top" class="tablehead3">
								<font class="tableheadtext"><?echo GetMessage("CET_EXPORT2YANDEX");?></font>&nbsp;<input style="vertical-align: middle;" type="checkbox" name="yandex_export_all" id="yandex_export_all" value="Y" onclick="checkAll(this,<? echo $intCountAvailIBlock; ?>);"<? echo ($boolAll ? ' checked' : ''); ?>>
							</td>
						</tr>
						<?
						foreach ($arIBlockList as $key => $arIBlock)
						{
							?><tr>
							<td class="tablebody1">
								<font class="tablebodytext"><? echo htmlspecialcharsex("[".$arIBlock["IBLOCK_TYPE_ID"]."] ".$arIBlock["NAME"]." ".$arIBlock['SITE_LIST']); ?></font>
							</td>
							<td class="tablebody3" align="center">
								<font class="tablebodytext">
									<input type="checkbox" name="YANDEX_EXPORT[<? echo $key; ?>]" id="YANDEX_EXPORT_<? echo $key; ?>" value="<? echo $arIBlock["ID"]; ?>"<? if ($arIBlock['YANDEX_EXPORT']) echo " checked"; ?> onclick="checkOne(this,<? echo $intCountAvailIBlock; ?>);">
								</font>
							</td>
							</tr><?
						}
						?>
					</table>
					<input type="hidden" name="count_checked" id="count_checked" value="<? echo $intCountChecked; ?>">
					<script type="text/javascript">
						function checkAll(obj,cnt)
						{
							var boolCheck = obj.checked;
							for (i = 0; i < cnt; i++)
							{
								BX('YANDEX_EXPORT_'+i).checked = boolCheck;
							}
							BX('count_checked').value = (boolCheck ? cnt : 0);
						}
						function checkOne(obj,cnt)
						{
							var boolCheck = obj.checked;
							var intCurrent = parseInt(BX('count_checked').value);
							intCurrent += (boolCheck ? 1 : -1);
							BX('yandex_export_all').checked = (intCurrent < cnt ? false : true);
							BX('count_checked').value = intCurrent;
						}
					</script>
					<br>
				</td>
			</tr>

			<tr>
				<td width="0%" valign="top">
					<font class="text" style="font-size: 20px;">2.&nbsp;&nbsp;&nbsp;</font>
				</td>
				<td width="100%" valign="top">
					<font class="text">
					<?echo GetMessage("CET_SERVER_NAME");?> <input type="text" name="SETUP_SERVER_NAME" value="<?echo (strlen($SETUP_SERVER_NAME)>0) ? htmlspecialcharsbx($SETUP_SERVER_NAME) : '' ?>" size="50" /> <input type="button" onclick="this.form['SETUP_SERVER_NAME'].value = window.location.host;" value="<?echo GetMessage('CET_SERVER_NAME_SET_CURRENT')?>" />
					</font>
					<br><br>
				</td>
			</tr>

			<tr>
				<td width="0%" valign="top">
					<font class="text" style="font-size: 20px;">3.&nbsp;&nbsp;&nbsp;</font>
				</td>
				<td width="100%" valign="top">
					<font class="text">
					<?echo GetMessage("CET_SAVE_FILENAME");?> <input type="text" name="SETUP_FILE_NAME" value="<?echo htmlspecialcharsbx(strlen($SETUP_FILE_NAME)>0 ? $SETUP_FILE_NAME : (COption::GetOptionString("catalog", "export_default_path", "/bitrix/catalog_export/"))."yandex_".mt_rand(0, 999999).".php"); ?>" size="50">
					</font>
					<br><br>
				</td>
			</tr>

			<?if ($ACTION=="EXPORT_SETUP" || $ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY'):?>
				<tr>
					<td width="0%" valign="top">
						<font class="text" style="font-size: 20px;">4.&nbsp;&nbsp;&nbsp;</font>
					</td>
					<td width="100%" valign="top">
						<font class="text">
						<?echo GetMessage("CET_PROFILE_NAME");?> <input type="text" name="SETUP_PROFILE_NAME" value="<?echo htmlspecialcharsbx($SETUP_PROFILE_NAME)?>" size="30">
						</font>
						<br><br>
					</td>
				</tr>
			<?endif;?>

			<tr>
				<td width="0%" valign="top">
					&nbsp;
				</td>
				<td width="100%" valign="top">
					<input type="hidden" name="lang" value="<?echo LANGUAGE_ID ?>">
					<input type="hidden" name="ACT_FILE" value="<?echo htmlspecialcharsbx($_REQUEST["ACT_FILE"]) ?>">
					<input type="hidden" name="ACTION" value="<?echo htmlspecialcharsbx($ACTION) ?>">
					<input type="hidden" name="STEP" value="<?echo intval($STEP) + 1 ?>">
					<input type="hidden" name="SETUP_FIELDS_LIST" value="YANDEX_EXPORT,SETUP_SERVER_NAME,SETUP_FILE_NAME">
					<input type="submit" value="<?echo ($ACTION=="EXPORT")?GetMessage("CET_EXPORT"):GetMessage("CET_SAVE")?>">
				</td>
			</tr>
		</table>
		</form>
		<?
	}
}
elseif ($STEP==2)
{
	$SETUP_SERVER_NAME = htmlspecialcharsbx($SETUP_SERVER_NAME);
	$_POST['SETUP_SERVER_NAME'] = htmlspecialcharsbx($_POST['SETUP_SERVER_NAME']);
	$_REQUEST['SETUP_SERVER_NAME'] = htmlspecialcharsbx($_REQUEST['SETUP_SERVER_NAME']);

	$FINITE = true;
}
?>