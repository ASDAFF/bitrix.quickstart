<?php
namespace Redsign\DevFunc\Iblock;

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Property
{
    static function OnIBlockPropertyBuildListStores(){
        return array(
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "redsign_stores",
            "DESCRIPTION" => Loc::getMessage("REDSIGN_DEVFUNC_IBLOCK_PROPERTY_LINK_PROP_STORES_TITLE"),
            "GetPropertyFieldHtml" => array("\Redsign\DevFunc\Iblock\Property", "GetPropertyFieldHtmlStores"),
            "GetPropertyFieldHtmlMulty" => array("\Redsign\DevFunc\Iblock\Property", "GetPropertyFieldHtmlStoresMulty"),
        );
    }

    static function GetPropertyFieldHtmlStores($arProperty, $value, $strHTMLControlName) {

		static $cache = array();
        $html = '';

		if (Loader::includeModule('catalog')) {
            $cache["STORES"] = array();
            $rsStore = \CCatalogStore::GetList( array("SORT" => "ASC"), array() );

            while ($arStore = $rsStore->GetNext()) {
                $cache["STORES"][] = $arStore;
            }

            $varName = str_replace("VALUE", "DESCRIPTION", $strHTMLControlName["VALUE"]);
            $val = ($value["VALUE"] ? $value["VALUE"] : $arProperty["DEFAULT_VALUE"]);
            if ($arProperty['MULTIPLE'] == 'Y') {
                $html .= '<select name="'.$strHTMLControlName["VALUE"].'[]" multiple size="6" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';
            } else {
                $html .= '<select name="'.$strHTMLControlName["VALUE"].'" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';
			}

            $html .= '<option value="component" '.($val == "component" ? 'selected' : '').'>'.Loc::getMessage("REDSIGN_DEVFUNC_IBLOCK_PROPERTY_LINK_PROP_FROM_COMPONENTS").'</option>';
            foreach ($cache["STORES"] as $arStore) {
                $html .= '<option value="'.$arStore["ID"].'"';
                if($val == $arStore["~ID"])
                    $html .= ' selected';
                $html .= '>'.$arStore["TITLE"].'</option>';
            }
            $html .= '</select>';
        }
        return $html;
    }

    static function GetPropertyFieldHtmlStoresMulty($arProperty, $value, $strHTMLControlName) {

        static $cache = array();
        $html = '';

        if (Loader::includeModule('catalog')) {
            $cache["STORES"] = array();
            $rsStore = \CCatalogStore::GetList( array("SORT" => "ASC"), array() );

            while ($arStore = $rsStore->GetNext()) {
                $cache["STORES"][] = $arStore;
            }

            $varName = str_replace("VALUE", "DESCRIPTION", $strHTMLControlName["VALUE"]);
            $arValues = array();

            if ($value && is_array($value)) {
                foreach ($value as $arValue) {
                    $arValues[] = $arValue["VALUE"];
                }
            } else {
                $arValues[] = $arProperty["DEFAULT_VALUE"];
			}

            if ($arProperty['MULTIPLE'] == 'Y') {
                $html .= '<select name="'.$strHTMLControlName["VALUE"].'[]" multiple size="6" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';
            } else {
                $html .= '<select name="'.$strHTMLControlName["VALUE"].'" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';
			}

            $html .= '<option value="component" '.(in_array("component", $arValues) ? 'selected' : '').'>'.Loc::getMessage("REDSIGN_DEVFUNC_IBLOCK_PROPERTY_LINK_PROP_FROM_COMPONENTS").'</option>';
            foreach ($cache["STORES"] as $arStore) {
                $html .= '<option value="'.$arStore["ID"].'"';
                if (in_array($arStore["~ID"], $arValues)) {
                    $html .= ' selected';
				}
                $html .= '>'.$arStore["TITLE"].'</option>';
            }
            $html .= '</select>';
        }

        return $html;
    }

    static function OnIBlockPropertyBuildListLocations() {
        return array(
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "redsign_locations",
            "DESCRIPTION" => Loc::getMessage("REDSIGN_DEVFUNC_IBLOCK_PROPERTY_LINK_PROP_LOCATIONS_TITLE"),
            "GetPropertyFieldHtml" => array("\Redsign\DevFunc\Iblock\Property", "GetPropertyFieldHtmlLocations"),
        );
    }

    static function GetPropertyFieldHtmlLocations($arProperty, $value, $strHTMLControlName) {

        static $cache = array();
        $html = '';

        if (Loader::includeModule('sale')) {

            $cache["LOCATIONS"] = array();
            $rsLoc = \CSaleLocation::GetList(array("CITY_NAME" => "ASC"), array());

            while ($arLoc = $rsLoc->GetNext()) {
                if ($arLoc["CITY_NAME"]) {
                    $cache["LOCATIONS"][$arLoc["ID"]] = $arLoc;
				}
            }

            $varName = str_replace("VALUE", "DESCRIPTION", $strHTMLControlName["VALUE"]);
            $val = ($value["VALUE"] ? $value["VALUE"] : $arProperty["DEFAULT_VALUE"]);
            $html = '<select name="'.$strHTMLControlName["VALUE"].'" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">
            <option value="" >-</option>';
            foreach ($cache["LOCATIONS"] as $arLocation) {
                $html .= '<option value="'.$arLocation["ID"].'"';
                if ($val == $arLocation["~ID"]) {
                    $html .= ' selected';
				}
                $html .= '>'.$arLocation["CITY_NAME"].'</option>';
            }
            $html .= '</select>';
        }
        return $html;
    }

    static function OnIBlockPropertyBuildListPrices() {
        return array(
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "redsign_prices",
            "DESCRIPTION" => Loc::getMessage("REDSIGN_DEVFUNC_IBLOCK_PROPERTY_LINK_PROP_PRICES_TITLE"),
            "GetPropertyFieldHtml" => array("\Redsign\DevFunc\Iblock\Property", "GetPropertyFieldHtmlPrices"),
            "GetPropertyFieldHtmlMulty" => array("\Redsign\DevFunc\Iblock\Property", "GetPropertyFieldHtmlPricesMulty"),
        );
    }


    static function GetPropertyFieldHtmlPrices($arProperty, $value, $strHTMLControlName) {
        static $cache = array();
        $html = '';

        if (Loader::includeModule('catalog')) {
            $cache["PRICE"] = array();
            $rsPrice = \CCatalogGroup::GetList( array("SORT" => "ASC"), array() );
            while ($arPrice = $rsPrice->GetNext()) {
                $cache["PRICE"][] = $arPrice;
            }

            $varName = str_replace("VALUE", "DESCRIPTION", $strHTMLControlName["VALUE"]);
            $val = ($value["VALUE"] ? $value["VALUE"] : $arProperty["DEFAULT_VALUE"]);
            $html = '<select name="'.$strHTMLControlName["VALUE"].'" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">
            <option value="component" '.($val == "component" ? 'selected' : '').'>'.Loc::getMessage("REDSIGN_DEVFUNC_IBLOCK_PROPERTY_LINK_PROP_FROM_COMPONENTS").'</option>';
            foreach ($cache["PRICE"] as $arPrice) {
                $html .= '<option value="'.$arPrice["ID"].'"';
                if ($val == $arPrice["~ID"]) {
                    $html .= ' selected';
				}
                $html .= '>'.$arPrice["NAME"].'</option>';
            }
            $html .= '</select>';
        }
        return $html;
    }

    static function GetPropertyFieldHtmlPricesMulty($arProperty, $value, $strHTMLControlName) {
        static $cache = array();
        $html = '';
        if (Loader::includeModule('catalog')) {
            $cache["PRICE"] = array();
            $rsPrice = \CCatalogGroup::GetList( array("SORT" => "ASC"), array() );
            while ($arPrice = $rsPrice->GetNext()) {
                $cache["PRICE"][] = $arPrice;
            }

            $varName = str_replace("VALUE", "DESCRIPTION", $strHTMLControlName["VALUE"]);
            $arValues = array();
            if ($value && is_array($value)) {
                foreach ($value as $arValue) {
                    $arValues[] = $arValue["VALUE"];
                }
            } else {
                $arValues[] = $arProperty["DEFAULT_VALUE"];
			}

            if ($arProperty['MULTIPLE'] == 'Y') {
                $html .= '<select name="'.$strHTMLControlName["VALUE"].'[]" multiple size="6" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';
            } else {
                $html .= '<select name="'.$strHTMLControlName["VALUE"].'" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';
			}

            $html .= '<option value="component" '.(in_array("component", $arValues) ? 'selected' : '').'>'.Loc::getMessage("REDSIGN_DEVFUNC_IBLOCK_PROPERTY_LINK_PROP_FROM_COMPONENTS").'</option>';
            foreach ($cache["PRICE"] as $arPrice) {
                $html .= '<option value="'.$arPrice["ID"].'"';
                if (in_array($arPrice["~ID"], $arValues)) {
                    $html .= ' selected';
				}
                $html .= '>'.$arPrice["NAME"].'</option>';
            }
            $html .= '</select>';
        }
        return $html;
    }
}