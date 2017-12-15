<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<a name="tb"></a>
<a href="<?=$arParams["PATH_TO_LIST"]?>"><?=GetMessage("SPPD_RECORDS_LIST")?></a>
<br /><br />
<?if(strlen($arResult["ID"])>0):?>
	<?=ShowError($arResult["ERROR_MESSAGE"])?>
	<form method="post" action="<?=POST_FORM_ACTION_URI?>">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="ID" value="<?=$arResult["ID"]?>">
	<table class="sale_personal_profile_detail data-table">
		<tr>
			<th colspan="2">
				<b><?= str_replace("#ID#", $arResult["ID"], GetMessage("SPPD_PROFILE_NO")) ?></b>
			</th>
		</tr>
		<tr>
			<td width="40%" align="right"><?echo GetMessage("SALE_PERS_TYPE")?>:</td>
			<td width="60%"><?=$arResult["PERSON_TYPE"]["NAME"]?></td>
		</tr>
		<tr>
			<td width="40%" align="right"><?echo GetMessage("SALE_PNAME")?>:<span class="req">*</span></td>
			<td width="60%"><input type="text" name="NAME" value="<?echo $arResult["NAME"];?>" size="40"></td>
		</tr>
		<tr>
			<td colspan="2"><img src="/bitrix/images/1.gif" width="1" height="8"></td>
		</tr>
		<?
		foreach($arResult["ORDER_PROPS"] as $val)
		{
			if(!empty($val["PROPS"]))
			{
				?>
				<tr>
					<th colspan="2"><b><?=$val["NAME"];?></b></th>
				</tr>
				<?
				foreach($val["PROPS"] as $vval)
				{
					$currentValue = $arResult["ORDER_PROPS_VALUES"]["ORDER_PROP_".$vval["ID"]];
					$name = "ORDER_PROP_".$vval["ID"];
					?>
					<tr>
						<td width="50%" align="right"><?=$vval["NAME"] ?>:
							<?if ($vval["REQUIED"]=="Y")
							{
								?><span class="req">*</span><?
							}
							?></td>
						<td width="50%">
							<?if ($vval["TYPE"]=="CHECKBOX"):?>
								<input type="hidden" name="<?=$name?>" value="">
								<input type="checkbox" name="<?=$name?>" value="Y"<?if ($currentValue=="Y" || !isset($currentValue) && $vval["DEFAULT_VALUE"]=="Y") echo " checked";?>>
							<?elseif ($vval["TYPE"]=="TEXT"):?>
								<input type="text" size="<?echo (IntVal($vval["SIZE1"])>0)?$vval["SIZE1"]:30; ?>" maxlength="250" value="<?echo (isset($currentValue)) ? $currentValue : $vval["DEFAULT_VALUE"];?>" name="<?=$name?>">
							<?elseif ($vval["TYPE"]=="SELECT"):?>
								<select name="<?=$name?>" size="<?echo (IntVal($vval["SIZE1"])>0)?$vval["SIZE1"]:1; ?>">
									<?foreach($vval["VALUES"] as $vvval):?>
										<option value="<?echo $vvval["VALUE"]?>"<?if ($vvval["VALUE"]==$currentValue || !isset($currentValue) && $vvval["VALUE"]==$vval["DEFAULT_VALUE"]) echo " selected"?>><?echo $vvval["NAME"]?></option>
									<?endforeach;?>
								</select>
							<?elseif ($vval["TYPE"]=="MULTISELECT"):?>
								<select multiple name="<?=$name?>[]" size="<?echo (IntVal($vval["SIZE1"])>0)?$vval["SIZE1"]:5; ?>">
									<?
									$arCurVal = array();
									$arCurVal = explode(",", $currentValue);
									for ($i = 0; $i<count($arCurVal); $i++)
										$arCurVal[$i] = Trim($arCurVal[$i]);
									$arDefVal = explode(",", $vval["DEFAULT_VALUE"]);
									for ($i = 0; $i<count($arDefVal); $i++)
										$arDefVal[$i] = Trim($arDefVal[$i]);
									foreach($vval["VALUES"] as $vvval):?>
										<option value="<?echo $vvval["VALUE"]?>"<?if (in_array($vvval["VALUE"], $arCurVal) || !isset($currentValue) && in_array($vvval["VALUE"], $arDefVal)) echo" selected"?>><?echo $vvval["NAME"]?></option>
									<?endforeach;?>
								</select>
							<?elseif ($vval["TYPE"]=="TEXTAREA"):?>
								<textarea rows="<?echo (IntVal($vval["SIZE2"])>0)?$vval["SIZE2"]:4; ?>" cols="<?echo (IntVal($vval["SIZE1"])>0)?$vval["SIZE1"]:40; ?>" name="<?=$name?>"><?echo (isset($currentValue)) ? $currentValue : $vval["DEFAULT_VALUE"];?></textarea>
							<?elseif ($vval["TYPE"]=="LOCATION"):?>
								<?if ($arParams['USE_AJAX_LOCATIONS'] == 'Y'):
									$APPLICATION->IncludeComponent('bitrix:sale.ajax.locations', '', array(
											"AJAX_CALL" => "N", 
											'CITY_OUT_LOCATION' => 'Y',
											'COUNTRY_INPUT_NAME' => $name.'_COUNTRY',
											'CITY_INPUT_NAME' => $name,
											'LOCATION_VALUE' => isset($currentValue) ? $currentValue : $vval["DEFAULT_VALUE"],
										),
										null,
										array('HIDE_ICONS' => 'Y')
									);
								else:
								?>
								<select name="<?=$name?>" size="<?echo (IntVal($vval["SIZE1"])>0)?$vval["SIZE1"]:1; ?>">
									<?foreach($vval["VALUES"] as $vvval):?>
										<option value="<?echo $vvval["ID"]?>"<?if (IntVal($vvval["ID"])==IntVal($currentValue) || !isset($currentValue) && IntVal($vvval["ID"])==IntVal($vval["DEFAULT_VALUE"])) echo " selected"?>><?echo $vvval["COUNTRY_NAME"]." - ".$vvval["CITY_NAME"]?></option>
									<?endforeach;?>
								</select>
								<?
								endif;
								?>
							<?elseif ($vval["TYPE"]=="RADIO"):?>
								<?foreach($vval["VALUES"] as $vvval):?>
									<input type="radio" name="<?=$name?>" value="<?echo $vvval["VALUE"]?>"<?if ($vvval["VALUE"]==$currentValue || !isset($currentValue) && $vvval["VALUE"]==$vval["DEFAULT_VALUE"]) echo " checked"?>><?echo $vvval["NAME"]?><br />
								<?endforeach;?>
							<?endif?>

							<?if (strlen($vval["DESCRIPTION"])>0):?>
								<br /><small><?echo $vval["DESCRIPTION"] ?></small>
							<?endif?>
						</td>
					</tr>
					<?
				}
			}
		}
		?>

	</table>

	<br />
	<div style="text-align: right;">
		<input type="submit" class="btn_all btn_green" style="display: inline-block;" name="save" value="<?echo GetMessage("SALE_SAVE") ?>">
		&nbsp;
		<input type="submit" class="btn_all " style="display: inline-block;" name="apply" value="<?=GetMessage("SALE_APPLY")?>">
		&nbsp;
		<input type="submit" class="btn_all " style="display: inline-block;" name="reset" value="<?echo GetMessage("SALE_RESET")?>">
	</div>
	</form>
<?else:?>
	<?=ShowError($arResult["ERROR_MESSAGE"]);?>
<?endif;?>
