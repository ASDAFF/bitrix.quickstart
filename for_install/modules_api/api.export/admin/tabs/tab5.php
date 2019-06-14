<?
/**
 * Bitrix vars
 *
 * @var array      $arFieldTitle
 * @var array      $profile
 * @var CAdminForm $tabControl
 *
 * @var CUser      $USER
 * @var CMain      $APPLICATION
 *
 */
use \Bitrix\Main\Localization\Loc;
use Api\Export\Tools;

Loc::loadMessages(__FILE__);
?>
<? $tabControl->BeginCustomField('PROFILE[TYPE]', ''); ?>
	<tr class="heading" align="center">
		<td colspan="2"><?=Loc::getMessage('AEAE_TAB_HEADING_TYPE_SWITCH')?></td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<p>
				<?=Tools::showHint('AEAE_TAB_HEADING_TYPE_SWITCH');?>
				<select name="PROFILE[TYPE]">
					<? foreach($arTypes as $groupName => $types): ?>
						<optgroup label="<?=$groupName?>">
							<? foreach($types as $arType): ?>
								<? $selected = ($profile['TYPE'] == $arType['CODE'] ? 'selected="selected"' : ''); ?>
								<option value="<?=$arType['CODE']?>"<?=$selected?>>&nbsp;&nbsp;<?=$arType['NAME']?></option>
							<? endforeach; ?>
						</optgroup>
					<? endforeach; ?>
				</select>
			</p>
			<p id="offer_type_desc" style="font-weight:bold"></p>
		</td>
	</tr>
	<tr class="heading" align="center">
		<td><?=Loc::getMessage('AEAE_TAB_HEADING_TYPE_OFFER')?></td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<table id="profile_fields_table" cellpadding="0" cellspacing="0" width="100%"></table>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center" id="fieldset-item-add-button">
			<br>
			<button class="adm-btn" onclick="customFieldAdd(this); return false;"><?=Loc::getMessage('AEAE_TAB_HEADING_FIELD_ADD')?></button>
		</td>
	</tr>
<? $tabControl->EndCustomField('PROFILE[TYPE]'); ?>