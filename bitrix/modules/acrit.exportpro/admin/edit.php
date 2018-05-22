<?php

$moduleId = "acrit.exportpro";
require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php" );
require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/$moduleId/include.php" );

IncludeModuleLangFile( __FILE__ );

global $ID, $PROFILE, $save, $apply, $copy;

$dbProfile = new CExportproProfileDB();
$profileUtils = new CExportproProfile();

$siteEncoding = array(
    "utf-8" => "utf8",
    "UTF8" => "utf8",
    "UTF-8" => "utf8",
    "WINDOWS-1251" => "cp1251",
    "windows-1251" => "cp1251",
    "CP1251" => "cp1251",
);

CModule::IncludeModule( "catalog" );

function CheckFields(){
    global $PROFILE, $APPLICATION, $ID;
    if( intval( $ID ) > 0 ){
        $export = new CAcritExportproExport( $ID );
    }
    $requiredFields = array(
        "NAME", "CODE", "SHOPNAME", "COMPANY", "DOMAIN_NAME"
    );

    foreach( $requiredFields as $field ){
        if( !$PROFILE[$field] ){
            $APPLICATION->ThrowException( GetMessage( "ACRIT_EXPORTPRO_REQUIRED_FIELD_FAIL" ).'"'.GetMessage( "ACRIT_EXPORTPRO_STEP1_".$field ).'"' );
            return false;
        }
    }

    foreach( $PROFILE["XMLDATA"] as $id => $field ){
        if( $field["REQUIRED"] == "Y" ){
            if( ( $field["TYPE"] == "field" && !$field["VALUE"] ) || ( $field["TYPE"] == "const" && !$field["CONTVALUE_TRUE"] ) || $field["TYPE"] == "none" ){
                $APPLICATION->ThrowException( GetMessage( "ACRIT_EXPORTPRO_REQUIRED_FIELD_FAIL", array( "#CODE#" => $field["CODE"], "#NAME#" => $field["NAME"] ) ) );
                return false;
            }
        }
    }
    return true;
}

function PrepareFields(){
    global $PROFILE, $APPLICATION, $ID;

    foreach( $PROFILE["XMLDATA"] as $id => $field ){
        if( $field["TYPE"] == "none" ){
            $PROFILE["XMLDATA"][$id]["VALUE"] = "";
            $PROFILE["XMLDATA"][$id]["CONTVALUE_TRUE"] = "";
            $PROFILE["XMLDATA"][$id]["CONTVALUE_FALSE"] = "";
        }
    }
    return true;
}

$fieldsCheck = true;
                                                                                   
if( $_SERVER["REQUEST_METHOD"] == "POST" && ( !empty( $save ) || !empty( $apply ) ) && check_bitrix_sessid() ){
    if( ( $fieldsCheck = CheckFields() ) ){
        PrepareFields();
        if( CModule::IncludeModule( "catalog" ) ){
            $obCond = new CAcritExportproCatalogCond();

            $boolCond = $obCond->Init( BT_COND_MODE_PARSE, 0, array() );
            if( !$boolCond ){
                if( $ex = $APPLICATION->GetException() ){
                    echo $ex->GetString()."<br>";
                }
            }
        }

        foreach( $PROFILE["XMLDATA"] as $id => $field ){
            if( intval( $id ) > 0 ){
                $PROFILE["XMLDATA"][$field["CODE"]] = $field;
                unset( $PROFILE["XMLDATA"][$id] );
            }
        }
        foreach( $PROFILE["XMLDATA"] as $id => $field ){
            if( !empty( $field["CONDITION"] ) && CModule::IncludeModule( "catalog" ) ){
                $PROFILE["XMLDATA"][$id]["CONDITION"] = $obCond->Parse( $field["CONDITION"] );
            }

            if( !isset( $field["DELETE_ONEMPTY"] ) ){
                $PROFILE["XMLDATA"][$id]["DELETE_ONEMPTY"] = "N";
            }

            if( !isset( $field["HTML_ENCODE"] ) ){
                $PROFILE["XMLDATA"][$id]["HTML_ENCODE"] = "N";
            }

            if( !isset( $field["HTML_ENCODE_CUT"] ) ){
                $PROFILE["XMLDATA"][$id]["HTML_ENCODE_CUT"] = "N";
            }

            if( !isset( $field["HTML_TO_TXT"] ) ){
                $PROFILE["XMLDATA"][$id]["HTML_TO_TXT"] = "N";
            }

            if( !isset( $field["SKIP_UNTERM_ELEMENT"] ) ){
                $PROFILE["XMLDATA"][$id]["SKIP_UNTERM_ELEMENT"] = "N";
            }
        }

        if( !empty( $PROFILE["CONDITION"] ) && CModule::IncludeModule( "catalog" ) ){
            $PROFILE["CONDITION"] = $obCond->Parse( $PROFILE["CONDITION"] );
        }

        if( $PROFILE["TYPE"] == "ua_hotline_ua" ){
            $firmIdPos = stripos( $PROFILE["FORMAT"], "<firmId>" );
            if( $firmIdPos !== false ){
                $firmIdFinalPos = stripos( $PROFILE["FORMAT"], "</firmId>" ) + 11;
                if( $firmIdFinalPos !== false ){
                    $PROFILE["FORMAT"] = substr_replace( $PROFILE["FORMAT"], "", $firmIdPos, ( $firmIdFinalPos - $firmIdPos ) );
                }
            }
            $PROFILE["FORMAT"] = str_replace( "</firmName>", "</firmName>".PHP_EOL."<firmId>".$PROFILE["HOTLINE_FIRM_ID"]."</firmId>", $PROFILE["FORMAT"] );
        }

        if( $PROFILE["TYPE"] == "google" ){
            $titlePos = stripos( $PROFILE["FORMAT"], "<title>" );
            if( $titlePos !== false ){
                $titleFinalPos = stripos( $PROFILE["FORMAT"], "<link>" );
                if( $titleFinalPos !== false ){
                    $PROFILE["FORMAT"] = substr_replace( $PROFILE["FORMAT"], "", $titlePos, ( $titleFinalPos - $titlePos ) );
                }
            }

            $PROFILE["FORMAT"] = str_replace( "<link>", "<title>".$PROFILE["GOOGLE_GOOGLEFEED"]."</title>".PHP_EOL."<link>", $PROFILE["FORMAT"] );
        }

        $temp = array();
        
        if( isset( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"] ) && is_array( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"] ) ){
	        $bNotEmptyMarketCategoryListValue = false;
            foreach( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"] as $k => $v ){
	            $bNotEmptyCurrentMarketCategoryListValue = false;
                if( strlen( trim( $v ) ) > 0 ){
                    if( !$bNotEmptyMarketCategoryListValue ){
                        $bNotEmptyMarketCategoryListValue = true;
                    }
                    $bNotEmptyCurrentMarketCategoryListValue = true;
                }
                
                if( $bNotEmptyCurrentMarketCategoryListValue ){
                    $rsParentSection = CIBlockSection::GetByID( $k );
	                if( $arParentSection = $rsParentSection->GetNext() ){
	                    $temp[$k] = $arParentSection["IBLOCK_SECTION_ID"];
	    
	                    $arFilter = array(
                            "IBLOCK_ID" => $arParentSection["IBLOCK_ID"],
                            ">LEFT_MARGIN" => $arParentSection["LEFT_MARGIN"],
                            "<RIGHT_MARGIN" => $arParentSection["RIGHT_MARGIN"],
                            ">DEPTH_LEVEL" => $arParentSection["DEPTH_LEVEL"]
                        );
                        
	                    $rsSect = CIBlockSection::GetList(
                            array(
                                "left_margin" => "asc"
                            ),
                            $arFilter,
                            false,
                            array(
                                "ID",
                                "IBLOCK_SECTION_ID"
                            )
                        );
	                    
                        while( $arSect = $rsSect->GetNext() ){
	                        $temp[$arSect["ID"]] = $arSect["IBLOCK_SECTION_ID"];
	                    }
	                }
                }    
	        }
		}
        
        if( $bNotEmptyMarketCategoryListValue ){
            $maxDepth = 0;
            $i = 0;
            $rsSect = CIBlockSection::GetList( array( "DEPTH_LEVEL" => "DESC" ), array( ">IBLOCK_ID" => 0 ), false, array( "DEPTH_LEVEL" ) );
            while( $arSect = $rsSect->GetNext() ){
                $i++;
                $maxDepth = $arSect["DEPTH_LEVEL"];
                if( $i == 1 ){
                    break;
                }
            }

            foreach( $temp as $k => $v ){
                $tempCatName = "";
                if( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"][$k] ){
                    $tempCatName = $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"][$k];
                }

                $j = 0;
                while( $j++ < $maxDepth ){
                    foreach( $temp as $k_ => $v_ ){
                        if( $v_ == $k ){
                            if( $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"][$k_] ){
                                $tempCatName = $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"][$k_];
                            }
                            else{
                                if( $tempCatName ){
                                    $PROFILE["MARKET_CATEGORY"]["CATEGORY_LIST"][$k_] = $tempCatName;
                                }
                            }
                        }
                    }
                }
            }
        }
        else{
            $PROFILE["MARKET_CATEGORY"] = null;
        }
        unset( $temp );
        
        if( $PROFILE["TYPE"] != "activizm" ){
            $PROFILE["VARIANT"] = null;
        }
        
        if( $PROFILE["CURRENCY"]["CONVERT_CURRENCY"] != "Y" ){
            $PROFILE["CURRENCY"] = null;
        }                  

        $arFields = array(
            "ACTIVE" => ( $PROFILE["ACTIVE"] <> "Y" ? "N" : "Y" ),
            "NAME" => $PROFILE["NAME"],
            "CODE" => $PROFILE["CODE"],
            "DESCRIPTION" => $PROFILE["DESCRIPTION"],
            "SHOPNAME" => $PROFILE["SHOPNAME"],
            "COMPANY" => $PROFILE["COMPANY"],
            "DOMAIN_NAME" => $PROFILE["DOMAIN_NAME"],
            "LID" => $PROFILE["LID"],
            "ENCODING" => $PROFILE["ENCODING"],
            "IBLOCK_TYPE_ID" => $PROFILE["IBLOCK_TYPE_ID"],
            "IBLOCK_ID" => $PROFILE["IBLOCK_ID"],
            "CATEGORY" => $PROFILE["CATEGORY"],
            "USE_SKU" => ( $PROFILE["USE_SKU"] <> "Y" ? "N" : "Y" ),
            "CHECK_INCLUDE" => ( $PROFILE["CHECK_INCLUDE"] <> "Y" ? "N" : "Y" ),
            "VIEW_CATALOG" => ( $PROFILE["VIEW_CATALOG"] <> "Y" ? "N" : "Y" ),
            "OTHER" => ( $PROFILE["OTHER"] <> "Y" ? "N" : "Y" ),
            "TYPE" => $PROFILE["TYPE"],
            "NAMESCHEMA" => $PROFILE["NAMESCHEMA"],
            "FORMAT" => $PROFILE["FORMAT"],
            "OFFER_TEMPLATE" => $PROFILE["OFFER_TEMPLATE"],
            "CURRENCY_TEMPLATE" => $PROFILE["CURRENCY_TEMPLATE"],
            "CATEGORY_TEMPLATE" => $PROFILE["CATEGORY_TEMPLATE"],
            "CATEGORY_INNER_TEMPLATE" => $PROFILE["CATEGORY_INNER_TEMPLATE"],
            "DATEFORMAT" => $PROFILE["DATEFORMAT"],
            "SITE_PROTOCOL" => $PROFILE["SITE_PROTOCOL"],
            "XMLDATA" => $PROFILE["XMLDATA"],
            "CONVERT_DATA" => $PROFILE["CONVERT_DATA"],
            "CONDITION" => $PROFILE["CONDITION"],
            "CURRENCY" => $PROFILE["CURRENCY"],
            "MARKET_CATEGORY" => $PROFILE["MARKET_CATEGORY"],
            "EXPORT_PARENT_CATEGORIES" => ( $PROFILE["EXPORT_PARENT_CATEGORIES"] == "Y" ) ? "Y" : "N",
            "EXPORT_PARENT_CATEGORIES_TO_OFFER" => ( $PROFILE["EXPORT_PARENT_CATEGORIES_TO_OFFER"] == "Y" ) ? "Y" : "N",
            "EXPORT_OFFER_CATEGORIES_TO_OFFER" => ( $PROFILE["EXPORT_OFFER_CATEGORIES_TO_OFFER"] == "Y" ) ? "Y" : "N",
            "EXPORT_PARENT_CATEGORIES_WITH_IBLOCK_FIELDS" => ( $PROFILE["EXPORT_PARENT_CATEGORIES_WITH_IBLOCK_FIELDS"] == "Y" ) ? "Y" : "N",
            "USE_COMPRESS" => ( $PROFILE["USE_COMPRESS"] == "Y" ) ? "Y" : "N",
            "USE_MARKET_CATEGORY" => ( $PROFILE["USE_MARKET_CATEGORY"] == "Y" ) ? "Y" : "N",
            "SETUP" => $PROFILE["SETUP"],
            "USE_VARIANT" => ( $PROFILE["USE_VARIANT"] == "Y" ) ? "Y" : "N",
            "VARIANT" => $PROFILE["VARIANT"],
            "OZON_APPID" => $PROFILE["OZON_APPID"],
            "OZON_APPKEY" => $PROFILE["OZON_APPKEY"],
            "HOTLINE_FIRM_ID" => $PROFILE["HOTLINE_FIRM_ID"],
            "GOOGLE_GOOGLEFEED" => $PROFILE["GOOGLE_GOOGLEFEED"],
            "TIMESTAMP_X" => date( "d.m.Y H:i:s" ),
            "SEND_LOG_EMAIL" => ( check_email( $PROFILE["SEND_LOG_EMAIL"] ) ? $PROFILE["SEND_LOG_EMAIL"] : "" ),
            "USE_REMARKETING" => ( $PROFILE["USE_REMARKETING"] <> "Y" ? "N" : "Y" ),
        );

        if( $ID ){
            $dbProfile->Update( $ID, $arFields );
        }
        else{
            $ID = $dbProfile->Add( $arFields );
        }

        switch( $PROFILE["SETUP"]["TYPE_RUN"] ){
            case "cron":
                if( $PROFILE["ACTIVE"] == "Y" ){
                    CExportproAgent::AddAgent( $ID, $PROFILE["SETUP"] );
                }
                break;
            case "comp":
                CExportproAgent::DelAgent( $ID );
                CExportproCron::CronRun( $ID, $PROFILE["SETUP"], true );
                break;
        }

        if( $PROFILE["ACTIVE"] <> "Y" ){
            CExportproAgent::DelAgent( $ID );
            CExportproCron::CronRun( $ID, $PROFILE["SETUP"], true );
        }

        if( $save ){
            LocalRedirect( "acrit_exportpro_list.php" );
            die();
        }
        else{
            $query = parse_url( $_SERVER["REQUEST_URI"], PHP_URL_QUERY );
            parse_str( $query, $arQuery );

            if( !isset( $arQuery["ID"] ) || empty( $arQuery["ID"] ) ){
                $arQuery["ID"] = $ID;
                LocalRedirect( "acrit_exportpro_edit.php?".http_build_query( $arQuery ) );
                die();
            }
        }
    }
}

if( $fieldsCheck ){
    if( !isset( $ID ) ){
        $arProfile = $profileUtils->GetDefaults();
    }
    else{
        $arProfile = $dbProfile->GetByID( $ID );
    }
}
else{
    $arProfile = $PROFILE;
}

$t = CJSCore::getExtInfo( "jquery" );
if( !is_array( $t ) || !isset( $t["js"] ) || !file_exists( $DOCUMENT_ROOT.$t["js"] ) ){
    $APPLICATION->ThrowException( GetMessage( "ACRIT_EXPORTPRO_JQUERY_REQUIRE" ) );
}

if( !isset( $_REQUEST["ajax"] ) && !isset( $_REQUEST["ib"] ) && !isset( $_REQUEST["ajax_start"] ) && !isset( $_REQUEST["ajax_count"] ) && !isset( $_POST["auth"] ) ){
    CUtil::InitJSCore( array( "ajax", "jquery" ) );
    $APPLICATION->AddHeadScript( "/bitrix/js/iblock/iblock_edit.js" );
    $APPLICATION->AddHeadScript( "/bitrix/js/acrit.exportpro/main.js" );

    if( !CModule::IncludeModule( "iblock" ) ){
        return false;
    }

    $catalog = ( !CModule::IncludeModule( "catalog" ) ) ? false : true;
    $currency = ( !CModule::IncludeModule( "currency" ) ) ? false : true;

    IncludeModuleLangFile( __FILE__ );

    $POST_RIGHT = $APPLICATION->GetGroupRight( $moduleId );
    if( $POST_RIGHT == "D" ){
        $APPLICATION->AuthForm( GetMessage( "ACCESS_DENIED" ) );
    }

    $ID = intval( $ID );		// Id of the edited record
    $bCopy = ( $action == "copy" );
    $message = null;
    $bVarsFromForm = false;
    $profileTitle = GetMessage( "ACRIT_EXPORTPRO_EDITPROFILE" ).": #".$arProfile["ID"]." ".$arProfile["NAME"];
    $APPLICATION->SetTitle( ( $ID > 0 ? $profileTitle : GetMessage( "ACRIT_EXPORTPRO_ADDPROFILE" ) ) );
    if( $copy && $ID > 0 ){
        unset( $arProfile["ID"] );       
        $ID = $dbProfile->Add( $arProfile );
        if( !$ID ){
            LocalRedirect( $APPLICATION->GetCurPageParam( "", array( "ID", "copy" ) ) );
        }
        else{
            LocalRedirect( $APPLICATION->GetCurPageParam( "ID=$ID", array( "ID", "copy" ) ) );
        }
        die();
    }

    $aContext = array(
        array(
            "TEXT" => GetMessage( "MAIN_ADD" ),
            "LINK" => "acrit_exportpro_edit.php?lang=".LANG,
            "TITLE" => GetMessage( "PARSER_ADD_TITLE" ),
            "ICON" => "btn_new",
        ),
    );

    // add attach it to list
    $sTableID = "tbl_acritprofile";
    $oSort = new CAdminSorting( $sTableID, "ID", "desc" );
    $lAdmin = new CAdminList( $sTableID, $oSort );
    $lAdmin->AddAdminContextMenu( $aContext );
    $lAdmin->CheckListMode();

    require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );

    AcritLicence::Show();

    $aMenu = array(
        array(
            "TEXT" => GetMessage( "ACRIT_EXPORTPRO_LIST" ),
            "TITLE" => GetMessage( "ACRIT_EXPORTPRO_LIST" ),
            "LINK" => "acrit_exportpro_list.php?lang=".LANG,
            "ICON" => "btn_list",
        ),
        array(
            "TEXT" => GetMessage( "ACRIT_EXPORTPRO_INSTRUCTION" ),
            "TITLE" => GetMessage( "ACRIT_EXPORTPRO_INSTRUCTION" ),
            "LINK" => "http://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/",
            "LINK_PARAM" => "target='blank'",
            "ICON" => "",
        ),
    );

    if( $ID > 0 ){
        $aMenu[] = array( "SEPARATOR" => "Y" );
        $aMenu[] = array(
            "TEXT" => GetMessage( "ACRIT_EXPORTPRO_ADD" ),
            "TITLE" => GetMessage( "ACRIT_EXPORTPRO_ADD" ),
            "LINK" => "acrit_exportpro_edit.php?lang=".LANG,
            "ICON" => "btn_new",
        );
        $aMenu[] = array(
            "TEXT" => GetMessage( "ACRIT_EXPORTPRO_COPY" ),
            "TITLE" => GetMessage( "ACRIT_EXPORTPRO_COPY" ),
            "LINK" => "acrit_exportpro_edit.php?copy=$ID&ID=$ID&lang=".LANG,
            "ICON" => "btn_copy",
        );
        $aMenu[] = array(
            "TEXT" => GetMessage( "ACRIT_EXPORTPRO_DEL" ),
            "TITLE" => GetMessage( "ACRIT_EXPORTPRO_DEL" ),
            "LINK" => "javascript:if(confirm('".GetMessage( "parser_mnu_del_conf" )."'))window.location='acrit_exportpro_list.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."';",
            "ICON" => "btn_delete",
        );
        $aMenu[] = array( "SEPARATOR" => "Y" );
        $aMenu[] = array(
            "TEXT" => GetMessage( "ACRIT_EXPORTPRO_RUN" ),
            "TITLE" => GetMessage( "ACRIT_EXPORTPRO_RUN" ),
            "LINK" => "/bitrix/tools/acrit.exportpro/acrit_exportpro.php?ID=".$ID,
            "ICON" => "btn_start_catalog"
        );
    }
    $context = new CAdminContextMenu( $aMenu );
    $context->Show();

    $aTabs = array(
        array(
            "DIV" => "step3",
            "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB3" ),
            "ICON" => "main_user_edit",
            "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB3" )
        ),
    );

    $aTabs[] = array( "DIV" => "step16", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB16" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB16" ) );
    $aTabs[] = array( "DIV" => "step17", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB17" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB17" ) );
    $aTabs[] = array( "DIV" => "step18", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB18" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB18" ) );
    $aTabs[] = array( "DIV" => "step1", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB1" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB1" ) );
    $aTabs[] = array( "DIV" => "step2", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB2" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB2" ) );
    $aTabs[] = array( "DIV" => "step4", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB4" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB4" ) );
    $aTabs[] = array( "DIV" => "step5", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB5" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB5" ) );
    $aTabs[] = array( "DIV" => "step19", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB19" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB19" ) );
    $aTabs[] = array( "DIV" => "step21", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB21" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB21" ) );

    if( CModule::IncludeModule( "catalog" ) ){
        $aTabs[] = array( "DIV" => "step6", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB6" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB6" ) );
        $aTabs[] = array( "DIV" => "step7", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB7" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB7" ) );
        $aTabs[] = array( "DIV" => "step8", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB8" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB8" ) );
        $aTabs[] = array( "DIV" => "step12", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB12" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB12" ) );
        $aTabs[] = array( "DIV" => "step13", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB13" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB13" ) );
    }

    $aTabs[] = array( "DIV" => "step10", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB10" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB10" ) );
    $aTabs[] = array( "DIV" => "step9", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB9" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB9" ) );
    $aTabs[] = array( "DIV" => "step11", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB11" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB11" ) );
    $aTabs[] = array( "DIV" => "step14", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB14" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB14" ) );
    $aTabs[] = array( "DIV" => "step15", "TAB" => GetMessage( "ACRIT_EXPORTPRO_TAB15" ), "ICON" => "main_user_edit", "TITLE" => GetMessage( "ACRIT_EXPORTPRO_TAB15" ) );

    $tabControl = new CAdminTabControl( "tabControl", $aTabs );

    if( $e = $APPLICATION->GetException() ){
        CAdminMessage::ShowMessage(
            array(
                "MESSAGE" => $e->GetString(),
                "HTML" => "TRUE",
            )
        );
    }

    require __DIR__."/auto_tests.php";

    $bDemo = false;
    $moduleStatus = CModule::IncludeModuleEx( $moduleId );
    if( $moduleStatus != 1 ){
        $bDemo = true;

        $timeDemoOff = "";
        if( $info = CModule::CreateModuleObject( $moduleId ) ){
            $timeDemoOff = ConvertTimeStamp( $GLOBALS["SiteExpireDate_".str_replace( ".", "_", $info->MODULE_ID )], "SHORT" );
        }
    }

    $byLicenceUrl = "http://www.acrit-studio.ru/market/rabota-s-torgovymi-ploshchadkami/eksport-tovarov-na-torgovye-portaly/?action=BUY&id=8915";
    ?>
    <div class="adm-info-message"<?if( $bDemo ){?> style="float: left;"<?}?>><?=GetMessage( "ACRIT_EXPORTPRO_EXPORT_CHANGE_PROFILE_INFO" );?></div>

    <?if( $bDemo ){?>
        <div class="adm-info-message" style="float: right; padding-left: 20px;">
            <div style="padding-left: 20px; float: right;">
                <a href="<?=$byLicenceUrl?>" target="_blank" class="adm-btn adm-btn-save"><?=GetMessage( "ACRIT_EXPORTPRO_EXPORT_BUY_LICENCE_INFO" )?></a>
            </div>
            <div style="float: left; padding-top: 5px;"><?=GetMessage( "ACRIT_EXPORTPRO_EXPORT_DEMO_PERIOD_INFO" )."<b>".$timeDemoOff."</b>";?></div>
            <div style="clear: both;"></div>
        </div>
    <?}?>

    <div style="clear: both;"></div>

    <form method="POST" action="" ENCTYPE="multipart/form-data" id="exportpro_form" name="exportpro_form">
    <?// check session id?>
        <?=bitrix_sessid_post();?>
        <?// show bookmark headers
        $tabControl->Begin();?>
        <div id="waitContainer" style="position: fixed; float: right; width: 100%; right: 0;"></div>
        <?foreach( $aTabs as $tab ){
            $tabControl->BeginNextTab();
            require( __DIR__."/tabs/{$tab["DIV"]}.php" );
        }

    	// end of form - show save buttons
        $tabControl->Buttons(
            array(
                "disabled"=>($POST_RIGHT<"W"),
                "back_url"=>"acrit_exportpro_list.php?lang=".LANG,
            )
        );

    	// end of bookmark interface
        $tabControl->End();
        $tabControl->ShowWarnings( "exportpro_form", $message );
        ?>
    </form>

    <form target="_blank" name="fticket" action="<?=GetMessage( "A_SUPPORT_URL" );?>" method="POST">
        <input type="hidden" name="send_ticket" value="Y">
        <input type="hidden" name="ticket_title" value="<?=GetMessage( "SC_RUS_L1" )." ".htmlspecialcharsbx( $_SERVER["HTTP_HOST"] );?>">
        <input type="hidden" name="ticket_text" value="Y">
        <input type="hidden" name="ticket_log" value="Y">
    </form>
    <script type="text/javascript">
        function SubmitToSupport(){
            var frm = document.forms.fticket;

            frm.ticket_text.value = BX( 'ticket_text_proxy' ).value;

            if( frm.ticket_text.value == '' ){
                alert( '<?=GetMessage( "SC_NOT_FILLED" )?>' );
                return;
            }
            frm.ticket_log.value = BX( 'ticket_text_log' ).value;
            frm.submit();
        }
    </script>
    <script>
        function ShowMarketCategoryList( categoryId, listContainer ){
            MarketCategoryItem = categoryId;
            listContainer = typeof listContainer == 'undefined' ? 'market_category_list' : listContainer;
            if( typeof MarketCategoryObject == 'undefined' ){
                MarketCategoryObject = new BX.PopupWindow( 'my_answer', null, {
                    content: BX( listContainer ),
                    closeIcon: { right: '20px', top: '10px' },
                    titleBar: { content: BX.create( 'span', { html: '<?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CATEGORY_POPUP_TITLE" );?>', 'props': { 'className': 'access-title-bar' } } ) },
                    zIndex: 0,
                    offsetLeft: 0,
                    offsetTop: 0,
                    draggable: { restrict: false },
                    buttons: [
                        //   new BX.PopupWindowButton({
						//	  text: '<?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CATEGORY_BUTTON_SEND" );?>',
                        //	  className: 'popup-window-button-accept',
                        //	  events: {click: function(){
						//		 BX.ajax.submit( BX( 'myForm' ), function( data ){ <?// send form data to action file?>
                        //			BX( 'ajax-add-answer' ).innerHTML = data;
                        //		  });
                        //	  }}
                        //   }),
                        //   new BX.PopupWindowButton({
						//	  text: '<?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CATEGORY_BUTTON_CLOSE" );?>',
                        //	  className: 'webform-button-link-cancel',
                        //	  events: {click: function(){
						//		 this.popupWindow.close(); // закрытие окна
                        //	  }}
                        //   })
                    ],
                });
            }
            MarketCategoryObject.show();
        }
        function ShowPropertyList( obj ){
            PropertyListItem = $( obj ).attr( 'name' );
            PropertyListItemValue = $( obj ).attr( 'data-value' );
            $( '#property_list select' ).val( $( 'input[name="' + PropertyListItemValue + '"]' ).val() );
            if( typeof PropertyListObject == 'undefined' ){
                PropertyListObject = new BX.PopupWindow( 'property_list_answer', null, {
                    content: BX( 'property_list' ),
                    closeIcon: { right: '20px', top: '10px' },
                    titleBar: { content: BX.create( 'span', { html: '<?=GetMessage( "ACRIT_EXPORTPRO_VARIANT_CATEGORY_POPUP_TITLE" );?>', 'props': { 'className': 'access-title-bar' } } ) },
                    zIndex: 0,
                    offsetLeft: 0,
                    offsetTop: 0,
                    draggable: { restrict: false },
                });
            }
            PropertyListObject.show();
        }
        $( function(){
            <?
                switch( $arProfile["TYPE"] ){
                    case "activizm":
                        echo "$( '#tab_cont_step12' ).hide();";
                        echo "$( '#tab_cont_step13' ).hide();";
                        echo "$( '#tab_cont_step16' ).hide();";
                        echo "$( '#tab_cont_step17' ).hide();";
                        echo "$( '#tab_cont_step18' ).hide();";
                        echo "$( '#tab_cont_step6' ).hide();";
                        echo "$( '#tab_cont_step21' ).hide();";
                        echo "$( '#market_category_data_ozon, #market_category_data_ebay' ).remove();";
                        break;
                    case "ebay_1":
                    case "ebay_2":
                        echo "$( '#tab_cont_step6' ).hide();";
                        echo "$( '#tab_cont_step10' ).hide();";
                        echo "$( '#tab_cont_step13' ).hide();";
                        echo "$( '#tab_cont_step16' ).hide();";
                        echo "$( '#tab_cont_step17' ).hide();";
                        echo "$( '#tab_cont_step18' ).hide();";
                        echo "$( '#tab_cont_step21' ).hide();";
                        echo "$( '#market_category_data_ozon' ).remove();";
                        break;
                    case "ozon":
                        echo "$( '#tab_cont_step10' ).hide();";
                        echo "$( '#tab_cont_step12' ).hide();";
                        echo "$( '#tab_cont_step6' ).hide();";
                        echo "$( '#tab_cont_step17' ).hide();";
                        echo "$( '#tab_cont_step18' ).hide();";
                        echo "$( '#tab_cont_step21' ).hide();";
                        echo "$( '#market_category_data_ebay' ).remove();";
                        break;
                    case "ua_hotline_ua":
                        echo "$( '#tab_cont_step10' ).hide();";
                        echo "$( '#tab_cont_step12' ).hide();";
                        echo "$( '#tab_cont_step13' ).hide();";
                        echo "$( '#tab_cont_step16' ).hide();";
                        echo "$( '#tab_cont_step18' ).hide();";
                        echo "$( '#tab_cont_step21' ).hide();";
                        echo "$( '#market_category_data_ozon, #market_category_data_ebay' ).remove();";
                        break;
                    case "google":
                        echo "$( '#tab_cont_step10' ).hide();";
                        echo "$( '#tab_cont_step12' ).hide();";
                        echo "$( '#tab_cont_step13' ).hide();";
                        echo "$( '#tab_cont_step16' ).hide();";
                        echo "$( '#tab_cont_step17' ).hide();";
                        echo "$( '#tab_cont_step21' ).hide();";
                        echo "$( '#market_category_data_ozon, #market_category_data_ebay' ).remove();";
                        break;
                    case "tiu_standart":
                    case "tiu_standart_vendormodel":
                        echo "$( '#tab_cont_step10' ).hide();";
                        echo "$( '#tab_cont_step12' ).hide();";
                        echo "$( '#tab_cont_step13' ).hide();";
                        echo "$( '#tab_cont_step16' ).hide();";
                        echo "$( '#tab_cont_step17' ).hide();";
                        echo "$( '#tab_cont_step18' ).hide();";
                        echo "$( '#tab_cont_step21' ).hide();";
                        echo "$( '#market_category_data_ozon, #market_category_data_ebay' ).remove();";
                        break;
                    case "mailru":
                    case "mailru_clothing":
                        echo "$( '#tab_cont_step10' ).hide();";
                        echo "$( '#tab_cont_step12' ).hide();";
                        echo "$( '#tab_cont_step13' ).hide();";
                        echo "$( '#tab_cont_step16' ).hide();";
                        echo "$( '#tab_cont_step17' ).hide();";
                        echo "$( '#tab_cont_step18' ).hide();";
                        echo "$( '#market_category_data_ozon, #market_category_data_ebay' ).remove();";
                        break;
                    case "1c_trade":
                        echo "$( '#tab_cont_step6' ).hide();";
                        echo "$( '#tab_cont_step10' ).hide();";
                        echo "$( '#tab_cont_step12' ).hide();";
                        echo "$( '#tab_cont_step13' ).hide();";
                        echo "$( '#tab_cont_step16' ).hide();";
                        echo "$( '#tab_cont_step17' ).hide();";
                        echo "$( '#tab_cont_step18' ).hide();";
                        echo "$( '#tab_cont_step21' ).hide();";
                        echo "$( '#market_category_data_ozon, #market_category_data_ebay' ).remove();";
                    	break;
                    default:
                        echo "$( '#tab_cont_step10' ).hide();";
                        echo "$( '#tab_cont_step12' ).hide();";
                        echo "$( '#tab_cont_step13' ).hide();";
                        echo "$( '#tab_cont_step16' ).hide();";
                        echo "$( '#tab_cont_step17' ).hide();";
                        echo "$( '#tab_cont_step18' ).hide();";
                        echo "$( '#tab_cont_step21' ).hide();";
                        echo "$( '#market_category_data_ozon, #market_category_data_ebay' ).remove();";
                        break;
                }
            ?>
        });
    </script>

    <?require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php" );
}?>