<?
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

CModule::IncludeModule('catalog');
CModule::IncludeModule('redsign.quickbuy');

$aTabs = array(
	array('DIV' => 'redsign_edit1', 'TAB' => GetMessage('RSQB.TAB1_NAME'), 'ICON' => '', 'TITLE' => GetMessage('RSQB.TAB1_TITLE')),
);

$tabControl = new CAdminTabControl('tabControl', $aTabs);

if(isset($_REQUEST['save']) && check_bitrix_sessid())
{
	COption::SetOptionString('redsign.quickbuy', 'init_jquery', $_REQUEST['init_jquery']=='Y' ? 'Y' : 'N' );
}

$tabControl->Begin();

?><form method="post" name="redsign_quickbuy_options" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>"><?
echo bitrix_sessid_post();





$tabControl->BeginNextTab();
$init_jquery = COption::GetOptionString('redsign.quickbuy', 'init_jquery', 'N');
?><tr><?
	?><td width="50%" valign="top"><?=GetMessage('RSQB.INIT_JQUERY')?>:</td><?
	?><td width="50%"><input type="checkbox" name="init_jquery" value="Y"<?if($init_jquery=="Y"):?> checked="checked" <?endif;?> /></td><?
?></tr><?





$tabControl->Buttons(array());
$tabControl->End();
?></form>