<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Seo\Engine;
use Bitrix\Main\Text\Converter;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\IO\Path;

\Bitrix\Main\Loader::includeModule('seo');
\Bitrix\Main\Loader::includeModule('socialservices');

class SotaXmlParser extends SotaContentParser
{

    /*public $id = false;
    public $rss;
    public $typeN;
    public $active;
    public $iblock_id;
    public $section_id;
    public $detail_dom;
    public $encoding;
    public $preview_delete_tag="";
    public $bool_preview_delete_tag="";
    public $detail_delete_tag="";
    public $bool_detail_delete_tag="";
    public $preview_first_img="";
    public $detail_first_img="";
    public $preview_save_img="";
    public $detail_save_img="";
    public $text = "";
    public $site = "";
    public $link = "";
    public $preview_delete_element="";
    public $detail_delete_element="";
    public $preview_delete_attribute="";
    public $detail_delete_attribute="";
    public $index_element="";
    public $resize_image="";
    public $meta_description="";
    public $meta_keywords="";
    public $meta_description_text="";
    public $meta_keywords_text="";
    public $agent = false;
    public $active_element = "Y";
    public $header_url;
    public $settings;
    public $countPage = 0;
    public $countItem = 0;
    public $stepStart = false;
    public $countSection = 0;

    public $page;*/
    const TEST = 0;
    const DEFAULT_DEBUG_ITEM = 30;

    public function __construct()
    {
        parent::__construct();
    }

    protected function parseXmlCatalog()
    {
        set_time_limit(0);
        parent::ClearAjaxFiles();
        parent::DeleteLog();
        parent::checkActionBegin();
        $this->arUrl = array();
        if (isset($this->settings["catalog"]["url_dop"]) && !empty($this->settings["catalog"]["url_dop"])) $this->arUrl = explode("\r\n", $this->settings["catalog"]["url_dop"]);

        $this->arUrl = array_merge(array($this->rss), $this->arUrl);
        $this->arUrlSave = $this->arUrl;

        if (!parent::PageFromFile()) return false;
        parent::CalculateStep();
        if ($this->settings["catalog"]["mode"] != "debug" && !$this->agent) $this->arUrlSave = array($this->rss);
        else $this->arUrlSave = $this->arUrl;
        //if(!$this->connectCatalogPage($this->rss));
        //return;
        foreach ($this->arUrlSave as $rss):
            $rss = trim($rss);
            if (empty($rss)) continue;
            $this->rss = $rss;
            parent::convetCyrillic($this->rss);
            parent::connectCatalogPage($this->rss);

            if (!$this->agent && $this->settings["catalog"]["mode"] != "debug" && isset($this->errors) && count($this->errors) > 0) {
                parent::SaveLog();
                unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog_step" . $this->id . ".txt");
                unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_copy_page" . $this->id . ".txt");
                return false;
            }
            if ($this->parseCatalogSectionXml($this->rss) === false) {
                if ($this->settings["catalog"]["add_parser_section"] == "Y") {
                    parent::SaveLog();
                    return false;
                }
            }
            $n = $this->currentPage;
            parent::parseCatalogProducts();
            if ($this->settings["catalog"]["mode"] != "debug" && !$this->agent) {
                $this->stepStart = true;
                parent::SavePrevPage($this->rss);
            }

            parent::SaveCurrentPage($this->pagenavigation);
            if ($this->stepStart) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog_step" . $this->id . ".txt"))
                    unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog_step" . $this->id . ".txt");
                parent::DeleteCopyPage();
            }
            if ((!parent::CheckOnePageNavigation() && $this->agent) || (!parent::CheckOnePageNavigation() && !$this->agent && $this->settings["catalog"]["mode"] == "debug")) parent::parseCatalogPages();
            if (parent::CheckOnePageNavigation() && $this->stepStart) {
                if (parent::IsEndSectionUrl()) parent::ClearBufferStop();
                else parent::ClearBufferStep();
                return false;
            }
        endforeach;

        parent::checkActionAgent();
    }

    protected function parseCatalogProductElementXml(&$el)
    {
        $this->countItem++;
        //$this->parseCatalogSection();
        if (!$this->parserCatalogPreviewXml($el)) {
            parent::SaveCatalogError();
            parent::clearFields();
            return false;
        }

        /*$this->parserCatalogDetail();
        $this->parseCatalogSection();
        $this->parseCatalogMeta();
        $this->parseCatalogFirstUrl();*/
        if ($this->settings["catalog"]["add_parser_section"] != "Y") parent::parseCatalogSection();
        parent::parseCatalogDate();
        parent::parseCatalogAllFields();


        $db_events = GetModuleEvents("sota.parser", "parserBeforeAddElementXml", true);
        $error = false;
        foreach ($db_events as $arEvent) {
            $bEventRes = ExecuteModuleEventEx($arEvent, array(&$this, &$el));
            if ($bEventRes === false) {
                $error = true;
                break 1;
            }
        }

        if (!$error && !$error_isad) {
            parent::AddElementCatalog();
            foreach (GetModuleEvents("sota.parser", "parserAfterAddElementXml", true) as $arEvent)
                ExecuteModuleEventEx($arEvent, array(&$this, &$el));
        }

        if ($this->isCatalog && $this->elementID) {
            if ($this->isOfferCatalog && !$this->boolOffer) {
                parent::AddElementOfferCatalog();
                $this->elementID = $this->elementOfferID;
                $this->elementUpdate = $this->elementOfferUpdate;
            }
            if ($this->boolOffer) {
                parent::addProductPriceOffers();
            } else {

                parent::AddProductCatalog();
                parent::AddMeasureCatalog();
                parent::AddPriceCatalog();
                $this->addAvailable();
            }

        }/*else{
            $this->AddElementOfferCatalog();
            $this->AddProductCatalog();
            $this->AddMeasureCatalog();
            $this->AddPriceCatalog();    
        }*/

        parent::SetCatalogElementsResult();
        parent::clearFilesTemp();
        parent::clearFields();

    }

    protected function parserCatalogPreviewXml(&$el)
    {
        if (!$this->parseCatalogIdElement($el)) return false;
        parent::parseCatalogNamePreview($el);
        //$this->parseCatalogPropertiesPreview($el);
        if ($this->isCatalog) parent::parseCatalogPricePreview($el);
        if ($this->isCatalog) $this->ParseCatalogAvailablePreview($el);
        if ($this->settings["catalog"]["add_parser_section"] == "Y") $this->parseCatalogParrentSection($el);
        parent::parseCatalogPreviewPicturePreview($el);
        parent::parseCatalogDetailPicture($el);
        $this->parseCatalogDescriptionXml($el);
        parent::parseCatalogDetailMorePhoto($el);
        $this->parseCatalogPropertiesXml($el);
        $this->parserOffersXml($el);

        return true;
    }

    protected function parserOffersXml($el)
    {
        $this->boolOffer = false;
        if ($this->settings["offer"]["load"] == "table" && $this->isOfferParsing && isset($this->settings["offer"]["selector_item"]) && $this->settings["offer"]["selector_item"]) {
            $this->parserOffersXmlTable($el);
        } elseif ($this->settings["offer"]["load"] == "one" && $this->isOfferParsing && isset($this->settings["offer"]["one"]["selector"]) && $this->settings["offer"]["one"]["selector"]) {
            $this->parserOffersOne($el);
        }

    }

    protected function parserOffersXmlTable(&$el)
    {
        $offerItem = $this->settings["offer"]["selector_item"];

        foreach (pq($el)->find($offerItem) as $offer) {
            $this->boolOffer = true;
            if (parent::parseOfferName($offer)) {
                parent::parseOfferPrice($offer);
                $this->parseOfferQuantity($offer);
                $this->parseOfferPropsXml($offer);
                if (!parent::parseOfferGetUniq()) {
                    parent::deleteOfferFields();;
                    continue 1;
                }

            } else
                continue 1;

            $this->arOfferAll["FIELDS"][] = $this->arOffer;
            $this->arOfferAll["PRICE"][] = $this->arPriceOffer;
            $this->arOfferAll["QUANTITY"][] = $this->arOfferQuantity;

            parent::deleteOfferFields();

        }
    }

    protected function parserOffersOne(&$el)
    {
        $offerItem = trim($this->settings["offer"]["one"]["selector"]);
        foreach (pq($el)->find($offerItem) as $offer) {
            if (empty($this->settings["offer"]["one"]["separator"])) {
                $this->boolOffer = true;
                if ($this->parseOfferNameOneXml($offer)) {
                    parent::parseOfferPrice($offer);
                    $this->parseOfferQuantity($offer);
                    parent::parseOfferProps($offer);
                    if (!parent::parseOfferGetUniq()) {
                        parent::deleteOfferFields();;
                        continue 1;
                    }

                } else
                    continue 1;

                $this->arOfferAll["FIELDS"][] = $this->arOffer;
                $this->arOfferAll["PRICE"][] = $this->arPriceOffer;
                $this->arOfferAll["QUANTITY"][] = $this->arOfferQuantity;

                parent::deleteOfferFields();
            } elseif (!empty($this->settings["offer"]["one"]["separator"])) {
                if (is_array($arrOffers = $this->GetArrayNameOffes($offer))) {
                    foreach ($arrOffers as $nameOffer) {
                        if (empty($nameOffer)) continue 1;
                        $this->boolOffer = true;
                        if ($this->parseOfferNameOneXml($offer, $nameOffer)) {
                            parent::parseOfferPrice($offer);
                            $this->parseOfferQuantity($offer);
                            parent::parseOfferProps($offer, $nameOffer);
                            if (!parent::parseOfferGetUniq()) {
                                parent::deleteOfferFields();;
                                continue 1;
                            }
                        } else
                            continue 1;
                        $this->arOfferAll["FIELDS"][] = $this->arOffer;
                        $this->arOfferAll["PRICE"][] = $this->arPriceOffer;
                        $this->arOfferAll["QUANTITY"][] = $this->arOfferQuantity;

                        parent::deleteOfferFields();
                    }
                }
            }
        }
    }

    protected function parseOfferNameOneXml($offer, $nameOffer = false)
    {
        if (!empty($this->settings["offer"]["one"]["selector"]) && !empty($this->settings["offer"]["add_name"])) {
            if ($nameOffer === false) {
                $arr = parent::GetArraySrcAttr($this->settings["offer"]["selector_name"]);
                if (empty($arr["path"]) && !empty($arr["attr"])) {
                    $name = trim(pq($offer)->attr($arr["attr"]));
                } else {
                    if (empty($arr["attr"])) {
                        $name = trim(strip_tags(pq($offer)->find($arr["path"])->html()));
                    } elseif (!empty($arr["attr"])) {
                        $name = trim(pq($offer)->find($arr["path"])->attr($arr["attr"]));
                    }
                }
            } elseif ($nameOffer !== false) {
                $name = $nameOffer;
            }
            $deleteSymb = parent::getOfferDeleteSelector();
            $name = str_replace($deleteSymb, "", $name);
            $this->arOffer["NAME"] = htmlspecialchars_decode($name);
            if (isset($this->settings["loc"]["f_name"]) && $this->settings["loc"]["f_name"] == "Y") {
                $this->arOffer["NAME"] = parent::locText($this->arOffer["NAME"]);
            }
        }
        if (!isset($this->arOffer["NAME"]) && (!isset($this->settings["offer"]["add_name"]) || empty($this->settings["offer"]["add_name"]))) {
            $this->errors[] = GetMessage("parser_error_name_notfound_offer");
            return false;
        } elseif (!isset($this->arOffer["NAME"]))
            $this->arOffer["NAME"] = $this->arFields["NAME"];
        return true;

    }

    protected function GetArrayNameOffes($offer)
    {
        $arr = parent::GetArraySrcAttr($this->settings["offer"]["selector_name"]);
        if (empty($arr["path"]) && !empty($arr["attr"])) {
            $name = trim(pq($offer)->attr($arr["attr"]));
        } else {
            if (empty($arr["attr"])) {
                $name = trim(strip_tags(pq($offer)->find($arr["path"])->html()));
            } elseif (!empty($arr["attr"])) {
                $name = trim(pq($offer)->find($arr["path"])->attr($arr["attr"]));
            }
        }

        $arrName = explode($this->settings["offer"]["one"]["separator"], $name);
        for ($i = 0; $i < count($arrName); $i++) {
            $arrName[$i] = trim($arrName[$i]);
        }
        return $arrName;
    }

    protected function parseOfferPropsXml($offer)
    {
        if (parent::checkUniq() && !$this->isUpdate) return false;
        if (isset($this->settings["offer"]["selector_prop"]) && !empty($this->settings["offer"]["selector_item"])) {
            $deleteSymb = parent::getOfferDeleteSelector();
            $deleteSymbRegular = parent::getOfferDeleteSelectorRegular();

            $arProperties = $this->arSelectorPropertiesOffer;

            foreach ($arProperties as $code => $val) {
                $arProp = $this->arPropertiesOffer[$code];
                if ($arProp["PROPERTY_TYPE"] == "F") {
                    parent::parseCatalogPropFile($code, $offer);
                } else {

                    $arr = parent::GetArraySrcAttr($this->settings["offer"]["selector_prop"][$code]);
                    if (empty($arr["path"]) && !empty($arr["attr"])) {
                        $text = trim(pq($offer)->attr($arr["attr"]));
                    } else {
                        if (empty($arr["attr"])) {
                            $text = trim(strip_tags(pq($offer)->find($arr["path"])->html()));
                        } elseif (!empty($arr["attr"])) {
                            $text = trim(pq($offer)->find($arr["path"])->attr($arr["attr"]));
                        }
                    }
                    if ($arProp["USER_TYPE"] != "HTML")
                        $text = strip_tags($text);
                    $text = str_replace($deleteSymb, "", $text);
                    $text = preg_replace($deleteSymbRegular, "", $text);
                    parent::parseCatalogPropOffer($code, $val, $text);
                }

            }

        }
    }

    protected function parseCatalogPropertiesXml(&$el)
    {
        if (parent::checkUniq() && !$this->isUpdate) return false;
        parent::parseCatalogDefaultProperties($el);
        parent::parseCatalogSelectorProperties($el);
        $this->parseCatalogAutoProps($el);
        $this->parseCatalogAutoPropsAdd();
        parent::AllDoProps();
        if ($this->isCatalog) parent::parseCatalogFindProduct($el);
        if ($this->isCatalog) parent::parseCatalogSelectorProduct($el);
    }

    protected function parseCatalogAutoProps(&$el)
    {
        if (parent::checkUniq() && (!$this->isUpdate || !$this->isUpdate["props"])) return false;
        if (($this->settings["catalog"]["add_auto_props"] != "Y") || empty($this->settings["catalog"]["selector_find_props"]) || empty($this->settings["catalog"]["attr_auto_props"])) return false;
        $props = $this->settings["catalog"]["selector_find_props"];
        $props_name = parent::GetArraySrcAttr($this->settings["catalog"]["attr_auto_props"]);
        $arr_val = parent::GetArraySrcAttr($this->settings["catalog"]["selector_attr_value_auto_props"]);

        foreach (pq($el)->find($props) as $property) {
            $isset_props = false;

            if (empty($props_name["path"])) {
                $name = trim(pq($property)->attr($props_name["attr"])); //name props
            } else {
                if (empty($props_name["attr"])) {
                    $name = trim(strip_tags(pq($property)->find($props_name["path"])->html()));
                } elseif (!empty($props_name["attr"])) {
                    $name = trim(pq($property)->find($props_name["path"])->attr($props_name["attr"]));
                }
            }

            if (empty($name)) continue 1;

            if (empty($arr_val["path"]) && empty($arr_val["attr"])) {
                $value = trim(pq($property)->html());
            } elseif (empty($arr_val["path"]) && !empty($arr_val["attr"])) {
                $value = trim(pq($property)->attr($arr_val["attr"]));
            } else {
                if (empty($arr_val["attr"])) {
                    $value = strip_tags(trim(pq($property)->find($arr_val["path"])->html()));
                } elseif (!empty($arr_val["attr"])) {
                    $value = trim(pq($property)->find($arr_val["path"])->attr($arr_val["attr"]));
                }
            }

            $isset_props = $this->issetPropsForName($name);
            if (!$isset_props) {
                $error = $this->addAutoProps($name);
                if ($error !== false) {
                    $db_props = CIBlockProperty::GetByID($error, $this->iblock_id, false);
                    $code_props = $db_props->GetNext();
                    if (!isset($this->arProperties[$code_props["CODE"]]) || empty($this->arProperties[$code_props["CODE"]])) {
                        $this->arProperties[$code_props["CODE"]] = $code_props;
                    }
                    if (!isset($this->arPropertiesParseAuto[$code_props["CODE"]]) || empty($this->arPropertiesParseAuto[$code_props["CODE"]])) {
                        $this->arPropertiesParseAuto[$code_props["CODE"]] = $value;
                    }
                }
            }
            if ($isset_props) {
                foreach ($isset_props as $code => $props) {
                    if (!isset($this->arProperties[$code]) || empty($this->arProperties[$code])) {
                        $this->arProperties[$code] = $props;
                    }
                    if (!isset($this->arPropertiesParseAuto[$code]) || empty($this->arPropertiesParseAuto[$code])) {
                        $this->arPropertiesParseAuto[$code] = $value;
                    }
                }
            }

        }

    }

    protected function parseCatalogAutoPropsAdd()
    {
        $arProperties = $this->arPropertiesParseAuto;
        if (!$arProperties) return false;
        if ($this->settings["catalog"]["catalog_delete_selector_find_props_symb"]) {
            $deleteSymb = explode(",", $this->settings["catalog"]["catalog_delete_selector_find_props_symb"]);

            foreach ($deleteSymb as $i => &$symb) {
                $symb = trim($symb);
                $symb = htmlspecialcharsBack($symb);
                if (empty($symb)) {
                    unset($deleteSymb[$i]);
                    continue;
                }
                if ($symb == "\\\\") {
                    $deleteSymb[$i] = ",";
                }

            }
        }
        foreach ($arProperties as $code => $value) {
            $arProp = $this->arProperties[$code];
            if (($arProp["PROPERTY_TYPE"] == "S") || ($arProp["PROPERTY_TYPE"] == "L") || ($arProp["PROPERTY_TYPE"] == "N")) {
                $text = $value;
                if ($arProp["USER_TYPE"] != "HTML")
                    $text = strip_tags($value);
                $text = str_replace($deleteSymb, "", $text);
                $this->parseCatalogPropAuto($code, $text);
            }
        }
    }

    public function parseCatalogPropAuto($code, $val)
    {
        if (empty($code)) return false;
        $val = html_entity_decode($val);
        $arProp = $this->arProperties[$code];
        //$default = $this->settings["catalog"]["default_prop"][$code];

        if ($arProp["PROPERTY_TYPE"] != "N" && isset($this->settings["loc"]["f_props"]) && $this->settings["loc"]["f_props"])
            $val = parent::locText($val, $arProp["USER_TYPE"] == "HTML" ? "html" : "plain");

        if ($arProp["USER_TYPE"] == "HTML" && $arProp["MULTIPLE"] != "Y") {
            $this->arFields["PROPERTY_VALUES"][$code] = Array("VALUE" => Array("TEXT" => $val, "TYPE" => "html"));
        } elseif ($arProp["USER_TYPE"] == "HTML" && $arProp["MULTIPLE"] == "Y") {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = Array("VALUE" => Array("TEXT" => $val, "TYPE" => "html"));
        } elseif ($arProp["PROPERTY_TYPE"] == "S" && $arProp["MULTIPLE"] != "Y" && $arProp["USER_TYPE"] == "directory") {
            $this->arFields["PROPERTY_VALUES"][$code] = parent::CheckPropsDirectory($arProp, $code, $val);;
        } elseif ($arProp["PROPERTY_TYPE"] == "S" && $arProp["MULTIPLE"] == "Y" && $arProp["USER_TYPE"] == "directory") {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = parent::CheckPropsDirectory($arProp, $code, $val);;
        } elseif ($arProp["PROPERTY_TYPE"] == "S" && $arProp["MULTIPLE"] != "Y") {
            $val = parent::actionFieldProps($code, $val);
            $this->arFields["PROPERTY_VALUES"][$code] = $val;
        } elseif ($arProp["PROPERTY_TYPE"] == "S" && $arProp["MULTIPLE"] == "Y") {
            $val = parent::actionFieldProps($code, $val);
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $val;
        } elseif ($arProp["PROPERTY_TYPE"] == "N") {
            $val = str_replace(",", ".", $val);
            $val = preg_replace("/\.{1}$/", "", $val);
            $val = preg_replace('/[^0-9.]/', "", $val);
            $this->arFields["PROPERTY_VALUES"][$code] = $val;

        } elseif ($arProp["PROPERTY_TYPE"] == "L" && $arProp["MULTIPLE"] != "Y") {
            $this->arFields["PROPERTY_VALUES"][$code] = parent::CheckPropsL($arProp["ID"], $code, $val);
        } elseif ($arProp["PROPERTY_TYPE"] == "L" && $arProp["MULTIPLE"] == "Y") {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = parent::CheckPropsL($arProp["ID"], $code, $val);
        } elseif ($arProp["PROPERTY_TYPE"] == "E" && $arProp["MULTIPLE"] != "Y") {
            $this->arFields["PROPERTY_VALUES"][$code] = parent::CheckPropsE($arProp, $code, $val);
        } elseif ($arProp["PROPERTY_TYPE"] == "E" && $arProp["MULTIPLE"] == "Y") {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = parent::CheckPropsE($arProp, $code, $val);
        }
    }

    protected function addAutoProps($setting)                       //add auto props
    {
        if (empty($setting)) return false;
        $CODE = strtoupper(CUtil::translit($setting, "ru", array(
            "max_len" => 100,
            "change_case" => 'S', // 'L' - toLower, 'U' - toUpper, false - do not change
            "replace_space" => '_',
            "replace_other" => '_',
            "delete_repeat_replace" => true,
        )));
        $arFields = Array(
            "NAME" => $setting,
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => $CODE,
            "PROPERTY_TYPE" => $this->settings["catalog"]["type_auto_props"],
            "IBLOCK_ID" => $this->iblock_id
        );
        $ibp = new CIBlockProperty;
        $PropID = $ibp->Add($arFields);
        if ($PropID) return $PropID;
        else {
            $this->errors[] = "[" . $setting . "]" . $ibp->LAST_ERROR;
            return false;
        }
    }

    protected function issetPropsForName($name)                     //search props for name
    {
        if (empty($name)) return true;
        $property = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y", "IBLOCK_ID" => $this->iblock_id, "?PROPERTY_TYPE" => "S || N || L", "NAME" => $name));
        $prors = array();
        while ($prop = $property->Fetch()) {
            $prors[$prop["CODE"]] = $prop;
        }
        if ($property) {
            return $prors;
        }
        if (!$property) return false;
    }

    protected function parseCatalogIdElement($el)                   //parse id element
    {
        $id_element = $this->settings["catalog"]["id_selector"];

        $ar = parent::GetArraySrcAttr($id_element);
        $selector = $ar["path"];
        $attr = $ar["attr"];
        if (empty($id_element)) return false;
        if ($selector == "") {
            $p = trim(pq($el)->attr($attr));
        } else {
            if (!empty($attr)) {
                $p = trim(pq($el)->find($selector)->attr($attr));
            } elseif (empty($attr)) {
                $p = strip_tags((pq($el)->find($selector)->html()));
            }
        }
        if (!$p) {
            $this->errors[] = GetMessage("parser_error_id_element_notfound");
            return false;
        }
        $this->arFields["LINK"] = $p;
        return true;
    }

    protected function parseCatalogParrentSection(&$el)             //parse parent section
    {
        if (($this->settings["catalog"]["add_parser_section"] == "N") || empty($this->settings["catalog"]["id_section"])) return false;
        $ar = parent::GetArraySrcAttr($this->settings["catalog"]["id_section"]);
        $selector = $ar["path"];
        $attr = $ar["attr"];

        if ($selector == "") {
            $parrent_id = trim(pq($el)->attr($attr));
        } else {
            if (empty($attr)) $parrent_id = trim(strip_tags(pq($el)->find($selector)->html()));
            elseif (!empty($attr)) $parrent_id = trim(pq($el)->find($selector)->attr());
        }
        $parrent_id = trim($this->settings["catalog"]["id_section"]);
        $parrent_id = trim(pq($el)->find($parrent_id)->text());
        if (empty($parrent_id)) return false;
        elseif (!empty($parrent_id)) $parrent_id = $this->issetSectionCatalog($parrent_id);
        if ($parrent_id !== false) {
            $this->arFields["IBLOCK_SECTION_ID"] = $parrent_id;
        }
    }

    protected function parseCatalogSectionXml($pageHref)            //parse section
    {
        $this->html = phpQuery::newDocument($this->page);
        $this->base = $this->GetMetaBase($this->html);
        if ($this->settings["catalog"]["add_parser_section"] != "Y") return false;
        if (empty($this->settings['catalog']['selector_category'])) {
            $this->errors[] = GetMessage("parser_no_selector_category");
            return false;
        }
        $arr = $this->GetArrSectionXml();
        if ($arr !== false) {
            $new_section_arr = $this->GetTreeArrSectionXml($arr);
        }

        if (is_array($new_section_arr)) {
            if ($this->settings["catalog"]["field_id_category"] == "EXT_FIELD") {
                $this->AddExtFieldSection();
            }
            /*foreach($new_section_arr as $key=>&$value)
            {
                $startFunc = $key;
                foreach($value as $k => &$parentSec)
                {
                    $value[$k]["parentId"] = 0;
                }
                break;
            }*/
            $this->addAllSectionXml($new_section_arr, 0, $arr);
            CIBlockSection::ReSort($this->iblock_id);
        }
    }

    protected function GetArrSectionXml()
    {
        if (empty($this->settings['catalog']['selector_category'])) return false;

        $arr_section = $this->html->find($this->settings['catalog']['selector_category']);
        $arr = array();
        $arr_id = parent::GetArraySrcAttr($this->settings['catalog']["attr_id_category"]);
        $arr_name = parent::GetArraySrcAttr($this->settings['catalog']["attr_category"]);
        $arr_parent = parent::GetArraySrcAttr($this->settings['catalog']["attr_id_parrent_category"]);


        foreach ($arr_section as $el_section) {
            if ($arr_id['path'] == "") {
                $section_id = trim(pq($el_section)->attr($arr_id['attr']));
            } else {
                if (!empty($arr_id['attr'])) $section_id = trim(pq($el_section)->find($arr_id['path'])->attr($arr_id['attr']));
                elseif (empty($arr_id['attr'])) {
                    $section_id = trim(pq($el_section)->find($arr_id['path'])->html());
                    $section_id = trim(strip_tags($section_id));
                }
            }
            if (empty($section_id)) {
                continue 1;
            }
            if ($arr_parent["path"] == "") {
                $parentId = trim(pq($el_section)->attr($arr_parent["attr"]));
            } else {
                if (!empty($arr_parent["attr"])) $parentId = trim(pq($el_section)->find($arr_parent["path"])->attr($arr_parent["attr"]));
                elseif (empty($arr_parent["attr"])) {
                    $parentId = trim(pq($el_section)->find($arr_parent["path"])->html());
                    $parentId = trim(strip_tags($parentId));
                }

            }
            if (!isset($parentId) || empty($parentId)) {
                $parentId = 0;
            }

            if (empty($this->settings['catalog']["attr_category"])) {
                $arr[$section_id]['text'] = strip_tags(trim(pq($el_section)->html()));
            } elseif (!empty($this->settings['catalog']["attr_category"])) {
                if (!empty($arr_name['attr'])) {
                    $arr[$section_id]['text'] = trim(pq($el_section)->find($arr_name["path"])->attr($arr_name["attr"]));
                } elseif (empty($arr_name['attr'])) {
                    $arr[$section_id]['text'] = strip_tags(trim(pq($el_section)->find($arr_name["path"])->html()));
                }
            }
            $arr[$section_id]['parentId'] = $parentId;
        }

        ksort($arr);
        return $arr;
    }

    protected function GetTreeArrSectionXml($arr)
    {
        if (!is_array($arr)) return false;

        $new_section_arr = array();

        /* foreach ($arr as $key => $value) 
         {
             if (!isset($new_section_arr[$value['parentId']]) || empty($new_section_arr[$value['parentId']]))
             {
                 $new_section_arr[$value['parentId']]['text'] = $arr[$value['parentId']]['text']; 
             }
         }*/

        foreach ($arr as $k => $v) {
            $new_section_arr[$v['parentId']][$k]['text'] = $v['text'];
            $new_section_arr[$v['parentId']][$k]['parentId'] = $v['parentId'];
        }

        return $new_section_arr;
    }

    protected function addAllSectionXml($cats, $parent_id, $arr)
    {
        if ($parent_Id != 0) {
            if ($this->issetSectionCatalog($parent_Id) === false) {
                if ($arr[$parent_Id]["parentId"] == 0) {
                    $parent_section = $this->section_id;
                } else {
                    $parent_section = $this->issetSectionCatalog($arr[$parent_Id]["parentId"]);
                }
                $this->addSectionXlm(array("id_section" => $parent_Id, "text_section" => $arr[$parent_Id]["text"], "id_parrent" => $parent_section));
            }
        }
        if (is_array($cats) and isset($cats[$parent_id])) {

            foreach ($cats[$parent_id] as $id => $cat) {

                $id_sec = $this->issetSectionCatalog($id);

                if ($id_sec === false) {
                    if ($cat["parentId"] == 0) {
                        $parent_sec = $this->section_id;
                    } else {
                        $parent_sec = $this->issetSectionCatalog($parent_id);
                    }

                    $this->addSectionXlm(array("id_section" => $id, "text_section" => $cat["text"], "id_parrent" => $parent_sec));
                }
                /*else
                {
                    $this->UpdateSectionXml($id_sec, array("id_section" => $id, "text_section" => $cat["text"], "id_parrent" => $parent_sec));
                }*/

                $this->addAllSectionXml($cats, $id, $arr);
            }
        } else {
            return null;
        }

        /*foreach ($arr as $id => $val)
        {
            $section_title =  $arr[$id]['text'];
            if (empty($section_title) && ($id != 0))
            {
                continue;
            } 
            elseif ($id == 0) $parrent_section = $this->section_id;
            elseif ($id != 0) $parrent_section = $this->issetSectionCatalog($id); 
            
            if (($this->issetSectionCatalog($id) === false) && ($id != 0))
            {
                $this->addSectionXlm(array("id_section" => $id, "text_section" => $section_title, "id_parrent" => $parrent_section));
            }
            foreach ($val as $key => $text)
            {  
                if($key !== "text")
                {
                   if (($this->issetSectionCatalog($key) === false) && $parrent_section !== false)
                   {
                       $this->addSectionXlm(array("id_section" => $key, "text_section" => $text["text"], "id_parrent" => $parrent_section));
                   } 
                }
            }           
        }*/
    }

    protected function addSectionXlm($settings)
    {
        if (empty($settings) || !is_array($settings)) return false;
        $index_category = ($this->settings["catalog"]["index_category"] == "Y") ? true : false;
        $new_section = new CIBlockSection;
        $arFields = $this->GetArrFieldsSection($settings);
        $ID = $new_section->Add($arFields, false, $index_category);
        if ($ID !== false) {
            $this->countSection++;
        } elseif ($ID === false) {
            $this->errors[] = GetMessage("parser_error_add_category") . $settings["id_section"] . " - " . $new_section->LAST_ERROR;
        }
    }

    protected function UpdateSectionXml($id_sec, $settings)
    {
        if (empty($settings) || !is_array($settings) || empty($id_sec)) return false;
        $index_category = ($this->settings["catalog"]["index_category"] == "Y") ? true : false;
        $new_section = new CIBlockSection;
        $arFields = $this->GetArrFieldsSection($settings);
        $arFields = Array("NAME" => $arFields["NAME"]);
        $ID = $new_section->Update($id_sec, $arFields, false, $index_category);
        if ($ID !== false) {
            $this->countSection++;
        } elseif ($ID === false) {
            $this->errors[] = $new_section->LAST_ERROR;
        }
    }

    protected function GetArrFieldsSection($settings)
    {
        $code = "";
        if ($this->settings["catalog"]["code_category"] == "Y") {
            $code = $this->GetCodeSection($settings["text_section"]);
        }
        $arFields = Array(
            "ACTIVE" => "Y",
            "CODE" => $code,
            "IBLOCK_SECTION_ID" => $settings["id_parrent"],
            "IBLOCK_ID" => $this->iblock_id,
            "NAME" => $settings["text_section"],
            "DESCRIPTION" => GetMessage("parser_add_category_description"),
            "DESCRIPTION_TYPE" => "text"
        );
        if ($this->settings["catalog"]["field_id_category"] == "XML_ID") {
            $arFields["XML_ID"] = "sota_" . md5($this->rss) . "_" . $settings["id_section"];
        } elseif ($this->settings["catalog"]["field_id_category"] == "EXT_FIELD") {
            $arFields["UF_SOTA_PARSER"] = "sota_" . md5($this->rss) . "_" . $settings["id_section"];
        }
        return $arFields;
    }

    protected function GetCodeSection($settings)
    {
        $arFieldCode = $this->arrayIblock["FIELDS"]["SECTION_CODE"]["DEFAULT_VALUE"];
        $code = CUtil::translit($settings, "ru", array(
            "max_len" => $arFieldCode["TRANS_LEN"],
            "change_case" => $arFieldCode["TRANS_CASE"],
            "replace_space" => $arFieldCode["TRANS_SPACE"],
            "replace_other" => $arFieldCode["TRANS_OTHER"],
            "delete_repeat_replace" => $arFieldCode["TRANS_EAT"] == "Y" ? true : false,
        ));

        $db_section = CIBlockSection::GetList(Array("SORT" => "ASC"), array("%CODE" => $code, "IBLOCK_ID" => $this->iblock_id), false, Array("ID", "CODE"), false);

        while ($ar = $db_section->Fetch())
            $arCodes[$ar["CODE"]] = $ar["ID"];

        if (array_key_exists($code, $arCodes)) {
            $i = 1;
            while (array_key_exists($code . "_" . $i, $arCodes))
                $i++;

            return $code . "_" . $i;
        } else {
            return $code;
        }
    }

    protected function AddExtFieldSection()
    {
        $arFields = Array(
            "ENTITY_ID" => "IBLOCK_" . $this->iblock_id . "_SECTION",
            "FIELD_NAME" => "UF_SOTA_PARSER",
            "USER_TYPE_ID" => "string",
            "EDIT_FORM_LABEL" => Array("ru" => GetMessage("EDIT_FORM_LABEL_RU"), "en" => GetMessage("EDIT_FORM_LABEL_EN"))
        );
        $obUserField = new CUserTypeEntity;
        $obUserField->Add($arFields);
    }

    protected function issetSectionCatalog($settings)
    {
        if (empty($settings)) return false;
        $arFilter = $this->GetArrFilterSection($settings);
        if (!is_array($arFilter)) return false;
        $db_section = CIBlockSection::GetList(Array("SORT" => "ASC"), $arFilter, false, Array("ID", "XML_ID", "UF_SOTA_PARSER"), false);
        $id_section = $db_section->Fetch();
        if ($id_section) return $id_section["ID"];
        else return false;
    }

    protected function GetArrFilterSection($settings)
    {
        if ($this->settings["catalog"]["field_id_category"] == "XML_ID") {
            $arFilter = Array('IBLOCK_ID' => $this->iblock_id, '=XML_ID' => "sota_" . md5($this->rss) . "_" . $settings);
        } elseif ($this->settings["catalog"]["field_id_category"] == "EXT_FIELD") {
            $arFilter = Array('IBLOCK_ID' => $this->iblock_id, '=UF_SOTA_PARSER' => "sota_" . md5($this->rss) . "_" . $settings);
        }
        return $arFilter;
    }

    protected function parseCatalogDescriptionXml(&$el)
    {
        if (parent::checkUniq() && (!$this->isUpdate || (!$this->isUpdate["detail_descr"] && (!$this->isUpdate["preview_descr"] && !$this->settings["catalog"]["text_preview_from_detail"] != "Y")))) return false;
        if ($this->settings["catalog"]["detail_text_selector"]) {
            $detail = $this->settings["catalog"]["detail_text_selector"];

            $arDetail = explode(",", $detail);
            $detail_text = "";
            if ($arDetail && !empty($arDetail)) {
                foreach ($arDetail as $detail) {
                    $detail = trim($detail);
                    if (!$detail) continue 1;

                    foreach (pq($el)->find($detail . " img") as $img) {
                        $src = pq($img)->attr("src");
                        $src = parent::parseCaralogFilterSrc($src);
                        $src = parent::getCatalogLink($src);
                        parent::parseCatalogSaveImgServer($img, $src);
                    }

                    $arr = parent::GetArraySrcAttr($detail);
                    $path = $arr["path"];
                    $attr = $arr["attr"];
                    if (empty($attr))
                        $detail_text .= pq($el)->find($path)->html();
                    elseif (!empty($attr))
                        $detail_text .= pq($el)->find($path)->attr($attr);

                }
            }

            $detail_text = trim($detail_text);
            if (isset($this->settings["loc"]["f_detail_text"]) && $this->settings["loc"]["f_detail_text"] == "Y") {
                $detail_text = parent::locText($detail_text, $this->detail_text_type == "html" ? "html" : "plain");
            }
            $this->arFields["DETAIL_TEXT"] = $detail_text;
            $this->arFields["DETAIL_TEXT_TYPE"] = $this->detail_text_type;
            if ($this->settings["catalog"]["text_preview_from_detail"] == "Y") {
                $this->arFields["PREVIEW_TEXT"] = $this->arFields["DETAIL_TEXT"];
                $this->arFields["PREVIEW_TEXT_TYPE"] = $this->arFields["DETAIL_TEXT_TYPE"];
            }
        }
    }

    protected function ValidateUrl($url)
    {
        if (preg_match("/^(http|https)?(:\/\/)?([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/Diu", $url)) {
            return true;
        } else {
            return false;
        }
    }

    protected function ParseCatalogAvailablePreview(&$el)
    {
        if (!empty($this->settings["catalog"]["preview_count"])) {
            if (parent::checkUniq() && (!$this->isUpdate || !$this->isUpdate["count"])) return false;
            $arr = parent::GetArraySrcAttr($this->settings["catalog"]["preview_count"]);
            $path = $arr["path"];
            $attr = $arr["attr"];
            if (empty($attr)) {
                $available = pq($el)->find($path)->html();
            } elseif (!empty($attr)) {
                $available = pq($el)->find($path)->attr($attr);
            } elseif (empty($path) && !empty($attr)) {
                $available = pq($el)->attr($attr);
            }
            $available = trim(strip_tags($available));
            $available = preg_replace('/[^0-9.]/', "", $available);

            if (is_numeric($available)) {
                $available = intval($available);
                if ($available == 0) {
                    $this->arFields["AVAILABLE_PREVIEW"] = 0;
                } else {
                    $this->arFields["AVAILABLE_PREVIEW"] = $available;
                }
            }
        }
    }

    protected function ParseCatalogAvailableDetail(&$el)
    {
        if (!empty($this->settings["catalog"]["detail_count"])) {
            if (parent::checkUniq() && (!$this->isUpdate || !$this->isUpdate["count"])) return false;
            $arr = parent::GetArraySrcAttr($this->settings["catalog"]["detail_count"]);
            $path = $arr["path"];
            $attr = $arr["attr"];
            if (empty($attr)) {
                $available = pq($el)->find($path)->html();
            } elseif (!empty($attr)) {
                $available = pq($el)->find($path)->attr($attr);
            } elseif (empty($path) && !empty($attr)) {
                $available = pq($el)->attr($attr);
            }

            $available = trim(strip_tags($available));
            $available = preg_replace('/[^0-9.]/', "", $available);
            if (is_numeric($available)) {
                $available = intval($available);
                if ($available == 0) {
                    $this->arFields["AVAILABLE_DETAIL"] = 0;
                } else {
                    $this->arFields["AVAILABLE_DETAIL"] = $available;
                }
            }
        }
    }

    protected function addAvailable()
    {
        if ($this->elementUpdate && (!$this->isUpdate || !$this->isUpdate["count"])) return false;
        //if(!isset($this->arFields["AVAILABLE_DETAIL"]) && !isset($this->arFields["AVAILABLE_PREVIEW"])) return false;
        $isElement = $this->elementUpdate;
        $QUANTITY = $this->GetQuantity();

        if (is_numeric($QUANTITY)) {
            if (!$isElement)                                 //add QUANTITY
            {
                $q = CCatalogProduct::Add(array("ID" => $this->elementID, "QUANTITY" => $QUANTITY));
                if ($q === false) {
                    $this->errors[] = $this->arFields["NAME"] . "[" . $this->arFields["LINK"] . "] " . GetMessage("parser_error_add_quantity");
                }
            } else {                                               //update QUANTITY
                $q = CCatalogProduct::Update($this->elementID, array("QUANTITY" => $QUANTITY));
                if ($q === false) {
                    $this->errors[] = $this->arFields["NAME"] . "[" . $this->arFields["LINK"] . "] " . GetMessage("parser_error_add_quantity");
                }
            }
        }

    }

    protected function GetQuantity()
    {
        if (isset($this->arFields["AVAILABLE_DETAIL"]) && is_numeric($this->arFields["AVAILABLE_DETAIL"])) {
            return $this->arFields["AVAILABLE_DETAIL"];
        } elseif (isset($this->arFields["AVAILABLE_PREVIEW"]) && is_numeric($this->arFields["AVAILABLE_PREVIEW"])) {
            return $this->arFields["AVAILABLE_PREVIEW"];
        } else {
            if (is_numeric($this->settings["catalog"]["count_default"])) {
                return intval($this->settings["catalog"]["count_default"]);
            } else return false;
        }
        return false;
    }

    protected function parseOfferQuantity($offer)
    {
        if (parent::checkUniq() && (!$this->isUpdate || !$this->isUpdate["count"])) return false;
        if (isset($this->settings["offer"]["selector_quantity"]) && $this->settings["offer"]["selector_quantity"]) {
            $arr = parent::GetArraySrcAttr($this->settings["offer"]["selector_quantity"]);
            if (empty($arr["path"]) && !empty($arr["attr"])) {
                $quantity = trim(pq($offer)->attr($arr["attr"]));
            } else {
                if (empty($arr["attr"])) {
                    $quantity = trim(strip_tags(pq($offer)->find($arr["path"])->html()));
                } elseif (!empty($arr["attr"])) {
                    $quantity = trim(pq($offer)->find($arr["path"])->attr($arr["attr"]));
                }
            }
            $quantity = trim(strip_tags($quantity));
            $quantity = preg_replace('/[^0-9.]/', "", $quantity);

            if (is_numeric($quantity)) {
                $quantity = intval($quantity);
                if ($quantity == 0) {
                    $this->arOfferQuantity["QUANTITY"] = 0;
                } else {
                    $this->arOfferQuantity["QUANTITY"] = $quantity;
                }
            }

        } elseif (isset($this->settings["offer"]["find_quantity"]) && $this->settings["offer"]["find_quantity"]) {
            if (isset($this->settings["offer"]["selector_item_td"]) && $this->settings["offer"]["selector_item_td"]) {
                $name = $this->settings["offer"]["find_quantity"];
                if (isset($this->tableHeaderNumber[$name])) {
                    $n = $this->tableHeaderNumber[$name];
                    $quantity = pq($offer)->find($this->settings["offer"]["selector_item_td"] . ":eq(" . $n . ")");
                    $quantity = trim(strip_tags($price));
                    $quantity = preg_replace('/[^0-9.]/', "", $quantity);
                    if (is_numeric($quantity)) {
                        $quantity = intval($quantity);
                        if ($quantity == 0) {
                            $this->arOfferQuantity["QUANTITY"] = 0;
                        } else {
                            $this->arOfferQuantity["QUANTITY"] = $quantity;
                        }
                    }
                }
            }
        } elseif (isset($this->settings["offer"]["one"]["quantity"]) && $this->settings["offer"]["one"]["quantity"]) {
            $attr = $this->settings["offer"]["one"]["quantity"];
            $quantity = pq($offer)->attr($attr);
            $quantity = trim(strip_tags($quantity));
            $quantity = preg_replace('/[^0-9.]/', "", $quantity);

            if (is_numeric($quantity)) {
                $quantity = intval($quantity);
                if ($quantity == 0) {
                    $this->arOfferQuantity["QUANTITY"] = 0;
                } else {
                    $this->arOfferQuantity["QUANTITY"] = $quantity;
                }
            }
        }

        if (!isset($this->arOfferQuantity["QUANTITY"])) {
            $quantity = $this->GetQuantity();
            if (is_numeric($quantity)) {
                $this->arOfferQuantity["QUANTITY"] = $quantity;
            } else {
                return false;
            }
        }
        return true;
    }

    protected function AddQuantityCatalogOffer($arQuantity, $arFields)
    {
        if ($this->elementOfferUpdate && (!$this->isUpdate || !$this->isUpdate["count"])) return false;
        //if(!$this->arPrice || !$this->arPrice["PRICE"]) return false;
        $isElement = $this->elementOfferUpdate;
        /*$this->arPrice["PRODUCT_ID"] = $this->elementOfferID;
        $this->ChangePrice();
        $this->ConvertCurrency();*/

        //$obPrice = new CPrice();
        if (!$isElement) {
            $q = CCatalogProduct::Add(array("ID" => $this->elementOfferID, "QUANTITY" => $arQuantity["QUANTITY"]));
            if ($q === false) {
                $this->errors[] = $this->arFields["NAME"] . " - " . $arFields["NAME"] . "[" . $this->arFields["LINK"] . "] " . GetMessage("parser_error_add_price_offer");
            }
        } else {
            $q = CCatalogProduct::Update($this->elementOfferID, $arQuantity);
            if ($q === false) {
                $this->errors[] = $this->arFields["NAME"] . " - " . $arFields["NAME"] . "[" . $this->arFields["LINK"] . "] " . GetMessage("parser_error_add_price_offer");
            }
        }

    }


}

?>