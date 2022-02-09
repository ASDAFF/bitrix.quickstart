<?
//<title>Froogle</title>
__IncludeLang(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/lang/", "/export_setup_templ.php"));

ClearVars();

$strFroogleError = "";
if (($ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY') && $STEP == 1)
{
	if (isset($arOldSetupVars['IBLOCK_ID']))
		$IBLOCK_ID = $arOldSetupVars['IBLOCK_ID'];
	if (isset($arOldSetupVars['SETUP_FILE_NAME']))
		$SETUP_FILE_NAME = $arOldSetupVars['SETUP_FILE_NAME'];
	if (isset($arOldSetupVars['SETUP_PROFILE_NAME']))
		$SETUP_PROFILE_NAME = $arOldSetupVars['SETUP_PROFILE_NAME'];
	if (isset($arOldSetupVars['V']))
		$V = $arOldSetupVars['V'];
}

if ($STEP>1)
{
	$IBLOCK_ID = intval($IBLOCK_ID);
	$rsIBlocks = CIBlock::GetByID($IBLOCK_ID);
	if ($IBLOCK_ID<=0 || !($arIBlock = $rsIBlocks->Fetch()))
	{
		$strFroogleError .= GetMessage("CET_ERROR_NO_IBLOCK1")." #".$IBLOCK_ID." ".GetMessage("CET_ERROR_NO_IBLOCK2")."<br>";
	}
	elseif (!CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, 'iblock_admin_display'))
	{
		$strCSVError .= str_replace('#IBLOCK_ID#',$IBLOCK_ID,GetMessage('CET_ERROR_IBLOCK_PERM')).'<br>';
	}

	if (strlen($SETUP_FILE_NAME)<=0)
	{
		$strFroogleError .= GetMessage("CET_ERROR_NO_FILENAME")."<br>";
	}
	elseif (preg_match(BX_CATALOG_FILENAME_REG,$SETUP_FILE_NAME))
	{
		$strFroogleError .= GetMessage("CES_ERROR_BAD_EXPORT_FILENAME")."<br>";
	}
	elseif ($GLOBALS["APPLICATION"]->GetFileAccessPermission($SETUP_FILE_NAME) < "W")
	{
		$strFroogleError .= str_replace("#FILE#", $SETUP_FILE_NAME, "You do not have access rights to add or modify #FILE#")."<br>";
	}

	if (strlen($strFroogleError)<=0)
	{
		$bAllSections = False;
		$arSections = array();
		if (is_array($V))
		{
			foreach ($V as $key => $value)
			{
				if (trim($value)=="0")
				{
					$bAllSections = True;
					break;
				}
				if (intval($value)>0)
				{
					$arSections[] = intval($value);
				}
			}
		}

		if (!$bAllSections && count($arSections)<=0)
			$strFroogleError .= GetMessage("CET_ERROR_NO_GROUPS")."<br>";
	}

	if (($ACTION=="EXPORT_SETUP" || $ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY') && strlen($SETUP_PROFILE_NAME)<=0)
		$strFroogleError .= GetMessage("CET_ERROR_NO_PROFILE_NAME")."<br>";

	if (strlen($strFroogleError)>0)
	{
		$STEP = 1;
	}
}

echo ShowError($strFroogleError);

if ($STEP==1)
{
	if (CModule::IncludeModule("iblock"))
	{
		// Get IBlock list
		?>
		<form method="post" action="<?echo $APPLICATION->GetCurPage() ?>" name="froogle_setup_form" id="froogle_setup_form">
		<?=bitrix_sessid_post()?>
		<?if ($ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY')
		{
			?><input type="hidden" name="PROFILE_ID" value="<? echo intval($PROFILE_ID); ?>"><?
		}
		?>
		<script type="text/javascript">
		var TreeSelected = new Array();
		<?
		$intCountSelected = 0;
		if (isset($V) && is_array($V) && !empty($V))
		{
			foreach ($V as $oneKey)
			{
				?>TreeSelected[<? echo $intCountSelected ?>] = <? echo intval($oneKey); ?>;
			<?
			$intCountSelected++;
			}
		}
		?>
		function ClearSelected()
		{
			TreeSelected = new Array();
		}
		</script>
		<table width="100%">
			<tr>
				<td width="0%" valign="top">
					<font class="text" style="font-size: 20px;">1.&nbsp;&nbsp;&nbsp;</font>
				</td>
				<td width="100%" valign="top">
						<font class="text"><b><? echo GetMessage('CET_SELECT_IBLOCK_EXT'); ?></b></font><br>
					<? echo GetIBlockDropDownListEx(
								$IBLOCK_ID, 'IBLOCK_TYPE_ID', 'IBLOCK_ID',
								array('CHECK_PERMISSIONS' => 'Y','MIN_PERMISSION' => 'W'),
								"ClearSelected(); document.getElementById('ifr').src='/bitrix/tools/catalog_export/froogle_util.php?IBLOCK_ID=0&'+'".bitrix_sessid_get()."';",
								"ClearSelected(); document.getElementById('ifr').src='/bitrix/tools/catalog_export/froogle_util.php?IBLOCK_ID='+this[this.selectedIndex].value+'&'+'".bitrix_sessid_get()."';",
								'class="adm-detail-iblock-types"',
								'class="adm-detail-iblock-list"'
							); ?>
					<br><br>
				</td>
			</tr>

			<tr>
				<td width="0%" valign="top">
					<font class="text" style="font-size: 20px;">2.&nbsp;&nbsp;&nbsp;</font>
				</td>
				<td width="100%" valign="top">
					<?// Get sections list ?>
					<font class="text"><b><?echo GetMessage("CET_SELECT_GROUP");?></b></font><br>
					<table border="0">
					<tr>
						<td><div id="tree"></div></td>
					</tr>
					</table>

					<script type="text/javascript">
						clevel = 0;

						function buildNoMenu()
						{
							var buffer;
							buffer  = '<br><table border="0" cellspacing="0" cellpadding="0">';
							buffer += '<tr><td><font class="text" style="color: #A9A9A9;"><?echo CUtil::JSEscape(GetMessage("CET_FIRST_SELECT_IBLOCK"));?></font></td></tr>';
							buffer += '</table>';
							document.getElementById('tree').innerHTML = buffer;
						}

						function buildMenu()
						{
							var i;
							var buffer;
							var imgSpace;

							buffer = '<br><table border="0" cellspacing="0" cellpadding="0">';
							buffer += '<tr>';
							buffer += '<td colspan="2" valign="top" align="left"><input type="checkbox" name="V[]" value="0" id="v0"'+(BX.util.in_array(0,TreeSelected) ? ' checked' : '')+'><label for="v0"><font class="text"><b><?echo CUtil::JSEscape(GetMessage("CET_ALL_GROUPS"));?></b></font></label></td>';
							buffer += '</tr>';

							for (i in Tree[0])
							{
								if (!Tree[i])
								{
									space = '<input type="checkbox" name="V[]" value="'+i+'" id="V'+i+'"'+(BX.util.in_array(i,TreeSelected) ? ' checked' : '')+'><label for="V'+i+'"><font class="text">' + Tree[0][i][0] + '</font></label>';
									imgSpace = '';
								}
								else
								{
									space = '<input type="checkbox" name="V[]" value="'+i+'"'+(BX.util.in_array(i,TreeSelected) ? ' checked' : '')+'><a href="javascript: collapse(' + i + ')"><font class="text"><b>' + Tree[0][i][0] + '</b></font></a>';
									imgSpace = '<img src="/bitrix/images/catalog/load/plus.gif" width="13" height="13" id="img_' + i + '" OnClick="collapse(' + i + ')">';
								}

								buffer += '<tr>';
								buffer += '<td width="20" valign="top" align="center">' + imgSpace + '</td>';
								buffer += '<td id="node_' + i + '">' + space + '</td>';
								buffer += '</tr>';
							}

							buffer += '</table>';

							document.getElementById('tree').innerHTML = buffer;
							BX.adminPanel.modifyFormElements('froogle_setup_form');
						}

						function collapse(node)
						{
							if (document.getElementById('table_' + node) == null)
							{
								var i;
								var buffer;
								var imgSpace;

								buffer = '<table border="0" id="table_' + node + '" cellspacing="0" cellpadding="0">';

								for (i in Tree[node])
								{
									if (!Tree[i])
									{
										space = '<input type="checkbox" name="V[]" value="'+i+'" id="V'+i+'"'+(BX.util.in_array(i,TreeSelected) ? ' checked' : '')+'><label for="V'+i+'"><font class="text">' + Tree[node][i][0] + '</font></label>';
										imgSpace = '';
									}
									else
									{
										space = '<input type="checkbox" name="V[]" value="'+i+'"'+(BX.util.in_array(i,TreeSelected) ? ' checked' : '')+'><a href="javascript: collapse(' + i + ')"><font class="text"><b>' + Tree[node][i][0] + '</b></font></a>';
										imgSpace = '<img src="/bitrix/images/catalog/load/plus.gif" width="13" height="13" id="img_' + i + '" OnClick="collapse(' + i + ')">';
									}

									buffer += '<tr>';
									buffer += '<td width="20" align="center" valign="top">' + imgSpace + '</td>';
									buffer += '<td id="node_' + i + '">' + space + '</td>';
									buffer += '</tr>';
								}

								buffer += '</table>';

								document.getElementById('node_' + node).innerHTML += buffer;
								document.getElementById('img_' + node).src = '/bitrix/images/catalog/load/minus.gif';
							}
							else
							{
								var tbl = document.getElementById('table_' + node);
								tbl.parentNode.removeChild(tbl);
								document.getElementById('img_' + node).src = '/bitrix/images/catalog/load/plus.gif';
							}
							BX.adminPanel.modifyFormElements('froogle_setup_form');
						}

						function changeStyle(node,action)
						{
							docStyle = document.getElementById('node_' + node).style;

							with (docStyle)
							{
								if (action == 'to')
								{
									backgroundColor= '#e5e5e5';
								}
								else
								{
									backgroundColor = '#ffffff';
								}
							}
						}

					//-->
					</script>

					<iframe src="/bitrix/tools/catalog_export/froogle_util.php?IBLOCK_ID=<?=intval($IBLOCK_ID)?>&<? echo bitrix_sessid_get(); ?>" id="ifr" name="ifr" style="display:none"></iframe>
					<br><br>
				</td>
			</tr>

			<tr>
				<td width="0%" valign="top">
					<font class="text" style="font-size: 20px;">3.&nbsp;&nbsp;&nbsp;</font>
				</td>
				<td width="100%" valign="top">
					<font class="text"><b><?echo GetMessage("CET_SAVE_FILENAME");?></b> <input type="text" name="SETUP_FILE_NAME" value="<?echo htmlspecialcharsbx(strlen($SETUP_FILE_NAME)>0 ? $SETUP_FILE_NAME : (COption::GetOptionString("catalog", "export_default_path", "/upload/"))."froogle_".mt_rand(0, 999999).".txt"); ?>" size="50">
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
						<font class="text"><b>
						<?echo GetMessage("CET_PROFILE_NAME");?> <input type="text" name="SETUP_PROFILE_NAME" value="<? echo (strlen($SETUP_PROFILE_NAME) > 0 ? htmlspecialcharsbx($SETUP_PROFILE_NAME) : ''); ?>" size="30">
						</b></font>
						<br><br>
					</td>
				</tr>
			<?endif;?>

			<tr>
				<td width="0%" valign="top">

				</td>
				<td width="100%" valign="top">
					<input type="hidden" name="lang" value="<?echo LANGUAGE_ID ?>">
					<input type="hidden" name="ACT_FILE" value="<?echo htmlspecialcharsbx($_REQUEST["ACT_FILE"]) ?>">
					<input type="hidden" name="ACTION" value="<?echo htmlspecialcharsbx($ACTION) ?>">
					<input type="hidden" name="STEP" value="<?echo intval($STEP) + 1 ?>">
					<input type="hidden" name="SETUP_FIELDS_LIST" value="V,IBLOCK_ID,SETUP_FILE_NAME">
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
	$FINITE = True;
}
?>