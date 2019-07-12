<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><div class="profildetail clearfix"><?
	
	if(strlen($arResult['ID'])>0) {
		
		?><?=ShowError($arResult['ERROR_MESSAGE'])?><?
		
		?><form method="post" action="<?=POST_FORM_ACTION_URI?>"><?
			?><?=bitrix_sessid_post()?><?
			?><input type="hidden" name="ID" value="<?=$arResult['ID']?>"><?
			
			?><div class="table"><?
				
				?><div class="full name"><?
					?><h2><?=str_replace('#ID#',$arResult['ID'],GetMessage('SPPD_PROFILE_NO'))?> &mdash; <?=$arResult['NAME']?></h2><?
				?></div><?
				
				?><div class="tr"><?
					?><div class="td name"><?=GetMessage('SALE_PERS_TYPE')?>:</div><?
					?><div class="td"><?=$arResult['PERSON_TYPE']['NAME']?></div><?
				?></div><?
				
				?><div class="tr"><?
					?><div class="td name"><?=GetMessage('SALE_PNAME')?>:<span class="req">*</span></div><?
					?><div class="td"><input type="text" name="NAME" value="<?=$arResult['NAME']?>"></div><?
				?></div><?
				
				foreach($arResult['ORDER_PROPS'] as $val) {
					if(!empty($val['PROPS'])) {
						?><div class="full header"><?=$val['NAME']?></div><?
						
						foreach($val['PROPS'] as $vval) {
							$currentValue = $arResult['ORDER_PROPS_VALUES']['ORDER_PROP_'.$vval['ID']];
							$name = 'ORDER_PROP_'.$vval['ID'];
							
							?><div class="tr"><?
								?><div class="td name"><?=$vval['NAME']?>:<?if($vval['REQUIED']=='Y'):?><span class="req">*</span><?endif;?></div><?
								?><div class="td"><?
									if($vval['TYPE']=='CHECKBOX'){
										?><input type="hidden" name="<?=$name?>" value=""><?
										?><input type="checkbox" name="<?=$name?>" id="<?=$name?>" value="Y"<?if($currentValue=='Y' || !isset($currentValue) && $vval['DEFAULT_VALUE']=='Y') echo ' checked';?> /><label for="<?=$name?>"></label><?
									} elseif ($vval['TYPE']=='TEXT') {
										?><input type="text" maxlength="250" value="<?=( isset($currentValue)?$currentValue:$vval['DEFAULT_VALUE'])?>" name="<?=$name?>"><?
									} elseif ($vval['TYPE']=='SELECT') {
										?><select name="<?=$name?>"><?
											foreach($vval['VALUES'] as $vvval)
											{
												?><option value="<?=$vvval['VALUE']?>"<?if($vvval['VALUE']==$currentValue || !isset($currentValue) && $vvval['VALUE']==$vval['DEFAULT_VALUE']) echo ' selected'?>><?=$vvval['NAME']?></option><?
											}
										?></select><?
									} elseif ($vval['TYPE']=='MULTISELECT') {
										?><select multiple name="<?=$name?>[]"><?
											$arCurVal = array();
											$arCurVal = explode(',', $currentValue);
											for($i = 0; $i<count($arCurVal); $i++) {
												$arCurVal[$i] = Trim($arCurVal[$i]);
											}
											$arDefVal = explode(',', $vval['DEFAULT_VALUE']);
											for($i = 0; $i<count($arDefVal); $i++) {
												$arDefVal[$i] = Trim($arDefVal[$i]);
											}
											foreach($vval['VALUES'] as $vvval) {
												?><option value="<?=$vvval['VALUE']?>"<?if (in_array($vvval['VALUE'], $arCurVal) || !isset($currentValue) && in_array($vvval['VALUE'], $arDefVal)) echo' selected'?>><?=$vvval['NAME']?></option><?
											}
										?></select><?
									} elseif ($vval['TYPE']=='TEXTAREA') {
										?><textarea rows="<?=((IntVal($vval['SIZE2'])>0)?$vval['SIZE2']:4)?>" name="<?=$name?>"><?=(isset($currentValue)?$currentValue:$vval['DEFAULT_VALUE'])?></textarea><?
									} elseif ($vval['TYPE']=='LOCATION') {
										if($arParams['USE_AJAX_LOCATIONS']=='Y') {
											if(!CSaleLocation::isLocationProEnabled()) {
												$locationTemplate = ( $locationTemplate=='popup'?'popup':'gopro' );
											} else {
												$locationTemplate = 'search';
											}
											$locationValue = intval($currentValue) ? $currentValue : $vval["DEFAULT_VALUE"];
											CSaleLocation::proxySaleAjaxLocationsComponent(
												array(
													"AJAX_CALL" => "N",
													'CITY_OUT_LOCATION' => 'Y',
													'COUNTRY_INPUT_NAME' => $name.'_COUNTRY',
													'CITY_INPUT_NAME' => $name,
													'LOCATION_VALUE' => $locationValue,
												),
												array(
												),
												$locationTemplate,
												true,
												'location-block-wrapper'
											);
										} else {
											?><select name="<?=$name?>"><?
											foreach($vval['VALUES'] as $vvval) {
												?><option value="<?=$vvval['ID']?>"<?if(IntVal($vvval['ID'])==IntVal($currentValue) || !isset($currentValue) && IntVal($vvval['ID'])==IntVal($vval['DEFAULT_VALUE'])) echo ' selected'?>><?=$vvval['COUNTRY_NAME'].' - '.$vvval['CITY_NAME']?></option><?
											}
										?></select><?
										}
									} elseif ($vval['TYPE']=='RADIO') {
										foreach($vval['VALUES'] as $vvval)
										{
											?><input type="radio" name="<?=$name?>" id="<?=$name?>" value="<?=$vvval['VALUE']?>"<?if($vvval['VALUE']==$currentValue || !isset($currentValue) && $vvval['VALUE']==$vval['DEFAULT_VALUE']) echo ' checked'?>><label for="<?=$name?>"><?=$vvval['NAME']?></label><br /><?
										}
									}
									if(strlen($vval['DESCRIPTION'])>0) {
										?><div class="description"><?=$vval['DESCRIPTION']?></div><?
									}
								?></div><?
							?></div><?
						}
					}
				}
				
				?><div class="full"><?
					?><input type="submit" name="save" value="<?=GetMessage('SALE_SAVE')?>" /> &nbsp; <?
					?><a href="<?=$arParams['PATH_TO_LIST']?>"><?=GetMessage('SALE_RESET')?></a><?
				?></div><?
				
			?></div><?
			
		?></form><?
		
	} else {
		?><?=ShowError($arResult['ERROR_MESSAGE']);?><?
	}
	
?></div><?

?><br /><br /><a class="fullback" href="<?=$arParams['PATH_TO_LIST']?>"><i class="icon pngicons"></i><?=GetMessage('SPPD_RECORDS_LIST')?></a>