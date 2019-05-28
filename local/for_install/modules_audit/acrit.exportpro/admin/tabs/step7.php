<?php
IncludeModuleLangFile( __FILE__ );
          
if( CModule::IncludeModule( "currency" ) ){
	$arCurrencyList = array();
	$dbRes = CCurrency::GetList(
        $by = "sort",
        $order = "asc",
        LANGUAGE_ID
    );
	
    while( $arRes = $dbRes->Fetch() ){
		$arCurrencyList[$arRes["CURRENCY"]] = $arRes["FULL_NAME"];
	}      
}
else{
	$arCurrencyList = array(
        "RUB" => GetMessage( "ACRIT_EXPORTPRO_CURRENCU_RUB" ),
		"USD" => GetMessage( "ACRIT_EXPORTPRO_CURRENCU_USD" ),
		"EUR" => GetMessage( "ACRIT_EXPORTPRO_CURRENCU_EUR" ),
		"UAH" => GetMessage( "ACRIT_EXPORTPRO_CURRENCU_UAH" ),
		"BYR" => GetMessage( "ACRIT_EXPORTPRO_CURRENCU_BYR" ),
		"KZT" => GetMessage( "ACRIT_EXPORTPRO_CURRENCU_KZH" )
    );
}

$convertCurrency = ( $arProfile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ) ? 'checked="checked"' : "";
$convertTable = $convertCurrency ? "" : "display:none;";
$correctTable = $convertTable ? "" : "display:none;";

$currencyRates = CExportproProfile::LoadCurrencyRates();
$currencyRates = array_intersect(
    $currencyRates["CBRF"],
    $currencyRates["NBU"],
    $currencyRates["NBK"],
    $currencyRates["NBB"]
);                       

if( empty( $currencyRates ) ){
    $currencyRates = $arCurrencyList;
}                                

ksort( $currencyRates );
?>
<tr align="center">
    <td colspan="2">
        <?=BeginNote();?>
        <?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CURRENCY_DESCRIPTION" )?>
        <?=EndNote();?>
    </td>
</tr>
<tr>
    <td width="50%">
        <span id="hint_PROFILE[CURRENCY][CONVERT_CURRENCY]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[CURRENCY][CONVERT_CURRENCY]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_CURRENCY_CONVERT_CURRENCY_HELP" )?>' );</script>
        <label for="PROFILE[CURRENCY][CONVERT_CURRENCY]"><?=GetMessage( "ACRIT_EXPORTPRO_CURRENCY_CONVERT_CURRENCY" )?></label>
    </td>
    <td><input type="checkbox" name="PROFILE[CURRENCY][CONVERT_CURRENCY]" value="Y" <?=$convertCurrency?> onclick="convertCurrency()" ></td>
</tr>
<tr>
    <td colspan="2" align="center">
        <table cellpadding="2" cellspacing="0" border="0" class="internal" align="center" width="100%">
            <thead>
                <tr class="heading">
                    <td colspan="3" align="left"><?=GetMessage( "ACRIT_EXPORTPRO_CURRENCU_HEAD_CURRENCY" )?></td>
                    <td align="center" class="currency_table" style="<?=$convertTable?>"><?=GetMessage( "ACRIT_EXPORTPRO_CURRENCU_HEAD_RATE" )?></td>
                    <td align="center" class="currency_table" style="<?=$convertTable?>"><?=GetMessage( "ACRIT_EXPORTPRO_CURRENCU_HEAD_CONVERTTO" )?></td>
                    <td align="center"><?=GetMessage( "ACRIT_EXPORTPRO_CURRENCU_HEAD_CORRECT" )?></td>
                </tr>
            </thead>
            <tbody>
                <?if( is_array( $arCurrencyList ) ){
                    foreach( $arCurrencyList as $id => $curr ){
                        $checked = $arProfile["CURRENCY"][$id]["CHECK"] == "Y" ? 'checked="checked"' : "";?>
                        <tr>
                            <td align="center"><input type="checkbox" name="PROFILE[CURRENCY][<?=$id?>][CHECK]" value="Y" <?=$checked?> /></td>
                            
                            <?$convertFrom = $arProfile["CURRENCY"][$id]["CONVERT_FROM"] ? $arProfile["CURRENCY"][$id]["CONVERT_FROM"] : $id?>
                            <td class="currency_table" style="<?=$convertTable?>">
                                <input type="text" name="PROFILE[CURRENCY][<?=$id?>][CONVERT_FROM]" value="<?=$convertFrom?>">
                            </td>
                            
                            <td><?=$curr?></td>
                            
                            <td align="center" class="currency_table" style="<?=$convertTable?>">
                                <select name="PROFILE[CURRENCY][<?=$id?>][RATE]">
                                    <?foreach( $profileUtils->GetCurrencyRate() as $rate => $name ){?>
                                        <?$selected = $rate == $arProfile["CURRENCY"][$id]["RATE"] ? 'selected="selected"' : "";?>
                                        <option value="<?=$rate?>" <?=$selected?>><?=$name?></option>
                                    <?}?>
                                </select>
                            </td>
                            
                            <td class="currency_table" style="<?=$convertTable?>">
                                <select name="PROFILE[CURRENCY][<?=$id?>][CONVERT_TO]">
                                    <?foreach( $currencyRates as $currency ){?>
                                        <?$selected = $currency["CURRENCY"] == $arProfile["CURRENCY"][$id]["CONVERT_TO"] ? 'selected="selected"' : "";?>
                                        <option value="<?=$currency["CURRENCY"]?>" <?=$selected?>><?=$currency["CURRENCY"]?></option>
                                    <?}?>
                                </select>
                            </td>
                            
                            <td align="center">+-<input type="text" name="PROFILE[CURRENCY][<?=$id?>][PLUS]" value="<?=$arProfile["CURRENCY"][$id]["PLUS"]?>">%</td>
                        </tr>
                    <?}
                }?>
            </tbody>
        </table>
    </td>
</tr>