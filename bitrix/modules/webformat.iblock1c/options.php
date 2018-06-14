<?php
//Including lang-file
IncludeModuleLangFile( __FILE__ );

// Подключаем модуль (выполняем код в файле include.php)
CModule::IncludeModule('iblock');
CModule::IncludeModule('webformat.iblock1c');
$webformatLangPrefix = $webformatLangPrefix = 'WEBFORMAT_IBLOCK1C_';

//if((int)substr(SM_VERSION, 0, 2) < 11){ShowError(GetMessage($webformatLangPrefix.'NOT_SUPPORTED'));}
if(isset($_REQUEST['webformat_bsend'])){
    if(($message = WebformatIblock1C::Adapt($_POST['webformat_iblock1c'])) === true){
        echo CAdminMessage::ShowNote(GetMessage($webformatLangPrefix.'SUCCESS'));
    }else{
        echo CAdminMessage::ShowMessage($message);
    }
}


//Tabs description
$aTabs = array();
$aTabs[] = array(
	'DIV'   => 'webformat_iblock1c_main',
	'TAB'   => GetMessage($webformatLangPrefix.'TAB_MAIN'),
	'ICON'  => '',
	'TITLE' => ''
);

//Including CSS
	ob_start();
		include('css/adminstyle.css');
		$css .= ob_get_contents();
	ob_end_clean();
	$APPLICATION->AddHeadString('<style>'.$css.'</style>');
//---End---Including CSS

//Initialazing tabs
$oTabControl = new CAdmintabControl('tabControl', $aTabs);
$oTabControl->Begin();
?>
<form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=webformat.iblock1c&lang=<?=LANG?>&mid_menu=1">
	<?=bitrix_sessid_post()?>

	<?php
		//include(rtrim($_SERVER['DOCUMENT_ROOT'], '/').'/bitrix/modules/webformat.thumbnail1/includes/options.php');
		foreach($aTabs as $tab){
			$oTabControl->BeginNextTab();
			switch($tab['DIV']){
				case 'webformat_iblock1c_main':
					include('options_tabs/main.php');
					break;
			}
			?>
			<tr><td colspan="2"><br></td></tr>
		<?}?>

	<?$oTabControl->Buttons();?>
	<input type="submit" name="webformat_bsend" value="<?=GetMessage($webformatLangPrefix.'BSEND')?>" />
	<?$oTabControl->End();?>
</form>