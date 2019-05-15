<?
// Templates of components
$arCTempls = CComponentUtil::GetTemplatesList('altasib:geobase.your.city');
$arDefCTempls = ".default,";
foreach($arCTempls as $Templ){
	if($Templ["NAME"] == ".default")
		$arDefCTempls = $Templ["NAME"].','.$Templ["TEMPLATE"];
}

$arCTempls = CComponentUtil::GetTemplatesList('altasib:geobase.your.city');
$arDefCSC_Templ = ".default,";
foreach($arCTempls as $Templ){
	if($Templ["NAME"] == ".default")
		$arDefCSC_Templ = $Templ["NAME"].','.$Templ["TEMPLATE"];
}
// array site templates
$rsData = CSiteTemplate::GetList(array($by => $order), array(), array("ID", "NAME"));
while ($arTemplRes = $rsData->Fetch()){
	$arTemplDefault .= $arTemplRes["ID"].",";
}
// sites array
$sites = CSite::GetList($by="sort", $order="desc", Array("ACTIVE" => "Y"));
while ($Site= $sites->Fetch()){
	$arSitesDefault .= $Site["ID"].",";
}
// default location
$rusID = "";
if(CModule::IncludeModule("sale"))
{
	$rus1 = GetMessage("ALTASIB_GEOBASE_RUSSIA");
	$rus2 = GetMessage("ALTASIB_GEOBASE_RF");
	$db_contList = CSaleLocation::GetCountryList(Array("NAME_LANG"=>"ASC"), Array(), LANG);
	while ($arContList = $db_contList->Fetch())
	{
		if(in_array($rus1, $arContList) || in_array($rus2, $arContList)){
			$rusID = $arContList["ID"]; break;
		}
	}
}

$altasib_geobase_default_option = array(
	"your_city_templates" => $arDefCTempls,
	"select_city_templates" => $arDefCSC_Templ,
	"template" => $arTemplDefault,
	"sites" => $arSitesDefault,
	"def_location" => $rusID,
);
?>