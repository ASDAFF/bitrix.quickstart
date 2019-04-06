<?php
IncludeModuleLangFile( __FILE__ );

$options = $profileUtils->createFieldset2( $arProfile["IBLOCK_ID"], true );

$fieldType = array(
    "none" => GetMessage( "ACRIT_EXPORTPRO_NE_VYBRANO" ),
    "field" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_FIELD" ),
    "const" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONST" ),
    "complex" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_COMPLEX" ),
    "composite" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_COMPOSITE" )
);   

$fieldTypeComplex = array(
    "none" => GetMessage( "ACRIT_EXPORTPRO_NE_VYBRANO" ),
    "field" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_FIELD" ),
    "const" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONST" ),
);

$fieldTypeComposite = array(
    "none" => GetMessage( "ACRIT_EXPORTPRO_NE_VYBRANO" ),
    "field" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_FIELD" ),
    "const" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONST" ),
);

$roundModes = array(
    "none" => GetMessage( "ACRIT_EXPORTPRO_NE_VYBRANO" ),
    "UP" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_ROUND_MODE_UP" ),
    "DOWN" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_ROUND_MODE_DOWN" ),
    "EVEN" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_ROUND_MODE_EVEN" ),
    "ODD" => GetMessage( "ACRIT_EXPORTPRO_FIELDSET_ROUND_MODE_ODD" )
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
                <?foreach( $arProfile["XMLDATA"] as $id => $field ){
                    $selectedPropertyType = null;
                    $useCondition = $field["USE_CONDITION"] == "Y" ? 'checked="checked"' : "";
                    $hideCondition = $useCondition ? "" : "hide";
                    $hideComplexBlock = $field["TYPE"] == "complex" ? "" : "hide";
                    $hideCompositeBlock = $field["TYPE"] == "composite" ? "" : "hide";
                    $compositeTrueDivider = $field["COMPOSITE_TRUE_DIVIDER"];
                    $compositeFalseDivider = $field["COMPOSITE_FALSE_DIVIDER"];
                                        
                    $hideConstBlock = $field["TYPE"] == "const" ? "" : "hide";
                    $hideFieldBlock = ( ( ( $field["TYPE"] != "field" ) && ( !$hideConstBlock || !$hideComplexBlock ) ) || ( $field["TYPE"] == "composite" ) || ( $field["TYPE"] == "none" ) ||  !$field["TYPE"] ) ? "hide" : "";
                    $hideResolveBlock = CExportproProfile::getFieldsetResolve($arProfile["IBLOCK_ID"], $options, $field["VALUE"])!==false ? "":"hide";
                    
                    $hideComplexConstTrueBlock = $field["COMPLEX_TRUE_TYPE"] == "const" ? "" : "hide";
                    $hideComplexFieldTrueBlock = ( ( $field["COMPLEX_TRUE_TYPE"] != "field" ) && !$hideComplexConstTrueBlock ) || $field["COMPLEX_TRUE_TYPE"] == "none" || !$field["COMPLEX_TRUE_TYPE"] ? "hide" : "";
                    
                    $hideComplexConstFalseBlock = $field["COMPLEX_FALSE_TYPE"] == "const" ? "" : "hide";
                    $hideComplexFieldFalseBlock = ( ( $field["COMPLEX_FALSE_TYPE"] != "field" ) && !$hideComplexConstFalseBlock ) || $field["COMPLEX_FALSE_TYPE"] == "none" || !$field["COMPLEX_FALSE_TYPE"] ? "hide" : "";
                    
                    $required = $field["REQUIRED"] == "Y" ? 'checked="checked"' : "";
                    $deleteOnEmpty = $field["DELETE_ONEMPTY"] == "N" ? "" : 'checked="checked"';
                    $htmlEncode = $field["HTML_ENCODE"] == "N" ? "" : 'checked="checked"';
                    $htmlEncodeCut = $field["HTML_ENCODE_CUT"] == "Y" ? 'checked="checked"' : "";
                    $htmlToTxt = $field["HTML_TO_TXT"] == "N" ? "" : 'checked="checked"';
                    $skipUntermElement = $field["SKIP_UNTERM_ELEMENT"] == "N" ? "" : 'checked="checked"';
                    $urlEncode = $field["URL_ENCODE"] == "Y" ? 'checked="checked"' : "";
                    $convertCase = $field["CONVERT_CASE"] == "Y" ? 'checked="checked"' : "";
                    $textLimit = $field["TEXT_LIMIT"];
                    $multiPropLimit = $field["MULTIPROP_LIMIT"];
                    $roundPrecision = $field["ROUND"]["PRECISION"];
                    $roundMode = $field["ROUND"]["MODE"];
                    $calculateMinimum = $field["MINIMUM_OFFER_PRICE"] == "Y" ? 'checked="checked"' : "";?>
                    <tr class="fieldset-item" data-id="<?=$idCnt++?>">
                        <td>
                            <label for="PROFILE[XMLDATA][<?=$id?>]"><?=$field["NAME"]?></label>
                            <input type="hidden" name="PROFILE[XMLDATA][<?=$id?>][NAME]" value="<?=$field["NAME"]?>" />
                        </td>
                        <td colspan="2" style="position: relative" class="adm-detail-content-cell-r">
                            <input type="text" name="PROFILE[XMLDATA][<?=$id?>][CODE]" value="<?=$field["CODE"]?>" />
                            <select name="PROFILE[XMLDATA][<?=$id?>][TYPE]" onchange="ShowConvalueBlock( this )" data-id="<?=$id?>">
                                <?foreach( $fieldType as $typeId => $typeName ){?>
                                    <?$selected = $typeId == $field["TYPE"] ? 'selected="selected"' : "";?>
                                    <option value="<?=$typeId?>" <?=$selected?>><?=$typeName?></option>
                                <?}?>
                            </select>
                            
                            <select class="field-block <?=$hideFieldBlock?>" name="PROFILE[XMLDATA][<?=$id?>][VALUE]" onchange="ShowResolveBlock( this )" data-id="<?=$id?>">
                                <option value="">--<?=GetMessage( "ACRIT_EXPORTPRO_NE_VYBRANO" )?>--</option>
                                <?if( $field["TYPE"] == "field" ){
                                    $opt = $profileUtils->selectFieldset2( $options, $field["VALUE"] , $selectedPropertyType );
                                    echo implode( "\n", $opt );
                                    unset( $opt );
                                }?>                                    
                            </select>
                           
                            <select class="resolve-block <?=$hideResolveBlock?>" name="PROFILE[XMLDATA][<?=$id?>][RESOLVE]">
                                <option value="">--<?=GetMessage( "ACRIT_EXPORTPRO_NE_VYBRANO" )?>--</option>
                                    <?if( ( $resolve = CExportproProfile::getFieldsetResolve( $arProfile["IBLOCK_ID"], $options, $field["VALUE"] ) ) !== false ){ 
                                        $opt = $profileUtils->selectFieldset2($resolve, $field["RESOLVE"]);
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
                                        <select name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_TRUE_TYPE]" onchange="ShowConvalueBlockComplex( this )" data-id="<?=$id?>">
                                            <?foreach( $fieldTypeComplex as $typeComplexId => $typeNameComplex ){?>
                                                <?$selectedComplex = $typeComplexId == $field["COMPLEX_TRUE_TYPE"] ? 'selected="selected"' : "";?>
                                                <option value="<?=$typeComplexId?>" <?=$selectedComplex?>><?=$typeNameComplex?></option>
                                            <?}?>
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
                                        <select name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_FALSE_TYPE]" onchange="ShowConvalueBlockComplexFalse( this )" data-id="<?=$id?>">
                                            <?foreach( $fieldTypeComplex as $typeComplexId => $typeNameComplex ){?>
                                                <?$selectedComplex = $typeComplexId == $field["COMPLEX_FALSE_TYPE"] ? 'selected="selected"' : "";?>
                                                <option value="<?=$typeComplexId?>" <?=$selectedComplex?>><?=$typeNameComplex?></option>
                                            <?}?>
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
                            <div class="composite-block-container <?=$hideCompositeBlock?>" style="width: 100%;">
                                <div class="composite-block" style="margin: 10px 0px 0px 0px; width: 100%;">
                                    <?$hideCompositeFalse = !$useCondition ? "hide" : "";?>
                                    <?$showPlaceholder = !$hideCompositeFalse ? "placeholder" : "data-placeholder";?>
                                
                                    <div style="width: 100%;">
                                        <div class="composite-divider" style="width: 100%;">
                                            <span id="hint_EXPORTPRO_FIELDSET_COMPOSITE_DIVIDER"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_COMPOSITE_DIVIDER' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_COMPOSITE_DIVIDER_HELP" )?>' );</script>
                                            <label for="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_TRUE_DIVIDER]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_COMPOSITE_DIVIDER" )?></label><br/>
                                            <input type="text" name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_TRUE_DIVIDER]" value="<?=$compositeTrueDivider;?>" style="width: 420px;" />
                                        </div>
                                        <div class="composite-data-area-true" style="margin: 10px 0px 0px 0px; width: 100%;">
                                            <?if( is_array( $arProfile["XMLDATA"][$id]["COMPOSITE_TRUE"] ) && !empty( $arProfile["XMLDATA"][$id]["COMPOSITE_TRUE"] ) ){
                                                foreach( $arProfile["XMLDATA"][$id]["COMPOSITE_TRUE"] as $compositeTrueId => $arCompositeTrue ){?>
                                                    <div class="composite-data-item" data-id="<?=$compositeTrueId?>" style="margin: 10px 0px 0px 0px;">
                                                        <select name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_TRUE][<?=$compositeTrueId?>][COMPOSITE_TRUE_TYPE]" onchange="ShowConvalueBlockComposite( this )" data-id="<?=$id?>" style="width: 430px;">
                                                            <?foreach( $fieldTypeComposite as $typeCompositeId => $typeNameComposite ){?>
                                                                <?$selectedComposite = ( ( $typeCompositeId == $arCompositeTrue["COMPOSITE_TRUE_TYPE"] ) ? 'selected="selected"' : "" );
                                                                $hideCompositeConstTrueBlock = ( ( $typeCompositeId == $arCompositeTrue["COMPOSITE_TRUE_TYPE"] ) && ( $typeCompositeId == "const" ) ) ? "" : "hide";
                                                                $hideCompositeFieldTrueBlock = ( ( $typeCompositeId != "field" ) && !$hideCompositeConstTrueBlock ) || $typeCompositeId == "none" || !$typeCompositeId ? "hide" : "";?>
                                                                <option value="<?=$typeCompositeId?>" <?=$selectedComposite?>><?=$typeNameComposite?></option>
                                                            <?}?>
                                                        </select><br/>
                                                        <select class="field-block-composite <?=$hideCompositeFieldTrueBlock?>" name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_TRUE][<?=$compositeTrueId?>][COMPOSITE_TRUE_VALUE]" style="width: 430px;">
                                                            <?$opt = $profileUtils->selectFieldset2( $options, $arCompositeTrue["COMPOSITE_TRUE_VALUE"] );
                                                            echo implode( "\n", $opt );
                                                            unset( $opt );?>                                    
                                                        </select>                                 
                                                        <div class="const-block-composite <?=$hideCompositeConstTrueBlock?>">
                                                            <textarea name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_TRUE][<?=$compositeTrueId?>][COMPOSITE_TRUE_CONTVALUE]" <?=$showPlaceholder?>="<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONDITION_TRUE" )?>" style="width: 420px;"><?=$arCompositeTrue["COMPOSITE_TRUE_CONTVALUE"]?></textarea>
                                                        </div>
                                                    </div>
                                                <?}
                                            }?>
                                        </div>
                                        <div class="composite-add-field-button truenode" data-id="<?=$idCnt?>" data-row-id="<?=$id?>" style="margin: 10px 0px 0px 0px;">
                                            <button class="adm-btn" onclick="CompositeFieldsetAdd( this ); return false;">
                                                <?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_ADD_PART_TO_COMPOSITE_FIELD" );?>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="composite-data-area-false-container <?=$hideCompositeFalse?>" style="width: 100%; margin: 20px 0px 0px 0px;">
                                        <div class="composite-divider" style="width: 100%;">
                                            <span id="hint_EXPORTPRO_FIELDSET_COMPOSITE_DIVIDER"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_COMPOSITE_DIVIDER' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_COMPOSITE_DIVIDER_HELP" )?>' );</script>
                                            <label for="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_FALSE_DIVIDER]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_COMPOSITE_DIVIDER" )?></label><br/>
                                            <input type="text" name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_FALSE_DIVIDER]" value="<?=$compositeFalseDivider;?>" style="width: 420px;" />
                                        </div>
                                        <div class="composite-data-area-false" style="margin: 10px 0px 0px 0px; width: 100%;">
                                            <?if( is_array( $arProfile["XMLDATA"][$id]["COMPOSITE_FALSE"] ) && !empty( $arProfile["XMLDATA"][$id]["COMPOSITE_FALSE"] ) ){
                                                foreach( $arProfile["XMLDATA"][$id]["COMPOSITE_FALSE"] as $compositeFalseId => $arCompositeFalse ){?>
                                                    <div class="composite-data-item" data-id="<?=$compositeFalseId?>" style="margin: 10px 0px 0px 0px;">
                                                        <select name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_FALSE][<?=$compositeFalseId?>][COMPOSITE_FALSE_TYPE]" onchange="ShowConvalueBlockComposite( this )" data-id="<?=$id?>" style="width: 430px;">
                                                            <?foreach( $fieldTypeComposite as $typeCompositeId => $typeNameComposite ){?>
                                                                <?$selectedComposite = ( ( $typeCompositeId == $arCompositeFalse["COMPOSITE_FALSE_TYPE"] ) ? 'selected="selected"' : "" );
                                                                $hideCompositeConstFalseBlock = ( ( $typeCompositeId == $arCompositeFalse["COMPOSITE_FALSE_TYPE"] ) && ( $typeCompositeId == "const" ) ) ? "" : "hide";
                                                                $hideCompositeFieldFalseBlock = ( ( $typeCompositeId != "field" ) && !$hideCompositeConstFalseBlock ) || $typeCompositeId == "none" || !$typeCompositeId ? "hide" : "";?>
                                                                <option value="<?=$typeCompositeId?>" <?=$selectedComposite?>><?=$typeNameComposite?></option>
                                                            <?}?>
                                                        </select><br/>
                                                        <select class="field-block-composite <?=$hideCompositeFieldFalseBlock?>" name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_FALSE][<?=$compositeFalseId?>][COMPOSITE_FALSE_VALUE]" style="width: 430px;">
                                                            <?$opt = $profileUtils->selectFieldset2( $options, $arCompositeFalse["COMPOSITE_FALSE_VALUE"] );
                                                            echo implode( "\n", $opt );
                                                            unset( $opt );?>                                    
                                                        </select>                                 
                                                        <div class="const-block-composite <?=$hideCompositeConstFalseBlock?>">
                                                            <textarea name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_FALSE][<?=$compositeFalseId?>][COMPOSITE_FALSE_CONTVALUE]" <?=$showPlaceholder?>="<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONDITION_FALSE" )?>" style="width: 420px;"><?=$arCompositeFalse["COMPOSITE_FALSE_CONTVALUE"]?></textarea>
                                                        </div>
                                                    </div>
                                                <?}
                                            }?>
                                        </div>
                                        <div class="composite-add-field-button falsenode" data-id="<?=$idCnt?>" data-row-id="<?=$id?>" style="margin: 10px 0px 0px 0px;">
                                            <button class="adm-btn" onclick="CompositeFieldsetAdd( this ); return false;">
                                                <?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_ADD_PART_TO_COMPOSITE_FIELD" );?>
                                            </button>
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
                                
                                <div style="height: 5px;">&nbsp;</div>
                                
                                <span id="hint_EXPORTPRO_FIELDSET_ROUND_PRECISION"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_ROUND_PRECISION' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_ROUND_PRECISION_HELP" )?>' );</script>
                                <label for="PROFILE[XMLDATA][<?=$id?>][ROUND][PRECISION]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_ROUND_PRECISION" )?></label><br/>
                                <input type="text" maxlength="5" size="5" name="PROFILE[XMLDATA][<?=$id?>][ROUND][PRECISION]" value="<?=$roundPrecision;?>" />
                                
                                <select name="PROFILE[XMLDATA][<?=$id?>][ROUND][MODE]">
                                    <?foreach( $roundModes as $typeId => $typeName ){?>
                                        <?$selected = $typeId == $roundMode ? 'selected="selected"' : "";?>
                                        <option value="<?=$typeId?>" <?=$selected?>><?=$typeName?></option>
                                    <?}?>
                                </select>
                                
                                <div style="height: 5px;">&nbsp;</div>

                                <div class="minimum_offer_price" data-dependent="price" <?if( $selectedPropertyType == "price" ):?><?else:?>style="display: none"<?endif;?>>
                                    <div style="height: 5px;">&nbsp;</div>
                                    <span id="hint_EXPORTPRO_FIELDSET_MINIMUM_OFFER_PRICE"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_MINIMUM_OFFER_PRICE' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_MINIMUM_OFFER_PRICE_HELP" )?>' );</script>
                                    <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][MINIMUM_OFFER_PRICE]" <?=$calculateMinimum?> value="Y" onchange="ShowMinimumOfferPriceCode( this )">
                                    <label for="PROFILE[XMLDATA][<?=$id?>][MINIMUM_OFFER_PRICE]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_MINIMUM_OFFER_PRICE" )?></label>
                                
                                    <div class="minimum_offer_price_code" <?if( strlen( $calculateMinimum ) ):?><?else:?>style="display: none"<?endif;?>>
                                        <div style="height: 5px;">&nbsp;</div>
                                        <span id="hint_EXPORTPRO_FIELDSET_MINIMUM_OFFER_PRICE_CODE"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_MINIMUM_OFFER_PRICE_CODE' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_MINIMUM_OFFER_PRICE_CODE_HELP" )?>' );</script>
                                        <label for="PROFILE[XMLDATA][<?=$id?>][MINIMUM_OFFER_PRICE_CODE]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_MINIMUM_OFFER_PRICE_CODE" )?></label>
                                        <div>
                                        <input type="text" maxlength="20" size="20" name="PROFILE[XMLDATA][<?=$id?>][MINIMUM_OFFER_PRICE_CODE]" value="<?=$field["MINIMUM_OFFER_PRICE_CODE"]?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="height: 5px;">&nbsp;</div>
                                
                                <span id="hint_EXPORTPRO_FIELDSET_CONVERT_DATA"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_EXPORTPRO_FIELDSET_CONVERT_DATA' ), '<?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONVERT_DATA_HELP" )?>' );</script>
                                <label for="PROFILE[XMLDATA][<?=$id?>][CONVERT_DATA]"><?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONVERT_DATA" )?></label><br/>
                                <table id="fieldset-convert-fieldset-container<?=$id?>" cellpadding="0" cellspacing="0" width="100%">
                                    <?if( is_array( $field["CONVERT_DATA"] ) ){
                                        foreach( $field["CONVERT_DATA"] as $convertDataId => $fields ){?>
                                            <tr class="convert-fieldset-item" data-id="<?=$convertDataId?>">
                                                <td align="right">
                                                    <input type="text" name="PROFILE[XMLDATA][<?=$id?>][CONVERT_DATA][<?=$convertDataId?>][0]" value="<?=$fields[0]?>" />
                                                </td>
                                                <td align="left" style="position: relative" class="adm-detail-content-cell-r">
                                                    <input type="text" name="PROFILE[XMLDATA][<?=$id?>][CONVERT_DATA][<?=$convertDataId?>][1]" value="<?=$fields[1]?>" />
                                                    <span class="fieldset-item-delete">&times</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <hr style="opacity: 0.2;">
                                                </td>
                                            </tr>
                                        <?}
                                    }?>
                                </table>
                                
                                <div data-row-id="<?=$id?>" style="margin: 0 auto; padding: 10px; text-align: center;">
                                    <button class="adm-btn" onclick="FieldsetConvertFieldsetAdd( this ); return false;">
                                        <?=GetMessage( "ACRIT_EXPORTPRO_FIELDSET_CONVERT_CONDITION_ADD" )?>
                                    </button>
                                </div>
                            </div>
                            <div id="PROFILE_XMLDATA_<?=$id?>_CONDITION" class="condition-block <?=$hideCondition?>">
                                <?if( $field["USE_CONDITION"] == "Y" && CModule::IncludeModule( "catalog" ) ){
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
                                }?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr style="opacity: 0.2;">
                        </td>
                    </tr>
                <?}?>
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