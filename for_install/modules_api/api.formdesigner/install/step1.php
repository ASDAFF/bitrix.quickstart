<?
/** @var CMain $APPLICATION */

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('AFD_INSTALL_MODULE_NAME'));

if(!check_bitrix_sessid())
	return Loc::getMessage('AFD_STEP1_SESS_EXPIRED');

if(!Loader::includeModule('iblock'))
	return Loc::getMessage('AFD_STEP1_IBLOCK_ERROR');

$arIblockType = \Bitrix\Iblock\TypeTable::getList(array(
	'select' => array('ID', 'NAME' => 'LANG_MESSAGE.NAME'),
	'filter' => array('=LANG_MESSAGE.LANGUAGE_ID' => LANGUAGE_ID),
))->fetchAll();
?>
<hr>
<?
echo BeginNote();
echo Loc::getMessage('AFD_STEP1_INSTALL_NOTE');
echo EndNote();
?>
<form action="<?=POST_FORM_ACTION_URI?>" method="post">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="hidden" name="id" value="api.formdesigner">
	<input type="hidden" name="install" value="Y">
	<input type="hidden" name="step" value="2">
	<fieldset>
		<legend><?=Loc::getMessage('AFD_STEP1_LEGEND_TITLE')?></legend>
		<p>
			<?=Loc::getMessage('AFD_STEP1_ADMIN_IBLOCK_TYPE')?><br>
			<select name="IBLOCK_TYPE" style="margin-top:5px">
				<option value=""><?=Loc::getMessage('AFD_STEP1_OPTION_EMPTY')?></option>
				<? foreach($arIblockType as $arType): ?>
					<option value="<?=$arType['ID']?>"><?=$arType['NAME']?></option>
				<? endforeach; ?>
			</select>
		</p>
		<p>
			<?=Loc::getMessage('AFD_STEP1_ADMIN_FORM_TYPE')?><br>
			<select name="FORM_TYPE" style="margin-top:5px">
				<option value=""><?=Loc::getMessage('AFD_STEP1_OPTION_EMPTY')?></option>
				<option value="simple"><?=Loc::getMessage('AFD_STEP1_ADMIN_FORM_TYPE_SIMPLE')?></option>
			</select>
		</p>
		<p>
			<label for="INSTALL_DEMO">
				<input type="hidden" name="INSTALL_DEMO" value="N">
				<input type="checkbox" name="INSTALL_DEMO" value="Y" style="vertical-align:middle" id="INSTALL_DEMO">
				<?=Loc::getMessage('AFD_STEP1_INSTALL_DEMO')?>
			</label>
		</p>
		<input type="submit" name="inst" value="<?=Loc::getMessage('AFD_STEP1_BUTTON_TEXT')?>">
	</fieldset>
</form>