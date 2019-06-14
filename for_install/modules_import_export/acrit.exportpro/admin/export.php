<?php
$moduleID = 'acrit.exportpro';
require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php" );
require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/$moduleID/include.php" );
//require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/dull/prolog.php" );

$POST_RIGHT = $APPLICATION->GetGroupRight( $moduleID );
if( $POST_RIGHT == "D" )
    $APPLICATION->AuthForm( GetMessage( "ACCESS_DENIED" ) );

if( !CModule::IncludeModule( 'iblock' ) ){
    return false;
}

IncludeModuleLangFile( __FILE__ );

if( !$_REQUEST['export_import'] ){
    require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );
    
    AcritLicence::Show();?>
    <div class="adm-detail-content-wrap">
        <div class="adm-detail-content">
            <div class="adm-detail-content-item-block" style="height: auto; overflow-y: visible;">
                <table class="adm-detail-content-table edit-table">
                    <tr class="heading"><td><?=GetMessage( 'ACRIT_EXPORTPRO_PROFILE_TABLE_EXPORT_IMPORT_TITLE' )?></td></tr>
                    <tr align="center">
                        <td>
                            <form method="post">
                                <div style="margin: 20px">
                                    <input type="radio" name="export_import" value="export">
                                    <label for="action"><?=GetMessage( 'ACRIT_EXPORTPRO_PROFILE_LIST_EXPORT_TITLE' )?></label>
                                    
                                    <input type="radio" name="export_import" value="import">
                                    <label for="action"><?=GetMessage( 'ACRIT_EXPORTPRO_PROFILE_LIST_IMPORT_TITLE' )?></label>
                                </div>
                                <input type="submit" class="adm-btn-save" value="<?=GetMessage( 'ACRIT_EXPORTPRO_PROFILE_LIST_PROCESS' )?>">
                            </form>
                            <br><br>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?}

/*  Экспорт профилей    */
if( $_REQUEST['export_import'] == 'export' ){
    /*  Выбор файла для  */
    if( $_REQUEST['step'] != 2 ){
        require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );
        AcritLicence::Show();
        ?>
        <div class="adm-detail-content-wrap">
            <div class="adm-detail-content">
                <div class="adm-detail-content-item-block" style="height: auto; overflow-y: visible;">
                    <table class="adm-detail-content-table edit-table">
                        <tr class="heading"><td><?=GetMessage( 'ACRIT_EXPORTPRO_PROFILE_TABLE_EXPORT_TITLE' )?></td></tr>
                        <tr align="center">
                            <td>
                                <form name="exportprofile_form">
                                    <br>
                                    <label style="font-size: 14px"><b><?=GetMessage( 'ACRIT_EXPORTPRO_PROFILE_LIST_EXPORT_FILE_TITLE' )?></b></label>
                                    &nbsp;&nbsp;&nbsp;
                                    <input type="text" name="URL_DATA_FILE_EXPORT">
                                    <input type="button" value="..." onclick="BtnClick()">
                                    <input type="hidden" name="export_import" value="export">
                                    <input type="hidden" name="step" value="2">
                                    <br><br>
                                    <a href="/bitrix/admin/acrit_exportpro_export.php" class="adm-btn"><?=GetMessage( 'ACRIT_EXPORTPRO_PROFILE_LIST_BACK' )?></a>&nbsp;&nbsp;&nbsp;
                                    <input type="submit" class="adm-btn-save" value="<?=GetMessage( 'ACRIT_EXPORTPRO_PROFILE_LIST_PROCESS' )?>">
                                </form>
                                <br><br>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <?
        CAdminFileDialog::ShowScript(
            array(
                "event" => "BtnClick",
                "arResultDest" => array(
                    "FORM_NAME" => 'exportprofile_form',
                    "FORM_ELEMENT_NAME" => "URL_DATA_FILE_EXPORT"
                ),
                "arPath" => array( "SITE" => SITE_ID, "PATH" => "/upload" ),
                "select" => 'F', // F - file only, D - folder only
                "operation" => 'S', // O - open, S - save
                "showUploadTab" => true,
                "showAddToMenuTab" => false,
                "fileFilter" => 'txt',
                "allowAllFiles" => true,
                "SaveConfig" => true,
            )
        );
    }
    /*  Отрисовка таблицы и сам экспорт  */
    else{
        $sTableID = "tbl_acritprofile";
        
        function CheckFilter(){
            global $FilterArr, $lAdmin;
            foreach( $FilterArr as $f ){
                global $$f;
            }
            return true;
        }
        
        $oSort = new CAdminSorting( $sTableID, "ID", "desc" );
        $lAdmin = new CAdminList( $sTableID, $oSort );
        $cData = new CExportproProfileDB();
        
        $FilterArr = Array(
            "find",
            "find_id",
            "find_name",
            "find_active",
            "find_type",
            "find_type_run",
            "find_timestamp",
            "find_start_last_time",
        );
        
        $lAdmin->InitFilter( $FilterArr );
        if( CheckFilter() ){
            $arFilter = array(
                "ID" => ( $find != "" && $find_type == "id" ? $find : $find_id ),
                "NAME" => $find_name,
                "ACTIVE" => $find_active,
                "TYPE" => $find_type,
                "TYPE_RUN" => $find_type_run,
                "TIMESTAMP" => $find_timestamp_1,
                "START_LAST_TIME" => $find_start_last_time_1
            );
        }
        
        if( ( $arID = $lAdmin->GroupAction() ) && $POST_RIGHT == "W" ){
            // если выбрано "Для всех элементов"
            if( $_REQUEST['action_target'] == 'selected' ){
                $rsData = $cData->GetList(
                    array( $by => $order ),
                    $arFilter
                );
                
                while( $arRes = $rsData->Fetch() )
                    $arID[] = $arRes['ID'];
            }
            
            // пройдем по списку элементов
            if( $_REQUEST['URL_DATA_FILE_EXPORT'] ){
                file_put_contents( $_SERVER["DOCUMENT_ROOT"].$_REQUEST['URL_DATA_FILE_EXPORT'], "" );
                $arProfiles = array();
                foreach( $arID as $ID ){
                    if( strlen( $ID ) <= 0 )
                        continue;
                    
                    $ID = IntVal( $ID );
                    
                    // для каждого элемента совершим требуемое действие
                    switch( $_REQUEST['action'] ){
                        case "export":
                            if( ( $rsData = $cData->GetByID( $ID ) ) ){
                                $profId = $rsData['ID'];
                                unset( $rsData['ID'] );
                                unset( $rsData["START_LAST_TIME_X"] );
                                unset( $rsData["TIMESTAMP_X"] );
                                $arProfiles[] = $rsData;
                                $message[] = "<li>[$profId] {$rsData['NAME']}</li>";
                            }
                            else
                                $lAdmin->AddGroupError( GetMessage( "rub_save_error" )." ".GetMessage( "rub_no_rubric" ), $ID );
                            break;
                    }
                }
                $message = GetMessage( 'ACRIT_EXPORTPRO_PROFILE_LIST_EXPORTED1' ).'<ul>'.implode( "\r\n", $message ).'</ul>'
                    .str_replace("#FILE#", "http://".$_SERVER['HTTP_HOST'].$_REQUEST['URL_DATA_FILE_EXPORT'], GetMessage( 'ACRIT_EXPORTPRO_PROFILE_LIST_EXPORTED2' ) );
                CAdminMessage::ShowMessage( array( "MESSAGE" => $message, "TYPE" => 'OK', 'HTML' => true ) );
                file_put_contents( $_SERVER["DOCUMENT_ROOT"].$_REQUEST['URL_DATA_FILE_EXPORT'], Bitrix\Main\Web\Json::encode( $arProfiles ) );
            }
        }
        
        $lAdmin->AddHeaders(
            array(
                array(
                    "id" => "ID",
                    "content" => "ID",
                    "sort" => "id",
                    "align" => "right",
                    "default" => true,
                ),
                array(
                    "id" => "ACTIVE",
                    "content" => GetMessage( "parser_active" ),
                    "sort" => "active",
                    "align" => "left",
                    "default" => true,
                ),
                array(
                    "id" => "NAME",
                    "content" => GetMessage( "parser_name" ),
                    "sort" => "name",
                    "default" => true,
                ),
                array(
                    "id" => "TYPE",
                    "content" => GetMessage( "parser_type" ),
                    "sort" => "type",
                    "default" => true,
                ),
                array(
                    "id" => "TYPE_RUN",
                    "content" => GetMessage( "parser_type_run" ),
                    "sort" => "type_run",
                    "default" => true,
                ),
                array(
                    "id" => "TIMESTAMP_X",
                    "content" => GetMessage( "parser_updated" ),
                    "sort" => "timestamp_x",
                    "default" => true,
                ),
                array(
                    "id" => "START_LAST_TIME_X",
                    "content" => GetMessage( "parser_start_last_time" ),
                    "sort" => "start_last_time_x",
                    "default" => true,
                ),
            )
        );
        
        $rsData = $cData->GetList(
            array( $by => $order ),
            $arFilter
        );
        
        $rsData = new CAdminResult( $rsData, $sTableID );
        $rsData->NavStart();
        $lAdmin->NavText( $rsData->GetNavPrint( GetMessage( "parser_nav" ) ) );
        
        $rsIBlock = CIBlock::GetList(
            array( "name" => "asc" ),
            array( "ACTIVE" => "Y" )
        );
        
        while( $arr = $rsIBlock->Fetch() ){
            $arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
            $arIBlockFilter['REFERENCE'][] = "[".$arr["ID"]."] ".$arr["NAME"];
            $arIBlockFilter['REFERENCE_ID'][] = $arr["ID"];
        }
        
        while( $arRes = $rsData->NavNext( true, "f_" ) ){
            $row = & $lAdmin->AddRow( $f_ID, $arRes );
        }
        
        $lAdmin->AddFooter(
            array(
                array(
                    "title" => GetMessage( "MAIN_ADMIN_LIST_SELECTED" ),
                    "value" => $rsData->SelectedRowsCount()
                ),
                array(
                    "counter" => true,
                    "title" => GetMessage( "MAIN_ADMIN_LIST_CHECKED" ),
                    "value" => "0"
                ),
            )
        );
        
        $lAdmin->AddGroupActionTable(
            array(
                "export" => GetMessage( "ACRIT_EXPORTPRO_PROFILE_EXPORT_SHORT" ), // активировать выбранные элементы
            )
        );
        
        $aContext = array();
        
        // и прикрепим его к списку
        
        $lAdmin->AddAdminContextMenu( $aContext );
        $lAdmin->CheckListMode();
        $APPLICATION->SetTitle( GetMessage( "post_title" ) );
        
        require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );
        //******************************
        // Send message and show progress
        //******************************
        
        if( isset( $_REQUEST['parser_end'] ) && $_REQUEST['parser_end'] == 1 && isset( $_REQUEST['parser_id'] ) && $_REQUEST['parser_id'] > 0 ){
            if( isset( $_GET['SUCCESS'][0] ) ){
                foreach( $_GET['SUCCESS'] as $success ){
                    CAdminMessage::ShowMessage( array( "MESSAGE" => $success, "TYPE" => "OK" ) );
                }
            }
            if( isset( $_GET['ERROR'][0] ) ){
                foreach( $_GET['ERROR'] as $error ){
                    CAdminMessage::ShowMessage( $error );
                }
            }
        }
        
        AcritLicence::Show();
        $lAdmin->DisplayList();
        ?>
        <br>
        <a href="/bitrix/admin/acrit_exportpro_list.php" class="adm-btn"><?=GetMessage( 'ACRIT_EXPORTPRO_PROFILE_LIST' )?></a>&nbsp;&nbsp;&nbsp;
        
        <?
        /* ------------------ */
    }
}

if( $_REQUEST['export_import'] == 'import' ){
    require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );
    AcritLicence::Show();
        
    if( $_REQUEST['step'] != 2 ){?>
        <div class="adm-detail-content-wrap">
            <div class="adm-detail-content">
                <div class="adm-detail-content-item-block" style="height: auto; overflow-y: visible;">
                    <table class="adm-detail-content-table edit-table">
                        <tr class="heading"><td><?=GetMessage( 'ACRIT_EXPORTPRO_PROFILE_TABLE_IMPORT_TITLE' )?></td></tr>
                        <tr align="center">
                            <td>
                                <form  name="exportprofile_form" method="post">
                                    <br>
                                    <label style="font-size: 14px"><b><?=GetMessage( 'ACRIT_EXPORTPRO_PROFILE_LIST_IMPORT_FILE_TITLE' )?></b></label>
                                    &nbsp;&nbsp;&nbsp;
                                    <input type="text" name="URL_DATA_FILE_IMPORT">
                                    <input type="button" value="..." onclick="BtnClick()">
                                    <input type="hidden" name="export_import" value="import">
                                    <input type="hidden" name="step" value="2">
                                    <br><br>
                                    <a href="/bitrix/admin/acrit_exportpro_export.php" class="adm-btn"><?=GetMessage( "ACRIT_EXPORTPRO_PROFILE_LIST_BACK" )?></a>&nbsp;&nbsp;&nbsp;
                                    <input type="submit" class="adm-btn-save" value="<?=GetMessage( "ACRIT_EXPORTPRO_PROFILE_LIST_PROCESS" )?>">
                                </form>
                                <br><br>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <?
        CAdminFileDialog::ShowScript(
            array(
                "event" => "BtnClick",
                "arResultDest" => array(
                    "FORM_NAME" => 'exportprofile_form',
                    "FORM_ELEMENT_NAME" => "URL_DATA_FILE_IMPORT"
                ),
                "arPath" => array( "SITE" => SITE_ID, "PATH" => "/upload" ),
                "select" => 'F', // F - file only, D - folder only
                "operation" => 'O', // O - open, S - save
                "showUploadTab" => true,
                "showAddToMenuTab" => false,
                "fileFilter" => 'txt',
                "allowAllFiles" => true,
                "SaveConfig" => true,
            )
        );
    }
    else{
        $profiles = file_get_contents( $_SERVER["DOCUMENT_ROOT"].$_REQUEST['URL_DATA_FILE_IMPORT'] );
        $arProfile = Bitrix\Main\Web\Json::decode( $profiles );
        $cData = new CExportproProfileDB();
        foreach( $arProfile as $prof ){
            $id = $cData->Add( $prof );
            switch( $prof['SETUP']['TYPE_RUN'] ){
                case 'cron':
                    CExportproCron::CronRun( $id, $prof['SETUP'] );
                    break;
                case 'comp':
                    CExportproCron::CronRun( $id, $prof['SETUP'], true );
                    break;
            }
            $message[] = "<li>[$id] {$prof['NAME']}</li>";
        }
        
        if( count( $message ) > 0 ){
            $message = GetMessage( 'ACRIT_EXPORTPRO_PROFILE_LIST_EXPORTED3' ).'<ul>'.implode( "\r\n", $message ).'</ul>';
            CAdminMessage::ShowMessage(
                array(
                    "MESSAGE" => $message,
                    "TYPE" => 'OK',
                    'HTML' => true
                )
            );
        }
        ?>
            <br>
            <a href="/bitrix/admin/acrit_exportpro_list.php" class="adm-btn"><?=GetMessage( 'ACRIT_EXPORTPRO_PROFILE_LIST' )?></a>
        <?
    }
}?>

<?require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php" );?>