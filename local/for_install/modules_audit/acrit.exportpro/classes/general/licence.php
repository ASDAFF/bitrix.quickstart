<?php

IncludeModuleLangFile(__FILE__);

class AcritLicence {
    public static function Show(){
        IncludeModuleLangFile( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/interface/prolog_main_admin.php" );
        $supportFinishDate = COption::GetOptionString( "main", "~support_finish_date", "" );
        if( ( $supportFinishDate <> "" ) && is_array( $aSupportFinishDate = ParseDate( $supportFinishDate, "ymd" ) ) ){
            $aGlobalOpt = CUserOptions::GetOption( "global", "settings", array() );
            
            if( $aGlobalOpt["messages"]["support"] <> "N" ){
                $supportFinishStamp = mktime( 0, 0, 0, $aSupportFinishDate[1], $aSupportFinishDate[0], $aSupportFinishDate[2] );
                $supportDateDiff = ceil( ( $supportFinishStamp - time() ) / 86400 );

                $sSupportMess = "";
                $sSupWIT = " (<span onclick=\"BX.toggle(BX('supdescr'))\" style='border-bottom: 1px dashed #1c91e7; color: #1c91e7; cursor: pointer;'>".GetMessage( "prolog_main_support_wit" )."</span>)";

                if( ( $supportDateDiff >= 0 ) && ( $supportDateDiff <= 30 ) ){
                    $sSupportMess = GetMessage( "prolog_main_support11",
                        array(
                            "#FINISH_DATE#" => GetTime( $supportFinishStamp ),
                            "#DAYS_AGO#" => ( $supportDateDiff == 0 ) ? GetMessage( "prolog_main_today" ) : GetMessage( "prolog_main_support_days", array( "#N_DAYS_AGO#" => $supportDateDiff ) ),
                            "#LICENSE_KEY#" => md5( LICENSE_KEY ),
                            "#WHAT_IS_IT#" => $sSupWIT,
                            "#SUP_FINISH_DATE#" => GetTime( mktime( 0, 0, 0, $aSupportFinishDate[1] + 1, $aSupportFinishDate[0], $aSupportFinishDate[2] ) ),
                        )
                    );
                }
                elseif( ( $supportDateDiff < 0 ) && ( $supportDateDiff >= -30 ) ){
                    $sSupportMess = GetMessage( "prolog_main_support21",
                        array(
                            "#FINISH_DATE#" => GetTime( $supportFinishStamp ),
                            "#DAYS_AGO#" => ( -$supportDateDiff ),
                            "#LICENSE_KEY#" => md5( LICENSE_KEY ),
                            "#WHAT_IS_IT#" => $sSupWIT,
                            "#SUP_FINISH_DATE#" => GetTime( mktime( 0, 0, 0, $aSupportFinishDate[1] + 1, $aSupportFinishDate[0], $aSupportFinishDate[2] ) ),
                        )
                    );
                }
                elseif( $supportDateDiff < -30 ){
                    $sSupportMess = GetMessage( "prolog_main_support31",
                        array(
                            "#FINISH_DATE#" => GetTime( $supportFinishStamp ),
                            "#LICENSE_KEY#" => md5( LICENSE_KEY ),
                            "#WHAT_IS_IT#" => $sSupWIT,
                        )
                    );
                }
                
                if( $sSupportMess <> "" ){
                    $sSupportMess .= GetMessage( "ACRIT_EXPORTPRO_BUY_LICENCE" );
                    $userOption = CUserOptions::GetOption( "main", "admSupInf" );
                    if( mktime() > $userOption["showInformerDate"] ){
                        $prolongUrl = "/bitrix/admin/buy_support.php?lang=".LANGUAGE_ID;
                        if( !in_array( LANGUAGE_ID, array( "ru", "ua" ) ) || intval( COption::GetOptionString( "main", "~PARAM_PARTNER_ID" ) ) <= 0 ){
                            require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client.php" );
                            $prolongUrl = "http://www.acrit-studio.ru/shop/list/lupd/";
                        }

                        echo BeginNote( 'style="position: relative; top: -15px;"' );
                        ?>
                        
                        <div style="float: right; padding-left: 50px; margin-top: -5px; text-align: center;">
                            <a href="<?=$prolongUrl?>" target="_blank" class="adm-btn adm-btn-save" style="margin-bottom: 4px;"><?=GetMessage( "prolog_main_support_button_prolong" )?></a><br />
                            <a href="http://www.acrit-studio.ru/market/" target="_blank"><?=GetMessage( "prolog_main_support_button_prolong_modules" )?></a>
                        </div>
                        <?=$sSupportMess;?>
                        <div id="supdescr" style="display: none;"><br /><br /><b><?=GetMessage( "prolog_main_support_wit_descr1" )?></b><hr><?=GetMessage( "prolog_main_support_wit_descr2" )?></div>
                        <?
                        echo EndNote();
                    }
                }
            }
        }
    }
}