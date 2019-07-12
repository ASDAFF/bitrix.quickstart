<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form id="<?=$arResult["FORM_ID"]?>" name="<?=$arResult["FORM_ID"]?>" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data" class="ciee-form">
<?
if (!empty($arResult["ERRORS"])):
	if(strlen($arParams["ERROR_LIST_MESSAGE"]) > 0)
	{
		ShowNote($arParams["ERROR_LIST_MESSAGE"]);
	}
	ShowError($arResult["ERRORS"]);
endif;

if (!empty($arResult["MESSAGE"]))
	echo '<p style="color: #0a0;">' . htmlspecialchars_decode($arResult["MESSAGE"]) . '</p>';

echo bitrix_sessid_post();
echo '<input type="hidden" name="ciee_hash" value="' . $arResult['FORM_HASH'] . '">';
foreach($arResult["ITEMS"] as $code => $fieldInfo):

	?><div class="ciee-field ciee-field-<?=strtolower($fieldInfo['PROPERTY_TYPE'])?> ciee-<?=strtolower($code)?>"><?

	$inputNum = 1;
	if ($fieldInfo['MULTIPLE'] == "Y"
		&& $fieldInfo['PROPERTY_TYPE'] != 'L'
		&& $fieldInfo['PROPERTY_TYPE'] != 'E'
		&& $fieldInfo['PROPERTY_TYPE'] != 'G'
	) {
		$inputNum += $fieldInfo["MULTIPLE_CNT"];
	}

	$name = ($fieldInfo['MULTIPLE'] == "N" && $fieldInfo['PROPERTY_TYPE'] != "F" ? "PROPERTY[" . $code . "]" : "PROPERTY[" . $code . "][]");

	?><label for="<?=$code?>" class="ciee-field-title"><?
	echo $fieldInfo['NAME'] . ':';
		if($fieldInfo['IS_REQUIRED'] == "Y")
	{
		?><span class="starrequired ciee-field-required">*</span><?
	}
	?></label><?
	
	?><span class="ciee-field-input"><?
	
	for($i = 0;$i < $inputNum;$i++)
	{
		$value = (is_array($arResult['OLD_VALUE'][$code]) ? $arResult['OLD_VALUE'][$code][$i] : $arResult['OLD_VALUE'][$code]);
		
		switch($fieldInfo['PROPERTY_TYPE']):
			
			case 'TAGS':
				$APPLICATION->IncludeComponent(
					"bitrix:search.tags.input",
					"",
					array(
						"VALUE" => $arResult["ELEMENT"][$propertyID],
						"NAME" => $name,
						"TEXT" => 'size="'.$fieldInfo["COL_COUNT"].'"',
					), null, array("HIDE_ICONS"=>"Y")
				);
				break;

			case 'T':
				?>
				<textarea cols="<?=$fieldInfo["COL_COUNT"]?>" rows="<?=$fieldInfo["ROW_COUNT"]?>" name="<?=$name?>" id="<?=$code?>"><?=$value?></textarea>
				<?
				break;

			case "S":
			case "N":
				?>
				<input type="text" name="<?=$name?>" size="25" value="<?=$value?>" id="<?=$code?>" />&nbsp;
				<?
				if($fieldInfo["USER_TYPE"] == "DateTime")
				{
					$APPLICATION->IncludeComponent(
						'bitrix:main.calendar',
						'',
						array(
							'FORM_NAME' => $arResult['FORM_ID'],
							'INPUT_NAME' => $name,
							'INPUT_VALUE' => $value,
						),
						null,
						array('HIDE_ICONS' => 'N')
					);
					?><small><?=GetMessage("IBLOCK_FORM_DATE_FORMAT")?><?=FORMAT_DATETIME?></small><?
				}
				break;
			
			case 'F':
				?>
				<input type="file" name="<?=$name?>" value="<?=$value?>" id="<?=$code?>" />
				<?
				break;
			
			case "E":
			case "G":
			case 'L':
				if(!empty($fieldInfo['ENUM'])):
					if($fieldInfo['LIST_TYPE'] == 'C'):
						foreach($fieldInfo['ENUM'] as $propID => $info):
							?>
							<label><input type="<?=($fieldInfo["MULTIPLE"] == "Y" ? "checkbox" : "radio")?>" value="<?=$propID?>" <?
								if(is_array($arResult['OLD_VALUE'][$code]) && in_array($propID,$arResult['OLD_VALUE'][$code]))
								{
									echo ' checked="checked" ';
								}
								elseif($propID == $arResult['OLD_VALUE'][$code])
								{
									echo ' checked="checked" ';
								}
							?> name="<?=$name?>" />	<?=$info['VALUE']?></label>
							<?
						endforeach;
						
					else:
						?>
						<select <?=$fieldInfo["MULTIPLE"] == "Y" ? 'multiple="multiple"' : ''?> name="<?=$name?>">
							<?
							foreach($fieldInfo['ENUM'] as $propID => $info):
								?>
								<option value="<?=$propID?>" <?
									if(is_array($arResult['OLD_VALUE'][$code]) && in_array($info['ID'],$arResult['OLD_VALUE'][$code]))
									{
										echo ' selected="selected" ';
									}
									elseif($info['ID'] == $arResult['OLD_VALUE'][$code])
									{
										echo ' selected="selected" ';
									}?> ><?=$info['VALUE']?></option>
								<?
							endforeach;
							?>
						</select>
						<?
					endif;
				endif;
				break;
			
			case 'CAPTCHA':
				?>
				<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" class="ciee-captcha-image" />
				<input type="text" name="captcha_word" maxlength="50" value="" class="ciee-captcha-input">
				<?
				break;
				
		endswitch;
	}

	?></span><?
	
	if (strlen($fieldInfo['TOOLTIP']) > 0)
	{
		?><small class="ciee-field-tooltip"><?=$fieldInfo['TOOLTIP']?></small><?
	}

	?></div><?

endforeach;


?>
	<div class="tooltip-block">
		<span class="required-fields">*</span>
		<span><?=GetMessage('REQUIRED_MESSAGE_LABLE')?></span>
	</div>

	<input id="getForecast" type="submit" name="iblock_submit" value="<?=$arParams['SUBMIT_TEXT']?>" />


</form>
