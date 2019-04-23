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

IncludeModuleLangFile(__FILE__);
global $DB, $sota_DEMO;
$db_type = strtolower($DB->type);

$module_id = 'sota.parser';
$module_status = CModule::IncludeModuleEx($module_id);
//$module_status = 3;
if ($module_status == '1') {
    $include_class = true;
    $sota_DEMO = 1;
} elseif ($module_status == '2') {
    $include_class = true;
    $sota_DEMO = 2;
    //echo GetMessage("DEMO");

} elseif ($module_status == '3') {
    $include_class = false;
    $sota_DEMO = 3;
}

CModule::AddAutoloadClasses(
    "sota.parser",
    array(
        "SotaParserContentGeneral" => "classes/general/list_parser.php",
        "SotaContentParser" => "classes/general/main_classes.php",
        "SotaXmlParser" => "classes/general/main_classes_xml.php",
        "SotaParserContent" => "classes/" . $db_type . "/list_parser.php",
        "SotaParserTmpTable" => "classes/" . $db_type . "/parser_tmp.php",
        "SotaParserTmpOldTable" => "classes/" . $db_type . "/parser_tmp_old.php",
        "SotaParserSectionTable" => "classes/" . $db_type . "/parser_section.php",
    )
);

include($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/sota.parser/classes/phpQuery/phpQuery.php');
include($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/sota.parser/classes/general/sota_idna_convert.class.php');
include($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/sota.parser/classes/general/file_get_html.php');

/**
 *** класс для парсинга rss, page, catalog
 **/
Class RssContentParser extends SotaXmlParser
{
    /*protected $id = false;
    protected $rss;
    protected $type;
    protected $active;
    protected $iblock_id;
    protected $section_id;
    protected $detail_dom;
    protected $encoding;
    protected $preview_delete_tag="";
    protected $bool_preview_delete_tag="";
    protected $detail_delete_tag="";
    protected $bool_detail_delete_tag="";
    protected $preview_first_img="";
    protected $detail_first_img="";
    protected $preview_save_img="";
    protected $detail_save_img="";
    protected $text = "";
    protected $site = "";
    protected $link = "";
    protected $preview_delete_element="";
    protected $detail_delete_element="";
    protected $preview_delete_attribute="";
    protected $detail_delete_attribute="";
    protected $index_element="";
    protected $resize_image="";
    protected $meta_description="";
    protected $meta_keywords="";
    protected $meta_description_text="";
    protected $meta_keywords_text="";
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
    const DEFAULT_DEBUG_ITEM = 3;*/

    public function __construct()
    {
        /*global $zis, $sota_ID, $sota_TYPE, $sota_ACTIVE, $sota_IBLOCK_ID, $sota_RSS, $sota_SECTION_ID, $sota_SELECTOR, $sota_ENCODING, $sota_PREVIEW_DELETE_TAG, $sota_PREVIEW_TEXT_TYPE, $sota_DETAIL_TEXT_TYPE, $sota_BOOL_PREVIEW_DELETE_TAG,$sota_PREVIEW_FIRST_IMG, $sota_PREVIEW_SAVE_IMG, $sota_DETAIL_DELETE_TAG, $sota_BOOL_DETAIL_DELETE_TAG,$sota_DETAIL_FIRST_IMG, $sota_DETAIL_SAVE_IMG, $sota_PREVIEW_DELETE_ELEMENT, $sota_DETAIL_DELETE_ELEMENT, $sota_PREVIEW_DELETE_ATTRIBUTE, $sota_DETAIL_DELETE_ATTRIBUTE, $sota_INDEX_ELEMENT, $sota_CODE_ELEMENT, $sota_RESIZE_IMAGE, $sota_META_DESCRIPTION, $sota_META_KEYWORDS, $sota_ACTIVE_ELEMENT, $sota_FIRST_TITLE, $sota_DATE_PUBLIC, $sota_FIRST_URL, $sota_DATE_ACTIVE, $sota_META_TITLE, $sota_SETTINGS;
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
        $this->mode = $this->settings["catalog"]["mode"];
        $this->currentPage = 0;
        $this->activeCurrentPage = 0;
        $this->debugErrors = array();
        $this->stepStart = false;
        $this->pagePrevElement = array();
        $this->pagenavigationPrev = array();
        $this->pagenavigation = array();*/
        $this->setDemo();
        CModule::IncludeModule("highloadblock");
        parent::__construct();

    }

    protected function setDemo()
    {
        global $sota_DEMO;
        $module_id = "sota.parser";
        $sota_DEMO = CModule::IncludeModuleEx($module_id);
        if ($sota_DEMO == 3) die("DEMO END");
        //elseif($sota_DEMO==2)$this->settings["catalog"]["mode"] = "debug";
    }

    public function sotaParserSetSettings(&$SETTINGS)
    {
        foreach ($SETTINGS as &$v) {
            if (is_array($v)) self::sotaParserSetSettings($v);
            else $v = htmlspecialcharsBack($v);
        }
    }

    public function createFolder()
    {
        global $sota_DEMO;
        $this->setDemo();
        $dir = $_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/sota.parser/include";
        if (!file_exists($dir) && $sota_DEMO != 3)
            mkdir($dir, BX_DIR_PERMISSIONS);
        elseif ($sota_DEMO == 3) unlink($dir);
    }


    public function auth($check = false, $type = "http")
    {
        //if($type=="http")return false;
        global $sota_DEMO;
        $this->setDemo();
        if ($sota_DEMO == 3) die("DEMO END");
        $this->check = $check;
        $this->GetAuthForm($check);

    }


}


Class CSotaParser
{
    static function startAgent($ID)
    {
        ignore_user_abort(true);
        @set_time_limit(0);
        if (CModule::IncludeModule('iblock') && CModule::IncludeModule('main')):
            CModule::IncludeModule("highloadblock");
            $parser = SotaParserContent::GetByID($ID);
            if (!$parser->ExtractFields("sota_"))
                $ID = 0;
            $rssParser = new RssContentParser();
            $rssParser->startParser(1);
            return "CSotaParser::startAgent(" . $ID . ");";
        endif;
    }
}

?> 