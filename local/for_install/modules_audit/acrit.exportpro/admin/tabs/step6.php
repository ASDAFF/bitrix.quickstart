<?php

IncludeModuleLangFile( __FILE__ );

$marketCategory = new CExportproMarketDB();
$marketCategory = $marketCategory->GetList();
if( !is_array( $marketCategory ) )
    $marketCategory = array();
    
$marketTiuCategory = new CExportproMarketTiuDB();
$marketTiuCategory = $marketTiuCategory->GetList();
if( !is_array( $marketTiuCategory ) )
    $marketTiuCategory = array();
    
$categoriesNew = array();
foreach( $categories as $depth ){
    $categoriesNew = array_merge( $categoriesNew, $depth );
}

$categories = $categoriesNew;

unset( $categoriesNew );
asort( $categories );

$use_market_category = $arProfile["USE_MARKET_CATEGORY"] == "Y" ? 'checked="checked"' : "";

$arTiuBlock = array(
    "id" => 3,
    "name" => "Tiu.ru",
    "data" => $marketTiuCategory
);

$arMarketCategoryList = array_slice( $marketCategory, 0, 2 );
$arMarketCategoryList[] = $arTiuBlock;
$arSpecialFormats = array_slice( $marketCategory, 2 );
foreach( $arSpecialFormats as $arSpecialFormatsItem ){
    $arSpecialFormatsItem["id"]++;
    $arMarketCategoryList[] = $arSpecialFormatsItem;
}
?>
<tr>
    <td width="40%" class="adm-detaell-l">
        <span id="hint_PROFILE[USE_MARKET_CATEGORY]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[USE_MARKET_CATEGORY]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_STEP1_USE_MARKETCATEGORY_HELP" )?>' );</script>
        <label for="PROFILE[USE_MARKET_CATEGORY]"><?=GetMessage( "ACRIT_EXPORTPRO_STEP1_USE_MARKETCATEGORY" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="checkbox" name="PROFILE[USE_MARKET_CATEGORY]" value="Y" <?=$use_market_category?> >
        <i><?=GetMessage( "ACRIT_EXPORTPRO_STEP1_USE_MARKETCATEGORY_DESC" )?></i>
    </td>
</tr>
<tr>
    <td id="market_category_select">
        <select name="PROFILE[MARKET_CATEGORY][CATEGORY]" onchange="ChangeMarketCategory( this.value )">
            <?foreach( $arMarketCategoryList as $cat ){?>
                <?$selected = ( $cat["id"] == $arProfile["MARKET_CATEGORY"]["CATEGORY"] ) ? 'selected="selected"' : ""?>
                <option value="<?=$cat["id"]?>" <?=$selected?>><?=$cat["name"]?></option>
            <?}?>
        </select>
    </td>
    <td>
        <a class="adm-btn" onclick="ShowMarketForm( 'edit' )"><?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CATEGORY_EDIT" )?></a>
        <a class="adm-btn adm-btn-save" onclick="ShowMarketForm( 'add' )"><?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CATEGORY_ADD" )?></a>
    </td>
</tr>
<tr>
    <td colspan="2">
        <div id="category_add">
            <input type="hidden" name="PROFILE[MARKET_CATEGORY_ID]" />
            <table>
                <tr>
                    <td><input type="text" name="PROFILE[MARKET_CATEGORY_NAME]" placeholder="<?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CATEGORY_NAME" )?>"/></td>
                </tr>
                <tr>
                    <td><textarea name="PROFILE[MARKET_CATEGORY_DATA]" placeholder="<?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CATEGORY_DATA" )?>" size="20"></textarea></td>
                </tr>
                <tr>
                    <td>
                        <a class="adm-btn save adm-btn-save" onclick="SaveMarketForm()"><?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CATEGORY_SAVE" )?></a>
                        <a class="adm-btn back" onclick="HideMarketForm()"><?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CATEGORY_BACK" )?></a>
                    </td>
                </tr>
            </table>
            <br><br><br>
        </div>
    </td>
</tr>

<?
    if( !$arProfile["MARKET_CATEGORY"]["CATEGORY"] || !$arMarketCategoryList[$arProfile["MARKET_CATEGORY"]["CATEGORY"] - 1]["data"] ){
        foreach( $arMarketCategoryList as $cat ){
            $arProfile["MARKET_CATEGORY"]["CATEGORY"] = $cat["id"];
            break;
        }
    }
    
    if( $arProfile["MARKET_CATEGORY"]["CATEGORY"] != 3 )
        $arMarketCategoryList = explode( PHP_EOL, $arMarketCategoryList[$arProfile["MARKET_CATEGORY"]["CATEGORY"] - 1]["data"] );
    else
        $arMarketCategoryList = $arMarketCategoryList[$arProfile["MARKET_CATEGORY"]["CATEGORY"] - 1]["data"];
    
    $validCategories = array();
    foreach( $arMarketCategoryList as $market ){
        if( $arProfile["MARKET_CATEGORY"]["CATEGORY"] == 3 ){
            $market = $market["NAME"];
        }
        
        if( is_array( $arProfile["MARKET_CATEGORY"]["CATEGORY_LIST"] ) ){
            foreach( $arProfile["MARKET_CATEGORY"]["CATEGORY_LIST"] as $catId => $catValue ){
                if(  trim( $catValue ) == trim( $market ) )
                    $validCategories[] = $catId;
            }
        }
    }    
?>
<tr align="center">
    <td colspan="2">
        <?=BeginNote();?>
        <?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CATEGORY_DESCRIPTION" );?>
        <?=EndNote();?>
    </td>
</tr>
<tr>
    <td colspan="2">
        <table width="100%" id="market_category_data">
            <?foreach( $categories as $cat ){
                if( $arProfile["CHECK_INCLUDE"] == "Y" ){
                    if( !in_array( $cat["ID"], $arProfile["CATEGORY"] ) )
                        continue;
                }
                else{
                    if( !in_array( $cat["PARENT_1"], $arProfile["CATEGORY"] ) )
                        continue;
                }?>
                <tr>
                    <td width="40%">
                        <label form="PROFILE[MARKET_CATEGORY][CATEGORY_LIST][<?=$cat["ID"]?>]"><?=$cat["NAME"]?></label>
                    </td>
                    <td>
                        <?$catVal = "";
                        if( in_array( $cat["ID"], $validCategories ) )
                            $catVal = $arProfile["MARKET_CATEGORY"]["CATEGORY_LIST"][$cat["ID"]];?>
                        <input type="text" value="<?=$catVal?>" name="PROFILE[MARKET_CATEGORY][CATEGORY_LIST][<?=$cat["ID"]?>]" />
                        <span class="field-edit" onclick="ShowMarketCategoryList(<?=$cat["ID"]?>)" style="cursor: pointer"></span>
                    </td>
                </tr>
            <?}?>
        </table> 
        <div id="market_category_list" style="display: none">
            <input onkeyup="FilterMarketCategoryList( this, 'market_category_list' )">
            <select onchange="SetMarketCategory( this.value )" size="25">
                <option></option>
                <?foreach( $arMarketCategoryList as $marketCat ):?>
                    <option data-search="<?=( ( $arProfile["MARKET_CATEGORY"]["CATEGORY"] == 3 ) ? strtolower( $marketCat["NAME"] ) : strtolower( $marketCat ) );?>"><?=( ( $arProfile["MARKET_CATEGORY"]["CATEGORY"] == 3 ) ? $marketCat["NAME"] : $marketCat )?></option>
                <?endforeach?>
            </select>
        </div>
    </td>
</tr>