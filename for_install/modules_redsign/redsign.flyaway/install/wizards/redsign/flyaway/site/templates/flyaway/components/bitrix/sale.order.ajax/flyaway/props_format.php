<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}
if (!function_exists("showFilePropertyField")) {
	function showFilePropertyField($name, $property_fields, $values, $max_file_size_show=50000)
	{
		$res = "";

		if (!is_array($values) || empty($values))
			$values = array(
				"n0" => 0,
			);

		if ($property_fields["MULTIPLE"] == "N")
		{
			$res = "<input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[0]\" id=\"".$name."[0]\">";
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

			$res .= "<input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[0]\" id=\"".$name."[0]\"></label>";
			$res .= "<br/><br/>";
			$res .= "<input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[1]\" id=\"".$name."[1]\" onChange=\"javascript:addControl(this);\">";
		}

		return $res;
	}
}

if (!function_exists("PrintPropsForm"))
{
	function PrintPropsForm($arSource = array(), $locationTemplate = ".default")
	{
		$i = 0;
		if (!empty($arSource))
		{
			?>
					<?
					$countSources = count($arSource);
					foreach ($arSource as $arProperties)
					{
						if($i%2 == 0) {
							echo '<div class = "row">';
						}
						?>
						<div class = "col col-md-6" data-property-id-row="<?=intval(intval($arProperties["ID"]))?>">

						
						<?
						if ($arProperties["TYPE"] == "CHECKBOX")
						{
							?>
                            <div class="form-group">
                                <div class = "gui-box">
                                    <label class="gui-checkbox" for="<?=$arProperties["FIELD_NAME"]?>">
                                        <input type="hidden" name="<?=$arProperties["FIELD_NAME"]?>" value="">
                                        <input class="gui-checkbox-input" type="checkbox" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" value="Y"<?if ($arProperties["CHECKED"]=="Y") echo " checked";?>>
                                        <span class="gui-checkbox-icon"></span>
                                        <?=$arProperties["NAME"]?>
                                    </label>
                                    <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
                                        <span class="required">*</span>
                                    <?endif;?>
                                    <span id="helpBlock" class="help-block">
                                    <? if (strlen(trim($arProperties["DESCRIPTION"])) > 0):?>
                                        <span id="helpBlock" class="help-block">
                                            <?=$arProperties["DESCRIPTION"]?>
                                        </span>
                                    <? endif; ?>
                                </div>
                            </div>
							<?
						}
						elseif ($arProperties["TYPE"] == "TEXT")
						{
							?>
							<div class = "form-group">
								<label class="control-label" for="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["NAME"]?></label>
								<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
									<span class="required">*</span>
								<?endif;?>
								<input class = "form-control" type="text" maxlength="250" size="<?=$arProperties["SIZE1"]?>" value="<?=$arProperties["VALUE"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" />
								<? if (strlen(trim($arProperties["DESCRIPTION"])) > 0):?>
									<span id="helpBlock" class="help-block">
										<?=$arProperties["DESCRIPTION"]?>
									</span>
								<? endif; ?>
							</div>
							<?
						}
						elseif ($arProperties["TYPE"] == "SELECT")
						{
							?>
							<div class = "form-group">
								<label class="control-label" for="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["NAME"]?></label>
								<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
									<span class="required">*</span>
								<?endif;?>
								<select class="form-control" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>">
									<?foreach($arProperties["VARIANTS"] as $arVariants):?>
										<option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
									<?endforeach;?>
								</select>
								<? if (strlen(trim($arProperties["DESCRIPTION"])) > 0):?>
									<span id="helpBlock" class="help-block">
										<?=$arProperties["DESCRIPTION"]?>
									</span>
								<? endif; ?>
							</div>
							<?
						}
						elseif ($arProperties["TYPE"] == "MULTISELECT")
						{
							?>
							<div class = "form-group">
								<label class="control-label" for="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["NAME"]?></label>
								<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
									<span class="required">*</span>
								<?endif;?>
								<select multiple class="form-control" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>">
									<?foreach($arProperties["VARIANTS"] as $arVariants):?>
										<option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
									<?endforeach;?>
								</select>
								<? if (strlen(trim($arProperties["DESCRIPTION"])) > 0):?>
									<span id="helpBlock" class="help-block">
										<?=$arProperties["DESCRIPTION"]?>
									</span>
								<? endif; ?>
							</div>

							<?
						}
						elseif ($arProperties["TYPE"] == "TEXTAREA")
						{
							$rows = ($arProperties["SIZE2"] > 10) ? 4 : $arProperties["SIZE2"];
							?>
							
							<div class = "form-group">
								<label class="control-label" for="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["NAME"]?></label>
								<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
									<span class="required">*</span>
								<?endif;?>
								<textarea class = "form-control" rows="<?=$rows?>" cols="<?=$arProperties["SIZE1"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["VALUE"]?></textarea>
								<? if (strlen(trim($arProperties["DESCRIPTION"])) > 0):?>
									<span id="helpBlock" class="help-block">
										<?=$arProperties["DESCRIPTION"]?>
									</span>
								<? endif; ?>
							</div>
							<?
						}
						elseif ($arProperties["TYPE"] == "LOCATION")
						{
							// TODO
							?>
							<?
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

							// here we can get '' or 'popup'
							// map them, if needed
							if(CSaleLocation::isLocationProMigrated())
							{
								$locationTemplateP = $locationTemplate == 'popup' ? 'search' : 'steps';
								$locationTemplateP = $_REQUEST['PERMANENT_MODE_STEPS'] == 1 ? 'steps' : $locationTemplateP; // force to "steps"
							}
							?>
							<div class="form-group">
								<label class="control-label" for="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["NAME"]?></label>
								<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
									<span class="required">*</span>
								<?endif;?>
								<?if($locationTemplateP == 'steps'):?>
									<input type="hidden" id="LOCATION_ALT_PROP_DISPLAY_MANUAL[<?=intval($arProperties["ID"])?>]" name="LOCATION_ALT_PROP_DISPLAY_MANUAL[<?=intval($arProperties["ID"])?>]" value="<?=($_REQUEST['LOCATION_ALT_PROP_DISPLAY_MANUAL'][intval($arProperties["ID"])] ? '1' : '0')?>" />
								<?endif?>

								<?CSaleLocation::proxySaleAjaxLocationsComponent(array(
									"AJAX_CALL" => "N",
									"COUNTRY_INPUT_NAME" => "COUNTRY",
									"REGION_INPUT_NAME" => "REGION",
									"CITY_INPUT_NAME" => $arProperties["FIELD_NAME"],
									"CITY_OUT_LOCATION" => "Y",
									"LOCATION_VALUE" => $value,
									"ORDER_PROPS_ID" => $arProperties["ID"],
									"ONCITYCHANGE" => ($arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y") ? "submitForm()" : "",
									"SIZE1" => $arProperties["SIZE1"],
									"INPUT_PLACEHOLDER" => $arProperties['NAME'].($arProperties['REQUIED_FORMATED'] == 'Y' ? '*' : '')
								),
								array(
									"ID" => $value,
									"CODE" => "",
									"SHOW_DEFAULT_LOCATIONS" => "Y",

									// function called on each location change caused by user or by program
									// it may be replaced with global component dispatch mechanism coming soon
									"JS_CALLBACK" => "submitFormProxy",

									// function window.BX.locationsDeferred['X'] will be created and lately called on each form re-draw.
									// it may be removed when sale.order.ajax will use real ajax form posting with BX.ProcessHTML() and other stuff instead of just simple iframe transfer
									"JS_CONTROL_DEFERRED_INIT" => intval($arProperties["ID"]),

									// an instance of this control will be placed to window.BX.locationSelectors['X'] and lately will be available from everywhere
									// it may be replaced with global component dispatch mechanism coming soon
									"JS_CONTROL_GLOBAL_ID" => intval($arProperties["ID"]),

									"DISABLE_KEYBOARD_INPUT" => "Y",
									"PRECACHE_LAST_LEVEL" => "Y",
									"PRESELECT_TREE_TRUNK" => "Y",
									"SUPPRESS_ERRORS" => "Y",
									"INPUT_PLACEHOLDER" => $arProperties['NAME'].($arProperties['REQUIED_FORMATED'] == 'Y' ? '*' : '')
								),
								$locationTemplateP,
								true,
								'location-block-wrapper form-control'
								)?>
							</div>
							<?
						}
						elseif ($arProperties["TYPE"] == "RADIO")
						{
							?>
							<div class = "form-group">
								<label class="control-label" for="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["NAME"]?></label>
								<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
									<span class="required">*</span>
								<?endif;?>
								<?
								if (is_array($arProperties["VARIANTS"]))
								{
									foreach($arProperties["VARIANTS"] as $arVariants):
									?>
										<div class = "gui-box">

											<label class="gui-radiobox" for="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>">
                                                <input
                                                    type="radio"
                                                    name="<?=$arProperties["FIELD_NAME"]?>"
                                                    id="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>"
                                                    class="gui-radiobox-item"
                                                    value="<?=$arVariants["VALUE"]?>" <?if($arVariants["CHECKED"] == "Y") echo " checked";?>>
                                                <span class="gui-out"><span class="gui-inside"></span></span>
                                                <?=$arVariants["NAME"]?>
                                            </label>
										</div>
									<?
									endforeach;
								}
								?>
								<? if (strlen(trim($arProperties["DESCRIPTION"])) > 0):?>
									<span id="helpBlock" class="help-block">
										<?=$arProperties["DESCRIPTION"]?>
									</span>
								<? endif; ?>
							</div>
							<?
						} elseif ($arProperties["TYPE"] == "DATE") {
                            ?>
                            <div class = "form-group">
                                <label class="control-label" for="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["NAME"]?></label>
								<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
									<span class="required">*</span>
								<?endif;?>
								<?
								global $APPLICATION;

								$APPLICATION->IncludeComponent('bitrix:main.calendar', 'flyaway', array(
									'SHOW_INPUT' => 'Y',
									'INPUT_NAME' => "ORDER_PROP_".$arProperties["ID"],
									'INPUT_VALUE' => $arProperties["VALUE"],
									'SHOW_TIME' => 'N'
								), null, array('HIDE_ICONS' => 'N'));
								?>
								<? if (strlen(trim($arProperties["DESCRIPTION"])) > 0):?>
									<span id="helpBlock" class="help-block">
										<?=$arProperties["DESCRIPTION"]?>
									</span>
								<? endif; ?>
							</div>
                            <?
						} elseif ($arProperties["TYPE"] == "FILE"){
							?>
							<div class = "form-group">
								<label class="control-label" for="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["NAME"]?></label><br>
								<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
									<span class="required">*</span>
								<?endif;?>
								<?=showFilePropertyField("ORDER_PROP_".$arProperties["ID"], $arProperties, $arProperties["VALUE"], $arProperties["SIZE1"])?>
								<? if (strlen(trim($arProperties["DESCRIPTION"])) > 0):?>
									<span id="helpBlock" class="help-block">
										<?=$arProperties["DESCRIPTION"]?>
									</span>
								<? endif; ?>
							</div>
							<?
						}
						?>

						<?if(CSaleLocation::isLocationProEnabled()):?>

							<?
							$propertyAttributes = array(
								'type' => $arProperties["TYPE"],
								'valueSource' => $arProperties['SOURCE'] == 'DEFAULT' ? 'default' : 'form' // value taken from property DEFAULT_VALUE or it`s a user-typed value?
							);

							if(intval($arProperties['IS_ALTERNATE_LOCATION_FOR']))
								$propertyAttributes['isAltLocationFor'] = intval($arProperties['IS_ALTERNATE_LOCATION_FOR']);

							if(intval($arProperties['CAN_HAVE_ALTERNATE_LOCATION']))
								$propertyAttributes['altLocationPropId'] = intval($arProperties['CAN_HAVE_ALTERNATE_LOCATION']);

							if($arProperties['IS_ZIP'] == 'Y')
								$propertyAttributes['isZip'] = true;
							?>

							<script>

								<?// add property info to have client-side control on it?>
								(window.top.BX || BX).saleOrderAjax.addPropertyDesc(<?=CUtil::PhpToJSObject(array(
									'id' => intval($arProperties["ID"]),
									'attributes' => $propertyAttributes
								))?>);

							</script>
						</div>
						<?endif?>
						<?
						if($i%2 == 1 || $countSources - 1 == $i) {
							echo '</div>';
						}
						$i++;
					}
					?>
			<?
		}
	}
}
?>