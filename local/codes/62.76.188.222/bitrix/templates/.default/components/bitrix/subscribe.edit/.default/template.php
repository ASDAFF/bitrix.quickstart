<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<section class="b-detail">
        <div class="b-detail-content">
            <?
foreach($arResult["MESSAGE"] as $itemID=>$itemValue)
	echo ShowMessage(array("MESSAGE"=>$itemValue, "TYPE"=>"OK"));
foreach($arResult["ERROR"] as $itemID=>$itemValue)
	echo ShowMessage(array("MESSAGE"=>$itemValue, "TYPE"=>"ERROR"));

if($arResult["ALLOW_ANONYMOUS"]=="N" && !$USER->IsAuthorized()):
	echo ShowMessage(array("MESSAGE"=>GetMessage("CT_BSE_AUTH_ERR"), "TYPE"=>"ERROR"));
else:?> 
        <form action="<?=$arResult["FORM_ACTION"]?>" method="post">
	<?echo bitrix_sessid_post();?>
	<input type="hidden" name="PostAction" value="<?echo ($arResult["ID"]>0? "Update":"Add")?>" />
	<input type="hidden" name="ID" value="<?echo $arResult["SUBSCRIPTION"]["ID"];?>" />
	<input type="hidden" name="RUB_ID[]" value="0" />

                <div class="b-checkout">
                        <table class="b-subcribe__table">
                                <tbody><tr>
                                        <td>E-mail:</td>
                                </tr>
                                <tr>
                                        <td><input type="text"  name="EMAIL" value="<?echo $arResult["SUBSCRIPTION"]["EMAIL"]!=""? $arResult["SUBSCRIPTION"]["EMAIL"]: $arResult["REQUEST"]["EMAIL"];?>"  class="b-text"></td>
                                </tr>
                        </tbody></table>
                        <table class="b-subcribe__table">
                                <tbody><tr>
                                        <td>Формат писем:</td>
                                </tr>
   
<tr>
<td><label class="b-radio m-radio_gp_1"><input type="radio" name="FORMAT" id="MAIL_TYPE_TEXT" value="text" <?if($arResult["SUBSCRIPTION"]["FORMAT"] != "html") echo "checked"?> />Текст</label></td>
</tr>
<tr>
<td><label class="b-radio m-radio_gp_1 b-checked"><input type="radio" name="FORMAT" id="MAIL_TYPE_HTML" value="html" <?if($arResult["SUBSCRIPTION"]["FORMAT"] == "html") echo "checked"?> />HTML</label></td>
</tr>
                                
                                
                        </tbody></table>
                        <table class="b-subcribe__table">
                                <tbody><tr>
                                        <td>Рубрики:</td>
                                </tr>
                                
                                

<?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
                                
                                           
<tr>
<td><label class="b-checkbox m-checkbox_gp_1">
        <input type="checkbox"  id="RUBRIC_<?echo $itemID?>" name="RUB_ID[]"   value="<?=$itemValue["ID"]?>"<?if($itemValue["CHECKED"]) echo " checked"?> /><?echo $itemValue["NAME"]?></label>
</td>
</tr>

  
<?endforeach;?>

             
                        </tbody></table>
                    
                     <input type="submit" class="b-button" name="Save" value="Изменить подписку" />
                    
                </div>
                <div class="b-reviews_add__title">Ваша подписка не подтверждена. Чтобы подтвердить подписку, введите код подтверждения:</div>
                <table class="b-subcribe__table">
                        <tbody><tr>
                                <td><input class="b-text" name="CONFIRM_CODE" type="text" class="subscription-textbox" value="<?echo GetMessage("CT_BSE_CONFIRMATION")?>" onblur="if (this.value=='')this.value='<?echo GetMessage("CT_BSE_CONFIRMATION")?>'" onclick="if (this.value=='<?echo GetMessage("CT_BSE_CONFIRMATION")?>')this.value=''" /></td>
                        </tr>
                </tbody></table> 
                    <input type="submit" class="b-button" name="confirm" value="<?echo GetMessage("CT_BSE_BTN_CONF")?>" />
                 </form>
             <?endif?>
        </div>
</section>
