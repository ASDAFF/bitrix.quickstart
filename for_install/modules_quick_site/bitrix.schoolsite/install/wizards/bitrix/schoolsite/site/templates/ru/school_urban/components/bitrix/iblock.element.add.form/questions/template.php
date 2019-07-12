<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<?
//echo "<pre>Template arParams: "; print_r($arParams); echo "</pre>";
//echo "<pre>Template arResult: "; print_r($arResult); echo "</pre>";
//exit();
?>

<?//echo '<pre>';print_r($arResult);echo '</pre>';?>
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
		<?if (is_array($arResult["PROPERTY_LIST"]) && count($arResult["PROPERTY_LIST"] > 0)):?>
        <?foreach($arResult['PROPERTY_LIST_FULL'] as $prop_id):?>
            <?if($prop_id['CODE'] == "CONTACTS"){$contacts = $prop_id['ID'];}
            elseif($prop_id['CODE'] == "SUBJECT"){$subject = $prop_id['ID'];}?>
            <?//echo '<pre>';print_r($prop_id);echo '</pre>';?>
        <?endforeach;?>
		<tbody>
            <tr>
                <th colspan="2">ФИО<?if (in_array("NAME", $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?></th>
            </tr>
            <tr>        
                <td style="padding-top:2px;" colspan="2">
                    <input class="inputtext" type="text" value="<?=$_REQUEST["PROPERTY"]["NAME"][0]?>" size="65" name="PROPERTY[NAME][0]"><br><br>					</td>
            </tr>
            <?if (in_array($contacts, $arResult['PROPERTY_LIST'])) {?>
            <tr>
                <th colspan="2">Телефон или другая контактная информация<?if (in_array($contacts, $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?></th>
            </tr>
            <tr>    
                <td style="padding-top:2px;" colspan="2">
                    <input class="inputtext" type="text" value="<?=$_REQUEST["PROPERTY"][$contacts][0]?>" size="65" name="PROPERTY[<?=$contacts?>][0]"><br><br>					
                </td>
            </tr>
            <?}?>
            <?if (in_array($subject, $arResult['PROPERTY_LIST'])) {?>
            <tr>
                <th style="width:90px;">Тема вопроса<?if (in_array($subject, $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?></th>
                <td>
                    <select size="1" name="PROPERTY[<?=$subject?>]">
                        <?foreach($arResult['PROPERTY_LIST_FULL'][$subject]['ENUM'] as $thems):?>
                            <option <?if($_REQUEST["PROPERTY"][$subject] == $thems['ID']){?>selected<?}?> value="<?=$thems['ID']?>"><?=$thems['VALUE']?></option>
                        <?endforeach;?>
                    </select>
                </td>
            </tr>
            <?}?>
            <?if (in_array("PREVIEW_TEXT", $arResult['PROPERTY_LIST'])) {?>
            <tr>
                <th colspan="2">Текст вопроса<?if (in_array("PREVIEW_TEXT", $arResult['PROPERTY_REQUIRED'])) {?><font color="red"><span class="form-required starrequired">*</span></font><?}?></th>
            </tr>
            <tr>    
                <td style="padding-top:2px;" colspan="2">
                    <textarea name="PROPERTY[PREVIEW_TEXT][0]" class="inputtextarea" rows="10" cols="50" id="itsalltext_generated_id_PROPERTY[PREVIEW_TEXT][0]_1"><?=$_REQUEST["PROPERTY"]["PREVIEW_TEXT"][0]?></textarea>
                </td>
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