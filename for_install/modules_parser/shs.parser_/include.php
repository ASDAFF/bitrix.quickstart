<?
use Bitrix\Seo\Engine;
use Bitrix\Main\Text\Converter;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\IO\Path;

\Bitrix\Main\Loader::includeModule('seo');
\Bitrix\Main\Loader::includeModule('socialservices');

IncludeModuleLangFile(__FILE__);
global $DB, $shs_DEMO;
$db_type = strtolower($DB->type);

$module_id = 'shs.parser';
$module_status = CModule::IncludeModuleEx($module_id);
//$module_status = 3;
if($module_status == '1') {
    $include_class = true;
    $shs_DEMO = 1;
}
elseif($module_status == '2') {
    $include_class = true;
    $shs_DEMO = 2;
    //echo GetMessage("DEMO");

}
elseif($module_status == '3'){
    $include_class = false;
    $shs_DEMO = 3;
}

CModule::AddAutoloadClasses(
    "shs.parser",
    array(
        "ShsParserContentGeneral" => "classes/general/list_parser.php",
        "SotbitContentParser" => "classes/general/main_classes.php",
        "SotbitXmlParser" => "classes/general/main_classes_xml.php",
        "ShsParserContent" => "classes/".$db_type."/list_parser.php",
        "ShsParserTmpTable" => "classes/".$db_type."/parser_tmp.php",
        "ShsParserTmpOldTable" => "classes/".$db_type."/parser_tmp_old.php",
        "ShsParserSectionTable" => "classes/".$db_type."/parser_section.php",
    )
);

include($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/shs.parser/classes/phpQuery/phpQuery.php');
include($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/shs.parser/classes/general/sotbit_idna_convert.class.php');
include($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/shs.parser/classes/general/file_get_html.php');
/**
*** класс для парсинга rss, page, catalog
**/
Class RssContentParser extends SotbitXmlParser {
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

    public function __construct(){
        /*global $zis, $shs_ID, $shs_TYPE, $shs_ACTIVE, $shs_IBLOCK_ID, $shs_RSS, $shs_SECTION_ID, $shs_SELECTOR, $shs_ENCODING, $shs_PREVIEW_DELETE_TAG, $shs_PREVIEW_TEXT_TYPE, $shs_DETAIL_TEXT_TYPE, $shs_BOOL_PREVIEW_DELETE_TAG,$shs_PREVIEW_FIRST_IMG, $shs_PREVIEW_SAVE_IMG, $shs_DETAIL_DELETE_TAG, $shs_BOOL_DETAIL_DELETE_TAG,$shs_DETAIL_FIRST_IMG, $shs_DETAIL_SAVE_IMG, $shs_PREVIEW_DELETE_ELEMENT, $shs_DETAIL_DELETE_ELEMENT, $shs_PREVIEW_DELETE_ATTRIBUTE, $shs_DETAIL_DELETE_ATTRIBUTE, $shs_INDEX_ELEMENT, $shs_CODE_ELEMENT, $shs_RESIZE_IMAGE, $shs_META_DESCRIPTION, $shs_META_KEYWORDS, $shs_ACTIVE_ELEMENT, $shs_FIRST_TITLE, $shs_DATE_PUBLIC, $shs_FIRST_URL, $shs_DATE_ACTIVE, $shs_META_TITLE, $shs_SETTINGS;
        $this->id = $shs_ID;
        $this->type = $shs_TYPE;
        $this->rss = $shs_RSS;
        $this->active = $shs_ACTIVE;
        $this->iblock_id = $shs_IBLOCK_ID;
        $this->section_id = $shs_SECTION_ID;
        $this->detail_dom = $shs_SELECTOR;
        $this->first_url = trim($shs_FIRST_URL);
        $this->encoding = $shs_ENCODING;
        $this->preview_text_type = $shs_PREVIEW_TEXT_TYPE;
        $this->detail_text_type = $shs_DETAIL_TEXT_TYPE;
        $this->preview_delete_tag = $shs_PREVIEW_DELETE_TAG;
        $this->detail_delete_tag = $shs_DETAIL_DELETE_TAG;
        $this->bool_preview_delete_tag = $shs_BOOL_PREVIEW_DELETE_TAG;
        $this->bool_detail_delete_tag = $shs_BOOL_DETAIL_DELETE_TAG;
        $this->preview_first_img = $shs_PREVIEW_FIRST_IMG;
        $this->detail_first_img = $shs_DETAIL_FIRST_IMG;
        $this->preview_save_img = $shs_PREVIEW_SAVE_IMG;
        $this->detail_save_img = $shs_DETAIL_SAVE_IMG;
        $this->preview_delete_element = $shs_PREVIEW_DELETE_ELEMENT;
        $this->detail_delete_element = $shs_DETAIL_DELETE_ELEMENT;
        $this->preview_delete_attribute = $shs_PREVIEW_DELETE_ATTRIBUTE;
        $this->detail_delete_attribute = $shs_DETAIL_DELETE_ATTRIBUTE;
        $this->index_element = $shs_INDEX_ELEMENT;
        $this->code_element = $shs_CODE_ELEMENT;
        $this->resize_image = $shs_RESIZE_IMAGE;
        $this->meta_title = $shs_META_TITLE;
        $this->meta_description = $shs_META_DESCRIPTION;
        $this->meta_keywords = $shs_META_KEYWORDS;
        $this->active_element = $shs_ACTIVE_ELEMENT;
        $this->first_title = $shs_FIRST_TITLE;
        $this->date_public = $shs_DATE_PUBLIC;
        $this->date_active = $shs_DATE_ACTIVE;
        $this->settings = unserialize(base64_decode($shs_SETTINGS));
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
        global $shs_DEMO;
        $module_id = "shs.parser";
        $shs_DEMO = CModule::IncludeModuleEx($module_id);
        if($shs_DEMO==3) die("DEMO END");
        //elseif($shs_DEMO==2)$this->settings["catalog"]["mode"] = "debug";   
    }

    public function sotbitParserSetSettings(&$SETTINGS)
    {
        foreach($SETTINGS as &$v)
        {
            if(is_array($v)) self::sotbitParserSetSettings($v);
            else $v = htmlspecialcharsBack($v);
        }
    }

    public function createFolder()
    {
        global $shs_DEMO;
        $this->setDemo();
        $dir = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include";
        if(!file_exists($dir) && $shs_DEMO!=3)
            mkdir($dir, BX_DIR_PERMISSIONS);
        elseif($shs_DEMO==3) unlink($dir);
    }
    
    
    
    public function auth($check=false, $type="http")
    {
        //if($type=="http")return false;
        global $shs_DEMO;
        $this->setDemo();
        if($shs_DEMO==3) die("DEMO END");
        $this->check = $check;
        $this->GetAuthForm($check);
        
    }
    
    
    
    
}


Class CShsParser
{
    static function startAgent($ID){
        ignore_user_abort(true);
        @set_time_limit(0);
        if(CModule::IncludeModule('iblock') && CModule::IncludeModule('main')):
        CModule::IncludeModule("highloadblock");
        $parser = ShsParserContent::GetByID($ID);
        if(!$parser->ExtractFields("shs_"))
        $ID=0;
        $rssParser = new RssContentParser();
        $rssParser->startParser(1);
        return "CShsParser::startAgent(".$ID.");";
        endif;
    }
}
?> 