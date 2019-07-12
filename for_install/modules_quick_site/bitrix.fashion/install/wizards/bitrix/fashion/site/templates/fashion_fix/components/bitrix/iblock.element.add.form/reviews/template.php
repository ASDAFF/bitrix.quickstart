<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?if (count($arResult["ERRORS"])):?>
<div id="message" class="dialog">
    <h3 class="error"><?=GetMessage("ERROR")?></h3>
    <div class="text">
    <?foreach ($arResult["ERRORS"] as $error) {?>
        <p><?=$error?></p>
    <?}?>
    </div>
    <p><span id="er-button" class="close button"><?=GetMessage("CONTINUE")?></span></p>
</div>
<?endif?>
<?if (strlen($arResult["MESSAGE"]) > 0):?>
<div id="message" class="dialog">
    <h3><?=GetMessage("MESSAGE")?></h3>
    <div class="text"><?=GetMessage("ADDED")?></div>
    <p><span id="mes-button" class="close button"><?=GetMessage("CONTINUE")?></span></p>
</div>
<?endif?>

<div id="review-form" class="review-form">
<form name="iblock_add" action="<?=$APPLICATION->GetCurPageParam("", array("clear_cache", "strIMessage"));?>" method="post" enctype="multipart/form-data">
    <h3><?=GetMessage("TITLE")?></h3>
    <?=bitrix_sessid_post()?>
    <?if (is_array($arResult["PROPERTY_LIST"]) && count($arResult["PROPERTY_LIST"] > 0)):?>
    <?foreach ($arResult["PROPERTY_LIST_FULL"] as $keyProp => &$valProp) {
        if ($valProp["CODE"] == "reviews_model") {
            $valProp["CODE"]["PROPERTY_TYPE"] = $valProp["CODE"]["~PROPERTY_TYPE"] = "S";?>
    <input type="hidden" name="PROPERTY[<?=$keyProp?>][0]" value="<?=$arParams["MODEL_ID"]?>" />
        <?}elseif($valProp["CODE"] == "reviews_user"){?>
			<input type="hidden" name="PROPERTY[<?=$keyProp?>][0]" value="<?=$USER->GetID()?>" />
		<?}
    }?>
    <input type="hidden" name="PROPERTY[NAME][0]" value="<?=$arParams["NAME"]?>" />
    <?foreach ($arResult["PROPERTY_LIST"] as $propertyID):?>
        <?if (intval($propertyID) > 0)
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
        <p><textarea id="review-text" name="PROPERTY[<?=$propertyID?>][<?=$i?>]"><?=$value?></textarea></p>
                <?
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
            <input class="star" type="<?=$type?>" name="PROPERTY[<?=$propertyID?>]<?=$type == "checkbox" ? "[".$key."]" : ""?>" value="<?=$key?>" id="property_<?=$key?>"<?=$checked ? " checked=\"checked\"" : ""?> title="<?=$arEnum["VALUE"]?>" />
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
    <?endforeach;?>
    <?endif?>
    
    <p><input id="add-review" type="submit" name="iblock_submit" value="<?=GetMessage("IBLOCK_FORM_SUBMIT")?>" /></p>
</form>
</div>
<script>//$(".star").hide();</script>
<?if (count($arResult["ERRORS"]) || strlen($arResult["MESSAGE"]) > 0) {?>
<script>
$("body").append($("#message"));
$("#message").show().css('top', ($(window).height() - $("#message").height()) / 2);
$("#overlay").show();

$("#er-button").click(function(){$.scrollTo("#review-form", 700); $("#review-text").focus();});
$("#mes-button").click(function(){$.scrollTo("#reviews", 700);});
</script>
<?}?>