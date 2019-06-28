<? $GLOBALS['_1099281248_'] = Array(
    'is_array',
    'is_numeric',
    'is_numeric',
    'is_numeric',
    'is_array',
    'is_array',
    'is_numeric',
    'floatval',
    'floatval',
    'floatval',
    'intval',
    'in_array',
    'preg_match',
    'preg_quote',
    'in_array',
    'preg_match',
    'preg_quote',
    'in_array',
    'is_array',
    'is_array',
    'exec',
    'in_array',
    'in_array',
    'in_array',
    'array_merge',
    'intval',
    'intval',
    'intval',
    'is_numeric',
    'intval',
    'intval',
    'is_numeric',
    'intval',
    'floatval',
    'floatval',
    'is_array',
    'is_numeric',
    'floatval');
?>

<? function _537036741($i)
{
    $a = Array(
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
        'PREVIEW_PICTURE',
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
        'DETAIL_PICTURE',
        'DETAIL_PICTURE',
        'DETAIL_PICTURE',
        'PREVIEW_PICTURE',
        'PREVIEW_PICTURE',
        'ID',
        'ID',
        'SRC',
        'src',
        'HEIGHT',
        'height',
        'WIDTH',
        'width',
        'SRC',
        'PROPERTIES',
        'PROPERTIES', '_', 'ID',
        'PROPERTIES', '_', 'ID',
        'VALUE',
        'PROPERTIES', '_', 'ID',
        'VALUE_XML_ID',
        'TYPE',
        'CODE',
        'TYPE',
        'CODE',
        "NAME",
        'N',
        "PROPERTY_TYPE",
        "N",
        "USER_TYPE",
        "",
        "IS_REQUIRED",
        "Y",
        "MULTIPLE",
        "N",
        "DEFAULT_VALUE",
        "SMART_FILTER",
        "N",
        'IBLOCK_ID',
        'CODE',
        "NAME",
        'N',
        "PROPERTY_TYPE",
        "N",
        "USER_TYPE",
        "",
        "IS_REQUIRED",
        "Y",
        "MULTIPLE",
        "N",
        "DEFAULT_VALUE",
        "SMART_FILTER",
        "N",
        'IBLOCK_ID',
        'CODE', '_', 'ID',
        "NAME", 'N',
        "PROPERTY_TYPE",
        "L",
        "LIST_TYPE",
        "L",
        "USER_TYPE",
        "",
        "IS_REQUIRED",
        "N",
        "MULTIPLE",
        "N",
        "DEFAULT_VALUE",
        "SMART_FILTER",
        "N",
        'ID',
        'IBLOCK_ID',
        'CODE',
        'XML_ID',
        'CODE',
        "VALUE",
        '[', 'CODE', '] ', 'LANG', 'NAME', "DEF", 'BASE', "SORT", 'SORT', 'PROPERTY_ID', 'XML_ID', 'CODE', 'CODE', 'XML_ID', 'ID', '_', 'ID', "NAME", 'LANG', 'NAME', "SORT", "ACTIVE", 'ACTIVE', "PROPERTY_TYPE", "N", "USER_TYPE", "", "IS_REQUIRED", "N", "MULTIPLE", "N", "DEFAULT_VALUE", "SMART_FILTER", "Y", "DISPLAY_TYPE", "A", 'IBLOCK_ID', 'CODE', '/^(', ')/', 'CODE', 'CODE', 'ID', '/^(', ')/', 'CODE', 'CODE', 'ID', 'STARTSHOP', 'OFFERS', 'STARTSHOP', 'OFFERS', 'STARTSHOP', 'PRICES', 'MINIMAL', 'VALUE', 'VALUE', 'STARTSHOP', 'PRICES', 'MINIMAL', 'OFFERS_IBLOCK', 'OFFERS_LINK_PROPERTY', 'OFFERS_PROPERTIES', 'OFFERS_PROPERTIES', 'PROPERTY_TYPE', "L", 'USER_TYPE', 'MULTIPLE', "N", "ID", 'ID', "CODE", "CODE", "NAME", 'NAME', "TYPE", "TEXT", "VALUES", "XML_ID", 'VALUES', "XML_ID", "ID", "ID", "CODE", "XML_ID", "TEXT", "VALUE", 'PROPERTY_TYPE', "S", 'USER_TYPE', "directory", 'MULTIPLE', "N", "filter", "TABLE_NAME", 'USER_TYPE_SETTINGS', 'TABLE_NAME', '*', 'UF_XML_ID', 'UF_NAME', 'UF_FILE', "ID", 'ID', "CODE", "CODE", "NAME", 'NAME', "TYPE", "PICTURE", "VALUES", "VALUES", "UF_XML_ID", "ID", "ID", "CODE", "UF_XML_ID", "TEXT", "UF_NAME", "PICTURE", "ID", 'ID', "CODE", "CODE", "NAME", 'NAME', "TYPE", "TEXT", "VALUES", "VALUES",
        "UF_XML_ID", "ID", "ID", "CODE", "UF_XML_ID", "TEXT", "UF_NAME", 'PROPERTY_TYPE', "L", 'USER_TYPE', 'VALUE', 'VALUES', 'VALUE_XML_ID', 'VALUES', 'PROPERTY_TYPE', "S", 'USER_TYPE', "directory", 'VALUE', 'VALUES', 'VALUE', 'VALUES', 'VALUE', 'PROPERTIES', 'OFFERS', 'STARTSHOP', 'OFFER', 'PROPERTIES', 'PROPERTIES', 'STARTSHOP', 'OFFERS', "ID", 'ID', 'STARTSHOP', 'DATA', 'DATA', 'STARTSHOP', 'OFFERS', 'ID', "OFFERS_IBLOCK", "OFFERS_LINK_PROPERTY", "PROPERTY_TYPE", "E", "USER_TYPE", "MULTIPLE", "N", "ACTIVE", "Y", "ACTIVE", "Y", "MULTIPLE", "N", "ACTIVE", "Y", "USER_TYPE_ID", "string", "MULTIPLE", "N", "ID", 'OFFERS_IBLOCK', 'OFFERS_LINK_PROPERTY', 'OFFERS_IBLOCK', 'OFFERS_LINK_PROPERTY', 'OFFERS_IBLOCK', 'OFFERS_LINK_PROPERTY', 'OFFERS_IBLOCK', 'OFFERS_LINK_PROPERTY', 'ID', 'IBLOCK_ID', 'ID', 'IBLOCK_ID', '_', 'ID', '_', 'ID', "", 'ID', "", 'ID', 'IBLOCK_ID');
    return $a[$i];
} ?>

<? IncludeModuleLangFile(__FILE__);

use Bitrix\Highloadblock as HLBlock;
use Bitrix\Main\Entity;

class CStartShopToolsIBlock
{
    private static $PrefixQuantity = "STARTSHOP_QUANTITY";
    private static $PrefixQuantityRatio = "STARTSHOP_QUANTITY_RATIO";
    private static $PrefixPrice = "STARTSHOP_PRICE";
    private static $PrefixCurrency = "STARTSHOP_CURRENCY";
    private static $PrefixPictures = "STARTSHOP_PICTURES";
    private static $PrefixArticle = "STARTSHOP_ARTICLE";
    private static $PrefixTraits = "STARTSHOP_TRAITS";
    private static $PrefixOfferLink = "STARTSHOP_OFFER_LINK";
    private static $PrefixUserExternalId = "STARTSHOP_USERID";

    public static function GetQuantityPrefix()
    {
        return static::$PrefixQuantity;
    }

    public static function GetQuantityRatioPrefix()
    {
        return static::$PrefixQuantityRatio;
    }

    public static function GetPricePrefix()
    {
        return static::$PrefixPrice;
    }

    public static function GetCurrencyPrefix()
    {
        return static::$PrefixCurrency;
    }

    public static function GetPicturesPrefix()
    {
        return static::$PrefixPictures;
    }

    public static function GetArticlePrefix()
    {
        return static::$PrefixArticle;
    }

    public static function GetTraitsPrefix()
    {
        return static::$PrefixTraits;
    }

    public static function GetItemPicture($arItem, $iWidth = false, $iHeight = false, $bPreviewMain = false, $iResizeMode = BX_RESIZE_IMAGE_PROPORTIONAL_ALT)
    {
        $arPicture = array();
        if (!empty($arItem['PREVIEW_PICTURE']) || !empty($arItem['DETAIL_PICTURE'])) {
            if ($bPreviewMain) {
                if (!empty($arItem['PREVIEW_PICTURE'])) $arPicture = $arItem['PREVIEW_PICTURE'];
                if (empty($arPicture) && !empty($arItem['DETAIL_PICTURE'])) $arPicture = $arItem['DETAIL_PICTURE'];
            } else {
                if (!empty($arItem['DETAIL_PICTURE'])) $arPicture = $arItem['DETAIL_PICTURE'];
                if (empty($arPicture) && !empty($arItem['PREVIEW_PICTURE'])) $arPicture = $arItem['PREVIEW_PICTURE'];
            }
            if (is_array($arPicture)) if (is_numeric($arPicture['ID'])) {
                $arPicture = $arPicture['ID'];
            } else {
                $arPicture = array();
            }
            if (!empty($arPicture)) {
                $arPicture = CFile::GetByID($arPicture)->Fetch();
                if (!empty($arPicture)) {
                    if (is_numeric($iWidth) && is_numeric($iHeight)) {
                        $arSizedPicture = CFile::ResizeImageGet($arPicture['ID'], array("width" => $iWidth, "height" => $iHeight), $iResizeMode, true);
                        $arPicture['SRC'] = $arSizedPicture['src'];
                        $arPicture['HEIGHT'] = $arSizedPicture['height'];
                        $arPicture['WIDTH'] = $arSizedPicture['width'];
                    } else {
                        $arPicture['SRC'] = CFile::GetPath($arPicture['ID']);
                    }
                } else {
                    $arPicture = array();
                }
            } else {
                $arPicture = array();
            }
        }
        CStartShopEvents::Call('OnToolsIBlockItemPictureGet', array('ITEM' => $arItem, 'WIDTH' => $iWidth, 'HEIGHT' => $iHeight, 'PREVIEW_MAIN' => $bPreviewMain, 'RESIZE_MODE' => $iResizeMode, 'PICTURE' => &$arPicture));
        return $arPicture;
    }

    public static function GetPricesValues($_0, $_7, $_8 = null, $_9 = LANGUAGE_ID)
    {
        $_10 = array();
        if (!is_array($_0) || !is_array($_7) || empty($_7) || empty($_0['PROPERTIES'])) return $_10;
        foreach ($_7 as $_11) {
            if (!empty($_0['PROPERTIES'][static::$PrefixPrice . '_' . $_11['ID']])) {
                $_12 = $_0['PROPERTIES'][static::$PrefixPrice . '_' . $_11['ID']]['VALUE'];
                $_13 = $_0['PROPERTIES'][static::$PrefixCurrency . '_' . $_11['ID']]['VALUE_XML_ID'];
                if (!is_numeric($_12)) continue;
                $_12 = floatval($_12);
                if (!empty($_8)) {
                    $_14 = CStartShopCurrency::ConvertAndFormatAsArray($_12, $_13, $_8, $_9);
                } else {
                    $_14 = CStartShopCurrency::FormatAsArray($_12, $_13, $_9);
                }
                $_14['TYPE'] = $_11['CODE'];
                $_10[$_14['TYPE']] = $_14;
            }
        }
        CStartShopEvents::Call('OnToolsIBlockPricesValuesGet', array('ITEM' => $_0, 'PRICES' => $_7, 'LANGUAGE_ID' => $_9, 'VALUES' => &$_10));
        return $_10;
    }

    public static function GetQuantityValue($_0)
    {
        $_15 = floatval($_0['PROPERTIES'][static::$PrefixQuantity]['VALUE']);
        CStartShopEvents::Call('OnToolsIBlockQuantityValueGet', array('ITEM' => $_0, 'VALUE' => &$_15));
        return $_15;
    }

    public static function GetQuantityRatioValue($_0)
    {
        $_15 = floatval($_0['PROPERTIES'][static::$PrefixQuantityRatio]['VALUE']);
        CStartShopEvents::Call('OnToolsIBlockQuantityRatioValueGet', array('ITEM' => $_0, 'VALUE' => &$_15));
        return $_15;
    }

    public static function UpdateProperties($_16, $_9 = LANGUAGE_ID)
    {
        if (!CModule::IncludeModule('iblock')) return false;
        $_16 = intval($_16);
        if (CStartShopCatalog::IsValid($_16)) {
            $_17 = round(0 + 0.2 + 0.2 + 0.2 + 0.2 + 0.2);
            $_18 = array();
            $_19 = CIBlockProperty::GetList(array(), array('IBLOCK_ID' => $_16));
            while ($_20 = $_19->Fetch()) $_18[$_20['CODE']] = $_20;
            $_21 = $_18[static::$PrefixQuantity];
            $_22 = array('NAME' => GetMessage('cstartshoptoolsiblock.properties.quantity'), "SORT" => $_17++, "ACTIVE" => CStartShopCatalog::IsUseQuantity($_16) ? 'Y' : 'N', 'PROPERTY_TYPE' => 'N', 'USER_TYPE' => '', 'IS_REQUIRED' => 'Y', 'MULTIPLE' => 'N', 'DEFAULT_VALUE' => round(0), 'SMART_FILTER' => 'N');
            if (!empty($_21)) {
                $_23 = new CIBlockProperty();
                $_23->Update($_21['ID'], $_22);
            } else {
                $_23 = new CIBlockProperty();
                $_22['IBLOCK_ID'] = $_16;
                $_22['CODE'] = static::$PrefixQuantity;
                $_23->Add($_22);
            }
            $_24 = $_18[static::$PrefixQuantityRatio];
            $_25 = array('NAME' => GetMessage('cstartshoptoolsiblock.properties.quantity_ratio'), "SORT" => $_17++, "ACTIVE" => 'Y', 'PROPERTY_TYPE' => 'N', 'USER_TYPE' => '', 'IS_REQUIRED' => 'Y', 'MULTIPLE' => 'N', 'DEFAULT_VALUE' => round(0 + 1), 'SMART_FILTER' => 'N');
            if (!empty($_24)) {
                $_23 = new CIBlockProperty();
                $_23->Update($_24['ID'], $_25);
            } else {
                $_23 = new CIBlockProperty();
                $_25['IBLOCK_ID'] = $_16;
                $_25['CODE'] = static::$PrefixQuantityRatio;
                $_23->Add($_25);
            }
            $_26 = array();
            $_27 = CStartShopPrice::GetList(array('SORT' => 'ASC'));
            while ($_28 = $_27->Fetch()) $_26[] = $_28;
            $_29 = array();
            $_30 = CStartShopCurrency::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'));
            while ($_31 = $_30->Fetch()) $_29[] = $_31;
            $_32 = array();
            $_33 = array();
            foreach ($_26 as $_28) {
                $_34 = static::$PrefixCurrency . '_' . $_28['ID'];
                $_35 = $_18[$_34];
                $_36 = array('NAME' => GetMessage('cstartshoptoolsiblock.properties.currency', array('#PRICE_TYPE#' => $_28['LANG'][$_9]['NAME'])), "SORT" => $_17++, "ACTIVE" => !empty($_29) ? 'Y' : 'N', 'PROPERTY_TYPE' => 'L', 'LIST_TYPE' => 'L', 'USER_TYPE' => '', 'IS_REQUIRED' => 'N', 'MULTIPLE' => 'N', 'DEFAULT_VALUE' => round(0), 'SMART_FILTER' => 'N');
                if (!empty($_35)) {
                    $_23 = new CIBlockProperty();
                    $_23->Update($_35['ID'], $_36);
                    $_37 = $_35['ID'];
                } else {
                    $_23 = new CIBlockProperty();
                    $_36['IBLOCK_ID'] = $_16;
                    $_36['CODE'] = $_34;
                    $_37 = $_23->Add($_36);
                }
                $_33[] = $_34;
                if (!empty($_37)) {
                    $_38 = array();
                    $_39 = array();
                    $_40 = CIBlockPropertyEnum::GetList(array(), array('PROPERTY_ID' => $_37));
                    while ($_41 = $_40->Fetch()) $_39[$_41['XML_ID']] = $_41;
                    foreach ($_29 as $_31) {
                        $_42 = $_39[$_31['CODE']];
                        $_43 = new CIBlockPropertyEnum();
                        $_44 = array('VALUE' => '[' . $_31['CODE'] . ']' . $_31['LANG'][$_9]['NAME'], 'DEF' => $_31['BASE'], 'SORT' => $_31['SORT']);
                        if (!empty($_42)) {
                            $_43->Update($_42['ID'], $_44);
                        } else {
                            $_44['PROPERTY_ID'] = $_37;
                            $_44['XML_ID'] = $_31['CODE'];
                            $_43->Add($_44);
                        }
                        $_38[] = $_31['CODE'];
                    }
                    foreach ($_39 as $_41) if (!in_array($_41['XML_ID'], $_38)) CIBlockPropertyEnum::Delete($_41['ID']);
                }
                $_45 = static::$PrefixPrice . '_' . $_28['ID'];
                $_46 = $_18[$_45];
                $_47 = array('NAME' => $_28['LANG'][LANGUAGE_ID]['NAME'], 'SORT' => $_17++, 'ACTIVE' => $_28['ACTIVE'], 'PROPERTY_TYPE' => 'N', 'USER_TYPE' => '', 'IS_REQUIRED' => 'N', 'MULTIPLE' => 'N', 'DEFAULT_VALUE' => round(0), 'SMART_FILTER' => 'Y', 'DISPLAY_TYPE' => 'A');
                if (!empty($_46)) {
                    $_23 = new CIBlockProperty();
                    $_23->Update($_46['ID'], $_47);
                } else {
                    $_23 = new CIBlockProperty();
                    $_47['IBLOCK_ID'] = $_16;
                    $_47['CODE'] = $_45;
                    $_23->Add($_47);
                }
                $_32[] = $_45;
            }
            foreach ($_18 as $_20) {
                if (preg_match('/^(' . preg_quote(static::$PrefixPrice) . ')/', $_20['CODE'])) if (!in_array($_20['CODE'], $_32)) CIBlockProperty::Delete($_20['ID']);
                if (preg_match('/^(' . preg_quote(static::$PrefixCurrency) . ')/', $_20['CODE'])) if (!in_array($_20['CODE'], $_33)) CIBlockProperty::Delete($_20['ID']);
            }
            CStartShopEvents::Call('OnToolsIBlockUpdateProperties', array('IBLOCK' => $_16, 'LANGUAGE_ID' => $_9));
        }
    }

    public static function UpdatePropertiesAll()
    {
        $_48 = CStartShopCatalog::GetList();
        while ($_49 = $_48->Fetch()) {
            static::UpdateProperties($_49['IBLOCK']);
            static::UpdateProperties($_49['OFFERS_IBLOCK']);
        }
        CStartShopEvents::Call('OnToolsIBlockUpdatePropertiesAll');
    }

    public static function GetOffersMinPrice($_0)
    {
        $_50 = array();
        if (!empty($_0['STARTSHOP']['OFFERS'])) foreach ($_0['STARTSHOP']['OFFERS'] as $_51) if (empty($_50) || $_51['STARTSHOP']['PRICES']['MINIMAL']['VALUE'] < $_50['VALUE']) $_50 = $_51['STARTSHOP']['PRICES']['MINIMAL'];
        return $_50;
    }

    public static function GetOffersProperties($_52)
    {
        if (!is_array($_52) || empty($_52)) return CStartShopUtil::ArrayToDBResult(array());
        $_53 = array();
        foreach ($_52 as $_49) if (!empty($_49['OFFERS_IBLOCK']) && !empty($_49['OFFERS_LINK_PROPERTY']) && is_array($_49['OFFERS_PROPERTIES'])) foreach ($_49['OFFERS_PROPERTIES'] as $_54) {
            $_55 = CIBlockProperty::GetByID($_54)->Fetch();
            if (!empty($_55)) {
                $_56 = array();
                if ($_55['PROPERTY_TYPE'] == 'L' && $_55['USER_TYPE'] == null && $_55['MULTIPLE'] == 'N') {
                    $_56 = array('ID' => $_55['ID'], 'CODE' => $_55['CODE'], 'NAME' => $_55['NAME'], 'TYPE' => 'TEXT', 'VALUES' => array());
                    $_57 = CStartShopUtil::DBResultToArray(CIBlockPropertyEnum::GetList(array(), array("PROPERTY_ID" => $_55['ID'])));
                    foreach ($_57 as $_58) if (!empty($_58['XML_ID'])) $_56['VALUES'][$_58['XML_ID']] = array('ID' => $_58['ID'], 'CODE' => $_58['XML_ID'], 'TEXT' => $_58['VALUE']);
                } else if ($_55['PROPERTY_TYPE'] == 'S' && $_55['USER_TYPE'] == 'directory' && $_55['MULTIPLE'] == 'N' && CModule::IncludeModule("highloadblock")) {
                    $_59 = HLBlock\HighloadBlockTable::getList(array('filter' => array('TABLE_NAME' => $_55['USER_TYPE_SETTINGS']['TABLE_NAME'])))->Fetch();
                    if (!empty($_59)) {
                        $_60 = HLBlock\HighloadBlockTable::compileEntity($_59);
                        $_61 = $_60->getDataClass();
                        $_62 = $_60->getFields();
                        $_63 = array();
                        foreach ($_62 as $_64) $_63[] = $_64->getName();
                        $_65 = new Entity\Query($_61);
                        $_65->setSelect(array('*'));
                        $_66 = $_65->exec();
                        $_57 = array();
                        while ($_58 = $_66->Fetch()) $_57[] = $_58;
                        if (in_array('UF_XML_ID', $_63) && in_array('UF_NAME', $_63)) if (in_array('UF_FILE', $_63)) {
                            $_56 = array('ID' => $_55['ID'], 'CODE' => $_55['CODE'], 'NAME' => $_55['NAME'], 'TYPE' => 'PICTURE', 'VALUES' => array());
                            foreach ($_57 as $_58) {
                                $_67 = CFile::GetPath($_58["UF_FILE"]);
                                $_56['VALUES'][$_58['UF_XML_ID']] = array('ID' => $_58['ID'], 'CODE' => $_58['UF_XML_ID'], 'TEXT' => $_58['UF_NAME'], 'PICTURE' => (!empty($_67) ? $_67 : null));
                            }
                        } else {
                            $_56 = array('ID' => $_55['ID'], 'CODE' => $_55['CODE'], 'NAME' => $_55['NAME'], 'TYPE' => 'TEXT', 'VALUES' => array());
                            foreach ($_57 as $_58) {
                                $_56['VALUES'][$_58['UF_XML_ID']] = array('ID' => $_58['ID'], 'CODE' => $_58['UF_XML_ID'], 'TEXT' => $_58['UF_NAME']);
                            }
                        }
                    }
                }
                if (!empty($_56)) $_53[] = $_56;
            }
        }
        return CStartShopUtil::ArrayToDBResult($_53);
    }

    public static function ConvertToOfferProperty($_55, $_53, $_68 = 'ID')
    {
        $_69 = array();
        if (!empty($_68)) {
            $_70 = $_53[$_55[$_68]];
            if (!empty($_70)) if ($_55['PROPERTY_TYPE'] == 'L' && $_55['USER_TYPE'] == null) {
                $_69 = $_53[$_55[$_68]];
                $_69['VALUE'] = $_69['VALUES'][$_55['VALUE_XML_ID']];
                unset($_69['VALUES']);
            } else if ($_55['PROPERTY_TYPE'] == 'S' && $_55['USER_TYPE'] == 'directory') {
                $_69 = $_53[$_55[$_68]];
                $_69['VALUE'] = $_69['VALUES'][$_55['VALUE']];
                unset($_69['VALUES']);
            }
        }
        if (empty($_69['VALUE'])) $_69 = array();
        return $_69;
    }

    public static function GetOffersJSStructure($_0)
    {
        $_71 = array();
        $_71['PROPERTIES'] = array();
        $_71['OFFERS'] = array();
        foreach ($_0['STARTSHOP']['OFFER']['PROPERTIES'] as $_55) {
            $_71['PROPERTIES'][] = $_55;
        }
        foreach ($_0['STARTSHOP']['OFFERS'] as $_51) {
            $_72 = array_merge(array('ID' => $_51['ID']), $_51['STARTSHOP']);
            $_72['DATA'] = $_51;
            unset($_72['DATA']['STARTSHOP']);
            $_71['OFFERS'][$_51['ID']] = $_72;
            unset($_72);
        }
        return $_71;
    }

    public static function CheckOffersLink($_16, $_73 = false)
    {
        $_49 = CStartShopCatalog::GetByIBlock(intval($_16))->Fetch();
        if (empty($_49)) $_49 = CStartShopCatalog::GetByOffersIBlock(intval($_16))->Fetch();
        if (!empty($_49)) {
            $_74 = false;
            if (!empty($_49['OFFERS_IBLOCK'])) if (CIBlock::GetByID($_49["OFFERS_IBLOCK"])->Fetch()) if (!empty($_49['OFFERS_LINK_PROPERTY'])) {
                $_75 = CIBlockProperty::GetByID($_49["OFFERS_LINK_PROPERTY"])->Fetch();
                if (!empty($_75)) {
                    if ($_75['PROPERTY_TYPE'] == 'E' && empty($_75['USER_TYPE']) && $_75['MULTIPLE'] == 'N' && $_75['ACTIVE'] == 'Y') {
                        return $_75;
                    } else {
                        $_23 = new CIBlockElement();
                        $_23->Update($_75['ID'], array("PROPERTY_TYPE" => "E", "USER_TYPE" => false, 'ACTIVE' => 'Y'));
                        if (empty($_23->_76)) {
                            unset($_23);
                            return $_75;
                        }
                        $_74 = true;
                        unset($_23);
                    }
                } else {
                    $_74 = true;
                }
            } else {
                $_74 = true;
            }
            if ($_74 && $_73) {
                $_23 = new CIBlockProperty();
                $_77 = $_23->Add(array("IBLOCK_ID" => $_49["OFFERS_IBLOCK"], "NAME" => GetMessage("cstartshoptoolsiblock.properties.offers_link_property"), "CODE" => static::$PrefixOfferLink, "PROPERTY_TYPE" => "E", "USER_TYPE" => false, 'MULTIPLE' => 'N', 'ACTIVE' => 'Y'));
                if (!empty($_77) && empty($_23->_76)) {
                    CStartShopCatalog::Update($_49['IBLOCK'], array('OFFERS_LINK_PROPERTY' => $_77));
                    unset($_23);
                    return CIBlockElement::GetByID($_77)->Fetch();
                }
                unset($_23);
            }
        }
        return null;
    }

    public static function CheckUserFieldExternalId()
    {
        $_78 = new CUserTypeEntity();
        $_79 = $_78->GetList(array(), array("ENTITY_ID" => "USER", "USER_TYPE_ID" => "string", "FIELD_NAME" => "UF_" . static::$PrefixUserExternalId))->Fetch();
        if (!empty($_79)) {
            if ($_79['USER_TYPE_ID'] == 'string' && $_79['MULTIPLE'] == 'N') {
                return $_79;
            } else {
                $_78->Delete($_79['ID']);
            }
        }
        $_80 = $_78->Add(array("ENTITY_ID" => "USER", "FIELD_NAME" => "UF_" . static::$PrefixUserExternalId, "USER_TYPE_ID" => "string", "MULTIPLE" => "N", "MANDATORY" => "N", "SHOW_FILTER" => "N", "IS_SEARCHABLE" => "N", "SHOW_IN_LIST" => "", "EDIT_IN_LIST" => "", "XML_ID" => static::$PrefixUserExternalId, "SETTINGS" => array('DEFAULT_VALUE' => '', 'SIZE' => '20', 'ROWS' => '1', 'MIN_LENGTH' => '0', 'MAX_LENGTH' => '0', 'REGEXP' => '',)));
        if (!empty($_80)) return $_78->GetByID($_80);
        return false;
    }

    public static function SetOffersLinkByOffersIBlock($_16, $_81, $_82)
    {
        $_49 = CStartShopCatalog::GetByOffersIBlock(intval($_16))->Fetch();
        if (!empty($_49) && is_numeric($_81) && !empty($_81)) {
            if (!empty($_49['OFFERS_IBLOCK']) && !empty($_49['OFFERS_LINK_PROPERTY'])) CIBlockElement::SetPropertyValuesEx($_81, $_49['OFFERS_IBLOCK'], array($_49['OFFERS_LINK_PROPERTY'] => intval($_82)));
        }
    }

    public static function SetOffersLinkByIBlock($_16, $_81, $_82)
    {
        $_49 = CStartShopCatalog::GetByIBlock(intval($_16))->Fetch();
        if (!empty($_49) && is_numeric($_81) && !empty($_81)) {
            if (!empty($_49['OFFERS_IBLOCK']) && !empty($_49['OFFERS_LINK_PROPERTY'])) CIBlockElement::SetPropertyValuesEx($_81, $_49['OFFERS_IBLOCK'], array($_49['OFFERS_LINK_PROPERTY'] => intval($_82)));
        }
    }

    public static function SetQuantity($_81, $_83)
    {
        $_0 = CIBlockElement::GetByID($_81)->Fetch();
        $_83 = floatval($_83);
        if (!empty($_0)) CIBlockElement::SetPropertyValuesEx($_0['ID'], $_0['IBLOCK_ID'], array(static::$PrefixQuantity => $_83));
    }

    public static function SetQuantityRatio($_81, $_84)
    {
        $_0 = CIBlockElement::GetByID($_81)->Fetch();
        $_84 = floatval($_84);
        if (!empty($_0)) CIBlockElement::SetPropertyValuesEx($_0['ID'], $_0['IBLOCK_ID'], array(static::$PrefixQuantityRatio => $_84));
    }

    public static function SetPriceCurrency($_81, $_85, $_86)
    {
        static::SetPrice($_81, array($_85), false, $_86);
    }

    public static function SetPriceValue($_81, $_85, $_12)
    {
        static::SetPrice($_81, array($_85), $_12, false);
    }

    public static function SetPrice($_81, $_85, $_12, $_86)
    {
        static::SetPrices($_81, array($_85), $_12, $_86);
    }

    public static function SetPrices($_81, $_26 = null, $_12 = false, $_86 = false)
    {
        $_0 = CIBlockElement::GetByID($_81)->Fetch();
        $_31 = array();
        if (!empty($_86) && $_86 !== null) {
            $_31 = CStartShopCurrency::GetByCode($_86)->Fetch();
        } else if ($_86 !== false && $_86 !== null) {
            $_31 = CStartShopCurrency::GetBase()->Fetch();
        }
        if (is_array($_26) && !empty($_26)) {
            $_26 = CStartShopUtil::DBResultToArray(CStartShopPrice::GetList(array(), array('CODE' => $_26)));
        } else {
            $_26 = CStartShopUtil::DBResultToArray(CStartShopPrice::GetList());
        }
        $_87 = CStartShopUtil::DBResultToArray(CIBlockProperty::GetList(array(), array('IBLOCK_ID' => $_0['IBLOCK_ID'])), 'CODE');
        $_88 = array();
        if (!empty($_0) && !empty($_26)) foreach ($_26 as $_28) {
            $_45 = static::$PrefixPrice . '_' . $_28['ID'];
            $_34 = static::$PrefixCurrency . '_' . $_28['ID'];
            if (is_numeric($_12)) {
                $_88[$_45] = floatval($_12);
            } else if ($_12 === null) {
                $_88[$_45] = '';
            }
            if (!empty($_31)) {
                $_55 = $_87[$_34];
                if (!empty($_55)) {
                    $_89 = CIBlockPropertyEnum::GetList(array(), array('PROPERTY_ID' => $_55['ID'], 'XML_ID' => $_31['CODE']))->Fetch();
                    if (!empty($_89)) $_88[$_34] = $_89['ID'];
                }
            } else if ($_86 === null) {
                $_88[$_34] = '';
            }
        }
        if (!empty($_88)) CIBlockElement::SetPropertyValuesEx($_0['ID'], $_0['IBLOCK_ID'], $_88);
    }
} ?>
