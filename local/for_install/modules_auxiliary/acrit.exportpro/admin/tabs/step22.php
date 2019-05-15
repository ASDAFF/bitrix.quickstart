<?php

IncludeModuleLangFile( __FILE__ );
$time[] = GetMessage( "ACRIT_EXPORTPRO_NOSELECT" );

//24hours format
$time["Y-m-d_H:i"] = date( "Y-m-d H:i", time() );
$time["Y-m-d_h:i"] = date( "Y-m-d h:i", time() );
$time["Y-m-d_h:i A"] = date( "Y-m-d h:i A", time() );
$time["d/m/Y"] = date( "d/m/Y", time() );
$time["Y/m/d"] = date( "Y/m/d", time() );
$time["d.m.Y"] = date( "d.m.Y", time() );
$time["Y-m-d_h:i:s"] = date( "Y-m-d h:i:s", time() );
$time["YmdThis"] = date( "YmdThis", time() );
$time["Y/m/d_h:i:s"] = date( "Y/m/d h:i:s", time() );
$time["d/m/Y_h:i:s"] = date( "d/m/Y h:i:s", time() );
$time["d.m.Y_h:i:s"] = date( "d.m.Y h:i:s", time() );
$time["c"] = date( "c", time() );
$time["Y-m-d_h:i:s_".GetMessage( "ACRIT_EXPORTPRO_" )] = date( "Y-m-d h:m:s ".GetMessage( "ACRIT_EXPORTPRO_" ), time() );
$time["Y-m-d_h:i:s_".GetMessage( "ACRIT_EXPORTPRO_1" )] = date( "Y-m-d h:i:s ".GetMessage( "ACRIT_EXPORTPRO_1" ), time() );
$time["Y-m-d_h:i:s_".GetMessage( "ACRIT_EXPORTPRO_2" )] = date( "Y-m-d h:i:s ".GetMessage( "ACRIT_EXPORTPRO_2" ), time() );
$time["Y/m/d_h:i:s_".GetMessage( "ACRIT_EXPORTPRO_" )] = date( "Y/m/d h:i:s ".GetMessage( "ACRIT_EXPORTPRO_" ), time() );
$time["Y/m/d_h:i:s_".GetMessage( "ACRIT_EXPORTPRO_1" )] = date( "Y/m/d h:i:s ".GetMessage( "ACRIT_EXPORTPRO_1" ), time() );
$time["Y/m/d_h:i:s_".GetMessage( "ACRIT_EXPORTPRO_2" )] = date( "Y/m/d h:i:s ".GetMessage( "ACRIT_EXPORTPRO_2" ), time() );
$time["D/m/Y_h:i:s_".GetMessage( "ACRIT_EXPORTPRO_" )] = date( "D/m/Y h:i:s ".GetMessage( "ACRIT_EXPORTPRO_" ), time() );
$time["D/m/Y_h:i:s_".GetMessage( "ACRIT_EXPORTPRO_1" )] = date( "D/m/Y h:i:s ".GetMessage( "ACRIT_EXPORTPRO_1" ), time() );
$time["D/m/Y_h:i:s_".GetMessage( "ACRIT_EXPORTPRO_2" )] = date( "D/m/Y h:i:s ".GetMessage( "ACRIT_EXPORTPRO_2" ), time() );
$time["d.m.Y_h:i:s".GetMessage( "ACRIT_EXPORTPRO_" )] = date( "d.m.Y h:i:s".GetMessage( "ACRIT_EXPORTPRO_" ), time() );
$time["d.m.Y_h:i:s".GetMessage( "ACRIT_EXPORTPRO_1" )] = date( "d.m.Y h:i:s".GetMessage( "ACRIT_EXPORTPRO_1" ), time() );
$time["d.m.Y_h:i:s".GetMessage( "ACRIT_EXPORTPRO_2" )] = date( "d.m.Y h:i:s".GetMessage( "ACRIT_EXPORTPRO_2" ), time() );
$time["Ymd"] = date( "Ymd", time() );
$time["Y-m-d"] = date( "Y-m-d", time() );
$time["YmdThis".GetMessage( "ACRIT_EXPORTPRO_" )] = date( "YmdThis".GetMessage( "ACRIT_EXPORTPRO_" ), time() );
$time["YmdThis".GetMessage( "ACRIT_EXPORTPRO_1" )] = date( "YmdThis".GetMessage( "ACRIT_EXPORTPRO_1" ), time() );
$time["Y-m-dTh:i:s".GetMessage( "ACRIT_EXPORTPRO_" )] = date( "Y-m-dTh:i:s".GetMessage( "ACRIT_EXPORTPRO_" ), time() );
$time["Y-m-dTh:i:s".GetMessage( "ACRIT_EXPORTPRO_2" )] = date( "Y-m-dTh:i:s".GetMessage( "ACRIT_EXPORTPRO_2" ), time() );
$time["YmdThis"] = date( "YmdThis", time() );
$time["Y-m-dTh:i:s"] = date( "Y-m-dTh:i:s", time() );
$time["YmdThi".GetMessage( "ACRIT_EXPORTPRO_" )] = date( "YmdThi".GetMessage( "ACRIT_EXPORTPRO_" ), time() );
$time["YmdThi".GetMessage( "ACRIT_EXPORTPRO_1" )] = date( "YmdThi".GetMessage( "ACRIT_EXPORTPRO_1" ), time() );
$time["Y-m-dTh:i".GetMessage( "ACRIT_EXPORTPRO_" )] = date( "Y-m-dTh:i".GetMessage( "ACRIT_EXPORTPRO_" ), time() );
$time["Y-m-dTh:i".GetMessage( "ACRIT_EXPORTPRO_2" )] = date( "Y-m-dTh:i".GetMessage( "ACRIT_EXPORTPRO_2" ), time() );
$time["YmdThi"] = date( "YmdThi", time() );
$time["Y-m-dTh:i"] = date( "Y-m-dTh:i", time() );

$schemeName = $profileUtils->GetSchemeName();
$schemePreviewText = $profileUtils->GetSchemePreviewText();
$schemeDetailText = $profileUtils->GetSchemeDetailText();
$schemeDetailPicture = $profileUtils->GetSchemeDetailPicture();
$schemeQuantity = $profileUtils->GetSchemeQuantity();

$arExportFileProtocol = array(
    "http",
    "https"
);

$exportFileProtocol = ( ( $arProfile["SITE_PROTOCOL"] == "http" ) || ( $arProfile["SITE_PROTOCOL"] == "https" ) ) ? 
                        $arProfile["SITE_PROTOCOL"] : 
                        ( ( CMain::IsHTTPS() ) ? "https" : "http" );
                        

$bExportParentCategories = $arProfile["EXPORT_PARENT_CATEGORIES"] == "Y" ? 'checked="checked"' : "";
$bExportParentCategoriesToOffer = $arProfile["EXPORT_PARENT_CATEGORIES_TO_OFFER"] == "Y" ? 'checked="checked"' : "";
$bExportOfferCategoriesToOffer = $arProfile["EXPORT_OFFER_CATEGORIES_TO_OFFER"] == "Y" ? 'checked="checked"' : "";
$bExportFieldsIBlockInsteadCategory = $arProfile["EXPORT_IBLOCK_FIELDS_INSTEAD_CATEGORY_FIELDS"] == "Y" ? 'checked="checked"' : "";

function AcritExportproGetDefaultSelectedStep22( $schemeValue, $arProfileValue ){
    $default = "OFFER_IF_SKU_EMPTY";
    if( empty( $arProfileValue ) ){
        if( substr_compare( $schemeValue, $default, "-".strlen( $default ) ) == 0 ){
            return $schemeValue;
        }
    }
    return $arProfileValue;
}

$types = $profileUtils->GetTypes();
$arType = $types[$arProfile["TYPE"]];

if( empty( $arProfile["NAMESCHEMA"]["CATALOG_QUANTITY"] ) && !empty($arType["NAMESCHEMA"]["CATALOG_QUANTITY"] ) ){
    $arProfile["NAMESCHEMA"]["CATALOG_QUANTITY"] = $arType["NAMESCHEMA"]["CATALOG_QUANTITY"];
}?>

<tr class="heading" align="center">
	<td colspan="2"><b><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL" );?></b></td>
</tr>
<tr align="center">
	<td colspan="2">
		<?=BeginNote();?>
		<?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_DETAIL_DESCRIPTION" );?>
		<?=EndNote();?>
	</td>
</tr>
<tr>
    <td width="50%" class="adm-detail-content-cell-l">
        <span id="hint_PROFILE[DATEFORMAT]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[DATEFORMAT]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_DATEFORMAT_HELP" )?>' );</script>
        <label for="PROFILE[DATEFORMAT]"><b><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_DATEFORMAT" );?></b></label>
    </td>
    <td width="" class="adm-detail-content-cell-r">
        <select name="PROFILE[DATEFORMAT]">
            <?foreach( $time as $format => $formatTime ):?>
                <?$selected = ( $format == $arProfile["DATEFORMAT"] ) ? 'selected="selected"' : "";?>
                <option value="<?=$format?>" <?=$selected?>><?=$formatTime?></option>
            <?endforeach?>
        </select>
    </td>
</tr>
<tr>
    <td width="50%" class="adm-detail-content-cell-l">
        <span id="hint_PROFILE[SITE_PROTOCOL]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[SITE_PROTOCOL]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_SITE_PROTOCOL_HELP" )?>' );</script>
        <label for="PROFILE[SITE_PROTOCOL]"><b><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_SITE_PROTOCOL" )?></b></label>
    </td>
    <td width="" class="adm-detail-content-cell-r">
        <select name="PROFILE[SITE_PROTOCOL]">
            <?foreach( $arExportFileProtocol as $protocol ):?>
                <?$selected = ( $protocol == $exportFileProtocol ) ? 'selected="selected"' : "";?>
                <option value="<?=$protocol?>" <?=$selected?>><?=$protocol?></option>
            <?endforeach?>
        </select>
    </td>
</tr>


<tr class="heading">
    <td colspan="2"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_SELECT" )?></td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <label for="PROFILE[NAMESCHEMA][NAME]"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <select name="PROFILE[NAMESCHEMA][NAME]">
            <?foreach( $schemeName as $schemeCode => $schemeTitle ):?>
                <?$selected = ( $schemeCode == $arProfile["NAMESCHEMA"]["NAME"] ) ? 'selected="selected"' : "";?>
                <option value="<?=$schemeCode;?>" <?=$selected;?>><?=$schemeTitle;?></option>
            <?endforeach?>
        </select>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <label for="PROFILE[NAMESCHEMA][PREVIEW_TEXT]"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_PREVIEWTEXT" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <select name="PROFILE[NAMESCHEMA][PREVIEW_TEXT]">
            <?foreach( $schemePreviewText as $schemeCode => $schemeTitle ):?>
                <?$selected = ( $schemeCode == $profileUtils->GetDefaultSelected($schemeCode, $arProfile["NAMESCHEMA"]["PREVIEW_TEXT"]) || $schemeCode == $arProfile["NAMESCHEMA"]["PREVIEW_TEXT"] ) ? 'selected="selected"' : "";?>
                <option value="<?=$schemeCode;?>" <?=$selected;?>><?=$schemeTitle;?></option>
            <?endforeach?>
        </select>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <label for="PROFILE[NAMESCHEMA][DETAIL_TEXT]"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_DETAILTEXT" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <select name="PROFILE[NAMESCHEMA][DETAIL_TEXT]">
            <?foreach( $schemeDetailText as $schemeCode => $schemeTitle ):?>
                <?$selected = ( $schemeCode == $profileUtils->GetDefaultSelected($schemeCode, $arProfile["NAMESCHEMA"]["DETAIL_TEXT"]) || $schemeCode == $arProfile["NAMESCHEMA"]["DETAIL_TEXT"] ) ? 'selected="selected"' : "";?>
                <option value="<?=$schemeCode?>" <?=$selected?>><?=$schemeTitle?></option>
            <?endforeach?>
        </select>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <label for="PROFILE[NAMESCHEMA][DETAIL_PICTURE]"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_DETAILPICTURE" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <select name="PROFILE[NAMESCHEMA][DETAIL_PICTURE]">
            <?foreach( $schemeDetailPicture as $schemeCode => $schemeTitle ):?>
                <?$selected = ( $schemeCode == $profileUtils->GetDefaultSelected($schemeCode, $arProfile["NAMESCHEMA"]["DETAIL_PICTURE"]) || $schemeCode == $arProfile["NAMESCHEMA"]["DETAIL_PICTURE"] ) ? 'selected="selected"' : "";?>
                <option value="<?=$schemeCode?>" <?=$selected?>><?=$schemeTitle?></option>
            <?endforeach?>
        </select>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <label for="PROFILE[NAMESCHEMA][CATALOG_QUANTITY]"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_CATALOG_QUANTITY" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <select name="PROFILE[NAMESCHEMA][CATALOG_QUANTITY]">
            <?foreach( $schemeQuantity as $schemeCode => $schemeTitle ):?>
                <?$selected = ( $schemeCode == $profileUtils->GetDefaultSelected($schemeCode, $arProfile["NAMESCHEMA"]["CATALOG_QUANTITY"]) || $schemeCode == $arProfile["NAMESCHEMA"]["CATALOG_QUANTITY"] ) ? 'selected="selected"' : "";?>
                <option value="<?=$schemeCode?>" <?=$selected?>><?=$schemeTitle?></option>
            <?endforeach?>
        </select>
    </td>
</tr>
