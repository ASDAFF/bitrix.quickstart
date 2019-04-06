<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/asd.favorite/include.php');
IncludeModuleLangFile(__FILE__);

if (!$USER->IsAdmin())
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));

$aTabs = array(
	array('DIV' => 'edit1', 'TAB' => GetMessage('asd_mod_tab_1'), 'TITLE' => GetMessage('asd_mod_tab_1_title')),
);
$tabControl = new CAdminTabControl('tabControl', $aTabs);

$error = '';
$message = null;
$bVarsFromForm = false;

if(
	ToUpper($REQUEST_METHOD)=='POST' &&
	(strlen($save) || strlen($apply)) &&
	check_bitrix_sessid()
)
{
	$NEW_CODE = trim($NEW_CODE);
	$CODE = trim($CODE);
	$NAME = trim($NAME);
	$MODULE = trim($MODULE);
	if ($MODULE == 'iblock')
		$arRef = $IBLOCK_REF;
	elseif ($MODULE == 'blog')
		$arRef = $BLOG_REF=='Y' ? array('blog') : array();

	if (!strlen($NAME))
		$error = GetMessage('asd_mod_error_required_fields');
	elseif (strlen($NEW_CODE)>0 && !preg_match('/^[a-z0-9]+$/is', $NEW_CODE))
		$error = GetMessage('asd_mod_error_bad_code');
	elseif (strlen($NEW_CODE)>0 && CASDfavorite::GetType($NEW_CODE)->Fetch())
		$error = GetMessage('asd_mod_error_code_exist');

	if (!strlen($error))
	{
		if (strlen($NEW_CODE))
			CASDfavorite::AddType(array('CODE' => $NEW_CODE, 'MODULE' => $MODULE, 'NAME' => $NAME, 'REF' => $arRef));
		elseif (strlen($CODE))
			CASDfavorite::UpdateType($CODE, array('NAME' => $NAME, 'REF' => $arRef));
		if (strlen($save))
			LocalRedirect('asd_fav_types_list.php?lang='.LANG);
		else
			LocalRedirect('asd_fav_types_edit.php?CODE='.(strlen($NEW_CODE) ? $NEW_CODE : $CODE).'&lang='.LANG);
	}
	else
	{
		$message = new CAdminMessage(array('TYPE' => 'ERROR', 'MESSAGE' => $error));
		$bVarsFromForm = true;
	}
}

if (strlen($CODE)>0 && $_REQUEST['action']=='delete' && check_bitrix_sessid())
{
	CASDfavorite::DeleteType($CODE);
	LocalRedirect('asd_fav_types_list.php?lang='.LANG);
}

if (strlen($CODE)>0)
	if (!CASDfavorite::GetType($CODE)->ExtractFields('str_'))
		$CODE = '';
	elseif (!$bVarsFromForm)
		${strtoupper($str_MODULE)._REF} = CASDfavorite::GetRefType($CODE);

if ($bVarsFromForm)
	$DB->InitTableVarsForEdit('b_asd_favorite_types', '', 'str_');

if (strlen($CODE)>0)
	$APPLICATION->SetTitle(GetMessage('asd_mod_title_add'));
else
	$APPLICATION->SetTitle(GetMessage('asd_mod_title_edit'));

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

$aContext = array();
$aContext[] = array(
			'TEXT'	=> GetMessage('asd_mod_but_list'),
			'TITLE'	=> GetMessage('asd_mod_but_list_title'),
			'LINK'	=> 'asd_fav_types_list.php?lang='.LANG,
			'ICON'	=> 'btn_list',
			);
if (strlen($CODE) > 0)
{
	$aContext[] = array(
				'TEXT'	=> GetMessage('asd_mod_but_new'),
				'TITLE'	=> GetMessage('asd_mod_but_new_title'),
				'LINK'	=> 'asd_fav_types_edit.php?lang='.LANG,
				'ICON'	=> 'btn_new',
				);
	$aContext[] = array(
				'TEXT'	=> GetMessage('asd_mod_but_del'),
				'TITLE'	=> GetMessage('asd_mod_but_del_title'),
				'LINK'	=> 'javascript:if(confirm(\''.GetMessage('asd_mod_confirm_delete').'\'))window.location=\'asd_fav_types_edit.php?CODE='.$CODE.'&amp;action=delete&amp;'.bitrix_sessid_get().'&amp;lang='.LANG.'\';',
				'ICON'	=> 'btn_delete',
				);
}
$context = new CAdminContextMenu($aContext);
$context->Show();

if ($message)
	echo $message->Show();
?>
<form action="<?= $APPLICATION->GetCurPage()?>" enctype="multipart/form-data" method="post">
<?
echo bitrix_sessid_post();
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<tr>
		<td width="30%"><?if (!strlen($CODE)){?><span class="required">*</span><?}?><?= GetMessage('asd_mod_label_code')?>:</td>
		<td width="70%">
			<?if (strlen($CODE) > 0):?>
				<b><?= $str_CODE?></b>
			<?else:?>
				<input type="text" name="NEW_CODE" value="<?= htmlspecialchars($_REQUEST['NEW_CODE'])?>" size="32" />
			<?endif;?>
		</td>
	</tr>
	<tr>
		<td><?if (!strlen($CODE)){?><span class="required">*</span><?}?><?= GetMessage('asd_mod_label_module')?>:</td>
		<td>
			<?if (strlen($CODE) > 0):?>
				<b><?= GetMessage('asd_mod_label_module_'.$str_MODULE)?></b>
				<input type="hidden" name="MODULE" value="<?= $str_MODULE?>" />
			<?else:?>
				<select name="MODULE">
					<option value="iblock"<?if ($str_MODULE == 'iblock'){?> selected="selected"<?}?>><?= GetMessage('asd_mod_label_module_iblock')?></option>
					<option value="blog"<?if ($str_MODULE == 'blog'){?> selected="selected"<?}?>><?= GetMessage('asd_mod_label_module_blog')?></option>
					<option value="forum"<?if ($str_MODULE == 'forum'){?> selected="selected"<?}?>><?= GetMessage('asd_mod_label_module_forum')?></option>
				</select>
			<?endif;?>
		</td>
	</tr>
	<tr>
		<td><span class="required">*</span><?= GetMessage('asd_mod_label_name')?>:</td>
		<td>
			<input type="text" name="NAME" value="<?=$str_NAME?>" size="32" />
		</td>
	</tr>
	<?
	if (strlen($CODE) && $str_MODULE=='iblock' && CModule::IncludeModule('iblock')):
		$arIBtypes = array();
		$rsIBtype = CIBlockType::GetList();
		while($arIBtype = $rsIBtype->Fetch())
			if ($arIBTypeLang = CIBlockType::GetByIDLang($arIBtype['ID'], LANG))
				$arIBtypes[$arIBTypeLang['IBLOCK_TYPE_ID']] = array('NAME' => $arIBTypeLang['NAME'], 'SORT' => $arIBTypeLang['SORT']);

		$arIBblocks = array();
		$rsIB = CIBlock::GetList(array('sort' => 'asc'));
		while ($arIB = $rsIB->GetNext(true, false))
		{
			if (!isset($arIBblocks[$arIB['IBLOCK_TYPE_ID']]))
			{
				$arIBblocks[$arIB['IBLOCK_TYPE_ID']] = array(
															'ID' => $arIB['IBLOCK_TYPE_ID'],
															'NAME' => $arIBtypes[$arIB['IBLOCK_TYPE_ID']]['NAME'],
															'SORT' => $arIBtypes[$arIB['IBLOCK_TYPE_ID']]['SORT'],
															'ITEMS' => array());
			}
			$arIBblocks[$arIB['IBLOCK_TYPE_ID']]['ITEMS'][] = array('ID' => $arIB['ID'], 'NAME' => $arIB['NAME']);
		}

		usort($arIBblocks, create_function('$a, $b', 'if ($a[\'SORT\'] == $b[\'SORT\']) return 0; return ($a[\'SORT\'] < $b[\'SORT\']) ? -1 : 1;'));
	?>
	<tr valign="top">
		<td><?= GetMessage('asd_mod_label_like_iblock')?>:</td>
		<td>
			<?
			$s = '<select name="IBLOCK_REF[]" multiple="multiple" size="10">';
			foreach ($arIBblocks as $arType)
			{
				$strIBlocksCpGr = '';
				foreach ($arType['ITEMS'] as $arIB) {
					if (is_array($IBLOCK_REF) && in_array('iblock_'.$arIB['ID'], $IBLOCK_REF))
						$sel = ' selected="selected"';
					else
						$sel = '';
					$strIBlocksCpGr .= '<option value="iblock_'.$arIB['ID'].'"'.$sel.'>'.$arIB['NAME'].'</option>';
				}
				if ($strIBlocksCpGr != '')
				{
					$s .= '<optgroup label="'.$arType['NAME'].'">';
					$s .= $strIBlocksCpGr;
					$s .= '</optgroup>';
				}
			}
			$s .= '</select>';
			echo $s;
			?>
			</select>
		</td>
	</tr>
	<?endif;?>
	<?if (strlen($CODE) && $str_MODULE=='blog'):?>
	<tr valign="top">
		<td><?= GetMessage('asd_mod_label_like_blog')?>:</td>
		<td><input type="checkbox" name="BLOG_REF" value="Y"<?if (is_array($BLOG_REF) && in_array('blog', $BLOG_REF)) {?> checked="checked"<?}?> /></td>
	</tr>
	<?endif;?>
<?
$tabControl->Buttons(
	array(
		'disabled' => false,
		'back_url' => 'asd_fav_types_list.php?lang='.LANG,
	)
);
$tabControl->End();
?>
	<input type="hidden" name="lang" value="<?=LANG?>" />
<?if (strlen($CODE) > 0):?>
	<input type="hidden" name="CODE" value="<?=$CODE?>" />
<?endif;?>
</form>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>