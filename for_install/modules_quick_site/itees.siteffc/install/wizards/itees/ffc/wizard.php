<?require_once("scripts/utils.php");

class SelectSiteStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("select_site");
		$this->SetTitle(GetMessage("SELECT_SITE_TITLE"));
		$this->SetSubTitle(GetMessage("SELECT_SITE_SUBTITLE"));
		$this->SetNextStep("select_template");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();

		if ($wizard->IsNextButtonClick())
		{
			$siteID = $wizard->GetVar("siteID");
			$siteFolder = str_replace(array("\\", "///", "//"), "/", "/".$wizard->GetVar("siteFolder")."/");
			$siteNewID = $wizard->GetVar("siteNewID");
			$createSite = $wizard->GetVar("createSite");
			
			if ($createSite == "Y")
			{
				if (strlen($siteNewID) != 2)
				{
					$this->SetError(GetMessage("wiz_site_id_error"));
					return;
				}
				$rsSites = CSite::GetList($by="sort", $order="desc", array());
				while($arSite = $rsSites->Fetch())
				{
					if (trim($arSite["DIR"], "/") == trim($siteFolder, "/"))
					{
						$this->SetError(GetMessage("wiz_site_folder_already_exists"));
						$bError = true;
					}

					if ($arSite["ID"] == trim($siteNewID))
					{
						$this->SetError(GetMessage("wiz_site_id_already_exists"));
						$bError = true;
					}
				}
				if ($bError)
					return; 
				$wizard->SetVar("siteID", $siteNewID);

				COption::SetOptionString("main", "site_create", "Y");
				
				COption::SetOptionString("main", "site_id", $siteNewID);
				COption::SetOptionString("main", "site_folder", $siteFolder);
			}
			elseif (strlen($siteID) > 0)
			{
				$db_res = CSite::GetList($by="sort", $order="desc", array("LID" => $siteID));
				if (!($db_res && $res = $db_res->Fetch()))
					$this->SetError(GetMessage("wiz_site_id_not_exists_error"));
				COption::SetOptionString("main", "site_create", "N");
				COption::SetOptionString("main", "site_id", $siteID);
				COption::RemoveOption("main", "site_folder");
				return;
			}
			else
			{
				$wizard->SetVar("siteID", WizardServices::GetCurrentSiteID());
			}
		}
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();

		$arSites = array(); 
		$arSitesSelect = array(); 
		$db_res = CSite::GetList($by="sort", $order="desc", array());
		if ($db_res && $res = $db_res->GetNext())
		{
			do 
			{
				$arSites[$res["ID"]] = $res; 
				$arSitesSelect[$res["ID"]] = '['.$res["ID"].'] '.$res["NAME"];
			} while ($res = $db_res->GetNext()); 
		}
		
		$createSite = $wizard->GetVar("createSite"); 
		$createSite = ($createSite == "Y" ? "Y" : "N"); 
		
		
$this->content = 
'<script type="text/javascript">
function SelectCreateSite(element, solutionId)
{
	var container = document.getElementById("solutions-container");
	var nodes = container.childNodes;
	for (var i = 0; i < nodes.length; i++)
	{
		if (!nodes[i].className)
			continue;
		nodes[i].className = "solution-item";
	}
	element.className = "solution-item solution-item-selected";
	var check = document.getElementById("createSite" + solutionId);
	if (check)
		check.checked = true;
}
</script>';
		$this->content .= '<div id="solutions-container">';
			$this->content .= "<div onclick=\"SelectCreateSite(this, 'N');\" ";
				$this->content .= 'class="solution-item'.($createSite != "Y" ? " solution-item-selected" : "").'">'; 
				$this->content .= '<b class="r3"></b><b class="r1"></b><b class="r1"></b>'; 
				$this->content .= '<div class="solution-inner-item">'; 
					$this->content .= $this->ShowRadioField("createSite", "N", (array("id" => "createSiteN", "class" => "solution-radio") + 
						($createSite != "Y" ? array("checked" => "checked") : array()))); 
					$this->content .= '<h4>'.GetMessage("wiz_site_existing").'</h4>'; 
				if (count($arSites) < 2)
					$this->content .= '<p>'.GetMessage("wiz_site_existing_title").' '.implode("", $arSitesSelect).'</p>'; 
				else
				{
					$this->content .= '<p>'.GetMessage("wiz_site_existing_title");
					$this->content .= "<br />". $this->ShowSelectField("siteID", $arSitesSelect)."</p>";
				}
				$this->content .= '<p>'.GetMessage("wiz_site_warning")."</p>";
				$this->content .= '</div>'; 
				$this->content .= '<b class="r1"></b><b class="r1"></b><b class="r3"></b>'; 
			$this->content .= '</div>';
		if (count($arSites) < COption::GetOptionInt("main", "PARAM_MAX_SITES", 100) || COption::GetOptionInt("main", "PARAM_MAX_SITES", 100) <= 0)
		{
			$this->content .= "<div onclick=\"SelectCreateSite(this, 'Y');\" ";
				$this->content .= 'class="solution-item'.($createSite == "Y" ? " solution-item-selected" : "").'">'; 
				$this->content .= '<b class="r3"></b><b class="r1"></b><b class="r1"></b>'; 
				$this->content .= '<div class="solution-inner-item">'; 
					$this->content .= $this->ShowRadioField("createSite", "Y", (array("id" => "createSiteY", "class" => "solution-radio") + 
						($createSite == "Y" ? array("checked" => "checked") : array()))); 
					$this->content .= '<h4>'.GetMessage("wiz_site_new").'</h4>'; 
					$this->content .= '<p>';
						$this->content .= str_replace(
							array(
								"#SITE_ID#", 
								"#SITE_DIR#"), 
							array(
								$this->ShowInputField("text", "siteNewID", array("size" => 2, "maxlength" => 2, "id" => "siteNewID")), 
								$this->ShowInputField("text", "siteFolder", array("id" => "siteFolder"))), 
							GetMessage("wiz_site_new_title")); 
					$this->content .= '</p>'; 
				$this->content .= '</div>'; 
				$this->content .= '<b class="r1"></b><b class="r1"></b><b class="r3"></b>'; 
			$this->content .= '</div>';
		}
		else
		{
			$this->content .= str_replace("#PARAM_MAX_SITES#", COption::GetOptionInt("main", "PARAM_MAX_SITES", 100), GetMessage("wiz_buy_site_new")); 
		}
		$this->content .= '</div>';
	}
}

class SelectTemplateStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("select_template");
		$this->SetTitle(GetMessage("SELECT_TEMPLATE_TITLE"));
		$this->SetSubTitle(GetMessage("SELECT_TEMPLATE_SUBTITLE"));
		if (!defined("WIZARD_DEFAULT_SITE_ID"))
		{
		$this->SetPrevStep("select_site");
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
		}
		else
		{
			$wizard =& $this->GetWizard(); 
			$wizard->SetVar("siteID", WIZARD_DEFAULT_SITE_ID); 
		}
		$this->SetNextStep("select_theme");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
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

		$defaultTemplateID = COption::GetOptionString("main", "wizard_template_id", "", $wizard->GetVar("siteID")); 
		if (!(strlen($defaultTemplateID) > 0 && array_key_exists($defaultTemplateID, $arTemplates)))
		{
			if (strlen($defaultTemplateID) > 0 && array_key_exists($defaultTemplateID, $arTemplates))
				$wizard->SetDefaultVar("templateID", $defaultTemplateID);
			else
				$defaultTemplateID = "";
		}

		$this->content = 
'<script type="text/javascript">
function SelectTemplate(element, solutionId)
{
	var container = document.getElementById("solutions-container");
	var anchors = container.getElementsByTagName("A");
	for (var i = 0; i < anchors.length; i++)
	{
		if (anchors[i].parentNode != container)
			continue;
		anchors[i].className = "solution-item";
	}
	element.className = "solution-item solution-item-selected";
	var hidden = document.getElementById("templateID");
	if (!hidden) 
	{
		hidden = document.createElement("INPUT");
		hidden.type = "hidden"
		hidden.id = "templateID";
		hidden.name = "'.$wizard->GetRealName("templateID").'";
		container.appendChild(hidden);
	}
	hidden.value = solutionId;
}
</script>';
		$this->content .= '<div id="solutions-container">';
		foreach ($arTemplates as $templateID => $arTemplate)
		{
			if ($defaultTemplateID == "")
			{
				$defaultTemplateID = $templateID;
				$wizard->SetDefaultVar("templateID", $defaultTemplateID);
			}

			$this->content .= "<a href=\"javascript:void(0);\" ondblclick=\"SubmitForm('next'); return false;\" onclick=\"SelectTemplate(this, '".$templateID."'); return false;\" ";
				$this->content .= 'class="solution-item'.($defaultTemplateID == $templateID ? " solution-item-selected" : "").'">'; 
				$this->content .= '<b class="r3"></b><b class="r1"></b><b class="r1"></b>'; 
				$this->content .= '<div class="solution-inner-item">'; 
					$this->content .= CFile::ShowImage($arTemplate["PREVIEW"], 100, 100, ' border="0" class="solution-image" '); 
					$this->content .= '<h4>'.$arTemplate["NAME"].'</h4><p>'.$arTemplate["DESCRIPTION"].'</p>'; 
				$this->content .= '</div>'; 
				$this->content .= '<b class="r1"></b><b class="r1"></b><b class="r3"></b>'; 
			$this->content .= '</a>'; 
		}
		$this->content .= $this->ShowHiddenField("templateID", $defaultTemplateID, array("id" => "templateID")); 
		$this->content .= '</div>'; 
	}
}

class SelectThemeStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("select_theme");
		$this->SetTitle(GetMessage("SELECT_THEME_TITLE"));
		$this->SetSubTitle(GetMessage("SELECT_THEME_SUBTITLE"));
		$this->SetPrevStep("select_template");
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
		$this->SetNextStep("site_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
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
			$arThemes = WizardServices::GetThemes($templatesPath."/".$templateID."/themes", $templatesPath."/".$templateID);

			if (!array_key_exists($themeID, $arThemes))
				$this->SetError(GetMessage("wiz_template_color"));
		}
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$templateID = $wizard->GetVar("templateID");
		
		$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
		$arThemes = WizardServices::GetThemes($templatesPath."/".$templateID."/themes", $templatesPath."/".$templateID);
		if (empty($arThemes))
			return;

		$themeVarName = $templateID."_themeID";
		$defaultThemeID = COption::GetOptionString("main", "wizard_".$templateID."_theme_id", "", $wizard->GetVar("siteID")); 
		
		if (!(strlen($defaultThemeID) > 0 && array_key_exists($defaultThemeID, $arThemes)))
		{
			$defaultThemeID = COption::GetOptionString("main", "wizard_".$templateID."_theme_id", "", $wizard->GetVar("siteID"));
			if (strlen($defaultThemeID) > 0 && array_key_exists($defaultThemeID, $arThemes))
				$wizard->SetDefaultVar($themeVarName, $defaultThemeID);
			else
				$defaultThemeID = "";
		}

		$this->content = 
'<script type="text/javascript">
function SelectTheme(element, solutionId, imageUrl)
{
	var container = document.getElementById("solutions-container");
	var anchors = container.getElementsByTagName("A");
	for (var i = 0; i < anchors.length; i++)
	{
		if (anchors[i].parentNode.parentNode != container)
			continue;
		anchors[i].className = "solution-item solution-picture-item";
	}
	element.className = "solution-item  solution-picture-item solution-item-selected";
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
	'<div style="overflow: hidden;" id="solutions-container">';
		$ii = 0;
		$arDefaultTheme = array(); 
		foreach ($arThemes as $themeID => $arTheme)
		{
			if ($defaultThemeID == "")
			{
				$defaultThemeID = $themeID;
				$wizard->SetDefaultVar($themeVarName, $defaultThemeID);
			}
			if ($defaultThemeID == $themeID)
				$arDefaultTheme = $arTheme;
			$ii++;
			$this->content .= 
				'<div class="solution-item-wrapper">'.
					'<a ondblclick="SubmitForm(\'next\'); return false;" onclick="SelectTheme(this, \''.$themeID.'\', \''.$arTheme["SCREENSHOT"].'\'); return false;" '.
						'href="javascript:void(0);" class="solution-item solution-picture-item'.($defaultThemeID == $themeID ? " solution-item-selected" : "").'">'.
						'<b class="r3"></b><b class="r1"></b><b class="r1"></b>'.
						'<div class="solution-inner-item">'.
							CFile::ShowImage($arTheme["PREVIEW"], 70, 70, ' border="0" class="solution-image"').
						'</div>'.
						'<b class="r1"></b><b class="r1"></b><b class="r3"></b>'. 
						'<div class="solution-description">'.$ii.'. '.$arTheme["NAME"].'</div>'. 
					'</a>'.
				'</div>';
		}
		
		$this->content .= $this->ShowHiddenField($themeVarName, $defaultThemeID, array("id" => "selected-solution"));  
		$this->content .= 
			'</div>'.
			'<div id="solution-preview">'.
				'<b class="r3"></b><b class="r1"></b><b class="r1"></b>'.
					'<div class="solution-inner-item">'.
						CFile::ShowImage($arDefaultTheme["SCREENSHOT"], 450, 450, ' border="0" id="solution-preview-image"').
					'</div>'.
				'<b class="r1"></b><b class="r1"></b><b class="r3"></b>'.
			'</div>'.
		'</div>';
	}
}

class SiteSettingsStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("site_settings");
		$this->SetTitle(GetMessage("wiz_settings"));
		$this->SetSubTitle(GetMessage("wiz_settings"));
		$this->SetNextStep("company_services");
		$this->SetPrevStep("select_theme");
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));

		$wizard =& $this->GetWizard();
		$wizard->SetDefaultVars(
			Array(
				"company_name" => COption::GetOptionString("main", "company_name", GetMessage("COMPANY_NAME_DEFAULT"), WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"))),
				"company_slogan" => COption::GetOptionString("main", "company_slogan", GetMessage("COMPANY_SLOGAN_DEFAULT"), WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"))),
				"company_phone" => GetMessage("COMPANY_PHONE_DEFAULT"),
				"company_adress" => GetMessage("COMPANY_ADRESS_DEFAULT"),
				"company_email" => GetMessage("COMPANY_EMAIL_DEFAULT"),
				"company_control_email" => GetMessage("COMPANY_CONTROL_EMAIL_DEFAULT"),
			)
		);
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		
		if ($wizard->IsNextButtonClick())
		{
			COption::SetOptionString("main", "company_name", str_replace(Array("<"), Array("&lt;"), $wizard->GetVar("company_name")), false, $wizard->GetVar("siteID"));
			COption::SetOptionString("main", "company_slogan", str_replace(Array("<"), Array("&lt;"), $wizard->GetVar("company_slogan")), false, $wizard->GetVar("siteID"));
			$this->SaveFile("company_logo", Array("max_file_size" => 1.5*1024*1024, "extensions" => "gif,jpg,jpeg,png", "max_height" => 120, "max_width" => 90, "make_preview" => "Y"));
			COption::SetOptionString("main", "wizard_site_logo", "", false, $wizard->GetVar("siteID"));
		}
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();

		$wizard->SetVar("company_name", COption::GetOptionString("main", "company_name", GetMessage("COMPANY_NAME_DEFAULT"), WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"))));
		$wizard->SetVar("company_slogan", COption::GetOptionString("main", "company_slogan", GetMessage("COMPANY_SLOGAN_DEFAULT"), WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"))));				
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="company_name">'.GetMessage("COMPANY_NAME").'</label><br />';
		$this->content .= $this->ShowInputField("text", "company_name", Array("id" => "company_name", "style" => "width:90%"));
		$this->content .= '</td></tr>';

		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="company_slogan">'.GetMessage("COMPANY_SLOGAN").'</label><br />';
		$this->content .= $this->ShowInputField("text", "company_slogan", Array("id" => "company_slogan", "style" => "width:90%"));
		$this->content .= '</td></tr>';
		
		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
		$fileID = COption::GetOptionString("main", "wizard_site_logo", false, $siteID);
		if (intval($fileID) > 0)
			$wizard->SetVar("company_logo", $fileID);
		$companyLogo = $wizard->GetVar("company_logo");
		
		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="company_logo">'.GetMessage("COMPANY_LOGO").'</label><br />';
		$this->content .= $this->ShowFileField("company_logo", Array("max_file_size" => 1.5*1024*1024, "show_file_info" => "N"));
		$this->content .= "<br />".CFile::ShowImage($companyLogo, 200, 200, "border=0", "", true);
		$this->content .= '</td></tr>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="company_phone">'.GetMessage("COMPANY_PHONE").'</label><br />';
		$this->content .= $this->ShowInputField("text", "company_phone", Array("id" => "company_phone", "style" => "width:90%"));
		$this->content .= '</td></tr>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="company_adress">'.GetMessage("COMPANY_ADRESS").'</label><br />';
		$this->content .= $this->ShowInputField("text", "company_adress", Array("id" => "company_adress", "style" => "width:90%"));
		$this->content .= '</td></tr>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="company_email">'.GetMessage("COMPANY_EMAIL").'</label><br />';
		$this->content .= $this->ShowInputField("text", "company_email", Array("id" => "company_email", "style" => "width:90%"));
		$this->content .= '</td></tr>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="company_control_email">'.GetMessage("COMPANY_CONTROL_EMAIL").'</label><br />';
		$this->content .= $this->ShowInputField("text", "company_control_email", Array("id" => "company_control_email", "style" => "width:90%"));
		$this->content .= '</td></tr>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr><td>';
		$this->content .= $this->ShowCheckboxField("install_demo_data", "Y", Array("id" => "install_demo_data", "checked" => ""));
		$this->content .= '&nbsp;<label for="install_demo_data">'.GetMessage("WIZARD_INSTALL_DEMO_DATA").'</label>';
		$this->content .= '</td></tr>';
		
		$this->content .= '</table>';

		$formName = $wizard->GetFormName();
		$installCaption = $this->GetNextCaption();
		$nextCaption = GetMessage("NEXT_BUTTON");
	}
}


class CompanyServicesStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("company_services");
		$this->SetTitle(GetMessage("COMPANY_SERVICES_TITLE"));
		$this->SetSubTitle(GetMessage("COMPANY_SERVICES_SUBTITLE"));
		$this->SetNextStep("data_install");
		$this->SetPrevStep("site_settings");
		$this->SetNextCaption(GetMessage("wiz_install"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
		$wizard =& $this->GetWizard();
	}

	function OnPostForm()
	{
		
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
		$this->content .= '<tr>';
		$this->content .= "<td>".$this->ShowCheckboxField("company_services[]", "auto", array("checked"=>""))."</td>";
		$this->content .= "<td>".GetMessage("SERVICES_AUTO")."</td>";
		$this->content .= '</tr>';

		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr>';
		$this->content .= "<td>".$this->ShowCheckboxField("company_services[]", "train", array("checked"=>""))."</td>";
		$this->content .= "<td>".GetMessage("SERVICES_TRAIN")."</td>";
		$this->content .= '</tr>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr>';
		$this->content .= "<td>".$this->ShowCheckboxField("company_services[]", "air", array("checked"=>""))."</td>";
		$this->content .= "<td>".GetMessage("SERVICES_AIR")."</td>";
		$this->content .= '</tr>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr>';
		$this->content .= "<td>".$this->ShowCheckboxField("company_services[]", "sea", array("checked"=>""))."</td>";
		$this->content .= "<td>".GetMessage("SERVICES_SEA")."</td>";
		$this->content .= '</tr>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr>';
		$this->content .= "<td>".$this->ShowCheckboxField("company_services[]", "river", array("checked"=>""))."</td>";
		$this->content .= "<td>".GetMessage("SERVICES_RIVER")."</td>";
		$this->content .= '</tr>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr>';
		$this->content .= "<td>".$this->ShowCheckboxField("company_services[]", "container", array("checked"=>""))."</td>";
		$this->content .= "<td>".GetMessage("SERVICES_CONTAINER")."</td>";
		$this->content .= '</tr>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr>';
		$this->content .= "<td>".$this->ShowCheckboxField("company_services[]", "addition", array("checked"=>""))."</td>";
		$this->content .= "<td>".GetMessage("SERVICES_ADDITION")."</td>";
		$this->content .= '</tr>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr>';
		$this->content .= "<td>".$this->ShowCheckboxField("company_services[]", "auto_transportation", array("checked"=>""))."</td>";
		$this->content .= "<td>".GetMessage("SERVICES_AUTO_TRANSPORTATION")."</td>";
		$this->content .= '</tr>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr>';
		$this->content .= "<td>".$this->ShowCheckboxField("company_services[]", "pass", array("checked"=>""))."</td>";
		$this->content .= "<td>".GetMessage("SERVICES_PASS")."</td>";
		$this->content .= '</tr>';
		
		$this->content .= '</table>';
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
		
		
		$install_demo_data = $wizard->GetVar("install_demo_data");
		if($install_demo_data == "Y"){
			define("WIZARD_INSTALL_DEMO_DATA", true);
		}else{
			define("WIZARD_INSTALL_DEMO_DATA", false);
		}
	
	
		if ($serviceID == "finish")
		{
			$wizard->SetCurrentStep("finish");
			return;
		}

		$arServices = WizardServices::GetServices($_SERVER["DOCUMENT_ROOT"].$wizard->GetPath(), "/site/services/");
		
		if ($serviceStage == "skip")
			$success = true;
		else
			$success = $this->InstallService($serviceID, $serviceStage);

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

		$arServices = WizardServices::GetServices($_SERVER["DOCUMENT_ROOT"].$wizard->GetPath(), "/site/services/");
		
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

	function InstallService($serviceID, $serviceStage)
	{
		$wizard =& $this->GetWizard();

		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
		
		define("WIZARD_SITE_ID", $siteID);
		define("WIZARD_SITE_ROOT_PATH", $_SERVER["DOCUMENT_ROOT"]);

		$rsSites = CSite::GetByID($siteID);
		if ($arSite = $rsSites->Fetch())
			define("WIZARD_SITE_DIR", $arSite["DIR"]);
		else
			define("WIZARD_SITE_DIR", "/");
		define("WIZARD_SITE_PATH", str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".WIZARD_SITE_DIR."/"));
				
		$wizardPath = $wizard->GetPath();
		define("WIZARD_RELATIVE_PATH", $wizardPath);
		define("WIZARD_ABSOLUTE_PATH", $_SERVER["DOCUMENT_ROOT"].$wizardPath);

		$templatesPath = WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH."/site");
		$arTemplates = WizardServices::GetTemplates($templatesPath);
		$templateID = $wizard->GetVar("templateID");
		define("WIZARD_TEMPLATE_ID", $templateID);
		define("WIZARD_TEMPLATE_RELATIVE_PATH", $templatesPath."/".WIZARD_TEMPLATE_ID);
		define("WIZARD_TEMPLATE_ABSOLUTE_PATH", $_SERVER["DOCUMENT_ROOT"].WIZARD_TEMPLATE_RELATIVE_PATH);

		$themeID = $wizard->GetVar($templateID."_themeID");
		$arThemes = WizardServices::GetThemes(WIZARD_TEMPLATE_RELATIVE_PATH."/themes", $templatesPath."/".WIZARD_TEMPLATE_ID);
		define("WIZARD_THEME_ID", $themeID);
		define("WIZARD_THEME_RELATIVE_PATH", WIZARD_TEMPLATE_RELATIVE_PATH."/themes/".WIZARD_THEME_ID);
		define("WIZARD_THEME_ABSOLUTE_PATH", $_SERVER["DOCUMENT_ROOT"].WIZARD_THEME_RELATIVE_PATH);

		$servicePath = WIZARD_RELATIVE_PATH."/site/services/".$serviceID;
		define("WIZARD_SERVICE_RELATIVE_PATH", $servicePath);
		define("WIZARD_SERVICE_ABSOLUTE_PATH", $_SERVER["DOCUMENT_ROOT"].$servicePath);
		define("WIZARD_IS_RERUN", $_SERVER["PHP_SELF"] != "/index.php");

		define("WIZARD_SITE_LOGO", intval($wizard->GetVar("siteLogo")));

		$dbUsers = CGroup::GetList($by="id", $order="asc", Array("ACTIVE" => "Y"));
		while($arUser = $dbUsers->Fetch())
			define("WIZARD_".$arUser["STRING_ID"]."_GROUP", $arUser["ID"]);

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
		$siteDir = "/"; 
		if ($arSite = $rsSites->Fetch())
			$siteDir = $arSite["DIR"]; 

		$wizard->SetFormActionScript(str_replace("//", "/", $siteDir."/?finish"));

		$this->CreateNewIndex();
		
		COption::SetOptionString("main", "wizard_solution", "itees-ffc", false, $siteID);
		
		$this->content .= GetMessage("FINISH_STEP_CONTENT");		
	}

	function CreateNewIndex()
	{
		$wizard =& $this->GetWizard();
		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
		
		define("WIZARD_SITE_ID", $siteID);
		define("WIZARD_SITE_ROOT_PATH", $_SERVER["DOCUMENT_ROOT"]);

		$rsSites = CSite::GetByID($siteID);
		if ($arSite = $rsSites->Fetch())
			define("WIZARD_SITE_DIR", $arSite["DIR"]);
		else
			define("WIZARD_SITE_DIR", "/");

		define("WIZARD_SITE_PATH", str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".WIZARD_SITE_DIR."/"));
		
		//Copy index page
		CopyDirFiles(
			WIZARD_SITE_PATH."/_index.php",
			WIZARD_SITE_PATH."/index.php",
			$rewrite = true,
			$recursive = true,
			$delete_after_copy = true
		);
	}
}
?>