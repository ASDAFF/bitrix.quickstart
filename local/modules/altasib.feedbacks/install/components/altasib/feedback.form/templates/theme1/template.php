<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$ALX = "FID".$arParams["FORM_ID"];?>
<script type="text/javascript">
<?if($arParams["REWIND_FORM"] == "Y" && ((count($arResult["FORM_ERRORS"]) > 0) || ($_REQUEST["success_".$ALX] == "yes"))):?>
$(document).ready(function(){
        document.location.hash = "alx_position_feedback";
});
<?endif?>
if (typeof ALX_ReloadCaptcha != 'function')
{
  function ALX_ReloadCaptcha(csid, ALX)
  {
          document.getElementById("alx_cm_CAPTCHA_"+ALX).src = '/bitrix/tools/captcha.php?captcha_sid='+csid+'&rnd='+Math.random();
  }
  function ALX_SetNameQuestion(obj, ALX)
  {
          var qw = obj.selectedIndex;
          document.getElementById("type_question_name_"+ALX).value = obj.options[qw].text;
  }
}
</script>
<?if($arParams["REWIND_FORM"] == "Y" && ((count($arResult["FORM_ERRORS"]) > 0) || ($_REQUEST["success_".$ALX] == "yes"))):?>
        <a name="alx_position_feedback"></a>
<?endif?>
<div class="alx_feed_back_form alx_feed_back_theme1" id="alx_feed_back_theme1_<?=$ALX?>">
<?if((count($arResult["FORM_ERRORS"]) == 0) && ($_REQUEST["success_".$ALX] == "yes")):?>
        <div class="alx_feed_back_form_error_block">
                <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                        <td class="alx_feed_back_form_error_pic"><?=CFile::ShowImage($arParams["IMG_OK"])?></td>
                        <td class="alx_feed_back_form_mess_ok_td_list">
                                <div class="alx_feed_back_form_mess_ok"><?=$arParams["MESSAGE_OK"];?></div>
                        </td>
                </tr>
                </table>
        </div>
<?endif?>
<?if($arParams["CHECK_ERROR"] == "Y"):?>
<?if(count($arResult["FORM_ERRORS"]) > 0):?>
        <div class="alx_feed_back_form_error_block">
                <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                        <td class="alx_feed_back_form_error_pic"><?=CFile::ShowImage($arParams["IMG_ERROR"])?></td>
                        <td class="alx_feed_back_form_error_td_list">
                        <div class="alx_feed_back_form_title_error">
                                <?=GetMessage("ALX_TP_REQUIRED_ERROR");?>
                        </div>
                                <ul>
                                        <?foreach($arResult["FORM_ERRORS"] as $error):?>
                                                <?foreach($error as $v):?>
                                                        <li><span>-</span> <?=$v?></li>
                                                <?endforeach?>
                                        <?endforeach?>
                                </ul>
                        </td>
                </tr>
                </table>
        </div>
<?endif?>
<?endif?>
<?
$hide = false;
if($arParams["HIDE_FORM"] == "Y" && $_REQUEST["success_".$ALX] == "yes")
        $hide = true;

$actionPage = $APPLICATION->GetCurPage();
if(strpos($actionPage, "index.php") !== false)
        $actionPage = $APPLICATION->GetCurDir();
?>
<?if(!$hide):?>
<div class="alx_feed_back_form_feedback_poles">
<form id="f_feedback" name="f_feedback_<?=$ALX?>" action="<?=$actionPage?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="FEEDBACK_FORM_<?=$ALX?>" value="Y" />
        <?echo bitrix_sessid_post()?>
        <?if(count($arResult["TYPE_QUESTION"]) >= 1):?>
                        <?/*TYPE_QUESTION*/?>
                        <div class="alx_feed_back_form_item_pole">
                                <div class="alx_feed_back_form_name"><?=$arParams["CATEGORY_SELECT_NAME"]?></div>
                                <div class="alx_feed_back_form_inputtext_bg">
                                        <input type="hidden" id="type_question_name_<?=$ALX?>" name="type_question_name_<?=$ALX?>" value="<?=$arResult["TYPE_QUESTION"][0]["NAME"]?>">
                                        <select id="type_question_<?=$ALX?>" name="type_question_<?=$ALX?>" onchange="ALX_SetNameQuestion(this, '<?=$ALX?>');">
                                                <?foreach($arResult["TYPE_QUESTION"] as $arField):?>
                                                        <?if(trim(htmlspecialcharsEx($_POST["type_question"])) == $arField["ID"]):?>
                                                                <option value="<?=$arField["ID"]?>" selected><?=$arField["NAME"]?></option>
                                                        <?else:?>
                                                                <option value="<?=$arField["ID"]?>"><?=$arField["NAME"]?></option>
                                                        <?endif?>
                                                <?endforeach?>
                                        </select>
                                </div>
                        </div>
        <?endif?>
        <?$k = 0;?>
        <?foreach($arResult["FIELDS"] as $arField):?>
                        <div class="alx_feed_back_form_item_pole">
                                <div class="alx_feed_back_form_name">
                                        <?=$arField["NAME"]?> <?if($arField["REQUIRED"]):?><span class="alx_feed_back_form_required_text">*</span><?endif?>
                                        <div class="alx_feed_back_form_hint"><?=$arField["HINT"]?></div>
                                </div>
                <?/*LIST*/?>
                        <?if($arField["TYPE"] == "L"):?>
                                <?if($arField["LIST_TYPE"] == "L"):?>
                                        <div class="alx_feed_back_form_inputtext_bg">
                                        <?if($arField["MULTIPLE"] == "Y"):?>
                                                <select name="FIELDS[<?=$arField["CODE"]?>][]" multiple="multiple">
                                        <?else:?>
                                                <select name="FIELDS[<?=$arField["CODE"]?>]">
                                        <?endif;?>
                                        <?foreach($arField["ENUM"] as $v):?>
                                                <?if(!isset($_POST["FIELDS"][$arField["CODE"]]) && !isset($arResult["FORM_ERRORS"]["EMPTY_FIELD"][$arField["CODE"]])):?>
                                                        <option value="<?=$v["ID"]?>" <?if($v['DEF'] == 'Y') echo 'selected="selected"';?> ><?=$v["VALUE"]?></option>
                                                <?else:?>
                                                        <?if($arField["MULTIPLE"] == "Y"):?>
                                                                <option value="<?=$v["ID"]?>" <?if(in_array($v['ID'], $_POST["FIELDS"][$arField["CODE"]])) echo 'selected="selected"';?> ><?=$v["VALUE"]?></option>
                                                        <?else:?>
                                                                <option value="<?=$v["ID"]?>" <?if($v['ID'] == $_POST["FIELDS"][$arField["CODE"]]) echo 'selected="selected"';?> ><?=$v["VALUE"]?></option>
                                                        <?endif;?>
                                                <?endif;?>
                                        <?endforeach?>
                                        </select>
                                        </div>
                                <?elseif($arField["LIST_TYPE"] == "C"):?>
                                        <?if($arField["MULTIPLE"] == "Y"):?>
                                                <?foreach($arField["ENUM"] as $v):?>
                                                        <?if(!isset($_POST["FIELDS"][$arField["CODE"]]) && !isset($arResult["FORM_ERRORS"]["EMPTY_FIELD"][$arField["CODE"]])):?>
                                                                <input id="<?=$v["ID"]?>" type="checkbox" name="FIELDS[<?=$arField["CODE"]?>][]" value="<?=$v["ID"]?>" <?if($v["DEF"] == "Y") echo 'checked="checked"';?>><label for="<?=$v["ID"]?>"><?=$v["VALUE"]?></label><br />
                                                        <?else:?>
                                                                <input id="<?=$v["ID"]?>" type="checkbox" name="FIELDS[<?=$arField["CODE"]?>][]" value="<?=$v["ID"]?>" <?if(in_array($v['ID'], $_POST["FIELDS"][$arField["CODE"]])) echo 'checked="checked"';?>><label for="<?=$v["ID"]?>"><?=$v["VALUE"]?></label><br />
                                                        <?endif;?>
                                                <?endforeach?>
                                        <?else:?>
                                                <?foreach($arField["ENUM"] as $v):?>
                                                        <?if(!isset($_POST["FIELDS"][$arField["CODE"]]) && !isset($arResult["FORM_ERRORS"]["EMPTY_FIELD"][$arField["CODE"]])):?>
                                                                <input id="<?=$v["ID"]?>" type="radio" name="FIELDS[<?=$arField["CODE"]?>]" value="<?=$v["ID"]?>" <?if($v['DEF'] == 'Y') echo 'checked="checked"';?>><label for="<?=$v["ID"]?>"><?=$v["VALUE"]?></label><br />
                                                        <?else:?>
                                                                <input id="<?=$v["ID"]?>" type="radio" name="FIELDS[<?=$arField["CODE"]?>]" value="<?=$v["ID"]?>" <?if($v['ID'] == $_POST["FIELDS"][$arField["CODE"]]) echo 'checked="checked"';?>><label for="<?=$v["ID"]?>"><?=$v["VALUE"]?></label><br />
                                                        <?endif;?>
                                                <?endforeach?>
                                        <?endif?>
                                <?endif?>
                        <?/*HTML/TEXT*/?>
            <?elseif($arField["USER_TYPE"] == "HTML"):?>
                                        <div class="alx_feed_back_form_inputtext_bg" id="error_<?=$arField["CODE"]?>">
                                                <?if(!empty($_POST["FIELDS"][$arField["CODE"]])):?>
                                                        <textarea cols="" rows="" id="<?=$arField["CODE"]?>1" name="FIELDS[<?=$arField["CODE"]?>]" style="height:<?=$arField["USER_TYPE_SETTINGS"]["height"]?>px;"><?=trim(htmlspecialcharsEx($_POST["FIELDS"][$arField["CODE"]]))?></textarea>
                                                <?else:?>
                                                        <textarea cols="" rows="" id="<?=$arField["CODE"]?>1" name="FIELDS[<?=$arField["CODE"]?>]" style="height:<?=$arField["USER_TYPE_SETTINGS"]["height"]?>px;" onblur="if(this.value==''){this.value='<?=$arField["DEFAULT_VALUE"]["TEXT"]?>'}" onclick="if(this.value=='<?=$arField["DEFAULT_VALUE"]["TEXT"]?>'){this.value=''}"><?=$arField["DEFAULT_VALUE"]["TEXT"]?></textarea>
                                                <?endif;?>
                                        </div>
            <?/*DATE*/?>
                        <?elseif($arField["USER_TYPE"] == "DateTime"):?>
                                <div class="alx_feed_back_form_inputtext_bg alx_feed_back_form_inputtext_bg_calendar" id="error_<?=$arField["CODE"]?>">
                                        <?if(!empty($_POST["FIELDS"][$arField["CODE"]])):?>
                                                <input type="text" size="40" id="<?=$arField["CODE"]?>1" name="FIELDS[<?=$arField["CODE"]?>]" value="<?=trim(htmlspecialcharsEx($_POST["FIELDS"][$arField["CODE"]]))?>" class="alx_feed_back_form_inputtext" readonly="readonly" onclick="BX.calendar({node:this, field:'FIELDS[<?=$arField["CODE"]?>]', form: '', bTime: false, currentTime: '<?=(time()+date("Z")+CTimeZone::GetOffset())?>', bHideTime: false});" />
                                        <?else:?>
                                                <input type="text" size="40" id="<?=$arField["CODE"]?>1" name="FIELDS[<?=$arField["CODE"]?>]" value="<?=$arField["DEFAULT_VALUE"]?>" class="alx_feed_back_form_inputtext" readonly="readonly" onclick="BX.calendar({node:this, field:'FIELDS[<?=$arField["CODE"]?>]', form: '', bTime: false, currentTime: '<?=(time()+date("Z")+CTimeZone::GetOffset())?>', bHideTime: false});" />
                                        <?endif;?>
                                        <div class="alx_feed_back_form_calendar_icon">
                                                <?
                                                require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/admin_lib.php");
                                                define("ADMIN_THEME_ID", CAdminTheme::GetCurrentTheme());
                                                echo CAdminPage::ShowScript();
                                                echo Calendar("FIELDS[".$arField["CODE"]."]", "f_feedback_".$ALX);
                                                ?>
                                        </div>
                                </div>
                        <?/*STRING*/?>
            <?elseif($arField["TYPE"] != "F"):?>
                                <div class="alx_feed_back_form_inputtext_bg" id="error_<?=$arField["CODE"]?>">
                                        <?if(!empty($_POST["FIELDS"][$arField["CODE"]])):?>
                                                <input type="text" size="40" id="<?=$arField["CODE"]?>1" name="FIELDS[<?=$arField["CODE"]?>]" value="<?=trim(htmlspecialcharsEx($_POST["FIELDS"][$arField["CODE"]]))?>" class="alx_feed_back_form_inputtext" />
                                        <?else:?>
                                                <input type="text" size="40" id="<?=$arField["CODE"]?>1" name="FIELDS[<?=$arField["CODE"]?>]" value="<?=$arField["DEFAULT_VALUE"]?>" class="alx_feed_back_form_inputtext" onblur="if(this.value==''){this.value='<?=$arField["DEFAULT_VALUE"]?>'}" onclick="if(this.value=='<?=$arField["DEFAULT_VALUE"]?>'){this.value=''}" />
                                        <?endif;?>
                                </div>
                        <?/*FILE*/?>
            <?elseif($arField["TYPE"] == "F"):?>
                <?$k++;?>
                <input type="hidden" id="codeFileFields" name="codeFileFields[<?=$arField['CODE']?>]" value="<?=$arField['CODE']?>">
                                <div class="alx_feed_back_form_inputtext_bg_file">
                                        <div class="alx_feed_back_form_inputfile_s"><div>
                                                <input type="hidden" name="FIELDS[myFile][<?=$arField["CODE"]?>]">
                                                <input type="file" id="alx_feed_back_form_file_input_add<?=$k?>" name="myFile[<?=$arField['CODE']?>]" class="alx_feed_back_form_file_input_add" />
                                        </div></div>
                                        <div id="alx_feed_back_form_filename<?=$k?>" class="alx_feed_back_form_filename">&nbsp;</div>
                                        <div class="alx_feed_back_form_file_button"><div class="alx_feed_back_form_file_button_bg"><span><?=GetMessage("ALX_TP_OVERVIEW")?></span></div></div>
                                </div>
            <?endif?>
                </div>
        <?endforeach?>
                <?if(in_array("FEEDBACK_TEXT", $arParams["PROPERTY_FIELDS"])):?>
                <div class="alx_feed_back_form_item_pole">
                                <div class="alx_feed_back_form_name"><?=GetMessage("ALX_TP_MESSAGE_TEXTMESS")?> <?if(in_array("FEEDBACK_TEXT_".$ALX, $arParams["PROPERTY_FIELDS_REQUIRED"])):?><span class="alx_feed_back_form_required_text">*</span><?endif?></div>
                    <div class="alx_feed_back_form_inputtext_bg" id="error_EMPTY_TEXT"><div class="alx_feed_back_form_textarea_bg"><textarea cols="10" rows="10" id="EMPTY_TEXT1" name="FEEDBACK_TEXT_<?=$ALX?>"><?=$arResult["FEEDBACK_TEXT"]?></textarea></div></div>
                </div>
                <?endif?>
        <?if($arParams["USE_CAPTCHA"]):?>
                        <div class="alx_feed_back_form_item_pole">
                                <div class="alx_feed_back_form_name"><?=GetMessage("ALX_TP_MESSAGE_INPUTF")?> <?=GetMessage("ALX_TP_MESSAGE_INPUTS")?> <span class="alx_feed_back_form_required_text">*</span></div>

				<?$frame = $this->createFrame()->begin('loading... <img src="/bitrix/themes/.default/start_menu/main/loading.gif">');?>
                                <?$capCode = $GLOBALS["APPLICATION"]->CaptchaGetCode();?>
                                <input type="hidden" name="captcha_sid" value="<?=htmlspecialcharsEx($capCode)?>">
                                <div><img id="alx_cm_CAPTCHA_<?=$ALX?>" src="/bitrix/tools/captcha.php?captcha_sid=<?=htmlspecialcharsEx($capCode)?>" width="180" height="40"></div>
                                <div style="margin-bottom:6px;"><small><a href="#" onclick="capCode='<?=htmlspecialcharsEx($capCode)?>'; ALX_ReloadCaptcha(capCode, '<?=$ALX?>'); return false;"><?=GetMessage("ALX_TP_RELOADIMG")?></a></small></div>
				<?$frame->end();?>

                                <div class="alx_feed_back_form_inputtext_bg"><input type="text" class="alx_feed_back_form_inputtext" id="captcha_word1" name="captcha_word" size="30" maxlength="50" value=""></div>
                        </div>
        <?endif?>
        <div class="alx_feed_back_form_submit_block">
                        <input type="submit" name="SEND_FORM" value="<?=GetMessage('ALX_TP_MESSAGE_SUBMIT')?>" />
        </div>
</form>
</div>
<?endif?>
</div>

<script type="text/javascript">
        $(document).ready(function()
        {
                var file_w_<?=$ALX?> = parseInt($("#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_feedback_poles").width()/5);
                <?for($i=1;$i<=$k;$i++):?>
                $("#alx_feed_back_theme1_<?=$ALX?> #alx_feed_back_form_file_input_add<?=$i?>").attr('size', file_w_<?=$ALX?>);
                <?endfor?>
                function str_replace_<?=$ALX?>(search, replace, subject)
                {
                        return subject.split(search).join(replace);
                }
                <?for($i=1;$i<=$k;$i++):?>
                $("#alx_feed_back_theme1_<?=$ALX?> #alx_feed_back_form_file_input_add<?=$i?>").change(function() {
                        var newpath_<?=$ALX?> = str_replace_<?=$ALX?>("C:\\fakepath\\", "", $("#alx_feed_back_theme1_<?=$ALX?> #alx_feed_back_form_file_input_add<?=$i?>").val());
                        $("#alx_feed_back_theme1_<?=$ALX?> #alx_feed_back_form_filename<?=$i?>").text(newpath_<?=$ALX?>);
                });
                <?endfor?>
        });
</script>
<style type="text/css">
#alx_feed_back_theme1_<?=$ALX?>
{
        width: <?=str_replace(" ", "", $arParams["WIDTH_FORM"])?> !important;
}
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_error_block
{
        background-color:<?=str_replace(" ", "", $arParams["BACKCOLOR_ERROR"])?>;
        color:<?=str_replace(" ", "", $arParams["COLOR_ERROR"])?>;
        -moz-border-radius: <?=str_replace(" ", "", $arParams["BORDER_RADIUS"])?>;
          -webkit-border-radius: <?=str_replace(" ", "", $arParams["BORDER_RADIUS"])?>;
          -khtml-border-radius: <?=str_replace(" ", "", $arParams["BORDER_RADIUS"])?>;
          border-radius: <?=str_replace(" ", "", $arParams["BORDER_RADIUS"])?>;
        font-size:<?=str_replace(" ", "", $arParams["SIZE_INPUT"])?>;
}
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_error_block ul,
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_error_block ul li,
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_error_block ul li span
{
        color:<?=str_replace(" ", "", $arParams["COLOR_ERROR"])?>;
}
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_error_block .alx_feed_back_form_mess_ok
{
        font-size:<?=str_replace(" ", "", $arParams["SIZE_NAME"])?>;
        color:<?=str_replace(" ", "", $arParams["COLOR_MESS_OK"])?>;
}

#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_title_error
{
        color:<?=str_replace(" ", "", $arParams["COLOR_ERROR_TITLE"])?>;
        font-size:<?=str_replace(" ", "", $arParams["SIZE_NAME"])?>;
}
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_feedback_poles .alx_feed_back_form_name
{
        font-size:<?=str_replace(" ", "", $arParams["SIZE_NAME"])?>;
        color:<?=str_replace(" ", "", $arParams["COLOR_NAME"])?>;
}
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_feedback_poles .alx_feed_back_form_hint
{
        font-size:<?=str_replace(" ", "", $arParams["SIZE_HINT"])?>;
        color:<?=str_replace(" ", "", $arParams["COLOR_HINT"])?>;
}
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_feedback_poles .alx_feed_back_form_inputtext_bg,
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_feedback_poles .alx_feed_back_form_inputtext_bg_file,
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_feedback_poles .alx_feed_back_form_submit_block,
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_feedback_poles .alx_feed_back_form_file_button .alx_feed_back_form_file_button_bg
{
        -moz-border-radius: <?=str_replace(" ", "", $arParams["BORDER_RADIUS"])?>;
          -webkit-border-radius: <?=str_replace(" ", "", $arParams["BORDER_RADIUS"])?>;
          -khtml-border-radius: <?=str_replace(" ", "", $arParams["BORDER_RADIUS"])?>;
          border-radius: <?=str_replace(" ", "", $arParams["BORDER_RADIUS"])?>;
}
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_feedback_poles .alx_feed_back_form_inputtext_bg input,
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_feedback_poles .alx_feed_back_form_inputtext_bg textarea,
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_feedback_poles .alx_feed_back_form_inputtext_bg select,
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_feedback_poles .alx_feed_back_form_filename,
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_feedback_poles .alx_feed_back_form_file_button_bg
{
        font-size:<?=str_replace(" ", "", $arParams["SIZE_INPUT"])?>;
        color:<?=str_replace(" ", "", $arParams["COLOR_INPUT"])?>;
        font-family:tahoma;
}
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_feedback_poles .alx_feed_back_form_file_input_add
{
        font-size:<?=str_replace(" ", "", $arParams["SIZE_INPUT"])?>;
        color:<?=str_replace(" ", "", $arParams["COLOR_INPUT"])?>;
}
#alx_feed_back_theme1_<?=$ALX?> .alx_feed_back_form_feedback_poles .alx_feed_back_form_required_text
{
        color:red;
}
</style>
