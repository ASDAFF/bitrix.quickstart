<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/asd.mailtpl/include.php');
IncludeModuleLangFile(__FILE__);

if (!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings')) {
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}

$isAdmin = $USER->CanDoOperation('edit_other_settings');

$aTabs = array(
	array('DIV' => 'edit1', 'TAB' => GetMessage('ASD_MAILTPL_ELEMENTY')),
);
$tabControl = new CAdminTabControl('tabControl', $aTabs);

$error = '';
$message = null;
$bVarsFromForm = false;

if (
	ToUpper($REQUEST_METHOD)=='POST' &&
	(strlen($save) || strlen($apply)) &&
	$isAdmin && check_bitrix_sessid()
) {
	if (!strlen(trim($NAME)) || !strlen(trim($TYPE))) {
		$error = GetMessage('ASD_MAILTPL_NE_ZAPOLNENY_VSE_OBA');
	}

	if (!strlen($error)) {
		$arFields = array(
							'NAME' => $NAME,
							'TYPE' => $TYPE,
							'HEADER' => $HEADER,
							'FOOTER' => $FOOTER,
							'EVENTS' => $EVENTS,
							'SETTINGS' => $SETTINGS);
		if ($ID > 0) {
			$res = CASDMailTplDB::Update($ID, $arFields);
		} else {
			$ID = $res = CASDMailTplDB::Add($arFields);
		}
		if ($res) {
			if (strlen($save)) {
				LocalRedirect('asd_mailtpl_list.php?lang='.LANG);
			} else {
				LocalRedirect('asd_mailtpl_edit.php?ID='.$ID.'&lang='.LANG);
			}
		} else {
			$error = GetMessage('ASD_MAILTPL_OSIBKA_SOHRANENIA');
		}
	}
	if (strlen($error)) {
		$message = new CAdminMessage(array('TYPE' => 'ERROR', 'MESSAGE' => $error));
		$bVarsFromForm = true;
	}
}

if (strlen($ID)>0 && $_REQUEST['action']=='delete' && check_bitrix_sessid() && $isAdmin) {
	CASDMailTplDB::Delete($ID);
	LocalRedirect('asd_mailtpl_list.php?lang='.LANG);
}

if ($ID > 0) {
	if (!CASDMailTplDB::GetByID($ID)->ExtractFields('str_')) {
		$ID = 0;
	} else {
		$SETTINGS = unserialize(htmlspecialcharsback($str_SETTINGS));
	}
}

if ($ID > 0) {
	$arEvents = CASDMailTplDB::GetEvents($ID);
} else {
	$arEvents = array();
}

if ($bVarsFromForm) {
	$arEvents = $EVENTS;
	$DB->InitTableVarsForEdit('b_asd_mailtpl', '', 'str_');
}

if ($ID > 0) {
	$APPLICATION->SetTitle(GetMessage('ASD_MAILTPL_IZMENENIE'));
} else {
	$APPLICATION->SetTitle(GetMessage('ASD_MAILTPL_DOBAVLENIE'));
}

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

$aContext = array();
$aContext[] = array(
			'TEXT'	=> GetMessage('ASD_MAILTPL_SPISOK'),
			'TITLE'	=> '',
			'LINK'	=> 'asd_mailtpl_list.php?lang='.LANG,
			'ICON'	=> 'btn_list',
			);
if ($ID > 0) {
	$aContext[] = array(
				'TEXT'	=> GetMessage('ASD_MAILTPL_DOBAVITQ'),
				'TITLE'	=> '',
				'LINK'	=> 'asd_mailtpl_edit.php?lang='.LANG,
				'ICON'	=> 'btn_new',
				);
	if ($isAdmin) {
		$aContext[] = array(
					'TEXT'	=> GetMessage('ASD_MAILTPL_UDALITQ'),
					'TITLE'	=> '',
					'LINK'	=> 'javascript:if(confirm(\''.GetMessage('ASD_MAILTPL_VY_DEYSTVITELQNO_HOT').'\'))window.location=\'asd_mailtpl_edit.php?ID='.$ID.'&amp;action=delete&amp;'.bitrix_sessid_get().'&amp;lang='.LANG.'\';',
					'ICON'	=> 'btn_delete',
					);
	}
}
$context = new CAdminContextMenu($aContext);
$context->Show();

if ($message) {
	echo $message->Show();
}
?>
<form action="<?= $APPLICATION->GetCurPage()?>" enctype="multipart/form-data" method="post">
<?
echo bitrix_sessid_post();
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<tr>
		<td widh="35%"><span class="required">*</span><?=GetMessage('ASD_MAILTPL_NAZVANIE')?></td>
		<td width="65%"><input type="text" name="NAME" value="<?=$str_NAME?>" size="32" /></td>
	</tr>
	<tr>
		<td><span class="required">*</span><?=GetMessage('ASD_MAILTPL_TIP')?></td>
		<td>
			<select name="TYPE">
				<option value="text">text</option>
				<option value="html"<?if ($str_TYPE == 'html'){?> selected="selected"<?}?>>html</option>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<td><span class="required">*</span><?=GetMessage('ASD_MAILTPL_DLA_TIPOV_SOBYTIY')?></td>
		<td>
			<select name="EVENTS[]" size="10" multiple="multiple">
				<?foreach (CASDMailTpl::GetAllEventTypes() as $event => $name):?>
				<option value="<?= $event?>"<?if (in_array($event, $arEvents)){?> selected="selected"<?}?>><?= $name?></option>
				<?endforeach;?>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('ASD_MAILTPL_SAPKA')?></td>
		<td>
			<textarea name="HEADER" rows="15" cols="75" style="width: 100%;"><?= $str_HEADER?></textarea>
		</td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('ASD_MAILTPL_PODVAL')?></td>
		<td>
			<textarea name="FOOTER" rows="15" cols="75" style="width: 100%;"><?= $str_FOOTER?></textarea>
		</td>
	</tr>
	<tr valign="top" class="heading">
		<td colspan="2"><?=GetMessage('ASD_MAILTPL_SETTINGS')?></td>
	</tr>
	<tr>
		<td widh="30%"><?=GetMessage('ASD_MAILTPL_STYLE_P')?></td>
		<td width="70%"><input type="text" name="SETTINGS[STYLE_P]" size="50" value="<?= htmlspecialchars($SETTINGS['STYLE_P'])?>" /></td>
	</tr>
	<tr>
		<td widh="30%"><?=GetMessage('ASD_MAILTPL_STYLE_SPAN')?></td>
		<td width="70%"><input type="text" name="SETTINGS[STYLE_SPAN]" size="50" value="<?= htmlspecialchars($SETTINGS['STYLE_SPAN'])?>" /></td>
	</tr>
<?
$tabControl->Buttons(
	array(
		'disabled' => !$isAdmin,
		'back_url' => 'asd_mailtpl_list.php?lang='.LANG,
	)
);
$tabControl->End();
?>
	<input type="hidden" name="lang" value="<?=LANG?>" />
<?if ($ID > 0):?>
	<input type="hidden" name="ID" value="<?=$ID?>" />
<?endif;?>
</form>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');