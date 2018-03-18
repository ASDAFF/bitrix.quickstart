<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
if(!CModule::IncludeModule("iblock"))
	return;

$iblockCode = "furniture_".WIZARD_SITE_ID; 
$iblockType = "catalog"; 

$iblockID1 = WizardServices::ImportIBlockFromXML(
		 WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/gallery_catalog.xml", 
		 "g_catalog",
		 "gallery", 
		 WIZARD_SITE_ID, 
		 $permissions = Array(
			 "1" => "X",
			 "2" => "R",
		 )
	);

$iblockID2 = WizardServices::ImportIBlockFromXML(
		 WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/catalog_obyvka.xml", 
		 "g_catalog_o",
		 "gallery", 
		 WIZARD_SITE_ID, 
		 $permissions = Array(
			 "1" => "X",
			 "2" => "R",
		 )
	);

$iblockID3 = WizardServices::ImportIBlockFromXML(
		 WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/g_catalog_sxem.xml", 
		 "g_catalog_sxem",
		 "gallery", 
		 WIZARD_SITE_ID, 
		 $permissions = Array(
			 "1" => "X",
			 "2" => "R",
		 )
	);

$iblockID4 = WizardServices::ImportIBlockFromXML(
		 WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/g_catalog_t.xml", 
		 "g_catalog_t",
		 "gallery", 
		 WIZARD_SITE_ID, 
		 $permissions = Array(
			 "1" => "X",
			 "2" => "R",
		 )
	);	
	
$rsIBlock = CIBlock::GetList(array(), array("CODE" => 'softmebel_catalog', "TYPE" => $iblockType));
$iblockID = false; 
if ($arIBlock = $rsIBlock->Fetch())
{
	$iblockID = $arIBlock["ID"]; 	
	
}
else
{	

	$iblockID = WizardServices::ImportIBlockFromXML(
		 WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/catalog.xml", 
		 "softmebel_catalog",
		 "catalog", 
		 WIZARD_SITE_ID, 
		 $permissions = Array(
			 "1" => "X",
			 "2" => "R",
		 )
	);
}	
		
if ($iblockID < 1)
 return;

 
WizardServices::IncludeServiceLang("catalog.php", LANG); 
 
$SectionHitiblockID = false;
$hit_sec_list = CIBlockSection::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID"=>$iblockID, "NAME"=>GetMessage("DIVAN_LIDER")));	
if ($res_hit_sec = $hit_sec_list->Fetch())	
	$SectionHitiblockID = $res_hit_sec["ID"];
	
	
$SEctionNewprodIblock = false;
$hit_sec_list = CIBlockSection::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID"=>$iblockID, "NAME"=>GetMessage("DIVAN_NOVINKY")));	
if ($res_hit_sec = $hit_sec_list->Fetch())	
	$SEctionNewprodIblock = $res_hit_sec["ID"];	
	
$Section_ugol_ID = false;
$hit_sec_list = CIBlockSection::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID"=>$iblockID, "NAME"=>GetMessage("DIVAN_UGLOVY")));	
if ($res_hit_sec = $hit_sec_list->Fetch())	
	$Section_ugol_ID = $res_hit_sec["ID"];	
	
$divan_krovat_id = false;
$hit_sec_list = CIBlockSection::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID"=>$iblockID, "NAME"=>GetMessage("DIVAN_KROVAT")));	
if ($res_hit_sec = $hit_sec_list->Fetch())	
	$divan_krovat_id = $res_hit_sec["ID"];		
	
$kreslo_krovat_id = false;
$hit_sec_list = CIBlockSection::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID"=>$iblockID, "NAME"=>GetMessage("KRESLO_KROVAT")));	
if ($res_hit_sec = $hit_sec_list->Fetch())	
	$kreslo_krovat_id = $res_hit_sec["ID"];			
	
	
$modulny_divany = false;
$hit_sec_list = CIBlockSection::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID"=>$iblockID, "NAME"=>GetMessage("DIVAN_MODUL")));	
if ($res_hit_sec = $hit_sec_list->Fetch())	
	$modulny_divany = $res_hit_sec["ID"];			

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", array("CATALOG_IBLOCK_ID" => $iblockID, "SITE_DIR"=>SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.top.menu.php", array("CATALOG_IBLOCK_ID" => $iblockID, "SITE_DIR"=>SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.top.menu.php", array("CATALOG_IBLOCK_ID" => $iblockID, "SITE_DIR"=>SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/contacts/.top2.menu.php", array("SITE_DIR"=>SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/company/.top2.menu.php", array("SITE_DIR"=>SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/sect_right2_inc.php", array("CATALOG_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."catalog/index.php", array("CATALOG_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.left.menu_ext.php", array("CATALOG_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", array("SECTION_HIT_ID" => $SectionHitiblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", array("SECTION_NEWPROD_ID" => $SEctionNewprodIblock));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/sect_right1_inc.php", array("SECTION_HIT_ID" => $SectionHitiblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/sect_right1_inc.php", array("SECTION_NEWPROD_ID" => $SEctionNewprodIblock));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", array("CURRENT_SITE_HREF" => WIZARD_SITE_PATH)); 
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."catalog/.top2.menu.php", array("UGOL_DIVAN_ID" =>$Section_ugol_ID)); 
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."catalog/.top2.menu.php", array("DIVAN_KROVAT_ID" =>$divan_krovat_id)); 
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."catalog/.top2.menu.php", array("KRESLO_KROVAT_ID" =>$kreslo_krovat_id)); 
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."catalog/.top2.menu.php", array("MODULNY_DIVAN_ID" =>$modulny_divany, "SITE_DIR"=>SITE_DIR)); 

?>
