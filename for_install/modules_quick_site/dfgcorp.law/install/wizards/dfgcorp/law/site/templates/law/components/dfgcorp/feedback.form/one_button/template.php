<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
	$arParams["FORM_TITLE"] = strlen($arParams["FORM_TITLE"])==0 ? GetMessage("FORM_TITLE") : $arParams["FORM_TITLE"];
	$arParams["BUTTON_TITLE"] = strlen($arParams["BUTTON_TITLE"])==0 ? GetMessage("BUTTON_TITLE") : $arParams["BUTTON_TITLE"];
	$arParams['CUSTOM_TITLE_NAME'] = strlen($arParams['CUSTOM_TITLE_NAME'])>0 ? $arParams['CUSTOM_TITLE_NAME'] : GetMessage("CUSTOM_TITLE_NAME");

	$arResult["PROPERTY_LIST_FULL"]["NAME"]["NAME"] = $arParams['CUSTOM_TITLE_NAME'] ;
?>
<script>
	jQuery(window).load(function(){
		jQuery('#signup form').submit(function(){
			var isError = false;
			jQuery("input[rel='required']").each(function(){
				if(jQuery(this).prop('value')==''){
					jQuery(this).addClass('error_input');
					isError = true;
				}
			});
			if(isError){
				alert("<?=GetMessage("FORM_ERROR_MSG")?>");
				return false;
			}
		});

		jQuery('#lean_overlay').on('click', function(){
			jQuery(this).css('display', 'none');
			jQuery('#signup').css('display', 'none');
			jQuery('body').css('overflow', 'auto');
		});
		
		jQuery('a[rel=leanModal]').on('click', function(){
			var modal = jQuery(this).attr('href');
			jQuery(modal).css('display', 'block');
			jQuery('#lean_overlay').css('display', 'block');
			jQuery('body').css('overflow', 'hidden');
			return false;
		});

		jQuery('#ok_window_<?=$arParams["UCID"]?>').click(function(){
			jQuery(this).css('display', 'none');
		});
	});
</script>
<?if ($arResult["ELEMENT_ADD_UCID"] == $arParams["UCID"]):?>
	<div id="ok_window_<?=$arParams["UCID"]?>">
        <div class="layout_ok"></div>
        <div class="message_ok">
            <?=GetMessage("USER_MESSAGE_ADD")?>
        </div>
    </div>
<?endif?>
		
		<div id="signup" style="display: none; position: fixed; opacity: 1; z-index: 11000; left: 50%; margin-left: -202px; top: 200px;">
			<div id="signup-ct">
				<div id="signup-header">
					<h2><?=$arParams["FORM_TITLE"]?></h2>
				</div>
		
<?
    $url_form = $APPLICATION->GetCurPageParam("", array("add", "send_request"));
?>
<form name="iblock_add" action="<?=$url_form?>" method="post" enctype="multipart/form-data">

    <?=bitrix_sessid_post()?>
    <input type="hidden" name="send_request" value="yes" />
    <input type="hidden" name="UCID" value="<?=$arParams["UCID"]?>" />
<?if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])):?>
    
    <?foreach ($arResult["PROPERTY_LIST"] as $propertyID):?>
<div class="txt-fld">

				  
        <?$prop_title='';?>
        <?if (intval($propertyID) > 0):?>
            <?$prop_title=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"];?>
        <?else:?>
            <?$prop_title = (!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID));?>
        <?endif?>

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
                                ?><?
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
<label for="user_name"><?=$prop_title?>:       <?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?><span class="starrequired_1">*</span><?endif?></label>

                        <textarea <?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?>rel="required"<?endif?> cols="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"]?>" rows="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"]?>" name="PROPERTY[<?=$propertyID?>][<?=$i?>]" placeholder1="<?=$prop_title?>"><?=$value?></textarea>
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
                                    $f_name = "PROPERTY[".$propertyID."][".$i."]";
                                    if($propertyID=="NAME")$f_name = "name";
                                ?>
                                <label for="<?=$f_name?>"><?=$prop_title?>:     <?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?><span class="starrequired_1">*</span><?endif?></label><input <?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?>rel="required"<?endif?> type="text" placeholder1="<?=$prop_title?>" id="<?=$f_name?>"  name="<?=$f_name?>" size="25" value="<?=$value?>" /><?
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
                                    ?><small><?=GetMessage("IBLOCK_FORM_DATE_FORMAT")?><?=FORMAT_DATETIME?></small><?
                                endif
                                ?><?
                                }
                            break;

                            case "F":
                                for ($i = 0; $i<$inputNum; $i++)
                                {
                                    $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
                                    ?>
                        <input type="hidden" name="PROPERTY[<?=$propertyID?>][<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>]" value="<?=$value?>" />
                        <label for="PROPERTY_FILE_<?=$propertyID?>">Прикрепить файл</label>
                        <input class="file" type="file" size="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"]?>"  name="PROPERTY_FILE_<?=$propertyID?>_<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>" />
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

                                        //echo "<pre>"; print_r($arResult["PROPERTY_LIST_FULL"][$propertyID]); echo "</pre>";?>
    <div class="checkboxes">
<?
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
                            <input type="<?=$type?>" name="PROPERTY[<?=$propertyID?>]<?=$type == "checkbox" ? "[".$key."]" : ""?>" value="<?=$key?>" id="property_<?=$key?>"<?=$checked ? " checked=\"checked\"" : ""?> /><label for="property_<?=$key?>"><?=$arEnum["VALUE"]?></label><br />
                                            <?
                                        }
                                        ?>
                                            </div>
                                <?
                                        
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
            <?endforeach;?>
            <?if($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0):?>
                    <?=GetMessage("IBLOCK_FORM_CAPTCHA_TITLE")?>
                        <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
                        <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
                    <?=GetMessage("IBLOCK_FORM_CAPTCHA_PROMPT")?><span class="starrequired">*</span>:
                    <input type="text" name="captcha_word" maxlength="50" value="">
            <?endif?>
        <?endif?>
            <input class="fr woo-sc-button custom" id="call_top_hide_form" id="submit" type="submit" style="float: right;" name="iblock_submit" value="<?=GetMessage("FEEDBACK_SEND_BUTTON")?>" />
            <?if (strlen($arParams["LIST_URL"]) > 0 && $arParams["ID"] > 0):?><input type="submit" name="iblock_apply" value="<?=GetMessage("IBLOCK_FORM_APPLY")?>" /><?endif?>
    <?if (strlen($arParams["LIST_URL"]) > 0):?><a href="<?=$arParams["LIST_URL"]?>"><?=GetMessage("IBLOCK_FORM_BACK")?></a><?endif?>
	</form>
	</div>
		</div>
<div id="lean_overlay" style="display: none; opacity: 0.5;"></div>
<a href="#signup" class="fr woo-sc-button custom"  rel="leanModal"><?=$arParams["BUTTON_TITLE"]?></a>