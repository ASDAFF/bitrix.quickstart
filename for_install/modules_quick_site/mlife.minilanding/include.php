<?
IncludeModuleLangFile(__FILE__);
class CMlifeMinilanding {

	function getref($date_ref,$key,$ref,$count) {
		if ($count==1) return md5($date_ref.$key.$ref);
		if ($count==2) return md5(($date_ref-3600).$key.$ref);
	}

	function checkspam($referer_start,$ref1,$ref2) {
		if($referer_start==$ref1 || $referer_start==$ref2) {
			return 0;
		}
		else {
			return 1;
		}
	}

	function getname($var) {
		if(ToLower(SITE_CHARSET) == "windows-1251") {
			$var = $GLOBALS["APPLICATION"]->ConvertCharset($var, 'UTF-8', SITE_CHARSET);
		}
		return htmlspecialcharsEx($var);
	}

	function mlife_macros_replace($arFields, $template) {
		if (is_array($arFields) && count($arFields)==0) return $template;
		foreach($arFields as $key=>$value) {
			$template = str_replace('#'.$key.'#',$value,$template);
		}
		return $template;
	}
	
	function SetDateShare($time=4)
	{
		$date = COption::GetOptionString("mlife.minilanding", "datecounter", "");
		if(!$date || strtotime($date)<time()) {
			$newdatetemp = time()+(60*60*$time);
			$newdate = date("Y-m-d H:i:s",$newdatetemp);
			COption::SetOptionString("mlife.minilanding", "datecounter", $newdate);
		}
		return 'CMlifeMinilanding::SetDateShare('.$time.');';
	}

}
class СMlifeSiteMinilanding {
	
	function ShowPanel(){
		if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "minilanding")
		{
			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF" => "/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardName=mlife:minilanding&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
				"ID" => "fitnes_wizard",
				//"SRC" => "/bitrix/images/fileman/panel/web_form.gif",
				"ICON" => "bx-panel-site-wizard-icon",
				"MAIN_SORT" => 2500,
				"TYPE" => "BIG",
				"SORT" => 10,	
				"ALT" => GetMessage("MLIFE_MINILANDING_BUTTON_DESCRIPTION"),
				"TEXT" => GetMessage("MLIFE_MINILANDING_BUTTON_NAME"),
				"MENU" => array(),
			));
		}
	}
}
?>