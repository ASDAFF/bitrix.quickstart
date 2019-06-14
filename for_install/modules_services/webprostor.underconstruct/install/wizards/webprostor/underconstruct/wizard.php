<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class WelcomeStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_WELCOME_TITLE"));
		$this->SetStepID("WelcomeStep");
		$this->SetNextStep("SiteStep");
		$this->SetCancelStep("CancelStep");
	}

	function ShowStep()
	{			
		$this->content .= GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_CONTENT");
	}
}

class SiteStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_SITE_TITLE"));
		$this->SetSubTitle(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_SITE_SUBTITLE"));
		$this->SetStepID("SiteStep");
		$this->SetPrevStep("WelcomeStep");
		$this->SetNextStep("TemplateStep");
		$this->SetCancelStep("CancelStep");
	}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		if ($wizard->IsNextButtonClick() || $wizard->IsFinishButtonClick())
		{
			if (!$wizard->GetVar("site")) {					
				$this->SetError(GetMessage('WEBPROSTOR_UNDERCONSTRUCT_MASTER_SITE_ERROR_SITE'));
			}
		}
	}
	
	function ShowStep()
	{
		$rsSites = CSite::GetList($by="sort", $order="desc");
		while ($arSite = $rsSites->Fetch())
		{
			$sites[] = $arSite;
		}
		
		$this->content .= '<table width="100%" class="wizard-data-table">';
		$this->content .= "<tr><th align='right'>".GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_SITE").' <span class="wizard-required">*</span></th>';
		$this->content .= "<td>";
		foreach($sites as $k => $site)
		{
			if($k==0)
				$this->content .= $this->ShowRadioField("site", $site["ID"], array('id'=>$site["ID"], 'checked'=>'checked'))."<label for='{$site["ID"]}'><strong>[{$site["ID"]}]</strong> {$site["NAME"]}</label><br />";
			else
				$this->content .= $this->ShowRadioField("site", $site["ID"], array('id'=>$site["ID"]))."<label for='{$site["ID"]}'><strong>[{$site["ID"]}]</strong> {$site["NAME"]}</label><br />";
		}
		$this->content .= "</td></tr>";
		$this->content .= "</table>";
		$this->content .= '<br /><div class="wizard-note-box"><span class="wizard-required">*</span> '.GetMessage("WEBPROSTOR_UNDERCONSTRUCT_WIZARD_REQUIRED").'.</div>';
	}
}

class TemplateStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_TEMPLATE_TITLE"));
		$this->SetSubTitle(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_TEMPLATE_CONTENT"));
		$this->SetStepID("TemplateStep");
		$this->SetPrevStep("SiteStep");
		$this->SetNextStep("DataStep");
		$this->SetCancelStep("CancelStep");
	}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		if ($wizard->IsNextButtonClick() || $wizard->IsFinishButtonClick())
		{
			if (!$wizard->GetVar("template")) {					
				$this->SetError(GetMessage('WEBPROSTOR_UNDERCONSTRUCT_MASTER_TEMPLATE_ERROR_TEMPLATE'));
			}
		}
	}
	
	function ShowStep()
	{
		$templates = Array(
			Array(
				"CODE" => "default",
				"NAME" => GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_TEMPLATE_TPL_DEFAULT_NAME"),
				"DESCRIPTION" => GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_TEMPLATE_TPL_DEFAULT_DESC"),
			),
			Array(
				"CODE" => "developing",
				"NAME" => GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_TEMPLATE_TPL_DEVELOPING_NAME"),
				"DESCRIPTION" => GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_TEMPLATE_TPL_DEVELOPING_DESC"),
			),
		);
		
		$this->content .= '<table width="100%" class="wizard-data-table">';
		
		foreach($templates as $k => $tpl)
		{
			$this->content .= "<tr><th align='left'>";
			if($k==0)
				$this->content .= $this->ShowRadioField("template", $tpl["CODE"], array('id'=> 'tpl_'.$k, 'checked'=>'checked'))." <strong><label for='tpl_{$k}'>{$tpl["NAME"]}</label></strong> <br />({$tpl["DESCRIPTION"]})";
			else
				$this->content .= $this->ShowRadioField("template", $tpl["CODE"], array('id'=> 'tpl_'.$k))." <strong><label for='tpl_{$k}'>{$tpl["NAME"]}</label></strong> <br />({$tpl["DESCRIPTION"]})";
			$this->content .= "</th>";
			$this->content .= "<td><img src='/bitrix/wizards/webprostor/underconstruct/source/images/tpl_{$tpl["CODE"]}.png' />";
			$this->content .= "</td></tr>";
		}
		
		$this->content .= "</table>";
	}
}

class DataStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_DATA_TITLE"));
		$this->SetSubTitle(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_DATA_CONTENT"));
		$this->SetPrevStep("TemplateStep");
		$this->SetStepID("DataStep");
		$this->SetNextStep("InstallStep");
		$this->SetCancelStep("CancelStep");
		
        $wizard = &$this->GetWizard();
		$arResult = $wizard->GetVars(true);
		$rsSite = CSite::GetByID($arResult["site"]);
		$arSite = $rsSite->GetNext();
        $wizard->SetDefaultVar("SITE_NAME", $arSite["SITE_NAME"]);
        $wizard->SetDefaultVar("EMAIL", $arSite["EMAIL"]);
        $wizard->SetDefaultVar("MESSAGE", GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_DATA_FIELD_MESSAGE_DEFAULT_".strtoupper($arResult["template"])));
        $wizard->SetDefaultVar("TITLE", GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_DATA_FIELD_TITLE_DEFAULT_".strtoupper($arResult["template"])));
	}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		if ($wizard->IsNextButtonClick() || $wizard->IsFinishButtonClick())
		{
			if (!$wizard->GetVar("TITLE")) {					
				$this->SetError(GetMessage('WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_DATA_FIELD_TITLE_ERROR'));
			}
			if (!$wizard->GetVar("MESSAGE")) {					
				$this->SetError(GetMessage('WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_DATA_FIELD_MESSAGE_ERROR'));
			}
			
			$this->SaveFile("LOGO", Array("extensions" => "png,jpg,gif", "max_file_size" => 1000000));
		}
	}
	
	function ShowStep()
	{
		
		$arData = Array(
			"TITLE" => Array(
				"TYPE" => "TEXT",
				"SIZE" => "40",
				"REQUIRED" => "Y",
			),
			"MESSAGE" => Array(
				"TYPE" => "TEXTAREA",
				"COLS" => "50",
				"ROWS" => "3",
				"REQUIRED" => "Y",
			),
			"LOGO" => Array(
				"TYPE" => "FILE",
			),
			"SITE_NAME" => Array(
				"TYPE" => "TEXT",
				"SIZE" => "40",
			),
			"EMAIL" => Array(
				"TYPE" => "TEXT",
				"SIZE" => "20",
			),
			"PHONE" => Array(
				"TYPE" => "TEXT",
				"SIZE" => "20",
			),
		);
		
		$this->content .= '<table width="100%" class="wizard-data-table">';
		
		foreach($arData as $k => $data)
		{
			if($data["REQUIRED"]=="Y")
				$required = ' <span class="wizard-required">*</span>';
			else
				$required = '';
			$this->content .= "<tr><th align='right'>".GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_DATA_FIELD_".$k).$required."</th><td>";
			switch($data["TYPE"])
			{
				case("TEXT"):
					$this->content .= $this->ShowInputField("text", $k, Array("size" => $data["SIZE"]));
					break;
				case("TEXTAREA"):
					$this->content .= $this->ShowInputField("textarea", $k, Array("rows" => $data["ROWS"], "cols" => $data["COLS"]));
					break;
				case("FILE"):
					$this->content .= $this->ShowFileField($k, Array("max_file_size" => 1000000, "size" => "25"));
					break;
			}
			$this->content .= "</td></tr>";
		}
		
		$this->content .= "</table>";
		$this->content .= '<br /><div class="wizard-note-box"><span class="wizard-required">*</span> '.GetMessage("WEBPROSTOR_UNDERCONSTRUCT_WIZARD_REQUIRED").'.</div>';
	}
}

class InstallStep extends CWizardStep
{
	function InitStep()
	{
		
		$this->SetTitle(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_INSTALL_TITLE"));
		$this->SetSubTitle(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_INSTALL_MESSAGE"));
		$this->SetPrevStep("DataStep");
		$this->SetStepID("InstallStep");
		$this->SetNextStep("FinalStep");
		$this->SetNextCaption(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_INSTALL_CAPTION"));
		$this->SetCancelStep("CancelStep");
	}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		if ($wizard->IsNextButtonClick())
		{
			$result_dir_path = $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/'.$wizard->GetVar("site").'/';
			$result_file_path = $result_dir_path.'site_closed.php';
			$result_logo_file_path = '/upload/site_closed/';
			$tpl_file = dirname(__FILE__).'/source/templates/'.$wizard->GetVar("template").'.php';
			$img_file = dirname(__FILE__).'/source/images/img_'.$wizard->GetVar("template").'.png';
			$template_content = file_get_contents($tpl_file);
			
			if($wizard->GetVar("TITLE")) {	
				$template_content = str_replace("#TITLE#", $wizard->GetVar("TITLE"), $template_content);
			}
			else
				$template_content = str_replace("#TITLE#", '', $template_content);
			
			if($wizard->GetVar("SITE_NAME")) {	
				$template_content = str_replace("#SITE_NAME#", '<div class="sitename">'.$wizard->GetVar("SITE_NAME").'</div>', $template_content);
			}
			else
				$template_content = str_replace("#SITE_NAME#", '', $template_content);
			
			if($wizard->GetVar("MESSAGE")) {	
				$template_content = str_replace("#MESSAGE#", '<div class="message">'.$wizard->GetVar("MESSAGE").'</div>', $template_content);
			}
			else
				$template_content = str_replace("#MESSAGE#", '', $template_content);
			
			if($wizard->GetVar("PHONE") || $wizard->GetVar("EMAIL")) {	
				$CONTACTS = '<div class="contacts">';
				if($wizard->GetVar("PHONE")) {	
					$CONTACTS .= '<span class="phone"><i class="fa fa-mobile" aria-hidden="true"></i> '.$wizard->GetVar("PHONE").'</span>';
				}
				
				if($wizard->GetVar("EMAIL")) {	
					$CONTACTS .= '<span class="email"><i class="fa fa-envelope-o" aria-hidden="true"></i> <a href="mailto:'.$wizard->GetVar("EMAIL").'">'.$wizard->GetVar("EMAIL").'</a></span>';
				}
				$CONTACTS .= '</div>';
				$template_content = str_replace("#CONTACTS#", $CONTACTS, $template_content);
			}
			else
				$template_content = str_replace("#CONTACTS#", '', $template_content);
			
			if($wizard->GetVar("LOGO") && $wizard->GetVar("LOGO")>0) {	
				$rsFile = CFile::GetByID($wizard->GetVar("LOGO"));
				$arFile = $rsFile->Fetch();
				$tmp_name = explode('.', $arFile["FILE_NAME"]);
				$logo_file_name = $wizard->GetVar("site").'_logo.'.$tmp_name[1];
				CWizardUtil::CopyFile($wizard->GetVar("LOGO"), $result_logo_file_path.$logo_file_name);
				$template_content = str_replace("#LOGO#", '<div class="logo"><img style="max-width: 200px;" src="'.$result_logo_file_path.$logo_file_name.'"></div>', $template_content);
			}
			else
			{
				$logo_file_name = $wizard->GetVar("site").'_logo.png';
				if (!copy($img_file, $_SERVER["DOCUMENT_ROOT"].$result_logo_file_path.$logo_file_name)) {
					$this->SetError("Can't copy file ".$img_file.' to '.$result_logo_file_path.$logo_file_name);
					return false;
				}
				$template_content = str_replace("#LOGO#", '<div class="logo no-border"><img src="'.$result_logo_file_path.$logo_file_name.'"></div>', $template_content);
			}
		
			if (!file_exists($result_dir_path)) {				
				if(!mkdir($result_dir_path, BX_DIR_PERMISSIONS, true)) {
					$this->SetError("Can't create directory ".$result_dir_path);
					return false;
				}			
			}
			
			if(!file_put_contents($result_file_path, $template_content)) {					
				$this->SetError("Can't create file ".$tpl_file);
				return false;
			} else {
				chmod($result_file_path, BX_FILE_PERMISSIONS);
			}
		}
	}

	function ShowStep()
	{		
		$wizard = &$this->GetWizard();
		$arResult = $wizard->GetVars(true);
		
		$this->content .= '<table width="100%" class="wizard-data-table">';
		$this->content .= "<tr><th><strong>".GetMessage('WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_INSTALL_NAME')."</strong></th><th><strong>".GetMessage('WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_INSTALL_VALUE')."</strong></th><tr>";
		
		foreach($arResult as $name=>$value) {
			$this->content .= '<tr><th align="right">'.GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_INSTALL_".strtoupper($name)."_NAME")."</th><td>";
			
			if ($name == 'LOGO') {
				$this->content .= '<img width="100" src="'.CFile::GetPath($value).'"/>';
			}
			else {
				$this->content .= $value;
			}
			
			$this->content .= "</td></tr>";
		}
		
		$this->content .= "</table>";
	}
}

class FinalStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_FINAL_TITLE"));
		$this->SetStepID("FinalStep");
		$this->SetCancelCaption(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_FINAL_CLOSE"));
		$this->SetCancelStep("CancelStep");
	}

	function ShowStep()
	{		
		$wizard = &$this->GetWizard();
		
		$this->content .= GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_FINAL_MESSAGE");
	}
}

class CancelStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_CANCEL_TITLE"));
		$this->SetStepID("CancelStep");
		$this->SetCancelCaption(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_CLOSE"));
		$this->SetCancelStep("CancelStep");
	}

	function ShowStep()
	{		
		$wizard = &$this->GetWizard();
		
		$this->content .= GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MASTER_STEP_CANCEL_MESSAGE");
	}
}
?>