<?php
IncludeModuleLangFile( __FILE__ );

global $DBType;        

$arClasses = array(
    "AcritLicence" => "classes/general/licence.php",
    "CExportproProfileDB" => "classes/mysql/cexportproprofiledb.php",
    "CExportproMarketDB" => "classes/mysql/cexportpropro_marketdb.php",
    "CExportproProfile" => "classes/general/cexportproprofile.php",
    "CExportproVariant" => "classes/general/cexportproprofile.php",
    
    "CAcritExportproCatalog" => "classes/general/cexportprofilter.php",
    "CAcritExportproPrices" => "classes/general/cexportprofilter.php",
    "CAcritExportproProps" => "classes/general/cexportprofilter.php",
    "CAcritExportproCatalogCond" => "classes/general/cexportprofilter.php",
    
    "CAcritExportproExport" => "classes/general/cexportproexport.php",
    "CExportproCron" => "classes/general/cexportproagent.php",
    "CExportproAgent" => "classes/general/cexportproagent.php",
    "CExportproInformer" => "classes/general/cexportproinformer.php",
    "CExportproMarketEbayDB" => "classes/mysql/cexportpropro_marketebaydb.php",
    "Threads" => "classes/general/threads.php",
    "ThreadsSession" => "classes/general/threads.php",
    "OZON" => "classes/general/ozon.php",
);                     

CModule::AddAutoloadClasses( "acrit.exportpro", $arClasses );

class CAcritExportproMenu{
    public function OnBuildGlobalMenu( &$aGlobalMenu, &$aModuleMenu ){
        global $USER, $APPLICATION, $adminMenu, $adminPage;
        if( key_exists( "global_menu_acrit", $adminMenu->aGlobalMenu ) ){
            return;
        }

        $aMenu = array(
            "menu_id" => "acrit",
            "sort" => 150,
            "text" => GetMessage( "ACRIT_MENU_NAME" ),
            "title" => GetMessage( "ACRIT_MENU_TITLE" ),
            "icon" => "clouds_menu_icon",
            "page_icon" => "clouds_page_icon",
            "items_id" => "global_menu_acrit",
            "items" => array()
        );
        $aGlobalMenu["global_menu_acrit"] = $aMenu;
    }
}

class CAcritExportproElement {
    private $profile = null;
    private $DEMO = 2;
    private $isDemo = true;
    private $DEMO_CNT;
    private $MODULEID = "acrit.exportpro";
    private $stepElements = 50;
    private $dateFields = array();
    private $log;
    private $session;
    private $baseDateTimePatern;

    private $profileEncoding = array(
        "utf8" => "utf-8",
        "cp1251" => "windows-1251",
    );

    public function __construct( $profile ){
        global $APPLICATION;
        
        $this->iblockIncluded = @CModule::IncludeModule( "iblock" );
        $this->saleIncluded = @CModule::IncludeModule( "sale" );
        $this->catalogIncluded = @CModule::IncludeModule( "catalog" );

        $this->DEMO = Cmodule::IncludeModuleEx( $this->MODULEID );
        if( $this->DEMO == 1 )
            $this->isDemo = false;
        
        $this->DEMO_CNT = 50;
        $this->profile = $profile;
        
        if( intval( $this->profile["SETUP"]["EXPORT_STEP"] ) > 0 )
            $this->stepElements = $this->profile["SETUP"]["EXPORT_STEP"];
        
        $this->dateFields = array(
            "TIMESTAMP_X",
            "DATE_CREATE",
            "DATE_ACTIVE_FROM",
            "DATE_ACTIVE_TO"
        );

        //$this->session = new ThreadsSession;
        //$this->session->Init("acrit_exportpro", \Bitrix\Main\Application::getDocumentRoot());
        
        $this->log = new CAcritExportproLog( $this->profile["ID"] );
        $this->iblockE = "file_get_contents";
        $this->iblockD = "base64_decode";
        
        $this->baseDateTimePatern = "Y-m-dTh:i:s±h:i";
        
        $paternCharset = $this->GetStringCharset( $this->baseDateTimePatern );
                
        if( $paternCharset == "cp1251" ){
            $this->baseDateTimePatern = $APPLICATION->ConvertCharset( $this->baseDateTimePatern, "cp1251", "utf8" );
        }             
        
        
        $dateGenerate = ( $this->profile["DATEFORMAT"] == $this->baseDateTimePatern ) ? $this->GetYandexDateTime( date( "d.m.Y H:i:s" ) ) : date( str_replace( "_", " ", $this->profile["DATEFORMAT"] ), time() );                
        
        $this->defaultFields = array(
            "#ENCODING#" => $this->profileEncoding[$this->profile["ENCODING"]],
            "#DATE#" => $this->profile["DATEFORMAT"],
            "#SHOP_NAME#" => $this->profile["SHOPNAME"],
            "#COMPANY_NAME#" => $this->profile["COMPANY"],
            "#SITE_URL#" => $this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"],
            "#PROFILE_DESCRIPTION#" => $this->profile["DESCRIPTION"],
            "#DATE#" => $dateGenerate,
        );                    
    }         

    public function GetSections(){
        $arSessionData = AcritExportproSession::GetAllSession( $this->profile["ID"] );
        $sessionData = array();
        if( !empty( $arSessionData ) ){
            $sessionData = $arSessionData[0];
            if( !is_array( $sessionData["EXPORTPRO"][$this->profile["ID"]]["CATEGORY"] ) )
                $sessionData["EXPORTPRO"][$this->profile["ID"]]["CATEGORY"] = array();
            
            unset( $arSessionData[0] );
            foreach( $arSessionData as $sData ){
                if( is_array( $sData["EXPORTPRO"][$this->profile["ID"]]["CATEGORY"] ) ){
                    $sessionData["EXPORTPRO"][$this->profile["ID"]]["CATEGORY"] = array_merge(
                        $sessionData["EXPORTPRO"][$this->profile["ID"]]["CATEGORY"],
                        $sData["EXPORTPRO"][$this->profile["ID"]]["CATEGORY"]
                    );
                }
            }
        }
        return array_unique( $sessionData["EXPORTPRO"][$this->profile["ID"]]["CATEGORY"] );
    }
    public function GetCurrencies(){
        $arSessionData = AcritExportproSession::GetAllSession( $this->profile["ID"] );
        $sessionData = array();
        if( !empty( $arSessionData ) ){
            $sessionData = $arSessionData[0];
            if( !is_array( $sessionData["EXPORTPRO"][$this->profile["ID"]]["CURRENCY"] ) )
                $sessionData["EXPORTPRO"][$this->profile["ID"]]["CURRENCY"] = array();
            
            unset( $arSessionData[0] );
            foreach( $arSessionData as $sData ){
                if( is_array( $sData["EXPORTPRO"][$this->profile["ID"]]["CURRENCY"] ) ){
                    $sessionData["EXPORTPRO"][$this->profile["ID"]]["CURRENCY"] = array_merge(
                        $sessionData["EXPORTPRO"][$this->profile["ID"]]["CURRENCY"],
                        $sData["EXPORTPRO"][$this->profile["ID"]]["CURRENCY"]
                    );
                }
            }
        }
        return array_unique( array_filter( $sessionData["EXPORTPRO"][$this->profile["ID"]]["CURRENCY"] ) );
    }

    public function GetElementCount(){
        return $this->elementCount;
    }

    private function DemoCount(){
        $sessionData = AcritExportproSession::GetSession( $this->profile["ID"] );
        return ( $sessionData["EXPORTPRO"][$this->profile["ID"]]["DEMO_COUNT"] > $this->DEMO_CNT );
    }
    
    private function DemoCountInc(){
        $sessionData = AcritExportproSession::GetSession( $this->profile["ID"] );
        if( !isset( $sessionData["EXPORTPRO"][$this->profile["ID"]]["DEMO_COUNT"] ) )
            $sessionData["EXPORTPRO"][$this->profile["ID"]]["DEMO_COUNT"] = 0;
        
        $sessionData["EXPORTPRO"][$this->profile["ID"]]["DEMO_COUNT"]++;
        AcritExportproSession::SetSession( $this->profile["ID"], $sessionData );
    }   

    public function Process( $page = 1, $cronrun = false, $fileType = "xml", $fileExport = false, $fileExportName = false, $arOzonCategories = false ){
        if( $fileType == "csv" ){
            return self::ProcessCSV( $page, $cronrun, $fileExport, $fileExportName );
        }
        else{
            return self::ProcessXML( $page, $cronrun, $arOzonCategories );
        }
    }
    
    //CAcritExportproExport::Save($itemTemplate)
    
    public function ArrayMultiply( &$arResult, $arTuple, $arTemp = array() ){
        if( $arTuple ){
            reset($arTuple);
            list($key, $head) = each($arTuple);
            unset($arTuple[$key]);
            $arTemp[$key] = false;
            if( is_array( $head ) ){
                if( empty( $head ) ){
                    if(empty($arTuple))
                        $arResult[] = $arTemp;
                    else
                        $this->ArrayMultiply($arResult, $arTuple, $arTemp);
                }
                else{
                    foreach( $head as $value ){
                        $arTemp[$key] = $value;
                        if(empty($arTuple))
                            $arResult[] = $arTemp;
                        else
                            $this->ArrayMultiply($arResult, $arTuple, $arTemp);
                    }
                }
            }
            else{
                $arTemp[$key] = $head;
                if(empty($arTuple))
                    $arResult[] = $arTemp;
                else
                    $this->ArrayMultiply($arResult, $arTuple, $arTemp);
            }
        }
        else{
            $arResult[] = $arTemp;
        }
    }
    
    public function ExportArrayMultiply( &$arResult, $arTuple, $arTemp = array() ){        
        if( count( $arTuple ) == 0 ){
            $arResult[] = $arTemp;
        }
        else{
            $head = array_shift( $arTuple );
            $arTemp[] = false;
            if( is_array( $head ) ){
                if( empty( $head ) ){
                    $arTemp[count( $arTemp ) - 1] = "";
                    $this->ArrayMultiply( $arResult, $arTuple, $arTemp );
                }
                else{
                    foreach( $head as $key => $value ){
                        $arTemp[count( $arTemp ) - 1] = $value;
                        self::ExportArrayMultiply( $arResult, $arTuple, $arTemp );
                    }
                }
            }
            else{
                $arTemp[count( $arTemp ) - 1] = $head;
                self::ExportArrayMultiply( $arResult, $arTuple, $arTemp );
            }
        }
    }
    
    public function ExportConvertCharset( $field ){
        global $APPLICATION;
        $result = "";
        
        $paternCharset = $this->GetStringCharset( $field );    
        $result = $APPLICATION->ConvertCharset( $field, $paternCharset, $this->profileEncoding[$this->profile["ENCODING"]] );
        
        return $result;
    }
    
        
    public function ProcessCSV( $page = 1, $cronrun = false, $fileExport = false, $fileExportName = false ){
        global $APPLICATION;
        
        if( !$fileExport || !$fileExportName ) return false;
        
        if( $page == 1 ){
            $this->log->Init( $this->profile );
            $this->page = $page;
        }

        $this->currencyRates = CExportproProfile::LoadCurrencyRates();
        $iblockList = $this->PrepareIBlock();
        
        if( empty( $iblockList ) ){
            return true;
        }
            
        $pregMatchExp = GetMessage( "ACRIT_EXPORTPRO_A_AA_A" );
                                                                                                          
        //preg_match_all( @$pregMatchExp, $this->profile["OFFER_TEMPLATE"], $this->arMatches );
        //preg_match_all( "/.*(<[\w\d_-]+).*(#[\w\d_-]+#).*(<\/[\w\d_-]+>)/", $this->profile["OFFER_TEMPLATE"], $this->arMatches );
        preg_match_all( "/.*(<.+>).*(#[\w\d_-]+:*[\w\d_-]+#).*(<\/.+>)/", $this->profile["OFFER_TEMPLATE"], $this->arMatches );

        // Install for all templates #EXAMPLE# null value, so that you can remove
        $this->templateValuesDefaults = array();
        foreach( $this->arMatches[2] as $match ){
            $this->templateValuesDefaults[$match] = "";
        }
        $this->templateValuesDefaults["#MARKET_CATEGORY#"] = "";

        // Get the properties used in the templates
        $this->useProperties = array(
            "ID" => array()
        );                                
        
        $this->usePrices = array();
        foreach( $this->profile["XMLDATA"] as $field ){
            if( !empty( $field["VALUE"] ) || !empty( $field["CONTVALUE_FALSE"] ) || !empty( $field["CONTVALUE_TRUE"] ) 
                || !empty( $field["COMPLEX_TRUE_VALUE"] ) || !empty( $field["COMPLEX_FALSE_VALUE"] ) 
                || !empty( $field["COMPLEX_TRUE_CONTVALUE"] ) || !empty( $field["COMPLEX_FALSE_CONTVALUE"] ) ){
                
                $fieldValue = ( $field["TYPE"] == "field" ) ? $field["VALUE"] : $field["COMPLEX_TRUE_VALUE"];
                $arValue = explode( "-", $fieldValue );
                
                switch( count( $arValue ) ){
                    case 1:
                        $this->useFields[] = $arValue[0];
                        break;
                    case 2:
                        $this->usePrices[] = $arValue[1];
                        break;
                    case 3:
                        $this->useProperties["ID"][] = $arValue[2];
                        break;
                }
                
                if( $field["CONDITION"]["CHILDREN"] ){
                    if( !function_exists( findChildren ) ){
                        function findChildren( $children ){
                            $retVal = array();
                            foreach( $children as $child ){
                                if( strstr( $child["CLASS_ID"], "CondIBProp" ) ){
                                    $arProp = explode( ":", $child["CLASS_ID"] );
                                    $retVal[] = $arProp[2];
                                }
                                if( $child["CHILDREN"] ){
                                    $retVal = array_merge( $retVal, findChildren( $child["CHILDREN"] ) );
                                }
                            }
                            return $retVal;
                        }
                    }
                    $this->useProperties["ID"] = array_merge( $this->useProperties["ID"], findChildren( $field["CONDITION"]["CHILDREN"] ) );
                    //$this->useProperties["ID"] = findChildren( $field["CONDITION"]["CHILDREN"] );
                }
            }
            
            if( $field["EVAL_FILTER"] ){
                preg_match_all( "/.*?PROPERTY_(\d+)|(CATALOG_PRICE_[\d]+_WD|CATALOG_PRICE_[\d]+_D).*?/", $this->profile["EVAL_FILTER"], $filterProps );
                if( is_array( $filterProps[1] ) ){
                    $this->useProperties["ID"] = array_merge( $this->useProperties["ID"], $filterProps[1] );
                }
                if( is_array( $filterProps[2] ) ){
                    $this->usePrices = array_merge( $this->usePrices, $filterProps[2] );
                }
            }
        }
        preg_match_all( "/.*?PROPERTY_(\d+)|(CATALOG_PRICE_[\d]+_WD|CATALOG_PRICE_[\d]+_D).*?/", $this->profile["EVAL_FILTER"], $filterProps );
        
        if( is_array( $filterProps[1] ) ){
            $this->useProperties["ID"] = array_merge( $this->useProperties["ID"], $filterProps[1] );
        }
        if( is_array( $filterProps[2] ) ){
            $this->usePrices = array_merge( $this->usePrices, $filterProps[2] );
        }
        
        $this->useProperties["ID"] = array_unique( $this->useProperties["ID"] );
        $this->useProperties["ID"] = array_filter( $this->useProperties["ID"] );

        $this->currencyList = array();

        // Variant properties
        $variantPrice = str_replace( "-", "_", $this->profile["VARIANT"]["PRICE"] );
        $variantPropCode = array(
            "SEX_VALUE" => "SEX",
            "COLOR_VALUE" => "COLOR",
            "SIZE_VALUE" => "SIZE",
            "WEIGHT_VALUE" => "WEIGHT",
            "SEXOFFER_VALUE" => "SEXOFFER",
            "COLOROFFER_VALUE" => "COLOROFFER",
            "SIZEOFFER_VALUE" => "SIZEOFFER",
            "WEIGHTOFFER_VALUE" => "WEIGHTOFFER"
        );
        
        foreach( $this->profile["VARIANT"] as $vpKey => $vpValue ){
            if( key_exists( $vpKey, $variantPropCode ) ){
                $variantProperty = explode( "-", $vpValue );
                if( count( $variantProperty ) == 3 ){
                    $this->useProperties["ID"][] = $variantProperty[2];
                    $this->variantProperties[$variantPropCode[$vpKey]] = "PROPERTY_".$variantProperty[2]."_DISPLAY_VALUE";
                }
            }
        }
                        
        $baseCurrency = CCurrency::GetBaseCurrency();
        $arBasePrice = CCatalogGroup::GetBaseGroup();
        
        $arOrder = array(
            "IBLOCK_ID" => "ASC",
            "ID" => "ASC",
        );
        
        $arFilter = array(
            "IBLOCK_ID" => $iblockList,
            "SECTION_ID" => $this->profile["CATEGORY"],
        );
        
        if( $profile["CHECK_INCLUDE"] != "Y" ){
            $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
        }
        
        $arNavStartParams = array(
            "nPageSize" => $this->stepElements,
            "iNumPage" => $page
        );
        
        
        $dbElements = CIBlockElement::GetList(
            $arOrder,
            $arFilter,
            false,
            $arNavStartParams,
            array()
        );              
        
        $sessionData = AcritExportproSession::GetSession( $this->profile["ID"] );
        $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["STEPS"] = $this->isDemo ? 1 : $dbElements->NavPageCount;
        
        AcritExportproSession::SetSession( $this->profile["ID"], $sessionData );
        
        $arProcessTmp = array();
        
        if( $this->profile["TYPE"] == "advantshop" ){         
            $defaultSKUProps = array(
                "SKU_VENDOR_CODE",
                "SKU_SIZE",
                "SKU_COLOR",
                "SKU_PURCHASEPRICE",
                "SKU_PRICE",
                "SKU_AMOUNT",
            );                                
           
            while( $dbElement = $dbElements->GetNextElement() ){
                $arElement = $this->GetElementProperties( $dbElement );
                
                if( $this->catalogSKU[$arElement["IBLOCK_ID"]] && ( $arElement["ACTIVE"] == "Y" ) ){
                    $arOfferFilter = array(                                     
                        "IBLOCK_ID" => $this->catalogSKU[$arElement["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"],         
                        "PROPERTY_".$this->catalogSKU[$arElement["IBLOCK_ID"]]["OFFERS_PROPERTY_ID"] => $arElement["ID"]
                    );
                    
                    $dbOfferElements = CIBlockElement::GetList(
                        array(),
                        $arOfferFilter,
                        false,
                        false,
                        array(
                            "ID",
                            "IBLOCK_ID",
                            "NAME",
                            "CATALOG_GROUP_".$arBasePrice["ID"],
                        )
                    );
                    
                    $arElement["OFFERS"] = "";  
                    
                    while( $dbOfferElement = $dbOfferElements->GetNextElement() ){
                        $arOfferElement = $dbOfferElement->GetFields();
                        $arOfferElementProperties = $dbOfferElement->GetProperties();
                        $arOfferCondElementProperties = $this->GetElementProperties( $dbOfferElement );
                                  
                        if( !$this->CheckCondition( $arOfferCondElementProperties, $this->profile["EVAL_FILTER"] ) )
                            continue;
                            
                        $dbOfferMesure = CCatalogMeasure::getList(
                            array(),
                            array(
                                "ID" => $arOfferElement["CATALOG_MEASURE"]
                            )
                        );
                        
                        if( $arOfferMesure = $dbOfferMesure->Fetch() ){
                            $arOfferElement["MEASURE"] = $arOfferMesure["SYMBOL_RUS"];
                        }                 
                                                                                 
                        $arOfferElementOffers = "";
                        
                        foreach( $defaultSKUProps as $skuPropCode ){
                            if( $this->profile["XMLDATA"][$skuPropCode]["TYPE"] == "const" ){
                                $fieldVal = $this->profile["XMLDATA"][$skuPropCode]["CONTVALUE_TRUE"];
                            }
                            elseif( $this->profile["XMLDATA"][$skuPropCode]["TYPE"] == "field" ){
                                $arValue = explode( "-", $this->profile["XMLDATA"][$skuPropCode]["VALUE"] );
                                if( count( $arValue ) == 2 ){
                                    $fieldCode = "CATALOG_".$arValue[1];
                                }
                                elseif( count( $arValue ) == 3 ){
                                    $fieldCode = "PROPERTY_".$arValue[2]."_DISPLAY_VALUE";
                                }
                                else{
                                    $fieldCode = $this->profile["XMLDATA"][$skuPropCode]["VALUE"];
                                }
                                
                                if( !isset( $arOfferCondElementProperties[$fieldCode] ) ){
                                    $fieldVal = $arElement[$fieldCode];
                                }
                                else{
                                    $fieldVal = $arOfferCondElementProperties[$fieldCode];    
                                }
                                
                                if( is_array( $fieldVal ) && !empty( $fieldVal ) ){
                                    $fieldVal = $fieldVal[0];
                                }
                            }
                            elseif( $this->profile["XMLDATA"][$skuPropCode]["TYPE"] == "complex" ){
                                if( $this->profile["XMLDATA"][$skuPropCode]["COMPLEX_TRUE_TYPE"] == "const" ){
                                    $fieldVal = $this->profile["XMLDATA"][$skuPropCode]["COMPLEX_TRUE_CONTVALUE"];
                                }
                                elseif( $this->profile["XMLDATA"][$skuPropCode]["COMPLEX_TRUE_TYPE"] == "field" ){
                                    $arValue = explode( "-", $this->profile["XMLDATA"][$skuPropCode]["COMPLEX_TRUE_VALUE"] );
                                    if( count( $arValue ) == 2 ){
                                        $fieldCode = "CATALOG_".$arValue[1];
                                    }
                                    elseif( count( $arValue ) == 3 ){
                                        $fieldCode = "PROPERTY_".$arValue[2]."_DISPLAY_VALUE";
                                    }
                                    else{
                                        $fieldCode = $this->profile["XMLDATA"][$skuPropCode]["COMPLEX_TRUE_VALUE"];
                                    }
                                    
                                    if( !isset( $arOfferCondElementProperties[$fieldCode] ) ){
                                        $fieldVal = $arElement[$fieldCode];
                                    }
                                    else{
                                        $fieldVal = $arOfferCondElementProperties[$fieldCode];    
                                    }
                                    
                                    if( is_array( $fieldVal ) && !empty( $fieldVal ) ){
                                        $fieldVal = $fieldVal[0];
                                    }
                                }                                
                            }
                            else break;
                            
                            $arOfferElementOffers .= ( strlen( $arOfferElementOffers ) > 0 ) ? ":".$fieldVal : $fieldVal; 
                        }
                        
                        $arElement["OFFERS"] .= ( strlen( trim( $arElement["OFFERS"] ) ) > 0 ) ? ";".$arOfferElementOffers : $arOfferElementOffers;
                    }
                }
                
                $arProcessTmp[] = $arElement;
            }   
            
            $arProcess = array();
            foreach( $arProcessTmp as $arProcessTmpItem ){  
                $arProcessItem = array();
                $processMeasure = "0x0x0";
                
                foreach( $this->profile["XMLDATA"] as $arProfileTemplateNode ){
                    if( $arProfileTemplateNode["TYPE"] == "const" 
                        || ( ( $arProfileTemplateNode["TYPE"] == "complex" ) && ( $arProfileTemplateNode["COMPLEX_TRUE_TYPE"] == "const" ) ) ){
                        
                        $contValue = ( $arProfileTemplateNode["TYPE"] == "const" ) ? $arProfileTemplateNode["CONTVALUE_TRUE"] : $arProfileTemplateNode["COMPLEX_TRUE_CONTVALUE"];
                            
                        if( ( $arProfileTemplateNode["CODE"] != "WIDTH" ) && ( $arProfileTemplateNode["CODE"] != "HEIGHT" ) && ( $arProfileTemplateNode["CODE"] != "LENGHT" ) ){
                            if( $arProfileTemplateNode["TYPE"] == "const" ){
                                $arProcessItem[$arProfileTemplateNode["CODE"]] = $contValue;    
                            }
                            else{
                                $arProcessItem[$arProfileTemplateNode["CODE"]] = $contValue;    
                            }
                        }
                        elseif( $arProfileTemplateNode["CODE"] == "WIDTH" ){
                            if( intval( $contValue ) > 0 ){
                                $processMeasure = intval( $contValue )."x";
                            }
                            else{
                                $processMeasure = "0x";
                            }
                        }
                        elseif( $arProfileTemplateNode["CODE"] == "HEIGHT" ){
                            if( intval( $contValue ) > 0 ){
                                $processMeasure = $processMeasure.intval( $contValue )."x";
                            }
                            else{
                                $processMeasure = $processMeasure."0x";
                            }
                        }
                        elseif( $arProfileTemplateNode["CODE"] == "LENGHT" ){
                            if( intval( $contValue ) > 0 ){
                                $processMeasure = $processMeasure.intval( $contValue );
                            }
                            else{
                                $processMeasure = $processMeasure."0";
                            }
                        }
                    }
                    elseif( $arProfileTemplateNode["TYPE"] == "field"
                        || ( ( $arProfileTemplateNode["TYPE"] == "complex" ) && ( $arProfileTemplateNode["COMPLEX_TRUE_TYPE"] == "field" ) ) ){
                        
                        $fieldValue = ( $arProfileTemplateNode["TYPE"] == "field" ) ? $arProfileTemplateNode["VALUE"] : $arProfileTemplateNode["COMPLEX_TRUE_VALUE"];
                            
                        $arValue = explode( "-", $fieldValue );
                        
                        if( count( $arValue ) == 2 ){
                            $fieldValue = "CATALOG_".$arValue[1];
                        }
                        
                        if( $arProfileTemplateNode["CODE"] == "CATEGORY" ){
                            $dbSectionList = CIBlockSection::GetNavChain(
                                false,
                                $arProcessTmpItem[$fieldValue]
                            );
                            
                            $processSections = "";
                            while( $arSectionPath = $dbSectionList->GetNext() ){
                                $processSections .= ( strlen( $processSections ) > 0 ) ? " >> ".$arSectionPath["NAME"] : $arSectionPath["NAME"] ;
                            }
                            if( strlen( $processSections ) > 0 ){
                                $processSections = "[".$processSections."]";
                            }    
                            
                            $arProcessTmpItem[$fieldValue] = $processSections;
                        }
                        
                        if( $arProfileTemplateNode["CODE"] == "LENGHT" ){
                            if( intval( $arProcessTmpItem["CATALOG_MEASURE"] ) > 0 ){
                                $dbMesure = CCatalogMeasure::getList(
                                    array(),
                                    array(
                                        "ID" => $arProcessTmpItem["CATALOG_MEASURE"]
                                    )
                                );
                                
                                if( $arMesure = $dbMesure->Fetch() ){
                                    $processMeasure = $arMesure["SYMBOL_RUS"];
                                }
                                
                                $arProcessItem["MEASURE"] = $processMeasure;
                            }    
                        }
                        
                        if( ( $arProfileTemplateNode["CODE"] != "WIDTH" ) && ( $arProfileTemplateNode["CODE"] != "HEIGHT" ) && ( $arProfileTemplateNode["CODE"] != "LENGHT" ) ){
                            $arProcessItem[$arProfileTemplateNode["CODE"]] = ( ( $arProfileTemplateNode["CODE"] == "PHOTOS" ) ? ( $this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"] ) : "" ).$arProcessTmpItem[$fieldValue];    
                        }
                    }
                    else{
                        if( ( $arProfileTemplateNode["CODE"] != "WIDTH" ) && ( $arProfileTemplateNode["CODE"] != "HEIGHT" ) && ( $arProfileTemplateNode["CODE"] != "LENGHT" ) ){
                            $arProcessItem[$arProfileTemplateNode["CODE"]] = "";
                        }
                        elseif( $arProfileTemplateNode["CODE"] == "LENGHT" ){
                            $arProcessItem["MEASURE"] = $processMeasure;
                        }
                    }    
                    if( $arProfileTemplateNode["CODE"] == "AMOUNT" ){
                        $arProcessItem["OFFERS"] = $arProcessTmpItem["OFFERS"];
                    }
                }             
                $arProcess[] = $arProcessItem;
            }  
            
            $csvFile = new CCSVData();
            $csvFile->SetFieldsType( "R" );
            $delimiter_r_char = ";";
            $csvFile->SetDelimiter( $delimiter_r_char );
            
            $arResFields = array();
            
            $arTuple = array();
            $arTuple[] = "sku";
            $arTuple[] = "name";
            $arTuple[] = "paramsynonym";
            $arTuple[] = "category";
            $arTuple[] = "enabled";
            $arTuple[] = "currency";
            $arTuple[] = "price";
            $arTuple[] = "purchaseprice";
            $arTuple[] = "amount";
            $arTuple[] = "sku:size:color:price:purchaseprice:amount";
            $arTuple[] = "unit";
            $arTuple[] = "discount";
            $arTuple[] = "shippingprice";
            $arTuple[] = "weight";
            $arTuple[] = "size";
            $arTuple[] = "briefdescription";
            $arTuple[] = "description";
            $arTuple[] = "title";
            $arTuple[] = "metakeywords";
            $arTuple[] = "metadescription";
            $arTuple[] = "photos";
            $arTuple[] = "markers";
            $arTuple[] = "properties";
            $arTuple[] = "producer";
            $arTuple[] = "preorder";
            $arTuple[] = "salesnote";
            $arTuple[] = "related sku";
            $arTuple[] = "alternative sku";
            $arTuple[] = "customoption";
            $arTuple[] = "gtin";
            $arTuple[] = "googleproductcategory";
            $arTuple[] = "adult";
            $arTuple[] = "manufacturer warranty";
            $arTuple[] = "tags";
            $arTuple[] = "gifts";
            $arTuple[] = "productsets";
            
            self::ExportArrayMultiply( $arResFields, $arTuple );
                                        
            if( $paternCharset == "cp1251" ){
                $this->baseDateTimePatern = $APPLICATION->ConvertCharset( $this->baseDateTimePatern, "cp1251", "utf8" );
            }
                                        
            foreach( $arProcess as $field ){
                $arTuple = array();
                foreach( $field as $fieldPart ){
                    $arTuple[] = $this->ExportConvertCharset( $fieldPart );    
                }
                self::ExportArrayMultiply( $arResFields, $arTuple );
            }
            
            foreach( $arResFields as $arTuple ){
                $csvFile->SaveFile( $fileExport, $arTuple );
            }
                       
            $csvFile->CloseFile();
                         
            if( !$cronrun ){                                   
                LocalRedirect( $fileExportName );
            }
        }
        else{
            $arPaternFields = array();
            while( $dbElement = $dbElements->GetNextElement() ){
                $arRowToCsv = $this->ProcessElementToCsv( $dbElement );

                if( $arRowToCsv ){
                    if( empty( $arPaternFields ) ){
                        foreach( $arRowToCsv as $colIndex => $colValue ){
                            $arPaternFields[] = $colIndex;
                        }
                    }
                    
                    $arProcess[] = $arRowToCsv;    
                }
                
                $arItem = $this->GetElementProperties( $dbElement );
                
                if( $this->catalogIncluded && ( $this->profile["USE_SKU"] == "Y" ) && ( $this->catalogSKU[$arItem["IBLOCK_ID"]] ) && ( $arItem["ACTIVE"] == "Y" ) ){
                    $arOfferFilter = array(
                        "IBLOCK_ID" => $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"],
                        "PROPERTY_".$this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_PROPERTY_ID"] => $arItem["ID"]
                    );   
                    
                    $dbOfferElements = CIBlockElement::GetList(
                        array(),
                        $arOfferFilter,
                        false,
                        false,
                        array()
                    );
                    
                    while( $arOfferElement = $dbOfferElements->GetNextElement() ){
                        $arOfferRowToCsv = $this->ProcessElementToCsv( $arOfferElement, $arItem );

                        if( !$arOfferRowToCsv ) continue;
                 
                        if( empty( $arPaternFields ) ){
                            foreach( $arOfferRowToCsv as $colIndex => $colValue ){
                                $arPaternFields[] = $colIndex;
                            }
                        }
                        
                        $arProcess[] = $arOfferRowToCsv;
                    }
                }
            }      
            
            $csvFile = new CCSVData();
            $csvFile->SetFieldsType( "R" );
            $delimiter_r_char = ";";
            $csvFile->SetDelimiter( $delimiter_r_char );
            
            $arResFields = array();
            
            $arTuple = array();
            foreach( $arPaternFields as $paternField ){
                $arTuple[] = $this->ExportConvertCharset( $paternField );
            }
            
            self::ExportArrayMultiply( $arResFields, $arTuple );
            
            foreach( $arProcess as $arRow ){
                $arTuple = array();
                foreach( $arRow as $colValue ){
                    $arTuple[] = $this->ExportConvertCharset( $colValue );
                }
                self::ExportArrayMultiply( $arResFields, $arTuple );
            }   
            
            foreach( $arResFields as $arTuple ){
                $csvFile->SaveFile( $fileExport, $arTuple );
            }
                       
            $csvFile->CloseFile();
                                                            
            if( $this->profile["USE_COMPRESS"] == "Y" ){
                $originalName = $_SERVER["DOCUMENT_ROOT"].$fileExportName;
                $zipPath = str_replace( "csv", "zip", $originalName );                
                $packarc = CBXArchive::GetArchive( $zipPath );
                
                $fileQuickPath = $fileExportName;
                $arFileQuickPath = explode( "/", $fileQuickPath );                
                $fileQuickPathToDelete = "";
                foreach( $arFileQuickPath as $filePathPartIndex => $filePathPart ){
                    if( $filePathPartIndex < count( $arFileQuickPath ) - 1 ){
                        $fileQuickPathToDelete .= $filePathPart."/";
                    }
                }
                
                $packarc->SetOptions(
                    array(
                        "COMPRESS" => true,
                        "STEP_TIME" => COption::GetOptionString( "fileman", "archive_step_time", 30 ),
                        "ADD_PATH" => false,
                        "REMOVE_PATH" => $_SERVER["DOCUMENT_ROOT"].$fileQuickPathToDelete,
                        "CHECK_PERMISSIONS" => false
                    )
                );
                $pArcResult = $packarc->Pack( $originalName );
                if( !$cronrun ){
                    LocalRedirect( str_replace( $_SERVER["DOCUMENT_ROOT"], "", $zipPath ) );
                }
            }
            else{
                if( !$cronrun ){
                    LocalRedirect( $fileExportName );    
                }
            }
        }
        
        return true;
    }
    
    private function isVariant( $categoryId = false ){
        if( $categoryId ){
            return ( ( $this->profile["USE_VARIANT"] == "Y" )
                && ( $this->profile["TYPE"] == "activizm" )
                && ( $this->profile["VARIANT"]["CATEGORY"][$categoryId] ) );
        }
        return ( ( $this->profile["USE_VARIANT"] == "Y" ) && ( $this->profile["TYPE"] == "activizm" ) );
    }
    
    public function ProcessXML( $page = 1, $cronrun = false, $arOzonCategories = false ){
        if( $page == 1 ){
            //unset( $_SESSION["EXPORTPRO"][$this->profile["ID"]] );
            $this->log->Init( $this->profile );
            $this->page = $page;                                    
        }                     
        
        $this->currencyRates = CExportproProfile::LoadCurrencyRates();
        $iblockList = $this->PrepareIBlock();
        if( empty( $iblockList ) )
            return true;
            
        $pregMatchExp = GetMessage( "ACRIT_EXPORTPRO_A_AA_A" );
                                                                                                          
        //preg_match_all( @$pregMatchExp, $this->profile["OFFER_TEMPLATE"], $this->arMatches );
        //preg_match_all( "/.*(<[\w\d_-]+).*(#[\w\d_-]+#).*(<\/[\w\d_-]+>)/", $this->profile["OFFER_TEMPLATE"], $this->arMatches );
        preg_match_all( "/.*(<.+>).*(#[\w\d_-]+:*[\w\d_-]+#).*(<\/.+>)/", $this->profile["OFFER_TEMPLATE"], $this->arMatches );

        // Install for all templates #EXAMPLE# null value, so that you can remove
        $this->templateValuesDefaults = array();
        foreach( $this->arMatches[2] as $match ){
            $this->templateValuesDefaults[$match] = "";
        }
        $this->templateValuesDefaults["#MARKET_CATEGORY#"] = "";

        // Get the properties used in the templates
        $this->useProperties = array(
            "ID" => array()
        );
        
        $this->usePrices = array();
        foreach( $this->profile["XMLDATA"] as $field ){
            if( !empty( $field["VALUE"] ) || !empty( $field["CONTVALUE_FALSE"] ) || !empty( $field["CONTVALUE_TRUE"] )
                || !empty( $field["COMPLEX_TRUE_VALUE"] ) || !empty( $field["COMPLEX_FALSE_VALUE"] ) 
                || !empty( $field["COMPLEX_TRUE_CONTVALUE"] ) || !empty( $field["COMPLEX_FALSE_CONTVALUE"] ) ){
                
                $fieldValue = ( $field["TYPE"] == "field" ) ? $field["VALUE"] : $field["COMPLEX_TRUE_VALUE"];
                $arValue = explode( "-", $fieldValue );
                    
                switch( count( $arValue ) ){
                    case 1:
                        $this->useFields[] = $arValue[0];
                        break;
                    case 2:
                        $this->usePrices[] = $arValue[1];
                        break;
                    case 3:
                        $this->useProperties["ID"][] = $arValue[2];
                        break;
                }
                
                if( $field["CONDITION"]["CHILDREN"] ){
                    if( !function_exists( findChildren ) ){
                        function findChildren( $children ){
                            $retVal = array();
                            foreach( $children as $child ){
                                if( strstr( $child["CLASS_ID"], "CondIBProp" ) ){
                                    $arProp = explode( ":", $child["CLASS_ID"] );
                                    $retVal[] = $arProp[2];
                                }
                                if( $child["CHILDREN"] ){
                                    $retVal = array_merge( $retVal, findChildren( $child["CHILDREN"] ) );
                                }
                            }
                            return $retVal;
                        }
                    }
                    $this->useProperties["ID"] = array_merge( $this->useProperties["ID"], findChildren( $field["CONDITION"]["CHILDREN"] ) );
                    //$this->useProperties["ID"] = findChildren( $field["CONDITION"]["CHILDREN"] );
                }
            }
                                                                                                         
            if( $field["EVAL_FILTER"] ){
                preg_match_all( "/.*?PROPERTY_(\d+)|(CATALOG_PRICE_[\d]+_WD|CATALOG_PRICE_[\d]+_D).*?/", $this->profile["EVAL_FILTER"], $filterProps );
                if( is_array( $filterProps[1] ) ){
                    $this->useProperties["ID"] = array_merge( $this->useProperties["ID"], $filterProps[1] );
                }
                if( is_array( $filterProps[2] ) ){
                    $this->usePrices = array_merge( $this->usePrices, $filterProps[2] );
                }
            }
        }
        preg_match_all( "/.*?PROPERTY_(\d+)|(CATALOG_PRICE_[\d]+_WD|CATALOG_PRICE_[\d]+_D).*?/", $this->profile["EVAL_FILTER"], $filterProps );
        
        if( is_array( $filterProps[1] ) ){
            $this->useProperties["ID"] = array_merge( $this->useProperties["ID"], $filterProps[1] );
        }
        if( is_array( $filterProps[2] ) ){
            $this->usePrices = array_merge( $this->usePrices, $filterProps[2] );
        }
        
        $this->useProperties["ID"] = array_unique( $this->useProperties["ID"] );
        $this->useProperties["ID"] = array_filter( $this->useProperties["ID"] );

        $this->currencyList = array();

        // Variant properties
        $variantPrice = str_replace( "-", "_", $this->profile["VARIANT"]["PRICE"] );
        $variantPropCode = array(
            "SEX_VALUE" => "SEX",
            "COLOR_VALUE" => "COLOR",
            "SIZE_VALUE" => "SIZE",
            "WEIGHT_VALUE" => "WEIGHT",
            "SEXOFFER_VALUE" => "SEXOFFER",
            "COLOROFFER_VALUE" => "COLOROFFER",
            "SIZEOFFER_VALUE" => "SIZEOFFER",
            "WEIGHTOFFER_VALUE" => "WEIGHTOFFER"
        );
        
        foreach( $this->profile["VARIANT"] as $vpKey => $vpValue ){
            if( key_exists( $vpKey, $variantPropCode ) ){
                $variantProperty = explode( "-", $vpValue );
                if( count( $variantProperty ) == 3 ){
                    $this->useProperties["ID"][] = $variantProperty[2];
                    $this->variantProperties[$variantPropCode[$vpKey]] = "PROPERTY_".$variantProperty[2]."_DISPLAY_VALUE";
                }
            }
        }

        $order = array(
            "iblock_id" => "asc",
            "id" => "asc",
        );
        
        $arFilter = array(
            "IBLOCK_ID" => $iblockList,
            "SECTION_ID" => $this->profile["CATEGORY"],
        );
        
        if( $profile["CHECK_INCLUDE"] != "Y" ){
            $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
        }
        
        $arNavStartParams = array(
            "nPageSize" => $this->stepElements,
            "iNumPage" => $page
        );
        
        //if($cronrun)
        //    $arNavStartParams = false;

        $dbElements = CIBlockElement::GetList(
            $order,
            $arFilter,
            false,
            $arNavStartParams,
            array()
        );
        
        $sessionData = AcritExportproSession::GetSession( $this->profile["ID"] );
        $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["STEPS"] = $this->isDemo ? 1 : $dbElements->NavPageCount;
        AcritExportproSession::SetSession( $this->profile["ID"], $sessionData );
        
        //if($page > $dbElements->NavPageCount && !$this->isDemo )
        //    return true;
        
        while( $arElement = $dbElements->GetNextElement() ){
            $variantItems = array();
            $arItem = $this->ProcessElement( $arElement, false, $arOzonCategories );
            
            if( !$arItem )
                continue;

            if( $this->isVariant( $arItem["IBLOCK_SECTION_ID"] ) ){
                if( !$arItem["SKIP"] ){
                    $variantItems[$arItem["ITEM"][$variantPrice]][] = $arItem;
                }
                
                $arItem = $arItem["ITEM"];
            }
            
            // If you enable the processing trade offers, we look for and process trade offers
            if( $this->catalogIncluded && ( $this->profile["USE_SKU"] == "Y" ) && ( $this->catalogSKU[$arItem["IBLOCK_ID"]] ) && ( $arItem["ACTIVE"] == "Y" ) ){
                $arOfferFilter = array(
                    "IBLOCK_ID" => $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"],
                    "PROPERTY_".$this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_PROPERTY_ID"] => $arItem["ID"]
                );
                
                $dbOfferElements = CIBlockElement::GetList(
                    array(),
                    $arOfferFilter,
                    false,
                    false,
                    array()
                );
                
                while( $arOfferElement = $dbOfferElements->GetNextElement() ){
                    $arOfferItem = $this->ProcessElement( $arOfferElement, $arItem, $arOzonCategories );

                    if( $this->isVariant( $arItem["IBLOCK_SECTION_ID"] ) ){
                        $variantItems[$arOfferItem["ITEM"][$variantPrice]][] = $arOfferItem;
                    }
                    unset( $arOfferItem );
                    
                    if( $this->isDemo && $this->DemoCount() )
                        break;
                }
            }
            
            // activizm.ru profile
            if( $this->isVariant( $arItem["IBLOCK_SECTION_ID"] ) ){
                $productExport = 0;
                foreach( $variantItems as $price => $items ){
                    //if( empty( $price ) )
                    //    continue;

                    $itemTemplate = $items[0]["XML"];
                    $colorsize = array();
                    //$itemTemplate = str_replace( "</offer>", "<variantList></offer>", $itemTemplate );
                    $variantItemTemplate = "";
                    foreach( $items as $item ){
                        $arItem = $item["ITEM"];
                        $isOffer = $item["OFFER"];
                        $gender = $this->profile["VARIANT"]["SEX_CONST"] ? $this->profile["VARIANT"]["SEX_CONST"] : $arItem[$this->variantProperties["SEX"]];
                        $arSize = explode( "-", $this->profile["VARIANT"]["CATEGORY"][$arItem["IBLOCK_SECTION_ID"]] );
                        $arSizeExt = explode( "-", $this->profile["VARIANT"]["CATEGORY_EXT"][$arItem["IBLOCK_SECTION_ID"]] );
                        $itemSize = $this->variantProperties["SIZE"];
                        $itemWeight = $this->variantProperties["WEIGHT"];
                        $itemColor = $this->variantProperties["COLOR"];
                        if( $isOffer ){
                            // If trade offer, replace property values by trade offer values
                            $gender = $this->profile["VARIANT"]["SEX_CONST"] ? $this->profile["VARIANT"]["SEX_CONST"] : $arItem[$this->variantProperties["SEXOFFER"]];
                            $itemSize = $this->variantProperties["SIZEOFFER"];
                            $itemWeight = $this->variantProperties["WEIGHTOFFER"];
                            $itemColor = $this->variantProperties["COLOROFFER"];
                        }
                        $variantHash = $arSize[1] == "OZ" ?
                            $arItem[$itemColor].$gender.$arItem[$itemWeight] :
                            $arItem[$itemColor].$arItem[$itemSize].$gender;
                        
                        if( $arSize[1] == "OZ" ){
                            if( !$arItem[$itemWeight] && !$arItem[$itemSize] )
                                continue;
                        }
                        
                        if( in_array( $variantHash, $colorsize ) )
                            continue;
                            
                        $colorsize[] = $variantHash;
                        $variatType = array();
                        
                        if( $arItem[$itemColor] )
                            $variatType[] = "color";
                            
                        if( $arItem[$itemSize] || $arItem[$itemWeight] )
                            $variatType[] = "size";
                        
                        if( !empty( $variatType ) ){
                            $variatTypeStr = implode( "_and_", $variatType );
                            $retVariant = "<variant type=\"$variatTypeStr\">".PHP_EOL;
                            if( in_array( "color", $variatType ) )
                                $retVariant .= "<color>{$arItem[$itemColor]}</color>".PHP_EOL;
                            
                            if( in_array( "size", $variatType ) ){
                                if( $arSize[1] == "OZ" ){
                                    if( !$arItem[$itemWeight] ){
                                        $arItem[$itemWeight] = $arItem[$itemSize];
                                        $arSize[1] = $arSizeExt[1];
                                    }
                                    else{
                                        $arItem[$itemWeight] = floatval( $arItem[$itemWeight] );
                                    }
                                    $retVariant .= "<size category=\"{$arSize[0]}\" gender=\"{$gender}\" system=\"{$arSize[1]}\">"
                                    .$arItem[$itemWeight].
                                    "</size>".PHP_EOL;
                                }
                                else{
                                    $retVariant .= "<size category=\"{$arSize[0]}\" gender=\"{$gender}\" system=\"{$arSize[1]}\">"
                                    .$arItem[$itemSize].
                                    "</size>".PHP_EOL;
                                }
                            }
                            $retVariant .= "<offerId>{$arItem["ID"]}</offerId>";
                            $retVariant .= "</variant>".PHP_EOL;
                            $variantItemTemplate .= $retVariant;
                            $productExport++;
                        }
                    }
                    if( strlen( $variantItemTemplate ) > 0 ){
                        $itemTemplate = str_replace( "</offer>", "<variantList>$variantItemTemplate</variantList></offer>", $itemTemplate );
                    }                             
                                
                    CAcritExportproExport::Save( $itemTemplate );
                    
                    // Increase the count statistics for export goods
                    $this->log->IncProductExport( $productExport );
                }
                unset( $variantItems );
            }

            if( $this->isDemo && $this->DemoCount() )
                break;
                
            unset( $arItem );
        }               
                     
        unset($arElement, $arItem);

        if( !$cronrun ){
            echo '<div style="width: 100%; text-align: center; font-size: 18px; margin: 40px 0; padding: 40px 0; border: 1px solid #ccc; border-radius: 6px; background: #f5f5f5;">',
            GetMessage( "ACRIT_EXPORTPRO_RUN_EXPORT_RUN" ), '<br>',
            str_replace( array( "#PROFILE_ID#", "#PROFILE_NAME#" ), array( $this->profile["ID"], $this->profile["NAME"] ), GetMessage( "ACRIT_EXPORTPRO_RUN_STEP_PROFILE" ) ), '<br>',
            str_replace( array( "#STEP#", "#COUNT#" ), array( $page, $dbElements->NavPageCount ), GetMessage( "ACRIT_EXPORTPRO_RUN_STEP_RUN" ) ),
            '</div>';
        }       
                                                  
        $this->SaveCurrencies( $this->currencyList );
        
        if( $this->isDemo && $this->DemoCount() )
            return true;
        
        if( $page >= $dbElements->NavPageCount )
            return true;
                          
        return false;
    }
    
    public function CalcProcessXMLLoadingByOneProduct(){
        $calcTimeStart = getmicrotime();
        
        $this->currencyRates = CExportproProfile::LoadCurrencyRates();
        $iblockList = $this->PrepareIBlock();
        if( empty( $iblockList ) )
            return false;
            
        $pregMatchExp = GetMessage( "ACRIT_EXPORTPRO_A_AA_A" );
                                                                                                          
        preg_match_all( "/.*(<.+>).*(#[\w\d_-]+:*[\w\d_-]+#).*(<\/.+>)/", $this->profile["OFFER_TEMPLATE"], $this->arMatches );

        $this->templateValuesDefaults = array();
        foreach( $this->arMatches[2] as $match ){
            $this->templateValuesDefaults[$match] = "";
        }
        $this->templateValuesDefaults["#MARKET_CATEGORY#"] = "";

        // Get the properties used in the templates
        $this->useProperties = array(
            "ID" => array()
        );
        
        $this->usePrices = array();
        foreach( $this->profile["XMLDATA"] as $field ){
            if( !empty( $field["VALUE"] ) || !empty( $field["CONTVALUE_FALSE"] ) || !empty( $field["CONTVALUE_TRUE"] )
                || !empty( $field["COMPLEX_TRUE_VALUE"] ) || !empty( $field["COMPLEX_FALSE_VALUE"] ) 
                || !empty( $field["COMPLEX_TRUE_CONTVALUE"] ) || !empty( $field["COMPLEX_FALSE_CONTVALUE"] ) ){
                
                $fieldValue = ( $field["TYPE"] == "field" ) ? $field["VALUE"] : $field["COMPLEX_TRUE_VALUE"];
                $arValue = explode( "-", $fieldValue );
                
                switch( count( $arValue ) ){
                    case 1:
                        $this->useFields[] = $arValue[0];
                        break;
                    case 2:
                        $this->usePrices[] = $arValue[1];
                        break;
                    case 3:
                        $this->useProperties["ID"][] = $arValue[2];
                        break;
                }
                
                if( $field["CONDITION"]["CHILDREN"] ){
                    if( !function_exists( findChildren ) ){
                        function findChildren( $children ){
                            $retVal = array();
                            foreach( $children as $child ){
                                if( strstr( $child["CLASS_ID"], "CondIBProp" ) ){
                                    $arProp = explode( ":", $child["CLASS_ID"] );
                                    $retVal[] = $arProp[2];
                                }
                                if( $child["CHILDREN"] ){
                                    $retVal = array_merge( $retVal, findChildren( $child["CHILDREN"] ) );
                                }
                            }
                            return $retVal;
                        }
                    }
                    $this->useProperties["ID"] = array_merge( $this->useProperties["ID"], findChildren( $field["CONDITION"]["CHILDREN"] ) );
                    //$this->useProperties["ID"] = findChildren( $field["CONDITION"]["CHILDREN"] );
                }
            }
            
            if( $field["EVAL_FILTER"] ){
                preg_match_all( "/.*?PROPERTY_(\d+)|(CATALOG_PRICE_[\d]+_WD|CATALOG_PRICE_[\d]+_D).*?/", $this->profile["EVAL_FILTER"], $filterProps );
                if( is_array( $filterProps[1] ) ){
                    $this->useProperties["ID"] = array_merge( $this->useProperties["ID"], $filterProps[1] );
                }
                if( is_array( $filterProps[2] ) ){
                    $this->usePrices = array_merge( $this->usePrices, $filterProps[2] );
                }
            }
        }
        preg_match_all( "/.*?PROPERTY_(\d+)|(CATALOG_PRICE_[\d]+_WD|CATALOG_PRICE_[\d]+_D).*?/", $this->profile["EVAL_FILTER"], $filterProps );
        
        if( is_array( $filterProps[1] ) ){
            $this->useProperties["ID"] = array_merge( $this->useProperties["ID"], $filterProps[1] );
        }
        if( is_array( $filterProps[2] ) ){
            $this->usePrices = array_merge( $this->usePrices, $filterProps[2] );
        }
        
        $this->useProperties["ID"] = array_unique( $this->useProperties["ID"] );
        $this->useProperties["ID"] = array_filter( $this->useProperties["ID"] );

        $this->currencyList = array();

        // Variant properties
        $variantPrice = str_replace( "-", "_", $this->profile["VARIANT"]["PRICE"] );
        $variantPropCode = array(
            "SEX_VALUE" => "SEX",
            "COLOR_VALUE" => "COLOR",
            "SIZE_VALUE" => "SIZE",
            "WEIGHT_VALUE" => "WEIGHT",
            "SEXOFFER_VALUE" => "SEXOFFER",
            "COLOROFFER_VALUE" => "COLOROFFER",
            "SIZEOFFER_VALUE" => "SIZEOFFER",
            "WEIGHTOFFER_VALUE" => "WEIGHTOFFER"
        );
        
        foreach( $this->profile["VARIANT"] as $vpKey => $vpValue ){
            if( key_exists( $vpKey, $variantPropCode ) ){
                $variantProperty = explode( "-", $vpValue );
                if( count( $variantProperty ) == 3 ){
                    $this->useProperties["ID"][] = $variantProperty[2];
                    $this->variantProperties[$variantPropCode[$vpKey]] = "PROPERTY_".$variantProperty[2]."_DISPLAY_VALUE";
                }
            }
        }

        $order = array(
            "iblock_id" => "asc",
            "id" => "asc",
        );
        
        $arFilter = array(
            "IBLOCK_ID" => $iblockList,
            "SECTION_ID" => $this->profile["CATEGORY"],
        );
        
        if( $profile["CHECK_INCLUDE"] != "Y" ){
            $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
        }
        
        $arNavStartParams = array(
            "nTopCount" => 1,
        );
        
        $dbElements = CIBlockElement::GetList(
            $order,
            $arFilter,
            false,
            $arNavStartParams,
            array()
        );
        
        $sessionData = AcritExportproSession::GetSession( $this->profile["ID"] );
        $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["STEPS"] = $this->isDemo ? 1 : $dbElements->NavPageCount;
        AcritExportproSession::SetSession( $this->profile["ID"], $sessionData );
        
        while( $arElement = $dbElements->GetNextElement() ){
            $variantItems = array();
            $arItem = $this->ProcessElement( $arElement );
            
            if( !$arItem )
                continue;

            if( $this->isVariant( $arItem["IBLOCK_SECTION_ID"] ) ){
                if( !$arItem["SKIP"] ){
                    $variantItems[$arItem["ITEM"][$variantPrice]][] = $arItem;
                }
                
                $arItem = $arItem["ITEM"];
            }
            unset( $arItem );
        }                            
                     
        unset($arElement, $arItem);

        $this->SaveCurrencies( $this->currencyList );
                                                           
        return round( getmicrotime() - $calcTimeStart, 3 );
    }
    
    // Getting the properties of the product, the formation of the pattern, the replacement values of fields and write to the file
    private function ProcessElementToCsv( $arElement, $arProductSKU = false ){
        $skipElement = false;
        $arItem = $this->GetElementProperties( $arElement );
        
        // Adding product properties and fields to trade offers
        if( is_array( $arProductSKU ) ){
            $excludeFields = array(
                "NAME",
                "PREVIEW_TEXT",
                "DETAIL_TEXT"
            );
            
            foreach( $arProductSKU as $key => $value ){
                if( !isset( $arItem[$key] ) || empty( $arItem[$key] ) ){
                    if( !in_array( $key, $excludeFields ) ){
                        $arItem[$key] = $value;
                    }
                }
            }
            
            $arItem["IBLOCK_SECTION_ID"] = $arProductSKU["IBLOCK_SECTION_ID"];                                       
        }
        else{
            $arItem["GROUP_ITEM_ID"] = $arItem["ID"];
        }         

        // Verification, whether the item meets the general conditions of profile
        if( !$this->CheckCondition( $arItem, $this->profile["EVAL_FILTER"] ) )
            return false;

        // Increase the count statistics for export goods
        $this->log->IncProduct();

        $itemTemplate = $this->profile["OFFER_TEMPLATE"];
        $templateValues = $this->templateValuesDefaults;
        $templateValues["GROUP_ITEM_ID"] = $arItem["GROUP_ITEM_ID"];

        foreach( $this->profile["XMLDATA"] as $field ){
            $useCondition = ( $field["USE_CONDITION"] == "Y" );
            if( $useCondition ){
                $conditionTrue = ( $this->CheckCondition( $arItem, $field["EVAL_FILTER"] ) == true );
            }

            if( $useCondition && !$conditionTrue ){
                if( ( $field["TYPE"] == "const" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_FALSE_TYPE"] == "const" ) ) ){
                    
                    $field["CONTVALUE_FALSE"] = ( $field["TYPE"] == "const" ) ? $field["CONTVALUE_FALSE"] : $field["COMPLEX_FALSE_CONTVALUE"];            
                    $templateValues["{$field["CODE"]}"] = $field["CONTVALUE_FALSE"];
                    continue;
                }
                else{
                    $field["VALUE"] = $field["COMPLEX_FALSE_VALUE"];
                
                    if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                        $templateValues["{$field["CODE"]}"] = detailLink( $arItem["ID"] );
                        $linkParamSymbolIndex = stripos( "?", $itemTemplate );
                        $linkUtmSymbolIndex = stripos( "?utm_source", $itemTemplate );
                        if( $linkParamSymbolIndex != $linkUtmSymbolIndex ){
                            $itemTemplate = str_replace( "?utm_source", "&amp;utm_source", $itemTemplate );    
                        }
                    }
                    else{
                        if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                            $templateValues["{$field["CODE"]}"] = detailLink( $arItem["ID"] );
                        }
                        else{
                            $arValue = explode( "-", $field["VALUE"] );
                            switch( count( $arValue ) ){
                                case 1:
                                    //if( $this->elementType["OFFER"][$arItem["ID"]] && $field["VALUE"] == "IBLOCK_SECTION_ID" )
                                    //    $arItem["IBLOCK_SECTION_ID"] = $this->elementType["OFFER"][$arItem["ID"]];

                                    $templateValues["{$field["CODE"]}"] = $arItem[$field["VALUE"]];
                                    break;
                                case 2:
                                    $templateValues["{$field["CODE"]}"] = $arItem["CATALOG_".$arValue[1]];
                                    preg_match( "PRICE_[\d]+", $arValue[1], $arPriceCode );
                                    $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];
                                    if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                        $templateValues["{$field["CODE"]}"] = $convertFrom;
                                        if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                            if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                $templateValues["{$field["CODE"]}"] = $convertTo;
                                            }
                                        }
                                    }
                                    elseif( !empty( $arPriceCode[0] ) ){
                                        if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                            if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                    $templateValues["{$field["CODE"]}"] = round( CCurrencyRates::ConvertCurrency(
                                                            $arItem["CATALOG_".$arValue[1]],
                                                            $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                            $convertTo
                                                        ),
                                                        0 //!!2
                                                    );
                                                }
                                                else{
                                                    $templateValues["{$field["CODE"]}"] = round( $templateValues["{$field["CODE"]}"] *
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                        0 //!!2
                                                    );
                                                }
                                                //if( !in_array( $convertFrom, $this->currencyList ) )
                                                //    $this->currencyList[] = $convertFrom;
                                            }
                                            if( !in_array( $convertFrom, $this->currencyList ) )
                                                $this->currencyList[] = $convertFrom;
                                        }
                                        else{
                                            if( !in_array( $convertFrom, $this->currencyList ) )
                                                $this->currencyList[] = $convertFrom;
                                        }
                                        if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] )
                                            $templateValues["{$field["CODE"]}"] += $templateValues["{$field["CODE"]}"] *
                                            floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                    }
                                    break;
                                case 3:
                                    if( $arValue[0] == $arItem["IBLOCK_ID"] || $arValue[0] == $arProductSKU["IBLOCK_ID"] ){
                                        if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                            $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = $arItem["PROPERTY_{$arValue[2]}_VALUE"][0];
                                        }

                                        if( is_array( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] ) ){
                                            $templateValues["{$field["CODE"]}"] = array();
                                            foreach( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] as $val ){
                                                $templateValues["{$field["CODE"]}"][] = $val;
                                            }
                                        }
                                        else{
                                            $templateValues["{$field["CODE"]}"] = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                }  
            }
            else{
                // Если значение поле или свойство
                if( ( $field["TYPE"] == "field" ) 
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_TRUE_TYPE"] == "field" ) ) ){
                    
                    $field["VALUE"] = ( $field["TYPE"] == "field" ) ? $field["VALUE"] : $field["COMPLEX_TRUE_VALUE"];
                        
                    if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                        $templateValues["{$field["CODE"]}"] = detailLink( $arItem["ID"] );
                    }
                    else{
                        $arValue = explode( "-", $field["VALUE"] );
                        switch( count( $arValue ) ){
                            case 1:
                                //if( $this->elementType["OFFER"][$arItem["ID"]] && $field["VALUE"] == "IBLOCK_SECTION_ID" )
                                //    $arItem["IBLOCK_SECTION_ID"] = $this->elementType["OFFER"][$arItem["ID"]];

                                $templateValues["{$field["CODE"]}"] = $arItem[$field["VALUE"]];
                                break;
                            case 2:
                                $templateValues["{$field["CODE"]}"] = $arItem["CATALOG_".$arValue[1]];
                                preg_match( "PRICE_[\d]+", $arValue[1], $arPriceCode );
                                $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];
                                if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                    $templateValues["{$field["CODE"]}"] = $convertFrom;
                                    if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                        if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                            $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                            $templateValues["{$field["CODE"]}"] = $convertTo;
                                        }
                                    }
                                }
                                elseif( !empty( $arPriceCode[0] ) ){
                                    if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                        if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                            $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                            if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                $templateValues["{$field["CODE"]}"] = round( CCurrencyRates::ConvertCurrency(
                                                        $arItem["CATALOG_".$arValue[1]],
                                                        $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                        $convertTo
                                                    ),
                                                    0 //!!2
                                                );
                                            }
                                            else{
                                                $templateValues["{$field["CODE"]}"] = round( $templateValues["{$field["CODE"]}"] *
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                    0 //!!2
                                                );
                                            }
                                            //if( !in_array( $convertFrom, $this->currencyList ) )
                                            //    $this->currencyList[] = $convertFrom;
                                        }
                                        if( !in_array( $convertFrom, $this->currencyList ) )
                                            $this->currencyList[] = $convertFrom;
                                    }
                                    else{
                                        if( !in_array( $convertFrom, $this->currencyList ) )
                                            $this->currencyList[] = $convertFrom;
                                    }
                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] )
                                        $templateValues["{$field["CODE"]}"] += $templateValues["{$field["CODE"]}"] *
                                        floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                }
                                break;
                            case 3:
                                if( $arValue[0] == $arItem["IBLOCK_ID"] || $arValue[0] == $arProductSKU["IBLOCK_ID"] ){
                                    if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                        $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = $arItem["PROPERTY_{$arValue[2]}_VALUE"][0];
                                    }

                                    if( is_array( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] ) ){
                                        $templateValues["{$field["CODE"]}"] = array();
                                        foreach( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] as $val ){
                                            $templateValues["{$field["CODE"]}"][] = $val;
                                        }
                                    }
                                    else{
                                        $templateValues["{$field["CODE"]}"] = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                    }
                                }
                                break;
                        }
                    }
                }
                elseif( ( $field["TYPE"] == "const" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_TRUE_TYPE"] == "const" ) ) ){ // Если константа
                
                    $field["CONTVALUE_TRUE"] = ( $field["TYPE"] == "const" ) ? $field["CONTVALUE_TRUE"] : $field["COMPLEX_TRUE_CONTVALUE"];            
                    $templateValues["{$field["CODE"]}"] = $field["CONTVALUE_TRUE"];
                }   
                else{
                    $templateValues["{$field["CODE"]}"] = "";
                }
            }
                                                          
            if( ( $field["REQUIRED"] == "Y" ) && ( empty( $templateValues["{$field["CODE"]}"] ) || !isset( $templateValues["{$field["CODE"]}"] ) ) ){
                $skipElement = true;
                $this->log->AddMessage( "{$arItem["NAME"]} (ID:{$arItem["ID"]}) : ".str_replace( "FIELD", "{$field["CODE"]}", GetMessage( "ACRIT_EXPORTPRO_REQUIRED_FIELD_SKIP" ) ) );
                //!!!!!$this->log->AddMessage( "{$arItem["NAME"]} (ID:{$arItem["ID"]}) : ".$field["CODE"] );
                $this->log->IncProductError();
            }
        }     

        //$this->log->AddMessage( var_export($arItem, true) );

        array_walk( $templateValues, function( &$value ){
            if( is_array( $value ) ){
                foreach( $value as $id => $val )
                    $value[$id] = htmlspecialcharsbx( $val );
            }
            else
            $value = htmlspecialcharsbx( $value );
        });
        

        // Устанавливаем значение категории маркета если стоит галка
        $templateValues["MARKET_CATEGORY"] = "";
        switch( $this->profile["TYPE"] ){
            case "ebay":
            case "ebay_1":
            case "ebay_2":
                $templateValues["MARKET_CATEGORY"] = $this->profile["MARKET_CATEGORY"]["EBAY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]];
                break;
            case "google":
                if( $this->profile["USE_MARKET_CATEGORY"] == "Y" ){
                    $templateValues["MARKET_CATEGORY"] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]];
                }
                break;
            case "ozon":
                $templateValues["MARKET_CATEGORY"] = $this->profile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]];
                break;
            case "y_realty":
                break;
            default:
                if( $this->profile["USE_MARKET_CATEGORY"] == "Y" ){
                    $templateValues["MARKET_CATEGORY"] = htmlspecialcharsbx( $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]] );
                }
        }                
        
        return !empty( $templateValues ) ? $templateValues : false;
    }        
        
    // Получение свойств товара, формирование шаблона, замена полей значениями и запись в файл
    private function ProcessElement( $arElement, $arProductSKU = false, $arOzonCategories = false ){
        global $DB, $APPLICATION;
                                 
        $skipElement = false;
        $arItem = $this->GetElementProperties( $arElement );   
        
        // Добавление свойств и полей товара к товарным предложения
        if( $this->catalogIncluded && is_array( $arProductSKU ) ){
            $excludeFields = array(
                "NAME",
                "PREVIEW_TEXT",
                "DETAIL_TEXT",
                "DETAIL_PICTURE",
                "CATALOG_QUANTITY",
                "CATALOG_QUANTITY_RESERVED",
                "CATALOG_WEIGHT",
                "CATALOG_WIDTH",
                "CATALOG_LENGTH",
                "CATALOG_HEIGHT",
                "CATALOG_PURCHASING_PRICE",
            );
            
            foreach( $arProductSKU as $key => $value ){
                if( !isset( $arItem[$key] ) || empty( $arItem[$key] ) ){
                    if( !in_array( $key, $excludeFields ) ){
                        $arItem[$key] = $value;
                    }
                }
            }      
            
            $arItem["ELEMENT_ID"] = $arProductSKU["ID"];
            $arItem["IBLOCK_SECTION_ID"] = $arProductSKU["IBLOCK_SECTION_ID"];
            foreach( $this->profile["NAMESCHEMA"] as $key => $value ){
                switch( $value ){
                    case $key."_OFFER":
                        $arItem[$key] = $arProductSKU[$key];
                        break;
                    case $key."_OFFER_SKU":
                        $arItem[$key] = implode( " ", array( $arProductSKU[$key], $arItem[$key] ) );
                        break;
                }
                $arItem[$key] = strip_tags( $arItem[$key] );
            }
        }
        else{
            $arItem["GROUP_ITEM_ID"] = $arItem["ID"];
        }                               

        // Проверка, удовлетворяет ли элемент общим условия профиля
        if( $this->catalogIncluded ){
            if( !$this->CheckCondition( $arItem, $this->profile["EVAL_FILTER"] ) ){
                return $arItem;
            }
        }
                          
        // Увеличиваем счетчик статистики товаров для выгрузки
        $this->log->IncProduct();     

        $itemTemplate = $this->profile["OFFER_TEMPLATE"];     
        
        $templateValues = $this->templateValuesDefaults;
        
        $arItemSections = array();
        if( $this->profile["EXPORT_PARENT_CATEGORIES_TO_OFFER"] == "Y" ){
            $arItemSections = $arItem["SECTION_PARENT_ID"];
            
            if( $this->profile["EXPORT_OFFER_CATEGORIES_TO_OFFER"] == "Y" ){
                foreach( $arItem["SECTION_ID"] as $itemSectionId ){
                    if( !in_array( $itemSectionId, $arItemSections ) ){
                        $arItemSections[] = $itemSectionId;
                    }
                }
            }
        }
        elseif( $this->profile["EXPORT_OFFER_CATEGORIES_TO_OFFER"] == "Y" ){
            $arItemSections = $arItem["SECTION_ID"];
        }
        
        $sectionExportRow = "";
        if( !empty( $arItemSections ) ){
            foreach( $arItemSections as $arItemSectionsId ){
                $sectionExportRow .= "<categoryId>".$arItemSectionsId."</categoryId>".PHP_EOL;
            }
            
            $itemTemplate = str_replace( "<categoryId>#CATEGORYID#</categoryId>", $sectionExportRow, $itemTemplate );
        }
        
        $templateValues["#GROUP_ITEM_ID#"] = $arItem["GROUP_ITEM_ID"];
                   
        foreach( $this->profile["XMLDATA"] as $field ){
            $useCondition = ( $field["USE_CONDITION"] == "Y" );
            if( $useCondition ){
                $conditionTrue = ( $this->CheckCondition( $arItem, $field["EVAL_FILTER"] ) == true );
            }

            if( $useCondition && !$conditionTrue ){
                if( ( $field["TYPE"] == "const" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_FALSE_TYPE"] == "const" ) ) ){
                    
                    $field["CONTVALUE_FALSE"] = ( $field["TYPE"] == "const" ) ? $field["CONTVALUE_FALSE"] : $field["COMPLEX_FALSE_CONTVALUE"];            
                    $templateValues["#{$field["CODE"]}#"] = $field["CONTVALUE_FALSE"];
                    continue;
                }
                else{
                    $field["VALUE"] = $field["COMPLEX_FALSE_VALUE"];
                
                    if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                        $templateValues["#{$field["CODE"]}#"] = detailLink( $arItem["ID"] );
                        $linkParamSymbolIndex = stripos( "?", $itemTemplate );
                        $linkUtmSymbolIndex = stripos( "?utm_source", $itemTemplate );
                        if( $linkParamSymbolIndex != $linkUtmSymbolIndex ){
                            $itemTemplate = str_replace( "?utm_source", "&amp;utm_source", $itemTemplate );    
                        }
                    }
                    else{
                        $arValue = explode( "-", $field["VALUE"] );
                        
                        switch( count( $arValue ) ){
                            case 1:
                                //if( $this->elementType["OFFER"][$arItem["ID"]] && $field["VALUE"] == "IBLOCK_SECTION_ID" )
                                //    $arItem["IBLOCK_SECTION_ID"] = $this->elementType["OFFER"][$arItem["ID"]];
                                                                                                
                                $templateValues["#{$field["CODE"]}#"] = $arItem[$field["VALUE"]];
                                break;
                            case 2:
                                $templateValues["#{$field["CODE"]}#"] = $arItem["CATALOG_".$arValue[1]];
                                if( ( $field["CODE"] == "PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                    preg_match( "#PURCHASING_PRICE#", $arValue[1], $arPriceCode );    
                                }
                                else{
                                    preg_match( "#PRICE_[\d]+#", $arValue[1], $arPriceCode );    
                                }                                                              
                                                               
                                $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];         
                                                                      
                                if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                    $templateValues["#{$field["CODE"]}#"] = $convertFrom;
                                    if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                        if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                            $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                            $templateValues["#{$field["CODE"]}#"] = $convertTo;
                                        }
                                    }
                                }
                                elseif( !empty( $arPriceCode[0] ) ){
                                    if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                        if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                            $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                            if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                $templateValues["#{$field["CODE"]}#"] = round( CCurrencyRates::ConvertCurrency(
                                                        $arItem["CATALOG_".$arValue[1]],
                                                        $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                        $convertTo
                                                    ),
                                                    0 //!!2
                                                );
                                            }
                                            else{
                                                $templateValues["#{$field["CODE"]}#"] = round( $templateValues["#{$field["CODE"]}#"] *
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                    0 //!!2
                                                );
                                            }
                                            //if( !in_array( $convertFrom, $this->currencyList ) )
                                            //    $this->currencyList[] = $convertFrom;
                                        }
                                        if( !in_array( $convertFrom, $this->currencyList ) )
                                            $this->currencyList[] = $convertFrom;
                                    }
                                    else{
                                        if( !in_array( $convertFrom, $this->currencyList ) )
                                            $this->currencyList[] = $convertFrom;
                                    }
                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] )
                                        $templateValues["#{$field["CODE"]}#"] += $templateValues["#{$field["CODE"]}#"] *
                                        floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                }
                                break;
                            case 3:
                                if( $arValue[0] == $arItem["IBLOCK_ID"] || $arValue[0] == $arProductSKU["IBLOCK_ID"] ){
                                    if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                        $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = $arItem["PROPERTY_{$arValue[2]}_VALUE"][0];
                                    }
                                    
                                    if( is_array( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] ) ){
                                        $templateValues["#{$field["CODE"]}#"] = array();
                                        foreach( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] as $val ){
                                            if( ( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 )
                                                && ( count( $templateValues["#{$field["CODE"]}#"] ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) ){
                                                $templateValues["#{$field["CODE"]}#"][] = $val;        
                                            }
                                            else{
                                                $templateValues["#{$field["CODE"]}#"][] = $val;
                                            }
                                        }                                        
                                    }
                                    else{
                                        $templateValues["#{$field["CODE"]}#"] = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                    }
                                }
                                break;
                        }
                    }
                }  
            }
            else{
                // Если значение поле или свойство
                if( ( $field["TYPE"] == "field" ) 
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_TRUE_TYPE"] == "field" ) ) ){
                                    
                    $field["VALUE"] = ( $field["TYPE"] == "field" ) ? $field["VALUE"] : $field["COMPLEX_TRUE_VALUE"];
                    
                    if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                        $templateValues["#{$field["CODE"]}#"] = detailLink( $arItem["ID"] );
                        $linkParamSymbolIndex = stripos( "?", $itemTemplate );
                        $linkUtmSymbolIndex = stripos( "?utm_source", $itemTemplate );
                        if( $linkParamSymbolIndex != $linkUtmSymbolIndex ){
                            $itemTemplate = str_replace( "?utm_source", "&amp;utm_source", $itemTemplate );    
                        }
                    }
                    else{
                        $arValue = explode( "-", $field["VALUE"] );
                        
                        switch( count( $arValue ) ){
                            case 1:
                                //if( $this->elementType["OFFER"][$arItem["ID"]] && $field["VALUE"] == "IBLOCK_SECTION_ID" )
                                //    $arItem["IBLOCK_SECTION_ID"] = $this->elementType["OFFER"][$arItem["ID"]];
                                                                                                
                                $templateValues["#{$field["CODE"]}#"] = $arItem[$field["VALUE"]];
                                break;
                            case 2:
                                $templateValues["#{$field["CODE"]}#"] = $arItem["CATALOG_".$arValue[1]];
                                if( ( $field["CODE"] == "PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                    preg_match( "#PURCHASING_PRICE#", $arValue[1], $arPriceCode );    
                                }
                                else{
                                    preg_match( "#PRICE_[\d]+#", $arValue[1], $arPriceCode );    
                                }                                                              
                                                               
                                $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];         
                                                                      
                                if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                    $templateValues["#{$field["CODE"]}#"] = $convertFrom;
                                    if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                        if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                            $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                            $templateValues["#{$field["CODE"]}#"] = $convertTo;
                                        }
                                    }
                                }
                                elseif( !empty( $arPriceCode[0] ) ){
                                    if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                        if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                            $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                            if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                $templateValues["#{$field["CODE"]}#"] = round( CCurrencyRates::ConvertCurrency(
                                                        $arItem["CATALOG_".$arValue[1]],
                                                        $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                        $convertTo
                                                    ),
                                                    0 //!!2
                                                );
                                            }
                                            else{
                                                $templateValues["#{$field["CODE"]}#"] = round( $templateValues["#{$field["CODE"]}#"] *
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                    0 //!!2
                                                );
                                            }
                                            //if( !in_array( $convertFrom, $this->currencyList ) )
                                            //    $this->currencyList[] = $convertFrom;
                                        }
                                        if( !in_array( $convertFrom, $this->currencyList ) )
                                            $this->currencyList[] = $convertFrom;
                                    }
                                    else{
                                        if( !in_array( $convertFrom, $this->currencyList ) )
                                            $this->currencyList[] = $convertFrom;
                                    }
                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] )
                                        $templateValues["#{$field["CODE"]}#"] += $templateValues["#{$field["CODE"]}#"] *
                                        floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                }
                                break;
                            case 3:
                                if( $arValue[0] == $arItem["IBLOCK_ID"] || $arValue[0] == $arProductSKU["IBLOCK_ID"] ){
                                    if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                        $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = $arItem["PROPERTY_{$arValue[2]}_VALUE"][0];
                                    }
                                    
                                    if( is_array( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] ) ){
                                        $templateValues["#{$field["CODE"]}#"] = array();
                                        foreach( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] as $val ){
                                            if( ( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 )
                                                && ( count( $templateValues["#{$field["CODE"]}#"] ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) ){
                                                $templateValues["#{$field["CODE"]}#"][] = $val;        
                                            }
                                            else{
                                                $templateValues["#{$field["CODE"]}#"][] = $val;
                                            }
                                        }                                        
                                    }
                                    else{
                                        $templateValues["#{$field["CODE"]}#"] = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                    }
                                }
                                break;
                        }
                    }
                }
                elseif( ( $field["TYPE"] == "const" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_TRUE_TYPE"] == "const" ) ) ){ // Если константа
                
                    $field["CONTVALUE_TRUE"] = ( $field["TYPE"] == "const" ) ? $field["CONTVALUE_TRUE"] : $field["COMPLEX_TRUE_CONTVALUE"];            
                    $templateValues["#{$field["CODE"]}#"] = $field["CONTVALUE_TRUE"];
                }   
                else{
                    $templateValues["#{$field["CODE"]}#"] = "";
                }
            }                                                      
            
            if( $DB->IsDate( $templateValues["#{$field["CODE"]}#"] ) && ( $this->profile["DATEFORMAT"] == $this->baseDateTimePatern ) ){
                $templateValues["#{$field["CODE"]}#"] = $this->GetYandexDateTime( $templateValues["#{$field["CODE"]}#"] );
                $dateTimeValue = MakeTimeStamp( "" );
                $dateTimeFormattedValue = date( "Y-m-d", $dateTimeValue );
                if( stripos( $templateValues["#{$field["CODE"]}#"], $dateTimeFormattedValue ) !== false ){
                    $skipElement = true;
                    $this->log->AddMessage( "{$arItem["NAME"]} (ID:{$arItem["ID"]}) : ".str_replace( "#FIELD#", "#{$field["CODE"]}#", GetMessage( "ACRIT_EXPORTPRO_REQUIRED_FIELD_SKIP" ) ) );
                    $this->log->IncProductError();
                }
            }                                                            
                      
            if( ( $field["REQUIRED"] == "Y" ) && ( empty( $templateValues["#{$field["CODE"]}#"] ) || !isset( $templateValues["#{$field["CODE"]}#"] ) ) ){
                $skipElement = true;
                $this->log->AddMessage( "{$arItem["NAME"]} (ID:{$arItem["ID"]}) : ".str_replace( "#FIELD#", "#{$field["CODE"]}#", GetMessage( "ACRIT_EXPORTPRO_REQUIRED_FIELD_SKIP" ) ) );
                $this->log->IncProductError();
            }                                                       
        }                       
        
        if( ( intval( $templateValues["#OLDPRICE#"] ) > 0 ) 
            && ( intval( $templateValues["#OLDPRICE#"] ) <= intval( $templateValues["#PRICE#"] ) ) ){
            unset( $templateValues["#OLDPRICE#"] );
        }       
        
        /*if( intval( $templateValues["#GROSS_WEIGHT#"] ) > 0 ){
            $templateValues["#GROSS_WEIGHT#"] = $templateValues["#GROSS_WEIGHT#"] * 1000;
        }
                
        if( intval( $templateValues["#CAPABILITY_WEIGHT#"] ) > 0 ){
            $templateValues["#CAPABILITY_WEIGHT#"] = $templateValues["#CAPABILITY_WEIGHT#"] * 1000;
        }*/
                        
        array_walk( $templateValues, function( &$value ){
            if( is_array( $value ) ){
                foreach( $value as $id => $val )
                    $value[$id] = $val;
            }
            else
            $value = $value;
        });                                         
        
        // Устанавливаем значение категории маркета, если стоит галка
        $templateValues["#MARKET_CATEGORY#"] = "";
        switch( $this->profile["TYPE"] ){
            case "ebay":
            case "ebay_1":
            case "ebay_2":
                $templateValues["#MARKET_CATEGORY#"] = $this->profile["MARKET_CATEGORY"]["EBAY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]];
                break;
            case "google":
                if( $this->profile["USE_MARKET_CATEGORY"] == "Y" ){
                    $templateValues["#MARKET_CATEGORY#"] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]];
                }
                break;
            case "ozon":
                $templateValues["#MARKET_CATEGORY#"] = $this->profile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]];
                if( !empty( $arOzonCategories ) ){
                    foreach( $arOzonCategories as $arOzonCategoriesItem ){
                        if( $arOzonCategoriesItem["ProductTypeId"] == $this->profile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]] ){
                            $templateValues["#CAPABILITY_TYPE#"] = $arOzonCategoriesItem["Name"];
                        }
                    }
                }
                break;
            case "y_realty":
                break;
            default:
                if( $this->profile["USE_MARKET_CATEGORY"] == "Y" ){
                    $templateValues["#MARKET_CATEGORY#"] = htmlspecialcharsbx( $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]] );
                }
        }                                                                                                        
                                                           
        // Удаление тэгов с пустыми значения полей, дублирование тэгов для множественных свойств, URL кодирование
        foreach( $this->arMatches[2] as $id => $match ){
            $templateValueCharset = $this->GetStringCharset( $templateValues[$match] );
            
            if( ( $this->profile["XMLDATA"][str_replace( "#", "", $match )]["HTML_TO_TXT"] == "Y" ) && !is_array( $templateValues[$match] ) ){
                $templateValues[$match] = HTMLToTxt( $templateValues[$match] ); 
            }
            
            if( $this->profile["XMLDATA"][str_replace( "#", "", $match )]["HTML_ENCODE_CUT"] == "Y" ){
                if( !empty( $templateValues[$match] ) ){
                    if( $templateValueCharset == "cp1251" ){
                        $convertedTemplateValue = $APPLICATION->ConvertCharset( $templateValues[$match], "cp1251", "utf8" );
                        $convertedTemplateValue = html_entity_decode( $convertedTemplateValue );
                        $templateValues[$match] = $APPLICATION->ConvertCharset( $convertedTemplateValue, "utf8", "cp1251" );
                    }    
                    else{
                        $templateValues[$match] = html_entity_decode( $templateValues[$match] );
                    }
                }
            }
            
            if( $this->profile["XMLDATA"][str_replace( "#", "", $match )]["HTML_ENCODE"] == "Y" ){
                if( !empty( $templateValues[$match] ) ){
                    $templateValues[$match] = htmlspecialcharsbx( $templateValues[$match] );
                }
            }
            
            if( $this->profile["XMLDATA"][str_replace( "#", "", $match )]["URL_ENCODE"] == "Y" ){
                $templateValues[$match] = str_replace( array( " " ), array( "%20" ), $templateValues[$match] );
            }

            if( $this->profile["XMLDATA"][str_replace( "#", "", $match )]["CONVERT_CASE"] == "Y" ){
                if( is_array( $templateValues[$match] ) ){
                    foreach( $templateValues[$match] as &$val ){
                        $val = explode( ".", $val );
                        foreach( $val as &$tmpStr ){
                            $tmpStr = strtolower( trim( $tmpStr ) );
                            $strWords = explode( " ", $tmpStr );
                            if( ( strlen( $strWords[0] ) > 0 ) && ( count( $strWords ) > 1 ) )
                                $strWords[0] = mb_convert_case( $strWords[0], MB_CASE_TITLE );
                            $tmpStr = implode( " ", $strWords );
                        }
                        $val = implode( ". ", $val );
                    }
                }
                else{
                    $arTmp = explode( ".", $templateValues[$match] );
                    
                    foreach( $arTmp as &$tmpStr ){
                        $tmpStr = ToLower( trim( $tmpStr ) );
                        
                        $strWords = explode( " ", $tmpStr );
                        
                        if( ( strlen( $strWords[0] ) > 0 ) && ( count( $strWords ) > 1 ) ){
                            if( $templateValueCharset == "cp1251" ){
                                $strWords[0] = mb_convert_case( $strWords[0], MB_CASE_TITLE, "WINDOWS-1251" );
                            }
                            else{
                                $strWords[0] = mb_convert_case( $strWords[0], MB_CASE_TITLE );
                            }
                        }
                        
                        $tmpStr = implode( " ", $strWords );
                    }
                    $templateValues[$match] = implode( ". ", $arTmp );
                }
            }
            
            if( intval( $this->profile["XMLDATA"][str_replace( "#", "", $match )]["TEXT_LIMIT"] ) > 0 ){
                $templateValues[$match] = TruncateText( $templateValues[$match], $this->profile["XMLDATA"][str_replace( "#", "", $match )]["TEXT_LIMIT"] );
            }            
            
            if( ( $templateValues[$match] == "" ) && $this->profile["XMLDATA"][str_replace( "#", "", $match )]["DELETE_ONEMPTY"] == "Y" ){        
                $itemTemplate = str_replace( $this->arMatches[0][$id], "", $itemTemplate );
            }
            elseif( is_array( $templateValues[$match] ) ){ 
                $replacementValue = array();
                for( $i = 0; $i < count( $templateValues[$match] ); $i++ ){
                    $newName = preg_replace( "/\#((.)+)\#/", "#$1_LISTVAL_ITEM_$i#", $this->arMatches[2][$id] );
                    $replacementValue[] = str_replace( $this->arMatches[2][$id], $newName, $this->arMatches[0][$id] );
                    $templateValues[$newName] = $templateValues[$match][$i];
                }
                $itemTemplate = str_replace( $this->arMatches[0][$id], implode( PHP_EOL, $replacementValue ), $itemTemplate );
            }
        }                           
                                    
        $itemTemplate = preg_replace( '#(<(.+)>\s*</\2>)#i', "", $itemTemplate );  
        
        if( 
            ( ( strlen( trim( $this->profile["XMLDATA"]["LOCAL_DELIVERY_COST"]["VALUE"] ) ) <= 0 ) 
                && ( strlen( trim( $this->profile["XMLDATA"]["LOCAL_DELIVERY_COST"]["COMPLEX_TRUE_VALUE"] ) ) <= 0 ) 
                && ( strlen( trim( $this->profile["XMLDATA"]["LOCAL_DELIVERY_COST"]["CONTVALUE_TRUE"] ) ) <= 0 )
                && ( strlen( trim( $this->profile["XMLDATA"]["LOCAL_DELIVERY_COST"]["COMPLEX_TRUE_CONTVALUE"] ) ) <= 0 ) )
            || ( ( strlen( trim( $this->profile["XMLDATA"]["LOCAL_DELIVERY_DAYS"]["VALUE"] ) ) <= 0 ) 
                && ( strlen( trim( $this->profile["XMLDATA"]["LOCAL_DELIVERY_DAYS"]["COMPLEX_TRUE_VALUE"] ) ) <= 0 )
                && ( strlen( trim( $this->profile["XMLDATA"]["LOCAL_DELIVERY_DAYS"]["CONTVALUE_TRUE"] ) ) <= 0 )
                && ( strlen( trim( $this->profile["XMLDATA"]["LOCAL_DELIVERY_DAYS"]["COMPLEX_TRUE_CONTVALUE"] ) ) <= 0 ) )
        ){
            $itemTemplate = preg_replace( '#<delivery-options>.*</delivery-options>#is', "", $itemTemplate ); 
        }                                       
        
        //for some realty
        if( isset( $templateValues["#PRICE_VALUE#"] ) ){
            $templateValues["#PRICE_VALUE#"] = intval( $templateValues["#PRICE_VALUE#"] );
        }
        
        if( isset( $templateValues["#OBJECT_IMAGE#"] ) ){
            if( !file_exists( $templateValues["#OBJECT_IMAGE#"] ) ){
                $templateValues["#OBJECT_IMAGE#"] = $this->defaultFields["#SITE_URL#"].$templateValues["#OBJECT_IMAGE#"];
            }
        }
                                                                                                                            
        // Set values
        $itemTemplate = str_replace( array_keys( $this->defaultFields ), array_values( $this->defaultFields ), $itemTemplate );
        $itemTemplate = str_replace( array_keys( $templateValues ), array_values( $templateValues ), $itemTemplate );
                 
        // Removes empty first level tags, if there is no nesting
        //$itemTemplate = preg_replace( "/(<[0-9a-zA-Z:_]+>[\r\n\t]*<\/[0-9a-zA-Z:_]+>)/", "\r\n", $itemTemplate );
        $itemTemplate = preg_replace( "/(\r\n[\t]*\r\n)/", "\r\n", $itemTemplate );
        $itemTemplate = preg_replace( "/(\r\n\r\n)/", "\r\n", $itemTemplate );
        
        $itemTemplate = preg_replace( "/\s\w+=\"\"/", "", $itemTemplate );
        $itemTemplate = preg_replace( '#(<[^\s]+/>)#i', "", $itemTemplate );  
        $itemTemplate = preg_replace( '#(<(.+)>\s*</\2>)#i', "", $itemTemplate );  
        
        if( !empty( $this->profile["CONVERT_DATA"] ) ){
            foreach( $this->profile["CONVERT_DATA"] as $arConvertBlock ){
                $itemTemplate = str_replace( $arConvertBlock[0], $arConvertBlock[1], $itemTemplate );
            }
        }
                                       
        if( !$skipElement ){
            $processElementId = ( intval( $arItem["ELEMENT_ID"] ) > 0 ) ? $arItem["ELEMENT_ID"] : $arItem["ID"];
            $dbElementGroups = CIBlockElement::GetElementGroups( $processElementId, true );
            $arItemSections = array();
            while( $arElementGroups = $dbElementGroups->Fetch() ){
                $arItemSections[] = $arElementGroups["ID"];
            }
            
            $this->SaveSections( $arItemSections );
            //$this->SaveSections( array( $arItem["IBLOCK_SECTION_ID"] ) );
            $this->DemoCountInc();


            if( $this->catalogIncluded && !$this->isVariant( $arItem["IBLOCK_SECTION_ID"] ) ){
                //if( $this->profile["USE_VARIANT"] == "Y" && $this->profile["TYPE"] == "activizm" ){
                //    $gender = $this->profile["VARIANT"]["SEX_CONST"] ? $this->profile["VARIANT"]["SEX_CONST"] : $arItem[$this->variantProperties["SEX"]];
                //    $arSize = explode( "-", $this->profile["VARIANT"]["CATEGORY"][$arItem["IBLOCK_SECTION_ID"]] );
                //
                //    $retVariant = "";
                //    if( !empty( $arItem[$this->variantProperties["COLOR"]] ) )
                //        $retVariant = "<color>{$arItem[$this->variantProperties["COLOR"]]}</color>".PHP_EOL;
                //
                //    if( $arSize[1] == "OZ" ){
                //        $retVariant .= "<size gender=\"{$gender}\" system=\"{$arSize[1]}\">
                //        {$arItem[$this->variantProperties["WEIGHT"]]}
                //        </size>".PHP_EOL;
                //    }
                //    else{
                //        $retVariant .= "<size gender=\"{$gender}\" system=\"{$arSize[1]}\">
                //        {$arItem[$this->variantProperties["SIZE"]]}
                //        </size>".PHP_EOL;
                //    }
                //    $itemTemplate = str_replace( "</offer>", "$retVariant</offer>", $itemTemplate );
                //}                                       
                
                CAcritExportproExport::Save( $itemTemplate );
                
                // Увеличиваем счетчик статистики выгружженных товаров
                $this->log->IncProductExport();
            }
            else{
                CAcritExportproExport::Save( $itemTemplate );
                
                // Увеличиваем счетчик статистики выгружженных товаров
                $this->log->IncProductExport();
            }
        }
        unset( $arElement, $dbPrices, $arQuantity );
        if( $this->isVariant( $arItem["IBLOCK_SECTION_ID"] ) )
            return array( "ITEM" => $arItem, "XML" => $itemTemplate, "SKIP" => $skipElement, "OFFER" => is_array( $arProductSKU ) );
        return $arItem;
    } 
    
    public function GetYandexDateTime( $dateTime ){
        global $DB;
        $resultTime = false;
        
        $localTime = new DateTime();
        $dateTimeZoneDiff = $localTime->getOffset() / 3600;
        
        $dateTimeZone = ( ( intval( $dateTimeZoneDiff ) > 0 ) ? "+" : "-" ).date( "H:i", mktime( $dateTimeZoneDiff, 0, 0, 0, 0, 0 ) );
        
        $dateTimeValue = MakeTimeStamp( $dateTime );
        $dateTimeFormattedValue = date( "Y-m-d", $dateTimeValue )."T".date( "H:i:s", $dateTimeValue );
        
        $resultTime = $dateTimeFormattedValue.$dateTimeZone;
        
        return $resultTime;
    }     

    // Поиск инфоблоков товарных предложений и удаление их из списка инфоблоков,
    // если они включены и есть каталог родитель товарных предложений
    private function PrepareIBlock(){
        $excludeIBlock = array();
        $this->catalogSKU = array();
        $ibv = $this->iblockE;
        $ibd = $this->iblockD;
        
        //likeit.pro hello!!!
        //$ib = @$ibv(@$ibd(GetMessage("ACRIT_EXPORTPRO_IBLOCK")));
        //if($ib !== false)
        //    return array();
            
        if( ( $this->profile["USE_SKU"] == "Y" ) || ( $this->profile["TYPE"] == "advantshop" ) ){
            foreach( $this->profile["IBLOCK_ID"] as $iblocID ){
                if( $this->catalogIncluded ){
                    if( $arIBlock = CCatalog::GetByID( $iblocID ) ){
                        if( intval( $arIBlock["PRODUCT_IBLOCK_ID"] ) > 0 && in_array( $arIBlock["PRODUCT_IBLOCK_ID"], $this->profile["IBLOCK_ID"] ) )
                            $excludeIBlock[] = $arIBlock["IBLOCK_ID"];
                        if( intval( $arIBlock["OFFERS_IBLOCK_ID"] ) > 0 )
                            $this->catalogSKU[$arIBlock["IBLOCK_ID"]] = $arIBlock;
                    }
                }
            }
        }
        return array_diff( $this->profile["IBLOCK_ID"], $excludeIBlock );
    }
    
    private function GetIblockUserFields( $iblockId ){
        $result = false;
        $dbSectionUserFields = CUserTypeEntity::GetList(
            array(),
            array(
                "ENTITY_ID" => "IBLOCK_".$iblockId."_SECTION",
                "LANG" => LANGUAGE_ID
            )
        );
        
        while( $arSectionUserFields = $dbSectionUserFields->Fetch() ){
            if( !$result ) $result = array();
            $result[] = $arSectionUserFields;
        }
        
        return $result;
    }
    
        
    // Получение полей и свойств элемента, используемых в шаблоне и условиях
    private function GetElementProperties( $arElement ){
        global $DB;         
                                  
        $arItem = $arElement->GetFields();
        if( in_array( "DETAIL_PICTURE", $this->useFields ) ){
            $arItem["DETAIL_PICTURE"] = CFile::GetPath($arItem["DETAIL_PICTURE"]);
        }
        if( in_array("PREVIEW_PICTURE", $this->useFields ) ){
            $arItem["PREVIEW_PICTURE"] = CFile::GetPath( $arItem["PREVIEW_PICTURE"] );
        }
                             
        foreach( $arItem as $key => &$value ){
            if( in_array( $key, $this->dateFields ) ){  
                $value = date( str_replace( "_", " ", $this->profile["DATEFORMAT"] ), strtotime( $value ) );
            }
        }

        $arItem["SECTION_ID"] = array();
        $arItem["IBLOCK_SECTION_NAME"] = array();
        
        $dbSomeSections = CIBlockElement::GetElementGroups( $arItem["ID"], true );
        while( $arSection = $dbSomeSections->Fetch() ){
            if( in_array( "IBLOCK_SECTION_NAME", $this->useFields ) ){
                $arItem["IBLOCK_SECTION_NAME"] = $arSection["NAME"];
            }
            $arItem["SECTION_ID"][] = $arSection["ID"];
        }
        
        $arItem["SECTION_PARENT_ID"] = array();
        $dbParentSection = CIBlockSection::GetNavChain( false, $arItem["IBLOCK_SECTION_ID"] );
        while( $arParentSection = $dbParentSection->GetNext() ){
            $arItem["SECTION_PARENT_ID"][] = $arParentSection["ID"];
        }
        
        $arSectionFilter = array(
            "IBLOCK_ID" => $arItem["IBLOCK_ID"],
            "ID" => $arItem["IBLOCK_SECTION_ID"],
        );
        
        $dbSectionList = CIBlockSection::GetList(
            array(),
            $arSectionFilter,
            false,
            array(
                "ID",
                "IBLOCK_ID",
                "IBLOCK_SECTION_ID",
                "NAME",
                "UF_*",
            )
        );
        
        $arSectionUserFields = self::GetIblockUserFields( $arItem["IBLOCK_ID"] );
        if( $arSectionList = $dbSectionList->GetNext() ){
            foreach( $arSectionUserFields as $arSectionUserFieldsItem ){
                if( in_array( $arSectionUserFieldsItem["FIELD_NAME"], $this->useFields ) ){
                    $arItem[$arSectionUserFieldsItem["FIELD_NAME"]] = $arSectionList[$arSectionUserFieldsItem["FIELD_NAME"]];
                }
            }
        }
        
        if( count( $this->useProperties["ID"] ) ){
            $arProperties = $this->GetProperties( $arItem, array( "ID" => $this->useProperties["ID"] ) );
            foreach( $this->useProperties["ID"] as $usePropID )
                if( !isset( $arProperties[$usePropID] ) ){
                    $arItem["PROPERTY_{$usePropID}_VALUE"] = array();
                }
            
            foreach( $arProperties as $property ){              
                //if($DB->DateFormatToPHP(FORMAT_DATETIME))
                if( $property["USER_TYPE"] == "DateTime" ){
                    $property["DISPLAY_VALUE"] = date( str_replace( "_", " ", $this->profile["DATEFORMAT"] ), strtotime( $property["VALUE"] ) );
                }
                elseif( $property["PROPERTY_TYPE"] == "E" ){
                    $property["ORIGINAL_VALUE"] = array();
                    if( !empty( $property["VALUE"] ) ){
                        $dbPropE = CIBlockElement::GetList(
                            array(),
                            array(
                                "ID" => $property["VALUE"]
                            ),
                            false,
                            false,
                            array( "ID", "NAME" )
                        );
                        while( $arPropE = $dbPropE->GetNext() ){
                            $property["DISPLAY_VALUE"][] = $arPropE["NAME"];
                            $property["ORIGINAL_VALUE"][] = $arPropE["ID"];
                        }
                    }
                }
                else{                                                               
                    $property = CIBlockFormatProperties::GetDisplayValue( $arItem, $property, "acrit_exportpro_event" );
                    if( empty( $property["VALUE_ENUM_ID"] ) ){
                        if( !is_array( $property["DISPLAY_VALUE"] ) )
                            $property["ORIGINAL_VALUE"] = array( $property["DISPLAY_VALUE"] );
                        else
                            $property["ORIGINAL_VALUE"] = $property["DISPLAY_VALUE"];
                    }
                    else{
                        if( !is_array( $property["VALUE_ENUM_ID"] ) )
                            $property["ORIGINAL_VALUE"] = array( $property["VALUE_ENUM_ID"] );
                        else
                            $property["ORIGINAL_VALUE"] = $property["VALUE_ENUM_ID"];
                    }
                }
                if( $property["PROPERTY_TYPE"] == "F" ){
                    $property["DISPLAY_VALUE"] = array();
                    if( count( $property["ORIGINAL_VALUE"] ) > 1 ){
                        foreach( $property["FILE_VALUE"] as $file )
                            $property["DISPLAY_VALUE"][] = $file["SRC"];
                    }
                    else{
                        $property["DISPLAY_VALUE"] = $property["FILE_VALUE"]["SRC"];
                    }                  
                }
                $arItem["PROPERTY_{$property["ID"]}_DISPLAY_VALUE"] = $property["DISPLAY_VALUE"];
                $arItem["PROPERTY_{$property["CODE"]}_DISPLAY_VALUE"] = $arItem["PROPERTY_{$property["ID"]}_VALUE"];
                $arItem["PROPERTY_{$property["ID"]}_VALUE"] = $property["ORIGINAL_VALUE"];
                $arItem["PROPERTY_{$property["CODE"]}_VALUE"] = $arItem["PROPERTY_{$property["ID"]}_VALUE"];
            }                    
        }         
        if( $this->catalogIncluded ){         
            $arProduct = CCatalogProduct::GetByID( $arItem["ID"] );                                               
            if( in_array( "PURCHASING_PRICE", $this->usePrices ) && ( $this->profile["TYPE"] != "advantshop" ) ){
                $arItem["CATALOG_PURCHASING_PRICE"] = intval( round( $arProduct["PURCHASING_PRICE"] ) );
                
                $dbPrices = CPrice::GetList(
                    array(),
                    array(
                        "PRODUCT_ID" => $arItem["ID"]
                    )
                );
                
                if( $arPrice = $dbPrices->fetch() ){                             
                    $arItem["CATALOG_PURCHASING_PRICE_CURRENCY"] = $arPrice["CURRENCY"];
                    $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_CURRENCY"] = $arPrice["CURRENCY"];
                }
                
                //$arItem["CATALOG_PURCHASING_PRICE_CURRENCY"] = $arProduct["PURCHASING_CURRENCY"];
            }
            else{
                $dbPrices = CPrice::GetList(
                    array(),
                    array(
                        "PRODUCT_ID" => $arItem["ID"]
                    )
                );
                
                while( $arPrice = $dbPrices->fetch() ){                             
                    if( in_array("PRICE_".$arPrice["CATALOG_GROUP_ID"]."_WD", $this->usePrices ) ||
                        in_array( "PRICE_".$arPrice["CATALOG_GROUP_ID"]."_D", $this->usePrices ) ){
                        $arDiscounts = CCatalogDiscount::GetDiscountByPrice($arPrice["ID"], array(2), "N", SITE_ID);
                        $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                            $arPrice["PRICE"],
                            $arPrice["CURRENCY"],
                            $arDiscounts
                        );
                        $discount = $arPrice["PRICE"] - $discountPrice;
                    }
                    else{
                        $discountPrice = $arPrice["PRICE"];
                        $discount = 0;
                    }
                    if( in_array( "PURCHASING_PRICE", $this->usePrices ) ){
                        //$arItem["CATALOG_PURCHASING_PRICE"] = intval( round( $arProduct["PURCHASING_PRICE"] ) );
                        $arItem["CATALOG_PURCHASING_PRICE"] = $arProduct["PURCHASING_PRICE"];
                    }

                    //$arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}"] = intval( round( $arPrice["PRICE"] ) );
                    //$arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_WD"] = intval( round( $discountPrice ) );
                    //$arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_D"] = intval( round( $discount ) );
                    //$arItem["CATALOG_PRICE{$arPrice["CATALOG_GROUP_ID"]}"] = intval( round( $arPrice["PRICE"] ) );
                    //$arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_CURRENCY"] = $arPrice["CURRENCY"];
                    
                    $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}"] = $arPrice["PRICE"];
                    $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_WD"] = $discountPrice;
                    $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_D"] = $discount;
                    $arItem["CATALOG_PRICE{$arPrice["CATALOG_GROUP_ID"]}"] = $arPrice["PRICE"];
                    $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_CURRENCY"] = $arPrice["CURRENCY"];
                }
            }   
            
            $arItem["CATALOG_QUANTITY"] = $arProduct["QUANTITY"];
            $arItem["CATALOG_QUANTITY_RESERVED"] = $arProduct["QUANTITY_RESERVED"];
            $arItem["CATALOG_WEIGHT"] = $arProduct["WEIGHT"];
            $arItem["CATALOG_WIDTH"] = $arProduct["WIDTH"];
            $arItem["CATALOG_LENGTH"] = $arProduct["LENGTH"];
            $arItem["CATALOG_HEIGHT"] = $arProduct["HEIGHT"];
        }

        unset( $arProperties, $arProduct, $dbPrices, $arPrice );
        return $arItem;
    }
    
    private function SaveSections( $sections ){                             
        if( is_array( $sections ) ){
            $sessionData = AcritExportproSession::GetSession( $this->profile["ID"] );
            
            if( !is_array( $sessionData["EXPORTPRO"][$this->profile["ID"]]["CATEGORY"] ) )
                $sessionData["EXPORTPRO"][$this->profile["ID"]]["CATEGORY"] = array();
            
            $sessionData["EXPORTPRO"][$this->profile["ID"]]["CATEGORY"] = array_merge(
                $sessionData["EXPORTPRO"][$this->profile["ID"]]["CATEGORY"],
                $sections
            );
            AcritExportproSession::SetSession( $this->profile["ID"], $sessionData );
        }
    }

    private function SaveCurrencies( $currencies ){
        if( is_array( $currencies ) ){
            $sessionData = AcritExportproSession::GetSession( $this->profile["ID"] );
            if( !is_array( $sessionData["EXPORTPRO"][$this->profile["ID"]]["CURRENCY"] ) )
                $sessionData["EXPORTPRO"][$this->profile["ID"]]["CURRENCY"] = array();
            $sessionData["EXPORTPRO"][$this->profile["ID"]]["CURRENCY"] = array_merge(
                $sessionData["EXPORTPRO"][$this->profile["ID"]]["CURRENCY"],
                $currencies
            );
            AcritExportproSession::SetSession( $this->profile["ID"], $sessionData );
        }
    }

    private function CheckCondition( $arItem, $code ){
        unset( $GLOBALS["CHECK_COND"] );
        $GLOBALS["CHECK_COND"] = $arItem;
        return eval( "return $code;" );
    }

    public function GetProperties( $arItem, $arFilter ){
        $props = CIBlockElement::GetProperty( $arItem["IBLOCK_ID"], $arItem["ID"], array(), $arFilter );

        $arAllProps = Array();
        while( $arProp = $props->Fetch() ){
            if( strlen( trim( $arProp["CODE"] ) ) > 0 )
                $PIND = $arProp["CODE"];
            else
                $PIND = $arProp["ID"];

            $arProp["ORIGINAL_VALUE"] = $arProp["VALUE"];

            if( $arProp["PROPERTY_TYPE"] == "L" ){
                if( $arProp["MULTIPLE"] != "Y" )
                    $arProp["ORIGINAL_VALUE"] = array( $arProp["ORIGINAL_VALUE"] );
                $arProp["VALUE_ENUM_ID"] = $arProp["VALUE"];
                $arProp["VALUE"] = $arProp["VALUE_ENUM"];
            }

            if( is_array( $arProp["VALUE"] ) || ( strlen( $arProp["VALUE"] ) > 0 ) ){
                $arProp["~VALUE"] = $arProp["VALUE"];
                if( is_array( $arProp["VALUE"] ) || preg_match( "/[;&<>\"]/", $arProp["VALUE"] ) )
                    $arProp["VALUE"] = htmlspecialcharsex( $arProp["VALUE"] );
                $arProp["~DESCRIPTION"] = $arProp["DESCRIPTION"];
                if( preg_match("/[;&<>\"]/", $arProp["DESCRIPTION"] ) )
                    $arProp["DESCRIPTION"] = htmlspecialcharsex( $arProp["DESCRIPTION"] );
            }
            else{
                $arProp["VALUE"] = $arProp["~VALUE"] = "";
                $arProp["DESCRIPTION"] = $arProp["~DESCRIPTION"] = "";
            }

            if( $arProp["MULTIPLE"] == "Y" ){
                if( array_key_exists( $PIND, $arAllProps ) ){
                    $arTemp = &$arAllProps[$PIND];
                    if( $arProp["VALUE"] !== "" ){
                        if( is_array( $arTemp["VALUE"] ) ){
                            $arTemp["ORIGINAL_VALUE"][] = $arProp["ORIGINAL_VALUE"];
                            $arTemp["VALUE"][] = $arProp["VALUE"];
                            $arTemp["~VALUE"][] = $arProp["~VALUE"];
                            $arTemp["DESCRIPTION"][] = $arProp["DESCRIPTION"];
                            $arTemp["~DESCRIPTION"][] = $arProp["~DESCRIPTION"];
                            $arTemp["PROPERTY_VALUE_ID"][] = $arProp["PROPERTY_VALUE_ID"];
                            if( $arProp["PROPERTY_TYPE"] == "L" ){
                                $arTemp["VALUE_ENUM_ID"][] = $arProp["VALUE_ENUM_ID"];
                                $arTemp["VALUE_ENUM"][] = $arProp["VALUE_ENUM"];
                                $arTemp["VALUE_XML_ID"][] = $arProp["VALUE_XML_ID"];
                                //$arTemp["VALUE_SORT"][] = $arProp["VALUE_SORT"];
                            }
                        }
                        else{
                            $arTemp["ORIGINAL_VALUE"] = array( $arProp["ORIGINAL_VALUE"] );
                            $arTemp["VALUE"] = array( $arProp["VALUE"] );
                            $arTemp["~VALUE"] = array( $arProp["~VALUE"] );
                            $arTemp["DESCRIPTION"] = array( $arProp["DESCRIPTION"] );
                            $arTemp["~DESCRIPTION"] = array( $arProp["~DESCRIPTION"] );
                            $arTemp["PROPERTY_VALUE_ID"] = array( $arProp["PROPERTY_VALUE_ID"] );
                            if( $arProp["PROPERTY_TYPE"] == "L" ){
                                $arTemp["VALUE_ENUM_ID"] = array( $arProp["VALUE_ENUM_ID"] );
                                $arTemp["VALUE_ENUM"] = array( $arProp["VALUE_ENUM"] );
                                $arTemp["VALUE_XML_ID"] = array( $arProp["VALUE_XML_ID"] );
                                $arTemp["VALUE_SORT"] = array( $arProp["VALUE_SORT"] );
                                $arTemp["ORIGINAL_VALUE"] = array( $arProp["ORIGINAL_VALUE"] );
                            }
                        }
                    }
                }
                else{
                    $arProp["~NAME"] = $arProp["NAME"];
                    if( preg_match( "/[;&<>\"]/", $arProp["NAME"] ) )
                        $arProp["NAME"] = htmlspecialcharsex( $arProp["NAME"] );
                    $arProp["~DEFAULT_VALUE"] = $arProp["DEFAULT_VALUE"];
                    if( is_array( $arProp["DEFAULT_VALUE"] ) || preg_match( "/[;&<>\"]/", $arProp["DEFAULT_VALUE"] ) )
                        $arProp["DEFAULT_VALUE"] = htmlspecialcharsex( $arProp["DEFAULT_VALUE"] );
                    if( $arProp["VALUE"] !== "" ){
                        $arProp["ORIGINAL_VALUE"] = array( $arProp["ORIGINAL_VALUE"] );
                        $arProp["VALUE"] = array( $arProp["VALUE"] );
                        $arProp["~VALUE"] = array( $arProp["~VALUE"] );
                        $arProp["DESCRIPTION"] = array( $arProp["DESCRIPTION"] );
                        $arProp["~DESCRIPTION"] = array( $arProp["~DESCRIPTION"] );
                        $arProp["PROPERTY_VALUE_ID"] = array( $arProp["PROPERTY_VALUE_ID"] );
                        if( $arProp["PROPERTY_TYPE"] == "L" ){
                            $arProp["VALUE_ENUM_ID"] = array( $arProp["VALUE_ENUM_ID"] );
                            $arProp["VALUE_ENUM"] = array( $arProp["VALUE_ENUM"] );
                            $arProp["VALUE_XML_ID"] = array( $arProp["VALUE_XML_ID"] );
                            $arProp["VALUE_SORT"] = array( $arProp["VALUE_SORT"] );
                        }
                    }
                    else{
                        $arProp["ORIGINAL_VALUE"] = false;
                        $arProp["VALUE"] = false;
                        $arProp["~VALUE"] = false;
                        $arProp["DESCRIPTION"] = false;
                        $arProp["~DESCRIPTION"] = false;
                        $arProp["PROPERTY_VALUE_ID"] = false;
                        if( $arProp["PROPERTY_TYPE"] == "L" ){
                            $arProp["VALUE_ENUM_ID"] = false;
                            $arProp["VALUE_ENUM"] = false;
                            $arProp["VALUE_XML_ID"] = false;
                            $arProp["VALUE_SORT"] = false;
                        }
                    }
                    $arAllProps[$PIND] = $arProp;
                }
            }
            else{
                $arProp["~NAME"] = $arProp["NAME"];
                if( preg_match( "/[;&<>\"]/", $arProp["NAME"] ) )
                    $arProp["NAME"] = htmlspecialcharsex( $arProp["NAME"] );
                $arProp["~DEFAULT_VALUE"] = $arProp["DEFAULT_VALUE"];
                if( is_array( $arProp["DEFAULT_VALUE"] ) || preg_match( "/[;&<>\"]/", $arProp["DEFAULT_VALUE"] ) )
                    $arProp["DEFAULT_VALUE"] = htmlspecialcharsex( $arProp["DEFAULT_VALUE"] );
                $arAllProps[$PIND] = $arProp;
            }
        }
        return $arAllProps;
    }
    
    private function GetStringCharset( $str ){ 
        $resEncoding = "cp1251";
        
        if( preg_match( "#.#u", $str ) ){
            $resEncoding = "utf8";
        }
        
        return $resEncoding;
    }
    
    public static function AcritDump( $dumpData, $clear = FALSE, $depth = 0 ){
        $fileName = "acrit_exportpro_dump.txt";
        $file = $_SERVER["DOCUMENT_ROOT"] . "/upload/" . $fileName;

        $depthSign = "----";

        $strResult = "";
        $strDepth  = "";
        $nextDepth = $depth + 1;

        if( isset( $dumpData )
            && filter_var( $depth ) !== FALSE
            && $depth >= 0
            && is_bool( $clear ) ){

            if( $depth == 0
                && $clear ){

                file_put_contents( $file, "" );
            }
            else{
                for( $ico = 0; $ico < (int) $depth; $ico += 1 ){
                    $strDepth .= $depthSign;
                }
                $strDepth .= " ";
            }

            if( is_array( $dumpData ) ){
                foreach( $dumpData as $key => $value ){
                    if( is_array( $value ) ){
                        $strResult .= $strDepth . $key . " = Array:\n";
                        file_put_contents( $file, $strResult, FILE_APPEND );
                        $strResult = "";

                        self::AcritDump( $value, $clear, $nextDepth );
                    }
                    elseif( is_null( $value ) ){
                        $strResult .= $strDepth . $key . " = *NULL*\n";
                    }
                    elseif( $value === FALSE ){
                        $strResult .= $strDepth . $key . " = *FALSE*\n";
                    }
                    elseif( is_string( $value )
                        && strlen( $value ) <= 0 ){

                        $strResult .= $strDepth . $key . " = *EMPTY STRING*\n";
                    }
                    else{
                        $strResult .= $strDepth . $key . " = " . $value . "\n";
                    }
                }
            }
            elseif( is_null( $dumpData ) ){
                $strResult = "*NULL*\n";
            }
            elseif( $dumpData === FALSE ){
                $strResult = "*FALSE*\n";
            }
            elseif( is_string( $dumpData )
                && strlen( $dumpData ) <= 0 ){

                $strResult = "*EMPTY STRING*\n";
            }    
            else{
                $strResult = $dumpData . "\n";
            }
        }

        if( $depth === 0 ){
            $strResult .= "____________________________________________________\n\n";
        }

        if( strlen( $strResult ) > 0 ){
            file_put_contents( $file, $strResult, FILE_APPEND );
        }
    }
}

class CAcritExportproLog{
    public $logFilename = "/upload/acrit.exportpro/";
    private $profileId;
    private $session;
    
    private $profileEncoding = array(
        "utf8" => "utf-8",
        "cp1251" => "windows-1251",
    );

    public function __construct( $profileId = false ){
        $this->profileId = $profileId;
        //$this->session = new ThreadsSession;
        //$this->session->Init( "acrit_exportpro_".$profileId, \Bitrix\Main\Application::getDocumentRoot() );
    }
    
    public static function AcritDump( $dumpData, $clear = FALSE, $depth = 0 ){
        $fileName = "acrit_exportpro_dump.txt";
        $file = $_SERVER["DOCUMENT_ROOT"] . "/upload/" . $fileName;

        $depthSign = "----";

        $strResult = "";
        $strDepth  = "";
        $nextDepth = $depth + 1;

        if( isset( $dumpData )
            && filter_var( $depth ) !== FALSE
            && $depth >= 0
            && is_bool( $clear ) ){

            if( $depth == 0
                && $clear ){

                file_put_contents( $file, "" );
            }
            else{
                for( $ico = 0; $ico < (int) $depth; $ico += 1 ){
                    $strDepth .= $depthSign;
                }
                $strDepth .= " ";
            }

            if( is_array( $dumpData ) ){
                foreach( $dumpData as $key => $value ){
                    if( is_array( $value ) ){
                        $strResult .= $strDepth . $key . " = Array:\n";
                        file_put_contents( $file, $strResult, FILE_APPEND );
                        $strResult = "";

                        self::AcritDump( $value, $clear, $nextDepth );
                    }
                    elseif( is_null( $value ) ){
                        $strResult .= $strDepth . $key . " = *NULL*\n";
                    }
                    elseif( $value === FALSE ){
                        $strResult .= $strDepth . $key . " = *FALSE*\n";
                    }
                    elseif( is_string( $value )
                        && strlen( $value ) <= 0 ){

                        $strResult .= $strDepth . $key . " = *EMPTY STRING*\n";
                    }
                    else{
                        $strResult .= $strDepth . $key . " = " . $value . "\n";
                    }
                }
            }
            elseif( is_null( $dumpData ) ){
                $strResult = "*NULL*\n";
            }
            elseif( $dumpData === FALSE ){
                $strResult = "*FALSE*\n";
            }
            elseif( is_string( $dumpData )
                && strlen( $dumpData ) <= 0 ){

                $strResult = "*EMPTY STRING*\n";
            }
            else{
                $strResult = $dumpData . "\n";
            }
        }

        if( $depth === 0 ){
            $strResult .= "____________________________________________________\n\n";
        }

        if( strlen( $strResult ) > 0 ){
            file_put_contents( $file, $strResult, FILE_APPEND );
        }
    }

    public function Init( $profile ){
        $sessionData = AcritExportproSession::GetSession( $profile["ID"] );

        $sessionData["EXPORTPRO"]["LOG"][$profile["ID"]] = array(
            "IBLOCK" => 0,
            "SECTIONS" => 0,
            "PRODUCTS" => 0,
            "PRODUCTS_EXPORT" => 0,
            "PRODUCTS_ERROR" => 0,
            "FILE" => "",
            "LAST_START_EXPORT" => date( "d.m H:i:s", time() )
        );
        $profileObj = new CExportproProfile();
        
        if( CModule::IncludeModule( "catalog" ) ){
            $sessionData["EXPORTPRO"]["LOG"][$profile["ID"]]["IBLOCK"] = count( $profileObj->PrepareIBlock( $profile["IBLOCK_ID"], $profile["USE_SKU"] ) );
        }
        else{
            $sessionData["EXPORTPRO"]["LOG"][$profile["ID"]]["IBLOCK"] = count( $profileObj->PrepareIBlock( $profile["IBLOCK_ID"], false ) );
        }

        if( $profile["CHECK_INCLUDE"] != "Y" ){
            $sections = array();
            $dbSection = CIBlockSection::GetList(
                array(),
                array(
                    "ID" => $profile["CATEGORY"]
                ),
                false,
                array(
                    "ID",
                    "LEFT_MARGIN",
                    "RIGHT_MARGIN"
                )
            );
            
            $arFilter = array( "LOGIC" => "OR" );
            
            while( $arSection = $dbSection->GetNext() ){
                $arFilter = array(
                    ">LEFT_MARGIN" => $arSection["LEFT_MARGIN"],
                    "<RIGHT_MARGIN" => $arSection["RIGHT_MARGIN"],
                    "IBLOCK_ID" => $profile["IBLOCK_ID"]
                );
                
                $dbSection2 = CIBlockSection::GetList(
                    array(),
                    $arFilter,
                    false,
                    array( "ID" )
                );
                
                while( $arSection2 = $dbSection2->GetNext() )
                    $sections[] = $arSection2["ID"];
            }
            $sections = array_unique( $sections );
            $sessionData["EXPORTPRO"]["LOG"][$profile["ID"]]["SECTIONS"] = count( $sections );
        }
        else{
            $sessionData["EXPORTPRO"]["LOG"][$profile["ID"]]["SECTIONS"] = count($profile["CATEGORY"]);
        }
        $this->profileId = $profile["ID"];
        CheckDirPath( $_SERVER["DOCUMENT_ROOT"].$this->logFilename );
        $this->logFilename = $this->logFilename."log_export_".$this->profileId.".txt";
        $sessionData["EXPORTPRO"]["LOG"][$profile["ID"]]["FILE"] = $this->logFilename;
        file_put_contents( $_SERVER["DOCUMENT_ROOT"].$this->logFilename, "" );
        //$this->session->Save( "LOG", $sessionData["EXPORTPRO"]["LOG"][$profile["ID"]] );
        AcritExportproSession::SetSession( $profile["ID"], $sessionData );
    }
    
    public function IncIblcok(){
        $sessionData = AcritExportproSession::GetSession( $this->profileId );
        $sessionData["EXPORTPRO"]["LOG"][$this->profileId]["IBLOCK"]++;
        AcritExportproSession::SetSession( $this->profileId, $sessionData );
    }
    
    public function IncSection(){
        $sessionData = AcritExportproSession::GetSession( $this->profileId );
        $sessionData["EXPORTPRO"]["LOG"][$this->profileId]["SECTIONS"]++;
        AcritExportproSession::SetSession( $this->profileId, $sessionData );
    }
    
    public function IncProduct( $cnt = 0 ){
        $sessionData = AcritExportproSession::GetSession( $this->profileId );
        if( !intval( $cnt ) )
            $sessionData["EXPORTPRO"]["LOG"][$this->profileId]["PRODUCTS"]++;
        else
            $sessionData["EXPORTPRO"]["LOG"][$this->profileId]["PRODUCTS"] += $cnt;
        
        AcritExportproSession::SetSession( $this->profileId, $sessionData );
    }
    
    public function IncProductExport(){
        $sessionData = AcritExportproSession::GetSession( $this->profileId );
        $sessionData["EXPORTPRO"]["LOG"][$this->profileId]["PRODUCTS_EXPORT"]++;
        AcritExportproSession::SetSession( $this->profileId, $sessionData );
    }
    
    public function IncProductError(){
        $sessionData = AcritExportproSession::GetSession( $this->profileId );
        $sessionData["EXPORTPRO"]["LOG"][$this->profileId]["PRODUCTS_ERROR"]++;
        AcritExportproSession::SetSession( $this->profileId, $sessionData );
    }
    
    public function AddMessage( $message ){
        $messageProfileId = $this->profileId;
        
        if( stripos( $this->logFilename, $messageProfileId ) === false ){
            $this->logFilename = $this->logFilename."log_export_".$messageProfileId.".txt";
        }
        
        if( is_file( $_SERVER["DOCUMENT_ROOT"].$this->logFilename ) ){
            file_put_contents( $_SERVER["DOCUMENT_ROOT"].$this->logFilename, $message, FILE_APPEND );    
        }
    }
    
    public function GetLog( $profileID, $bSendEmailReport = true ){
        $dbProfile = new CExportproProfileDB();
        $arProfile = $dbProfile->GetByID( $profileID );
        
        $arSessionData = AcritExportproSession::GetAllSession( $profileID );
        $sessionData = array();
        if( !empty( $arSessionData ) ){
            $sessionData = $arSessionData[0];
            $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS"] = intval( $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS"] );
            $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_EXPORT"] = intval( $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_EXPORT"] );
            $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_ERROR"] = intval( $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_ERROR"] );
            
            unset( $arSessionData[0] );
            
            foreach( $arSessionData as $sData ){
                $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS"] += intval( $sData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS"] );
                $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_EXPORT"] += intval( $sData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_EXPORT"] );
                $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_ERROR"] += intval( $sData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_ERROR"] );
            }
            
            $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_ERROR"] = $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS"] - $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_EXPORT"];
        }    
        
        if( $bSendEmailReport ){
            $dbProfile = new CExportproProfileDB();
            $arProfile = $dbProfile->GetByID( $profileID );
            
            if( check_email( $arProfile["SEND_LOG_EMAIL"] ) ){
                $messageTitle = GetMessage( "ACRIT_LOG_SEND_TITLE" ).$arProfile["DOMAIN_NAME"];
                $messageBlock = GetMessage( "ACRIT_LOG_SEND_OFFERS" ).$sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS"]."\n".
                GetMessage( "ACRIT_LOG_SEND_OFFERS_TERM" ).$sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_EXPORT"]."\n".
                GetMessage( "ACRIT_LOG_SEND_OFFERS_ERROR" ).$sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_ERROR"]."\n".
                GetMessage( "ACRIT_LOG_SEND_DATE" ).$sessionData["EXPORTPRO"]["LOG"][$profileID]["LAST_START_EXPORT"]."\n\n".
                GetMessage( "ACRIT_LOG_PROFILE" ).$arProfile["SITE_PROTOCOL"]."://".$arProfile["DOMAIN_NAME"]."/bitrix/admin/acrit_exportpro_edit.php?ID=".$profileID."\n".            
                GetMessage( "ACRIT_LOG_SEND_FILE" ).$arProfile["SITE_PROTOCOL"]."://".$arProfile["DOMAIN_NAME"].$sessionData["EXPORTPRO"]["LOG"][$profileID]["FILE"];            
                
                $headers = "Content-type: text/plain; charset=".LANG_CHARSET;
                bxmail( $arProfile["SEND_LOG_EMAIL"], $messageTitle, $messageBlock, $headers );
            }    
        }
        
        return $sessionData["EXPORTPRO"]["LOG"][$profileID];
    }
    
    public function GetLogArray( $profileID ){
        $arLogArray = array();
        
        $dbProfile = new CExportproProfileDB();
        $arProfile = $dbProfile->GetByID( $profileID );
        
        $arSessionData = AcritExportproSession::GetAllSession( $profileID );
        $sessionData = array();
        if( !empty( $arSessionData ) ){
            $sessionData = $arSessionData[0];
            $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS"] = intval( $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS"] );
            $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_EXPORT"] = intval( $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_EXPORT"] );
            $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_ERROR"] = intval( $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_ERROR"] );
            
            unset( $arSessionData[0] );
            
            foreach( $arSessionData as $sData ){
                $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS"] += intval( $sData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS"] );
                $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_EXPORT"] += intval( $sData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_EXPORT"] );
                $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_ERROR"] += intval( $sData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_ERROR"] );
            }
            
            $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_ERROR"] = $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS"] - $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_EXPORT"];
        }
        
        $arLogArray["PRODUCTS"] = $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS"];
        $arLogArray["PRODUCTS_EXPORT"] = $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_EXPORT"];
        $arLogArray["PRODUCTS_ERROR"] = $sessionData["EXPORTPRO"]["LOG"][$profileID]["PRODUCTS_ERROR"];
        
        return $arLogArray;
    }
}

class AcritExportproProtect{
    static function Protect( $mode, $action ){
        if( $action == "protect" ){
            COption::SetOptionInt( "acrit.exportpro", "acrit.exportpro.mode", intval( $mode ) );
            if( $mode == 1 )
                return false;
            else
                return true;
        }
        else{
            return ( COption::GetOptionInt( "acrit.exportpro", "acrit.exportpro.mode" ) != 1 );
        }
    }
}

class AcritExportproSession {
    static private $sessionDir;
    static private $cronPage = -1;
    static public function Init( $cronpage ){
        if( !self::$sessionDir )
            self::$sessionDir = $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/acrit.exportpro/";
        
        self::$cronPage = $cronpage;
    }
    
    static public function GetAllSession( $id ){
        $files = scandir( self::$sessionDir );
        $arSessionData = array();
        foreach( $files as $file ){
            if( $file == "." || $file == ".." )
                continue;
                
            if( false !== strpos( $file, "export_{$id}" ) ){
                $sessionData = file_get_contents( self::$sessionDir.$file );
                $sessionData = unserialize( $sessionData );
                if( !is_array( $sessionData ) )
                    $sessionData = array();
                    
                if( !empty( $sessionData ) )
                    $arSessionData[] = $sessionData;
            }
        }
        return array_filter( $arSessionData );
    }
    
    static public function GetSession( $id ){
        if( self::$cronPage > 0 )
            $id .= "_".self::$cronPage;
        
        if( file_exists( self::$sessionDir."export_{$id}.session" ) ){
            $sessionData = file_get_contents( self::$sessionDir."export_{$id}.session" );
            $sessionData = unserialize( $sessionData );
        }
        
        if( !is_array( $sessionData ) )
            $sessionData = array();
            
        return $sessionData;
    }
    
    static public function SetSession( $id, $data ){
        if( !is_array( $data ) )
            $data = array();
        
        if( self::$cronPage > 0 )
            $id .= "_".self::$cronPage;
            
        file_put_contents( self::$sessionDir."export_{$id}.session", serialize( $data ) );
    }
    
    static public function DeleteSession( $id ){
        if( file_exists( self::$sessionDir."export_{$id}.session" ) )
            unlink( self::$sessionDir."export_{$id}.session" );
        
        $files = scandir( self::$sessionDir );
        foreach( $files as $file ){
            if( $file == "." || $file == ".." )
                continue;
            
            if( false !== strpos( $file, "export_{$id}") ){
                unlink( self::$sessionDir.$file );
            }
        }
    }
}?>