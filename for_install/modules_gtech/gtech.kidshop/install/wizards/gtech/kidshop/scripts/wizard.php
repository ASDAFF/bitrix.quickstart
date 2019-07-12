<?defined("B_PROLOG_INCLUDED")&&B_PROLOG_INCLUDED or die();

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client.php");
require_once("utils.php");

class AWelcomeStep extends CWizardStep
{
	var $package,$wizard,$updateInfo;

	function InitStep()
	{
		parent::InitStep();
		$this->wizard = $this->GetWizard();$this->package = $this->wizard->GetPackage();

		$this->wizard->SetFirstStep('welcome');
		$this->SetStepID("welcome");
		$this->SetTitle(GetMessage("WIZ_WELCOME_TITLE"));
		$this->SetSubTitle(GetMessage("WIZ_WELCOME_SUBTITLE"));
		$this->content .= GetMessage("WIZ_WELCOME_CONTENT");
		$this->SetNextStep("select_site");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));

		$this->updateInfo = ArealRealty_SiteWizard::GetUpdateInfo();
	}

	function OnPostForm()
	{
		$wizard = $this->GetWizard();
		// если кликнута Отмена - то пустим пользователя на завершение, иначе задержим на этом шаге
		if (!$wizard->IsCancelButtonClick()) {

			// направим пользователя далее, только если редакция его продукта удовлетворяет требованиям
			if (!$this->updateInfo or in_array($this->updateInfo["CLIENT"][0]["@"]["LICENSE"],ArealRealty_SiteWizard::$unallowed_editions)) {
				$this->SetError(GetMessage("WIZ_WELCOME_STEP_EDITION_ERROR"));
			}
		}
	}

	function ShowStep()
	{

		if (!$this->updateInfo or in_array($this->updateInfo["CLIENT"][0]["@"]["LICENSE"],ArealRealty_SiteWizard::$unallowed_editions)) {
			$this->content = GetMessage("WIZ_WELCOME_STEP_ALLOWED_EDITIONS");
			$this->content .= '<br />Редакция вашего продукта: <b>'.$this->updateInfo["CLIENT"][0]["@"]["LICENSE"].'</b>';
		}
	}
}

class BSelectSiteStep extends CSelectSiteWizardStep {
	function InitStep()
	{
		parent::InitStep();
		$this->SetPrevStep("welcome");
	}
}

class CSelectTemplateStep extends CSelectTemplateWizardStep {
	function INitStep()
	{
		parent::InitStep();
		$this->SetNextStep('site_settings');
	}
}

class DSiteSettingsStep extends CSiteSettingsWizardStep {

	function InitStep()
	{
		parent::InitStep();
		$wizard = $this->GetWizard();

		$this->SetTitle(GetMessage("wiz_settings"));
		$this->SetNextStep("data_install");
		$this->SetNextCaption(GetMessage("wiz_install"));

		// set default vars for  templates
		$def_template_vars = array(
			"COMPANY_NAME" =>
				COption::GetOptionString("main", "site_name", GetMessage("WIZ_COMPANY_NAME_DEFAULT")),
			"COMPANY_PHONECODE" =>
				COption::GetOptionString("main", "wizard_company_phonecode", GetMessage("WIZ_COMPANY_PHONECODE_DEFAULT")),
			"COMPANY_PHONE" =>
				COption::GetOptionString("main", "wizard_company_phone", GetMessage("WIZ_COMPANY_PHONE_DEFAULT")),
			"COMPANY_FAX" =>
				COption::GetOptionString("main", "wizard_company_fax", GetMessage("WIZ_COMPANY_FAX_DEFAULT")),
			"COMPANY_ADDRESS" =>
				COption::GetOptionString("main", "wizard_company_address", GetMessage("WIZ_COMPANY_ADDRESS_DEFAULT")),
			"COMPANY_METRO" =>
				COption::GetOptionString("main", "wizard_company_metro", GetMessage("WIZ_COMPANY_METRO_DEFAULT")),
		);
		$wizard->SetDefaultVars($def_template_vars);

		$def_site_vars = array(
			'SITE_NAME' => GetMessage("WIZ_SITE_NAME_DEFAULT"),
			'SITE_EMAIL' => COption::GetOptionString('main','email_from'),
			'SITE_DOMAIN' => COption::GetOptionString('main','server_name'),
		);
		$wizard->SetDefaultVars($def_site_vars);

		$def_replace_vars = array(
			'MAGIC_REPLACE' => serialize(array('THIS_CONTENT_DOESNOT_ALLOWED' => 'THIS_CONTENT_DOESNOT_ALLOWED')),
		);
		$wizard->SetDefaultVars($def_replace_vars);

		$siteID = $wizard->GetVar("siteID");


		$wizard->SetDefaultVars(
			Array(
				"installDemoData" => COption::GetOptionString("main", "wizard_demo_data", "Y")
			)
		);


		// Определяем ключ для карты от текущего сайта
		/*$MAP_KEY = '';
		$strMapKeys = COPtion::GetOptionString('fileman', 'map_yandex_keys');

		$domain = $wizard->GetVar('SITE_DOMAIN',true);
		$strDomain = strlen($domain) ? $domain : $_SERVER['HTTP_HOST'];
		$wwwPos = strpos($strDomain, 'www.');
		if ($wwwPos === 0)
			$strDomain = substr($strDomain, 4);

		if ($strMapKeys)
		{
			$arMapKeys = unserialize($strMapKeys);

			if (array_key_exists($strDomain, $arMapKeys))
				$MAP_KEY = $arMapKeys[$strDomain];
		}
		$wizard->SetDefaultVar('YANDEX_MAP_KEY',$MAP_KEY);*/
	}

	function ShowStep()
	{
		$wizard = $this->GetWizard();$package = $wizard->GetPackage();

		$style_text = array('size' => '0', 'style' => 'width:95%');

		$this->content .= GetMessage("WIZ_CUSTOMIZE_DATA_HINT")."<br /><br />";

		$this->content .= '<table style="width:100%">';
		// COMPANY NAME
		$this->content .= '<tr><td>';
		$this->content .= '<label for="COMPANY_NAME">'.GetMessage("WIZ_COMPANY_NAME").'</label><br />';
		$this->content .= $this->ShowInputField("text", "COMPANY_NAME", $style_text + array("id" => "COMPANY_NAME"));
		$this->content .= '</td></tr>';
		// COMPANY PHONECODE
		$this->content .= '<tr><td>';
		$this->content .= '<label for="COMPANY_PHONECODE">'.GetMessage("WIZ_COMPANY_PHONECODE").'</label><br />';
		$this->content .= $this->ShowInputField("text", "COMPANY_PHONECODE", $style_text + array("id" => "COMPANY_PHONECODE"));
		$this->content .= '</td></tr>';
		// COMPANY PHONE
		$this->content .= '<tr><td>';
		$this->content .= '<label for="COMPANY_PHONE">'.GetMessage("WIZ_COMPANY_PHONE").'</label><br />';
		$this->content .= $this->ShowInputField("text", "COMPANY_PHONE", $style_text + array("id" => "COMPANY_PHONE"));
		$this->content .= '</td></tr>';
		// COMPANY FAX
		$this->content .= '<tr><td>';
		$this->content .= '<label for="COMPANY_FAX">'.GetMessage("WIZ_COMPANY_FAX").'</label><br />';
		$this->content .= $this->ShowInputField("text", "COMPANY_FAX", $style_text + array("id" => "COMPANY_FAX"));
		$this->content .= '</td></tr>';
		// COMPANY ADDRESS
		$this->content .= '<tr><td>';
		$this->content .= '<label for="COMPANY_ADDRESS">'.GetMessage("WIZ_COMPANY_ADDRESS").'</label><br />';
		$this->content .= $this->ShowInputField("text", "COMPANY_ADDRESS", $style_text + array("id" => "COMPANY_ADDRESS"));
		$this->content .= '</td></tr>';
		// COMPANY METRO
		$this->content .= '<tr><td>';
		$this->content .= '<label for="COMPANY_METRO">'.GetMessage("WIZ_COMPANY_METRO").'</label><br />';
		$this->content .= $this->ShowInputField("text", "COMPANY_METRO", $style_text + array("id" => "COMPANY_METRO"));
		$this->content .= '</td></tr>';

		// COMPANY LOGO
		$fileID = COption::GetOptionString("main", "wizard_site_logo", "");
		if (intval($fileID) > 0)
			$wizard->SetVar("COMPANY_LOGO", $fileID);
		$companyLogo = $wizard->GetVar("COMPANY_LOGO");

		$this->content .= '<tr><td>';
		$this->content .= '<label for="COMPANY_LOGO">'.GetMessage("WIZ_COMPANY_LOGO").'</label><br />';
		$this->content .= $this->ShowFileField("COMPANY_LOGO", array("max_file_size" => 1.5*1024*1024, "show_file_info" => "N"));
		$this->content .= "<br />".CFile::ShowImage($companyLogo, 200, 200, "border=0", "", true);
		$this->content .= "</td></tr>";

		// Some site settings
		// SITE DOMAIN
		$this->content .= '<tr><td>';
		$this->content .= '<label for="SITE_DOMAIN">'.GetMessage('WIZ_SITE_DOMAIN').'</label><br />';
		$this->content .= $this->ShowInputField("text", "SITE_DOMAIN", $style_text + array("id" => "SITE_DOMAIN"));
		$this->content .= '</td></tr>';
		// SITE EMAIL
		$this->content .= '<tr><td>';
		$this->content .= '<label for="SITE_EMAIL">'.GetMessage('WIZ_SITE_EMAIL').'</label><br />';
		$this->content .= $this->ShowInputField("text", "SITE_EMAIL", $style_text + array("id" => "SITE_EMAIL"));
		$this->content .= '</td></tr>';

		// YANDEX MAP
		$this->content .= '<tr><td>';
		$this->content .= '<label for="YANDEX_MAP_KEY">'.GetMessage('WIZ_YANDEX_MAP_KEY').'</label><br />';
		$this->content .= $this->ShowInputField("text", "YANDEX_MAP_KEY", $style_text + array("id" => "YANDEX_MAP_KEY"));
		$this->content .= '</td></tr>';

		$this->content .= '<tr><td>';
		$this->content .= GetMessage("WIZ_YANDEX_MAP_CONTENT");
		$this->content .= '</td></tr>';
		$this->content .= "</table>";

/*
		$this->content .= "<p>".GetMessage("WIZ_YANDEX_MAP_CONTENT")."</p>";
		$this->content .= "<br /><br />";
		$this->content .= "
		<table>
			<tr>
				<td>".GetMessage('WIZ_STEP_SELECT_SERVICE_MAPS_YANDEXMAP_LABEL')."</td>
				<td>".$this->ShowInputField("text", "YANDEX_MAP_KEY", $style_text + array("size" => "35"))."</td>
			</tr>
		</table>
		";
*/

/*
		$wizard = $this->GetWizard();

		$wizard->SetVar("siteName", COption::GetOptionString("main", "site_personal_name", GetMessage("wiz_name"), $wizard->GetVar("siteID")));
		$wizard->SetVar("copyright", COption::GetOptionString("main", "site_copyright", GetMessage("wiz_copyright"), $wizard->GetVar("siteID")));
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-name">'.GetMessage("wiz_company_name").'</label><br />';
		$this->content .= $this->ShowInputField("text", "siteName", Array("id" => "site-name", "style" => "width:90%"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-copyright">'.GetMessage("wiz_company_copyright").'</label><br />';
		$this->content .= $this->ShowInputField("text", "copyright", Array("id" => "site-copyright", "style" => "width:90%"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td>&nbsp;</td></tr>';

		$firstStep = COption::GetOptionString("main", "wizard_first" . substr($wizard->GetID(), 7)  . "_" . $wizard->GetVar("siteID"), false, $wizard->GetVar("siteID"));
		if($firstStep == "Y")
		{
			$this->content .= '<tr><td style="padding-bottom:3px;">';
			$this->content .= $this->ShowCheckboxField(
									"installDemoData",
									"Y",
									(array("id" => "installDemoData"))
								);
			$this->content .= '<label for="install-demo-data">'.GetMessage("wiz_structure_data").'</label><br />';
			$this->content .= '</td></tr>';

			$this->content .= '<tr><td>&nbsp;</td></tr>';
		}
		else
		{
			$this->content .= $this->ShowHiddenField("installDemoData","Y");

		}
		$this->content .= '</table>';
*/

		$formName = $wizard->GetFormName();
		$installCaption = $this->GetNextCaption();
		$nextCaption = GetMessage("NEXT_BUTTON");
	}
}

class EDataInstallStep extends CDataInstallWizardStep
{
	function InitStep()
	{
		parent::InitStep();
		$wizard = $this->GetWizard();
		$this->SaveFile("company_logo", Array("max_file_size" => 1.5*1024*1024, "extensions" => "gif,jpg,jpeg,png", "max_height" => 87, "max_width" => 87, "make_preview" => "Y"));
		COption::SetOptionString("main", "wizard_site_logo", "");


		$LID = $wizard->GetVar("siteID",true);
		$site = CSite::GetByID($LID)->Fetch();
		// get magic replace
		$arReplace = unserialize($wizard->GetVar('MAGIC_REPLACE',true));

		$email = explode('@',$wizard->GetVar('SITE_EMAIL'));
		$email_user = strlen($email[0]) ? $email[0] : 'info';
		$email_domain = strlen($email[1]) ? $email[1] : $_SERVER['HTTP_HOST'];
		$email = $email_user.'@'.$email_domain;


		$arReplace["COMPANY_NAME"] = $wizard->GetVar("COMPANY_NAME");
		$arReplace["SITE_EMAIL"] = $email;
		$arReplace["SITE_EMAIL_USER"] = $email_user;
		$arReplace["SITE_EMAIL_DOMAIN"] = $email_domain;
		$arReplace["COMPANY_PHONECODE"] = $wizard->GetVar("COMPANY_PHONECODE");
		$arReplace["COMPANY_PHONE"] = $wizard->GetVar("COMPANY_PHONE");
		$arReplace["COMPANY_FAX"] = $wizard->GetVar("COMPANY_FAX");
		$arReplace["COMPANY_ADDRESS"] = $wizard->GetVar("COMPANY_ADDRESS");
		$arReplace["COMPANY_METRO"] = $wizard->GetVar("COMPANY_METRO");

		$wizard->SetVar('SITE_EMAIL_USER',$email_user);
		$wizard->SetVar('SITE_EMAIL_DOMAIN',$email_domain);

		// disable user registration
		COption::SetOptionString('main','new_user_registration','N');
		// set menu types for component sitemap
		COption::SetOptionString('main','map_top_menu_type','left');
		COption::SetOptionString('main','map_left_menu_type','left_2');
		// enable authorize/registration components 2.0
		COption::SetOptionString('main','auth_comp2','Y');

		// save magic replace
		$wizard->SetVar('MAGIC_REPLACE',serialize($arReplace));
	}

	function ShowStep()
	{
		parent::ShowStep();
		$wizard = $this->GetWizard();
	}
	function CorrectServices(&$arServices)
	{
		$wizard = $this->GetWizard();
		if($wizard->GetVar("installDemoData") != "Y")
		{
		}
	}
}

class FinishStep extends CFinishWizardStep
{
}
?>