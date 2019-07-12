<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
function PrintPropsForm($arSource=Array(), $locationTemplate = ".default")
{
	if (!empty($arSource))
	{
		?>

		<?
		foreach($arSource as $arProperties)
		{
			?><div class="prop_name">
				<?echo $arProperties["NAME"];
				if($arProperties["REQUIED_FORMATED"]=="Y")
				{
					?><span class="star">*</span><?
				}?></div><div class="prop_field">
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
					<input type="text" maxlength="250" class="input_text_style" size="<?=$arProperties["SIZE1"]?>" value="<?=$arProperties["VALUE"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>">
					
					<?
				}
				elseif($arProperties["TYPE"] == "SELECT")
				{
					?>
					<select name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>">
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
					<select multiple name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>">
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
					<textarea rows="<?=$arProperties["SIZE2"]?>" cols="<?=$arProperties["SIZE1"]?>" style="width:307px; height:80px;" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["VALUE"]?></textarea>
					
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
						"bitrix:sale.ajax.locations",
						$locationTemplate,
						array(
							"AJAX_CALL" => "N",
							"COUNTRY_INPUT_NAME" => "COUNTRY_".$arProperties["FIELD_NAME"],
							"REGION_INPUT_NAME" => "REGION_".$arProperties["FIELD_NAME"],
							"CITY_INPUT_NAME" => $arProperties["FIELD_NAME"],
							"CITY_OUT_LOCATION" => "Y",
							"LOCATION_VALUE" => $value,
							"ORDER_PROPS_ID" => $arProperties["ID"],
							"ONCITYCHANGE" => ($arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y") ? "submitForm()" : "",
							"SIZE1" => $arProperties["SIZE1"],
						),
						null,
						array('HIDE_ICONS' => 'Y')
					);
					?><?
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
			</div><div style="clear:both;"></div><?
		}
		?>
		<?
		return true;
	}
	return false;
}
?>

<h2><?=GetMessage("SOA_TEMPL_PROP_INFO")?></h2>
<div class="myorder_properties">
<?
if(!empty($arResult["ORDER_PROP"]["USER_PROFILES"]))
{
	?>
	<div class="prop_name"><?=GetMessage("SOA_TEMPL_PROP_PROFILE")?></div><div class="prop_field">
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
	</select>
	<br />
	<small><?=GetMessage("SOA_TEMPL_PROP_CHOOSE_DESCR")?></small> </div><div style="clear:both;"></div>
	<?
}
?>
<div style="display:none;">
	<?
	$APPLICATION->IncludeComponent(
		"bitrix:sale.ajax.locations",
		".default",
		array(
			"AJAX_CALL" => "N",
			"COUNTRY_INPUT_NAME" => "COUNTRY_tmp",
			"REGION_INPUT_NAME" => "REGION_tmp",
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


<?
PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_N"], $arParams["TEMPLATE_LOCATION"]);
PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_Y"], $arParams["TEMPLATE_LOCATION"]);
?>
	<div class="prop_name"><?=GetMessage("SOA_TEMPL_SUM_ADIT_INFO")?></div><div class="prop_field">
		<textarea style="max-width:500px;" name="ORDER_DESCRIPTION"><?=$arResult["USER_VALS"]["ORDER_DESCRIPTION"]?></textarea>
	</div><div style="clear:both;"></div>
</div>
 




