<?php
IncludeModuleLangFile( __FILE__ );

//$options = $profileUtils->createFieldset();
//$iblockList = array();
//$dbIblock = CIBlock::GetList();
//while($arIBlock = $dbIblock->Fetch())
//    $iblockList[] = $arIBlock["ID"];
$options = $profileUtils->createFieldset2( $arProfile["IBLOCK_ID"], true );

$fieldType = array(
    "none" => GetMessage( "ACRIT_EXPORTPRO_NE_VYBRANO" ),
    "field" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_FIELD" ),
    "const" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONST" ),
    "complex" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_COMPLEX" )
);

$fieldTypeComplex = array(
    "none" => GetMessage( "ACRIT_EXPORTPRO_NE_VYBRANO" ),
    "field" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_FIELD" ),
    "const" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONST" ),
);

$idCnt = 0;                      
?>
<tr class="heading" align="center">
    <td colspan="2"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_HEADER" )?></td>
</tr>
<tr align="center">
    <td colspan="2">
        <?=BeginNote();?>
        <?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_DESCRIPTION" )?>
        <?=EndNote();?>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <table id="fieldset-container" cellpadding="0" cellspacing="0" width="100%">
            <tbody>
        <?foreach( $arProfile["XMLDATA"] as $id => $field ):?>
        <?                                               
            $useCondition = $field["USE_CONDITION"] == "Y" ? 'checked="checked"' : "";
            $hideCondition = $useCondition ? "" : "hide";
            $hideComplexBlock = $field["TYPE"] == "complex" ? "" : "hide";
            $hideConstBlock = $field["TYPE"] == "const" ? "" : "hide";
            $hideFieldBlock = ( ( $field["TYPE"] != "field" ) && ( !$hideConstBlock || !$hideComplexBlock ) ) || $field["TYPE"] == "none" || !$field["TYPE"] ? "hide" : "";
            
            $hideComplexConstTrueBlock = $field["COMPLEX_TRUE_TYPE"] == "const" ? "" : "hide";
            $hideComplexFieldTrueBlock = ( ( $field["COMPLEX_TRUE_TYPE"] != "field" ) && !$hideComplexConstTrueBlock ) || $field["COMPLEX_TRUE_TYPE"] == "none" || !$field["COMPLEX_TRUE_TYPE"] ? "hide" : "";
            
            $hideComplexConstFalseBlock = $field["COMPLEX_FALSE_TYPE"] == "const" ? "" : "hide";
            $hideComplexFieldFalseBlock = ( ( $field["COMPLEX_FALSE_TYPE"] != "field" ) && !$hideComplexConstFalseBlock ) || $field["COMPLEX_FALSE_TYPE"] == "none" || !$field["COMPLEX_FALSE_TYPE"] ? "hide" : "";
            
            $required = $field["REQUIRED"] == "Y" ? 'checked="checked"' : "";
            $deleteOnEmpty = $field["DELETE_ONEMPTY"] == "N" ? "" : 'checked="checked"';
            //$deleteOnEmptyAttributes = $field["DELETE_ONEMPTY_ATTRIBUTES"] == "N" ? "" : 'checked="checked"';
            $htmlEncode = $field["HTML_ENCODE"] == "N" ? "" : 'checked="checked"';
            $htmlEncodeCut = $field["HTML_ENCODE_CUT"] == "Y" ? 'checked="checked"' : "";
            $htmlToTxt = $field["HTML_TO_TXT"] == "N" ? "" : 'checked="checked"';
            $skipUntermElement = $field["SKIP_UNTERM_ELEMENT"] == "N" ? "" : 'checked="checked"';
            $urlEncode = $field["URL_ENCODE"] == "Y" ? 'checked="checked"' : "";
            $convertCase = $field["CONVERT_CASE"] == "Y" ? 'checked="checked"' : "";
            $textLimit = $field["TEXT_LIMIT"];
            $multiPropLimit = $field["MULTIPROP_LIMIT"];
        ?>
        <tr class="fieldset-item" data-id="<?=$idCnt++?>">
            <td>
                <label for="PROFILE[XMLDATA][<?=$id?>]"><?=$field["NAME"]?></label>
                <input type="hidden" name="PROFILE[XMLDATA][<?=$id?>][NAME]" value="<?=$field["NAME"]?>" />
            </td>
            <td colspan="2" style="position: relative" class="adm-detail-content-cell-r">
                <input type="text" name="PROFILE[XMLDATA][<?=$id?>][CODE]" value="<?=$field["CODE"]?>" />
                <select name="PROFILE[XMLDATA][<?=$id?>][TYPE]" onchange="ShowConvalueBlock(this)" data-id="<?=$id?>">
                    <?foreach( $fieldType as $typeId => $typeName ):?>
                        <?$selected = $typeId == $field["TYPE"] ? 'selected="selected"' : "";?>
                        <option value="<?=$typeId?>" <?=$selected?>><?=$typeName?></option>
                    <?endforeach?>
                </select>
                <select class="field-block <?=$hideFieldBlock?>" name="PROFILE[XMLDATA][<?=$id?>][VALUE]">
                    <option value="">--<?=GetMessage( "ACRIT_EXPORTPRO_NE_VYBRANO" )?>--</option>
                    <?if( $field["TYPE"] == "field" ){
                        $opt = $profileUtils->selectFieldset2( $options, $field["VALUE"] );
                        echo implode( "\n", $opt );
                        unset( $opt );
                    }?>                                    
                </select>                                 
                <div class="const-block <?=$hideConstBlock?>">
                    <?$hideContvalueFalse = !$useCondition ? "hide" : "";?>
                    <?$showPlaceholder = !$hideContvalueFalse ? "placeholder" : "data-placeholder";?>
                    <textarea name="PROFILE[XMLDATA][<?=$id?>][CONTVALUE_TRUE]" <?=$showPlaceholder?>="<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONDITION_TRUE" )?>"><?=$field["CONTVALUE_TRUE"]?></textarea>
                    <textarea name="PROFILE[XMLDATA][<?=$id?>][CONTVALUE_FALSE]" placeholder="<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONDITION_FALSE" )?>" class="<?=$hideContvalueFalse?>"><?=$field["CONTVALUE_FALSE"]?></textarea>
                </div>
                <div class="complex-block-container <?=$hideComplexBlock?>">
                    <div class="complex-block">
                        <?$hideComplexFalse = !$useCondition ? "hide" : "";?>
                        <?$showPlaceholder = !$hideComplexFalse ? "placeholder" : "data-placeholder";?>
                        
                        <div>
                            <select name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_TRUE_TYPE]" onchange="ShowConvalueBlockComplex(this)" data-id="<?=$id?>">
                                <?foreach( $fieldTypeComplex as $typeComplexId => $typeNameComplex ):?>
                                    <?$selectedComplex = $typeComplexId == $field["COMPLEX_TRUE_TYPE"] ? 'selected="selected"' : "";?>
                                    <option value="<?=$typeComplexId?>" <?=$selectedComplex?>><?=$typeNameComplex?></option>
                                <?endforeach?>
                            </select>
                            <select class="field-block-complex <?=$hideComplexFieldTrueBlock?>" name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_TRUE_VALUE]">
                                <option value="">--<?=GetMessage( "ACRIT_EXPORTPRO_NE_VYBRANO" )?>--</option>
                                <?if( $field["COMPLEX_TRUE_TYPE"] == "field" ){
                                    $opt = $profileUtils->selectFieldset2( $options, $field["COMPLEX_TRUE_VALUE"] );
                                    echo implode( "\n", $opt );
                                    unset( $opt );
                                }?>                                    
                            </select>                                 
                            <div class="const-block-complex <?=$hideComplexConstTrueBlock?>">
                                <textarea name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_TRUE_CONTVALUE]" <?=$showPlaceholder?>="<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONDITION_TRUE" )?>"><?=$field["COMPLEX_TRUE_CONTVALUE"]?></textarea>
                            </div>
                        </div>                                                                 
                        <div class="<?=$hideComplexFalse?>">                                                                 
                            <select name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_FALSE_TYPE]" onchange="ShowConvalueBlockComplexFalse(this)" data-id="<?=$id?>">
                                <?foreach( $fieldTypeComplex as $typeComplexId => $typeNameComplex ):?>
                                    <?$selectedComplex = $typeComplexId == $field["COMPLEX_FALSE_TYPE"] ? 'selected="selected"' : "";?>
                                    <option value="<?=$typeComplexId?>" <?=$selectedComplex?>><?=$typeNameComplex?></option>
                                <?endforeach?>
                            </select>
                            <select class="field-block-complex-false <?=$hideComplexFieldFalseBlock?>" name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_FALSE_VALUE]">
                                <option value="">--<?=GetMessage( "ACRIT_EXPORTPRO_NE_VYBRANO" )?>--</option>
                                <?if( $field["COMPLEX_FALSE_TYPE"] == "field" ){
                                    $opt = $profileUtils->selectFieldset2( $options, $field["COMPLEX_FALSE_VALUE"] );
                                    echo implode( "\n", $opt );
                                    unset( $opt );
                                }?>                                    
                            </select>                                 
                            <div class="const-block-complex-false <?=$hideComplexConstFalseBlock?>">
                                <textarea name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_FALSE_CONTVALUE]" <?=$showPlaceholder?>="<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONDITION_TRUE" )?>"><?=$field["COMPLEX_FALSE_CONTVALUE"]?></textarea>
                            </div>
                        </div>                                                                 
                    </div>
                </div>
                <span class="fieldset-item-delete">&times</span>
                <div style="margin: 10px 0px 10px 15px;">
                    <span id="hint_EXPORTPRO_FIELDSET_REQUIRED"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_REQUIRED' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_REQUIRED_HELP" )?>' );</script>
                    <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][REQUIRED]" value="Y" <?=$required?> />
                    <label for="PROFILE[XMLDATA][<?=$id?>][REQUIRED]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_REQUIRED" )?></label>
                    
                    <div style="height: 5px;">&nbsp;</div>
                    
                    <span id="hint_EXPORTPRO_FIELDSET_CONDITION"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_CONDITION' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONDITION_HELP" )?>' );</script>
                    <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][USE_CONDITION]" <?=$useCondition?> value="Y" data-id="<?=$id?>" onclick="ShowConditionBlock( this, <?=$idCnt?> )"/>
                    <label for="PROFILE[XMLDATA][<?=$id?>][USE_CONDITION]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONDITION" )?></label>
                    
                    <div style="height: 5px;">&nbsp;</div>
                    
                    <span id="hint_EXPORTPRO_FIELDSET_DELETE_ONEMPTY"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_DELETE_ONEMPTY' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_DELETE_ONEMPTY_HELP" )?>' );</script>
                    <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][DELETE_ONEMPTY]" <?=$deleteOnEmpty?> value="Y">
                    <label for="PROFILE[XMLDATA][<?=$id?>][DELETE_ONEMPTY]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_DELETE_ONEMPTY" )?></label>
                    
                    <div style="height: 5px;">&nbsp;</div>
                    
                    <?/*<span id="hint_EXPORTPRO_FIELDSET_DELETE_ONEMPTY_ATTRIBUTES"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_DELETE_ONEMPTY_ATTRIBUTES' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_DELETE_ONEMPTY_ATTRIBUTES_HELP" )?>' );</script>
                    <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][DELETE_ONEMPTY_ATTRIBUTES]" <?=$deleteOnEmptyAttributes?> value="Y">
                    <label for="PROFILE[XMLDATA][<?=$id?>][DELETE_ONEMPTY_ATTRIBUTES]"><?=GetMessage('ACRIT_EXPORTPRO_FIELDSET_DELETE_ONEMPTY_ATTRIBUTES')?></label>
                    
                    <div style="height: 5px;">&nbsp;</div>*/?>
                    
                    <span id="hint_EXPORTPRO_FIELDSET_URL_ENCODE"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_URL_ENCODE' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_URL_ENCODE_HELP" )?>' );</script>
                    <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][URL_ENCODE]" <?=$urlEncode?> value="Y">
                    <label for="PROFILE[XMLDATA][<?=$id?>][URL_ENCODE]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_URL_ENCODE" )?></label>
                    
                    <div style="height: 5px;">&nbsp;</div>
                    
                    <span id="hint_EXPORTPRO_FIELDSET_CONVERT_CASE"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_CONVERT_CASE' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONVERT_CASE_HELP" )?>' );</script>                    
                    <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][CONVERT_CASE]" <?=$convertCase?> value="Y">
                    <label for="PROFILE[XMLDATA][<?=$id?>][CONVERT_CASE]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONVERT_CASE" )?></label>
                    
                    <div style="height: 5px;">&nbsp;</div>
                    
                    <span id="hint_EXPORTPRO_FIELDSET_HTML_ENCODE"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_HTML_ENCODE' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_HTML_ENCODE_HELP" )?>' );</script>
                    <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][HTML_ENCODE]" <?=$htmlEncode?> value="Y">
                    <label for="PROFILE[XMLDATA][<?=$id?>][HTML_ENCODE]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_HTML_ENCODE" )?></label>
                    
                    <div style="height: 5px;">&nbsp;</div>
                    
                    <span id="hint_EXPORTPRO_FIELDSET_HTML_ENCODE_CUT"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_HTML_ENCODE_CUT' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_HTML_ENCODE_CUT_HELP" )?>' );</script>
                    <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][HTML_ENCODE_CUT]" <?=$htmlEncodeCut?> value="Y">
                    <label for="PROFILE[XMLDATA][<?=$id?>][HTML_ENCODE_CUT]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_HTML_ENCODE_CUT" )?></label>
                    
                    <div style="height: 5px;">&nbsp;</div>
                    
                    <span id="hint_EXPORTPRO_FIELDSET_HTML_TO_TXT"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_HTML_TO_TXT' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_HTML_TO_TXT_HELP" )?>' );</script>
                    <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][HTML_TO_TXT]" <?=$htmlToTxt?> value="Y">
                    <label for="PROFILE[XMLDATA][<?=$id?>][HTML_TO_TXT]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_HTML_TO_TXT" )?></label>
                    
                    <div style="height: 5px;">&nbsp;</div>
                    
                    <span id="hint_EXPORTPRO_FIELDSET_SKIP_UNTERM_ELEMENT"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_SKIP_UNTERM_ELEMENT' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_SKIP_UNTERM_ELEMENT_HELP" )?>' );</script>
                    <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][SKIP_UNTERM_ELEMENT]" <?=$skipUntermElement?> value="Y">
                    <label for="PROFILE[XMLDATA][<?=$id?>][SKIP_UNTERM_ELEMENT]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_SKIP_UNTERM_ELEMENT" )?></label>
                    
                    <div style="height: 5px;">&nbsp;</div>
                    
                    <span id="hint_EXPORTPRO_FIELDSET_TEXT_LIMIT"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_TEXT_LIMIT' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_TEXT_LIMIT_HELP" )?>' );</script>
                    <label for="PROFILE[XMLDATA][<?=$id?>][TEXT_LIMIT]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_TEXT_LIMIT" )?></label><br/>
                    <input type="text" name="PROFILE[XMLDATA][<?=$id?>][TEXT_LIMIT]" value="<?=$textLimit;?>" />
                    
                    <div style="height: 5px;">&nbsp;</div>
                    
                    <span id="hint_EXPORTPRO_FIELDSET_MULTIPROP_LIMIT"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_MULTIPROP_LIMIT' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_MULTIPROP_LIMIT_HELP" )?>' );</script>
                    <label for="PROFILE[XMLDATA][<?=$id?>][MULTIPROP_LIMIT]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_MULTIPROP_LIMIT" )?></label><br/>
                    <input type="text" name="PROFILE[XMLDATA][<?=$id?>][MULTIPROP_LIMIT]" value="<?=$multiPropLimit;?>" />
                </div>
                <div id="PROFILE_XMLDATA_<?=$id?>_CONDITION" class="condition-block <?=$hideCondition?>">
                <?
                    if( $field["USE_CONDITION"] == "Y" && CModule::IncludeModule( "catalog" ) ){
                        $obCond = new CAcritExportproCatalogCond();
                        CAcritExportproProps::$arIBlockFilter = $profileUtils->PrepareIBlock( $arProfile["IBLOCK_ID"], $arProfile["USE_SKU"] );
                        $boolCond = $obCond->Init(
                            0,
                            0,
                            array(
                                "FORM_NAME" => "exportpro_form",
                                "CONT_ID" => "PROFILE_XMLDATA_".$id."_CONDITION",
                                "JS_NAME" => "JSCatCond_field_".$idCnt, "PREFIX" => "PROFILE[XMLDATA][".$id."][CONDITION]"
                            )
                        );
                        if( !$boolCond ){
                            if( $ex = $APPLICATION->GetException() ){
                                echo $ex->GetString()."<br>";
                            }
                        }
                        $obCond->Show( $field["CONDITION"] );
                    }
                ?>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <hr style="opacity: 0.2;">
            </td>
        </tr>
        <?endforeach?>
        </tbody>
        </table>
    </td>
</tr>
<tr>
    <td colspan="2" align="center" id="fieldset-item-add-button">
        <button class="adm-btn" onclick="FieldsetAdd( this ); return false;">
            <?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONDITION_ADD" );?>
        </button>
    </td>
</tr>


