<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

class SotaContentParser
{

    protected $id = false;
    protected $rss;
    protected $type;
    protected $active;
    protected $iblock_id;
    protected $section_id;
    protected $detail_dom;
    protected $encoding;
    protected $preview_delete_tag = "";
    protected $bool_preview_delete_tag = "";
    protected $detail_delete_tag = "";
    protected $bool_detail_delete_tag = "";
    protected $preview_first_img = "";
    protected $detail_first_img = "";
    protected $preview_save_img = "";
    protected $detail_save_img = "";
    protected $text = "";
    protected $site = "";
    protected $link = "";
    protected $preview_delete_element = "";
    protected $detail_delete_element = "";
    protected $preview_delete_attribute = "";
    protected $detail_delete_attribute = "";
    protected $index_element = "";
    protected $resize_image = "";
    protected $meta_description = "";
    protected $meta_keywords = "";
    protected $meta_description_text = "";
    protected $meta_keywords_text = "";
    protected $agent = false;
    protected $active_element = "Y";
    protected $header_url;
    protected $settings;
    protected $countPage = 0;
    protected $countItem = 0;
    protected $stepStart = false;

    protected $page;
    const TEST = 0;
    const DEFAULT_DEBUG_LIST = 3;
    const DEFAULT_DEBUG_ITEM = 3;

    public function __construct()
    {
        global $zis, $sota_ID, $sota_TYPE, $sota_ACTIVE, $sota_IBLOCK_ID, $sota_RSS, $sota_SECTION_ID, $sota_SELECTOR, $sota_ENCODING, $sota_PREVIEW_DELETE_TAG, $sota_PREVIEW_TEXT_TYPE, $sota_DETAIL_TEXT_TYPE, $sota_BOOL_PREVIEW_DELETE_TAG, $sota_PREVIEW_FIRST_IMG, $sota_PREVIEW_SAVE_IMG, $sota_DETAIL_DELETE_TAG, $sota_BOOL_DETAIL_DELETE_TAG, $sota_DETAIL_FIRST_IMG, $sota_DETAIL_SAVE_IMG, $sota_PREVIEW_DELETE_ELEMENT, $sota_DETAIL_DELETE_ELEMENT, $sota_PREVIEW_DELETE_ATTRIBUTE, $sota_DETAIL_DELETE_ATTRIBUTE, $sota_INDEX_ELEMENT, $sota_CODE_ELEMENT, $sota_RESIZE_IMAGE, $sota_META_DESCRIPTION, $sota_META_KEYWORDS, $sota_ACTIVE_ELEMENT, $sota_FIRST_TITLE, $sota_DATE_PUBLIC, $sota_FIRST_URL, $sota_DATE_ACTIVE, $sota_META_TITLE, $sota_SETTINGS;
        $this->id = $sota_ID;
        $this->type = $sota_TYPE;
        $this->rss = $sota_RSS;
        $this->active = $sota_ACTIVE;
        $this->iblock_id = $sota_IBLOCK_ID;
        $this->section_id = $sota_SECTION_ID;
        $this->detail_dom = $sota_SELECTOR;
        $this->first_url = trim($sota_FIRST_URL);
        $this->encoding = $sota_ENCODING;
        $this->preview_text_type = $sota_PREVIEW_TEXT_TYPE;
        $this->detail_text_type = $sota_DETAIL_TEXT_TYPE;
        $this->preview_delete_tag = $sota_PREVIEW_DELETE_TAG;
        $this->detail_delete_tag = $sota_DETAIL_DELETE_TAG;
        $this->bool_preview_delete_tag = $sota_BOOL_PREVIEW_DELETE_TAG;
        $this->bool_detail_delete_tag = $sota_BOOL_DETAIL_DELETE_TAG;
        $this->preview_first_img = $sota_PREVIEW_FIRST_IMG;
        $this->detail_first_img = $sota_DETAIL_FIRST_IMG;
        $this->preview_save_img = $sota_PREVIEW_SAVE_IMG;
        $this->detail_save_img = $sota_DETAIL_SAVE_IMG;
        $this->preview_delete_element = $sota_PREVIEW_DELETE_ELEMENT;
        $this->detail_delete_element = $sota_DETAIL_DELETE_ELEMENT;
        $this->preview_delete_attribute = $sota_PREVIEW_DELETE_ATTRIBUTE;
        $this->detail_delete_attribute = $sota_DETAIL_DELETE_ATTRIBUTE;
        $this->index_element = $sota_INDEX_ELEMENT;
        $this->code_element = $sota_CODE_ELEMENT;
        $this->resize_image = $sota_RESIZE_IMAGE;
        $this->meta_title = $sota_META_TITLE;
        $this->meta_description = $sota_META_DESCRIPTION;
        $this->meta_keywords = $sota_META_KEYWORDS;
        $this->active_element = $sota_ACTIVE_ELEMENT;
        $this->first_title = $sota_FIRST_TITLE;
        $this->date_public = $sota_DATE_PUBLIC;
        $this->date_active = $sota_DATE_ACTIVE;
        $this->settings = unserialize(base64_decode($sota_SETTINGS));
        $this->header_url = "";
        $this->sleep = (int)$this->settings[$this->type]["sleep"];
        $this->proxy = (int)$this->settings[$this->type]["proxy"];
        $this->errors = array();
        $this->auth = $this->settings[$this->type]["auth"]["active"] ? true : false;
        $this->currentPage = 0;
        $this->activeCurrentPage = 0;
        $this->debugErrors = array();
        $this->stepStart = false;
        $this->pagePrevElement = array();
        $this->pagenavigationPrev = array();
        $this->pagenavigation = array();
    }

    public function startParser($agent = false)
    {
        global $DB, $sota_DEMO; //$agent = true;
        if ($sota_DEMO == 3) return;
        $this->createFolder();
        if ($this->active != "Y") {
            $result["ERROR"][] = GetMessage("parser_active_no");
            $this->errors[] = GetMessage("parser_active_no");
            if (!$agent) CAdminMessage::ShowMessage(GetMessage("parser_active_no"));
            return $result;
        }
        $parser = new SotaParserContent();
        $now = time() + CTimeZone::GetOffset();
        $arFieldsTime['START_LAST_TIME_X'] = date($DB->DateFormatToPHP(FORMAT_DATETIME), $now);
        $parser->Update($this->id, $arFieldsTime);


        if ($this->meta_description != "N") {
            $propDescr = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y", "IBLOCK_ID" => $this->iblock_id, "CODE" => $this->meta_description))->Fetch();
            if (!$propDescr) {
                $result["ERROR"][] = GetMessage("parser_error_description");
                $this->errors[] = GetMessage("parser_error_description");
            }
        }
        if ($this->meta_keywords != "N") {
            $propKey = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y", "IBLOCK_ID" => $this->iblock_id, "CODE" => $this->meta_keywords))->Fetch();
            if (!$propKey) {
                $result["ERROR"][] = GetMessage("parser_error_keywords");
                $this->errors[] = GetMessage("parser_error_keywords");
                //return $result;
            }
        }
        if ($this->meta_title != "N") {
            $propKey = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y", "IBLOCK_ID" => $this->iblock_id, "CODE" => $this->meta_title))->Fetch();
            if (!$propKey) {
                $result["ERROR"][] = GetMessage("parser_error_title");
                $this->errors[] = GetMessage("parser_error_title");
                //return $result;
            }
        }
        if ($this->first_title != "N") {
            $propFirst = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y", "IBLOCK_ID" => $this->iblock_id, "CODE" => $this->first_title))->Fetch();
            if (!$propFirst) {
                $result["ERROR"][] = GetMessage("parser_error_first");
                $this->errors[] = GetMessage("parser_error_first");
                //return $result;
            }
        }
        if ($this->date_public != "N") {
            $propDate = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y", "IBLOCK_ID" => $this->iblock_id, "CODE" => $this->date_public))->Fetch();
            if (!$propDate) {
                $result["ERROR"][] = GetMessage("parser_error_date");
                $this->errors[] = GetMessage("parser_error_date");
                //return $result;
            }
        }


        if (isset($result['ERROR'])) {
            if (!$agent) foreach ($result['ERROR'] as $error) CAdminMessage::ShowMessage($error);
            return false;
        }
        $this->agent = $agent;
        if ($sota_DEMO == 2) $this->settings["catalog"]["mode"] = "debug";
        if ($_GET["begin"]) $this->auth(true);
        elseif ($this->agent || $this->settings["catalog"]["mode"] == "debug") $this->auth(true);


        if ($this->type == "catalog") {
            $this->isCatalog();
            $this->getUniqElement();
            $this->isUpdateElement();
            $this->GetSortFields();
            $this->getArrayIblock();
            $this->DoPageNavigation();
            $this->CheckFields($this->settings["catalog"]);

            if (!$this->errors) $this->parseCatalog();
            else {
                if (!$agent) foreach ($this->errors as $error) CAdminMessage::ShowMessage($error);
                $this->SaveLog();
                return false;
            }
            if ($this->debugErrors && $this->settings["catalog"]["mode"] == "debug") {
                if (!$agent) foreach ($this->debugErrors as $error) CAdminMessage::ShowMessage($error);
                $this->SaveLog();
                return false;
            }
            return true;
        }

        $this->rss = str_replace(array('http://', 'www.'), '', $this->rss);
        $arSite = explode('/', $this->rss);
        $level = explode('.', $arSite[0]);
        if (count($level) >= 3) $this->rss = $this->rss;
        else $this->rss = 'www.' . $this->rss;
        $arSite = explode('/', $this->rss);
        $this->site = $arSite[0];
        $uri = preg_replace('/^(www\.){0,1}([a-zA-Z0-9-\.])+\//', '', $this->rss);
        $arPath = explode('?', $uri);
        $path = '/' . $arPath[0];
        $query = $arPath[1];
        $arContent = $this->getContentsArray($this->site, 80, $path, $query);
        if (empty($arContent['title']) && empty($arContent['link'])) {
            $arContent = $this->getContentsArray(str_replace("www.", "", $this->site), 80, $path, $query);
            if (empty($arContent['title']) && empty($arContent['link'])) {
                $arContent = $this->getContentsArray("www." . $this->site, 80, $path, $query);
                if (empty($arContent['title']) && empty($arContent['link'])) {
                    if (!$agent) {
                        $result["ERROR"][] = GetMessage("parser_error");
                        $this->errors[] = GetMessage("parser_error");
                        //CAdminMessage::ShowMessage(GetMessage("parser_error"));
                        //return $result;
                    }
                    //return false;
                }
            }
        }
        if ($this->errors) {
            if (!$agent) foreach ($this->errors as $error) CAdminMessage::ShowMessage($error);
            if ($this->type != "page") return false;
        }
        if (isset($result['ERROR'])) {
            //if(!$agent)foreach($result['ERROR'] as $error) CAdminMessage::ShowMessage($error);
            if ($this->type != "page") return false;
        }


        return $this->setContentIblock($arContent, $this->iblock_id, $this->section_id, $this->detail_dom, $this->encoding);
    }

    private function setContentIblock($arContent = array(), $iblock_id = false, $section_id = false, $detail_dom = "", $encoding = "utf-8")
    {
        $first = false;
        global $sota_preview, $sota_first, $DB, $sota_DEMO;
        set_time_limit(0);
        $this->setDemo();
        /*if($this->first_url)
        {
            if($this->first_url && strpos($this->header_url, $this->first_url)===false && strpos($item['link'], $this->first_url)==false) continue;
        }
        else */
        $count = count($arContent['item']);
        $ci = 0;
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser" . $this->id . ".txt", $count . "|" . $ci);
        foreach ($arContent['item'] as $i => $item) {

            //if($this->sleep && $this->sleep>0)sleep($this->sleep);
            $item['title'] = trim($item['title']);
            $isElement = CIBlockElement::GetList(Array(), array("NAME" => $item['title'], "SECTION_ID" => $section_id, "IBLOCK_ID" => $iblock_id), false, Array("nTopCount" => 1), array("ID"))->Fetch();
            $ci++;
            if ($isElement && !self::TEST && $sota_DEMO == 1) continue;
            $first = true;
            $item['description'] = trim($item['description']);
            $this->link = $item['link'];
            $item['link'] = str_replace('http://', '', $item['link']);
            $fileHtml = new FileGetHtml();
            $this->date_public_text = $item["pubDate"];
            $proxy = $this->proxy;
            $data = $fileHtml->file_get_html($item['link'], $proxy, $this->auth);
            $this->header_url = $fileHtml->headerUrl;
            if ($this->first_url && strpos($this->header_url, $this->first_url) === false && strpos($item['link'], $this->first_url) == false) continue;
            $sota_first = true;
            //preg_replace("/\<meta\s+charser=[\"|']{0,1}.+[\"|']{0,1}\s*\/{0,1}\>/ig")
            //print $data;
            $this->DeleteCharsetHtml5($data);
            $html = phpQuery::newDocument($data, "text/html;charset=" . LANG_CHARSET);
            $sota_first = false;
            $this->first_title_text = $this->header_url;

            $this->getUrlSite();
            $DETAIL_TEXT = "";
            $this->text = "";

            $DETAIL_TEXT = $this->parserSelector($html, htmlspecialchars_decode(trim($detail_dom)));

            $el = new CIBlockElement;
            $sota_preview = true;
            if ($this->preview_first_img == "Y") $PREVIEW_IMG = $this->parserFirstImg(phpQuery::newDocument($item['description']), "text/html;charset=" . LANG_CHARSET);
            $sota_preview = false;
            if ($this->detail_first_img == "Y") $DETAIL_IMG = $this->parserFirstImg(phpQuery::newDocument($DETAIL_TEXT), "text/html;charset=" . LANG_CHARSET);

            $this->preview_delete_element = trim($this->preview_delete_element);
            $this->detail_delete_element = trim($this->detail_delete_element);
            $sota_preview = true;
            $preview_html = phpQuery::newDocument($item['description'], "text/html;charset=" . LANG_CHARSET);
            $sota_preview = false;
            $detail_html = phpQuery::newDocument($DETAIL_TEXT, "text/html;charset=" . LANG_CHARSET);
            if (!empty($this->preview_delete_element)) {
                $preview_html = $this->deleteElementStart($preview_html, htmlspecialchars_decode($this->preview_delete_element));
            }
            if (!empty($this->detail_delete_element)) {
                $detail_html = $this->deleteElementStart($detail_html, htmlspecialchars_decode($this->detail_delete_element));
            }
            if (!empty($this->preview_delete_attribute)) {
                $preview_html = $this->deleteAttributeStart($preview_html, htmlspecialchars_decode($this->preview_delete_attribute));
            }
            if (!empty($this->detail_delete_attribute)) {
                $detail_html = $this->deleteAttributeStart($detail_html, htmlspecialchars_decode($this->detail_delete_attribute));
            }


            $detail_html = $this->changeImgSrc($detail_html);
            $preview_html = $this->changeImgSrc($preview_html);

            if ($this->preview_save_img == "Y") $item['description'] = $this->saveImgServer($preview_html);
            else $item['description'] = $preview_html->htmlOuter();
            if ($this->detail_save_img == "Y") $DETAIL_TEXT = $this->saveImgServer($detail_html);
            else $DETAIL_TEXT = $detail_html->htmlOuter();
            $item['description'] = preg_replace("/\<meta(.)+\>{1}/", "", $item['description']);
            $DETAIL_TEXT = preg_replace("/\<meta(.)+\>{1}/", "", $DETAIL_TEXT);

            if ($this->code_element == "Y") $code = $arProperty["CODE"] = CUtil::translit($item['title'], LANGUAGE_ID, array(
                "max_len" => 100,
                "change_case" => 'L', // 'L' - toLower, 'U' - toUpper, false - do not change
                "replace_space" => '_',
                "replace_other" => '_',
                "delete_repeat_replace" => true,
            ));
            if ($this->date_public_text) $unix = strtotime($this->date_public_text);
            if ($this->date_active == "NOW") $date_from = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "SHORT");
            elseif ($this->date_active == "NOW_TIME") $date_from = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "FULL");
            elseif ($this->date_active == "PUBLIC" && $unix) $date_from = ConvertTimeStamp($unix, "FULL");


            if (!empty($this->preview_delete_tag) && $this->bool_preview_delete_tag == "Y") {
                $item['description'] = strip_tags($item['description'], htmlspecialchars_decode($this->preview_delete_tag));
            } elseif ($this->bool_preview_delete_tag == "Y") $item['description'] = strip_tags($item['description']);
            if (!empty($this->detail_delete_tag) && $this->bool_detail_delete_tag == "Y") {
                $DETAIL_TEXT = strip_tags($DETAIL_TEXT, htmlspecialchars_decode($this->detail_delete_tag));
            } elseif ($this->bool_detail_delete_tag == "Y") {
                $DETAIL_TEXT = strip_tags($DETAIL_TEXT);
            }

            $arLoadProductArray = Array(
                "MODIFIED_BY" => 1, // элемент изменен текущим пользователем
                "IBLOCK_SECTION_ID" => $this->section_id,          // элемент лежит в корне раздела
                "DATE_ACTIVE_FROM" => $date_from,
                "IBLOCK_ID" => $this->iblock_id,
                "NAME" => trim($item['title']),
                "ACTIVE" => $this->active_element == "Y" ? "Y" : "N",            // активен
                "PREVIEW_TEXT" => trim($item['description']),
                "PREVIEW_TEXT_TYPE" => $this->preview_text_type,
                "DETAIL_TEXT" => trim($DETAIL_TEXT),
                "DETAIL_TEXT_TYPE" => $this->detail_text_type,
                "CODE" => $code ? $code : ""
            );


            if (empty($PREVIEW_IMG) && $this->preview_first_img == "Y") $PREVIEW_IMG = $this->filterSrc($this->parseImgFromRss($item));
            if ($this->preview_first_img == "Y") $arLoadProductArray['PREVIEW_PICTURE'] = CFile::MakeFileArray($PREVIEW_IMG);
            if ($this->detail_first_img == "Y") $arLoadProductArray['DETAIL_PICTURE'] = CFile::MakeFileArray($DETAIL_IMG);
            if ($this->date_public != "N" && $this->date_public_text) {
                $new_date = date($DB->DateFormatToPHP(FORMAT_DATETIME), $unix);
                $arLoadProductArray['PROPERTY_VALUES'][$this->date_public] = $new_date;
            }
            if ($this->first_title != "N") $arLoadProductArray['PROPERTY_VALUES'][$this->first_title] = $this->first_title_text;
            if ($this->meta_title != "N") $arLoadProductArray['PROPERTY_VALUES'][$this->meta_title] = $this->meta_title_text;
            if ($this->meta_description != "N") $arLoadProductArray['PROPERTY_VALUES'][$this->meta_description] = $this->meta_description_text;
            if ($this->meta_keywords != "N") $arLoadProductArray['PROPERTY_VALUES'][$this->meta_keywords] = $this->meta_keywords_text;
            if ($PRODUCT_ID = $el->Add($arLoadProductArray, false, $this->index_element == "Y" ? true : false, $this->resize_image == "Y" ? true : false)) {
                $elem[] = ' ' . $PRODUCT_ID;
            } elseif (!$this->agent) {
                $result[ERROR][] = $el->LAST_ERROR;
            }

            $el = null;
            $isElement = null;
            if (isset($preview_html)) {
                unset($preview_html);
            }
            if (isset($detail_html)) {
                unset($detail_html);
            }
            unset($html);
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser" . $this->id . ".txt", $count . "|" . $ci);
            unset($fileHtml);
            if (self::TEST || $sota_DEMO == 2) break;
            if ($this->sleep && $this->sleep > 0) sleep($this->sleep);

        }
        unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser" . $this->id . ".txt");
        if ($elem) {
            $message = implode(',', $elem);
            $message = GetMessage("parser_pars_el_ok") . ' ' . $message . ' ' . GetMessage("parser_pars_create_ok");
        }
        if ($first && !$this->agent) {
            $result[SUCCESS][] = $message;
        } elseif (!$this->agent) {
            $result[ERROR][] = GetMessage("parser_no");
        }
        if (!$this->agent) {
            if (isset($result[SUCCESS]) && count($result[SUCCESS]) > 0) {
                foreach ($result['SUCCESS'] as $success) CAdminMessage::ShowMessage(array("MESSAGE" => $success, "TYPE" => "OK"));
            }
            if (isset($result[ERROR]) && count($result[ERROR]) > 0) {
                foreach ($result['ERROR'] as $error) CAdminMessage::ShowMessage($error);
            }
        }
        return $result;
    }

    public function GetAuthForm($check = false)
    {
        if (isset($this->settings[$this->type]["auth"]["active"]) && $this->settings[$this->type]["auth"]["active"] != "Y") return false;
        elseif (!isset($this->settings[$this->type]["auth"]["active"])) return false;
        elseif (isset($this->settings[$this->type]["auth"]["selector"]) && !$this->settings[$this->type]["auth"]["selector"]) {
            $this->errors[] = GetMessage("parser_auth_error_selector");
            return false;
        }


        $url = $this->settings[$this->type]["auth"]["url"] ? $this->settings["catalog"]["auth"]["url"] : $this->rss;
        $form = $this->settings[$this->type]["auth"]["selector"];
        $proxy = $this->settings[$this->type]["proxy"];

        $auth = new FileGetHtml();
        $data = $auth->file_get_html($url, $proxy);
        $this->urlCatalog = $auth->headerUrl;
        $this->urlSite = $this->getCatalogUrlSite();

        $this->CheckAuthForm($data, $form, $proxy);

        if ($check) if (isset($this->errors) && count(isset($this->errors)) > 0) {
            foreach ($this->errors as $error) {
                if (isset($_POST["auth"])) CAdminMessage::ShowMessage($error);
            }
        }
        if ($check) if (isset($this->success) && count(isset($this->success)) > 0) {
            foreach ($this->success as $success) {
                if (isset($_POST["auth"])) CAdminMessage::ShowMessage(array("MESSAGE" => $success, "TYPE" => "OK"));
            }
        }

    }

    public function CheckAuthform($data, $form, $proxy)
    {
        $this->html = phpQuery::newDocument($data, "text/html;charset=" . LANG_CHARSET);
        //print pq($this->html)->html();
        $objForm = pq($this->html)->find($form);
        $url = $objForm->attr("action");
        $url = empty($url) ? $this->urlCatalog : $this->getCatalogLink($url);

        $login = trim($this->settings[$this->type]["auth"]["login"]);
        $password = trim($this->settings[$this->type]["auth"]["password"]);
        foreach ($this->html[$form . " input"] as $input) {
            $name = trim(pq($input)->attr("name"));
            $value = trim(pq($input)->attr("value"));
            $type = trim(pq($input)->attr("type"));
            if (isset($this->settings[$this->type]["auth"]["password_name"]) && !empty($this->settings[$this->type]["auth"]["password_name"]) && $name == $this->settings[$this->type]["auth"]["password_name"]) {
                //if($name==$this->settings[$this->type]["auth"]["password_name"])
                {
                    $arInput[$name] = $password;
                    continue;
                }
            } elseif (isset($this->settings[$this->type]["auth"]["login_name"]) && !empty($this->settings[$this->type]["auth"]["login_name"]) && $name == $this->settings[$this->type]["auth"]["login_name"]) {
                //if($name==$this->settings[$this->type]["auth"]["login_name"])
                {
                    $arInput[$name] = $login;
                    continue;
                }
            } elseif ($type == "password") {
                $arInput[$name] = $password;
                continue;
            } elseif ($type == "text" || $type == "email") {
                $arInput[$name] = $login;
                continue;
            }
            $arInput[$name] = $value;
        }
        if (isset($arInput)) $this->doAuth($url, $arInput, $proxy);
        else {
            $this->errors[] = GetMessage("parser_auth_error_selector");
        }
    }

    protected function doAuth($url, $arInput, $proxy)
    {
        $auth = new FileGetHtml();
        $data = $auth->auth($url, $proxy, $arInput, true);
        //if(isset($_POST["auth"]))
        $this->AdminAuth($data);
    }

    protected function AdminAuth($data)
    {
        $form = $this->settings[$this->type]["auth"]["selector"];
        $this->html = phpQuery::newDocument($data, "text/html;charset=" . LANG_CHARSET);
        $passw = false;
        foreach ($this->html[$form . " input"] as $input) {
            $type = pq($input)->attr("type");
            if ($type == "password") {
                $passw = true;
            }
        }

        if ($passw)
            $this->errors[] = GetMessage("parser_auth_no");
        else
            $this->success[] = GetMessage("parser_auth_ok");
    }

    protected function DoPageNavigation()
    {
        $begin = $this->settings["catalog"]["pagenavigation_begin"];
        $end = $this->settings["catalog"]["pagenavigation_end"];
        $this->arPageNavigationDelta[0] = $begin;
        $this->arPageNavigationDelta[1] = $end;
    }

    protected function ValidatePageNavigation($n)
    {
        $n = strip_tags($n);
        $n = preg_replace("/\D/", "", $n);
        return $n;
    }

    //Входит ли число в пагинацию
    protected function CheckPageNavigation($n)
    {
        if (!preg_match("/\d/", $n) || empty($n)) return false;
        if ($this->currentPage > $n) return false;
        if ($this->arPageNavigationDelta[0] && $this->arPageNavigationDelta[1]) {
            if ($n >= $this->arPageNavigationDelta[0] && $n <= $this->arPageNavigationDelta[1]) return $n;
        } elseif ($this->arPageNavigationDelta[0] && !$this->arPageNavigationDelta[1]) {
            if ($n >= $this->arPageNavigationDelta[0]) return $n;
        } elseif (!$this->arPageNavigationDelta[0] && $this->arPageNavigationDelta[1]) {
            if ($n <= $this->arPageNavigationDelta[1]) return $n;
        }
        return false;
    }

    protected function CheckPageNavigationLess($n)
    {
        if (!preg_match("/\d/", $n) || empty($n)) return false;

        if ($this->currentPage > $n) return false;

        if ($this->arPageNavigationDelta[1]) {
            if ($n <= $this->arPageNavigationDelta[1]) return $n;
        } elseif (!$this->arPageNavigationDelta[1]) return true;
        return false;
    }

    protected function CheckValidatePageNavigation($n)
    {
        if ($this->arPageNavigationDelta[0] && $this->arPageNavigationDelta[1]) {
            if ($n <= $this->arPageNavigationDelta[0] && $n <= $this->arPageNavigationDelta[1]) return true;
        } elseif ($this->arPageNavigationDelta[0] && !$this->arPageNavigationDelta[1]) {
            if ($n <= $this->arPageNavigationDelta[0] && $n <= 100000) return true;
        } elseif (!$this->arPageNavigationDelta[0] && $this->arPageNavigationDelta[1]) {
            if ($n <= $this->arPageNavigationDelta[1]) return true;
        }
    }

    protected function CheckOnePageNavigation()
    {
        if ($this->settings["catalog"]["pagenavigation_begin"] == 1 && $this->settings["catalog"]["pagenavigation_end"] == 1) {
            return true;
        } elseif (!$this->settings["catalog"]["pagenavigation_selector"]) return true;

        return false;
    }

    protected function CheckAlonePageNavigation($n)
    {
        if (!empty($this->settings["catalog"]["pagenavigation_begin"]) && !empty($this->settings["catalog"]["pagenavigation_end"]) && $this->settings["catalog"]["pagenavigation_end"] == $this->settings["catalog"]["pagenavigation_begin"] && $n == $this->settings["catalog"]["pagenavigation_begin"]) {
            return true;
        }

        return false;
    }

    protected function IsNumberPageNavigation()
    {
        if (!$this->settings["catalog"]["pagenavigation_begin"] && !$this->settings["catalog"]["pagenavigation_end"]) return false;
        else return true;
    }

    protected function DeleteLog()
    {
        if ($this->agent) unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog" . $this->id . ".txt");
    }

    protected function SaveLog()
    {
        if ($this->settings["catalog"]["log"] == "Y" && isset($this->errors) && count($this->errors) > 0)
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_log_" . $this->id . ".txt", print_r($this->errors, true), FILE_APPEND);

        if (!isset($this->errors)) $this->errors = array();
        $this->debugErrors = array_merge($this->debugErrors, $this->errors);
    }

    protected function isUpdateElement()
    {
        if ($this->settings["catalog"]["update"]["active"]) {
            unset($this->settings["catalog"]["update"]["active"]);
            foreach ($this->settings["catalog"]["update"] as $id => $val) {
                if ($val == "Y" || $val == "empty") $this->isUpdate[$id] = $val;
            }
            if (!isset($this->isUpdate) || !$this->isUpdate) $this->isUpdate = false;
        } else  $this->isUpdate = false;
    }

    protected function getUniqElement()
    {
        //if($this->settings["catalog"]["update"]["active"]=="Y")
        {
            $this->uniqFields["NAME"] = "NAME";
            $this->uniqFields["LINK"] = "LINK";

            if ($this->settings["catalog"]["uniq"]["prop"]) {
                unset($this->uniqFields["LINK"]);
                unset($this->uniqFields["NAME"]);
                $prop = $this->settings["catalog"]["uniq"]["prop"];
                $this->uniqFields[$prop] = $prop;
            }
            if ($this->settings["catalog"]["uniq"]["name"]) {
                unset($this->uniqFields["LINK"]);
                unset($this->uniqFields["NAME"]);
                $this->uniqFields["NAME"] = "NAME";
            }
        }
    }

    protected function GetSortFields()
    {
        $this->arSortUpdate = array();
        $this->arEmptyUpdate = array();
        if ($this->isUpdate) {
            foreach ($this->isUpdate as $id => $val) {
                if ($val != "empty") continue;
                if ($id == "preview_img") $this->arSortUpdate[] = "PREVIEW_PICTURE";
                elseif ($id == "detail_img") $this->arSortUpdate[] = "DETAIL_PICTURE";
                elseif ($id == "preview_descr") $this->arSortUpdate[] = "PREVIEW_TEXT";
                elseif ($id == "detail_descr") $this->arSortUpdate[] = "DETAIL_TEXT";
            }
        }
    }

    protected function checkUniq()
    {
        if ($this->elementUpdate) return $this->elementUpdate;
        if (!isset($this->arSortUpdate)) $this->arSortUpdate = array();
        if ($this->uniqFields["LINK"]) {
            $uniq = md5($this->arFields["NAME"] . $this->arFields["LINK"]);
            $isElement = CIBlockElement::GetList(Array(), array("XML_ID" => $uniq, "IBLOCK_ID" => $this->iblock_id), false, Array("nTopCount" => 1), array_merge(array("ID"), $this->arSortUpdate))->Fetch();
            $this->elementUpdate = $isElement["ID"];
            if ($isElement) {
                $this->arEmptyUpdate = $isElement;
                return $isElement["ID"];
            } else return false;
        } else {
            if ($this->settings["catalog"]["uniq"]["prop"]) {
                $prop = $this->settings["catalog"]["uniq"]["prop"];
                if ($this->arFields["PROPERTY_VALUES"][$prop]) $arFields["PROPERTY_" . $prop] = $this->arFields["PROPERTY_VALUES"][$prop];
            }
            if ($this->settings["catalog"]["uniq"]["name"]) {
                $prop = $this->settings["catalog"]["uniq"]["prop"];
                if ($this->arFields["NAME"]) $arFields["NAME"] = $this->arFields["NAME"];
            }
            if (count($arFields) == count($this->uniqFields)) $isElement = CIBlockElement::GetList(Array(), array_merge(array("IBLOCK_ID" => $this->iblock_id), $arFields), false, Array("nTopCount" => 1), array_merge(array("ID"), $this->arSortUpdate))->Fetch();
            $this->elementUpdate = $isElement["ID"];
            if ($isElement) {
                $this->arEmptyUpdate = $isElement;
                return $isElement["ID"];
            } else return false;
        }

        return false;
    }

    protected function checkOfferUniqSpecial($size)
    {
        if ($this->elementOfferUpdate) return $this->elementOfferUpdate;

        $uniq = "offer#" . md5($this->arFields["NAME"] . $this->arFields["LINK"] . $size);
        $isElement = CIBlockElement::GetList(Array(), array("XML_ID" => $uniq, "IBLOCK_ID" => $this->offerArray["IBLOCK_ID"]), false, Array("nTopCount" => 1), array("ID"))->Fetch();
        $this->elementOfferUpdate = $isElement["ID"];
        if ($isElement) return $isElement["ID"];
        else return false;
    }

    protected function isCatalog()
    {
        if (CModule::IncludeModule('catalog') && ($this->iblock_id && CCatalog::GetList(Array("name" => "asc"), Array("ACTIVE" => "Y", "ID" => $this->iblock_id))->Fetch())) {
            if ($this->settings["catalog"]["preview_price"] || $this->settings["catalog"]["detail_price"]) {
                $this->isCatalog = true;
            } else $this->isCatalog = false;
        } else $this->isCatalog = false;
        if (isset($this->settings["catalog"]["cat_vat_price_offer"]) && $this->settings["catalog"]["cat_vat_price_offer"] == "Y") {
            $arIblock = CCatalogSKU::GetInfoByIBlock($this->iblock_id);
            if (is_array($arIblock) && !empty($arIblock) && $arIblock["PRODUCT_IBLOCK_ID"] != 0 && $arIblock["SKU_PROPERTY_ID"] != 0) {
                $this->isOfferCatalog = true;
                $this->offerArray = $arIblock;
                $this->isCatalog = true;
            } else $this->isOfferCatalog = false;
        }


    }

    protected function CheckFields($settings)
    {
        if (preg_match("/\D/", $settings["pagenavigation_begin"]) && $settings["pagenavigation_begin"] != "") {
            $this->errors[] = GetMessage("parser_error_pagenavigation_begin");
        }
        if (preg_match("/\D/", $settings["pagenavigation_end"]) && $settings["pagenavigation_end"] != "") {
            $this->errors[] = GetMessage("parser_error_pagenavigation_end");
        }
        if (preg_match("/\D/", $settings["step"])) {
            $this->errors[] = GetMessage("parser_error_step");
        }

        if (is_array($settings["price_updown"])) {
            foreach ($settings["price_updown"] as $i => $val) {
                if ($settings["price_updown"][$i]) {
                    if ($settings["price_terms"][$i] && !self::isFloat($settings["price_terms_value"][$i])) {
                        $this->errors[] = GetMessage("parser_error_price_terms_value");
                    }
                    if ($settings["price_terms"][$i] && !self::isFloat($settings["price_terms_value_to"][$i])) {
                        $this->errors[] = GetMessage("parser_error_price_terms_value");
                    }
                    if ($settings["price_updown"][$i] && !self::isFloat($settings["price_value"][$i])) {
                        $this->errors[] = GetMessage("parser_error_price_value");
                    }
                }
            }
        }


        $properties = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y", "IBLOCK_ID" => $this->iblock_id));
        while ($prop_fields = $properties->GetNext()) {
            $this->arProperties[$prop_fields["CODE"]] = $prop_fields;
        }

        $this->arSelectorProduct = $this->getSelectorProduct();
        $this->arFindProduct = $this->getFindProduct();
        $this->arSelectorProperties = $this->getSelectorProperties();
        $this->arFindProperties = $this->getFindProperties();
        $this->arDubleFindProperties = $this->getFindDubleProperties();
        //printr($this->ArDubleFindProperties);
    }

    protected function isFloat($n)
    {
        if (preg_match("/^(?:\+|\-)?(?:(?:\d+)|(?:\d+\.)|(?:\.\d+)|(?:\d+\.\d+)){1}(?:e(?:\+|\-)?\d+)?$/i", $n)) return true;
        else return false;
    }


    protected function parseCatalog()
    {
        set_time_limit(0);
        $this->ClearAjaxFiles();
        $this->DeleteLog();
        $this->arUrl = array();
        if (isset($this->settings["catalog"]["url_dop"]) && !empty($this->settings["catalog"]["url_dop"])) $this->arUrl = explode("\r\n", $this->settings["catalog"]["url_dop"]);

        $this->arUrl = array_merge(array($this->rss), $this->arUrl);
        $this->arUrlSave = $this->arUrl;
        //print "TEST";
        //printr($this->arUrl);

        if (!$this->PageFromFile()) return false;
        $this->CalculateStep();
        if ($this->settings["catalog"]["mode"] != "debug" && !$this->agent) $this->arUrlSave = array($this->rss);
        else $this->arUrlSave = $this->arUrl;
        //if(!$this->connectCatalogPage($this->rss));
        //return;
        foreach ($this->arUrlSave as $rss):
            $rss = trim($rss);
            if (empty($rss)) continue;
            $this->rss = $rss;
            $this->connectCatalogPage($this->rss);
            if (!$this->agent && $this->settings["catalog"]["mode"] != "debug" && isset($this->errors) && count($this->errors) > 0) {
                $this->SaveLog();
                unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog_step" . $this->id . ".txt");
                unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_copy_page" . $this->id . ".txt");
                return false;
            }

            $this->parseCatalogNavigation($this->rss);
            $n = $this->currentPage;
            if (!$this->IsNumberPageNavigation()) {
                $this->parseCatalogProducts();
            } elseif ($this->IsNumberPageNavigation() && $this->CheckPageNavigation($n)) {
                $this->parseCatalogProducts();
            } elseif ($this->settings["catalog"]["mode"] != "debug" && !$this->agent) {
                $this->stepStart = true;
                $this->SavePrevPage($this->rss);
            }

            $this->SaveCurrentPage($this->pagenavigation);
            if ($this->stepStart) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog_step" . $this->id . ".txt"))
                    unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog_step" . $this->id . ".txt");
                $this->DeleteCopyPage();
            }
            if ((!$this->CheckOnePageNavigation() && $this->agent) || (!$this->CheckOnePageNavigation() && !$this->agent && $this->settings["catalog"]["mode"] == "debug")) $this->parseCatalogPages();
            if ($this->CheckOnePageNavigation() && $this->stepStart) {
                if ($this->IsEndSectionUrl()) $this->ClearBufferStop();
                else $this->ClearBufferStep();
                return false;
            }
        endforeach;
    }

    protected function PageFromFile()
    {
        if ($this->settings["catalog"]["mode"] == "debug" || $this->agent || $_GET["begin"]) return true;
        $prevPage = $prevElement = $currentPage = 0;
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_prev_page" . $this->id . ".txt"))
            $prevPage = file_get_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_prev_page" . $this->id . ".txt");
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_prev_element" . $this->id . ".txt"))
            $prevElement = file_get_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_prev_element" . $this->id . ".txt");
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_current_page" . $this->id . ".txt"))
            $currentPage = file_get_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_current_page" . $this->id . ".txt");

        if ($prevPage) {
            $arPrevPage = explode("|", $prevPage);
            $arPrevElement = explode("|", $prevElement);
            $arCurrentPage = explode("|", $currentPage);
        } else {
            $arPrevPage = array();
            $arCurrentPage = array();
        }

        if (isset($arPrevElement) && is_array($arPrevElement)) foreach ($arPrevElement as $i => $p) {
            $p = trim($p);
            if (empty($p)) continue;
            $this->pagePrevElement[$p] = $p;
        }

        if (!$_GET["begin"] && !$prevPage) return true;

        if (isset($arPrevPage) && is_array($arPrevPage)) foreach ($arPrevPage as $i => $p) {
            $p = trim($p);
            if (empty($p)) continue;
            $this->pagenavigationPrev[$p] = $p;
        }


        if (isset($arCurrentPage) && is_array($arCurrentPage)) foreach ($arCurrentPage as $p) {
            $p = trim($p);
            if (empty($p)) continue;
            $this->pagenavigation[$p] = $p;
        }

        if (isset($this->pagenavigationPrev) && is_array($this->pagenavigationPrev)) foreach ($this->pagenavigationPrev as $i => $v) {
            foreach ($this->pagenavigation as $i1 => $v1) {
                if ($v1 == $v) unset($this->pagenavigation[$i1]);
            }
        }

        if (isset($this->pagenavigation) && is_array($this->pagenavigation)) foreach ($this->pagenavigation as $p) {
            $isContinue = true;
            $this->rss = $p;
            break;
        }
        if (!$isContinue && !empty($this->pagenavigationPrev) && $this->IsEndSectionUrl()) {

            //if($this->IsEndSectionUrl())
            $this->ClearBufferStop();
            //else $this->ClearBufferStep();
            return false;
        } elseif (!$isContinue && !empty($this->pagenavigationPrev) && !$this->IsEndSectionUrl()) {
            $isContinue = true;
            $this->rss = $this->GetUrlRss();
        }

        $this->currentPage = count($this->pagenavigationPrev);
        if ($this->IsNumberPageNavigation() && $this->CheckPageNavigation($this->currentPage)) {

            $this->activeCurrentPage = $this->currentPage - $this->arPageNavigationDelta[0] + 1;
        } elseif (!$this->IsNumberPageNavigation()) $this->activeCurrentPage = $this->currentPage;
        return true;
    }

    protected function IsEndSectionUrl()
    {
        if (!isset($this->settings["catalog"]["url_dop"]) || empty($this->settings["catalog"]["url_dop"]) || empty($this->arUrl)) return true;
        $count = 0;
        foreach ($this->arUrl as $i => $url) {
            if (isset($this->pagenavigationPrev[$url])) $count++;
        }
        if ($count == count($this->arUrl)) return true;
        else return false;
    }

    protected function GetUrlRss()
    {
        foreach ($this->arUrl as $i => $url) {
            if (isset($this->pagenavigationPrev[$url])) continue;
            return $url;
        }
    }

    protected function ClearBufferStop()
    {
        if (!$this->agent && $this->settings["catalog"]["mode"] != "debug") {
            global $APPLICATION;
            //if(self::TEST==0)
            $APPLICATION->RestartBuffer();
            die("stop");
        }
    }

    protected function ClearBufferStep()
    {
        if (!$this->agent && $this->settings["catalog"]["mode"] != "debug") {
            global $APPLICATION;
            if (self::TEST == 0) $APPLICATION->RestartBuffer();
            die();
        }
    }

    protected function parseCatalogProducts()
    {
        $count = 0;

        $this->activeCurrentPage++;
        $this->SetCatalogElementsResult($this->activeCurrentPage);

        $element = $this->settings["catalog"]["selector"];

        if ($this->preview_delete_element) $this->deleteCatalogElement($this->preview_delete_element, $element, $this->html[$element]);
        if ($this->preview_delete_attribute) $this->deleteCatalogAttribute($this->preview_delete_attribute, $element, $this->html[$element]);
        $i = 0;
        $ci = 0;

        foreach ($this->html[$element] as $el) {
            $count++;
        }

        if ($this->settings["catalog"]["mode"] != "debug" && !$this->agent) {
            if ($count > $this->settings["catalog"]["step"] && ($this->settings["catalog"]["mode"] != "debug" && !$this->agent))
                $countStep = $this->settings["catalog"]["step"];
            else {
                $this->stepStart = true;
                if ($this->CheckOnePageNavigation() || $this->CheckAlonePageNavigation($this->currentPage)) $this->pagenavigation[$this->rss] = $this->rss;
                $this->SaveCurrentPage($this->pagenavigation);
                $this->SavePrevPage($this->sectionPage);
                $countStep = $count;
            }
        } else {
            $countStep = $count;
        }

        file_put_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser" . $this->id . ".txt", $countStep . "|" . $ci);

        if ($count == 0) {
            $this->errors[] = GetMessage("parser_error_selector_notfound") . "[" . $element . "]";
            $this->clearFields();
            //die();
        }

        foreach ($this->html[$element] as $el) {
            $ci++;
            if ($this->StepContinue($ci, $count)) continue;
            if ($i == self::DEFAULT_DEBUG_ITEM && $this->settings["catalog"]["mode"] == "debug") break;
            $this->parseCatalogProductElement($el);

            $i++;
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser" . $this->id . ".txt", $countStep . "|" . $i);
            $this->CalculateStep($count);

        }
        unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser" . $this->id . ".txt");
    }

    protected function parseCatalogProductElement(&$el)
    {
        $this->countItem++;
        if (!$this->parserCatalogPreview($el)) {
            //$this->SaveLog();
            $this->SaveCatalogError();
            $this->clearFields();
            return false;
        }

        $this->parserCatalogDetail();
        $this->parseCatalogSection();
        $this->parseCatalogMeta();
        $this->parseCatalogFirstUrl();
        $this->parseCatalogDate();
        $this->parseCatalogAllFields();
        $this->AddElementCatalog();
        if ($this->isCatalog && $this->elementID) {
            /*if($this->isOfferCatalog)
            {                                   
                $this->AddElementOfferCatalog();
                $this->elementID = $this->elementOfferID;
                $this->elementUpdate = $this->elementOfferUpdate;
            }
            $this->AddProductCatalog();
            $this->AddMeasureCatalog();
            $this->AddPriceCatalog();*/

            if (isset($this->strArSize) && is_array($this->strArSize)) {
                $elID = $this->elementID;
                $elUpdateID = $this->elementUpdate;
                $arPrice = $this->arPrice;
                foreach ($this->strArSize as $size) {
                    $size = trim($size);
                    if (!$size) continue;
                    $this->elementID = $elID;
                    $this->elementUpdate = $elUpdateID;
                    $this->arPrice = $arPrice;
                    $this->AddElementOfferCatalogSpecial($size);
                    $this->elementID = $this->elementOfferID;
                    $this->elementUpdate = $this->elementOfferUpdate;

                    $this->AddProductCatalog();
                    $this->AddMeasureCatalog();
                    $this->AddPriceCatalog();
                    unset($this->elementUpdate);
                    unset($this->elementOfferUpdate);
                }
            }


            /**/
            //printr($this->arFields["PROPERTY_VALUES"]);
            /**/


        }/*else{
            $this->AddElementOfferCatalog();
            $this->AddProductCatalog();
            $this->AddMeasureCatalog();
            $this->AddPriceCatalog();    
        }*/

        $this->SetCatalogElementsResult();
        $this->clearFields();

    }

    protected function StepContinue($n, $count = 0)
    {
        if ($this->settings["catalog"]["mode"] == "debug" || $this->agent) return false;
        $step = (int)$this->settings["catalog"]["step"];
        if ($step > $count && $count > 0) return false;
        $file = 0;

        if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog_step" . $this->id . ".txt"))
            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog_step" . $this->id . ".txt");
        if ($file) {
            $arFile = explode("|", $file);
            $countElement = (int)$arFile[0];
            $currentElement = (int)$arFile[1];
        } else {
            return false;
        }

        if ($currentElement > 0 && $n <= $currentElement && $currentElement % $step == 0) return true;
        else return false;
    }

    protected function CalculateStep($count = 0)
    {

        if ($this->settings["catalog"]["mode"] == "debug" || $this->agent || $this->stepStart) return true;
        $step = $this->settings["catalog"]["step"];
        if ($step > $count && $count > 0) {
            $this->stepStart = true;
            return true;
        }
        $file = 0;
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog_step" . $this->id . ".txt"))
            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog_step" . $this->id . ".txt");
        if ($file) {
            $arFile = explode("|", $file);
            $countElement = (int)$arFile[0];
            $currentElement = (int)$arFile[1];
        } else {
            $countElement = $count;
            $currentElement = 0;
        }
        if ($countElement - $currentElement <= $step && $countElement > 0 && $count == 0) {
            $this->stepStart = true;
        }

        if ($count == 0) return true;
        $currentElement++;
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog_step" . $this->id . ".txt", $countElement . "|" . $currentElement);
        if ($currentElement % $step == 0 && !$this->stepStart) {
            $this->clearFields();
            $this->ClearBufferStep();
        }


    }

    protected function SetCatalogElementsResult($page = false)
    {
        $file = 0;
        if (file_exists(($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog" . $this->id . ".txt")))
            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog" . $this->id . ".txt");

        if ($file) {
            $arFile = explode("|", $file);

            $countPage = (int)$arFile[1];
            $ciElement = (int)$arFile[2];
            $errorElement = (int)$arFile[3];
            $allError = (int)$arFile[4];
        } else {
            $countPage = 0;
            $ciElement = 0;
            $errorElement = 0;
            $allError = 0;
        }

        if ($page) {
            $countPage = $page;
        } elseif (isset($this->elementID)) {
            $ciElement++;
            if (isset($this->errors) && count($this->errors)) $errorElement++;
            $this->SavePrevPageDetail($this->arFields["LINK"]);
        }
        if (isset($this->errors) && count($this->errors) > 0) $allError = $allError + count($this->errors);
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog" . $this->id . ".txt", "|" . $countPage . "|" . $ciElement . "|" . $errorElement . "|" . $allError);
    }

    protected function SaveCatalogError()
    {
        $file = 0;
        if (file_exists(($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog" . $this->id . ".txt")))
            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog" . $this->id . ".txt");

        if ($file) {
            $arFile = explode("|", $file);

            $countPage = (int)$arFile[1];
            $ciElement = (int)$arFile[2];
            $errorElement = (int)$arFile[3];
            $allError = (int)$arFile[4];
        } else {
            $countPage = 0;
            $ciElement = 0;
            $errorElement = 0;
            $allError = 0;
        }
        if (isset($this->elementID)) {
            if (isset($this->errors) && count($this->errors)) $errorElement++;
        }
        if (isset($this->errors) && count($this->errors) > 0) $allError = $allError + count($this->errors);
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog" . $this->id . ".txt", "|" . $countPage . "|" . $ciElement . "|" . $errorElement . "|" . $allError);
    }

    protected function parseCatalogAllFields()
    {
        if ($this->checkUniq()) return false;
        $this->arFields["IBLOCK_ID"] = $this->iblock_id;
        $this->arFields["ACTIVE"] = $this->active_element;
        if ($this->code_element == "Y") {
            $this->arFields["CODE"] = $this->getCodeElement($this->arFields["NAME"]);
        }

        if ($this->uniqFields["LINK"]) {
            $uniq = md5($this->arFields["NAME"] . $this->arFields["LINK"]);
            $this->arFields["XML_ID"] = $uniq;
        }

        if ($this->date_active == "NOW") $this->arFields["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "SHORT");
        elseif ($this->date_active == "NOW_TIME") $this->arFields["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "FULL");
        //elseif($this->date_active=="PUBLIC" && $unix) $this->arFields["DATE_ACTIVE_FROM"] = ConvertTimeStamp($unix, "FULL");
    }

    protected function AddElementCatalog()
    {
        if ($this->checkUniq() && !$this->isUpdate) return false;
        $el = new CIBlockElement;
        $isElement = $this->checkUniq();
        if (!$isElement) {
            $id = $el->Add($this->arFields, "N", $this->index_element, $this->resize_image);
            if (!$id) {
                $this->errors[] = $this->arFields["NAME"] . "[" . $this->arFields["LINK"] . "] - " . $el->LAST_ERROR;
            } else $this->elementID = $id;
        } else {
            $this->clearFieldsUpdate();

            $this->elementID = $isElement;
            $el->Update($isElement, $this->arFields);
        }
        unset($el);
    }

    protected function AddElementOfferCatalog()
    {
        if ($this->checkUniq() && !$this->isUpdate) return false;
        $el = new CIBlockElement;
        $isElement = $this->checkOfferUniq();
        if (!$isElement) {
            $this->arOfferFields["XML_ID"] = "offer#" . md5($this->arFields["NAME"] . $this->arFields["LINK"]);
            $this->arOfferFields["NAME"] = $this->arFields["NAME"];
            $this->arOfferFields["IBLOCK_ID"] = $this->offerArray["IBLOCK_ID"];
            $this->arOfferFields["PROPERTY_VALUES"][$this->offerArray["SKU_PROPERTY_ID"]] = $this->elementID;
            $id = $el->Add($this->arOfferFields, "N", $this->index_element, $this->resize_image);
            if (!$id) {
                $this->errors[] = GetMessage("parser_offer_name") . $this->arOfferFields["NAME"] . "[" . $this->arFields["LINK"] . "] - " . $el->LAST_ERROR;
            } else $this->elementOfferID = $id;
        } else $this->elementOfferID = $isElement;

        unset($el);
    }

    protected function AddElementOfferCatalogSpecial($size)
    {
        if ($this->checkUniq() && !$this->isUpdate) return false;
        $el = new CIBlockElement;
        $isElement = $this->checkOfferUniqSpecial($size);
        if (!$isElement) {
            $this->arOfferFields["XML_ID"] = "offer#" . md5($this->arFields["NAME"] . $this->arFields["LINK"] . $size);
            $this->arOfferFields["NAME"] = $this->arFields["NAME"] . " (" . $size . ")";
            $this->arOfferFields["IBLOCK_ID"] = $this->offerArray["IBLOCK_ID"];
            $this->arOfferFields["PROPERTY_VALUES"][$this->offerArray["SKU_PROPERTY_ID"]] = $this->elementID;
            $this->arOfferFields["PROPERTY_VALUES"]["SIZES_CLOTHES"] = $this->CheckPropsOfferL(2787, "SIZES_CLOTHES", $size);
            $id = $el->Add($this->arOfferFields, "N", $this->index_element, $this->resize_image);
            if (!$id) {
                $this->errors[] = GetMessage("parser_offer_name") . $this->arOfferFields["NAME"] . "[" . $this->arFields["LINK"] . "] - " . $el->LAST_ERROR;
            } else $this->elementOfferID = $id;
        } else $this->elementOfferID = $isElement;

        unset($el);
    }

    protected function clearFieldsUpdate()
    {

        $this->arEmptyUpdate["PREVIEW_TEXT"] = trim($this->arEmptyUpdate["PREVIEW_TEXT"]);
        $this->arEmptyUpdate["DETAIL_TEXT"] = trim($this->arEmptyUpdate["DETAIL_TEXT"]);
        if ($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["props"])) {
            unset($this->arFields["PROPERTY_VALUES"]);
        }
        if ($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["preview_descr"] || (in_array("PREVIEW_TEXT", $this->arSortUpdate) && !empty($this->arEmptyUpdate["PREVIEW_TEXT"])))) {
            unset($this->arFields["PREVIEW_TEXT"]);
            unset($this->arFields["PREVIEW_TEXT_TYPE"]);
        }
        if ($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["detail_descr"] || (in_array("DETAIL_TEXT", $this->arSortUpdate) && !empty($this->arEmptyUpdate["DETAIL_TEXT"])))) {
            unset($this->arFields["DETAIL_TEXT"]);
            unset($this->arFields["DETAIL_TEXT_TYPE"]);
        }
        if ($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["preview_img"] || (in_array("PREVIEW_PICTURE", $this->arSortUpdate) && !empty($this->arEmptyUpdate["PREVIEW_PICTURE"])))) {
            unset($this->arFields["PREVIEW_PICTURE"]);
        }
        if ($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["detail_img"] || (in_array("DETAIL_PICTURE", $this->arSortUpdate) && !empty($this->arEmptyUpdate["DETAIL_PICTURE"])))) {
            unset($this->arFields["DETAIL_PICTURE"]);
        }
        if ($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["price"])) {
            unset($this->arPrice);
        }
        if ($this->checkUniq()) {
            $code = $this->settings["catalog"]["more_image_props"];
            unset($this->arFields["PROPERTY_VALUES"][$code]);
        }

    }

    protected function AddMeasureCatalog()
    {
        if ($this->elementUpdate) return false;
        $info = CModule::CreateModuleObject('catalog');
        if (!CheckVersion("14.0.0", $info->MODULE_VERSION)) {
            if ($this->settings["catalog"]["koef"] > 0) {
                $arMes = array("RATIO" => $this->settings["catalog"]["koef"], "PRODUCT_ID" => $this->elementID);
                $str_CAT_MEASURE_RATIO = 1;
                $CAT_MEASURE_RATIO_ID = 0;
                $db_CAT_MEASURE_RATIO = CCatalogMeasureRatio::getList(array(), array("PRODUCT_ID" => $this->elementID));
                if ($ar_CAT_MEASURE_RATIO = $db_CAT_MEASURE_RATIO->Fetch()) {
                    $str_CAT_MEASURE_RATIO = $ar_CAT_MEASURE_RATIO["RATIO"];
                    $CAT_MEASURE_RATIO_ID = $ar_CAT_MEASURE_RATIO["ID"];
                }
                if ($CAT_MEASURE_RATIO_ID > 0) {
                    if (!CCatalogMeasureRatioAll::Update($CAT_MEASURE_RATIO_ID, $arMes)) {
                        $this->errors[] = $this->arFields["NAME"] . "[" . $this->arFields["LINK"] . "] " . GetMessage("parser_error_ratio");
                    }
                } else {
                    if (!CCatalogMeasureRatio::add($arMes)) {
                        $this->errors[] = $this->arFields["NAME"] . "[" . $this->arFields["LINK"] . "] " . GetMessage("parser_error_ratio");
                    }
                }

            }
        }
    }

    protected function AddProductCatalog()
    {
        if ($this->elementUpdate && (!$this->isUpdate || !$this->isUpdate["param"])) return false;
        $this->arProduct["MEASURE"] = $this->settings["catalog"]["measure"];
        $this->arProduct["VAT_ID"] = $this->settings["catalog"]["cat_vat_id"];
        $this->arProduct["VAT_INCLUDED"] = $this->settings["catalog"]["cat_vat_included"];
        $this->arProduct["ID"] = $this->elementID;

        $isElement = $this->elementUpdate;
        if (!$isElement) {
            if (!CCatalogProduct::Add($this->arProduct)) {
                $this->errors[] = $this->arFields["NAME"] . "[" . $this->arFields["LINK"] . "] " . GetMessage("parser_error_add_product");
            }
        } else {
            $this->UpdateProductCatalog($isElement);
        }

    }

    protected function UpdateProductCatalog($productID)
    {
        if (!$productID) {
            $this->errors[] = $this->arFields["NAME"] . "[" . $this->arFields["LINK"] . "] " . GetMessage("parser_error_update_product");
            return false;
        }
        CCatalogProduct::Update($productID, $this->arProduct);
    }

    protected function ConvertCurrency()
    {
        if ($this->settings["catalog"]["convert_currency"]) {
            $this->arPrice["CURRENCY"] = $this->settings["catalog"]["convert_currency"];
            $this->arPrice["PRICE"] = CCurrencyRates::ConvertCurrency($this->arPrice["PRICE"], $this->settings["catalog"]["currency"], $this->settings["catalog"]["convert_currency"]);
        }
    }

    protected function ChangePrice()
    {
        if (is_array($this->settings["catalog"]["price_updown"]) && count($this->settings["catalog"]["price_updown"]) > 0) {
            foreach ($this->settings["catalog"]["price_updown"] as $i => $val) {
                if ($this->settings["catalog"]["price_updown"][$i] && $this->settings["catalog"]["price_value"][$i]) {
                    if ($this->settings["catalog"]["price_terms"][$i] == "delta") {
                        if (empty($this->settings["catalog"]["price_terms_value"][$i]) && !empty($this->settings["catalog"]["price_terms_value_to"][$i])) {
                            if ($this->arPrice["PRICE"] > $this->settings["catalog"]["price_terms_value_to"][$i]) continue;
                        }

                        if (!empty($this->settings["catalog"]["price_terms_value"][$i]) && empty($this->settings["catalog"]["price_terms_value_to"][$i])) {
                            if ($this->arPrice["PRICE"] < $this->settings["catalog"]["price_terms_value"][$i]) continue;
                        }

                        if (!empty($this->settings["catalog"]["price_terms_value"][$i]) && !empty($this->settings["catalog"]["price_terms_value_to"][$i])) {
                            if ($this->arPrice["PRICE"] < $this->settings["catalog"]["price_terms_value"][$i] || $this->arPrice["PRICE"] > $this->settings["catalog"]["price_terms_value_to"][$i]) continue;
                        }
                    }
                    if ($this->settings["catalog"]["price_type_value"][$i] == "percent") {
                        $delta = $this->arPrice["PRICE"] * $this->settings["catalog"]["price_value"][$i] / 100;
                    } else {
                        $delta = $this->settings["catalog"]["price_value"][$i];
                    }
                    if ($this->settings["catalog"]["price_updown"][$i] == "up") {
                        $this->arPrice["PRICE"] += $delta;
                    } elseif ($this->settings["catalog"]["price_updown"][$i] == "down") {
                        $this->arPrice["PRICE"] -= $delta;
                    }
                    break;
                }
            }
        } else {
            if ($this->settings["catalog"]["price_updown"] && $this->settings["catalog"]["price_value"]) {
                if ($this->settings["catalog"]["price_terms"] == "up" && $this->settings["catalog"]["price_terms_value"]) {
                    if ($this->arPrice["PRICE"] < $this->settings["catalog"]["price_terms_value"]) return false;
                }
                if ($this->settings["catalog"]["price_terms"] == "down" && $this->settings["catalog"]["price_terms_value"]) {
                    if ($this->arPrice["PRICE"] > $this->settings["catalog"]["price_terms_value"]) return false;
                }

                if ($this->settings["catalog"]["price_type_value"] == "percent") {
                    $delta = $this->arPrice["PRICE"] * $this->settings["catalog"]["price_value"] / 100;
                } else {
                    $delta = $this->settings["catalog"]["price_value"];
                }
                if ($this->settings["catalog"]["price_updown"] == "up") {
                    $this->arPrice["PRICE"] += $delta;
                } elseif ($this->settings["catalog"]["price_updown"] == "down") {
                    $this->arPrice["PRICE"] -= $delta;
                }
            }
        }

    }

    protected function AddPriceCatalog()
    {
        if ($this->elementUpdate && (!$this->isUpdate || !$this->isUpdate["price"])) return false;

        if (!$this->arPrice || !$this->arPrice["PRICE"]) return false;
        $isElement = $this->elementUpdate;
        $this->arPrice["PRODUCT_ID"] = $this->elementID;
        $this->ChangePrice();
        $this->ConvertCurrency();
        $obPrice = new CPrice();
        if (!$isElement) {
            $price = $obPrice->Add($this->arPrice);
            if (!$price) {
                $this->errors[] = $this->arFields["NAME"] . "[" . $this->arFields["LINK"] . "] " . GetMessage("parser_error_add_price") . $obPrice->LAST_ERROR;
            }
        } else $this->UpdatePriceCatalog($isElement);

        unset($obPrice);
    }

    protected function UpdatePriceCatalog($elementID)
    {
        if (!$elementID) {
            $this->errors[] = $this->arFields["NAME"] . "[" . $this->arFields["LINK"] . "] " . GetMessage("parser_error_update_price");
            return false;
        }
        $res = CPrice::GetList(
            array(),
            array(
                "PRODUCT_ID" => $elementID,
                "CATALOG_GROUP_ID" => $this->arPrice["CATALOG_GROUP_ID"]
            )
        );

        if ($arr = $res->Fetch()) {
            CPrice::Update($arr["ID"], $this->arPrice);
        }
    }

    protected function parserCatalogPreview(&$el)
    {
        if (!$this->parseCatalogUrlPreview($el)) return false;
        $this->parseCatalogNamePreview($el);
        if ($this->isCatalog) $this->parseCatalogPricePreview($el);
        $this->parseCatalogDescriptionPreview($el);
        $this->parseCatalogPreviewPicturePreview($el);
        return true;
    }

    protected function parserCatalogDetail()
    {
        if ($this->checkUniq() && !$this->isUpdate) return false;
        $el = $this->parserCatalogDetailPage();
        $this->parseCatalogNameDetail($el);
        $this->parseCatalogDetailMorePhoto($el);
        $this->parseCatalogProperties($el);

        if ($this->isCatalog) $this->parseCatalogPriceDetail($el);
        $this->parseCatalogDescriptionDetail($el);
        $this->parseCatalogDetailPicture($el);


    }

    protected function parseCatalogProperties(&$el)
    {
        if ($this->checkUniq() && !$this->isUpdate) return false;
        $this->parseCatalogDefaultProperties($el);
        $this->parseCatalogSelectorProperties($el);
        $this->parseCatalogFindProperties($el);
        $this->AllDoProps();
        if ($this->isCatalog) $this->parseCatalogFindProduct($el);
        if ($this->isCatalog) $this->parseCatalogSelectorProduct($el);
    }

    protected function AllDoProps()
    {
        if ($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["props"])) return false;
        $isElement = $this->checkUniq();
        if ($isElement) {
            $obElement = new CIBlockElement;
            $rsProperties = $obElement->GetProperty($this->iblock_id, $isElement, "sort", "asc");
            while ($arProperty = $rsProperties->Fetch()) {

                if (isset($this->arFields["PROPERTY_VALUES"][$arProperty["CODE"]]) || $arProperty["PROPERTY_TYPE"] == "F") continue;
                $this->arFields["PROPERTY_VALUES"][$arProperty["ID"]][$arProperty['PROPERTY_VALUE_ID']] = array(
                    "VALUE" => $arProperty['VALUE'],
                    "DESCRIPTION" => $arProperty["DESCRIPTION"]
                );
            }

        }
    }

    protected function clearFields()
    {
        unset($this->arFields);
        unset($this->arProduct);
        unset($this->arPrice);
        unset($this->elementUpdate);
        if (isset($this->elementOfferUpdate)) unset($this->elementOfferUpdate);
        unset($this->elementID);
        unset($this->detailHtml);
        unset($this->arEmptyUpdate);
        $this->SaveLog();
        //if($this->settings["catalog"]["mode"]!="debug")

        unset($this->errors);
    }

    protected function clearHtml()
    {
        unset($this->html);
    }

    protected function getCodeElement($name)
    {
        $arFieldCode = $this->arrayIblock["FIELDS"]["CODE"]["DEFAULT_VALUE"];
        $CODE = CUtil::translit($name, "ru", array(
            "max_len" => $arFieldCode["TRANS_LEN"],
            "change_case" => $arFieldCode["TRANS_CASE"],
            "replace_space" => $arFieldCode["TRANS_SPACE"],
            "replace_other" => $arFieldCode["TRANS_OTHER"],
            "delete_repeat_replace" => $arFieldCode["TRANS_EAT"] == "Y" ? true : false,
        ));

        $IBLOCK_ID = $this->arrayIblock['ID'];

        $arCodes = array();
        $rsCodeLike = CIBlockElement::GetList(array(), array(
            "IBLOCK_ID" => $IBLOCK_ID,
            "CODE" => $CODE . "%",
        ), false, false, array("ID", "CODE"));
        while ($ar = $rsCodeLike->Fetch())
            $arCodes[$ar["CODE"]] = $ar["ID"];

        if (array_key_exists($CODE, $arCodes)) {
            $i = 1;
            while (array_key_exists($CODE . "_" . $i, $arCodes))
                $i++;

            return $CODE . "_" . $i;
        } else {
            return $CODE;
        }
    }

    protected function getArrayIblock()
    {
        $arIBlock = CIBlock::GetArrayByID($this->iblock_id);
        $this->arrayIblock = $arIBlock;
    }

    protected function parseCatalogSection()
    {
        if ($this->checkUniq()) return false;
        $this->arFields["IBLOCK_SECTION_ID"] = $this->section_id;
    }

    protected function parseCatalogFindProduct(&$el)
    {
        $arProperties = $this->arFindProduct;
        if (!$arProperties) return false;
        if ($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["param"])) return false;
        $find = $this->settings["catalog"]["selector_find_size"];
        if ($this->settings["catalog"]["catalog_delete_find_symb"]) {
            $deleteSymb = explode(",", $this->settings["catalog"]["catalog_delete_find_symb"]);

            foreach ($deleteSymb as $i => &$symb) {
                $symb = trim($symb);
                if (empty($symb)) {
                    unset($deleteSymb[$i]);
                    continue;
                }
                if ($symb == "\\\\") {
                    $deleteSymb[$i] = ",";
                }

            }
        }

        foreach (pq($el)->find($find) as $prop) {
            $text = pq($prop)->html();
            $text = strip_tags($text);
            $text = str_replace($deleteSymb, "", $text);
            foreach ($arProperties as $code => $val) {
                //if(preg_match("/".$val."/", $text))
                if (strpos($text, $val) !== false) {
                    $text = str_replace($val, "", $text);
                    $text = trim($text);
                    $this->arProduct[$code] = $text;
                }
            }

        }
    }

    protected function parseCatalogSelectorProduct(&$el)
    {
        $arProperties = $this->arSelectorProduct;
        if (!$arProperties) return false;
        if ($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["param"])) return false;
        if ($this->settings["catalog"]["catalog_delete_selector_symb"]) {
            $deleteSymb = explode(",", $this->settings["catalog"]["catalog_delete_selector_symb"]);

            foreach ($deleteSymb as $i => &$symb) {
                $symb = trim($symb);
                if (empty($symb)) {
                    unset($deleteSymb[$i]);
                    continue;
                }
                if ($symb == "\\\\") {
                    $deleteSymb[$i] = ",";
                }
            }
        }

        foreach ($arProperties as $code => $val) {
            $text = pq($el)->find($this->settings["catalog"]["selector_product"][$code])->html();
            $text = strip_tags($text);
            $text = str_replace($deleteSymb, "", $text);
            $text = trim($text);
            $this->arProduct[$code] = $text;
        }
    }

    protected function parseCatalogSelectorProperties(&$el)
    {
        $arProperties = $this->arSelectorProperties;
        if (!$arProperties) return false;
        if ($this->settings["catalog"]["catalog_delete_selector_props_symb"]) {
            $deleteSymb = explode(",", $this->settings["catalog"]["catalog_delete_selector_props_symb"]);

            foreach ($deleteSymb as $i => &$symb) {
                $symb = trim($symb);
                if (empty($symb)) {
                    unset($deleteSymb[$i]);
                    continue;
                }
                if ($symb == "\\\\") {
                    $deleteSymb[$i] = ",";
                }

            }
        }

        foreach ($arProperties as $code => $val) {
            $text = pq($el)->find($this->settings["catalog"]["selector_prop"][$code])->html();
            $text = strip_tags($text);
            $text = str_replace($deleteSymb, "", $text);
            $this->parseCatalogProp($code, $val, $text);
        }


    }

    protected function parseCatalogFindProperties(&$el)
    {
        $arProperties = $this->arFindProperties;
        if (!$arProperties) return false;
        $find = $this->settings["catalog"]["selector_find_props"];
        if ($this->settings["catalog"]["catalog_delete_selector_find_props_symb"]) {
            $deleteSymb = explode(",", $this->settings["catalog"]["catalog_delete_selector_find_props_symb"]);

            foreach ($deleteSymb as $i => &$symb) {
                $symb = trim($symb);
                if (empty($symb)) {
                    unset($deleteSymb[$i]);
                    continue;
                }
                if ($symb == "\\\\") {
                    $deleteSymb[$i] = ",";
                }

            }
        }
        $arFind = explode(",", $find);

        foreach ($arFind as $vFind) {
            if (strpos($vFind, " br") !== false || strpos($vFind, "<br/>") || strpos($vFind, "<br />")) {
                $vFind = str_replace(array(" br", "<br/>", "<br />"), "", $vFind);
                $vFind = trim($vFind);
                $arBr = array("<br>", "<br/>", "<br />");

                foreach (pq($el)->find($vFind) as $prop) {
                    $text = pq($prop)->html();
                    $text = str_replace($arBr, "<br>", $text);
                    unset($arBr[1]);
                    unset($arBr[2]);
                    foreach ($arBr as $br) {
                        $arTextBr = explode($br, $text);
                        if (!empty($arTextBr) && count($arTextBr) > 1) {
                            foreach ($arTextBr as $textBr) {
                                $textBr = strip_tags($textBr);
                                $textBr = str_replace($deleteSymb, "", $textBr);
                                foreach ($arProperties as $code => $val) {
                                    //if(preg_match("/".$val."/", $textBr))
                                    if ($this->CheckFindProps($code, $val, $textBr)) {
                                        $this->parseCatalogProp($code, $val, $textBr);
                                    }
                                }

                            }
                        }
                    }

                }
            } else {
                foreach (pq($el)->find($vFind) as $prop) {
                    $text = pq($prop)->html();
                    $text = strip_tags($text);
                    $text = str_replace($deleteSymb, "", $text);
                    foreach ($arProperties as $code => $val) {
                        //if(preg_match("/".$val."/", $text))

                        if ($this->CheckFindProps($code, $val, $text)) {
                            $this->parseCatalogProp($code, $val, $text);
                        }
                    }

                }
            }
        }


    }

    protected function parseCatalogDefaultProperties(&$el)
    {
        if (isset($this->settings["catalog"]["default_prop"]) && !empty($this->settings["catalog"]["default_prop"])) {
            foreach ($this->settings["catalog"]["default_prop"] as $code => $val) {
                if ($val) $this->parseCatalogDefaultProp($code, $val);
            }
        }
    }

    protected function CheckFindProps($code, $val, $text)
    {
        $arDubleProperties = $this->arDubleFindProperties;
        $bool = false;
        if (isset($arDubleProperties[$code])) {
            foreach ($arDubleProperties[$code] as $prop) {
                $v = $this->arFindProperties[$prop];
                //if(preg_match("/".$v."/", $text))
                if (strpos($text, $v) !== false) {
                    $bool = true;
                }
            }
            if ($bool) return false;
        }
        //if(preg_match("/".$val."/", $text)) return true;
        if (strpos($text, $val) !== false) return true;
        else return false;
    }

    protected function parseCatalogMeta()
    {
        if ($this->checkUniq()) return false;
        if ($this->meta_description != "N" || $this->meta_keywords != "N") {
            foreach ($this->detailHtml["meta"] as $meta) {
                if ($this->meta_description != "N" && strtolower(pq($meta)->attr("name")) == "description") {
                    $meta_text = pq($meta)->attr("content");
                    if (!$meta_text) $meta_text = pq($meta)->attr("value");
                    if (strtoupper(LANG_CHARSET) == "WINDOWS-1251"/* && strtoupper(LANG_CHARSET)!=strtoupper($sota_DOC_ENCODING)*/) {
                        $meta_text = mb_convert_encoding($meta_text, LANG_CHARSET, "utf-8");
                    }
                    $this->arFields["PROPERTY_VALUES"][$this->meta_description] = strip_tags($meta_text);
                } elseif ($this->meta_keywords != "N" && strtolower(pq($meta)->attr("name")) == "keywords") {
                    $meta_text = pq($meta)->attr("content");
                    if (!$meta_text) $meta_text = pq($meta)->attr("value");
                    if (strtoupper(LANG_CHARSET) == "WINDOWS-1251"/* && strtoupper(LANG_CHARSET)!=strtoupper($sota_DOC_ENCODING)*/) {
                        $meta_text = mb_convert_encoding($meta_text, LANG_CHARSET, "utf-8");
                    }
                    $this->arFields["PROPERTY_VALUES"][$this->meta_keywords] = strip_tags($meta_text);
                }
                unset($meta_text);
            }
        }

        if ($this->meta_title != "N") {
            $meta_title = pq($this->detailHtml["head:eq(0) title:eq(0)"])->text();
            $meta_title = strip_tags($meta_title);
            if (strtoupper(LANG_CHARSET) == "WINDOWS-1251"/* && strtoupper(LANG_CHARSET)!=strtoupper($sota_DOC_ENCODING)*/) {
                $meta_title = mb_convert_encoding($meta_title, LANG_CHARSET, "utf-8");
            }
            $this->arFields["PROPERTY_VALUES"][$this->meta_title] = $meta_title;
        }

    }

    protected function parseCatalogFirstUrl()
    {
        if ($this->checkUniq()) return false;
        if ($this->first_title != "N") {
            $this->arFields["PROPERTY_VALUES"][$this->first_title] = $this->arFields["LINK"];
        }
    }

    protected function parseCatalogDate()
    {

    }

    protected function parseCatalogProp($code, $val, $text)
    {
        $text = str_replace($val, "", $text);
        $val = trim($text);
        $arProp = $this->arProperties[$code];
        //$default = $this->settings["catalog"]["default_prop"][$code];

        if ($arProp["PROPERTY_TYPE"] == "S") {
            $this->arFields["PROPERTY_VALUES"][$code] = $val;
        } elseif ($arProp["PROPERTY_TYPE"] == "N") {
            $val = str_replace(",", ".", $val);
            $val = preg_replace("/\.{1}$/", "", $val);
            $val = preg_replace('/[^0-9.]/', "", $val);
            $this->arFields["PROPERTY_VALUES"][$code] = $val;

        } elseif ($arProp["PROPERTY_TYPE"] == "L" && $arProp["MULTIPLE"] != "Y") {
            $this->arFields["PROPERTY_VALUES"][$code] = $this->CheckPropsL($arProp["ID"], $code, $val);
            if ($code == "SIZE") {
                $strSize = $val;
                $this->strArSize = explode(" ", $val);
            }


        } elseif ($arProp["PROPERTY_TYPE"] == "L" && $arProp["MULTIPLE"] == "Y") {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $this->CheckPropsL($arProp["ID"], $code, $val);
        } elseif ($arProp["PROPERTY_TYPE"] == "E" && $arProp["MULTIPLE"] != "Y") {
            $this->arFields["PROPERTY_VALUES"][$code] = $this->CheckPropsE($arProp, $code, $val);
        } elseif ($arProp["PROPERTY_TYPE"] == "E" && $arProp["MULTIPLE"] == "Y") {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $this->CheckPropsE($arProp, $code, $val);
        }
    }

    protected function parseCatalogDefaultProp($code, $val)
    {
        $val = trim($val);
        $arProp = $this->arProperties[$code];

        if ($arProp["PROPERTY_TYPE"] == "S") {
            $this->arFields["PROPERTY_VALUES"][$code] = $val;
        } elseif ($arProp["PROPERTY_TYPE"] == "N") {
            $val = str_replace(",", ".", $val);
            $val = preg_replace("/\.{1}$/", "", $val);
            $val = preg_replace('/[^0-9.]/', "", $val);
            $this->arFields["PROPERTY_VALUES"][$code] = $val;

        } elseif ($arProp["PROPERTY_TYPE"] == "L" && $arProp["MULTIPLE"] != "Y") {
            $this->arFields["PROPERTY_VALUES"][$code] = $val;
        } elseif ($arProp["PROPERTY_TYPE"] == "L" && $arProp["MULTIPLE"] == "Y") {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $val;
        } elseif ($arProp["PROPERTY_TYPE"] == "E" && $arProp["MULTIPLE"] != "Y") {
            $this->arFields["PROPERTY_VALUES"][$code] = $this->CheckPropsE($arProp, $code, $val);
        } elseif ($arProp["PROPERTY_TYPE"] == "E" && $arProp["MULTIPLE"] == "Y") {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $this->CheckPropsE($arProp, $code, $val);
        }
    }

    protected function CheckPropsE($arProp, $code, $val)
    {
        $IBLOCK_ID = $arProp["LINK_IBLOCK_ID"];

        $rsProp = CIBlockElement::GetList(Array(), array("IBLOCK_ID" => $IBLOCK_ID, "%NAME" => $val), false, false, array("ID", "NAME"));
        while ($arIsProp = $rsProp->Fetch()) {
            $arIsProp["NAME"] = mb_strtolower($arIsProp["NAME"], LANG_CHARSET);
            $val0 = mb_strtolower($val, LANG_CHARSET);
            if ($val0 == $arIsProp["NAME"]) {
                $isProp = $arIsProp["ID"];
            }
        }

        if ($isProp) return $isProp;
        else {
            $arFields = array(
                "NAME" => $val,
                "ACTIVE" => "Y",
                "IBLOCK_ID" => $IBLOCK_ID
            );
            $el = new CIBlockElement;
            $id = $el->Add($arFields);
            unset($el);
            return $id;
        }
    }

    protected function CheckPropsL($id, $code, $val)
    {
        $res2 = CIBlockProperty::GetPropertyEnum(
            $id,
            array(),
            array("IBLOCK_ID" => $this->iblock_id, "VALUE" => $val)
        );

        if ($arRes2 = $res2->Fetch()) {
            $kz = $arRes2["ID"];
        } else {
            $tmpid = md5(uniqid(""));
            $kz = CIBlockPropertyEnum::Add(
                array(
                    "PROPERTY_ID" => $id,
                    "VALUE" => $val,
                    "TMP_ID" => $tmpid
                )
            );

        }

        return $kz;
    }

    protected function CheckPropsOfferL($id, $code, $val)
    {
        $res2 = CIBlockProperty::GetPropertyEnum(
            $id,
            array(),
            array("IBLOCK_ID" => $this->offerArray["IBLOCK_ID"], "VALUE" => $val)
        );

        if ($arRes2 = $res2->Fetch()) {
            $kz = $arRes2["ID"];
        } else {
            $tmpid = md5(uniqid(""));
            $kz = CIBlockPropertyEnum::Add(
                array(
                    "PROPERTY_ID" => $id,
                    "VALUE" => $val,
                    "TMP_ID" => $tmpid
                )
            );

        }

        return $kz;
    }

    protected function getFindProduct()
    {
        foreach ($this->settings["catalog"]["find_product"] as $i => $prop) {
            $prop = trim($prop);
            if (!empty($prop)) {
                $arProps[$i] = $prop;
            }
        }
        if (!$arProps) return false;
        return $arProps;
    }

    protected function getSelectorProduct()
    {
        foreach ($this->settings["catalog"]["selector_product"] as $i => $prop) {
            $prop = trim($prop);
            if (!empty($prop)) {
                $arProps[$i] = $prop;
            }
        }
        if (!$arProps) return false;
        return $arProps;
    }

    protected function getFindDubleProperties()
    {
        $arFindProps = $this->arFindProperties;
        //printr($arFindProps);
        foreach ($arFindProps as $code => $prop) {
            foreach ($arFindProps as $code1 => $prop1) {
                if (strpos($prop1, $prop) !== false && $code1 != $code) {
                    $arDubleProps[$code][] = $code1;
                }
            }
        }
        if (isset($arDubleProps)) return $arDubleProps;
        else return false;
    }

    protected function getFindProperties()
    {
        foreach ($this->settings["catalog"]["find_prop"] as $i => $prop) {
            $prop = trim($prop);
            if (!empty($prop)) {
                $arProps[$i] = $prop;
            }
        }
        if (!isset($arProps)) return false;
        return $arProps;
    }

    protected function getSelectorProperties()
    {
        foreach ($this->settings["catalog"]["selector_prop"] as $i => $prop) {
            $prop = trim($prop);
            if (!empty($prop)) {
                $arProps[$i] = $prop;
            }
        }
        if (!$arProps) return false;
        return $arProps;
    }

    protected function parseCatalogDetailMorePhoto(&$el)
    {
        if ($this->settings["catalog"]["more_image_props"]) {
            if ($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["more_img"])) return false;
            $code = $this->settings["catalog"]["more_image_props"];
            $ar = $this->GetArraySrcAttr($this->settings["catalog"]["more_image"]);
            $image = $ar["path"];
            $attr = $ar["attr"];
            $n = 0;

            $isElement = $this->checkUniq();

            foreach (pq($el)->find($image) as $img) {
                $src = pq($img)->attr($attr);
                $src = $this->parseCaralogFilterSrc($src);
                $src = $this->getCatalogLink($src);
                $this->arFields["PROPERTY_VALUES"][$code]["n" . $n]["VALUE"] = CFile::MakeFileArray($src);
                $this->arFields["PROPERTY_VALUES"][$code]["n" . $n]["DESCRIPTION"] = "";
                $n++;
            }
            if ($isElement) {
                $arImages = $this->arFields["PROPERTY_VALUES"][$code];
                //unset($this->arFields["PROPERTY_VALUES"][$code]);
                $obElement = new CIBlockElement;
                $rsProperties = $obElement->GetProperty($this->iblock_id, $isElement, "sort", "asc", Array("CODE" => $code));
                while ($arProperty = $rsProperties->Fetch()) {
                    $arImages[$arProperty["PROPERTY_VALUE_ID"]] = array(
                        "tmp_name" => "",
                        "del" => "Y",
                    );
                }
                CIBlockElement::SetPropertyValueCode($isElement, $code, $arImages);
                unset($obElement);
            }

        }


    }

    protected function parserCatalogDetailPage()
    {
        $this->catalogSleep();
        $this->detailFileHtml = new FileGetHtml();
        $this->detailPage = $this->fileHtml->file_get_html($this->arFields["LINK"], $this->settings["catalog"]["proxy"], $this->auth);
        $this->DeleteCharsetHtml5($this->detailPage);
        $this->detailHttpCode = $this->fileHtml->httpCode;

        if ($this->detailHttpCode != 200 && $this->detailHttpCode != 301 && $this->detailHttpCode != 302 && $this->detailHttpCode != 303) {
            $this->errors[] = $this->arFields["NAME"] . "[" . $this->arFields["LINK"] . "]" . GetMessage("parser_error_connect") . "[" . $this->detailHttpCode . "]";
            //return false;
        }
        $this->detailHtml = phpQuery::newDocument($this->detailPage, "text/html;charset=" . LANG_CHARSET);
        //if($this->detail_delete_element)$this->deleteCatalogElement($this->detail_delete_element, $this->detail_dom, $this->detailHtml[$this->detail_dom]);
        //if($this->detail_delete_attribute)$this->deleteCatalogAttribute($this->detail_delete_attribute, $this->detail_dom, $this->detailHtml[$this->detail_dom]);

        foreach ($this->detailHtml[$this->detail_dom] as $detail) {
            return $detail;
        }
        $this->errors[] = GetMessage("parser_error_selecto_detail_notfound");


    }

    protected function parseCatalogUrlPreview($el)
    {
        $url = $this->settings["catalog"]["href"] ? $this->settings["catalog"]["href"] : "a:eq(0)";
        $this->settings["catalog"]["href"] = $url;
        $p = pq($el)->find($url)->attr("href");
        if (!$p) {
            $this->errors[] = GetMessage("parser_error_href_notfound");
            return false;
        }
        $p = $this->getCatalogLink($p);
        $this->arFields["LINK"] = $p;
        if (isset($this->pagePrevElement[$p])) return false;
        return true;
    }

    protected function parseCatalogNamePreview($el)
    {
        if (isset($this->settings["catalog"]["detail_name"]) && $this->settings["catalog"]["detail_name"]) return false;
        $name = $this->settings["catalog"]["name"] ? $this->settings["catalog"]["name"] : $this->settings["catalog"]["href"];

        $this->arFields["NAME"] = trim(strip_tags(pq($el)->find($name)->html()));
        if (!$this->arFields["NAME"]) {
            $this->errors[] = GetMessage("parser_error_name_notfound");
            return false;
        }
    }

    protected function parseCatalogNameDetail($el)
    {
        if (!isset($this->settings["catalog"]["detail_name"]) || !$this->settings["catalog"]["detail_name"]) return false;
        $name = $this->settings["catalog"]["detail_name"];

        $this->arFields["NAME"] = trim(strip_tags(pq($el)->find($name)->html()));
        if (!$this->arFields["NAME"]) {
            $this->errors[] = GetMessage("parser_error_name_notfound");
            return false;
        }
    }

    protected function parseCatalogPricePreview(&$el)
    {
        if ($this->settings["catalog"]["preview_price"]) {
            if ($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["price"])) return false;
            $price = $this->settings["catalog"]["preview_price"];
            $price = strip_tags(pq($el)->find($price)->html());
            $price = trim($price);
            $price = str_replace(",", ".", $price);
            $price = preg_replace("/\.{1}$/", "", $price);
            $price = preg_replace('/[^0-9.]/', "", $price);
            $this->arPrice["PRICE"] = $price;
            $this->arPrice["PRICE"] = trim($this->arPrice["PRICE"]);
            if (!$this->arPrice["PRICE"]) {
                $this->errors[] = $this->arFields["NAME"] . "[" . $this->arFields["LINK"] . "]" . GetMessage("parser_error_price_notfound");
                unset($this->arPrice["PRICE"]);
                return false;
            }
            $this->arPrice["CATALOG_GROUP_ID"] = $this->settings["catalog"]["price_type"];
            $this->arPrice["CURRENCY"] = $this->settings["catalog"]["currency"];
        }

    }

    protected function parseCatalogPriceDetail(&$el)
    {

        if ($this->settings["catalog"]["detail_price"]) {
            if ($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["price"])) return false;
            $price = $this->settings["catalog"]["detail_price"];
            $price = strip_tags(pq($el)->find($price)->html());
            $price = trim($price);
            $price = str_replace(",", ".", $price);
            $price = preg_replace("/\.{1}$/", "", $price);
            $price = preg_replace('/[^0-9.]/', "", $price);
            $this->arPrice["PRICE"] = $price;
            $this->arPrice["PRICE"] = trim($this->arPrice["PRICE"]);
            if (!$this->arPrice["PRICE"]) {
                $this->errors[] = $this->arFields["NAME"] . "[" . $this->arFields["LINK"] . "]" . GetMessage("parser_error_price_notfound");
                unset($this->arPrice["PRICE"]);
                return false;
            }
            $this->arPrice["CATALOG_GROUP_ID"] = $this->settings["catalog"]["price_type"];
            $this->arPrice["CURRENCY"] = $this->settings["catalog"]["currency"];
        }

    }

    protected function parseCatalogDescriptionPreview(&$el)
    {
        if ($this->checkUniq() && (!$this->isUpdate || $this->isUpdate["preview_descr"] == "N")) return false;
        if ($this->settings["catalog"]["preview_text_selector"] && $this->settings["catalog"]["text_preview_from_detail"] != "Y") {
            $preview = $this->settings["catalog"]["preview_text_selector"];
            foreach (pq($el)->find($preview . " img") as $img) {
                $src = pq($img)->attr("src");
                $src = $this->parseCaralogFilterSrc($src);
                $src = $this->getCatalogLink($src);
                $this->parseCatalogSaveImgServer($img, $src);
            }

            if ($this->bool_preview_delete_tag == "Y") $preview_text = strip_tags(pq($el)->find($preview)->html(), $this->preview_delete_tag);
            else $preview_text = pq($el)->find($preview)->html();
            $this->arFields["PREVIEW_TEXT"] = trim($preview_text);
            $this->arFields["PREVIEW_TEXT_TYPE"] = $this->preview_text_type;
        }
    }

    protected function parseCatalogDescriptionDetail(&$el)
    {
        if ($this->detail_delete_element) $this->deleteCatalogElement($this->detail_delete_element, $this->detail_dom, $this->detailHtml[$this->detail_dom]);
        if ($this->detail_delete_attribute) $this->deleteCatalogAttribute($this->detail_delete_attribute, $this->detail_dom, $this->detailHtml[$this->detail_dom]);
        if ($this->checkUniq() && (!$this->isUpdate || (!$this->isUpdate["detail_descr"] && (!$this->isUpdate["preview_descr"] && !$this->settings["catalog"]["text_preview_from_detail"] != "Y")))) return false;
        if ($this->settings["catalog"]["detail_text_selector"]) {
            $detail = $this->settings["catalog"]["detail_text_selector"];
            foreach (pq($el)->find($detail . " img") as $img) {
                $src = pq($img)->attr("src");
                $src = $this->parseCaralogFilterSrc($src);
                $src = $this->getCatalogLink($src);
                $this->parseCatalogSaveImgServer($img, $src);
            }
            if ($this->bool_detail_delete_tag == "Y") $detail_text = strip_tags(pq($el)->find($detail)->html(), $this->detail_delete_tag);
            else $detail_text = pq($el)->find($detail)->html();
            $this->arFields["DETAIL_TEXT"] = trim($detail_text);
            $this->arFields["DETAIL_TEXT_TYPE"] = $this->detail_text_type;
            if ($this->settings["catalog"]["text_preview_from_detail"] == "Y") {
                $this->arFields["PREVIEW_TEXT"] = $this->arFields["DETAIL_TEXT"];
                $this->arFields["PREVIEW_TEXT_TYPE"] = $this->arFields["DETAIL_TEXT_TYPE"];
            }
        }
    }

    protected function parseCatalogDetailPicture(&$el)
    {
        if ($this->checkUniq() && (!$this->isUpdate || (!$this->isUpdate["detail_img"] && (!$this->isUpdate["preview_img"] && !$this->settings["catalog"]["img_preview_from_detail"] != "Y")))) return false;
        if ($this->settings["catalog"]["detail_picture"]) {
            $arSelPic = explode(",", $this->settings["catalog"]["detail_picture"]);

            foreach ($arSelPic as $sel) {
                $sel = trim($sel);
                if (empty($sel)) continue;
                $ar = $this->GetArraySrcAttr($sel);
                $img = $ar["path"];
                $attr = $ar["attr"];
                $src = pq($el)->find($img)->attr($attr);
                $src = $this->parseCaralogFilterSrc($src);
                $src = $this->getCatalogLink($src);
                if (!self::CheckImage($src)) continue;
                //$src = str_replace("cdn.", "", $src);
                $this->arFields["DETAIL_PICTURE"] = CFile::MakeFileArray($src);
                if ($this->settings["catalog"]["img_preview_from_detail"] == "Y") {
                    $this->arFields["PREVIEW_PICTURE"] = $this->arFields["DETAIL_PICTURE"];
                }
            }

        }
    }

    protected function CheckImage($src)
    {
        if (!empty($src) && preg_match("/(jpeg|jpg|gif|png|JPEG|JPG|GIF|PNG)$/", $src)) {
            return true;
        } else return false;
    }

    protected function parseCatalogPreviewPicturePreview(&$el)
    {
        if ($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["preview_img"])) return false;
        if ($this->settings["catalog"]["preview_picture"] && $this->settings["catalog"]["img_preview_from_detail"] != "Y") {
            $ar = $this->GetArraySrcAttr($this->settings["catalog"]["preview_picture"]);
            $img = $ar["path"];
            $attr = $ar["attr"];
            $src = pq($el)->find($img)->attr($attr);
            $src = $this->parseCaralogFilterSrc($src);
            $src = $this->getCatalogLink($src);
            //$src = str_replace("cdn.", "", $src);
            if (!self::CheckImage($src)) return;
            $this->arFields["PREVIEW_PICTURE"] = CFile::MakeFileArray($src);
        }
    }

    protected function parseCatalogSaveImgServer($img, $src)
    {
        $arImg = CFile::MakeFileArray($src);
        $fid = CFile::SaveFile($arImg, "sota.parser");
        pq($img)->attr('src', CFile::GetPath($fid));
    }

    public function parseCaralogFilterSrc($src)
    {
        $src = preg_replace('/#.+/', '', $src);
        $src = preg_replace('/\?.+/', '', $src);
        //$src = str_replace('http:/', 'http://', $src);
        $src = str_replace('//', '/', $src);
        $src = str_replace('http:/', 'http://', $src);
        if (preg_match("/www\./", $src) || preg_match("/http:\//", $src)) $src = preg_replace("/^\/{2}/", "http://", $src);
        if (preg_match("/www\./", $src) || preg_match("/http:\//", $src)) $src = preg_replace("/^\/{1}/", "http://", $src);
        //$src = str_replace('//', '/', $src);
        return $src;
    }

    protected function GetArraySrcAttr($path)
    {
        $ar["path"] = $image = preg_replace('#\[[^\[]+$#', '', $path);
        preg_match('#\[[^\[]+$#', $path, $matches);
        $ar["attr"] = $attr = str_replace(array("[", "]"), "", $matches[0]);
        return $ar;
    }

    protected function parseCatalogPages()
    {
        global $zis;
        foreach ($this->pagenavigation as $id => $page) {
            $this->clearHtml();

            try {
                if (isset($this->pagenavigationPrev[$page]) || isset($this->pagenavigationPrev[$id]) || empty($page)) continue;
            } catch (Exception $e) {
                continue;
            }


            $zis++;

            if ($this->currentPage >= self::DEFAULT_DEBUG_LIST && $this->settings["catalog"]["mode"] == "debug") return;
            $this->connectCatalogPage($page);
            $this->parseCatalogNavigation($page);
            if ($this->IsNumberPageNavigation() && $this->CheckPageNavigation($id)) {
                $this->parseCatalogProducts();
            } elseif (!$this->IsNumberPageNavigation()) {
                $this->parseCatalogProducts();
            }

            $i++;

        }
        foreach ($this->pagenavigationPrev as $i => $v) {
            foreach ($this->pagenavigation as $i1 => $v1) {
                if ($v1 == $v) unset($this->pagenavigation[$i1]);
            }
        }

        if (count($this->pagenavigation) > 0) {
            $this->parseCatalogPages();
        }

    }

    protected function SaveCopyPage()
    {
        if (!$this->agent && $this->settings["catalog"]["mode"] != "debug") {
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_copy_page" . $this->id . ".txt", $this->page);
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_copy_url" . $this->id . ".txt", $this->fileHtml->headerUrl);
        }
    }

    protected function DeleteCopyPage()
    {
        if (!$this->agent && $this->settings["catalog"]["mode"] != "debug" && file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_copy_page" . $this->id . ".txt")) {
            unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_copy_page" . $this->id . ".txt");
            unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_copy_url" . $this->id . ".txt");
        }
    }

    protected function GetCopyPage()
    {
        if (!$this->agent && $this->settings["catalog"]["mode"] != "debug" && file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_copy_page" . $this->id . ".txt")) {
            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_copy_page" . $this->id . ".txt");
            $this->httpCode = 200;
            return $file;
        }
        $this->httpCode = 0;
        return false;
    }

    protected function GetCopyUrl()
    {
        if (!$this->agent && $this->settings["catalog"]["mode"] != "debug" && file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_copy_url" . $this->id . ".txt")) {
            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_copy_url" . $this->id . ".txt");
            return $file;
        }
        $this->httpCode = 0;
        return false;
    }

    /*private function connectCatalogPage0($page)
    {
        global $zis;
        //file_put_contents(dirname(__FILE__)."/catalog_page.txt", print_r(array($page), true), FILE_APPEND);
        $this->catalogSleep();
        $this->sectionPage = $page;
        $this->fileHtml = new FileGetHtml();
        $this->page = $this->GetCopyPage();
        if(!$this->page)
            $this->page = $this->fileHtml->file_get_html($page, $this->settings["catalog"]["proxy"]);
        $this->DeleteCharsetHtml5($this->page);
        $this->SaveCopyPage();
        
        $this->httpCode = $this->fileHtml->httpCode;

        if($this->httpCode!=200 && $this->httpCode!=301 && $this->httpCode!=302 && $this->httpCode!=303)
        {
            if($this->settings["catalog"]["404"]!="Y" && $this->httpCode==404)
            {
                $this->errors[] = "[".$page."]".GetMessage("parser_error_connect")."[".$this->httpCode."]";
                //if($this->agent || $this->settings["catalog"]["mode"]=="debug")
                {
                    $this->SaveLog();
                    unset($this->errors);
                        
                }
                
                return false;
            }

        }
        
        $this->currentPage++;
        if(!$this->agent && $this->settings["catalog"]["mode"]!="debug")$this->SavePrevPage($page);
        //if($page==$this->rss)
        {
            $this->urlCatalog = $this->fileHtml->headerUrl;
            $this->urlSite = $this->getCatalogUrlSite();
        }
        
        return true;
          

    }*/

    protected function connectCatalogPage($page)
    {
        $this->catalogSleep();
        $this->sectionPage = $page;
        $this->fileHtml = new FileGetHtml();
        $this->page = $this->GetCopyPage();
        if (!$this->page) {
            $this->page = $this->fileHtml->file_get_html($page, $this->settings["catalog"]["proxy"], $this->auth);

        } else {
            $this->fileHtml->httpCode = 200;
            $this->fileHtml->headerUrl = $this->GetCopyUrl();
        }
        $this->DeleteCharsetHtml5($this->page);
        $this->SaveCopyPage();
        $this->httpCode = $this->fileHtml->httpCode;
        if ($this->httpCode != 200 && $this->httpCode != 301 && $this->httpCode != 302 && $this->httpCode != 303) {
            {

                $this->errors[] = "[" . $page . "]" . GetMessage("parser_error_connect") . "[" . $this->httpCode . "]";
                //if($this->agent || $this->settings["catalog"]["mode"]=="debug")
                {
                    $this->SaveLog();
                    unset($this->errors);
                }

                if ($this->settings["catalog"]["404"] != "Y") {
                    if (!$this->agent && $this->settings["catalog"]["mode"] != "debug") {
                        $this->stepStart = 1;
                        $this->SavePrevPage($page);
                        if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog_step" . $this->id . ".txt"))
                            unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog_step" . $this->id . ".txt");
                        $this->DeleteCopyPage();
                        $this->activeCurrentPage++;
                        $this->SetCatalogElementsResult($this->activeCurrentPage);
                        $this->clearFields();
                        $this->ClearBufferStep();
                    }

                    return false;
                }
            }
        }

        $this->currentPage++;
        if (!$this->agent && $this->settings["catalog"]["mode"] != "debug") $this->SavePrevPage($page);
        //if($page==$this->rss)
        {
            $this->urlCatalog = $this->fileHtml->headerUrl;
            $this->urlSite = $this->getCatalogUrlSite();
        }

        return true;
    }

    protected function catalogSleep()
    {
        $sleep = $this->settings["catalog"]["sleep"];
        if ($sleep) {
            sleep($sleep);
        }
    }

    protected function SavePrevPage($page)
    {
        if (!$this->agent && $this->settings["catalog"]["mode"] != "debug" && $this->stepStart) {
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_prev_page" . $this->id . ".txt", $page . "|", FILE_APPEND);
        }
    }

    protected function SavePrevPageDetail($page)
    {
        if (!$this->agent && $this->settings["catalog"]["mode"] != "debug") {
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_prev_element" . $this->id . ".txt", $page . "|", FILE_APPEND);
        }
    }

    protected function SaveCurrentPage($arPage)
    {
        if (!$this->agent && $this->settings["catalog"]["mode"] != "debug" && $this->stepStart) {
            $page = implode("|", $arPage);
            if (!empty($arPage)) file_put_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_current_page" . $this->id . ".txt", $page . "|");
            elseif ($this->IsEndSectionUrl()) file_put_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_current_page" . $this->id . ".txt", "");
            else file_put_contents($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_current_page" . $this->id . ".txt", "0");
        }

    }

    protected function ClearAjaxFiles()
    {
        if (!$this->agent && $_GET["begin"]) {
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_prev_page" . $this->id . ".txt")) unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_prev_page" . $this->id . ".txt");
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_prev_element" . $this->id . ".txt")) unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_prev_element" . $this->id . ".txt");
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_current_page" . $this->id . ".txt")) unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_parser_current_page" . $this->id . ".txt");
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog" . $this->id . ".txt")) unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog" . $this->id . ".txt");
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_log_" . $this->id . ".txt")) unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/catalog_log_" . $this->id . ".txt");
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser" . $this->id . ".txt")) unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser" . $this->id . ".txt");
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog_step" . $this->id . ".txt")) unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_catalog_step" . $this->id . ".txt");
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_copy_page" . $this->id . ".txt")) unlink($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include/count_parser_copy_page" . $this->id . ".txt");
        }

    }

    protected function getCatalogUrlSite()
    {
        if (preg_match("/http:/", $this->rss)) {
            $url = str_replace("http://", "", $this->rss);
            $url = preg_replace("/\/.*/", "", $url);
            $url = "http://" . $url;
        } else {
            $url = preg_replace("/\/.*/", "", $url);
        }
        return $url;
    }

    protected function searchCatalogNavigation()
    {

    }

    protected function parseCatalogNavigation($pageHref)
    {
        $this->html = phpQuery::newDocument($this->page, "text/html;charset=" . LANG_CHARSET);


        if ($this->settings["catalog"]["pagenavigation_selector"]) {
            $this->deleteCatalogElement($this->settings["catalog"]["pagenavigation_delete"], $this->settings["catalog"]["pagenavigation_selector"]);
            $element = $this->settings["catalog"]["pagenavigation_selector"] . " " . $this->settings["catalog"]["pagenavigation_one"];
            unset($this->pagenavigation[$pageHref]);
            unset($this->pagenavigation[$this->currentPage]);
            $this->pagenavigationPrev[$pageHref] = $pageHref;


            foreach ($this->html[$element] as $page) {

                $p = pq($page)->attr("href");
                $p = $this->getCatalogLink($p);
                $p1 = $p . "\\r\\n";
                if (!$p || empty($p)) continue;
                $n = pq($page)->text();
                $n = $this->ValidatePageNavigation($n);
                if (isset($this->pagenavigationPrev[$p])) continue;

                if ($this->IsNumberPageNavigation()) {
                    if (!$this->CheckValidatePageNavigation($n) && !$this->CheckPageNavigation($n)) continue;

                    if (($this->currentPage + 5) < $n) continue;
                    if ($this->CheckPageNavigation($n)) {
                        if (isset($this->pagenavigationPrev[$p])) continue;
                        if (isset($this->pagenavigationPrev[$n])) continue;
                        $this->pagenavigation[$n] = $p;
                    } elseif ($this->CheckPageNavigationLess($n)) {
                        if (isset($this->pagenavigationPrev[$p])) continue;
                        if (isset($this->pagenavigationPrev[$n])) continue;
                        $this->pagenavigation[$n] = $p;
                    } else {

                    }
                } else {

                    $this->pagenavigation[$p] = $p;
                }

            }
            return true;
        }
        return false;


    }

    protected function deleteCatalogElement($element, $parentElement = false, $dom = false)
    {
        if ($parentElement) {
            $arElement = explode(",", $element);
            $parentElement = trim($parentElement);
            $element = "";
            foreach ($arElement as $i => $el) {
                $el = trim($el);
                if (empty($el)) {
                    unset($arElement[$i]);
                    continue;
                }
                $element .= $parentElement . " " . $el;
                if (($i + 1) != count($arElement)) $element .= ",";
            }
        }
        pq($element)->remove();
    }

    protected function deleteCatalogAttribute($element, $parentElement = false, $dom = false)
    {
        if ($parentElement) {
            $arElement = explode(",", $element);
            $parentElement = trim($parentElement);
            $element = "";
            foreach ($arElement as $i => $el) {
                $el = trim($el);
                if (empty($el)) {
                    unset($arElement[$i]);
                    continue;
                }

                preg_match('#\[[^\[]+$#', $el, $matches);
                $el = preg_replace('#\[[^\[]+$#', '', $el);
                $attr = str_replace(array("[", "]"), "", $matches[0]);

                $element = $parentElement . " " . $el;
                pq($element)->removeAttr($attr);
            }
        }

    }

    protected function getCatalogLink($url)
    {
        $url = trim($url);
        if (empty($url)) return false;
        elseif (preg_match("/^\/{2}www/", $url)) {
            $url = preg_replace("/^\/{2}www/", "www", $url);
        } elseif (preg_match('/^http:/', $url) || preg_match('/www\./', $url)) {
            $url = $url;
        } elseif (preg_match("/^\//", $url)) {
            $url = $this->urlSite . $url;
        } elseif (!preg_match("/^\//", $url) && preg_match("/\/{1}$/", $this->urlCatalog)) {
            $url = $this->urlCatalog . $url;
        } elseif (!preg_match("/\?/", $url) && !preg_match("/^\//", $url) && !preg_match("/\/{1}$/", $this->urlCatalog)) {
            //$site
            $uri = preg_replace('#/[^/]+$#', '', $this->urlCatalog);
            $url = $uri . "/" . $url;
        } elseif (preg_match("/\?/", $url) && preg_match("/\?/", $this->urlCatalog)) {
            $uri = preg_replace("/\?.+/", "", $this->urlCatalog);
            $url = $uri . $url;
        }

        return $url;
    }

    public function DeleteCharsetHtml5(&$data)
    {
        $data = preg_replace("/\s*<meta\s+charset=[\"|']{0,1}.+?[\"|']{0,1}\s*\/{0,1}\>/i", "", $data);
    }

    public function parserSelector(&$html, $selector, $nextSelector = 0)
    {
        global $sota_DOC_ENCODING;
        phpQuery::selectDocument($html);
        if ($nextSelector == 0 && $this->meta_description != "N") foreach ($html['meta'] as $meta) {
            if (strtolower(pq($meta)->attr("name")) == "description") {
                $this->meta_description_text = pq($meta)->attr("content");
                if (!$this->meta_description_text) $this->meta_description_text = pq($meta)->attr("value");
                if (strtoupper(LANG_CHARSET) == "WINDOWS-1251"/* && strtoupper(LANG_CHARSET)!=strtoupper($sota_DOC_ENCODING)*/) $this->meta_description_text = mb_convert_encoding($this->meta_description_text, LANG_CHARSET, "utf-8");
                if ($this->meta_description_text) {
                    $this->meta_description_text = strip_tags($this->meta_description_text);
                    break;
                }

            }
        }
        if ($nextSelector == 0 && $this->meta_keywords != "N") foreach ($html['meta'] as $meta) {
            if (strtolower(pq($meta)->attr("name")) == "keywords") {
                $this->meta_keywords_text = pq($meta)->attr("content");
                if (!$this->meta_keywords_text) $this->meta_keywords_text = pq($meta)->attr("value");
                if (strtoupper(LANG_CHARSET) == "WINDOWS-1251"/* && strtoupper(LANG_CHARSET)!=strtoupper($sota_DOC_ENCODING)*/) $this->meta_keywords_text = mb_convert_encoding($this->meta_keywords_text, LANG_CHARSET, "utf-8");
                if ($this->meta_keywords_text) {
                    $this->meta_keywords_text = strip_tags($this->meta_keywords_text);
                    break;
                }

            }
        }
        if ($nextSelector == 0 && $this->meta_title != "N") {
            $this->meta_title_text = pq($html['title'])->text();
            $this->meta_title_text = strip_tags($this->meta_title_text);
            //print_r(array(strtoupper(LANG_CHARSET), strtoupper($sota_DOC_ENCODING)));
            if (strtoupper(LANG_CHARSET) == "WINDOWS-1251"/* && strtoupper(LANG_CHARSET)!=strtoupper($sota_DOC_ENCODING)*/) {
                //$this->meta_title_text = mb_convert_encoding($this->meta_title_text, LANG_CHARSET, $sota_DOC_ENCODING);
                $this->meta_title_text = mb_convert_encoding($this->meta_title_text, LANG_CHARSET, "utf-8");
                //print_r(array(strtoupper(LANG_CHARSET), strtoupper($sota_DOC_ENCODING)));
            }
        }


        if (empty($selector)) return $html->htmlOuter();
        else {
            $out = '<meta http-equiv="Content-Type" content="text/html;charset=' . LANG_CHARSET . '">' . pq($selector)->html();
            //print $out;
            return $out;
        }


    }

    public function changeImgSrc($html)
    {
        phpQuery::selectDocument($html);
        $site = $this->getUrlSite();
        foreach ($html["img"] as $img) {
            $src = $this->filterSrc(pq($img)->attr("src"));
            if (!preg_match('/^http:/', $img->getAttribute('src')) && !preg_match('/^www/', $img->getAttribute('src')) && !preg_match('/^\/{2}/', $img->getAttribute('src'))) {
                if (preg_match("/^\/{1}/", $src)) $src = $site . $src;
                else $src = $site . "/" . $src;
                $img->setAttribute('src', $src);
            } else {
                $img->setAttribute('src', $src);
            }
        }
        return $html;
    }

    public function parserFirstImg($html)
    {
        phpQuery::selectDocument($html);
        $site = $this->getUrlSite();
        foreach ($html["img"] as $img) {
            $first_img = $this->filterSrc(pq($img)->attr("src"));
            if (!preg_match('/^http:/', $first_img) && !preg_match('/^www/', $first_img)) {

                if (preg_match("/^\/{1}/", $first_img)) $first_img = $site . $first_img;
                else $first_img = $site . "/" . $first_img;
                $arWidth = getimagesize($first_img);
                if ($arWidth[0] < 40) continue;
                return $first_img;
            } else {
                $arWidth = getimagesize($first_img);
                if ($arWidth[0] < 40) continue;
                return $first_img;
            }
        }
        return $first_img;
    }

    public function saveImgServer($html)
    {
        foreach ($html["img"] as $img) {
            $arImg = CFile::MakeFileArray(pq($img)->attr("src"));
            $fid = CFile::SaveFile($arImg, "sota.parser");
            $img->setAttribute('src', CFile::GetPath($fid));
        }
        return $html->htmlOuter();
    }

    public function deleteElementStart(&$html, $selector_delete_element)
    {
        phpQuery::selectDocument($html);
        $arElements = explode(',', $selector_delete_element);
        foreach ($arElements as $selector) {
            if (empty($selector)) continue;
            $selector = trim($selector);
            $html[$selector]->remove();
        }
        return $html;
    }

    public function deleteElements(&$html, $selector, $nextSelector = 0)
    {
        $arSelector = $this->arraySelector($selector);
        $n = 0;
        if (!isset($arSelector[$nextSelector])) {
            $html->outertext = "";
            return;
        }
        if (strpos($arSelector[$nextSelector], '[') !== false && preg_match("/\[[0-9]{1,3}\]/", $arSelector[$nextSelector])) {
            $sel = $arSelector[$nextSelector];
            $arSelector[$nextSelector] = preg_replace('/\[[0-9]{1,3}\]/', '', $sel);
            preg_match_all('/\[[0-9]{1,3}\]/', $sel, $matches);
            $n = str_replace(array('[', ']'), "", $matches[0][0]);
            $item = $html->find($arSelector[$nextSelector], $n);
            if (gettype($item) == "NULL") {
                return false;
            }
            $data = $this->deleteElements($item, $selector, $nextSelector + 1);

        } else {
            foreach ($html->find($arSelector[$nextSelector]) as $item) {
                $data = $this->deleteElements($item, $selector, $nextSelector + 1);
            }
        }

    }

    public function deleteAttributeStart(&$html, $selector_delete_attribute)
    {

        $arElements = explode(',', $selector_delete_attribute);

        foreach ($arElements as $selector) {
            if (empty($selector)) continue;
            preg_match('/\[[a-zA-Z]+\]$/', $selector, $attribute);
            $attributes = str_replace(array(']', '['), "", $attribute[0]);
            $selector = preg_replace('/\[[a-zA-Z]+\]$/', "", trim($selector));
            $this->deleteAttributes($html, trim($selector), $attributes);
        }
        return $html;
    }

    public function deleteAttributes(&$html, $selector, $attribute, $nextSelector = 0)
    {
        phpQuery::selectDocument($html);
        pq($selector)->removeAttr($attribute);
    }

    public function getContentsArray($site = '', $port = 80, $path = '', $query = '')
    {
        if (!$this->type || $this->type == "rss") {
            $arContent = CIBlockRSS::GetNewsEx($site, $port, $path, $query);

            return CIBlockRSS::FormatArray($arContent);
        } elseif ($this->type == "page") {
            $url = $site . $path;
            if ($query) $url = $url . "?" . $query;
            $fileHtml = new FileGetHtml();
            $data = $fileHtml->file_get_html($url, $this->proxy, $this->auth);
            $this->header_url = $url = $fileHtml->headerUrl;
            $this->DeleteCharsetHtml5($data);
            $html = phpQuery::newDocument($data, "text/html;charset=" . LANG_CHARSET);
            $dom = htmlspecialcharsBack(trim($this->settings["page"]["selector"]));
            $href = htmlspecialcharsBack(trim($this->settings["page"]["href"]));
            $name = htmlspecialcharsBack(trim($this->settings["page"]["name"]));
            $href = $href ? $href : "a:eq(0)";
            $name = $name ? $name : $href;

            $i = 0;
            $site = $this->getUrlSite();
            foreach ($html[$dom] as $val) {
                $strName = strip_tags(pq($val)->find($name)->html());
                //$strHref =  mb_strtolower(pq($val)->find($href)->attr("href"));
                $strHref = pq($val)->find($href)->attr("href");


                if (!preg_match('/^http:/', $strHref) && !preg_match('/^www/', $strHref) && !preg_match('/^\/{2}/', $strHref)) {
                    if (preg_match('/^\//', $strHref)) $strHref = $site . $strHref;
                    else $strHref = $site . $path . $strHref;
                }

                if (empty($strName)) $this->errors[] = GetMessage("parser_error_noname");
                if (empty($strHref)) $this->errors[] = GetMessage("parser_error_nohref");
                if (empty($strName) || empty($strHref)) continue;
                $arContent["item"][$i]["title"] = $strName;
                $arContent["item"][$i]["link"] = $strHref;
                $arContent["item"][$i]["description"] = pq($val)->html();
                $i++;
            }
            if ($i > 0) {
                $arContent['title'] = $site;
                $arContent['link'] = $site;
                return $arContent;
            }
        }
    }

    public function getUrlSite()
    {
        $this->header_url = strtolower($this->header_url);
        $site = str_replace(array('http://', 'www.', "HTTP://", "WWW."), "", $this->header_url);
        $site = preg_replace('/\/(.)+/', '', $site);
        $arLevel = explode(".", $site);
        if (count($arLevel) == 2) return 'http://www.' . $site;
        else return 'http://' . $site;
    }

    public function filterSrc($src)
    {
        $src = preg_replace('/#.+/', '', $src);
        $src = preg_replace('/\?.+/', '', $src);
        //$src = str_replace('http:/', 'http://', $src);
        $src = str_replace('//', '/', $src);
        $src = str_replace('http:/', 'http://', $src);
        if (preg_match("/www\./", $src) || preg_match("/http:\//", $src)) $src = preg_replace("/^\/{2}/", "http://", $src);
        if (preg_match("/www\./", $src) || preg_match("/http:\//", $src)) $src = preg_replace("/^\/{1}/", "http://", $src);
        //$src = str_replace('//', '/', $src);
        return $src;
    }

    public function parseImgFromRss($arItem)
    {
        foreach ($arItem as $item) {
            if (is_array($item)) $preview = $this->parseImgFromRss($item);
            elseif (preg_match("/^(http:)(.)+(jpg|JPG|gif|GIF|png|PNG|JPEG|jpeg)$/", $item, $match)) {
                $preview = $match[0];
                break;
            }
        }
        return $preview;
    }

    public function arraySelector($selector, $debug = 0)
    {
        $bool = false;
        $selector = trim($selector);
        $arSel = explode(' ', $selector);
        $newArSel = array();
        $selStr = "";
        foreach ($arSel as $i => $val) {
            if (preg_match('/\[/', $val) && preg_match('/\]/', $val) && !$bool) $newArSel[] = $val;
            elseif (!preg_match('/\[/', $val) && !preg_match('/\]/', $val) && !$bool) $newArSel[] = $val;
            elseif (preg_match('/\[/', $val) && !preg_match('/\]/', $val)) {
                $bool = true;
                $selStr .= $val;
            } elseif (!preg_match('/\[/', $val) && !preg_match('/\]/', $val) && $bool) {
                $selStr .= " " . $val;
            } elseif (preg_match('/\]/', $val) && $bool) {
                $selStr .= " " . $val;
                $bool = false;
                $newArSel[] = $selStr;
                $selStr = "";
            }
        }
        return $newArSel;
    }
}

?>