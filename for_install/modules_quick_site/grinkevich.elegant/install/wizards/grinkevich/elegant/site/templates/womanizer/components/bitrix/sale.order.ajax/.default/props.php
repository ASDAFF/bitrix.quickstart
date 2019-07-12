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
			?>
				<div style="width: 150px; float: left;"><?echo $arProperties["NAME"];
				if($arProperties["REQUIED_FORMATED"]=="Y")
				{
					?><span class="star">*</span><?
				}?> </div>
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
					<br><br>
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
					<br><br>
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
					<br><br>
					<?
				}
				elseif ($arProperties["TYPE"] == "TEXTAREA")
				{
					?>
					<textarea rows="<?=$arProperties["SIZE2"]?>" cols="<?=$arProperties["SIZE1"]?>" style="width:307px; height:80px;" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["VALUE"]?></textarea>
					<br><br>
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
					?><br><br><?
				}
				elseif ($arProperties["TYPE"] == "RADIO")
				{
					foreach($arProperties["VARIANTS"] as $arVariants)
					{
						?>
						<input type="radio" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>" value="<?=$arVariants["VALUE"]?>"<?if($arVariants["CHECKED"] == "Y") echo " checked";?>> <label for="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>"><?=$arVariants["NAME"]?></label><br />
						<br><br>
						<?
					}
				}

				if (strlen($arProperties["DESCRIPTION"]) > 0)
				{
					?><br /><small><?echo $arProperties["DESCRIPTION"] ?></small><?
				}
				?>
			<?
		}
		?>
		<?
		return true;
	}
	return false;
}
?>

<h2><?=GetMessage("SOA_TEMPL_PROP_INFO")?></h2>

<?
if(!empty($arResult["ORDER_PROP"]["USER_PROFILES"]))
{
	?>
	<b><?=GetMessage("SOA_TEMPL_PROP_PROFILE")?></b><br />
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
	<span style="font-style:italic; display: block;padding: 5px 0px 5px 0px;"><?=GetMessage("SOA_TEMPL_PROP_CHOOSE_DESCR")?></span><br><br>
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

<div style="width: 150px; float: left;"><?=GetMessage("SOA_TEMPL_SUM_COMMENTS")?></div>
<textarea style="width:307px; height:80px;" name="ORDER_DESCRIPTION"><?=$arResult["USER_VALS"]["ORDER_DESCRIPTION"]?></textarea>

