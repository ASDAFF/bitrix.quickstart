<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();if(!CModule::IncludeModule("search"))return;if(!CModule::IncludeModule("catalog"))return;if(COption::GetOptionString("search","exclude_mask")=="")
COption::SetOptionString("search","exclude_mask","/bitrix/*;/404.php;/upload/*");if(WIZARD_SITE_ID!="")
$NS["SITE_ID"]=WIZARD_SITE_ID;if(!isset($_SESSION['SearchFirst']))
$NS=CSearch::ReIndexAll(false,20,$NS);else $NS=CSearch::ReIndexAll(false,20,$_SESSION['SearchNS']);if(is_array($NS))
{$this->repeatCurrentService=true;$_SESSION['SearchNS']=$NS;$_SESSION['SearchFirst']=1;}
else
{unset($_SESSION['SearchNS']);unset($_SESSION['SearchFirst']);}
$dbPriceType=CCatalogGroup::GetList(array(),array("BASE"=>"Y"));if($arPriceType=$dbPriceType->Fetch())
$priceTypeName=$arPriceType["NAME"];else $priceTypeName="BASE";CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_SITE_ID."_detail_streetstyle_sl/header.php",array("CATEGORY_0_TITLE"=>GetMessage("SALE_WIZARD_SEARCH_CATEGORY"),"PRICE_CODE"=>$priceTypeName,));CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_SITE_ID."_main_streetstyle_sl/header.php",array("CATEGORY_0_TITLE"=>GetMessage("SALE_WIZARD_SEARCH_CATEGORY"),"PRICE_CODE"=>$priceTypeName,));?>