<?php
$moduleID = 'acrit.exportpro';
require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php" );
require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/$moduleID/include.php" );
//require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/dull/prolog.php" );

CModule::IncludeModule( 'acrit.exportpro' );

$POST_RIGHT = $APPLICATION->GetGroupRight( $moduleID );
if( $POST_RIGHT == "D" )
	$APPLICATION->AuthForm( GetMessage( "ACCESS_DENIED" ) );

if( !CModule::IncludeModule( 'iblock' ) ){
	return false;
}

IncludeModuleLangFile( __FILE__ );

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
		"START_LAST_TIME" => $find_start_last_time_1,
	);
}

if( $lAdmin->EditAction() && $POST_RIGHT == "W" ){
	foreach( $FIELDS as $ID => $arFields ){
		if( !$lAdmin->IsUpdated( $ID ) ){
			continue;
		}

		$DB->StartTransaction();
		$ID = IntVal($ID);
		if( !$ob->Update( $ID, $arFields ) ){
			$lAdmin->AddUpdateError( GetMessage( "export_save_err" ).$ID.": ".$ob->LAST_ERROR, $ID );
			$DB->Rollback();
		}
		$DB->Commit();
	}
}

if( ( $arID = $lAdmin->GroupAction() ) && $POST_RIGHT == "W" ){
	// если выбрано "Для всех элементов"
	if( $_REQUEST['action_target'] == 'selected' ){
		$rsData = $cData->GetList(
            array( $by => $order ),
            $arFilter
        );
		
        while( $arRes = $rsData->Fetch() ){
            $arID[] = $arRes['ID'];
        }
	}
	
	// пройдем по списку элементов
	foreach( $arID as $ID ){
		if( strlen( $ID ) <= 0 )
			continue;
		
        $ID = IntVal( $ID );
		
		// для каждого элемента совершим требуемое действие
		switch( $_REQUEST['action'] ){
			// удаление
			case "delete":
				@set_time_limit( 0 );
				$DB->StartTransaction();
				
                if( !$cData->Delete( $ID ) ){
					$DB->Rollback();
					$lAdmin->AddGroupError( GetMessage( "rub_del_err" ), $ID );
				}
				
                $DB->Commit();
				break;
			
			// активация/деактивация
			case "activate":
			case "deactivate":
				if( ( $rsData = $cData->GetByID( $ID ) ) ){
                    $rsData["ACTIVE"] = ( $_REQUEST['action'] == "activate" ? "Y" : "N" );
					if( !$cData->Update( $ID, $rsData ) ){
                        $lAdmin->AddGroupError( GetMessage( "rub_save_error" ).$cData->LAST_ERROR, $ID );
                    }
                        
                    if( $rsData["ACTIVE"] == "N" ){
                        CExportproAgent::DelAgent( $ID );
                    }
				}
				else
					$lAdmin->AddGroupError( GetMessage( "rub_save_error" )." ".GetMessage( "rub_no_rubric" ), $ID );
				break;
		}
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
        array(
            "id" => "UNLOADED_OFFERS",
            "content" => GetMessage( "parser_unloaded_offers" ),
            "sort" => "unloaded_offers",
            "default" => true,
        ),
        array(
            "id" => "UNLOADED_OFFERS_CORRECT",
            "content" => GetMessage( "parser_unloaded_offers_correct" ),
            "sort" => "unloaded_offers_correct",
            "default" => true,
        ),
        array(
            "id" => "UNLOADED_OFFERS_ERROR",
            "content" => GetMessage( "parser_unloaded_offers_error" ),
            "sort" => "unloaded_offers_error",
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
	$f_SETUP = unserialize( base64_decode( $f_SETUP ) );
	$row = & $lAdmin->AddRow( $f_ID, $arRes );
	$row->AddViewField( "NAME", '<a href="acrit_exportpro_edit.php?ID='.$f_ID.'&amp;lang='.LANG.'" title="'.GetMessage( "parser_act_edit" ).'">'.$f_NAME.'</a>' );
	$row->AddInputField( "NAME", array( "size" => 20 ) );
	$row->AddViewField( "START_LAST_TIME_X", $f_SETUP['LAST_START_EXPORT'] );
	$row->AddViewField( "TYPE_RUN", $f_TYPE_RUN == 'comp' ? GetMessage( 'ACRIT_EXPORTPRO_RUN_TYPE_COMPONENT' ) : GetMessage( 'ACRIT_EXPORTPRO_RUN_TYPE_CRON' ) );
	$arActions = Array();
	if( $POST_RIGHT == "W" ){
		$arActions[] = array(
			"ICON" => "edit",
			"DEFAULT" => true,
			"TEXT" => GetMessage( "parser_act_edit" ),
			"ACTION" => $lAdmin->ActionRedirect( "acrit_exportpro_edit.php?ID=".$f_ID )
		);
	}

	if( $POST_RIGHT == "W" ){
		$arActions[] = array(
			"ICON" => "delete",
			"TEXT" => GetMessage( "parser_act_del" ),
			"ACTION" => "if(confirm('".GetMessage( "parser_act_del_conf" )."')) ".$lAdmin->ActionDoGroup( $f_ID, "delete" )
		);
	}

	if( $POST_RIGHT == "W" ){
		$arActions[] = array(
			"ICON" => "copy",
			"DEFAULT" => true,
			"TEXT" => GetMessage( "parser_act_copy" ),
			"ACTION" => $lAdmin->ActionRedirect( "acrit_exportpro_edit.php?copy=$f_ID&ID=$f_ID" )
		);
	}

	$arActions[] = array( "SEPARATOR" => true );
	if( is_set( $arActions[count( $arActions ) - 1], "SEPARATOR" ) ){
		unset( $arActions[count( $arActions ) - 1] );
	}

	$row->AddActions( $arActions );
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
	    "delete" => GetMessage( "MAIN_ADMIN_LIST_DELETE" ), // удалить выбранные элементы
	    "activate" => GetMessage( "MAIN_ADMIN_LIST_ACTIVATE" ), // активировать выбранные элементы
	    "deactivate" => GetMessage( "MAIN_ADMIN_LIST_DEACTIVATE" ), // деактивировать выбранные элементы
    )
);

$aContext = array(
	array(
		"TEXT" => GetMessage( "MAIN_ADD" ),
		"LINK" => 'acrit_exportpro_edit.php?lang='.LANG,
		"TITLE" => GetMessage( "PARSER_ADD_TITLE" ),
		"ICON" => "btn_new",
	),
);

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
			CAdminMessage::ShowMessage(
                array(
                    "MESSAGE" => $success,
                    "TYPE" => "OK"
                )
            );
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

<?require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php" );?>