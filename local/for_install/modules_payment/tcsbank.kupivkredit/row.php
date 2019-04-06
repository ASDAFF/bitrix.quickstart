<?IncludeModuleLangFile(__FILE__);?>
<?$arTypes = Array("ANOTHER","USER","ORDER","PROPERTY");?>
<tr>
	<td><?=$arRow["NAME"]?></td>
	<td>
		<?$iNum = 0;?>
		<?foreach($arRow["SELECTED"] as $iSelectedKey=>$arSelectedValue):?>
			<?$iNum++?>
			<?if($iNum>1):?><div class = "dAdded"><?endif;?>
				<table cellspacing="2" cellpadding="0" border="0" class = "tType">
					<tr>
						<td><?=GetMessage("TCS_TYPE")?></td>
						<td>
							<select onchange="PropertyTypeChange(this,<?=$val["ID"]?>)" id="<?=$sSiteID?>[OPTIONS][<?=$val["ID"]?>][<?=$arRow["CODE"]?>][<?=$iSelectedKey?>][TYPE]" name="<?=$sSiteID?>[OPTIONS][<?=$val["ID"]?>][<?=$arRow["CODE"]?>][<?=$iSelectedKey?>][TYPE]">
								<?foreach($arTypes as $sType):?>
									<option value="<?=$sType?>" <?=($arSelectedValue["TYPE"]==$sType)?"selected":""?>><?=GetMessage("TCS_{$sType}")?></option>
								<?endforeach;?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?=GetMessage("TCS_VALUE")?></td>
						<td>
							<?if($arSelectedValue["TYPE"]=="ANOTHER"):?>
								<select style="display: none;" id="<?=$sSiteID?>[OPTIONS][<?=$val["ID"]?>][<?=$arRow["CODE"]?>][<?=$iSelectedKey?>][VALUE]" name="<?=$sSiteID?>[OPTIONS][<?=$val["ID"]?>][<?=$arRow["CODE"]?>][<?=$iSelectedKey?>][VALUE]"></select>
								<input style = "display:block;" id = "<?=$sSiteID?>[OPTIONS][<?=$val["ID"]?>][<?=$arRow["CODE"]?>][<?=$iSelectedKey?>][VALUE_ANOTHER]" name = "<?=$sSiteID?>[OPTIONS][<?=$val["ID"]?>][<?=$arRow["CODE"]?>][<?=$iSelectedKey?>][VALUE_ANOTHER]" type = "text" value = "<?=$arSelectedValue["VALUE"]?>"/>
							<?elseif($arSelectedValue["TYPE"]=="PROPERTY"):?>
								<select style="display: block;" id="<?=$sSiteID?>[OPTIONS][<?=$val["ID"]?>][<?=$arRow["CODE"]?>][<?=$iSelectedKey?>][VALUE]" name="<?=$sSiteID?>[OPTIONS][<?=$val["ID"]?>][<?=$arRow["CODE"]?>][<?=$iSelectedKey?>][VALUE]">
									<?foreach($arFields[$arSelectedValue["TYPE"]][$val["ID"]] as $sKey=>$sValue):?>
										<option value = "<?=$sKey?>" <?=($arSelectedValue["VALUE"]==$sKey)?"selected":""?>><?=$sValue?></option>
									<?endforeach;?>
								</select>
								<input style = "display:none;" type = "text" id = "<?=$sSiteID?>[OPTIONS][<?=$val["ID"]?>][<?=$arRow["CODE"]?>][<?=$iSelectedKey?>][VALUE_ANOTHER]" name = "<?=$sSiteID?>[OPTIONS][<?=$val["ID"]?>][<?=$arRow["CODE"]?>][<?=$iSelectedKey?>][VALUE_ANOTHER]" value = ""/>												
							<?else:?>
								<select style="display: block;" id="<?=$sSiteID?>[OPTIONS][<?=$val["ID"]?>][<?=$arRow["CODE"]?>][<?=$iSelectedKey?>][VALUE]" name="<?=$sSiteID?>[OPTIONS][<?=$val["ID"]?>][<?=$arRow["CODE"]?>][<?=$iSelectedKey?>][VALUE]">
									<?foreach($arFields[$arSelectedValue["TYPE"]] as $sKey=>$sValue):?>
										<option value = "<?=$sKey?>" <?=($arSelectedValue["VALUE"]==$sKey)?"selected":""?>><?=$sValue?></option>
									<?endforeach;?>
								</select>
								<input style = "display:none;" type = "text" id = "<?=$sSiteID?>[OPTIONS][<?=$val["ID"]?>][<?=$arRow["CODE"]?>][<?=$iSelectedKey?>][VALUE_ANOTHER]" name = "<?=$sSiteID?>[OPTIONS][<?=$val["ID"]?>][<?=$arRow["CODE"]?>][<?=$iSelectedKey?>][VALUE_ANOTHER]" value = ""/>					
							<?endif;?>
						</td>
					</tr>
				</table>
			
		<?endforeach;?>
	</td>
</tr>
