<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!function_exists('showFilePropertyField')) {
	function showFilePropertyField($name, $property_fields, $values, $max_file_size_show=50000) {
		$res = "";

		if (!is_array($values) || empty($values))
			$values = array(
				'n0' => 0,
			);

		if($property_fields['MULTIPLE']=='N') {
			$res = "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[0]\" id=\"".$name."[0]\"></label>";
		} else {
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

if (!function_exists('thisIsPhoneInput')) {
	function thisIsPhoneInput($code='', $arParams=array()) {
		$return = false;
		if( is_array($arParams['CODE_PHONES']) && count($arParams['CODE_PHONES'])>0 ) {
			foreach($arParams['CODE_PHONES'] as $pCode) {
				if($pCode==$code) {
					$return = true;
					break;
				}
			}
		}
		return $return;
	}
}

if (!function_exists('PrintPropsForm')) {
	function PrintPropsForm($arSource=array(), $locationTemplate='gopro', $RSDETECTED_LOCATION_VALUE=0,$arParams=array()) {
		if (!empty($arSource)) {
			?><div class="clearfix"><?
				$index = 0;
				foreach($arSource as $arProperties) {
					if(CSaleLocation::isLocationProMigrated()){
						$propertyAttributes = array(
							'type' => $arProperties['TYPE'],
							'valueSource' => $arProperties['SOURCE'] == 'DEFAULT' ? 'default' : 'form'
						);
						if(intval($arProperties['IS_ALTERNATE_LOCATION_FOR']))
							$propertyAttributes['isAltLocationFor'] = intval($arProperties['IS_ALTERNATE_LOCATION_FOR']);
						if(intval($arProperties['INPUT_FIELD_LOCATION']))
							$propertyAttributes['altLocationPropId'] = intval($arProperties['INPUT_FIELD_LOCATION']);
						if($arProperties['IS_ZIP'] == 'Y')
							$propertyAttributes['isZip'] = true;
					}
					if($index%2==0) {
						?><div class="separator"></div><?
					}
					$index++;
					if($arProperties['TYPE']=='CHECKBOX') {
						?><div class="line f_checkbox" data-type="CHECKBOX"><?
							?><input type="hidden" name="<?=$arProperties["FIELD_NAME"]?>" value="" /><?
							?><div class="vl"><?
								?><input type="checkbox" name="<?=$arProperties['FIELD_NAME']?>" id="<?=$arProperties['FIELD_NAME']?>" value="Y"<?if($arProperties['CHECKED']=='Y') echo ' checked';?> /><?
								?><label for="<?=$arProperties['FIELD_NAME']?>"><?=$arProperties['NAME']?><?=($arProperties['REQUIED_FORMATED']=='Y'?'<span class="required">*</span>':'')?></label><?
								if(strlen(trim($arProperties['DESCRIPTION']))>0) {
									?><div class="description"><div class="arrow pngicons"></div><div class="in"><?=$arProperties['DESCRIPTION']?></div></div><?
								}
							?></div><?
						?></div><?
					} elseif ($arProperties['TYPE']=='TEXT') {
						$isPhoneInput = thisIsPhoneInput($arProperties['CODE'],$arParams);
						?><div class="line f_text" data-type="TEXT"><?
							?><div class="nm"><?
								?><?=$arProperties['NAME']?><?
								if($arProperties['REQUIED_FORMATED']=='Y') {
									?><span class="required">*</span><?
								}
							?></div><?
							?><div class="vl"><?
								?><input<?if($isPhoneInput):?> class="maskPhone"<?endif;?> type="text" maxlength="250" value="<?=$arProperties['VALUE']?>" name="<?=$arProperties['FIELD_NAME']?>" id="<?=$arProperties['FIELD_NAME']?>" placeholder="<?=$arProperties['NAME']?><?=($arProperties['REQUIED_FORMATED']=='Y'?'*':'')?>" /><?
								if(strlen(trim($arProperties['DESCRIPTION']))>0) {
									?><div class="description"><div class="arrow pngicons"></div><div class="in"><?=$arProperties['DESCRIPTION']?></div></div><?
								}
							?></div><?
						?></div><?
					} elseif ($arProperties['TYPE']=='SELECT') {
						?><div class="line f_select" data-type="SELECT"><?
							?><div class="nm"><?
								?><?=$arProperties['NAME']?><?
								if($arProperties['REQUIED_FORMATED']=='Y') {
									?><span class="required">*</span><?
								}
							?></div><?
							?><div class="vl"><?
								?><select name="<?=$arProperties['FIELD_NAME']?>" id="<?=$arProperties['FIELD_NAME']?>"><?
									foreach($arProperties['VARIANTS'] as $arVariants) {
										?><option value="<?=$arVariants['VALUE']?>"<?if($arVariants['SELECTED']=='Y') echo ' selected';?>><?=$arVariants['NAME']?></option><?
									}
								?></select><?
								if(strlen(trim($arProperties['DESCRIPTION']))>0) {
									?><div class="description"><div class="arrow pngicons"></div><div class="in"><?=$arProperties['DESCRIPTION']?></div></div><?
								}
							?></div><?
						?></div><?
					} elseif ($arProperties['TYPE']=='MULTISELECT') {
						?><div class="line f_multiselect" data-type="MULTISELECT"><?
							?><div class="nm"><?
								?><?=$arProperties['NAME']?><?
								if($arProperties['REQUIED_FORMATED']=='Y') {
									?><span class="required">*</span><?
								}
							?></div><?
							?><div class="vl"><?
								?><select multiple name="<?=$arProperties['FIELD_NAME']?>" id="<?=$arProperties['FIELD_NAME']?>"><?
									foreach($arProperties['VARIANTS'] as $arVariants) {
										?><option value="<?=$arVariants['VALUE']?>"<?if($arVariants['SELECTED']=='Y') echo ' selected';?>><?=$arVariants['NAME']?></option><?
									}
								?></select><?
								if(strlen(trim($arProperties['DESCRIPTION']))>0) {
									?><div class="description"><div class="arrow pngicons"></div><div class="in"><?=$arProperties['DESCRIPTION']?></div></div><?
								}
							?></div><?
						?></div><?
					} elseif ($arProperties['TYPE']=='TEXTAREA') {
						?><div class="line f_textarea" data-type="TEXTAREA"><?
							$rows = ($arProperties['SIZE2']>10) ? 4 : $arProperties['SIZE2'];
							?><div class="nm"><?
								?><?=$arProperties['NAME']?><?
								if($arProperties['REQUIED_FORMATED']=='Y') {
									?><span class="required">*</span><?
								}
							?></div><?
							?><div class="vl"><?
								?><textarea rows="<?=$rows?>" <?
									?>name="<?=$arProperties['FIELD_NAME']?>" <?
									?>id="<?=$arProperties['FIELD_NAME']?>" <?
									?>placeholder="<?=$arProperties['NAME']?><?=($arProperties['REQUIED_FORMATED']=='Y'?'*':'')?>" <?
									?>><?=$arProperties['VALUE']?></textarea><?
								if(strlen(trim($arProperties['DESCRIPTION']))>0) {
									?><div class="description"><div class="arrow pngicons"></div><div class="in"><?=$arProperties['DESCRIPTION']?></div></div><?
								}
							?></div><?
						?></div><?
					} elseif ($arProperties['TYPE']=='LOCATION') {
						?><div class="line f_location" data-type="LOCATION"><?
							$value = 0;
							if(is_array($arProperties['VARIANTS']) && count($arProperties['VARIANTS'])>0) {
								foreach($arProperties['VARIANTS'] as $arVariant) {
									if($arVariant['SELECTED']=='Y') {
										$value = $arVariant['ID'];
										break;
									}
								}
							}
							if( IntVal($value)<1 && IntVal($RSDETECTED_LOCATION_VALUE)>0 ) {
								$value = $RSDETECTED_LOCATION_VALUE;
								if(CSaleLocation::isLocationProEnabled() && $arProperties['VALUE']<1){
									$arProperties['VALUE'] = $value;
								}
							}
							?><div class="nm"><?
								?><?=$arProperties['NAME']?><?
								if($arProperties['REQUIED_FORMATED']=='Y') {
									?><span class="required">*</span><?
								}
							?></div><?
							?><div class="vl"><?
								if(!CSaleLocation::isLocationProEnabled()) {
									$locationTemplate = ( $locationTemplate=='popup'?'popup':'gopro' );
								}
								CSaleLocation::proxySaleAjaxLocationsComponent(array(
										'AJAX_CALL' => 'N',
										'COUNTRY_INPUT_NAME' => 'COUNTRY',
										'REGION_INPUT_NAME' => 'REGION',
										'CITY_INPUT_NAME' => $arProperties['FIELD_NAME'],
										'CITY_OUT_LOCATION' => 'Y',
										'LOCATION_VALUE' => $value,
										'ORDER_PROPS_ID' => $arProperties['ID'],
										'ONCITYCHANGE' => ($arProperties['IS_LOCATION'] == 'Y' || $arProperties['IS_LOCATION4TAX'] == 'Y') ? 'submitForm()' : '',
										'SIZE1' => $arProperties['SIZE1'],
										'REQUIED_FORMATED' => $arProperties['REQUIED_FORMATED'],
										'PROPERTY_NAME' => $arProperties['NAME'],
									),
									array(
										'ID' => $arProperties['VALUE'],
										'CODE' => '',
										'SHOW_DEFAULT_LOCATIONS' => 'Y',

										// function called on each location change caused by user or by program
										// it may be replaced with global component dispatch mechanism coming soon
										'JS_CALLBACK' => 'submitFormProxy', //($arProperties['IS_LOCATION'] == 'Y' || $arProperties['IS_LOCATION4TAX'] == 'Y') ? 'submitFormProxy' : '',
										
										// function window.BX.locationsDeferred['X'] will be created and lately called on each form re-draw.
										// it may be removed when sale.order.ajax will use real ajax form posting with BX.ProcessHTML() and other stuff instead of just simple iframe transfer
										'JS_CONTROL_DEFERRED_INIT' => intval($arProperties['ID']),

										// an instance of this control will be placed to window.BX.locationSelectors['X'] and lately will be available from everywhere
										// it may be replaced with global component dispatch mechanism coming soon
										'JS_CONTROL_GLOBAL_ID' => intval($arProperties['ID']),

										'DISABLE_KEYBOARD_INPUT' => 'Y',
										
										'REQUIED_FORMATED' => $arProperties['REQUIED_FORMATED'],
										'PROPERTY_NAME' => $arProperties['NAME'],
									),
									$_REQUEST['PERMANENT_MODE_STEPS'] == 1 ? 'steps' : $locationTemplate,
									true
								);
								if(strlen(trim($arProperties['DESCRIPTION']))>0) {
									?><div class="description"><div class="arrow pngicons"></div><div class="in"><?=$arProperties['DESCRIPTION']?></div></div><?
								}
							?></div><?
						?></div><?
					} elseif ($arProperties['TYPE']=='RADIO') {
						?><div class="line f_radio" data-type="RADIO"><?
							?><div class="nm"><?
								?><?=$arProperties['NAME']?><?
								if($arProperties['REQUIED_FORMATED']=='Y') {
									?><span class="required">*</span><?
								}
							?></div><?
							?><div class="vl"><?
								if(is_array($arProperties['VARIANTS'])) {
									foreach($arProperties['VARIANTS'] as $arVariants) {
										?><input <?
											?>type="radio" <?
											?>name="<?=$arProperties['FIELD_NAME']?>" <?
											?>id="<?=$arProperties['FIELD_NAME']?>_<?=$arVariants['VALUE']?>" <?
											?>value="<?=$arVariants['VALUE']?>" <?if($arVariants['CHECKED']=='Y') echo ' checked';?> /><?
										?><label for="<?=$arProperties['FIELD_NAME']?>_<?=$arVariants['VALUE']?>"><?=$arVariants['NAME']?></label><?
									}
								}
								if(strlen(trim($arProperties['DESCRIPTION']))>0) {
									?><div class="description"><div class="arrow pngicons"></div><div class="in"><?=$arProperties['DESCRIPTION']?></div></div><?
								}
							?></div><?
						?></div><?
					} elseif ($arProperties['TYPE']=='FILE') {
						?><div class="line f_file" data-type="FILE"><?
							?><div class="nm"><?
								?><?=$arProperties['NAME']?><?
								if($arProperties['REQUIED_FORMATED']=='Y') {
									?><span class="required">*</span><?
								}
							?></div><?
							?><div class="vl"><?
								?><?=showFilePropertyField('ORDER_PROP_'.$arProperties['ID'], $arProperties, $arProperties['VALUE'], '')?><?
								if(strlen(trim($arProperties['DESCRIPTION']))>0) {
									?><div class="description"><div class="arrow pngicons"></div><div class="in"><?=$arProperties['DESCRIPTION']?></div></div><?
								}
							?></div><?
						?></div><?
					}
					if(CSaleLocation::isLocationProEnabled()){
						?><script>
							(window.top.BX || BX).saleOrderAjax.addPropertyDesc(<?=CUtil::PhpToJSObject(array(
								'id' => intval($arProperties['ID']),
								'attributes' => $propertyAttributes
							))?>);
						</script><?
					}
				}
			?></div><?
		}
	}
}