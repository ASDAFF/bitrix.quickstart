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
$schemeQuantityReserved = $profileUtils->GetSchemeQuantityReserved();
$schemeWeight = $profileUtils->GetSchemeWeight();
$schemeWidth = $profileUtils->GetSchemeWidth();
$schemeLength = $profileUtils->GetSchemeLength();
$schemeHeight = $profileUtils->GetSchemeHeight();
$schemePurchasingPrice = $profileUtils->GetSchemePurchasingPrice();

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
$bExportFieldsIBlockInsteadCategory = $arProfile["EXPORT_PARENT_CATEGORIES_WITH_IBLOCK_FIELDS"] == "Y" ? 'checked="checked"' : "";

function AcritExportproGetDefaultSelectedStep4( $schemeValue, $arProfileValue ){
    $default = "OFFER_IF_SKU_EMPTY";
    if( empty( $arProfileValue ) ){
        if( substr_compare( $schemeValue, $default, "-".strlen( $default ) ) == 0 ){
            return $schemeValue;
        }
    }
    return $arProfileValue;
}
?>

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
<tr>
    <td width="50%">
        <span id="hint_PROFILE[EXPORT_PARENT_CATEGORIES]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[EXPORT_PARENT_CATEGORIES]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_EXPORT_PARENT_CATEGORIES_HELP" )?>' );</script>
        <label for="PROFILE[EXPORT_PARENT_CATEGORIES]"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_EXPORT_PARENT_CATEGORIES" );?></label>
    </td>
    <td><input type="checkbox" name="PROFILE[EXPORT_PARENT_CATEGORIES]" value="Y" <?=$bExportParentCategories?> ></td>
</tr>
<tr>
    <td width="50%">
        <span id="hint_PROFILE[EXPORT_PARENT_CATEGORIES_TO_OFFER]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[EXPORT_PARENT_CATEGORIES_TO_OFFER]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_EXPORT_PARENT_CATEGORIES_TO_OFFER_HELP" )?>' );</script>
        <label for="PROFILE[EXPORT_PARENT_CATEGORIES_TO_OFFER]"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_EXPORT_PARENT_CATEGORIES_TO_OFFER" );?></label>
    </td>
    <td><input type="checkbox" name="PROFILE[EXPORT_PARENT_CATEGORIES_TO_OFFER]" value="Y" <?=$bExportParentCategoriesToOffer?> ></td>
</tr>
<tr>
    <td width="50%">
        <span id="hint_PROFILE[EXPORT_OFFER_CATEGORIES_TO_OFFER]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[EXPORT_OFFER_CATEGORIES_TO_OFFER]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_EXPORT_OFFER_CATEGORIES_TO_OFFER_HELP" )?>' );</script>
        <label for="PROFILE[EXPORT_OFFER_CATEGORIES_TO_OFFER]"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_EXPORT_OFFER_CATEGORIES_TO_OFFER" );?></label>
    </td>
    <td><input type="checkbox" name="PROFILE[EXPORT_OFFER_CATEGORIES_TO_OFFER]" value="Y" <?=$bExportOfferCategoriesToOffer?> ></td>
</tr>
<tr>
    <td width="50%">
        <span id="hint_PROFILE[EXPORT_PARENT_CATEGORIES_WITH_IBLOCK_FIELDS]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[EXPORT_PARENT_CATEGORIES_WITH_IBLOCK_FIELDS]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_EXPORT_PARENT_CATEGORIES_WITH_IBLOCK_FIELDS_HELP" )?>' );</script>
        <label for="PROFILE[EXPORT_PARENT_CATEGORIES_WITH_IBLOCK_FIELDS]"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_EXPORT_PARENT_CATEGORIES_WITH_IBLOCK_FIELDS" );?></label>
    </td>
    <td><input type="checkbox" name="PROFILE[EXPORT_PARENT_CATEGORIES_WITH_IBLOCK_FIELDS]" value="Y" <?=$bExportFieldsIBlockInsteadCategory?> ></td>
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
                <?$selected = ( $schemeCode == $profileUtils->GetDefaultSelected( $schemeCode, $arProfile["NAMESCHEMA"]["PREVIEW_TEXT"]) || ( $schemeCode == $arProfile["NAMESCHEMA"]["PREVIEW_TEXT"] ) ) ? 'selected="selected"' : "";?>
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
                <?$selected = ( $schemeCode == $profileUtils->GetDefaultSelected( $schemeCode, $arProfile["NAMESCHEMA"]["DETAIL_TEXT"]) || ( $schemeCode == $arProfile["NAMESCHEMA"]["DETAIL_TEXT"] ) ) ? 'selected="selected"' : "";?>
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
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <label for="PROFILE[NAMESCHEMA][CATALOG_QUANTITY_RESERVED]"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_CATALOG_QUANTITY_RESERVED" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <select name="PROFILE[NAMESCHEMA][CATALOG_QUANTITY_RESERVED]">
            <?foreach( $schemeQuantityReserved as $schemeCode => $schemeTitle ):?>
                <?$selected = ( $schemeCode == $profileUtils->GetDefaultSelected($schemeCode, $arProfile["NAMESCHEMA"]["CATALOG_QUANTITY_RESERVED"]) || $schemeCode == $arProfile["NAMESCHEMA"]["CATALOG_QUANTITY_RESERVED"] ) ? 'selected="selected"' : "";?>
                <option value="<?=$schemeCode?>" <?=$selected?>><?=$schemeTitle?></option>
            <?endforeach?>
        </select>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <label for="PROFILE[NAMESCHEMA][CATALOG_WEIGHT]"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_CATALOG_WEIGHT" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <select name="PROFILE[NAMESCHEMA][CATALOG_WEIGHT]">
            <?foreach( $schemeWeight as $schemeCode => $schemeTitle ):?>
                <?$selected = ( $schemeCode == $profileUtils->GetDefaultSelected($schemeCode, $arProfile["NAMESCHEMA"]["CATALOG_WEIGHT"]) || $schemeCode == $arProfile["NAMESCHEMA"]["CATALOG_WEIGHT"] ) ? 'selected="selected"' : "";?>
                <option value="<?=$schemeCode?>" <?=$selected?>><?=$schemeTitle?></option>
            <?endforeach?>
        </select>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <label for="PROFILE[NAMESCHEMA][CATALOG_WIDTH]"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_CATALOG_WIDTH" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <select name="PROFILE[NAMESCHEMA][CATALOG_WIDTH]">
            <?foreach( $schemeWidth as $schemeCode => $schemeTitle ):?>
                <?$selected = ( $schemeCode == $profileUtils->GetDefaultSelected($schemeCode, $arProfile["NAMESCHEMA"]["CATALOG_WIDTH"]) || $schemeCode == $arProfile["NAMESCHEMA"]["CATALOG_WIDTH"] ) ? 'selected="selected"' : "";?>
                <option value="<?=$schemeCode?>" <?=$selected?>><?=$schemeTitle?></option>
            <?endforeach?>
        </select>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <label for="PROFILE[NAMESCHEMA][CATALOG_LENGTH]"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_CATALOG_LENGTH" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <select name="PROFILE[NAMESCHEMA][CATALOG_LENGTH]">
            <?foreach( $schemeLength as $schemeCode => $schemeTitle ):?>
                <?$selected = ( $schemeCode == $profileUtils->GetDefaultSelected($schemeCode, $arProfile["NAMESCHEMA"]["CATALOG_LENGTH"]) || $schemeCode == $arProfile["NAMESCHEMA"]["CATALOG_LENGTH"] ) ? 'selected="selected"' : "";?>
                <option value="<?=$schemeCode?>" <?=$selected?>><?=$schemeTitle?></option>
            <?endforeach?>
        </select>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <label for="PROFILE[NAMESCHEMA][CATALOG_HEIGHT]"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_CATALOG_HEIGHT" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <select name="PROFILE[NAMESCHEMA][CATALOG_HEIGHT]">
            <?foreach( $schemeHeight as $schemeCode => $schemeTitle ):?>
                <?$selected = ( $schemeCode == $profileUtils->GetDefaultSelected($schemeCode, $arProfile["NAMESCHEMA"]["CATALOG_HEIGHT"]) || $schemeCode == $arProfile["NAMESCHEMA"]["CATALOG_HEIGHT"] ) ? 'selected="selected"' : "";?>
                <option value="<?=$schemeCode?>" <?=$selected?>><?=$schemeTitle?></option>
            <?endforeach?>
        </select>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <label for="PROFILE[NAMESCHEMA][CATALOG_PURCHASING_PRICE]"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_NAMESCHEMA_CATALOG_PURCHASING_PRICE" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <select name="PROFILE[NAMESCHEMA][CATALOG_PURCHASING_PRICE]">
            <?foreach( $schemePurchasingPrice as $schemeCode => $schemeTitle ):?>
                <?$selected = ( $schemeCode == $profileUtils->GetDefaultSelected($schemeCode, $arProfile["NAMESCHEMA"]["CATALOG_PURCHASING_PRICE"]) || $schemeCode == $arProfile["NAMESCHEMA"]["CATALOG_PURCHASING_PRICE"] ) ? 'selected="selected"' : "";?>
                <option value="<?=$schemeCode?>" <?=$selected?>><?=$schemeTitle?></option>
            <?endforeach?>
        </select>
    </td>
</tr>
<tr class="heading">
	<td colspan="2"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_TAGS" )?></td>
</tr>
<tr>
	<td align="center" colspan="2">
		<div class="adm-info-message"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_FULLDESC" )?></div>
	</td>
</tr>

<tr class="heading">
	<td colspan="2"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_ALL" )?></td>
</tr>
<tr align="center">
	<td colspan="2">
		<?=BeginNote();?>
		<?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_MAIN_DESCRIPTION" )?>
		<br>
		<div id="scheme_main_add_descr">
			<?=$types[$arProfile["TYPE"]]["SCHEME_DESCRIPTION"]?>
		</div>
		<?=EndNote();?>
	</td>
</tr>
<tr align="center">
	<td colspan="2" id="scheme_format">
		<textarea name="PROFILE[FORMAT]" rows="5" cols="150" id="scheme_format_main"><?=$arProfile["FORMAT"]?></textarea>
	</td>
</tr>


<tr class="heading">
	<td colspan="2"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_OFFER" )?></td>
</tr>
<tr align="center">
	<td colspan="2">
		<?=BeginNote();?>
		<?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_TAGS_DESCRIPTION" );?>
		<div id="scheme_main_add_offer_descr">
			<?=$types[$arProfile["TYPE"]]["SCHEME_OFFER_DESCRIPTION"]?>
		</div>
		<?=EndNote();?>
	</td>
</tr>

<tr>
	<td align="center" colspan="2" id="scheme_offer">
		<textarea name="PROFILE[OFFER_TEMPLATE]" rows="5" cols="150" id="scheme_offer_template"><?=htmlspecialcharsbx( $arProfile["OFFER_TEMPLATE"] )?></textarea>
	</td>
</tr>

<tr class="heading">
	<td colspan="2"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_CATEGORY" )?></td>
</tr>
<tr align="center">
	<td colspan="2">
		<?=BeginNote();?>
		<?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_CATEGORY_DESCRIPTION" );?>
		<?=EndNote();?>
	</td>
</tr>
<tr>
	<td align="center" colspan="2" id="scheme_category">
		<textarea name="PROFILE[CATEGORY_TEMPLATE]" rows="5" cols="150" id="scheme_category_template"><?=htmlspecialcharsbx( $arProfile["CATEGORY_TEMPLATE"] )?></textarea>
	</td>
</tr>

<tr class="heading">
	<td colspan="2"><?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_CURRENCY" )?></td>
</tr>
<tr align="center">
	<td colspan="2">
		<?=BeginNote();?>
		<?=GetMessage( "ACRIT_EXPORTPRO_SCHEME_CURRENCY_DESCRIPTION" );?>
		<?=EndNote();?>
	</td>
</tr>
<tr>
	<td align="center" colspan="2" id="scheme_currency">
		<textarea name="PROFILE[CURRENCY_TEMPLATE]" rows="5" cols="150" id="scheme_currency_template"><?=htmlspecialcharsbx($arProfile["CURRENCY_TEMPLATE"])?></textarea>
	</td>
</tr>
