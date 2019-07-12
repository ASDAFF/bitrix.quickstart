<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();if(!CModule::IncludeModule("catalog"))return;if(!CModule::IncludeModule("sale"))return;if(!CModule::IncludeModule("currency"))return;if(COption::GetOptionString("catalog","1C_GROUP_PERMISSIONS")=="")
COption::SetOptionString("catalog","1C_GROUP_PERMISSIONS","1",GetMessage("SALE_1C_GROUP_PERMISSIONS"));$arGeneralInfo=Array();$dbSite=CSite::GetByID(WIZARD_SITE_ID);if($arSite=$dbSite->Fetch())
$lang=$arSite["LANGUAGE_ID"];if(strlen($lang)<=0)
$lang="ru";$bRus=false;if($lang=="ru")
$bRus=true;$defCurrency="EUR";if($lang=="ru")$defCurrency="RUB";elseif($lang=="en")$defCurrency="USD";$arLanguages=Array();$rsLanguage=CLanguage::GetList($by,$order,array());while($arLanguage=$rsLanguage->Fetch())
$arLanguages[]=$arLanguage["LID"];WizardServices::IncludeServiceLang("sale.php",$lang);$loc_file=$wizard->GetVar("locations_csv");if(strlen($loc_file)>0)
{define("LOC_STEP_LENGTH",20);$time_limit=ini_get("max_execution_time");if($time_limit<LOC_STEP_LENGTH)set_time_limit(LOC_STEP_LENGTH+5);$start_time=time();$finish_time=$start_time+LOC_STEP_LENGTH;if($loc_file=="loc_ussr.csv")
$file_url=$_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/locations/ru/".$loc_file;else $file_url=$_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/locations/".$loc_file;if(file_exists($file_url))
{$bFinish=true;$arSysLangs=Array();$db_lang=CLangAdmin::GetList(($b="sort"),($o="asc"),array("ACTIVE"=>"Y"));while($arLang=$db_lang->Fetch())
$arSysLangs[$arLang["LID"]]=$arLang["LID"];$arLocations=array();$bSync=true;$dbLocations=CSaleLocation::GetList(array(),array(),false,false,array("ID","COUNTRY_ID","REGION_ID","CITY_ID"));while($arLoc=$dbLocations->Fetch())
$arLocations[$arLoc["ID"]]=$arLoc;if(count($arLocations)<=0)
$bSync=false;include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/csv_data.php");$csvFile=new CCSVData();$csvFile->LoadFile($file_url);$csvFile->SetFieldsType("R");$csvFile->SetFirstHeader(false);$csvFile->SetDelimiter(",");$arRes=$csvFile->Fetch();if(is_array($arRes)&&count($arRes)>0&&strlen($arRes[0])==2)
{$DefLang=$arRes[0];if(in_array($DefLang,$arSysLangs))
{if(is_set($_SESSION["LOC_POS"]))
{$csvFile->SetPos($_SESSION["LOC_POS"]);$CurCountryID=$_SESSION["CUR_COUNTRY_ID"];$CurRegionID=$_SESSION["CUR_REGION_ID"];$numCountries=$_SESSION["NUM_COUNTRIES"];$numRegiones=$_SESSION["NUM_REGIONES"];$numCities=$_SESSION["NUM_CITIES"];$numLocations=$_SESSION["NUM_LOCATIONS"];}
else
{$CurCountryID=0;$CurRegionID=0;$numCountries=0;$numRegiones=0;$numCities=0;$numLocations=0;}
$tt=0;while($arRes=$csvFile->Fetch())
{$type=strtoupper($arRes[0]);$tt++;$arArrayTmp=array();foreach($arRes as $ind=>$value)
{if($ind%2&&isset($arSysLangs[$value]))
{$arArrayTmp[$value]=array("LID"=>$value,"NAME"=>$arRes[$ind+1]);if($value==$DefLang)
$arArrayTmp["NAME"]=$arRes[$ind+1];}}
if(is_array($arArrayTmp)&&strlen($arArrayTmp["NAME"])>0)
{if($type=="S")
{$CurCountryID=null;$arContList=array();$LLL=0;if($bSync)
{$db_contList=CSaleLocation::GetList(Array(),Array("COUNTRY_NAME"=>$arArrayTmp["NAME"],"LID"=>$DefLang));if($arContList=$db_contList->Fetch())
{$LLL=IntVal($arContList["ID"]);$CurCountryID=IntVal($arContList["COUNTRY_ID"]);}}
if(IntVal($CurCountryID)<=0)
{$CurCountryID=CSaleLocation::AddCountry($arArrayTmp);$CurCountryID=IntVal($CurCountryID);if($CurCountryID>0)
{$numCountries++;if(IntVal($LLL)<=0)
{$LLL=CSaleLocation::AddLocation(array("COUNTRY_ID"=>$CurCountryID));if(IntVal($LLL)>0)$numLocations++;}}}}
elseif($type=="R")
{$CurRegionID=null;$arRegionList=Array();$LLL=0;if($bSync)
{$db_rengList=CSaleLocation::GetList(Array(),Array("COUNTRY_ID"=>$CurCountryID,"REGION_NAME"=>$arArrayTmp["NAME"],"LID"=>$DefLang));if($arRegionList=$db_rengList->Fetch())
{$LLL=$arRegionList["ID"];$CurRegionID=IntVal($arRegionList["REGION_ID"]);}}
if(IntVal($CurRegionID)<=0)
{$CurRegionID=CSaleLocation::AddRegion($arArrayTmp);$CurRegionID=IntVal($CurRegionID);if($CurRegionID>0)
{$numRegiones++;if(IntVal($LLL)<=0)
{$LLL=CSaleLocation::AddLocation(array("COUNTRY_ID"=>$CurCountryID,"REGION_ID"=>$CurRegionID));if(IntVal($LLL)>0)$numLocations++;}}}}
elseif($type=="T"&&IntVal($CurCountryID)>0)
{$city_id=0;$LLL=0;$arCityList=Array();if($bSync)
{$arFilter=Array("COUNTRY_ID"=>$CurCountryID,"CITY_NAME"=>$arArrayTmp["NAME"],"LID"=>$DefLang);if(IntVal($CurRegionID)>0)
$arFilter["REGION_ID"]=$CurRegionID;$db_cityList=CSaleLocation::GetList(Array(),$arFilter);if($arCityList=$db_cityList->Fetch())
{$LLL=$arCityList["ID"];$city_id=IntVal($arCityList["CITY_ID"]);}}
if($city_id<=0)
{$city_id=CSaleLocation::AddCity($arArrayTmp);$city_id=IntVal($city_id);if($city_id>0)
$numCities++;}
if($city_id>0)
{if(IntVal($LLL)<=0)
{$LLL=CSaleLocation::AddLocation(array("COUNTRY_ID"=>$CurCountryID,"REGION_ID"=>$CurRegionID,"CITY_ID"=>$city_id));if(intval($LLL)>0)$numLocations++;}}}}
if($tt==10)
{$tt=0;$cur_time=time();if($cur_time>=$finish_time)
{$cur_step=$csvFile->GetPos();$amount=$csvFile->iFileLength;$_SESSION["LOC_POS"]=$cur_step;$_SESSION["CUR_COUNTRY_ID"]=$CurCountryID;$_SESSION["CUR_REGION_ID"]=$CurRegionID;$_SESSION["NUM_COUNTRIES"]=$numCountries;$_SESSION["NUM_REGIONES"]=$numRegiones;$_SESSION["NUM_CITIES"]=$numCities;$_SESSION["NUM_LOCATIONS"]=$numLocations;$this->repeatCurrentService=true;$bFinish=false;}}}}}
if($bFinish)unset($_SESSION["LOC_POS"]);else return true;$time_limit=ini_get("max_execution_time");if($time_limit<LOC_STEP_LENGTH)set_time_limit(LOC_STEP_LENGTH+5);$start_time=time();$finish_time=$start_time+LOC_STEP_LENGTH;if($loc_file=="loc_ussr.csv"&&file_exists($_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/locations/ru/zip_ussr.csv"))
{$rsLocations=CSaleLocation::GetList(array(),array("LID"=>"ru"),false,false,array("ID","CITY_NAME_LANG","REGION_NAME_LANG"));$arLocationMap=array();while($arLocation=$rsLocations->Fetch())
{if(strlen($arLocation["CITY_NAME_LANG"])>0)
{if(strlen($arLocation["REGION_NAME_LANG"])>0)
$arLocationMap[$arLocation["CITY_NAME_LANG"]][$arLocation["REGION_NAME_LANG"]]=$arLocation["ID"];else $arLocationMap[$arLocation["CITY_NAME_LANG"]]=$arLocation["ID"];}}
$DB->StartTransaction();include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/csv_data.php");$csvFile=new CCSVData();$csvFile->LoadFile($_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/locations/ru/zip_ussr.csv");$csvFile->SetFieldsType("R");$csvFile->SetFirstHeader(false);$csvFile->SetDelimiter(";");if(is_set($_SESSION,"ZIP_POS"))
{$numZIP=$_SESSION["NUM_ZIP"];$csvFile->SetPos($_SESSION["ZIP_POS"]);}
else
{CSaleLocation::ClearAllLocationZIP();unset($_SESSION["NUM_ZIP"]);$numZIP=0;}
$bFinish=true;$arLocationsZIP=array();$tt=0;$REGION="";while($arRes=$csvFile->Fetch())
{$tt++;$CITY=$arRes[1];if(strlen($arRes[3])>0)
$REGION=$arRes[3];if(array_key_exists($CITY,$arLocationMap))
{if(strlen($REGION)>0)
$ID=$arLocationMap[$CITY][$REGION];else $ID=$arLocationMap[$CITY];}
else $ID=0;if($ID)
{CSaleLocation::AddLocationZIP($ID,$arRes[2]);$numZIP++;}
if($tt==10)
{$tt=0;$cur_time=time();if($cur_time>=$finish_time)
{$cur_step=$csvFile->GetPos();$amount=$csvFile->iFileLength;$_SESSION["ZIP_POS"]=$cur_step;$_SESSION["NUM_ZIP"]=$numZIP;$bFinish=false;$this->repeatCurrentService=true;}}}
$DB->Commit();if($bFinish)unset($_SESSION["ZIP_POS"]);else return true;}}}
if(CSaleLang::GetByID(WIZARD_SITE_ID))
CSaleLang::Update(WIZARD_SITE_ID,array("LID"=>WIZARD_SITE_ID,"CURRENCY"=>"RUB"));else CSaleLang::Add(array("LID"=>WIZARD_SITE_ID,"CURRENCY"=>"RUB"));$shopLocation=$wizard->GetVar("shopLocation");$siteTelephone=$wizard->GetVar("siteTelephone");$paysystem=$wizard->GetVar("paysystem");$shopOfName=$wizard->GetVar("shopOfName");$shopAdr=$wizard->GetVar("shopAdr");$shopINN=$wizard->GetVar("shopINN");$shopKPP=$wizard->GetVar("shopKPP");$shopNS=$wizard->GetVar("shopNS");$shopBANK=$wizard->GetVar("shopBANK");$shopBANKREKV=$wizard->GetVar("shopBANKREKV");$shopKS=$wizard->GetVar("shopKS");$siteStamp=$wizard->GetVar("siteStamp");if($siteStamp=="")
$siteStamp=COption::GetOptionString("streetstyle","siteStamp","",WIZARD_SITE_ID);if(strlen($siteStamp)>0)
{if(IntVal($siteStamp)>0)
{$ff=CFile::GetByID($siteStamp);if($zr=$ff->Fetch())
{$strOldFile=str_replace("//","/",WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main","upload_dir","upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);@copy($strOldFile,WIZARD_SITE_PATH."images/stamp.gif");CFile::Delete($zr["ID"]);$siteStamp=WIZARD_SITE_DIR."images/stamp.gif";COption::SetOptionString("streetstyle","siteStamp",$siteStamp,false,WIZARD_SITE_ID);}}}
else $siteStamp=WIZARD_SITE_DIR."images/stamp.gif";if(!$bRus)
$shopLocation=GetMessage("WIZ_CITY");$location=0;$dbLocation=CSaleLocation::GetList(Array("ID"=>"ASC"),Array("LID"=>$lang,"CITY_NAME"=>$shopLocation));if($arLocation=$dbLocation->Fetch())
$location=$arLocation["ID"];if(IntVal($location)<=0)
{$CurCountryID=0;$db_contList=CSaleLocation::GetList(Array(),Array("COUNTRY_NAME"=>GetMessage("WIZ_COUNTRY"),"LID"=>$langID));if($arContList=$db_contList->Fetch())
{$LLL=IntVal($arContList["ID"]);$CurCountryID=IntVal($arContList["COUNTRY_ID"]);}
if(IntVal($CurCountryID)<=0)
{$arArrayTmp=Array();$arArrayTmp["NAME"]=GetMessage("WIZ_COUNTRY");foreach($arLanguages as $langID)
{WizardServices::IncludeServiceLang("sale.php",$langID);$arArrayTmp[$langID]=array("LID"=>$langID,"NAME"=>GetMessage("WIZ_COUNTRY"));}
$CurCountryID=CSaleLocation::AddCountry($arArrayTmp);}
$arArrayTmp=Array();$arArrayTmp["NAME"]=$shopLocation;foreach($arLanguages as $langID)
{$arArrayTmp[$langID]=array("LID"=>$langID,"NAME"=>$shopLocation);}
$city_id=CSaleLocation::AddCity($arArrayTmp);$location=CSaleLocation::AddLocation(array("COUNTRY_ID"=>$CurCountryID,"CITY_ID"=>$city_id));if($bRus)
CSaleLocation::AddLocationZIP($location,"101000");}
COption::SetOptionString("sale","location",$location);$dbPerson=CSalePersonType::GetList();while($arPerson=$dbPerson->Fetch())
{$arPersonTypeNames[$arPerson["NAME"]]["LIDS"]=$arPerson["LIDS"];$arPersonTypeNames[$arPerson["NAME"]]["ID"]=$arPerson["ID"];}
$personType=$wizard->GetVar("personType");$personTypeFiz=($personType["fiz"]=="Y")?"Y":"N";$personTypeUr=($personType["ur"]=="Y")?"Y":"N";$fizExist=array_key_exists(GetMessage("SALE_WIZARD_PERSON_1"),$arPersonTypeNames)?true:false;$urExist=array_key_exists(GetMessage("SALE_WIZARD_PERSON_2"),$arPersonTypeNames)?true:false;if($fizExist)
{$arGeneralInfo["personType"]["fiz"]=$arPersonTypeNames[GetMessage("SALE_WIZARD_PERSON_1")]["ID"];$lids=array();$lids=$arPersonTypeNames[GetMessage("SALE_WIZARD_PERSON_1")]["LIDS"];if(!in_array(WIZARD_SITE_ID,$lids)&&$personTypeFiz=="Y")
$lids[]=WIZARD_SITE_ID;if(in_array(WIZARD_SITE_ID,$lids)&&$personTypeFiz=="N")
unset($lids[array_search(WIZARD_SITE_ID,$lids)]);CSalePersonType::Update($arGeneralInfo["personType"]["fiz"],Array("ACTIVE"=>$personTypeFiz,"LID"=>$lids,));}
elseif($personType["fiz"]=="Y")
{$arGeneralInfo["personType"]["fiz"]=CSalePersonType::Add(Array("LID"=>WIZARD_SITE_ID,"NAME"=>GetMessage("SALE_WIZARD_PERSON_1"),"SORT"=>"100"));}
if($urExist)
{$arGeneralInfo["personType"]["ur"]=$arPersonTypeNames[GetMessage("SALE_WIZARD_PERSON_2")]["ID"];$lids=array();$lids=$arPersonTypeNames[GetMessage("SALE_WIZARD_PERSON_2")]["LIDS"];if(!in_array(WIZARD_SITE_ID,$lids)&&$personTypeUr=="Y")
$lids[]=WIZARD_SITE_ID;if(in_array(WIZARD_SITE_ID,$lids)&&$personTypeUr=="N")
unset($lids[array_search(WIZARD_SITE_ID,$lids)]);CSalePersonType::Update($arGeneralInfo["personType"]["ur"],Array("ACTIVE"=>$personTypeUr,"LID"=>$lids,));}
elseif($personType["ur"]=="Y")
{$arGeneralInfo["personType"]["ur"]=CSalePersonType::Add(Array("LID"=>WIZARD_SITE_ID,"NAME"=>GetMessage("SALE_WIZARD_PERSON_2"),"SORT"=>"150"));}
$arProps=Array();if($fizExist||$personType["fiz"]=="Y")
{$dbSaleOrderPropsGroup=CSaleOrderPropsGroup::GetList(Array(),Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>GetMessage("SALE_WIZARD_PROP_GROUP_FIZ1")),false,false,array("ID"));if($arSaleOrderPropsGroup=$dbSaleOrderPropsGroup->GetNext())
$arGeneralInfo["propGroup"]["user_fiz"]=$arSaleOrderPropsGroup["ID"];$dbSaleOrderPropsGroup=CSaleOrderPropsGroup::GetList(Array(),Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>GetMessage("SALE_WIZARD_PROP_GROUP_FIZ2")),false,false,array("ID"));if($arSaleOrderPropsGroup=$dbSaleOrderPropsGroup->GetNext())
$arGeneralInfo["propGroup"]["adres_fiz"]=$arSaleOrderPropsGroup["ID"];if($arGeneralInfo["propGroup"]["user_fiz"]<1)
$arGeneralInfo["propGroup"]["user_fiz"]=CSaleOrderPropsGroup::Add(Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>GetMessage("SALE_WIZARD_PROP_GROUP_FIZ1"),"SORT"=>100));if($arGeneralInfo["propGroup"]["adres_fiz"]<1)
$arGeneralInfo["propGroup"]["adres_fiz"]=CSaleOrderPropsGroup::Add(Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>GetMessage("SALE_WIZARD_PROP_GROUP_FIZ2"),"SORT"=>200));$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>GetMessage("SALE_WIZARD_PROP_6"),"TYPE"=>"TEXT","REQUIED"=>"Y","DEFAULT_VALUE"=>"","SORT"=>100,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["user_fiz"],"SIZE1"=>40,"SIZE2"=>0,"DESCRIPTION"=>"","IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"Y","IS_PAYER"=>"Y","IS_LOCATION4TAX"=>"N","CODE"=>"FIO","IS_FILTERED"=>"Y",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>"E-Mail","TYPE"=>"TEXT","REQUIED"=>"Y","DEFAULT_VALUE"=>"","SORT"=>110,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["user_fiz"],"SIZE1"=>40,"SIZE2"=>0,"DESCRIPTION"=>"","IS_EMAIL"=>"Y","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"EMAIL","IS_FILTERED"=>"Y",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>GetMessage("SALE_WIZARD_PROP_9"),"TYPE"=>"TEXT","REQUIED"=>"Y","DEFAULT_VALUE"=>"","SORT"=>120,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["user_fiz"],"SIZE1"=>0,"SIZE2"=>0,"DESCRIPTION"=>"","IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"PHONE","IS_FILTERED"=>"N",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>GetMessage("SALE_WIZARD_PROP_4"),"TYPE"=>"TEXT","REQUIED"=>"N","DEFAULT_VALUE"=>"101000","SORT"=>130,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["adres_fiz"],"SIZE1"=>8,"SIZE2"=>0,"DESCRIPTION"=>GetMessage("SALE_WIZARD_PROP_1"),"IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"ZIP","IS_FILTERED"=>"N","IS_ZIP"=>"Y",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>GetMessage("SALE_WIZARD_PROP_21"),"TYPE"=>"TEXT","REQUIED"=>"N","DEFAULT_VALUE"=>$shopLocation,"SORT"=>145,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["adres_fiz"],"SIZE1"=>40,"SIZE2"=>0,"DESCRIPTION"=>"","IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"CITY","IS_FILTERED"=>"Y",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>GetMessage("SALE_WIZARD_PROP_2"),"TYPE"=>"LOCATION","REQUIED"=>"Y","DEFAULT_VALUE"=>$location,"SORT"=>140,"USER_PROPS"=>"Y","IS_LOCATION"=>"Y","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["adres_fiz"],"SIZE1"=>40,"SIZE2"=>0,"DESCRIPTION"=>GetMessage("SALE_WIZARD_PROP_1"),"IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"LOCATION","IS_FILTERED"=>"N","INPUT_FIELD_LOCATION"=>"");$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>GetMessage("SALE_WIZARD_PROP_5"),"TYPE"=>"TEXTAREA","REQUIED"=>"N","DEFAULT_VALUE"=>"","SORT"=>150,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["adres_fiz"],"SIZE1"=>30,"SIZE2"=>3,"DESCRIPTION"=>"","IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"ADDRESS","IS_FILTERED"=>"N",);}
if($urExist||$personType["ur"]=="Y")
{$dbSaleOrderPropsGroup=CSaleOrderPropsGroup::GetList(Array(),Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PROP_GROUP_UR1")),false,false,array("ID"));if($arSaleOrderPropsGroup=$dbSaleOrderPropsGroup->GetNext())
$arGeneralInfo["propGroup"]["user_ur"]=$arSaleOrderPropsGroup["ID"];$dbSaleOrderPropsGroup=CSaleOrderPropsGroup::GetList(Array(),Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PROP_GROUP_UR2")),false,false,array("ID"));if($arSaleOrderPropsGroup=$dbSaleOrderPropsGroup->GetNext())
$arGeneralInfo["propGroup"]["adres_ur"]=$arSaleOrderPropsGroup["ID"];if($arGeneralInfo["propGroup"]["user_ur"]<1)
$arGeneralInfo["propGroup"]["user_ur"]=CSaleOrderPropsGroup::Add(Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PROP_GROUP_UR1"),"SORT"=>300));if($arGeneralInfo["propGroup"]["adres_ur"]<1)
$arGeneralInfo["propGroup"]["adres_ur"]=CSaleOrderPropsGroup::Add(Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PROP_GROUP_UR2"),"SORT"=>400));$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PROP_8"),"TYPE"=>"TEXT","REQUIED"=>"Y","DEFAULT_VALUE"=>"","SORT"=>200,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["user_ur"],"SIZE1"=>40,"SIZE2"=>0,"DESCRIPTION"=>"","IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"Y","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"COMPANY","IS_FILTERED"=>"Y",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PROP_7"),"TYPE"=>"TEXTAREA","REQUIED"=>"N","DEFAULT_VALUE"=>"","SORT"=>210,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["user_ur"],"SIZE1"=>40,"SIZE2"=>0,"DESCRIPTION"=>"","IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"COMPANY_ADR","IS_FILTERED"=>"N",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PROP_13"),"TYPE"=>"TEXT","REQUIED"=>"N","DEFAULT_VALUE"=>"","SORT"=>220,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["user_ur"],"SIZE1"=>0,"SIZE2"=>0,"DESCRIPTION"=>"","IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"INN","IS_FILTERED"=>"N",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PROP_14"),"TYPE"=>"TEXT","REQUIED"=>"N","DEFAULT_VALUE"=>"","SORT"=>230,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["user_ur"],"SIZE1"=>0,"SIZE2"=>0,"DESCRIPTION"=>"","IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"KPP","IS_FILTERED"=>"N",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PROP_10"),"TYPE"=>"TEXT","REQUIED"=>"Y","DEFAULT_VALUE"=>"","SORT"=>240,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["adres_ur"],"SIZE1"=>0,"SIZE2"=>0,"DESCRIPTION"=>"","IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"Y","IS_LOCATION4TAX"=>"N","CODE"=>"CONTACT_PERSON","IS_FILTERED"=>"N",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>"E-Mail","TYPE"=>"TEXT","REQUIED"=>"Y","DEFAULT_VALUE"=>"","SORT"=>250,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["adres_ur"],"SIZE1"=>40,"SIZE2"=>0,"DESCRIPTION"=>"","IS_EMAIL"=>"Y","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"EMAIL","IS_FILTERED"=>"N",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PROP_9"),"TYPE"=>"TEXT","REQUIED"=>"N","DEFAULT_VALUE"=>"","SORT"=>260,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["adres_ur"],"SIZE1"=>0,"SIZE2"=>0,"DESCRIPTION"=>"","IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"PHONE","IS_FILTERED"=>"N",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PROP_11"),"TYPE"=>"TEXT","REQUIED"=>"N","DEFAULT_VALUE"=>"","SORT"=>270,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["adres_ur"],"SIZE1"=>0,"SIZE2"=>0,"DESCRIPTION"=>"","IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"FAX","IS_FILTERED"=>"N",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PROP_4"),"TYPE"=>"TEXT","REQUIED"=>"N","DEFAULT_VALUE"=>"101000","SORT"=>280,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["adres_ur"],"SIZE1"=>8,"SIZE2"=>0,"DESCRIPTION"=>GetMessage("SALE_WIZARD_PROP_1"),"IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"ZIP","IS_FILTERED"=>"N","IS_ZIP"=>"Y",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PROP_21"),"TYPE"=>"TEXT","REQUIED"=>"N","DEFAULT_VALUE"=>$shopLocation,"SORT"=>285,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["adres_ur"],"SIZE1"=>40,"SIZE2"=>0,"DESCRIPTION"=>"","IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"CITY","IS_FILTERED"=>"Y",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PROP_2"),"TYPE"=>"LOCATION","REQUIED"=>"Y","DEFAULT_VALUE"=>"","SORT"=>290,"USER_PROPS"=>"Y","IS_LOCATION"=>"Y","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["adres_ur"],"SIZE1"=>40,"SIZE2"=>0,"DESCRIPTION"=>GetMessage("SALE_WIZARD_PROP_1"),"IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"Y","CODE"=>"LOCATION","IS_FILTERED"=>"N",);$arProps[]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PROP_12"),"TYPE"=>"TEXTAREA","REQUIED"=>"N","DEFAULT_VALUE"=>"","SORT"=>300,"USER_PROPS"=>"Y","IS_LOCATION"=>"N","PROPS_GROUP_ID"=>$arGeneralInfo["propGroup"]["adres_ur"],"SIZE1"=>30,"SIZE2"=>40,"DESCRIPTION"=>"","IS_EMAIL"=>"N","IS_PROFILE_NAME"=>"N","IS_PAYER"=>"N","IS_LOCATION4TAX"=>"N","CODE"=>"ADDRESS","IS_FILTERED"=>"N",);}
$propCityId=0;foreach($arProps as $prop)
{$variants=Array();if(!empty($prop["VARIANTS"]))
{$variants=$prop["VARIANTS"];unset($prop["VARIANTS"]);}
if($prop["CODE"]=="LOCATION"&&$propCityId>0)
{$prop["INPUT_FIELD_LOCATION"]=$propCityId;$propCityId=0;}
$dbSaleOrderProps=CSaleOrderProps::GetList(array(),array("PERSON_TYPE_ID"=>$prop["PERSON_TYPE_ID"],"CODE"=>$prop["CODE"]));if($arSaleOrderProps=$dbSaleOrderProps->GetNext())
$id=$arSaleOrderProps["ID"];else
$id=CSaleOrderProps::Add($prop);if($prop["CODE"]=="CITY")
$propCityId=$id;if(strlen($prop["CODE"])>0)
{$arGeneralInfo["propCodeID"][$prop["CODE"]]=$id;$arGeneralInfo["properies"][$prop["PERSON_TYPE_ID"]][$prop["CODE"]]=$prop;$arGeneralInfo["properies"][$prop["PERSON_TYPE_ID"]][$prop["CODE"]]["ID"]=$id;}
if(!empty($variants))
{foreach($variants as $val)
{$val["ORDER_PROPS_ID"]=$id;CSaleOrderPropsVariant::Add($val);}}}
$propReplace="";foreach($arGeneralInfo["properies"]as $key=>$val)
{if(IntVal($val["LOCATION"]["ID"])>0)
$propReplace.='"PROP_'.$key.'" => Array(0 => "'.$val["LOCATION"]["ID"].'"), ';}
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."personal/order/",Array("PROPS"=>$propReplace));if($personType["fiz"]=="Y"&&!$fizExist)
{$val=serialize(Array("AGENT_NAME"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["FIO"]["ID"]),"FULL_NAME"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["FIO"]["ID"]),"SURNAME"=>Array("TYPE"=>"USER","VALUE"=>"LAST_NAME"),"NAME"=>Array("TYPE"=>"USER","VALUE"=>"NAME"),"ADDRESS_FULL"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["ADDRESS"]["ID"]),"INDEX"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["ZIP"]["ID"]),"COUNTRY"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["LOCATION"]["ID"]."_COUNTRY"),"CITY"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["LOCATION"]["ID"]."_CITY"),"STREET"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["ADDRESS"]["ID"]),"EMAIL"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["EMAIL"]["ID"]),"CONTACT_PERSON"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["CONTACT_PERSON"]["ID"]),"IS_FIZ"=>"Y",));CSaleExport::Add(Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"VARS"=>$val));}
if($personType["ur"]=="Y"&&!$urExist)
{$val=serialize(Array("AGENT_NAME"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["COMPANY"]["ID"]),"FULL_NAME"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["COMPANY"]["ID"]),"ADDRESS_FULL"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["COMPANY_ADR"]["ID"]),"COUNTRY"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["LOCATION"]["ID"]."_COUNTRY"),"CITY"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["LOCATION"]["ID"]."_CITY"),"STREET"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["COMPANY_ADR"]["ID"]),"INN"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["INN"]["ID"]),"KPP"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["KPP"]["ID"]),"PHONE"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["PHONE"]["ID"]),"EMAIL"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["EMAIL"]["ID"]),"CONTACT_PERSON"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["NAME"]["ID"]),"F_ADDRESS_FULL"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["ADDRESS"]["ID"]),"F_COUNTRY"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["LOCATION"]["ID"]."_COUNTRY"),"F_CITY"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["LOCATION"]["ID"]."_CITY"),"F_INDEX"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["ZIP"]["ID"]),"F_STREET"=>Array("TYPE"=>"PROPERTY","VALUE"=>$arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["ADDRESS"]["ID"]),"IS_FIZ"=>"N",));CSaleExport::Add(Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"VARS"=>$val));}
$locationGroupID=0;$arLocation4Delivery=Array();$arLocationArr=Array();$dbLocation=CSaleLocation::GetList(Array(),array("LID"=>$lang));while($arLocation=$dbLocation->Fetch())
{$arLocation4Delivery[]=Array("LOCATION_ID"=>$arLocation["ID"],"LOCATION_TYPE"=>"L");$arLocationArr[]=$arLocation["ID"];}
$dbGroup=CSaleLocationGroup::GetList();if($arGroup=$dbGroup->Fetch())
$arLocation4Delivery[]=Array("LOCATION_ID"=>$arGroup["ID"],"LOCATION_TYPE"=>"G");else
{$groupLang=array(array("LID"=>"en","NAME"=>"Group 1"));if($bRus)
$groupLang[]=array("LID"=>$lang,"NAME"=>GetMessage("SALE_WIZARD_GROUP"));$locationGroupID=CSaleLocationGroup::Add(array("SORT"=>150,"LOCATION_ID"=>$arLocationArr,"LANG"=>$groupLang));}
if(IntVal($locationGroupID)>0)
$arLocation4Delivery[]=Array("LOCATION_ID"=>$locationGroupID,"LOCATION_TYPE"=>"G");$delivery=$wizard->GetVar("delivery");$dbDelivery_=array();$dbDelivery=CSaleDelivery::GetList(array(),Array("LID"=>WIZARD_SITE_ID));while($ar=$dbDelivery->Fetch())
$dbDelivery_[$ar["NAME"]]=$ar;$arFields=Array("NAME"=>GetMessage("SALE_WIZARD_COUR"),"LID"=>WIZARD_SITE_ID,"PERIOD_FROM"=>0,"PERIOD_TO"=>0,"PERIOD_TYPE"=>"D","WEIGHT_FROM"=>0,"WEIGHT_TO"=>0,"ORDER_PRICE_FROM"=>0,"ORDER_PRICE_TO"=>0,"ORDER_CURRENCY"=>$defCurrency,"ACTIVE"=>"Y","PRICE"=>($bRus?"500":"30"),"CURRENCY"=>$defCurrency,"SORT"=>100,"DESCRIPTION"=>GetMessage("SALE_WIZARD_COUR_DESCR"),"LOCATIONS"=>$arLocation4Delivery,);if($delivery["courier"]!="Y")
$arFields["ACTIVE"]="N";if(!array_key_exists(GetMessage("SALE_WIZARD_COUR"),$dbDelivery_))
CSaleDelivery::Add($arFields);$arFields=Array("NAME"=>GetMessage("SALE_WIZARD_COUR1"),"LID"=>WIZARD_SITE_ID,"PERIOD_FROM"=>0,"PERIOD_TO"=>0,"PERIOD_TYPE"=>"D","WEIGHT_FROM"=>0,"WEIGHT_TO"=>0,"ORDER_PRICE_FROM"=>0,"ORDER_PRICE_TO"=>0,"ORDER_CURRENCY"=>$defCurrency,"ACTIVE"=>"Y","PRICE"=>0,"CURRENCY"=>$defCurrency,"SORT"=>200,"DESCRIPTION"=>GetMessage("SALE_WIZARD_COUR1_DESCR"),"LOCATIONS"=>$arLocation4Delivery,);if($delivery["self"]!="Y")
$arFields["ACTIVE"]="N";if(!array_key_exists(GetMessage("SALE_WIZARD_COUR1"),$dbDelivery_))
CSaleDelivery::Add($arFields);if($bRus)
{$arFields=Array("LID"=>"","ACTIVE"=>"N","HID"=>"cpcr","NAME"=>GetMessage("SALE_WIZARD_SPSR"),"SORT"=>100,"DESCRIPTION"=>GetMessage("SALE_WIZARD_SPSR_DESCR"),"HANDLERS"=>"/bitrix/modules/sale/delivery/delivery_cpcr.php","SETTINGS"=>"8","PROFILES"=>"","TAX_RATE"=>0,);if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif"))
$arFields["LOGOTIP"]=CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif");CSaleDeliveryHandler::Set("cpcr",$arFields);$arFields=Array("LID"=>"","ACTIVE"=>"Y","HID"=>"russianpost","NAME"=>GetMessage("SALE_WIZARD_MAIL"),"SORT"=>400,"DESCRIPTION"=>GetMessage("SALE_WIZARD_MAIL_DESC"),"HANDLERS"=>"/bitrix/modules/sale/ru/delivery/delivery_russianpost.php","SETTINGS"=>"23","PROFILES"=>"","TAX_RATE"=>0,);if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif"))
$arFields["LOGOTIP"]=CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif");if($delivery["russianpost"]!="Y")
$arFields["ACTIVE"]="N";CSaleDeliveryHandler::Set("russianpost",$arFields);$arFields=Array("LID"=>"","ACTIVE"=>"Y","HID"=>"rus_post","NAME"=>GetMessage("SALE_WIZARD_MAIL2"),"SORT"=>400,"DESCRIPTION"=>GetMessage("SALE_WIZARD_MAIL_DESC"),"HANDLERS"=>"/bitrix/modules/sale/delivery/delivery_rus_post.php","SETTINGS"=>"23","PROFILES"=>"","TAX_RATE"=>0,);if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif"))
$arFields["LOGOTIP"]=CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif");if($delivery["russianpost"]!="Y")
$arFields["ACTIVE"]="N";CSaleDeliveryHandler::Set("rus_post",$arFields);}
$arFields=Array("LID"=>"","ACTIVE"=>"Y","HID"=>"ups","NAME"=>"UPS","SORT"=>300,"DESCRIPTION"=>GetMessage("SALE_WIZARD_UPS"),"HANDLERS"=>"/bitrix/modules/sale/delivery/delivery_ups.php","SETTINGS"=>"/bitrix/modules/sale/delivery/ups/ru_csv_zones.csv;/bitrix/modules/sale/delivery/ups/ru_csv_export.csv","PROFILES"=>"","TAX_RATE"=>0,);if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/delivery/".$arFields["HID"]."_logo.gif"))
$arFields["LOGOTIP"]=CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/delivery/".$arFields["HID"]."_logo.gif");if($delivery["ups"]!="Y")
$arFields["ACTIVE"]="N";CSaleDeliveryHandler::Set("ups",$arFields);$arFields=Array("LID"=>"","ACTIVE"=>"Y","HID"=>"dhlusa","NAME"=>"DHL (USA)","SORT"=>400,"DESCRIPTION"=>GetMessage("SALE_WIZARD_UPS"),"HANDLERS"=>"/bitrix/modules/sale/delivery/delivery_dhl_usa.php ","SETTINGS"=>"","PROFILES"=>"","TAX_RATE"=>0,);if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/delivery/".$arFields["HID"]."_logo.gif"))
$arFields["LOGOTIP"]=CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/delivery/".$arFields["HID"]."_logo.gif");if($delivery["dhl"]!="Y")
$arFields["ACTIVE"]="N";CSaleDeliveryHandler::Set("dhlusa",$arFields);$arPaySystems=Array();if($paysystem["cash"]=="Y")
{$arPaySystemTmp=Array("NAME"=>GetMessage("SALE_WIZARD_PS_CASH"),"SORT"=>50,"ACTIVE"=>"Y","DESCRIPTION"=>GetMessage("SALE_WIZARD_PS_CASH_DESCR"),"CODE_TEMP"=>"cash");if($personType["fiz"]=="Y")
{$arPaySystemTmp["ACTION"][]=Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>GetMessage("SALE_WIZARD_PS_CASH"),"ACTION_FILE"=>"/bitrix/modules/sale/payment/cash","RESULT_FILE"=>"","NEW_WINDOW"=>"N","PARAMS"=>"","HAVE_PAYMENT"=>"Y","HAVE_ACTION"=>"N","HAVE_RESULT"=>"N","HAVE_PREPAY"=>"N","HAVE_RESULT_RECEIVE"=>"N",);}
$arPaySystems[]=$arPaySystemTmp;}
if($personType["fiz"]=="Y")
{if($bRus)
{$arPaySystems[]=Array("NAME"=>GetMessage("SALE_WIZARD_PS_CC"),"SORT"=>60,"ACTIVE"=>"N","DESCRIPTION"=>GetMessage("SALE_WIZARD_PS_CC_DESCR"),"CODE_TEMP"=>"card","ACTION"=>Array(Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>GetMessage("SALE_WIZARD_PS_CC"),"ACTION_FILE"=>"/bitrix/modules/sale/payment/assist","RESULT_FILE"=>"/bitrix/modules/sale/payment/assist_res.php","NEW_WINDOW"=>"N","PARAMS"=>serialize(Array("FIRST_NAME"=>Array("TYPE"=>"USER","VALUE"=>"NAME"),"LAST_NAME"=>Array("TYPE"=>"USER","VALUE"=>"LAST_NAME"),"EMAIL"=>Array("TYPE"=>"PROPERTY","VALUE"=>"EMAIL"),"ADDRESS"=>Array("TYPE"=>"PROPERTY","VALUE"=>"ADDRESS"),)),"HAVE_PAYMENT"=>"Y","HAVE_ACTION"=>"N","HAVE_RESULT"=>"Y","HAVE_PREPAY"=>"N","HAVE_RESULT_RECEIVE"=>"N",)));$arPaySystems[]=Array("NAME"=>GetMessage("SALE_WIZARD_PS_WM"),"SORT"=>70,"ACTIVE"=>"N","DESCRIPTION"=>GetMessage("SALE_WIZARD_PS_WM_DESCR"),"CODE_TEMP"=>"webmoney","ACTION"=>Array(Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>GetMessage("SALE_WIZARD_PS_WM"),"ACTION_FILE"=>"/bitrix/modules/sale/payment/webmoney_web","RESULT_FILE"=>"","NEW_WINDOW"=>"Y","PARAMS"=>"","HAVE_PAYMENT"=>"Y","HAVE_ACTION"=>"N","HAVE_RESULT"=>"Y","HAVE_PREPAY"=>"N","HAVE_RESULT_RECEIVE"=>"N",)));$arPaySystems[]=Array("NAME"=>GetMessage("SALE_WIZARD_PS_PC"),"SORT"=>80,"ACTIVE"=>"N","DESCRIPTION"=>"","CODE_TEMP"=>"paycash","ACTION"=>Array(Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>GetMessage("SALE_WIZARD_PS_PC"),"ACTION_FILE"=>"/bitrix/modules/sale/payment/yandex","RESULT_FILE"=>"","NEW_WINDOW"=>"N","PARAMS"=>serialize(Array("ORDER_ID"=>Array("TYPE"=>"ORDER","VALUE"=>"ID"),"USER_ID"=>Array("TYPE"=>"PROPERTY","VALUE"=>"FIO"),"ORDER_DATE"=>Array("TYPE"=>"ORDER","VALUE"=>"DATE_INSERT"),"SHOULD_PAY"=>Array("TYPE"=>"ORDER","VALUE"=>"PRICE"),)),"HAVE_PAYMENT"=>"Y","HAVE_ACTION"=>"N","HAVE_RESULT"=>"N","HAVE_PREPAY"=>"N","HAVE_RESULT_RECEIVE"=>"Y",)));if($paysystem["sber"]=="Y")
{$arPaySystems[]=Array("NAME"=>GetMessage("SALE_WIZARD_PS_SB"),"SORT"=>90,"DESCRIPTION"=>GetMessage("SALE_WIZARD_PS_SB_DESCR"),"CODE_TEMP"=>"sberbank","ACTION"=>Array(Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>GetMessage("SALE_WIZARD_PS_SB"),"ACTION_FILE"=>"/bitrix/modules/sale/payment/sberbank_new","RESULT_FILE"=>"","NEW_WINDOW"=>"Y","PARAMS"=>serialize(Array("COMPANY_NAME"=>Array("TYPE"=>"","VALUE"=>$shopOfName),"INN"=>Array("TYPE"=>"","VALUE"=>$shopINN),"KPP"=>Array("TYPE"=>"","VALUE"=>$shopKPP),"SETTLEMENT_ACCOUNT"=>Array("TYPE"=>"","VALUE"=>$shopNS),"BANK_NAME"=>Array("TYPE"=>"","VALUE"=>$shopBANK),"BANK_BIC"=>Array("TYPE"=>"","VALUE"=>$shopBANKREKV),"BANK_COR_ACCOUNT"=>Array("TYPE"=>"","VALUE"=>$shopKS),"ORDER_ID"=>Array("TYPE"=>"ORDER","VALUE"=>"ID"),"DATE_INSERT"=>Array("TYPE"=>"ORDER","VALUE"=>"DATE_INSERT_DATE"),"PAYER_CONTACT_PERSON"=>Array("TYPE"=>"PROPERTY","VALUE"=>"FIO"),"PAYER_ZIP_CODE"=>Array("TYPE"=>"PROPERTY","VALUE"=>"ZIP"),"PAYER_COUNTRY"=>Array("TYPE"=>"PROPERTY","VALUE"=>"LOCATION_COUNTRY"),"PAYER_CITY"=>Array("TYPE"=>"PROPERTY","VALUE"=>"LOCATION_CITY"),"PAYER_ADDRESS_FACT"=>Array("TYPE"=>"PROPERTY","VALUE"=>"ADDRESS"),"SHOULD_PAY"=>Array("TYPE"=>"ORDER","VALUE"=>"PRICE"),)),"HAVE_PAYMENT"=>"Y","HAVE_ACTION"=>"N","HAVE_RESULT"=>"N","HAVE_PREPAY"=>"N","HAVE_RESULT_RECEIVE"=>"N",)));}}
else
{$arPaySystems[]=Array("NAME"=>"PayPal","SORT"=>90,"DESCRIPTION"=>"","CODE_TEMP"=>"paypal","ACTION"=>Array(Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["fiz"],"NAME"=>"PayPal","ACTION_FILE"=>"/bitrix/modules/sale/payment/paypal","RESULT_FILE"=>"","NEW_WINDOW"=>"N","PARAMS"=>serialize(Array("ORDER_ID"=>Array("TYPE"=>"ORDER","VALUE"=>"ID"),"DATE_INSERT"=>Array("TYPE"=>"ORDER","VALUE"=>"DATE_INSERT_DATE"),"SHOULD_PAY"=>Array("TYPE"=>"ORDER","VALUE"=>"SHOULD_PAY"),"CURRENCY"=>Array("TYPE"=>"ORDER","VALUE"=>"CURRENCY"),)),"HAVE_PAYMENT"=>"Y","HAVE_ACTION"=>"N","HAVE_RESULT"=>"N","HAVE_PREPAY"=>"N","HAVE_RESULT_RECEIVE"=>"Y",)));}}
if($personType["ur"]=="Y"&&$paysystem["bill"]=="Y")
{$arPaySystems[]=Array("NAME"=>GetMessage("SALE_WIZARD_PS_BILL"),"SORT"=>100,"DESCRIPTION"=>"","CODE_TEMP"=>"bill","ACTION"=>Array(Array("PERSON_TYPE_ID"=>$arGeneralInfo["personType"]["ur"],"NAME"=>GetMessage("SALE_WIZARD_PS_BILL"),"ACTION_FILE"=>"/bitrix/modules/sale/payment/bill","RESULT_FILE"=>"","NEW_WINDOW"=>"Y","PARAMS"=>serialize(Array("DATE_INSERT"=>Array("TYPE"=>"ORDER","VALUE"=>"DATE_INSERT_DATE"),"SELLER_NAME"=>Array("TYPE"=>"","VALUE"=>$shopOfName),"SELLER_ADDRESS"=>Array("TYPE"=>"","VALUE"=>$shopAdr),"SELLER_PHONE"=>Array("TYPE"=>"","VALUE"=>$siteTelephone),"SELLER_INN"=>Array("TYPE"=>"","VALUE"=>$shopINN),"SELLER_KPP"=>Array("TYPE"=>"","VALUE"=>$shopKPP),"SELLER_RS"=>Array("TYPE"=>"","VALUE"=>$shopNS),"SELLER_KS"=>Array("TYPE"=>"","VALUE"=>$shopKS),"SELLER_BIK"=>Array("TYPE"=>"","VALUE"=>$shopBANKREKV),"BUYER_NAME"=>Array("TYPE"=>"PROPERTY","VALUE"=>"COMPANY_NAME"),"BUYER_INN"=>Array("TYPE"=>"PROPERTY","VALUE"=>"INN"),"BUYER_ADDRESS"=>Array("TYPE"=>"PROPERTY","VALUE"=>"COMPANY_ADR"),"BUYER_PHONE"=>Array("TYPE"=>"PROPERTY","VALUE"=>"PHONE"),"BUYER_FAX"=>Array("TYPE"=>"PROPERTY","VALUE"=>"FAX"),"BUYER_PAYER_NAME"=>Array("TYPE"=>"PROPERTY","VALUE"=>"CONTACT_PERSON"),"PATH_TO_STAMP"=>Array("TYPE"=>"","VALUE"=>$siteStamp),)),"HAVE_PAYMENT"=>"Y","HAVE_ACTION"=>"N","HAVE_RESULT"=>"N","HAVE_PREPAY"=>"N","HAVE_RESULT_RECEIVE"=>"N",)));}
foreach($arPaySystems as $val)
{$dbSalePaySystem=CSalePaySystem::GetList(array(),array("NAME"=>$val["NAME"]),false,false,array("ID","NAME"));if($arSalePaySystem=$dbSalePaySystem->GetNext())
{if($arSalePaySystem["NAME"]==GetMessage("SALE_WIZARD_PS_SB")||$arSalePaySystem["NAME"]==GetMessage("SALE_WIZARD_PS_BILL"))
{foreach($val["ACTION"]as $action)
{$arGeneralInfo["paySystem"][$val["CODE_TEMP"]][$action["PERSON_TYPE_ID"]]=$arSalePaySystem["ID"];$action["PAY_SYSTEM_ID"]=$arSalePaySystem["ID"];$dbSalePaySystemAction=CSalePaySystemAction::GetList(array(),array("PAY_SYSTEM_ID"=>$arSalePaySystem["ID"],"PERSON_TYPE_ID"=>$action["PERSON_TYPE_ID"]),false,false,array("ID"));if($arSalePaySystemAction=$dbSalePaySystemAction->GetNext())
CSalePaySystemAction::Update($arSalePaySystemAction["ID"],$action);else
{if(strlen($action["ACTION_FILE"])>0&&file_exists($_SERVER["DOCUMENT_ROOT"].$action["ACTION_FILE"]."/logo.gif"))
{$action["LOGOTIP"]=CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$action["ACTION_FILE"]."/logo.gif");}
CSalePaySystemAction::Add($action);}}}}
else
{$id=CSalePaySystem::Add(Array("LID"=>WIZARD_SITE_ID,"CURRENCY"=>$defCurrency,"NAME"=>$val["NAME"],"ACTIVE"=>($val["ACTIVE"]=="N")?"N":"Y","SORT"=>$val["SORT"],"DESCRIPTION"=>$val["DESCRIPTION"]));foreach($val["ACTION"]as&$action)
{$arGeneralInfo["paySystem"][$val["CODE_TEMP"]][$action["PERSON_TYPE_ID"]]=$id;$action["PAY_SYSTEM_ID"]=$id;if(strlen($action["ACTION_FILE"])>0&&file_exists($_SERVER["DOCUMENT_ROOT"].$action["ACTION_FILE"]."/logo.gif"))
{$action["LOGOTIP"]=CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$action["ACTION_FILE"]."/logo.gif");}
CSalePaySystemAction::Add($action);}}}
$bStatusP=false;$dbStatus=CSaleStatus::GetList(Array("SORT"=>"ASC"));while($arStatus=$dbStatus->Fetch())
{$arFields=Array();foreach($arLanguages as $langID)
{WizardServices::IncludeServiceLang("sale.php",$langID);$arFields["LANG"][]=Array("LID"=>$langID,"NAME"=>GetMessage("WIZ_SALE_STATUS_".$arStatus["ID"]),"DESCRIPTION"=>GetMessage("WIZ_SALE_STATUS_DESCRIPTION_".$arStatus["ID"]));}
$arFields["ID"]=$arStatus["ID"];CSaleStatus::Update($arStatus["ID"],$arFields);if($arStatus["ID"]=="P")
$bStatusP=true;}
if(!$bStatusP)
{$arFields=Array("ID"=>"P","SORT"=>150);foreach($arLanguages as $langID)
{WizardServices::IncludeServiceLang("sale.php",$langID);$arFields["LANG"][]=Array("LID"=>$langID,"NAME"=>GetMessage("WIZ_SALE_STATUS_P"),"DESCRIPTION"=>GetMessage("WIZ_SALE_STATUS_DESCRIPTION_P"));}
CSaleStatus::Add($arFields);}
$dbCur=CCurrency::GetList($by="currency",$o="asc");while($arCur=$dbCur->Fetch())
$arCur_[$arCur["CURRENCY"]]=$arCur;$dbCurLang=CCurrencyLang::GetList();while($arCurLang=$dbCurLang->Fetch())
$arCurLang_[$arCurLang["CURRENCY"].$arCurLang["LID"]]=$arCurLang;if(!array_key_exists("RUB",$arCur_))
CCurrency::Add(array("CURRENCY"=>"RUB","AMOUNT"=>1.000,"AMOUNT_CNT"=>1,"SORT"=>100));if(!array_key_exists("RUBru",$arCurLang_))
CCurrencyLang::Add(Array("CURRENCY"=>"RUB","LID"=>"ru","DECIMALS"=>0,"FORMAT_STRING"=>GetMessage("SALE_WIZARD_CUR_RUB_FORMAT"),"FULL_NAME"=>GetMessage("SALE_WIZARD_CUR_RUB"),"DEC_POINT"=>".","THOUSANDS_VARIANT"=>"C","THOUSANDS_SEP"=>false));else
CCurrencyLang::Update("RUB","ru",Array("DECIMALS"=>0,"FORMAT_STRING"=>GetMessage("SALE_WIZARD_CUR_RUB_FORMAT"),"FULL_NAME"=>GetMessage("SALE_WIZARD_CUR_RUB"),"DEC_POINT"=>".","THOUSANDS_VARIANT"=>"C","THOUSANDS_SEP"=>false));if(!array_key_exists("RUBen",$arCurLang_))
CCurrencyLang::Add(Array("CURRENCY"=>"RUB","LID"=>"en","DECIMALS"=>0,"FORMAT_STRING"=>"#R","FULL_NAME"=>"Ruble","DEC_POINT"=>".","THOUSANDS_VARIANT"=>"C","THOUSANDS_SEP"=>false));else
CCurrencyLang::Update("RUB","en",Array("DECIMALS"=>0,"FORMAT_STRING"=>"#R","FULL_NAME"=>"Ruble","DEC_POINT"=>".","THOUSANDS_VARIANT"=>"C","THOUSANDS_SEP"=>false));if(!array_key_exists("USD",$arCur_))
CCurrency::Add(array("CURRENCY"=>"USD","AMOUNT"=>35.000,"AMOUNT_CNT"=>1,"SORT"=>200));if(!array_key_exists("USDru",$arCurLang_))
CCurrencyLang::Add(Array("CURRENCY"=>"USD","LID"=>"ru","DECIMALS"=>0,"FORMAT_STRING"=>"$#","FULL_NAME"=>GetMessage("SALE_WIZARD_CUR_USD"),"DEC_POINT"=>".","THOUSANDS_VARIANT"=>"C","THOUSANDS_SEP"=>false));else
CCurrencyLang::Update("USD","ru",Array("DECIMALS"=>0,"FORMAT_STRING"=>"$#","FULL_NAME"=>GetMessage("SALE_WIZARD_CUR_USD"),"DEC_POINT"=>".","THOUSANDS_VARIANT"=>"C","THOUSANDS_SEP"=>false));if(!array_key_exists("USDen",$arCurLang_))
CCurrencyLang::Add(Array("CURRENCY"=>"USD","LID"=>"en","DECIMALS"=>0,"FORMAT_STRING"=>"$#","FULL_NAME"=>"Dollar","DEC_POINT"=>".","THOUSANDS_VARIANT"=>"C","THOUSANDS_SEP"=>false));else
CCurrencyLang::Update("USD","en",Array("DECIMALS"=>0,"FORMAT_STRING"=>"$#","FULL_NAME"=>"Dollar","DEC_POINT"=>".","THOUSANDS_VARIANT"=>"C","THOUSANDS_SEP"=>false));if(!array_key_exists("EUR",$arCur_))
CCurrency::Add(array("CURRENCY"=>"EUR","AMOUNT"=>45.000,"AMOUNT_CNT"=>1,"SORT"=>300));if(!array_key_exists("EURru",$arCurLang_))
CCurrencyLang::Add(Array("CURRENCY"=>"EUR","LID"=>"ru","DECIMALS"=>0,"FORMAT_STRING"=>"&euro;#","FULL_NAME"=>GetMessage("SALE_WIZARD_CUR_EUR"),"DEC_POINT"=>".","THOUSANDS_VARIANT"=>"C","THOUSANDS_SEP"=>false));else
CCurrencyLang::Update("EUR","ru",Array("DECIMALS"=>0,"FORMAT_STRING"=>"&euro;#","FULL_NAME"=>GetMessage("SALE_WIZARD_CUR_EUR"),"DEC_POINT"=>".","THOUSANDS_VARIANT"=>"C","THOUSANDS_SEP"=>false));if(!array_key_exists("EURen",$arCurLang_))
CCurrencyLang::Add(Array("CURRENCY"=>"EUR","LID"=>"en","DECIMALS"=>0,"FORMAT_STRING"=>"&euro;#","FULL_NAME"=>"Euro","DEC_POINT"=>".","THOUSANDS_VARIANT"=>"C","THOUSANDS_SEP"=>false));else
CCurrencyLang::Update("EUR","en",Array("DECIMALS"=>0,"FORMAT_STRING"=>"&euro;#","FULL_NAME"=>"Euro","DEC_POINT"=>".","THOUSANDS_VARIANT"=>"C","THOUSANDS_SEP"=>false));$dbVat=CCatalogVat::GetList(array(),Array("SITE_ID"=>WIZARD_SITE_ID));if(!($dbVat->Fetch()))
{CCatalogVat::Set(Array("ACTIVE"=>"Y","SORT"=>"100","NAME"=>GetMessage("WIZ_VAT_1"),"RATE"=>0));CCatalogVat::Set(Array("ACTIVE"=>"Y","SORT"=>"200","NAME"=>GetMessage("WIZ_VAT_2"),"RATE"=>18));}
$dbResultList=CCatalogGroup::GetList(Array(),Array("BASE"=>"Y"));if($arRes=$dbResultList->Fetch())
{$arFields=Array();$arFields=$arRes;foreach($arLanguages as $langID)
{WizardServices::IncludeServiceLang("sale.php",$langID);$arFields["USER_LANG"][$langID]=GetMessage("WIZ_PRICE_NAME");}
$db_res=CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID"=>$arRes["ID"],"BUY"=>"Y"));if($ar_res=$db_res->Fetch())
$wizGroupId[]=$ar_res["GROUP_ID"];if(!in_array(2,$wizGroupId))
$wizGroupId[]=2;$arFields["USER_GROUP"]=$wizGroupId;$arFields["USER_GROUP_BUY"]=$wizGroupId;CCatalogGroup::Update($arRes["ID"],$arFields);}
function __MakeOrder($prdCnt=1,$arData=Array())
{global $APPLICATION,$USER,$DB;CModule::IncludeModule("iblock");CModule::IncludeModule("sale");CModule::IncludeModule("catalog");$arPrd=Array();$dbItem=CIBlockElement::GetList(Array("PROPERTY_MORE_PHOTO"=>"DESC","ID"=>"ASC"),Array("IBLOCK_TYPE"=>"catalog","IBLOCK_SITE_ID"=>WIZARD_SITE_ID,"PROPERTY_NEWPRODUCT"=>false),false,Array("nTopCount"=>10),Array("ID","IBLOCK_ID","XML_ID","NAME","DETAIL_PAGE_URL","IBLOCK_XML_ID","PROPERTY_SIZE","PROPERTY_COLOR","PROPERTY_ARTNUMBER",));while($arItem=$dbItem->GetNext())
$arPrd[]=$arItem;if(!empty($arPrd))
{for($i=0;$i<$prdCnt;$i++)
{$prdID=$arPrd[mt_rand(0,9)];$arProduct=CCatalogProduct::GetByID($prdID["ID"]);$CALLBACK_FUNC="";$arCallbackPrice=CSaleBasket::ReReadPrice($CALLBACK_FUNC,"catalog",$prdID["ID"],1);$arFields=array("PRODUCT_ID"=>$prdID["ID"],"PRODUCT_PRICE_ID"=>$arCallbackPrice["PRODUCT_PRICE_ID"],"PRICE"=>$arCallbackPrice["PRICE"],"CURRENCY"=>$arCallbackPrice["CURRENCY"],"WEIGHT"=>$arProduct["WEIGHT"],"QUANTITY"=>1,"LID"=>WIZARD_SITE_ID,"DELAY"=>"N","CAN_BUY"=>"Y","NAME"=>$prdID["NAME"],"CALLBACK_FUNC"=>$CALLBACK_FUNC,"MODULE"=>"catalog","PRODUCT_PROVIDER_CLASS"=>"CCatalogProductProvider","ORDER_CALLBACK_FUNC"=>"","CANCEL_CALLBACK_FUNC"=>"","PAY_CALLBACK_FUNC"=>"","DETAIL_PAGE_URL"=>$prdID["DETAIL_PAGE_URL"],"CATALOG_XML_ID"=>$prdID["IBLOCK_XML_ID"],"PRODUCT_XML_ID"=>$prdID["XML_ID"],"VAT_RATE"=>$arCallbackPrice['VAT_RATE'],"PROPS"=>array(array("NAME"=>GetMessage("COLOR"),"CODE"=>"COLOR","VALUE"=>$prdID["PROPERTY_COLOR_VALUE"],"SORT"=>100),array("NAME"=>GetMessage("SIZE"),"CODE"=>"SIZE","VALUE"=>$prdID["PROPERTY_SIZE_VALUE"],"SORT"=>200),array("NAME"=>GetMessage("ARTNUMBER"),"CODE"=>"ARTNUMBER","VALUE"=>$prdID["PROPERTY_ARTNUMBER_VALUE"],"SORT"=>300),),);$addres=CSaleBasket::Add($arFields);}
$arOrder=Array("LID"=>$arData["SITE_ID"],"PERSON_TYPE_ID"=>$arData["PERSON_TYPE_ID"],"PAYED"=>"N","CANCELED"=>"N","STATUS_ID"=>"N","PRICE"=>1,"CURRENCY"=>$arData["CURRENCY"],"USER_ID"=>$arData["USER_ID"],"PAY_SYSTEM_ID"=>$arData["PAY_SYSTEM_ID"],);$dbFUserListTmp=CSaleUser::GetList(array("USER_ID"=>$arData["USER_ID"]));if(empty($dbFUserListTmp))
{$arFields=array("=DATE_INSERT"=>$DB->GetNowFunction(),"=DATE_UPDATE"=>$DB->GetNowFunction(),"USER_ID"=>$arData["USER_ID"]);$ID=CSaleUser::_Add($arFields);}
$orderID=CSaleOrder::Add($arOrder);CSaleBasket::OrderBasket($orderID,CSaleBasket::GetBasketUserID(),WIZARD_SITE_ID);$dbBasketItems=CSaleBasket::GetList(array("NAME"=>"ASC"),array("FUSER_ID"=>CSaleBasket::GetBasketUserID(),"LID"=>WIZARD_SITE_ID,"ORDER_ID"=>$orderID),false,false,array("ID","CALLBACK_FUNC","MODULE","PRODUCT_ID","QUANTITY","DELAY","CAN_BUY","PRICE","WEIGHT","NAME"));$ORDER_PRICE=0;while($arBasketItems=$dbBasketItems->GetNext())
$ORDER_PRICE+=roundEx($arBasketItems["PRICE"],SALE_VALUE_PRECISION)*DoubleVal($arBasketItems["QUANTITY"]);$totalOrderPrice=$ORDER_PRICE+$arData["PRICE_DELIVERY"];CSaleOrder::Update($orderID,Array("PRICE"=>$totalOrderPrice));foreach($arData["PROPS"]as $val)
{$arFields=Array("ORDER_ID"=>$orderID,"ORDER_PROPS_ID"=>$val["ID"],"NAME"=>$val["NAME"],"CODE"=>$val["CODE"],"VALUE"=>$val["VALUE"],);CSaleOrderPropsValue::Add($arFields);}
return $orderID;}}
if(WIZARD_INSTALL_DEMO_DATA)
{$personType=$arGeneralInfo["personType"]["ur"];if(IntVal($arGeneralInfo["personType"]["fiz"])>0)
$personType=$arGeneralInfo["personType"]["fiz"];if(IntVal($personType)<=0)
{$dbPerson=CSalePersonType::GetList(array(),Array("LID"=>WIZARD_SITE_ID));if($arPerson=$dbPerson->Fetch())
$personType=$arPerson["ID"];}
if(IntVal($arGeneralInfo["paySystem"]["cash"][$personType])>0)
$paySystem=$arGeneralInfo["paySystem"]["cash"][$personType];elseif(IntVal($arGeneralInfo["paySystem"]["bill"][$personType])>0)
$paySystem=$arGeneralInfo["paySystem"]["bill"][$personType];elseif(IntVal($arGeneralInfo["paySystem"]["sberbank"][$personType])>0)
$paySystem=$arGeneralInfo["paySystem"]["sberbank"][$personType];elseif(IntVal($arGeneralInfo["paySystem"]["paypal"][$personType])>0)
$paySystem=$arGeneralInfo["paySystem"]["paypal"][$personType];else
{$dbPS=CSalePaySystem::GetList(Array(),Array("LID"=>WIZARD_SITE_ID));if($arPS=$dbPS->Fetch())
$paySystem=$arPS["ID"];}
if(IntVal($location)<=0)
{$dbLocation=CSaleLocation::GetList(Array("ID"=>"ASC"),Array("LID"=>$lang));if($arLocation=$dbLocation->Fetch())
$location=$arLocation["ID"];}
if(empty($arGeneralInfo["properies"][$personType]))
{$dbProp=CSaleOrderProps::GetList(array(),Array("PERSON_TYPE_ID"=>$personType));while($arProp=$dbProp->Fetch())
$arGeneralInfo["properies"][$personType][$arProp["CODE"]]=$arProp;}
$arData=Array("SITE_ID"=>WIZARD_SITE_ID,"PERSON_TYPE_ID"=>$personType,"CURRENCY"=>$defCurrency,"USER_ID"=>1,"PAY_SYSTEM_ID"=>$paySystem,"PROPS"=>Array(),);foreach($arGeneralInfo["properies"][$personType]as $key=>$val)
{$arProp=Array("ID"=>$val["ID"],"NAME"=>$val["NAME"],"CODE"=>$val["CODE"],"VALUE"=>"",);if($key=="FIO"||$key=="CONTACT_PERSON")
$arProp["VALUE"]=GetMessage("WIZ_ORD_FIO");elseif($key=="ADDRESS"||$key=="COMPANY_ADR")
$arProp["VALUE"]=GetMessage("WIZ_ORD_ADR");elseif($key=="EMAIL")
$arProp["VALUE"]="example@example.com";elseif($key=="PHONE")
$arProp["VALUE"]="8 495 231 21 21";elseif($key=="ZIP")
$arProp["VALUE"]="101000";elseif($key=="LOCATION")
$arProp["VALUE"]=$location;elseif($key=="CITY")
$arProp["VALUE"]=$shopLocation;$arData["PROPS"][]=$arProp;}
$arVals_=array();$db_vals=CSaleOrderPropsValue::GetList();while($arVals=$db_vals->Fetch())
$arVals_[$arVals["ORDER_ID"]][$arVals["CODE"]]=$arVals;$ar_sales_=array();$db_sales=CSaleOrder::GetList(array("DATE_INSERT"=>"ASC"),array("LID"=>WIZARD_SITE_ID));while($ar_sales=$db_sales->Fetch())
$ar_sales_[$arVals_[$ar_sales["ID"]]["EMAIL"]["VALUE"]]++;$countOrder=array_shift($ar_sales_);if(empty($countOrder))$countOrder=0;switch($countOrder)
{case 0:$orderID=__MakeOrder(3,$arData);CSaleOrder::DeliverOrder($orderID,"Y");CSaleOrder::PayOrder($orderID,"Y");CSaleOrder::StatusOrder($orderID,"F");case 1:$orderID=__MakeOrder(4,$arData);CSaleOrder::DeliverOrder($orderID,"Y");CSaleOrder::PayOrder($orderID,"Y");CSaleOrder::StatusOrder($orderID,"F");case 2:$orderID=__MakeOrder(2,$arData);CSaleOrder::PayOrder($orderID,"Y");CSaleOrder::StatusOrder($orderID,"P");case 3:$orderID=__MakeOrder(1,$arData);case 4:$orderID=__MakeOrder(3,$arData);CSaleOrder::CancelOrder($orderID,"Y");}
CAgent::RemoveAgent("CSaleProduct::RefreshProductList();","sale");CAgent::AddAgent("CSaleProduct::RefreshProductList();","sale","N",60*60*24*4,"","Y");}?>