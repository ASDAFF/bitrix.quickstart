<?php

IncludeModuleLangFile(__FILE__);

$marketCategory = new CExportproMarketEbayDB;
$marketCategory = $marketCategory->GetList();
if( !is_array( $marketCategory ) )
	$marketCategory = array();

$newMarketCategory = array();
foreach( $marketCategory as $marketCat ){
	$newMarketCategory[$marketCat["id"]] = $marketCat;
}

$marketCategory = $newMarketCategory;
unset( $newMarketCategory );

for( $i = 2; $i < 100; $i++ ){
	$levelCnt = 0;
	foreach( $marketCategory as &$marketCat ){
		if( $marketCat["level"] == $i ){
			$levelCnt++;
			if( is_array( $marketCategory[$marketCat["parent_id"]] ) )
				$marketCat["name"] = $marketCategory[$marketCat["parent_id"]]["name"]." / ".$marketCat["name"];
		}
	}
	if( $levelCnt == 0 )
		break;
}

$use_market_category = $arProfile["USE_MARKET_CATEGORY"] == "Y" ? 'checked="checked"' : "";
?>

<tr align="center">
    <td colspan="2">
        <?=BeginNote();?>
        <?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CATEGORY_EBAY_DESCRIPTION" )?>
        <?=EndNote();?>
    </td>
</tr>
<tr>
	<td colspan="2" id="market_category_data_ebay">
		<table width="100%">
			<?foreach( $categories as $cat ){?>
				<tr>
					<td>
						<label form="PROFILE[MARKET_CATEGORY][EBAY][CATEGORY_LIST][<?=$cat["ID"]?>]"><?=$cat["NAME"]?></label>
					</td>
					<td width="60%">
                        <input type="text" readonly="readonly" value="<?=$marketCategory[$arProfile["MARKET_CATEGORY"]["EBAY"]["CATEGORY_LIST"][$cat["ID"]]]["name"]?>" name="PROFILE_MARKET_CATEGORY_CATEGORY_LIST_EBAY_<?=$cat["ID"]?>_NAME" style="width:100%; opacity:1"/>
						<input type="hidden" value="<?=$arProfile["MARKET_CATEGORY"]["EBAY"]["CATEGORY_LIST"][$cat["ID"]]?>" name="PROFILE[MARKET_CATEGORY][EBAY][CATEGORY_LIST][<?=$cat["ID"]?>]" />
						<span class="field-edit" onclick="ShowMarketCategoryList( <?=$cat["ID"]?>, 'market_category_list_ebay' )"></span>
					</td>
				</tr>
			<?}?>
		</table>
		<div id="market_category_list_ebay" style="display: none">
			<?foreach( $marketCategory as $marketCat ){
			    $sortetMarketCategory[$marketCat["name"]] = $marketCat;
			}
				
            $marketCategory = $sortetMarketCategory;
			unset( $sortetMarketCategory );
			ksort( $marketCategory );?>
			
            <input onkeyup="FilterMarketCategoryList( this, 'market_category_list_ebay' )">
			<select onchange="SetMarketCategoryEbay( this.value, this )" size="25">
				<option></option>
				<?foreach( $marketCategory as $marketCat ){?>
					<option value="<?=$marketCat["id"]?>" data-search="<?=strtolower( $marketCat["name"] )?>"><?=$marketCat["name"]?></option>
				<?}?>
			</select>
		</div>
	</td>
</tr>