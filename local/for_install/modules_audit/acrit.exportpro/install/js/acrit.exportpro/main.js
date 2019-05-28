$( function(){
    $( 'select[name="PROFILE[LID]"]' ).change( function(){
        getData( 'change_site' );
    });
    
    $( 'input[name="PROFILE[VIEW_CATALOG]"]' ).click( function(){
        getData( 'catalog_only' );
    });
    
    //$( 'input[name="PROFILE[USE_SKU]"]' ).click( function(){
    //    getData( 'use_offers' );
    //});
    
    $( 'input[name="PROFILE[CHECK_INCLUDE]"]' ).click( function(){
        getData( 'include_subsection' );
    });
    
    $( document ).on( 'change', 'select[name="PROFILE[IBLOCK_TYPE_ID][]"]', function(){
        getData( 'change_ibtype' );
    });
    
    $( document ).on( 'change', 'select[name="PROFILE[IBLOCK_ID][]"]', function(){
        getData( 'change_iblock' );
    });
    
    $( document ).on( 'change', 'select[name="PROFILE[TYPE]"]', function(){
        getData( 'change_type' );
        $( '#tab_cont_step6' ).hide();
        $( '#tab_cont_step10' ).hide();
        $( '#tab_cont_step13' ).hide();
        $( '#tab_cont_step16' ).hide();
        $( '#tab_cont_step17' ).hide();
        $( '#tab_cont_step18' ).hide();
        $( '#tab_cont_step12' ).hide();
        switch( $( this ).val() ){
            case 'activizm':
                $( '#tab_cont_step10' ).show();
                break;
            case 'ebay_1':
            case 'ebay_2':
                $( '#tab_cont_step12' ).show();
                break;
            case 'ozon':
                $( '#tab_cont_step16' ).show();
                $( '#tab_cont_step13' ).show();
                break;
            case 'ua_hotline_ua':
                $( '#tab_cont_step17' ).show();
                break;
            case 'google':
                $( '#tab_cont_step18' ).show();
                break;
            default:
                $( '#tab_cont_step6' ).show();
                break;
        }
    });
    $( document ).on( 'click', '#step5 .fieldset-item-delete', function(){
        $( this ).parent().parent().remove();
    });
    $( document ).on( 'click', '#step19 .fieldset-item-delete', function(){
        $( this ).parent().parent().remove();
    });
});

function prepareData(){
    queryData = {
        'PROFILE[VIEW_CATALOG]' : typeof $( 'input[name="PROFILE[VIEW_CATALOG]"]' ).attr( 'checked' ) === "undefined" ? '' : 'Y',
        'PROFILE[USE_SKU]' : typeof $( 'input[name="PROFILE[USE_SKU]"]' ).attr( 'checked' ) === "undefined" ? '' : 'Y',
        'PROFILE[CHECK_INCLUDE]' : typeof $( 'input[name="PROFILE[CHECK_INCLUDE]"]' ).attr( 'checked' ) === "undefined" ? '' : 'Y',
        'PROFILE[IBLOCK_TYPE_ID][]' : $( 'select[name="PROFILE[IBLOCK_TYPE_ID][]"]' ).val(),
        'PROFILE[IBLOCK_ID][]' : $( 'select[name="PROFILE[IBLOCK_ID][]"]' ).val(),
        'PROFILE[CATEGORY][]' : $( 'select[name="PROFILE[CATEGORY][]"]' ).val(),
        'PROFILE[LID]' : $( 'select[name="PROFILE[LID]"]' ).val(),
        'PROFILE[TYPE]' : $( 'select[name="PROFILE[TYPE]"]' ).val(),
        'sessid' : BX.message( 'bitrix_sessid' )
    };
    return queryData;
}

function getData(action, data, async, append){
    if (typeof data === 'undefined') {
        data = prepareData();
    }
    BX.showWait('waitContainer');
    data['ajax_action'] = action;
    $.ajax({
        'type': 'POST',
        'method': 'POST',
        'dataType': 'json',
        'url': '/bitrix/admin/acrit_exportpro_ajax.php',
        'data': data, //$( 'form' ).serialize() + "&ajax_action=" + action,
        'async': async,
        'success': function( data ){
            if( data.result == 'ok' ){
                for( var block_key in data.blocks ){
                    if( data.blocks[block_key].append == true ){
                        $( data.blocks[block_key].id ).append( data.blocks[block_key].html );
                    }
                    else{
                        $( data.blocks[block_key].id ).html( data.blocks[block_key].html );
                    }
                }
            }
            BX.closeWait( 'waitContainer' );
        },
    });
}

function ShowConditionBlock( value, cnt ){
    var condId = $( value ).parent().parent().parent().attr( 'data-id' );
    getData( 'get_condition_block', {
        'sessid' : BX.message( 'bitrix_sessid' ),
        'fId' : condId,
        'fCnt' : cnt,
        'PROFILE[USE_SKU]' : typeof $( 'input[name="PROFILE[USE_SKU]"]' ).attr( 'checked' ) === 'undefined' ? '' : 'Y',
        'PROFILE[IBLOCK_ID][]' : $( 'select[name="PROFILE[IBLOCK_ID][]"]' ).val(),
    });                                                                                                                                           
    
    var contValueFalse = $( 'textarea[name="PROFILE[XMLDATA][' + $( value ).attr( 'data-id' ) + '][CONTVALUE_FALSE]"]' );
    var contComplexValueFalse = $( 'textarea[name="PROFILE[XMLDATA][' + $( value ).attr( 'data-id' ) + '][COMPLEX_FALSE_CONTVALUE]"]' );
    
    fieldType = $( 'select[name="PROFILE[XMLDATA][' + $( value ).attr( 'data-id' ) + '][TYPE]"] option:selected' ).val();
    
    var aaa = $( '.fieldset-item[data-id=' + condId + '] .complex-block-container .complex-block div:eq(2)' );
    console.log( aaa );
    
    if( typeof $( value ).attr( 'checked' ) !== 'undefined' ){
        $( contValueFalse ).show().removeClass( 'hide' );
        $( '.fieldset-item[data-id=' + condId + '] .complex-block-container .complex-block div:eq(2)' ).show().removeClass( 'hide' );
        $( contComplexValueFalse ).show().removeClass( 'hide' );
        $( '#PROFILE_XMLDATA_' + condId + '_CONDITION' ).removeClass( 'hide' );
    }
    else{
        $( contValueFalse ).hide().addClass( 'hide' );
        $( '.fieldset-item[data-id=' + condId + '] .complex-block-container .complex-block div:eq(2)' ).hide().addClass( 'hide' );
        $( contComplexValueFalse ).hide().addClass( 'hide' );
        $( '#PROFILE_XMLDATA_' + condId + '_CONDITION' ).addClass( 'hide' );
    }
}

function ShowConvalueBlock( value ){
    if( value.value == 'field' ){
        $( value ).siblings( '.field-block').show().removeClass( 'hide' );
        $( value ).siblings( '.const-block').hide().addClass( 'hide' );
        $( value ).siblings( '.complex-block-container' ).hide().addClass( 'hide' );
        var data = prepareData();
        data['data_id'] = $( value ).attr( 'data-id' );
        getData( 'fieldset_field_select', data );
    }
    else if( value.value == 'const' ){                                     
        $( value ).siblings( '.const-block' ).show().removeClass( 'hide' );
        $( value ).siblings( '.field-block' ).hide().addClass( 'hide' );
        $( value ).siblings( '.complex-block-container' ).hide().addClass( 'hide' );
    }
    else if( value.value == 'complex' ){    
        $( value ).siblings( '.complex-block-container' ).show().removeClass( 'hide' );
        $( value ).siblings( '.const-block' ).hide().addClass( 'hide' );
        $( value ).siblings( '.field-block' ).hide().addClass( 'hide' );
    }
    else{
        $( value ).siblings( '.complex-block-container' ).hide().addClass( 'hide' );
        $( value ).siblings( '.const-block' ).hide().addClass( 'hide' );
        $( value ).siblings( '.field-block' ).hide().addClass( 'hide' );
    }
}

function ShowConvalueBlockComplex( value ){
    if( value.value == 'field' ){
        $( value ).siblings( '.field-block-complex').show().removeClass( 'hide' );
        $( value ).siblings( '.const-block-complex').hide().addClass( 'hide' );
        var data = prepareData();
        data['data_id'] = $( value ).attr( 'data-id' );
        data['action_holder'] = "COMPLEX_TRUE_VALUE";
        getData( 'fieldset_field_select', data );
    }
    else if( value.value == 'const' ){                                     
        $( value ).siblings( '.const-block-complex' ).show().removeClass( 'hide' );
        $( value ).siblings( '.field-block-complex' ).hide().addClass( 'hide' );
    }
    else{
        $( value ).siblings( '.const-block-complex' ).hide().addClass( 'hide' );
        $( value ).siblings( '.field-block-complex' ).hide().addClass( 'hide' );
    }
}

function ShowConvalueBlockComplexFalse( value ){
    if( value.value == 'field' ){
        $( value ).siblings( '.field-block-complex-false').show().removeClass( 'hide' );
        $( value ).siblings( '.const-block-complex-false').hide().addClass( 'hide' );
        var data = prepareData();
        data['data_id'] = $( value ).attr( 'data-id' );
        data['action_holder'] = "COMPLEX_FALSE_VALUE";
        getData( 'fieldset_field_select', data );
    }
    else if( value.value == 'const' ){                                     
        $( value ).siblings( '.const-block-complex-false' ).show().removeClass( 'hide' );
        $( value ).siblings( '.field-block-complex-false' ).hide().addClass( 'hide' );
    }
    else{
        $( value ).siblings( '.const-block-complex-false' ).hide().addClass( 'hide' );
        $( value ).siblings( '.field-block-complex-false' ).hide().addClass( 'hide' );
    }
}

function CalcExportStep( profileId ){
    BX.showWait( 'waitContainer' );
    $.ajax({
        'type': 'POST',
        'method': 'POST',
        'dataType': 'json',
        'url': '/bitrix/admin/acrit_exportpro_ajax.php',
        'data': {
            'profileId' : profileId,
            'ajax_action' : 'calcSteps',
            'sessid' : BX.message( 'bitrix_sessid' ),
        },
        'success': function( data ){
            if( data.result == 'ok' ){
                $( 'input[name="PROFILE[SETUP][EXPORT_STEP]"]' ).val( data.data );
            }
            BX.closeWait( 'waitContainer' );
        },
    });
}

function convertCurrency(){
    $( '.currency_table' ).toggle();
}

function FieldsetAdd( obj ){
    var id = $( '#step5 tr.fieldset-item' ).last().attr( 'data-id' );
    if( typeof id === 'undefined' ){
        id = 0;
    }
    BX.showWait( 'waitContainer' );
    $.ajax({
        'type': 'POST',
        'method': 'POST',
        'dataType': 'json',
        'url': '/bitrix/admin/acrit_exportpro_ajax.php',
        'data': 'id=' + id + "&ajax_action=fieldset_add&sessid=" + BX.message( 'bitrix_sessid' ),
        'success': function( data ){
            if( data.result == 'ok' ){
                var text = $( "#step5 #fieldset-container tbody" ).append( data.data );
            }
            BX.closeWait( 'waitContainer' );   
        },
    });
    return false;
}

function ConvertFieldsetAdd( obj ){
    var id = $( '#step19 tr.fieldset-item' ).last().attr( 'data-id' );
    if( typeof id === 'undefined' ){
        id = 0;
    }
    BX.showWait( 'waitContainer' );
    $.ajax({
        'type': 'POST',
        'method': 'POST',
        'dataType': 'json',
        'url': '/bitrix/admin/acrit_exportpro_ajax.php',
        'data': 'id=' + id + "&ajax_action=convert_fieldset_add&sessid=" + BX.message( 'bitrix_sessid' ),
        'success': function( data ){
            if( data.result == 'ok' ){
                var text = $( "#step19 #convert-fieldset-container tbody" ).append( data.data );
            }
            BX.closeWait( 'waitContainer' );   
        },
    });
    return false;
}

function ShowMarketForm( type ){
    var marketId = $( 'select[name="PROFILE[MARKET_CATEGORY][CATEGORY]"]' ).val();
    if( type == 'edit' ){
        BX.showWait( 'waitContainer' );
        $.ajax({
            'type': 'POST',
            'method': 'POST',
            'dataType': 'json',
            'url': '/bitrix/admin/acrit_exportpro_ajax.php',
            'data': {
                'marketId' : marketId,
                'ajax_action' : 'market_edit',
                'sessid' : BX.message( 'bitrix_sessid' ),
            },
            'success': function( data ){
                if( data.result == 'ok' ){
                    $( 'input[name="PROFILE[MARKET_CATEGORY_NAME]"]' ).val( data.name );
                    $( 'textarea[name="PROFILE[MARKET_CATEGORY_DATA]"]' ).val( data.data );
                    $( 'input[name="PROFILE[MARKET_CATEGORY_ID]"]' ).val( data.id );
                }
                BX.closeWait( 'waitContainer' );
            },
        });
    }
    $( '#step6 #category_add' ).show();
}

function HideMarketForm(){
    $( 'input[name="PROFILE[MARKET_CATEGORY_ID]"]' ).val( '' );
    $( 'input[name="PROFILE[MARKET_CATEGORY_NAME]"]' ).val( '' );
    $( 'textarea[name="PROFILE[MARKET_CATEGORY_DATA]"]' ).val( '' );
    $( '#step6 #category_add' ).hide();
}

function SaveMarketForm(){
    var marketId = $( 'input[name="PROFILE[MARKET_CATEGORY_ID]"]' ).val();
    var marketName = $( 'input[name="PROFILE[MARKET_CATEGORY_NAME]"]' ).val();
    var marketData = $( 'textarea[name="PROFILE[MARKET_CATEGORY_DATA]"]' ).val();
    var current = $( 'select[name="PROFILE[MARKET_CATEGORY][CATEGORY]"]' ).val();
    
    var data = {
        'marketId' : marketId,
        'marketName' : marketName,
        'marketData' : marketData,
        'current' : current,
        'sessid' : BX.message( 'bitrix_sessid' )
    };
    
    getData( 'market_save', data );
    HideMarketForm();
}

function ChangeMarketCategory( marketId ){
    var data = {
        'marketId' : marketId,
        'sessid' : BX.message( 'bitrix_sessid' ),
        'PROFILE[IBLOCK_ID][]' : $( 'select[name="PROFILE[IBLOCK_ID][]"]' ).val(),
        'PROFILE[CHECK_INCLUDE]' : typeof $( 'input[name="PROFILE[CHECK_INCLUDE]"]' ).attr( 'checked' ) === "undefined" ? '' : 'Y',
        'PROFILE[CATEGORY][]' : $( 'select[name="PROFILE[CATEGORY][]"]' ).val(),
    };
    getData( 'change_market_category', data );
}

var MarketCategoryItem = '';
var MarketCategoryObject;
var PropertyListItem = '';
var PropertyListItemValue = '';
var PropertyListObject;

function SetMarketCategory( categoryValue ){
    $( 'input[name="PROFILE[MARKET_CATEGORY][CATEGORY_LIST][' + MarketCategoryItem + ']"]').val( categoryValue );
    MarketCategoryItem = '';
    MarketCategoryObject.close();
}

function SetMarketCategoryEbay( categoryValue, categoryName ){
    categoryName = $( categoryName ).find( 'option[value="' + categoryValue + '"]' ).text();
    $( 'input[name="PROFILE[MARKET_CATEGORY][EBAY][CATEGORY_LIST][' + MarketCategoryItem + ']"]' ).val( categoryValue );
    $( 'input[name="PROFILE_MARKET_CATEGORY_CATEGORY_LIST_EBAY_' + MarketCategoryItem + '_NAME"]' ).val( categoryName );
    MarketCategoryItem = '';
    MarketCategoryObject.close();
}

function SetMarketCategoryOzon( categoryValue, categoryName ){
    categoryName = $( categoryName ).find( 'option[value="' + categoryValue + '"]' ).text();
    $( 'input[name="PROFILE[MARKET_CATEGORY][OZON][CATEGORY_LIST][' + MarketCategoryItem + ']"]' ).val( categoryValue );
    $( 'input[name="PROFILE_MARKET_CATEGORY_CATEGORY_LIST_OZON_' + MarketCategoryItem + '_NAME"]' ).val( categoryName );
    MarketCategoryItem = '';
    MarketCategoryObject.close();
}

function ChangeFileType( value ){
    if( value == 'csv' ){
        $fieldVal = $( '#URL_DATA_FILE' ).val();
        $fieldVal = $fieldVal.replace( '.xml', '.csv' );
        $( '#URL_DATA_FILE' ).val( $fieldVal );
        $( '#export_step_value' ).val( 50000 );
        $( '#tr_csv_info' ).show();
    }
    else{
        $fieldVal = $( '#URL_DATA_FILE' ).val();
        $fieldVal = $fieldVal.replace( '.csv', '.xml' );
        $( '#URL_DATA_FILE' ).val( $fieldVal );
        $( '#export_step_value' ).val( 50 );
        $( '#tr_csv_info' ).hide();
    }
}

function ChangeRunType( value ){
    if( value == 'cron' ){
        $( '#tr_cron_info' ).removeClass( 'hide' );
        $( '#tr_date_start' ).removeClass( 'hide' );
        $( '#tr_date_period' ).removeClass( 'hide' );
        $( '#tr_cron_threads' ).removeClass( 'hide' );
        $( '#tr_run_new_window' ).addClass( 'hide' );
    }
    else{
        $( '#tr_cron_info' ).addClass( 'hide' );
        $( '#tr_date_start' ).addClass( 'hide' );
        $( '#tr_date_period' ).addClass( 'hide' );
        $( '#tr_cron_threads' ).addClass( 'hide' );
        $( '#tr_run_new_window' ).removeClass( 'hide' );
    }
}

function UpdateLog( obj ){
    getData( 'update_log', {
        'sessid' : BX.message( 'bitrix_sessid' ),
        'profileID' : $( obj ).attr( 'profileID' )
    });
}

$( document ).on( 'change', '#property_list select', function(){
    $( 'input[name="' + PropertyListItem + '"]' ).val( $( this ).find( 'option:selected' ).text() );
    $( 'input[name="' + PropertyListItemValue + '"]' ).val( $( this ).val() );
    PropertyListObject.close();
});

function UnlockExport( profileID ){
    getData( 'unlock_export', {
        'sessid' : BX.message( 'bitrix_sessid' ),
        'profileID' : profileID
    });
    $( '#tr_run_new_window' ).show();
}

function FilterMarketCategoryList( obj, $container ){
    console.log( $( obj ).val() );
    if( $( obj ).val() == '' ){
        $( '#' + $container + ' select option' ).show();
    }
    else{
        searchWords = $( obj ).val().split( " " );
        $( '#' + $container + ' select option' ).each( function(){
            var cOption = $( this );
            var cOoptionSearch = cOption.data( 'search' );
            var find = true;
            searchWords.forEach( function( curVal, ind, arr ){
                if( typeof( cOoptionSearch ) == 'string' ){
                    if( cOoptionSearch.indexOf( curVal.toLowerCase().trim() ) == -1 )
                        find = false;
                }
                
            })
            if( find ){
                cOption.show();
            }
            else{
                cOption.hide();
            }
        });
    }
}