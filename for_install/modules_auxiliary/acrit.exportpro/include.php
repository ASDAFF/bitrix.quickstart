<?php
IncludeModuleLangFile( __FILE__ );

global $DBType;        

$arClasses = array(
    "AcritLicence" => "classes/general/licence.php",
    "CExportproProfileDB" => "classes/mysql/cexportproprofiledb.php",
    "CExportproMarketDB" => "classes/mysql/cexportpropro_marketdb.php",
    "CExportproMarketTiuDB" => "classes/mysql/cexportpropro_markettiudb.php",
    "CExportproProfile" => "classes/general/cexportproprofile.php",
    "CExportproVariant" => "classes/general/cexportproprofile.php",
    
    "CAcritExportproCatalog" => "classes/general/cexportprofilter.php",
    "CAcritExportproPrices" => "classes/general/cexportprofilter.php",
    "CAcritExportproProps" => "classes/general/cexportprofilter.php",
    "CAcritExportproCatalogCond" => "classes/general/cexportprofilter.php",
    "CAcritExportproLog" => "classes/general/cexportprolog.php",
    "AcritExportproSession" => "classes/general/cexportprosession.php",
    "CAcritExportproUrlRewrite" => "classes/general/cexportprourlrewrite.php",
    "CAcritExportproTools" => "classes/general/cexportprotools.php",
    "CAcritCML2ExportTools" => "classes/general/cexportprocml2exporttools.php",
    
    "CAcritExportproExport" => "classes/general/cexportproexport.php",
    "CExportproCron" => "classes/general/cexportproagent.php",
    "CExportproAgent" => "classes/general/cexportproagent.php",
    "CExportproInformer" => "classes/general/cexportproinformer.php",
    "CExportproMarketEbayDB" => "classes/mysql/cexportpropro_marketebaydb.php",
    "Threads" => "classes/general/threads.php",
    "ThreadsSession" => "classes/general/threads.php",
    "OZON" => "classes/general/ozon.php",
    "CAcritCML2ExportElement" => "classes/general/cexportpro1c.php",
    "CAcritCML2" => "classes/general/cexportpro1c.php",
    "CAcritCML2Export" => "classes/general/cexportpro1c.php",
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

class CAcritExportproElement{
    public $profile = null;
    public $DEMO = 2;
    public $isDemo = true;
    public $DEMO_CNT;
    public $MODULEID = "acrit.exportpro";
    public $stepElements = 50;
    public $dateFields = array();
    public $log;
    public $session;
    public $baseDateTimePatern;

    protected $profileEncoding = array(
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

        $this->log = new CAcritExportproLog( $this->profile["ID"] );
        $this->iblockE = "file_get_contents";
        $this->iblockD = "base64_decode";
        
        $this->baseDateTimePatern = "Y-m-dTh:i:s±h:i";
        
        $paternCharset = CAcritExportproTools::GetStringCharset( $this->baseDateTimePatern );
                
        if( $paternCharset == "cp1251" ){
            $this->baseDateTimePatern = $APPLICATION->ConvertCharset( $this->baseDateTimePatern, "cp1251", "utf8" );
        }             
        
        $dateGenerate = ( $this->profile["DATEFORMAT"] == $this->baseDateTimePatern ) ? CAcritExportproTools::GetYandexDateTime( date( "d.m.Y H:i:s" ) ) : date( str_replace( "_", " ", $this->profile["DATEFORMAT"] ), time() );                
        
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
    
    public static function OnBeforePropertiesSelect( &$arFields ){    
        foreach( $arFields as $Key => &$arValue ){    
            if( is_array( $arValue ) ){    
                foreach( $arValue as &$Value ){    
                    $arProperty = explode( "-", $Value );
                    $cProperty = count( $arProperty );    
                    if( $cProperty == 3 ){                         
                        $Value = "PROPERTY_".$arProperty[2]."_DISPLAY_VALUE";
                    }
                }
            }
            else{
                $arProperty = explode( "-", $arValue );
                $cProperty = count( $arProperty );    
                if( $cProperty == 3 ){
                    $arValue = "PROPERTY_".$arProperty[2]."_DISPLAY_VALUE";
                }    
            }             
        }
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

    protected function DemoCount(){
        $sessionData = AcritExportproSession::GetSession( $this->profile["ID"] );
        return ( $sessionData["EXPORTPRO"][$this->profile["ID"]]["DEMO_COUNT"] > $this->DEMO_CNT );
    }
    
    protected function DemoCountInc(){
        $sessionData = AcritExportproSession::GetSession( $this->profile["ID"] );
        if( !isset( $sessionData["EXPORTPRO"][$this->profile["ID"]]["DEMO_COUNT"] ) )
            $sessionData["EXPORTPRO"][$this->profile["ID"]]["DEMO_COUNT"] = 0;
        
        $sessionData["EXPORTPRO"][$this->profile["ID"]]["DEMO_COUNT"]++;
        AcritExportproSession::SetSession( $this->profile["ID"], $sessionData );
    }   

    public function Process( $page = 1, $cronrun = false, $fileType = "xml", $fileExport = false, $fileExportName = false, $arOzonCategories = false , &$_ProcessEnd=false ){
        global $fileExportDataSize, $fileExportData, $ProcessEnd;
        $fileThread = false;
        $this->SetProcessStart( $fileThread );
        if( $fileType == "csv" ){            
            $ret= self::ProcessCSV( $page, $cronrun, $fileExport, $fileExportName );
            $this->SetProcessEnd( $fileThread );
            while( true !== $ProcessEnd ){            
            }
            $_ProcessEnd = $ProcessEnd;
            return $ret;
        }
        else{
            $ret = self::ProcessXML( $page, $cronrun, $arOzonCategories );
            
            $this->SetProcessEnd( $fileThread );
            while( true !== $ProcessEnd ){                
            }
            $_ProcessEnd = $ProcessEnd;
            return $ret;
        }        
    }
    
    public function ExportConvertCharset( $field ){
        global $APPLICATION;
        $result = "";
        
        $paternCharset = CAcritExportproTools::GetStringCharset( $field );    
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
                                                                                                          
        preg_match_all( "/.*(<[\w\d_-]+).*(#[\w\d_-]+:*[\w\d_-]+#).*(<\/.+>)/", $this->profile["OFFER_TEMPLATE"], $this->arMatches );
        
        // install for all templates #EXAMPLE# null value, so that you can remove
        $this->templateValuesDefaults = array();
        foreach( $this->arMatches[2] as $match ){
            $this->templateValuesDefaults[$match] = "";
        }
        $this->templateValuesDefaults["#MARKET_CATEGORY#"] = "";

        // get the properties used in the templates
        $this->useProperties = array(
            "ID" => array()
        );                                
        
        $this->usePrices = array();
        foreach( $this->profile["XMLDATA"] as $field ){
            if( !empty( $field["VALUE"] ) || !empty( $field["CONTVALUE_FALSE"] ) || !empty( $field["CONTVALUE_TRUE"] ) 
                || !empty( $field["COMPLEX_TRUE_VALUE"] ) || !empty( $field["COMPLEX_FALSE_VALUE"] ) 
                || !empty( $field["COMPLEX_TRUE_CONTVALUE"] ) || !empty( $field["COMPLEX_FALSE_CONTVALUE"] ) || ( $field["TYPE"] == "composite" ) ){
                
                if( $field["TYPE"] == "composite" ){
                    foreach( $field["COMPOSITE_TRUE"] as $compositeFieldIndex => $compositeField ){
                        if( $compositeField["COMPOSITE_TRUE_TYPE"] == "field" ){
                            $arValue = explode( "-", $compositeField["COMPOSITE_TRUE_VALUE"] );
                            
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
                        }
                    }
                    
                    foreach( $field["COMPOSITE_FALSE"] as $compositeFieldIndex => $compositeField ){
                        if( $compositeField["COMPOSITE_FALSE_TYPE"] == "field" ){
                            $arValue = explode( "-", $compositeField["COMPOSITE_FALSE_VALUE"] );
                            
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
                        }
                    }
                }
                else{
                    if( $field["TYPE"] == "field" ){
                        $fieldValue = $field["VALUE"];
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
                    }
                    elseif( $field["TYPE"] == "complex" ){
                        $fieldValue = $field["COMPLEX_TRUE_VALUE"];
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
                        
                        $fieldValue = $field["COMPLEX_FALSE_VALUE"];
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
                    }
                    
                    if( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                        $arElementConfig["DELAY"] = true;
                    }
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
        $dbEvents = GetModuleEvents( "acrit.exportpro", "OnBeforePropertiesSelect" );
        $eventResult = array();
        while( $arEvent = $dbEvents->Fetch() ){             
            ExecuteModuleEventEx(
                $arEvent,
                array(
                    array(
                        "ID" => $this->profile["ID"],
                        "CODE" => $this->profile["CODE"],
                        "NAME" => $this->profile["NAME"]
                    ),
                    &$eventResult
                )
            );             
        }
        
        foreach( $eventResult as $arValue ){        
            if( is_array( $arValue ) ){        
                foreach( $arValue as $Value ){        
                    $arProperty = explode( "-", $Value );
                    if( count( $arProperty ) == 3 ){
                        $this->useProperties["ID"][] = $arProperty[2];                         
                    }
                }
            }
            else{
                $arProperty = explode( "-", $arValue );
                if( count( $arProperty ) == 3 ){
                    $this->useProperties["ID"][] = $arProperty[2];
                }
            }
        }
        $this->useProperties["ID"] = array_unique( $this->useProperties["ID"] );
        $this->useProperties["ID"] = array_filter( $this->useProperties["ID"] );

        $this->currencyList = array();

        // variant properties
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
                                  
                        if( !CAcritExportproTools::CheckCondition( $arOfferCondElementProperties, $this->profile["EVAL_FILTER"] ) )
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
                    elseif( $arProfileTemplateNode["TYPE"] == "field" //!
                        || ( ( $arProfileTemplateNode["TYPE"] == "complex" ) && ( $arProfileTemplateNode["COMPLEX_TRUE_TYPE"] == "field" ) ) ){
                        
                        $fieldValue = ( $arProfileTemplateNode["TYPE"] == "field" ) ? $arProfileTemplateNode["VALUE"] : $arProfileTemplateNode["COMPLEX_TRUE_VALUE"];   
                        $arValue = explode( "-", $fieldValue );
                        
                        if( count( $arValue ) == 2 ){
                            $fieldValue = "CATALOG_".$arValue[1];
                        }
                        if( count( $arValue ) == 3 ){
                            $test = "";
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
            
            CAcritExportproTools::ExportArrayMultiply( $arResFields, $arTuple );
                                        
            if( $paternCharset == "cp1251" ){
                $this->baseDateTimePatern = $APPLICATION->ConvertCharset( $this->baseDateTimePatern, "cp1251", "utf8" );
            }
                                        
            foreach( $arProcess as $field ){
                $arTuple = array();
                foreach( $field as $fieldPart ){
                    $arTuple[] = $this->ExportConvertCharset( $fieldPart );    
                }
                CAcritExportproTools::ExportArrayMultiply( $arResFields, $arTuple );
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
            
            CAcritExportproTools::ExportArrayMultiply( $arResFields, $arTuple );
            
            foreach( $arProcess as $arRow ){
                $arTuple = array();
                foreach( $arRow as $colValue ){
                    $arTuple[] = $this->ExportConvertCharset( $colValue );
                }
                CAcritExportproTools::ExportArrayMultiply( $arResFields, $arTuple );
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
    
    protected function isVariant( $categoryId = false ){
        if( $categoryId ){
            return ( ( $this->profile["USE_VARIANT"] == "Y" )
                && ( $this->profile["TYPE"] == "activizm" )
                && ( $this->profile["VARIANT"]["CATEGORY"][$categoryId] ) );
        }
        return ( ( $this->profile["USE_VARIANT"] == "Y" ) && ( $this->profile["TYPE"] == "activizm" ) );
    }
    
    public function GetProfileMarketCategoryType( $type ){
        switch( $type ){            
            case "tiu_standart":
            case "tiu_standart_vendormodel":
                return "CExportproMarketTiuDB";
                break;    
        }
    }
    
    public function ProcessXML( $page = 1, $cronrun = false, $arOzonCategories = false ){
        if( $page == 1 ){
            $this->log->Init( $this->profile );
            $this->page = $page;                                    
        }                     
        if( $this->GetProfileMarketCategoryType( $this->profile["TYPE"] ) == "CExportproMarketTiuDB" ){            
            $marketCategory = new CExportproMarketTiuDB();
            $marketCategory = $marketCategory->GetList();
            $this->marketCategory = $marketCategory; 
        }
        
        $this->currencyRates = CExportproProfile::LoadCurrencyRates();
        $iblockList = $this->PrepareIBlock();
        if( empty( $iblockList ) )
            return true;
            
        $pregMatchExp = GetMessage( "ACRIT_EXPORTPRO_A_AA_A" );
                                                                                                          
        preg_match_all( "/.*(<[\w\d_-]+).*(#[\w\d_-]+:*[\w\d_-]+#).*(<\/.+>)/", $this->profile["OFFER_TEMPLATE"], $this->arMatches );
        
        // install for all templates #EXAMPLE# null value, so that you can remove
        $this->templateValuesDefaults = array();
        foreach( $this->arMatches[2] as $match ){
            $this->templateValuesDefaults[$match] = "";
        }
        $this->templateValuesDefaults["#MARKET_CATEGORY#"] = "";

        // get the properties used in the templates
        $this->useProperties = array(
            "ID" => array()
        );
        
        $this->usePrices = array();  
        
        foreach( $this->profile["XMLDATA"] as $field ){
            if( !empty( $field["VALUE"] ) || !empty( $field["CONTVALUE_FALSE"] ) || !empty( $field["CONTVALUE_TRUE"] )
                || !empty( $field["COMPLEX_TRUE_VALUE"] ) || !empty( $field["COMPLEX_FALSE_VALUE"] ) 
                || !empty( $field["COMPLEX_TRUE_CONTVALUE"] ) || !empty( $field["COMPLEX_FALSE_CONTVALUE"] ) || ( $field["TYPE"] == "composite" ) ){
                
                if( $field["TYPE"] == "composite" ){
                    foreach( $field["COMPOSITE_TRUE"] as $compositeFieldIndex => $compositeField ){
                        if( $compositeField["COMPOSITE_TRUE_TYPE"] == "field" ){
                            $arValue = explode( "-", $compositeField["COMPOSITE_TRUE_VALUE"] );
                            
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
                        }
                    }
                    
                    foreach( $field["COMPOSITE_FALSE"] as $compositeFieldIndex => $compositeField ){
                        if( $compositeField["COMPOSITE_FALSE_TYPE"] == "field" ){
                            $arValue = explode( "-", $compositeField["COMPOSITE_FALSE_VALUE"] );
                            
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
                        }
                    }
                }
                else{
                    if( $field["TYPE"] == "field" ){
                        $fieldValue = $field["VALUE"];
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
                    }
                    elseif( $field["TYPE"] == "complex" ){
                        $fieldValue = $field["COMPLEX_TRUE_VALUE"];
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
                        
                        $fieldValue = $field["COMPLEX_FALSE_VALUE"];
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
                    }
                    
                    if( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                        $arElementConfig["DELAY"] = true;
                    }
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
        $dbEvents = GetModuleEvents( "acrit.exportpro", "OnBeforePropertiesSelect" );
        $eventResult = array();
        while( $arEvent = $dbEvents->Fetch() ){
            ExecuteModuleEventEx(
                $arEvent,
                array(
                    array(
                        "ID" => $this->profile["ID"],
                        "CODE" => $this->profile["CODE"],
                        "NAME" => $this->profile["NAME"]
                    ),
                    &$eventResult
                )
            );             
        }
        foreach( $eventResult as $arValue ){
            if( is_array( $arValue ) ){
                foreach( $arValue as $Value ){
                    $arProperty = explode( "-", $Value );
                    if( count( $arProperty ) == 3 ){
                        $this->useProperties["ID"][] = $arProperty[2];                         
                    }
                }
            }
            else{
                $arProperty = explode( "-", $arValue );
                if( count( $arProperty ) == 3 ){
                    $this->useProperties["ID"][] = $arProperty[2];                     
                }        
            }
        }
        $this->useProperties["ID"] = array_unique( $this->useProperties["ID"] );
        $this->useProperties["ID"] = array_filter( $this->useProperties["ID"] );

        $this->currencyList = array();

        // variant properties
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
        
        if( is_array( $this->profile["VARIANT"] ) && !empty( $this->profile["VARIANT"] ) ){
            foreach( $this->profile["VARIANT"] as $vpKey => $vpValue ){
                if( key_exists( $vpKey, $variantPropCode ) ){
                    $variantProperty = explode( "-", $vpValue );
                    if( count( $variantProperty ) == 3 ){
                        $this->useProperties["ID"][] = $variantProperty[2];
                        $this->variantProperties[$variantPropCode[$vpKey]] = "PROPERTY_".$variantProperty[2]."_DISPLAY_VALUE";
                    }
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
            $variantCatalogProducts = array();
            $arOfferElementResult = array();
            $this->delay = "";
            $arItem = $this->ProcessElement( $arElement, false, $arOzonCategories, $arElementConfig );
             
            if( !$arItem )
                continue;

            if( $this->isVariant( $arItem["IBLOCK_SECTION_ID"] ) ){
                if( isset( $arItem["SKIP"] ) && !$arItem["SKIP"] ){
                    $variantItems[$arItem["ITEM"][$variantPrice]][] = $arItem;                   
                }
                if( isset( $arItem["ITEM"] ) ){
                    if( isset( $arItem["ITEM"]["GROUP_ITEM_ID"] ) && ( $arItem["ITEM"]["GROUP_ITEM_ID"] == $arItem["ITEM"]["ID"] ) ){
                        $variantCatalogProducts[] = $arItem;
                    }       
                    $arItem = $arItem["ITEM"];
                }
            }
            
            // if you enable the processing trade offers, we look for and process trade offers
            if( $this->catalogIncluded && ( $this->profile["USE_SKU"] == "Y" ) && ( $this->catalogSKU[$arItem["IBLOCK_ID"]] ) && ( $arItem["ACTIVE"] == "Y" ) ){  
                if( isset( $arElementConfig["DELAY"] ) && ( $arElementConfig["DELAY"] == true ) )
                    $arElementConfig["DELAY_SKU"] = true;
                
                $arOfferFilter = array(
                    "IBLOCK_ID" => $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"],
                    "PROPERTY_".$this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_PROPERTY_ID"] => $arItem["ID"]
                );
                
                if( $this->isVariant( $arItem["IBLOCK_SECTION_ID"] ) ){
                    $arOfferFilter = array_merge(
                        $arOfferFilter,
                        array(
                            "CATALOG_AVAILABLE" => "Y"
                        )
                    );
                }
                
                $dbOfferElements = CIBlockElement::GetList(
                    array(),
                    $arOfferFilter,
                    false,
                    false,
                    array()
                );
                
                while( $arOfferElement = $dbOfferElements->GetNextElement() ){
                    $arOfferItem = $this->ProcessElement( $arOfferElement, $arItem, $arOzonCategories, $arElementConfig, $arOfferElementResult );

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
                $dbEvents = GetModuleEvents( "acrit.exportpro", "OnBeforePropertiesSelect" );
                $eventResult = array();
                while( $arEvent=$dbEvents->Fetch() ){
                    ExecuteModuleEventEx(
                        $arEvent,
                        array(
                            array(
                                "ID" => $this->profile["ID"],
                                "CODE" => $this->profile["CODE"],
                                "NAME" => $this->profile["NAME"]
                            ),
                            &$eventResult
                        )
                    ); 
                }
                CAcritExportproElement::OnBeforePropertiesSelect( $eventResult );

                $productExport = 0;
                foreach( $variantItems as $price => $items ){
                    $itemTemplate = $items[0]["XML"];
                    $colorsize = array();
                    $variantItemTemplate = "";
                    
                    foreach( $items as $item ){
                        $arItem = $item["ITEM"];
                        $isOffer = $item["OFFER"];
                        $eventProperty = array();
                        foreach( array( "SIZE", "WEIGHT", "COLOR", "SIZEOFFER", "WEIGHTOFFER", "COLOROFFER" ) as $name ){
                            if( isset( $eventResult[$name] ) ){
                                foreach( $eventResult[$name] as $prop ){
                                    if( !empty( $arItem[$prop] ) ){
                                       $eventProperty[$name][] = $prop; 
                                    }    
                                }
                            }
                        }
                        
                        $gender = $this->profile["VARIANT"]["SEX_CONST"] ? $this->profile["VARIANT"]["SEX_CONST"] : $arItem[$this->variantProperties["SEX"]];
                        $arSize = explode( "-", $this->profile["VARIANT"]["CATEGORY"][$arItem["IBLOCK_SECTION_ID"]] );
                        $arSizeExt = explode( "-", $this->profile["VARIANT"]["CATEGORY_EXT"][$arItem["IBLOCK_SECTION_ID"]] );
                        
                        $itemSize = $this->variantProperties["SIZE"];
                        if( empty( $arItem[$itemSize] ) && count( $eventProperty["SIZE"] ) ){
                            $ar = $eventProperty["SIZE"];
                            $itemSize = current( $ar );
                                    
                        }
                                
                        $itemWeight = $this->variantProperties["WEIGHT"];
                        if( empty( $arItem[$itemWeight] ) && count( $eventProperty["WEIGHT"] ) ){
                            $ar = $eventProperty["WEIGHT"];
                            $itemWeight = current( $ar );
                        }
                        
                        $itemColor = $this->variantProperties["COLOR"];
                        if( empty( $arItem[$itemColor] ) && count( $eventProperty["COLOR"] ) ){
                            $ar = $eventProperty["COLOR"];
                            $itemColor = current( $ar );
                        }
                        
                        if( $isOffer ){
                            // if trade offer, replace property values by trade offer values
                            $gender = $this->profile["VARIANT"]["SEX_CONST"] ? $this->profile["VARIANT"]["SEX_CONST"] : $arItem[$this->variantProperties["SEXOFFER"]];
                            $itemSize = $this->variantProperties["SIZEOFFER"];
                            if( empty( $arItem[$itemSize] ) && count( $eventProperty["SIZEOFFER"] ) ){
                                $ar = $eventProperty["SIZEOFFER"];
                                $itemSize = current( $ar );
                            }
                            
                            $itemWeight = $this->variantProperties["WEIGHTOFFER"];
                            if( empty( $arItem[$itemWeight] ) && count( $eventProperty["WEIGHTOFFER"] ) ){
                                $ar = $eventProperty["WEIGHTOFFER"];
                                $itemWeight = current( $ar );                            
                            }
                            
                            $itemColor = $this->variantProperties["COLOROFFER"];
                            if( empty( $arItem[$itemColor] ) && count( $eventProperty["COLOROFFER"] ) ){
                                $ar = $eventProperty["COLOROFFER"];
                                $itemColor = current( $ar );
                            }
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
                            
                        if( $arSize[1] == "OZ" ){
                            if( $arItem[$itemSize] || $arItem[$itemWeight] )
                                $variatType[] = "size";    
                        }
                        else{
                            if( $arItem[$itemSize])
                                $variatType[] = "size";
                        }
                        
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
                    
                    // increase the count statistics for export goods
                    $this->log->IncProductExport( $productExport );
                }
                if( ( $productExport == 0 ) && count( $variantCatalogProducts ) ){
                    foreach( $variantCatalogProducts as $catalogProduct ){
                        CAcritExportproExport::Save( $catalogProduct["XML"] );
                        $this->log->IncProductExport( 1 );
                    }
                }
                unset( $variantItems );
                unset( $variantCatalogProducts );
            }

            if( $this->isDemo && $this->DemoCount() )
                break;
                
            unset( $arItem );
            
            if( isset( $arElementConfig["DELAY"] ) && $arElementConfig["DELAY"] == true ){               
                $arElementConfig["DELAY_FLUSH"] = true;                
                if( isset( $field["MINIMUM_OFFER_PRICE"] ) && $field["MINIMUM_OFFER_PRICE"] == "Y" ){                
                    $arElementConfig["MINIMUM_OFFER_PRICE"] = "Y";
                }
                $this->ProcessElement( $arElement, false, $arOzonCategories, $arElementConfig, $arOfferElementResult );

                unset( $arElementConfig["DELAY_SKU"] );
                unset( $arElementConfig["DELAY_FLUSH"] );
                if( isset( $arElementConfig["MINIMUM_OFFER_PRICE"] ) )
                    unset( $arElementConfig["MINIMUM_OFFER_PRICE"] );            
            }
        }            
        
        unset( $arElement, $arItem );

        if( !$cronrun ){
            echo '<div class="width: 100%; text-align: center; font-size: 18px; margin: 40px 0; padding: 40px 0; border: 1px solid #ccc; border-radius: 6px; background: #f5f5f5;">',
            GetMessage( "ACRIT_EXPORTPRO_RUN_EXPORT_RUN" ), "<br>",
            str_replace( array( "#PROFILE_ID#", "#PROFILE_NAME#" ), array( $this->profile["ID"], $this->profile["NAME"] ), GetMessage( "ACRIT_EXPORTPRO_RUN_STEP_PROFILE" ) ), "<br>",
            str_replace( array( "#STEP#", "#COUNT#" ), array( $page, $dbElements->NavPageCount ), GetMessage( "ACRIT_EXPORTPRO_RUN_STEP_RUN" ) ),
            "</div>";
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

        // get the properties used in the templates
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
        
        $dbEvents = GetModuleEvents( "acrit.exportpro", "OnBeforePropertiesSelect" );
        $eventResult = array();
        while( $arEvent = $dbEvents->Fetch() ){             
            ExecuteModuleEventEx(
                $arEvent,
                array(
                    array(
                        "ID" => $this->profile["ID"],
                        "CODE" => $this->profile["CODE"],
                        "NAME" => $this->profile["NAME"]
                    ),
                    &$eventResult
                )
            );             
        }
        
        foreach( $eventResult as $arValue ){        
            if( is_array( $arValue ) ){
                foreach( $arValue as $Value ){        
                    $arProperty = explode( "-", $Value );
                    if( count( $arProperty ) == 3 ){
                        $this->useProperties["ID"][] = $arProperty[2];                         
                    }
                }
            }
            else{
                $arProperty = explode( "-", $arValue );
                if( count( $arProperty ) == 3 ){
                    $this->useProperties["ID"][] = $arProperty[2];
                }
            }
        }
        
        $this->useProperties["ID"] = array_unique( $this->useProperties["ID"] );
        $this->useProperties["ID"] = array_filter( $this->useProperties["ID"] );

        $this->currencyList = array();

        // variant properties
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
    
    // getting the properties of the product, the formation of the pattern, the replacement values of fields and write to the file
    private function ProcessElementToCsv( $arElement, $arProductSKU = false ){
        $skipElement = false;
        $this->AddResolve();
        $this->xmlCode = false;
        $arItem = $this->GetElementProperties( $arElement );
        
        // adding product properties and fields to trade offers
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

        // verification, whether the item meets the general conditions of profile
        if( !CAcritExportproTools::CheckCondition( $arItem, $this->profile["EVAL_FILTER"] ) )
            return false;

        // increase the count statistics for export goods
        $this->log->IncProduct();

        $itemTemplate = $this->profile["OFFER_TEMPLATE"];
        $templateValues = $this->templateValuesDefaults;
        $templateValues["GROUP_ITEM_ID"] = $arItem["GROUP_ITEM_ID"];
        $arItemMain = $arItem;
        foreach( $this->profile["XMLDATA"] as $xmlCode => $field ){
            $this->xmlCode = $xmlCode;
            $arItem = $arItemMain;
            $useCondition = ( $field["USE_CONDITION"] == "Y" );
            if( $useCondition ){
                $conditionTrue = ( CAcritExportproTools::CheckCondition( $arItem, $field["EVAL_FILTER"] ) == true );
            }

            if( $useCondition && !$conditionTrue ){
                if( ( $field["TYPE"] == "const" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_FALSE_TYPE"] == "const" ) ) ){
                    
                    $field["CONTVALUE_FALSE"] = ( $field["TYPE"] == "const" ) ? $field["CONTVALUE_FALSE"] : $field["COMPLEX_FALSE_CONTVALUE"];            
                    $templateValues["{$field["CODE"]}"] = $field["CONTVALUE_FALSE"];
                    continue;
                }
                else{
                    if( $field["TYPE"] == "composite" ){
                        $compositeValue = "";
                        $compositeFalseDivider = ( strlen( trim( $field["COMPOSITE_FALSE_DIVIDER"] ) ) > 0 ) ? trim( $field["COMPOSITE_FALSE_DIVIDER"] ) : " ";
                        foreach( $field["COMPOSITE_FALSE"] as $compositeFieldIndex => $compositeField ){
                            if( $compositeFieldIndex > 1 ){
                                $compositeValue .= $compositeFalseDivider;
                            }
                            if( $compositeField["COMPOSITE_FALSE_TYPE"] == "const" ){                            
                                $compositeValue .= CAcritExportproTools::RoundNumber( $compositeField["COMPOSITE_FALSE_CONTVALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                            }
                            elseif( $compositeField["COMPOSITE_FALSE_TYPE"] == "field" ){
                                $compositeValueTmp = "";
                                if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                                    $compositeValueTmp = detailLink( $arItem["ID"] );
                                }
                                else{
                                    $arValue = explode( "-", $compositeField["COMPOSITE_FALSE_VALUE"] );
                                    
                                    switch( count( $arValue ) ){
                                        case 1:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){                                            
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( strpos( $compositeField["COMPOSITE_FALSE_VALUE"], "." ) !== false ){
                                                $arField = explode( ".", $compositeField["COMPOSITE_FALSE_VALUE"] );
                                                switch( $arField[0] ){
                                                    case "SECTION":
                                                        $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                        $value = $curSection[$arField[1]] ? : "";
                                                        break;
                                                    default:
                                                        $value = "";
                                                }
                                                unset( $arField );
                                                                                        
                                                $compositeValueTmp = CAcritExportproTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            else{
                                                $compositeValueTmp = CAcritExportproTools::RoundNumber( $arItem[$compositeField["COMPOSITE_FALSE_VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 2:
                                            $values = null;
                                            $compositeValueTmp = $arItem["CATALOG_".$arValue[1]];
                                            $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            if( is_array( $arProductSKU ) ){
                                                $values = $compositeValueTmp;
                                            }                           
                                            if( ( $field["CODE"] == "PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                                preg_match( "#PURCHASING_PRICE#", $arValue[1], $arPriceCode );    
                                            }
                                            else{
                                                preg_match( "#PRICE_[\d]+#", $arValue[1], $arPriceCode );    
                                            }                                                              
                                                                           
                                            $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];         
                                                                                  
                                            if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                                $compositeValueTmp = $convertFrom;
                                                $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values = $compositeValueTmp;
                                                }                                    
                                             
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        $compositeValueTmp = $convertTo;
                                                        $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $compositeValueTmp;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif( !empty( $arPriceCode[0] ) ){
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                            $compositeValueTmp = CAcritExportproTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                    $arItem["CATALOG_".$arValue[1]],
                                                                    $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                    $convertTo
                                                                ),
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }                                            
                                                        }
                                                        else{
                                                            $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }                                                                                                           
                                                        }
                                                    }
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                else{
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $compositeValueTmp += $compositeValueTmp * floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                    $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $compositeValueTmp;
                                                    }                                       
                                                }
                                            }
                                            if( is_array( $arProductSKU )&& !is_null( $values ) )
                                                $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;
                                            
                                            break;
                                        case 3:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){                                            
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                                if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                    $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                                
                                                if( is_array( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] ) ){
                                                    $compositeValueTmp = array();
                                                    foreach( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] as $val ){
                                                        if( ( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 )
                                                            && ( count( $compositeValueTmp ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) ){
                                                            $compositeValueTmp[] = CAcritExportproTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                            
                                                        }
                                                        else{
                                                            $compositeValueTmp[] = CAcritExportproTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }
                                                    
                                                    $compositeValueTmpStr = "";
                                                    if( !empty( $compositeValueTmp ) ){
                                                        foreach( $compositeValueTmp as $compositeValueTmpIndex => $compositeValueTmpItem ){
                                                            if( $compositeValueTmpIndex ){
                                                                $compositeValueTmpStr .= $compositeFalseDivider;
                                                            }
                                                            $compositeValueTmpStr .= $compositeValueTmpItem;
                                                        }
                                                    }
                                                    
                                                    if( strlen( $compositeValueTmpStr ) > 0 ){
                                                        $compositeValueTmp = $compositeValueTmpStr;
                                                    }                                        
                                                }
                                                else{
                                                    $compositeValueTmp = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                    }
                                }
                                $compositeValue .= $compositeValueTmp;
                            }
                        }
                        $templateValues["#{$field["CODE"]}#"] =  CAcritExportproTools::RoundNumber( $compositeValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
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
                                        $arItem = $arItemMain;
                                        if( isset( $this->useResolve[$xmlCode] ) ){
                                            $arItem = $this->GetElementProperties( $arElement );
                                        }
                                        $templateValues["{$field["CODE"]}"] = $arItem[$field["VALUE"]];
                                        $arItem = $arItemMain;
                                        break;
                                    case 2:
                                        $templateValues["{$field["CODE"]}"] = $arItem["CATALOG_".$arValue[1]];
                                        $templateValues["{$field["CODE"]}"] = CAcritExportproTools::RoundNumber( $templateValues["{$field["CODE"]}"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        preg_match( "PRICE_[\d]+", $arValue[1], $arPriceCode );
                                        $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];
                                        if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                            $templateValues["{$field["CODE"]}"] = $convertFrom;
                                            $templateValues["{$field["CODE"]}"] = CAcritExportproTools::RoundNumber( $templateValues["{$field["CODE"]}"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            
                                            if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                    $templateValues["{$field["CODE"]}"] = $convertTo;
                                                    $templateValues["{$field["CODE"]}"] = CAcritExportproTools::RoundNumber( $templateValues["{$field["CODE"]}"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }
                                        }
                                        elseif( !empty( $arPriceCode[0] ) ){
                                            if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                    if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                        $templateValues["{$field["CODE"]}"] = CAcritExportproTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                $arItem["CATALOG_".$arValue[1]],
                                                                $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                $convertTo
                                                            ),
                                                            $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                        );
                                                    }
                                                    else{
                                                        $templateValues["{$field["CODE"]}"] = CAcritExportproTools::RoundNumber( $templateValues["{$field["CODE"]}"] *
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                            $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                        );
                                                    }
                                                }
                                                if( !in_array( $convertFrom, $this->currencyList ) )
                                                    $this->currencyList[] = $convertFrom;
                                            }
                                            else{
                                                if( !in_array( $convertFrom, $this->currencyList ) )
                                                    $this->currencyList[] = $convertFrom;
                                            }
                                            if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                $templateValues["{$field["CODE"]}"] += $templateValues["{$field["CODE"]}"] *
                                                floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                $templateValues["{$field["CODE"]}"] = CAcritExportproTools::RoundNumber( $templateValues["{$field["CODE"]}"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                        }
                                        break;
                                    case 3:
                                        $arItem = $arItemMain;
                                        if( isset( $this->useResolve[$xmlCode] ) ){                                        
                                            $arItem = $this->GetElementProperties( $arElement );
                                        }
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
                                        $arItem = $arItemMain;
                                        break;
                                }
                            }
                        }
                    }
                }  
            }
            else{
                // field or property
                if( ( $field["TYPE"] == "field" )
                    || ( $field["TYPE"] == "composite" ) 
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_TRUE_TYPE"] == "field" ) ) ){
                    
                    if( $field["TYPE"] == "composite" ){
                        $compositeValue = "";
                        $compositeTrueDivider = ( strlen( trim( $field["COMPOSITE_TRUE_DIVIDER"] ) ) > 0 ) ? trim( $field["COMPOSITE_TRUE_DIVIDER"] ) : " ";
                        foreach( $field["COMPOSITE_TRUE"] as $compositeFieldIndex => $compositeField ){
                            if( $compositeFieldIndex > 1 ){
                                $compositeValue .= $compositeTrueDivider;
                            }
                            if( $compositeField["COMPOSITE_TRUE_TYPE"] == "const" ){                            
                                $compositeValue .= CAcritExportproTools::RoundNumber( $compositeField["COMPOSITE_TRUE_CONTVALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                            }
                            elseif( $compositeField["COMPOSITE_TRUE_TYPE"] == "field" ){
                                $compositeValueTmp = "";
                                if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                                    $compositeValueTmp = detailLink( $arItem["ID"] );
                                }
                                else{
                                    $arValue = explode( "-", $compositeField["COMPOSITE_TRUE_VALUE"] );
                                    switch( count( $arValue ) ){
                                        case 1:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){                                            
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( strpos( $compositeField["COMPOSITE_TRUE_VALUE"], "." ) !== false ){
                                                $arField = explode( ".", $compositeField["COMPOSITE_TRUE_VALUE"] );
                                                switch( $arField[0] ){
                                                    case "SECTION":
                                                        $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                        $value = $curSection[$arField[1]] ? : "";
                                                        break;
                                                    default:
                                                        $value = "";
                                                }
                                                unset( $arField );                       
                                                $compositeValueTmp = CAcritExportproTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            else{
                                                $compositeValueTmp = CAcritExportproTools::RoundNumber( $arItem[$compositeField["COMPOSITE_TRUE_VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 2:
                                            $values = null;
                                            $compositeValueTmp = $arItem["CATALOG_".$arValue[1]];
                                            $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            if( is_array( $arProductSKU ) ){
                                                $values = $compositeValueTmp;
                                            }                           
                                            if( ( $field["CODE"] == "PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                                preg_match( "#PURCHASING_PRICE#", $arValue[1], $arPriceCode );    
                                            }
                                            else{
                                                preg_match( "#PRICE_[\d]+#", $arValue[1], $arPriceCode );    
                                            }                                                              
                                                                           
                                            $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];         
                                                                                  
                                            if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                                $compositeValueTmp = $convertFrom;
                                                $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values = $compositeValueTmp;
                                                }                                    
                                             
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        $compositeValueTmp = $convertTo;
                                                        $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $compositeValueTmp;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif( !empty( $arPriceCode[0] ) ){
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                            $compositeValueTmp = CAcritExportproTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                    $arItem["CATALOG_".$arValue[1]],
                                                                    $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                    $convertTo
                                                                ),
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }                                            
                                                        }
                                                        else{
                                                            $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }                                                                                                           
                                                        }
                                                    }
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                else{
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $compositeValueTmp += $compositeValueTmp * floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                    $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $compositeValueTmp;
                                                    }                                       
                                                }
                                            }
                                            if( is_array( $arProductSKU )&& !is_null( $values ) )
                                                $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;
                                            
                                            break;
                                        case 3:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){                                            
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                                if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                    $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                                
                                                if( is_array( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] ) ){
                                                    $compositeValueTmp = array();
                                                    foreach( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] as $val ){
                                                        if( ( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 )
                                                            && ( count( $compositeValueTmp ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) ){
                                                            $compositeValueTmp[] = CAcritExportproTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                            
                                                        }
                                                        else{
                                                            $compositeValueTmp[] = CAcritExportproTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }
                                                    
                                                    $compositeValueTmpStr = "";
                                                    if( !empty( $compositeValueTmp ) ){
                                                        foreach( $compositeValueTmp as $compositeValueTmpIndex => $compositeValueTmpItem ){
                                                            if( $compositeValueTmpIndex ){
                                                                $compositeValueTmpStr .= $compositeTrueDivider;
                                                            }
                                                            $compositeValueTmpStr .= $compositeValueTmpItem;
                                                        }
                                                    }
                                                    
                                                    if( strlen( $compositeValueTmpStr ) > 0 ){
                                                        $compositeValueTmp = $compositeValueTmpStr;
                                                    }                                        
                                                }
                                                else{
                                                    $compositeValueTmp = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                    }
                                }
                                $compositeValue .= $compositeValueTmp;
                            }
                        }
                        $templateValues["#{$field["CODE"]}#"] =  CAcritExportproTools::RoundNumber( $compositeValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                    }
                    else{
                        $field["VALUE"] = ( $field["TYPE"] == "field" ) ? $field["VALUE"] : $field["COMPLEX_TRUE_VALUE"];
                            
                        if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                            $templateValues["{$field["CODE"]}"] = detailLink( $arItem["ID"] );
                        }
                        else{
                            $arValue = explode( "-", $field["VALUE"] );
                            switch( count( $arValue ) ){
                                case 1:
                                    $arItem = $arItemMain;
                                    if( isset( $this->useResolve[$xmlCode] ) ){
                                    
                                        $arItem = $this->GetElementProperties( $arElement );
                                    
                                    }
                                    $templateValues["{$field["CODE"]}"] = $arItem[$field["VALUE"]];
                                    $arItem = $arItemMain;
                                    break;
                                case 2:
                                    $templateValues["{$field["CODE"]}"] = $arItem["CATALOG_".$arValue[1]];
                                    $templateValues["{$field["CODE"]}"] = CAcritExportproTools::RoundNumber( $templateValues["{$field["CODE"]}"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                    
                                    preg_match( "PRICE_[\d]+", $arValue[1], $arPriceCode );
                                    $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];
                                    if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                        $templateValues["{$field["CODE"]}"] = $convertFrom;
                                        $templateValues["{$field["CODE"]}"] = CAcritExportproTools::RoundNumber( $templateValues["{$field["CODE"]}"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        
                                        if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                            if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                $templateValues["{$field["CODE"]}"] = $convertTo;
                                                $templateValues["{$field["CODE"]}"] = CAcritExportproTools::RoundNumber( $templateValues["{$field["CODE"]}"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                        }
                                    }
                                    elseif( !empty( $arPriceCode[0] ) ){
                                        if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                            if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                    $templateValues["{$field["CODE"]}"] = CAcritExportproTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                            $arItem["CATALOG_".$arValue[1]],
                                                            $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                            $convertTo
                                                        ),
                                                        $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                    );
                                                }
                                                else{
                                                    $templateValues["{$field["CODE"]}"] = CAcritExportproTools::RoundNumber( $templateValues["{$field["CODE"]}"] *
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                        $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                    );
                                                }
                                            }
                                            if( !in_array( $convertFrom, $this->currencyList ) )
                                                $this->currencyList[] = $convertFrom;
                                        }
                                        else{
                                            if( !in_array( $convertFrom, $this->currencyList ) )
                                                $this->currencyList[] = $convertFrom;
                                        }
                                        if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                            $templateValues["{$field["CODE"]}"] += $templateValues["{$field["CODE"]}"] *
                                            floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                            $templateValues["{$field["CODE"]}"] = CAcritExportproTools::RoundNumber( $templateValues["{$field["CODE"]}"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                    }
                                    break;
                                case 3:
                                    $arItem = $arItemMain;
                                    if( isset( $this->useResolve[$xmlCode] ) ){
                                        $arItem = $this->GetElementProperties( $arElement );
                                    }
                                    if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
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
                                    $arItem = $arItemMain;
                                    break;
                            }
                        }
                    }
                }
                elseif( ( $field["TYPE"] == "const" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_TRUE_TYPE"] == "const" ) ) ){ // const
                
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
                $this->log->IncProductError();
            }
        }     

        array_walk( $templateValues, function( &$value ){
            if( is_array( $value ) ){
                foreach( $value as $id => $val )
                    $value[$id] = htmlspecialcharsbx( $val );
            }
            else
            $value = htmlspecialcharsbx( $value );
        });

        // set market category if it checked
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
                if( strlen( trim( $this->profile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]] ) ) <= 0 ){
                    return $arItem;
                }
            
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
        
    // get product properties, template creation, set fields values, write it in file
    private function ProcessElement( $arElement, $arProductSKU = false, $arOzonCategories = false, $arItemConfig=array(), &$arOfferElementResult = array() ){
        static $arSectionCache;
        global $DB, $APPLICATION;
        $this->AddResolve();
        $skipElement = false;
        $this->xmlCode = false;
        $_arOfferElementResult = array();
        $arItem = $this->GetElementProperties( $arElement );
        
        // add product properties and fields to product offers
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
                        $arItem[$key] = strip_tags( $arItem[$key] );
                        break;
                    case $key."_OFFER_SKU":
                        $arItem[$key] = implode( " ", array( $arProductSKU[$key], $arItem[$key] ) );
                        $arItem[$key] = strip_tags( $arItem[$key] );
                        break;
                    case $key."_OFFER_IF_SKU_EMPTY":
                        if( !isset( $arItem[$key] ) || empty( $arItem[$key] ) ){
                            if( isset( $arProductSKU[$key] ) && !empty( $arProductSKU[$key] ) ){
                                $value = $arProductSKU[$key];
                                if( is_array( $value ) ){
                                    foreach( $value as $_key => $_value )
                                        $arItem[$key][$_key] = strip_tags( $_value );
                                }
                                else{
                                    $arItem[$key] = $value;
                                    $arItem[$key] = strip_tags( $arItem[$key] );
                                }
                            }
                        }
                        break;
                }
            }
        }
        else{
            $arItem["GROUP_ITEM_ID"] = $arItem["ID"];
        }          
        
        // check element on basic profile conditions
        if( $this->catalogIncluded ){
            if( !CAcritExportproTools::CheckCondition( $arItem, $this->profile["EVAL_FILTER"] ) ){
                return $arItem;
            }
        }
                          
        // inc statistic product counter
        $this->log->IncProduct();     
        $itemTemplate = $this->profile["OFFER_TEMPLATE"];         
        $templateValues = $this->templateValuesDefaults;
        
        if( empty( $arSectionCache[$arItem["IBLOCK_ID"]] ) ){
            $rs = CIBlockSection::GetList(
                array(
                    "LEFT_MARGIN" => "ASC"
                ),
                array(
                    "IBLOCK_ID" => $arItem["IBLOCK_ID"]
                )
                );
            while( $ar = $rs->GetNext( false, false ) ){
                $arSectionCache[$arItem["IBLOCK_ID"]][$ar["ID"]] = $ar;
            }
        }
        
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
        $arItemMain = $arItem;
        foreach( $this->profile["XMLDATA"] as $xmlCode => $field ){
            $this->xmlCode = $xmlCode;
            $arItem = $arItemMain;

            $useCondition = ( $field["USE_CONDITION"] == "Y" );
            if( $useCondition ){
                $conditionTrue = ( CAcritExportproTools::CheckCondition( $arItem, $field["EVAL_FILTER"] ) == true );
            }                 

            if( $useCondition && !$conditionTrue ){
                if( ( $field["TYPE"] == "const" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_FALSE_TYPE"] == "const" ) ) ){
                    
                    $field["CONTVALUE_FALSE"] = ( $field["TYPE"] == "const" ) ? $field["CONTVALUE_FALSE"] : $field["COMPLEX_FALSE_CONTVALUE"];            
                    $templateValues["#{$field["CODE"]}#"] = $field["CONTVALUE_FALSE"];
                    continue;
                }
                else{
                    if( $field["TYPE"] == "composite" ){
                        $compositeValue = "";
                        $compositeFalseDivider = ( strlen( trim( $field["COMPOSITE_FALSE_DIVIDER"] ) ) > 0 ) ? trim( $field["COMPOSITE_FALSE_DIVIDER"] ) : " ";
                        foreach( $field["COMPOSITE_FALSE"] as $compositeFieldIndex => $compositeField ){
                            if( $compositeFieldIndex > 1 ){
                                $compositeValue .= $compositeFalseDivider;
                            }
                            if( $compositeField["COMPOSITE_FALSE_TYPE"] == "const" ){                            
                                $compositeValue .= CAcritExportproTools::RoundNumber( $compositeField["COMPOSITE_FALSE_CONTVALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                            }
                            elseif( $compositeField["COMPOSITE_FALSE_TYPE"] == "field" ){
                                $compositeValueTmp = "";
                                if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                                    $compositeValueTmp = detailLink( $arItem["ID"] );
                                }
                                else{
                                    $arValue = explode( "-", $compositeField["COMPOSITE_FALSE_VALUE"] );
                                    
                                    switch( count( $arValue ) ){
                                        case 1:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){                                            
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( strpos( $compositeField["COMPOSITE_FALSE_VALUE"], "." ) !== false ){
                                                $arField = explode( ".", $compositeField["COMPOSITE_FALSE_VALUE"] );
                                                switch( $arField[0] ){
                                                    case "SECTION":
                                                        $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                        $value = $curSection[$arField[1]] ? : "";
                                                        break;
                                                    default:
                                                        $value = "";
                                                }
                                                unset( $arField );
                                                                                        
                                                $compositeValueTmp = CAcritExportproTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            else{
                                                $compositeValueTmp = CAcritExportproTools::RoundNumber( $arItem[$compositeField["COMPOSITE_FALSE_VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 2:
                                            $values = null;
                                            $compositeValueTmp = $arItem["CATALOG_".$arValue[1]];
                                            $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            if( is_array( $arProductSKU ) ){
                                                $values = $compositeValueTmp;
                                            }                           
                                            if( ( $field["CODE"] == "PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                                preg_match( "#PURCHASING_PRICE#", $arValue[1], $arPriceCode );    
                                            }
                                            else{
                                                preg_match( "#PRICE_[\d]+#", $arValue[1], $arPriceCode );    
                                            }                                                              
                                                                           
                                            $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];         
                                                                                  
                                            if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                                $compositeValueTmp = $convertFrom;
                                                $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values = $compositeValueTmp;
                                                }                                    
                                             
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        $compositeValueTmp = $convertTo;
                                                        $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $compositeValueTmp;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif( !empty( $arPriceCode[0] ) ){
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                            $compositeValueTmp = CAcritExportproTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                    $arItem["CATALOG_".$arValue[1]],
                                                                    $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                    $convertTo
                                                                ),
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }                                            
                                                        }
                                                        else{
                                                            $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }                                                                                                           
                                                        }
                                                    }
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                else{
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $compositeValueTmp += $compositeValueTmp * floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                    $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $compositeValueTmp;
                                                    }                                       
                                                }
                                            }
                                            if( is_array( $arProductSKU )&& !is_null( $values ) )
                                                $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;
                                            
                                            break;
                                        case 3:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){                                            
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                                if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                    $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                                
                                                if( is_array( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] ) ){
                                                    $compositeValueTmp = array();
                                                    foreach( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] as $val ){
                                                        if( ( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 )
                                                            && ( count( $compositeValueTmp ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) ){
                                                            $compositeValueTmp[] = CAcritExportproTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                            
                                                        }
                                                        else{
                                                            $compositeValueTmp[] = CAcritExportproTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }                                        
                                                    
                                                    $compositeValueTmpStr = "";
                                                    if( !empty( $compositeValueTmp ) ){
                                                        foreach( $compositeValueTmp as $compositeValueTmpIndex => $compositeValueTmpItem ){
                                                            if( $compositeValueTmpIndex ){
                                                                $compositeValueTmpStr .= $compositeFalseDivider;
                                                            }
                                                            $compositeValueTmpStr .= $compositeValueTmpItem;
                                                        }
                                                    }
                                                    
                                                    if( strlen( $compositeValueTmpStr ) > 0 ){
                                                        $compositeValueTmp = $compositeValueTmpStr;
                                                    }
                                                }
                                                else{
                                                    $compositeValueTmp = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                    }
                                }
                                $compositeValue .= $compositeValueTmp;
                            }
                        }
                        $templateValues["#{$field["CODE"]}#"] =  CAcritExportproTools::RoundNumber( $compositeValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
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
                                    $arItem = $arItemMain;
                                    if( isset( $this->useResolve[$xmlCode] ) ){
                                        $arItem = $this->GetElementProperties( $arElement );
                                    }
                                    if( strpos( $field["VALUE"], "." ) !== false ){
                                        $arField = explode( ".", $field["VALUE"] );
                                        switch( $arField[0] ){
                                            case "SECTION":
                                                $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                $value = $curSection[$arField[1]] ? : "";
                                                break;
                                            default:
                                                $value = "";
                                        }
                                        unset( $arField );
                                        $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                    }
                                    else{
                                        $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $arItem[$field["VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                    }
                                    $arItem = $arItemMain;
                                    break;
                                case 2:
                                    $values = null;
                                    $templateValues["#{$field["CODE"]}#"] = $arItem["CATALOG_".$arValue[1]];
                                    $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                    if( is_array( $arProductSKU ) ){
                                        $values = $templateValues["#{$field["CODE"]}#"];
                                    }
                                    
                                    if( ( $field["CODE"] == "PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                        preg_match( "#PURCHASING_PRICE#", $arValue[1], $arPriceCode );    
                                    }
                                    else{
                                        preg_match( "#PRICE_[\d]+#", $arValue[1], $arPriceCode );    
                                    }                                                              
                                                                   
                                    $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];         
                                                                          
                                    if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                        $templateValues["#{$field["CODE"]}#"] = $convertFrom;
                                        $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        if( is_array( $arProductSKU ) ){
                                            $values = $templateValues["#{$field["CODE"]}#"];
                                        }
                                        
                                        if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                            if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                $templateValues["#{$field["CODE"]}#"] = $convertTo;
                                                $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values=$templateValues["#{$field["CODE"]}#"];
                                                }
                                            }
                                        }
                                    }
                                    elseif( !empty( $arPriceCode[0] ) ){
                                        if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                            if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                    $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                            $arItem["CATALOG_".$arValue[1]],
                                                            $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                            $convertTo
                                                        ),
                                                        $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                    );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values=$templateValues["#{$field["CODE"]}#"];
                                                    }
                                                                                                   
                                                }
                                                else{
                                                    $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"] *
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                        $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                    );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $templateValues["#{$field["CODE"]}#"];
                                                    }
                                                }
                                            }
                                            if( !in_array( $convertFrom, $this->currencyList ) )
                                                $this->currencyList[] = $convertFrom;
                                        }
                                        else{
                                            if( !in_array( $convertFrom, $this->currencyList ) )
                                                $this->currencyList[] = $convertFrom;
                                        }
                                        if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                            $templateValues["#{$field["CODE"]}#"] += $templateValues["#{$field["CODE"]}#"] *
                                            floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                            $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            if(is_array( $arProductSKU )){
                                                $values = $templateValues["#{$field["CODE"]}#"];
                                            }
                                        }
                                    }
                                    if( is_array( $arProductSKU )&& !is_null( $values ) )
                                        $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;
                                    
                                    if( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) && ( $arItemConfig["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                                        if( isset( $arOfferElementResult[$xmlCode][$field["CODE"]] ) && count( $arOfferElementResult[$xmlCode][$field["CODE"]] ) ){
                                            if( isset( $field["MINIMUM_OFFER_PRICE_CODE"] ) && strlen( $field["MINIMUM_OFFER_PRICE_CODE"] ) ){
                                                $templateValues["#{$field["MINIMUM_OFFER_PRICE_CODE"]}#"] = min( $arOfferElementResult[$xmlCode][$field["CODE"]] );
                                            }
                                        }
                                    }
                                    elseif( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                                    }
                                    break;
                                case 3:
                                    $arItem = $arItemMain;
                                    if( isset( $this->useResolve[$xmlCode] ) ){
                                        $arItem = $this->GetElementProperties( $arElement );
                                    }
                                    if( $arValue[0] == $arItem["IBLOCK_ID"] || $arValue[0] == $arProductSKU["IBLOCK_ID"] ){
                                        if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                            $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                        
                                        if( is_array( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] ) ){
                                            $templateValues["#{$field["CODE"]}#"] = array();
                                            foreach( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] as $val ){
                                                if( ( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 )
                                                    && ( count( $templateValues["#{$field["CODE"]}#"] ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) ){
                                                    $templateValues["#{$field["CODE"]}#"][] = CAcritExportproTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );       
                                                }
                                                else{
                                                    $templateValues["#{$field["CODE"]}#"][] = CAcritExportproTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }                                        
                                        }
                                        else{
                                            $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                    }
                                    $arItem = $arItemMain;
                                    break;
                            }
                        }
                    }
                }  
            }
            else{                                                          
                // field or property
                if( ( $field["TYPE"] == "field" ) 
                    || ( $field["TYPE"] == "composite" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_TRUE_TYPE"] == "field" ) ) ){
                                    
                    if( $field["TYPE"] == "composite" ){                               
                        $compositeValue = "";
                        $compositeTrueDivider = ( strlen( trim( $field["COMPOSITE_TRUE_DIVIDER"] ) ) > 0 ) ? trim( $field["COMPOSITE_TRUE_DIVIDER"] ) : " ";
                        foreach( $field["COMPOSITE_TRUE"] as $compositeFieldIndex => $compositeField ){
                            if( $compositeFieldIndex > 1 ){
                                $compositeValue .= $compositeTrueDivider;
                            }
                            if( $compositeField["COMPOSITE_TRUE_TYPE"] == "const" ){                            
                                $compositeValue .= CAcritExportproTools::RoundNumber( $compositeField["COMPOSITE_TRUE_CONTVALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                            }
                            elseif( $compositeField["COMPOSITE_TRUE_TYPE"] == "field" ){
                                $compositeValueTmp = "";
                                if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                                    $compositeValueTmp = detailLink( $arItem["ID"] );
                                }
                                else{
                                    $arValue = explode( "-", $compositeField["COMPOSITE_TRUE_VALUE"] );
                                            
                                    switch( count( $arValue ) ){
                                        case 1:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){                                            
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( strpos( $compositeField["COMPOSITE_TRUE_VALUE"], "." ) !== false ){
                                                $arField = explode( ".", $compositeField["COMPOSITE_TRUE_VALUE"] );
                                                switch( $arField[0] ){
                                                    case "SECTION":
                                                        $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                        $value = $curSection[$arField[1]] ? : "";
                                                        break;
                                                    default:
                                                        $value = "";
                                                }
                                                unset( $arField );
                                                                                        
                                                $compositeValueTmp = CAcritExportproTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            else{
                                                $compositeValueTmp = CAcritExportproTools::RoundNumber( $arItem[$compositeField["COMPOSITE_TRUE_VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 2:
                                            $values = null;
                                            $compositeValueTmp = $arItem["CATALOG_".$arValue[1]];
                                            $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            if( is_array( $arProductSKU ) ){
                                                $values = $compositeValueTmp;
                                            }                           
                                            if( ( $field["CODE"] == "PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                                preg_match( "#PURCHASING_PRICE#", $arValue[1], $arPriceCode );    
                                            }
                                            else{
                                                preg_match( "#PRICE_[\d]+#", $arValue[1], $arPriceCode );    
                                            }                                                              
                                                                           
                                            $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];         
                                                                                  
                                            if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                                $compositeValueTmp = $convertFrom;
                                                $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values = $compositeValueTmp;
                                                }                                    
                                             
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        $compositeValueTmp = $convertTo;
                                                        $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $compositeValueTmp;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif( !empty( $arPriceCode[0] ) ){
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                            $compositeValueTmp = CAcritExportproTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                    $arItem["CATALOG_".$arValue[1]],
                                                                    $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                    $convertTo
                                                                ),
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }                                            
                                                        }
                                                        else{
                                                            $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }                                                                                                           
                                                        }
                                                    }
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                else{
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $compositeValueTmp += $compositeValueTmp * floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                    $compositeValueTmp = CAcritExportproTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $compositeValueTmp;
                                                    }                                       
                                                }
                                            }
                                            if( is_array( $arProductSKU )&& !is_null( $values ) )
                                                $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;
                                            
                                            break;
                                        case 3:   
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){                                            
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }                                                   
                                            if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                                if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                    $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                                
                                                if( is_array( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] ) ){
                                                    $compositeValueTmp = array();
                                                    foreach( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] as $val ){
                                                        if( ( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 )
                                                            && ( count( $compositeValueTmp ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) ){
                                                            $compositeValueTmp[] = CAcritExportproTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                            
                                                        }
                                                        else{
                                                            $compositeValueTmp[] = CAcritExportproTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }
                                                    
                                                    $compositeValueTmpStr = "";
                                                    if( !empty( $compositeValueTmp ) ){
                                                        foreach( $compositeValueTmp as $compositeValueTmpIndex => $compositeValueTmpItem ){
                                                            if( $compositeValueTmpIndex ){
                                                                $compositeValueTmpStr .= $compositeTrueDivider;
                                                            }
                                                            $compositeValueTmpStr .= $compositeValueTmpItem;
                                                        }
                                                    }
                                                    
                                                    if( strlen( $compositeValueTmpStr ) > 0 ){
                                                        $compositeValueTmp = $compositeValueTmpStr;
                                                    }
                                                }
                                                else{
                                                    $compositeValueTmp = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                    }
                                }
                                $compositeValue .= $compositeValueTmp;
                            }
                        }
                        $templateValues["#{$field["CODE"]}#"] =  CAcritExportproTools::RoundNumber( $compositeValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                    }
                    else{
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
                                    $arItem = $arItemMain;
                                    if( isset( $this->useResolve[$xmlCode] ) ){
                                        $arItem = $this->GetElementProperties( $arElement );
                                    }
                                    if( strpos( $field["VALUE"], "." ) !== false ){
                                        $arField = explode( ".", $field["VALUE"] );
                                        switch( $arField[0] ){
                                            case "SECTION":
                                                $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                $value = $curSection[$arField[1]] ?: "";
                                                break;
                                            default:
                                                $value = "";
                                        }
                                        unset( $arField );
                                        $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                    }
                                    else{
                                        $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $arItem[$field["VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                    }
                                    $arItem = $arItemMain;
                                    break;
                                case 2:
                                    $values = null;
                                    $templateValues["#{$field["CODE"]}#"] = $arItem["CATALOG_".$arValue[1]];
                                    $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                    
                                    if( is_array( $arProductSKU ) ){
                                        $values = $templateValues["#{$field["CODE"]}#"];
                                    }                           
                                    
                                    if( ( $field["CODE"] == "PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                        preg_match( "#PURCHASING_PRICE#", $arValue[1], $arPriceCode );    
                                    }
                                    else{
                                        preg_match( "#PRICE_[\d]+#", $arValue[1], $arPriceCode );    
                                    }                                                              
                                                                   
                                    $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];         
                                                                          
                                    if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                        $templateValues["#{$field["CODE"]}#"] = $convertFrom;
                                        $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        if( is_array( $arProductSKU ) ){
                                            $values = $templateValues["#{$field["CODE"]}#"];
                                        }                                    
                                     
                                        if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                            if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                $templateValues["#{$field["CODE"]}#"] = $convertTo;
                                                $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values = $templateValues["#{$field["CODE"]}#"];
                                                }
                                            }
                                        }
                                    }
                                    elseif( !empty( $arPriceCode[0] ) ){
                                        if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                            if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                    $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                            $arItem["CATALOG_".$arValue[1]],
                                                            $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                            $convertTo
                                                        ),
                                                        $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                    );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $templateValues["#{$field["CODE"]}#"];
                                                    }                                            
                                                }
                                                else{
                                                    $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"] *
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                        $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                    );
                                                    
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $templateValues["#{$field["CODE"]}#"];
                                                    }                                                                                                   
                                                }
                                            }
                                            if( !in_array( $convertFrom, $this->currencyList ) )
                                                $this->currencyList[] = $convertFrom;
                                        }
                                        else{
                                            if( !in_array( $convertFrom, $this->currencyList ) )
                                                $this->currencyList[] = $convertFrom;
                                        }
                                        if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                            $templateValues["#{$field["CODE"]}#"] += $templateValues["#{$field["CODE"]}#"] *
                                            floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                            $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            if( is_array( $arProductSKU ) ){
                                                $values=$templateValues["#{$field["CODE"]}#"];
                                            }                                       
                                        }
                                    }
                                    if( is_array( $arProductSKU )&& !is_null( $values ) )
                                        $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;
                                    
                                    if( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) && ( $arItemConfig["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                                        if( count( $arOfferElementResult[$xmlCode][$field["CODE"]] ) ){
                                            if( isset( $field["MINIMUM_OFFER_PRICE_CODE"] ) && strlen( $field["MINIMUM_OFFER_PRICE_CODE"] ) ){
                                                $templateValues["#{$field["MINIMUM_OFFER_PRICE_CODE"]}#"] = min( $arOfferElementResult[$xmlCode][$field["CODE"]] );
                                            }
                                        }
                                    }
                                    elseif( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                                    }
                                    break;
                                case 3:
                                    $arItem = $arItemMain;
                                    if( isset( $this->useResolve[$xmlCode] ) ){
                                        $arItem = $this->GetElementProperties( $arElement );
                                    }
                                    if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                        if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                            $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                        
                                        if( is_array( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] ) ){
                                            $templateValues["#{$field["CODE"]}#"] = array();
                                            foreach( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] as $val ){
                                                if( ( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 )
                                                    && ( count( $templateValues["#{$field["CODE"]}#"] ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) ){
                                                    $templateValues["#{$field["CODE"]}#"][] = CAcritExportproTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                                else{
                                                    $templateValues["#{$field["CODE"]}#"][] = CAcritExportproTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }                                        
                                        }
                                        else{
                                            $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                    }
                                    $arItem = $arItemMain;
                                    break;
                            }
                        }
                    }
                }
                elseif( ( $field["TYPE"] == "const" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_TRUE_TYPE"] == "const" ) ) ){ // const
                
                    $field["CONTVALUE_TRUE"] = ( $field["TYPE"] == "const" ) ? $field["CONTVALUE_TRUE"] : $field["COMPLEX_TRUE_CONTVALUE"];            
                    $templateValues["#{$field["CODE"]}#"] =  CAcritExportproTools::RoundNumber( $field["CONTVALUE_TRUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                }   
                else{
                    $templateValues["#{$field["CODE"]}#"] = "";
                }
            }                                                      
            
            if( $DB->IsDate( $templateValues["#{$field["CODE"]}#"] ) && ( $this->profile["DATEFORMAT"] == $this->baseDateTimePatern ) ){
                $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( CAcritExportproTools::GetYandexDateTime( $templateValues["#{$field["CODE"]}#"] ), $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                
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
        $arItem = $arItemMain;
        if( ( intval( $templateValues["#OLDPRICE#"] ) > 0 ) 
            && ( intval( $templateValues["#OLDPRICE#"] ) <= intval( $templateValues["#PRICE#"] ) ) ){
            unset( $templateValues["#OLDPRICE#"] );
        }       
        
        array_walk( $templateValues, function( &$value ){
            if( is_array( $value ) ){
                foreach( $value as $id => $val )
                    $value[$id] = $val;
            }
            else
            $value = $value;
        });                                         
        
        // set market category if it checked
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
                if( strlen( trim( $this->profile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]] ) ) <= 0 ){
                    return $arItem;
                }
                
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
            case "tiu_standart":
            case "tiu_standart_vendormodel":
                $templateValues["#MARKET_CATEGORY#"] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]];
                if( !empty( $this->marketCategory ) && !empty($templateValues["#MARKET_CATEGORY#"])){
                    foreach( $this->marketCategory as $arCategoriesItem ){
                        if( $arCategoriesItem["NAME"] == $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]] ){
                            $templateValues["#PORTAL_ID#"] = $arCategoriesItem["PORTAL_ID"];
                            $templateValues["#PORTAL_URL#"] = $arCategoriesItem["PORTAL_URL"];
                            break;
                        }
                    }
                }
                break;
            default:
                if( $this->profile["USE_MARKET_CATEGORY"] == "Y" ){
                    $templateValues["#MARKET_CATEGORY#"] = htmlspecialcharsbx( $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]] );
                }
        }                                                                                                        
                                                           
        // removing tags with empty field values, duplicate tags for multiple properties, url encoding
        foreach( $this->arMatches[2] as $id => $match ){                        
            if( ( $this->profile["XMLDATA"][str_replace( "#", "", $match )]["HTML_TO_TXT"] == "Y" ) && !is_array( $templateValues[$match] ) ){
                $templateValues[$match] = HTMLToTxt( $templateValues[$match] ); 
            }
            
            if( is_array( $this->profile["XMLDATA"][str_replace( "#", "", $match )]["CONVERT_DATA"] ) && !empty( $this->profile["XMLDATA"][str_replace( "#", "", $match )]["CONVERT_DATA"] ) ){
                foreach( $this->profile["XMLDATA"][str_replace( "#", "", $match )]["CONVERT_DATA"] as $arConvertBlock ){
                    $templateValues[$match] = str_replace( $arConvertBlock[0], $arConvertBlock[1], $templateValues[$match] );
                }
            }
            
            if( $this->profile["XMLDATA"][str_replace( "#", "", $match )]["HTML_ENCODE_CUT"] == "Y" ){
                if( !empty( $templateValues[$match] ) ){
                    if( is_array( $templateValues[$match] ) ){
                        foreach( $templateValues[$match] as &$val ){
                            $templateValueCharset = CAcritExportproTools::GetStringCharset( $val );
                            if( $templateValueCharset == "cp1251" ){
                                $convertedTemplateValue = $APPLICATION->ConvertCharset( $val, "cp1251", "utf8" );
                                $convertedTemplateValue = html_entity_decode( $convertedTemplateValue );
                                $val = $APPLICATION->ConvertCharset( $convertedTemplateValue, "utf8", "cp1251" );
                            }
                            else{
                                $val = html_entity_decode( $val );
                            }
                        }
                    }
                    else{
                        $templateValueCharset = CAcritExportproTools::GetStringCharset( $templateValues[$match] );                        
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
            }
            
            if( $this->profile["XMLDATA"][str_replace( "#", "", $match )]["HTML_ENCODE"] == "Y" ){
                if( !empty( $templateValues[$match] ) ){
                    if( is_array( $templateValues[$match] ) ){
                        foreach( $templateValues[$match] as &$val ){
                            $val = htmlspecialcharsbx( $val );
                        }
                    }
                    else{                        
                        $templateValues[$match] = htmlspecialcharsbx( $templateValues[$match] );  
                    }   
                }
            }
            
            if( $this->profile["XMLDATA"][str_replace( "#", "", $match )]["URL_ENCODE"] == "Y" ){
                if( !empty( $templateValues[$match] ) ){
                    if( is_array( $templateValues[$match] ) ){
                        foreach( $templateValues[$match] as &$val ){
                            $val = str_replace( array( " " ), array( "%20" ), $val );
                        }
                    }
                    else{
                       $templateValues[$match] = str_replace( array( " " ), array( "%20" ), $templateValues[$match] );
                    }
                }
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
                            $templateValueCharset = CAcritExportproTools::GetStringCharset( $templateValues[$match] );
                            
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
                                    
        $itemTemplate = preg_replace( "#(<(.+)>\s*</\2>)#i", "", $itemTemplate );  
        
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
            $itemTemplate = preg_replace( "#<delivery-options>.*</delivery-options>#is", "", $itemTemplate ); 
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
                                                                                                                            
        // set values
        $itemTemplate = str_replace( array_keys( $this->defaultFields ), array_values( $this->defaultFields ), $itemTemplate );
        $itemTemplate = str_replace( array_keys( $templateValues ), array_values( $templateValues ), $itemTemplate );
        
        // removes empty first level tags, if there is no nesting
        $itemTemplate = preg_replace( "/(\r\n[\t]*\r\n)/", "\r\n", $itemTemplate );
        $itemTemplate = preg_replace( "/(\r\n\r\n)/", "\r\n", $itemTemplate );
        
        $itemTemplate = preg_replace( "/\s\w+=\"\"/", "", $itemTemplate );
        $itemTemplate = preg_replace( "#(<[^\s]+/>)#i", "", $itemTemplate );  
        $itemTemplate = preg_replace( "#(<(.+)>\s*</\2>)#i", "", $itemTemplate );  
        
        if( !empty( $this->profile["CONVERT_DATA"] ) ){
            foreach( $this->profile["CONVERT_DATA"] as $arConvertBlock ){
                $itemTemplate = str_replace( $arConvertBlock[0], $arConvertBlock[1], $itemTemplate );
            }
        }
                                       
        if( !$skipElement ){
            if( is_array( $_arOfferElementResult ) && count( $_arOfferElementResult ) ){
                $arOfferElementResult = array_merge_recursive( $arOfferElementResult, $_arOfferElementResult );
            }
            $processElementId = ( intval( $arItem["ELEMENT_ID"] ) > 0 ) ? $arItem["ELEMENT_ID"] : $arItem["ID"];
            $dbElementGroups = CIBlockElement::GetElementGroups( $processElementId, true );
            $arItemSections = array();
            while( $arElementGroups = $dbElementGroups->Fetch() ){
                $arItemSections[] = $arElementGroups["ID"];
            }
                
            $this->SaveSections( $arItemSections );
            $this->DemoCountInc();
           
            if( !$this->isVariant( $arItem["IBLOCK_SECTION_ID"] ) ){
                if( isset( $arItemConfig["DELAY_FLUSH"] ) && ( $arItemConfig["DELAY_FLUSH"] === true ) ){
                    CAcritExportproExport::Save( $itemTemplate.$this->delay );
                    $this->delay = "";
                }
                elseif( isset( $arItemConfig["DELAY_SKU"] ) && ( $arItemConfig["DELAY_SKU"] === true ) ){
                    $this->delay .= $itemTemplate;
                    $this->log->IncProductExport();
                }
                elseif( isset( $arItemConfig["DELAY"] ) && ( $arItemConfig["DELAY"] === true ) ){
                    $this->log->IncProductExport();
                }
                else{
                    CAcritExportproExport::Save( $itemTemplate );
                    $this->log->IncProductExport();
                }
            }
        }               
        unset( $arElement, $dbPrices, $arQuantity );
        if( $this->isVariant( $arItem["IBLOCK_SECTION_ID"] ) )
            return array( "ITEM" => $arItem, "XML" => $itemTemplate, "SKIP" => $skipElement, "OFFER" => is_array( $arProductSKU ) );
        return $arItem;
    } 
    
    // searching product offers IB and remove them from list if them active and isset parent product offers IB 
    protected function PrepareIBlock(){
        $excludeIBlock = array();
        $this->catalogSKU = array();
        $ibv = $this->iblockE;
        $ibd = $this->iblockD;
        
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
    
    // get product fields and properties used in template and conditions
    private function GetElementProperties( $arElement ){
        global $DB;         
        
        $arItem = $arElement->GetFields();
        if( in_array( "DETAIL_PICTURE", $this->useFields ) ){
            $arItem["DETAIL_PICTURE"] = CFile::GetPath($arItem["DETAIL_PICTURE"]);
        }
        if( in_array( "PREVIEW_PICTURE", $this->useFields ) ){
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
        
        $arSectionUserFields = CAcritExportproTools::GetIblockUserFields( $arItem["IBLOCK_ID"] );
        if( $arSectionList = $dbSectionList->GetNext() ){
            foreach( $arSectionUserFields as $arSectionUserFieldsItem ){
                if( in_array( $arSectionUserFieldsItem["FIELD_NAME"], $this->useFields ) ){
                    $arItem[$arSectionUserFieldsItem["FIELD_NAME"]] = $arSectionList[$arSectionUserFieldsItem["FIELD_NAME"]];
                    $value = $arSectionList[$arSectionUserFieldsItem["FIELD_NAME"]];
                    if( $this->GetResolveProperties( $arSectionUserFieldsItem, $arSectionUserFieldsItem["FIELD_NAME"], "FIELDS", $value ) ){
                        $arItem[$arSectionUserFieldsItem["FIELD_NAME"]] = $value;
                    }
                }
            }
        }                                    
        
        if( count( $this->useProperties["ID"] ) ){
            $arProperties = $this->GetProperties( $arItem, array( "ID" => $this->useProperties["ID"] ) );
            foreach( $this->useProperties["ID"] as $usePropID ){
                if( !isset( $arProperties[$usePropID] ) ){
                    $arItem["PROPERTY_{$usePropID}_VALUE"] = array();
                }
            }
            
            foreach( $arProperties as $property ){              
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
                elseif( $this->GetResolveProperties( $property, $property["ID"], "PROPERTIES" ) ){
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
            }
            else{
                $dbPrices = CPrice::GetList(
                    array(),
                    array(
                        "PRODUCT_ID" => $arItem["ID"]
                    )
                );
                
                while( $arPrice = $dbPrices->fetch() ){                             
                    if( in_array( "PRICE_".$arPrice["CATALOG_GROUP_ID"]."_WD", $this->usePrices ) ||
                        in_array( "PRICE_".$arPrice["CATALOG_GROUP_ID"]."_D", $this->usePrices ) ){
                        $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arPrice["ID"], array(2), "N", SITE_ID );
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
                        $arItem["CATALOG_PURCHASING_PRICE"] = $arProduct["PURCHASING_PRICE"];
                    }

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
    
    protected function SaveSections( $sections ){                             
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

    protected function SaveCurrencies( $currencies ){
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

    public function GetProperties( $arItem, $arFilter ){
        $props = CIBlockElement::GetProperty( $arItem["IBLOCK_ID"], $arItem["ID"], array(), $arFilter );

        $arAllProps = array();
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
                if( preg_match( "/[;&<>\"]/", $arProp["DESCRIPTION"] ) )
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
    
    protected function GetResolveProperties( &$item, $id, $type, &$value = "" ){
        if( ( $this->xmlCode === false ) || !isset( $this->useResolve[$this->xmlCode][$type][$id] ) ) return false;
        $resolve = $this->useResolve[$this->xmlCode][$type][$id];
           
        switch( $type ){
            case "PROPERTIES":
                if( ( $item["PROPERTY_TYPE"] == "S" ) && ( $item["USER_TYPE"] == "UserID" ) ){
                    $rsUser = CUser::GetByID( $item["VALUE"] );
                    $arUser = $rsUser->Fetch();
                    if( array_key_exists( $resolve, $arUser ) ){
                        $item["VALUE"] = $arUser[$resolve];
                        $item["~VALUE"] = $arUser[$resolve];
                        $item["DISPLAY_VALUE"] = $arUser[$resolve];
                        $item["ORIGINAL_VALUE"] = $arUser[$resolve];
                    }
                    return true;
                } 
                break;    
        }
    }

    protected function AddResolve(){
        foreach( $this->profile["XMLDATA"] as $xmlCode => $field ){
            if( !empty( $field["VALUE"] ) || !empty( $field["CONTVALUE_FALSE"] ) || !empty( $field["CONTVALUE_TRUE"] )
                || !empty( $field["COMPLEX_TRUE_VALUE"] ) || !empty( $field["COMPLEX_FALSE_VALUE"] )
                || !empty( $field["COMPLEX_TRUE_CONTVALUE"] ) || !empty( $field["COMPLEX_FALSE_CONTVALUE"] ) ){        
                    
                $fieldValue = ( $field["TYPE"] == "field" ) ? $field["VALUE"] : $field["COMPLEX_TRUE_VALUE"];
                $arValue = explode( "-", $fieldValue );        
                switch( count( $arValue ) ){
                    case 1:
                        if( !is_null( $field["RESOLVE"] ) && strlen( $field["RESOLVE"] ) > 0 ){
                            $this->useResolve[$xmlCode]["FIELDS"][$arValue[0]] = $field["RESOLVE"];
                        }
                        break;
                    case 2:
                        if( !is_null( $field["RESOLVE"] ) && strlen( $field["RESOLVE"] ) ){
                            $this->useResolve[$xmlCode]["PRICES"][$arValue[1]] = $field["RESOLVE"];
                        }
                        break;
                    case 3:
                        if( !is_null( $field["RESOLVE"] ) && strlen( $field["RESOLVE"] ) ){
                            $this->useResolve[$xmlCode]["PROPERTIES"][$arValue[2]] = $field["RESOLVE"];
                        }        
                        break;
                }
            }
        }
    }
    
    public function SetCronPage( $cronpage ){
        $this->cronpage = $cronpage;
    }
    
    public function SetProcessEnd( $fileExport ){
        global $ProcessEnd;
        $ProcessEnd = true;         
    }
    
    public function SetProcessStart( $fileExport ){
        if( false === $fileExport ) return;
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

class CAcritExportproComponent{
    private $__componentId = false;
    private $__path = false;
    private $__data = false;
    private $__siteDocRoot = false;
    private $__Class = false;

    private static $__components = array(
        "catalog" => "CAcritExportproRemarketingCatalog",
        "catalog.element" => "CAcritExportproRemarketingCatalogElement",
    );
    
    private static $__component_name = array(
        "catalog",
        "catalog.element"
    );

    function __construct( $componentId ){
        $this->__siteDocRoot = \CSite::GetSiteDocRoot( SITE_ID );
    } 

    public function execute(){
        if( $this->__path !== false ){
            global $APPLICATION;

            $__components = \PHPParser::ParseScript( $APPLICATION->GetFileContent( $this->__siteDocRoot.$this->__path ) );
            foreach( $__components as $component ){
                list( $__componentNamecpace, $__componentName ) = explode( ":", trim( $component["DATA"]["COMPONENT_NAME"] ) );
                if( !in_array( $__componentName, static::$__component_name ) )
                    continue;

                $this->__componentId = trim( $component["DATA"]["COMPONENT_NAME"] );
                
                if( !empty( $this->__data["DATA"]["PARAMS"] ) )
                    $component["DATA"]["PARAMS"] = array_merge_recursive( $component["DATA"]["PARAMS"], $this->__data["DATA"]["PARAMS"] );

                $Ob = $this->__initComponent( $component );
                if( !$Ob )
                    continue;
                
                $__component_result = call_user_func( array( $this->__Class, "execute" ), $Ob );
                
                if( !$__component_result )
                    continue;
                
                if( 0 < $__component_result["params"]["ELEMENT_ID"] )
                    return $__component_result;
            }
        } 

        return false;
    }

    public function setPath( $path ){
        $this->__path = $path;

        return $this;
    }

    public function setData( $data ){
        $this->__data = array(
            "DATA" => array(
                "TEMPLATE_NAME" => $data["templateName"],
                "PARAMS" => $data["params"]
            )
        );

        return $this;
    } 

    private function __initComponent( $component ){
        list( $__componentNamecpace, $__componentName ) = explode( ":", trim( $this->__componentId ) );
        $class_name = static::$__components[$__componentName];
        if( !class_exists( $class_name ) ){           
            return false;
        }
        $this->__Class = static::$__components[$__componentName];
            
        $obComponent = new \CBitrixComponent();

        $obComponent->initComponent( $this->__componentId, $component["DATA"]["TEMPLATE_NAME"] );
        $obComponent->arParams = $component["DATA"]["PARAMS"];
        
        return $obComponent;
    }
}

class CAcritExportproRemarketing{
    function OnEndBufferContent( &$bufferContent ){
        if( defined( "ADMIN_SECTION" ) && ( ADMIN_SECTION == true ) )
            return;
        
        $arVersion = explode( ".", phpversion() );
        $version = floatval( implode( ".", array( $arVersion[0], $arVersion[1] ) ) );
        if( $version < 5.4 )
            return;
        
        $arResult = array();
        $profiles = array();
       
        $DB = new CExportproProfileDB();
        $dbRes = $DB->GetList( array(), array( "ACTIVE" => "Y" ) );
        
        if( $dbRes ){
            if( $arRes = $dbRes->Fetch() ){
                $bProfile = $DB->GetByID( $arRes["ID"] );
                if( !array_key_exists( "USE_REMARKETING", $bProfile ) )
                return;
            }
            unset( $dbRes );
            unset( $arRes );
            unset( $bProfile );
        }
        else{
            return;
        }
        
        $dbRes = $DB->GetList( array(), array( "ACTIVE" => "Y", "USE_REMARKETING" => "Y" ) );
        if( $dbRes ){
            if( $arRes = $dbRes->Fetch() ){
                while( $arProfile = $DB->GetByID( $arRes["ID"] ) ){
                    $profiles[$arProfile["ID"]] = $arProfile;
                }
            }
        }
        
        if( !count( $profiles ) )
            return;
        
        $result = self::execute();
        
        if( !$result )
            return;
        
        if( ( !isset( $result["variables"]["SECTION_ID"] ) || ( $result["variables"]["SECTION_ID"] <= 0 ) ) ) {               
            $rsElement = CIBlockElement::GetByID( $result["variables"]["ELEMENT_ID"] );
            $arElement = $rsElement->Fetch();
            $result["variables"]["SECTION_ID"] = $arElement["IBLOCK_SECTION_ID"];
            $result["params"]["SECTION_ID"] = $arElement["IBLOCK_SECTION_ID"];
        }
            
        $arProfileCategory = array();        
        foreach( $profiles as $profile ){
            self::setProfileCategory( $arProfileCategory, $profile, $result );
        }
     
        if( !count( $arProfileCategory ) )
            return;

        $jcode = self::getRemarketingTemplate( $arProfileCategory, $result );
        
        if( strlen( $jcode ) <= 0 )
            return;
        
        $arSettings = self::GetSettings(); 
        $tag = $arSettings["html_tag"]["name"];
        
        if( $arSettings["html_tag"]["position"] == "before" ){
            $bufferContent = preg_replace( "/".str_replace( array( "/" ), array( "\/" ), $tag )."/", $jcode.PHP_EOL.$tag, $bufferContent );
        }
        elseif( $arSettings["html_tag"]["position"] == "after" ){
            $bufferContent = preg_replace( "/".str_replace( array( "/" ), array( "\/" ), $tag )."/", $tag.PHP_EOL.$jcode, $bufferContent );
        }    
    }

    private static function GetSettings(){
        $arSettings = array(
            "html_tag" => array(
                "name" => "<body>",
                "position" => "after" 
            )
        );

        $dbEvents = GetModuleEvents( "acrit.exportpro", "OnGetRemarketingSettings" );
        while( $arEvent = $dbEvents->Fetch() ){
            ExecuteModuleEventEx( $arEvent, array( &$arSettings ) );
        }

        return $arSettings;
    }
    
    private static function setProfileCategory( &$arProfileCategory, $profile, $result ){
        $remarketing_type=self::getRemarketingType( $profile["TYPE"] );
        if( !$remarketing_type )
            return false;
        
        $arCategory = array();
        if( $profile["CHECK_INCLUDE"] <> "Y" )
            $resultSection = CAcritExportproTools::GetSectionNavChain( $result["variables"]["SECTION_ID"] );
        
        foreach( $profile["CATEGORY"] as $category ){
            if( $profile["CHECK_INCLUDE"] <> "Y" ){
                if( in_array( $category, $resultSection ) ){
                    if( in_array( $remarketing_type, $arProfileCategory ) )
                        continue;
                    
                    $arProfileCategory[] = $remarketing_type;
                }
            }
            elseif( $profile["CHECK_INCLUDE"] == "Y" ){
                if( $category == $result["variables"]["SECTION_ID"] ){
                    if( in_array( $remarketing_type, $arProfileCategory ) )
                        continue;
                    
                    $arProfileCategory[] = $remarketing_type;
                }
            }
        }       
    }
    
    private static function getRemarketingType( $type = null, $profile = null ){
        switch( $type ) {
            case "google":
                return "google";
                break;
            case "mailru":
            case "mailru_clothing":
                return "mail";
                break;         
        }
    
        return false;
    }
    
    private static function getRemarketingTemplate( $arProfileCategory, $result ){
        $return = "";
        $arTemplate = array(
            "google" => '
            <script type="text/javascript">    
                var google_tag_params = { 
                  ecomm_pagetype:\'product\',
                  ecomm_prodid:#ID#,
                }; 
            </script>',
            "mail" => '
            <script type="text/javascript">
            var _tmr = _tmr || [];
            _tmr.push({
                type: \'itemView\',
                productid:#ID#,
                pagetype:\'product\',
            });
            </script>'
        );
        
        $dbEvents = GetModuleEvents( "acrit.exportpro", "OnGetRemarketingTemplate" );
        while( $arEvent = $dbEvents->Fetch() ){
            ExecuteModuleEventEx( $arEvent, array( &$arTemplate ) );
        }
          
        foreach( $arProfileCategory as $type ){
            if( array_key_exists( $type, $arTemplate ) )
                $return .= str_replace( array( "#ID#" ), array( $result["params"]["ELEMENT_ID"] ), $arTemplate[$type] ).PHP_EOL;
        }
            
        return  $return;   
    }
  
    private static function _getJCodeType( $arTypes ){
        $return = array();
        foreach( $arTypes as $type ){
            $remarketing_type = getRemarketingType( $type );
            if( !$remarketing_type ) continue;
            $return[] = $remarketing_type;
        }
        
        return array_filter( $return );
    }
    
    private static function execute(){
        $componentsOnPage = CAcritExportproUrlRewrite::getInstance();
        $component= new CAcritExportproComponent();
        
        foreach( $componentsOnPage->getUrlRewrite() as $componentId => $componentVal ){
            if( ( $rule = $componentsOnPage->getRuleByComponentId( $componentId ) ) !== false ){
                $component->setPath( $rule["PATH"] );
                $result = $component->execute();
              
                if( $result !== false )
                    return $result;
            }
        }
        
        return false;
    } 
}

class CAcritExportproRemarketingCatalog{
    protected static function __onPrepareComponentParams( &$params ){}

    protected static function __component( &$component ){
        if( $component->arParams["SEF_MODE"] == "Y" )
            $result = static::__sefMode( $component );
        else
            $result = static::__standartMode( $component );

        if( $result !== false ){
            if( $result["componentPage"] == "element" ){
                if( ( !isset($result["variables"]["ELEMENT_ID"] ) || ( $result["variables"]["ELEMENT_ID"] <= 0 ) ) && strlen( $result["variables"]["ELEMENT_CODE"] ) ){
                    $findFilter = array(
                        "IBLOCK_ID" => $component->arParams["IBLOCK_ID"],
                        "IBLOCK_LID" => SITE_ID,
                        "IBLOCK_ACTIVE" => "Y",
                        "ACTIVE_DATE" => "Y",
                        "CHECK_PERMISSIONS" => "Y",
                        "MIN_PERMISSION" => "R",
                    );
                    
                    if( $component->arParams["SHOW_DEACTIVATED"] !== "Y" )
                        $findFilter["ACTIVE"] = "Y";
                
                    $result["variables"]["ELEMENT_ID"] = \CIBlockFindTools::GetElementID(
                        ( isset( $result["variables"]["ELEMENT_ID"] ) && strlen( $result["variables"]["ELEMENT_ID"] ) ) ? $result["variables"]["ELEMENT_ID"] : false,
                        $result["variables"]["ELEMENT_CODE"],
                        ( isset( $result["variables"]["SECTION_ID"] ) && strlen( $result["variables"]["SECTION_ID"] ) ) ? $result["variables"]["SECTION_ID"] : false,
                        ( isset( $result["variables"]["SECTION_CODE"] ) && strlen( $result["variables"]["SECTION_CODE"] ) ) ? $result["variables"]["SECTION_CODE"] : false,
                        $findFilter
                    );
                }
                
                if( !isset( $result["variables"]["ELEMENT_ID"] ) || ( $result["variables"]["ELEMENT_ID"] <= 0 ) )
                    return false;
                    
                foreach( $result["variables"] as $code => $val )
                    $result["params"][$code] = $val;
            }
            else
                $result = false;
        }

        return $result;
    } 

    public static function execute( \CBitrixComponent $component ){
        static::__onPrepareComponentParams( $component->arParams );
        $result = static::__component( $component );

        return $result;
    } 
    
    private static function __sefMode( &$component ){
        $arVariables = array();

        $smartBase = ( $component->arParams["SEF_URL_TEMPLATES"]["section"] ? $component->arParams["SEF_URL_TEMPLATES"]["section"] : "#SECTION_ID#/" );
        $arDefaultUrlTemplates404 = array(
            "sections" => "",
            "section" => "#SECTION_ID#/",
            "element" => "#SECTION_ID#/#ELEMENT_ID#/",
            "compare" => "compare.php?action=COMPARE",
            "smart_filter" => $smartBase."filter/#SMART_FILTER_PATH#/apply/"
        );

        $arComponentVariables = array(
            "SECTION_ID",
            "SECTION_CODE",
            "ELEMENT_ID",
            "ELEMENT_CODE",
            "action",
        );
        
        $engine = new \CComponentEngine( $component );
        if( Loader::includeModule( "iblock" ) ){
            $engine->addGreedyPart( "#SECTION_CODE_PATH#" );
            $engine->addGreedyPart( "#SMART_FILTER_PATH#" );
            $engine->setResolveCallback( array( "\CIBlockFindTools", "resolveComponentEngine" ) );
        }

        $arUrlTemplates = \CComponentEngine::MakeComponentUrlTemplates( $arDefaultUrlTemplates404, $component->arParams["SEF_URL_TEMPLATES"] );
        $arVariableAliases = \CComponentEngine::MakeComponentVariableAliases( $arDefaultVariableAliases404, $component->arParams["VARIABLE_ALIASES"] );

        $componentPage = $engine->guessComponentPath(
            $component->arParams["SEF_FOLDER"],
            $arUrlTemplates,
            $arVariables
        );

        \CComponentEngine::InitComponentVariables( $componentPage, $arComponentVariables, $arVariableAliases, $arVariables );

        return array(
            "componentPage" => $componentPage,
            "variables" => $arVariables,
            "params" => $component->arParams
        );
    } 

    private static function __standartMode( &$component ){
        $arVariables = array();
        $arDefaultVariableAliases = array();
        $arComponentVariables = array(
            "SECTION_ID",
            "SECTION_CODE",
            "ELEMENT_ID",
            "ELEMENT_CODE",
            "action",
        );
        
        $arVariableAliases = CComponentEngine::MakeComponentVariableAliases( $arDefaultVariableAliases, $component->arParams["VARIABLE_ALIASES"] );
        CComponentEngine::InitComponentVariables( false, $arComponentVariables, $arVariableAliases, $arVariables );
        
        $componentPage = "";
        $arCompareCommands = array(
            "COMPARE",
            "DELETE_FEATURE",
            "ADD_FEATURE",
            "DELETE_FROM_COMPARE_RESULT",
            "ADD_TO_COMPARE_RESULT",
            "COMPARE_BUY",
            "COMPARE_ADD2BASKET",
        );
        
        if( isset( $arVariables["action"] ) && in_array( $arVariables["action"], $arCompareCommands ) )
            $componentPage = "compare";
        elseif( isset( $arVariables["ELEMENT_ID"] ) && intval( $arVariables["ELEMENT_ID"] ) > 0 )
            $componentPage = "element";
        elseif( isset( $arVariables["ELEMENT_CODE"] ) && strlen( $arVariables["ELEMENT_CODE"] ) > 0 )
            $componentPage = "element";
        elseif( isset( $arVariables["SECTION_ID"] ) && intval( $arVariables["SECTION_ID"] ) > 0 )
            $componentPage = "section";
        elseif( isset( $arVariables["SECTION_CODE"] ) && strlen( $arVariables["SECTION_CODE"] ) > 0 )
            $componentPage = "section";
        elseif( isset( $_REQUEST["q"] ) )
            $componentPage = "search";
        else
            $componentPage = "sections";
            
        return array(
            "componentPage" => $componentPage,
            "variables" => $arVariables,
            "params" => $component->arParams
        );
    } 
}

class CAcritExportproRemarketingCatalogElement{
    protected static function __onPrepareComponentParams( &$params ){}
    
    protected static function __component( &$component ){
        if( $component->arParams["SEF_MODE"] == "Y" )
            $result = static::__sefMode( $component );
        else
            $result = static::__standartMode( $component );

        if( $result !== false ){
            if( $result["componentPage"] == "element" ){
                if( ( !isset( $result["variables"]["ELEMENT_ID"] ) || ( $result["variables"]["ELEMENT_ID"] <= 0 ) ) && ( strlen( $result["variables"]["ELEMENT_CODE"] ) > 0 ) ){
                    $findFilter = array(
                        "IBLOCK_ID" => $component->arParams["IBLOCK_ID"],
                        "IBLOCK_LID" => SITE_ID,
                        "IBLOCK_ACTIVE" => "Y",
                        "ACTIVE_DATE" => "Y",
                        "CHECK_PERMISSIONS" => "Y",
                        "MIN_PERMISSION" => "R",
                    );
                    
                    if( $component->arParams["SHOW_DEACTIVATED"] !== "Y" )
                        $findFilter["ACTIVE"] = "Y";
                
                    $result["variables"]["ELEMENT_ID"] = \CIBlockFindTools::GetElementID(
                        ( isset( $result["variables"]["ELEMENT_ID"] ) && strlen( $result["variables"]["ELEMENT_ID"] ) ) ? $result["variables"]["ELEMENT_ID"] : false,
                        $result["variables"]["ELEMENT_CODE"],
                        ( isset( $result["variables"]["SECTION_ID"] ) && strlen( $result["variables"]["SECTION_ID"] ) ) ? $result["variables"]["SECTION_ID"] : false,
                        ( isset( $result["variables"]["SECTION_CODE"] ) && strlen( $result["variables"]["SECTION_CODE"] ) ) ? $result["variables"]["SECTION_CODE"] : false,
                        $findFilter
                    );
                }
                                
                if( !isset( $result["variables"]["ELEMENT_ID"] ) || ( $result["variables"]["ELEMENT_ID"] <= 0 ) )
                    return false;
                    
                foreach( $result["variables"] as $code => $val )
                    $result["params"][$code] = $val;
            }
            else
                $result = false;
        }

        return $result;
    }

    public static function execute( \CBitrixComponent $component ){
        static::__onPrepareComponentParams( $component->arParams );
        $result = static::__component( $component );

        return $result;
    } 

    private static function __sefMode( &$component ){
        return false;
    } 

    private static function __standartMode( &$component ){
        $arVariables = array();
        $arComponentVariables = array(
            "SECTION_ID",
            "SECTION_CODE",
            "ELEMENT_ID",
            "ELEMENT_CODE",
            "action",
        );
        
        $arVariableAliases = array();
        CComponentEngine::InitComponentVariables( false, $arComponentVariables, $arVariableAliases, $arVariables );
        
        $componentPage = "";
        $arCompareCommands = array(
            "ADD_TO_COMPARE_LIST",
        );
        
        if( isset( $arVariables["action"] ) && in_array( $arVariables["action"], $arCompareCommands ) )
            $componentPage = "compare";
        elseif( isset( $arVariables["ELEMENT_ID"] ) && ( intval( $arVariables["ELEMENT_ID"] ) > 0 ) )
            $componentPage = "element";
        elseif( isset( $arVariables["ELEMENT_CODE"] ) && ( strlen( $arVariables["ELEMENT_CODE"] ) > 0 ) )
            $componentPage = "element";
        else
            $componentPage = false;
            
        if( !$componentPage )
            return false;
            
        return array(
            "componentPage" => $componentPage,
            "variables" => $arVariables,
            "params" => $component->arParams
        );
    } 
}
?>