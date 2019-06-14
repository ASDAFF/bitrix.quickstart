<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

use Bitrix\Seo\Engine;
use Bitrix\Main\Text\Converter;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\IO\Path;

\Bitrix\Main\Loader::includeModule('seo');
\Bitrix\Main\Loader::includeModule('socialservices');
$tabControl->BeginNextTab();
$arEncoding['reference'] = array('utf-8', 'windows-1251');
$arEncoding['reference_id'] = array('utf-8', 'windows-1251');
$arType['reference'] = array('html', 'text');
$arType['reference_id'] = array('html', 'text');

$arTypeParser['reference'] = array('rss', 'page', 'catalog', 'xml');
$arTypeParser['reference_id'] = array('rss', 'page', 'catalog', 'xml');

$arrDate = ParseDateTime($sota_START_LAST_TIME_X, "YYYY.MM.DD HH:MI:SS");

$disabled = false;

$arrUniq["reference"] = array(GetMessage("parser_page_uniq_name"), GetMessage("parser_page_uniq_url"));
$arrUniq["reference_id"] = array("name", "url");

if ($sota_TYPE) $disabled = 'disabled=""';
?>
    <tr>
        <td><? echo GetMessage("parser_type") ?></td>
        <td><?= SelectBoxFromArray('TYPE', $arTypeParser, $sota_TYPE ? $sota_TYPE : $_GET["type"], "", $disabled); ?>
            <? if ($disabled): ?><input type="hidden" name="TYPE" value="<?= $sota_TYPE ?>" /><? endif; ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_act") ?></td>
        <td width="60%"><input type="checkbox" name="ACTIVE"
                               value="Y"<? if ($sota_ACTIVE == "Y" || !$ID) echo " checked" ?>>
        </td>
    </tr>
    <tr>
        <td><? echo GetMessage("parser_sort") ?></td>
        <td><input type="text" name="SORT" value="<? echo !$ID ? "100" : $sota_SORT; ?>" size="4"></td>
    </tr>
<? if (isset($arCategory) && !empty($arCategory)): ?>
    <tr>
        <td><? echo GetMessage("parser_category_title") ?></td>
        <td><?= SelectBoxFromArray('CATEGORY_ID', $arCategory, isset($sota_CATEGORY_ID) ? $sota_CATEGORY_ID : $parentID, GetMessage("parser_category_select"), "id='category' style='width:262px'"); ?></td>
    </tr>
<? endif; ?>
    <tr>
        <td><span class="required">*</span><? echo GetMessage("parser_name") ?></td>
        <td><input type="text" name="NAME" value="<? echo $sota_NAME; ?>" size="40" maxlength="250"></td>
    </tr>
    <tr>
        <td><span class="required">*</span><? echo GetMessage("parser_rss") ?></td>
        <td><input type="text" name="RSS" value="<? echo $sota_RSS; ?>" size="40" maxlength="250"></td>
    </tr>
    <tr>
        <td><span class="required">*</span><? echo GetMessage("parser_iblock_id") ?></td>
        <td><?= SelectBoxFromArray('IBLOCK_ID', $arIBlock, $sota_IBLOCK_ID, GetMessage("parser_iblock_id"), "id='iblock' style='width:262px'"); ?></td>
    </tr>
    <tr>
        <td><? echo GetMessage("parser_section_id") ?></td>
        <td><?= SelectBoxFromArray('SECTION_ID', $arSection, $sota_SECTION_ID, GetMessage("parser_section_id"), "id='section' style='width:262px'"); ?></td>
    </tr>
    <tr>
        <td><? echo GetMessage("parser_selector") ?></td>
        <td><input type="text" name="SELECTOR" value="<? echo $sota_SELECTOR; ?>" size="40" maxlength="250"></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_selector_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
    <tr>
        <td><? echo GetMessage("parser_first_url") ?></td>
        <td><input type="text" name="FIRST_URL" value="<? echo $sota_FIRST_URL; ?>" size="40" maxlength="250"></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_first_url_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
    <tr>
        <td><? echo GetMessage("parser_encoding") ?></td>
        <td><?= SelectBoxFromArray('ENCODING', $arEncoding, $sota_ENCODING); ?></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_encoding_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
    <tr>
        <td><? echo GetMessage("parser_start_last_time") ?></td>
        <td><input type="text" disabled name="START_LAST_TIME_X"
                   value="<? echo $arrDate[DD] . '.' . $arrDate[MM] . '.' . $arrDate[YYYY] . ' ' . $arrDate[HH] . ':' . $arrDate[MI] . ':' . $arrDate[SS]; ?>"
                   size="20"></td>
    </tr>
<?
//********************
//Auto params
//********************
?>
<?
//********************
//Attachments
//********************
$tabControl->BeginNextTab();
?>
    <tr>
        <td><? echo GetMessage("parser_preview_text_type") ?></td>
        <td><?= SelectBoxFromArray('PREVIEW_TEXT_TYPE', $arType, $sota_PREVIEW_TEXT_TYPE, "", ""); ?></td>
    </tr>
    <tr>
        <td><? echo GetMessage("parser_preview_delete_tag") ?></td>
        <td><input class="bool-delete" type="checkbox" name="BOOL_PREVIEW_DELETE_TAG"
                   value="Y"<? if ($sota_BOOL_PREVIEW_DELETE_TAG == "Y") echo " checked" ?>> <? echo GetMessage("parser_bool_preview_delete_tag") ?>
            <input <? if ($sota_BOOL_PREVIEW_DELETE_TAG != "Y"): ?>disabled <? endif ?> type="text"
                   name="PREVIEW_DELETE_TAG" value="<? echo $sota_PREVIEW_DELETE_TAG; ?>" size="40" maxlength="300">
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_preview_delete_tag_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_preview_first_img") ?></td>
        <td width="60%"><input type="checkbox" name="PREVIEW_FIRST_IMG"
                               value="Y"<? if ($sota_PREVIEW_FIRST_IMG == "Y") echo " checked" ?>></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_preview_first_img_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_preview_save_img") ?></td>
        <td width="60%"><input type="checkbox" name="PREVIEW_SAVE_IMG"
                               value="Y"<? if ($sota_PREVIEW_SAVE_IMG == "Y") echo " checked" ?>></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_preview_save_img_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_preview_delete_element") ?></td>
        <td width="60%"><input size="80" maxlength="300" type="text" name="PREVIEW_DELETE_ELEMENT"
                               value="<?= $sota_PREVIEW_DELETE_ELEMENT ?>"></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_preview_delete_element_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_preview_delete_attribute") ?></td>
        <td width="60%"><input size="80" maxlength="300" type="text" name="PREVIEW_DELETE_ATTRIBUTE"
                               value="<?= $sota_PREVIEW_DELETE_ATTRIBUTE ?>"></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_preview_delete_attribute_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>

<?
//********************
//Attachments
//********************
$tabControl->BeginNextTab();
?>
    <tr>
        <td><? echo GetMessage("parser_detail_text_type") ?></td>
        <td><?= SelectBoxFromArray('DETAIL_TEXT_TYPE', $arType, $sota_DETAIL_TEXT_TYPE, "", ""); ?></td>
    </tr>
    <tr>
        <td><? echo GetMessage("parser_detail_delete_tag") ?></td>
        <td><input class="bool-delete" type="checkbox" name="BOOL_DETAIL_DELETE_TAG"
                   value="Y"<? if ($sota_BOOL_DETAIL_DELETE_TAG == "Y") echo " checked" ?>> <? echo GetMessage("parser_bool_detail_delete_tag") ?>
            <input <? if ($sota_BOOL_DETAIL_DELETE_TAG != "Y"): ?>disabled <? endif ?> type="text"
                   name="DETAIL_DELETE_TAG" value="<? echo $sota_DETAIL_DELETE_TAG; ?>" size="40" maxlength="300"></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_preview_delete_tag_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_detail_first_img") ?></td>
        <td width="60%"><input type="checkbox" name="DETAIL_FIRST_IMG"
                               value="Y"<? if ($sota_DETAIL_FIRST_IMG == "Y") echo " checked" ?>></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_detail_first_img_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_detail_save_img") ?></td>
        <td width="60%"><input type="checkbox" name="DETAIL_SAVE_IMG"
                               value="Y"<? if ($sota_DETAIL_SAVE_IMG == "Y") echo " checked" ?>></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_detail_save_img_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_detail_delete_element") ?></td>
        <td width="60%"><input size="80" maxlength="300" type="text" name="DETAIL_DELETE_ELEMENT"
                               value="<?= $sota_DETAIL_DELETE_ELEMENT ?>"></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_detail_delete_element_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_detail_delete_attribute") ?></td>
        <td width="60%"><input size="80" maxlength="300" type="text" name="DETAIL_DELETE_ATTRIBUTE"
                               value="<?= $sota_DETAIL_DELETE_ATTRIBUTE ?>"></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_detail_delete_attribute_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>

<?
$tabControl->BeginNextTab();

?>
    <tr>
        <td width="40%"><? echo GetMessage("parser_active_element") ?></td>
        <td width="60%"><input type="checkbox" name="ACTIVE_ELEMENT"
                               value="Y"<? if ($sota_ACTIVE_ELEMENT == "Y") echo " checked" ?>></td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_code_element") ?></td>
        <td width="60%"><input type="checkbox" name="CODE_ELEMENT"
                               value="Y"<? if ($sota_CODE_ELEMENT == "Y") echo " checked" ?>></td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_index_element") ?></td>
        <td width="60%"><input type="checkbox" name="INDEX_ELEMENT"
                               value="Y"<? if ($sota_INDEX_ELEMENT == "Y") echo " checked" ?>></td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_resize_image") ?></td>
        <td width="60%"><input type="checkbox" name="RESIZE_IMAGE"
                               value="Y"<? if ($sota_RESIZE_IMAGE == "Y") echo " checked" ?>></td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_date_active") ?></td>
        <td width="60%"><input type="checkbox" name="DATE_ACTIVE"
                               value="Y"<? if ($sota_DATE_ACTIVE && $sota_DATE_ACTIVE != "N") echo " checked" ?>> <?= SelectBoxFromArray('DATE_PROP_ACTIVE', $arrDateActive, $sota_DATE_ACTIVE, GetMessage("parser_date_type"), "id='prop-active' style='width:262px'"); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_date_public") ?></td>
        <td width="60%"><input type="checkbox" name="DATE_PUBLIC"
                               value="Y"<? if ($sota_DATE_PUBLIC && $sota_DATE_PUBLIC != "N") echo " checked" ?>> <?= SelectBoxFromArray('DATE_PROP_PUBLIC', $arrProp, $sota_DATE_PUBLIC, GetMessage("parser_prop_id"), "id='prop-date' style='width:262px' class='prop-iblock'"); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_first_title") ?></td>
        <td width="60%"><input type="checkbox" name="FIRST_TITLE"
                               value="Y"<? if ($sota_FIRST_TITLE && $sota_FIRST_TITLE != "N") echo " checked" ?>> <?= SelectBoxFromArray('FIRST_PROP_TITLE', $arrProp, $sota_FIRST_TITLE, GetMessage("parser_prop_id"), "id='prop-first' style='width:262px' class='prop-iblock'"); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_meta_title") ?></td>
        <td width="60%"><input type="checkbox" name="META_TITLE"
                               value="Y"<? if ($sota_META_TITLE && $sota_META_TITLE != "N") echo " checked" ?>> <?= SelectBoxFromArray('META_PROP_TITLE', $arrProp, $sota_META_TITLE, GetMessage("parser_prop_id"), "id='prop-title' style='width:262px' class='prop-iblock'"); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_meta_description") ?></td>
        <td width="60%"><input type="checkbox" name="META_DESCRIPTION"
                               value="Y"<? if ($sota_META_DESCRIPTION && $sota_META_DESCRIPTION != "N") echo " checked" ?>> <?= SelectBoxFromArray('META_PROP_DESCRIPTION', $arrProp, $sota_META_DESCRIPTION, GetMessage("parser_prop_id"), "id='prop-key' style='width:262px' class='prop-iblock'"); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_meta_keywords") ?></td>
        <td width="60%"><input type="checkbox" name="META_KEYWORDS"
                               value="Y"<? if ($sota_META_KEYWORDS && $sota_META_KEYWORDS != "N") echo " checked" ?>> <?= SelectBoxFromArray('META_PROP_KEYWORDS', $arrProp, $sota_META_KEYWORDS, GetMessage("parser_prop_id"), "id='prop-meta' style='width:262px' class='prop-iblock'"); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_page_uniq") ?></td>
        <td width="60%">
            <?= SelectBoxFromArray('SETTINGS[rss][uniq]', $arrUniq, $sota_SETTINGS["rss"]["uniq"]); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_start_agent") ?></td>
        <td width="60%"><input type="checkbox" name="START_AGENT"
                               value="Y"<? if ($sota_START_AGENT == "Y") echo " checked" ?>></td>
    </tr>

    <tr>
        <td width="40%"><? echo GetMessage("parser_time_agent") ?></td>
        <td width="60%"><input type="text" size="40" name="TIME_AGENT" value="<?= $sota_TIME_AGENT ?>"></td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_sleep") ?></td>
        <td width="60%"><input type="text" size="40" name="SETTINGS[rss][sleep]"
                               value="<?= $sota_SETTINGS["rss"]["sleep"] ?>"></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_sleep_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_proxy") ?></td>
        <td width="60%"><input type="text" size="40" name="SETTINGS[rss][proxy]"
                               value="<?= $sota_SETTINGS["rss"]["proxy"] ?>"></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_proxy_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
<?
$tabControl->BeginNextTab();
?>
    <tr class="heading">
        <td colspan="2"><? echo GetMessage("parser_loc_type_head") ?></td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_loc_type") ?>:</td>
        <td width="60%">
            <?= SelectBoxFromArray('SETTINGS[loc][type]', $arLocType, $sota_SETTINGS["loc"]["type"], "", "class='select_load'"); ?>
        </td>
    </tr>
<? if (isset($sota_SETTINGS["loc"]["type"]) && $sota_SETTINGS["loc"]["type"] == "yandex"): ?>
    <tr>
        <td width="40%"><? echo GetMessage("parser_loc_yandex_key") ?>:</td>
        <td width="60%"><input type="text" size="40" name="SETTINGS[loc][yandex][key]"
                               value="<?= $sota_SETTINGS["loc"]["yandex"]["key"] ?>"></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_loc_yandex_key_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_loc_yandex_lang") ?>:</td>
        <td width="60%"><input type="text" size="20" name="SETTINGS[loc][yandex][lang]"
                               value="<?= $sota_SETTINGS["loc"]["yandex"]["lang"] ?>"></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_loc_yandex_lang_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
    <tr class="heading">
        <td colspan="2"><? echo GetMessage("parser_loc_fields") ?></td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_loc_fields_name") ?>:</td>
        <td width="60%"><input type="checkbox" name="SETTINGS[loc][f_name]"
                               value="Y"<? if ($sota_SETTINGS["loc"]["f_name"] == "Y") echo " checked" ?>></td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_loc_fields_preview_text") ?>:</td>
        <td width="60%"><input type="checkbox" name="SETTINGS[loc][f_preview_text]"
                               value="Y"<? if ($sota_SETTINGS["loc"]["f_preview_text"] == "Y") echo " checked" ?>></td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_loc_fields_detail_text") ?>:</td>
        <td width="60%"><input type="checkbox" name="SETTINGS[loc][f_detail_text]"
                               value="Y"<? if ($sota_SETTINGS["loc"]["f_detail_text"] == "Y") echo " checked" ?>></td>
    </tr>
    <tr>
        <td width="40%"><? echo GetMessage("parser_loc_fields_props") ?>:</td>
        <td width="60%"><input type="checkbox" name="SETTINGS[loc][f_props]"
                               value="Y"<? if ($sota_SETTINGS["loc"]["f_props"] == "Y") echo " checked" ?>></td>
    </tr>
<? endif; ?>
    <tr class="heading">
        <td colspan="2"><? echo GetMessage("parser_loc_uniq") ?></td>
    </tr>
<?
$engine = new Engine\Yandex();
$arSettings = $engine->getSettings();
$arDomains = \CSeoUtils::getDomainsList();

foreach ($arDomains as $key => $domain) {
    if (!isset($arSettings['SITES'][$domain['DOMAIN']])) {
        unset($arDomains[$key]);
    }
}

if (count($arDomains) <= 0) {
    $msg = new CAdminMessage(array(
        'MESSAGE' => Loc::getMessage('SOTA_PARSER_SEO_YANDEX_ERROR'),
        'HTML' => 'Y'
    ));
} else {
    $arrDomain['REFERENCE'][] = Loc::getMessage('sota_parser_loc_uniq_no');
    $arrDomain['REFERENCE_ID'][] = "";
    foreach ($arDomains as $domain) {   //printr($domain);
        $domainEnc = Converter::getHtmlConverter()->encode($domain['DOMAIN']);
        $arrDomain['REFERENCE'][] = $domainEnc;
        $arrDomain['REFERENCE_ID'][] = $domainEnc;
    }
}
?>
<? if (count($arDomains) <= 0): ?>
    <tr>
        <td colspan="2" align="center"><? echo $msg->Show(); ?></td>
    </tr>
<? else: ?>
    <tr>
        <td><? echo GetMessage("parser_loc_uniq_domain") ?>:</td>
        <td><?= SelectBoxFromArray('SETTINGS[loc][uniq][domain]', $arrDomain, $sota_SETTINGS["loc"]["uniq"]["domain"], "", ""); ?></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?= BeginNote(); ?>
            <? echo GetMessage("parser_loc_uniq_domain_descr") ?>
            <?= EndNote(); ?>
        </td>
    </tr>
<? endif ?>


<?
$tabControl->Buttons(
    array(
        "disabled" => ($POST_RIGHT < "W"),
        "back_url" => "list_parser_admin.php?lang=" . LANG,

    )
);
?>
<? echo bitrix_sessid_post(); ?>
    <input type="hidden" name="lang" value="<?= LANG ?>">
<? if ($ID > 0 && !$bCopy): ?>
    <input type="hidden" name="ID" value="<?= $ID ?>">
<? endif; ?>
<?
$tabControl->End();
?>

<?
$tabControl->ShowWarnings("post_form", $message);
?>