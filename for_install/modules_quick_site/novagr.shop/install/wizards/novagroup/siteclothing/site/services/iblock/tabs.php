<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

if (!CModule::IncludeModule("iblock"))
    return;

WizardServices::IncludeServiceLang("tabs.php", "ru");

$rsIBlock = CIBlock::GetList(array(), array("CODE" => "novagr_standard_products"));
while ($arIBlock = $rsIBlock->Fetch()) {

    $properties = CIBlockProperty::GetList(Array("sort" => "asc"), Array("IBLOCK_ID" => $arIBlock['ID']));
    $TIMEBUY_FORM_FIELDS = array();
    $FORM_FIELDS = Array(
        "ACTIVE" => GetMessage("ACTIVE"),
        "ACTIVE_FROM" => GetMessage("ACTIVE_FROM"),
        "ACTIVE_TO" => GetMessage("ACTIVE_TO"),
        "NAME" => GetMessage("NAME"),
        "CODE" => GetMessage("CODE"),
        "XML_ID" => GetMessage("XML_ID"),
        "SORT" => GetMessage("SORT"),
        "IBLOCK_ELEMENT_PROPERTY" => GetMessage("IBLOCK_ELEMENT_PROPERTY"),
        "IBLOCK_ELEMENT_PROP_VALUE" => GetMessage("IBLOCK_ELEMENT_PROP_VALUE")
    );
    while ($prop_fields = $properties->GetNext()) {
        if (in_array($prop_fields['CODE'], array("TIMETOBUYACTIVETO", "DISCOUNT", "QUANTITY"))) {
            $TIMEBUY_FORM_FIELDS["PROPERTY_" . $prop_fields["ID"]] = $prop_fields["NAME"];
        } else {
            $FORM_FIELDS["PROPERTY_" . $prop_fields["ID"]] = $prop_fields["NAME"];
        }
    }
    $FORM_FIELDS["LINKED_PROP"] = GetMessage("LINKED_PROP");

    $customTabber = Array(
        "edit1" => Array(
            "TAB" => GetMessage("TAB_PROPERTY"),
            "FIELDS" => $FORM_FIELDS
        ),
        "edit5" => Array(
            "TAB" => GetMessage("TAB_PREVIEW"),
            "FIELDS" => Array(
                "PREVIEW_PICTURE" => GetMessage("PREVIEW_PICTURE"),
                "PREVIEW_TEXT" => GetMessage("PREVIEW_TEXT")
            )
        ),
        "edit6" => Array(
            "TAB" => GetMessage("TAB_DETAIL"),
            "FIELDS" => Array(
                "DETAIL_PICTURE" => GetMessage("DETAIL_PICTURE"),
                "DETAIL_TEXT" => GetMessage("DETAIL_TEXT")
            )
        ),
        "edit14" => Array(
            "TAB" => "SEO",
            "FIELDS" => Array(
                "IPROPERTY_TEMPLATES_ELEMENT_META_TITLE" => GetMessage("IPROPERTY_TEMPLATES_ELEMENT_META_TITLE"),
                "IPROPERTY_TEMPLATES_ELEMENT_META_KEYWORDS" => GetMessage("IPROPERTY_TEMPLATES_ELEMENT_META_KEYWORDS"),
                "IPROPERTY_TEMPLATES_ELEMENT_META_DESCRIPTION" => GetMessage("IPROPERTY_TEMPLATES_ELEMENT_META_DESCRIPTION"),
                "IPROPERTY_TEMPLATES_ELEMENT_PAGE_TITLE" => GetMessage("IPROPERTY_TEMPLATES_ELEMENT_PAGE_TITLE"),
                "IPROPERTY_TEMPLATES_ELEMENTS_PREVIEW_PICTURE" => GetMessage("IPROPERTY_TEMPLATES_ELEMENTS_PREVIEW_PICTURE"),
                "IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_ALT" => GetMessage("IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_ALT"),
                "IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_TITLE" => GetMessage("IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_TITLE"),
                "IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_NAME" => GetMessage("IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_NAME"),
                "IPROPERTY_TEMPLATES_ELEMENTS_DETAIL_PICTURE" => GetMessage("IPROPERTY_TEMPLATES_ELEMENTS_DETAIL_PICTURE"),
                "IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_ALT" => GetMessage("IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_ALT"),
                "IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_TITLE" => GetMessage("IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_TITLE"),
                "IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_NAME" => GetMessage("IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_NAME"),
                "SEO_ADDITIONAL" => GetMessage("SEO_ADDITIONAL"),
                "TAGS" => GetMessage("TAGS")
            )),
        "edit2" => Array(
            "TAB" => GetMessage("SECTIONS"),
            "FIELDS" => Array(
                "SECTIONS" => GetMessage("SECTIONS")
            )
        ),
        "edit8" => Array(
            "TAB" => GetMessage("OFFERS"),
            "FIELDS" => Array(
                "OFFERS" => GetMessage("OFFERS")
            )
        ),
        "cedit1" => Array(
            "TAB" => GetMessage("TIMETOBUY"),
            "FIELDS" => $TIMEBUY_FORM_FIELDS
        )
    );

    $result = CAdminFormSettings::setTabsArray("form_element_" . $arIBlock['ID'], $customTabber);
}