<?php
IncludeModuleLangFile( __FILE__ );

CModule::IncludeModule( "catalog" );
CModule::IncludeModule( "acrit.exportpro" );

class CAcritExportproExport{
    private $profile;
    private $dbMan;
    static $fileExport;
    static $fileExportUrl;
    static $firstStepFilename;
    private $baseDir;
    private $lockDir;

    private $siteEncoding = array(
        "utf-8" => "utf8",
        "UTF-8" => "utf8",
        "WINDOWS-1251" => "cp1251",
        "windows-1251" => "cp1251"
    );

    private $profileEncoding = array(
        "utf8" => "utf-8",
        "cp1251" => "windows-1251",
    );

    const PREPEND = 1;
    const APPEND = 0;
    const REWRITE = 2;

    public function __construct( $profileID ){
        global $exportstep;
        $this->lockDir = $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/acrit.exportpro/";
        
        $sessionData = AcritExportproSession::GetSession( $profileID );
        
        $this->dbMan = new CExportproProfileDB();
        $this->baseDir = $_SERVER["DOCUMENT_ROOT"]."/acrit.exportpro/";
        $this->profile = $this->dbMan->GetByID( $profileID );
        
        //if( is_dir( $this->profile["SETUP"]["URL_DATA_FILE"] ) )
        //    self::$fileExport = $_SERVER["DOCUMENT_ROOT"].$this->profile["SETUP"]["URL_DATA_FILE"]."export_".date( "d_m_Y_H_i" ).".xml";
        //else
        //    self::$fileExport = $_SERVER["DOCUMENT_ROOT"].$this->profile["SETUP"]["URL_DATA_FILE"];
                                         
        self::$fileExport = $_SERVER["DOCUMENT_ROOT"].$this->profile["SETUP"]["URL_DATA_FILE"];
        self::$fileExportUrl = $this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"].$this->profile["SETUP"]["URL_DATA_FILE"];
        $this->originalName = self::$fileExport;
        $this->originalNamePath = $this->profile["SETUP"]["URL_DATA_FILE"];
        if( empty( self::$fileExport ) || self::$fileExport == $_SERVER["DOCUMENT_ROOT"] ){
            if( !$exportstep || ( $exportstep == 1 ) ){
                self::$fileExport = $this->baseDir."market_".date( "d_m_Y_H_i", time() ).".xml";
                $sessionData["EXPORTPRO"]["TMP_NAME"][$this->profile["ID"]] = self::$fileExport;
            }
            else{
                self::$fileExport = $sessionData["EXPORTPRO"]["TMP_NAME"][$this->profile["ID"]];
            }
        }
        $this->dynamicDownload = false;
        
        if( strlen( strstr( self::$fileExport, $this->baseDir ) ) > 0 ){
            self::$fileExport = str_replace( $this->baseDir, $_SERVER["DOCUMENT_ROOT"]."/upload/acrit.exportpro/", self::$fileExport );
            CheckDirPath( dirname( self::$fileExport )."/" );
            $this->dynamicDownload = true;
        }
        
        AcritExportproSession::SetSession( $profileID, $sessionData );
    }

    public function Export( $type = "", $cronpage = 0 ){        
        set_time_limit( 0 );                                 
        global $APPLICATION, $USER, $DB, $exportstep, $end;
                                  
        if( !$this->profile )
            return false;
            
        if( $this->profile["ACTIVE"] != "Y" && $type == "cron" )
            return false;
        
        $cronrun = ( $type == "cron" ) || ( $type == "agent" );
        if( !$cronpage )
            $cronpage = 0;
            
        if( $cronrun ){
            $exportstep = $cronpage;
            self::$firstStepFilename = self::$fileExport;
            self::$fileExport .= ".part".$cronpage;
            if( file_exists( self::$fileExport ) )
                unlink( self::$fileExport );
        }
        AcritExportproSession::Init( $exportstep );
		
		$log = new CAcritExportproLog( $this->profile["ID"] );
        
        /*Проверка запущен ли уже процесс экспорта*/
        if( $this->isLock() && ( !$exportstep || ( $exportstep == 1 ) ) ){
            if( $_REQUEST["unlock"] == "Y" ){
                unlink( $this->lockDir."export_{$this->profile["ID"]}_run.lock" );
            }
            else{
                require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php" );
                $APPLICATION->AddHeadScript( "/bitrix/panel/main/admin-public.css" );
                if( $type != "cron" ){
                    echo '<div id="bx-admin-prefix">';
                    CAdminMessage::ShowMessage(
                        array(
                            "MESSAGE" => GetMessage( "ACRIT_EXPORTPRO_PROCESS_RUN" ),
                            "TYPE" => "FAIL",
                            "HTML" => "TRUE"
                        )
                    );
                    echo '</div>';
                }
                else{
                    $adminEmail = COption::GetOptionString( "main", "email_from" );
                    $subject = GetMessage( "ACRIT_EXOPRTPRO_PROCESS_RUN_SUBJECT" );
                    $errorMessage = GetMessage( "ACRIT_EXOPRTPRO_PROCESS_RUN_ERROE_MESSAGE" );
                    $errorMessage = str_replace( array( "#PROFILE_ID#", "#PROFILE_NAME#" ), array( $this->profile["ID"], $this->profile["NAME"] ), $errorMessage );
                    //mail($adminEmail, $subject, $errorMessage);
                }
                return false;
            }
        }

        $profileUtils = new CExportproProfile();
        $profileUtils->GetProfileData();
        
        if( CModule::IncludeModule( "catalog" ) ){
            $obCond = new CAcritExportproCatalogCond();
            CAcritExportproProps::$arIBlockFilter = $profileUtils->PrepareIBlock( $this->profile["IBLOCK_ID"], $this->profile["USE_SKU"] );
		    $obCond->Init( BT_COND_MODE_GENERATE, 0, array() );                                                                      
            $this->profile["EVAL_FILTER"] = $obCond->Generate( $this->profile["CONDITION"], array( "FIELD" => '$GLOBALS["CHECK_COND"]' ) );
            $this->PrepareFieldFilter();
        }
                   
        //if( !$exportstep || $exportstep == 1 )
        //    if( !eval( GetMessage("ACRIT_EXPORTPRO_PROTECT" ) ) )
        //        return false;

        if( $this->profile["TYPE"] == "ozon" ){
            $ozonAppId = $this->profile["OZON_APPID"];
            $ozonAppKey = $this->profile["OZON_APPKEY"];

            $marketCategory = array();
            if( !empty( $ozonAppId ) && !empty( $ozonAppKey ) ){
                $ozon = new OZON( $ozonAppId, $ozonAppKey );
                $marketCategory = $ozon->GetAllTypes();
            }
        }        
        
        $elementsObj = new CAcritExportproElement( $this->profile );
        $this->Lock();
            
        if( !$end ){
            if( !$exportstep || $exportstep == 1 ){
                $exportstep = 1;
                if( file_exists( self::$fileExport ) )
                    unlink( self::$fileExport );
                
                AcritExportproSession::DeleteSession( $this->profile["ID"] );
            }
            
            if( $cronrun ){
                if( intval( $cronpage ) > 1 ){
                    $procResult = $elementsObj->Process( $exportstep, $cronrun, $this->profile["SETUP"]["FILE_TYPE"], self::$fileExport, $this->profile["SETUP"]["URL_DATA_FILE"], $marketCategory );
                    echo serialize( array( "procResult" => ( $procResult == true ) ) );
                    exit();
                }
                else{
                    $threads = new Threads();
                    
                    $tCnt = intval( $this->profile["SETUP"]["THREADS"] ) > 0 ? intval( $this->profile["SETUP"]["THREADS"] ) : 1;
                    $cronpage = 2;
                    
                    $allPages = $elementsObj->Process( 1, $cronrun, $this->profile["SETUP"]["FILE_TYPE"], self::$fileExport, $this->profile["SETUP"]["URL_DATA_FILE"], $marketCategory );
                    
                    $sessionData = AcritExportproSession::GetSession( $this->profile["ID"] );
                    
                    $steps = $sessionData["EXPORTPRO"]["LOG"][$this->profile["ID"]]["STEPS"];
                    $steps2 = $steps / $tCnt + ($steps % $tCnt == 0 ? 0 : 1);
                    
                    for( $i = 0; $i < $steps2; $i++ ){
                        for( $j = 0; $j < $tCnt; $j++ ){
                            if( $cronpage > $steps )
                                break;
                                
                            $threads->newThread( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/acrit.exportpro/tools/cronrun_proc.php",
                                array(
                                    "documentRoot" => $_SERVER["DOCUMENT_ROOT"],
                                    "profileId" => $this->profile["ID"],
                                    "cronPage" => $cronpage++,
                                )
                            );
                        }
                        
                        while( false !== ( $procResult = $threads->iteration() ) ){
                        }
                    }
                    $allPages = true;
                }
            }
            else{                              
                $allPages = $elementsObj->Process( $exportstep, $cronrun, $this->profile["SETUP"]["FILE_TYPE"], self::$fileExport, $this->profile["SETUP"]["URL_DATA_FILE"], $marketCategory );
            }
            if( !$allPages && $allPages != -1 && !$cronrun ){
                $exportstep++;
                $this->profile["LOG"] = $log->GetLog( $this->profile["ID"], false );
                
                $arLogArray = $log->GetLogArray( $this->profile["ID"] );
                if( !empty( $arLogArray ) ){
                    $this->profile["UNLOADED_OFFERS"] = $arLogArray["PRODUCTS"];
                    $this->profile["UNLOADED_OFFERS_CORRECT"] = $arLogArray["PRODUCTS_EXPORT"];
                    $this->profile["UNLOADED_OFFERS_ERROR"] = $arLogArray["PRODUCTS_ERROR"];    
                }
                
                $this->dbMan->Update( $this->profile["ID"], $this->profile );
                echo "<script>window.location=\"", $APPLICATION->GetCurPageParam( "exportstep=$exportstep", array( "exportstep", "unlock" ) ), "\"</script>";
                die();
            }
            else{
                if( $cronrun ){
                    if( file_exists( self::$firstStepFilename ) )
                        unlink( self::$firstStepFilename );
                        
                    $dirName = dirname( self::$firstStepFilename );
                    $files = scandir( $dirName );
                    foreach( $files as $file ){
                        if( ( $file == "." ) || ( $file == ".." ) )
                            continue;
                        
                        if( ( false !== strpos( $file, basename( self::$firstStepFilename ) ) )
                            && ( $file != basename( self::$firstStepFilename ) ) ){
                                file_put_contents( self::$firstStepFilename, file_get_contents( $dirName."/".$file ), FILE_APPEND );
                                unlink( $dirName."/".$file );
                        }
                    }
                    self::$fileExport = self::$firstStepFilename;
                }
				
				$basePatern =  "Y-m-dTh:i:s±h:i";
				$paternCharset = $this->GetStringCharset( $basePatern );
				
				if( $paternCharset == "cp1251" ){
					$basePatern = $APPLICATION->ConvertCharset( $basePatern, "cp1251", "utf8" );
				}
								
				$dateGenerate = ( $this->profile["DATEFORMAT"] == $basePatern ) ? $elementsObj->GetYandexDateTime( date( "d.m.Y H:i:s" ) ) : date( str_replace( "_", " ", $this->profile["DATEFORMAT"] ), time() );                
				                                         
                //$currencyXML = ( CModule::IncludeModule( "catalog" ) ) ? $this->GetCurrencyXML( $elementsObj->GetCurrencies() ) : "RUR";
                
                /*if( $this->profile["TYPE"] == "ua_hotline_ua" ){
                    $currencyXML = $this->GetUAHRate( $elementsObj->GetCurrencies() );
                }*/ //!!fix
                    
                $baseDeliveryCost = $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["VALUE"];
                if( $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["TYPE"] == "const" ){
                    $baseDeliveryCost = $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["CONTVALUE_TRUE"];
                }
                elseif( $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["TYPE"] == "complex" ){
                    if( $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["COMPLEX_TRUE_TYPE"] == "const" ){
                        $baseDeliveryCost = $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["COMPLEX_TRUE_CONTVALUE"];
                    }
                    else{
                        $baseDeliveryCost = $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["COMPLEX_TRUE_VALUE"];
                    }
                }
                
                $baseDeliveryDays = $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["VALUE"];
                if( $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["TYPE"] == "const" ){
                    $baseDeliveryDays = $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["CONTVALUE_TRUE"];
                }
                elseif( $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["TYPE"] == "complex" ){
                    if( $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["COMPLEX_TRUE_TYPE"] == "const" ){
                        $baseDeliveryDays = $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["COMPLEX_TRUE_CONTVALUE"];
                    }
                    else{
                        $baseDeliveryDays = $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["COMPLEX_TRUE_VALUE"];
                    }
                }
                
                $defaultFields = array(
                    "#ENCODING#" => $this->profileEncoding[$this->profile["ENCODING"]],
                    "#DATE#" => $this->profile["DATEFORMAT"],
                    "#SHOP_NAME#" => $this->profile["SHOPNAME"],
                    "#COMPANY_NAME#" => $this->profile["COMPANY"],
                    "#SITE_URL#" => $this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"],
                    "#DESCRIPTION#" => $this->profile["DESCRIPTION"],
                    "#CATEGORY#" => $this->GetCategoryXML( $elementsObj->GetSections() ),
                    //"#CURRENCY#" => $currencyXML,
                    "#CURRENCY#" => ( CModule::IncludeModule( "catalog" ) ) ? $this->GetCurrencyXML( $elementsObj->GetCurrencies() ) : "RUB",
                    "#DATE#" => $dateGenerate,
                    "#BASE_DELIVERY_COST#" => $baseDeliveryCost,
                    "#BASE_DELIVERY_DAYS#" => $baseDeliveryDays,
                    
                );      
                               
                $xmlHeader = explode( "#ITEMS#", $this->profile["FORMAT"] );
                $xmlHeader[0] = str_replace( array_keys( $defaultFields ), array_values( $defaultFields ), $xmlHeader[0] );
                $xmlHeader[1] = str_replace( array_keys( $defaultFields ), array_values( $defaultFields ), $xmlHeader[1] );
                
                
                if( 
                    ( ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["VALUE"] ) ) <= 0 ) 
                        && ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["COMPLEX_TRUE_VALUE"] ) ) <= 0 ) 
                        && ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["CONTVALUE_TRUE"] ) ) <= 0 )
                        && ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_COST"]["COMPLEX_TRUE_CONTVALUE"] ) ) <= 0 ) )
                    || ( ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["VALUE"] ) ) <= 0 ) 
                        && ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["COMPLEX_TRUE_VALUE"] ) ) <= 0 )
                        && ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["CONTVALUE_TRUE"] ) ) <= 0 )
                        && ( strlen( trim( $this->profile["XMLDATA"]["BASE_DELIVERY_DAYS"]["COMPLEX_TRUE_CONTVALUE"] ) ) <= 0 ) )
                ){
                    $xmlHeader[0] = preg_replace( "#<delivery-options>.*</delivery-options>#is", "", $xmlHeader[0] ); 
                    $xmlHeader[1] = preg_replace( "#<delivery-options>.*</delivery-options>#is", "", $xmlHeader[1] ); 
                }
                                            
                self::Save( $xmlHeader[0], self::PREPEND );
                self::Save( $xmlHeader[1] );
                
                CExportproInformer::GetInformerData();
                
                $this->ConvertEncoding();

                $this->profile["LOG"] = $log->GetLog( $this->profile["ID"] );
                
                $arLogArray = $log->GetLogArray( $this->profile["ID"] );
                if( !empty( $arLogArray ) ){
                    $this->profile["UNLOADED_OFFERS"] = $arLogArray["PRODUCTS"];
                    $this->profile["UNLOADED_OFFERS_CORRECT"] = $arLogArray["PRODUCTS_EXPORT"];
                    $this->profile["UNLOADED_OFFERS_ERROR"] = $arLogArray["PRODUCTS_ERROR"];    
                }
                
                $this->profile["SETUP"]["LAST_START_EXPORT"] = $this->profile["LOG"]["LAST_START_EXPORT"];
                $this->profile["TIMESTAMP_X"] = CDatabase::FormatDate( $this->profile["TIMESTAMP_X"], "YYYY-MM-DD HH:MI:SS", "DD.MM.YYYY HH:MI:SS" );
                
                $this->dbMan->Update( $this->profile["ID"], $this->profile );
                $this->Unlock();
                AcritExportproSession::DeleteSession( $this->profile["ID"] );

                if( $this->profile["USE_COMPRESS"] == "Y" ){
                    if( file_exists( $this->originalName ) ){
                        $zipSavePath = $this->originalName;
                    }
                    elseif( file_exists( $_SERVER["DOCUMENT_ROOT"]."/upload/acrit.exportpro/".$this->originalNamePath ) ){
                        $zipSavePath = $_SERVER["DOCUMENT_ROOT"]."/upload/acrit.exportpro/".$this->originalNamePath;
                    }
                    elseif( file_exists( $_SERVER["DOCUMENT_ROOT"]."/upload".$this->originalNamePath ) ){
                        $zipSavePath = $_SERVER["DOCUMENT_ROOT"]."/upload".$this->originalNamePath;
                    }
                    
                    $zipPath = ( stripos( $zipSavePath, "xml" ) !== false ) ? str_replace( "xml", "zip", $zipSavePath ) : str_replace( "csv", "zip", $zipSavePath );
                    $packarc = CBXArchive::GetArchive( $zipPath );
                    
                    $fileQuickPath = str_replace( $_SERVER["DOCUMENT_ROOT"], "", $zipSavePath );
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
                    
                    $pArcResult = $packarc->Pack( $zipSavePath );
                    LocalRedirect( str_replace( $_SERVER["DOCUMENT_ROOT"], "", $zipPath ) );
                }
                                   
                if( !$cronrun )
                    LocalRedirect( str_replace( $_SERVER["DOCUMENT_ROOT"], "", $this->originalName )."?encoding=".$this->profileEncoding[$this->profile["ENCODING"]] );
                
                //LocalRedirect($APPLICATION->GetCurPageParam("end=Y", array("page")));
                //die();
            }
        }

        /* Вывод xml файла*/
        //$APPLICATION->RestartBuffer();
        //header("Content-Type: text/xml");
        //echo $this->Get();
        //die();
        /* -- */
        //echo "<pre>", print_r($elementsObj->GetElementCount(), true), "</pre>";
    }
    
    private function Unlock(){                                            
        if( file_exists( $this->lockDir."export_{$this->profile["ID"]}_run.lock" ) )
            unlink( $this->lockDir."export_{$this->profile["ID"]}_run.lock" );
    }
    
    private function Lock(){
        file_put_contents( $this->lockDir."export_{$this->profile["ID"]}_run.lock", "" );
    }
    
    public function isLock(){
        return file_exists( $this->lockDir."export_{$this->profile["ID"]}_run.lock" );
    }                         
    
    private function GetUAHRate(){
        $baseCurrency = CCurrency::GetBaseCurrency();
        
        $currencyRates = CExportproProfile::LoadCurrencyRates();
        $CURRENCIES = array();
        if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
            $currencyTo = array();
            foreach( $currencies as $curr ){
                $curr2 = $this->profile["CURRENCY"][$curr]["CHECK"] == "Y" ? $this->profile["CURRENCY"][$curr]["CONVERT_TO"] : $curr;
                $rate = $baseCurrency == $curr2;
                
                if( $rate ){
                    $rate = 1;
                }
                else{
                    if( !key_exists( $this->profile["CURRENCY"][$curr]["RATE"], $currencyRates ) ){
                        $rate = CCurrencyRates::ConvertCurrency( 1, $this->profile["CURRENCY"][$curr]["CONVERT_TO"], $baseCurrency );
                    }
                    else{
                        $rate = $this->profile["CURRENCY"][$curr]["RATE"];
                    }
                }
                
                foreach( $currencyTo as $acur ){
                    if( $acur["CURRENCY"] == $curr2 )
                        continue 2;
                }
                
                $currencyTo[] = array(
                    "CURRENCY" => $curr2,
                    "RATE" => $rate,
                    "PLUS" => $this->profile["CURRENCY"][$curr]["PLUS"],
                );
            }
            $currencies = $currencyTo;
            unset( $currencyTo );
        }
        else{
            $currencyTo = array();
            foreach( $currencies as $curr ){
                $rate = $baseCurrency == $curr;
                
                if( $rate ){
                    $rate = 1;
                }
                else{
                    if( !key_exists( $this->profile["CURRENCY"][$curr]["RATE"], $currencyRates ) ){
                        $rate = CCurrencyRates::ConvertCurrency( 1, $curr, $baseCurrency );
                    }
                    else{
                        $rate = $this->profile["CURRENCY"][$curr]["RATE"];
                    }
                }
                
                foreach( $currencyTo as $acur ){
                    if( $acur["CURRENCY"] == $curr2 )
                        continue 2;
                }
                
                $currencyTo[] = array(
                    "CURRENCY" => $curr,
                    "RATE" => $rate,
                    "PLUS" => $this->profile["CURRENCY"][$curr]["PLUS"],
                );
            }
            $currencies = $currencyTo;
            unset( $currencyTo );
        }
        
        return $rate;
    }
     
    private function GetCurrencyXML( $currencies ){
        $baseCurrency = CCurrency::GetBaseCurrency();
        $currencyRates = CExportproProfile::LoadCurrencyRates();
        $CURRENCIES = array();
        
        if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
            $currencyTo = array();
            foreach( $currencies as $curr ){
                $curr2 = ( $this->profile["CURRENCY"][$curr]["CHECK"] == "Y" ) ? $this->profile["CURRENCY"][$curr]["CONVERT_TO"] : $curr;
                $rate = $baseCurrency == $curr2;
                if( $rate ){
                    $rate = 1;
                }
                else{
                    if( !key_exists( $this->profile["CURRENCY"][$curr]["RATE"], $currencyRates ) ){
                        $rate = CCurrencyRates::ConvertCurrency( 1, $this->profile["CURRENCY"][$curr]["CONVERT_TO"], $baseCurrency );
                    }
                    else{
                        $rate = $this->profile["CURRENCY"][$curr]["RATE"];
                    }
                }
                
                foreach( $currencyTo as $acur ){
                    if( $acur["CURRENCY"] == $curr2 )
                        continue 2;
                }
                
                $currencyTo[] = array(
                    "CURRENCY" => $curr2,
                    "RATE" => $rate,
                    "PLUS" => $this->profile["CURRENCY"][$curr]["PLUS"],
                );
            }
            $currencies = $currencyTo;
            unset( $currencyTo );
        }
        else{
            $currencyTo = array();
            foreach( $currencies as $curr ){
                $rate = $baseCurrency == $curr;
                if( $rate ){
                    $rate = 1;
                }
                else{
                    if( !key_exists( $this->profile["CURRENCY"][$curr]["RATE"], $currencyRates ) ){
                        $rate = CCurrencyRates::ConvertCurrency( 1, $curr, $baseCurrency );
                    }
                    else{
                        $rate = $this->profile["CURRENCY"][$curr]["RATE"];
                    }
                }
                
                foreach( $currencyTo as $acur ){
                    if( $acur["CURRENCY"] == $curr2 )
                        continue 2;
                }
                
                $currencyTo[] = array(
                    "CURRENCY" => $curr,
                    "RATE" => $rate,
                    "PLUS" => $this->profile["CURRENCY"][$curr]["PLUS"],
                );
            }
            $currencies = $currencyTo;
            unset( $currencyTo );
        }
        
        foreach( $currencies as $curr ){
            $currencyTempalte = $this->profile["CURRENCY_TEMPLATE"];
            foreach($curr as $id => $value)
            {
                $currencyFields["#$id#"] = htmlspecialcharsbx(html_entity_decode($value));
            }
            $CURRENCIES[] = str_replace(array_keys($currencyFields), array_values($currencyFields), $currencyTempalte);
        }  
        return implode( "", $CURRENCIES );
    }
    
    private function GetCategoryXML( $sections ){
        $arTerminalPathSections = array();
        foreach( $sections as $sectionId ){
            $dbSectionList = CIBlockSection::GetNavChain(
                false,
                $sectionId
            );
            
            while( $arSectionPath = $dbSectionList->GetNext() ){
                $arTerminalPathSections[] = $arSectionPath["ID"];
            }    
        }
        
        sort( $arTerminalPathSections );
        $arTerminalPathSections = array_unique( $arTerminalPathSections );
        
        $sections = array_filter( $sections );
        if( empty( $sections ) )
            return "";
            
        $CATEGORIES = array();
        
        $fields = array(
            "ID" => "ID",
            "NAME" => "NAME",
            "IBLOCK_SECTION_ID" => "PARENT_ID",
        );
        
        $arSectionFilter = array();
        if( $this->profile["EXPORT_PARENT_CATEGORIES"] != "Y" ){
            $arSectionFilter["ID"] = $sections;
        }
        
        $dbSection = CIBlockSection::GetList(
            array(
                "ID" => "ASC"
            ),
            $arSectionFilter,
            false,
            array(
                "ID",
                "NAME",
                "IBLOCK_SECTION_ID"
            )
        );
        while( $arSection = $dbSection->GetNext() ){
            if( !in_array( $arSection["ID"], $arTerminalPathSections ) ){
                continue;
            }
            
            $sectionTempalte = $this->profile["CATEGORY_TEMPLATE"];
            
            if( $this->profile["EXPORT_PARENT_CATEGORIES"] == "Y" ){
                $innerXmlCategory = simplexml_load_string( $sectionTempalte );
                                 
                if( $innerXmlCategory ){
                    $innerXmlCategory->addAttribute( "parentId", "#PARENT_ID#" );
                    $sectionInnerTempalte = str_replace( '<?xml version="1.0"?>', "", $innerXmlCategory->asXML() );
                }    
            }
            
            foreach( $arSection as $id => $value ){
                $sectionFields["#$fields[$id]#"] = htmlspecialcharsbx( html_entity_decode( $value ) );
            }
            
            unset( $arSection );
            
            if( ( strlen( $sectionInnerTempalte ) > 0 ) && intval( $sectionFields["#PARENT_ID#"] ) > 0 ){
                $CATEGORIES[] = str_replace( array_keys( $sectionFields ), array_values( $sectionFields ), $sectionInnerTempalte );
            }
            else{
                $CATEGORIES[] = str_replace( array_keys( $sectionFields ), array_values( $sectionFields ), $sectionTempalte );
            }
        } 
        
        return implode( "", $CATEGORIES );
    }
    
    private function PrepareFieldFilter(){
        $obCond = new CAcritExportproCatalogCond();
        $obCond->Init( BT_COND_MODE_GENERATE, 0, array() );

        foreach( $this->profile["XMLDATA"] as $id => $field ){
            if( empty( $field["CONDITION"]["CHILDREN"] ) ) continue;
            $this->profile["XMLDATA"][$id]["EVAL_FILTER"] = $obCond->Generate( 
                $field["CONDITION"],
                array(
                    "FIELD" => '$GLOBALS["CHECK_COND"]'
                )
            );
        }
    }

    public function Get(){
        return file_get_contents( self::$fileExport );
    }

    public static function Save( $data, $mode = self::APPEND ){
        if( !isset( self::$fileExport ) )
            return false;
                                                                
        if( $mode == self::APPEND ){
            file_put_contents( self::$fileExport, $data, FILE_APPEND );
        }
        elseif( $mode == self::PREPEND ){
            //$preData = file_get_contents( self::$fileExportUrl );
            //if( file_exists( self::$fileExport ) ){
                $preData = file_get_contents( self::$fileExport );
                file_put_contents( self::$fileExport, $data );
                file_put_contents( self::$fileExport, $preData, FILE_APPEND );
                unset( $preData );    
            //}
        }
        else{
            file_put_contents( self::$fileExport, $data );
        }
        return true;
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
    
    public function printProfile(){
        echo "<pre>", print_r( $this->profile, true ), "</pre>";
    }
    
    private function ConvertEncoding(){
        if( $this->profile["ENCODING"] != $this->siteEncoding[ToLower( SITE_CHARSET )] ){
            $data = $this->Get();
            $encodingData = mb_convert_encoding( $data, $this->profile["ENCODING"], $this->siteEncoding[ToLower( SITE_CHARSET )] );
            if( !$encodingData ){
                return false;
            }
            unset( $data );
            self::Save( $encodingData, self::REWRITE );
        }
        return true;
    }
    
    private function GetStringCharset( $str ){ 
        $resEncoding = "cp1251";
        
        if( preg_match( "#.#u", $str ) ){
            $resEncoding = "utf8";
        }
        
        return $resEncoding;
    }
}