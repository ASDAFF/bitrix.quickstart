<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

foreach ($arResult['VALUE'] as $res) {
	?>
	<span class="system-field-edit-item">
		<?
		switch ($arParams['arUserField']['SETTINGS']['DISPLAY']) {
			case 'DROPDOWN':
				?>
				<select class="form-control" name="<?=$arParams['arUserField']['FIELD_NAME']?>" id="<?=$arParams['domID']?>">
					<option value="1"<?=$res ? ' selected=""': ''?>><?=GetMessage('MAIN_YES')?></option>
					<option value="0"<?=$res ? '': ' selected=""'?>><?=GetMessage('MAIN_NO')?></option>
				</select>
				<?
				break;
			
			case 'RADIO':
				?>
				<label class="radio-inline">
					<input type="radio" name="<?=$arParams['arUserField']['FIELD_NAME']?>" value="1" <?=$res ? ' checked=""': ''?>/> <?=GetMessage('MAIN_YES')?>
				</label>
				<label class="radio-inline">
					<input type="radio" name="<?=$arParams['arUserField']['FIELD_NAME']?>" value="0" <?=$res ? '': ' checked=""'?>/> <?=GetMessage('MAIN_NO')?>
				</label>
				<?
				break;
			
			default:
				?>
				<input type="hidden" value="0" name="<?=$arParams['arUserField']['FIELD_NAME']?>"/>
				<label class="checkbox-inline">
					<input type="checkbox" name="<?=$arParams['arUserField']['FIELD_NAME']?>" value="1"<?=$res ? ' checked=""': ''?>/> <?=GetMessage('MAIN_YES')?>
				</label>
				<?
		}
		?>
	</span>
	<?
}

if ($arParams['arUserField']['MULTIPLE'] == 'Y' && $arParams['SHOW_BUTTON'] != 'N') {
	?>
	<button class="btn btn-default" onClick="addElement('<?=$arParams['arUserField']['FIELD_NAME']?>', this)"><?=GetMessage('USER_TYPE_PROP_ADD')?></button>
	<?
}
?>