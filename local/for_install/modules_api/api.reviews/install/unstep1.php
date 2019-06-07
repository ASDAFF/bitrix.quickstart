<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
/** @var CMain $APPLICATION */

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
	<form action="<?=$APPLICATION->GetCurPage()?>">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="lang" value="<? echo LANGUAGE_ID ?>">
		<input type="hidden" name="id" value="api.reviews">
		<input type="hidden" name="uninstall" value="Y">
		<input type="hidden" name="step" value="2">
		<?=CAdminMessage::ShowMessage(Loc::getMessage('MOD_UNINST_WARN'))?>
		<p><?=Loc::getMessage('MOD_UNINST_SAVE')?></p>
		<p>
			<label for="savedata">
				<input type="checkbox" name="savedata" id="savedata" value="Y" checked>
				<?=Loc::getMessage('MOD_UNINST_SAVE_DATA')?>
			</label>
		</p>
		<input type="submit" name="inst" value="<?=Loc::getMessage('MOD_UNINST_DEL')?>">
	</form>
<?
