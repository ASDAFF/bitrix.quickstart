<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
function PrintPropsForm($arSource=Array(), $locationTemplate = ".default")
{
	if (!empty($arSource))
	{
		foreach($arSource as $arProperties)
		{
			if($arProperties["SHOW_GROUP_NAME"] == "Y")
			{
				?>
				<tr>
					<td colspan="2">
						<b><?= $arProperties["GROUP_NAME"] ?></b>
					</td>
				</tr>
				<?
			}
			?>
			<tr>
				<td align="right" valign="top" width="25%">
					
					<?
					if($arProperties["REQUIED_FORMATED"]=="Y")
					{
						?><span class="sof-req">*</span><?
					}
					?>
					<?= $arProperties["NAME"] ?>:
				</td>
				<td class="props">
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
						if ($arProperties["IS_ZIP"] == "Y")
						{
						?>
							<input type="hidden" name="CHANGE_ZIP" id="change_zip_val" value="" />
							<input onChange="fChangeZip();" type="text" maxlength="250" size="<?=$arProperties["SIZE1"]?>" value="<?=$arProperties["VALUE"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>">
							<script>
								function fChangeZip () 
								{
									document.getElementById("change_zip_val").value = "Y";
									submitForm();
								}
							</script>
						<?
						}
						else
						{
						?>
							<input type="text" maxlength="250" size="<?=$arProperties["SIZE1"]?>" value="<?=$arProperties["VALUE"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>">
						<?
						}
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
						<textarea rows="<?=$arProperties["SIZE2"]?>" cols="<?=$arProperties["SIZE1"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["VALUE"]?></textarea>
						<?
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
		?>
		<?
		return true;
	}
	return false;
}
?>

<div class="order_props_title"><div><?=GetMessage("SOA_PROP_INFO")?></div></div>
<div class="order_props">
	<table class="sale_order_full_table">
<!--	<tr>
		<td align="right" valign="top"><?//=GetMessage("SOA_TEMPL_PROFILE")?>:</td>
		<td>
			<select name="PROFILE_ID" id="ID_PROFILE_ID" onChange="submitForm();">
				<option value="0"><?//=GetMessage("SOA_TEMPL_PROP_NEW_PROFILE")?></option>
				<?
//				$default = "0";
//				foreach($arResult["USER_PROFILES"] as $key => $arUserProfiles)
//				{
					?>
					<option value="<?//=$key?>"<?//if ($arUserProfiles["CHECKED"]=="Y") {echo " selected";$default=$key;}?>><?//=$arUserProfiles["NAME"]?></option>
					<?
				//}
				?>
			</select>
			<input type="hidden" name="PROFILE_ID_OLD" value="<?//=$default?>" />
			<div class="desc"><?//=GetMessage("SOA_TEMPL_PROP_CHOOSE")?></div>
		</td>
	</tr>-->
	<?
	PrintPropsForm($arResult["ORDER_PROPS"]["USER_PROPS_N"], $arParams["TEMPLATE_LOCATION"]);
	PrintPropsForm($arResult["ORDER_PROPS"]["USER_PROPS_Y"], $arParams["TEMPLATE_LOCATION"]);
	?>

	</table>
</div>
