<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<script src="http://code.jquery.com/jquery-1.9.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function($){
	
var button = $('button[name=iblock_submit]');
    var submit = $('input[name=iblock_submit]');
    button.toggle();
    submit.toggle();
    button.on('click', function(){submit.click()});
})
</script>
<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700|PT+Sans+Narrow:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/bootstrap/css/bootstrap-responsive.min.css">
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/style.css">
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/thm_#COLOR#.css">


<style type="text/css">
	body {
		background: #E4E4E4;
		margin: 0;
	}
	body font.notetext {
		color: #000000;
	}
</style>

<div id="myModal" class="form_call_me" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="display: block;">
	<div class="modal-header">
		<button onclick="parent.$.colorbox.close(); return false;" type="button" class="close" data-dismiss="modal" aria-hidden="true">?</button>		
		<h1 id="myModalLabel"><?=GetMessage("COLORS3_TAXI_PEREZVONITE_MNE")?></h1>
		<?if (count($arResult["ERRORS"])):?>
			<?=ShowError(implode("<br />", $arResult["ERRORS"]))?>
		<?endif?>
		<?if (strlen($arResult["MESSAGE"]) > 0):?>
			<?=ShowNote($arResult["MESSAGE"])?>
		<?endif?>
	</div>
	
	<div class="modal-body">

		
		<form class="form-horizontal" name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">

			<?=bitrix_sessid_post()?>

			<?if ($arParams["MAX_FILE_SIZE"] > 0):?><input type="hidden" name="MAX_FILE_SIZE" value="<?=$arParams["MAX_FILE_SIZE"]?>" /><?endif?>

			

				<?if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])):?>

					<?foreach ($arResult["PROPERTY_LIST"] as $propertyID):?>
					
						<div class="control-group" style="<?=($propertyID == 'NAME' && $i==0)?'display:none;':''?>">
							<label class="control-label"><?if (intval($propertyID) > 0):?><?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?><?else:?><?=!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID)?><?endif?><?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?><span class="starrequired">*</span><?endif?></label>
							
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
											'height' => '200px',
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
								<textarea cols="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"]?>" rows="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"]?>" name="PROPERTY[<?=$propertyID?>][<?=$i?>]"><?=$value?></textarea>
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

										<input class="zak_input" type="text" name="PROPERTY[<?=$propertyID?>][<?=$i?>]" value="<?=($propertyID == 'NAME' && $i==0)?GetMessage("COLORS3_TAXI_ZAKAZ_ZVONKA"):$value?>" />
										
										<?
										if($arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"] == "DateTime"):?><?
											$APPLICATION->IncludeComponent(
												'bitrix:main.calendar',
												'',
												array(
													'FORM_NAME' => 'iblock_add',
													'INPUT_NAME' => "PROPERTY[".$propertyID."][".$i."]",
													'INPUT_VALUE' => $value,
												),
												null,
												array('HIDE_ICONS' => 'Y')
											);
											?>
											<small><?=GetMessage("IBLOCK_FORM_DATE_FORMAT")?><?=FORMAT_DATETIME?></small><?
										endif
										?>
										<?
										}
									break;

									case "F":
										for ($i = 0; $i<$inputNum; $i++)
										{
											$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
											?>
								<input type="hidden" name="PROPERTY[<?=$propertyID?>][<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>]" value="<?=$value?>" />
								<input type="file" size="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"]?>"  name="PROPERTY_FILE_<?=$propertyID?>_<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>" />
											<?

											if (!empty($value) && is_array($arResult["ELEMENT_FILES"][$value]))
											{
												?>
							<input type="checkbox" name="DELETE_FILE[<?=$propertyID?>][<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>]" id="file_delete_<?=$propertyID?>_<?=$i?>" value="Y" /><label for="file_delete_<?=$propertyID?>_<?=$i?>"><?=GetMessage("IBLOCK_FORM_FILE_DELETE")?></label>
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
							<?=GetMessage("IBLOCK_FORM_FILE_SIZE")?>: <?=$arResult["ELEMENT_FILES"][$value]["FILE_SIZE"]?> b
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
									<input type="<?=$type?>" name="PROPERTY[<?=$propertyID?>]<?=$type == "checkbox" ? "[".$key."]" : ""?>" value="<?=$key?>" id="property_<?=$key?>"<?=$checked ? " checked=\"checked\"" : ""?> /><label for="property_<?=$key?>"><?=$arEnum["VALUE"]?></label>
													<?
												}
											break;

											case "dropdown":
											case "multiselect":
											?>
									<select name="PROPERTY[<?=$propertyID?>]<?=$type=="multiselect" ? "[]\" size=\"".$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"]."\" multiple=\"multiple" : ""?>">
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
							</div>
						</div>
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
						
				<input class="btn rel call_me" type="submit" name="iblock_submit" value="<?=GetMessage("COLORS3_TAXI_OTPRAVITQ")?>" />

				<?if (strlen($arParams["LIST_URL"]) > 0 && $arParams["ID"] > 0):?><input type="submit" name="iblock_apply" value="<?=GetMessage("IBLOCK_FORM_APPLY")?>" /><?endif?>
				<?/*<input type="reset" value="<?=GetMessage("IBLOCK_FORM_RESET")?>" />*/?>				
				
		</form>
	</div>
	<div class="modal-footer">	
		

			
		<button style="display: none;" class="btn rel call_me" type="button" name="iblock_submit"><?=GetMessage("COLORS3_TAXI_OTPRAVITQ")?></button>					
	    				
		

	</div>

</div>