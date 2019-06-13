<?
/** @var CMain $APPLICATION */

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('AFD_INSTALL_MODULE_NAME'));

CAdminMessage::ShowMessage(Loc::getMessage('AFD_INSTALL_DEPENDENCY_ERROR'));
?>
<style>
	.afd-table{
		border-spacing:0;
		border-collapse:collapse;
		margin:15px 0;
	}
	.afd-table th,
	.afd-table td{
		border:1px solid #A4B9CC;
		padding:5px 10px;
	}
</style>
<table class="afd-table">
	<thead>
	<tr>
		<th><?=Loc::getMessage('AFD_INSTALL_DEPENDENCY_MINIMUM_HEADER')?></th>
		<th><?=Loc::getMessage('AFD_INSTALL_DEPENDENCY_CURRENT_HEADER')?></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td><?=Loc::getMessage('AFD_INSTALL_DEPENDENCY_MINIMUM_CORE')?></td>
		<td><?=Loc::getMessage('AFD_INSTALL_DEPENDENCY_MINIMUM_CORE_CURRENT', array('#VERSION#' => SM_VERSION))?></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage('AFD_INSTALL_DEPENDENCY_IBLOCK')?></td>
		<td><?=(Loader::includeModule('iblock') ? Loc::getMessage('AFD_INSTALL_DEPENDENCY_IBLOCK_ON') : Loc::getMessage('AFD_INSTALL_DEPENDENCY_IBLOCK_OFF'))?></td>
	</tr>
	</tbody>
</table>
<form action="<?=$APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=Loc::getMessage('AFD_INSTALL_BUTTON_BACK')?>">
</form>
