<?
$module_id = 'asd.advmini';
$POST_RIGHT = $APPLICATION->GetGroupRight('main');

if (!function_exists('htmlspecialcharsbx')) {
	function htmlspecialcharsbx($string, $flags=ENT_COMPAT) {
		return htmlspecialchars($string, $flags, (defined('BX_UTF')? 'UTF-8' : 'ISO-8859-1'));
	}
}

if ($POST_RIGHT >= 'R'):

	IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/options.php');
	IncludeModuleLangFile(__FILE__);

	$arSites = array();
	$rsSite = CSite::GetList($by='sort', $order='asc');
	while ($arSite = $rsSite->Fetch()) {
		$arSites[$arSite['ID']] = $arSite;
	}

	$arIBblocks = array();
	if (CModule::IncludeModule('iblock')) {

		$arIBtypes = array();
		$rsIBtype = CIBlockType::GetList();
		while($arIBtype = $rsIBtype->GetNext()) {
			if ($arIBTypeLang = CIBlockType::GetByIDLang($arIBtype['ID'], LANG)) {
				$arIBtypes[$arIBTypeLang['IBLOCK_TYPE_ID']] = array('NAME' => $arIBTypeLang['NAME'], 'SORT' => $arIBTypeLang['SORT']);
			}
		}

		$rsIB = CIBlock::GetList(array('sort' => 'asc'));
		while ($arIB = $rsIB->GetNext(true, false)) {
			if (!isset($arIBblocks[$arIB['IBLOCK_TYPE_ID']])) {
				$arIBblocks[$arIB['IBLOCK_TYPE_ID']] = array(
															'ID' => $arIB['IBLOCK_TYPE_ID'],
															'NAME' => $arIBtypes[$arIB['IBLOCK_TYPE_ID']]['NAME'],
															'SORT' => $arIBtypes[$arIB['IBLOCK_TYPE_ID']]['SORT'],
															'ITEMS' => array());
			}
			$arIBblocks[$arIB['IBLOCK_TYPE_ID']]['ITEMS'][] = array('ID' => $arIB['ID'], 'NAME' => $arIB['NAME']);
		}

		usort($arIBblocks, create_function('$a, $b', 'if ($a[\'SORT\'] == $b[\'SORT\']) return 0; return ($a[\'SORT\'] < $b[\'SORT\']) ? -1 : 1;'));
	}

	$arAllOptions = array();
	foreach ($arSites as $siteID => $arSite) {
		$arAllOptions[] = array('iblock_id_'.$siteID, GetMessage('OPT_IBLOCK_ID').' ['.$siteID.']:', array('selectboxtree', $arGroups));
	}

	$tabControl = new CAdmintabControl('tabControl', array(
									array('DIV' => 'edit1', 'TAB' => GetMessage('MAIN_TAB_SET'), 'ICON' => ''),
									array('DIV' => 'edit2', 'TAB' => GetMessage('MAIN_TAB_NEW_IB'), 'ICON' => ''),
									));

	if (ToUpper($REQUEST_METHOD) == 'POST' &&
		strlen($Update.$Apply.$RestoreDefaults)>0 &&
		($POST_RIGHT=='W' || $POST_RIGHT=='X') &&
		check_bitrix_sessid())
	{
		$bRedirect = true;

		if (strlen($new_ib_type) && CModule::IncludeModule('iblock')) {
			$ib = new CIBlock;
			$newIB = $ib->Add(array(
				'NAME' => GetMessage('OPT_IBLOCK_NEW_NAME'),
				'SITE_ID' => $new_ib_site,
				'IBLOCK_TYPE_ID' => $new_ib_type,
				'GROUP_ID' => array(
					'2' => 'R'
				)
			));
			if ($newIB) {
				$ibp = new CIBlockProperty;
				$PROP_LINK = $ibp->Add(array('NAME' => GetMessage('OPT_NEW_PROP_LINK'), 'CODE' => 'LINK', 'PROPERTY_TYPE' => 'S',
											'IBLOCK_ID' => $newIB, 'COL_COUNT' => 50, 'SORT' => 100));
				$PROP_TARGET = $ibp->Add(array('NAME' => GetMessage('OPT_NEW_PROP_TARGET'), 'CODE' => 'TARGET', 'PROPERTY_TYPE' => 'L',
											'IBLOCK_ID' => $newIB, 'LIST_TYPE' => 'C', 'SORT' => 200));
				$PROP_TYPE = $ibp->Add(array('NAME' => GetMessage('OPT_NEW_PROP_TYPE'), 'CODE' => 'TYPE', 'PROPERTY_TYPE' => 'L',
											'IBLOCK_ID' => $newIB, 'LIST_TYPE' => 'L', 'SORT' => 300, 'IS_REQUIRED' => 'Y'));
				if ($PROP_TYPE > 0) {
					foreach (array('LEFT', 'RIGHT', 'TOP', 'BOTTOM') as $type) {
						CIBlockPropertyEnum::Add(array('PROPERTY_ID' => $PROP_TYPE, 'XML_ID' => $type, 'VALUE' => GetMessage('OPT_NEW_PROP_TYPE_'.$type)));
					}
				}
				if ($PROP_TARGET > 0) {
					CIBlockPropertyEnum::Add(array('PROPERTY_ID' => $PROP_TARGET, 'VALUE' => 'Y'));
				}
				$tabs = GetMessage('OPT_IBLOCK_SERIALIZE_FORM');
				$tabs = str_replace('#PROP_TYPE#', $PROP_TYPE, $tabs);
				$tabs = str_replace('#PROP_LINK#', $PROP_LINK, $tabs);
				$tabs = str_replace('#PROP_TARGET#', $PROP_TARGET, $tabs);
				$arOptions = array(array(
					'd' => 'Y',
					'c' => 'form',
					'n' => 'form_element_'.$newIB,
					'v' => array(
						'tabs' => $tabs
					)
				));
				CUserOptions::SetOptionsFromArray($arOptions);
				COption::SetOptionString($module_id, 'iblock_id_'.$new_ib_site, $newIB);
				LocalRedirect('iblock_list_admin.php?IBLOCK_ID='.$newIB.'&type='.$new_ib_type.'&lang='.LANGUAGE_ID);
			} else {
				ShowError($ib->LAST_ERROR);
				$bRedirect = false;
			}
		}

		if (strlen($RestoreDefaults)>0) {
			COption::RemoveOption($module_id);
		} else {
			foreach ($arAllOptions as $arOption)
			{
				$name = $arOption[0];
				if ($arOption[2][0]=='text-list')
				{
					$val = '';
					for ($j=0; $j<count($$name); $j++)
						if (strlen(trim(${$name}[$j])) > 0)
							$val .= ($val <> ''? ',':'').trim(${$name}[$j]);
				}
				elseif ($arOption[2][0]=='selectbox' || $arOption[2][0]=='selectboxtree')
				{
					$val = '';
					for ($j=0; $j<count($$name); $j++)
						if (strlen(trim(${$name}[$j])) > 0)
							$val .= ($val <> ''? ',':'').trim(${$name}[$j]);
				}
				else
					$val = $$name;

				if ($arOption[2][0] == 'checkbox' && $val<>'Y')
					$val = 'N';


				COption::SetOptionString($module_id, $name, $val);
			}
		}

		$Update = $Update.$Apply;

		if ($bRedirect) {
			if (strlen($Update)>0 && strlen($_REQUEST['back_url_settings'])>0)
				LocalRedirect($_REQUEST['back_url_settings']);
			else
				LocalRedirect($APPLICATION->GetCurPage().'?mid='.urlencode($mid).'&lang='.urlencode(LANGUAGE_ID).'&back_url_settings='.urlencode($_REQUEST['back_url_settings']).'&'.$tabControl->ActiveTabParam());
		}
	}

	?><form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>"><?
	$tabControl->Begin();
	$tabControl->BeginNextTab();
	foreach($arAllOptions as $Option):
		$type = $Option[2];
		$val = COption::GetOptionString($module_id, $Option[0]);
		?>
		<tr>
			<td valign="top" width="30%"><?
				if ($type[0]=='checkbox')
					echo '<label for="'.htmlspecialcharsbx($Option[0]).'">'.$Option[1].'</label>';
				else
					echo $Option[1];
		?></td>
		<td valign="middle" width="70%"><?
			if ($type[0] == 'checkbox'):
				?><input type="checkbox" name="<?echo htmlspecialcharsbx($Option[0])?>" id="<?echo htmlspecialcharsbx($Option[0])?>" value="Y"<?if($val == 'Y')echo ' checked="checked"';?> /><?
			elseif ($type[0] == 'text'):
				?><input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialcharsbx($val)?>" name="<?echo htmlspecialcharsbx($Option[0])?>" /><?
			elseif ($type[0] == 'textarea'):
				?><textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialcharsbx($Option[0])?>"><?echo htmlspecialcharsbx($val)?></textarea><?
			elseif ($type[0] == 'text-list'):
				$aVal = explode(",", $val);
				for($j=0; $j<count($aVal); $j++):
					?><input type="text" size="<?echo $type[2]?>" value="<?echo htmlspecialcharsbx($aVal[$j])?>" name="<?echo htmlspecialcharsbx($Option[0]).'[]'?>" /><br /><?
				endfor;
				for($j=0; $j<$type[1]; $j++):
					?><input type="text" size="<?echo $type[2]?>" value="" name="<?echo htmlspecialcharsbx($Option[0]).'[]'?>" /><br /><?
				endfor;
			elseif ($type[0]=="selectbox"):
				$arr = $type[1];
				$arr_keys = array_keys($arr);
				$arVal = explode(",", $val);
				?><select name="<?echo htmlspecialcharsbx($Option[0])?>[]"<?= $type[2]?>><?
					for($j=0; $j<count($arr_keys); $j++):
						?><option value="<?echo $arr_keys[$j]?>"<?if(in_array($arr_keys[$j], $arVal))echo ' selected="selected"'?>><?echo htmlspecialcharsbx($arr[$arr_keys[$j]])?></option><?
					endfor;
					?></select><?
			elseif ($type[0]=="selectboxtree"):
				$arr = $type[1];
				$arr_keys = array_keys($arr);
				$arVal = explode(',', $val);

				$s = '<select name="'.htmlspecialchars($Option[0]).'[]"'.$type[2].'>';
				$s .= '<option value=""></option>';
				foreach ($arIBblocks as $arType) {
					$strIBlocksCpGr = '';
					foreach ($arType['ITEMS'] as $arIB) {
						if (in_array($arIB['ID'], $arVal)) {
							$sel = ' selected="selected"';
						} else {
							$sel = '';
						}
						$strIBlocksCpGr .= '<option value="'.$arIB['ID'].'"'.$sel.'>'.$arIB['NAME'].'</option>';
					}
					if ($strIBlocksCpGr != '') {
						$s .= '<optgroup label="'.$arType['NAME'].'">';
						$s .= $strIBlocksCpGr;
						$s .= '</optgroup>';
					}
				}
				$s .= '</select>';
				echo $s;
			endif;
	endforeach;

	$tabControl->BeginNextTab();

	?>
		<tr>
			<td width="30%"><?= GetMessage('OPT_IBLOCK_TYPE')?></td>
			<td width="70%">
				<select name="new_ib_type">
					<option value=""></option>
					<?foreach ($arIBblocks as $arType):?>
					<option value="<?= $arType['ID'];?>"><?= $arType['NAME'];?></option>
					<?endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?= GetMessage('OPT_IBLOCK_SITE')?></td>
			<td>
				<select name="new_ib_site">
					<?foreach ($arSites as $siteID => $arSite):?>
					<option value="<?= $arSite['ID'];?>">[<?= $arSite['ID'];?>] <?= $arSite['NAME'];?></option>
					<?endforeach;?>
				</select>
			</td>
		</tr>
	<?
	$tabControl->Buttons();
	?>
	<input <?if ($POST_RIGHT < 'W') echo 'disabled="disabled"' ?> type="submit" name="Update" value="<?=GetMessage('MAIN_SAVE')?>" title="<?=GetMessage('MAIN_OPT_SAVE_TITLE')?>" />
	<input <?if ($POST_RIGHT < 'W') echo 'disabled="disabled"' ?> type="submit" name="Apply" value="<?=GetMessage('MAIN_OPT_APPLY')?>" title="<?=GetMessage('MAIN_OPT_APPLY_TITLE')?>" />
	<?if (strlen($_REQUEST["back_url_settings"]) > 0):?>
		<input <?if ($POST_RIGHT < 'W') echo 'disabled="disabled"' ?> type="button" name="Cancel" value="<?=GetMessage('MAIN_OPT_CANCEL')?>" title="<?=GetMessage('MAIN_OPT_CANCEL_TITLE')?>" onclick="window.location='<?echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST['back_url_settings']))?>'" />
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>" />
	<?endif?>
	<input <?if ($POST_RIGHT < 'W') echo 'disabled="disabled"' ?> type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" onclick="confirm('<?echo AddSlashes(GetMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING'))?>')" value="<?echo GetMessage('MAIN_RESTORE_DEFAULTS')?>" />
	<?=bitrix_sessid_post();?>
	<?$tabControl->End();?>
	</form>

<?endif;?>