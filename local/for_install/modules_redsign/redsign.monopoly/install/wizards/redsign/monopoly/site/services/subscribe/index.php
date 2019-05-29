<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$dbSite = CSite::GetByID(WIZARD_SITE_ID);
if($arSite = $dbSite -> Fetch())
	$lang = $arSite["LANGUAGE_ID"];



if(CModule::IncludeModule('subscribe'))
{
	$templates_dir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/subscribe/templates";
	$template = $templates_dir."/store_news_".WIZARD_SITE_ID;
	//Copy template from module if where was no template
	if(!file_exists($template))
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/install/php_interface/subscribe/templates/news", $template, false, true);
		$fname = $template."/template.php";
		if(file_exists($fname) && is_file($fname) && ($fh = fopen($fname, "rb")))
		{
			$php_source = fread($fh, filesize($fname));
			$php_source = preg_replace("#([\"'])(SITE_ID)(\\1)(\\s*=>\s*)([\"'])(.*?)(\\5)#", "\\1\\2\\3\\4\\5".WIZARD_SITE_ID."\\7", $php_source);
			$php_source = str_replace("Windows-1251", $arSite["CHARSET"], $php_source);
			$php_source = str_replace("Hello!", GetMessage("SUBSCR_NAME_1"), $php_source);
			$php_source = str_replace("<P>Best Regards!</P>", "", $php_source);
			fclose($fh);
			$fh = fopen($fname, "wb");
			if($fh)
			{
				fwrite($fh, $php_source);
				fclose($fh);
			}
		}
	}

	$rsRubric = CRubric::GetList(array(), array(
		"LID" => WIZARD_SITE_ID,
	));
	if(!$rsRubric->Fetch())
	{
		//Database actions
        $arRubrics = array(
            array(
                "ACTIVE"	=> "Y",
                "NAME"		=> GetMessage("SUBSCR_NAME_1"),
                "SORT"		=> 100,
                "DESCRIPTION"	=> GetMessage("SUBSCR_DESC_1"),
                "LID"		=> WIZARD_SITE_ID,
                "AUTO"		=> "Y",
                "DAYS_OF_MONTH"	=> "",
                "DAYS_OF_WEEK"	=> "1,2,3,4,5,6,7",  
                "TIMES_OF_DAY"	=> "05:00",
                "TEMPLATE"	=> substr($template, strlen($_SERVER["DOCUMENT_ROOT"]."/")),
                "VISIBLE"	=> "Y",
                "FROM_FIELD"	=> COption::GetOptionString("main", "email_from", "info@ourtestsite.com"),
                "LAST_EXECUTED"	=> ConvertTimeStamp(false, "FULL"),
            ),
            array(
                "ACTIVE"	=> "Y",
                "NAME"		=> GetMessage("SUBSCR_NAME_2"),
                "SORT"		=> 100,
                "DESCRIPTION"	=> GetMessage("SUBSCR_DESC_2"),
                "LID"		=> WIZARD_SITE_ID,
                "AUTO"		=> "Y",
                "DAYS_OF_MONTH"	=> "",
                "DAYS_OF_WEEK"	=> "1,2,3,4,5,6,7",  
                "TIMES_OF_DAY"	=> "05:00",
                "TEMPLATE"	=> substr($template, strlen($_SERVER["DOCUMENT_ROOT"]."/")),
                "VISIBLE"	=> "Y",
                "FROM_FIELD"	=> COption::GetOptionString("main", "email_from", "info@ourtestsite.com"),
                "LAST_EXECUTED"	=> ConvertTimeStamp(false, "FULL"),
            ),
        );
        
		$obRubric = new CRubric;
        foreach($arRubrics as $arFields){
            $ID = $obRubric->Add($arFields);
        }

	}
	COption::SetOptionString('subscribe', 'subscribe_section', '#SITE_DIR#personal/subscribe/');
}

$shopEmail = $wizard->GetVar("shopEmail");
$siteName = $wizard->GetVar("siteName");
COption::SetOptionString('main', 'email_from', $shopEmail);
COption::SetOptionString('main', 'new_user_registration', 'Y');
COption::SetOptionString('main', 'captcha_registration', 'Y');
COption::SetOptionString('main', 'site_name', $siteName);
COption::SetOptionInt("search", "suggest_save_days", 250);

if(strlen(COption::GetOptionString('main', 'CAPTCHA_presets', '')) <= 0)
{
	COption::SetOptionString('main', 'CAPTCHA_transparentTextPercent', '0');
	COption::SetOptionString('main', 'CAPTCHA_arBGColor_1', 'FFFFFF');
	COption::SetOptionString('main', 'CAPTCHA_arBGColor_2', 'FFFFFF');
	COption::SetOptionString('main', 'CAPTCHA_numEllipses', '0');
	COption::SetOptionString('main', 'CAPTCHA_arEllipseColor_1', '7F7F7F');
	COption::SetOptionString('main', 'CAPTCHA_arEllipseColor_2', 'FFFFFF');
	COption::SetOptionString('main', 'CAPTCHA_bLinesOverText', 'Y');
	COption::SetOptionString('main', 'CAPTCHA_numLines', '0');
	COption::SetOptionString('main', 'CAPTCHA_arLineColor_1', 'FFFFFF');
	COption::SetOptionString('main', 'CAPTCHA_arLineColor_2', 'FFFFFF');
	COption::SetOptionString('main', 'CAPTCHA_textStartX', '40');
	COption::SetOptionString('main', 'CAPTCHA_textFontSize', '26');
	COption::SetOptionString('main', 'CAPTCHA_arTextColor_1', '000000');
	COption::SetOptionString('main', 'CAPTCHA_arTextColor_2', '000000');
	COption::SetOptionString('main', 'CAPTCHA_textAngel_1', '-15');
	COption::SetOptionString('main', 'CAPTCHA_textAngel_2', '15');
	COption::SetOptionString('main', 'CAPTCHA_textDistance_1', '-2');
	COption::SetOptionString('main', 'CAPTCHA_textDistance_2', '-2');
	COption::SetOptionString('main', 'CAPTCHA_bWaveTransformation', 'Y');
	COption::SetOptionString('main', 'CAPTCHA_arBorderColor', '000000');
	COption::SetOptionString('main', 'CAPTCHA_arTTFFiles', 'bitrix_captcha.ttf');
	COption::SetOptionString('main', 'CAPTCHA_letters', 'ABCDEFGHJKLMNPQRSTWXYZ23456789');
	COption::SetOptionString('main', 'CAPTCHA_presets', '2');
}	
COption::SetOptionString('socialnetwork', 'allow_tooltip', 'N', false ,  WIZARD_SITE_ID);

//Edit profile task
$editProfileTask = false;
$dbResult = CTask::GetList(Array(), Array("NAME" => "main_change_profile"));
if ($arTask = $dbResult->Fetch())
	$editProfileTask = $arTask["ID"];
//Registered users group
$dbResult = CGroup::GetList($by, $order, Array("STRING_ID" => "REGISTERED_USERS"));
if (!$dbResult->Fetch())
{
	$group = new CGroup;
	$arFields = Array(
		"ACTIVE" => "Y",
		"C_SORT" => 3,
		"NAME" => GetMessage("REGISTERED_USERS"),
		"STRING_ID" => "REGISTERED_USERS",
	);

	$groupID = $group->Add($arFields);
	if ($groupID > 0)
	{
		COption::SetOptionString("main", "new_user_registration_def_group", $groupID);
		if ($editProfileTask)
			CGroup::SetTasks($groupID, Array($editProfileTask), true);
	}
}

$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), array("ACTIVE"=>"Y", "ADMIN"=>"N", "ANONYMOUS"=>"N")); 
if(!($rsGroups->Fetch()))
{
	$group = new CGroup;
	$arFields = Array(
		"ACTIVE"       => "Y",
		"C_SORT"       => 100,
		"NAME"         => GetMessage("REGISTERED_USERS"),
		"DESCRIPTION"  => "",
		);
	$NEW_GROUP_ID = $group->Add($arFields);
	COption::SetOptionString('main', 'new_user_registration_def_group', $NEW_GROUP_ID);
	
	$rsTasks = CTask::GetList(array(), array("MODULE_ID"=>"main", "SYS"=>"Y", "BINDIG"=>"module","LETTER"=>"P"));
	if($arTask = $rsTasks->Fetch())
	{
		CGroup::SetModulePermission($NEW_GROUP_ID, $arTask["MODULE_ID"], $arTask["ID"]);
	}
}

$userGroupID = "";
$dbGroup = CGroup::GetList($by = "", $order = "", Array("STRING_ID" => "content_editor"));

if($arGroup = $dbGroup -> Fetch())
{
	$userGroupID = $arGroup["ID"];
}
else
{
	$group = new CGroup;
	$arFields = Array(
		"ACTIVE"       => "Y",
		"C_SORT"       => 300,
		"NAME"         => GetMessage("SALE_WIZARD_CONTENT_EDITOR"),
		"DESCRIPTION"  => GetMessage("SALE_WIZARD_CONTENT_EDITOR_DESCR"),
		"USER_ID"      => array(),
		"STRING_ID"      => "content_editor",
		);
	$userGroupID = $group->Add($arFields);
	$DB->Query("INSERT INTO b_sticker_group_task(GROUP_ID, TASK_ID)	SELECT ".intVal($userGroupID).", ID FROM b_task WHERE NAME='stickers_edit' AND MODULE_ID='fileman'", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
}
if(IntVal($userGroupID) > 0)
{
	WizardServices::SetFilePermission(Array($siteID, "/bitrix/admin"), Array($userGroupID => "R"));
	
	$rsTasks = CTask::GetList(array(), array("MODULE_ID"=>"main", "SYS"=>"Y", "BINDIG"=>"module","LETTER"=>"P"));
	if($arTask = $rsTasks->Fetch())
	{
		CGroup::SetModulePermission($userGroupID, $arTask["MODULE_ID"], $arTask["ID"]);
	}
	
	$rsTasks = CTask::GetList(array(), array("MODULE_ID"=>"fileman", "SYS"=>"Y", "BINDIG"=>"module","LETTER"=>"F"));
	while($arTask = $rsTasks->Fetch())
	{
		CGroup::SetModulePermission($userGroupID, $arTask["MODULE_ID"], $arTask["ID"]);
	}
	
	$SiteDir = "";
	if(WIZARD_SITE_ID != "s1"){
		$SiteDir = "/site_" . WIZARD_SITE_ID;
	}
	WizardServices::SetFilePermission(Array($siteID, $SiteDir . "/index.php"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array($siteID, $SiteDir . "/about/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array($siteID, $SiteDir . "/action/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array($siteID, $SiteDir . "/catalog/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array($siteID, $SiteDir . "/contacts/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array($siteID, $SiteDir . "/forms/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array($siteID, $SiteDir . "/info/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array($siteID, $SiteDir . "/projects/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array($siteID, $SiteDir . "/services/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array($siteID, $SiteDir . "/stocks/"), Array($userGroupID => "W"));
}
?>