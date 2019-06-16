<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);
$this->addExternalCss("/bitrix/css/main/bootstrap.css");
?>
<div class="row panel">
    <div class="col-md-12 heading">
        <h4 class="title-form-review">Оставить отзыв</h4>
    </div>
    <?
    if (!empty($arResult["ERRORS"])):?>
        <?
        ShowError(implode("<br />", $arResult["ERRORS"])) ?>
    <?endif;
    if (strlen($arResult["MESSAGE"]) > 0):?>
        <? ShowNote($arResult["MESSAGE"]) ?>
    <? endif ?>

    <form class="reviews_add" name="iblock_add" action="<?= POST_FORM_ACTION_URI ?>" method="post"
          enctype="multipart/form-data">
        <?= bitrix_sessid_post() ?>
        <? if ($arParams["MAX_FILE_SIZE"] > 0): ?>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?= $arParams["MAX_FILE_SIZE"] ?>"/>
        <? endif ?>
        <? if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])): ?>
            <section>


                <? foreach ($arResult["PROPERTY_LIST"] as $propertyID): ?>
                    <div class="col-md-12 field-input">
                        <div class="form-group">
                            <label for="PROPERTY[<?= $propertyID ?>][<?= $i ?>]">
                                <? if (intval($propertyID) > 0): ?>
                                    <?= $arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"] ?>
                                <? else: ?>
                                    <?= !empty($arParams["CUSTOM_TITLE_" . $propertyID]) ? $arParams["CUSTOM_TITLE_" . $propertyID] : GetMessage("IBLOCK_FIELD_" . $propertyID) ?>
                                <? endif ?>
                                <? if (in_array($propertyID, $arResult["PROPERTY_REQUIRED"])): ?>
                                    <span class="starrequired">*</span>
                                <? endif ?>
                            </label>
                            <div class="input-group">
                                <? if (intval($propertyID) > 0) {
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
                                } elseif (($propertyID == "TAGS") && CModule::IncludeModule('search'))
                                    $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "TAGS";

                                if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y") {
                                    $inputNum = ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) ? count($arResult["ELEMENT_PROPERTIES"][$propertyID]) : 0;
                                    $inputNum += $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE_CNT"];
                                } else {
                                    $inputNum = 1;
                                }

                                if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"])
                                    $INPUT_TYPE = "USER_TYPE";
                                else
                                    $INPUT_TYPE = $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"];

                                switch ($INPUT_TYPE):
                                    case "USER_TYPE":
                                        for ($i = 0; $i < $inputNum; $i++) {
                                            if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
                                                $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["~VALUE"] : $arResult["ELEMENT"][$propertyID];
                                                $description = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["DESCRIPTION"] : "";
                                            } elseif ($i == 0) {
                                                $value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
                                                $description = "";
                                            } else {
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
                                                        "VALUE" => "PROPERTY[" . $propertyID . "][" . $i . "][VALUE]",
                                                        "DESCRIPTION" => "PROPERTY[" . $propertyID . "][" . $i . "][DESCRIPTION]",
                                                        "FORM_NAME" => "iblock_add",
                                                    ),
                                                ));

                                        }
                                        break;
                                    case "TAGS":
                                        $APPLICATION->IncludeComponent(
                                            "bitrix:search.tags.input",
                                            "",
                                            array(
                                                "VALUE" => $arResult["ELEMENT"][$propertyID],
                                                "NAME" => "PROPERTY[" . $propertyID . "][0]",
                                                "TEXT" => 'size="' . $arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"] . '"',
                                            ), null, array("HIDE_ICONS" => "Y")
                                        );
                                        break;
                                    case "HTML":
                                        $LHE = new CHTMLEditor;
                                        $LHE->Show(array(
                                            'name' => "PROPERTY[" . $propertyID . "][0]",
                                            'id' => preg_replace("/[^a-z0-9]/i", '', "PROPERTY[" . $propertyID . "][0]"),
                                            'inputName' => "PROPERTY[" . $propertyID . "][0]",
                                            'content' => $arResult["ELEMENT"][$propertyID],
                                            'width' => '100%',
                                            'minBodyWidth' => 350,
                                            'normalBodyWidth' => 555,
                                            'height' => '200',
                                            'bAllowPhp' => false,
                                            'limitPhpAccess' => false,
                                            'autoResize' => true,
                                            'autoResizeOffset' => 40,
                                            'useFileDialogs' => false,
                                            'saveOnBlur' => true,
                                            'showTaskbars' => false,
                                            'showNodeNavi' => false,
                                            'askBeforeUnloadPage' => true,
                                            'bbCode' => false,
                                            'siteId' => SITE_ID,
                                            'controlsMap' => array(
                                                array('id' => 'Bold', 'compact' => true, 'sort' => 80),
                                                array('id' => 'Italic', 'compact' => true, 'sort' => 90),
                                                array('id' => 'Underline', 'compact' => true, 'sort' => 100),
                                                array('id' => 'Strikeout', 'compact' => true, 'sort' => 110),
                                                array('id' => 'RemoveFormat', 'compact' => true, 'sort' => 120),
                                                array('id' => 'Color', 'compact' => true, 'sort' => 130),
                                                array('id' => 'FontSelector', 'compact' => false, 'sort' => 135),
                                                array('id' => 'FontSize', 'compact' => false, 'sort' => 140),
                                                array('separator' => true, 'compact' => false, 'sort' => 145),
                                                array('id' => 'OrderedList', 'compact' => true, 'sort' => 150),
                                                array('id' => 'UnorderedList', 'compact' => true, 'sort' => 160),
                                                array('id' => 'AlignList', 'compact' => false, 'sort' => 190),
                                                array('separator' => true, 'compact' => false, 'sort' => 200),
                                                array('id' => 'InsertLink', 'compact' => true, 'sort' => 210),
                                                array('id' => 'InsertImage', 'compact' => false, 'sort' => 220),
                                                array('id' => 'InsertVideo', 'compact' => true, 'sort' => 230),
                                                array('id' => 'InsertTable', 'compact' => false, 'sort' => 250),
                                                array('separator' => true, 'compact' => false, 'sort' => 290),
                                                array('id' => 'Fullscreen', 'compact' => false, 'sort' => 310),
                                                array('id' => 'More', 'compact' => true, 'sort' => 400)
                                            ),
                                        ));
                                        break;

                                    case "S":
                                    case "N":
                                        for ($i = 0; $i < $inputNum; $i++) {
                                            if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
                                                $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
                                            } elseif ($i == 0) {
                                                $value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
                                            } else {
                                                $value = "";
                                            } ?>
                                            <input type="text" name="PROPERTY[<?= $propertyID ?>][<?= $i ?>]"
                                                   size="<?= $arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"]; ?>"
                                                   value="<?= $value ?>"/>
                                            <? if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"] == "DateTime"): ?>
                                                <? $APPLICATION->IncludeComponent(
                                                    'bitrix:main.calendar',
                                                    '',
                                                    array(
                                                        'FORM_NAME' => 'iblock_add',
                                                        'INPUT_NAME' => "PROPERTY[" . $propertyID . "][" . $i . "]",
                                                        'INPUT_VALUE' => $value,
                                                    ),
                                                    null,
                                                    array('HIDE_ICONS' => 'Y')
                                                );
                                                ?><br/>
                                                <small><?= GetMessage("IBLOCK_FORM_DATE_FORMAT") ?><?= FORMAT_DATETIME ?></small>

                                            <? endif;
                                        }
                                        break;

                                    case "F":
                                        for ($i = 0; $i < $inputNum; $i++) {
                                            $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
                                            ?>
                                            <input type="hidden"
                                                   name="PROPERTY[<?= $propertyID ?>][<?= $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i ?>]"
                                                   value="<?= $value ?>"/>
                                            <input type="file"
                                                   size="<?= $arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"] ?>"
                                                   name="PROPERTY_FILE_<?= $propertyID ?>_<?= $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i ?>"/>
                                            <br/>
                                            <?

                                            if (!empty($value) && is_array($arResult["ELEMENT_FILES"][$value])) {
                                                ?>
                                                <input type="checkbox"
                                                       name="DELETE_FILE[<?= $propertyID ?>][<?= $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i ?>]"
                                                       id="file_delete_<?= $propertyID ?>_<?= $i ?>" value="Y"/><label
                                                        for="file_delete_<?= $propertyID ?>_<?= $i ?>"><?= GetMessage("IBLOCK_FORM_FILE_DELETE") ?></label>
                                                <br/>
                                                <?

                                                if ($arResult["ELEMENT_FILES"][$value]["IS_IMAGE"]) {
                                                    ?>
                                                    <img src="<?= $arResult["ELEMENT_FILES"][$value]["SRC"] ?>"
                                                         height="<?= $arResult["ELEMENT_FILES"][$value]["HEIGHT"] ?>"
                                                         width="<?= $arResult["ELEMENT_FILES"][$value]["WIDTH"] ?>"
                                                         border="0"/>
                                                    <br/>
                                                    <?
                                                } else {
                                                    ?>
                                                    <?= GetMessage("IBLOCK_FORM_FILE_NAME") ?>: <?= $arResult["ELEMENT_FILES"][$value]["ORIGINAL_NAME"] ?>
                                                    <br/>
                                                    <?= GetMessage("IBLOCK_FORM_FILE_SIZE") ?>: <?= $arResult["ELEMENT_FILES"][$value]["FILE_SIZE"] ?> b
                                                    <br/>
                                                    [
                                                    <a href="<?= $arResult["ELEMENT_FILES"][$value]["SRC"] ?>"><?= GetMessage("IBLOCK_FORM_FILE_DOWNLOAD") ?></a>]
                                                    <br/>
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
                                                foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum) {
                                                    $checked = false;
                                                    if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
                                                        if (is_array($arResult["ELEMENT_PROPERTIES"][$propertyID])) {
                                                            foreach ($arResult["ELEMENT_PROPERTIES"][$propertyID] as $arElEnum) {
                                                                if ($arElEnum["VALUE"] == $key) {
                                                                    $checked = true;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        if ($arEnum["DEF"] == "Y") $checked = true;
                                                    }

                                                    ?>
                                                    <input type="<?= $type ?>"
                                                           name="PROPERTY[<?= $propertyID ?>]<?= $type == "checkbox" ? "[" . $key . "]" : "" ?>"
                                                           value="<?= $key ?>"
                                                           id="property_<?= $key ?>"<?= $checked ? " checked=\"checked\"" : "" ?> />
                                                    <label for="property_<?= $key ?>"><?= $arEnum["VALUE"] ?></label>

                                                    <?
                                                }
                                                break;

                                            case "dropdown":
                                            case "multiselect":
                                                ?>
                                                <select name="PROPERTY[<?= $propertyID ?>]<?= $type == "multiselect" ? "[]\" size=\"" . $arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] . "\" multiple=\"multiple" : "" ?>">
                                                    <option value=""><?
                                                        echo GetMessage("CT_BIEAF_PROPERTY_VALUE_NA") ?></option>
                                                    <?
                                                    if (intval($propertyID) > 0) $sKey = "ELEMENT_PROPERTIES";
                                                    else $sKey = "ELEMENT";

                                                    foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum) {
                                                        $checked = false;
                                                        if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
                                                            foreach ($arResult[$sKey][$propertyID] as $elKey => $arElEnum) {
                                                                if ($key == $arElEnum["VALUE"]) {
                                                                    $checked = true;
                                                                    break;
                                                                }
                                                            }
                                                        } else {
                                                            if ($arEnum["DEF"] == "Y") $checked = true;
                                                        }
                                                        ?>
                                                        <option value="<?= $key ?>" <?= $checked ? " selected=\"selected\"" : "" ?>><?= $arEnum["VALUE"] ?></option>
                                                        <?
                                                    }
                                                    ?>
                                                </select>
                                                <?
                                                break;

                                        endswitch;
                                        break;
                                    case "T":
                                        for ($i = 0; $i < $inputNum; $i++) {

                                            if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
                                                $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
                                            } elseif ($i == 0) {
                                                $value = intval($propertyID) > 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
                                            } else {
                                                $value = "";
                                            }
                                            ?>


                                            <textarea
                                                    cols="<?= $arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"] ?>"
                                                    rows="3<?/*= $arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] */?>"
                                                    name="PROPERTY[<?= $propertyID ?>][<?= $i ?>]"><?= $value ?></textarea>
                                            <?
                                        }
                                        break;

                                endswitch; ?>
                            </div>
                        </div>
                    </div>
                <? endforeach; ?>
                <? if ($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0): ?>

                    <div><?= GetMessage("IBLOCK_FORM_CAPTCHA_TITLE") ?></div>
                    <div>
                        <input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>"/>
                        <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>"
                             width="180"
                             height="40" alt="CAPTCHA"/>
                    </div>
                    <?= GetMessage("IBLOCK_FORM_CAPTCHA_PROMPT") ?><span class="starrequired">*</span>:
                    <input type="text" name="captcha_word" maxlength="50" value="">

                <? endif ?>
            </section>
        <? endif ?>

        <div class="col-md-12">

            <input class="btn submit-button wrap-bttn" type="submit" name="iblock_submit"
                   value="<?= GetMessage("IBLOCK_FORM_SUBMIT") ?>"/>
            <? if (strlen($arParams["LIST_URL"]) > 0): ?>
                <input class="btn submit-button wrap-bttn" type="submit" name="iblock_apply"
                       value="<?= GetMessage("IBLOCK_FORM_APPLY") ?>"/>
                <input class="btn submit-button wrap-bttn"
                       type="button"
                       name="iblock_cancel"
                       value="<? echo GetMessage('IBLOCK_FORM_CANCEL'); ?>"
                       onclick="location.href='<? echo CUtil::JSEscape($arParams["LIST_URL"]) ?>';"
                >
            <? endif ?>
        </div>

    </form>
</div>
<?$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "reviews",
    Array(
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "NEWS_COUNT" => $arParams["NEWS_COUNT"],
        "SORT_BY1" => $arParams["SORT_BY1"],
        "SORT_ORDER1" => $arParams["SORT_ORDER1"],
        "SORT_BY2" => $arParams["SORT_BY2"],
        "SORT_ORDER2" => $arParams["SORT_ORDER2"],
        "FIELD_CODE" => $arParams["LIST_FIELD_CODE"],
        "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
        "DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
        "SET_TITLE" => $arParams["SET_TITLE"],
        "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
        "MESSAGE_404" => $arParams["MESSAGE_404"],
        "SET_STATUS_404" => $arParams["SET_STATUS_404"],
        "SHOW_404" => $arParams["SHOW_404"],
        "FILE_404" => $arParams["FILE_404"],
        "INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
        "ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_FILTER" => $arParams["CACHE_FILTER"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
        "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
        "PAGER_TITLE" => $arParams["PAGER_TITLE"],
        "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
        "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
        "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
        "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
        "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
        "PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
        "PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
        "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
        "DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
        "DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
        "PREVIEW_TRUNCATE_LEN" => $arParams["PREVIEW_TRUNCATE_LEN"],
        "ACTIVE_DATE_FORMAT" => $arParams["LIST_ACTIVE_DATE_FORMAT"],
        "USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
        "GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
        "FILTER_NAME" => $arParams["FILTER_NAME"],
        "HIDE_LINK_WHEN_NO_DETAIL" => $arParams["HIDE_LINK_WHEN_NO_DETAIL"],
        "CHECK_DATES" => $arParams["CHECK_DATES"],
        "STRICT_SECTION_CHECK" => $arParams["STRICT_SECTION_CHECK"],

        "PARENT_SECTION" => $arResult["VARIABLES"]["SECTION_ID"],
        "PARENT_SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
        "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
        "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
        "IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
    ),
    $component
);?>