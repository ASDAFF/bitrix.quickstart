<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('AFD_IU1_TITLE'));
?>
<form action="<?=$APPLICATION->GetCurPage()?>">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<input type="hidden" name="id" value="api.formdesigner">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<?=CAdminMessage::ShowMessage(Loc::getMessage('AFD_IU1_DANGER'))?>
	<p><?=Loc::getMessage('AFD_IU1_INFO')?></p>
	<p>
		<label for="savedata">
			<input type="checkbox" name="savedata" id="savedata" value="Y" checked>
			<?=Loc::getMessage('AFD_IU1_SAVE')?>
		</label>
	</p>
	<input type="submit" name="inst" value="<?=Loc::getMessage('AFD_IU1_SUBMIT')?>">
</form>