<?php
IncludeModuleLangFile(__FILE__);
          
$ozonAppId = $arProfile["OZON_APPID"];
$ozonAppKey = $arProfile["OZON_APPKEY"];

$marketCategory = array();
if( !empty( $ozonAppId ) && !empty( $ozonAppKey ) ){
	$ozon = new OZON( $ozonAppId, $ozonAppKey );
	$marketCategory = $ozon->GetAllTypes();
}

foreach( $marketCategory as $key => $cat ){
	$marketCategory[$cat["ProductTypeId"]] = $cat;
	unset( $marketCategory[$key] );
}

//$categories = $profileUtils->GetSections( $arProfile["IBLOCK_ID"], true );          
?>

<tr align="center">
    <td colspan="2">
        <?=BeginNote();?>
		<?=GetMessage( "ACRIT_EXPORTPRO_OZON_CATEGORY_DESCRIPTION" )?>
        <?=EndNote();?>
    </td>
</tr>
<?

?>
<tr>
	<td colspan="2" id="market_category_data_ozon">
		<table width="100%">
			<?foreach( $categories as $cat ):?>
				<tr>
					<td width="40%">
						<label form="PROFILE[MARKET_CATEGORY][OZON][CATEGORY_LIST][<?=$cat["ID"]?>]"><?=$cat["NAME"]?></label>
					</td>
					<td width="60%">
                        <input type="text" readonly="readonly" value="<?=$marketCategory[$arProfile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$cat["ID"]]]["PathName"]?>" name="PROFILE_MARKET_CATEGORY_CATEGORY_LIST_OZON_<?=$cat["ID"]?>_NAME" style="width: 100%; opacity: 1"/>
						<input type="hidden" value="<?=$arProfile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$cat["ID"]]?>" name="PROFILE[MARKET_CATEGORY][OZON][CATEGORY_LIST][<?=$cat["ID"]?>]" />
						<span class="field-edit" onclick="ShowMarketCategoryList( <?=$cat["ID"]?>, 'market_category_list_ozon' )"></span>
					</td>
				</tr>
			<?endforeach?>
		</table>
		<div id="market_category_list_ozon" style="display: none">
			<?
				$sortetMarketCategory = array();
				foreach( $marketCategory as $marketCat ){
					$sortetMarketCategory[$marketCat["PathName"]] = $marketCat;
				}
				$marketCategory = $sortetMarketCategory;
				unset( $sortetMarketCategory );
				ksort( $marketCategory );
			?>
			<input onkeyup="FilterMarketCategoryList( this, 'market_category_list_ozon' )">
			<select onclick="SetMarketCategoryOzon( this.value, this )" size="25">
				<option></option>
				<?foreach( $marketCategory as $marketCat ):?>
					<option value="<?=$marketCat["ProductTypeId"]?>" data-search="<?=strtolower( $marketCat["PathName"] )?>"><?=$marketCat["PathName"]?></option>
				<?endforeach?>
			</select>
		</div>
	</td>
</tr>
