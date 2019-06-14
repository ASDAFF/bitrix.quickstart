<?php
use Bitrix\Main\Localization\Loc;
Loc::loadMessages( __FILE__ );

require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client.php" );

class CExportproInformer{
    private static $moduleId = "acrit.exportpro";
    private static $modulePrefix = "acrit";
    private static $timeExpire = 2592000;
    
    private function GetSiteInfo(){
        $dbSite = CSite::GetList(
            $by = "sort",
            $order = "asc",
            array(
                "ACTIVE" => "Y",
            )
        );
                                                               
        $arProcessSite = array();
        if( $arSite = $dbSite->Fetch() ){
            $arProcessSite["LID"] = $arSite["LID"];
            $arProcessSite["DOMAIN_NAME"] = $arSite["SERVER_NAME"];
            $arProcessSite["SITE_NAME"] = $arSite["SITE_NAME"];
            $arProcessSite["DESCRIPTION"] = $arSite["NAME"];
            $arProcessSite["SITE_PROTOCOL"] = ( ( CMain::IsHTTPS() ) ? "https" : "http" );
        }
        
        return $arProcessSite;
    }
    
    private function GetAdminList(){
        $dbAdminUsers = CUser::GetList(
            ( $by = "ID" ),
            ( $order = "asc" ),
            array(
                "GROUPS_ID" => array( 1 ),
            )
        );
        
        $arResultAdminUsers = array();
        while( $arAdminUser = $dbAdminUsers->Fetch() ){
            $arResultAdminUser = array();
            $arResultAdminUser["ID"] = $arAdminUser["ID"];
            $arResultAdminUser["FULL_NAME"] = $arAdminUser["NAME"]." ".$arAdminUser["LAST_NAME"];
            $arResultAdminUser["EMAIL"] = $arAdminUser["EMAIL"];
            $arResultAdminUsers[] = $arResultAdminUser;
        }
        
        return $arResultAdminUsers;
    }
    
    private function GetLicenseInfo(){
        $result = false;
        
        $arUpdateList = CUpdateClient::GetUpdatesList( $errorMessage, LANGUAGE_ID, "N" );
        $bDateBitrixExpire = ( ( MakeTimeStamp( $arUpdateList["CLIENT"][0]["@"]["DATE_TO"] ) - time() ) < self::$timeExpire ) ? true : false;
        
        if( $bDateBitrixExpire ){
            $arProcessSite = self::GetSiteInfo();
            $arAdminList = self::GetAdminList();
            
            if( !empty( $arProcessSite ) ){
                $messageTitle = GetMessage( "ACRIT_INFORMER_LICENSE_EXPRIRE_TITLE" ).$arProcessSite["DOMAIN_NAME"];
                $messageBlock = GetMessage( "ACRIT_INFORMER_LICENSE_REGISTERED" ).htmlspecialcharsback( $arUpdateList["CLIENT"][0]["@"]["NAME"] )."\n".
                GetMessage( "ACRIT_INFORMER_LICENSE_TYPE" ).$arUpdateList["CLIENT"][0]["@"]["LICENSE"]."\n".
                GetMessage( "ACRIT_INFORMER_LICENSE_EXPIRE_DATE" ).$arUpdateList["CLIENT"][0]["@"]["DATE_TO"]."\n";
                
                if( !empty( $arAdminList ) ){
                    $messageBlock .= "\n".GetMessage( "ACRIT_INFORMER_ADMIN_LIST" )."\n\n";
                                
                    foreach( $arAdminList as $arAdminListItem ){
                        $messageBlock .= "ID: ".$arAdminListItem["ID"].": ".$arAdminListItem["FULL_NAME"].", email: ".$arAdminListItem["EMAIL"]."\n";
                    }
                }
                
                $messageBlock .= GetMessage( "ACRIT_INFORMER_LICENSE_SITE" ).$arProcessSite["SITE_PROTOCOL"]."://".$arProcessSite["DOMAIN_NAME"]."/bitrix/admin/update_system.php";            

                $headers = "Content-type: text/plain; charset=".LANG_CHARSET;
                if( bxmail( GetMessage( "ACRIT_INFORMER_LICENSE_ADMIN_EMAIL" ), $messageTitle, $messageBlock, $headers ) ){
                    $result = true;
                }
            }
        }
        
        return $result;
    }
    
    private function GetCRMIntergationInfo(){
        $result = false;
        
        $crmIntegrationData = COption::GetOptionString( "sale", "~crm_integration", "" );
        $arCrmIntegration = unserialize( $crmIntegrationData );
        if( !is_array( $arCrmIntegration ) || empty( $arCrmIntegration ) ){
            $arProcessSite = self::GetSiteInfo();
            $arAdminList = self::GetAdminList();
        
            if( !empty( $arProcessSite ) ){
                $messageTitle = GetMessage( "ACRIT_INFORMER_CRM_NOLICENSE_TITLE" ).$arProcessSite["DOMAIN_NAME"];
                $messageBlock = GetMessage( "ACRIT_INFORMER_CRM_NOLICENSE_INFO_PRE" ).$arProcessSite["SITE_PROTOCOL"]."://".$arProcessSite["DOMAIN_NAME"]."/ ".GetMessage( "ACRIT_INFORMER_CRM_NOLICENSE_INFO_POST" )."\n";

                if( !empty( $arAdminList ) ){
                    $messageBlock .= "\n".GetMessage( "ACRIT_INFORMER_ADMIN_LIST" )."\n\n";
                                
                    foreach( $arAdminList as $arAdminListItem ){
                        $messageBlock .= "ID: ".$arAdminListItem["ID"].": ".$arAdminListItem["FULL_NAME"].", email: ".$arAdminListItem["EMAIL"]."\n";
                    }
                }
                                
                $headers = "Content-type: text/plain; charset=".LANG_CHARSET;
                if( bxmail( GetMessage( "ACRIT_INFORMER_LICENSE_ADMIN_EMAIL" ), $messageTitle, $messageBlock, $headers ) ){
                    $result = true;
                }
            }    
        }
        
        return $result;
    }
    
    private function GetMarketModuleList(){
        $arModules = array();
        
        $arRequestedModules = CUpdateClientPartner::GetRequestedModules( "" );
        $arUpdateList = CUpdateClientPartner::GetUpdatesList(
            $errorMessage,
            LANGUAGE_ID,
            "N",
            $arRequestedModules,
            array(
                "fullmoduleinfo" => "Y"
            )
        );
        
        $arModules = $arUpdateList;
        
        return $arModules;
    }
    
    private function GetMarketModulesInfo(){
        $result = false;
        
        $arModuleList = self::GetMarketModuleList();
        
        foreach( $arModuleList["MODULE"] as $arModule ){
            if( stripos( $arModule["@"]["ID"], self::$modulePrefix ) !== false ){
                if( $arModule["@"]["ID"] != self::$moduleId ){
                    continue;
                }
            }
            
            $bDateModuleExpire = ( ( MakeTimeStamp( $arModule["@"]["DATE_TO"] ) - time() ) < self::$timeExpire ) ? true : false;
            if( !$bDateModuleExpire ){
                if( ( $arModule["@"]["UPDATE_END"] == "Y" ) && ( $arModule["@"]["FREE_MODULE"] == "D" ) ){
                    $bDateModuleExpire = true;
                }
            }
            
            if( $bDateBitrixExpire ){
                $arProcessSite = self::GetSiteInfo();
                $arAdminList = self::GetAdminList();
                
                if( !empty( $arProcessSite ) ){
                    $messageTitle = GetMessage( "ACRIT_INFORMER_MODULE_LICENSE_SITE_PRE" ).$arProcessSite["DOMAIN_NAME"].": (".htmlspecialcharsback( $arModule["@"]["ID"] ).")";
                    $messageBlock = GetMessage( "ACRIT_INFORMER_MODULE_LICENSE_PARTNER_MODULE" ).htmlspecialcharsback( $arModule["@"]["NAME"] ).": (".htmlspecialcharsback( $arModule["@"]["ID"] ).")"."\n".
                    GetMessage( "ACRIT_INFORMER_MODULE_LICENSE_PARTNER_NAME" ).htmlspecialcharsback( $arModule["@"]["PARTNER_NAME"] )."\n";
                    
                    if( strlen( $arModule["@"]["DATE_TO"] ) > 0 ){
                        $messageBlock .= GetMessage( "ACRIT_INFORMER_MODULE_LICENSE_EXPIRE_DATE" ).$arModule["@"]["DATE_TO"]."\n";
                    }
                    
                    if( !empty( $arAdminList ) ){
                        $messageBlock .= "\n".GetMessage( "ACRIT_INFORMER_ADMIN_LIST" )."\n\n";
                                    
                        foreach( $arAdminList as $arAdminListItem ){
                            $messageBlock .= "ID: ".$arAdminListItem["ID"].": ".$arAdminListItem["FULL_NAME"].", email: ".$arAdminListItem["EMAIL"]."\n";
                        }
                    }
                    
                    $messageBlock .= GetMessage( "ACRIT_INFORMER_MODULE_LICENSE_SITE" ).$arProcessSite["SITE_PROTOCOL"]."://".$arProcessSite["DOMAIN_NAME"]."/";

                    $headers = "Content-type: text/plain; charset=".LANG_CHARSET;
                    if( bxmail( GetMessage( "ACRIT_INFORMER_LICENSE_ADMIN_EMAIL" ), $messageTitle, $messageBlock, $headers ) ){
                        $result = true;
                    }
                }
            }
        }

        return $result;
    }
    
    public static function GetInformerData(){
        self::GetLicenseInfo();
        self::GetCRMIntergationInfo();
        self::GetMarketModulesInfo();
    }
}