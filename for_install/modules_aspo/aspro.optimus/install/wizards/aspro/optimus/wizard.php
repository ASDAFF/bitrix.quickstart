<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");?>
<?include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards/aspro/optimus/css/styles.css");?>
<script>
	<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/wizards/aspro/optimus/js/jquery-1.8.3.min.js");?>
	<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/wizards/aspro/optimus/js/jquery.keyboard.js");?>
	function setWizardBackgroundColor(theme){
		window.console&&console.log(theme);
		switch (theme) {
			case "YELLOW":
			case "yellow":
				$(".instal-bg").css("backgroundColor", "#ffad00");
				break;
			case "ORANGE":
			case "orange":
				$(".instal-bg").css("backgroundColor", "#ff6d00");
				break;
			case "RED":
			case "red":
				$(".instal-bg").css("backgroundColor", "#de002b");
				break;
			case "MAGENTA":
			case "magenta":
				$(".instal-bg").css("backgroundColor", "#b82945");
				break;
			case "ORCHID":
			case "orchid":
				$(".instal-bg").css("backgroundColor", "#d75cb6");
				break;
			case "NAVY":
			case "navy":
				$(".instal-bg").css("backgroundColor", "#006dca");
				break;
			case "BLUE":
			case "blue":
				$(".instal-bg").css("backgroundColor", "#01aae3");
				break;
			case "GREEN_SEA":
			case "green_sea":
				$(".instal-bg").css("backgroundColor", "#01b1af");
				break;
			case "GREEN":
			case "green":
				$(".instal-bg").css("backgroundColor", "#009f4f");
				break;
			case "IRISH_GREEN":
			case "irish_green":
				$(".instal-bg").css("backgroundColor", "#6db900");
				break;
			case "CUSTOM":
			case "custom":
				$(".instal-bg").css("backgroundColor", "#006dca");
				break;
			default:
				$(".instal-bg").css("backgroundColor", "#006dca");
				break;
		}
	}
	$(document).ready(function(){
		$("body").keyboard('ctrl+shift+f', { preventDefault : true }, function () { document.location.href = document.location.href+"&fast=y"; } )
	});
</script>
<?if(isset($_REQUEST["fast"]) && (strtolower($_REQUEST["fast"])=="y")):?>
	<script>
		$(document).ready(function(){
			if($("input#installDemoData").length){
				$("input#installDemoData").attr("checked", "checked");
			}
			if($(".wizard-next-button").length){
				if($(".wizard-next-button").attr("value")!="Перейти на сайт"){
					$(".wizard-next-button").click();
				}
			}
		});
	</script>
<?endif;?>
<?

function setLastWritedIblockParams($id = false, $type = false, $code = false){
	$_SESSION["WIZARD_LAST_WRITTED_IBLOCK"]["ID"] = ($id && intVal($id)) ? intVal($id) : false;
	$_SESSION["WIZARD_LAST_WRITTED_IBLOCK"]["CODE"] = ($code && trim($code)) ? trim($code) : false;
	$_SESSION["WIZARD_LAST_WRITTED_IBLOCK"]["TYPE"] = ($type && trim($type)) ? trim($type) : false;
	
	if(intVal($_SESSION["WIZARD_LAST_WRITTED_IBLOCK"]["ID"]) || trim($_SESSION["WIZARD_LAST_WRITTED_IBLOCK"]["TYPE"]) || trim($_SESSION["WIZARD_LAST_WRITTED_IBLOCK"]["CODE"])){
		return true;
	}
	else{
		return false;
	}
}

function getLastWritedIblockParams(){
	$arResult = array(
		"ID" => ($_SESSION["WIZARD_LAST_WRITTED_IBLOCK"]["ID"] ? intVal($_SESSION["WIZARD_LAST_WRITTED_IBLOCK"]["ID"]) : false), 
		"TYPE" => ($_SESSION["WIZARD_LAST_WRITTED_IBLOCK"]["TYPE"] ? trim($_SESSION["WIZARD_LAST_WRITTED_IBLOCK"]["TYPE"]) : false),
		"CODE" => ($_SESSION["WIZARD_LAST_WRITTED_IBLOCK"]["CODE"] ? trim($_SESSION["WIZARD_LAST_WRITTED_IBLOCK"]["CODE"]) : false),
	);
	foreach($arResult as $key => $value){
		if(!$value){
			unset($arResult[$key]);
		}
	}
	return count($arResult) ? $arResult : false;
}

function clearLastWritedIblockParams(){
	unset($_SESSION["WIZARD_LAST_WRITTED_IBLOCK"]);
	return true;
}

class SelectSiteStep extends CSelectSiteWizardStep{
	function InitStep(){
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "optimus";
	}
}

class SelectTemplateStep extends CSelectTemplateWizardStep{
	function InitStep(){
		$wizard =& $this->GetWizard();
		
		$this->SetStepID("select_template");
		$this->SetTitle(GetMessage("SELECT_TEMPLATE_TITLE"));
		$this->SetSubTitle(GetMessage("SELECT_TEMPLATE_SUBTITLE"));
		
		if(!defined("WIZARD_DEFAULT_SITE_ID")){
			$this->SetPrevStep("select_site");
			$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
		}
		else{
			$wizard =& $this->GetWizard(); 
			$wizard->SetVar("siteID", WIZARD_DEFAULT_SITE_ID); 
		}

		$this->SetNextStep("select_theme");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$wizard->SetDefaultVars(Array("templateID" => "optimus"));
	}

	function OnPostForm(){
		$wizard =& $this->GetWizard();
		
		$proactive = COption::GetOptionString("statistic", "DEFENCE_ON", "N");
		if($proactive == "Y"){
			COption::SetOptionString("statistic", "DEFENCE_ON", "N");
			$wizard->SetVar("proactive", "Y");
		}
		else{
			$wizard->SetVar("proactive", "N");
		}

		if($wizard->IsNextButtonClick()){
			$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
			$arTemplates = WizardServices::GetTemplates($templatesPath);					
			$templateID = $wizard->GetVar("templateID");

			if(!array_key_exists($templateID, $arTemplates))
				$this->SetError(GetMessage("wiz_template"));
				
		}
	}

	
	function ShowStep(){
		if(!CModule::IncludeModule("aspro.optimus")){
			$this->content .= "<p style='color:red'>".GetMessage("WIZ_NO_MODULE_")."</p>";
			?>
			<script type="text/javascript">
			$(document).ready(function() {
				$('.wizard-next-button').remove();
			});
			</script>
			<?
		}
		else{
			$wizard =& $this->GetWizard();

			$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
			$arTemplates = WizardServices::GetTemplates($templatesPath);
			
			if (empty($arTemplates))
				return;
				
			$templateID = $wizard->GetVar("templateID");
			if(isset($templateID) && array_key_exists($templateID, $arTemplates)){
			
				$defaultTemplateID = $templateID;
				$wizard->SetDefaultVar("templateID", $templateID);
				
			} else {
			
				$defaultTemplateID = COption::GetOptionString("main", "wizard_template_id", "", $wizard->GetVar("siteID")); 
				if (!(strlen($defaultTemplateID) > 0 && array_key_exists($defaultTemplateID, $arTemplates)))
				{
					if (strlen($defaultTemplateID) > 0 && array_key_exists($defaultTemplateID, $arTemplates))
						$wizard->SetDefaultVar("templateID", $defaultTemplateID);
					else
						$defaultTemplateID = "";
				}
			}

			global $SHOWIMAGEFIRST;
			$SHOWIMAGEFIRST = true;
			
			$this->content .= '<div id="solutions-container" class="inst-template-list-block">';
			foreach ($arTemplates as $templateID => $arTemplate)
			{
				if ($defaultTemplateID == "")
				{
					$defaultTemplateID = $templateID;
					$wizard->SetDefaultVar("templateID", $defaultTemplateID);
				}
			
				$this->content .= '<div class="inst-template-description">';
				$this->content .= $this->ShowRadioField("templateID", $templateID, Array("id" => $templateID, "class" => "inst-template-list-inp"));
				if ($arTemplate["SCREENSHOT"] && $arTemplate["PREVIEW"])
					$this->content .= CFile::Show2Images($arTemplate["PREVIEW"], $arTemplate["SCREENSHOT"], 150, 150, ' class="inst-template-list-img"');
				else
					$this->content .= CFile::ShowImage($arTemplate["SCREENSHOT"], 150, 150, ' class="inst-template-list-img"', "", true);

				$this->content .= '<label for="'.$templateID.'" class="inst-template-list-label">'.$arTemplate["NAME"].'<p>'.$arTemplate["DESCRIPTION"].'</p></label>';
				$this->content .= "</div>";

			}
			
			$this->content .= '</div>'; 
		}
	}
}

class SelectThemeStep extends CSelectThemeWizardStep{
	function InitStep(){
		$this->SetStepID("select_theme");
		$this->SetTitle(GetMessage("SELECT_THEME_TITLE"));
		$this->SetSubTitle(GetMessage("SELECT_THEME_SUBTITLE"));
		$this->SetPrevStep("select_template");
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
		$this->SetNextStep("site_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
	}

	function OnPostForm(){
		$wizard =& $this->GetWizard();
		if($wizard->IsNextButtonClick()){
			$templateID = $wizard->GetVar("templateID");
			$themeVarName = $templateID."_themeID";
			$themeID = $wizard->GetVar($themeVarName);
			$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
			$arThemes = WizardServices::GetThemes($templatesPath."/".$templateID."/themes");
		
			if (!array_key_exists($themeID, $arThemes)){
				$this->SetError(GetMessage("wiz_template_color"));
			}
		}
	}

	function ShowStep(){
		$wizard =& $this->GetWizard();
		$templateID = $wizard->GetVar("templateID");
		$siteID = $wizard->GetVar("siteID");
		$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
		if(!$arThemes = WizardServices::GetThemes($templatesPath."/".$templateID."/themes")){
			return;
		}
		
		$this->sortThemes($arThemes);
		$themeVarName = $templateID."_themeID";

		$ThemeID = strtolower(COption::GetOptionString("aspro.optimus", "COLOR_THEME", 'NAVY', $siteID));

		if(!strlen($ThemeID)){
			$ThemeID = "navy";
		}
		if(strlen($ThemeID) && array_key_exists($ThemeID, $arThemes)){
			$defaultThemeID = $ThemeID;
			$wizard->SetDefaultVar($themeVarName, $ThemeID);
		}
		
		$this->content = 
		'<script type="text/javascript">	
		function SelectTheme(element, solutionId, imageUrl)
		{
			setWizardBackgroundColor(solutionId);
			
			var backgroundContainer = document.getElementsByClassName("instal-bg");
			
			var container = document.getElementById("solutions-container");
			var anchors = container.getElementsByTagName("SPAN");
			for (var i = 0; i < anchors.length; i++)
			{
				if (anchors[i].parentNode == container)
					anchors[i].className = "inst-template-color";
			}
			element.className = "inst-template-color inst-template-color-selected";
			var hidden = document.getElementById("selected-solution");
			if (!hidden) 
			{
				hidden = document.createElement("INPUT");
				hidden.type = "hidden"
				hidden.id = "selected-solution";
				hidden.name = "selected-solution";
				container.appendChild(hidden);
			}
			hidden.value = solutionId;

			var preview = document.getElementById("solution-preview");
			if (!imageUrl)
				preview.style.display = "none";
			else 
			{
				document.getElementById("solution-preview-image").src = imageUrl;
				preview.style.display = "";
			}
		}
		</script>'.
		'<div id="html_container">'.
		'<div class="inst-template-color-block" id="solutions-container"><style>#solution-preview-image{width:537px; height: 571px;}</style>';
		$ii = 0;
		$arDefaultTheme = array(); 
		foreach($arThemes as $themeID => $arTheme){
			if($themeID == "custom" || $themeID == "CUSTOM"){
				continue;
			}
			if($defaultThemeID == ""){
				$defaultThemeID = $themeID;
				$wizard->SetDefaultVar($themeVarName, $defaultThemeID);
			}
			if($defaultThemeID == $themeID){
				$arDefaultTheme = $arTheme;
			}
			++$ii;

			$this->content .= '
				<span themeName="'.$themeID.'" class="inst-template-color'.($defaultThemeID == $themeID ? " inst-template-color-selected" : "").'" ondblclick="SubmitForm(\'next\');"  onclick="SelectTheme(this, \''.$themeID.'\', \''.$arTheme["SCREENSHOT"].'\');">
					<span class="inst-templ-color-img">'.CFile::ShowImage($arTheme["PREVIEW"], 70, 64, ' border="0" class="solution-image"').'</span>
					<span class="inst-templ-color-name">'.$arTheme["NAME"].'</span>
				</span>';
		}
		
		$this->content .= '<script type="text/javascript">	$(document).ready(function(){setWizardBackgroundColor($(".inst-template-color-block .inst-template-color.inst-template-color-selected").attr("themeName"));});</script>';
		$this->content .= $this->ShowHiddenField($themeVarName, $defaultThemeID, array("id" => "selected-solution"));  
		$this->content .= 
			'</div>'.
			'<div id="solution-preview">'.
				'<b class="r3"></b><b class="r1"></b><b class="r1"></b>'.
					'<div class="solution-inner-item">'.
						CFile::ShowImage($arDefaultTheme["SCREENSHOT"], 682, 625, ' border="0" id="solution-preview-image"').
					'</div>'.
				'<b class="r1"></b><b class="r1"></b><b class="r3"></b>'.
			'</div>'.
		'</div>';
	}
	
	function sortThemes(&$arThemes){
		function cmpSort($t1, $t2){
			return ($t1["SORT"] > $t2["SORT"] ? 1 : ($t1["SORT"] < $t2["SORT"] ? -1 : 0));
		}
		uasort($arThemes, "cmpSort");
	}
}

class SiteSettingsStep extends CSiteSettingsWizardStep{
	function InitStep(){
		if(CModule::IncludeModule("aspro.optimus")){
			$wizard =& $this->GetWizard();
			$wizard->solutionName = "optimus";
			parent::InitStep();
			$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
			$this->SetTitle(GetMessage("WIZ_STEP_SITE_SET"));
			$this->SetNextStep("data_install");
			$siteID = $wizard->GetVar("siteID");
			$obSite = new CSite;
			$arSite = $obSite->GetByID($siteID)->Fetch();
			$themeID = $wizard->GetVar($themeVarName);
		   

			/*
			if(!file_exists($siteLogo = WIZARD_SITE_PATH."include/logo.png")){
				$siteLogo = "/bitrix/wizards/aspro/optimus/site/templates/optimus/themes/".$themeID."/images/logo.png";
			}
			*/
			
			if(LANGUAGE_ID != "ru"){
				$this->SetNextStep("pay_system");
			}
			else{
				$this->SetNextStep("shop_settings");
			}
			
			// net multisaitovosti na kshope dlyay soc setej ( X3
			$shopVk = $shopOdnoklassniki = $shopFacebook = $shopTwitter = $shopMailru = $shopInstagram = $shopYouTube = $shopGoogle = false;
			
			$wizard->SetDefaultVars(
				Array(
					//"siteLogo" => $siteLogo,
					"siteLogoSet" => false,
					"siteNameSet" => true,
					"siteName" => (strlen($arSite["SITE_NAME"]) ? $arSite["SITE_NAME"] : (strlen($arSite["NAME"]) ? $arSite["NAME"] : GetMessage("WIZ_COMPANY_NAME_DEF"))),
					"siteTelephone" =>  GetMessage("WIZ_COMPANY_TELEPHONE_DEF"),
					"siteTelephone2" => GetMessage("WIZ_COMPANY_TELEPHONE_DEF2"),
					"siteCopy" => str_replace('<?=date("Y")?> &copy; ', '', $this->GetFileContent(WIZARD_SITE_PATH."include/copyright.php", GetMessage("WIZ_COMPANY_COPY_DEF"))),
					"siteEmail" => strip_tags($this->GetFileContent(WIZARD_SITE_PATH."include/email.php", GetMessage("WIZ_COMPANY_EMAIL_DEF"))),
					//"siteSkype" => $this->GetFileContent(WIZARD_SITE_PATH."include/site-skype.php", GetMessage("WIZ_COMPANY_SKYPE_DEF")),
					"siteAddress" => $this->GetFileContent(WIZARD_SITE_PATH."include/address.php", GetMessage("WIZ_COMPANY_ADDRESS_DEF")),
					"siteSchedule" => $this-> GetFileContent(WIZARD_SITE_PATH."include/schedule.php", GetMessage("WIZ_COMPANY_SCHEDULE_DEF")),
					"shopVk" => (strlen($shopVk) ? $shopVk : GetMessage("WIZ_SHOP_VK_DEF")),
					"shopOdnoklassniki" => (strlen($shopOdnoklassniki) ? $shopOdnoklassniki : GetMessage("WIZ_SHOP_ODNOKLASSNIKI_DEF")),
					"shopFacebook" => (strlen($shopFacebook) ? $shopFacebook : GetMessage("WIZ_SHOP_FACEBOOK_DEF")),
					"shopTwitter" => (strlen($shopTwitter) ? $shopTwitter : GetMessage("WIZ_SHOP_TWITTER_DEF")),
					"shopMailru" => (strlen($shopMailru) ? $shopMailru : GetMessage("WIZ_SHOP_MAILRU_DEF")),
					"shopInstagram" => (strlen($shopInstagram) ? $shopInstagram : GetMessage("WIZ_SHOP_INSTAGRAM_DEF")),
					"shopYouTube" => (strlen($shopYouTube) ? $shopYouTube : GetMessage("WIZ_SHOP_YOUTUBE_DEF")),
					"shopGoogle" => (strlen($shopGoogle) ? $shopGoogle : GetMessage("WIZ_SHOP_GOOGLE_DEF")),
					//"shopLiveJournal" => (strlen($shopLiveJournal) ? $shopLiveJournal : GetMessage("WIZ_SHOP_LIVEJOURNAL_DEF")),
					"siteMetaDescription" => GetMessage("wiz_site_desc"),
					"siteMetaKeywords" => GetMessage("wiz_keywords"),
				)
			);
		}
	}

	function ShowStep(){
		if (!CModule::IncludeModule("aspro.optimus")){
			$this->content .= "<p style='color:red'>".GetMessage("WIZ_NO_MODULE_")."</p>";
			?>
			<script type="text/javascript">
			$(document).ready(function() {
				$('.wizard-next-button').remove();
			});
			</script>
			<?
		}
		else{
			$wizard =& $this->GetWizard();
			$templateID = $wizard->GetVar("templateID");
			$themeVarName = $templateID."_themeID";
			$themeID = $wizard->GetVar($themeVarName);

			$this->content .='<script type="text/javascript">$(document).ready(function(){setWizardBackgroundColor("'.$themeID.'");});</script>';
			
			$this->content .= '<div class="wizard-input-form">';
			if($wizard->GetVar('siteNameSet', true)){
				$this->content .= '
				<div class="wizard-input-form-block">
					<label for="siteName" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_NAME").'</label><br />'
					.$this->ShowInputField('text', 'siteName', array("class"=>"wizard-field", "id" => "siteName")).'
				</div>';
			}
			
			if($wizard->GetVar('siteLogoSet', true)){
				$siteLogo = $wizard->GetVar("siteLogo", true);
		
				$this->content .= '
				<div class="wizard-input-form-block">
					<label for="siteLogo" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_LOGO").'</label><br />'
					.CFile::ShowImage($siteLogo, 193, 43, "border=0 vspace=15") . '<br>' . 
					$this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "siteLogo")).'
				</div>';
			}		
			
			// copyright
			$this->content .= '
			<div class="wizard-input-form-block">
				<label for="siteCopy" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_COPY").'</label><br />'
				.$this->ShowInputField('textarea', 'siteCopy', array("class"=>"wizard-field", "rows"=>"3", "id" => "siteCopy")).'
				<span style="display:inline-block;font-size:12px;margin-top:5px;vertical-align:top;">'.GetMessage("WIZ_COMPANY_COPY_NOTE").'</span>
			</div>';
			
			// phone
			$this->content .= '
			<div class="wizard-input-form-block">
				<label for="siteTelephone" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_TELEPHONE").'</label><br />'
				.$this->ShowInputField('text', 'siteTelephone', array("class"=>"wizard-field", "id" => "siteTelephone")).'
			</div>';

			// phone2
			$this->content .= '
			<div class="wizard-input-form-block">
				<label for="siteTelephone2" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_TELEPHONE2").'</label><br />'
				.$this->ShowInputField('text', 'siteTelephone2', array("class"=>"wizard-field", "id" => "siteTelephone2")).'
			</div>';
			
			// email
			$this->content .= '
			<div class="wizard-input-form-block">
				<label for="siteEmail" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_EMAIL").'</label><br />'
				.$this->ShowInputField('textarea', 'siteEmail', array("class"=>"wizard-field", "id" => "siteEmail")).'
			</div>';
			
			// skype
			/*$this->content .= '
			<div class="wizard-input-form-block">
				<label for="siteSkype" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_SKYPE").'</label><br />'
				.$this->ShowInputField('textarea', 'siteSkype', array("class"=>"wizard-field", "id" => "siteSkype")).'
			</div>';*/
			
			// address
			/*$this->content .= '
			<div class="wizard-input-form-block">
				<label for="siteAddress" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_ADDRESS").'</label><br />'
				.$this->ShowInputField('textarea', 'siteAddress', array("class"=>"wizard-field", "id" => "siteAddress")).'
			</div>';*/
			
			// schedule
			$this->content .= '
			<div class="wizard-input-form-block">
				<label for="siteSchedule" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_SCHEDULE").'</label><br />'
				.$this->ShowInputField('textarea', 'siteSchedule', array("class"=>"wizard-field", "id" => "siteSchedule")).'
			</div>';
			
			// social
			if(LANGUAGE_ID == "ru"){
				$this->content .= '
				<div class="wizard-input-form-block">
					<label for="shopVk" class="wizard-input-title">'.GetMessage("WIZ_SHOP_VK").'</label><br />'
					.$this->ShowInputField('text', 'shopVk', array("class"=>"wizard-field", "id" => "shopVk")).'
				</div>';
			}	
			
			$this->content .= '
			<div class="wizard-input-form-block">
				<label for="shopOdnoklassniki" class="wizard-input-title">'.GetMessage("WIZ_SHOP_ODNOKLASSNIKI").'</label><br />'
				.$this->ShowInputField('text', 'shopOdnoklassniki', array("class"=>"wizard-field", "id" => "shopOdnoklassniki")).'
			</div>';
			
			$this->content .= '
			<div class="wizard-input-form-block">
				<label for="shopFacebook" class="wizard-input-title">'.GetMessage("WIZ_SHOP_FACEBOOK").'</label><br />'
				.$this->ShowInputField('text', 'shopFacebook', array("class"=>"wizard-field", "id" => "shopFacebook")).'
			</div>';
			
			$this->content .= '
			<div class="wizard-input-form-block">
				<label for="shopTwitter" class="wizard-input-title">'.GetMessage("WIZ_SHOP_TWITTER").'</label><br />'
				.$this->ShowInputField('text', 'shopTwitter', array("class"=>"wizard-field", "id" => "shopTwitter")).'
			</div>';
			
			$this->content .= '
			<div class="wizard-input-form-block">
				<label for="shopMailru" class="wizard-input-title">'.GetMessage("WIZ_SHOP_MAILRU").'</label><br />'
				.$this->ShowInputField('text', 'shopMailru', array("class"=>"wizard-field", "id" => "shopMailru")).'
			</div>';
			
			$this->content .= '
			<div class="wizard-input-form-block">
				<label for="shopInstagram" class="wizard-input-title">'.GetMessage("WIZ_SHOP_INSTAGRAM").'</label><br />'
				.$this->ShowInputField('text', 'shopInstagram', array("class"=>"wizard-field", "id" => "shopInstagram")).'
			</div>';
			
			
			$this->content .= '
			<div class="wizard-input-form-block">
				<label for="shopYouTube" class="wizard-input-title">'.GetMessage("WIZ_SHOP_YOUTUBE").'</label><br />'
				.$this->ShowInputField('text', 'shopYouTube', array("class"=>"wizard-field", "id" => "shopYouTube")).'
			</div>';

			$this->content .= '
			<div class="wizard-input-form-block">
				<label for="shopGoogle" class="wizard-input-title">'.GetMessage("WIZ_SHOP_GOOGLE").'</label><br />'
				.$this->ShowInputField('text', 'shopGoogle', array("class"=>"wizard-field", "id" => "shopGoogle")).'
			</div>';
			
			/*$this->content .= '
			<div class="wizard-input-form-block">
				<label for="shopLiveJournal" class="wizard-input-title">'.GetMessage("WIZ_SHOP_LIVEJOURNAL").'</label><br />'
				.$this->ShowInputField('text', 'shopLiveJournal', array("class"=>"wizard-field", "id" => "shopLiveJournal")).'
			</div>';
			*/

			// meta
			$this->content .= '
			<div  id="bx_metadata" '.$styleMeta.'>
				<div class="wizard-input-form-block">
					<div class="wizard-metadata-title">'.GetMessage("wiz_meta_data").'</div>
					<label for="siteMetaDescription" class="wizard-input-title">'.GetMessage("wiz_meta_description").'</label>
					'.$this->ShowInputField("textarea", "siteMetaDescription", array("class" => "wizard-field", "id" => "siteMetaDescription", "style" => "width:100%", "rows"=>"3")).'
				</div>';
				
			$this->content .= '
				<div class="wizard-input-form-block">
					<label for="siteMetaKeywords" class="wizard-input-title">'.GetMessage("wiz_meta_keywords").'</label><br>
					'.$this->ShowInputField('text', 'siteMetaKeywords', array("class" => "wizard-field", "id" => "siteMetaKeywords")).'
				</div>
			</div>';
			
			$this->content .= $this->ShowHiddenField("installDemoData", "Y");
			
			if(LANGUAGE_ID != "ru"){
				CModule::IncludeModule("catalog");
				$db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID"=> '1', "BUY" => "Y", "GROUP_ID" => 2));
				if(!$db_res->Fetch()){
					$this->content .= '
					<div class="wizard-input-form-block">
						<label for="shopAdr">'.GetMessage("WIZ_SHOP_PRICE_BASE_TITLE").'</label>
						<div class="wizard-input-form-block-content">
							'. GetMessage("WIZ_SHOP_PRICE_BASE_TEXT1") .'<br><br>
							'. $this->ShowCheckboxField("installPriceBASE", "Y", 
							(array("id" => "install-demo-data")))
							. ' <label for="install-demo-data">'.GetMessage("WIZ_SHOP_PRICE_BASE_TEXT2").'</label><br />
						</div>
					</div>';	
				}
			}
			
			$this->content .= '</div>';
		}
	}
	
	function OnPostForm(){
		$wizard =& $this->GetWizard();
		
		$res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 43, "max_width" => 193, "make_preview" => "Y"));
		if(file_exists(WIZARD_SITE_PATH."include/logo.jpg")){
			$wizard->SetVar("siteLogoSet", true);
		}
	}
}

class ShopSettings extends CWizardStep{
	function InitStep(){
		$this->SetStepID("shop_settings");
		$this->SetTitle(GetMessage("WIZ_STEP_SS"));
		$this->SetNextStep("person_type");
		$this->SetPrevStep("site_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();
		$siteStamp =$wizard->GetPath()."/site/templates/minimal/images/pechat.gif";
		$siteID = $wizard->GetVar("siteID");
		
		$wizard->SetDefaultVars(
			Array(
				"shopLocalization" => COption::GetOptionString("optimus", "shopLocalization", "ru", $siteID),
				"shopEmail" => COption::GetOptionString("optimus", "shopEmail", $wizard->GetVar("siteEmail"), $siteID),
				"shopOfName" => COption::GetOptionString("optimus", "shopOfName", GetMessage("WIZ_SHOP_OF_NAME_DEF"), $siteID),
				"shopLocation" => COption::GetOptionString("optimus", "shopLocation", GetMessage("WIZ_SHOP_LOCATION_DEF"), $siteID),
				//"shopZip" => 101000,
				"shopAdr" => COption::GetOptionString("optimus", "shopAdr", GetMessage("WIZ_SHOP_ADR_DEF"), $siteID),
				"shopINN" => COption::GetOptionString("optimus", "shopINN", "1234567890", $siteID),
				"shopKPP" => COption::GetOptionString("optimus", "shopKPP", "123456789", $siteID),
				"shopNS" => COption::GetOptionString("optimus", "shopNS", "0000 0000 0000 0000 0000", $siteID),
				"shopBANK" => COption::GetOptionString("optimus", "shopBANK", GetMessage("WIZ_SHOP_BANK_DEF"), $siteID),
				"shopBANKREKV" => COption::GetOptionString("optimus", "shopBANKREKV", GetMessage("WIZ_SHOP_BANKREKV_DEF"), $siteID),
				"shopKS" => COption::GetOptionString("optimus", "shopKS", "30101 810 4 0000 0000225", $siteID),
				"siteStamp" => COption::GetOptionString("optimus", "siteStamp", $siteStamp, $siteID),

				//"shopCompany_ua" => COption::GetOptionString("optimus", "shopCompany_ua", "", $siteID),
				"shopOfName_ua" => COption::GetOptionString("optimus", "shopOfName_ua", GetMessage("WIZ_SHOP_OF_NAME_DEF_UA"), $siteID),
				"shopLocation_ua" => COption::GetOptionString("optimus", "shopLocation_ua", GetMessage("WIZ_SHOP_LOCATION_DEF_UA"), $siteID),
				"shopAdr_ua" => COption::GetOptionString("optimus", "shopAdr_ua", GetMessage("WIZ_SHOP_ADR_DEF_UA"), $siteID),
				"shopEGRPU_ua" =>  COption::GetOptionString("optimus", "shopCompany_ua", "", $siteID),
				"shopINN_ua" =>  COption::GetOptionString("optimus", "shopINN_ua", "", $siteID),
				"shopNDS_ua" =>  COption::GetOptionString("optimus", "shopNDS_ua", "", $siteID),
				"shopNS_ua" =>  COption::GetOptionString("optimus", "shopNS_ua", "", $siteID),
				"shopBank_ua" =>  COption::GetOptionString("optimus", "shopBank_ua", "", $siteID),
				"shopMFO_ua" =>  COption::GetOptionString("optimus", "shopMFO_ua", "", $siteID),
				"shopPlace_ua" =>  COption::GetOptionString("optimus", "shopPlace_ua", "", $siteID),
				"shopFIO_ua" =>  COption::GetOptionString("optimus", "shopFIO_ua", "", $siteID),
				"shopTax_ua" =>  COption::GetOptionString("optimus", "shopTax_ua", "", $siteID),

				"installPriceBASE" => COption::GetOptionString("optimus", "installPriceBASE", "Y", $siteID),
			)
		);
	}

	function ShowStep(){
		$wizard =& $this->GetWizard();
		$siteStamp = $wizard->GetVar("siteStamp", true);
		$templateID = $wizard->GetVar("templateID");
		$ThemeID = $wizard->GetVar($templateID."_themeID");
		$this->content .='<script type="text/javascript">$(document).ready(function(){setWizardBackgroundColor("'.$ThemeID.'");});</script>';
		
		if(!CModule::IncludeModule("catalog")){
			$this->content .= "<p style='color:red'>".GetMessage("WIZ_NO_MODULE_CATALOG")."</p>";
			$this->SetNextStep("shop_settings");
		}
		else{
			/*$this->content .=
				$this->ShowSelectField("shopLocalization", array("ru" => GetMessage("WIZ_SHOP_LOCALIZATION_RUSSIA"), "ua" => GetMessage("WIZ_SHOP_LOCALIZATION_UKRAINE")), array("onchange" => "langReload()", "id" => "localization_select"))
				.' <label for="shopLocalization">'.GetMessage("WIZ_SHOP_LOCALIZATION").'</label><br />';*/

			$this->content .= '<div class="wizard-input-form">';

			$this->content .= '<div class="wizard-input-form-block">
				<label for="shopOfName" class="wizard-input-title">'.GetMessage("WIZ_SHOP_OF_NAME").'</label><br />
				'.$this->ShowInputField('text', 'shopOfName', array("class" => "wizard-field", "id" => "shopOfName")).'
			</div>';			
			
			$this->content .= '<div class="wizard-input-form-block">
				<label for="shopEmail" class="wizard-input-title">'.GetMessage("WIZ_SHOP_EMAIL").'</label><br />
				'.$this->ShowInputField('text', 'shopEmail', array("class" => "wizard-field", "id" => "shopEmail")).'
			</div>';	
	
			$this->content .= '<div class="wizard-input-form-block">
				<label for="shopLocation" class="wizard-input-title">'.GetMessage("WIZ_SHOP_LOCATION").'</label><br />';
				
			$this->content .= $this->ShowInputField('text', 'shopLocation', array("class" => "wizard-field", "id" => "shopLocation"));
			$this->content .= '</div>';			
	
			$this->content .= '
			<div class="wizard-input-form-block">
				<label for="shopAdr" class="wizard-input-title">'.GetMessage("WIZ_SHOP_ADR").'</label><br />
				'.$this->ShowInputField('textarea', 'shopAdr', array("class" => "wizard-field", "rows"=>"3", "id" => "shopAdr")).'
			</div>';


			$currentLocalization = $wizard->GetVar("shopLocalization");
			if (empty($currentLocalization))
				$currentLocalization = $wizard->GetDefaultVar("shopLocalization");
	 //ru
			$this->content .= '
			<div id="ru_bank_details" class="wizard-input-form-block" >
				<div class="wizard-catalog-title">'.GetMessage("WIZ_SHOP_BANK_TITLE").'</div>
				<table  class="wizard-input-table" >
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_INN").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopINN', array('class' => 'wizard-field')).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_KPP").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopKPP', array('class' => 'wizard-field')).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_NS").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopNS', array('class' => 'wizard-field')).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_BANK").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopBANK', array('class' => 'wizard-field')).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_BANKREKV").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopBANKREKV', array('class' => 'wizard-field')).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_KS").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopKS', array('class' => 'wizard-field')).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_STAMP").':</td>
						<td class="wizard-input-table-right">'.$this->ShowFileField("siteStamp", Array("show_file_info"=> "N", "id" => "siteStamp")).'<br />'.CFile::ShowImage($siteStamp, 75, 75, "border=0 vspace=5", false, false).'</td>
						</tr>
				</table>
			</div>
			';

			if (CModule::IncludeModule("catalog")){
				$db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID"=>'1', "BUY"=>"Y", "GROUP_ID"=>2));
				if (!$db_res->Fetch()){
					$this->content .= '
					<div class="wizard-input-form-block">
						<label for="shopAdr">'.GetMessage("WIZ_SHOP_PRICE_BASE_TITLE").'</label>
						<div class="wizard-input-form-block-content">
							'. GetMessage("WIZ_SHOP_PRICE_BASE_TEXT1") .'<br><br>
							'. $this->ShowCheckboxField("installPriceBASE", "Y",
							(array("id" => "install-demo-data")))
							. ' <label for="install-demo-data">'.GetMessage("WIZ_SHOP_PRICE_BASE_TEXT2").'</label><br />

						</div>
					</div>';
				}
			}
			
			$this->content .= '</div>';
		}
	}
	
	function OnPostForm(){
		$wizard =& $this->GetWizard();
		$res = $this->SaveFile("siteStamp", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 70, "max_width" => 190, "make_preview" => "Y"));
	}

}

class PersonType extends CWizardStep{
	function InitStep(){
		$this->SetStepID("person_type");
		$this->SetTitle(GetMessage("WIZ_STEP_PT"));
		$this->SetNextStep("pay_system");
		$this->SetPrevStep("shop_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();
		$shopLocalization = $wizard->GetVar("shopLocalization", true);

		if ($shopLocalization == "ua")
			$wizard->SetDefaultVars(
				Array(
					"personType" => Array(
						"fiz" => "Y",
						"fiz_ua" => "Y",
						"ur" => "Y",
					)
				)
			);
		else
			$wizard->SetDefaultVars(
				Array(
					"personType" => Array(
						"fiz" => "Y",
						"ur" => "Y",
					)
				)
			);
	}

	function ShowStep(){
		$wizard =& $this->GetWizard();
		$shopLocalization = $wizard->GetVar("shopLocalization", true);
		
		$templateID = $wizard->GetVar("templateID");
		$ThemeID = $wizard->GetVar($templateID."_themeID");
		$this->content .='<script type="text/javascript">$(document).ready(function(){setWizardBackgroundColor("'.$ThemeID.'");});</script>';
		
		$this->content .= '<div class="wizard-input-form">';
		$this->content .= '
		<div class="wizard-input-form-block">
			<div style="padding-top:15px">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					<div class="wizard-catalog-form-item">
						'.$this->ShowCheckboxField('personType[fiz]', 'Y', (array("id" => "personTypeF"))).' 
						<label for="personTypeF">'.GetMessage("WIZ_PERSON_TYPE_FIZ").'</label>
					</div>
					<div class="wizard-catalog-form-item">
						'.$this->ShowCheckboxField('personType[ur]', 'Y', (array("id" => "personTypeU"))).' 
						<label for="personTypeU">'.GetMessage("WIZ_PERSON_TYPE_UR").'</label>
					</div>';
					$this->content .= 
				'</div>
				<div class="wizard-catalog-form-item" style="font-size: 14px;">'.GetMessage("WIZ_PERSON_TYPE").'</div>
			</div>
		</div>';
		$this->content .= '</div>';
	}
	
	function OnPostForm(){
		$wizard = &$this->GetWizard();
		$personType = $wizard->GetVar("personType");

		if (empty($personType["fiz"]) && empty($personType["ur"]))
			$this->SetError(GetMessage('WIZ_NO_PT'));
	}
}

class PaySystem extends CWizardStep{
	function InitStep(){
		$this->SetStepID("pay_system");
		$this->SetTitle(GetMessage("WIZ_STEP_PS"));
		$this->SetNextStep("data_install");
		if(LANGUAGE_ID != "ru")
			$this->SetPrevStep("site_settings");
		else
		$this->SetPrevStep("person_type");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();

		if(LANGUAGE_ID == "ru"){
				$wizard->SetDefaultVars(
					Array(
						"paysystem" => Array(
							"cash" => "Y",
							"sber" => "Y",
							"bill" => "Y",
						),
						"delivery" => Array(
							"courier" => "Y",
							"self" => "Y",
							"russianpost" => "N",
						)
					)
				);
		}
		else{
			$wizard->SetDefaultVars(
				Array(
					"paysystem" => Array(
						"cash" => "Y",	
						"paypal" => "Y",
					),			
					"delivery" => Array(
						"courier" => "Y",	
						"self" => "Y",
						"dhl" => "Y",
						"ups" => "Y",
					)
				)
			);
		}
	}
	
	function OnPostForm(){
		$wizard = &$this->GetWizard();
		$paysystem = $wizard->GetVar("paysystem");

		if (empty($paysystem["cash"]) && empty($paysystem["sber"]) && empty($paysystem["bill"]) && empty($paysystem["paypal"]))
			$this->SetError(GetMessage('WIZ_NO_PS'));
	}

	function ShowStep(){
		$wizard =& $this->GetWizard();
		$shopLocalization = $wizard->GetVar("shopLocalization", true);
		$personType = $wizard->GetVar("personType");
		
		$templateID = $wizard->GetVar("templateID");
		$ThemeID = $wizard->GetVar($templateID."_themeID");
		$this->content .='<script type="text/javascript">$(document).ready(function(){setWizardBackgroundColor("'.$ThemeID.'");});</script>';
		
		$this->content .= '<div class="wizard-input-form">';
		$this->content .= '
		<div class="wizard-input-form-block">
			<div class="wizard-catalog-title">'.GetMessage("WIZ_PAY_SYSTEM_TITLE").'</div>
			<div class="wizard-input-form-field wizard-input-form-field-checkbox">
				<div class="wizard-catalog-form-item">
					'.$this->ShowCheckboxField('paysystem[cash]', 'Y', (array("id" => "paysystemC"))).' <label for="paysystemC">'.GetMessage("WIZ_PAY_SYSTEM_C").'</label></div>';
					if(LANGUAGE_ID == "ru")
					{
						/*	if($shopLocalization == "ua" && ($personType["fiz"] == "Y" || $personType["fiz_ua"] == "Y"))
							$this->content .= $this->ShowCheckboxField('paysystem[oshad]', 'Y', (array("id" => "paysystemO"))).' <label for="paysystemS">'.GetMessage("WIZ_PAY_SYSTEM_O").'</label><br />';
						else*/
						if ($personType["fiz"] == "Y")
							$this->content .= '<div class="wizard-catalog-form-item">'.$this->ShowCheckboxField('paysystem[sber]', 'Y', (array("id" => "paysystemS"))).' <label for="paysystemS">'.GetMessage("WIZ_PAY_SYSTEM_S").'</label></div>';
						if($personType["ur"] == "Y")
							$this->content .= '<div class="wizard-catalog-form-item">'.$this->ShowCheckboxField('paysystem[bill]', 'Y', (array("id" => "paysystemB"))).' <label for="paysystemB">'.GetMessage("WIZ_PAY_SYSTEM_B").'</label></div>';
					}
					else
					{
						$this->content .= '<div class="wizard-catalog-form-item">'.$this->ShowCheckboxField('paysystem[paypal]', 'Y', (array("id" => "paysystemP"))).' <label for="paysystemP">PayPal</label></div>';
					}
					$this->content .= '
					<div class="wizard-catalog-form-item" style="font-size: 14px;">'.GetMessage("WIZ_PAY_SYSTEM").'</div>
			</div>
		</div>';
		$this->content .= '
		<div class="wizard-input-form-block">
			<div class="wizard-catalog-title">'.GetMessage("WIZ_DELIVERY_TITLE").'</div>
			<div class="wizard-input-form-field wizard-input-form-field-checkbox">
				<div class="wizard-catalog-form-item">'.$this->ShowCheckboxField('delivery[courier]', 'Y', (array("id" => "deliveryC"))).' <label for="deliveryC">'.GetMessage("WIZ_DELIVERY_C").'</label></div>
				<div class="wizard-catalog-form-item">'.$this->ShowCheckboxField('delivery[self]', 'Y', (array("id" => "deliveryS"))).' <label for="deliveryS">'.GetMessage("WIZ_DELIVERY_S").'</label></div>';
					if(LANGUAGE_ID == "ru")
					{
						//if ($shopLocalization != "ua")
							$this->content .= '<div class="wizard-catalog-form-item">'.$this->ShowCheckboxField('delivery[russianpost]', 'Y', (array("id" => "deliveryR"))).' <label for="deliveryR">'.GetMessage("WIZ_DELIVERY_R").'</label></div>';
					}
					else
					{
						$this->content .= '<div class="wizard-catalog-form-item">'.$this->ShowCheckboxField('delivery[dhl]', 'Y', (array("id" => "deliveryD"))).' <label for="deliveryD">DHL</label></div>';
						$this->content .= '<div class="wizard-catalog-form-item">'.$this->ShowCheckboxField('delivery[ups]', 'Y', (array("id" => "deliveryU"))).' <label for="deliveryU">UPS</label></div>';
					}
					$this->content .= '
				<div class="wizard-catalog-form-item" style="font-size: 14px;">'.GetMessage("WIZ_DELIVERY").'</div>	
			</div>
		</div>';

		$this->content .= '
		<div class="wizard-input-form-block">
			<div class="wizard-catalog-title">'.GetMessage("WIZ_LOCATION_TITLE").'</div>
			<div class="wizard-input-form-field wizard-input-form-field-checkbox">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">';
					if(LANGUAGE_ID == "ru")
					{
						$this->content .= '<div class="wizard-catalog-form-item">'.$this->ShowRadioField("locations_csv", "loc_ussr.csv", array("id" => "loc_ussr", "checked" => "checked"))
							." <label for=\"loc_ussr\">".GetMessage('WSL_STEP2_GFILE_USSR')."</label></div>";
					}
					$this->content .= '<div class="wizard-catalog-form-item">'.$this->ShowRadioField("locations_csv", "loc_usa.csv", array("id" => "loc_usa"))
						." <label for=\"loc_usa\">".GetMessage('WSL_STEP2_GFILE_USA')."</label></div>";
					$this->content .= '<div class="wizard-catalog-form-item">'.$this->ShowRadioField("locations_csv", "loc_cntr.csv", array("id" => "loc_cntr"))
						." <label for=\"loc_cntr\">".GetMessage('WSL_STEP2_GFILE_CNTR')."</label></div>";
					$this->content .= '<div class="wizard-catalog-form-item">'.$this->ShowRadioField("locations_csv", "", array("id" => "none"))
						." <label for=\"none\">".GetMessage('WSL_STEP2_GFILE_NONE')."</label></div>"; $this->content .= '
					<div class="wizard-catalog-form-item" style="font-size: 14px;">'.GetMessage("WIZ_DELIVERY_HINT").'</div>	
				</div>
			</div>
		</div>';
	}
}

class DataInstallStep extends CDataInstallWizardStep{
	function InitStep(){	
		$wizard =& $this->GetWizard();
		$this->SetStepID("data_install");
		$this->SetTitle(GetMessage("wiz_install_data"));
		$this->SetSubTitle(GetMessage("wiz_install_data"));
		$templateID = $wizard->GetVar("templateID");
		$ThemeID = $wizard->GetVar($templateID."_themeID");
		$this->content .='<script type="text/javascript">$(document).ready(function(){setWizardBackgroundColor("'.$ThemeID.'");});</script>';
	}
	
	function CorrectServices(&$arServices){
		if($_SESSION["BX_optimus_LOCATION"] == "Y") $this->repeatCurrentService = true;
		else $this->repeatCurrentService = false;
		$wizard =& $this->GetWizard();
		
		$iblockParams = getLastWritedIblockParams();
		if($iblockParams && intVal($iblockParams["ID"]) && trim($iblockParams["CODE"])){
			switch ($iblockParams["CODE"]){
				//perform any manipulations with last installed infoblock
				default: 
				break;
			}
		}
		clearLastWritedIblockParams(); //cuz correct need only once
		
		if($wizard->GetVar("installDemoData") != "Y"){
		}		
	}
}

class FinishStep extends CFinishWizardStep{
	function InitStep(){
		$this->SetStepID("finish");
		$this->SetNextStep("finish");
		$this->SetTitle(GetMessage("FINISH_STEP_TITLE"));
		$this->SetNextCaption(GetMessage("wiz_go"));  
	}
	
	function checkValid(){
		return true;
	}

	function ShowStep(){
		$wizard =& $this->GetWizard();
		
		$templateID = $wizard->GetVar("templateID");
		$ThemeID = $wizard->GetVar($templateID."_themeID");
		$this->content .='<script type="text/javascript">$(document).ready(function(){setWizardBackgroundColor("'.$ThemeID.'");});</script>';
		
		if($wizard->GetVar("installDemoData") == "Y")
		{
			if(!CModule::IncludeModule("iblock")) return;
		}
		
		if ($wizard->GetVar("proactive") == "Y")
			COption::SetOptionString("statistic", "DEFENCE_ON", "Y");
		
		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
		$rsSites = CSite::GetByID($siteID);
		$siteDir = "/"; 
		if ($arSite = $rsSites->Fetch())
			$siteDir = $arSite["DIR"]; 

		$wizard->SetFormActionScript(str_replace("//", "/", $siteDir."/?finish"));

		$this->CreateNewIndex();
		
		COption::SetOptionString("main", "wizard_solution", $wizard->solutionName, false, $siteID); 
		
		$this->content .= GetMessage("FINISH_STEP_CONTENT");
		$this->content .= "";
		
		if ($wizard->GetVar("installDemoData") == "Y")
			$this->content .= GetMessage("FINISH_STEP_REINDEX");
			
		if(CModule::IncludeModule("aspro.optimus")){
			COptimus::newAction("wizard_installed");
		}
		
		COption::SetOptionString("aspro.optimus", "WIZARD_DEMO_INSTALLED", "Y");
	}
}
?>