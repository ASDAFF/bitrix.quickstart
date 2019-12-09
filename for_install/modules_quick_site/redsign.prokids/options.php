<?
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/options.php');

CModule::IncludeModule('redsign.prokids');

$aTabs = array(
	array('DIV' => 'redsign_gopro1', 'TAB' => GetMessage('GOPRO.TAB_NAME_SETTINGS'), 'ICON' => '', 'TITLE' => GetMessage('GOPRO.TAB_TITLE_SETTINGS')),
);

$tabControl = new CAdminTabControl('tabControl', $aTabs);

if( (isset($_REQUEST['save']) ||isset($_REQUEST['apply']) ) && check_bitrix_sessid()){
	COption::SetOptionString('redsign.prokids', 'adaptive', $_REQUEST['adaptive']=='Y' ? 'Y' : 'N' );
	COption::SetOptionInt('redsign.prokids', 'clickprotectiondelay', IntVal($_REQUEST['clickprotectiondelay']) );
	COption::SetOptionInt('redsign.prokids', 'requestdelay', IntVal($_REQUEST['requestdelay']) );
	COption::SetOptionString('redsign.prokids', 'prop_option', htmlspecialchars($_REQUEST['prop_option']) );
}

$arrPropOption = array(
	'line_through' => GetMessage('GOPRO.PROP_OPTION_LINE_THROUGH'),
	'hide' => GetMessage('GOPRO.PROP_OPTION_HIDE'),
);
$tabControl->Begin();
?><form method="post" name="redsign_quickbuy_options" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>"><?
echo bitrix_sessid_post();
$tabControl->BeginNextTab();
?><tr class="heading"><?
	?><td colspan="3"><?=GetMessage('GOPRO.SOLUTION')?></td><?
?></tr><?
$adaptive = COption::GetOptionString('redsign.prokids', 'adaptive', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('GOPRO.ADAPTIVE')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="adaptive" value="Y"<?if($adaptive=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$prop_option = COption::GetOptionString('redsign.prokids', 'prop_option', 'line_through');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('GOPRO.PROP_OPTION')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?
		?><select name="prop_option"><?
			foreach($arrPropOption as $val => $mess)
			{
				?><option value="<?=$val?>"<?if($val==$prop_option):?> selected <?endif;?>><?=$mess?></option><?
			}
		?></select><?
	?></td><?
?></tr><?
$tabControl->Buttons(array());
$tabControl->End();
?></form>