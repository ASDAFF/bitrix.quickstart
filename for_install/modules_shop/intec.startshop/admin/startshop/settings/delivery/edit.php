<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");?>
<?
	global $USER, $APPLICATION;
	IncludeModuleLangFile(__FILE__);

	if (!CModule::IncludeModule("iblock"))
		return;

	if (!CModule::IncludeModule("intec.startshop"))
		return;

	$bRightsView = CStartShopUtilsRights::AllowedForGroups(
		$USER->GetUserGroupArray(),
		'STARTSHOP_SETTINGS_DELIVERY',
		'V'
	) || $USER->IsAdmin();

	$bRightsEdit = CStartShopUtilsRights::AllowedForGroups(
		$USER->GetUserGroupArray(),
		'STARTSHOP_SETTINGS_DELIVERY',
		'E'
	) || $USER->IsAdmin();

	if (!$bRightsEdit || !$bRightsView) {
		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
		die();
	}

	require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/intec.startshop/web/include.php');

	$arLinks = array(
		'ADD' => "/bitrix/admin/startshop_settings_delivery_edit.php?lang=".LANG."&action=add",
		'EDIT' => "/bitrix/admin/startshop_settings_delivery_edit.php?lang=".LANG."&action=edit&ID=#ID#",
		'BACK' => "/bitrix/admin/startshop_settings_delivery.php?lang=".LANG
	);

	$arItem = array();

	$bActionSave = !empty($_REQUEST['save']);
	$bActionApply = !empty($_REQUEST['apply']);

	$arActions = array('add', 'edit');
	$sAction = $_REQUEST['action'];

	$sError = null;
	$sNotify = null;

	if (!in_array($sAction, $arActions)) {
		LocalRedirect($arLinks['BACK']);
		die();
	}

	if (!is_numeric($_REQUEST['SORT']) || !isset($_REQUEST['SORT']))
		$_REQUEST['SORT'] = 500;

	$arValues = array();
	$arValues['CODE'] = strval($_REQUEST['CODE']);
	$arValues['ACTIVE'] = $_REQUEST['ACTIVE'] == "Y" ? "Y" : "N";
	$arValues['SID'] = strval($_REQUEST['SID']);
	$arValues['PRICE'] = floatval($_REQUEST['PRICE']);
	$arValues['SORT'] = intval($_REQUEST['SORT']);
	$arValues['PROPERTIES'] = $_REQUEST['PROPERTIES'];
	$arValues['LANG'] = array();

	$arLanguages = array();
	$dbLanguages = CLanguage::GetList($by = "lid", $order = "asc");

	while ($arLanguage = $dbLanguages->Fetch()) {
		$arLanguages[] = $arLanguage;
		$arValues['LANG'][$arLanguage['LID']]['NAME'] = $_REQUEST['LANG_'.$arLanguage['LID'].'_NAME'];
	}

	if ($sAction == 'add') {
		$APPLICATION->SetTitle(GetMessage('title.add'));

		if ($bActionSave || $bActionApply)
			if (!empty($arValues['CODE']) && !empty($arValues['SID'])) {
				$iItemID = CStartShopDelivery::Add($arValues);

				if ($iItemID) {
					if ($bActionSave) LocalRedirect($arLinks['BACK']);
					if ($bActionApply) LocalRedirect(CStartShopUtil::ReplaceMacros($arLinks['EDIT'], array("ID" => $iItemID)).'&ADDED=Y');
					die();
				}

				$sError = GetMessage('messages.warning.exists');
			} else {
				$arFields = array();

				if (empty($arValues['CODE'])) $arFields[] = GetMessage('fields.code');
				if (empty($arValues['SID'])) $arFields[] = GetMessage('fields.site');

				$sError = GetMessage('messages.warning.empty_fields', array(
					'#FIELDS#' => '\''.implode('\', \'', $arFields).'\''
				));

				unset($arFields);
			}
	}

	if ($sAction == 'edit') {
		$arItem = CStartShopDelivery::GetByID($_REQUEST['ID'])->GetNext();

		if ($_REQUEST['ADDED'] == 'Y')
			$sNotify = GetMessage('messages.notify.added');

		if (empty($arItem)) {
			LocalRedirect($arLinks['BACK']);
			die();
		}

		if ($bActionSave || $bActionApply) {
			$bUpdated = CStartShopDelivery::Update($arItem['ID'], $arValues);

			if ($bUpdated) {
				if ($bActionSave) {
					LocalRedirect($arLinks['BACK']);
					die();
				}

				$sNotify = GetMessage('messages.notify.saved');
			} else {
				$sError = GetMessage('messages.warning.exists');
			}

			$arItem = CStartShopDelivery::GetByID($_REQUEST['ID'])->GetNext();
		}

		$arValues['CODE'] = strval($arItem['CODE']);
		$arValues['ACTIVE'] = strval($arItem['ACTIVE']);
		$arValues['SID'] = strval($arItem['SID']);
		$arValues['PRICE'] = floatval($arItem['PRICE']);
		$arValues['SORT'] = intval($arItem['SORT']);
		$arValues['PROPERTIES'] = $arItem['PROPERTIES'];
		$arValues['LANG'] = $arItem['LANG'];

		$APPLICATION->SetTitle(GetMessage('title.edit'));
	}

	if (!is_array($arValues['PROPERTIES']))
		$arValues['PROPERTIES'] = array();

	$arSites = array();
	$dbSites = CSite::GetList($by = "sort", $order = "asc");

	while ($arSite = $dbSites->Fetch())
		$arSites[] = $arSite;

	$arOrderProperties = array();
	$dbOrderProperties = CStartShopOrderProperty::GetList(array('SORT' => 'ASC'), array('SID' => $arValues['SID']));

	while ($arOrderProperty = $dbOrderProperties->Fetch())
		$arOrderProperties[$arOrderProperty['ID']] = $arOrderProperty;

	$arContextMenu = array(
		array(
			"TEXT" => GetMessage("title.buttons.back"),
			"ICON" => "btn_list",
			"LINK" => $arLinks['BACK']
		),
		array(
			"TEXT" => GetMessage("title.buttons.add"),
			"ICON" => "btn_new",
			"LINK" => $arLinks['ADD'],
		)
	);

	$arTabs = array(
		array(
			"DIV" => "common",
			"TAB" => GetMessage("tabs.common"),
			"ICON" => "catalog",
			"TITLE" => GetMessage("tabs.common")
		)
	);

	$oContextMenu = new CAdminContextMenu($arContextMenu);
	$oTabControl = new CAdminTabControl("tabs", $arTabs);
?>
<?require_once($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");?>
<?
	$oContextMenu->Show();

	if (!empty($sError))
		CAdminMessage::ShowMessage($sError);

	if (!empty($sNotify) && empty($sError))
		CAdminMessage::ShowNote($sNotify);
?>
<form method="POST">
    <?
        $oTabControl->Begin();
        $oTabControl->BeginNextTab();
    ?>
	<?if ($sAction == 'add'):?>
		<input type="hidden" name="ADDED" value="Y" />
	<?endif;?>
	<?if ($sAction == 'edit'):?>
		<tr>
			<td width="40%"><b><?=GetMessage("fields.id")?>:</b></td>
			<td width="60%"><?=htmlspecialcharsbx($arItem['ID'])?></td>
		</tr>
	<?endif;?>
	<tr>
		<td width="40%"><b><?=GetMessage("fields.code")?>:</b></td>
		<td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['CODE'])?>" name="CODE"/></td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("fields.sort")?>:</td>
		<td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['SORT'])?>" name="SORT"/></td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("fields.active")?>:</td>
		<td width="60%"><input type="checkbox" value="Y" name="ACTIVE"<?=$arValues['ACTIVE'] == 'Y' ? ' checked="checked"' : ''?>/></td>
	</tr>
	<tr>
		<td width="40%"><b><?=GetMessage("fields.site")?>:</b></td>
		<td width="60%">
			<?foreach ($arSites as $arSite):?>
				<label><input type="radio" value="<?=$arSite['ID']?>" name="SID"<?=$arValues['SID'] == $arSite['ID'] ? ' checked="checked"' : ''?>/><?=$arSite['NAME']?> (<?=$arSite['ID']?>)</label><br />
			<?endforeach?>
		</td>
	</tr>
	<tr>
		<td width="40%"><b><?=GetMessage("fields.price")?>: <span class="required" style="vertical-align: super; font-size: smaller;">1</span></b></td>
		<td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['PRICE'])?>" name="PRICE"/></td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("fields.fields")?>: <b><span class="required" style="vertical-align: super; font-size: smaller;">2</span></b></td>
		<td width="60%">
			<select name="PROPERTIES[]" multiple="multiple">
				<option value=""><?=GetMessage("fields.field.not_selected")?></option>
				<?foreach ($arOrderProperties as $arOrderProperty):?>
                    <option value="<?=$arOrderProperty['ID']?>"<?=in_array($arOrderProperty['ID'], $arValues['PROPERTIES']) ? ' selected="selected"' : ''?>>[<?=$arOrderProperty['CODE']?>] <?=$arOrderProperty['LANG'][LANGUAGE_ID]['NAME']?></option>
				<?endforeach?>
			</select>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("fields.language.caption")?></td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<table border="0" cellspacing="6" class="internal">
				<tr class="heading">
					<td><?=GetMessage("fields.language.language")?></td>
					<td><?=GetMessage("fields.language.name")?></td>
				</tr>
				<?foreach ($arLanguages as $arLanguage):?>
					<tr>
						<td><?=$arLanguage['NAME']?></td>
						<td><input type="text" value="<?=htmlspecialcharsbx($arValues['LANG'][$arLanguage['LID']]['NAME'])?>" name="LANG_<?=$arLanguage['LID']?>_NAME"/></td>
					</tr>
				<?endforeach;?>
			</table>
		</td>
	</tr>
	<?
		$oTabControl->Buttons(
			array(
				"back_url" => $arLinks['BACK']
			)
		);
	?>
	<?
		$oTabControl->End();
	?>
</form>
<div class="adm-info-message-wrap" style="display: block;">
	<div class="adm-info-message" style="display: block;">
		<b><span class="required" style="vertical-align: super; font-size: smaller;">1</span></b> - <?=GetMessage("fields.price.description")?><br />
		<b><span class="required" style="vertical-align: super; font-size: smaller;">2</span></b> - <?=GetMessage("fields.fields.description")?>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>