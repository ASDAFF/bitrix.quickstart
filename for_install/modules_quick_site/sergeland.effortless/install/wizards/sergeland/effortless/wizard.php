<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/iblock/iblock.php");class SelectSiteStep extends CSelectSiteWizardStep
{function InitStep()
{parent::InitStep();$this->SetNextStep("site_settings");}
function ShowStep()
{parent::ShowStep();$this->content.=GetMessage("wiz_site_existing_warning");}}
class SelectColorStep extends CSelectThemeWizardStep
{function InitStep()
{parent::InitStep();$wizard=&$this->GetWizard();$this->SetTitle(GetMessage("WIZ_STEP_SITE_COLOR"));$this->SetNextStep("select_background");$wizard->SetVar("templateID","main");if(defined("RUN_WIZARD_DESIGN"))
{$this->SetPrevStep("select_theme");if(defined("WIZARD_DEFAULT_SITE_ID"))
{$wizard=&$this->GetWizard();$wizard->SetVar("siteID",WIZARD_DEFAULT_SITE_ID);}
$siteID=WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));$rsSites=CSite::GetByID($siteID);if($arSite=$rsSites->Fetch())
{$WIZARD_SITE_DIR=$arSite["DIR"];$WIZARD_SITE_ROOT_PATH=empty($arSite["DOC_ROOT"])?$_SERVER["DOCUMENT_ROOT"]:$arSite["DOC_ROOT"];$SERVER_NAME=empty($arSite["SERVER_NAME"])?$_SERVER["SERVER_NAME"]:$arSite["SERVER_NAME"];}
else
{$WIZARD_SITE_DIR="/";$WIZARD_SITE_ROOT_PATH=$_SERVER["DOCUMENT_ROOT"];$SERVER_NAME=$_SERVER["SERVER_NAME"];}
define("WIZARD_SITE_DIR",$WIZARD_SITE_DIR);define("WIZARD_SITE_ROOT_PATH",$WIZARD_SITE_ROOT_PATH);define("SERVER_NAME",$SERVER_NAME);define("WIZARD_SITE_PATH",str_replace("//","/",WIZARD_SITE_ROOT_PATH."/".WIZARD_SITE_DIR."/"));}}}
class SelectBackgroundStep extends CWizardStep
{function InitStep()
{$wizard=&$this->GetWizard();$this->SetStepID("select_background");$this->SetTitle(GetMessage("WIZ_STEP_SITE_BACKGROUND"));$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));$this->SetPrevStep("select_theme");$this->SetNextCaption(GetMessage("NEXT_BUTTON"));if(defined("RUN_WIZARD_DESIGN"))
$this->SetNextStep("data_install");else $this->SetNextStep("site_settings");}
function ShowStep()
{$wizard=&$this->GetWizard();$templateID=$wizard->GetVar("templateID");$templatesPath=WizardServices::GetTemplatesPath($wizard->GetPath()."/site");$arTemplates=WizardServices::GetThemes($templatesPath."/".$templateID."/themes_backgrounds");if(empty($arTemplates))
return;$backgroundID=$wizard->GetVar("backgroundID");if(isset($backgroundID)&&array_key_exists($backgroundID,$arTemplates)){$defaultTemplateID=$backgroundID;$wizard->SetDefaultVar("backgroundID",$backgroundID);}else{$defaultTemplateID=COption::GetOptionString("main","wizard_background_id","",$wizard->GetVar("siteID"));if(!(strlen($defaultTemplateID)>0&&array_key_exists($defaultTemplateID,$arTemplates)))
{if(strlen($defaultTemplateID)>0&&array_key_exists($defaultTemplateID,$arTemplates))
$wizard->SetDefaultVar("backgroundID",$defaultTemplateID);else
$defaultTemplateID="";}}
CFile::DisableJSFunction();$this->content.='<div id="solutions-container" class="inst-template-list-block">';foreach($arTemplates as $backgroundID=>$arTemplate)
{if($defaultTemplateID=="")
{$defaultTemplateID=$backgroundID;$wizard->SetDefaultVar("backgroundID",$defaultTemplateID);}
$this->content.='<div class="inst-template-description">';$this->content.=$this->ShowRadioField("backgroundID",$backgroundID,Array("id"=>$backgroundID,"class"=>"inst-template-list-inp"));if($arTemplate["SCREENSHOT"]&&$arTemplate["PREVIEW"])
$this->content.=CFile::Show2Images($arTemplate["PREVIEW"],$arTemplate["SCREENSHOT"],150,150,' class="inst-template-list-img"');else
$this->content.=CFile::ShowImage($arTemplate["SCREENSHOT"],150,150,' class="inst-template-list-img"',"",true);$this->content.='<label for="'.$backgroundID.'" class="inst-template-list-label">'.$arTemplate["NAME"].'<p>'.$arTemplate["DESCRIPTION"].'</p></label>';$this->content.="</div>";}
$this->content.='</div>';}}
class SiteSettingsStep extends CSiteSettingsWizardStep
{function InitStep()
{$wizard=&$this->GetWizard();$this->SetStepID("site_settings");$this->SetTitle(GetMessage("WIZ_STEP_SITE_SET"));$this->SetNextCaption(GetMessage("NEXT_BUTTON"));$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));$this->SetPrevStep("select_background");$this->SetNextStep("catalog_settings");if(defined("WIZARD_DEFAULT_SITE_ID"))
{$wizard=&$this->GetWizard();$wizard->SetVar("siteID",WIZARD_DEFAULT_SITE_ID);}
if($wizard->GetVar("createSite")=="Y")
{$WIZARD_SITE_DIR=$wizard->GetVar("siteFolder");$WIZARD_SITE_ROOT_PATH=$_SERVER["DOCUMENT_ROOT"];$SERVER_NAME=$_SERVER["SERVER_NAME"];$siteNewID=$wizard->GetVar("siteNewID");$wizard->SetVar("siteID",$siteNewID);$siteID=$wizard->GetVar("siteID");}
else
{$siteID=WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));$rsSites=CSite::GetByID($siteID);if($arSite=$rsSites->Fetch())
{$WIZARD_SITE_DIR=$arSite["DIR"];$WIZARD_SITE_ROOT_PATH=empty($arSite["DOC_ROOT"])?$_SERVER["DOCUMENT_ROOT"]:$arSite["DOC_ROOT"];$SERVER_NAME=empty($arSite["SERVER_NAME"])?$_SERVER["SERVER_NAME"]:$arSite["SERVER_NAME"];}
else
{$WIZARD_SITE_DIR="/";$WIZARD_SITE_ROOT_PATH=$_SERVER["DOCUMENT_ROOT"];$SERVER_NAME=$_SERVER["SERVER_NAME"];}}
define("WIZARD_SITE_DIR",$WIZARD_SITE_DIR);define("WIZARD_SITE_ROOT_PATH",$WIZARD_SITE_ROOT_PATH);define("SERVER_NAME",$SERVER_NAME);define("WIZARD_SITE_PATH",str_replace("//","/",WIZARD_SITE_ROOT_PATH."/".WIZARD_SITE_DIR."/"));$wizard->SetDefaultVars(Array("siteName"=>COption::GetOptionString("effortless","siteName",GetMessage("WIZ_COMPANY_NAME_DEF"),$siteID),"EMAIL"=>COption::GetOptionString("effortless","EMAIL",GetMessage("WIZ_COMPANY_EMAIL_DEF"),$siteID),"PHONE"=>COption::GetOptionString("effortless","PHONE",GetMessage("WIZ_COMPANY_TELEPHONE_DEF"),$siteID),"SKYPE"=>COption::GetOptionString("effortless","SKYPE",GetMessage("WIZ_COMPANY_SKYPE_DEF"),$siteID),"ADDRESS"=>COption::GetOptionString("effortless","ADDRESS",GetMessage("WIZ_COMPANY_ADDRESS_DEF"),$siteID),"LATITUDE"=>COption::GetOptionString("effortless","LATITUDE",GetMessage("WIZ_MAP_GOOGLE_LATITUDE_DEF"),$siteID),"LONGITUDE"=>COption::GetOptionString("effortless","LONGITUDE",GetMessage("WIZ_MAP_GOOGLE_LONGITUDE_DEF"),$siteID),"LATITUDE_CENTER"=>COption::GetOptionString("effortless","LATITUDE_CENTER",GetMessage("WIZ_MAP_GOOGLE_LATITUDE_CENTER_DEF"),$siteID),"LONGITUDE_CENTER"=>COption::GetOptionString("effortless","LONGITUDE_CENTER",GetMessage("WIZ_MAP_GOOGLE_LONGITUDE_CENTER_DEF"),$siteID),"KEYWORDS"=>COption::GetOptionString("effortless","KEYWORDS",GetMessage("WIZ_KEYWORDS"),$siteID),"DESCRIPTION"=>COption::GetOptionString("effortless","DESCRIPTION",GetMessage("WIZ_DESCRIPTION"),$siteID),));}
function ShowStep()
{$this->content.='<div class="wizard-input-form">';$this->content.='
  <div class="wizard-input-form-block">
   <label for="siteName" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_NAME").'</label><br>
   '.$this->ShowInputField('text','siteName',array("id"=>"siteName","class"=>"wizard-field")).'
  </div>';$this->content.='
  <div class="wizard-input-form-block">
   <div class="wizard-metadata-title">'.GetMessage("WIZ_CONTACTS").'</div>
   <label for="EMAIL" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_EMAIL").'</label><br>
   '.$this->ShowInputField('text','EMAIL',array("id"=>"EMAIL","class"=>"wizard-field")).'
  </div>';$this->content.='
  <div class="wizard-input-form-block">
   <label for="PHONE" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_TELEPHONE").'</label><br>
   '.$this->ShowInputField('text','PHONE',array("id"=>"PHONE","class"=>"wizard-field")).'
  </div>';$this->content.='
  <div class="wizard-input-form-block">
   <label for="SKYPE" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_SKYPE").'</label><br>
   '.$this->ShowInputField('text','SKYPE',array("id"=>"SKYPE","class"=>"wizard-field")).'
  </div>';$this->content.='
  <div class="wizard-input-form-block">
   <label for="ADDRESS" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_ADDRESS").'</label><br>
   '.$this->ShowInputField('text','ADDRESS',array("id"=>"ADDRESS","class"=>"wizard-field")).'
  </div>';$this->content.='
  <div class="wizard-input-form-block">
   <div class="wizard-metadata-title">'.GetMessage("WIZ_MAP_GOOGLE").'</div>   
   <p><i>'.GetMessage("WIZ_MAP_GOOGLE_SUB").'</i></p>
   
   <label for="LATITUDE" class="wizard-input-title">'.GetMessage("WIZ_MAP_GOOGLE_LATITUDE").'</label><br>
   '.$this->ShowInputField('text','LATITUDE',array("id"=>"LATITUDE","class"=>"wizard-field")).'         
  </div>';$this->content.='
  <div class="wizard-input-form-block">
   <label for="LONGITUDE" class="wizard-input-title">'.GetMessage("WIZ_MAP_GOOGLE_LONGITUDE").'</label><br>
   '.$this->ShowInputField('text','LONGITUDE',array("id"=>"LONGITUDE","class"=>"wizard-field")).'
  </div>';$this->content.='
  <div class="wizard-input-form-block">
   <label for="LATITUDE_CENTER" class="wizard-input-title">'.GetMessage("WIZ_MAP_GOOGLE_LATITUDE_CENTER").'</label><br>
   '.$this->ShowInputField('text','LATITUDE_CENTER',array("id"=>"LATITUDE_CENTER","class"=>"wizard-field")).'
  </div>';$this->content.='
  <div class="wizard-input-form-block">
   <label for="LONGITUDE_CENTER" class="wizard-input-title">'.GetMessage("WIZ_MAP_GOOGLE_LONGITUDE_CENTER").'</label><br>
   '.$this->ShowInputField('text','LONGITUDE_CENTER',array("id"=>"LONGITUDE_CENTER","class"=>"wizard-field")).'
  </div>';$this->content.='
  <div  id="bx_metadata" style="display:block">
  
   <div class="wizard-input-form-block">
    <div class="wizard-metadata-title">'.GetMessage("WIZ_META_DATA").'</div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <label for="DESCRIPTION" class="wizard-input-title">'.GetMessage("WIZ_META_DESCRIPTION").'</label><br>
    '.$this->ShowInputField('textarea','DESCRIPTION',array("id"=>"DESCRIPTION","class"=>"wizard-field","rows"=>"2")).'
   </div>
  </div>';$this->content.='
   <div class="wizard-input-form-block">
    <label for="KEYWORDS" class="wizard-input-title">'.GetMessage("WIZ_META_KEYWORDS").'</label><br>
    '.$this->ShowInputField('textarea','KEYWORDS',array("id"=>"KEYWORDS","class"=>"wizard-field","rows"=>"2")).'
   </div>
  </div>';$this->content.='
  </div>';}}
class CatalogSettings extends CWizardStep
{function InitStep()
{$this->SetStepID("catalog_settings");$this->SetTitle(GetMessage("WIZ_STEP_CT"));$this->SetPrevStep("site_settings");$this->SetNextStep("data_install");$this->SetNextCaption(GetMessage("NEXT_BUTTON"));$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));$wizard=&$this->GetWizard();$siteID=$wizard->GetVar("siteID");$wizard->SetDefaultVars(Array("iblockSliderID"=>COption::GetOptionInt("effortless","iblockSliderID",0,$siteID),"iblockExtraID"=>COption::GetOptionInt("effortless","iblockExtraID",0,$siteID),"iblockReviewsID"=>COption::GetOptionInt("effortless","iblockReviewsID",0,$siteID),"iblockLogoID"=>COption::GetOptionInt("effortless","iblockLogoID",0,$siteID),"iblockNewsID"=>COption::GetOptionInt("effortless","iblockNewsID",0,$siteID),"iblockServicesMainID"=>COption::GetOptionInt("effortless","iblockServicesMainID",0,$siteID),"iblockCatalogID"=>COption::GetOptionInt("effortless","iblockCatalogID",0,$siteID),"iblockCatalogCommentsID"=>COption::GetOptionInt("effortless","iblockCatalogCommentsID",0,$siteID),"iblockOrdersID"=>COption::GetOptionInt("effortless","iblockOrdersID",0,$siteID),"iblockDocumentsID"=>COption::GetOptionInt("effortless","iblockDocumentsID",0,$siteID),"iblockVacanciesID"=>COption::GetOptionInt("effortless","iblockVacanciesID",0,$siteID),"iblockPartnersID"=>COption::GetOptionInt("effortless","iblockPartnersID",0,$siteID),"iblockFaqID"=>COption::GetOptionInt("effortless","iblockFaqID",0,$siteID),"iblockLicensesID"=>COption::GetOptionInt("effortless","iblockLicensesID",0,$siteID),"iblockStaffID"=>COption::GetOptionInt("effortless","iblockStaffID",0,$siteID),"iblockHistoryID"=>COption::GetOptionInt("effortless","iblockHistoryID",0,$siteID),"iblockArticlesID"=>COption::GetOptionInt("effortless","iblockArticlesID",0,$siteID),"iblockArticlesCommentsID"=>COption::GetOptionInt("effortless","iblockArticlesCommentsID",0,$siteID),"iblockWorksID"=>COption::GetOptionInt("effortless","iblockWorksID",0,$siteID),"iblockServicesID"=>COption::GetOptionInt("effortless","iblockServicesID",0,$siteID),"iblockActionsID"=>COption::GetOptionInt("effortless","iblockActionsID",0,$siteID),"iblockPhotoMainID"=>COption::GetOptionInt("effortless","iblockPhotoMainID",0,$siteID),"iblockPhotoID"=>COption::GetOptionInt("effortless","iblockPhotoID",0,$siteID),"iblockBannerCatalogID"=>COption::GetOptionInt("effortless","iblockBannerCatalogID",0,$siteID),"iblockBannerServicesID"=>COption::GetOptionInt("effortless","iblockBannerServicesID",0,$siteID),"iblockBannerWorksID"=>COption::GetOptionInt("effortless","iblockBannerWorksID",0,$siteID),"installDemoData"=>COption::GetOptionString("effortless","installDemoData","Y",$siteID),));}
function ShowStep()
{$wizard=&$this->GetWizard();CModule::IncludeModule("iblock");$rsIBlockType=CIBlockType::GetList(array("sort"=>"asc"),array("ACTIVE"=>"Y"));while($arr=$rsIBlockType->Fetch())
if($ar=CIBlockType::GetByIDLang($arr["ID"],LANGUAGE_ID))
$arTypesEx[$arr["ID"]]=$ar["~NAME"];$iblockID=array();$res=CIBlock::GetList(array("sort"=>"asc"),array("ACTIVE"=>"Y"));while($ar_res=$res->Fetch())
$iblockID[$arTypesEx[$ar_res[IBLOCK_TYPE_ID]]][$ar_res[ID]]="[".$ar_res[ID]."] ".$ar_res[NAME];ksort($iblockID);array_unshift($iblockID,GetMessage("WIZ_CATALOG_NEW"));$this->content.='
   <style>
    .wizard-catalog-form-item label{
     color: #A8ABB2;
     font-size: 13px;
     margin: 0;
     padding: 2px 0 0;
     font-weight: normal;
    }
   </style>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_SLIDER").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockSliderID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_SLIDER_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_ICONS").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockExtraID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_ICONS_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_REVIEWS").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockReviewsID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_REVIEWS_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_LOGO").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockLogoID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_LOGO_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_NEWS").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockNewsID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_NEWS_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_ABOUT").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockServicesMainID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_ABOUT_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_CATALOG").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockCatalogID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_CATALOG_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_CATALOG_COMMENTS").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockCatalogCommentsID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_CATALOG_COMMENTS_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_ORDERS").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockOrdersID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_ORDERS_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_DOCUMENTS").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockDocumentsID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_DOCUMENTS_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_VACANCIES").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockVacanciesID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_VACANCIES_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_PARTNERS").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockPartnersID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_PARTNERS_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_FAQ").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockFaqID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_FAQ_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_LICENSES").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockLicensesID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_LICENSES_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_STAFF").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockStaffID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_STAFF_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_HISTORY").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockHistoryID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_HISTORY_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_ARTICLES").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockArticlesID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_ARTICLES_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_ARTICLES_COMMENTS").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockArticlesCommentsID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_ARTICLES_COMMENTS_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_WORKS").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockWorksID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_WORKS_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_SERVICES").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockServicesID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_SERVICES_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_ACTIONS").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockActionsID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_ACTIONS_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_PRICE").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockPhotoMainID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_PRICE_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_PHOTO").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockPhotoID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_PHOTO_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_BANNER_CATALOG").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockBannerCatalogID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_BANNER_CATALOG_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_BANNER_SERVICES").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockBannerServicesID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_BANNER_SERVICES_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_BANNER_WORKS").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("iblockBannerWorksID",$iblockID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_BANNER_WORKS_DESCR").'</p>
      </div>
     </div>
    </div>
   </div>';$this->content.='
  <div class="wizard-input-form-block">
   '.$this->ShowCheckboxField("installDemoData","Y",array("id"=>"installDemoData")).'
   <label for="installDemoData">'.GetMessage("WIZ_STRUCTURE_DATA").'</label>
  </div>';}
function OnPostForm()
{$wizard=&$this->GetWizard();$iblockID=array();$iblockWizard=array($wizard->GetVar("iblockSliderID"),$wizard->GetVar("iblockExtraID"),$wizard->GetVar("iblockReviewsID"),$wizard->GetVar("iblockLogoID"),$wizard->GetVar("iblockNewsID"),$wizard->GetVar("iblockServicesMainID"),$wizard->GetVar("iblockCatalogID"),$wizard->GetVar("iblockCatalogCommentsID"),$wizard->GetVar("iblockOrdersID"),$wizard->GetVar("iblockDocumentsID"),$wizard->GetVar("iblockVacanciesID"),$wizard->GetVar("iblockPartnersID"),$wizard->GetVar("iblockFaqID"),$wizard->GetVar("iblockLicensesID"),$wizard->GetVar("iblockStaffID"),$wizard->GetVar("iblockHistoryID"),$wizard->GetVar("iblockArticlesID"),$wizard->GetVar("iblockArticlesCommentsID"),$wizard->GetVar("iblockWorksID"),$wizard->GetVar("iblockServicesID"),$wizard->GetVar("iblockActionsID"),$wizard->GetVar("iblockPhotoMainID"),$wizard->GetVar("iblockPhotoID"),$wizard->GetVar("iblockBannerCatalogID"),$wizard->GetVar("iblockBannerServicesID"),$wizard->GetVar("iblockBannerWorksID"),);for($i=0,$count=count($iblockWizard);$i<$count;$i++)
{if($iblockWizard[$i]==0)
$iblockID["_".$i]=$iblockWizard[$i];else $iblockID[$iblockWizard[$i]]=$iblockWizard[$i];}
if(count($iblockID)<count($iblockWizard)&&!$wizard->IsPrevButtonClick())
{$this->SetError(GetMessage("WIZ_CATALOG_ERROR"));return;}}
function ShowSelectField($name,$arValues=Array(),$arAttributes=Array(),$optgroup=false)
{$wizard=$this->GetWizard();$this->SetDisplayVars(Array($name));$varValue=$wizard->GetVar($name);$selectedValues=($varValue!==null&&$varValue!=""?$varValue:($varValue===""?Array():$wizard->GetDefaultVar($name)));if(!is_array($selectedValues))
$selectedValues=Array($selectedValues);$viewName=$wizard->GetRealName(str_replace("[]","",$name));$strReturn='<input name="'.htmlspecialcharsbx($viewName).'" value="" type="hidden" />';$prefixName=$wizard->GetRealName($name);$strReturn.='<select name="'.htmlspecialcharsbx($prefixName).'"'.$this->_ShowAttributes($arAttributes).'>';if($optgroup)
{foreach($arValues as $optgroupName=>$optionMas)
{if(is_array($optionMas))
{$strReturn.='<optgroup label="'.$optgroupName.'">';foreach($optionMas as $optionValue=>$optionName)
$strReturn.='<option value="'.htmlspecialcharsEx($optionValue).'"'.(in_array($optionValue,$selectedValues)?" selected=\"selected\"":"").'>'.htmlspecialcharsEx($optionName).'</option>';$strReturn.='</optgroup>';}
else $strReturn.='<option value="'.htmlspecialcharsEx($optgroupName).'"'.(in_array($optgroupName,$selectedValues)?" selected=\"selected\"":"").'>'.htmlspecialcharsEx($optionMas).'</option>';}}
else
foreach($arValues as $optionValue=>$optionName)
$strReturn.='<option value="'.htmlspecialcharsEx($optionValue).'"'.(in_array($optionValue,$selectedValues)?" selected=\"selected\"":"").'>'.htmlspecialcharsEx($optionName).'</option>';$strReturn.='</select>';return $strReturn;}}
class DataInstallStep extends CDataInstallWizardStep
{}
class FinishStep extends CFinishWizardStep
{function InitStep()
{$this->SetStepID("finish");$this->SetNextStep("finish");$this->SetTitle(GetMessage("FINISH_STEP_TITLE"));if(SERVER_NAME==$_SERVER["SERVER_NAME"])
$this->SetNextCaption(GetMessage("wiz_go"));else $this->SetNextCaption(GetMessage("wiz_go2"));}
function ShowStep()
{$wizard=&$this->GetWizard();$siteID=WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));$rsSites=CSite::GetByID($siteID);$siteDir="/";if($arSite=$rsSites->Fetch())
$siteDir=$arSite["DIR"];$wizard->SetFormActionScript(str_replace("//","/",$siteDir."/?finish"));$this->CreateNewIndex();$this->content.='<table class="wizard-completion-table">
    <tr>
     <td class="wizard-completion-cell">'.GetMessage("FINISH_STEP_CONTENT").'</td>
    </tr>
   </table>';}}
class WizardServices_SERGELAND extends CWizardStep
{function ImportIBlockFromXML($xmlFile,$iblockType,$iblock_id,$siteID,$permissions=Array(),$element_import)
{if(!CModule::IncludeModule("iblock"))return false;global $APPLICATION;$iblockID=WizardServices_SERGELAND::ImportXMLFile($xmlFile,$iblockType,$iblock_id,$siteID,$section_action="N",$element_action="N",$element_import,$use_crc=false,$preview=false,$sync=false,$return_last_error=false,$return_iblock_id=true);if((!is_integer($iblockID))||($iblockID<=0))
{$rsIBlock=CIBlock::GetList(array(),array("ID"=>$iblock_id,"TYPE"=>$iblockType,"SITE_ID"=>$siteID));if($arIBlock=$rsIBlock->Fetch())
$iblockID=$arIBlock["ID"];else $iblockID=false;}
if($iblockID>0)
{if(empty($permissions))
$permissions=Array(1=>"X",2=>"R");CIBlock::SetPermission($iblockID,$permissions);}
return $iblockID;}
function ImportXMLFile($file_name,$iblock_type="-",$iblock_id=0,$site_id='',$section_action="D",$element_action="D",$element_import=true,$use_crc=false,$preview=false,$sync=false,$return_last_error=false,$return_iblock_id=false)
{global $APPLICATION;$ABS_FILE_NAME=false;if(strlen($file_name)>0)
{if(file_exists($file_name)&&is_file($file_name)&&(substr($file_name,-4)===".xml"||substr($file_name,-7)===".tar.gz"))
{$ABS_FILE_NAME=$file_name;}
else
{$filename=trim(str_replace("\\","/",trim($file_name)),"/");$FILE_NAME=rel2abs($_SERVER["DOCUMENT_ROOT"],"/".$filename);if((strlen($FILE_NAME)>1)&&($FILE_NAME==="/".$filename)&&($APPLICATION->GetFileAccessPermission($FILE_NAME)>="W"))
$ABS_FILE_NAME=$_SERVER["DOCUMENT_ROOT"].$FILE_NAME;}}
if(!$ABS_FILE_NAME)
die(GetMessage("IBLOCK_XML2_FILE_ERROR")." ".$file_name);$WORK_DIR_NAME=substr($ABS_FILE_NAME,0,strrpos($ABS_FILE_NAME,"/")+1);if(substr($ABS_FILE_NAME,-7)==".tar.gz")
{include_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/tar_gz.php");$obArchiver=new CArchiver($ABS_FILE_NAME);if(!$obArchiver->ExtractFiles($WORK_DIR_NAME))
{$strError="";if(is_object($APPLICATION))
{$arErrors=$obArchiver->GetErrors();if(count($arErrors))
{foreach($arErrors as $error)
$strError.=$error[1]."<br>";}}
if($strError!="")die($strError);else die(GetMessage("IBLOCK_XML2_FILE_ERROR")." ".$file_name);}
$IMP_FILE_NAME=substr($ABS_FILE_NAME,0,-7).".xml";}
else $IMP_FILE_NAME=$ABS_FILE_NAME;$fp=fopen($IMP_FILE_NAME,"rb");if(!$fp)
die(GetMessage("IBLOCK_XML2_FILE_ERROR")." ".$file_name);if($sync)
$table_name="b_xml_tree_sync";else $table_name="b_xml_tree";$obCatalog=new CIBlockCMLImport_SERGELAND;$NS=array("STEP"=>0,"lang"=>LangSubst(LANGUAGE_ID));$obCatalog->Init($NS,$WORK_DIR_NAME,$use_crc,$preview,false,false,$iblock_id,$table_name);if($sync)
{if(!$obCatalog->StartSession(bitrix_sessid()))
die(GetMessage("IBLOCK_XML2_TABLE_CREATE_ERROR"));$obCatalog->ReadXMLToDatabase($fp,$NS,0,1024);$xml_root=$obCatalog->GetSessionRoot();$bUpdateIBlock=false;}
else
{$obCatalog->DropTemporaryTables();if(!$obCatalog->CreateTemporaryTables())
die(GetMessage("IBLOCK_XML2_TABLE_CREATE_ERROR"));$obCatalog->ReadXMLToDatabase($fp,$NS,0,1024);if(!$obCatalog->IndexTemporaryTables())
die(GetMessage("IBLOCK_XML2_INDEX_ERROR"));$xml_root=1;$bUpdateIBlock=true;}
fclose($fp);$result=$obCatalog->ImportMetaData($xml_root,$iblock_type,$site_id,$bUpdateIBlock);if($result!==true)
die(GetMessage("IBLOCK_XML2_METADATA_ERROR").implode("\n",$result));if($element_import)
{$obCatalog->ImportSections();$obCatalog->DeactivateSections($section_action);$obCatalog->SectionsResort();$obCatalog=new CIBlockCMLImport_SERGELAND;$obCatalog->Init($NS,$WORK_DIR_NAME,$use_crc,$preview,false,false,$iblock_id,$table_name);if($sync)
{if(!$obCatalog->StartSession(bitrix_sessid()))
die(GetMessage("IBLOCK_XML2_TABLE_CREATE_ERROR"));}
$SECTION_MAP=false;$PRICES_MAP=false;$obCatalog->ReadCatalogData($SECTION_MAP,$PRICES_MAP);$result=$obCatalog->ImportElements(time(),0);$obCatalog->DeactivateElement($element_action,time(),0);if($sync)
$obCatalog->EndSession();}
if($return_last_error)
{if(strlen($obCatalog->LAST_ERROR))
die($obCatalog->LAST_ERROR);}
if($return_iblock_id)
return intval($NS["IBLOCK_ID"]);else return true;}
function ReplaceMacrosRecursive($filePath,$arReplace)
{clearstatcache();if((!is_dir($filePath)&&!is_file($filePath))||!is_array($arReplace))
return;if($handle=@opendir($filePath))
{while(($file=readdir($handle))!==false)
{if($file=="."||$file==".."||(trim($filePath,"/")==trim($_SERVER["DOCUMENT_ROOT"],"/")&&($file=="bitrix"||$file=="upload")))
continue;if(is_dir($filePath."/".$file))
{self::ReplaceMacrosRecursive($filePath.$file."/",$arReplace);}
elseif(is_file($filePath."/".$file))
{if(!is_writable($filePath."/".$file))
continue;@chmod($filePath."/".$file,BX_FILE_PERMISSIONS);if(!$handleFile=@fopen($filePath."/".$file,"rb"))
continue;$content=@fread($handleFile,filesize($filePath."/".$file));@fclose($handleFile);$handleFile=false;if(!$handleFile=@fopen($filePath."/".$file,"wb"))
continue;if(flock($handleFile,LOCK_EX))
{$arSearch=Array();$arValue=Array();foreach($arReplace as $search=>$replace)
{if($skipSharp)
$arSearch[]=$search;else
$arSearch[]="#".$search."#";$arValue[]=$replace;}
$content=str_replace($arSearch,$arValue,$content);@fwrite($handleFile,$content);@flock($handleFile,LOCK_UN);}@fclose($handleFile);}}@closedir($handle);}}}
class CIBlockCMLImport_SERGELAND extends CIBlockCMLImport
{function ImportMetaData($xml_root_id,$IBLOCK_TYPE,$IBLOCK_LID,$bUpdateIBlock=true)
{global $APPLICATION;$rs=$this->_xml_file->GetList(array(),array("ID"=>$xml_root_id),array("ID","NAME","ATTRIBUTES"));$ar=$rs->Fetch();if($ar)
{foreach(array(LANGUAGE_ID,"en","ru")as $lang)
{$mess=IncludeModuleLangFile(__FILE__,$lang,true);if($ar["NAME"]===$mess["IBLOCK_XML2_COMMERCE_INFO"])
{$this->mess=$mess;$this->next_step["lang"]=$lang;}}}
if($ar&&(strlen($ar["ATTRIBUTES"])>0))
{$info=unserialize($ar["ATTRIBUTES"]);if(is_array($info)&&array_key_exists($this->mess["IBLOCK_XML2_SUM_FORMAT"],$info))
{if(preg_match("#".$this->mess["IBLOCK_XML2_SUM_FORMAT_DELIM"]."=(.);{0,1}#",$info[$this->mess["IBLOCK_XML2_SUM_FORMAT"]],$match))
{$this->next_step["sdp"]=$match[1];}}}
$meta_data_xml_id=false;$XML_ELEMENTS_PARENT=false;$XML_SECTIONS_PARENT=false;$XML_PROPERTIES_PARENT=false;$XML_SECTIONS_PROPERTIES_PARENT=false;$XML_PRICES_PARENT=false;$XML_STORES_PARENT=false;$XML_BASE_UNITS_PARENT=false;$XML_SECTION_PROPERTIES=false;$arIBlock=array();$this->next_step["bOffer"]=false;$rs=$this->_xml_file->GetList(array(),array("PARENT_ID"=>$xml_root_id,"NAME"=>$this->mess["IBLOCK_XML2_CATALOG"]),array("ID","ATTRIBUTES"));$ar=$rs->Fetch();if(!$ar)
{$rs=$this->_xml_file->GetList(array(),array("PARENT_ID"=>$xml_root_id,"NAME"=>$this->mess["IBLOCK_XML2_OFFER_LIST"]),array("ID","ATTRIBUTES"));$ar=$rs->Fetch();$this->next_step["bOffer"]=true;}
if(!$ar)
{$rs=$this->_xml_file->GetList(array(),array("PARENT_ID"=>$xml_root_id,"NAME"=>$this->mess["IBLOCK_XML2_OFFERS_CHANGE"]),array("ID","ATTRIBUTES"));$ar=$rs->Fetch();$this->next_step["bOffer"]=true;$this->next_step["bUpdateOnly"]=true;$bUpdateIBlock=false;}
if($ar)
{if(strlen($ar["ATTRIBUTES"])>0)
{$attrs=unserialize($ar["ATTRIBUTES"]);if(is_array($attrs))
{if(array_key_exists($this->mess["IBLOCK_XML2_UPDATE_ONLY"],$attrs))
$this->next_step["bUpdateOnly"]=($attrs[$this->mess["IBLOCK_XML2_UPDATE_ONLY"]]=="true")||intval($attrs[$this->mess["IBLOCK_XML2_UPDATE_ONLY"]])?true:false;}}
$rs=$this->_xml_file->GetList(array("ID"=>"asc"),array("PARENT_ID"=>$ar["ID"]));while($ar=$rs->Fetch())
{if(isset($ar["VALUE_CLOB"]))
$ar["VALUE"]=$ar["VALUE_CLOB"];if($ar["NAME"]==$this->mess["IBLOCK_XML2_ID"])
$arIBlock["XML_ID"]=$ar["VALUE"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_CATALOG_ID"])
$arIBlock["CATALOG_XML_ID"]=$ar["VALUE"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_NAME"])
{if(!$this->use_iblock_type_id)
$arIBlock["NAME"]=$ar["VALUE"];}
elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_DESCRIPTION"])
{$arIBlock["DESCRIPTION"]=$ar["VALUE"];$arIBlock["DESCRIPTION_TYPE"]="html";}
elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_POSITIONS"]||$ar["NAME"]==$this->mess["IBLOCK_XML2_OFFERS"])
$XML_ELEMENTS_PARENT=$ar["ID"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_PRICE_TYPES"])
$XML_PRICES_PARENT=$ar["ID"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_STORES"])
$XML_STORES_PARENT=$ar["ID"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_BASE_UNITS"])
$XML_BASE_UNITS_PARENT=$ar["ID"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_METADATA_ID"])
$meta_data_xml_id=$ar["VALUE"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_UPDATE_ONLY"])
$this->next_step["bUpdateOnly"]=($ar["VALUE"]=="true")||intval($ar["VALUE"])?true:false;elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_BX_CODE"])
$arIBlock["CODE"]=$ar["VALUE"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_BX_SORT"])
$arIBlock["SORT"]=$ar["VALUE"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_BX_LIST_URL"])
$arIBlock["LIST_PAGE_URL"]=$ar["VALUE"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_BX_DETAIL_URL"])
$arIBlock["DETAIL_PAGE_URL"]=$ar["VALUE"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_BX_SECTION_URL"])
$arIBlock["SECTION_PAGE_URL"]=$ar["VALUE"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_BX_INDEX_ELEMENTS"])
$arIBlock["INDEX_ELEMENT"]=($ar["VALUE"]=="true")||intval($ar["VALUE"])?"Y":"N";elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_BX_INDEX_SECTIONS"])
$arIBlock["INDEX_SECTION"]=($ar["VALUE"]=="true")||intval($ar["VALUE"])?"Y":"N";elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_BX_SECTIONS_NAME"])
$arIBlock["SECTIONS_NAME"]=$ar["VALUE"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_BX_SECTION_NAME"])
$arIBlock["SECTION_NAME"]=$ar["VALUE"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_BX_ELEMENTS_NAME"])
$arIBlock["ELEMENTS_NAME"]=$ar["VALUE"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_BX_ELEMENT_NAME"])
$arIBlock["ELEMENT_NAME"]=$ar["VALUE"];elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_BX_PICTURE"])
{if(strlen($ar["VALUE"])>0)
$arIBlock["PICTURE"]=$this->MakeFileArray($ar["VALUE"]);else
$arIBlock["PICTURE"]=$this->MakeFileArray($this->_xml_file->GetAllChildrenArray($ar["ID"]));}
elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_BX_WORKFLOW"])
$arIBlock["WORKFLOW"]=($ar["VALUE"]=="true")||intval($ar["VALUE"])?"Y":"N";elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_INHERITED_TEMPLATES"])
{$arIBlock["IPROPERTY_TEMPLATES"]=array();$arTemplates=$this->_xml_file->GetAllChildrenArray($ar["ID"]);foreach($arTemplates as $TEMPLATE)
{$id=$TEMPLATE[$this->mess["IBLOCK_XML2_ID"]];$template=$TEMPLATE[$this->mess["IBLOCK_XML2_VALUE"]];if(strlen($id)>0&&strlen($template)>0)
$arIBlock["IPROPERTY_TEMPLATES"][$id]=$template;}}
elseif($ar["NAME"]==$this->mess["IBLOCK_XML2_LABELS"])
{$arLabels=$this->_xml_file->GetAllChildrenArray($ar["ID"]);foreach($arLabels as $arLabel)
{$id=$arLabel[$this->mess["IBLOCK_XML2_ID"]];$label=$arLabel[$this->mess["IBLOCK_XML2_VALUE"]];if(strlen($id)>0&&strlen($label)>0)
$arIBlock[$id]=$label;}}}
if($this->next_step["bOffer"]&&!$this->use_offers)
{if(strlen($arIBlock["CATALOG_XML_ID"])>0)
{$arIBlock["XML_ID"]=$arIBlock["CATALOG_XML_ID"];$this->next_step["bUpdateOnly"]=true;}}
$obIBlock=new CIBlock;$rsIBlocks=$obIBlock->GetList(array(),array("ID"=>($this->use_iblock_type_id>0?$this->use_iblock_type_id:0)));$ar=$rsIBlocks->Fetch();if(!$ar&&!array_key_exists("CODE",$arIBlock))
{if($this->next_step["bOffer"]&&$this->use_offers)
$rsIBlocks=$obIBlock->GetList(array(),array("XML_ID"=>"FUTURE-1C-OFFERS"));else
$rsIBlocks=$obIBlock->GetList(array(),array("XML_ID"=>"FUTURE-1C-CATALOG"));$ar=$rsIBlocks->Fetch();}
if($ar)
{$rsSites=$obIBlock->GetSite($ar["ID"]);while($arSite=$rsSites->Fetch())
$arIBlock["LID"][]=$arSite["LID"];if(!in_array($IBLOCK_LID,$arIBlock["LID"]))
$arIBlock["LID"][]=$IBLOCK_LID;$arIBlock["GROUP_ID"]=array(2=>"R");$arIBlock["ACTIVE"]="Y";if($obIBlock->Update($ar["ID"],$arIBlock))
$arIBlock["ID"]=$ar["ID"];else
return $obIBlock->LAST_ERROR;}
else
{$arIBlock["IBLOCK_TYPE_ID"]=$this->CheckIBlockType($IBLOCK_TYPE);if(!$arIBlock["IBLOCK_TYPE_ID"])
return GetMessage("IBLOCK_XML2_TYPE_ADD_ERROR");$arIBlock["GROUP_ID"]=array(2=>"R");$arIBlock["LID"]=$this->CheckSites($IBLOCK_LID);$arIBlock["ACTIVE"]="Y";$arIBlock["WORKFLOW"]="N";$arIBlock["ID"]=$obIBlock->Add($arIBlock);if(!$arIBlock["ID"])
return $obIBlock->LAST_ERROR;}
if($this->bCatalog&&$this->next_step["bOffer"])
{$obCatalog=new CCatalog();$intParentID=$this->GetIBlockByXML_ID($arIBlock["CATALOG_XML_ID"]);if(0<intval($intParentID)&&$this->use_offers)
{$mxSKUProp=$obCatalog->LinkSKUIBlock($intParentID,$arIBlock["ID"]);if(!$mxSKUProp)
{if($ex=$APPLICATION->GetException())
{$result=$ex->GetString();return $result;}}
else
{$rs=CCatalog::GetList(array(),array("IBLOCK_ID"=>$arIBlock["ID"]));if($arOffer=$rs->Fetch())
{$boolFlag=$obCatalog->Update($arIBlock["ID"],array('PRODUCT_IBLOCK_ID'=>$intParentID,'SKU_PROPERTY_ID'=>$mxSKUProp));}
else
{$boolFlag=$obCatalog->Add(array("IBLOCK_ID"=>$arIBlock["ID"],"YANDEX_EXPORT"=>"N","SUBSCRIPTION"=>"N",'PRODUCT_IBLOCK_ID'=>$intParentID,'SKU_PROPERTY_ID'=>$mxSKUProp));}
if(!$boolFlag)
{if($ex=$APPLICATION->GetException())
{$result=$ex->GetString();return $result;}}}}
else
{$rs=CCatalog::GetList(array(),array("IBLOCK_ID"=>$arIBlock["ID"]));if(!($rs->Fetch()))
{$boolFlag=$obCatalog->Add(array("IBLOCK_ID"=>$arIBlock["ID"],"YANDEX_EXPORT"=>"N","SUBSCRIPTION"=>"N"));if(!$boolFlag)
{if($ex=$APPLICATION->GetException())
{$result=$ex->GetString();return $result;}}}}}
if(!array_key_exists("CODE",$arIBlock))
{$arProperties=array("CML2_BAR_CODE"=>GetMessage("IBLOCK_XML2_BAR_CODE"),"CML2_ARTICLE"=>GetMessage("IBLOCK_XML2_ARTICLE"),"CML2_ATTRIBUTES"=>array("NAME"=>GetMessage("IBLOCK_XML2_ATTRIBUTES"),"MULTIPLE"=>"Y","WITH_DESCRIPTION"=>"Y","MULTIPLE_CNT"=>1,),"CML2_TRAITS"=>array("NAME"=>GetMessage("IBLOCK_XML2_TRAITS"),"MULTIPLE"=>"Y","WITH_DESCRIPTION"=>"Y","MULTIPLE_CNT"=>1,),"CML2_BASE_UNIT"=>GetMessage("IBLOCK_XML2_BASE_UNIT_NAME"),"CML2_TAXES"=>array("NAME"=>GetMessage("IBLOCK_XML2_TAXES"),"MULTIPLE"=>"Y","WITH_DESCRIPTION"=>"Y","MULTIPLE_CNT"=>1,),"CML2_PICTURES"=>array("NAME"=>GetMessage("IBLOCK_XML2_PICTURES"),"MULTIPLE"=>"Y","WITH_DESCRIPTION"=>"Y","MULTIPLE_CNT"=>1,"PROPERTY_TYPE"=>"F","CODE"=>"MORE_PHOTO",),"CML2_FILES"=>array("NAME"=>GetMessage("IBLOCK_XML2_FILES"),"MULTIPLE"=>"Y","WITH_DESCRIPTION"=>"Y","MULTIPLE_CNT"=>1,"PROPERTY_TYPE"=>"F","CODE"=>"FILES",),"CML2_MANUFACTURER"=>array("NAME"=>GetMessage("IBLOCK_XML2_PROP_MANUFACTURER"),"MULTIPLE"=>"N","WITH_DESCRIPTION"=>"N","MULTIPLE_CNT"=>1,"PROPERTY_TYPE"=>"L",),);foreach($arProperties as $k=>$v)
{$result=$this->CheckProperty($arIBlock["ID"],$k,$v);if($result!==true)
return $result;}
if(isset($arIBlock["CATALOG_XML_ID"])&&$this->use_offers)
$this->CheckProperty($arIBlock["ID"],"CML2_LINK",array("NAME"=>GetMessage("IBLOCK_XML2_CATALOG_ELEMENT"),"PROPERTY_TYPE"=>"E","USER_TYPE"=>"SKU","LINK_IBLOCK_ID"=>$this->GetIBlockByXML_ID($arIBlock["CATALOG_XML_ID"]),"FILTRABLE"=>"Y",));}
$this->next_step["IBLOCK_ID"]=$arIBlock["ID"];$this->next_step["XML_ELEMENTS_PARENT"]=$XML_ELEMENTS_PARENT;}
if($meta_data_xml_id)
{$rs=$this->_xml_file->GetList(array(),array("PARENT_ID"=>$xml_root_id,"NAME"=>$this->mess["IBLOCK_XML2_METADATA"]),array("ID"));while($arMetadata=$rs->Fetch())
{$bMetaFound=false;$meta_roots=array();$rsMetaRoots=$this->_xml_file->GetList(array("ID"=>"asc"),array("PARENT_ID"=>$arMetadata["ID"]));while($arMeta=$rsMetaRoots->Fetch())
{if(isset($arMeta["VALUE_CLOB"]))
$arMeta["VALUE"]=$arMeta["VALUE_CLOB"];if($arMeta["NAME"]==$this->mess["IBLOCK_XML2_ID"]&&$arMeta["VALUE"]==$meta_data_xml_id)
$bMetaFound=true;$meta_roots[]=$arMeta;}
if($bMetaFound)
{foreach($meta_roots as $arMeta)
{if($arMeta["NAME"]==$this->mess["IBLOCK_XML2_GROUPS"])
$XML_SECTIONS_PARENT=$arMeta["ID"];elseif($arMeta["NAME"]==$this->mess["IBLOCK_XML2_PROPERTIES"])
$XML_PROPERTIES_PARENT=$arMeta["ID"];elseif($arMeta["NAME"]==$this->mess["IBLOCK_XML2_GROUPS_PROPERTIES"])
$XML_SECTIONS_PROPERTIES_PARENT=$arMeta["ID"];elseif($arMeta["NAME"]==$this->mess["IBLOCK_XML2_SECTION_PROPERTIES"])
$XML_SECTION_PROPERTIES=$arMeta["ID"];elseif($arMeta["NAME"]==$this->mess["IBLOCK_XML2_PRICE_TYPES"])
$XML_PRICES_PARENT=$arMeta["ID"];elseif($arMeta["NAME"]==$this->mess["IBLOCK_XML2_STORES"])
$XML_STORES_PARENT=$arMeta["ID"];elseif($arMeta["NAME"]==$this->mess["IBLOCK_XML2_BASE_UNITS"])
$XML_BASE_UNITS_PARENT=$arMeta["ID"];}
break;}}}
if($XML_PROPERTIES_PARENT)
{$result=$this->ImportProperties($XML_PROPERTIES_PARENT,$arIBlock["ID"]);if($result!==true)
return $result;}
if($XML_SECTION_PROPERTIES)
{$result=$this->ImportSectionProperties($XML_SECTION_PROPERTIES,$arIBlock["ID"]);if($result!==true)
return $result;}
if($XML_SECTIONS_PROPERTIES_PARENT)
{$result=$this->ImportSectionsProperties($XML_SECTIONS_PROPERTIES_PARENT,$arIBlock["ID"]);if($result!==true)
return $result;}
if($XML_PRICES_PARENT)
{if($this->bCatalog)
{$result=$this->ImportPrices($XML_PRICES_PARENT,$arIBlock["ID"],$IBLOCK_LID);if($result!==true)
return $result;}}
if($XML_STORES_PARENT)
{if($this->bCatalog&&CBXFeatures::IsFeatureEnabled('CatMultiStore'))
{$result=$this->ImportStores($XML_STORES_PARENT);if($result!==true)
return $result;}}
if($XML_BASE_UNITS_PARENT)
{if($this->bCatalog)
{$result=$this->ImportBaseUnits($XML_BASE_UNITS_PARENT);if($result!==true)
return $result;}}
$this->next_step["section_sort"]=100;$this->next_step["XML_SECTIONS_PARENT"]=$XML_SECTIONS_PARENT;$rs=$this->_xml_file->GetList(array(),array("PARENT_ID"=>$xml_root_id,"NAME"=>$this->mess["IBLOCK_XML2_PRODUCTS_SETS"]),array("ID","ATTRIBUTES"));$ar=$rs->Fetch();if($ar)
{$this->next_step["SETS"]=$ar["ID"];}
return true;}
function SetProductPrice($PRODUCT_ID,$arPrices,$arDiscounts=false)
{$dbPriceType=CCatalogGroup::GetList(array(),array("BASE"=>"Y"));if($arPriceType=$dbPriceType->Fetch())
$elementPrice=$arPriceType["ID"];else $elementPrice=0;$arDBPrices=array();$rsPrice=CPrice::GetList(array(),array("PRODUCT_ID"=>$PRODUCT_ID));while($ar=$rsPrice->Fetch())
$arDBPrices[$ar["CATALOG_GROUP_ID"].":".$ar["QUANTITY_FROM"].":".$ar["QUANTITY_TO"]]=$ar["ID"];$arToDelete=$arDBPrices;if(!is_array($arPrices))
$arPrices=array();foreach($arPrices as $price)
{if(!isset($price[$this->mess["IBLOCK_XML2_CURRENCY"]]))
$price[$this->mess["IBLOCK_XML2_CURRENCY"]]=$price["PRICE"]["CURRENCY"];$arPrice=Array("PRODUCT_ID"=>$PRODUCT_ID,"CATALOG_GROUP_ID"=>$elementPrice>0?$elementPrice:$price["PRICE"]["ID"],"^PRICE"=>$this->ToFloat($price[$this->mess["IBLOCK_XML2_PRICE_FOR_ONE"]]),"CURRENCY"=>$this->CheckCurrency($price[$this->mess["IBLOCK_XML2_CURRENCY"]]),);foreach($this->ConvertDiscounts($arDiscounts)as $arDiscount)
{$arPrice["QUANTITY_FROM"]=$arDiscount["QUANTITY_FROM"];$arPrice["QUANTITY_TO"]=$arDiscount["QUANTITY_TO"];if($arDiscount["PERCENT"]>0)
$arPrice["PRICE"]=$arPrice["^PRICE"]-$arPrice["^PRICE"]/100*$arDiscount["PERCENT"];else $arPrice["PRICE"]=$arPrice["^PRICE"];$id=$arPrice["CATALOG_GROUP_ID"].":".$arPrice["QUANTITY_FROM"].":".$arPrice["QUANTITY_TO"];if(array_key_exists($id,$arDBPrices))
{CPrice::Update($arDBPrices[$id],$arPrice);unset($arToDelete[$id]);}
else CPrice::Add($arPrice);}}
foreach($arToDelete as $id)
CPrice::Delete($id);}}?>