
<?
require_once("scripts/utils.php");

function SaveLog($Str='',$oneBlock=false){
	$file=$_SERVER['DOCUMENT_ROOT'].'/log.php';
	$Str=print_r($Str,true);
	if (!isset ($_SESSION['testtime'])) $_SESSION['testtime'] = microtime(1);
	$Str=(microtime(1)- $_SESSION['testtime'])."\n".$Str."\n";
	if (!$oneBlock) $_SESSION['testtime'] = microtime(1);
	if (!$handle = fopen($file, 'a')) {
	}
	if (fwrite($handle, $Str) === FALSE) {
	}
}

// Велкам
class WelcomeStep extends CWizardStep
{
	function InitStep()
	{
		$wizard =& $this->GetWizard();
		$this->SetTitle(GetMessage("WELCOME_STEP_TITLE"));
		$this->SetStepID("welcome_step");
		$this->SetNextStep("select_template");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$wizName = str_replace (':','.',$wizard->package->name);
		define("WIZARD_NAME", $wizName); 
	}

	function ShowStep()
	{
		$this->content .= GetMessage("WELCOME_TEXT");
		$wizard =& $this->GetWizard();
		if (defined("WIZARD_DEFAULT_SITE_ID"))
		{
			$wizard =& $this->GetWizard();
			$wizard->SetVar("siteID",WIZARD_DEFAULT_SITE_ID);
			$solution = COption::GetOptionString('main', 'wizard_solution', '',WIZARD_DEFAULT_SITE_ID);
			if ($solution==WIZARD_NAME) {
				$wizard->SetVar("WIZARD_IS_RERUN",true);
				define("WIZARD_REINSTALL", true); 
				define("WIZARD_IS_RERUN", true); 
			}
			else {
				$wizard->SetVar("WIZARD_IS_RERUN",false);
				define("WIZARD_REINSTALL", false); 
				define("WIZARD_IS_RERUN", false); 
			}
		}
		else {
			$wizard->SetVar("WIZARD_IS_RERUN",false);
			define("WIZARD_REINSTALL", false); 
			define("WIZARD_IS_RERUN", false); 
		}
	}

	function OnPostForm()
	{

	}
}

class SelectTemplateStep extends CWizardStep
{
	function InitStep()
	{		
		$this->SetStepID("select_template");
		$this->SetTitle(GetMessage("SELECT_TEMPLATE_TITLE"));
		$this->SetSubTitle(GetMessage("SELECT_TEMPLATE_SUBTITLE"));
		$this->SetPrevStep("welcome_step");
		$this->SetNextStep("site_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();

		if ($wizard->IsNextButtonClick())
		{
			$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
			$arTemplates = WizardServices::GetTemplates($templatesPath);
			
		  $templateID = $wizard->GetVar("templateID");

			if (!array_key_exists($templateID, $arTemplates))
				$this->SetError(GetMessage("wiz_template"));
		}
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();

		$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
		$arTemplates = WizardServices::GetTemplates($templatesPath);

		if (empty($arTemplates))
			return;

		$defaultTemplateID = COption::GetOptionString("main", "wizard_template_id", "");
		if (strlen($defaultTemplateID) > 0 && array_key_exists($defaultTemplateID, $arTemplates))
			$wizard->SetDefaultVar("templateID", $defaultTemplateID);
		else
			$defaultTemplateID = "";

		$this->content .= '<table width="100%" cellspacing="4" cellpadding="8">';

		foreach ($arTemplates as $templateID => $arTemplate)
		{
			if ($defaultTemplateID == "")
			{
				$defaultTemplateID = $templateID;
				$wizard->SetDefaultVar("templateID", $defaultTemplateID);
			}

			$this->content .= "<tr>";
			$this->content .= '<td>'.$this->ShowRadioField("templateID", $templateID, Array("id" => $templateID))."</td>";

			if ($arTemplate["SCREENSHOT"] && $arTemplate["PREVIEW"])
				$this->content .= '<td valign="top">'.CFile::Show2Images($arTemplate["PREVIEW"], $arTemplate["SCREENSHOT"], 150, 150, ' border="0"')."</td>";
			else
				$this->content .= '<td valign="top">'.CFile::ShowImage($arTemplate["SCREENSHOT"], 150, 150, ' border="0"', "", true)."</td>";

			$this->content .= '<td valign="top" width="100%"><label for="'.$templateID.'"><b>'.$arTemplate["NAME"]."</b><br />".$arTemplate["DESCRIPTION"]."</label></td>";

			$this->content .= "</tr>";
			$this->content .= "<tr><td><br /></td></tr>";
		}

		$this->content .= "</table>";
	}
}

// Цветовая схема
class SelectThemeStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("select_theme");
		$this->SetTitle(GetMessage("SELECT_THEME_TITLE"));
		$this->SetSubTitle(GetMessage("SELECT_THEME_SUBTITLE"));
		$this->SetPrevStep("select_template");
		$wizard =& $this->GetWizard();
		$this->SetNextStep("data_install");		
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));		
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();

		if ($wizard->IsNextButtonClick())
		{
			$templateID = $wizard->GetVar("templateID");
			$themeVarName = $templateID."_themeID";
			$themeID = $wizard->GetVar($themeVarName);

			$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
			$arThemes = WizardServices::GetThemes($templatesPath."/".$templateID."/themes");

			if (!array_key_exists($themeID, $arThemes))
				$this->SetError(GetMessage("wiz_template_color"));
			else
			{
				if ($_SERVER["PHP_SELF"] != "/index.php") 
				{
			      		$wizard->SetCurrentStep("data_install");
			    	}
			}
		}
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$templateID = $wizard->GetVar("templateID");

		$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
		$arThemes = WizardServices::GetThemes($templatesPath."/".$templateID."/themes");

		if (empty($arThemes))
			return;

		$themeVarName = $templateID."_themeID";

		$defaultThemeID = COption::GetOptionString("main", "wizard_".$templateID."_theme_id", "");
		if (strlen($defaultThemeID) > 0 && array_key_exists($defaultThemeID, $arThemes))
			$wizard->SetDefaultVar($themeVarName, $defaultThemeID);
		else
			$defaultThemeID = "";

		$this->content .= '<table width="100%" cellspacing="4" cellpadding="8">';

		foreach ($arThemes as $themeID => $arTheme)
		{
			if ($defaultThemeID == "")
			{
				$defaultThemeID = $themeID;
				$wizard->SetDefaultVar($themeVarName, $defaultThemeID);
			}

			$this->content .= "<tr>";

			$this->content .= "<td>".$this->ShowRadioField($themeVarName, $themeID, Array("id" => $themeVarName."_".$themeID))."</td>";

			if ($arTheme["SCREENSHOT"] && $arTheme["PREVIEW"])
				$this->content .= '<td valign="top">'.CFile::Show2Images($arTheme["PREVIEW"], $arTheme["SCREENSHOT"], 150, 150, ' border="0"')."</td>";
			else
				$this->content .= '<td valign="top">'.CFile::ShowImage($arTheme["SCREENSHOT"], 150, 150, ' border="0"', "", true)."</td>";

			$this->content .= '<td valign="top" width="100%"><label for="'.$themeVarName."_".$themeID.'"><b>'.$arTheme["NAME"]."</b><br />".$arTheme["DESCRIPTION"]."</label></td>";

			$this->content .= "</tr>";
			$this->content .= "<tr><td><br /></td></tr>";
		}

		$this->content .= "</table>";


	}
}
class SiteSettingsStep extends CWizardStep
{

	function LDAPServerExists()
	{
		if (!CModule::IncludeModule("ldap"))
			return false;

		$rsData = CLdapServer::GetList(Array(), Array("ACTIVE" => "Y"));
		return ($rsData->Fetch());
	}

	function InitStep()
	{
		$this->SetStepID("site_settings");
		$this->SetTitle(GetMessage("wiz_settings"));
		$this->SetSubTitle(GetMessage("wiz_settings"));
		$this->SetNextStep("data_install");
		$this->SetPrevStep("select_template");
		$this->SetNextCaption(GetMessage("wiz_install"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();
		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
		$rsSites = CSite::GetByID($siteID);
		if ($arSite = $rsSites->Fetch())
			$siteDir = $_SERVER['DOCUMENT_ROOT'].$arSite["DIR"];
		else
			$siteDir = $_SERVER['DOCUMENT_ROOT'].'/';

		$cMail = COption::GetOptionString("main", "email_from", '');
			
		if (!file_exists ($siteDir.'include/icq.php'))
			$cIcq = GetMessage("wiz_company_ICQ_DEFAULT");
		else
			$cIcq = file_get_contents($siteDir.'include/icq.php');
			
		if (!file_exists ($siteDir.'include/company_name.php'))
			$cName = GetMessage('wiz_company_name_DEFAULT');
		else
			$cName = file_get_contents($siteDir.'include/company_name.php');
			
		if (!file_exists ($siteDir.'include/company_address.php'))
			$cAddr = GetMessage('wiz_company_address_DEFAULT');
		else
			$cAddr = file_get_contents($siteDir.'include/company_address.php');

			$wizard->SetDefaultVars(
			Array(
				"server_name" => $_SERVER["SERVER_NAME"],
				"siteName" =>  htmlspecialchars_decode ($cName),
				"siteDescr" => GetMessage("wiz_slogan2"),
				"company_mail" => $cMail,
				"company_phone" => GetMessage("wiz_company_phone_DEFAULT"),
				"company_phone_code" => GetMessage("wiz_company_phone_code_DEFAULT"),
				"company_icq" => $cIcq,
				"company_skype" => GetMessage("wiz_company_skype_DEFAULT"),
				"company_address" => $cAddr,
				"installDemoData" => COption::GetOptionString("main", "wizard_demo_data", "Y"),
			)
		);

		if (function_exists("ldap_connect") && IsModuleInstalled("ldap") && $_SERVER["PHP_SELF"] == "/index.php")
			$wizard->SetDefaultVar("allowLDAP", "Y");
	}

	function OnPostForm()
	{

		$wizard =& $this->GetWizard();

		if ($wizard->IsNextButtonClick())
		{
			$this->SaveFile("siteLogo", Array("extensions" => "png", "make_preview" => "Y"));
			if (strlen($wizard->GetVar("company_mail"))>0) {
			
				if (!check_email($wizard->GetVar("company_mail")))
				{
					$this->SetError(GetMessage("ERR_MAIL_ADMIN"), $wizard->GetVar("company_mail"));
				}
			}
			if (strlen($wizard->GetVar("siteName"))<=0)
				$this->SetError(GetMessage("ERR_FILL"));
			if (!$wizard->GetVar("WIZARD_IS_RERUN") &&
				(
					strlen($wizard->GetVar("company_address"))<=0 ||
					strlen($wizard->GetVar("company_phone_code"))<=0 ||
					strlen($wizard->GetVar("company_phone"))<=0 ||
					strlen($wizard->GetVar("company_mail"))<=0
				)
			)
				$this->SetError(GetMessage("ERR_FILL"));
			if($wizard->GetVar("siteLogo"))
			{
				if((int)$wizard->GetVar("siteLogo")>0)
				{
					$arFile = CFile::MakeFileArray($wizard->GetVar("siteLogo"));
					$path=$arFile['tmp_name'];
				}
				else
					$path=$wizard->GetVar("siteLogo");
				$ext=end(explode(".", $path));
				if($ext!='png')
				{
					$this->SetError(GetMessage("ERR_LOGO"));
				}
				else {
					$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
					$rsSites = CSite::GetByID($siteID);
					if ($arSite = $rsSites->Fetch())
						$siteDir = $arSite["DIR"];
					else
						$siteDir = '/';
					CWizardUtil::CopyFile($wizard->GetVar("siteLogo"), $siteDir."images/logo-bg.png");
				}
			}
			WizardServices::SetFilePermission(Array(SITE_ID, "/" ), Array("2" => "R"));
			COption::SetOptionString("main", "wizard_demo_data", "N");

			$allowLDAP = $wizard->GetVar("allowLDAP");
			if ($allowLDAP == "Y" && function_exists("ldap_connect") && IsModuleInstalled("ldap"))
				$wizard->SetCurrentStep("ldap_settings");
		}
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();

		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->content .= "<tr valign='top'><td style='padding-top:7px;'>".GetMessage("wiz_company_name")."<span style='color:red;'>*</span>:</td><td style='padding-top:7px;'>";
		$this->content .= $this->ShowInputField("text", "siteName", Array("id" => "site-name", "style" => "width:90%;font-family:sans-serif; font-size:110.01%"));
		$this->content .= '</td></tr>';
		
		$this->content .= "<tr valign='top'><td style='padding-top:7px;'>".GetMessage("wiz_company_url")."<span style='color:red;'>*</span>:</td><td style='padding-top:7px;'>";
		$this->content .= $this->ShowInputField("text", "server_name", Array("id" => "server_name", "style" => "width:90%;font-family:sans-serif; font-size:110.01%"));
		$this->content .= '</td></tr>';

		if (!$wizard->GetVar("WIZARD_IS_RERUN")) {
			$this->content .= "<tr valign='top'><td style='padding-top:7px;'>".GetMessage("wiz_company_mail")."<span style='color:red;'>*</span>:</td><td style='padding-top:7px;'>";
			$this->content .= $this->ShowInputField("text", "company_mail", Array("id" => "s_email", "style" => "width:90%;font-family:sans-serif; font-size:110.01%"));
			$this->content .= '</td></tr>';

			$this->content .= "<tr valign='top'><td style='padding-top:7px;'>".GetMessage("wiz_company_address")."<span style='color:red;'>*</span>:</td>";
			$this->content .= "<td style='padding-top:7px;'>".$this->ShowInputField("text", "company_address", Array("size" => "35"))."</td>";
			$this->content .= "</tr>";

			$this->content .= "<tr valign='top'><td style='padding-top:7px;'>".GetMessage("wiz_company_phone_code")."<span style='color:red;'>*</span>:</td>";
			$this->content .= "<td style='padding-top:7px;'>".$this->ShowInputField("text", "company_phone_code", Array("size" => "10"))."</td>";
			$this->content .= "</tr>";

			$this->content .= "<tr valign='top'><td style='padding-top:7px;'>".GetMessage("wiz_company_phone")."<span style='color:red;'>*</span>:</td>";
			$this->content .= "<td style='padding-top:7px;'>".$this->ShowInputField("text", "company_phone", Array("size" => "25"))."</td>";
			$this->content .= "</tr>";

			$this->content .= "<tr valign='top'><td style='padding-top:7px;'>".GetMessage("wiz_company_ICQ").":</td>";
			$this->content .= "<td style='padding-top:7px;'>".$this->ShowInputField("text", "company_icq", Array("size" => "35"))."</div></td>";
			$this->content .= "</tr>";

			$this->content .= "<tr valign='top'><td style='padding-top:7px;'>".GetMessage("wiz_company_skype").":</td>";
			$this->content .= "<td style='padding-top:7px;'>".$this->ShowInputField("text", "company_skype", Array("size" => "35"))."</div></td>";
			$this->content .= "</tr>";
		}

		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));

		$this->content .= "<tr valign='top'><td style='padding-top:7px;'>";
		$this->content .= '<label for="site-logo">'.GetMessage("wiz_company_logo")."</label></td><td style='padding-top:7px;'>";
		$this->content .= $this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "site-logo"));

		$this->content .= "<br />".CFile::ShowImage('/bitrix/templates/'.$wizard->GetVar("templateID").'_'.$siteID."/images/logo-bg.png", 200, 200, "border=0");
		$this->content .= '</td></tr>';

		// $this->content .= '<tr><td>&nbsp;</td></tr>';
		// if(WIZARD_IS_RERUN===true)
		// {
			// $this->content .= '<tr><td style="padding-top: 10px;">';
			// $this->content .= $this->ShowCheckboxField(
										// "set_demo", 
										// "Y", 
										// (array("id" => "set_demo", "style"=>'float:left;'))
									// );
			// $this->content .= '<label for="set_demo" style="float: left; width: 410px; margin-left: 5px;">'.GetMessage("wiz_set_demo").'</label><div style="clear:both;"></div>';			
			// $this->content .= '</td></tr>';
			// $this->content .= '<tr><td>&nbsp;</td></tr>';
		// }

		$this->content .= '<tr><td>';

		$this->content .= '</td></tr>';
		$this->content .= '</table>';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$formName = $wizard->GetFormName();
		$installCaption = $this->GetNextCaption();
		$nextCaption = GetMessage("NEXT_BUTTON");

		$this->content .= '
			<script type="text/javascript">
			function OnClickDemoData(checkbox)
			{
				if (!checkbox.checked)
					alert("'.GetMessage("wiz_galochka").'");
			}

			function OnClickAllowLdap(checkbox)
			{
				if (!checkbox)
					return;

				var button = document.getElementById("next-button-caption");
				if (button && !checkbox.disabled)
					button.innerHTML = (checkbox.checked ? "'.$nextCaption.'" : "'.$installCaption.'");
			}

			setTimeout(function() {OnClickAllowLdap(document.getElementById("allow-ldap"))}, 0);
			</script>
		';
	}
}

class DataInstallStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("data_install");
		$this->SetTitle(GetMessage("wiz_install_data"));
		$this->SetSubTitle(GetMessage("wiz_install_data"));
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		$serviceID = $wizard->GetVar("nextStep");
		$serviceStage = $wizard->GetVar("nextStepStage");

		if ($serviceID == "finish")
		{
			$wizard->SetCurrentStep("finish");
			return;
		}
			
		$arServiceSelected = $wizard->GetVar("services");
		if (!$arServiceSelected)
			$arServiceSelected = Array();
			
		if($_SERVER["PHP_SELF"] != "/index.php")
			$arServices = WizardServices::GetServices($_SERVER["DOCUMENT_ROOT"].$wizard->GetPath(), "/site/services/", Array("INSTALL_ONLY" => "Y"));
		else
			$arServices = WizardServices::GetServices($_SERVER["DOCUMENT_ROOT"].$wizard->GetPath(), "/site/services/", Array("SERVICES" => $arServiceSelected));

		if ($serviceStage == "skip")
			$success = true;
		else
			$success = $this->InstallService($serviceID, $serviceStage, $arServices[$serviceID]);

		list($nextService, $nextServiceStage, $stepsComplete, $status) = $this->GetNextStep($arServices, $serviceID, $serviceStage);

		if ($nextService == "finish")
		{
			$formName = $wizard->GetFormName();
			$response = "window.ajaxForm.StopAjax(); window.ajaxForm.SetStatus('100'); window.ajaxForm.Post('".$nextService."', '".$nextServiceStage."','".$status."');";
		}
		else
		{
			$arServiceID = array_keys($arServices);
			$lastService = array_pop($arServiceID);
			$stepsCount = $arServices[$lastService]["POSITION"];
			if (array_key_exists("STAGES", $arServices[$lastService]) && is_array($arServices[$lastService]))
				$stepsCount += count($arServices[$lastService]["STAGES"])-1;

			$percent = round($stepsComplete/$stepsCount * 100);
			$response = "window.ajaxForm.SetStatus('".$percent."'); window.ajaxForm.Post('".$nextService."', '".$nextServiceStage."','".$status."');";
		}

		die("[response]".$response."[/response]");
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();

		define("WIZARD_INSTALL_DEMO_TYPE", $wizard->GetVar("typeID"));

		$arServiceSelected = $wizard->GetVar("services");
		if (!$arServiceSelected)
			$arServiceSelected = Array();

		if($_SERVER["PHP_SELF"] != "/index.php")
			$arServices = WizardServices::GetServices($_SERVER["DOCUMENT_ROOT"].$wizard->GetPath(), "/site/services/", Array("INSTALL_ONLY" => "Y"));
		else
			$arServices = WizardServices::GetServices($_SERVER["DOCUMENT_ROOT"].$wizard->GetPath(), "/site/services/", Array("SERVICES" => $arServiceSelected));

		list($firstService, $stage, $status) = $this->GetFirstStep($arServices);

		$this->content .= '
			<table border="0" cellspacing="0" cellpadding="2" width="100%">
				<tr>
					<td colspan="2"><div id="status"></div></td>
				</tr>
				<tr>
					<td width="90%" height="10">
						<div style="border:1px solid #B9CBDF; width:100%;"><div id="indicator" style="height:10px; width:0%; background-color:#B9CBDF"></div></div>
					</td>
					<td width="10%">&nbsp;<span id="percent">0%</span></td>
				</tr>
			</table>
			<div id="wait" align=center>
			<br />
			<table width=200 cellspacing=0 cellpadding=0 border=0 style="border:1px solid #EFCB69" bgcolor="#FFF7D7">
				<tr>
					<td height=50 width="50" valign="middle" align=center><img src="'.$wizard->GetPath().'/images/wait.gif"></td>
					<td height=50 width=150>'.GetMessage("WIZARD_WAIT_WINDOW_TEXT").'</td>
				</tr>
			</table>
		</div><br />
			<br />
			<div id="error_container" style="display:none">
				<div id="error_notice"><span style="color:red;">'.GetMessage("INST_ERROR_OCCURED").'<br />'.GetMessage("INST_TEXT_ERROR").':</span></div>
				<div id="error_text"></div>
				<div><span style="color:red;">'.GetMessage("INST_ERROR_NOTICE").'</span></div>
				<div id="error_buttons" align="center">
				<br /><input type="button" value="'.GetMessage("INST_RETRY_BUTTON").'" id="error_retry_button" onclick="" />&nbsp;<input type="button" id="error_skip_button" value="'.GetMessage("INST_SKIP_BUTTON").'" onclick="" />&nbsp;</div>
			</div>

		'.$this->ShowHiddenField("nextStep", $firstService).'
		'.$this->ShowHiddenField("nextStepStage", $stage).'
		<iframe style="display:none;" id="iframe-post-form" name="iframe-post-form" src="javascript:\'\'"></iframe>';

		$wizard =& $this->GetWizard();

		$formName = $wizard->GetFormName();
		$NextStepVarName = $wizard->GetRealName("nextStep");


		$this->content .= '
		<script type="text/javascript">
			var ajaxForm = new CAjaxForm("'.$formName.'", "iframe-post-form", "'.$NextStepVarName.'");
			ajaxForm.Post("'.$firstService.'", "'.$stage.'", "'.$status.'");
		</script>';
	}

	function InstallService($serviceID, $serviceStage, $arServices)
	{   
		$wizard =& $this->GetWizard();
		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
		define("WIZARD_SITE_ID", $siteID);
		define("WIZARD_SITE_PATH", $_SERVER["DOCUMENT_ROOT"]);
		$rsSites = CSite::GetByID($siteID);
		if ($arSite = $rsSites->Fetch())
		{
			define("WIZARD_SITE_DIR", $arSite["DIR"]);	
			define("WIZARD_SITE_CHARSET", $arSite["CHARSET"]);	
		}
		else
		{
			define("WIZARD_SITE_DIR", "/");	
			define("WIZARD_SITE_CHARSET", "windows-1251");
		}
		$wizardPath = $wizard->GetPath();
		define("WIZARD_RELATIVE_PATH", $wizardPath);
		define("WIZARD_ABSOLUTE_PATH", $_SERVER["DOCUMENT_ROOT"].$wizardPath);

		$templatesPath = WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH."/site");
		$templateID = $wizard->GetVar("templateID");
		define("WIZARD_TEMPLATE_ID", $templateID);
		define("WIZARD_TEMPLATE_RELATIVE_PATH", $templatesPath."/".WIZARD_TEMPLATE_ID);
		define("WIZARD_TEMPLATE_ABSOLUTE_PATH", $_SERVER["DOCUMENT_ROOT"].WIZARD_TEMPLATE_RELATIVE_PATH);

		$themeID = $wizard->GetVar($templateID."_themeID");
		$arThemes = WizardServices::GetThemes(WIZARD_TEMPLATE_RELATIVE_PATH."/themes");
		define("WIZARD_THEME_ID", $themeID);
		define("WIZARD_THEME_RELATIVE_PATH", WIZARD_TEMPLATE_RELATIVE_PATH."/themes/".WIZARD_THEME_ID);
		define("WIZARD_THEME_ABSOLUTE_PATH", $_SERVER["DOCUMENT_ROOT"].WIZARD_THEME_RELATIVE_PATH);
		define("WIZARD_IS_RERUN", $wizard->GetVar("WIZARD_IS_RERUN"));

		$servicePath = WIZARD_RELATIVE_PATH."/site/services/".$serviceID;
		define("WIZARD_SERVICE_RELATIVE_PATH", $servicePath);
		define("WIZARD_SERVICE_ABSOLUTE_PATH", $_SERVER["DOCUMENT_ROOT"].$servicePath);

		$dbUsers = CGroup::GetList($by="id", $order="asc", Array("ACTIVE" => "Y"));
		while($arUser = $dbUsers->Fetch())
			define("WIZARD_".$arUser["STRING_ID"]."_GROUP", $arUser["ID"]);
		if(ToLower(WIZARD_SITE_CHARSET)=="windows-1251" && $serviceStage=="geocode.php")
		{
			$content = file_get_contents(WIZARD_SERVICE_ABSOLUTE_PATH."/geocode.php");
			$content=iconv('windows-1251', 'UTF-8', $content);
			file_put_contents(WIZARD_SERVICE_ABSOLUTE_PATH."/geocode.php",$content);	
		}	
		if(is_array($arServices["FILES"]) && !WIZARD_IS_RERUN)
		{
			foreach($arServices["FILES"] as $copyPaths)
			{
				$copyPaths["TO"] = rtrim($copyPaths["TO"], "/");
				CopyDirFiles(
					WIZARD_ABSOLUTE_PATH.$copyPaths["FROM"],
					WIZARD_SITE_PATH.WIZARD_SITE_DIR.$copyPaths["TO"],
					$rewrite = false, 
					$recursive = true
				);
			}
		}
		if (!file_exists(WIZARD_SERVICE_ABSOLUTE_PATH."/".$serviceStage))
			return false;

		if (LANGUAGE_ID != "en" && LANGUAGE_ID != "ru")
		{
			if (file_exists(WIZARD_SERVICE_ABSOLUTE_PATH."/lang/en/".$serviceStage))
				__IncludeLang(WIZARD_SERVICE_ABSOLUTE_PATH."/lang/en/".$serviceStage);
		}

		if (file_exists(WIZARD_SERVICE_ABSOLUTE_PATH."/lang/".LANGUAGE_ID."/".$serviceStage))
			__IncludeLang(WIZARD_SERVICE_ABSOLUTE_PATH."/lang/".LANGUAGE_ID."/".$serviceStage);

		@set_time_limit(3600);
		global $DB, $DBType, $APPLICATION, $USER, $CACHE_MANAGER;
		include(WIZARD_SERVICE_ABSOLUTE_PATH."/".$serviceStage);
	}
	
	function GetNextStep(&$arServices, &$currentService, &$currentStage)
	{
		$nextService = "finish";
		$nextServiceStage = "finish";
		$status = GetMessage("INSTALL_SERVICE_FINISH_STATUS");

		if (!array_key_exists($currentService, $arServices))
			return Array($nextService, $nextServiceStage, 0, $status); //Finish

		if ($currentStage != "skip" && array_key_exists("STAGES", $arServices[$currentService]) && is_array($arServices[$currentService]["STAGES"]))
		{
			$stageIndex = array_search($currentStage, $arServices[$currentService]["STAGES"]);
			if ($stageIndex !== false && isset($arServices[$currentService]["STAGES"][$stageIndex+1]))
				return Array(
					$currentService,
					$arServices[$currentService]["STAGES"][$stageIndex+1],
					$arServices[$currentService]["POSITION"]+ $stageIndex,
					$arServices[$currentService]["NAME"]
				); //Current step, next stage
		}

		$arServiceID = array_keys($arServices);
		$serviceIndex = array_search($currentService, $arServiceID);

		if (!isset($arServiceID[$serviceIndex+1]))
			return Array($nextService, $nextServiceStage, 0, $status); //Finish

		$nextServiceID = $arServiceID[$serviceIndex+1];
		$nextServiceStage = "index.php";
		if (array_key_exists("STAGES", $arServices[$nextServiceID]) && is_array($arServices[$nextServiceID]["STAGES"]) && isset($arServices[$nextServiceID]["STAGES"][0]))
			$nextServiceStage = $arServices[$nextServiceID]["STAGES"][0];

		return Array($nextServiceID, $nextServiceStage, $arServices[$nextServiceID]["POSITION"]-1, $arServices[$nextServiceID]["NAME"]); //Next service
	}

	function GetFirstStep(&$arServices)
	{
		foreach ($arServices as $serviceID => $arService)
		{
			$stage = "index.php";
			if (array_key_exists("STAGES", $arService) && is_array($arService["STAGES"]) && isset($arService["STAGES"][0]))
				$stage = $arService["STAGES"][0];
			return Array($serviceID, $stage, $arService["NAME"]);
		}

		return Array("service_not_found", "finish", GetMessage("INSTALL_SERVICE_FINISH_STATUS"));
	}
}

class FinishStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("finish");
		$this->SetNextStep("finish");
		$this->SetTitle(GetMessage("FINISH_STEP_TITLE"));
		$this->SetNextCaption(GetMessage("wiz_go"));
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
		$rsSites = CSite::GetByID($siteID);
		if ($arSite = $rsSites->Fetch())
			$siteDir = $arSite["DIR"];
		else
			$siteDir = '/';
		COption::SetOptionString("main", "wizard_solution", 'smedia.childshop','', $siteID);
		$wizard->SetFormActionScript($siteDir."?finish");

		$this->CreateNewIndex($siteDir);
		$this->content .= GetMessage("FINISH_STEP_CONTENT");
	}

	function CreateNewIndex($siteDir)
	{
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"].$siteDir."_index.php",
			$_SERVER["DOCUMENT_ROOT"].$siteDir."index.php",
			$rewrite = true,
			$recursive = true,
			$delete_after_copy = true
		);
	}
}

?>