<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CModule::IncludeModule ('iblock');

//echo "<pre>Template arParams: "; print_r($arParams); echo "</pre>";
//echo "<pre>Template arResult: "; print_r($arResult); echo "</pre>";
//exit();
?>

<?$field_quickly = array('CITY_OTKUDA','CITY_KUDA','FROM', 'TO', 'FIO', 'TEL', 'COMM', 'FROM_HOUSE', 'FROM_HOUSING', 'FROM_BUILDING', 'FROM_PORCH','TO_HOUSE', 'TO_HOUSING', 'TO_BUILDING', 'TO_PORCH');?>
<?$field_small = array('FROM_HOUSE', 'FROM_HOUSING', 'FROM_BUILDING', 'FROM_PORCH' ,'TO_HOUSE', 'TO_HOUSING', 'TO_BUILDING', 'TO_PORCH');?>


<style type="text/css">
#FIELD_FROM {
	float: left;
	
}


.form-horizontal .control-label-small {
	width: auto;
	display: inline;
	text-align: left;
	float: none;
	margin-right: 5px;
}

	.form-horizontal .control-group .control-group {
		float: left;
		display: block;
		margin-left: 2.127659574468085%;
	}
@media (max-width: 767px) {   
	.form-horizontal .control-group .control-group {
        margin-left: 0;
        
    }
    .zakaz .form-horizontal .control-group .control-group {
    	margin-bottom: 13px;
    }
    .zakaz .form-horizontal .control-group.CITY_OTKUDA-group, .zakaz .form-horizontal .control-group.CITY_KUDA-group{
    	margin-bottom: 0px;
    }

    .form-horizontal .control-group .span4 {
        width: 31.9149%;
        float: left;
        margin-left: 2.127%;
    }
    .form-horizontal .control-group .span4:first-child {
        margin-left: 0;
    }

}

	.form-horizontal .control-group .control-group:first-child {
		margin-left: 0;
	}
	.form-horizontal .control-group .control-group .controls {
		margin: 0;
		display: inline;
		width: auto;
	}
	.form-horizontal .control-group .control-group .controls input{
		width: 100%;
	}
	.form-horizontal .control-group .control-group .control-label {
		width: auto;
		display: inline;
		text-align: left;
		float: none;
		margin-right: 5px;
	}
</style>

<input id="path_travel" type="hidden" value="<?=SITE_TEMPLATE_PATH?>">

<div class="span6">
    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#quick"><?=GetMessage("COLORS3_TAXI_BYSTRYY")?></a></li>
        <li><a href="#full"><?=GetMessage("COLORS3_TAXI_PODROBNYY")?></a></li>
    </ul>

    <?if (count($arResult["ERRORS"])):?>
		<?=ShowError(implode("<br />", $arResult["ERRORS"]))?>
	<?endif?>

	<?if (strlen($arResult["MESSAGE"]) > 0):?>
		<?=ShowNote($arResult["MESSAGE"])?>
	<?endif?>

    <div class="tab-content">
    	<div id="quick" class="tab-pane active">
		</div>
		<div id="full" class="tab-pane">

		</div>
    </div>	
	<form class="form-horizontal row-fluid" name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">

		<?=bitrix_sessid_post()?>

		<?if ($arParams["MAX_FILE_SIZE"] > 0):?><input type="hidden" name="MAX_FILE_SIZE" value="<?=$arParams["MAX_FILE_SIZE"]?>" /><?endif?>

			<?if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])):?>

				<?$first = true;?>
				<?foreach ($arResult["PROPERTY_LIST"] as $propertyID):?>
				<?if ( in_array($arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE'], array('FROM_HOUSE', 'TO_HOUSE'))):?>
				<div class="control-group <?=$arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE']?>-group" style="display: none">
					<div class="controls"> 
				<?endif;?>
				
				<?if ( in_array($arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE'], array('CITY_OTKUDA','CITY_KUDA'))):?>
				<div class="control-group <?=$arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE']?>-group">
					<?if ($arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE'] == 'CITY_OTKUDA'):?>
						<label class="control-label " for="FROM">
							<?=GetMessage("COLORS3_TAXI_FROM")?>																				
						</label>
					<?endif;?>
					<?if ($arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE'] == 'CITY_KUDA'):?>
						<label class="control-label " for="TO">
							<?=GetMessage("COLORS3_TAXI_TO")?>																				
						</label>
					<?endif;?>
					<div class="controls"> 
				<?endif;?>
				
					<?$small = in_array($arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE'], $field_small);?>
					
					
					<div  class="control-group  <?if (($arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE'] == 'CITY_OTKUDA') || ($arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE'] == 'CITY_KUDA')) echo 'span3';?><?if (($arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE'] == 'FROM') || ($arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE'] == 'TO')) echo 'span9';?><?=($small)?' span4 ':''?> <?if ($propertyID != 'NAME') echo in_array($arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE'], $field_quickly) ? 'quickly' : 'no_quickly'?>" style="<?=($propertyID == 'NAME' && $i==0)?'display:none;':''?>">

						<label class="control-label <?=($small)?' span4 ':''?>" for="FIELD_<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE']?>">
							<? //print_r($arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE']);?>
							<?if (intval($propertyID) > 0):?>
								<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?>
							<?else:?>
								<?=!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID)?>
							<?endif?>
														
						</label>

						<div class="controls">
							<?
							//echo "<pre>"; print_r($arResult["PROPERTY_LIST_FULL"]); echo "</pre>";
							if (intval($propertyID) > 0)
							{
								if (
									$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "T"
									&&
									$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] == "1"
								)
									$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "S";
								elseif (
									(
										$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "S"
										||
										$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "N"
									)
									&&
									$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] > "1"
								)
									$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "T";
							}
							elseif (($propertyID == "TAGS") && CModule::IncludeModule('search'))
								$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "TAGS";

							if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y")
							{
								$inputNum = ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) ? count($arResult["ELEMENT_PROPERTIES"][$propertyID]) : 0;
								$inputNum += $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE_CNT"];
							}
							else
							{
								$inputNum = 1;
							}
							if($arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"])
								$INPUT_TYPE = "USER_TYPE";
							else
								$INPUT_TYPE = $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"];
							switch ($INPUT_TYPE):
								case "USER_TYPE": 
									for ($i = 0; $i<$inputNum; $i++)
									{
										if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
										{
											$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["~VALUE"] : $arResult["ELEMENT"][$propertyID];
											$description = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["DESCRIPTION"] : "";
										}
										elseif ($i == 0)
										{
											$value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
											$description = "";
										}
										else
										{
											$value = "";
											$description = "";
										}
										if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"][0]=="CIBlockPropertyDateTime") {?>
											<input class="<?echo $first ? 'find-me-input ui-autocomplete-input' : 'zak_input_ span12';?>" type="text" name="PROPERTY[<?=$propertyID?>][<?=$i?>]" value="<?=$value?>" style="float: left" />
											<?$APPLICATION->IncludeComponent(
											'bitrix:main.calendar',
											'',
											array(
												'FORM_NAME' => 'iblock_add',
												'INPUT_NAME' => "PROPERTY[".$propertyID."][".$i."]",
												'INPUT_VALUE' => $value,
												'INPUT_CLASS' =>'zak_input_ span9',
												'SHOW_TIME' => 'Y'
											),
											null,
											array('HIDE_ICONS' => 'Y')
										);
										?>
										<?}else {
											echo call_user_func_array($arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"],
												array(
													$arResult["PROPERTY_LIST_FULL"][$propertyID],
													array(
														"VALUE" => $value,
														"DESCRIPTION" => $description,
													),
													array(
														"VALUE" => "PROPERTY[".$propertyID."][".$i."][VALUE]",
														"DESCRIPTION" => "PROPERTY[".$propertyID."][".$i."][DESCRIPTION]",
														"FORM_NAME"=>"iblock_add",
													),
												));
													
										}
									?>
									<?
									}
									
								break;
								case "TAGS":
									$APPLICATION->IncludeComponent(
										"bitrix:search.tags.input",
										"",
										array(
											"VALUE" => $arResult["ELEMENT"][$propertyID],
											"NAME" => "PROPERTY[".$propertyID."][0]",
											"TEXT" => 'size="'.$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"].'"',
										), null, array("HIDE_ICONS"=>"Y")
									);
									break;
								case "HTML":
									$LHE = new CLightHTMLEditor;
									$LHE->Show(array(
										'id' => preg_replace("/[^a-z0-9]/i", '', "PROPERTY[".$propertyID."][0]"),
										'width' => '100%',
										'height' => '100px',
										'inputName' => "PROPERTY[".$propertyID."][0]",
										'content' => $arResult["ELEMENT"][$propertyID],
										'bUseFileDialogs' => false,
										'bFloatingToolbar' => false,
										'bArisingToolbar' => false,
										'toolbarConfig' => array(
											'Bold', 'Italic', 'Underline', 'RemoveFormat',
											'CreateLink', 'DeleteLink', 'Image', 'Video',
											'BackColor', 'ForeColor',
											'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyFull',
											'InsertOrderedList', 'InsertUnorderedList', 'Outdent', 'Indent',
											'StyleList', 'HeaderList',
											'FontList', 'FontSizeList',
										), 
									));
									break;
								//case "E":?>
																											
									<?//break;
								case "T":
									for ($i = 0; $i<$inputNum; $i++)
									{

										if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
										{
											$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
										}
										elseif ($i == 0)
										{
											$value = intval($propertyID) > 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
										}
										else
										{
											$value = "";
										}
									?>
												            
					            	<textarea id="FIELD_<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE']?>" class="zak_input_ span12" cols="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"]?>" rows="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"]?>" name="PROPERTY[<?=$propertyID?>][<?=$i?>]"><?=$value?></textarea>
												      
									<?
									}
								break;

								case "S":
								case "N":
									for ($i = 0; $i<$inputNum; $i++)
									{
										if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
										{
											$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
										}
										elseif ($i == 0)
										{
											$value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];

										}
										else
										{
											$value = "";
										}
									?>
																
									<?if ($arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE'] == 'TIP'):
										$db_city = CIBlockElement::GetList(false, array('IBLOCK_CODE'=>'tarify', 'ACTIVE'=>'Y'), false, false, array('ID', 'IBLOCK', 'NAME', 'PROPERTY_KM_VKL_POSAD', 'PROPERTY_PRICE_KM_CITY', 'PROPERTY_PRICE_POSADKA_CITY', 'PROPERTY_MIN_PRICE_CITY'));
											?><select class="span12" id="tariff_travel" name="PROPERTY[<?=$propertyID?>][<?=$i?>]"><?
											while ($city = $db_city->GetNext()){
												?>
												
												<option value="<?=$city['ID'];?>" data-minpricecity="<?=$city['PROPERTY_MIN_PRICE_CITY_VALUE'];?>" data-included="<?=$city['PROPERTY_KM_VKL_POSAD_VALUE'];?>" data-mileage="<?=$city['PROPERTY_PRICE_KM_CITY_VALUE'];?>" data-landing="<?=$city['PROPERTY_PRICE_POSADKA_CITY_VALUE'];?>"><?=$city['NAME'];?></option>
												<?
											}
											?></select>
									<?else:?>
										<input id="FIELD_<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE']?>" class="<?echo $first ? 'find-me-input ui-autocomplete-input span9' : 'zak_input_ span12';?>  <?=($small)?' span8 ':''?>" type="text" name="PROPERTY[<?=$propertyID?>][<?=$i?>]" value="<?=($propertyID == 'NAME' && $i==0) ? GetMessage("COLORS3_TAXI_ZAKAZ_S_SAYTA") : $value?>" />
									<?endif;?>
									
									
									<?
									if($arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"] == "DateTime"):?>
										<?$APPLICATION->IncludeComponent(
											'bitrix:main.calendar',
											'calendar',
											array(
												'FORM_NAME' => 'iblock_add',
												'INPUT_NAME' => "PROPERTY[".$propertyID."][".$i."]",
												'INPUT_VALUE' => $value,
												'INPUT_CLASS' =>'zak_input span9',
											),
											null,
											array('HIDE_ICONS' => 'Y')
										);
										?>
										<small><?=GetMessage("IBLOCK_FORM_DATE_FORMAT")?><?=FORMAT_DATETIME?></small>
									<?endif;?>
									<?
									}
								break;

								case "F":
									for ($i = 0; $i<$inputNum; $i++)
									{
										$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
										?>
							<input class="zak_input" type="hidden" name="PROPERTY[<?=$propertyID?>][<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>]" value="<?=$value?>" />
							<input class="zak_input" type="file" size="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"]?>"  name="PROPERTY_FILE_<?=$propertyID?>_<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>" />
										<?

										if (!empty($value) && is_array($arResult["ELEMENT_FILES"][$value]))
										{
											?>
						
						<label for="file_delete_<?=$propertyID?>_<?=$i?>"><?=GetMessage("IBLOCK_FORM_FILE_DELETE")?>
							<input type="checkbox" name="DELETE_FILE[<?=$propertyID?>][<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>]" id="file_delete_<?=$propertyID?>_<?=$i?>" value="Y" />
						</label>
											<?

											if ($arResult["ELEMENT_FILES"][$value]["IS_IMAGE"])
											{
												?>
						<img src="<?=$arResult["ELEMENT_FILES"][$value]["SRC"]?>" height="<?=$arResult["ELEMENT_FILES"][$value]["HEIGHT"]?>" width="<?=$arResult["ELEMENT_FILES"][$value]["WIDTH"]?>" border="0" />
												<?
											}
											else
											{
												?>
						<?=GetMessage("IBLOCK_FORM_FILE_NAME")?>: <?=$arResult["ELEMENT_FILES"][$value]["ORIGINAL_NAME"]?>
						<?=GetMessage("IBLOCK_FORM_FILE_SIZE")?>: <?=$arResult["ELEMENT_FILES"][$value]["FILE_SIZE"]?>
						[<a href="<?=$arResult["ELEMENT_FILES"][$value]["SRC"]?>"><?=GetMessage("IBLOCK_FORM_FILE_DOWNLOAD")?></a>]
												<?
											}
										}
									}

								break;
								case "L":

									if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["LIST_TYPE"] == "C")
										$type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "checkbox" : "radio";
									else
										$type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "multiselect" : "dropdown";

									switch ($type):
										case "checkbox":
										case "radio":

											//echo "<pre>"; print_r($arResult["PROPERTY_LIST_FULL"][$propertyID]); echo "</pre>";

											foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
											{
												$checked = false;
												if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
												{
													if (is_array($arResult["ELEMENT_PROPERTIES"][$propertyID]))
													{
														foreach ($arResult["ELEMENT_PROPERTIES"][$propertyID] as $arElEnum)
														{
															if ($arElEnum["VALUE"] == $key) {$checked = true; break;}
														}
													}
												}
												else
												{
													if ($arEnum["DEF"] == "Y") $checked = true;
												}

												?>
								
								<label class="checkbox inline" for="property_<?=$key?>">
									<input class="dop_input" data-cost="<?=$arResult['DOP_COST'][$arEnum['XML_ID']]?>" type="<?=$type?>" name="PROPERTY[<?=$propertyID?>]<?=$type == "checkbox" ? "[".$key."]" : ""?>" value="<?=$key?>" id="property_<?=$key?>"<?=$checked ? " checked=\"checked\"" : ""?> />
									<?=$arEnum["VALUE"]?>
								</label>
												<?
											}
										break;

										case "dropdown":
										case "multiselect":
										?>
								<select class="styled zak_input_ span12" name="PROPERTY[<?=$propertyID?>]<?=$type=="multiselect" ? "[]\" size=\"".$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"]."\" multiple=\"multiple" : ""?>">
										<?
											if (intval($propertyID) > 0) $sKey = "ELEMENT_PROPERTIES";
											else $sKey = "ELEMENT";

											foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
											{
												$checked = false;
												if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
												{
													foreach ($arResult[$sKey][$propertyID] as $elKey => $arElEnum)
													{
														if ($key == $arElEnum["VALUE"]) {$checked = true; break;}
													}
												}
												else
												{
													if ($arEnum["DEF"] == "Y") $checked = true;
												}
												?>
									<option value="<?=$key?>" <?=$checked ? " selected=\"selected\"" : ""?>><?=$arEnum["VALUE"]?></option>
												<?
											}
										?>
								</select>
										<?
										break;

									endswitch;
								break;
							endswitch;?>
						<?if ($propertyID != 'NAME' && $first):?>								
								<!--a href="#" id="find-me" class="find-me" style="cursor: pointer; color: rgb(0, 136, 204); border-bottom-width: 1px; border-bottom-style: dashed; border-bottom-color: rgb(0, 136, 204);">Найти меня</a-->
							<?$first = false;
						endif;?>
						<?if ($arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE'] == 'FROM'):?>
							<a href="#" id="find-me" class="find-me" style="cursor: pointer;float: right;margin:5px 5px 0 -24px;"><img alt="find me" src="/target.png" /></a>
						<?endif;?>	
						</div>						
					</div>

				<?if (in_array($arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE'], array('FROM_PORCH', 'TO_PORCH'))):?>
						</div>						
					</div>
				<?endif;?>
				<?if (in_array($arResult["PROPERTY_LIST_FULL"][$propertyID]['CODE'], array('FROM', 'TO'))):?>
						</div>						
					</div>
				<?endif;?>
				<?endforeach;?>

				<?if($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0):?>
					<table>
						<tr>
							<td><?=GetMessage("IBLOCK_FORM_CAPTCHA_TITLE")?></td>
							<td>
								<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
								<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
							</td>
						</tr>
						<tr>
							<td><?=GetMessage("IBLOCK_FORM_CAPTCHA_PROMPT")?><span class="starrequired">*</span>:</td>
							<td><input type="text" name="captcha_word" maxlength="50" value=""></td>
						</tr>
					</table>	
				<?endif?>
				

			<?endif?>

			<div id="list"></div>
        
	        <div class="control-group knopka">
	            <div class="controls submit_controls">					
	            	<button style="display: none;" class="btn rel call_me" type="button" name="iblock_submit"><?=GetMessage("COLORS3_TAXI_ZAKAZATQ")?><span></span></button>					
	                <input class="btn rel call_me" type="submit" name="iblock_submit" value="<?=GetMessage("COLORS3_TAXI_ZAKAZATQ")?>" />					

					<?if (strlen($arParams["LIST_URL"]) > 0 && $arParams["ID"] > 0):?><input type="submit" name="iblock_apply" value="<?=GetMessage("IBLOCK_FORM_APPLY")?>" /><?endif?>
					<?/*<input type="reset" value="<?=GetMessage("IBLOCK_FORM_RESET")?>" />*/?>
	            </div>
	        </div>
						
		
		<?if (strlen($arParams["LIST_URL"]) > 0):?><a href="<?=$arParams["LIST_URL"]?>"><?=GetMessage("IBLOCK_FORM_BACK")?></a><?endif?>
	</form>
</div>