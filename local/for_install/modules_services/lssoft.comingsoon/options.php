<?php
IncludeModuleLangFile(__FILE__);

global $MESS;
$sModuleId='lssoft.comingsoon';

if (!CModule::IncludeModule($sModuleId)) {
	return;
}
/**
 * Разрешаем только админам
 */
if (!$USER->IsAdmin()) {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	return;
}

/**
 * Определяем список возможных тем оформления
 */
$sDirComponent=$_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$sModuleId.'/install/components/lssoft/cs';
$aPaths = glob("$sDirComponent/templates/.default/themes/*", GLOB_ONLYDIR);
$aThemes=array('default');
if($aPaths) {
	foreach($aPaths as $sPath){
		$aThemes[]=basename($sPath);
	}
}
/**
 * Дефолтный код для шаринга с социальных сетях
 */
$sDefaultLike="
<span class='st_facebook_hcount' displayText='Facebook'></span><br/><br/>
							<span class='st_twitter_hcount' displayText='Tweet'></span><br/><br/>
							<span class='st_vkontakte_hcount' displayText='Vkontakte'></span><br/><br/>
							<span class='st_odnoklassniki_hcount' displayText='Odnoklassniki'></span>
							<script type=\"text/javascript\" src=\"http://w.sharethis.com/button/buttons.js\"></script>
							<script type=\"text/javascript\">stLight.options({publisher: \"ur-9f409bce-94fb-d405-d754-fd65f94e5eca\", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
";

/**
 * Формируем настройки в виде табов для каждого сайта
 */
$aTabs=array();
$aOptions=array();
$oSiteItems = CSite::GetList($by="sort",$order="desc");
while($aSite=$oSiteItems->Fetch()) {
	$sDateCalendar='<span id="calendar_'.$aSite['LID'].'">'.Calendar("LS_CS_TIMER_DATE_".$aSite['LID'],'cs_form').'</span>';
	/**
	 * Формируем список полей для каждого сайта
	 */
	$aOptions[$aSite['LID']]=array(
    	Array("LS_CS_ENABLED_".$aSite['LID'],GetMessage('LS_CS_FORM_ENABLED'), "N", Array("checkbox", "Y")),
    	Array("LS_CS_TIMER_".$aSite['LID'],GetMessage('LS_CS_FORM_TIMER'), "Y", Array("checkbox", "Y")),
    	Array("LS_CS_TIMER_DATE_".$aSite['LID'],GetMessage('LS_CS_FORM_TIMER_DATE').$sDateCalendar, date('d.m.Y',time()+60*60*24*30), Array("text", 30)),
    	Array("LS_CS_INVITE_ENABLED_".$aSite['LID'],GetMessage('LS_CS_FORM_INVITE_ENABLED'), "N", Array("checkbox", "Y")),
    	Array("LS_CS_INVITE_NEED_LOGIN_".$aSite['LID'],GetMessage('LS_CS_FORM_INVITE_NEED_LOGIN'), "Y", Array("checkbox", "Y")),
		Array("LS_CS_THEME_".$aSite['LID'],GetMessage('LS_CS_FORM_THEME'), '', Array("selectbox", array_combine($aThemes,$aThemes))),
    	Array("LS_CS_THEME_CUSTOM_".$aSite['LID'],GetMessage('LS_CS_FORM_THEME_CUSTOM'), '', Array("text", 30)),
    	Array("LS_CS_TITLE_".$aSite['LID'],GetMessage('LS_CS_FORM_TITLE'), GetMessage('LS_CS_FORM_TITLE_DEFAULT'), Array("text", 30)),
    	Array("LS_CS_DESCRIPTION_".$aSite['LID'],GetMessage('LS_CS_FORM_DESCRIPTION'), GetMessage('LS_CS_FORM_DESCRIPTION_DEFAULT'), Array("textarea",7,40)),
    	Array("LS_CS_MAIL_".$aSite['LID'],GetMessage('LS_CS_FORM_MAIL'), '', Array("text", 30)),
    	Array("LS_CS_LOGO_".$aSite['LID'],GetMessage('LS_CS_FORM_LOGO'), '/bitrix/images/lssoft.comingsoon/logo.png', Array("text", 30)),
    	Array("LS_CS_LIKE_".$aSite['LID'],GetMessage('LS_CS_FORM_LIKE'), $sDefaultLike, Array("textarea",10,70)),
    	GetMessage('LS_CS_FORM_NOTICE_SHARE'),
    	Array("LS_CS_SHARE_FB_".$aSite['LID'],GetMessage('LS_CS_SHARE_FB'), '', Array("text", 50)),
    	Array("LS_CS_SHARE_TW_".$aSite['LID'],GetMessage('LS_CS_SHARE_TW'), '', Array("text", 50)),
    	Array("LS_CS_SHARE_VK_".$aSite['LID'],GetMessage('LS_CS_SHARE_VK'), '', Array("text", 50)),
    	Array("LS_CS_SHARE_ODN_".$aSite['LID'],GetMessage('LS_CS_SHARE_ODN'), '', Array("text", 50)),
    	Array("LS_CS_SHARE_GP_".$aSite['LID'],GetMessage('LS_CS_SHARE_GP'), '', Array("text", 50)),
	);
    $aTabs[]=array(
		'DIV' => 'site_'.$aSite['LID'],
		'TAB' => $aSite['NAME'],
		'TITLE' => GetMessage('LS_CS_TITLE').' "'.$aSite['NAME'].'"',
		'_OPT' => $aOptions[$aSite['LID']],
		'_SID' => $aSite['LID'],
	);
}

/**
 * Вкладка с настройками прав доступа
 */
$aTabs[]=array(
		'DIV' => 'access_settings',
		'TAB' => GetMessage('LS_CS_ACCESS_TAB'),
		'TITLE' => GetMessage('LS_CS_ACCESS_TITLE'),
);


/**
 * Обработка отправки формы
 */
if (isset($_POST['submit']) and check_bitrix_sessid()) {
		$Update='Y';
		foreach($aTabs as $aTab) {
			if (!isset($aTab['_SID'])) {
				continue;
			}
			/**
			 * Теперь проходим по настройкам для каждого сайта
			 */
			foreach($aOptions[$aTab['_SID']] as $aOption) {
				$sOptionName=$aOption[0];
				$sOptionValue=isset($_POST[$sOptionName]) ? $_POST[$sOptionName] : '';
				COption::SetOptionString($sModuleId,$sOptionName,$sOptionValue);
			}
		}
}
/**
 * Обработка сброса параметров модуля
 */
if (isset($_POST['restore']) and check_bitrix_sessid()) {
	COption::RemoveOption($sModuleId);
}


$oTabControl = new CAdminTabControl('ls_cs_admin', $aTabs);
$oTabControl->Begin();
?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?echo LANG?>" name="cs_form">
	<?php
	/**
	 * Выводим табы с настройками
	 */
	foreach($aTabs as $aTab) {
		$oTabControl->BeginNextTab();
		if (!isset($aTab['_SID'])) {
			continue;
		}
		__AdmSettingsDrawList('lssoft.comingsoon',$aTab['_OPT']);
	}
	/**
	 * Подключаем страницу с настройками прав доступа
	 */
	$module_id=$sModuleId;
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights2.php");
	
	$oTabControl->Buttons();
	?>
    
    <input type="submit" name="submit" value="<?php echo GetMessage('MAIN_SAVE')?>" class="adm-btn-save">
    <input type="reset" name="reset" value="<?php echo GetMessage('MAIN_RESET')?>">
    <input type="submit" name="restore" value="<?php echo GetMessage('LS_CS_FORM_BUTTON_DEFAULT')?>" onclick="return confirm('<?php echo GetMessage('LS_CS_FORM_BUTTON_DEFAULT_CONFIRM')?>');">
    
    <?php
    echo(bitrix_sessid_post());
    $oTabControl->End();
    ?>
</form>