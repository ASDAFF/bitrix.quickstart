<?
//<title>Yandex</title>
__IncludeLang(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/lang/", "/export_setup_templ.php"));

global $APPLICATION, $USER;

ClearVars();

$strYandexError = "";

if (($ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY') && $STEP == 1)
{
	if (isset($arOldSetupVars['IBLOCK_ID']))
		$IBLOCK_ID = $arOldSetupVars['IBLOCK_ID'];
	if (isset($arOldSetupVars['SETUP_FILE_NAME']))
		$SETUP_FILE_NAME = str_replace(COption::GetOptionString("catalog", "export_default_path", "/bitrix/catalog_export/"),'',$arOldSetupVars['SETUP_FILE_NAME']);
	if (isset($arOldSetupVars['SETUP_PROFILE_NAME']))
		$SETUP_PROFILE_NAME = $arOldSetupVars['SETUP_PROFILE_NAME'];
	if (isset($arOldSetupVars['V']))
		$V = $arOldSetupVars['V'];
	if (isset($arOldSetupVars['XML_DATA']))
	{
		if (get_magic_quotes_gpc())
		{
			$XML_DATA = base64_encode(stripslashes($arOldSetupVars['XML_DATA']));
		}
		else
		{
			$XML_DATA = base64_encode($arOldSetupVars['XML_DATA']);
		}
	}
	if (isset($arOldSetupVars['SETUP_SERVER_NAME']))
		$SETUP_SERVER_NAME = $arOldSetupVars['SETUP_SERVER_NAME'];
}

if ($STEP>1)
{
	$IBLOCK_ID = intval($IBLOCK_ID);
	$rsIBlocks = CIBlock::GetByID($IBLOCK_ID);
	if ($IBLOCK_ID<=0 || !($arIBlock = $rsIBlocks->Fetch()))
	{
		$strYandexError .= GetMessage("CET_ERROR_NO_IBLOCK1")." #".$IBLOCK_ID." ".GetMessage("CET_ERROR_NO_IBLOCK2")."<br>";
	}
	else
	{
		$bRightBlock = !CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, "iblock_admin_display");
		if ($bRightBlock)
		{
			$strYandexError .= str_replace('#IBLOCK_ID#',$IBLOCK_ID,GetMessage("CET_ERROR_IBLOCK_PERM"))."<br>";
		}
	}

	if (strlen($SETUP_FILE_NAME)<=0)
	{
		$strYandexError .= GetMessage("CET_ERROR_NO_FILENAME")."<br>";
	}
	elseif (preg_match(BX_CATALOG_FILENAME_REG,$SETUP_FILE_NAME))
	{
		$strYandexError .= GetMessage("CES_ERROR_BAD_EXPORT_FILENAME")."<br>";
	}
	elseif ($APPLICATION->GetFileAccessPermission($SETUP_FILE_NAME) < "W")
	{
		$strYandexError .= str_replace("#FILE#", $SETUP_FILE_NAME, GetMessage('CET_YAND_RUN_ERR_SETUP_FILE_ACCESS_DENIED'))."<br>";
	}

	$SETUP_SERVER_NAME = trim($SETUP_SERVER_NAME);

	if (strlen($strYandexError)<=0)
	{
		$bAllSections = false;
		$arSections = array();
		if (is_array($V) && !empty($V))
		{
			foreach ($V as $key => $value)
			{
				if (trim($value)=="0")
				{
					$bAllSections = true;
					break;
				}
				$value = intval($value);
				if ($value>0)
				{
					$arSections[] = $value;
				}
			}
		}

		if (!$bAllSections && !empty($arSections))
		{
			$arCheckSections = array();
			$rsSections = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $IBLOCK_ID, 'ID' => $arSections), false, array('ID'));
			while ($arOneSection = $rsSections->Fetch())
			{
				$arCheckSections[] = $arOneSection['ID'];
			}
			$arSections = $arCheckSections;
		}

		if (!$bAllSections && empty($arSections))
		{
			$strYandexError .= GetMessage("CET_ERROR_NO_GROUPS")."<br>";
			$V = array();
		}
	}

	if (is_array($V))
	{
		$V = array_unique(array_values($V));
		$_REQUEST['V'] = $V;
	}

	$arCatalog = CCatalog::GetByIDExt($IBLOCK_ID);
	if ('P' == $arCatalog['CATALOG_TYPE'] || 'X' == $arCatalog['CATALOG_TYPE'])
	{
		if (strlen($XML_DATA) <= 0)
		{
			$strYandexError .= GetMessage('YANDEX_ERR_SKU_SETTINGS_ABSENT').'<br>';
		}
	}

	if (($ACTION=="EXPORT_SETUP" || $ACTION=="EXPORT_EDIT" || $ACTION=="EXPORT_COPY") && strlen($SETUP_PROFILE_NAME)<=0)
		$strYandexError .= GetMessage("CET_ERROR_NO_PROFILE_NAME")."<br>";

	if (strlen($strYandexError)>0)
	{
		$STEP = 1;
	}
}

echo ShowError($strYandexError);

if ($STEP==1)
{
	if (CModule::IncludeModule("iblock"))
	{
		?>
		<form method="post" action="<?echo $APPLICATION->GetCurPage() ?>" name="yandex_setup_form" id="yandex_setup_form">
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
		<?
		if ($intCountSelected)
		{
			foreach ($V as &$oneKey)
			{

				?><input type="hidden" value="<? echo intval($oneKey); ?>" name="V[]" id="oldV<? echo intval($oneKey); ?>"><?
			}
		}
		?>
		<table width="100%" class="edit-table">
			<tr>
				<td width="100%" valign="top"><font class="text"><b><? echo GetMessage('CET_SELECT_IBLOCK_EXT'); ?></b></font><br>
					<? echo GetIBlockDropDownListEx(
								$IBLOCK_ID, 'IBLOCK_TYPE_ID', 'IBLOCK_ID',
								array('CHECK_PERMISSIONS' => 'Y','MIN_PERMISSION' => 'W'),
								"ClearSelected(); document.getElementById('id_ifr').src='/bitrix/tools/catalog_export/yandex_util.php?IBLOCK_ID=0&'+'".bitrix_sessid_get()."';",
								"ClearSelected(); document.getElementById('id_ifr').src='/bitrix/tools/catalog_export/yandex_util.php?IBLOCK_ID='+this[this.selectedIndex].value+'&'+'".bitrix_sessid_get()."';",
								'class="adm-detail-iblock-types"',
								'class="adm-detail-iblock-list"'
							); ?>
					<br><br>
				</td>
			</tr>

			<tr>
				<td width="100%" valign="top">
					<font class="text"><b><?echo GetMessage("CET_SELECT_GROUP");?></b></font><br>
					<table border="0">
					<tr>
						<td><div id="tree"></div></td>
					</tr>
					</table>

					<script type="text/javascript">

						clevel = 0;

						function delOldV(obj)
						{
							if (obj)
							{
								if (obj.checked == false)
								{
									var objOldVal = BX('oldV'+obj.value);
									if (objOldVal)
									{
										objOldVal.parentNode.removeChild(objOldVal);
										objOldVal = null;
									}
								}
							}
						}

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
							buffer += '<td colspan="2" valign="top" align="left"><input type="checkbox" name="V[]" value="0" id="v0"'+(BX.util.in_array(0,TreeSelected) ? ' checked' : '')+' onclick="delOldV(this);"><label for="v0"><font class="text"><b><?echo CUtil::JSEscape(GetMessage("CET_ALL_GROUPS"));?></b></font></label></td>';
							buffer += '</tr>';

							for (i in Tree[0])
							{
								if (!Tree[i])
								{
									space = '<input type="checkbox" name="V[]" value="'+i+'" id="V'+i+'"'+(BX.util.in_array(i,TreeSelected) ? ' checked' : '')+' onclick="delOldV(this);"><label for="V'+i+'"><font class="text">' + Tree[0][i][0] + '</font></label>';
									imgSpace = '';
								}
								else
								{
									space = '<input type="checkbox" name="V[]" value="'+i+'"'+(BX.util.in_array(i,TreeSelected) ? ' checked' : '')+' onclick="delOldV(this);"><a href="javascript: collapse(' + i + ')"><font class="text"><b>' + Tree[0][i][0] + '</b></font></a>';
									imgSpace = '<img src="/bitrix/images/catalog/load/plus.gif" width="13" height="13" id="img_' + i + '" OnClick="collapse(' + i + ')">';
								}

								buffer += '<tr>';
								buffer += '<td width="20" valign="top" align="center">' + imgSpace + '</td>';
								buffer += '<td id="node_' + i + '">' + space + '</td>';
								buffer += '</tr>';
							}

							buffer += '</table>';

							document.getElementById('tree').innerHTML = buffer;
							BX.adminPanel.modifyFormElements('yandex_setup_form');
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
										space = '<input type="checkbox" name="V[]" value="'+i+'" id="V'+i+'"'+(BX.util.in_array(i,TreeSelected) ? ' checked' : '')+' onclick="delOldV(this);"><label for="V'+i+'"><font class="text">' + Tree[node][i][0] + '</font></label>';
										imgSpace = '';
									}
									else
									{
										space = '<input type="checkbox" name="V[]" value="'+i+'"'+(BX.util.in_array(i,TreeSelected) ? ' checked' : '')+' onclick="delOldV(this);"><a href="javascript: collapse(' + i + ')"><font class="text"><b>' + Tree[node][i][0] + '</b></font></a>';
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
							BX.adminPanel.modifyFormElements('yandex_setup_form');
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
					</script>

					<iframe src="/bitrix/tools/catalog_export/yandex_util.php?IBLOCK_ID=<?=intval($IBLOCK_ID)?>&<? echo bitrix_sessid_get(); ?>" id="id_ifr" name="ifr" style="display:none"></iframe>
					<br><br>
				</td>
			</tr>

			<tr>
				<td width="100%" valign="top">
<script type="text/javascript">
function showDetailPopup()
{
	if (null == obDetailWindow)
	{
		var s = BX('IBLOCK_ID');
		var dat = BX('XML_DATA');
		var obDetailWindow = new BX.CAdminDialog({
			'content_url': '/bitrix/tools/catalog_export/yandex_detail.php?lang=<?=LANGUAGE_ID?>&bxpublic=Y&IBLOCK_ID=' + s[s.selectedIndex].value,
			'content_post': 'XML_DATA='+BX.util.urlencode(dat.value)+'&'+'<?echo bitrix_sessid_get(); ?>',
			'width': 900, 'height': 550,
			'resizable': true
		});
		obDetailWindow.Show();
	}
}

function setDetailData(data)
{
	BX('XML_DATA').value = data;
}
</script>
					<font class="text"><b><?=GetMessage('CAT_DETAIL_PROPS')?>:</b> </font>
					<button onclick="showDetailPopup(); return false;"><?=GetMessage('CAT_DETAIL_PROPS_RUN')?></button>
					<input type="hidden" id="XML_DATA" name="XML_DATA" value="<? echo (strlen($XML_DATA) > 0 ? $XML_DATA : ''); ?>" />
				</td>
			</td>

			<tr>
				<td width="100%" valign="top">
					<font class="text"><b>
					<?echo GetMessage("CET_SERVER_NAME");?> <input type="text" name="SETUP_SERVER_NAME" value="<?echo (strlen($SETUP_SERVER_NAME)>0) ? htmlspecialcharsbx($SETUP_SERVER_NAME) : '' ?>" size="50" /> <input type="button" onclick="this.form['SETUP_SERVER_NAME'].value = window.location.host;" value="<?echo htmlspecialcharsbx(GetMessage('CET_SERVER_NAME_SET_CURRENT'))?>" />
					</b></font>
					<br><br>
				</td>
			</tr>
			<tr>
				<td width="100%" valign="top">
					<font class="text"><b>
					<?echo GetMessage("CET_SAVE_FILENAME");?> <b><? echo htmlspecialcharsbx(COption::GetOptionString("catalog", "export_default_path", "/bitrix/catalog_export/"));?></b><input type="text" name="SETUP_FILE_NAME" value="<?echo (strlen($SETUP_FILE_NAME)>0) ? htmlspecialcharsbx($SETUP_FILE_NAME) : "yandex_".mt_rand(0, 999999).".php" ?>" size="50" />
					</b></font>
					<br><br>
				</td>
			</tr>

			<?if ($ACTION=="EXPORT_SETUP" || $ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY'):?>
				<tr>
					<td width="100%" valign="top">
						<font class="text"><b>
						<?echo GetMessage("CET_PROFILE_NAME");?> <input type="text" name="SETUP_PROFILE_NAME" value="<?echo htmlspecialcharsbx($SETUP_PROFILE_NAME) ?>" size="30">
						</b></font>
						<br><br>
					</td>
				</tr>
			<?endif;?>

			<tr>
				<td width="100%" valign="top">
					<input type="hidden" name="lang" value="<?echo LANGUAGE_ID ?>">
					<input type="hidden" name="ACT_FILE" value="<?echo htmlspecialcharsbx($_REQUEST["ACT_FILE"]) ?>">
					<input type="hidden" name="ACTION" value="<?echo htmlspecialcharsbx($ACTION) ?>">
					<input type="hidden" name="STEP" value="<?echo intval($STEP) + 1 ?>">
					<input type="hidden" name="SETUP_FIELDS_LIST" value="V,IBLOCK_ID,SETUP_SERVER_NAME,SETUP_FILE_NAME,XML_DATA">
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
	$SETUP_FILE_NAME = COption::GetOptionString("catalog", "export_default_path", "/bitrix/catalog_export/").$SETUP_FILE_NAME;
	if (strlen($XML_DATA) > 0)
	{
		$XML_DATA = base64_decode($XML_DATA);
	}
	$SETUP_SERVER_NAME = htmlspecialcharsbx($SETUP_SERVER_NAME);
	$_POST['SETUP_SERVER_NAME'] = htmlspecialcharsbx($_POST['SETUP_SERVER_NAME']);
	$_REQUEST['SETUP_SERVER_NAME'] = htmlspecialcharsbx($_REQUEST['SETUP_SERVER_NAME']);

	$FINITE = true;
}
?>