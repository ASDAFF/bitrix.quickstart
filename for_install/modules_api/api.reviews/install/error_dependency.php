<?
/** @var CMain $APPLICATION */

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('ARIED_MODULE_NAME'));

CAdminMessage::ShowMessage(Loc::getMessage('ARIED_ERROR'));
?>
<style>
	.afd-table{
		border-spacing: 0;
		border-collapse: collapse;
		margin: 15px 0;
	}
	.afd-table th,
	.afd-table td{
		border: 1px solid #A4B9CC;
		padding: 5px 10px;
	}
</style>
<table class="afd-table">
	<thead>
	<tr>
		<th><?=Loc::getMessage('ARIED_MINIMUM_HEADER')?></th>
		<th><?=Loc::getMessage('ARIED_CURRENT_HEADER')?></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td><?=Loc::getMessage('ARIED_MINIMUM_CORE')?></td>
		<td><?=Loc::getMessage('ARIED_MINIMUM_CORE_CURRENT', array('#VERSION#' => SM_VERSION))?></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage('ARIED_MINIMUM_PHP')?></td>
		<td><?=Loc::getMessage('ARIED_MINIMUM_PHP_CURRENT', array('#VERSION#' => PHP_VERSION))?></td>
	</tr>
	</tbody>
</table>
<form action="<?=$APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=Loc::getMessage('ARIED_BUTTON_BACK')?>">
</form>
