<?
if (!WIZARD_IS_RERUN && WIZARD_SITE_CHARSET)
{	
	$wizard =& $this->GetWizard();	
	$company_address=$wizard->GetVar("company_address");
	if(ToLower(WIZARD_SITE_CHARSET)=="windows-1251")
	{
		$company_address=iconv('windows-1251', 'UTF-8', $company_address);
	}	
	$geocode = simplexml_load_file('http://maps.googleapis.com/maps/api/geocode/xml?address='.urlencode($company_address).'&sensor=false');
	$lat=$geocode->result->geometry->location->lat;
	$lon=$geocode->result->geometry->location->lng;	
	if(ToLower(WIZARD_SITE_CHARSET)=="windows-1251")
	{
		$lat=iconv('UTF-8', 'windows-1251', $lat);
		$lon=iconv('UTF-8', 'windows-1251', $lon);			
	}
	$arReplace = Array(
						"LAT" => $lat,
						"LON" => $lon,	
						"siteName"=> htmlspecialchars($wizard->GetVar("siteName")),
						"stringLenght"=> strlen(htmlspecialchars($wizard->GetVar("siteName"))),								
			);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR."/about/contacts/index.php", $arReplace);
}
?>