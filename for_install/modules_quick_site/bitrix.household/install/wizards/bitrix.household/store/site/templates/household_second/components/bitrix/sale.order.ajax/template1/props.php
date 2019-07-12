<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
function PrintPropsForm($arSource=Array())
{
	if (!empty($arSource))
	{
		foreach($arSource as $arProperties)
		{
			if ($arProperties["TYPE"] == "LOCATION" && count($arProperties["VARIANTS"]) == 1)
			{
				$arC = array_values($arProperties["VARIANTS"]);
				?>
				<tr style="display:none;"><td colspan="2" style="display:none;">
				<input type="hidden" name="<?=$arProperties["FIELD_NAME"]?>" value="<?=$arC[0]["ID"]?>">
				</td></tr>
				<?
			}
			else
			{
				?>
				<tr>
					<td valign="top" align="right" style="width:5% !important;" >
						<?if($arProperties["REQUIED_FORMATED"]=="Y")
						{
							?><span class="starrequired">*</span><?
						}
						echo $arProperties["NAME"] ?>:
					</td>
					<td style="width:95% !important;" >
						<?
						if($arProperties["TYPE"] == "CHECKBOX")
						{
							?>
							
							<input type="hidden" name="<?=$arProperties["FIELD_NAME"]?>" value="">
							<input type="checkbox" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" value="Y"<?if ($arProperties["CHECKED"]=="Y") echo " checked";?>>
							<?
						}
						elseif($arProperties["TYPE"] == "TEXT")
						{
							?>
							<input type="text" maxlength="250" size="<?=$arProperties["SIZE1"]?>" value="<?=$arProperties["VALUE"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" style="width:300px;">
							<?
						}
						elseif($arProperties["TYPE"] == "SELECT")
						{
							?>
							<select name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>" style="width:300px;">
							<?
							foreach($arProperties["VARIANTS"] as $arVariants)
							{
								?>
								<option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
								<?
							}
							?>
							</select>
							<?
						}
						elseif ($arProperties["TYPE"] == "MULTISELECT")
						{
							?>
							<select multiple name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>" style="width:300px;">
							<?
							foreach($arProperties["VARIANTS"] as $arVariants)
							{
								?>
								<option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
								<?
							}
							?>
							</select>
							<?
						}
						elseif ($arProperties["TYPE"] == "TEXTAREA")
						{
							?>
							<textarea rows="<?=$arProperties["SIZE2"]?>" cols="<?=$arProperties["SIZE1"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" style="width:300px;"><?=$arProperties["VALUE"]?></textarea>
							<?
						}
						elseif ($arProperties["TYPE"] == "LOCATION")
						{
							$value = 0;
							foreach ($arProperties["VARIANTS"] as $arVariant) 
							{
								if ($arVariant["SELECTED"] == "Y") 
								{
									$value = $arVariant["ID"]; 
									break;
								}
							}

							$GLOBALS["APPLICATION"]->IncludeComponent(
								'bitrix:sale.ajax.locations', 
								'', 
								array(
									"AJAX_CALL" => "N", 
									"COUNTRY_INPUT_NAME" => "COUNTRY_".$arProperties["FIELD_NAME"],
									"CITY_INPUT_NAME" => $arProperties["FIELD_NAME"],
									"CITY_OUT_LOCATION" => "Y",
									"LOCATION_VALUE" => $value,
									"ONCITYCHANGE" => ($arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y") ? "submitForm()" : "",
								),
								null,
								array('HIDE_ICONS' => 'Y')
							);
						}
						elseif ($arProperties["TYPE"] == "RADIO")
						{
							foreach($arProperties["VARIANTS"] as $arVariants)
							{
								?>
								<input type="radio" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>" value="<?=$arVariants["VALUE"]?>"<?if($arVariants["CHECKED"] == "Y") echo " checked";?>> <label for="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>"><?=$arVariants["NAME"]?></label><br />
								<?
							}
						}

						if (strlen($arProperties["DESCRIPTION"]) > 0)
						{
							?><br /><small><?echo $arProperties["DESCRIPTION"] ?></small><?
						}
						?>
						
					</td>
				</tr>
				<?
			}
		}

		return true;
	}
	return false;
}
?>
<br/>
<div class="order-item">
<div class="order-title">
	<b class="r2"></b><b class="r1"></b><b class="r0"></b>
	<div class="order-title-inner">
		<span><b><?=GetMessage("SOA_TEMPL_PROP_INFO")?></b></span>
	</div>
</div>
<div class="order-info">
	<div style="display:none;">
	<?
		$APPLICATION->IncludeComponent(
			'bitrix:sale.ajax.locations', 
			'', 
			array(
				"AJAX_CALL" => "N", 
				"COUNTRY_INPUT_NAME" => "COUNTRY_tmp",
				"CITY_INPUT_NAME" => "tmp",
				"CITY_OUT_LOCATION" => "Y",
				"LOCATION_VALUE" => "",
				"ONCITYCHANGE" => "",
			),
			null,
			array('HIDE_ICONS' => 'Y')
		);
	?>
	</div>
	<table class="no_border" cellspacing="0" cellpadding="6">
	<tbody>
		<?
		if(!empty($arResult["ORDER_PROP"]["USER_PROFILES"]))
		{
			?>
			<tr>
				<td style="width:5% !important;" valign="top" align="right">
					<?=GetMessage("SOA_TEMPL_PROP_PROFILE")?>
				</td>
				<td style="width:95% !important;">
					
					<script language="JavaScript">
					function SetContact(profileId)
					{
						document.getElementById("profile_change").value = "Y";
						submitForm();
					}
					</script>
					<input type="hidden" name="profile_change" id="profile_change" value="N">
					<select name="PROFILE_ID" id="ID_PROFILE_ID" onChange="SetContact(this.value)">
						<option value="0"><?=GetMessage("SOA_TEMPL_PROP_NEW_PROFILE")?></option>
						<?
						foreach($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles)
						{
							?>
							<option value="<?= $arUserProfiles["ID"] ?>"<?if ($arUserProfiles["CHECKED"]=="Y") echo " selected";?>><?=$arUserProfiles["NAME"]?></option>
							<?
						}
						?>
					</select><br />
					<?=GetMessage("SOA_TEMPL_PROP_CHOOSE_DESCR")?>
				</td>
			</tr>
			<?
		}
		PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_N"]);
		PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_Y"]);
		?>
	</tbody>
	</table>
</div>
</div>
