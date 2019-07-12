<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/iblock/iblock.php");class SelectSiteStep extends CSelectSiteWizardStep
{function InitStep()
{parent::InitStep();$this->SetNextStep("site_settings");}
function ShowStep()
{parent::ShowStep();$this->content.=GetMessage("wiz_site_existing_warning");}}
class SelectTemplateStep extends CSelectTemplateWizardStep
{function InitStep()
{$this->SetStepID("select_template");$this->SetTitle(GetMessage("SELECT_TEMPLATE_TITLE"));$this->SetSubTitle(GetMessage("SELECT_TEMPLATE_SUBTITLE"));$this->SetNextStep("select_theme");$this->SetNextCaption(GetMessage("NEXT_BUTTON"));}
function ShowStep()
{$wizard=&$this->GetWizard();$arTemplateOrder=array("streetstyle_horizontal","streetstyle_vertical","streetstyle_vertical_popup");$defaultTemplateID=COption::GetOptionString("main","wizard_template_id","streetstyle_horizontal",$wizard->GetVar("siteID"));if(!in_array($defaultTemplateID,array("streetstyle_vertical","streetstyle_horizontal","streetstyle_vertical_popup")))$defaultTemplateID="streetstyle_horizontal";$wizard->SetDefaultVar("wizTemplateID",$defaultTemplateID);$arTemplateInfo=array("streetstyle_horizontal"=>array("NAME"=>GetMessage("WIZ_TEMPLATE_HORIZONTAL"),"DESCRIPTION"=>"","PREVIEW"=>$wizard->GetPath()."/site/templates/streetstyle/lang/".LANGUAGE_ID."/preview_horizontal.gif","SCREENSHOT"=>$wizard->GetPath()."/site/templates/streetstyle/lang/".LANGUAGE_ID."/screen_horizontal.gif",),"streetstyle_vertical"=>array("NAME"=>GetMessage("WIZ_TEMPLATE_VERTICAL"),"DESCRIPTION"=>"","PREVIEW"=>$wizard->GetPath()."/site/templates/streetstyle/lang/".LANGUAGE_ID."/preview_vertical.gif","SCREENSHOT"=>$wizard->GetPath()."/site/templates/streetstyle/lang/".LANGUAGE_ID."/screen_vertical.gif",),"streetstyle_vertical_popup"=>array("NAME"=>GetMessage("WIZ_TEMPLATE_VERTICAL_POPUP"),"DESCRIPTION"=>"","PREVIEW"=>$wizard->GetPath()."/site/templates/streetstyle/lang/".LANGUAGE_ID."/preview_vertical_popup.gif","SCREENSHOT"=>$wizard->GetPath()."/site/templates/streetstyle/lang/".LANGUAGE_ID."/screen_vertical_popup.gif",),);$wizard->SetVar("templateID","streetstyle");$this->content.="<input type='hidden' value='streetstyle' name='templateID' id='templateID'>";global $SHOWIMAGEFIRST;$SHOWIMAGEFIRST=true;$this->content.='<div class="inst-template-list-block">';foreach($arTemplateOrder as $templateID)
{$arTemplate=$arTemplateInfo[$templateID];if(!$arTemplate)
continue;$this->content.='<div class="inst-template-description">';$this->content.=$this->ShowRadioField("wizTemplateID",$templateID,Array("id"=>$templateID,"class"=>"inst-template-list-inp"));global $SHOWIMAGEFIRST;$SHOWIMAGEFIRST=true;if($arTemplate["SCREENSHOT"]&&$arTemplate["PREVIEW"])
$this->content.=CFile::Show2Images($arTemplate["PREVIEW"],$arTemplate["SCREENSHOT"],150,150,' class="inst-template-list-img"');else
$this->content.=CFile::ShowImage($arTemplate["SCREENSHOT"],150,150,' class="inst-template-list-img"',"",true);$this->content.='<label for="'.$templateID.'" class="inst-template-list-label">'.$arTemplate["NAME"]."</label>";$this->content.="</div>";}
$this->content.="</div>";$this->content.='<script>
   function ImgShw(ID, width, height, alt)
   {
    var scroll = "no";
    var top=0, left=0;
    if(width > screen.width-10 || height > screen.height-28) scroll = "yes";
    if(height < screen.height-28) top = Math.floor((screen.height - height)/2-14);
    if(width < screen.width-10) left = Math.floor((screen.width - width)/2-5);
    width = Math.min(width, screen.width-10);
    height = Math.min(height, screen.height-28);
    var wnd = window.open("","","scrollbars="+scroll+",resizable=yes,width="+width+",height="+height+",left="+left+",top="+top);
    wnd.document.write(
     "<html><head>"+
      "<"+"script type=\"text/javascript\">"+
      "function KeyPress()"+
      "{"+
      " if(window.event.keyCode == 27) "+
      "  window.close();"+
      "}"+
      "</"+"script>"+
      "<title></title></head>"+
      "<body topmargin=\"0\" leftmargin=\"0\" marginwidth=\"0\" marginheight=\"0\" onKeyPress=\"KeyPress()\">"+
      "<img src=\""+ID+"\" border=\"0\" alt=\""+alt+"\" />"+
      "</body></html>"
    );
    wnd.document.close();
   }
  </script>';}
function OnPostForm()
{$wizard=&$this->GetWizard();$proactive=COption::GetOptionString("statistic","DEFENCE_ON","N");if($proactive=="Y")
{COption::SetOptionString("statistic","DEFENCE_ON","N");$wizard->SetVar("proactive","Y");}
else
{$wizard->SetVar("proactive","N");}
if($wizard->IsNextButtonClick())
{$arTemplates=array("streetstyle_vertical","streetstyle_horizontal","streetstyle_vertical_popup");$templateID=$wizard->GetVar("wizTemplateID");if(!in_array($templateID,$arTemplates))
$this->SetError(GetMessage("wiz_template"));}}}
class SelectThemeStep extends CSelectThemeWizardStep
{}
class SiteSettingsStep extends CSiteSettingsWizardStep
{function InitStep()
{$wizard=&$this->GetWizard();$this->SetStepID("site_settings");$this->SetTitle(GetMessage("wiz_settings"));$this->SetSubTitle(GetMessage("wiz_settings"));$this->SetNextStep("data_install");$this->SetPrevStep("select_theme");$this->SetNextCaption(GetMessage("wiz_install"));$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));if(defined("WIZARD_DEFAULT_SITE_ID"))
{$wizard=&$this->GetWizard();$wizard->SetVar("siteID",WIZARD_DEFAULT_SITE_ID);}
if($wizard->GetVar("createSite")=="Y")
{$WIZARD_SITE_DIR=$wizard->GetVar("siteFolder");$WIZARD_SITE_ROOT_PATH=$_SERVER["DOCUMENT_ROOT"];$SERVER_NAME=$_SERVER["SERVER_NAME"];$siteNewID=$wizard->GetVar("siteNewID");$wizard->SetVar("siteID",$siteNewID);$siteID=$wizard->GetVar("siteID");}
else
{$siteID=WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));$rsSites=CSite::GetByID($siteID);if($arSite=$rsSites->Fetch())
{$WIZARD_SITE_DIR=$arSite["DIR"];$WIZARD_SITE_ROOT_PATH=empty($arSite["DOC_ROOT"])?$_SERVER["DOCUMENT_ROOT"]:$arSite["DOC_ROOT"];$SERVER_NAME=empty($arSite["SERVER_NAME"])?$_SERVER["SERVER_NAME"]:$arSite["SERVER_NAME"];}
else
{$WIZARD_SITE_DIR="/";$WIZARD_SITE_ROOT_PATH=$_SERVER["DOCUMENT_ROOT"];$SERVER_NAME=$_SERVER["SERVER_NAME"];}}
define("WIZARD_SITE_DIR",$WIZARD_SITE_DIR);define("WIZARD_SITE_ROOT_PATH",$WIZARD_SITE_ROOT_PATH);define("SERVER_NAME",$SERVER_NAME);define("WIZARD_SITE_PATH",str_replace("//","/",WIZARD_SITE_ROOT_PATH."/".WIZARD_SITE_DIR."/"));$this->SetPrevStep("select_site");$this->SetNextStep("catalog_settings");$this->SetNextCaption(GetMessage("NEXT_BUTTON"));$this->SetTitle(GetMessage("WIZ_STEP_SITE_SET"));$siteLogo=$this->GetFileContentImgSrc(WIZARD_SITE_PATH."include/company_logo.php",WIZARD_SITE_DIR."images/logo.png");if(!file_exists(WIZARD_SITE_PATH."images/logo.png")&&$siteLogo==WIZARD_SITE_DIR."images/logo.png")
$siteLogo=$wizard->GetPath()."/site/public/".LANGUAGE_ID."/images/logo.png";if(file_exists(WIZARD_SITE_PATH."images/logo.png")&&strlen($wizard->GetVar("siteLogo_"))==0)
{$arFile=CFile::MakeFileArray(WIZARD_SITE_PATH."images/logo.png");$fileID=(int)CFile::SaveFile($arFile,"tmp");if($fileID>0)$wizard->SetVar("siteLogo_",$fileID);else $wizard->UnSetVar("siteLogo_");}
if(!file_exists(WIZARD_SITE_ROOT_PATH.$siteLogo))
$siteLogo=$wizard->GetPath()."/site/public/".LANGUAGE_ID."/images/logo.png";$banner_head=array();$banner_text=array();for($i=0;$i<9;$i++)
{$banner_head["banner_head_$i"]=COption::GetOptionString("streetstyle","banner_head_$i",GetMessage("WIZ_BANNER_HEAD_$i"),$siteID);$banner_text["banner_text_$i"]=COption::GetOptionString("streetstyle","banner_text_$i",GetMessage("WIZ_BANNER_TEXT_$i"),$siteID);}
$wizard->SetDefaultVars(Array("siteLogo"=>$siteLogo,"siteName"=>COption::GetOptionString("streetstyle","siteName",GetMessage("WIZ_COMPANY_NAME_DEF"),$siteID),"banner_head"=>$banner_head,"banner_text"=>$banner_text,"siteMetaDescription"=>COption::GetOptionString("streetstyle","siteMetaDescription",GetMessage("WIZ_DESCRIPTION"),$siteID),"siteMetaKeywords"=>COption::GetOptionString("streetstyle","siteMetaKeywords",GetMessage("WIZ_KEYWORDS"),$siteID),"shopFacebook"=>COption::GetOptionString("streetstyle","shopFacebook",GetMessage("WIZ_SHOP_FACEBOOK_DEF"),$siteID),"shopTwitter"=>COption::GetOptionString("streetstyle","shopTwitter",GetMessage("WIZ_SHOP_TWITTER_DEF"),$siteID),"shopVk"=>COption::GetOptionString("streetstyle","shopVk",GetMessage("WIZ_SHOP_VK_DEF"),$siteID),"installDemoData"=>COption::GetOptionString("streetstyle","installDemoData","Y",$siteID),));}
function ShowStep()
{$wizard=&$this->GetWizard();$this->content.='<div class="wizard-input-form">';$siteLogo=$wizard->GetVar("siteLogo",true);$bannerCount=$wizard->GetVar("banner_head",true);$this->content.='
  <div class="wizard-input-form-block">
   <br><label for="siteName" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_LOGO").'</label><br>
   '.CFile::ShowImage($siteLogo,180,50,"border=0 vspace=17").'<br>'.$this->ShowFileField("siteLogo",Array("show_file_info"=>"N","id"=>"siteLogo")).'
  </div>';$this->content.='
  <div class="wizard-input-form-block">
   <label for="siteName" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_NAME").'</label><br>
   '.$this->ShowInputField('text','siteName',array("id"=>"siteName","class"=>"wizard-field")).'
  </div>';$this->content.='
  <div class="wizard-input-form-block">
   <div class="wizard-metadata-title">'.GetMessage("WIZ_BANNER").'
    <p><small><i>'.GetMessage("WIZ_BANNER_SUB").'</i></small></p>
   </div>';for($i=0;$i<count($bannerCount);$i++)
{$this->content.="<p><label for=\"banner_$i\" class=\"wizard-input-title\">".GetMessage("WIZ_BANNER_SUB_$i").'</label></p>'.$this->ShowInputField("text","banner_head[banner_head_$i]",array("id"=>"banner_head_$i","class"=>"wizard-field")).$this->ShowInputField("textarea","banner_text[banner_text_$i]",array("id"=>"banner_text_$i","class"=>"wizard-field","rows"=>"3"));}
$this->content.='
  </div>';$this->content.='
  <div class="wizard-input-form-block">
   <div class="wizard-metadata-title">'.GetMessage("WIZ_SOCIAL").'</div>
   <label for="shopFacebook" class="wizard-input-title">'.GetMessage("WIZ_SHOP_FACEBOOK").'</label><br>
   '.$this->ShowInputField('text','shopFacebook',array("id"=>"shopFacebook","class"=>"wizard-field")).'
  </div>';$this->content.='
  <div class="wizard-input-form-block">
   <label for="shopTwitter" class="wizard-input-title">'.GetMessage("WIZ_SHOP_TWITTER").'</label><br>
   '.$this->ShowInputField('text','shopTwitter',array("id"=>"shopTwitter","class"=>"wizard-field")).'
  </div>';$this->content.='
  <div class="wizard-input-form-block">
   <label for="shopVk" class="wizard-input-title">'.GetMessage("WIZ_SHOP_VK").'</label><br>
   '.$this->ShowInputField('text','shopVk',array("id"=>"shopVk","class"=>"wizard-field")).'
  </div>';$this->content.='
  <div  id="bx_metadata" style="display:block">
   <div class="wizard-input-form-block">
    <div class="wizard-metadata-title">'.GetMessage("wiz_meta_data").'</div>
    <label for="siteMetaDescription" class="wizard-input-title">'.GetMessage("wiz_meta_description").'</label><br>
    '.$this->ShowInputField("textarea","siteMetaDescription",Array("id"=>"siteMetaDescription","rows"=>"3","class"=>"wizard-field")).'
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <label for="siteMetaKeywords" class="wizard-input-title">'.GetMessage("wiz_meta_keywords").'</label><br>
    '.$this->ShowInputField('text','siteMetaKeywords',array("id"=>"siteMetaKeywords","class"=>"wizard-field")).'
   </div>
  </div>';$this->content.='
  <div class="wizard-input-form-block">
   '.$this->ShowCheckboxField("installDemoData","Y",array("id"=>"installDemoData")).'
   <label for="installDemoData">'.GetMessage("wiz_structure_data").'</label>
  </div>';$this->content.='</div>';}
function OnPostForm()
{$res=$this->SaveFile("siteLogo",Array("extensions"=>"gif,jpg,jpeg,png","max_height"=>300,"max_width"=>300,"make_preview"=>"Y"));}
function GetFileContentImgSrc($filename,$default_value)
{$siteLogo=$this->GetFileContent($filename,false);if($siteLogo!==false)
{if(preg_match("/src\s*=\s*(\S+)[ \t\r\n\/>]*/i",$siteLogo,$reg))
$siteLogo="/".trim($reg[1],"\"' />");else $siteLogo=$default_value;}
else $siteLogo=$default_value;return $siteLogo;}}
class CatalogSettings extends CWizardStep
{function InitStep()
{$this->SetStepID("catalog_settings");$this->SetTitle(GetMessage("WIZ_STEP_CT"));$this->SetPrevStep("site_settings");$this->SetNextStep("shop_settings");$this->SetNextCaption(GetMessage("NEXT_BUTTON"));$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));$wizard=&$this->GetWizard();$siteID=$wizard->GetVar("siteID");$subscribe=COption::GetOptionString("sale","subscribe_prod","");$arSubscribe=unserialize($subscribe);$wizard->SetDefaultVars(Array("catalogSubscribe"=>(isset($arSubscribe[$siteID]))?($arSubscribe[$siteID]['use']=="Y"?"Y":false):"Y","catalogNewsID"=>COption::GetOptionInt("streetstyle","catalogNewsID",0,$siteID),"catalogNewsCount"=>COption::GetOptionInt("streetstyle","catalogNewsCount",20,$siteID),"catalogProductID"=>COption::GetOptionInt("streetstyle","catalogProductID",0,$siteID),"catalogProductCount"=>COption::GetOptionInt("streetstyle","catalogProductCount",12,$siteID),"catalogPriceID"=>COption::GetOptionInt("streetstyle","catalogPriceID",0,$siteID),"useSKUPrice"=>COption::GetOptionString("streetstyle","useSKUPrice","Y",$siteID),"catalogQuicklyID"=>COption::GetOptionInt("streetstyle","catalogQuicklyID",0,$siteID),"catalogCheapID"=>COption::GetOptionInt("streetstyle","catalogCheapID",0,$siteID),"catalogFaqID"=>COption::GetOptionInt("streetstyle","catalogFaqID",0,$siteID),"catalogArticlesID"=>COption::GetOptionInt("streetstyle","catalogArticlesID",0,$siteID),"catalogArticlesCount"=>COption::GetOptionInt("streetstyle","catalogArticlesCount",3,$siteID),"useStoreControl"=>COption::GetOptionString("catalog","default_use_store_control","Y"),"productReserveCondition"=>COption::GetOptionString("sale","product_reserve_condition","P")));}
function ShowStep()
{$wizard=&$this->GetWizard();CModule::IncludeModule("iblock");$rsIBlockType=CIBlockType::GetList(array("sort"=>"asc"),array("ACTIVE"=>"Y"));while($arr=$rsIBlockType->Fetch())
if($ar=CIBlockType::GetByIDLang($arr["ID"],LANGUAGE_ID))
$arTypesEx[$arr["ID"]]=$ar["~NAME"];$catalogID=array();$res=CIBlock::GetList(array(),array("ACTIVE"=>"Y"));while($ar_res=$res->Fetch())
$catalogID[$arTypesEx[$ar_res[IBLOCK_TYPE_ID]]][$ar_res[ID]]="[".$ar_res[ID]."] ".$ar_res[NAME];ksort($catalogID);array_unshift($catalogID,GetMessage("WIZ_CATALOG_NEW"));$this->content.='
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
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_NEWS").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("catalogNewsID",$catalogID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_NEWS_DESCR").'</p>
      </div>    
      <div class="wizard-catalog-form-item">
       '.$this->ShowSelectField("catalogNewsCount",array("10"=>10,"20"=>20,"30"=>30,)).' <label for="catalogNewsCount">'.GetMessage("WIZ_CATALOG_NEWS_COUNT").'</label><br />
      </div>      
     </div>    
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">    
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_PRODUCT").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("catalogProductID",$catalogID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_PRODUCT_DESCR").'</p>
      </div>    
      <div class="wizard-catalog-form-item">
       '.$this->ShowSelectField("catalogProductCount",array("8"=>8,"12"=>12,"16"=>16,)).' <label for="catalogProductCount">'.GetMessage("WIZ_CATALOG_PRODUCT_COUNT").'</label><br />
      </div>      
     </div>    
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">    
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_PRICE").'</div>
      <div class="wizard-catalog-form-item">
       '.$this->ShowCheckboxField("useSKUPrice","Y",array("id"=>"use-sku-price")).'<label for="use-sku-price">'.GetMessage("WIZ_SKU_PRICE").'</label>
      </div>      
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("catalogPriceID",$catalogID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_PRICE_DESCR").'</p>
      </div>          
     </div>    
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">    
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_QUICKLY").'</div>      
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("catalogQuicklyID",$catalogID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_QUICKLY_DESCR").'</p>
      </div>          
     </div>    
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">    
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_CHEAP").'</div>      
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("catalogCheapID",$catalogID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_CHEAP_DESCR").'</p>
      </div>          
     </div>    
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">    
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_FAQ").'</div>      
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("catalogFaqID",$catalogID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_FAQ_DESCR").'</p>
      </div>          
     </div>    
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-form">    
     <div class="wizard-input-form-block">
     <div class="wizard-catalog-title"></div>
     <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_ARTICLES").'</div>
      <div class="wizard-catalog-form-item">'.$this->ShowSelectField("catalogArticlesID",$catalogID,array(),true).'<p>'.GetMessage("WIZ_CATALOG_ARTICLES_DESCR").'</p>
      </div>    
      <div class="wizard-catalog-form-item">
       '.$this->ShowSelectField("catalogArticlesCount",array("1"=>1,"3"=>3,"5"=>5,)).' <label for="catalogProductCount">'.GetMessage("WIZ_CATALOG_ARTICLES_COUNT").'</label><br />
      </div>      
     </div>    
    </div>
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <div class="wizard-catalog-title"></div>
    <div class="wizard-catalog-title">'.GetMessage("WIZ_CATALOG_USE_STORE_CONTROL").'</div>
    <div>
     <div class="wizard-catalog-form-item">
      '.$this->ShowCheckboxField("useStoreControl","Y",array("id"=>"use-store-control")).'<label for="use-store-control">'.GetMessage("WIZ_STORE_CONTROL").'</label>
     </div>';$arConditions=array("O"=>GetMessage("SALE_PRODUCT_RESERVE_1_ORDER"),"P"=>GetMessage("SALE_PRODUCT_RESERVE_2_PAYMENT"),"D"=>GetMessage("SALE_PRODUCT_RESERVE_3_DELIVERY"),"S"=>GetMessage("SALE_PRODUCT_RESERVE_4_DEDUCTION"));foreach($arConditions as $conditionID=>$conditionName)
$arReserveConditions[$conditionID]=$conditionName;$this->content.='
    <div class="wizard-catalog-form-item">'.$this->ShowSelectField("productReserveCondition",$arReserveConditions).'<p>'.GetMessage("SALE_PRODUCT_RESERVE_CONDITION").'</p>
    </div>
   </div>
  </div>';}
function OnPostForm()
{$wizard=&$this->GetWizard();$useSKUPrice=$wizard->GetVar("useSKUPrice")=="Y"?true:false;$catalogID=array();$catalog=array($wizard->GetVar("catalogNewsID"),$wizard->GetVar("catalogProductID"),$wizard->GetVar("catalogQuicklyID"),$wizard->GetVar("catalogFaqID"),$wizard->GetVar("catalogArticlesID"));if($useSKUPrice)
$catalog[]=$wizard->GetVar("catalogPriceID");for($i=0,$count=count($catalog);$i<$count;$i++)
{if($catalog[$i]==0)
$catalogID["_".$i]=$catalog[$i];else $catalogID[$catalog[$i]]=$catalog[$i];}
if(count($catalogID)<count($catalog)&&!$wizard->IsPrevButtonClick())
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
class ShopSettings extends CWizardStep
{function InitStep()
{$this->SetStepID("shop_settings");$this->SetTitle(GetMessage("WIZ_STEP_SS"));$this->SetNextStep("person_type");$this->SetPrevStep("catalog_settings");$this->SetNextCaption(GetMessage("NEXT_BUTTON"));$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));$wizard=&$this->GetWizard();$siteID=$wizard->GetVar("siteID");$wizard->SetDefaultVars(Array("shopEmail"=>COption::GetOptionString("streetstyle","shopEmail","sale@".SERVER_NAME,$siteID),"shopOfName"=>COption::GetOptionString("streetstyle","shopOfName",GetMessage("WIZ_SHOP_OF_NAME_DEF"),$siteID),"shopLocation"=>COption::GetOptionString("streetstyle","shopLocation",GetMessage("WIZ_SHOP_LOCATION_DEF"),$siteID),"shopAdr"=>COption::GetOptionString("streetstyle","shopAdr",GetMessage("WIZ_SHOP_ADR_DEF"),$siteID),"siteTelephone"=>COption::GetOptionString("streetstyle","siteTelephone",GetMessage("WIZ_COMPANY_TELEPHONE_DEF"),$siteID),"siteSchedule"=>COption::GetOptionString("streetstyle","siteSchedule",GetMessage("WIZ_COMPANY_SCHEDULE_DEF"),$siteID),"shopINN"=>COption::GetOptionString("streetstyle","shopINN","1234567890",$siteID),"shopKPP"=>COption::GetOptionString("streetstyle","shopKPP","123456789",$siteID),"shopNS"=>COption::GetOptionString("streetstyle","shopNS","0000 0000 0000 0000 0000",$siteID),"shopBANK"=>COption::GetOptionString("streetstyle","shopBANK",GetMessage("WIZ_SHOP_BANK_DEF"),$siteID),"shopBANKREKV"=>COption::GetOptionString("streetstyle","shopBANKREKV",GetMessage("WIZ_SHOP_BANKREKV_DEF"),$siteID),"shopKS"=>COption::GetOptionString("streetstyle","shopKS","30101 810 4 0000 0000225",$siteID),"siteStamp"=>COption::GetOptionString("streetstyle","siteStamp",$wizard->GetPath()."/site/public/".LANGUAGE_ID."/images/stamp.gif",$siteID),));}
function ShowStep()
{$wizard=&$this->GetWizard();$siteStamp=$wizard->GetVar("siteStamp",true);if(!CModule::IncludeModule("catalog"))
{$this->SetError(GetMessage("WIZ_NO_MODULE_CATALOG"));$this->SetNextStep("shop_settings");}
else
{$this->content.='
   <div class="wizard-input-form">';$this->content.='   
    <div class="wizard-input-form-block">
     <label class="wizard-input-title" for="shopEmail">'.GetMessage("WIZ_SHOP_EMAIL").'</label><br>
     '.$this->ShowInputField('text','shopEmail',array("id"=>"shopEmail","class"=>"wizard-field")).'
    </div>';$this->content.='
   <div id="ru_bank_details" class="wizard-input-form-block">
    <div class="wizard-input-form-block">
     <label class="wizard-input-title" for="shopOfName">'.GetMessage("WIZ_SHOP_OF_NAME").'</label><br>
     '.$this->ShowInputField('text','shopOfName',array("id"=>"shopOfName","class"=>"wizard-field")).'
    </div>';$this->content.='
    <div class="wizard-input-form-block">
     <label class="wizard-input-title" for="shopLocation">'.GetMessage("WIZ_SHOP_LOCATION").'</label><br>
     '.$this->ShowInputField('text','shopLocation',array("id"=>"shopLocation","class"=>"wizard-field")).'
    </div>';$this->content.='
    <div class="wizard-input-form-block">
     <label class="wizard-input-title" for="shopAdr">'.GetMessage("WIZ_SHOP_ADR").'</label><br>
     '.$this->ShowInputField('textarea','shopAdr',array("rows"=>"3","id"=>"shopAdr","class"=>"wizard-field")).'
    </div>';$this->content.='
   <div class="wizard-input-form-block">
    <label for="siteTelephone" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_TELEPHONE").'</label><br>
    '.$this->ShowInputField('text','siteTelephone',array("id"=>"siteTelephone","class"=>"wizard-field")).'
   </div>';$this->content.='
   <div class="wizard-input-form-block">
    <label for="siteSchedule" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_SCHEDULE").'</label><br>
    '.$this->ShowInputField('textarea','siteSchedule',array("rows"=>"3","id"=>"siteSchedule","class"=>"wizard-field")).'
   </div>';$this->content.='
    <div class="wizard-catalog-title">'.GetMessage("WIZ_SHOP_BANK_TITLE").'</div>
    <table class="wizard-input-table">';$this->content.='    
     <tr>
      <td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_INN").':</td>
      <td class="wizard-input-table-right">'.$this->ShowInputField('text','shopINN',array("class"=>"wizard-field")).'</td>
     </tr>';$this->content.='     
     <tr>
      <td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_KPP").':</td>
      <td class="wizard-input-table-right">'.$this->ShowInputField('text','shopKPP',array("class"=>"wizard-field")).'</td>
     </tr>';$this->content.='     
     <tr>
      <td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_NS").':</td>
      <td class="wizard-input-table-right">'.$this->ShowInputField('text','shopNS',array("class"=>"wizard-field")).'</td>
     </tr>';$this->content.='     
     <tr>
      <td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_BANK").':</td>
      <td class="wizard-input-table-right">'.$this->ShowInputField('text','shopBANK',array("class"=>"wizard-field")).'</td>
     </tr>';$this->content.='     
     <tr>
      <td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_BANKREKV").':</td>
      <td class="wizard-input-table-right">'.$this->ShowInputField('text','shopBANKREKV',array("class"=>"wizard-field")).'</td>
     </tr>';$this->content.='     
     <tr>
      <td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_KS").':</td>
      <td class="wizard-input-table-right">'.$this->ShowInputField('text','shopKS',array("class"=>"wizard-field")).'</td>
     </tr>';$this->content.='     
     <tr>
      <td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_STAMP").':</td>
      <td class="wizard-input-table-right">'.$this->ShowFileField("siteStamp",Array("show_file_info"=>"N","id"=>"siteStamp")).'<br />'.CFile::ShowImage($siteStamp,75,75,"border=0 vspace=5",false,false).'</td>
     </tr>';$this->content.='     
    </table>
   </div>
   </div>';}}
function OnPostForm()
{$wizard=&$this->GetWizard();$res=$this->SaveFile("siteStamp",Array("extensions"=>"gif,jpg,jpeg,png","max_height"=>70,"max_width"=>190,"make_preview"=>"Y"));}}
class PersonType extends CWizardStep
{function InitStep()
{$this->SetStepID("person_type");$this->SetTitle(GetMessage("WIZ_STEP_PT"));$this->SetNextStep("pay_system");$this->SetPrevStep("shop_settings");$this->SetNextCaption(GetMessage("NEXT_BUTTON"));$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));$wizard=&$this->GetWizard();$siteID=$wizard->GetVar("siteID");$wizard->SetDefaultVars(Array("personType"=>Array("fiz"=>COption::GetOptionString("streetstyle","fiz","Y",$siteID),"ur"=>COption::GetOptionString("streetstyle","ur","Y",$siteID),)));}
function ShowStep()
{$wizard=&$this->GetWizard();$this->content.='
  <div class="wizard-input-form">
  <div class="wizard-input-form-block">
   <div style="padding-top:15px">
    <div class="wizard-input-form-field wizard-input-form-field-checkbox">
     <div class="wizard-catalog-form-item">
      '.$this->ShowCheckboxField('personType[fiz]','Y',(array("id"=>"personTypeF"))).' <label for="personTypeF">'.GetMessage("WIZ_PERSON_TYPE_FIZ").'</label><br />
     </div>
     <div class="wizard-catalog-form-item">
      '.$this->ShowCheckboxField('personType[ur]','Y',(array("id"=>"personTypeU"))).' <label for="personTypeU">'.GetMessage("WIZ_PERSON_TYPE_UR").'</label><br />
     </div>
    </div>    
   </div>
   <div class="wizard-catalog-form-item">'.GetMessage("WIZ_PERSON_TYPE").'</div>
  </div>
  </div>';}
function OnPostForm()
{$wizard=&$this->GetWizard();$type=$wizard->GetVar("personType");$statusCount=array();foreach($type as $status)
{if(strlen($status)<1)continue;$statusCount[]=$status;}
if(count($statusCount)<1&&!$wizard->IsPrevButtonClick())
{$this->SetError(GetMessage('WIZ_NO_PT'));return;}}}
class PaySystem extends CWizardStep
{function InitStep()
{$this->SetStepID("pay_system");$this->SetTitle(GetMessage("WIZ_STEP_PS"));$this->SetNextStep("data_install");$this->SetPrevStep("person_type");$this->SetNextCaption(GetMessage("NEXT_BUTTON"));$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));$wizard=&$this->GetWizard();$siteID=$wizard->GetVar("siteID");if(LANGUAGE_ID=="ru")
{$wizard->SetDefaultVars(Array("paysystem"=>Array("cash"=>COption::GetOptionString("streetstyle","cash","Y",$siteID),"sber"=>COption::GetOptionString("streetstyle","sber","Y",$siteID),"bill"=>COption::GetOptionString("streetstyle","bill","Y",$siteID),),"delivery"=>Array("courier"=>COption::GetOptionString("streetstyle","courier","Y",$siteID),"self"=>COption::GetOptionString("streetstyle","self","Y",$siteID),"russianpost"=>COption::GetOptionString("streetstyle","russianpost","N",$siteID),)));}
else
{$wizard->SetDefaultVars(Array("paysystem"=>Array("cash"=>COption::GetOptionString("streetstyle","cash","Y",$siteID),"paypal"=>COption::GetOptionString("streetstyle","paypal","Y",$siteID),),"delivery"=>Array("courier"=>COption::GetOptionString("streetstyle","courier","Y",$siteID),"self"=>COption::GetOptionString("streetstyle","self","Y",$siteID),"dhl"=>COption::GetOptionString("streetstyle","dhl","Y",$siteID),"ups"=>COption::GetOptionString("streetstyle","ups","Y",$siteID),)));}}
function ShowStep()
{$wizard=&$this->GetWizard();$shopLocalization=$wizard->GetVar("shopLocalization",true);$personType=$wizard->GetVar("personType");$this->content.='<div class="wizard-input-form">';$this->content.='
  <div class="wizard-input-form-block">
   <div class="wizard-catalog-title">'.GetMessage("WIZ_PAY_SYSTEM_TITLE").'</div>
   <div>
    <div class="wizard-input-form-field wizard-input-form-field-checkbox">
     <div class="wizard-catalog-form-item">
      '.$this->ShowCheckboxField('paysystem[cash]','Y',(array("id"=>"paysystemC"))).' <label for="paysystemC">'.GetMessage("WIZ_PAY_SYSTEM_C").'</label>
     </div>';if(LANGUAGE_ID=="ru")
{if($personType["fiz"]=="Y")
$this->content.='<div class="wizard-catalog-form-item">'.$this->ShowCheckboxField('paysystem[sber]','Y',(array("id"=>"paysystemS"))).' <label for="paysystemS">'.GetMessage("WIZ_PAY_SYSTEM_S").'</label>
       </div>';if($personType["ur"]=="Y")
{$this->content.='<div class="wizard-catalog-form-item">'.$this->ShowCheckboxField('paysystem[bill]','Y',(array("id"=>"paysystemB"))).' <label for="paysystemB">'.GetMessage("WIZ_PAY_SYSTEM_B").'</label>
       </div>';}}
else
{$this->content.='<div class="wizard-catalog-form-item">'.$this->ShowCheckboxField('paysystem[paypal]','Y',(array("id"=>"paysystemP"))).' <label for="paysystemP">PayPal</label>
      </div>';}
$this->content.='</div>
   </div>
   <div class="wizard-catalog-form-item">'.GetMessage("WIZ_PAY_SYSTEM").'</div>
  </div>';$this->content.='
  <div class="wizard-input-form-block">
   <div class="wizard-catalog-title">'.GetMessage("WIZ_DELIVERY_TITLE").'</div>
   <div>
    <div class="wizard-input-form-field wizard-input-form-field-checkbox">
     <div class="wizard-catalog-form-item">
      '.$this->ShowCheckboxField('delivery[courier]','Y',(array("id"=>"deliveryC"))).' <label for="deliveryC">'.GetMessage("WIZ_DELIVERY_C").'</label>
     </div>
     <div class="wizard-catalog-form-item">
      '.$this->ShowCheckboxField('delivery[self]','Y',(array("id"=>"deliveryS"))).' <label for="deliveryS">'.GetMessage("WIZ_DELIVERY_S").'</label>
     </div>';if(LANGUAGE_ID=="ru")
{$this->content.='<div class="wizard-catalog-form-item">'.$this->ShowCheckboxField('delivery[russianpost]','Y',(array("id"=>"deliveryR"))).' <label for="deliveryR">'.GetMessage("WIZ_DELIVERY_R").'</label>
        </div>';}
else
{$this->content.='<div class="wizard-catalog-form-item">'.$this->ShowCheckboxField('delivery[dhl]','Y',(array("id"=>"deliveryD"))).' <label for="deliveryD">DHL</label>
       </div>';$this->content.='<div class="wizard-catalog-form-item">'.$this->ShowCheckboxField('delivery[ups]','Y',(array("id"=>"deliveryU"))).' <label for="deliveryU">UPS</label>
       </div>';}
$this->content.='
    </div>
   </div>
   <div class="wizard-catalog-form-item">'.GetMessage("WIZ_DELIVERY").'</div>
  </div>';$this->content.='
  <div>
   <div class="wizard-catalog-title">'.GetMessage("WIZ_LOCATION_TITLE").'</div>
   <div>
    <div class="wizard-input-form-field wizard-input-form-field-checkbox">';if(LANGUAGE_ID=="ru")
{$this->content.='<div class="wizard-catalog-form-item">'.$this->ShowRadioField("locations_csv","loc_ussr.csv",array("id"=>"loc_ussr","checked"=>"checked"))." <label for=\"loc_ussr\">".GetMessage('WSL_STEP2_GFILE_USSR')."</label>
    </div>";}
$this->content.='<div class="wizard-catalog-form-item">'.$this->ShowRadioField("locations_csv","loc_usa.csv",array("id"=>"loc_usa"))." <label for=\"loc_usa\">".GetMessage('WSL_STEP2_GFILE_USA')."</label>
   </div>";$this->content.='<div class="wizard-catalog-form-item">'.$this->ShowRadioField("locations_csv","loc_cntr.csv",array("id"=>"loc_cntr"))." <label for=\"loc_cntr\">".GetMessage('WSL_STEP2_GFILE_CNTR')."</label>
   </div>";$this->content.='<div class="wizard-catalog-form-item">'.$this->ShowRadioField("locations_csv","",array("id"=>"none"))." <label for=\"none\">".GetMessage('WSL_STEP2_GFILE_NONE')."</label>
   </div>";$this->content.='
    </div>
   </div>
  </div>';$this->content.='
  <div class="wizard-catalog-form-item">'.GetMessage("WIZ_DELIVERY_HINT").'</div>';$this->content.='
  </div>';}
function OnPostForm()
{$wizard=&$this->GetWizard();$type=$wizard->GetVar("paysystem");$statusCount=array();foreach($type as $status)
{if(strlen($status)<1)continue;$statusCount[]=$status;}
if(count($statusCount)<1&&!$wizard->IsPrevButtonClick())
{$this->SetError(GetMessage('WIZ_NO_PS'));return;}
$type=$wizard->GetVar("delivery");$statusCount=array();foreach($type as $status)
{if(strlen($status)<1)continue;$statusCount[]=$status;}
if(count($statusCount)<1&&!$wizard->IsPrevButtonClick())
{$this->SetError(GetMessage('WIZ_NO_DL'));return;}}}
class DataInstallStep extends CDataInstallWizardStep
{}
class FinishStep extends CFinishWizardStep
{function InitStep()
{$this->SetStepID("finish");$this->SetNextStep("finish");$this->SetTitle(GetMessage("FINISH_STEP_TITLE"));if(SERVER_NAME==$_SERVER["SERVER_NAME"])
$this->SetNextCaption(GetMessage("wiz_go"));else $this->SetNextCaption(GetMessage("wiz_go2"));}
function ShowStep()
{$wizard=&$this->GetWizard();if($wizard->GetVar("proactive")=="Y")
COption::SetOptionString("statistic","DEFENCE_ON","Y");$siteID=WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));$rsSites=CSite::GetByID($siteID);$siteDir="/";if($arSite=$rsSites->Fetch())
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