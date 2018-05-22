<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

\Bitrix\Main\Loader::includeModule( "acrit.exportpro" );

Loc::loadMessages( __FILE__ );

class CAcritCML2ExportElement extends CAcritExportproElement{
    private $__arElement;
    private $__dbElements;
    private $catalogItems;
    public $PROPERTY_MAP;
    public $SECTION_MAP;
    public $step;

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

        $this->baseDateTimePatern = "c";
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

    public function GetIBlockElementProperty( &$arValue ){
        if( array_key_exists( $arValue["ID"], $this->ITEM["RESULT"]["PROPERTIES"] ) ){
            $arValue["VALUE"] = $this->ITEM["RESULT"]["PROPERTIES"]["DISPLAY_VALUE"];
        }
    }
    
    public function CMLExportElementGetCatalogProperty( $arElement, &$arCatalogProduct ){
        $arItem["CATALOG_QUANTITY"] = "QUANTITY";
        $arItem["CATALOG_QUANTITY_RESERVED"] = "QUANTITY_RESERVED";
        $arItem["CATALOG_WEIGHT"] = "WEIGHT";
        $arItem["CATALOG_WIDTH"] = "WIDTH";
        $arItem["CATALOG_LENGTH"] = "LENGTH";
        $arItem["CATALOG_HEIGHT"] = "HEIGHT";

        foreach( $arItem as $item => $catalog ){
            if( array_key_exists( $item, $arElement ) && !empty( $arElement[$item] ) && array_key_exists( $catalog, $arCatalogProduct ) ){
                $arCatalogProduct[$catalog] = $arElement[$item];
            }
        }
    }

    public function CMLExportSectionsAddFilterCondition( &$arFilter ){
        $bActive = false;
        $arSections = array();
        if( isset( $this->profile["CONDITION"]["CHILDREN"] ) ){
            foreach( $this->profile["CONDITION"]["CHILDREN"] as $cond ){
                if( $cond["CLASS_ID"] == "CondIBActive" ){
                    if( ( $cond["DATA"]["logic"] == "Equal" ) && ( $cond["DATA"]["value"] == "Y" ) ){
                        $bActive = true;
                        break;
                    }
                }
            }
        }

        if( !$bActive )
            unset( $arFilter["GLOBAL_ACTIVE"] );

        if( $this->step == 1 ){
            if( $this->profile["CHECK_INCLUDE"] != "Y" ){
                $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
                $arSections = $this->profile["CATEGORY"];
            }
            else{
                foreach( $this->profile["CATEGORY"] as $id ){
                    $dbParentSection = CIBlockSection::GetNavChain( false, $id );
                    while( $arParentSection = $dbParentSection->GetNext() ){
                        $arSections[] = $arParentSection["ID"];
                    }
                }
                $arSections = array_unique($arSections);
            }

            $arFilter["ID"] = $arSections;
        }
    }
    
    public function Process( $page = 1, $cronrun = false, $fileType = "xml", $fileExport = false, $fileExportName = false, $arLog = false , &$_ProcessEnd = false ){
        if( $page == 1 ){
            $this->log->Init( $this->profile );
            $this->PrepareStage();
        }
        
        $arStage = $this->GetStage( $page );
        if( $arStage === false )
            return false;

        $this->fileExport = $fileExport;
        $this->step = $arStage["step"];
        $this->stage = $arStage["stage"];
        $this->page = $this->GetNavPage( $page );

        $cml2 = new CAcritCML2Export( $this );
        $cml2->Process( array( "IBLOCK_ID" => current( $this->PrepareIBlock() ), "FILE_EXPORT" => $fileExport ) );

        return true;
    }

    public function GetPrice( &$arPrice ){
        if( !isset( $this->ITEM["RESULT"]["CATALOG"][$arPrice["CATALOG_GROUP_ID"]] ) )
            return;
             
        $arPrice["PRICE"] = $this->ITEM["RESULT"]["CATALOG"][$arPrice["CATALOG_GROUP_ID"]]["PRICE"];
        $arPrice["CURRENCY"] = $this->ITEM["RESULT"]["CATALOG"][$arPrice["CATALOG_GROUP_ID"]]["CURRENCY"];

        if( isset( $this->profile["SETUPTYPE"]["DISCOUNT_PRICE"] ) && ( $this->profile["SETUPTYPE"]["DISCOUNT_PRICE"] == "Y" ) && isset( $this->ITEM["RESULT"]["CATALOG"][$arPrice["CATALOG_GROUP_ID"]]["DISCOUNT_PRICE"] ) ){
            $arPrice["PRICE"] = $this->ITEM["RESULT"]["CATALOG"][$arPrice["CATALOG_GROUP_ID"]]["DISCOUNT_PRICE"];
        }
    }

    public function GetProperty( &$arProp ){
        if( isset( $this->ITEM["RESULT"]["PROPERTIES"][$arProp["ID"]] ) )
            $arProp;
    }

    public function GetList( $arElementOrder = array(), $arElementFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array() ){
        $this->selectFields = $arSelectFields;
        $this->GetListOrder = $arElementOrder;
        $this->GetListFilter = $arElementFilter;
        $this->GetListGroup = $arGroupBy;
        $this->GetListNavStart = $arNavStartParams;
        $this->GetListSelectFields = $arSelectFields;

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
            ExecuteModuleEventEx( $arEvent, array( array( "ID" => $this->profile["ID"], "CODE" => $this->profile["CODE"], "NAME" => $this->profile["NAME"] ), &$eventResult ) );
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

        $iblock = null;
        $this->catalogItems = false;

        if( in_array( $arElementFilter["IBLOCK_ID"], $iblockList ) )
            $iblock = array( $arElementFilter["IBLOCK_ID"] );
        
        if( $this->catalogIncluded ){
            if( $arIBlock = CCatalog::GetByID( $arElementFilter["IBLOCK_ID"]) ){
                if( ( intval( $arIBlock["PRODUCT_IBLOCK_ID"] ) > 0 ) && in_array( $arIBlock["PRODUCT_IBLOCK_ID"], $this->profile["IBLOCK_ID"] ) ){
                    $iblock = array( $arIBlock["PRODUCT_IBLOCK_ID"] );
                    $this->catalogItems = true;
                }
            }
        }

        $arFilter = array(
            "IBLOCK_ID" => $iblock,
            "SECTION_ID" => $this->profile["CATEGORY"],

        );

        if( $this->profile["CHECK_INCLUDE"] != "Y" ){
            $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
        }

        $order = array(
            "iblock_id" => "asc",
            "ID" => "ASC",
        );

        if( is_array( $arElementOrder ) && count( $arElementOrder ) )
            $order = array_merge( $order, $arElementOrder );
        
        $arNavStartParams = array(
            "nPageSize" => $this->stepElements,
            "iNumPage" => $this->page
        );
         
        $dbElements = CIBlockElement::GetList(
            $order,
            $arFilter,
            false,
            $arNavStartParams,
            array()
        );
        
        if( $dbElements->NavPageCount < $this->page )
            $dbElements = false;
             
        $dbSections = CIBlockSection::GetList(
            array(),
            $arFilter = array(
                "IBLOCK_ID" => $iblockList,
                "SECTION_ID" => $this->profile["CATEGORY"]),
            false,
            array()
        );
        
        while( $arSection = $dbSections->GetNext( false, false ) ){
            $this->arSections[$arSection["ID"]] = $arSection;
        }

        $this->__dbElements=$dbElements;
        return $this;
    }
    
    public function Fetch(){
        return $this->__Fetch();
    }
    
    private function __Fetch(){
        $returnFields = array();
        
        $arElement = array_shift( $this->__arElement );
        if( is_null( $arElement ) ){
            $this->__GetNextElement();
            $arElement = array_shift( $this->__arElement );
        }

        if( is_null( $arElement ) ){
            $this->ITEM = null;
            return false;
        }
        $this->ITEM = $arElement;

        if( is_array( $this->selectFields ) && count( $this->selectFields ) ){
            foreach( $this->selectFields as $val ){
                if( array_key_exists( $val, $arElement["ITEM"] ) )
                    $returnFields[$val] = $arElement["ITEM"][$val];

                if( is_array( $arElement["RESULT"] ) && array_key_exists( $val, $arElement["RESULT"] ) )
                    $returnFields[$val] = $arElement["RESULT"][$val];
            }

            return $returnFields;
        }

        if( is_array( $arElement["RESULT"] ) ){
            foreach( $arElement["ITEM"] as $fieldKey => $fieldVal ){
                if( array_key_exists( $fieldKey, $arElement["RESULT"] ) )
                    $returnFields[$fieldKey] = $arElement["RESULT"][$fieldKey];
                else
                    $returnFields[$fieldKey] = $arElement["ITEM"][$fieldKey];
            }

            return $returnFields;
        }

        return $arElement["ITEM"];
    }

    private function __GetNextElement(){
        if( !is_object( $this->__dbElements ) )
            return false;

        while( $arElement = $this->__dbElements->GetNextElement() ){
            $this->__arElement = array();
            $variantItems = array();
            $variantCatalogProducts = array();
            $arOfferElementResult = array();

            $arProcessElement = $this->ProcessElement( $arElement, false, $arOzonCategories, $arElementConfig);
            if( $arProcessElement["SKIP"] )
                continue;

            $arItem = $arProcessElement["ITEM"];

            // if you enable the processing trade offers, we look for and process trade offers
            if( $this->catalogItems && $this->catalogIncluded && ( $this->profile["USE_SKU"] == "Y" ) && ( $this->catalogSKU[$arItem["IBLOCK_ID"]] ) ){
                $arOfferFilter = array(
                    "IBLOCK_ID" => $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"],
                    "PROPERTY_".$this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_PROPERTY_ID"] => $arItem["ID"],
                     
                );
                 
                $dbOfferElements = CIBlockElement::GetList(
                    array(),
                    $arOfferFilter,
                    false,
                    false,
                    array()
                );

                while( $arOfferElement = $dbOfferElements->GetNextElement() ){
                    $arOfferItem = $this->ProcessElement( $arOfferElement, $arItem, $arOzonCategories, $arElementConfig, $arOfferElementResult );
                    if( $arOfferItem["SKIP"] )
                        continue;

                    $this->__arElement[] = $arOfferItem;
                    unset( $arOfferItem );

                    if( $this->isDemo && $this->DemoCount() )
                        break;
                }

                return true;
            }
             
            if( $this->isDemo && $this->DemoCount() )
                break;

            $this->__arElement[]=$arProcessElement;
            return true;
        }

        return false;
    }

    private function ProcessElement( $arElement, $arProductSKU = false, $arOzonCategories = false, $arItemConfig = array(), &$arOfferElementResult = array() ){
        static $arSectionCache;
        global $DB, $APPLICATION;
        $this->AddResolve();
        $skipElement = false;
        $this->xmlCode = false;
        $_arOfferElementResult = array();
        $arItem = $this->GetElementProperties( $arElement );

        $arItemResult["CATALOG"] = $arItem["CATALOG"];
        $this->ConvertCurrency( $arItemResult["CATALOG"] );

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

        if( $this->catalogIncluded ){
            if( !CAcritExportproTools::CheckCondition( $arItem, $this->profile["EVAL_FILTER"] ) ){
                return array( "ITEM" => $arItem, "SKIP" => true, "OFFER" => is_array( $arProductSKU ) );
            }
        }

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
                if( ( $field["TYPE"] == "const" ) || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_FALSE_TYPE"] == "const" ) ) ){
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
                                    $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                    $arItemResult[$field["VALUE"]]= $this->ProcessField( $field, $round, $arItem, $arItemMain );
                                }
                                else{
                                    $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( $arItem[$field["VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                    $arItemResult[$field["VALUE"]]= $this->ProcessField( $field, $round, $arItem, $arItemMain );
                                }
                                $arItem = $arItemMain;
                                break;
                            case 2:
                                $values = null;
                                $templateValues["#{$field["CODE"]}#"] = $arItem["CATALOG_".$arValue[1]];
                                $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );

                                if( is_array( $arProductSKU ) ){
                                    $values = $templateValues["#{$field["CODE"]}#"];
                                }

                                if( ( $field["CODE"] == "PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                    preg_match( "#PURCHASING_PRICE#", $arValue[1], $arPriceCode );
                                }
                                else{
                                    preg_match( "#PRICE_[\d]+#", $arValue[1], $arPriceCode );
                                }
                                
                                preg_match( "#[\d]+#", $arPriceCode[0], $arPriceId );
                                $arItemResult["CATALOG"][$arPriceId[0]] = array_merge( $arItem["CATALOG"][$arPriceId[0]], array( "PRICE" => $round ) );

                                $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];

                                if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                    $templateValues["#{$field["CODE"]}#"] = $convertFrom;
                                    $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );

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
                                                $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                        $arItem["CATALOG_".$arValue[1]],
                                                        $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                        $convertTo
                                                    ),
                                                    $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                );

                                                $arItemResult["CATALOG"][$arPriceId[0]] = array_merge( $arItem["CATALOG"][$arPriceId[0]], array( "PRICE" => $round, "CURRENCY" => $convertTo ) );

                                                if( is_array( $arProductSKU ) ){
                                                    $values = $templateValues["#{$field["CODE"]}#"];
                                                }
                                            }
                                            else{
                                                $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"] *
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                    $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                    $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                );

                                                $arItemResult["CATALOG"][$arPriceId[0]] = array_merge( $arItem["CATALOG"][$arPriceId[0]], array( "PRICE" => $round, "CURRENCY" => $convertTo ) );

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
                                        $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );

                                        $arItemResult["CATALOG"][$arPriceId[0]] = array_merge( $arItem["CATALOG"][$arPriceId[0]], array( "PRICE" => $round ) );

                                        if( is_array( $arProductSKU ) ){
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
                                elseif( isset( $field["MINIMUM_OFFER_PRICE"] ) && $field["MINIMUM_OFFER_PRICE"] == "Y" ){}
                                
                                break;
                            case 3:
                                $arItem = $arItemMain;
                                if( isset( $this->useResolve[$xmlCode] ) ){
                                    $arItem = $this->GetElementProperties( $arElement );
                                }
                                if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                    if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                        $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = $round = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        $arItemResult["PROPERTIES"][$arValue[2]] = array_merge( $arItem["PROPERTIES"][$arValue[2]], array( "DISPLAY_VALUE" => $this->ProcessField( $field, $round, $arItem, $arItemMain ) ) );
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
                                        $arItemResult["PROPERTIES"][$arValue[2]] = array_merge( $arItem["PROPERTIES"][$arValue[2]], array( "DISPLAY_VALUE" => $this->ProcessField( $field, $templateValues["#{$field["CODE"]}#"], $arItem, $arItemMain ) ) );
                                    }
                                    else{
                                        $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        $arItemResult["PROPERTIES"][$arValue[2]] = array_merge( $arItem["PROPERTIES"][$arValue[2]], array( "DISPLAY_VALUE" => $this->ProcessField( $field, $round, $arItem, $arItemMain ) ) );
                                    }
                                }
                                $arItem = $arItemMain;
                                break;
                        }
                    }
                }
            }
            else{
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

                                        $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        $arItemResult[$field["VALUE"]]= $this->ProcessField( $field, $round, $arItem, $arItemMain );
                                    }
                                    else{
                                        $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( $arItem[$field["VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        $arItemResult[$field["VALUE"]]= $this->ProcessField( $field, $round, $arItem, $arItemMain );
                                    }
                                    $arItem = $arItemMain;
                                    break;
                                case 2:
                                    $values = null;
                                    $templateValues["#{$field["CODE"]}#"] = $arItem["CATALOG_".$arValue[1]];
                                    $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );

                                    if( is_array( $arProductSKU ) ){
                                        $values = $templateValues["#{$field["CODE"]}#"];
                                    }
                                    
                                    if( ( $field["CODE"] == "PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                        preg_match( "#PURCHASING_PRICE#", $arValue[1], $arPriceCode );
                                    }
                                    else{
                                        preg_match( "#PRICE_[\d]+#", $arValue[1], $arPriceCode );
                                    }
                                    
                                    preg_match( "#[\d]+#", $arPriceCode[0], $arPriceId );
                                    $arItemResult["CATALOG"][$arPriceId[0]] = array_merge( $arItem["CATALOG"][$arPriceId[0]], array( "PRICE" => $round ) );
                                    $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];

                                    if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                        $templateValues["#{$field["CODE"]}#"] = $convertFrom;
                                        $templateValues["#{$field["CODE"]}#"] = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        if(is_array( $arProductSKU )){
                                            $values = $templateValues["#{$field["CODE"]}#"];
                                        }
                                         
                                        if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                            if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                $templateValues["#{$field["CODE"]}#"] = $convertTo;
                                                $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
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
                                                    $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                            $arItem["CATALOG_".$arValue[1]],
                                                            $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                            $convertTo
                                                        ),
                                                        $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                    );

                                                    $arItemResult["CATALOG"][$arPriceId[0]] = array_merge($arItem["CATALOG"][$arPriceId[0]], array( "PRICE" => $round, "CURRENCY" => $convertTo ) );

                                                    if(is_array( $arProductSKU )){
                                                        $values = $templateValues["#{$field["CODE"]}#"];
                                                    }
                                                }
                                                else{
                                                    $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"] *
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                        $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                        $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                    );
                                                     
                                                    $arItemResult["CATALOG"][$arPriceId[0]] = array_merge( $arItem["CATALOG"][$arPriceId[0]], array( "PRICE" => $round, "CURRENCY" => $convertTo ) );

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
                                            $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( $templateValues["#{$field["CODE"]}#"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );

                                            $arItemResult["CATALOG"][$arPriceId[0]] = array_merge( $arItem["CATALOG"][$arPriceId[0]], array( "PRICE" => $round ) );

                                            if( is_array( $arProductSKU ) ){
                                                $values = $templateValues["#{$field["CODE"]}#"];
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
                                    if( $arValue[0] == $arItem["IBLOCK_ID"] || $arValue[0] == $arProductSKU["IBLOCK_ID"] ){
                                        if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                            $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = $round = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            $arItemResult["PROPERTIES"][$arValue[2]] = array_merge( $arItem["PROPERTIES"][$arValue[2]], array( "DISPLAY_VALUE" => $this->ProcessField( $field, $round, $arItem, $arItemMain ) ) );
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

                                            $arItemResult["PROPERTIES"][$arValue[2]] = array_merge( $arItem["PROPERTIES"][$arValue[2]], array( "DISPLAY_VALUE" => $this->ProcessField( $field, $templateValues["#{$field["CODE"]}#"], $arItem, $arItemMain ) ) );
                                        }
                                        else{
                                            $templateValues["#{$field["CODE"]}#"] = $round = CAcritExportproTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            $arItemResult["PROPERTIES"][$arValue[2]] = array_merge( $arItem["PROPERTIES"][$arValue[2]], array( "DISPLAY_VALUE" => $this->ProcessField( $field, $round, $arItem, $arItemMain ) ) );
                                        }
                                    }
                                    $arItem = $arItemMain;
                                    break;
                            }
                        }
                }
                elseif( ( $field["TYPE"] == "const" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_TRUE_TYPE"] == "const" ) ) ){
                    
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

             
        if( is_array( $_arOfferElementResult ) && count( $_arOfferElementResult ) ){
            $arOfferElementResult = array_merge_recursive( $arOfferElementResult, $_arOfferElementResult );
        }
        $processElementId = ( intval( $arItem["ELEMENT_ID"] ) > 0 ) ? $arItem["ELEMENT_ID"] : $arItem["ID"];
        $dbElementGroups = CIBlockElement::GetElementGroups( $processElementId, true );
        $arItemSections = array();
        while( $arElementGroups = $dbElementGroups->Fetch() ){
            $arItemSections[] = $arElementGroups["ID"];
        }

        if( !$skipElement ){
            $this->DemoCountInc();
            $this->log->IncProductExport();
        }
        unset( $arElement, $dbPrices, $arQuantity );

        $arExclusion["DISPLAY_PROPERTIES"] = array();
        $arExclusion["PROPERTIES"] = array();
        $arExclusion["CATALOG"] = array();
        
        return array( "ITEM" => array_diff( $arItem, $arExclusion ), "RESULT" => $arItemResult, "PROPERTIES" => $arItem["PROPERTIES"], "CATALOG" => $arItem["CATALOG"], "SKIP" => $skipElement, "OFFER" => is_array( $arProductSKU ), "SECTIONS" => $arItemSections );
    }

    private function GetElementProperties( $arElement){
        global $DB;

        $arItem = $arElement->GetFields();
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
                    $value=$arSectionList[$arSectionUserFieldsItem["FIELD_NAME"]];
                    if( $this->GetResolveProperties( $arSectionUserFieldsItem, $arSectionUserFieldsItem["FIELD_NAME"], "FIELDS", $value ) ){
                        $arItem[$arSectionUserFieldsItem["FIELD_NAME"]] = $value;
                    }

                }
            }
        }

        if( count( $this->useProperties["ID"] ) ){
            $arProperties = $this->GetProperties( $arItem, array( "ID" => $this->useProperties["ID"] ) );
            foreach( $this->useProperties["ID"] as $usePropID )
                if( !isset( $arProperties[$usePropID] ) ){
                    $arItem["PROPERTY_{$usePropID}_VALUE"] = array();
            }

            foreach( $arProperties as $PIND => $property ){
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

                $arItem["PROPERTIES"][$property["ID"]] = $property;

                if( count( $this->useProperties["ID"] ) && in_array( $property["ID"], $this->useProperties["ID"] ) ){
                    $arItem["PROPERTY_{$property["ID"]}_DISPLAY_VALUE"] = $property["DISPLAY_VALUE"];
                    $arItem["PROPERTY_{$property["CODE"]}_DISPLAY_VALUE"] = $arItem["PROPERTY_{$property["ID"]}_VALUE"];
                    $arItem["PROPERTY_{$property["ID"]}_VALUE"] = $property["ORIGINAL_VALUE"];
                    $arItem["PROPERTY_{$property["CODE"]}_VALUE"] = $arItem["PROPERTY_{$property["ID"]}_VALUE"];
                }
            }
        }
        if( $this->catalogIncluded ){
            $arProduct = CCatalogProduct::GetByID( $arItem["ID"] );
            $arItem["CATALOG"]["PURCHASING_PRICE"] = array( "PRICE" => $arProduct["PURCHASING_PRICE"], "CURRENCY" => $arProduct["PURCHASING_CURRENCY"] );
            
            $dbPrices = CPrice::GetList(
                array(),
                array(
                    "PRODUCT_ID" => $arItem["ID"]
                )
            );

            while( $arPrice = $dbPrices->fetch() ){
                $arItem["CATALOG"][$arPrice["CATALOG_GROUP_ID"]] = array( "PRICE" => $arPrice["PRICE"], "CURRENCY" => $arPrice["CURRENCY"] );
                if( isset( $this->profile["SETUPTYPE"]["DISCOUNT_PRICE"] ) && ( $this->profile["SETUPTYPE"]["DISCOUNT_PRICE"] == "Y" ) ){
                    $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arPrice["ID"], array( 2 ), "N", SITE_ID );
                    $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                        $arPrice["PRICE"],
                        $arPrice["CURRENCY"],
                        $arDiscounts
                    );
                    $arItem["CATALOG"][$arPrice["CATALOG_GROUP_ID"]]["DISCOUNT_PRICE"] = $discountPrice;
                }

                if( in_array( "PRICE_".$arPrice["CATALOG_GROUP_ID"]."_WD", $this->usePrices ) ||
                    in_array( "PRICE_".$arPrice["CATALOG_GROUP_ID"]."_D", $this->usePrices ) ){
                        $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arPrice["ID"], array( 2 ), "N", SITE_ID );
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
                    $arItem["CATALOG_PURCHASING_PRICE_CURRENCY"] = $arProduct["PURCHASING_CURRENCY"];
                }

                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}"] = $arPrice["PRICE"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_WD"] = $discountPrice;
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_D"] = $discount;
                $arItem["CATALOG_PRICE{$arPrice["CATALOG_GROUP_ID"]}"] = $arPrice["PRICE"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_CURRENCY"] = $arPrice["CURRENCY"];
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

    private function ProcessField( $field, &$value, &$arItem = array(), &$arItemMain = array() ){
        if( $field["HTML_TO_TXT"] == "Y" ){
            $value = HTMLToTxt( $value );
            $typeField = $field["VALUE"]."_TYPE";
            
            if( array_key_exists( $typeField, $arItem ) )
                $arItem[$typeField] = "text";
            if( array_key_exists( $typeField, $arItemMain ) )
                $arItemMain[$typeField] = "text";
        }

        if( $field["HTML_ENCODE_CUT"] == "Y" ){
            if( !empty( $value ) ){
                if( is_array( $value) ){
                    foreach( $value as &$val ){
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
                    $templateValueCharset = CAcritExportproTools::GetStringCharset( $value );
                    if( $templateValueCharset == "cp1251" ){
                        $convertedTemplateValue = $APPLICATION->ConvertCharset( $value, "cp1251", "utf8" );
                        $convertedTemplateValue = html_entity_decode( $convertedTemplateValue );
                        $value = $APPLICATION->ConvertCharset( $convertedTemplateValue, "utf8", "cp1251" );
                    }
                    else{
                        $value = html_entity_decode( $value );
                    }
                }
                
                $typeField = $field["VALUE"]."_TYPE";
                if( array_key_exists( $typeField, $arItem ) )
                    $arItem[$typeField] = "html";
                    
                if( array_key_exists( $typeField, $arItemMain ) )
                    $arItemMain[$typeField] = "html";
            }
        }

        if( $field["HTML_ENCODE"] == "Y" ){
            if( !empty( $value ) ){
                if( is_array( $value ) ){
                    foreach( $value as &$val ){
                        $val = htmlspecialcharsbx( $val );
                    }
                }
                else{
                    $value = htmlspecialcharsbx( $value );
                }

                $typeField = $field["VALUE"]."_TYPE";
                if( array_key_exists( $typeField, $arItem ) )
                    $arItem[$typeField] = "html";
                    
                if( array_key_exists( $typeField, $arItemMain ) )
                        $arItemMain[$typeField] = "html";
            }
        }

        if( $field["URL_ENCODE"] == "Y" ){
            if( !empty( $value ) ){
                if( is_array( $value ) ){
                    foreach( $value as &$val ){
                        $val = str_replace( array( " " ), array( "%20" ), $val );
                    }
                }
                else{
                    $value = str_replace( array( " " ), array( "%20" ), $value );
                }
            }
        }

        if( $field["CONVERT_CASE"] == "Y" ){
            if( is_array( $value ) ){
                foreach( $value as &$val ){
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
                $arTmp = explode( ".", $value );

                foreach( $arTmp as &$tmpStr ){
                    $tmpStr = ToLower( trim( $tmpStr ) );
                    $strWords = explode( " ", $tmpStr );
                    if( ( strlen( $strWords[0] ) > 0 ) && ( count( $strWords ) > 1 ) ){
                        $templateValueCharset = CAcritExportproTools::GetStringCharset( $value );

                        if( $templateValueCharset == "cp1251" ){
                            $strWords[0] = mb_convert_case( $strWords[0], MB_CASE_TITLE, "WINDOWS-1251" );
                        }
                        else{
                            $strWords[0] = mb_convert_case( $strWords[0], MB_CASE_TITLE );
                        }
                    }
                    $tmpStr = implode( " ", $strWords );
                }
                $value = implode( ". ", $arTmp );
            }
        }

        if( intval( $field["TEXT_LIMIT"] ) > 0 ){
            $value = TruncateText( $value, $field["TEXT_LIMIT"] );
        }

        return  $value;
    }

    private function PrepareStage( $arIBlock = array(), $UseSKU = fasle ){
        $excludeIBlock = array();
        $offersIBlock = array();
        $productElements = null;
        $offersElements = null;
        $productPages = 0;
        $offersPages = 0;
        $totalPages = 0;
        $arFilter = array();
        $arFilter["SECTION_ID"] =  $this->profile["CATEGORY"];

        if( $this->profile["CHECK_INCLUDE"] != "Y" ){
            $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
        }

        if( isset( $this->profile["CONDITION"]["CHILDREN"] ) ){
            foreach( $this->profile["CONDITION"]["CHILDREN"] as $cond ){
                if( $cond["CLASS_ID"] == "CondIBActive" ){
                    if( ( $cond["DATA"]["logic"] == "Equal" ) && ( $cond["DATA"]["value"] == "Y" ) ){
                        $arFilter["ACTIVE"] = "Y";
                        break;
                    }
                }
            }
        }

        $sessionData = AcritExportproSession::GetSessionPage( $this->profile["ID"], 1 );

        foreach( $this->profile["IBLOCK_ID"] as $iblocID ){
            if( $this->catalogIncluded ){
                if( $arIBlock = CCatalog::GetByID( $iblocID ) ){
                    if( intval( $arIBlock["PRODUCT_IBLOCK_ID"] ) > 0 && in_array( $arIBlock["PRODUCT_IBLOCK_ID"], $this->profile["IBLOCK_ID"] ) )
                        $excludeIBlock[] = $arIBlock["IBLOCK_ID"];

                    if( intval( $arIBlock["OFFERS_IBLOCK_ID"] ) > 0 )
                        $offersIBlock[] = $arIBlock["OFFERS_IBLOCK_ID"];
                }
            }
        }

        $productIBlock = array_diff( $this->profile["IBLOCK_ID"], $excludeIBlock );
        if( count( $productIBlock ) ){
            $productElements = CIBlockElement::GetList( array(), array_merge( array( "IBLOCK_ID" => $productIBlock ), $arFilter ), array() );
            if( $this->stepElements < $productElements ){
                $productPages = $productElements / $this->stepElements;
                $productPages = ceil( $productPages );
            }
            else{
                $productPages = 1;
            } 
        }

        $sectionPages = 1;
        if( $this->profile["USE_SKU"] == "Y" && count( $offersIBlock ) ){
            $totalPages = $sectionPages + $productPages + $productPages + 2;
        }
        else{
            $totalPages=$sectionPages + $productPages + 1;
        }
        
        $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["SECTION_PAGES"]["START"] = 1;
        $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["SECTION_PAGES"]["END"] = 1;

        $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["PRODUCT_PAGES"]["START"] = $sectionPages + 1;
        $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["PRODUCT_PAGES"]["END"] = $sectionPages + $productPages + 1;

        if( $this->profile["USE_SKU"] == "Y" && count( $offersIBlock ) ){
            $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["OFFERS_PAGES"]["START"] = $sectionPages + $productPages + 2;
            $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["OFFERS_PAGES"]["END"] = $sectionPages + $productPages + $productPages + 2;
        }
        $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["STEPS"]= $this->isDemo ? 1 :$totalPages;

        AcritExportproSession::SetSessionPage( $this->profile["ID"], $sessionData, 1 );
    }

    private function GetNavPage( $page ){
        $sessionData = AcritExportproSession::GetSessionPage( $this->profile["ID"], 1 );
        $sectionPages = $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["SECTION_PAGES"];

        if( isset( $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["PRODUCT_PAGES"] ) )
            $productPages = $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["PRODUCT_PAGES"];

        if( isset( $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["OFFERS_PAGES"] ) )
            $offersPages = $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["OFFERS_PAGES"];

        if( ( $page >= $sectionPages["START"] ) && ( $page <= $sectionPages["END"] ) )
            return $page;

        if( is_array( $productPages ) && ( $page >= $productPages["START"] ) && ( $page <= $productPages["END"] ) )
            return $page-$sectionPages["END"];

        if( is_array( $offersPages ) && ( $page >= $offersPages["START"] ) && ( $page <= $offersPages["END"] ) )
            return $page-$productPages["END"];

        return false;
    }

    function GetAllStage(){
        $arStages = array();

        $sessionData = AcritExportproSession::GetSessionPage( $this->profile["ID"], 1 );

        $arStages["SECTION_PAGES"] = $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["SECTION_PAGES"];

        if( isset( $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["PRODUCT_PAGES"] ) )
            $arStages["PRODUCT_PAGES"] = $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["PRODUCT_PAGES"];

        if( isset( $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["OFFERS_PAGES"] ) )
            $arStages["OFFERS_PAGES"] = $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["OFFERS_PAGES"];

        return $arStages;
    }

    private function GetStage( $page ){
        $productPages = false;
        $offersPages = false;

        $sessionData = AcritExportproSession::GetSessionPage( $this->profile["ID"], 1 );
        $sectionPages = $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["SECTION_PAGES"];

        if( isset( $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["PRODUCT_PAGES"] ) )
            $productPages = $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["PRODUCT_PAGES"];

        if( isset( $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["OFFERS_PAGES"] ) )
            $offersPages = $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["OFFERS_PAGES"];

        if( ( $page >= $sectionPages["START"] ) && ( $page <= $sectionPages["END"] ) ){
            if( $page == $sectionPages["START"] )
                return array( "step" => 1, "stage" => "start_export" );
            elseif( $page == $sectionPages["END"] ){
                if( $productPages == false )
                    return array( "step" => 1, "stage" => "end" );
                else
                    return array( "step" => 1, "stage" => "end_export" );
            }
            else
                return array( "step" => 1, "stage" => "continue" );
        }

        if( is_array( $productPages ) && ( $page >= $productPages["START"] ) && ( $page <= $productPages["END"] ) ){
            if( $page == $productPages["START"] )
                return  array( "step" => 2, "stage" => "start_export" );
            elseif( $page == $productPages["END"] ){
                if( $offersPages == false )
                    return array( "step" => 2, "stage" => "end" );
                else
                    return array( "step" => 2, "stage" => "end_export" );
            }
            else
                return array( "step" => 2, "stage" => "continue" );
        }

        if( is_array( $offersPages ) && ( $page >= $offersPages["START"] ) && ( $page <= $offersPages["END"] ) ){
            if( $page == $offersPages["START"] )
                return array( "step" => 3, "stage" => "start_export" );
            elseif( $page == $offersPages["END"] )
                return array( "step" => 3, "stage" => "end_export" );
            else
                return array( "step" => 3, "stage" => "continue" );
        }

        return false;
    }
    
    public function SetStepParams( $arNames, $obExport = null ){
        $sessionData = AcritExportproSession::GetSessionPage( $this->profile["ID"], 1 );
        foreach( $arNames as $name ){
            switch( $name ){
                case "next_step":
                    if( is_object( $obExport ) )
                        $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["STEP_PARAMS"]["next_step"] = $obExport->next_step;
                    break;
                case "PROPERTY_MAP":
                    $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["STEP_PARAMS"]["PROPERTY_MAP"] = $this->PROPERTY_MAP;
                    break;
                case "SECTION_MAP":
                    $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["STEP_PARAMS"]["SECTION_MAP"] = $this->SECTION_MAP;
                    break;
            }

        }
        AcritExportproSession::SetSessionPage( $this->profile["ID"], $sessionData, 1 );

    }

    public function GetStepParams( $name ){
        $sessionData = AcritExportproSession::GetSessionPage( $this->profile["ID"], 1 );
        
        return $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["STEP_PARAMS"][$name];
    }

    private function ConvertCurrency( &$arPrices ){
        $precision = 2;
        $mode = "UP";

        if( isset( $this->profile["CURRENCY"]["CONVERT_CURRENCY_PRECISSION"] ) && is_numeric( $this->profile["CURRENCY"]["CONVERT_CURRENCY_PRECISSION"] ) ){
            $precision = $this->profile["CURRENCY"]["CONVERT_CURRENCY_PRECISSION"];
        }

        if( isset( $this->profile["CURRENCY"]["CONVERT_CURRENCY_MODE"] ) && strlen( $this->profile["CURRENCY"]["CONVERT_CURRENCY_MODE"] ) ){
            $mode = $this->profile["CURRENCY"]["CONVERT_CURRENCY_MODE"];
        }

        if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
            foreach( $arPrices as &$arPrice ){
                if( is_null( $arPrice["PRICE"] ) )
                    continue;

                $convertFrom = $arPrice["CURRENCY"];
                if( is_null( $convertFrom ) )
                    continue;

                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                    $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                    if( is_null( $convertTo ) )
                        continue;

                    if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                        $arPrice["PRICE"] = CAcritExportproTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                            $arPrice["PRICE"],
                            $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                            $convertTo
                            ),
                            $precision, $mode
                        );
                        if( array_key_exists( "DISCOUNT_PRICE", $arPrice ) ){
                            $arPrice["DISCOUNT_PRICE"] = CAcritExportproTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                    $arPrice["DISCOUNT_PRICE"],
                                    $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                    $convertTo
                                ),
                                $precision, $mode
                            );
                        }
                        $arPrice["CURRENCY"] = $convertTo;
                    }
                    else{
                        $arPrice["PRICE"] = CAcritExportproTools::RoundNumber( $arPrice["PRICE"] *
                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                            $precision, $mode
                        );
                        if( array_key_exists( "DISCOUNT_PRICE", $arPrice ) ){
                            $arPrice["DISCOUNT_PRICE"] = CAcritExportproTools::RoundNumber( $arPrice["DISCOUNT_PRICE"] *
                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                $precision, $mode
                            );
                        }
                        $arPrice["CURRENCY"] = $convertTo;
                    }
                }
            }
        }
    }

    public function Save( $fp, $data ){
        if( !isset( $this->fileExport ) )
            return false;

        if( !empty( $this->profile["CONVERT_DATA"] ) ){
            foreach( $this->profile["CONVERT_DATA"] as $arConvertBlock ){
                $data = str_replace( $arConvertBlock[0], $arConvertBlock[1], $data );
            }
        }

        $fp = fopen( $this->fileExport, "ab" );
        if( flock( $fp, LOCK_EX ) ){
            fwrite( $fp, $data );
            fflush( $fp );
            flock( $fp, LOCK_UN );
        }
        fclose($fp);

        return true;
    }
}

class CAcritCML2{
    var $fp = null;
    var $IBLOCK_ID = false;
    var $bExtended = false;
    var $work_dir = false;
    var $file_dir = false;
    var $next_step = false;
    var $arIBlock = false;
    var $prices = false;
    var $only_price = false;
    var $download_files = true;
    var $export_as_url = false;
    var $PRODUCT_IBLOCK_ID = false;

    function Init( $fp, $IBLOCK_ID, $next_step, $bExtended=false, $work_dir=false, $file_dir=false, $bCheckPermissions = true, $PRODUCT_IBLOCK_ID = false ){
        $this->fp = $fp;
        $this->IBLOCK_ID = intval( $IBLOCK_ID );
        $this->bExtended = $bExtended;
        $this->work_dir = $work_dir;
        $this->file_dir = $file_dir;
        $this->next_step = $next_step;
        $this->only_price = false;
        $this->download_files = true;
        $this->PRODUCT_IBLOCK_ID = intval( $PRODUCT_IBLOCK_ID );

        $arFilter = array(
            "ID" => $this->IBLOCK_ID,
            "MIN_PERMISSION" => "W",
        );
        if( !$bCheckPermissions )
            $arFilter["CHECK_PERMISSIONS"] = "N";

        $rsIBlock = CIBlock::GetList( array(), $arFilter );
        if( ( $this->arIBlock = $rsIBlock->Fetch() ) && ( $this->arIBlock["ID"] == $this->IBLOCK_ID ) ){
            $this->next_step["catalog"] = CModule::IncludeModule( "catalog" );
            if( $this->next_step["catalog"] ){
                $rs = CCatalog::GetList( array(), array( "IBLOCK_ID" => $this->arIBlock["ID"] ) );
                if( $rs->Fetch() ){
                    $this->next_step["catalog"] = true;
                    $this->prices = array();
                    $rsPrice = CCatalogGroup::GetList( array(), array() );
                    while( $arPrice = $rsPrice->Fetch() ){
                        $this->prices[$arPrice["ID"]] = $arPrice["NAME"];
                    }
                }
                else{
                    $this->next_step["catalog"] = false;
                }
            }
            return true;
        }
        else
            return false;
    }

    function DoNotDownloadCloudFiles(){
        $this->download_files = false;
    }

    function NotCatalog(){
        $this->next_step["catalog"] = false;
    }

    function ExportFileAsURL(){
        $this->export_as_url = true;
    }

    function GetIBlockXML_ID( $IBLOCK_ID, $XML_ID = false ){
        if( $XML_ID === false ){
            $IBLOCK_ID = intval( $IBLOCK_ID );
            if( $IBLOCK_ID > 0 ){
                $obIBlock = new CIBlock();
                $rsIBlock = $obIBlock->GetList( array(), array( "ID" => $IBLOCK_ID ) );
                if( $arIBlock = $rsIBlock->Fetch() )
                    $XML_ID = $arIBlock["XML_ID"];
                else
                    return "";
            }
            else
                return "";
        }
        if( strlen( $XML_ID ) <= 0 ){
            $XML_ID = $IBLOCK_ID;
            $obIBlock = new CIBlock();
            $rsIBlock = $obIBlock->GetList( array(), array( "XML_ID" => $XML_ID ) );
            while( $rsIBlock->Fetch() ){
                $XML_ID = md5( uniqid( mt_rand(), true ) );
                $rsIBlock = $obIBlock->GetList( array(), array( "XML_ID" => $XML_ID ) );
            }
            $obIBlock->Update( $IBLOCK_ID, array( "XML_ID" => $XML_ID ) );
        }
        
        return $XML_ID;
    }

    function GetSectionXML_ID( $IBLOCK_ID, $SECTION_ID, $XML_ID = false ){
        if( $XML_ID === false ){
            $obSection = new CIBlockSection();
            $rsSection = $obSection->GetList( array(), array( "IBLOCK_ID" => $IBLOCK_ID, "ID" => $SECTION_ID ), false, array( "ID", "XML_ID" ) );
            if( $arSection = $rsSection->Fetch() ){
                $XML_ID = $arSection["XML_ID"];
            }
        }
        if( strlen( $XML_ID ) <= 0 ){
            $XML_ID = $SECTION_ID;
            $obSection = new CIBlockSection();
            $rsSection = $obSection->GetList( array(), array( "IBLOCK_ID" => $IBLOCK_ID, "EXTERNAL_ID" => $XML_ID ), false, array( "ID" ) );
            while( $rsSection->Fetch() ){
                $XML_ID = md5( uniqid( mt_rand(), true ) );
                $rsSection = $obSection->GetList( array(), array( "IBLOCK_ID" => $IBLOCK_ID, "EXTERNAL_ID" => $XML_ID ), false, array( "ID" ) );
            }
            $obSection->Update( $SECTION_ID, array( "XML_ID" => $XML_ID ), false, false );
        }
        
        return $XML_ID;
    }

    function GetElementXML_ID( $IBLOCK_ID, $ELEMENT_ID, $XML_ID = false ){
        if( $XML_ID === false ){
            $arFilter = array(
                "ID" => $ELEMENT_ID,
                "SHOW_HISTORY"=>"Y",
            );
            
            if( $IBLOCK_ID > 0 )
                $arFilter["IBLOCK_ID"] = $IBLOCK_ID;
                
            $obElement = new CIBlockElement();
            $rsElement = $obElement->GetList(
                array( "ID" => "asc" ),
                $arFilter,
                false,
                false,
                array( "ID", "XML_ID" )
            );
            
            if( $arElement = $rsElement->Fetch() ){
                $XML_ID = $arElement["XML_ID"];
            }
        }
        
        return $XML_ID;
    }

    function GetPropertyXML_ID( $IBLOCK_ID, $NAME, $PROPERTY_ID, $XML_ID ){
        if( strlen( $XML_ID ) <= 0 ){
            $XML_ID = $PROPERTY_ID;
            $obProperty = new CIBlockProperty();
            $rsProperty = $obProperty->GetList( array(), array( "IBLOCK_ID" => $IBLOCK_ID, "XML_ID" => $XML_ID ) );
            while( $rsProperty->Fetch() ){
                $XML_ID = md5( uniqid( mt_rand(), true ) );
                $rsProperty = $obProperty->GetList( array(), array( "IBLOCK_ID" => $IBLOCK_ID, "XML_ID" => $XML_ID ) );
            }
            $obProperty->Update( $PROPERTY_ID, array( "NAME" => $NAME, "XML_ID" => $XML_ID ) );
        }
        
        return $XML_ID;
    }

    function StartExport(){
        $this->obProfile->Save( $this->fp, str_replace( array_keys( $this->obProfile->defaultFields ), array_values( $this->obProfile->defaultFields ), "<"."?xml version=\"1.0\" encoding=\"#ENCODING#\"?".">\n" ) );
        $this->obProfile->Save( $this->fp, str_replace( array_keys( $this->obProfile->defaultFields ), array_values( $this->obProfile->defaultFields ), "<".GetMessage( "IBLOCK_XML2_COMMERCE_INFO" )." ".GetMessage( "IBLOCK_XML2_SCHEMA_VERSION" )."=\"2.021\" ".GetMessage( "IBLOCK_XML2_TIMESTAMP" )."=\"#DATE#\">\n" ) );
    }

    function ExportFile( $FILE_ID ){
        if( $this->work_dir ){
            $arFile = CFile::GetFileArray( $FILE_ID );
            if( $arFile ){
                if( ( !$this->download_files ) && ( $arFile["HANDLER_ID"] > 0 ) ){
                    return array(
                        GetMessage( "IBLOCK_XML2_BX_ORIGINAL_NAME" ) => $arFile["ORIGINAL_NAME"],
                        GetMessage( "IBLOCK_XML2_DESCRIPTION" ) => $arFile["DESCRIPTION"],
                        GetMessage( "IBLOCK_XML2_BX_URL" ) => urldecode($arFile["SRC"]),
                        GetMessage( "IBLOCK_XML2_BX_FILE_SIZE" ) => $arFile["FILE_SIZE"],
                        GetMessage( "IBLOCK_XML2_BX_FILE_WIDTH" ) => $arFile["WIDTH"],
                        GetMessage( "IBLOCK_XML2_BX_FILE_HEIGHT" ) => $arFile["HEIGHT"],
                        GetMessage( "IBLOCK_XML2_BX_FILE_CONTENT_TYPE" ) => $arFile["CONTENT_TYPE"],
                    );
                }
                else{
                    $arTempFile = CFile::MakeFileArray( $FILE_ID );
                    if( isset( $arTempFile["tmp_name"] ) && ( $arTempFile["tmp_name"] <> "" ) ){
                        $strFile = $arFile["SUBDIR"]."/".$arFile["FILE_NAME"];
                        $strNewFile = str_replace( "//", "/", $this->work_dir.$this->file_dir.$strFile );
                        CheckDirPath( $strNewFile );

                        if( @copy( $arTempFile["tmp_name"], $strNewFile ) )
                            return $this->file_dir.$strFile;
                    }
                }
            }
        }
        elseif( $this->export_as_url ){
            $arFile = CFile::GetFileArray( $FILE_ID );
            if( $arFile )
                return CHTTP::URN2URI( $arFile["SRC"] );
        }

        return "";
    }

    function ExportEnum( $arUserField, $value ){
        static $cache = array();
        if( !isset( $cache[$value] ) ){
            $obEnum = new CUserFieldEnum();
            $rsEnum = $obEnum->GetList(
                array(),
                array(
                    "USER_FIELD_ID" => $arUserField["ID"],
                    "ID" => $value,
                )
            );
            
            $cache[$value] = $rsEnum->Fetch();
        }
        return $cache[$value]["XML_ID"];
    }

    function formatXMLNode( $level, $tagName, $value ){
        if( is_array( $value ) ){
            $xmlValue = "";
            foreach( $value as $k => $v ){
                if( $k )
                    $xmlValue .= "\n".rtrim( $this->formatXMLNode( $level + 1, $k, $v ), "\n" );
            }
            $xmlValue .= "\n".str_repeat( "\t", $level );
        }
        else{
            $xmlValue = htmlspecialcharsbx( $value );
        }

        return str_repeat( "\t", $level )."<".$tagName.">".$xmlValue."</".$tagName.">\n";
    }

    function StartExportMetadata(){
        $xml_id = $this->GetIBlockXML_ID( $this->arIBlock["ID"], $this->arIBlock["XML_ID"] );
        $this->arIBlock["XML_ID"] = $xml_id;
        $this->obProfile->Save( $this->fp, "\t<".GetMessage( "IBLOCK_XML2_METADATA" ).">\n" );
        $this->obProfile->Save( $this->fp, $this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_ID" ), $xml_id ) );
        $this->obProfile->Save( $this->fp, $this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_NAME" ), $this->arIBlock["NAME"] ) );
        if( strlen( $this->arIBlock["DESCRIPTION"] ) > 0 )
            $this->obProfile->Save( $this->fp, $this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_DESCRIPTION" ), FormatText( $this->arIBlock["DESCRIPTION"], $this->arIBlock["DESCRIPTION_TYPE"] ) ) );
    }

    function ExportSectionsProperties( $arUserFields ){
        if( empty( $arUserFields ) )
            return;

        $this->obProfile->Save( $this->fp, "\t\t<".GetMessage( "IBLOCK_XML2_GROUPS_PROPERTIES" ).">\n" );
        foreach( $arUserFields as $FIELD_ID => $arField ){
            $this->obProfile->Save( $this->fp, "\t\t\t<".GetMessage( "IBLOCK_XML2_PROPERTY" ).">\n" );
            $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_ID" ).">".htmlspecialcharsbx( $arField["XML_ID"] )."</".GetMessage( "IBLOCK_XML2_ID" ).">\n" );
            $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_NAME" ).">".htmlspecialcharsbx( $FIELD_ID )."</".GetMessage( "IBLOCK_XML2_NAME" ).">\n" );
            $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_SORT" ).">".htmlspecialcharsbx( $arField["SORT"] )."</".GetMessage( "IBLOCK_XML2_SORT" ).">\n" );
            $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_MULTIPLE" ).">".( $arField["MULTIPLE"] == "Y" ? "true": "false" )."</".GetMessage( "IBLOCK_XML2_MULTIPLE" ).">\n" );
            $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_BX_PROPERTY_TYPE" ).">".htmlspecialcharsbx( $arField["USER_TYPE_ID"] )."</".GetMessage( "IBLOCK_XML2_BX_PROPERTY_TYPE" ).">\n" );
            $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_BX_IS_REQUIRED" ).">".( $arField["MANDATORY"] == "Y" ? "true": "false" )."</".GetMessage( "IBLOCK_XML2_BX_IS_REQUIRED" ).">\n" );
            $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_BX_FILTER" ).">".( $arField["SHOW_FILTER"] == "Y" ? "true": "false" )."</".GetMessage( "IBLOCK_XML2_BX_FILTER" ).">\n" );
            $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_BX_SHOW_IN_LIST" ).">".( $arField["SHOW_IN_LIST"] == "Y" ? "true": "false" )."</".GetMessage( "IBLOCK_XML2_BX_SHOW_IN_LIST" ).">\n" );
            $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_BX_EDIT_IN_LIST" ).">".( $arField["EDIT_IN_LIST"] == "Y" ? "true": "false" )."</".GetMessage( "IBLOCK_XML2_BX_EDIT_IN_LIST" ).">\n" );
            $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_BX_SEARCH" ).">".( $arField["IS_SEARCHABLE"] == "Y" ? "true": "false" )."</".GetMessage( "IBLOCK_XML2_BX_SEARCH" ).">\n" );
            $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_BX_SETTINGS" ).">".htmlspecialcharsbx( serialize( $arField["SETTINGS"] ) )."</".GetMessage( "IBLOCK_XML2_BX_SETTINGS" ).">\n" );

            if( is_callable( array( $arField["USER_TYPE"]["CLASS_NAME"], "getlist" ) ) ){
                $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_CHOICE_VALUES" ).">\n" );
                $rsEnum = call_user_func_array(
                    array( $arField["USER_TYPE"]["CLASS_NAME"], "getlist" ),
                    array(
                        $arField,
                    )
                );
                
                while( $arEnum = $rsEnum->GetNext() ){
                    $this->obProfile->Save(
                        $this->fp,
                        "\t\t\t\t\t<".GetMessage( "IBLOCK_XML2_CHOICE" ).">\n"
                        .$this->formatXMLNode( 6, GetMessage( "IBLOCK_XML2_ID" ), $arEnum["XML_ID"] )
                        .$this->formatXMLNode( 6, GetMessage( "IBLOCK_XML2_VALUE" ), $arEnum["VALUE"] )
                        .$this->formatXMLNode( 6, GetMessage( "IBLOCK_XML2_BY_DEFAULT" ), ( $arEnum["DEF"] == "Y" ? "true": "false" ) )
                        .$this->formatXMLNode( 6, GetMessage( "IBLOCK_XML2_SORT" ), intval( $arEnum["SORT"] ) )
                        ."\t\t\t\t\t</".GetMessage( "IBLOCK_XML2_CHOICE" ).">\n"
                    );
                }

                $this->obProfile->Save( $this->fp, "\t\t\t\t</".GetMessage( "IBLOCK_XML2_CHOICE_VALUES" ).">\n" );
            }
            $this->obProfile->Save( $this->fp, "\t\t\t</".GetMessage( "IBLOCK_XML2_PROPERTY" ).">\n" );
        }
        $this->obProfile->Save( $this->fp, "\t\t</".GetMessage( "IBLOCK_XML2_GROUPS_PROPERTIES" ).">\n" );
    }

    function ExportSections( &$SECTION_MAP, $start_time, $INTERVAL, $FILTER = "", $PROPERTY_MAP = array() ){
        /** @var CUserTypeManager $USER_FIELD_MANAGER */
        global $USER_FIELD_MANAGER;

        $counter = 0;
        if( !array_key_exists( "CURRENT_DEPTH", $this->next_step ) )
            $this->next_step["CURRENT_DEPTH"] = 0;
        else // this makes second "step"
            return $counter;

        $arUserFields = $USER_FIELD_MANAGER->GetUserFields( "IBLOCK_".$this->arIBlock["ID"]."_SECTION" );
        
        foreach( $arUserFields as $FIELD_ID => $arField )
            if( strlen( $arField["XML_ID"] ) <= 0 )
                $arUserFields[$FIELD_ID]["XML_ID"] = $FIELD_ID;

        if( $this->bExtended )
            $this->ExportSectionsProperties( $arUserFields );

        $SECTION_MAP = array();

        if( $FILTER === "none" )
            return 0;
                     
        $arFilter = array(
            "IBLOCK_ID" => $this->arIBlock["ID"],
            "GLOBAL_ACTIVE" => "Y",
            "CHECK_PERMISSIONS" => "N",
        );

        if( $FILTER === "all" )
            unset( $arFilter["GLOBAL_ACTIVE"] );

        $this->obProfile->CMLExportSectionsAddFilterCondition( $arFilter );

        $rsSections = CIBlockSection::GetList( array( "left_margin" => "asc" ), $arFilter, false, array( "UF_*" ) );
        $this->obProfile->Save( $this->fp, "\t\t<".GetMessage( "IBLOCK_XML2_GROUPS" ).">\n" );
        while( $arSection = $rsSections->Fetch() ){
            $white_space = str_repeat( "\t\t", $arSection["DEPTH_LEVEL"] );
            $level = ( $arSection["DEPTH_LEVEL"] + 1 ) * 2;

            while( $this->next_step["CURRENT_DEPTH"] >= $arSection["DEPTH_LEVEL"] ){
                $this->obProfile->Save( $this->fp, str_repeat( "\t\t", $this->next_step["CURRENT_DEPTH"] )."\t\t</".GetMessage( "IBLOCK_XML2_GROUPS" ).">\n" );
                $this->obProfile->Save( $this->fp, str_repeat( "\t\t", $this->next_step["CURRENT_DEPTH"] - 1 )."\t\t\t</".GetMessage( "IBLOCK_XML2_GROUP" ).">\n" );
                $this->next_step["CURRENT_DEPTH"]--;
            }

            $xml_id = $this->GetSectionXML_ID( $this->arIBlock["ID"], $arSection["ID"], $arSection["XML_ID"] );
            $SECTION_MAP[$arSection["ID"]] = $xml_id;

            $this->obProfile->Save(
                $this->fp,
                $white_space."\t<".GetMessage( "IBLOCK_XML2_GROUP" ).">\n"
                .$this->formatXMLNode( $level, GetMessage( "IBLOCK_XML2_ID" ), $xml_id )
                .$this->formatXMLNode( $level, GetMessage( "IBLOCK_XML2_NAME" ), $arSection["NAME"] )
            );
            
            if( strlen( $arSection["DESCRIPTION"] ) > 0 )
                $this->obProfile->Save( $this->fp, $white_space."\t\t<".GetMessage( "IBLOCK_XML2_DESCRIPTION" ).">".htmlspecialcharsbx( FormatText( $arSection["DESCRIPTION"], $arSection["DESCRIPTION_TYPE"] ) )."</".GetMessage( "IBLOCK_XML2_DESCRIPTION" ).">\n" );
                
            if( $this->bExtended ){
                $this->obProfile->Save(
                    $this->fp,
                    $this->formatXMLNode( $level, GetMessage( "IBLOCK_XML2_BX_ACTIVE" ), ( $arSection["ACTIVE"] == "Y" ? "true" : "false" ) )
                    .$this->formatXMLNode( $level, GetMessage( "IBLOCK_XML2_BX_SORT" ), intval( $arSection["SORT"] ) )
                    .$this->formatXMLNode( $level, GetMessage( "IBLOCK_XML2_BX_CODE" ), $arSection["CODE"] )
                    .$this->formatXMLNode( $level, GetMessage( "IBLOCK_XML2_BX_PICTURE" ), $this->ExportFile( $arSection["PICTURE"] ) )
                    .$this->formatXMLNode( $level, GetMessage( "IBLOCK_XML2_BX_DETAIL_PICTURE" ), $this->ExportFile( $arSection["DETAIL_PICTURE"] ) )
                );

                if( !empty( $arUserFields ) ){
                    $this->obProfile->Save( $this->fp, $white_space."\t\t<".GetMessage( "IBLOCK_XML2_PROPERTIES_VALUES" ).">\n" );
                    foreach( $arUserFields as $FIELD_ID => $arField ){
                        $this->obProfile->Save( $this->fp, $white_space."\t\t\t<".GetMessage( "IBLOCK_XML2_PROPERTY_VALUES" ).">\n" );
                        $this->obProfile->Save( $this->fp, $this->formatXMLNode( $level + 2, GetMessage( "IBLOCK_XML2_ID" ), $arField["XML_ID"] ) );

                        $values = array();
                        if( !is_array( $arSection[$FIELD_ID] ) ){
                            if( $arField["USER_TYPE"]["BASE_TYPE"] === "file" )
                                $values[] = $this->ExportFile( $arSection[$FIELD_ID] );
                            elseif( $arField["USER_TYPE"]["BASE_TYPE"] === "enum" )
                                $values[] = $this->ExportEnum( $arField, $arSection[$FIELD_ID] );
                            else
                                $values[] = $arSection[$FIELD_ID];
                        }
                        elseif( empty( $arSection[$FIELD_ID] ) ){
                            $values[] = "";
                        }
                        else{
                            foreach( $arSection[$FIELD_ID] as $value ){
                                if( $arField["USER_TYPE"]["BASE_TYPE"] === "file" )
                                    $values[] = $this->ExportFile( $value );
                                elseif( $arField["USER_TYPE"]["BASE_TYPE"] === "enum" )
                                    $values[] = $this->ExportEnum( $arField, $value );
                                else
                                    $values[] = $value;
                            }
                        }

                        foreach( $values as $value ){
                            $this->obProfile->Save( $this->fp, $this->formatXMLNode( $level + 2, GetMessage( "IBLOCK_XML2_VALUE" ), $value ) );
                        }
                        $this->obProfile->Save( $this->fp, $white_space."\t\t\t</".GetMessage( "IBLOCK_XML2_PROPERTY_VALUES" ).">\n" );
                    }
                    $this->obProfile->Save( $this->fp, $white_space."\t\t</".GetMessage( "IBLOCK_XML2_PROPERTIES_VALUES" ).">\n" );
                }
                $this->ExportSmartFilter( $level, $this->arIBlock["ID"], $arSection["ID"], $PROPERTY_MAP );

                $sectionTemplates = new \Bitrix\Iblock\InheritedProperty\SectionTemplates( $this->arIBlock["ID"], $arSection["ID"] );
                $this->exportInheritedTemplates( $arSection["DEPTH_LEVEL"] * 2 + 2, $sectionTemplates );
            }
            $this->obProfile->Save( $this->fp, $white_space."\t\t<".GetMessage( "IBLOCK_XML2_GROUPS" ).">\n" );

            $this->next_step["CURRENT_DEPTH"] = $arSection["DEPTH_LEVEL"];
            $counter++;
        }

        while( $this->next_step["CURRENT_DEPTH"] > 0 ){
            $this->obProfile->Save( $this->fp, str_repeat( "\t\t", $this->next_step["CURRENT_DEPTH"] )."\t\t</".GetMessage( "IBLOCK_XML2_GROUPS" ).">\n" );
            $this->obProfile->Save( $this->fp, str_repeat( "\t\t", $this->next_step["CURRENT_DEPTH"] - 1 )."\t\t\t</".GetMessage( "IBLOCK_XML2_GROUP" ).">\n" );
            $this->next_step["CURRENT_DEPTH"]--;
        }
        $this->obProfile->Save( $this->fp, "\t\t</".GetMessage( "IBLOCK_XML2_GROUPS" ).">\n" );

        return $counter;
    }

    function ExportProperties( &$PROPERTY_MAP ){
        $PROPERTY_MAP = array();
        $this->obProfile->Save( $this->fp, "\t\t<".GetMessage( "IBLOCK_XML2_PROPERTIES" ).">\n" );

        if( $this->bExtended ){
            $arElementFields = array(
                "CML2_ACTIVE" => GetMessage( "IBLOCK_XML2_BX_ACTIVE" ),
                "CML2_CODE" => GetMessage( "IBLOCK_XML2_SYMBOL_CODE" ),
                "CML2_SORT" => GetMessage( "IBLOCK_XML2_SORT" ),
                "CML2_ACTIVE_FROM" => GetMessage( "IBLOCK_XML2_START_TIME" ),
                "CML2_ACTIVE_TO" => GetMessage( "IBLOCK_XML2_END_TIME" ),
                "CML2_PREVIEW_TEXT" => GetMessage( "IBLOCK_XML2_ANONS" ),
                "CML2_DETAIL_TEXT" => GetMessage( "IBLOCK_XML2_DETAIL" ),
                "CML2_PREVIEW_PICTURE" => GetMessage( "IBLOCK_XML2_PREVIEW_PICTURE" ),
            );

            foreach( $arElementFields as $key => $value ){
                $this->obProfile->Save($this->fp, $this->formatXMLNode( 3, GetMessage( "IBLOCK_XML2_PROPERTY" ), array(
                    GetMessage( "IBLOCK_XML2_ID" ) => $key,
                    GetMessage( "IBLOCK_XML2_NAME" ) => $value,
                    GetMessage( "IBLOCK_XML2_MULTIPLE" ) => "false",
                ) ) );
            }
        }

        $arFilter = array(
            "IBLOCK_ID" => $this->arIBlock["ID"],
            "ACTIVE" => "Y",
        );
        
        $arSort = array(
            "sort" => "asc",
        );

        $obProp = new CIBlockProperty();
        $rsProp = $obProp->GetList( $arSort, $arFilter );
        while( $arProp = $rsProp->Fetch() ){
            $this->obProfile->Save( $this->fp, "\t\t\t<".GetMessage( "IBLOCK_XML2_PROPERTY" ).">\n" );

            $xml_id = $this->GetPropertyXML_ID( $this->arIBlock["ID"], $arProp["NAME"], $arProp["ID"], $arProp["XML_ID"] );
            $PROPERTY_MAP[$arProp["ID"]] = $xml_id;
            $PROPERTY_MAP["~".$arProp["ID"]] = $arProp["NAME"];
            $this->obProfile->Save( $this->fp, $this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_ID" ), $xml_id ) );

            $this->obProfile->Save( $this->fp, $this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_NAME" ), $arProp["NAME"] ) );
            $this->obProfile->Save( $this->fp, $this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_MULTIPLE" ), ( $arProp["MULTIPLE"] == "Y" ? "true" : "false" ) ) );
            if( $arProp["PROPERTY_TYPE"] == "L" ){
                $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_CHOICE_VALUES" ).">\n" );
                $rsEnum = CIBlockProperty::GetPropertyEnum( $arProp["ID"] );
                while( $arEnum = $rsEnum->Fetch() ){
                    $this->obProfile->Save( $this->fp, $this->formatXMLNode( 5, GetMessage( "IBLOCK_XML2_VALUE" ), $arEnum["VALUE"] ) );
                    if( $this->bExtended ){
                        $this->obProfile->Save(
                            $this->fp,
                            "\t\t\t\t\t<".GetMessage( "IBLOCK_XML2_CHOICE" ).">\n"
                            .$this->formatXMLNode( 6, GetMessage( "IBLOCK_XML2_ID" ), $arEnum["XML_ID"] )
                            .$this->formatXMLNode( 6, GetMessage( "IBLOCK_XML2_VALUE" ), $arEnum["VALUE"] )
                            .$this->formatXMLNode( 6, GetMessage( "IBLOCK_XML2_BY_DEFAULT" ), ( $arEnum["DEF"] == "Y" ? "true": "false" ) )
                            .$this->formatXMLNode( 6, GetMessage( "IBLOCK_XML2_SORT" ), intval( $arEnum["SORT"] ) )
                            ."\t\t\t\t\t</".GetMessage( "IBLOCK_XML2_CHOICE" ).">\n"
                        );
                    }
                }
                $this->obProfile->Save( $this->fp, "\t\t\t\t</".GetMessage( "IBLOCK_XML2_CHOICE_VALUES" ).">\n" );
            }

            if( $this->bExtended ){
                $strUserSettings = "";
                if( "" != $arProp["USER_TYPE"] ){
                    if( !empty( $arProp["USER_TYPE_SETTINGS"] ) && is_array( $arProp["USER_TYPE_SETTINGS"] ) ){
                        $strUserSettings = $this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_USER_TYPE_SETTINGS" ), serialize( $arProp["USER_TYPE_SETTINGS"] ) );
                    }
                }
                $this->obProfile->Save(
                    $this->fp,
                    $this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_SORT" ), intval( $arProp["SORT"] ) )
                    .$this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_CODE" ), $arProp["CODE"] )
                    .$this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_PROPERTY_TYPE" ), $arProp["PROPERTY_TYPE"] )
                    .$this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_ROWS" ), $arProp["ROW_COUNT"] )
                    .$this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_COLUMNS" ), $arProp["COL_COUNT"] )
                    .$this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_LIST_TYPE" ), $arProp["LIST_TYPE"] )
                    .$this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_FILE_EXT" ), $arProp["FILE_TYPE"] )
                    .$this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_FIELDS_COUNT" ), $arProp["MULTIPLE_CNT"] )
                    .$this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_LINKED_IBLOCK" ), $this->GetIBlockXML_ID( $arProp["LINK_IBLOCK_ID"] ) )
                    .$this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_WITH_DESCRIPTION" ), ( $arProp["WITH_DESCRIPTION"] == "Y" ? "true" : "false" ) )
                    .$this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_SEARCH" ), ( $arProp["SEARCHABLE"] == "Y" ? "true": "false" ) )
                    .$this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_FILTER" ), ( $arProp["FILTRABLE"] == "Y" ? "true" : "false" ) )
                    .$this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_USER_TYPE" ), $arProp["USER_TYPE"] )
                    .$this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_IS_REQUIRED" ), ( $arProp["IS_REQUIRED"] == "Y" ? "true" : "false" ) )
                    .$this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_BX_DEFAULT_VALUE" ), serialize( $arProp["DEFAULT_VALUE"] ) )
                    .$this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_SERIALIZED" ), 1 )
                    .$strUserSettings
                );
            }
            $this->obProfile->Save( $this->fp, "\t\t\t</".GetMessage( "IBLOCK_XML2_PROPERTY" ).">\n" );
        }
        $this->obProfile->Save( $this->fp, "\t\t</".GetMessage( "IBLOCK_XML2_PROPERTIES" ).">\n" );

        if( $this->bExtended ){
            $catalog = false;
            if( CModule::IncludeModule( "catalog" ) ){
                $catalog = CCatalogSKU::getInfoByOfferIBlock( $this->arIBlock["ID"] );
            }

            if( !empty( $catalog ) && is_array( $catalog ) ){
                $this->ExportSmartFilter( 2, $this->arIBlock["ID"], false, $PROPERTY_MAP, $catalog["PRODUCT_IBLOCK_ID"] );
            }
            else{
                $this->ExportSmartFilter( 2, $this->arIBlock["ID"], 0, $PROPERTY_MAP );
            }
        }
    }

    function ExportSmartFilter( $level, $iblockId, $sectionId = false, $PROPERTY_MAP, $productIblockId = 0 ){
        $propertyLinksBySection = array();
        if( $sectionId === false ){
            $propertyLinksBySection[0] = CIBlockSectionPropertyLink::GetArray( $iblockId, 0 );
            foreach( $propertyLinksBySection[0] as $PID => $arLink ){
                if( ( $arLink["INHERITED"] != "N" ) || !array_key_exists( $PID, $PROPERTY_MAP ) ){
                    unset( $propertyLinksBySection[0][$PID] );
                }
                else{
                    if( $productIblockId > 0 ){
                        $iblock_xml_id = $this->GetIBlockXML_ID( $productIblockId, CIBlock::GetArrayByID( $productIblockId, "XML_ID" ) );
                        $propertyLinksBySection[0][$PID]["IBLOCK_XML_ID"] = $iblock_xml_id;
                    }
                }
            }

            $arFilter = array(
                "IBLOCK_ID" => $productIblockId ? $productIblockId: $iblockId,
                "CHECK_PERMISSIONS" => "N",
            );
                
            $rsSections = CIBlockSection::GetList( array( "left_margin"=>"asc" ), $arFilter, false, array( "ID", "XML_ID", "IBLOCK_ID" ) );
            while( $arSection = $rsSections->Fetch() ){
                $section_xml_id = $this->GetSectionXML_ID( $arSection["IBLOCK_ID"], $arSection["ID"], $arSection["XML_ID"] );
                $iblock_xml_id = $this->GetIBlockXML_ID( $arSection["IBLOCK_ID"], CIBlock::GetArrayByID( $arSection["IBLOCK_ID"], "XML_ID" ) );

                $propertyLinksBySection[$arSection["ID"]] = CIBlockSectionPropertyLink::GetArray( $iblockId, $arSection["ID"] );
                foreach( $propertyLinksBySection[$arSection["ID"]] as $PID => $arLink ){
                    if( ( $arLink["INHERITED"] != "N" ) || !array_key_exists( $PID, $PROPERTY_MAP ) ){
                        unset( $propertyLinksBySection[$arSection["ID"]][$PID] );
                    }
                    else{
                        $propertyLinksBySection[$arSection["ID"]][$PID]["IBLOCK_XML_ID"] = $iblock_xml_id;
                        $propertyLinksBySection[$arSection["ID"]][$PID]["SECTION_XML_ID"] = $section_xml_id;
                    }
                }
            }
        }
        else{
            $propertyLinksBySection[$sectionId] = CIBlockSectionPropertyLink::GetArray( $iblockId, $sectionId );
            foreach( $propertyLinksBySection[$sectionId] as $PID => $arLink ){
                if( ( $arLink["INHERITED"] != "N" ) || !array_key_exists( $PID, $PROPERTY_MAP ) )
                    unset( $propertyLinksBySection[$sectionId][$PID] );
            }
        }

        $first = true;
        foreach( $propertyLinksBySection as $arPropLink ){
            if( !empty( $arPropLink ) ){
                if( $first ){
                    $this->obProfile->Save( $this->fp, str_repeat( "\t", $level )."<".GetMessage( "IBLOCK_XML2_SECTION_PROPERTIES" ).">\n" );
                    $first = false;
                }

                foreach( $arPropLink as $PID => $arLink ){
                    $xmlLink = array(
                        GetMessage( "IBLOCK_XML2_ID" ) => $PROPERTY_MAP[$PID],
                        GetMessage( "IBLOCK_XML2_SMART_FILTER" ) => ( $arLink["SMART_FILTER"] == "Y"? "true": "false" ),
                        GetMessage( "IBLOCK_XML2_SMART_FILTER_DISPLAY_TYPE" ) => $arLink["DISPLAY_TYPE"],
                        GetMessage( "IBLOCK_XML2_SMART_FILTER_DISPLAY_EXPANDED" ) => ( $arLink["DISPLAY_EXPANDED"] == "Y"? "true": "false" ),
                        GetMessage( "IBLOCK_XML2_SMART_FILTER_HINT" ) => $arLink["FILTER_HINT"],
                    );

                    if( isset( $arLink["IBLOCK_XML_ID"] ) ){
                        $xmlLink[GetMessage( "IBLOCK_XML2_BX_LINKED_IBLOCK" )] = $arLink["IBLOCK_XML_ID"];
                    }

                    if( isset( $arLink["SECTION_XML_ID"] ) ){
                        $xmlLink[GetMessage( "IBLOCK_XML2_GROUP" )] = $arLink["SECTION_XML_ID"];
                    }

                    $this->obProfile->Save( $this->fp, $this->formatXMLNode( $level + 1, GetMessage( "IBLOCK_XML2_PROPERTY" ), $xmlLink ) );
                }
            }
        }
        if( !$first ){
            $this->obProfile->Save( $this->fp, str_repeat( "\t", $level )."</".GetMessage( "IBLOCK_XML2_SECTION_PROPERTIES" ).">\n" );
        }
    }

    function ExportPrices(){
        if( $this->next_step["catalog"] ){
            $rsPrice = CCatalogGroup::GetList( array(), array() );
            if( $arPrice = $rsPrice->Fetch() ){
                $this->obProfile->Save( $this->fp, "\t\t<".GetMessage( "IBLOCK_XML2_PRICE_TYPES" ).">\n" );
                do{
                    $this->obProfile->Save(
                        $this->fp,
                        $this->formatXMLNode(
                            3,
                            GetMessage( "IBLOCK_XML2_PRICE_TYPE" ),
                            array(
                                GetMessage( "IBLOCK_XML2_ID" ) => $arPrice["NAME"],
                                GetMessage( "IBLOCK_XML2_NAME" ) => $arPrice["NAME"],
                            )
                        )
                    );
                } while( $arPrice = $rsPrice->Fetch() );
                $this->obProfile->Save( $this->fp, "\t\t</".GetMessage( "IBLOCK_XML2_PRICE_TYPES" ).">\n" );
            }
        }
    }

    function EndExportMetadata(){
        $this->obProfile->Save( $this->fp, "\t</".GetMessage( "IBLOCK_XML2_METADATA" ).">\n" );
    }

    function StartExportCatalog( $with_metadata = true, $changes_only = false ){
        if( $this->next_step["catalog"] )
            $this->obProfile->Save( $this->fp, "\t<".GetMessage( "IBLOCK_XML2_OFFER_LIST" ).">\n" );
        else
            $this->obProfile->Save( $this->fp, "\t<".GetMessage( "IBLOCK_XML2_CATALOG" ).">\n" );

        if( $this->PRODUCT_IBLOCK_ID )
            $xml_id = $this->GetIBlockXML_ID( $this->PRODUCT_IBLOCK_ID, CIBlock::GetArrayByID( $this->PRODUCT_IBLOCK_ID, "XML_ID" ) );
        else
            $xml_id = $this->GetIBlockXML_ID( $this->arIBlock["ID"], $this->arIBlock["XML_ID"] );
        
        $this->arIBlock["XML_ID"] = $xml_id;
        $this->obProfile->Save( $this->fp, $this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_ID" ), $xml_id ) );
        if( $with_metadata ){
            $this->obProfile->Save($this->fp, $this->formatXMLNode(2, GetMessage( "IBLOCK_XML2_METADATA_ID" ), $xml_id));
            $this->obProfile->Save($this->fp, $this->formatXMLNode(2, GetMessage( "IBLOCK_XML2_NAME" ), $this->arIBlock["NAME"]));

            if( strlen( $this->arIBlock["DESCRIPTION"] ) > 0 )
                $this->obProfile->Save( $this->fp, $this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_DESCRIPTION" ), FormatText( $this->arIBlock["DESCRIPTION"], $this->arIBlock["DESCRIPTION_TYPE"] ) ) );
                
            if( $this->bExtended ){
                $this->obProfile->Save(
                    $this->fp,
                    $this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_BX_CODE" ), $this->arIBlock["CODE"] )
                    .$this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_BX_SORT" ), intval( $this->arIBlock["SORT"] ) )
                    .$this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_BX_LIST_URL" ), $this->arIBlock["LIST_PAGE_URL"] )
                    .$this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_BX_DETAIL_URL" ), $this->arIBlock["DETAIL_PAGE_URL"] )
                    .$this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_BX_SECTION_URL" ), $this->arIBlock["SECTION_PAGE_URL"] )
                    .$this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_BX_CANONICAL_URL" ), $this->arIBlock["CANONICAL_PAGE_URL"] )
                    .$this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_BX_PICTURE" ), $this->ExportFile( $this->arIBlock["PICTURE"] ) )
                    .$this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_BX_INDEX_ELEMENTS" ), ( $this->arIBlock["INDEX_ELEMENT"] == "Y" ? "true": "false" ) )
                    .$this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_BX_INDEX_SECTIONS" ), ( $this->arIBlock["INDEX_SECTION"] == "Y" ? "true": "false" ) )
                    .$this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_BX_WORKFLOW" ), ( $this->arIBlock["WORKFLOW"] == "Y" ? "true": "false" ) )
                );

                $this->obProfile->Save( $this->fp, "\t\t<".GetMessage( "IBLOCK_XML2_LABELS" ).">\n" );
                $arLabels = CIBlock::GetMessages( $this->arIBlock["ID"] );
                foreach( $arLabels as $id => $label ){
                    $this->obProfile->Save(
                        $this->fp,
                        $this->formatXMLNode(
                            3,
                            GetMessage( "IBLOCK_XML2_LABEL" ),
                            array(
                                GetMessage( "IBLOCK_XML2_ID" ) => $id,
                                GetMessage( "IBLOCK_XML2_VALUE" ) => $label,
                            )
                        )
                    );
                }
                $this->obProfile->Save( $this->fp, "\t\t</".GetMessage( "IBLOCK_XML2_LABELS" ).">\n" );

                $iblockTemplates = new \Bitrix\Iblock\InheritedProperty\IblockTemplates( $this->arIBlock["ID"] );
                $this->exportInheritedTemplates( 2, $iblockTemplates );
            }
        }

        if( $with_metadata || $this->only_price ){
            $this->ExportPrices();
        }

        if( $changes_only )
            $this->obProfile->Save( $this->fp, $this->formatXMLNode( 2, GetMessage( "IBLOCK_XML2_UPDATE_ONLY" ), "true" ) );

        if( $this->next_step["catalog"] )
            $this->obProfile->Save( $this->fp, "\t\t<".GetMessage( "IBLOCK_XML2_OFFERS" ).">\n" );
        else
            $this->obProfile->Save( $this->fp, "\t\t<".GetMessage( "IBLOCK_XML2_POSITIONS" ).">\n" );
    }

    function ExportPropertyValue( $xml_id, $value, $type = null ){
        $this->obProfile->Save(
            $this->fp,
            $this->formatXMLNode(
                5,
                GetMessage( "IBLOCK_XML2_PROPERTY_VALUES" ),
                array(
                    GetMessage( "IBLOCK_XML2_ID" ) => $xml_id,
                    GetMessage( "IBLOCK_XML2_VALUE" ) => $value,
                    ( isset( $type ) ? GetMessage( "IBLOCK_XML2_TYPE" ): "" ) => $type,
                )
            )
        );
    }

    function exportInheritedTemplates( $level, \Bitrix\Iblock\InheritedProperty\BaseTemplate $template ){
        $templates = $template->get();
        if( !empty( $templates ) ){
            $ws = str_repeat( "\t", $level );
            $this->obProfile->Save( $this->fp, $ws."<".GetMessage( "IBLOCK_XML2_INHERITED_TEMPLATES" ).">\n" );
            foreach( $templates as $CODE => $TEMPLATE ){
                $this->obProfile->Save( $this->fp, $ws."\t<".GetMessage( "IBLOCK_XML2_TEMPLATE" ).">\n" );
                $this->obProfile->Save( $this->fp, $ws."\t\t<".GetMessage( "IBLOCK_XML2_ID" ).">".htmlspecialcharsbx( $CODE )."</".GetMessage( "IBLOCK_XML2_ID" ).">\n" );
                $this->obProfile->Save( $this->fp, $ws."\t\t<".GetMessage( "IBLOCK_XML2_VALUE" ).">".htmlspecialcharsbx( $TEMPLATE["TEMPLATE"] )."</".GetMessage( "IBLOCK_XML2_VALUE" ).">\n" );
                $this->obProfile->Save( $this->fp, $ws."\t</".GetMessage( "IBLOCK_XML2_TEMPLATE" ).">\n" );
            }
            $this->obProfile->Save( $this->fp, $ws."</".GetMessage( "IBLOCK_XML2_INHERITED_TEMPLATES" ).">\n" );
        }
    }
    
    function exportElementProperties( $arElement, $PROPERTY_MAP ){
        if( $this->bExtended ){
            $this->ExportPropertyValue( "CML2_ACTIVE", ( $arElement["ACTIVE"] == "Y"? "true": "false" ) );
            $this->ExportPropertyValue( "CML2_CODE", $arElement["CODE"] );
            $this->ExportPropertyValue( "CML2_SORT", intval( $arElement["SORT"] ) );
            $this->ExportPropertyValue( "CML2_ACTIVE_FROM", CDatabase::FormatDate( $arElement["ACTIVE_FROM"], CLang::GetDateFormat( "FULL" ), "YYYY-MM-DD HH:MI:SS" ) );
            $this->ExportPropertyValue( "CML2_ACTIVE_TO", CDatabase::FormatDate( $arElement["ACTIVE_TO"], CLang::GetDateFormat( "FULL" ), "YYYY-MM-DD HH:MI:SS" ) );
            $this->ExportPropertyValue( "CML2_PREVIEW_TEXT", $arElement["PREVIEW_TEXT"], $arElement["PREVIEW_TEXT_TYPE"] );
            $this->ExportPropertyValue( "CML2_DETAIL_TEXT", $arElement["DETAIL_TEXT"], $arElement["DETAIL_TEXT_TYPE"] );
            $this->ExportPropertyValue( "CML2_PREVIEW_PICTURE", $this->ExportFile( $arElement["PREVIEW_PICTURE"] ) );
        }

        $arPropOrder = array(
            "sort" => "asc",
            "id" => "asc",
            "enum_sort" => "asc",
            "value_id" => "asc",
        );

        $rsProps = CIBlockElement::GetProperty( $this->arIBlock["ID"], $arElement["ID"], $arPropOrder, array( "ACTIVE" => "Y" ) );
        $arProps = array();
        while( $arProp = $rsProps->Fetch() ){
            $this->obProfile->GetIBlockElementProperty( $arProp );
            $pid = $arProp["ID"];
            if( !array_key_exists( $pid, $arProps ) )
                $arProps[$pid] = array(
                    "PROPERTY_TYPE" => $arProp["PROPERTY_TYPE"],
                    "LINK_IBLOCK_ID" => $arProp["LINK_IBLOCK_ID"],
                    "VALUES" => array(),
                );

            if( $arProp["PROPERTY_TYPE"] == "L" )
                $arProps[$pid]["VALUES"][] = array(
                    "VALUE" => $arProp["VALUE_ENUM"],
                    "DESCRIPTION" => $arProp["DESCRIPTION"],
                    "VALUE_ENUM_ID" => $arProp["VALUE"],
                );
            else
                $arProps[$pid]["VALUES"][] = array(
                    "VALUE" => $arProp["VALUE"],
                    "DESCRIPTION" => $arProp["DESCRIPTION"],
                    "VALUE_ENUM_ID" => $arProp["VALUE_ENUM_ID"],
                );
        }

        foreach( $arProps as $pid => $arProp ){
            $bEmpty = true;
            if( $this->next_step["catalog"] && !$this->bExtended )
                $this->obProfile->Save( $this->fp, "\t\t\t\t\t<".GetMessage( "IBLOCK_XML2_ITEM_ATTRIBUTE" ).">\n" );
            else
                $this->obProfile->Save( $this->fp, "\t\t\t\t\t<".GetMessage( "IBLOCK_XML2_PROPERTY_VALUES" ).">\n" );

            if( $this->next_step["catalog"] && !$this->bExtended )
                $this->obProfile->Save( $this->fp, "\t\t\t\t\t\t<".GetMessage( "IBLOCK_XML2_NAME" ).">".htmlspecialcharsbx( $PROPERTY_MAP["~".$pid] )."</".GetMessage( "IBLOCK_XML2_NAME" ).">\n" );
            else
                $this->obProfile->Save($this->fp, "\t\t\t\t\t\t<".GetMessage( "IBLOCK_XML2_ID" ).">".htmlspecialcharsbx($PROPERTY_MAP[$pid])."</".GetMessage( "IBLOCK_XML2_ID" ).">\n" );

            foreach( $arProp["VALUES"] as $arValue ){
                $value = $arValue["VALUE"];
                if( is_array( $value ) || strlen( $value ) ){
                    $bEmpty = false;
                    $bSerialized = false;
                    if( $this->bExtended ){
                        if( $arProp["PROPERTY_TYPE"] == "L" ){
                            $value = CIBlockPropertyEnum::GetByID( $arValue["VALUE_ENUM_ID"] );
                            $value = $value["XML_ID"];
                        }
                        elseif( $arProp["PROPERTY_TYPE"] == "F" ){
                            $value = $this->ExportFile( $value );
                        }
                        elseif( $arProp["PROPERTY_TYPE"] == "G" ){
                            $value = $this->GetSectionXML_ID( $arProp["LINK_IBLOCK_ID"], $value );
                        }
                        elseif( $arProp["PROPERTY_TYPE"] == "E" ){
                            $value = $this->GetElementXML_ID( $arProp["LINK_IBLOCK_ID"], $value );
                        }

                        if( is_array( $value ) && ( $arProp["PROPERTY_TYPE"] !== "F" ) ){
                            $bSerialized = true;
                            $value = serialize( $value );
                        }
                    }
                    $this->obProfile->Save( $this->fp, $this->formatXMLNode( 6, GetMessage( "IBLOCK_XML2_VALUE" ), $value ) );
                    if( $this->bExtended ){
                        $this->obProfile->Save( $this->fp, "\t\t\t\t\t\t<".GetMessage( "IBLOCK_XML2_PROPERTY_VALUE" ).">\n" );
                        if( $bSerialized )
                            $this->obProfile->Save( $this->fp, "\t\t\t\t\t\t\t<".GetMessage( "IBLOCK_XML2_SERIALIZED" ).">true</".GetMessage( "IBLOCK_XML2_SERIALIZED" ).">\n" );
                            $this->obProfile->Save( $this->fp, $this->formatXMLNode( 7, GetMessage( "IBLOCK_XML2_VALUE" ), $value ) );
                            $this->obProfile->Save( $this->fp, "\t\t\t\t\t\t\t<".GetMessage( "IBLOCK_XML2_DESCRIPTION" ).">".htmlspecialcharsbx( $arValue["DESCRIPTION"] )."</".GetMessage( "IBLOCK_XML2_DESCRIPTION" ).">\n" );
                            $this->obProfile->Save( $this->fp, "\t\t\t\t\t\t</".GetMessage( "IBLOCK_XML2_PROPERTY_VALUE" ).">\n" );
                    }
                }
            }

            if( $bEmpty )
                $this->obProfile->Save( $this->fp, "\t\t\t\t\t\t<".GetMessage( "IBLOCK_XML2_VALUE" )."></".GetMessage( "IBLOCK_XML2_VALUE" ).">\n" );

            if( $this->next_step["catalog"] && !$this->bExtended )
                $this->obProfile->Save( $this->fp, "\t\t\t\t\t</".GetMessage( "IBLOCK_XML2_ITEM_ATTRIBUTE" ).">\n" );
            else
                $this->obProfile->Save( $this->fp, "\t\t\t\t\t</".GetMessage( "IBLOCK_XML2_PROPERTY_VALUES" ).">\n" );
        }
    }

    function exportElementFields( $arElement, $SECTION_MAP ){
        $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_NAME" ).">".htmlspecialcharsbx( $arElement["NAME"] )."</".GetMessage( "IBLOCK_XML2_NAME" ).">\n" );
        if( $this->bExtended )
            $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_BX_TAGS" ).">".htmlspecialcharsbx( $arElement["TAGS"] )."</".GetMessage( "IBLOCK_XML2_BX_TAGS" ).">\n" );

        $arSections = array();
        $rsSections = CIBlockElement::GetElementGroups( $arElement["ID"], true );
        while( $arSection = $rsSections->Fetch() )
            if( array_key_exists( $arSection["ID"], $SECTION_MAP ) )
                $arSections[] = $SECTION_MAP[$arSection["ID"]];

        $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_GROUPS" ).">\n" );
        foreach( $arSections as $xml_id )
            $this->obProfile->Save( $this->fp, "\t\t\t\t\t<".GetMessage( "IBLOCK_XML2_ID" ).">".htmlspecialcharsbx( $xml_id )."</".GetMessage( "IBLOCK_XML2_ID" ).">\n" );
        
        $this->obProfile->Save( $this->fp, "\t\t\t\t</".GetMessage( "IBLOCK_XML2_GROUPS" ).">\n" );

        if( !$this->bExtended )
            $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_DESCRIPTION" ).">".htmlspecialcharsbx( FormatText( $arElement["DETAIL_TEXT"], $arElement["DETAIL_TEXT_TYPE"] ) )."</".GetMessage( "IBLOCK_XML2_DESCRIPTION" ).">\n" );

        $this->obProfile->Save( $this->fp, $this->formatXMLNode( 4, GetMessage( "IBLOCK_XML2_PICTURE" ), $this->ExportFile( $arElement["DETAIL_PICTURE"] ) ) );
    }

    function exportElement( $arElement, $SECTION_MAP, $PROPERTY_MAP ){
        if( strlen( $arElement["XML_ID"] ) > 0 )
            $xml_id = $arElement["XML_ID"];
        else
            $xml_id = $arElement["ID"];

        if( $this->PRODUCT_IBLOCK_ID > 0 ){
            $arPropOrder = array(
                "sort" => "asc",
                "id" => "asc",
                "enum_sort" => "asc",
                "value_id" => "asc",
            );
            $rsLink = CIBlockElement::GetProperty( $this->arIBlock["ID"], $arElement["ID"], $arPropOrder, array( "ACTIVE" => "Y", "CODE" => "CML2_LINK" ) );
            $arLink = $rsLink->Fetch();
            if( is_array( $arLink ) && !is_array( $arLink["VALUE"] ) && $arLink["VALUE"] > 0 )
                $xml_id = $this->GetElementXML_ID( $this->PRODUCT_IBLOCK_ID, $arLink["VALUE"] )."#".$xml_id;
        }

        $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_ID" ).">".htmlspecialcharsbx( $xml_id )."</".GetMessage( "IBLOCK_XML2_ID" ).">\n" );

        if( !$this->only_price ){
            $this->exportElementFields( $arElement, $SECTION_MAP );

            if( $this->next_step["catalog"] && !$this->bExtended )
                $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_ITEM_ATTRIBUTES" ).">\n" );
            else
                $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_PROPERTIES_VALUES" ).">\n" );

            $this->exportElementProperties( $arElement, $PROPERTY_MAP );

            if( $this->next_step["catalog"] && !$this->bExtended )
                $this->obProfile->Save( $this->fp, "\t\t\t\t</".GetMessage( "IBLOCK_XML2_ITEM_ATTRIBUTES" ).">\n" );
            else
                $this->obProfile->Save( $this->fp, "\t\t\t\t</".GetMessage( "IBLOCK_XML2_PROPERTIES_VALUES" ).">\n" );

            if( $this->bExtended ){
                $elementTemplates = new \Bitrix\Iblock\InheritedProperty\ElementTemplates( $this->arIBlock["ID"], $arElement["ID"] );
                $this->exportInheritedTemplates( 4, $elementTemplates );
            }
        }

        if( $this->next_step["catalog"] ){
            $rsProduct = CCatalogProduct::GetList( array(), array( "ID" => $arElement["ID"] ) );
            $arProduct = $rsProduct->Fetch();

            static $measure = null;
            if( !isset( $measure ) ){
                $measure = array();
                $rsBaseUnit = CCatalogMeasure::GetList( array(), array() );
                while( $arIDUnit = $rsBaseUnit->Fetch() )
                    $measure[$arIDUnit["ID"]] = $arIDUnit["CODE"];
            }
            
            $xmlMeasure = GetMessage( "IBLOCK_XML2_PCS" );
            if( ( $arProduct["MEASURE"] > 0 ) && isset( $measure[$arProduct["MEASURE"]] ) )
                $xmlMeasure = $measure[$arProduct["MEASURE"]];

            $arPrices = array();
            $rsPrices = CPrice::GetList(array(), array( "PRODUCT_ID" => $arElement["ID"]));
            while( $arPrice = $rsPrices->Fetch() ){
                $this->obProfile->GetPrice( $arPrice );

                if( !$arPrice["QUANTITY_FROM"] && !$arPrice["QUANTITY_TO"] ){
                    $arPrices[] = array(
                        GetMessage( "IBLOCK_XML2_PRICE_TYPE_ID" ) => $this->prices[$arPrice["CATALOG_GROUP_ID"]],
                        GetMessage( "IBLOCK_XML2_PRICE_FOR_ONE" ) => $arPrice["PRICE"],
                        GetMessage( "IBLOCK_XML2_CURRENCY" ) => $arPrice["CURRENCY"],
                        GetMessage( "IBLOCK_XML2_MEASURE" ) => $xmlMeasure,
                    );
                }
            }
            if( count( $arPrices ) > 0 ){
                $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_PRICES" ).">\n" );
                foreach( $arPrices as $arPrice ){
                    $this->obProfile->Save( $this->fp, "\t\t\t\t\t<".GetMessage( "IBLOCK_XML2_PRICE" ).">\n" );
                    foreach( $arPrice as $key => $value ){
                        $this->obProfile->Save( $this->fp, "\t\t\t\t\t\t<".$key.">".htmlspecialcharsbx( $value )."</".$key.">\n" );
                    }
                    $this->obProfile->Save( $this->fp, "\t\t\t\t\t</".GetMessage( "IBLOCK_XML2_PRICE" ).">\n" );
                }
                $this->obProfile->Save( $this->fp, "\t\t\t\t</".GetMessage( "IBLOCK_XML2_PRICES" ).">\n" );
                $arCatalogProduct = CCatalogProduct::GetByID( $arElement["ID"] );
                if( $arCatalogProduct ){
                    $this->obProfile->CMLExportElementGetCatalogProperty( $arElement, $arCatalogProduct );
                    $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_AMOUNT" ).">".htmlspecialcharsbx( $arCatalogProduct["QUANTITY"] )."</".GetMessage( "IBLOCK_XML2_AMOUNT" ).">\n" );

                }

            }
        }
    }
    
    function ExportElements( $PROPERTY_MAP, $SECTION_MAP, $start_time, $INTERVAL, $counter_limit = 0, $arElementFilter = false ){
        $counter = 0;
        $arSelect = array(
            "ID",
            "IBLOCK_ID",
            "XML_ID",
            "ACTIVE",
            "CODE",
            "NAME",
            "PREVIEW_TEXT",
            "PREVIEW_TEXT_TYPE",
            "ACTIVE_FROM",
            "ACTIVE_TO",
            "SORT",
            "TAGS",
            "DETAIL_TEXT",
            "DETAIL_TEXT_TYPE",
            "PREVIEW_PICTURE",
            "DETAIL_PICTURE",
        );

        if( is_array( $arElementFilter ) ){
            $arFilter = $arElementFilter;
        }
        else{
            if( $arElementFilter === "none" )
                return 0;
            
            $arFilter = array (
                "IBLOCK_ID"=> $this->arIBlock["ID"],
                "ACTIVE" => "Y",
                ">ID" => $this->next_step["LAST_ID"],
            );
            
            if( $arElementFilter === "all" )
                unset( $arFilter["ACTIVE"] );
        }

        $arOrder = array(
            "ID" => "ASC",
        );

        $rsElements = $this->obProfile->GetList( $arOrder, $arFilter, false, false, $arSelect );
        while( $arElement = $rsElements->Fetch() ){
            if( $this->next_step["catalog"] )
                $this->obProfile->Save( $this->fp, "\t\t\t<".GetMessage( "IBLOCK_XML2_OFFER" ).">\n" );
            else
                $this->obProfile->Save( $this->fp, "\t\t\t<".GetMessage( "IBLOCK_XML2_POSITION" ).">\n" );

            $this->exportElement($arElement, $SECTION_MAP, $PROPERTY_MAP);

            if( $this->next_step["catalog"] )
                $this->obProfile->Save( $this->fp, "\t\t\t</".GetMessage( "IBLOCK_XML2_OFFER" ).">\n" );
            else
                $this->obProfile->Save($this->fp, "\t\t\t</".GetMessage( "IBLOCK_XML2_POSITION" ).">\n" );

            $this->next_step["LAST_ID"] = $arElement["ID"];
            $counter++;
        }
        return $counter;
    }

    function EndExportCatalog(){
        if( $this->next_step["catalog"] ){
            $this->obProfile->Save( $this->fp, "\t\t</".GetMessage( "IBLOCK_XML2_OFFERS" ).">\n" );
            $this->obProfile->Save( $this->fp, "\t</".GetMessage( "IBLOCK_XML2_OFFER_LIST" ).">\n" );
        }
        else{
            $this->obProfile->Save( $this->fp, "\t\t</".GetMessage( "IBLOCK_XML2_POSITIONS" ).">\n" );
            $this->obProfile->Save( $this->fp, "\t</".GetMessage( "IBLOCK_XML2_CATALOG" ).">\n" );
        }
    }

    function ExportProductSet( $elementId, $elementXml ){
        $arSetItems = CCatalogProductSet::getAllSetsByProduct( $elementId, CCatalogProductSet::TYPE_GROUP );
        if( is_array( $arSetItems ) && !empty( $arSetItems ) ){
            $this->obProfile->Save( $this->fp, "\t\t<".GetMessage( "IBLOCK_XML2_PRODUCT_SETS" ).">\n" );
            $this->obProfile->Save( $this->fp, "\t\t\t<".GetMessage( "IBLOCK_XML2_ID" ).">".htmlspecialcharsbx( $elementXml )."</".GetMessage( "IBLOCK_XML2_ID" ).">\n" );
            foreach( $arSetItems as $arOneSet ){
                $this->obProfile->Save( $this->fp, "\t\t\t<".GetMessage( "IBLOCK_XML2_PRODUCT_SET" ).">\n" );
                if( is_array( $arOneSet["ITEMS"] ) && !empty( $arOneSet["ITEMS"] ) ){
                    foreach( $arOneSet["ITEMS"] as $setItem ){
                        $xmlId = $this->GetElementXML_ID( $this->arIBlock["ID"], $setItem["ITEM_ID"] );
                        if( $xmlId !== false ){
                            $this->obProfile->Save( $this->fp, "\t\t\t\t<".GetMessage( "IBLOCK_XML2_PRODUCT_SET_ITEM" ).">\n" );
                            $this->obProfile->Save( $this->fp, "\t\t\t\t\t<".GetMessage( "IBLOCK_XML2_VALUE" ).">".htmlspecialcharsbx( $xmlId )."</".GetMessage( "IBLOCK_XML2_VALUE" ).">\n" );
                            $this->obProfile->Save( $this->fp, "\t\t\t\t\t<".GetMessage( "IBLOCK_XML2_SORT" ).">".intval( $setItem["SORT"] )."</".GetMessage( "IBLOCK_XML2_SORT" ).">\n" );
                            $this->obProfile->Save( $this->fp, "\t\t\t\t</".GetMessage( "IBLOCK_XML2_PRODUCT_SET_ITEM" ).">\n" );
                        }
                    }
                }
                $this->obProfile->Save( $this->fp, "\t\t\t</".GetMessage( "IBLOCK_XML2_PRODUCT_SET" ).">\n" );
            }
            $this->obProfile->Save( $this->fp, "\t\t</".GetMessage( "IBLOCK_XML2_PRODUCT_SETS" ).">\n" );
        }
    }

    function ExportProductSets(){
        if( $this->bCatalog && $this->bExtended ){
            unset( $this->next_step["FILTER"][">ID"] );
            $rsElements = CIBlockElement::GetList( array(), $this->next_step["FILTER"], false, false, array( "ID", "XML_ID" ) );

            $this->obProfile->Save( $this->fp, "\t<".GetMessage( "IBLOCK_XML2_PRODUCTS_SETS" ).">\n" );
            while( $arElement = $rsElements->Fetch() ){
                if( CCatalogProductSet::isProductHaveSet( $arElement["ID"], CCatalogProductSet::TYPE_GROUP ) ){
                    if(strlen($arElement["XML_ID"])>0)
                        $xml_id = $arElement["XML_ID"];
                    else
                        $xml_id = $arElement["ID"];

                    $this->ExportProductSet($arElement["ID"], $xml_id);
                }
            }
            $this->obProfile->Save( $this->fp, "\t</".GetMessage( "IBLOCK_XML2_PRODUCTS_SETS" ).">\n" );
        }
    }

    function EndExport(){
        $this->obProfile->Save( $this->fp, "</".GetMessage( "IBLOCK_XML2_COMMERCE_INFO" ).">\n" );
    }
}

class CAcritCML2Export extends CAcritCML2{
    public $obProfile;

    function __construct( $obProfile ){
        $this->obProfile = $obProfile;
    }

    function Process( $arParams ){
        CAcritCML2ExportTools::IncludeModuleLangFile();

        if( isset( $this->profile["SETUPTYPE"]["ONLY_PRICE"] ) && ( $this->profile["SETUPTYPE"]["ONLY_PRICE"] == "Y" ) ){
            $bOnlyPrice = true;
        }

        if( isset( $this->profile["SETUPTYPE"]["EXTENDED"] ) && ( $this->profile["SETUPTYPE"]["EXTENDED"] == "Y" ) ){
            $bExtended = true;
        }

        $start_time = time();
        if( $fp = fopen( $arParams["FILE_EXPORT"], "ab" ) ){
            if( $this->obProfile->step === 1 ){
                ob_start();
                if( $this->Init(
                    $fp,
                    $arParams["IBLOCK_ID"],
                    null,
                    false,
                    $work_dir = false,
                    $file_dir = false,
                    $bCheckPermissions = false
                ) ){
                    $this->NotCatalog();
                    $this->ExportFileAsURL();

                    $this->StartExport();
                    $this->StartExportMetadata();
                    $this->ExportProperties( $this->obProfile->PROPERTY_MAP );
                    $this->obProfile->SetStepParams( array( "PROPERTY_MAP" ) );
                    $this->ExportSections(
                        $this->obProfile->SECTION_MAP,
                        0,
                        0
                    );
                    $this->obProfile->SetStepParams( array( "SECTION_MAP" ) );
                    $this->EndExportMetadata();
                    
                    if( $this->obProfile->stage == "end" )
                        $this->EndExport();
                    else
                        $this->obProfile->SetStepParams( array( "next_step" ), $this );
                }
                else{
                    $this->obProfile->log->AddMessage( GetMessage( "CC_BCE1_ERROR_INIT" ) );
                }

                ob_end_clean();
            }
            elseif( $this->obProfile->step === 2 ){
                ob_start();
                if( $this->Init(
                    $fp,
                    $arParams["IBLOCK_ID"],
                    $this->obProfile->GetStepParams( "next_step" ),
                    false,
                    $work_dir = false,
                    $file_dir = false,
                    $bCheckPermissions = false
                ) ){
                    $this->NotCatalog();
                    $this->ExportFileAsURL();

                    if( $this->obProfile->stage == "start_export" )
                        $this->StartExportCatalog();

                    $result = $this->ExportElements(
                        $this->obProfile->GetStepParams( "PROPERTY_MAP" ),
                        $this->obProfile->GetStepParams( "SECTION_MAP" ),
                        $start_time,
                        $arParams["INTERVAL"],
                        $arParams["ELEMENTS_PER_STEP"]
                    );
                     
                    if( $this->obProfile->stage == "end_export" ){
                        $this->EndExportCatalog();
                        $this->obProfile->SetStepParams( array( "next_step" ), $this );
                    }
                    elseif( $this->obProfile->stage == "end" ){
                        $this->EndExportCatalog();
                        $this->EndExport();
                    }
                }
                ob_end_clean();
            }
            elseif( $this->obProfile->step === 3 ){
                ob_start();
                $arCatalog = false;
                $arCatalog = CCatalog::GetSkuInfoByProductID( $arParams["IBLOCK_ID"] );
                if( $this->Init(
                    $fp,
                    is_array( $arCatalog ) ? $arCatalog["IBLOCK_ID"] : $arParams["IBLOCK_ID"],
                    $this->obProfile->GetStepParams( "next_step" ),
                    false,
                    $work_dir = false,
                    $file_dir = false,
                    $bCheckPermissions = false,
                    is_array( $arCatalog ) ? $arCatalog["PRODUCT_IBLOCK_ID"] : false
                )){
                    if( $this->obProfile->stage == "start_export" ){
                        $this->StartExportMetadata();
                        $this->ExportProperties( $this->obProfile->PROPERTY_MAP );
                        $this->obProfile->SetStepParams( array( "PROPERTY_MAP" ) );
                        $this->ExportSections(
                            $this->obProfile->SECTION_MAP,
                            0,
                            0
                        );
                        $this->obProfile->SetStepParams( array( "SECTION_MAP" ) );
                        $this->EndExportMetadata();
                        $this->StartExportCatalog();
                    }
                    $result = $this->ExportElements(
                        $this->obProfile->GetStepParams( "PROPERTY_MAP" ),
                        $this->obProfile->GetStepParams( "SECTION_MAP" ),
                        $start_time,
                        $arParams["INTERVAL"],
                        $arParams["ELEMENTS_PER_STEP"]
                    );

                    if( ( $this->obProfile->stage == "end_export" ) || ( $this->obProfile->stage == "end" ) ){
                        $this->EndExportCatalog();
                        $this->EndExport();
                    }
                    elseif( $this->obProfile->stage == "end" ){
                        $this->EndExportCatalog();
                        $this->EndExport();
                    }
                }
                ob_end_clean();
            }
        }
    }
}