<?
/**
 * Bitrix vars
 *
 * @var array     $arParams
 * @var array     $arResult
 *
 * @var CDatabase $DB
 * @var CUser     $USER
 * @var CMain     $APPLICATION
 *
 * @global        $REQUEST_METHOD
 * @global        $Update
 */
global $MESS, $USER;

if(!$USER->IsAdmin())
	return false;

$MODULE_ID = basename(dirname(__FILE__));

IncludeModuleLangFile(__FILE__);


$arTabs[] = array(
	'DIV'   => 'ts_tab_general',
	'TAB'   => GetMessage("MAIN_TAB_SET"),
	'ICON'  => 'fileman_settings',
	'TITLE' => GetMessage('MAIN_TAB_TITLE_SET'),
);

//Get Iblock List
CModule::IncludeModule('iblock');
$arIBlocks = array();
$res       = CIBlock::GetList(Array('id' => 'asc'), Array('ACTIVE' => 'Y'));
while($ar_res = $res->Fetch())
	$arIBlocks[] = $ar_res;

unset($ar_res);
//END Get Iblock List


if($REQUEST_METHOD == 'POST' && strlen($Update) && check_bitrix_sessid())
{
	COption::SetOptionString($MODULE_ID, 'PRINT_IBLOCK_ID', intval($_REQUEST['PRINT_IBLOCK_ID']));
}

$tabControl = new CAdminTabControl('tabcontrol', $arTabs);
$tabControl->Begin();
/** Форма с настройками модуля */
?>
<form method="POST" action="<? echo $APPLICATION->GetCurPage(); ?>?mid=<?=$MODULE_ID;?>&lang=<?=LANGUAGE_ID;?>">
	<?=bitrix_sessid_post()?>
	<? $tabControl->BeginNextTab(); ?>
	<tr>
		<td width="50%" valign="top">
			<label for="orders_iblock_id"><?=GetMessage("SET_PRINT_IBLOCK_ID")?>:</label>
		</td>
		<td valign="top">
			<? $iblock_id = COption::GetOptionString($MODULE_ID, 'PRINT_IBLOCK_ID'); ?>
			<? if(count($arIBlocks)): ?>
				<select name="PRINT_IBLOCK_ID">
					<option value="">---</option>
					<? foreach($arIBlocks as $arIBlock): ?>
						<option value="<?=$arIBlock['ID'];?>"<? if($arIBlock['ID'] == $iblock_id): ?> selected="selected"<? endif; ?>>[<?=$arIBlock['ID'];?>] <?=$arIBlock['NAME'];?></option>
					<? endforeach; ?>
				</select>
			<? endif; ?>
		</td>
	</tr>
	<? $tabControl->Buttons(); ?>
	<input type="hidden" name="Update" value="Y"/>
	<input <? if(!$USER->IsAdmin())	echo "disabled" ?> type="submit" name="Save" value="<? echo GetMessage("MAIN_SAVE") ?>" title="<? echo GetMessage("MAIN_OPT_SAVE_TITLE") ?>" class="adm-btn-save">
	<? $tabControl->End(); ?>
</form>