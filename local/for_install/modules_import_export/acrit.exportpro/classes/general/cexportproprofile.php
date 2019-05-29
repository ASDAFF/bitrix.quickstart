<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use Bitrix\Catalog;

Loc::loadMessages( __FILE__ );

class CExportproVariant{
    public function GetMeasure(){
        return array();
    }
    
    public static function GetCategory(){
        return array(
            "" => "",
            "apparel-RU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_apparel-RU" ),
            "apparel-INT" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_apparel-INT" ),

            "shoes-cm" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_shoes-cm" ),
            "shoes-EU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_shoes-EU" ),
            "shoes-RU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_shoes-RU" ),
            "shoes-US" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_shoes-US" ),

            "headgears-INT" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_headgears-INT" ),

            "gloves-cm" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_gloves-cm" ),
            "gloves-INT" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_gloves-INT" ),

            "skis-cm" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_skis-cm" ),
            "skisticks-cm" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_skisticks-cm" ),
            "skiboots-cm" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_skiboots-cm" ),
            "skihelmets-INT" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_skihelmets-INT" ),

            "boardboots-cm" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_boardboots-cm" ),
            "boardboots-UK" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_boardboots-UK" ),
            "boardboots-RU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_boardboots-RU" ),
            "boardboots-EU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_boardboots-EU" ),
            "boardboots-US" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_boardboots-US" ),

            "bikeframes-INCH" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_bikeframes-INCH" ),
            "boxinggloves-OZ" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_boxinggloves-OZ" ),
            "boxinggloves-INT" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_boxinggloves-INT" ),
            "boxinghelmets-INT" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_AKTIVIZM_boxinghelmets-INT" ),
        );
    }
    public static function GetCategorySelect( $params ){
        $options = array();
        foreach( self::GetCategory() as $key => $category ){
            $selected = $params["DEFAULT"] == $key ? 'selected="selected"' : "";
            $options[] = "<option value='$key' $selected>$category</option>";
        }
        return "<select name='{$params["NAME"]}'>".implode( "\r\n", $options )."</select>";
    }
}

require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client_partner.php" );

class CExportproProfile{
    private $typeLocation = "/../../lib/types";

    public function GetIBlockTypes( $lid, $catalogOnly = true, $hideOffers = true ){
        CModule::IncludeModule( "iblock" );

        $arTypeAll = array();
        $dbIBlock = CIBlock::GetList(
            array(
                "IBLOCK_TYPE" => "ASC",
                "NAME" => "ASC"
            ),
            array(
                "LID" => $lid
            )
        );
        
        while( $arIBlock = $dbIBlock->Fetch() ){               
            if( true == $catalogOnly ){
                if( CModule::IncludeModule( "catalog" ) && CCatalog::GetByID( $arIBlock["ID"] ) ){
                    if( $hideOffers == true && CCatalogSKU::GetInfoByOfferIBlock( $arIBlock["ID"] ) ){
                        continue;
                    }
                    $arTypeIblock[] = $arIBlock;
                }
            }
            else{
                $arTypeIblock[] = $arIBlock;
            }
        }
        unset( $dbIBlock );
        foreach( $arTypeIblock as $arIBlock ){
            $arType = CIBlockType::GetByIDLang( $arIBlock["IBLOCK_TYPE_ID"], LANG );
            $arTypeAll[$arType["ID"]]["ID"] = $arType["ID"];
            $arTypeAll[$arType["ID"]]["NAME"] = "{$arType["NAME"]} [{$arType["ID"]}]";
            $arTypeAll[$arType["ID"]]["IBLOCK"][$arIBlock["ID"]] = "{$arIBlock["NAME"]} [{$arIBlock["ID"]}]";
            unset( $arType );
        }

        unset( $arTypeIblock );
        return $arTypeAll;
    }
    
    public function GetSections( $ib, $subsection = false ){
        CModule::IncludeModule( "iblock" );

        if( !is_array( $ib ) || empty( $ib ) )
            return array();

        $arFilter = array( "IBLOCK_ID" => $ib );
        if( !$subsection )
            $arFilter["DEPTH_LEVEL"] = 1;
        
        $dbSection = CIBlockSection::GetList(
            array(
                "NAME" => "ASC"
            ),
            $arFilter
        );
        
        $sections = array();
        while( $arSection = $dbSection->Fetch() ){
            $sections[$arSection["DEPTH_LEVEL"]][$arSection["ID"]] = array(
                "ID" => $arSection["ID"],
                "NAME" => "{$arSection["NAME"]} [{$arSection["ID"]}]",
                "LEVEL" => $arSection["DEPTH_LEVEL"],
                "PARENT" => $arSection["IBLOCK_SECTION_ID"],
                "PARENT_1" => $arSection["ID"],
                "IBLOCK_ID" => $arSection["IBLOCK_ID"]
            );
        }
        
        unset( $dbSection );
        ksort( $sections );
        
        foreach( $sections as $depth => &$depthSections ){
            if( $depth == 1 )
                continue;
            
            foreach( $depthSections as $id => &$current ){
                $current["NAME"] = implode( " / ", array(
                    $sections[$depth - 1][$current["PARENT"]]["NAME"],
                    $current["NAME"]
                ));
                $current["PARENT_1"] = $sections[$depth - 1][$current["PARENT"]]["PARENT_1"];
            }
        }
        return $sections;
    }

    public function GetProfileData(){
        return "";
    }
    
    function GetTypes(){
        $typeFiles = scandir( __DIR__.$this->typeLocation );
        $typeFiles = CExportproProfile::PathFilter( $typeFiles );
        foreach( $typeFiles as $file ){                   
            require( __DIR__.$this->typeLocation."/".$file );
        }              
        return $profileTypes;
    }
    
    function GetIBlockFields(){
        @CModule::IncludeModule( "iblock" );
        $fields = CIBlock::GetFieldsDefaults();
        return $fields;
    }
    
    function GetDefaults(){
        $type = $this->GetTypes();
        foreach( $type as $code => $value ){
            $value["CODE"] = $code;
            $type = $value;
            break;
        }                         
                                                               
        $dbSite = CSite::GetList(
            $by = "sort",
            $order = "asc",
            array(
                "ACTIVE" => "Y",
                "DOMAIN" => "%".$_SERVER["SERVER_NAME"],
            )
        );
                                                               
        $arProcessSite = false;
        if( $arSite = $dbSite->Fetch() ){
            $arProcessSite = $arSite;
        }      
        
        return array(
            "ACTIVE" => "Y",
            "TYPE" => $type["CODE"],
            "VIEW_CATALOG" => "Y",
            "USE_SKU" => "Y",
            "LID" => $arProcessSite["LID"],
            "FORMAT" => $type["FORMAT"],
            "OFFER_TEMPLATE" => $type["ITEMS_FORMAT"],
            "XMLDATA" => $type["FIELDS"],
            "CURRENCY_TEMPLATE" => $type["CURRENCIES"],
            "CATEGORY_TEMPLATE" => $type["SECTIONS"],
            "CATEGORY_INNER_TEMPLATE" => $type["SECTIONS_INNER"],
            "DATEFORMAT" => $type["DATEFORMAT"],
            "ENCODING" => !$type["ENCODING"] ? "cp1251" : $type["ENCODING"],
            "DOMAIN_NAME" => $arProcessSite["SERVER_NAME"],
            "SITE_NAME" => $arProcessSite["SITE_NAME"],
            "DESCRIPTION" => $arProcessSite["NAME"],
        );
    }
    
    private function createFieldsetRecursive( $ibFields ){
        $options = array();
        foreach( $ibFields as $field ){
            if( isset( $field["children"] ) ){
                $options[] = "<optgroup label=\"{$field['label']}\">";
                $options_ext = $this->createFieldsetRecursive( $field["children"] );
                $options = array_merge( $options, $options_ext );
                $options[] = "</optgroup>";
            }
            else{
                if( empty( $field["label"] ) )
                    continue;
                
                $options[$field["controlId"]] = "<option value=\"{$field['controlId']}\" #SELECTED#>{$field['label']}</option>";
            }
        }
        return $options;
    }
    
    function createFieldset(){
        $ibFields = array( CAcritExportproCondCtrlIBlockFields::GetControlShow() );
        $ibProps = CAcritExportproCondCtrlIBlockProps::GetControlShow();
        $ibFields = array_merge( $ibFields, $ibProps );
        $options = $this->createFieldsetRecursive( $ibFields );
        return $options;
    }
    
    function selectFieldset( $options, $value ){
        foreach( $options as $id => &$opt ){
            $selected = $value == $id ? 'selected="selected"' : "";
            $opt = str_replace( "#SELECTED#", $selected, $opt );
        }
        return $options;
    }
    
    function createFieldset2( $IBLOCK, $SKU = false, $PRICE = false ){
        $filedNames = CIBlock::GetFieldsDefaults();
        $Properties = array(
            "ID" => GetMessage( "ACRIT_EXPORTPRO_IDENTIFIKACIONNYY_NO" ),
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_NAIMENOVANIE" ),
			"CODE" => GetMessage( "ACRIT_EXPORTPRO_SIMVOLQNYY_KOD" ),
			"ACTIVE" => GetMessage( "ACRIT_EXPORTPRO_AKTIVNOSTQ" ),
			"DETAIL_PAGE_URL" => GetMessage( "ACRIT_EXPORTPRO_SSYLKA_NA_DETALQNUU" ),
			"DATE_ACTIVE_FROM" => GetMessage( "ACRIT_EXPORTPRO_DATA_NACALA_AKTIVNOS" ),
			"DATE_ACTIVE_TO" => GetMessage( "ACRIT_EXPORTPRO_DATA_OKONCANIA_AKTIV" ),
			"PREVIEW_TEXT" => GetMessage( "ACRIT_EXPORTPRO_TEKST_ANONSA" ),
			"PREVIEW_PICTURE" => GetMessage( "ACRIT_EXPORTPRO_IZOBRAJENIE_ANONSA" ),
			"DETAIL_TEXT" => GetMessage( "ACRIT_EXPORTPRO_DETALQNOE_OPISANIE" ),
			"DETAIL_PICTURE" => GetMessage( "ACRIT_EXPORTPRO_DETALQNOE_IZOBRAJENI" ),
            "IBLOCK_ID" => GetMessage( "ACRIT_EXPORTPRO_FIELD_IBLOCK_ID" ),
            "IBLOCK_CODE" => GetMessage( "ACRIT_EXPORTPRO_FIELD_IBLOCK_CODE" ),
            "IBLOCK_SECTION_ID" => GetMessage( "ACRIT_EXPORTPRO_FIELD_IBLOCK_SECTION_ID" ),
            "IBLOCK_SECTION_NAME" => GetMessage( "ACRIT_EXPORTPRO_FIELD_IBLOCK_SECTION_NAME" ),
            "CREATED_BY" => GetMessage( "ACRIT_EXPORTPRO_FIELD_CREATED_BY" ),
            "CREATED_USER_NAME" => GetMessage( "ACRIT_EXPORTPRO_FIELD_CREATED_USER_NAME" ),
            "TIMESTAMP_X" => GetMessage( "ACRIT_EXPORTPRO_FIELD_TIMESTAMP_X" ),
            "MODIFIED_BY" => GetMessage( "ACRIT_EXPORTPRO_FIELD_MODIFIED_BY" ),
            "USER_NAME" => GetMessage( "ACRIT_EXPORTPRO_FIELD_USER_NAME" ),
        );
        
        /*$SKU_Properties = array(
            "ID_SKU" => GetMessage( "ACRIT_EXPORTPRO_IDENTIFIKACIONNYY_NO" ),
            "NAME_SKU" => GetMessage( "ACRIT_EXPORTPRO_NAIMENOVANIE" ),
            "CODE_SKU" => GetMessage( "ACRIT_EXPORTPRO_SIMVOLQNYY_KOD" ),
            "ACTIVE_SKU" => GetMessage( "ACRIT_EXPORTPRO_AKTIVNOSTQ" ),
            "DETAIL_PAGE_URL_SKU" => GetMessage( "ACRIT_EXPORTPRO_SSYLKA_NA_DETALQNUU" ),
            "DATE_ACTIVE_FROM_SKU" => GetMessage( "ACRIT_EXPORTPRO_DATA_NACALA_AKTIVNOS" ),
            "DATE_ACTIVE_TO_SKU" => GetMessage( "ACRIT_EXPORTPRO_DATA_OKONCANIA_AKTIV" ),
            "PREVIEW_TEXT_SKU" => GetMessage( "ACRIT_EXPORTPRO_TEKST_ANONSA" ),
            "PREVIEW_PICTURE_SKU" => GetMessage( "ACRIT_EXPORTPRO_IZOBRAJENIE_ANONSA" ),
            "DETAIL_TEXT_SKU" => GetMessage( "ACRIT_EXPORTPRO_DETALQNOE_OPISANIE" ),
            "DETAIL_PICTURE_SKU" => GetMessage( "ACRIT_EXPORTPRO_DETALQNOE_IZOBRAJENI" ),
            "IBLOCK_ID_SKU" => GetMessage( "ACRIT_EXPORTPRO_FIELD_IBLOCK_ID" ),
            "IBLOCK_CODE_SKU" => GetMessage( "ACRIT_EXPORTPRO_FIELD_IBLOCK_CODE" ),
            "IBLOCK_SECTION_ID_SKU" => GetMessage( "ACRIT_EXPORTPRO_FIELD_IBLOCK_SECTION_ID" ),
            "IBLOCK_SECTION_NAME_SKU" => GetMessage( "ACRIT_EXPORTPRO_FIELD_IBLOCK_SECTION_NAME" ),
            "CREATED_BY_SKU" => GetMessage( "ACRIT_EXPORTPRO_FIELD_CREATED_BY" ),
            "CREATED_USER_NAME_SKU" => GetMessage( "ACRIT_EXPORTPRO_FIELD_CREATED_USER_NAME" ),
            "TIMESTAMP_X_SKU" => GetMessage( "ACRIT_EXPORTPRO_FIELD_TIMESTAMP_X" ),
            "MODIFIED_BY_SKU" => GetMessage( "ACRIT_EXPORTPRO_FIELD_MODIFIED_BY" ),
            "USER_NAME_SKU" => GetMessage( "ACRIT_EXPORTPRO_FIELD_USER_NAME" ),
        );*/
		
        if( $PRICE ){
            //$arGroups = array();
            //if( CModule::IncludeModule( "catalog" ) ){
            //    $dbRes = CCatalogGroup::GetList( array( "SORT" => "ASC" ), array( "ACTIVE" => "Y" ) );
            //    while( $arRes = $dbRes->GetNext() ){
            //        $price["CATALOG-PRICE_" . $arRes["ID"]] = "[" . $arRes["ID"] . "] " . $arRes["NAME_LANG"];
            //        $price["CATALOG-PRICE_" . $arRes["ID"]."_CURRENCY"] = "[" . $arRes["ID"] . "] " . $arRes["NAME_LANG"] ."(".GetMessage("ACRIT_EXPORTPRO_FIELD_CURRNECY").")";
            //    }
            //}
			//$arGroups = "";
			//if( CModule::IncludeModule( "catalog" ) ){
			//	$dbRes = CCatalogGroup::GetGroupsList( array() );
			//	while( $arRes = $dbRes->Fetch() ){
			//		if( $arRes["BUY"] == "Y" )
			//			$arGroups[] = $arRes["CATALOG_GROUP_ID"];
			//	}
			//	$dbRes = CCatalogGroup::GetList( array( "SORT" => "ASC" ), array( "ACTIVE" => "Y", "ID" => $arGroups ), 0, 0, array( "ID", "NAME", "BASE" ) );
			//	while( $arRes = $dbRes->GetNext() ){
			//		$price["CATALOG-PRICE_" . $arRes["ID"]] = "[" . $arRes["ID"] . "] " . $arRes["NAME"];
			//	}
			//}                      
		}
        
		if( !is_array( $IBLOCK ) ){
			$k = $IBLOCK;
			$IBLOCK = array();
			$IBLOCK[] = $k;
            
            
		}
        
        /*$arProcessSectons = self::GetSections( $IBLOCK, true );
        
        if( !empty( $arProcessSectons ) ){
            $arProfileSections = array();
            $arProfileSectionIds = array();
            
            $arProfileSectionByIblocks = array();
            $arProfileSectionByIblockSectionsIds = array();
            foreach( $arProcessSectons as $arSectionLevel ){
                foreach( $arSectionLevel as $arSectionLevelItem ){
                    $arProfileSectionIds[] = $arSectionLevelItem["ID"];
                    $arProfileSections[$arSectionLevelItem["ID"]] = $arSectionLevelItem;
                    
                    if( !is_array( $arProfileSectionByIblocks[$arSectionLevelItem["IBLOCK_ID"]] ) ){
                        $arProfileSectionByIblocks[$arSectionLevelItem["IBLOCK_ID"]] = array();
                    }
                    
                    if( !is_array( $arProfileSectionByIblockSectionsIds[$arSectionLevelItem["IBLOCK_ID"]] ) ){
                        $arProfileSectionByIblockSectionsIds[$arSectionLevelItem["IBLOCK_ID"]] = array();
                    }
                    
                    $arProfileSectionByIblocks[$arSectionLevelItem["IBLOCK_ID"]][$arSectionLevelItem["ID"]] = $arSectionLevelItem;
                    $arProfileSectionByIblockSectionsIds[$arSectionLevelItem["IBLOCK_ID"]][] = $arSectionLevelItem["ID"];
                }
            }
        }*/
        
        foreach( $IBLOCK as $iblockId ){
            $dbSectionUserFields = CUserTypeEntity::GetList(
                array(),
                array(
                    "ENTITY_ID" => "IBLOCK_".$iblockId."_SECTION",
                    "LANG" => LANGUAGE_ID
                )
            );
            
            while( $arSectionUserFields = $dbSectionUserFields->Fetch() ){
                if( !array_key_exists( $arSectionUserFields["FIELD_NAME"], $Properties ) ){
                    $userFieldTitle = ( strlen( $arSectionUserFields["EDIT_FORM_LABEL"] ) > 0 ) ? $arSectionUserFields["FIELD_NAME"].": ".$arSectionUserFields["EDIT_FORM_LABEL"] : $arSectionUserFields["FIELD_NAME"];
                    $Properties[$arSectionUserFields["FIELD_NAME"]] = $userFieldTitle;
                }
            }
        }
        
        if( is_array( $IBLOCK ) && sizeof( $IBLOCK ) > 0 ){
			foreach( $IBLOCK as $iblock ){
				$arIBlock[$iblock]["FIELDS"] = $Properties;
				$res = CIBlock::GetByID( $iblock );
				if( $ar_res = $res->GetNext() )
					$arIBlock[$iblock]["NAME"] = $ar_res["NAME"];
				
                $intIBlockID = $iblock;
				if( $SKU == true && CModule::IncludeModule( "catalog" ) ){
					$arOffers = CCatalogSKU::GetInfoByProductIBlock( $intIBlockID );
					if( !empty( $arOffers["IBLOCK_ID"] ) ){
						$intOfferIBlockID = $arOffers["IBLOCK_ID"];
						$strPerm = "D";
						$rsOfferIBlocks = CIBlock::GetByID( $intOfferIBlockID );
						if( $arOfferIBlock = $rsOfferIBlocks->Fetch() ){
							$bBadBlock = !CIBlockRights::UserHasRightTo( $intOfferIBlockID, $intOfferIBlockID, "iblock_admin_display" );
							if( $bBadBlock ){
								echo GetMessage( "ERR_NO_ACCESS_IBLOCK_SKU" );
							}
						}
						$boolOffers = true;
					}
					else{
						$boolOffers = false;
					}
				}
				else{
					$boolOffers = false;
				}

				//--получем свойства инфоблока

				$dbRes = CIBlockProperty::GetList(
                    array(
                        "name" => "asc"
                    ),
                    array(
                        "IBLOCK_ID" => $intIBlockID,
                        "ACTIVE" => "Y"
                    )
                );
				
                $arIBlock[$iblock]["PROPERTY"] = array();
				$arIBlock[$iblock]["OFFERS_PROPERTY"] = array();
				while( $arRes = $dbRes->Fetch() ){
					$arIBlock[$iblock]["PROPERTY"][$arRes["ID"]] = $arRes;
				}
				
                if( $boolOffers ){                            
					$rsProps = CIBlockProperty::GetList(
                        array(
                            "SORT" => "ASC"
                        ),
                        array(
                            "IBLOCK_ID" => $intOfferIBlockID,
                            "ACTIVE" => "Y"
                        )
                    );
					
                    while( $arProp = $rsProps->Fetch() ){
					    //if( $arOffers["SKU_PROPERTY_ID"] != $arProp["ID"] ){
						if( $arProp["PROPERTY_TYPE"] == "L" ){
							$arProp["VALUES"] = array();
							$rsPropEnums = CIBlockProperty::GetPropertyEnum(
                                $arProp["ID"],
                                array(
                                    "sort" => "asc"
                                ),
                                array(
                                    "IBLOCK_ID" => $intOfferIBlockID
                                )
                            );
							while( $arPropEnum = $rsPropEnums->Fetch() ){
								$arProp["VALUES"][$arPropEnum["ID"]] = $arPropEnum["VALUE"];
							}
						}
						$arIBlock[$iblock]["OFFERS_PROPERTY"][$arProp["ID"]] = $arProp;
						if( is_array( $arSelectedPropTypes ) && in_array( $arProp["PROPERTY_TYPE"], $arSelectedPropTypes ) )
							$arSelectOfferProps[] = $arProp["ID"];
                        //}
					}
                    
                    /*if( !empty( $arIBlock[$iblock]["OFFERS_PROPERTY"] ) ){
                        $arIBlock[$iblock]["OFFERS_FIELDS"] = $SKU_Properties;
                    }*/
				}
			}
        }
        if( @CModule::IncludeModule( "catalog" ) ){
            $arIBlock["CATALOG"] = array();
            $arIBlock["CATALOG"]["QUANTITY"] = GetMessage( "ACRIT_EXPORTPRO_KOLICESTVO" );
            $arIBlock["CATALOG"]["QUANTITY_RESERVED"] = GetMessage( "ACRIT_EXPORTPRO_QUANTITY_RESERVED" );
            $arIBlock["CATALOG"]["WEIGHT"] = GetMessage( "ACRIT_EXPORTPRO_WEIGHT" );
            $arIBlock["CATALOG"]["WIDTH"] = GetMessage( "ACRIT_EXPORTPRO_WIDTH" );
            $arIBlock["CATALOG"]["LENGTH"] = GetMessage( "ACRIT_EXPORTPRO_LENGTH" );
            $arIBlock["CATALOG"]["HEIGHT"] = GetMessage( "ACRIT_EXPORTPRO_HEIGHT" );
            $arIBlock["CATALOG"]["PURCHASING_PRICE"] = GetMessage( "ACRIT_EXPORTPRO_FIELD_PURCHASING_PRICE" );
            
            $p = GetCatalogGroups( ( $b = "SORT" ), ( $o = "ASC" ) );
            while( $p1 = $p->Fetch() ){
                if( $p1["CAN_ACCESS"] == "Y" ){
                    $arIBlock["CATALOG"]["PRICE_".$p1["ID"]] = $p1["NAME_LANG"]." (".GetMessage( "ACRIT_EXPORTPRO_FIELD_PRICE" ).")";
                    $arIBlock["CATALOG"]["PRICE_".$p1["ID"]."_WD"] = $p1["NAME_LANG"]." (".GetMessage( "ACRIT_EXPORTPRO_FIELD_PRICE_WITH_DISCOUNT" ).")";
                    $arIBlock["CATALOG"]["PRICE_".$p1["ID"]."_D"] = $p1["NAME_LANG"]." (".GetMessage( "ACRIT_EXPORTPRO_FIELD_PRICE_DISCOUNT" ).")";
                    $arIBlock["CATALOG"]["PRICE_".$p1["ID"]."_CURRENCY"] = $p1["NAME_LANG"]." (".GetMessage( "ACRIT_EXPORTPRO_FIELD_CURRNECY" ).")";
                }
            }
        }               
        return $arIBlock;
    }

    function selectFieldset2( $arIBlock, $value ){
        $options = array();
        $fieldsSetup = false;
        if( is_array( $arIBlock ) ){
            foreach( $arIBlock as $IB_ID => $arFields ){
                if( is_array( $arFields["FIELDS"] ) && sizeof( $arFields["FIELDS"] ) > 0 && !$fieldsSetup ){
					$options[] = '<optgroup label="'.GetMessage("ACRIT_EXPORTPRO_POLA")/* . $arFields['NAME'] */. '">';
					foreach ($arFields['FIELDS'] as $idf => $fields){
						$selected = $idf == $value ? 'selected="selected"' : '';
						$options[] = "<option value=\"$idf\" $selected>$fields</option>";
					}
					$options[] = '</optgroup>';
                    $fieldsSetup = true;
				}
				if( is_array( $arFields["PROPERTY"] ) && sizeof( $arFields["PROPERTY"] ) > 0 ){
                    $options[] = '<optgroup label="'.GetMessage( "ACRIT_EXPORTPRO_SVOYSTVA" ) . $arFields["NAME"] . '">';
					foreach( $arFields["PROPERTY"] as $fields ){
						$selected = $IB_ID."-PROPERTY-".$fields["ID"] == $value ? 'selected="selected"' : "";
						$options[] = "<option value=\"$IB_ID-PROPERTY-{$fields["ID"]}\" $selected>{$fields["NAME"]} [{$fields["ID"]}]</option>";
					}
					$options[] = '</optgroup>';
				}
                /*if( is_array( $arFields["OFFERS_FIELDS"] ) && sizeof( $arFields["OFFERS_FIELDS"] ) > 0 ){
                    $options[] = '<optgroup label="'.GetMessage("ACRIT_EXPORTPRO_POLA1") . $arFields["NAME"] . '">';                    
                    foreach ($arFields["OFFERS_FIELDS"] as $idf => $fields){
                        $selected = $idf == $value ? 'selected="selected"' : '';
                        $options[] = "<option value=\"$idf\" $selected>$fields</option>";
                    }
                    $options[] = '</optgroup>';
                }*/
				if( is_array( $arFields["OFFERS_PROPERTY"] ) && sizeof( $arFields["OFFERS_PROPERTY"] ) > 0 ){
					$options[] = '<optgroup label="'.GetMessage("ACRIT_EXPORTPRO_SVOYSTVA1") . $arFields["NAME"] . '">';
                    foreach( $arFields["OFFERS_PROPERTY"] as $fields ){
						$selected = $IB_ID."-PROPERTY-".$fields["ID"] == $value ? 'selected="selected"' : "";
						$options[] = "<option value=\"$IB_ID-PROPERTY-{$fields["ID"]}\" $selected>{$fields["NAME"]} [{$fields["ID"]}]</option>";
					}
					$options[] = '</optgroup>';
				}
                $ibd = $IB_ID;
            }
            if( is_array( $arIBlock["CATALOG"]) && sizeof( $arIBlock["CATALOG"] ) > 0 ){
				$options[] = '<optgroup label="'.GetMessage( "ACRIT_EXPORTPRO_SVOYSTVA_TORGOVOGO_K" ).$arFields["NAME"].'">';
                foreach( $arIBlock["CATALOG"] as $idf => $fields ){
					$selected = "CATALOG-".$idf == $value ? 'selected="selected"' : "";
					$options[] = "<option value=\"CATALOG-$idf\" $selected>$fields</option>";
				}
				$options[] = '</optgroup>';
			}
        }
        return $options;
    }
    
    public function GetCurrencyRate(){
        $rates = array(
            "SITE" => GetMessage( "ACRIT_EXPORTPRO_CURRENCYRATE_SITE" ),
            "CBRF" => GetMessage( "ACRIT_EXPORTPRO_CURRENCYRATE_CBRF" ),
            "NBU" => GetMessage( "ACRIT_EXPORTPRO_CURRENCYRATE_NBU" ),
            "NBK" => GetMessage( "ACRIT_EXPORTPRO_CURRENCYRATE_NBK" ),
            "NBB" => GetMessage( "ACRIT_EXPORTPRO_CURRENCYRATE_NBB" ),
        );
        return $rates;
    }
    
    public static function LoadCurrencyRates( $bank = array() ){
        $arResult = array();
        $DATA = array(
			"CBRF" => array(
				"URL" => "www.cbr.ru",
				"LINK" => "/scripts/XML_daily.asp",
				"PORT" => "80",
				"ITEMS" => array( "ValCurs", "#", "Valute" ),
				"CURRENCY" => array( "#", "CharCode", 0, "#" ),
				"RATE" => array( "#", "Nominal", 0, "#" ),
				"VALUE" => array( "#", "Value", 0, "#" )
			),
			"NBU" => array(
				"URL" => "bank-ua.com",
				"LINK" => "/export/currrate.xml",
				"PORT" => "80",
				"ITEMS" => array( "chapter", "#", "item" ),
				"CURRENCY" => array( "#", "char3", 0, "#" ),
				"RATE" => array( "#", "size", 0, "#" ),
				"VALUE" => array( "#", "rate", 0, "#" )
			),
			"NBK" => array(
				"URL" => "www.nationalbank.kz",
				"LINK" => "/rss/rates_all.xml",
				"PORT" => "80",
				"ITEMS" => array( "rss", "#", "channel", 0, "#", "item" ),
				"CURRENCY" => array( "#", "title", 0, "#" ),
				"RATE" => array( "#", "quant", 0, "#" ),
				"VALUE" => array( "#", "description", 0, "#" )
			),
			"NBB" => array(
				"URL" => "www.nbrb.by",
				"LINK" => "/Services/XmlExRates.aspx",
				"PORT" => "80",
				"ITEMS" => array( "DailyExRates", "#", "Currency" ),
				"CURRENCY" => array( "#", "CharCode", 0, "#" ),
				"RATE" => array( "#", "Scale", 0, "#" ),
				"VALUE" => array( "#", "Rate", 0, "#" )
			),
		);

        if( sizeof( $bank ) <= 0 )
            $bank = array_keys( $DATA );

		require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php" );

        foreach( $bank as $bnk ){
            $strQueryText = QueryGetData(
                $DATA[$bnk]["URL"],
                $DATA[$bnk]["PORT"],
                $DATA[$bnk]["LINK"],
                "",
                $error_number,
                $error_text
            );

            if( strlen( $strQueryText ) > 0 ){
                $objXML = new CDataXML();
                $objXML->LoadString( $strQueryText );
                $arData = $objXML->GetArray();
                for( $i = 0; $i < sizeof( $DATA[$bnk]["ITEMS"] ); $i++ ){
                    $arData = $arData[$DATA[$bnk]["ITEMS"][$i]];
                }

                if( is_array( $arData ) && count( $arData ) > 0 ){
                    for( $j1 = 0; $j1 < count( $arData ); $j1++ ){
                        for( $i = 0; $i < sizeof( $DATA[$bnk]["VALUE"] ); $i++ ){
                            if( $i == 0 ){
                                $q1 = $arData[$j1][$DATA[$bnk]["VALUE"][$i]];
                            }
                            elseif( $i > 0 ){
                                $q1 = $q1[$DATA[$bnk]["VALUE"][$i]];
                            }
                        }
                        $arCurrValue = str_replace( ",", ".", $q1 );
                        for( $i = 0; $i < sizeof( $DATA[$bnk]["CURRENCY"] ); $i++ ){
                            if( $i == 0 ){
                                $currency1 = $arData[$j1][$DATA[$bnk]["CURRENCY"][$i]];
                            }
                            elseif( $i > 0 ){
                                $currency1 = $currency1[$DATA[$bnk]["CURRENCY"][$i]];
                            }
                        }
                        for( $i = 0; $i < sizeof( $DATA[$bnk]["RATE"] ); $i++ ){
                            if( $i == 0 ){
                                $rate = $arData[$j1][$DATA[$bnk]["RATE"][$i]];
                            }
                            elseif( $i > 0 ){
                                $rate = $rate[$DATA[$bnk]["RATE"][$i]];
                            }
                        }
                        $curr = DoubleVal( $arCurrValue );
                        if( sizeof( $currency ) > 0 ){
                            if( in_array( $currency1, $currency ) )
                                $arResult[$bnk][] = array(
                                    "RATE" => round( $curr, 2 ),
                                    "RATE_CNT" => $rate,
                                    "CURRENCY" => $currency1,
                                );
                        }
                        else{
                            $arResult[$bnk][] = array(
                                "RATE" => round( $curr, 2 ),
                                "RATE_CNT" => $rate,
                                "CURRENCY" => $currency1,
                            );
                        }
                    }
                }
            }
		}
        $arResultNew = array();
        foreach( $arResult as $rate => $value ){
            foreach($value as $currency)
            {
                $arResultNew[$rate][$currency["CURRENCY"]] = $currency;
            }
        }
        $arResult = $arResultNew;
        $arResult["CBRF"]["RUB"] = array(
            "CURRENCY" => "RUB",
            "RATE" => 1,
            "RATE_CNT" => 1
        );
        unset( $arResultNew );
        return $arResult;
    }
    
    public function PrepareIBlock( $arIBlock = array(), $UseSKU = fasle ){
		$excludeIBlock = array();
        if( !is_array( $arIBlock ) )
            $arIBlock = array();
            
		$catalogSKU = array();
		if( $UseSKU ){
			foreach( $arIBlock as $iblocID ){
				if( $iblock = CCatalog::GetByID( $iblocID ) ){
					if( intval( $iblock["OFFERS_IBLOCK_ID"] ) > 0 )
						$catalogSKU[] = $iblock["OFFERS_IBLOCK_ID"];
				}
			}
		}
        
        $arIblocks = array_merge( $arIBlock, $catalogSKU );
        $arIblocks = array_unique( $arIblocks );
        
		return $arIblocks;
	}
    
    public function GetFileExportType(){
        return array( "xml", "csv" );
    }

    public function GetRunType(){
        return array("comp", /*"agent", */"cron");
    }

    function memoryUsage( $usage, $base_memory_usage = 0 ){
        printf( "Bytes diff: %d\n", $usage - $base_memory_usage );
    }

    public static  function PathFilterHandler( $value ){
        if( $value == "." || $value == ".." )
            return false;
        
        return $value;
    }
    
    public static function PathFilter( $arPath ){
        if( !is_array( $arPath ) )
            return array();
            
        return array_filter( $arPath, array( self, "PathFilterHandler" ) );
    }
    
    public function GetSchemeName(){
        $schemes = array(
            "NAME_SKU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_OFFER_NAME" ),
            "NAME_OFFER" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_PRODUCT_NAME" ),
            "NAME_OFFER_SKU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_OFFER_PRODUCT_NAME" ),
        );
        return $schemes;
    }
    
    public function GetSchemePreviewText(){
        $schemes = array(
            "PREVIEW_TEXT_SKU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_PREVIEW_OFFER_NAME" ),
            "PREVIEW_TEXT_OFFER" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_PREVIEW_PRODUCT_NAME" ),
            "PREVIEW_TEXT_OFFER_SKU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_PREVIEW_OFFER_PRODUCT_NAME" ),
        );
        return $schemes;
    }
    
    public function GetSchemeDetailText(){
        $schemes = array(
            "DETAIL_TEXT_SKU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_OFFER_NAME" ),
            "DETAIL_TEXT_OFFER" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_PRODUCT_NAME" ),
            "DETAIL_TEXT_OFFER_SKU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_OFFER_PRODUCT_NAME" ),
        );
        return $schemes;
    }
    
    public function GetSchemeDetailPicture(){
        $schemes = array(
            "DETAIL_PICTURE_SKU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_OFFER_DETAIL_PICTURE" ),
            "DETAIL_PICTURE_OFFER" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_PRODUCT_DETAIL_PICTURE" ),
        );
        return $schemes;
    }
    
    public function GetSchemeQuantity(){
        $schemes = array(
            "CATALOG_QUANTITY_SKU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_OFFER_QUANTITY" ),
            "CATALOG_QUANTITY_OFFER" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_PRODUCT_QUANTITY" ),
        );
        return $schemes;
    }
    
    public function GetSchemeQuantityReserved(){
        $schemes = array(
            "CATALOG_QUANTITY_RESERVED_SKU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_OFFER_QUANTITY_RESERVED" ),
            "CATALOG_QUANTITY_RESERVED_OFFER" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_PRODUCT_QUANTITY_RESERVED" ),
        );
        return $schemes;
    }
    
    public function GetSchemeWeight(){
        $schemes = array(
            "CATALOG_WEIGHT_SKU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_OFFER_WEIGHT" ),
            "CATALOG_WEIGHT_OFFER" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_PRODUCT_WEIGHT" ),
        );
        return $schemes;
    }
    
    public function GetSchemeWidth(){
        $schemes = array(
            "CATALOG_WIDTH_SKU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_OFFER_WIDTH" ),
            "CATALOG_WIDTH_OFFER" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_PRODUCT_WIDTH" ),
        );
        return $schemes;
    }
    
    public function GetSchemeLength(){
        $schemes = array(
            "CATALOG_LENGTH_SKU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_OFFER_LENGTH" ),
            "CATALOG_LENGTH_OFFER" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_PRODUCT_LENGTH" ),
        );
        return $schemes;
    }
    
    public function GetSchemeHeight(){
        $schemes = array(
            "CATALOG_HEIGHT_SKU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_OFFER_HEIGHT" ),
            "CATALOG_HEIGHT_OFFER" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_PRODUCT_HEIGHT" ),
        );
        return $schemes;
    }
    
    public function GetSchemePurchasingPrice(){
        $schemes = array(
            "CATALOG_PURCHASING_PRICE_SKU" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_OFFER_PURCHASING_PRICE" ),
            "CATALOG_PURCHASING_PRICE_OFFER" => GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_PRODUCT_PURCHASING_PRICE" ),
        );
        return $schemes;
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