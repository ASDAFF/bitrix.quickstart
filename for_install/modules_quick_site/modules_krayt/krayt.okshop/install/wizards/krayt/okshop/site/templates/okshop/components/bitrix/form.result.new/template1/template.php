<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?if ($arResult["isFormErrors"] == "Y"):?>
<div class="error-byuone">
<?=$arResult["FORM_ERRORS_TEXT"];?>
</div>
<?else:?>
<?if($arResult["FORM_NOTE"]):?>
<div class="note-buyone">
<?=$arResult["FORM_NOTE"]?>
</div>
<?endif;?>
<?endif;?>
<?if ($arResult["isFormNote"] != "Y")
{
?>
<?=$arResult["FORM_HEADER"]?>
<div class="form-buyone">
	<?
    $strrod = "";
    if(CModule::IncludeModule("iblock"))
   { 
     if($arParams['PRODUCT_ID']> 0)
    {
      $prod =   CIBlockElement::GetByID($arParams['PRODUCT_ID'])->Fetch();
      if($prod)
      {
        $strrod = "{$prod['NAME']}";
      }
    }
   }
    
	foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion)
	{
	   $arF = $arQuestion['STRUCTURE'][0];	      
		if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'textarea')
		{		
             echo CForm::GetTextAreaField(
                $arF["ID"],
                "",
                "",
                "style='display:none'",
                $strrod
                );
		}
		else
		{
	?>
		<div>
            <label>
				<?if (is_array($arResult["FORM_ERRORS"]) && array_key_exists($FIELD_SID, $arResult['FORM_ERRORS'])):?>
				<span class="error-fld" title="<?=$arResult["FORM_ERRORS"][$FIELD_SID]?>"></span>
				<?endif;?>			
			</label>
			<? 
            echo CForm::GetTextField(
                $arF["ID"],
                "",
                "",
                "placeholder=\"{$arQuestion["CAPTION"]}\" class=\"inputtext\""
                );
           // $arQuestion["HTML_CODE"]?>
		</div>
	<?
		}
	} //endwhile
	?>
<?
if($arResult["isUseCaptcha"] == "Y")
{
?>
		<tr>
			<th colspan="2"><b><?=GetMessage("FORM_CAPTCHA_TABLE_TITLE")?></b></th>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="hidden" name="captcha_sid" value="<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" /><img src="/bitrix/tools/captcha.php?captcha_sid=<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" width="180" height="40" /></td>
		</tr>
		<tr>
			<td><?=GetMessage("FORM_CAPTCHA_FIELD_TITLE")?><?=$arResult["REQUIRED_SIGN"];?></td>
			<td><input type="text" name="captcha_word" size="30" maxlength="50" value="" class="inputtext" /></td>
		</tr>
<?
} // isUseCaptcha
?>
<div class="btns-byuone"> 
				<input class="em_button" <?=(intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : "");?> type="submit" name="web_form_submit" value="<?=htmlspecialcharsbx(strlen(trim($arResult["arForm"]["BUTTON"])) <= 0 ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]);?>" />
				<input type="hidden" name="web_form_apply" value="Y" />
                			
	</div>
</div>
<?=$arResult["FORM_FOOTER"]?>
<?
} //endif (isFormNote)
?>