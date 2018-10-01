<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");


//Разрабочик решения Антон Почкин Email:Kopernik83@gmail.com Skype:Odisei83

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "stindustrial";
	}
}


class SelectTemplateStep extends CSelectTemplateWizardStep
{
}


class SelectThemeStep extends CSelectThemeWizardStep
{

}

class SiteSettingsStep extends CSiteSettingsWizardStep
{
	function InitStep()
	{
		$wizard =& $this->GetWizard();
		$wizard->solutionName = "stindustrial";
		parent::InitStep();

		$templateID = $wizard->GetVar("templateID");
		$themeID = $wizard->GetVar($templateID."_themeID");

               
		$siteLogo = "/bitrix/wizards/design2u/stindustrial/site/templates/electrocomp/images/Logo.png";
		
             
         //Устанавливаем переменные мастера
$wizard->SetDefaultVars(
	     array
		 ("siteLogo" => $siteLogo,				
           "siteSlogan" => GetMessage("WIZ_COMPANY_SLOGAN_T"),
            "siteName" => GetMessage("WIZ_COMPANY_NAME_T"),
			"siteCopyright"=>GetMessage("WIZ_COMPANY_COPYRIGHT_T"),
			"siteAdress"=>GetMessage("WIZ_COMPANY_ADRESS_T"),
			"siteTime"=>GetMessage("WIZ_COMPANY_TIME_T"),
			"sitePhone"=>GetMessage("WIZ_COMPANY_PHONE_T"),
			"siteEmail"=>GetMessage("WIZ_COMPANY_EMAIL_T"),
			"siteSkype"=>GetMessage("WIZ_COMPANY_SKYPE_T"),
			"siteICQ"=>GetMessage("WIZ_COMPANY_ICQ_T")
			
		)
	);
}
	function ShowStep()
	{

               

        $wizard =& $this->GetWizard();
				
		$siteLogo = $wizard->GetVar("siteLogo", true);                

         //Картинка для лого
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-logo">'.GetMessage("WIZ_COMPANY_LOGO").'</label><br />';
		$this->content .= CFile::ShowImage($siteLogo, 190, 70, "border=0 vspace=15");
		$this->content .= "<br />".$this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "site-logo"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /><br /><br /></td></tr>';
		
		
		/*
		$siteNameI = $wizard->GetVar("siteNameImage", true);                 
		//картинка для названия
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-comnane">'.GetMessage("WIZ_COMPANY_NAME").'</label><br />';
		$this->content .= CFile::ShowImage($siteNameI, 190, 70, "border=0 vspace=15");
		$this->content .= "<br />".$this->ShowFileField("siteNameI", Array("show_file_info" => "N", "id" => "site-namei"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /><br /><br /></td></tr>';
		*/
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-slogan">'.GetMessage("WIZ_COMPANY_SLOGAN").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteSlogan", Array("id" => "site-slogan", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		
		
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="siteName">'.GetMessage("WIZ_COMPANY_NAME").'</label><br />';
		$this->content .= $this->ShowInputField("text", "siteName", Array("id" => "site-adress", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		
		
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="siteCopyright">'.GetMessage("WIZ_COMPANY_COPYRIGHT").'</label><br />';
		$this->content .= $this->ShowInputField("text", "siteCopyright", Array("id" => "site-adress", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
	
	
	



        $this->content .= '<tr><td>';
		$this->content .= '<label for="siteAdress">'.GetMessage("WIZ_COMPANY_ADRESS").'</label><br />';
		$this->content .= $this->ShowInputField("text", "siteAdress", Array("id" => "site-adress", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		
		
		 $this->content .= '<tr><td>';
		$this->content .= '<label for="siteTime">'.GetMessage("WIZ_COMPANY_TIME").'</label><br />';
		$this->content .= $this->ShowInputField("text", "siteTime", Array("id" => "site-adress", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		
		
		
		
		     $this->content .= '<tr><td>';
		$this->content .= '<label for="sitePhone">'.GetMessage("WIZ_COMPANY_PHONE").'</label><br />';
		$this->content .= $this->ShowInputField("text", "sitePhone", Array("id" => "site-phone", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		
		
	  

        $this->content .= '<tr><td>';
		$this->content .= '<label for="siteEmail">'.GetMessage("WIZ_COMPANY_EMAIL").'</label><br />';
		$this->content .= $this->ShowInputField("text", "siteEmail", Array("id" => "site-email", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		
		
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="siteSkype">'.GetMessage("WIZ_COMPANY_SKYPE").'</label><br />';
		$this->content .= $this->ShowInputField("text", "siteSkype", Array("id" => "site-skype", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		
		
			$this->content .= '<tr><td>';
		$this->content .= '<label for="siteIcq">'.GetMessage("WIZ_COMPANY_ICQ").'</label><br />';
		$this->content .= $this->ShowInputField("text", "siteICQ", Array("id" => "site-icq", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';


		
		

		

         /*
		 
		 Контактная информация

Адрес :	332432, Россия, Город ул. Улица, 5а
Тел :	 +7 812 111-93-49
E-mаil :	info@kompany.ru
	CorpSkype                           443-111-3
		 
		 
		 
		 
		 
		 */


                //$this->content .= WIZARD_SITE_PATH."bitrix/templates/autoschool/modules/contacts-header.php";

                 /*
                $this->content .= '<tr><td>';
		$this->content .= '<label for="title">'.GetMessage("WIZ_COMPANY_TITLE_DEF").'</label><br />';
		$this->content .= $this->ShowInputField("text", "title", Array("id" => "title", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		*/
		
		
		/*


                $this->content .= '<tr><td>';
		$this->content .= '<label for="title">'.GetMessage("WIZ_COMPANY_ALT_DEF").'</label><br />';
		$this->content .= $this->ShowInputField("text", "alt", Array("id" => "alt", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
*/

/*

                $this->content .= '<tr><td>';
		$this->content .= '<label for="siteName">'.GetMessage("WIZ_COMPANY_NAME").'</label><br />';
		$this->content .= $this->ShowInputField("text", "siteName", Array("id" => "site-name", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
*/


   

/*
		$siteBanner = $wizard->GetVar("siteBanner", true);

		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-banner">'.GetMessage("WIZ_SITE_BANNER").'</label><br />';
		$this->content .= CFile::ShowImage($siteBanner, 485, 175, "border=0 vspace=15");
		$this->content .= "<br />".$this->ShowFileField("siteBanner", Array("show_file_info" => "N", "id" => "site-banner"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';

		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-text">'.GetMessage("WIZ_BANNER_TEXT").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteBannerText", Array("id" => "site-text", "style" => "width:100%", "rows"=>"4"));
		$this->content .= '<img src="/bitrix/wizards/bitrix/corp_services/images/'.LANGUAGE_ID.'/banner_processed.png">';
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
*/

/*

                $this->content .= '<tr><td>';
		$this->content .= '<label for="site-slogan">'.GetMessage("WIZ_COMPANY_CATEGORY").'</label><br />';

		$this->content .= $this->ShowCheckboxField("siteCategoryA", "Y", Array("id" => "siteCategoryA", "checked" => "checked")).'<label for="siteCategoryA">'.GetMessage("A").'</label>'."&nbsp;";
                $this->content .= $this->ShowCheckboxField("siteCategoryB", "Y", Array("id" => "siteCategoryB", "checked" => "checked")).'<label for="siteCategoryB">'.GetMessage("B").'</label>'."&nbsp;";
                $this->content .= $this->ShowCheckboxField("siteCategoryC", "Y", Array("id" => "siteCategoryC", "checked" => "checked")).'<label for="siteCategoryC">'.GetMessage("C").'</label>'."&nbsp;";
                $this->content .= $this->ShowCheckboxField("siteCategoryD", "Y", Array("id" => "siteCategoryD", "checked" => "checked")).'<label for="siteCategoryD">'.GetMessage("D").'</label>'."&nbsp;";
                $this->content .= $this->ShowCheckboxField("siteCategoryE", "Y", Array("id" => "siteCategoryE", "checked" => "checked")).'<label for="siteCategoryE">'.GetMessage("E").'</label>'."&nbsp;";
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';

*/
/*
                $this->content .= '<tr><td>';
		$this->content .= '<label for="site-Contact2">'.GetMessage("WIZ_COMPANY_CONTACT2").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteContact", Array("id" => "site-contact2", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';

*/
       /*
                $this->content .= '<tr><td>';
		$this->content .= '<label for="siteName">'.GetMessage("WIZ_COMPANY_YANDEX").'</label><br />';
		$this->content .= $this->ShowInputField("text", "siteYandex", Array("id" => "site-yandex", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
       */


		
                /*
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-copy">'.GetMessage("WIZ_COMPANY_COPY").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteCopy", Array("id" => "site-copy", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br/></td></tr>'; */

		$firstStep = COption::GetOptionString("main", "wizard_first" . substr($wizard->GetID(), 7)  . "_" . $wizard->GetVar("siteID"), false, $wizard->GetVar("siteID"));
		if($firstStep == "Y")
		{
			$this->content .= '<tr><td style="padding-bottom:3px;">';
			$this->content .= $this->ShowCheckboxField("installDemoData", "Y", 
				(array("id" => "install-demo-data", "checked" => "checked")));
			$this->content .= '<label for="install-demo-data">'.GetMessage("wiz_structure_data").'</label><br />';
			$this->content .= '</td></tr>';
			
			$this->content .= '<tr><td>&nbsp;</td></tr>';
		}
		else
		{
			$this->content .= $this->ShowHiddenField("installDemoData","Y");
		}
		
		$this->content .= '</table>';

		$formName = $wizard->GetFormName();
		$installCaption = $this->GetNextCaption();
		$nextCaption = GetMessage("NEXT_BUTTON");
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
                
		$res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 64, "max_width" => 200, "make_preview" => "Y"));
                if($res)
                {
                    $file = CFile::GetPath($res);
                    $wizard->SetVar("siteLogo", $file);
                }
                
	}
}

class DataInstallStep extends CDataInstallWizardStep
{
	function CorrectServices(&$arServices)
	{
		$wizard =& $this->GetWizard();
		if($wizard->GetVar("installDemoData") != "Y")
		{
		}
	}
}

class FinishStep extends CFinishWizardStep
{
}
?>