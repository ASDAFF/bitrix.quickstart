<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
if (!function_exists("showFilePropertyField"))
{
	function showFilePropertyField($name, $property_fields, $values, $max_file_size_show=50000)
	{
		$res = "";

		if (!is_array($values) || empty($values))
			$values = array(
				"n0" => 0,
			);

		if ($property_fields["MULTIPLE"] == "N")
		{
			$res = "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[0]\" id=\"".$name."[0]\"></label>";
		}
		else
		{
			$res = '
			<script type="text/javascript">
				function addControl(item)
				{
					var current_name = item.id.split("[")[0],
						current_id = item.id.split("[")[1].replace("[", "").replace("]", ""),
						next_id = parseInt(current_id) + 1;

					var newInput = document.createElement("input");
					newInput.type = "file";
					newInput.name = current_name + "[" + next_id + "]";
					newInput.id = current_name + "[" + next_id + "]";
					newInput.onchange = function() { addControl(this); };

					var br = document.createElement("br");
					var br2 = document.createElement("br");

					BX(item.id).parentNode.appendChild(br);
					BX(item.id).parentNode.appendChild(br2);
					BX(item.id).parentNode.appendChild(newInput);
				}
			</script>
			';

			$res .= "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[0]\" id=\"".$name."[0]\"></label>";
			$res .= "<br/><br/>";
			$res .= "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[1]\" id=\"".$name."[1]\" onChange=\"javascript:addControl(this);\"></label>";
		}

		return $res;
	}
}
if (!function_exists("PrintPropsForm"))
{
	function PrintPropsForm($arSource = array(), $locationTemplate = ".default", $arOrderProps)
	{
		if (!empty($arSource))
		{
			$eqPost = false;
			foreach ($arSource as $arProperties)
			{
				if($arProperties['CODE'] == 'CONFIDENTIAL' || !in_array($arProperties['CODE'], $arOrderProps))
				{
					continue;
				}
				if ($arProperties["TYPE"] == "CHECKBOX")
				{
					if($arProperties['CODE'] == 'EQ_POST' && $arProperties["CHECKED"]=="Y")
					{
						$eqPost = true;
					}
					?>
					<div class="wrap_field">
						<div class="row">
							<div class="col-sm-9 col-md-8 col-lg-8 sm-padding-right-no">
								<div class="field_name">
									<?
									if($arProperties['CODE'] == 'EQ_POST')
									{
										echo Loc::getMessage('MS_POST_ADDRESS');
									}
									else
									{
										echo $arProperties["NAME"];
									}
									if ($arProperties["REQUIED_FORMATED"]=="Y")
									{
										?>
										<span class="req"> *</span>
										<?
									}?>
								</div>
							</div>
							<div class="col-sm-15 col-md-16 col-lg-16 confidential-field">
								<input type="hidden" name="<?=$arProperties["FIELD_NAME"]?>" value="N" >
								<input
									type="checkbox"
									class="checkbox"
									name="<?=$arProperties["FIELD_NAME"]?>"
									id="<?=$arProperties["FIELD_NAME"]?>"
									value="Y"
									<?
									if($arProperties['CODE'] == 'EQ_POST')
									{
										echo 'onclick="showPostForm()"';
									}
									if ($arProperties["CHECKED"]=="Y")
									{
										echo " checked";
									}?>
								>
								<?php
								if($arProperties['CODE'] == 'EQ_POST')
								{?>
									<label for="<?=$arProperties["FIELD_NAME"]?>">
										<?php echo Loc::getMessage('MS_EQ_POST');?>
									</label>
								<?php }?>
							</div>
						</div>
					</div>
					<?
				}
				elseif ($arProperties["TYPE"] == "TEXT")
				{
					?>
					<div class="wrap_field <?php if(in_array($arProperties['CODE'], array('POST_ZIP','POST_CITY','POST_ADDRESS'))) echo 'post-field';?>" <?php if(in_array($arProperties['CODE'], array('POST_ZIP','POST_CITY','POST_ADDRESS')) && $eqPost) echo 'style="display:none;"';?>>
						<div class="row">
							<div class="col-sm-9 col-md-8 col-lg-8 sm-padding-right-no">
								<div class="field_name">
									<?echo $arProperties["NAME"];
									if ($arProperties["REQUIED_FORMATED"]=="Y")
									{
										?>
										<span class="req"> *</span>
										<?
									}?>
								</div>
							</div>
							<div class="col-sm-15 col-md-16 col-lg-16">
								<input
									type="text"
									maxlength="250"
									size="<?=$arProperties["SIZE1"]?>"
									value="<?if(in_array($arProperties['CODE'], array('POST_ZIP','POST_CITY','POST_ADDRESS')) && trim($arProperties["VALUE"]) == '') {echo 'test-test';} else echo $arProperties["VALUE"];?>"
									name="<?=$arProperties["FIELD_NAME"]?>"
									id="<?=$arProperties["FIELD_NAME"]?>"
									<?php echo ($arProperties['MASK'] == 'Y')?'class="show-mask"':'';?>
								>
							</div>
						</div>
					</div>
					<?
				}
				elseif ($arProperties["TYPE"] == "SELECT")
				{
					?>
					<div class="wrap_field">
						<div class="row">
							<div class="col-sm-9 col-md-8 col-lg-8 sm-padding-right-no">
								<div class="field_name">
									<?echo $arProperties["NAME"];
									if ($arProperties["REQUIED_FORMATED"]=="Y")
									{
										?>
										<span class="req"> *</span>
										<?
									}?>
								</div>
							</div>
							<div class="col-sm-15 col-md-16 col-lg-16">
								<select name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>">
									<?
									foreach($arProperties["VARIANTS"] as $arVariants):
									?>
									<option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
									<?
									endforeach;
									?>
								</select>
							</div>
						</div>
					</div>
					<?
				}
				elseif ($arProperties["TYPE"] == "MULTISELECT")
				{
					?>
					<div class="wrap_field">
						<div class="row">
							<div class="col-sm-9 col-md-8 col-lg-8 sm-padding-right-no">
								<div class="field_name">
									<?echo $arProperties["NAME"];
									if ($arProperties["REQUIED_FORMATED"]=="Y")
									{
										?>
										<span class="req"> *</span>
										<?
									}?>
								</div>
							</div>
							<div class="col-sm-15 col-md-16 col-lg-16">
								<select multiple name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>">
									<?
									foreach($arProperties["VARIANTS"] as $arVariants):
									?>
									<option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
									<?
									endforeach;
									?>
								</select>
							</div>
						</div>
					</div>
					<?
				}
				elseif ($arProperties["TYPE"] == "TEXTAREA")
				{
					$rows = ($arProperties["SIZE2"] > 10) ? 4 : $arProperties["SIZE2"];
					?>
					<div class="wrap_field">
						<div class="row">
							<div class="col-sm-9 col-md-8 col-lg-8 sm-padding-right-no">
								<div class="field_name">
									<?echo $arProperties["NAME"];
									if ($arProperties["REQUIED_FORMATED"]=="Y")
									{
										?>
										<span class="req"> *</span>
										<?
									}?>
								</div>
							</div>
							<div class="col-sm-15 col-md-16 col-lg-16">
								<textarea rows="<?=$rows?>" cols="<?=$arProperties["SIZE1"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["VALUE"]?></textarea>
							</div>
						</div>
					</div>
					<?
				}
				elseif ($arProperties["TYPE"] == "LOCATION")
				{
					$value = 0;
					if (is_array($arProperties["VARIANTS"]) && count($arProperties["VARIANTS"]) > 0)
					{
						foreach ($arProperties["VARIANTS"] as $arVariant)
						{
							if ($arVariant["SELECTED"] == "Y")
							{
								$value = $arVariant["ID"];
								break;
							}
						}
					}
					?>
					<div class="wrap_field">
						<div class="row">
							<div class="col-sm-9 col-md-8 col-lg-8 sm-padding-right-no">
								<div class="field_name">
									<?echo $arProperties["NAME"];
									if ($arProperties["REQUIED_FORMATED"]=="Y")
									{
										?>
										<span class="req"> *</span>
										<?
									}?>
								</div>
							</div>
							<div class="col-sm-15 col-md-16 col-lg-16">
								<?
								$GLOBALS["APPLICATION"]->IncludeComponent(
									"bitrix:sale.ajax.locations",
									$locationTemplate,
									array(
										"AJAX_CALL" => "N",
										"COUNTRY_INPUT_NAME" => "COUNTRY",
										"REGION_INPUT_NAME" => "REGION",
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
								?>
							</div>
						</div>
					</div>
					<?
				}
				elseif ($arProperties["TYPE"] == "RADIO")
				{
					?>
					<div class="wrap_field">
						<div class="row">
							<div class="col-sm-9 col-md-8 col-lg-8 sm-padding-right-no">
								<div class="field_name"><?=$arProperties["NAME"]?><?if ($arProperties["REQUIED_FORMATED"]=="Y"):?><span class="req"> *</span><?endif;?></div>
							</div>
							<div class="col-sm-15 col-md-16 col-lg-16">
								<?
								if (is_array($arProperties["VARIANTS"]))
								{
									foreach($arProperties["VARIANTS"] as $arVariants)
									{
										?>
										<input
											type="radio"
											name="<?=$arProperties["FIELD_NAME"]?>"
											id="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>"
											value="<?=$arVariants["VALUE"]?>" <?if($arVariants["CHECKED"] == "Y") echo " checked";?> />

										<label for="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>"><?=$arVariants["NAME"]?></label></br>
										<?
									}
								}
								?>
							</div>
						</div>
					</div>
					<?
				}
				elseif ($arProperties["TYPE"] == "FILE")
				{
					?>
					<div class="wrap_field">
						<div class="row">
							<div class="col-sm-9 col-md-8 col-lg-8 sm-padding-right-no">
								<div class="field_name">
									<?echo $arProperties["NAME"];
									if ($arProperties["REQUIED_FORMATED"]=="Y")
									{
										?>
										<span class="req"> *</span>
										<?
									}?>
								</div>
							</div>
							<div class="col-sm-15 col-md-16 col-lg-16">
								<? echo showFilePropertyField("ORDER_PROP_".$arProperties["ID"], $arProperties, $arProperties["VALUE"], $arProperties["SIZE1"]);
								if (strlen(trim($arProperties["DESCRIPTION"])) > 0)
								{
									?>
									<div class="bx_description">
										<?=$arProperties["DESCRIPTION"]?>
									</div>
									<?
								}
								?>
							</div>
						</div>
					</div>
					<?
				}
			}
		}
	}
}
?>