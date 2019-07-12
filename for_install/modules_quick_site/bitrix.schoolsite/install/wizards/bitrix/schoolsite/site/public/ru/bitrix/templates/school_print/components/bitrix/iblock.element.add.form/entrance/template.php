<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<?
//echo "<pre>Template arParams: "; print_r($arParams); echo "</pre>";
//echo "<pre>Template arResult: "; print_r($arResult); echo "</pre>";
//exit();
?>

<?if (count($arResult["ERRORS"])):?>
	<?=ShowError(implode("<br />", $arResult["ERRORS"]))?>
<?endif?>
<?if (strlen($arResult["MESSAGE"]) > 0):?>
	<?=ShowNote($arResult["MESSAGE"])?>
<?endif?>
<form name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">

	<?=bitrix_sessid_post()?>

	<?if ($arParams["MAX_FILE_SIZE"] > 0):?><input type="hidden" name="MAX_FILE_SIZE" value="<?=$arParams["MAX_FILE_SIZE"]?>" /><?endif?>

	<table class="parents-question">
		<thead>

		</thead>
		<?if (is_array($arResult["PROPERTY_LIST"]) && count($arResult["PROPERTY_LIST"] > 0)):?>
        <?foreach($arResult['PROPERTY_LIST_FULL'] as $prop_id):?>
            <?if($prop_id['CODE'] == "DATE_BIRTH"){$date_birth = $prop_id['ID'];}
            elseif($prop_id['CODE'] == "BIRTH_CERTIFICATE"){$birth_certificate = $prop_id['ID'];}
            elseif($prop_id['CODE'] == "COPY_CITIZENSHIP"){$copy_citizenship = $prop_id['ID'];}
            elseif($prop_id['CODE'] == "NAME_FATHER"){$name_father = $prop_id['ID'];}
            elseif($prop_id['CODE'] == "JOB_FATHER"){$job_father = $prop_id['ID'];}
            elseif($prop_id['CODE'] == "PHONE_FATHER"){$phone_father = $prop_id['ID'];}
            elseif($prop_id['CODE'] == "NAME_MOTHER"){$name_mother = $prop_id['ID'];}
            elseif($prop_id['CODE'] == "JOB_MPTHER"){$job_mother = $prop_id['ID'];}
            elseif($prop_id['CODE'] == "PHONE_MOTHER"){$phone_mother = $prop_id['ID'];}?>
            <?//echo '<pre>';print_r($prop_id);echo '</pre>';?>
        <?endforeach;?>
		<tbody>
        	<tr>
				<th style="width:90px;">	
					<?if (in_array("NAME", $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?> Ф.И.О. ребенка:
				</th>
				<td><input type="text" size="65" value="" name="PROPERTY[NAME][0]" class="inputtext"></td>
			</tr>
            <?if (in_array($date_birth, $arResult['PROPERTY_LIST'])) {?>
			<tr>
				<th style="width:90px;">	
					<?if (in_array($date_birth, $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?> Дата рождения ребенка:
				</th>
				<td><input type="text" size="30" value="" name="PROPERTY[<?=$date_birth?>][0]">
                    <?if($arResult["PROPERTY_LIST_FULL"][18]["USER_TYPE"] == "DateTime"):?><?
									$APPLICATION->IncludeComponent(
										'bitrix:main.calendar',
										'',
										array(
											'FORM_NAME' => 'iblock_add',
											'INPUT_NAME' => "PROPERTY[18][0]",
											'INPUT_VALUE' => $value,
										),
										null,
										array('HIDE_ICONS' => 'Y')
									);
									?>(DD.MM.YYYY)<?
								endif?>
                </td>
			</tr>
            <?}?>
            <?if (in_array($birth_certificate, $arResult['PROPERTY_LIST'])) {?>
			<tr>
				<th style="width:90px;">	
					<?if (in_array($birth_certificate, $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?> Свидетельство о рождении ребенка:
				</th>
				<td> <input type="file" size="0" class="inputfile" name="PROPERTY[<?=$birth_certificate?>][0]"></td>
			</tr>
            <?}?>
            <?if (in_array($copy_citizenship, $arResult['PROPERTY_LIST'])) {?>
            <tr>
				<th style="width:90px;">	
					<?if (in_array($copy_citizenship, $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?> Копия гражданства РФ:
				</th>
				<td> <input type="file" size="0" class="inputfile" name="PROPERTY[<?=$copy_citizenship?>][0]"></td>
			</tr>
            <?}?>
            <?if (in_array("PREVIEW_TEXT", $arResult['PROPERTY_LIST'])) {?>
			<tr>
				<th style="width:90px;">	
					<?if (in_array("PREVIEW_TEXT", $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?> Адрес прописки ребенка:
				</th>
				<td><textarea class="inputtextarea" rows="3" cols="50" name="PROPERTY[PREVIEW_TEXT][0]" id="itsalltext_generated_id_form_textarea_17_1"></textarea></td>
			</tr>
            <?}?>
            <?if (in_array("DETAIL_TEXT", $arResult['PROPERTY_LIST'])) {?>
			<tr>
				<th style="width:90px;">	
					<?if (in_array("DETAIL_TEXT", $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?> Адрес фактического проживания ребенка:
				</th>
				<td><textarea class="inputtextarea" rows="3" cols="50" name="PROPERTY[DETAIL_TEXT][0]" id="itsalltext_generated_id_form_textarea_18_2"></textarea></td>
			</tr>
            <?}?>
            <?if (in_array($name_father, $arResult['PROPERTY_LIST'])) {?>
			<tr>
				<th style="width:90px;">	
					<?if (in_array($name_father, $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?> Ф.И.О. отца:
				</th>
				<td><input type="text" size="65" value="" name="PROPERTY[<?=$name_father?>][0]" class="inputtext"></td>
			</tr>
            <?}?>
            <?if (in_array($job_father, $arResult['PROPERTY_LIST'])) {?>
			<tr>
				<th style="width:90px;">	
					<?if (in_array($job_father, $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?> Место работы отца:
				</th>
				<td><input type="text" size="65" value="" name="PROPERTY[<?=$job_father?>][0]" class="inputtext"></td>
			</tr>
            <?}?>
            <?if (in_array($phone_father, $arResult['PROPERTY_LIST'])) {?>
			<tr>
				<th style="width:90px;">	
					<?if (in_array($phone_father, $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?> Телефон отца:
				</th>
				<td><input type="text" size="65" value="" name="PROPERTY[<?=$phone_father?>][0]" class="inputtext"></td>
			</tr>
            <?}?>
            <?if (in_array($name_mother, $arResult['PROPERTY_LIST'])) {?>
			<tr>
				<th style="width:90px;">	
					<?if (in_array($name_mother, $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?> Ф.И.О. матери:
				</th>
				<td><input type="text" size="65" value="" name="PROPERTY[<?=$name_mother?>][0]" class="inputtext"></td>
			</tr>
            <?}?>
            <?if (in_array($job_mother, $arResult['PROPERTY_LIST'])) {?>
			<tr>
				<th style="width:90px;">	
					<?if (in_array($job_mother, $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?> Место работы матери:
				</th>
				<td><input type="text" size="65" value="" name="PROPERTY[<?=$job_mother?>][0]" class="inputtext"></td>
			</tr>
            <?}?>
            <?if (in_array($phone_mother, $arResult['PROPERTY_LIST'])) {?>
			<tr>
				<th style="width:90px;">	
					<?if (in_array($phone_mother, $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?> Телефон матери:
				</th>
				<td><input type="text" size="65" value="" name="PROPERTY[<?=$phone_mother?>][0]" class="inputtext"></td>
			</tr>
            <?}?>

			<?if($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0):?>
				<th>
					<td><?=GetMessage("IBLOCK_FORM_CAPTCHA_TITLE")?></td>
					<td>
						<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
						<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
					</td>
				</th>
				<tr>
					<td><?=GetMessage("IBLOCK_FORM_CAPTCHA_PROMPT")?><span class="starrequired">*</span>:</td>
					<td><input type="text" name="captcha_word" maxlength="50" value=""></td>
				</tr>
			<?endif?>
		</tbody>
		<?endif?>
		<tfoot>
			<tr>
				<td colspan="2">
                <p><font color="red"><span class="form-required starrequired">*</span></font> - Поля, обязательные для заполнения</p>
					<input type="submit" name="iblock_submit" value="<?=GetMessage("IBLOCK_FORM_SUBMIT")?>" />
					<?if (strlen($arParams["LIST_URL"]) > 0 && $arParams["ID"] > 0):?><input type="submit" name="iblock_apply" value="<?=GetMessage("IBLOCK_FORM_APPLY")?>" /><?endif?>
					<?/*<input type="reset" value="<?=GetMessage("IBLOCK_FORM_RESET")?>" />*/?>
				</td>
			</tr>
		</tfoot>
	</table>
	<br />
	<?if (strlen($arParams["LIST_URL"]) > 0):?><a href="<?=$arParams["LIST_URL"]?>"><?=GetMessage("IBLOCK_FORM_BACK")?></a><?endif?>
</form>