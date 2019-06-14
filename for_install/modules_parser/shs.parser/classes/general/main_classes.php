<?
use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity;
use Bitrix\Seo\Engine;
use Bitrix\Main\Text\Converter;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\IO\Path;

\Bitrix\Main\Loader::includeModule('seo');
\Bitrix\Main\Loader::includeModule('socialservices');

class SotbitContentParser {
    
    public $id = false;
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

    public $page;
    const TEST = 0;
    const DEFAULT_DEBUG_LIST = 3;
    const DEFAULT_DEBUG_ITEM = 3;
    
    public function __construct()
    {
        global $zis, $shs_ID, $shs_TYPE, $shs_ACTIVE, $shs_IBLOCK_ID, $shs_RSS, $shs_SECTION_ID, $shs_SELECTOR, $shs_ENCODING, $shs_PREVIEW_DELETE_TAG, $shs_PREVIEW_TEXT_TYPE, $shs_DETAIL_TEXT_TYPE, $shs_BOOL_PREVIEW_DELETE_TAG,$shs_PREVIEW_FIRST_IMG, $shs_PREVIEW_SAVE_IMG, $shs_DETAIL_DELETE_TAG, $shs_BOOL_DETAIL_DELETE_TAG,$shs_DETAIL_FIRST_IMG, $shs_DETAIL_SAVE_IMG, $shs_PREVIEW_DELETE_ELEMENT, $shs_DETAIL_DELETE_ELEMENT, $shs_PREVIEW_DELETE_ATTRIBUTE, $shs_DETAIL_DELETE_ATTRIBUTE, $shs_INDEX_ELEMENT, $shs_CODE_ELEMENT, $shs_RESIZE_IMAGE, $shs_META_DESCRIPTION, $shs_META_KEYWORDS, $shs_ACTIVE_ELEMENT, $shs_FIRST_TITLE, $shs_DATE_PUBLIC, $shs_FIRST_URL, $shs_DATE_ACTIVE, $shs_META_TITLE, $shs_SETTINGS, $shs_TMP;
        $this->id = $shs_ID;
        $this->typeN = $shs_TYPE;
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
        $this->index_element = ($shs_INDEX_ELEMENT=="Y")?true:false;
        $this->code_element = $shs_CODE_ELEMENT;
        $this->resize_image = ($shs_RESIZE_IMAGE=="Y")?true:false;
        $this->meta_title = $shs_META_TITLE;
        $this->meta_description = $shs_META_DESCRIPTION;
        $this->meta_keywords = $shs_META_KEYWORDS;
        $this->active_element = $shs_ACTIVE_ELEMENT;
        $this->first_title = $shs_FIRST_TITLE;
        $this->date_public = $shs_DATE_PUBLIC;
        $this->date_active = $shs_DATE_ACTIVE;
        $this->tmp = $shs_TMP;
        $this->settings = unserialize(base64_decode($shs_SETTINGS));
        $this->header_url = "";
        $this->sleep = (int)$this->settings[$this->typeN]["sleep"];
        $this->proxy = (int)$this->settings[$this->typeN]["proxy"];
        $this->errors = array();
        $this->auth = $this->settings[$this->typeN]["auth"]["active"]?true:false;
        $this->currentPage = 0;
        $this->activeCurrentPage = 0;
        $this->debugErrors = array();
        $this->stepStart = false;
        $this->pagePrevElement = array();
        $this->pagenavigationPrev = array();
        $this->pagenavigation = array(); 
    }
    
    public function startParser($agent=false){
        global $DB, $shs_DEMO; //$agent = true;
        if($shs_DEMO==3) return;
        $this->createFolder();
        $this->createAlbum();
        if($this->active!="Y"){
          $result["ERROR"][] = GetMessage("parser_active_no");
          $this->errors[] = GetMessage("parser_active_no");
          if(!$agent)CAdminMessage::ShowMessage(GetMessage("parser_active_no"));
          return $result;
        }
        $parser = new ShsParserContent();
        $now = time()+CTimeZone::GetOffset();
        $arFieldsTime['START_LAST_TIME_X'] = date($DB->DateFormatToPHP(FORMAT_DATETIME), $now);
        $parser->Update($this->id, $arFieldsTime);
        $this->convetCyrillic($this->rss);
        if($this->meta_description!="N"){
            $propDescr = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$this->iblock_id, "CODE"=>$this->meta_description))->Fetch();
            if(!$propDescr){
                $result["ERROR"][] = GetMessage("parser_error_description");
                $this->errors[] = GetMessage("parser_error_description");
            }
        }
        if($this->meta_keywords!="N"){
            $propKey = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$this->iblock_id, "CODE"=>$this->meta_keywords))->Fetch();
            if(!$propKey){
                $result["ERROR"][] = GetMessage("parser_error_keywords");
                $this->errors[] = GetMessage("parser_error_keywords");
                //return $result;
            }
        }
        if($this->meta_title!="N"){
            $propKey = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$this->iblock_id, "CODE"=>$this->meta_title))->Fetch();
            if(!$propKey){
                $result["ERROR"][] = GetMessage("parser_error_title");
                $this->errors[] = GetMessage("parser_error_title");
                //return $result;
            }
        }
        if($this->first_title!="N"){
            $propFirst= CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$this->iblock_id, "CODE"=>$this->first_title))->Fetch();
            if(!$propFirst){
                $result["ERROR"][] = GetMessage("parser_error_first");
                $this->errors[] = GetMessage("parser_error_first");
                //return $result;
            }
        }
        if($this->date_public!="N"){
            $propDate= CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$this->iblock_id, "CODE"=>$this->date_public))->Fetch();
            if(!$propDate){
                $result["ERROR"][] = GetMessage("parser_error_date");
                $this->errors[] = GetMessage("parser_error_date");
                //return $result;
            }
        }


        if(isset($result['ERROR']))
        {
            if(!$agent)foreach($result['ERROR'] as $error) CAdminMessage::ShowMessage($error);
            return false;
        }
        $this->agent = $agent;
        if($shs_DEMO==2) $this->settings["catalog"]["mode"] = "debug";


        if($_GET["begin"]){
            $this->auth(true);
            foreach(GetModuleEvents("shs.parser", "startPars", true) as $arEvent)
                ExecuteModuleEventEx($arEvent, array($this->id, &$this));
        }
        elseif($this->agent || $this->settings["catalog"]["mode"]=="debug"){
            $this->auth(true);
            foreach(GetModuleEvents("shs.parser", "startPars", true) as $arEvent)
                ExecuteModuleEventEx($arEvent, array($this->id, &$this));
        }
        
        if($this->typeN=="catalog")
        {
            $this->isCatalog();
            $this->getUniqElement();
            $this->isUpdateElement();
            $this->GetSortFields();
            $this->getArrayIblock();
            $this->DoPageNavigation();
            $this->CheckFields($this->settings["catalog"]);
            
            if(!$this->errors)$this->parseCatalog();
            else{    
                if(!$agent)foreach($this->errors as $error) CAdminMessage::ShowMessage($error);
                $this->SaveLog();
                return false;
            } 
            if($this->debugErrors && $this->settings["catalog"]["mode"]=="debug")
            {
                if(!$agent)foreach($this->debugErrors as $error) CAdminMessage::ShowMessage($error);
                $this->SaveLog();
                return false;
            }
            return true;
        }
        if ($this->typeN=="xml")
        {
            $this->isCatalog(); //если каталог
            $this->getUniqElement(); //уникальность элементов
            $this->isUpdateElement(); //если элементы обновляется
            $this->GetSortFields(); //формируем массив из тех что постоянно обновляются 
            $this->getArrayIblock(); //массив полей инфоблока
            $this->CheckFields($this->settings["catalog"]); //проверки правльности данных и вывод ошибок
            
            if(!$this->errors)$this->parseXmlCatalog();
            else{    
                if(!$agent)foreach($this->errors as $error) CAdminMessage::ShowMessage($error);
                $this->SaveLog();
                return false;
            } 
            if($this->debugErrors && $this->settings["catalog"]["mode"]=="debug")
            {
                if(!$agent)foreach($this->debugErrors as $error) CAdminMessage::ShowMessage($error);
                $this->SaveLog();
                return false;
            }
            return true;
        }

        $this->rss = str_replace(array('http://', 'www.', 'https://'), '', $this->rss);
        $arSite = explode('/', $this->rss);
        $level = explode('.', $arSite[0]);
        if(count($level)>=3) $this->rss = $this->rss;
        else $this->rss = 'www.'.$this->rss;
        $arSite = explode('/', $this->rss);
        $this->site = $arSite[0];
        $uri = preg_replace('/^(www\.){0,1}([a-zA-Z0-9-\.])+\//', '', $this->rss);
        $arPath = explode('?', $uri);
        $path = '/'.$arPath[0];
        $query = $arPath[1];   
        $arContent = $this->getContentsArray($this->site, 80, $path, $query);
        if(empty($arContent['title']) && empty($arContent['link'])){
            $arContent = $this->getContentsArray(str_replace("www.", "", $this->site), 80, $path, $query);
            if(empty($arContent['title']) && empty($arContent['link'])){
                $arContent = $this->getContentsArray("www.".$this->site, 80, $path, $query);
                if(empty($arContent['title']) && empty($arContent['link'])){
                    if(!$agent){
                        $result["ERROR"][] = GetMessage("parser_error");
                        $this->errors[] = GetMessage("parser_error");
                        //CAdminMessage::ShowMessage(GetMessage("parser_error"));
                        //return $result;
                    }
                    //return false;
                }
            }
        }
        if($this->errors)
        {
            if(!$agent)foreach($this->errors as $error) CAdminMessage::ShowMessage($error);
            if($this->typeN!="page")return false;
        }
        if(isset($result['ERROR']))
        {
            //if(!$agent)foreach($result['ERROR'] as $error) CAdminMessage::ShowMessage($error);
            if($this->typeN!="page")return false;
        }


        return $this->setContentIblock($arContent, $this->iblock_id, $this->section_id, $this->detail_dom, $this->encoding);
    }
    
    protected function convetCyrillic(&$url)
    {
        if(preg_match("/^\/{2}www/", $url))
        {
            $url = preg_replace("/^\/{2}www/", "www", $url);
        }
        $converter = new sotbit_idna_convert();
        $url = $converter->encode($url);
    }
    
    private function setContentIblock($arContent=array(), $iblock_id=false, $section_id=false, $detail_dom="", $encoding="utf-8"){
        $first = false;
        global $shs_preview, $shs_first, $DB, $shs_DEMO;
        set_time_limit(0);
        $this->setDemo();
        /*if($this->first_url)
        {
            if($this->first_url && strpos($this->header_url, $this->first_url)===false && strpos($item['link'], $this->first_url)==false) continue;
        }
        else */ 
        $count = count($arContent['item']);
        $ci = 0;  
        //printr($arContent);
        file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser".$this->id.".txt", $count."|".$ci);
        foreach($arContent['item'] as $i=>$item){
            $item['title'] = trim($item['title']);
            //if($this->sleep && $this->sleep>0)sleep($this->sleep);
            $this->link = $item['link'];
            $item['link'] = str_replace('http://', '', $item['link']);
            $this->convetCyrillic($item['link']);
            if(!isset($this->settings[$this->typeN]["uniq"]) || $this->settings[$this->typeN]["uniq"]=="name")
            {
                if($item['title'] && isset($this->settings['loc']["f_name"]) && $this->settings['loc']["f_name"]=="Y")
                {
                    $item['title'] = $this->locText($item['title']);        
                }
                $isElement = CIBlockElement::GetList(Array(), array("NAME"=>$item['title'], "SECTION_ID"=>$section_id, "IBLOCK_ID"=>$iblock_id), false, Array("nTopCount"=>1), array("ID"))->Fetch();
            }
            else{
                $md5 = md5($item['link']);
                $isElement = CIBlockElement::GetList(Array(), array("XML_ID"=>$md5, "SECTION_ID"=>$section_id, "IBLOCK_ID"=>$iblock_id), false, Array("nTopCount"=>1), array("ID"))->Fetch();
            }
            
            
            $ci++;
            if($isElement && !self::TEST && $shs_DEMO==1) continue;
            $first = true;
            $item['description'] = trim($item['description']);
            
            
            $fileHtml = new FileGetHtml();
            $this->date_public_text = $item["pubDate"];
            $proxy = $this->proxy;
            
            $data = $fileHtml->file_get_html(mb_ereg_replace("\n", "", $item['link']), $proxy, $this->auth, $this); 
            $this->header_url = $fileHtml->headerUrl;
            if($this->first_url && strpos($this->header_url, $this->first_url)===false && strpos($item['link'], $this->first_url)==false) continue;
            $shs_first = true;
            //preg_replace("/\<meta\s+charser=[\"|']{0,1}.+[\"|']{0,1}\s*\/{0,1}\>/ig")
            //print $data;
            $this->DeleteCharsetHtml5($data);
            $html = phpQuery::newDocument($data, "text/html;charset=".LANG_CHARSET);
            $shs_first = false;
            $this->first_title_text = $this->header_url;

            $this->getUrlSite();
            $DETAIL_TEXT = "";
            $this->text = "";

            $DETAIL_TEXT = $this->parserSelector($html, htmlspecialchars_decode(trim($detail_dom)));

            $el = new CIBlockElement;
            $shs_preview = true;
            if($this->preview_first_img=="Y") $PREVIEW_IMG = $this->parserFirstImg(phpQuery::newDocument($item['description']), "text/html;charset=".LANG_CHARSET);
            $shs_preview = false;
            if($this->detail_first_img=="Y") $DETAIL_IMG = $this->parserFirstImg(phpQuery::newDocument($DETAIL_TEXT), "text/html;charset=".LANG_CHARSET);
            
            $this->preview_delete_element = trim($this->preview_delete_element);
            $this->detail_delete_element = trim($this->detail_delete_element);
            $shs_preview = true;
            $preview_html = phpQuery::newDocument($item['description'], "text/html;charset=".LANG_CHARSET);
            $shs_preview = false;
            $detail_html = phpQuery::newDocument($DETAIL_TEXT, "text/html;charset=".LANG_CHARSET);
            if(!empty($this->preview_delete_element)){
                $preview_html = $this->deleteElementStart($preview_html, htmlspecialchars_decode($this->preview_delete_element));
            }
            if(!empty($this->detail_delete_element)){
                $detail_html = $this->deleteElementStart($detail_html, htmlspecialchars_decode($this->detail_delete_element));
            }
            if(!empty($this->preview_delete_attribute)){
                $preview_html = $this->deleteAttributeStart($preview_html, htmlspecialchars_decode($this->preview_delete_attribute));
            }
            if(!empty($this->detail_delete_attribute)){
                $detail_html = $this->deleteAttributeStart($detail_html, htmlspecialchars_decode($this->detail_delete_attribute));
            }
            
            
            
            
            $detail_html = $this->changeImgSrc($detail_html);
            $preview_html = $this->changeImgSrc($preview_html);

            if($this->preview_save_img=="Y") $item['description'] = $this->saveImgServer($preview_html);
            else $item['description'] = $preview_html->htmlOuter();
            if($this->detail_save_img=="Y") $DETAIL_TEXT = $this->saveImgServer($detail_html);
            else $DETAIL_TEXT = $detail_html->htmlOuter();
            $item['description'] = preg_replace("/\<meta(.)+\>{1}/", "", $item['description']);
            $DETAIL_TEXT = preg_replace("/\<meta(.)+\>{1}/", "", $DETAIL_TEXT);
                        
            if($this->code_element=="Y")$code = $arProperty["CODE"] = CUtil::translit($item['title'], "ru", array(
                        "max_len" => 100,
                        "change_case" => 'L', // 'L' - toLower, 'U' - toUpper, false - do not change
                        "replace_space" => '_',
                        "replace_other" => '_',
                        "delete_repeat_replace" => true,
            ));
            if($this->date_public_text)$unix = strtotime($this->date_public_text);
            if($this->date_active=="NOW") $date_from = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "SHORT");
            elseif($this->date_active=="NOW_TIME") $date_from = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "FULL");
            elseif($this->date_active=="PUBLIC" && $unix) $date_from = ConvertTimeStamp($unix, "FULL");
            
            
            if(!empty($this->preview_delete_tag) && $this->bool_preview_delete_tag=="Y"){
                $item['description'] = strip_tags($item['description'], htmlspecialchars_decode($this->preview_delete_tag));
            }elseif($this->bool_preview_delete_tag=="Y") $item['description'] = strip_tags($item['description']);
            if(!empty($this->detail_delete_tag) && $this->bool_detail_delete_tag=="Y"){
                $DETAIL_TEXT = strip_tags($DETAIL_TEXT, htmlspecialchars_decode($this->detail_delete_tag));
            }elseif($this->bool_detail_delete_tag=="Y")
            {
                $DETAIL_TEXT = strip_tags($DETAIL_TEXT);
            }
            $item['description'] = trim($item['description']);
            $DETAIL_TEXT = trim($DETAIL_TEXT);
            
            if($item['title'] && isset($this->settings['loc']["f_name"]) && $this->settings['loc']["f_name"]=="Y" && $this->settings[$this->typeN]["uniq"]=="url")
            {
                $item['title'] = $this->locText($item['title']);        
            }
            if($item['description'] && isset($this->settings['loc']["f_preview_text"]) && $this->settings['loc']["f_preview_text"]=="Y")
            {
                $item['description'] = $this->locText($item['description'], $this->preview_text_type=="html"?"html":"plain");        
            }
            if($DETAIL_TEXT && isset($this->settings['loc']["f_detail_text"]) && $this->settings['loc']["f_detail_text"]=="Y")
            {   
                $DETAIL_TEXT = $this->locText($DETAIL_TEXT, $this->detail_text_type=="html"?"html":"plain", true);
            }

            $arLoadProductArray = Array(
                "MODIFIED_BY"    => 1, // ??????? ??????? ??????? ?????????????
                "IBLOCK_SECTION_ID" => $this->section_id,          // ??????? ????? ? ????? ???????
                "DATE_ACTIVE_FROM" => $date_from,
                "IBLOCK_ID"      => $this->iblock_id,
                "NAME"           => $item['title'],
                "ACTIVE"         => $this->active_element=="Y"?"Y":"N",            // ???????
                "PREVIEW_TEXT"   => $item['description'],
                "PREVIEW_TEXT_TYPE"  => $this->preview_text_type,
                "DETAIL_TEXT"    => $DETAIL_TEXT,
                "DETAIL_TEXT_TYPE"    => $this->detail_text_type,
                "CODE" => $code?$code:""
            );
            if(isset($md5))
            {
                $arLoadProductArray["XML_ID"] = $md5;
                unset($md5);    
            }
             

            if(empty($PREVIEW_IMG) && $this->preview_first_img=="Y") $PREVIEW_IMG = $this->filterSrc($this->parseImgFromRss($item));
            
            if($this->preview_first_img=="Y")
            {
                $this->convetCyrillic($PREVIEW_IMG);
                $arLoadProductArray['PREVIEW_PICTURE'] = CFile::MakeFileArray($PREVIEW_IMG);
            }
             
            if($this->detail_first_img=="Y")
            {
                $this->convetCyrillic($DETAIL_IMG);
                $arLoadProductArray['DETAIL_PICTURE'] = CFile::MakeFileArray($DETAIL_IMG);
            }
             
            if($this->date_public!="N" && $this->date_public_text)
            {
                $new_date = date($DB->DateFormatToPHP(FORMAT_DATETIME), $unix);
                $arLoadProductArray['PROPERTY_VALUES'][$this->date_public] = $new_date;
            }
            if($this->first_title!="N")$arLoadProductArray['PROPERTY_VALUES'][$this->first_title] = $this->first_title_text;
            if($this->meta_title!="N" && $this->meta_title_text)
            {
                if(isset($this->settings["loc"]["f_props"]) && $this->settings["loc"]["f_props"]=="Y")
                    $this->meta_title_text = $this->locText($this->meta_title_text);    
                $arLoadProductArray['PROPERTY_VALUES'][$this->meta_title] = $this->meta_title_text;    
            }
            if($this->meta_description!="N" && $this->meta_description)
            {
                if(isset($this->settings["loc"]["f_props"]) && $this->settings["loc"]["f_props"]=="Y")
                    $this->meta_description = $this->locText($this->meta_description);
                
                $arLoadProductArray['PROPERTY_VALUES'][$this->meta_description] = $this->meta_description_text;    
            }
            if($this->meta_keywords!="N" && $this->meta_keywords)
            {
                if(isset($this->settings["loc"]["f_props"]) && $this->settings["loc"]["f_props"]=="Y")
                    $this->meta_keywords = $this->locText($this->meta_keywords);
                
                $arLoadProductArray['PROPERTY_VALUES'][$this->meta_keywords] = $this->meta_keywords_text;    
            }
            
            
            $this->addSeoUniqYandex($arLoadProductArray);
            
            if($PRODUCT_ID = $el->Add($arLoadProductArray, false, $this->index_element=="Y"?true:false, $this->resize_image=="Y"?true:false)){
                $elem[]= ' '.$PRODUCT_ID;
            }
            elseif(!$this->agent){
                $result[ERROR][] = $el->LAST_ERROR;
            }

            $el = null;
            $isElement = null;
            if(isset($preview_html)){
                unset($preview_html);
            }
            if(isset($detail_html)){
                unset($detail_html);
            }
            unset($html);
            file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser".$this->id.".txt", $count."|".$ci);
            unset($fileHtml);
            if(self::TEST || $shs_DEMO==2)break;
            if($this->sleep && $this->sleep>0)sleep($this->sleep);

        }
        unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser".$this->id.".txt");
        if($elem){
            $message = implode(',', $elem);
            $message = GetMessage("parser_pars_el_ok").' '.$message.' '.GetMessage("parser_pars_create_ok");
        }
        if($first && !$this->agent){
            $result[SUCCESS][] = $message;
        }
        elseif(!$this->agent){
            $result[ERROR][] = GetMessage("parser_no");
        }
        if(!$this->agent)
        {
            if(isset($result[SUCCESS]) && count($result[SUCCESS])>0)
            {
                foreach($result['SUCCESS'] as $success) CAdminMessage::ShowMessage(array("MESSAGE"=>$success, "TYPE"=>"OK"));
            }
            if(isset($result[ERROR]) && count($result[ERROR])>0)
            {
                foreach($result['ERROR'] as $error) CAdminMessage::ShowMessage($error);
            }
        }
        return $result;
    }
    
    public function GetAuthForm($check=false)
    {   
        if(isset($this->settings[$this->typeN]["auth"]["type"]) && $this->settings[$this->typeN]["auth"]["type"]=="http")
        {
            if(isset($_POST["auth"]) && $_POST["auth"])
            {
                //printr($this);
                //$auth = new FileGetHtml();
                //$data = $auth->file_get_html($this->rss, $this->proxy, $this->auth, $this);
                //printr($auth);
            }
        
            return true;    
        }
        
        
        if(isset($this->settings[$this->typeN]["auth"]["active"]) && $this->settings[$this->typeN]["auth"]["active"]!="Y") return false;
        elseif(!isset($this->settings[$this->typeN]["auth"]["active"])) return false;
        elseif(isset($this->settings[$this->typeN]["auth"]["selector"]) && !$this->settings[$this->typeN]["auth"]["selector"])
        {
            $this->errors[] = GetMessage("parser_auth_error_selector");
            return false;    
        }
         
        
        $url = $this->settings[$this->typeN]["auth"]["url"]?$this->settings["catalog"]["auth"]["url"]:$this->rss;
        $form = $this->settings[$this->typeN]["auth"]["selector"];
        $proxy = $this->settings[$this->typeN]["proxy"];
        
        $auth = new FileGetHtml();
        $data = $auth->file_get_html($url, $proxy);
        $this->urlCatalog = $auth->headerUrl;
        $this->urlSite = $this->getCatalogUrlSite();
        
        $this->CheckAuthForm($data, $form, $proxy);
        
        if($check)if(isset($this->errors) && count(isset($this->errors))>0)
        {
            foreach($this->errors as $error)
            {
                if(isset($_POST["auth"]))CAdminMessage::ShowMessage($error);
            }
        }
        if($check)if(isset($this->success) && count(isset($this->success))>0)
        {
            foreach($this->success as $success)
            {
                if(isset($_POST["auth"]))CAdminMessage::ShowMessage(array("MESSAGE"=>$success, "TYPE"=>"OK"));    
            }
        }
            
    }
    
    public function CheckAuthform($data, $form, $proxy)
    {   
        $this->html = phpQuery::newDocument($data, "text/html;charset=".LANG_CHARSET); 
        //print pq($this->html)->html();
        $objForm = pq($this->html)->find($form);
        $url = $objForm->attr("action");
        $url = empty($url)?$this->urlCatalog:$this->getCatalogLink($url);
        
        $login = trim($this->settings[$this->typeN]["auth"]["login"]);
        $password = trim($this->settings[$this->typeN]["auth"]["password"]);
        foreach($this->html[$form." input"] as $input)
        {
            $name = trim(pq($input)->attr("name"));
            $value = trim(pq($input)->attr("value"));
            $type = trim(pq($input)->attr("type"));
            if(isset($this->settings[$this->typeN]["auth"]["password_name"]) && !empty($this->settings[$this->typeN]["auth"]["password_name"]) && $name==$this->settings[$this->typeN]["auth"]["password_name"])
            {
                //if($name==$this->settings[$this->type]["auth"]["password_name"])
                {
                    $arInput[$name] = $password;
                    continue;    
                }    
            }
            elseif(isset($this->settings[$this->typeN]["auth"]["login_name"]) && !empty($this->settings[$this->typeN]["auth"]["login_name"]) && $name==$this->settings[$this->typeN]["auth"]["login_name"])
            {
                //if($name==$this->settings[$this->type]["auth"]["login_name"])
                {
                    $arInput[$name] = $login;
                    continue;    
                }    
            }
            elseif($type=="password")
            {
                $arInput[$name] = $password;
                continue;        
            }
            elseif($type=="text" || $type=="email"){
                $arInput[$name] = $login;
                continue;    
            } 
            $arInput[$name] = $value;
        }
        if(isset($arInput))$this->doAuth($url, $arInput, $proxy);
        else{
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
        $form = $this->settings[$this->typeN]["auth"]["selector"];
        $this->html = phpQuery::newDocument($data, "text/html;charset=".LANG_CHARSET);
        $passw = false;
        foreach($this->html[$form." input"] as $input)
        {
            $type = pq($input)->attr("type");
            if($type=="password")
            {
                $passw = true;    
            }
        } 
        
        if($passw)
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
    //?????? ?? ????? ? ?????????
    protected function CheckPageNavigation($n)
    {
        if(!preg_match("/\d/", $n) || empty($n)) return false;
        if($this->currentPage>$n) return false;
        if($this->arPageNavigationDelta[0] && $this->arPageNavigationDelta[1])
        {
            if($n>=$this->arPageNavigationDelta[0] && $n<=$this->arPageNavigationDelta[1]) return $n;
        }elseif($this->arPageNavigationDelta[0] && !$this->arPageNavigationDelta[1])
        {
            if($n>=$this->arPageNavigationDelta[0]) return $n;
        }elseif(!$this->arPageNavigationDelta[0] && $this->arPageNavigationDelta[1])
        {
            if($n<=$this->arPageNavigationDelta[1]) return $n;
        }
        return false;
    }
    
    protected function CheckPageNavigationLess($n)
    {
        if(!preg_match("/\d/", $n) || empty($n)) return false;
        
        if($this->currentPage>$n) return false;

        if($this->arPageNavigationDelta[1])
        {
            if($n<=$this->arPageNavigationDelta[1]) return $n;
        }elseif(!$this->arPageNavigationDelta[1]) return true;
        return false;
    }

    protected function CheckValidatePageNavigation($n)
    {
        if($this->arPageNavigationDelta[0] && $this->arPageNavigationDelta[1])
        {
            if($n<=$this->arPageNavigationDelta[0] && $n<=$this->arPageNavigationDelta[1]) return true;
        }elseif($this->arPageNavigationDelta[0] && !$this->arPageNavigationDelta[1])
        {
            if($n<=$this->arPageNavigationDelta[0] && $n<=100000) return true;
        }elseif(!$this->arPageNavigationDelta[0] && $this->arPageNavigationDelta[1])
        {
            if($n<=$this->arPageNavigationDelta[1]) return true;
        }
    }

    protected function CheckOnePageNavigation()
    {
        if($this->settings["catalog"]["pagenavigation_begin"]==1 && $this->settings["catalog"]["pagenavigation_end"]==1)
        {
            return true;
        }elseif(!$this->settings["catalog"]["pagenavigation_selector"] && (!isset($this->settings["catalog"]["pagenavigation_var"]) || !$this->settings["catalog"]["pagenavigation_var"])) return true;
        
        return false;
    }
    
    protected function CheckAlonePageNavigation($n)
    {   
        if(!empty($this->settings["catalog"]["pagenavigation_begin"]) && !empty($this->settings["catalog"]["pagenavigation_end"]) && $this->settings["catalog"]["pagenavigation_end"]==$this->settings["catalog"]["pagenavigation_begin"] && $n==$this->settings["catalog"]["pagenavigation_begin"])
        {
            return true;
        }
        
        return false;
    }

    protected function IsNumberPageNavigation()
    {
        if(!$this->settings["catalog"]["pagenavigation_begin"] && !$this->settings["catalog"]["pagenavigation_end"]) return false;
        else return true;
    }
    
    protected function DeleteLog()
    {
        if($this->agent)unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog".$this->id.".txt");    
    }
    
    protected function SaveLog()
    {
        if($this->settings["catalog"]["log"]=="Y" && isset($this->errors) && count($this->errors)>0)
            file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_log_".$this->id.".txt", print_r($this->errors, true), FILE_APPEND);
            
        if(!isset($this->errors)) $this->errors = array();
        $this->debugErrors = array_merge($this->debugErrors, $this->errors);
    }

    protected function isUpdateElement()
    {
        $this->updateActive = false;
        if($this->settings["catalog"]["update"]["active"])
        {
            unset($this->settings["catalog"]["update"]["active"]);
            $this->updateActive = true;
            foreach($this->settings["catalog"]["update"] as $id=>$val)
            {
                if($val=="Y" || $val=="empty") $this->isUpdate[$id] = $val;
            }
            if(!isset($this->isUpdate) || !$this->isUpdate) $this->isUpdate = false;
        }else  $this->isUpdate = false;
    }

    protected function getUniqElement()
    {
        //if($this->settings["catalog"]["update"]["active"]=="Y")
        {
            $this->uniqFields["NAME"] = "NAME";
            $this->uniqFields["LINK"] = "LINK";

            if($this->settings["catalog"]["uniq"]["prop"])
            {
                unset($this->uniqFields["LINK"]);
                unset($this->uniqFields["NAME"]);
                $prop = $this->settings["catalog"]["uniq"]["prop"];
                $this->uniqFields[$prop] = $prop;
            }
            if($this->settings["catalog"]["uniq"]["name"])
            {
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
        if($this->isUpdate)
        {
            foreach($this->isUpdate as $id=>$val)
            {
                if($val!="empty") continue;
                if($id=="preview_img") $this->arSortUpdate[] = "PREVIEW_PICTURE";
                elseif($id=="detail_img") $this->arSortUpdate[] = "DETAIL_PICTURE";
                elseif($id=="preview_descr") $this->arSortUpdate[] = "PREVIEW_TEXT";
                elseif($id=="detail_descr") $this->arSortUpdate[] = "DETAIL_TEXT";
            }
        }    
    }

    protected function checkUniq()
    {   
        if($this->elementUpdate) return $this->elementUpdate;
        if(!isset($this->arSortUpdate)) $this->arSortUpdate = array();
        
        if(isset($this->uniqFields["LINK"]) && $this->uniqFields["LINK"] && isset($this->arFields["NAME"]) && $this->arFields["NAME"])
        {
            $uniq = md5($this->arFields["NAME"].$this->arFields["LINK"]);
            $isElement = CIBlockElement::GetList(Array(), array("XML_ID"=>$uniq, "IBLOCK_ID"=>$this->iblock_id), false, Array("nTopCount"=>1), array_merge(array("ID"), $this->arSortUpdate))->Fetch();
            $this->elementUpdate = $isElement["ID"];
            if($isElement)
            {
                $this->arEmptyUpdate = $isElement;
                return $isElement["ID"];
            }
            else return false;
        }else{
            if($this->settings["catalog"]["uniq"]["prop"])
            {
                $prop = $this->settings["catalog"]["uniq"]["prop"];
                if($this->arFields["PROPERTY_VALUES"][$prop])$arFields["PROPERTY_".$prop] = $this->arFields["PROPERTY_VALUES"][$prop];
            }
            if($this->settings["catalog"]["uniq"]["name"])
            {
                $prop = $this->settings["catalog"]["uniq"]["prop"];
                if($this->arFields["NAME"])$arFields["NAME"] = $this->arFields["NAME"];
            }
            if(count($arFields)==count($this->uniqFields))$isElement = CIBlockElement::GetList(Array(), array_merge(array("IBLOCK_ID"=>$this->iblock_id), $arFields), false, Array("nTopCount"=>1), array_merge(array("ID"), $this->arSortUpdate))->Fetch();
            $this->elementUpdate = $isElement["ID"];
            if($isElement)
            {
                $this->arEmptyUpdate = $isElement;
                return $isElement["ID"];
            }
            else return false;
        }

        return false;
    }
    
    protected function checkOfferUniq()
    {
        if(isset($this->elementOfferUpdate)) return $this->elementOfferUpdate;
        
        $uniq = "offer#".md5($this->arFields["NAME"].$this->arFields["LINK"]);
        $isElement = CIBlockElement::GetList(Array(), array("XML_ID"=>$uniq, "IBLOCK_ID"=>$this->offerArray["IBLOCK_ID"]), false, Array("nTopCount"=>1), array("ID"))->Fetch();
        $this->elementOfferUpdate = $isElement["ID"];
        if($isElement) return $isElement["ID"];
        else return false;
    }
    
    protected function checkOfferUniqTable($arFields=array())
    {
        if(isset($this->elementOfferUpdate)) return $this->elementOfferUpdate;
        
        $uniq = "offer#".md5($this->arFields["LINK"].$arFields["NAME"]);
        $isElement = CIBlockElement::GetList(Array(), array("XML_ID"=>$uniq, "IBLOCK_ID"=>$this->offerArray["IBLOCK_ID"]), false, Array("nTopCount"=>1), array("ID"))->Fetch();
        $this->elementOfferUpdate = $isElement["ID"];
        if($isElement) return $isElement["ID"];
        else return false;
    }

    protected function isCatalog()
    {
        $this->isOfferCatalog = false;
        $this->isOfferParsing = false;
        $this->iblockOffer = 0;
        if(CModule::IncludeModule('catalog') && ($this->iblock_id && CCatalog::GetList(Array("name" => "asc"), Array("ACTIVE"=>"Y", "ID"=>$this->iblock_id))->Fetch()))
        {
            if($this->settings["catalog"]["preview_price"] || $this->settings["catalog"]["detail_price"])
            {
                $this->isCatalog = true;
            }else $this->isCatalog = false;
        }else $this->isCatalog = false;
        if(CModule::IncludeModule('catalog') && isset($this->settings["catalog"]["cat_vat_price_offer"]) && $this->settings["catalog"]["cat_vat_price_offer"]=="Y")
        {
            $arIblock = CCatalogSKU::GetInfoByIBlock($this->iblock_id);
            if(is_array($arIblock) && !empty($arIblock) && $arIblock["PRODUCT_IBLOCK_ID"]!=0 && $arIblock["SKU_PROPERTY_ID"]!=0)
            {
                $this->isOfferCatalog = true;
                $this->offerArray = $arIblock;
                $this->isCatalog = true;            
            }else $this->isOfferCatalog = false;    
        }
        if(CModule::IncludeModule('catalog') && isset($this->settings["offer"]["load"]) && $this->settings["offer"]["load"])
        {
            if(!isset($this->settings["catalog"]["cat_vat_price_offer"]) || isset($this->settings["catalog"]["cat_vat_price_offer"]) && $this->settings["catalog"]["cat_vat_price_offer"]!="Y")
            {
                $arIblock = CCatalogSKU::GetInfoByIBlock($this->iblock_id);
            }
            
            if(is_array($arIblock) && !empty($arIblock) && $arIblock["PRODUCT_IBLOCK_ID"]!=0 && $arIblock["SKU_PROPERTY_ID"]!=0 && $arIblock["IBLOCK_ID"])
            {
                $this->offerArray = $arIblock;
                $this->isCatalog = true;
                $this->isOfferParsing = true;
                if($arIblock["IBLOCK_ID"] && $arIblock["PRODUCT_IBLOCK_ID"])
                    $this->iblockOffer = $arIblock["IBLOCK_ID"];
                           
            }else $this->isOfferParsing = false; 
        }
        
        
    }

    protected function CheckFields($settings)
    {   
        if(preg_match("/\D/", $settings["pagenavigation_begin"]) && $settings["pagenavigation_begin"]!="")
        {
            $this->errors[] = GetMessage("parser_error_pagenavigation_begin");
        }
        if(preg_match("/\D/", $settings["pagenavigation_end"]) && $settings["pagenavigation_end"]!="")
        {
            $this->errors[] = GetMessage("parser_error_pagenavigation_end");
        }
        if(preg_match("/\D/", $settings["step"]))
        {
            $this->errors[] = GetMessage("parser_error_step");
        }
        
        if(is_array($settings["price_updown"]))
        {
            foreach($settings["price_updown"] as $i=>$val)
            {
                if($settings["price_updown"][$i])
                {
                    if($settings["price_terms"][$i] && !self::isFloat($settings["price_terms_value"][$i]))
                    {       
                        $this->errors[] = GetMessage("parser_error_price_terms_value");    
                    }
                    if($settings["price_terms"][$i] && !self::isFloat($settings["price_terms_value_to"][$i]))
                    {       
                        $this->errors[] = GetMessage("parser_error_price_terms_value");    
                    } 
                    if($settings["price_updown"][$i] && !self::isFloat($settings["price_value"][$i]))
                    {
                        $this->errors[] = GetMessage("parser_error_price_value");
                    }   
                }    
            }    
        }
        
        
        

        $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$this->iblock_id));
        while ($prop_fields = $properties->GetNext())
        {
            $this->arProperties[$prop_fields["CODE"]] = $prop_fields;
        }
        
        if($this->iblockOffer)
        {
            $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$this->iblockOffer));
            while ($prop_fields = $properties->GetNext())
            {
                $this->arPropertiesOffer[$prop_fields["CODE"]] = $prop_fields;
            }    
        }

        $this->arSelectorProduct = $this->getSelectorProduct();
        $this->arFindProduct = $this->getFindProduct();
        $this->arSelectorProperties = $this->getSelectorProperties();
        $this->arSelectorPropertiesOffer = $this->getSelectorPropertiesOffer();
        $this->arFindProperties = $this->getFindProperties();
        $this->arFindPropertiesOffer = $this->getFindPropertiesOffer();
        $this->arDubleFindProperties = $this->getFindDubleProperties();
        $this->arDubleFindPropertiesOffer = $this->getFindDublePropertiesOffer();
        
        $this->arSelectorPropertiesPreview = $this->getSelectorPropertiesPreview();
        $this->arFindPropertiesPreview = $this->getFindPropertiesPreview();
        $this->arDubleFindPropertiesPreview = $this->getFindDublePropertiesPreview();
        //printr($this->ArDubleFindProperties);
    }
    
    protected function isFloat($n)
    {
        if(preg_match("/^(?:\+|\-)?(?:(?:\d+)|(?:\d+\.)|(?:\.\d+)|(?:\d+\.\d+)){1}(?:e(?:\+|\-)?\d+)?$/i", $n)) return true;
        else return false;
    }

    protected function GetArUrlSave()
    {
        $arrUrl = array();
        $this->section_array = array();
        
        if(isset($this->arUrl) && !empty($this->arUrl))
        {
            foreach($this->arUrl as $key => $url)
            {
                if(empty($url)) continue 1;
                $this->convetCyrillic($url);
                $arrUrl[] = $url;
                $this->section_array[$url] = $this->section_id;
            }
        }
        
        if(isset($this->settings["catalog"]["rss_dop"]) && !empty($this->settings["catalog"]["rss_dop"]))
        {
            foreach($this->settings["catalog"]["rss_dop"] as $key => $url)
            {
                if(empty($url)) continue 1;
                $this->convetCyrillic($url);
                $arrUrl[] = $url;
                $this->section_array[$url] = $this->settings["catalog"]["section_dop"][$key];
            }
        }
        if(!empty($arrUrl)) return $arrUrl;
        return false;
    }
    
    protected function SaveParseSection($rss)
    {   
        if(isset($this->section_array[$rss]) && !empty($this->section_array[$rss]))
        {
            file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_section".$this->id.".txt", $this->section_array[$rss]);
        }
    }
    
    protected function parseCatalog()
    {   
        set_time_limit(0);
        $this->ClearAjaxFiles();
        $this->DeleteLog();
        $this->checkActionBegin();
        $this->arUrl = array();
        if(isset($this->settings["catalog"]["url_dop"]) && !empty($this->settings["catalog"]["url_dop"]))$this->arUrl = explode("\r\n", $this->settings["catalog"]["url_dop"]);
        
        $this->arUrl = array_merge(array($this->rss), $this->arUrl);
        $this->arUrl = $this->GetArUrlSave();
        $this->arUrlSave = $this->arUrl;
        if(!$this->PageFromFile()) return false;
        $this->CalculateStep();
        if($this->settings["catalog"]["mode"]!="debug" && !$this->agent)
        {
            $this->arUrlSave = array($this->rss);   
        } 
        else
        {
            $this->arUrlSave = $this->arUrl;
        } 
        
        //if(($this->arUrlSave === false) || !is_array($this->arUrlSave)) return false;
        //if(!$this->connectCatalogPage($this->rss));
        //return;
        foreach($this->arUrlSave as $rss):
            $rss = trim($rss);
            if(empty($rss)) continue;
            
            $this->rss = $rss;
            $this->convetCyrillic($this->rss); 
            $this->connectCatalogPage($this->rss);
            $this->SaveParseSection($this->rss);
            if(!$this->agent && $this->settings["catalog"]["mode"]!="debug" && isset($this->errors) && count($this->errors)>0)
            {
                $this->SaveLog();
                unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt");
                unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_copy_page".$this->id.".txt");
                return false;    
            }
        
            $this->parseCatalogNavigation($this->rss);
            $n = $this->currentPage;
            if(!$this->IsNumberPageNavigation())
            {   
                $this->parseCatalogProducts();
            }elseif($this->IsNumberPageNavigation() && $this->CheckPageNavigation($n))
            {   
                $this->parseCatalogProducts();
            }elseif($this->settings["catalog"]["mode"]!="debug" && !$this->agent)
            {
                $this->stepStart = true;
                $this->SavePrevPage($this->rss);    
            }
         
            $this->SaveCurrentPage($this->pagenavigation);
            if($this->stepStart)
            {
                if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt"))
                    unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt");
                $this->DeleteCopyPage();
            } 
            if((!$this->CheckOnePageNavigation() && $this->agent) || (!$this->CheckOnePageNavigation() && !$this->agent && $this->settings["catalog"]["mode"]=="debug"))$this->parseCatalogPages();
            if($this->CheckOnePageNavigation() && $this->stepStart)
            {   
                if($this->IsEndSectionUrl())$this->ClearBufferStop();
                else $this->ClearBufferStep();
                return false;    
            }
        endforeach;
        
        $this->checkActionAgent();

        if($this->agent || $this->settings["catalog"]["mode"]=="debug"){
            foreach(GetModuleEvents("shs.parser", "EndPars", true) as $arEvent)
                ExecuteModuleEventEx($arEvent, array($this->id));
        }
    }
    
    protected function PageFromFile()
    {
        if($this->settings["catalog"]["mode"]=="debug" || $this->agent || $_GET["begin"]) return true;
        $prevPage = $prevElement = $currentPage = 0;
        if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_prev_page".$this->id.".txt"))
            $prevPage = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_prev_page".$this->id.".txt");
        if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_prev_element".$this->id.".txt"))
            $prevElement = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_prev_element".$this->id.".txt");
        if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_current_page".$this->id.".txt"))
            $currentPage = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_current_page".$this->id.".txt");
        
        if($prevPage)
        {
            $arPrevPage = explode("|", $prevPage);
            $arPrevElement = explode("|", $prevElement);
            $arCurrentPage = explode("|", $currentPage);    
        }else{
            $arPrevPage = array();
            $arCurrentPage = array();
        }
        
        if(isset($arPrevElement) && is_array($arPrevElement))foreach($arPrevElement as $i=>$p)
        {
            $p = trim($p);
            if(empty($p)) continue;
            $this->pagePrevElement[$p] = $p;
        }
        
        if(!$_GET["begin"] && !$prevPage) return true;
        
        if(isset($arPrevPage) && is_array($arPrevPage))foreach($arPrevPage as $i=>$p)
        {
            $p = trim($p);
            if(empty($p)) continue;
            $this->pagenavigationPrev[$p] = $p;
        }
        
        
        if(isset($arCurrentPage) && is_array($arCurrentPage))foreach($arCurrentPage as $p)
        {
            $p = trim($p);
            if(empty($p)) continue;
            $this->pagenavigation[$p] = $p;    
        }
        
        if(isset($this->pagenavigationPrev) && is_array($this->pagenavigationPrev))foreach($this->pagenavigationPrev as $i=>$v)
        {
            foreach($this->pagenavigation as $i1=>$v1)
            {
                if($v1==$v) unset($this->pagenavigation[$i1]);   
            }
        }
        
        if(isset($this->pagenavigation) && is_array($this->pagenavigation))foreach($this->pagenavigation as $p)
        {
            $isContinue = true;
            $this->rss = $p;
            break;
        }
        if(!$isContinue && !empty($this->pagenavigationPrev) && $this->IsEndSectionUrl())
        {
                 
            //if($this->IsEndSectionUrl())
            $this->ClearBufferStop();
            //else $this->ClearBufferStep();
            return false;
        }elseif(!$isContinue && !empty($this->pagenavigationPrev) && !$this->IsEndSectionUrl())
        {
            $isContinue = true;
            $this->rss = $this->GetUrlRss();        
        }
         
        $this->currentPage = count($this->pagenavigationPrev);
        if($this->IsNumberPageNavigation() && $this->CheckPageNavigation($this->currentPage))
        {
            
            $this->activeCurrentPage = $this->currentPage-$this->arPageNavigationDelta[0]+1;
        }elseif(!$this->IsNumberPageNavigation()) $this->activeCurrentPage = $this->currentPage;
        return true;
    }
    
    protected function IsEndSectionUrl()
    {
        if(empty($this->arUrl)) return true;
        if((!isset($this->settings["catalog"]["url_dop"]) || empty($this->settings["catalog"]["url_dop"])) && (!isset($this->settings["catalog"]["rss_dop"]) || empty($this->settings["catalog"]["rss_dop"]))) return true;
        
        $count = 0;
        foreach($this->arUrl as $i=>$url)
        {
            if(isset($this->pagenavigationPrev[$url])) $count++;   
        }
        if($count==count($this->arUrl)) return true;
         
        else return false;
    }
    
    protected function GetUrlRss()
    {
        foreach($this->arUrl as $i=>$url)
        {
            if(isset($this->pagenavigationPrev[$url])) continue;
            return $url;
        }        
    }
    
    protected function ClearBufferStop()
    {
        if(!$this->agent && $this->settings["catalog"]["mode"]!="debug")
        {
            global $APPLICATION;
            //if(self::TEST==0)
            $APPLICATION->RestartBuffer();
            $this->checkActionAgent(false);
            foreach(GetModuleEvents("shs.parser", "EndPars", true) as $arEvent)
                ExecuteModuleEventEx($arEvent, array($this->id));

            die("stop");    
        }
    }
    
    protected function ClearBufferStep()
    {
        if(!$this->agent && $this->settings["catalog"]["mode"]!="debug")
        {
            global $APPLICATION;
            if(self::TEST==0)$APPLICATION->RestartBuffer();
            die();    
        }
    }

    protected function parseCatalogProducts()
    {
        $count = 0;
        
        $this->activeCurrentPage++;
        $this->SetCatalogElementsResult($this->activeCurrentPage);
        
        $element = $this->settings["catalog"]["selector"];

        if($this->preview_delete_element)$this->deleteCatalogElement($this->preview_delete_element, $element, $this->html[$element]);
        if($this->preview_delete_attribute)$this->deleteCatalogAttribute($this->preview_delete_attribute, $element, $this->html[$element]);
        $i = 0;
        $ci = 0;
        foreach($this->html[$element] as $el)
        {
            $count++;
        }

        if($this->settings["catalog"]["mode"]!="debug" && !$this->agent)
        {
            if($count>$this->settings["catalog"]["step"] && ($this->settings["catalog"]["mode"]!="debug" && !$this->agent))
                $countStep = $this->settings["catalog"]["step"];
            else{
                $this->stepStart = true;
                if($this->CheckOnePageNavigation() || $this->CheckAlonePageNavigation($this->currentPage)) $this->pagenavigation[$this->rss] = $this->rss;
                $this->SaveCurrentPage($this->pagenavigation);
                $this->SavePrevPage($this->sectionPage);
                $countStep = $count;
            }    
        }else{
            $countStep = $count;    
        }
            
        file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser".$this->id.".txt", $countStep."|".$ci);    
        
        if($count==0)
        {
            $this->errors[] = GetMessage("parser_error_selector_notfound")."[".$element."]";
            $this->clearFields();
            //die();
        }

        foreach($this->html[$element] as $el)
        {
            $ci++;
            if($this->StepContinue($ci, $count)) continue;
            if ($this->typeN == "xml") $debug_item = SotbitXmlParser::DEFAULT_DEBUG_ITEM;
            else $debug_item = self::DEFAULT_DEBUG_ITEM;
            if($i==$debug_item && $this->settings["catalog"]["mode"]=="debug") break;
            if($this->typeN=="catalog")
            {
               $this->parseCatalogProductElement($el); 
            }
            if($this->typeN=="xml")
            {
                $this->parseCatalogProductElementXml($el);
            }
            $i++;
            file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser".$this->id.".txt", $countStep."|".$i);
            $this->CalculateStep($count);
            
        }
        unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser".$this->id.".txt");
    }

    protected function parseCatalogProductElement(&$el)
    {
        $this->countItem++;
        if(!$this->parserCatalogPreview($el))
        {
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


        $db_events = GetModuleEvents("shs.parser", "parserBeforeAddElementCatalog", true); //27.10.2015
        $error = false;
        foreach($db_events as $arEvent)
        {
            $bEventRes = ExecuteModuleEventEx($arEvent, array(&$this, &$el));
            if($bEventRes===false)
            {
                $error = true;
                break 1;
            }
        }

        if(!$error && !$error_isad) //27.10.2015
        {
            $this->AddElementCatalog();
            foreach(GetModuleEvents("shs.parser", "parserAfterAddElementCatalog", true) as $arEvent)
                ExecuteModuleEventEx($arEvent, array(&$this, &$el));
        }

        if($this->isCatalog && $this->elementID)
        {   
            /*if($this->boolOffer)
            {
                return true;    
            }*/
            
            
            
            if($this->isOfferCatalog && !$this->boolOffer)
            {                                  
                $this->AddElementOfferCatalog();
                $this->elementID = $this->elementOfferID;
                $this->elementUpdate = $this->elementOfferUpdate;
            }
            if($this->boolOffer)
            {
                $this->addProductPriceOffers();
            }else{
                
                $this->AddProductCatalog();
                $this->AddMeasureCatalog();
                $this->AddPriceCatalog();
                $this->addAvailable();    
            }
            
        }/*else{
            $this->AddElementOfferCatalog();
            $this->AddProductCatalog();
            $this->AddMeasureCatalog();
            $this->AddPriceCatalog();    
        }*/

        $this->SetCatalogElementsResult();
        $this->clearFilesTemp();
        $this->clearFields();
        
    }
    
    protected function addProductPriceOffers()
    {
        if(isset($this->arOfferAll))
        {
            if(isset($this->arOfferAll["FIELDS"]) && !empty($this->arOfferAll["FIELDS"]))
            {
                foreach($this->arOfferAll["FIELDS"] as $i=>$field)
                {
                    $this->AddElementOfferCatalogTable($field);
                    $arPrice = $this->arOfferAll["PRICE"][$i];
                    $arQuantity = $this->arOfferAll["QUANTITY"][$i];
                    $this->arPrice = $arPrice;
                    $this->AddProductCatalogOffer($field);
                    $this->AddMeasureCatalogOffer($field);
                    $this->AddPriceCatalogOffer($arPrice, $field);
                    $this->AddQuantityCatalogOffer($arQuantity, $field);
                    
                    if(isset($this->elementOfferID))
                        unset($this->elementOfferID);
                        
                    if(isset($this->elementOfferUpdate))
                        unset($this->elementOfferUpdate);
                }
            }
        
            
        }
        
        //printr($this->arOfferAll);
    }
    
    protected function AddElementOfferCatalogTable($arFields)
    {
        if($this->checkOfferUniqTable($arFields) && !$this->isUpdate) return false;
        $el = new CIBlockElement;
        $isElement = $this->checkOfferUniqTable($arFields);
        $arFields["IBLOCK_ID"] = $this->iblockOffer;
        $arFields["PROPERTY_VALUES"][$this->offerArray["SKU_PROPERTY_ID"]] = $this->elementID; 
        if(!$isElement)
        {   

            $id = $el->Add($arFields, "N", $this->index_element, $this->resize_image);
            if(!$id)
            {   
                $this->errors[] = GetMessage("parser_offer_name").$arFields["NAME"]."[".$this->arFields["LINK"]."] - ".$el->LAST_ERROR;
            }else{
                $this->elementOfferID = $id;
                $this->addTmp($id);    
            } 
        }else{
            $this->elementOfferID = $isElement;
            $el->Update($isElement, $arFields);
            $this->addTmp($isElement);
        } 
       
        unset($el);    
    }
    
    protected function StepContinue($n, $count=0)
    {
        if($this->settings["catalog"]["mode"]=="debug" || $this->agent) return false;
        $step = (int)$this->settings["catalog"]["step"];
        if($step>$count && $count>0) return false;
        $file = 0;
        
        if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt"))
            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt");
        if($file)
        {
            $arFile = explode("|", $file);
            $countElement = (int)$arFile[0];
            $currentElement = (int)$arFile[1];    
        }else{
            return false;
        }

        if($currentElement>0 && $n<=$currentElement && $currentElement%$step==0) return true;
        else return false;    
    }
    
    protected function CalculateStep($count = 0)
    {           

        if($this->settings["catalog"]["mode"]=="debug" || $this->agent || $this->stepStart) return true;
        $step = $this->settings["catalog"]["step"];
        if($step>$count && $count>0)
        {
            $this->stepStart = true;
            return true;    
        }
        $file = 0;
        if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt"))  
            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt");
        if($file)
        {
            $arFile = explode("|", $file);
            $countElement = (int)$arFile[0];
            $currentElement = (int)$arFile[1];    
        }else{
            $countElement = $count;
            $currentElement = 0;   
        }
        if($countElement-$currentElement<=$step && $countElement>0 && $count==0)
        {
            $this->stepStart = true;
        }
         
        if($count==0) return true;
        $currentElement++;
        file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt", $countElement."|".$currentElement);
        if($currentElement%$step==0 && !$this->stepStart)
        {
            $this->clearFields();
            $this->ClearBufferStep();
        }
        
            
    }
    
    protected function SetCatalogElementsResult($page=false)
    {
        $file = 0;
        if(file_exists(($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog".$this->id.".txt")))
            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog".$this->id.".txt");
        
        if($file)
        {
            $arFile = explode("|", $file);
            
            $countPage = (int)$arFile[1];
            $ciElement = (int)$arFile[2];
            $errorElement = (int)$arFile[3];
            $allError = (int)$arFile[4];
        }
        else{
            $countPage = 0;
            $ciElement = 0;
            $errorElement = 0;
            $allError = 0; 
        }
        
        if($page)
        {
            $countPage = $page;    
        }elseif(isset($this->elementID)){
            $ciElement++;
            if(isset($this->errors) && count($this->errors))$errorElement++;
            $this->SavePrevPageDetail($this->arFields["LINK"]);    
        }
        if(isset($this->errors) && count($this->errors)>0)$allError = $allError+count($this->errors);
        file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog".$this->id.".txt", "|".$countPage."|".$ciElement."|".$errorElement."|".$allError."|".$this->countSection);
    }
    /*?????????? ? ????? ?????? ????????*/
    protected function SaveCatalogError()
    {
        $file = 0;
        if(file_exists(($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog".$this->id.".txt")))
            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog".$this->id.".txt");
        
        if($file)
        {
            $arFile = explode("|", $file);
            
            $countPage = (int)$arFile[1];
            $ciElement = (int)$arFile[2];
            $errorElement = (int)$arFile[3];
            $allError = (int)$arFile[4];
        }
        else{
            $countPage = 0;
            $ciElement = 0;
            $errorElement = 0;
            $allError = 0; 
        }
        if(isset($this->elementID)){
            if(isset($this->errors) && count($this->errors))$errorElement++;
        }
        if(isset($this->errors) && count($this->errors)>0)$allError = $allError+count($this->errors);
        file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog".$this->id.".txt", "|".$countPage."|".$ciElement."|".$errorElement."|".$allError."|".$this->countSection);    
    }
    /*???????? ??? ?????*/
    protected function parseCatalogAllFields()
    {
        if($this->updateActive && isset($this->settings["catalog"]["uniq"]["action"]) && $this->settings["catalog"]["uniq"]["action"]=="A")
            $this->arFields["ACTIVE"] = $this->active_element;
        
        if($this->checkUniq()) return false;
        $this->arFields["IBLOCK_ID"] = $this->iblock_id;
        $this->arFields["ACTIVE"] = $this->active_element;
        if($this->code_element=="Y")
        {
            $this->arFields["CODE"] = $this->getCodeElement($this->arFields["NAME"]);
        }

        if($this->uniqFields["LINK"])
        {
            $uniq = md5($this->arFields["NAME"].$this->arFields["LINK"]);
            $this->arFields["XML_ID"] = $uniq;
        }
        
        if($this->date_active=="NOW") $this->arFields["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "SHORT");
        elseif($this->date_active=="NOW_TIME") $this->arFields["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "FULL");
        //elseif($this->date_active=="PUBLIC" && $unix) $this->arFields["DATE_ACTIVE_FROM"] = ConvertTimeStamp($unix, "FULL");
    }

    protected function AddElementCatalog()
    {
        if($this->checkUniq() && !$this->isUpdate) return false;
        $el = new CIBlockElement;   
        $isElement = $this->checkUniq();
        $this->boolUpdate = true;

        if(!$isElement)
        {   
            if($this->settings["catalog"]["update"]["add_element"] == "Y")
            {
                $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."] - ".GetMessage("parser_error_id_not_add_element");
                return false;
            }
            $id = $el->Add($this->arFields, "N", $this->index_element, $this->resize_image);
            if(!$id)
            {   
                $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."] - ".$el->LAST_ERROR;
            }else{
                $this->elementID = $id; 
                $this->addTmp($id);
                $this->addSeoUniqYandex($this->arFields);   
            } 
        }else{
             $this->clearFieldsUpdate();
             $this->elementID = $isElement;
             $el->Update($isElement, $this->arFields);
             $this->arFields["NAME"] = $this->elementName;
             $this->addTmp($isElement);
        }
        
        unset($el);
    }
    
    protected function AddElementOfferCatalog()
    {   
        if($this->elementUpdate && !$this->isUpdate) return false;
        $el = new CIBlockElement;  
        
        $isElement = $this->checkOfferUniq(); 
        if(!$isElement)
        {   
            $this->arOfferFields["XML_ID"] = "offer#".md5($this->arFields["NAME"].$this->arFields["LINK"]);
            $this->arOfferFields["NAME"] = $this->arFields["NAME"];
            $this->arOfferFields["IBLOCK_ID"] = $this->offerArray["IBLOCK_ID"];
            $this->arOfferFields["PROPERTY_VALUES"][$this->offerArray["SKU_PROPERTY_ID"]] = $this->elementID;
            
            $id = $el->Add($this->arOfferFields, "N", $this->index_element, $this->resize_image); 
            if(!$id)
            {   
                $this->errors[] = GetMessage("parser_offer_name").$this->arOfferFields["NAME"]."[".$this->arFields["LINK"]."] - ".$el->LAST_ERROR;
            }else{
                $this->elementOfferID = $id;
                $this->addTmp($id);    
            } 
        }else{
            $this->elementOfferID = $isElement;
            $this->addTmp($isElement);    
        } 
        
        /*else{
             $this->clearFieldsUpdate();

             $this->elementID = $isElement;
             $el->Update($isElement, $this->arOfferFields);
        }*/
        unset($el);
    }
    
    protected function addSeoUniqYandex($arFields)
    {
        if(isset($this->settings["loc"]["uniq"]["domain"]) && !empty($this->settings["loc"]["uniq"]["domain"]) && isset($arFields["DETAIL_TEXT"]) && !empty($arFields["DETAIL_TEXT"]) && strlen($arFields["DETAIL_TEXT"])>=500)
        {
            $textContent = $arFields["DETAIL_TEXT"];
            $engine = new Engine\Yandex();
            $domain = $this->settings["loc"]["uniq"]["domain"];
            try
            {
                $res = $engine->addOriginalText($textContent, $domain);
            }catch(Engine\YandexException $e)
            {   
                $this->errors[] = $e->getMessage();
            }
            
            unset($engine);
        }
    }
                           
    protected function clearFieldsUpdate()
    {

        $this->arEmptyUpdate["PREVIEW_TEXT"] = trim($this->arEmptyUpdate["PREVIEW_TEXT"]);
        $this->arEmptyUpdate["DETAIL_TEXT"] = trim($this->arEmptyUpdate["DETAIL_TEXT"]);
        if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["props"]))
        {
            unset($this->arFields["PROPERTY_VALUES"]);
        }
        if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["preview_descr"] || (in_array("PREVIEW_TEXT", $this->arSortUpdate) && !empty($this->arEmptyUpdate["PREVIEW_TEXT"]))))
        {
            unset($this->arFields["PREVIEW_TEXT"]);
            unset($this->arFields["PREVIEW_TEXT_TYPE"]);
        }
        if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["detail_descr"] || (in_array("DETAIL_TEXT", $this->arSortUpdate) && !empty($this->arEmptyUpdate["DETAIL_TEXT"]))))
        {
            unset($this->arFields["DETAIL_TEXT"]);
            unset($this->arFields["DETAIL_TEXT_TYPE"]);
        }
        if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["preview_img"] || (in_array("PREVIEW_PICTURE", $this->arSortUpdate) && !empty($this->arEmptyUpdate["PREVIEW_PICTURE"]))))
        {
            unset($this->arFields["PREVIEW_PICTURE"]);
        }
        if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["detail_img"] || (in_array("DETAIL_PICTURE", $this->arSortUpdate) && !empty($this->arEmptyUpdate["DETAIL_PICTURE"]))))
        {
            unset($this->arFields["DETAIL_PICTURE"]);
        }
        $this->elementName = $this->arFields["NAME"];
        if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["name"]))
        {
            unset($this->arFields["NAME"]);
        }
        if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["price"]))
        {
            unset($this->arPrice);
        }
        if($this->checkUniq())
        {
            $code = $this->settings["catalog"]["more_image_props"];
            unset($this->arFields["PROPERTY_VALUES"][$code]);
        }

    }
    
    protected function AddMeasureCatalogOffer()
    {
        if($this->elementOfferUpdate) return false;
        $info = CModule::CreateModuleObject('catalog');
        if(!CheckVersion("14.0.0", $info->MODULE_VERSION))
        {
            if($this->settings["catalog"]["koef"]>0)
            {
                $arMes = array("RATIO"=>$this->settings["catalog"]["koef"], "PRODUCT_ID"=>$this->elementOfferID);
                $str_CAT_MEASURE_RATIO = 1;
                $CAT_MEASURE_RATIO_ID = 0;
                $db_CAT_MEASURE_RATIO = CCatalogMeasureRatio::getList(array(), array("PRODUCT_ID" => $this->elementOfferID));
                if($ar_CAT_MEASURE_RATIO = $db_CAT_MEASURE_RATIO->Fetch())
                {
                    $str_CAT_MEASURE_RATIO = $ar_CAT_MEASURE_RATIO["RATIO"];
                    $CAT_MEASURE_RATIO_ID =  $ar_CAT_MEASURE_RATIO["ID"];
                }
                if($CAT_MEASURE_RATIO_ID>0)
                {
                    if(!CCatalogMeasureRatioAll::Update($CAT_MEASURE_RATIO_ID, $arMes))
                    {
                        $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."] ".GetMessage("parser_error_ratio");
                    }
                }
                else{
                    if(!CCatalogMeasureRatio::add($arMes))
                    {
                        $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."] ".GetMessage("parser_error_ratio");
                    }
                }
                
            }
        }
    }

    protected function AddMeasureCatalog()
    {
        if($this->elementUpdate) return false;
        $info = CModule::CreateModuleObject('catalog');
        if(!CheckVersion("14.0.0", $info->MODULE_VERSION))
        {
            if($this->settings["catalog"]["koef"]>0)
            {
                $arMes = array("RATIO"=>$this->settings["catalog"]["koef"], "PRODUCT_ID"=>$this->elementID);
                $str_CAT_MEASURE_RATIO = 1;
                $CAT_MEASURE_RATIO_ID = 0;
                $db_CAT_MEASURE_RATIO = CCatalogMeasureRatio::getList(array(), array("PRODUCT_ID" => $this->elementID));
                if($ar_CAT_MEASURE_RATIO = $db_CAT_MEASURE_RATIO->Fetch())
                {
                    $str_CAT_MEASURE_RATIO = $ar_CAT_MEASURE_RATIO["RATIO"];
                    $CAT_MEASURE_RATIO_ID =  $ar_CAT_MEASURE_RATIO["ID"];
                }
                if($CAT_MEASURE_RATIO_ID>0)
                {
                    if(!CCatalogMeasureRatioAll::Update($CAT_MEASURE_RATIO_ID, $arMes))
                    {
                        $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."] ".GetMessage("parser_error_ratio");
                    }    
                }
                else{
                    if(!CCatalogMeasureRatio::add($arMes))
                    {
                        $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."] ".GetMessage("parser_error_ratio");
                    }    
                }
                
            }
        }
    }

    protected function AddProductCatalog()
    {
        if($this->elementUpdate && (!$this->isUpdate || !$this->isUpdate["param"])) return false;
        $this->arProduct["MEASURE"] = $this->settings["catalog"]["measure"];
        $this->arProduct["VAT_ID"] = $this->settings["catalog"]["cat_vat_id"];
        $this->arProduct["VAT_INCLUDED"] = $this->settings["catalog"]["cat_vat_included"];
        $this->arProduct["ID"] = $this->elementID;

        $isElement = $this->elementUpdate;
        if(!$isElement)
        {
            if(!CCatalogProduct::Add($this->arProduct))
            {
                $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."] ".GetMessage("parser_error_add_product");
            }
        }else{
            $this->UpdateProductCatalog($isElement);
        }

    }
    
    protected function AddProductCatalogOffer($arFields)
    {
        if($this->elementOfferUpdate && (!$this->isUpdate || !$this->isUpdate["param"])) return false;
        $this->arProduct["MEASURE"] = $this->settings["catalog"]["measure"];
        $this->arProduct["VAT_ID"] = $this->settings["catalog"]["cat_vat_id"];
        $this->arProduct["VAT_INCLUDED"] = $this->settings["catalog"]["cat_vat_included"];
        $this->arProduct["ID"] = $this->elementOfferID;

        $isElement = $this->elementOfferUpdate;
        if(!$isElement)
        {
            if(!CCatalogProduct::Add($this->arProduct))
            {
                $this->errors[] = $this->arFields["NAME"]." - ".$arFields["NAME"]."[".$this->arFields["LINK"]."] ".GetMessage("parser_error_add_product_offer");
            }
        }else{
            $this->UpdateProductCatalogOffer($isElement, $arFields);
        }

    }
    
    protected function UpdateProductCatalogOffer($productID, $arFields)
    {
        if(!$productID){
            $this->errors[] = $this->arFields["NAME"]."-".$arFields["NAME"]."[".$this->arFields["LINK"]."] ".GetMessage("parser_error_update_product_offer");
            return false;
        }
        CCatalogProduct::Update($productID, $this->arProduct);
    } 
    
    protected function UpdateProductCatalog($productID)
    {
        if(!$productID){
            $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."] ".GetMessage("parser_error_update_product");
            return false;
        }
        CCatalogProduct::Update($productID, $this->arProduct);
    }
    
    protected function AddProductCatalogOffers()
    {
        
    }
    
    protected function clearFilesTemp()
    {
        if(!isset($this->arrFilesTemp) || count($this->arrFilesTemp) == 0) return false;
        foreach($this->arrFilesTemp as $id => $path)
        {
            if(file_exists($path))
            {
                unlink($path);
            }
        }
    }
    
    protected function ConvertCurrency()
    {
        if($this->settings["catalog"]["convert_currency"])
        {
            $this->arPrice["CURRENCY"] = $this->settings["catalog"]["convert_currency"];
            $this->arPrice["PRICE"] = CCurrencyRates::ConvertCurrency($this->arPrice["PRICE"], $this->settings["catalog"]["currency"], $this->settings["catalog"]["convert_currency"]);    
        }    
    }
    
    protected function ChangePrice()
    {
        if(is_array($this->settings["catalog"]["price_updown"]) && count($this->settings["catalog"]["price_updown"])>0)
        {
            foreach($this->settings["catalog"]["price_updown"] as $i=>$val)
            {
                if($this->settings["catalog"]["price_updown"][$i] && $this->settings["catalog"]["price_value"][$i])
                {
                    if($this->typeN == "catalog")
                    {
                        if($this->settings["catalog"]["price_updown_section_dop"][$i] != "section_all")
                        {
                            if($current_section = $this->GetCatalogSectionId())
                            {
                                if($current_section != $this->settings["catalog"]["price_updown_section_dop"][$i])
                                {
                                    continue 1;    
                                }
                            }
                            else
                            {
                                if($this->section_id != $this->settings["catalog"]["price_updown_section_dop"][$i])
                                {
                                    continue 1;
                                }
                            }
                        }
                    }
                    if($this->settings["catalog"]["price_terms"][$i]=="delta")
                    {
                        if(empty($this->settings["catalog"]["price_terms_value"][$i]) && !empty($this->settings["catalog"]["price_terms_value_to"][$i]))
                        {
                            if($this->arPrice["PRICE"]>$this->settings["catalog"]["price_terms_value_to"][$i]) continue;
                        }
                        
                        if(!empty($this->settings["catalog"]["price_terms_value"][$i]) && empty($this->settings["catalog"]["price_terms_value_to"][$i]))
                        {
                            if($this->arPrice["PRICE"]<$this->settings["catalog"]["price_terms_value"][$i]) continue;
                        }

                        if(!empty($this->settings["catalog"]["price_terms_value"][$i]) && !empty($this->settings["catalog"]["price_terms_value_to"][$i]))
                        {
                            if($this->arPrice["PRICE"]<$this->settings["catalog"]["price_terms_value"][$i] || $this->arPrice["PRICE"]>$this->settings["catalog"]["price_terms_value_to"][$i]) continue;
                        }
                    }
                    if($this->settings["catalog"]["price_type_value"][$i]=="percent")
                    {
                        $delta = $this->arPrice["PRICE"]*$this->settings["catalog"]["price_value"][$i]/100; 
                    }else{
                        $delta = $this->settings["catalog"]["price_value"][$i]; 
                    }
                    if($this->settings["catalog"]["price_updown"][$i]=="up")
                    {   
                        $this->arPrice["PRICE"] += $delta;
                    }
                    elseif($this->settings["catalog"]["price_updown"][$i]=="down")
                    {    
                        $this->arPrice["PRICE"] -= $delta;
                    }
                    break;
                }
            }
        }else{
            if($this->settings["catalog"]["price_updown"] && $this->settings["catalog"]["price_value"])
            { 
                    if($this->typeN == "catalog")
                    {
                        if($this->settings["catalog"]["price_updown_section_dop"] != "section_all")
                        {
                            if($current_section = $this->GetCatalogSectionId())
                            {
                                if($current_section != $this->settings["catalog"]["price_updown_section_dop"])
                                {
                                    return false;    
                                }
                            }
                            else
                            {
                                if($this->section_id != $this->settings["catalog"]["price_updown_section_dop"])
                                {
                                    return false;
                                }
                            }
                        }  
                    }
                    if($this->settings["catalog"]["price_terms"]=="up" && $this->settings["catalog"]["price_terms_value"])
                    {
                        if($this->arPrice["PRICE"]<$this->settings["catalog"]["price_terms_value"]) return false;
                    }
                    if($this->settings["catalog"]["price_terms"]=="down" && $this->settings["catalog"]["price_terms_value"])
                    {
                        if($this->arPrice["PRICE"]>$this->settings["catalog"]["price_terms_value"]) return false;
                    }
            
                    if($this->settings["catalog"]["price_type_value"]=="percent")
                    {
                        $delta = $this->arPrice["PRICE"]*$this->settings["catalog"]["price_value"]/100; 
                    }else{
                        $delta = $this->settings["catalog"]["price_value"];
                    }
                    if($this->settings["catalog"]["price_updown"]=="up")
                    {
                        $this->arPrice["PRICE"] += $delta;
                    }
                    elseif($this->settings["catalog"]["price_updown"]=="down")
                    {
                        $this->arPrice["PRICE"] -= $delta;
                    }
            }    
        }

    }

    protected function AddPriceCatalog()
    {
        if($this->elementUpdate && (!$this->isUpdate || !$this->isUpdate["price"])) return false;

        if(!$this->arPrice || !$this->arPrice["PRICE"]) return false;
        $isElement = $this->elementUpdate;
        $this->arPrice["PRODUCT_ID"] = $this->elementID;
        $this->ChangePrice();
        $this->ConvertCurrency();
        $this->arPrice["PRICE"] = $this->parseCatalogPriceOkrug($this->arPrice["PRICE"]);
        $obPrice = new CPrice();
        if(!$isElement)
        {
            $price = $obPrice->Add($this->arPrice);
            if(!$price)
            {
                $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."] ".GetMessage("parser_error_add_price").$obPrice->LAST_ERROR;
            }
        }else $this->UpdatePriceCatalog($isElement);

        unset($obPrice);
    }
    
    protected function AddPriceCatalogOffer($arPrice, $arFields)
    {
        if($this->elementOfferUpdate && (!$this->isUpdate || !$this->isUpdate["price"])) return false;
        if(!$this->arPrice || !$this->arPrice["PRICE"]) return false;
        $isElement = $this->elementOfferUpdate;
        $this->arPrice["PRODUCT_ID"] = $this->elementOfferID;
        $this->ChangePrice();
        $this->ConvertCurrency();

        $this->arPrice["PRICE"] = $this->parseCatalogPriceOkrug($this->arPrice["PRICE"]);

        $obPrice = new CPrice();
        if(!$isElement)
        {
            $price = $obPrice->Add($this->arPrice);
            if(!$price)
            {
                $this->errors[] = $this->arFields["NAME"]." - ".$arFields["NAME"]."[".$this->arFields["LINK"]."] ".GetMessage("parser_error_add_price_offer").$obPrice->LAST_ERROR;
            }
        }else $this->UpdatePriceCatalog($isElement);

        unset($obPrice);
    }

    protected function UpdatePriceCatalog($elementID)
    {
        if(!$elementID){
            $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."] ".GetMessage("parser_error_update_price");
            return false;
        }
        $res = CPrice::GetList(
            array(),
            array(
                "PRODUCT_ID" => $elementID,
                "CATALOG_GROUP_ID" => $this->arPrice["CATALOG_GROUP_ID"]
                )
        );

        if ($arr = $res->Fetch())
        {
            CPrice::Update($arr["ID"], $this->arPrice);
        }
    }

    protected function parserCatalogPreview(&$el)
    {
        foreach(GetModuleEvents("shs.parser", "parserCatalogPreview", true) as $arEvent)
            ExecuteModuleEventEx($arEvent, array($this->id, &$el, &$this->arFields));
        if(!$this->parseCatalogUrlPreview($el)) return false;
        $this->parseCatalogNamePreview($el);
        $this->parseCatalogPropertiesPreview($el);
        if($this->isCatalog)$this->parseCatalogPricePreview($el);
        if($this->isCatalog)$this->ParseCatalogAvailablePreview($el);
        $this->parseCatalogPreviewPicturePreview($el);
        $this->parseCatalogDescriptionPreview($el);

        return true;
    }

    protected function parserCatalogDetail()
    {

        if($this->checkUniq() && !$this->isUpdate) return false;
        foreach(GetModuleEvents("shs.parser", "parserCatalogDetailBefore", true) as $arEvent)
            ExecuteModuleEventEx($arEvent, array($this->id, &$el, &$this->arFields));
        $el = $this->parserCatalogDetailPage();
        foreach(GetModuleEvents("shs.parser", "parserCatalogDetail", true) as $arEvent)
            ExecuteModuleEventEx($arEvent, array($this->id, &$el, &$this->arFields));
        $this->parseCatalogNameDetail($el);
        $this->parseCatalogProperties($el);
        $this->parseCatalogDetailPicture($el);
        $this->parseCatalogDetailMorePhoto($el);
        if($this->isCatalog)$this->parseCatalogPriceDetail($el);
        if($this->isCatalog)$this->ParseCatalogAvailableDetail($el);
        $this->parseCatalogDescriptionDetail($el);
        $this->parserOffers($el);
    }
    
    protected function parserOffers($el)
    {
        $this->boolOffer = false;
        if($this->settings["offer"]["load"]=="table" && $this->isOfferParsing && isset($this->settings["offer"]["selector"]) && $this->settings["offer"]["selector"] && isset($this->settings["offer"]["selector_item"]) && $this->settings["offer"]["selector_item"])
        {
            //$this->arFields["NAME"] = htmlspecialchars_decode(trim(strip_tags(pq($el)->find($name)->html())));
           $offerItem = $this->settings["offer"]["selector"]." ".$this->settings["offer"]["selector_item"];
           $this->parserHeadTableOffer($el);

           foreach(pq($el)->find($offerItem) as $offer)
           {
                $this->boolOffer = true;
                if($this->parseOfferName($offer))
                {
                    $this->parseOfferPrice($offer);
                    $this->parseOfferQuantity($offer);
                    $this->parseOfferProps($offer);
                    if(!$this->parseOfferGetUniq())
                    {
                        $this->deleteOfferFields();;
                        continue 1;
                    }

                }else
                    continue 1;

                $this->arOfferAll["FIELDS"][] = $this->arOffer;
                $this->arOfferAll["PRICE"][] = $this->arPriceOffer;
                $this->arOfferAll["QUANTITY"][] = $this->arOfferQuantity;
                
                $this->deleteOfferFields();
                    
           }
        }elseif($this->settings["offer"]["load"]=="one" && $this->isOfferParsing && isset($this->settings["offer"]["one"]["selector"]) && $this->settings["offer"]["one"]["selector"])
        {
            $offerItem = trim($this->settings["offer"]["one"]["selector"]);
            foreach(pq($el)->find($offerItem) as $offer)
            {    
                $this->boolOffer = true;
                if($this->parseOfferName($offer))
                {
                    $this->parseOfferPrice($offer);
                    $this->parseOfferQuantity($offer);
                    $this->parseOfferProps($offer);
                    if(!$this->parseOfferGetUniq())
                    {
                        $this->deleteOfferFields();;
                        continue 1;
                    }

                }else
                    continue 1;

                $this->arOfferAll["FIELDS"][] = $this->arOffer;
                $this->arOfferAll["PRICE"][] = $this->arPriceOffer;
                $this->arOfferAll["QUANTITY"][] = $this->arOfferQuantity;
                
                $this->deleteOfferFields();
                    
            }    
        }
        elseif($this->settings["offer"]["load"]=="more" && $this->isOfferParsing && isset($this->settings["offer"]["selector_prop_more"]) && count($this->settings["offer"]["selector_prop_more"] > 0))
        {
            $allOfferProps = $this->parseOffersSelectorPropMore($el);
            if(($allOfferProps !== false) && is_array($allOfferProps))
            {
                $nm = 0;
                $arRes = array();
                $count = count($allOfferProps);
        
                foreach($allOfferProps as $code => $props)
                {
                   $nm ++;
                   
                   foreach($props as $id => $valProps)
                   {
                        $val = $valProps["value"];
                        $arTemp[] = $valProps;

                        $this->funcX($val, $nm, $allOfferProps, $arRes, $arTemp, $count);
                   }
                   break 1; 
                }
                
                /*$countOffers = 1;
                foreach($allOfferProps as $prop)
                {
                    $countOffers = $countOffers*count($prop);
                }
                
                if(count($arRes) == $countOffers)
                {
                    file_put_contents(dirname(__FILE__)."/logvvvvvv.log", print_r($arRes, true), FILE_APPEND);    
                }*/
                $this->parseAllOffersMoreProps($arRes);
                
            }
            
        }
    
    }
    
    protected function parseAllOffersMoreProps($offers)
    {
        if(empty($offers) || !is_array($offers)) return false;
        foreach($offers as $id => $offer)
        {
            $this->boolOffer = true;
            if($this->parseOfferName($offer))
            {
                $this->parseOfferPrice($offer);
                $this->parseOfferQuantity($offer);
                $this->parseOfferProps($offer);
                if(!$this->parseOfferGetUniq())
                {
                    $this->deleteOfferFields();;
                    continue 1;
                }

            }else
                continue 1;

            $this->arOfferAll["FIELDS"][] = $this->arOffer;
            $this->arOfferAll["PRICE"][] = $this->arPriceOffer;
            $this->arOfferAll["QUANTITY"][] = $this->arOfferQuantity;
            
            $this->deleteOfferFields();
            
        }
        
    }
    
    protected function funcX(&$val, &$nm, &$allOfferProps, &$arRes, &$arTemp, $count)
    {   
        $i = 0;
        foreach($allOfferProps as $idProp=>$arProp)
        {
            $i++;
            if($i<=$nm) continue 1;
            
            foreach($arProp as $idP=>$prop)
            {
                $val = $prop["value"];
                $sovp = 0;
                foreach($arTemp as $v)
                {
                    if($prop["code"] == $v["code"])
                    {
                        $sovp++;
                        break 1;
                    }
                }
                if($sovp <= 0)
                {
                    $arTemp[] = $prop;
                }
               
                $this->funcX($val, $i, $allOfferProps, $arRes, $arTemp, $count);   
            }   
            
        }
        
        if($count==count($arTemp))
        {
            $arRes[] = array_slice($arTemp, 0, $count);
        }
        $arTemp = array_slice($arTemp, 0, $nm-1); 
        
    }
    
    
    protected function parseOffersSelectorPropMore($el)
    {
        $deleteSymb = $this->getOfferDeleteSelector();
        $deleteSymbRegular = $this->getOfferDeleteSelectorRegular();
        
        $offerPropsAll = $this->arSelectorPropertiesOffer;
        if(empty($offerPropsAll)) return false;
        $arrPropsAll = array();
        
        foreach($offerPropsAll as $code => $selector)
        {
            if(empty($selector)) continue 1;
            $arProp = $this->arPropertiesOffer[$code];
            
            $arr = $this->GetArraySrcAttr($selector);
            $path = $arr["path"];
            $attr = $arr["attr"];
            
            $item = 0;
            if(!empty($path))
            {
                foreach(pq($el)->find($path) as $valProps)
                {   
                    if(!empty($path) && empty($attr))
                    {
                        $arrPropsAll[$code][$item]["value"] = trim(pq($valProps)->html());
                    }
                    elseif(!empty($path) && !empty($attr))
                    {
                        $arrPropsAll[$code][$item]["value"] = pq($valProps)->find($path)->attr($attr);
                    }
                    
                    if($arProp["USER_TYPE"]!="HTML")
                    {
                        $arrPropsAll[$code][$item]["value"] = strip_tags($arrPropsAll[$code][$item]["value"]);
                    }
                    
                    $arrPropsAll[$code][$item]["value"] = str_replace($deleteSymb, "", $arrPropsAll[$code][$item]["value"]);
                    $arrPropsAll[$code][$item]["value"] = preg_replace($deleteSymbRegular, "", $arrPropsAll[$code][$item]["value"]);
                    
                    $arrPropsAll[$code][$item]["code"] = $code;
                    $item ++;
                }
            }
            else
            {
                $arrPropsAll[$code][$item]["value"] = pq($el)->attr($attr);
                
                if($arProp["USER_TYPE"]!="HTML")
                {
                    $arrPropsAll[$code][$item]["value"] = strip_tags($arrPropsAll[$code][$item]["value"]);
                }
                
                $arrPropsAll[$code][$item]["value"] = str_replace($deleteSymb, "", $arrPropsAll[$code][$item]["value"]);
                $arrPropsAll[$code][$item]["value"] = preg_replace($deleteSymbRegular, "", $arrPropsAll[$code][$item]["value"]);
                
                $arrPropsAll[$code][$item]["code"] = $code;
            }

        }
        
        if(!isset($arrPropsAll) || empty($arrPropsAll)) return false;
        
        return $arrPropsAll;
    }

    protected function deleteOfferFields()
    {
        if(isset($this->arOffer))
            unset($this->arOffer);
                    
        if(isset($this->arPriceOffer))
            unset($this->arPriceOffer);
        
        if(isset($this->arOfferQuantity))
            unset($this->arOfferQuantity);    
    }

    protected function parseOfferGetUniq()
    {
        if(isset($this->settings["offer"]["add_name"]) && !empty($this->settings["offer"]["add_name"]))
        {
            $strV = "";
            $bool = true;
            foreach($this->settings["offer"]["add_name"] as $v)
            {
                if(isset($this->arOffer["PROPERTY_VALUES"][$v]))
                {
                    
                    if(is_array($this->arOffer["PROPERTY_VALUES"][$v]))
                    {
                        
                        foreach($this->arOffer["PROPERTY_VALUES"][$v] as $val)
                        {
                            if($bool) $strV .= $val;
                            else $strV .= " / ".$val;
                            $bool = false;
                        }
                    }else{
                        $val = $this->arOffer["PROPERTY_VALUES"][$v];
                        if($bool) $strV .= $val;
                        else $strV .= " / ".$val;
                        $bool = false;   
                    }
                }
            }
            
            if(!isset($this->arOffer["NAME"]))
                $this->arOffer["NAME"] = "";
            if($strV)
                $strV =  " (".$strV.")";
            
            if($this->typeN == "catalog")
                $this->arOffer["NAME"] .= $strV;
            
            if($this->typeN == "xml")
                $this->arOffer["NAME"] = $this->arFields["NAME"].$strV;

            if(!$this->arOffer["NAME"])
            {
                $this->errors[] = GetMessage("parser_error_name_notfound_offer");
                return false;    
            }
        }
        if($this->arOffer["NAME"])
        {
            $this->arOffer["XML_ID"] = "offer#".md5($this->arFields["LINK"].$this->arOffer["NAME"]);
        } 
        return true;    
    }
    
    protected function parserHeadTableOffer($el)
    {
        if(isset($this->settings["offer"]["selector"]) && $this->settings["offer"]["selector"] && isset($this->settings["offer"]["selector_head"]) && $this->settings["offer"]["selector_head"] && isset($this->settings["offer"]["selector_head_th"]) && $this->settings["offer"]["selector_head_th"])
        {
            $offerHead = $this->settings["offer"]["selector"]." ".$this->settings["offer"]["selector_head"]." ".$this->settings["offer"]["selector_head_th"];
            $i = 0;
            foreach(pq($el)->find($offerHead) as $head)
            {
                $textHead = trim(strip_tags(pq($head)->html()));    
                
                $this->tableHeaderNumber[$textHead] = $i;
                $i++;
            }
        }
    }
    
    protected function parseOfferName($offer)
    {
        if(isset($this->settings["offer"]["selector_name"]) && $this->settings["offer"]["selector_name"])
        {
            
            $arr = $this->GetArraySrcAttr($this->settings["offer"]["selector_name"]);
            if (empty($arr["path"]) && !empty($arr["attr"]))
            {
                $name = trim(pq($offer)->attr($arr["attr"]));
            }
            else{
                if(empty($arr["attr"])){
                    $name = trim(strip_tags(pq($offer)->find($arr["path"])->html()));
                }
                elseif(!empty($arr["attr"]))
                {
                    $name = trim(pq($offer)->find($arr["path"])->attr($arr["attr"]));
                }
            } 
             
            $deleteSymb = $this->getOfferDeleteSelector();
            $name = str_replace($deleteSymb, "", $name);
            $this->arOffer["NAME"] = htmlspecialchars_decode($name);
            if(isset($this->settings["loc"]["f_name"]) && $this->settings["loc"]["f_name"]=="Y")
            {
                $this->arOffer["NAME"] = $this->locText($this->arOffer["NAME"]);    
            }   
        }elseif(isset($this->settings["offer"]["find_name"]) && $this->settings["offer"]["find_name"])
        {
            if(isset($this->settings["offer"]["selector_item_td"]) && $this->settings["offer"]["selector_item_td"])
            {
                $deleteSymb = $this->getOfferDeleteFind();
                $name = $this->settings["offer"]["find_name"];
                
                if(isset($this->tableHeaderNumber[$name]))
                {
                    $n = $this->tableHeaderNumber[$name];
                    $name = pq($offer)->find($this->settings["offer"]["selector_item_td"].":eq(".$n.")");
                    $this->arOffer["NAME"] = htmlspecialchars_decode(trim(strip_tags($name)));
                    $this->arOffer["NAME"] = str_replace($deleteSymb, "", $this->arOffer["NAME"]);
                    if(isset($this->settings["loc"]["f_name"]) && $this->settings["loc"]["f_name"]=="Y")
                    {
                        $this->arOffer["NAME"] = $this->locText($this->arOffer["NAME"]);
                    }
                    
                }
            }        
        }elseif(isset($this->settings["offer"]["selector_prop_more"]) && $this->settings["offer"]["selector_prop_more"] && (!isset($this->settings["offer"]["add_name"]) || empty($this->settings["offer"]["add_name"])))
        {
            if(!empty($offer) && is_array($offer))
            {
                 if(isset($this->settings["offer"]["add_offer_name_more"]) && !empty($this->settings["offer"]["add_offer_name_more"]))
                {
                    $arName = explode("|", trim(str_replace(" ", "", $this->settings["offer"]["add_offer_name_more"])));
                }else
                {
                    if(isset($this->settings["offer"]["selector_prop_more"]) && count($this->settings["offer"]["selector_prop_more"] > 0))
                    {
                        foreach($this->settings["offer"]["selector_prop_more"] as $code => $value)
                        {
                            if(empty($code)) continue 1;
                            $arName[] = $code;
                        }
                    }
                    else return false;
                }
                $this->arOffer["NAME"] = "";
                foreach($arName as $code)
                {
                    if(empty($code)) continue 1;
                    
                    foreach($offer as $val)
                    {
                        if($val["code"] == $code)
                        {
                            if($this->arOffer["NAME"] != "")
                            {
                                $this->arOffer["NAME"] = $this->arOffer["NAME"]." / ".$val["value"];
                            } 
                            else
                            {
                                $this->arOffer["NAME"] = $val["value"];
                            }   
                        }
                    }
                }
                $this->arOffer["NAME"] = trim(str_replace("  ", " ", $this->arOffer["NAME"]));
                $this->arOffer["NAME"] = htmlspecialchars_decode(trim(strip_tags($this->arOffer["NAME"])));
                $this->arOffer["NAME"] = $this->arFields["NAME"]." (".$this->arOffer["NAME"].")";
                
                if(isset($this->settings["loc"]["f_name"]) && $this->settings["loc"]["f_name"]=="Y")
                {
                    $this->arOffer["NAME"] = $this->locText($this->arOffer["NAME"]);    
                }
            }
        }
        
        if(!isset($this->arOffer["NAME"]) && (!isset($this->settings["offer"]["add_name"]) || empty($this->settings["offer"]["add_name"])))
        {
            $this->errors[] = GetMessage("parser_error_name_notfound_offer");
            return false;
        }elseif(!isset($this->arOffer["NAME"]))
            $this->arOffer["NAME"] = $this->arFields["NAME"];     
        
        
        return true;
    }
    
    protected function getOfferDeleteSelector()
    {
        $deleteSymb = array();
        if($this->settings["offer"]["catalog_delete_selector_props_symb"])
        {
            $deleteSymb = explode("||", $this->settings["offer"]["catalog_delete_selector_props_symb"]);

            foreach($deleteSymb as $i=>&$symb)
            {
                $symb = trim($symb);
                $symb = htmlspecialcharsBack($symb);
                if(empty($symb))
                {
                    unset($deleteSymb[$i]);
                    continue;
                }
                if(preg_match("/^\//", $symb) && preg_match("/\/$/", $symb))
                {
                    unset($deleteSymb[$i]);
                    continue;    
                }
            }
            
        }

        return $deleteSymb;
    }
    
    protected function getOfferDeleteSelectorRegular()
    {
        $deleteSymb = array();
        if($this->settings["offer"]["catalog_delete_selector_props_symb"])
        {
            $deleteSymb = explode("||", $this->settings["offer"]["catalog_delete_selector_props_symb"]);

            foreach($deleteSymb as $i=>&$symb)
            {
                $symb = trim($symb);
                $symb = htmlspecialcharsBack($symb);
                if(empty($symb))
                {
                    unset($deleteSymb[$i]);
                    continue;
                }
                
                if(!preg_match("/^\//", $symb) || !preg_match("/\/$/", $symb))
                {
                    unset($deleteSymb[$i]);
                    continue;    
                }
            }
            
        }

        return $deleteSymb;
    }

    protected function getOfferDeleteFind()
    {
        $deleteSymb = array();
        
        if($this->settings["offer"]["catalog_delete_selector_find_props_symb"])
        {
            $deleteSymb = explode("||", $this->settings["offer"]["catalog_delete_selector_find_props_symb"]);

            foreach($deleteSymb as $i=>&$symb)
            {
                $symb = trim($symb);
                $symb = htmlspecialcharsBack($symb);
                if(empty($symb))
                {
                    unset($deleteSymb[$i]);
                    continue;
                }
                
                if(preg_match("/^\//", $symb) && preg_match("/\/$/", $symb))
                {
                    unset($deleteSymb[$i]);
                    continue;    
                }
            }

        }
        
        return $deleteSymb;
    }
    
    protected function getOfferDeleteFindRegular()
    {
        $deleteSymb = array();
        if($this->settings["offer"]["catalog_delete_selector_find_props_symb"])
        {
            $deleteSymb = explode("||", $this->settings["offer"]["catalog_delete_selector_find_props_symb"]);

            foreach($deleteSymb as $i=>&$symb)
            {
                $symb = trim($symb);
                $symb = htmlspecialcharsBack($symb);
                if(empty($symb))
                {
                    unset($deleteSymb[$i]);
                    continue;
                }
                
                if(!preg_match("/^\//", $symb) || !preg_match("/\/$/", $symb))
                {
                    unset($deleteSymb[$i]);
                    continue;    
                }
            }

        }
        
        return $deleteSymb;
    }
    
    protected function parseOfferPrice($offer)
    {
        if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["price"])) return false;
        if(isset($this->settings["offer"]["selector_price"]) && $this->settings["offer"]["selector_price"])
        {   
            $arr = $this->GetArraySrcAttr($this->settings["offer"]["selector_price"]);
            if (empty($arr["path"]) && !empty($arr["attr"]))
            {
                $price = trim(pq($offer)->attr($arr["attr"]));
            }
            else{
                if(empty($arr["attr"])){
                    $price = trim(strip_tags(pq($offer)->find($arr["path"])->html()));
                }
                elseif (!empty($arr["attr"]))
                {
                    $price = trim(pq($offer)->find($arr["path"])->attr($arr["attr"]));
                }
            }
            $price = $this->parseCatalogPriceFormat($price);
            //$price = $this->parseCatalogPriceOkrug($price);
            $this->arPriceOffer["PRICE"] = $price;
            $this->arPriceOffer["CATALOG_GROUP_ID"] = $this->settings["catalog"]["price_type"];
            $this->arPriceOffer["CURRENCY"] = $this->settings["catalog"]["currency"];
               
        }elseif(isset($this->settings["offer"]["find_price"]) && $this->settings["offer"]["find_price"])
        {
            if(isset($this->settings["offer"]["selector_item_td"]) && $this->settings["offer"]["selector_item_td"])
            {
                $name = $this->settings["offer"]["find_price"];
                if(isset($this->tableHeaderNumber[$name]))
                {
                    $n = $this->tableHeaderNumber[$name];
                    $price = pq($offer)->find($this->settings["offer"]["selector_item_td"].":eq(".$n.")");
                    $price = trim(strip_tags($price));
                    $price = $this->parseCatalogPriceFormat($price);
                    //$price = $this->parseCatalogPriceOkrug($price);
                    $this->arPriceOffer["PRICE"] = $price;
                    $this->arPriceOffer["CATALOG_GROUP_ID"] = $this->settings["catalog"]["price_type"];
                    $this->arPriceOffer["CURRENCY"] = $this->settings["catalog"]["currency"];
                }
            }        
        }elseif(isset($this->settings["offer"]["one"]["price"]) && $this->settings["offer"]["one"]["price"])
        {
            $attr = $this->settings["offer"]["one"]["price"];
            $price = pq($offer)->attr($attr);
            $price = trim(strip_tags($price));
            $this->arPriceOffer["PRICE"] = $price;
            $this->arPriceOffer["CATALOG_GROUP_ID"] = $this->settings["catalog"]["price_type"];
            $this->arPriceOffer["CURRENCY"] = $this->settings["catalog"]["currency"];
        }
        
        if(isset($this->arPrice["PRICE"]) && !empty($this->arPrice["PRICE"]) && (!isset($this->arPriceOffer["PRICE"]) || empty($this->arPriceOffer["PRICE"])))
        {
             $this->arPriceOffer["PRICE"] = $this->arPrice["PRICE"];
             $this->arPriceOffer["CATALOG_GROUP_ID"] = $this->settings["catalog"]["price_type"];
             $this->arPriceOffer["CURRENCY"] = $this->settings["catalog"]["currency"];    
        }
        
        if(!isset($this->arPriceOffer["PRICE"]))
        {
            $this->errors[] = GetMessage("parser_error_name_notfound_offer");
            return false;
        }
        return true;        
    }
    
    protected function parseOfferProps($offer, $nameOffer=false)
    {
        if($this->checkUniq() && !$this->isUpdate) return false;
        if(isset($this->settings["offer"]["selector_prop"]) && !empty($this->settings["offer"]["selector_prop"]))
        {
            $deleteSymb = $this->getOfferDeleteSelector();
            $deleteSymbRegular = $this->getOfferDeleteSelectorRegular();
            
            $arProperties = $this->arSelectorPropertiesOffer;
            
            foreach($arProperties as $code=>$val)
            {
                $arProp = $this->arPropertiesOffer[$code];
                if($arProp["PROPERTY_TYPE"]=="F")
                {
                    $this->parseCatalogPropFile($code, $offer);
                }else{
                    $arr = $this->GetArraySrcAttr($this->settings["offer"]["selector_prop"][$code]);
                    if (empty($arr["path"]) && !empty($arr["attr"]))
                    {
                        $text = trim(pq($offer)->attr($arr["attr"]));
                    }
                    else{
                        if(empty($arr["attr"])){
                            $text = trim(strip_tags(pq($offer)->find($arr["path"])->html()));
                        }
                        elseif (!empty($arr["attr"]))
                        {
                            $text = trim(pq($offer)->find($arr["path"])->attr($arr["attr"]));
                        }
                    }
                    
                    if($arProp["USER_TYPE"]!="HTML")
                        $text = strip_tags($text);
                    $text = str_replace($deleteSymb, "", $text);
                    $text = preg_replace($deleteSymbRegular, "", $text);
                    $this->parseCatalogPropOffer($code, $val, $text);
                }

            }
                
        }
        if(isset($this->settings["offer"]["find_prop"]) && !empty($this->settings["offer"]["find_prop"]))
        {
            $deleteSymb = $this->getOfferDeleteFind();
            $deleteSymbRegular = $this->getOfferDeleteFindRegular();
            
            $arProperties = $this->arFindPropertiesOffer; 
            foreach($arProperties as $code=>$val)
            {
                $arProp = $this->arPropertiesOffer[$code];
                if(isset($this->tableHeaderNumber[$val]))
                {
                    $n = $this->tableHeaderNumber[$val];
                    $text = pq($offer)->find($this->settings["offer"]["selector_item_td"].":eq(".$n.")");
                    $text = str_replace($deleteSymb, "", $text);
                    $text = preg_replace($deleteSymbRegular, "", $text);
                    
                    $text1 = $text; 
                    if($arProp["USER_TYPE"]!="HTML")
                        $text1 = strip_tags($text);
                    if($this->CheckFindPropsOffer($code, $val, $text1))
                    {   
                        $this->parseCatalogPropOffer($code, $val, $text1);
                    }   
                }    
            }
        }
        
        if(isset($this->settings["offer"]["one"]["selector"]) && !empty($this->settings["offer"]["one"]["selector"]) && isset($this->settings["offer"]["add_name"]) && !empty($this->settings["offer"]["add_name"])) 
        {
            $arProperties = $this->settings["offer"]["add_name"];
            $deleteSymb = $this->getOfferDeleteSelector();
            $deleteSymbRegular = $this->getOfferDeleteSelectorRegular();
            foreach($arProperties as $code)
            {
                if($nameOffer === false)
                {
                    $text = pq($offer)->html();
                }
                elseif($nameOffer !== false)
                {
                    $text = $nameOffer;
                }
                $text = str_replace($deleteSymb, "", $text); 
                $text = preg_replace($deleteSymbRegular, "", $text);
                $text1 = $text; 
                $text1 = strip_tags($text); 
                $this->parseCatalogPropOffer($code, "", $text1);
                break 1;
            }    
        }  
        
        if(isset($this->settings["offer"]["selector_prop_more"]) && !empty($this->settings["offer"]["selector_prop_more"]))
        {
            if(!empty($offer) && is_array($offer))
            {
                $deleteSymb = $this->getOfferDeleteSelector();
                $deleteSymbRegular = $this->getOfferDeleteSelectorRegular();
                
                $arProperties = $this->arSelectorPropertiesOffer;
                //file_put_contents(dirname(__FILE__)."/props.log", print_r($arProperties, true), FILE_APPEND);
                /*foreach($arProperties as $code=>$val)
                {
                    $arProp = $this->arPropertiesOffer[$code];
                    
                        if($arProp["USER_TYPE"]!="HTML")
                        {
                            $text = strip_tags($text);
                        } 
                        $text = str_replace($deleteSymb, "", $text);
                        $text = preg_replace($deleteSymbRegular, "", $text);
                        $this->parseCatalogPropOffer($code, $val, $text);
                }*/
                
                foreach($offer as $props)
                {
                    if(array_key_exists($props["code"], $arProperties))
                    {
                        $arProp = $this->arPropertiesOffer[$props["code"]];
                        $text = $props["value"];
                        
                        if($arProp["USER_TYPE"]!="HTML")
                        {
                            $text = strip_tags($text);
                        } 
                        
                        $text = str_replace($deleteSymb, "", $text);
                        $text = preg_replace($deleteSymbRegular, "", $text);
                        $this->parseCatalogPropOffer($props["code"], $arProperties[$props["code"]], $text);
                    }
                }
            }
        }    
    }

    protected function parseCatalogProperties(&$el)
    {
        if($this->checkUniq() && !$this->isUpdate) return false;
        $this->parseCatalogDefaultProperties($el);
        $this->parseCatalogSelectorProperties($el);
        $this->parseCatalogFindProperties($el);
        $this->AllDoProps();
        if($this->isCatalog)$this->parseCatalogFindProduct($el);
        if($this->isCatalog)$this->parseCatalogSelectorProduct($el);
    }

    protected function parseCatalogPropertiesPreview(&$el)
    {
        if($this->checkUniq() && !$this->isUpdate) return false;
        $this->parseCatalogSelectorPropertiesPreview($el);
        $this->parseCatalogFindPropertiesPreview($el);
        //$this->AllDoProps();
    }
    


    protected function AllDoProps()
    {
        if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["props"])) return false;
        $isElement = $this->checkUniq();
        if($isElement)
        {
            $obElement = new CIBlockElement;
            $rsProperties = $obElement->GetProperty($this->iblock_id, $isElement, "sort", "asc");
            while($arProperty = $rsProperties->Fetch())
            {

                if(isset($this->arFields["PROPERTY_VALUES"][$arProperty["CODE"]]) || $arProperty["PROPERTY_TYPE"]=="F") continue;
                $this->arFields["PROPERTY_VALUES"][$arProperty["ID"]][$arProperty['PROPERTY_VALUE_ID']] = array(
                    "VALUE"=>$arProperty['VALUE'],
                    "DESCRIPTION"=>$arProperty["DESCRIPTION"]
                );
            }

        }
    }

    protected function clearFields()
    {
        unset($this->arFields);
        
        if($this->arProduct)
            unset($this->arProduct);
        if(isset($this->arPrice))
            unset($this->arPrice);
        unset($this->elementUpdate);
        if(isset($this->elementOfferUpdate))
            unset($this->elementOfferUpdate);
        unset($this->elementID);
        unset($this->detailHtml);
        unset($this->arEmptyUpdate);
        if(isset($this->arPhoto))
            unset($this->arPhoto);
        if(isset($this->arOfferAll))
            unset($this->arOfferAll);
        if(isset($this->elementName))
            unset($this->elementName);
        if(isset($this->arrFilesTemp))
            unset($this->arrFilesTemp);
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
            "delete_repeat_replace" => $arFieldCode["TRANS_EAT"]=="Y"?true:false,
        ));
        
        $IBLOCK_ID = $this->arrayIblock['ID'];

        $arCodes = array();
        $rsCodeLike = CIBlockElement::GetList(array(), array(
                "IBLOCK_ID" => $IBLOCK_ID,
                "CODE" => $CODE."%",
        ), false, false, array("ID", "CODE"));
        while($ar = $rsCodeLike->Fetch())
            $arCodes[$ar["CODE"]] = $ar["ID"];

        if (array_key_exists($CODE, $arCodes))
        {
            $i = 1;
            while(array_key_exists($CODE."_".$i, $arCodes))
                $i++;

            return $CODE."_".$i;
        }
        else
        {
            return $CODE;
        }
    }

    protected function getArrayIblock()
    {
        $arIBlock = CIBlock::GetArrayByID($this->iblock_id);
        $this->arrayIblock = $arIBlock;
    }
    
    protected function GetCatalogSectionId()
    {
        if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_section".$this->id.".txt"))
        {
            $section_id = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_section".$this->id.".txt");
            $section_id = trim($section_id);
            if(is_numeric($section_id))
            {
                return $section_id;
            }
            else return false;
        }
        else return false;
    }

    protected function parseCatalogSection()
    {
        if($this->checkUniq()) return false;
        if(isset($this->section_array) && !empty($this->section_array))
        {
            $IBLOCK_SECTION_ID = $this->GetCatalogSectionId();
            if($IBLOCK_SECTION_ID !== false)
            {
                $this->arFields["IBLOCK_SECTION_ID"] = $IBLOCK_SECTION_ID;
            }
            else
            {
                $this->arFields["IBLOCK_SECTION_ID"] = $this->section_id;
            }
        }
        else
        {
            $this->arFields["IBLOCK_SECTION_ID"] = $this->section_id;
        } 
        
    }

    protected function parseCatalogFindProduct(&$el)
    {
        $arProperties = $this->arFindProduct;
        if(!$arProperties) return false;
        if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["param"])) return false;
        $find = $this->settings["catalog"]["selector_find_size"];
        if($this->settings["catalog"]["catalog_delete_find_symb"])
        {
            $deleteSymb = explode(",", $this->settings["catalog"]["catalog_delete_find_symb"]);

            foreach($deleteSymb as $i=>&$symb)
            {
                $symb = trim($symb);
                $symb = htmlspecialcharsBack($symb);
                if(empty($symb))
                {
                    unset($deleteSymb[$i]);
                    continue;
                }
                if($symb=="\\\\")
                {
                    $deleteSymb[$i] = ",";
                }

            }
        }

        foreach(pq($el)->find($find) as $prop)
        {
            $text = pq($prop)->html();
            $text = strip_tags($text);
            $text = str_replace($deleteSymb, "", $text);
            foreach($arProperties as $code=>$val)
            {
                //if(preg_match("/".$val."/", $text))
                if(strpos($text, $val)!==false)
                {
                    $text = str_replace($val, "", $text);
                    $text = trim($text);
                    
                    $text =  str_replace(",", ".", $text);
                    $text = preg_replace("/\.{1}$/", "", $text);
                    $text = preg_replace('/[^0-9.]/', "", $text);
                    
                    if(isset($this->settings["catalog"]["find_product_koef"][$code]) && !empty($this->settings["catalog"]["find_product_koef"][$code]))
                    {
                        $text = $text*$this->settings["catalog"]["find_product_koef"][$code];    
                    }
                    
                    $this->arProduct[$code] = $text;
                }
            }

        }
    }

    protected function parseCatalogSelectorProduct(&$el)
    {
        $arProperties = $this->arSelectorProduct;
        if(!$arProperties) return false;
        if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["param"])) return false;
        if($this->settings["catalog"]["catalog_delete_selector_symb"])
        {
            $deleteSymb = explode(",", $this->settings["catalog"]["catalog_delete_selector_symb"]);

            foreach($deleteSymb as $i=>&$symb)
            {
                $symb = trim($symb);
                $symb = htmlspecialcharsBack($symb);
                if(empty($symb))
                {
                    unset($deleteSymb[$i]);
                    continue;
                }
                if($symb=="\\\\")
                {
                    $deleteSymb[$i] = ",";
                }
            }
        }

        foreach($arProperties as $code=>$val)
        {
            $text = pq($el)->find($this->settings["catalog"]["selector_product"][$code])->html();
            $text = strip_tags($text);
            $text = str_replace($deleteSymb, "", $text);
            $text = trim($text);
            
            $text =  str_replace(",", ".", $text);
            $text = preg_replace("/\.{1}$/", "", $text);
            $text = preg_replace('/[^0-9.]/', "", $text);
            
            if(isset($this->settings["catalog"]["selector_product_koef"][$code]) && !empty($this->settings["catalog"]["selector_product_koef"][$code]))
            {
                $text = $text*$this->settings["catalog"]["selector_product_koef"][$code];    
            }
            
            $this->arProduct[$code] = $text;
        }
    }

    protected function parseCatalogSelectorProperties(&$el)
    {
        $arProperties = $this->arSelectorProperties;
        
        if(!$arProperties) return false;
        if($this->settings["catalog"]["catalog_delete_selector_props_symb"])
        {
            $deleteSymb = explode(",", $this->settings["catalog"]["catalog_delete_selector_props_symb"]);

            foreach($deleteSymb as $i=>&$symb)
            {
                $symb = trim($symb);
                $symb = htmlspecialcharsBack($symb);
                if(empty($symb))
                {
                    unset($deleteSymb[$i]);
                    continue;
                }
                if($symb=="\\\\")
                {
                    $deleteSymb[$i] = ",";
                }

            }
        }

        foreach($arProperties as $code=>$val)
        {
            $arProp = $this->arProperties[$code];
            if($arProp["PROPERTY_TYPE"]=="F")
            {
                $this->parseCatalogPropFile($code, $el);
            }else{
                $ar = $this->GetArraySrcAttr($this->settings["catalog"]["selector_prop"][$code]);
                $path = $ar["path"];
                $attr = $ar["attr"];
                
                if($attr)
                    $text = pq($el)->find($path)->attr($attr);
                else
                    $text = pq($el)->find($path)->html();

                if($arProp["USER_TYPE"]!="HTML")
                    $text = strip_tags($text);
                $text = str_replace($deleteSymb, "", $text);
                $this->parseCatalogProp($code, $val, $text);
            }

        }
    }

    protected function parseCatalogSelectorPropertiesPreview(&$el)
    {
        $arProperties = $this->arSelectorPropertiesPreview;
        if(!$arProperties) return false;
        if($this->settings["catalog"]["catalog_delete_selector_props_symb_preview"])
        {
            $deleteSymb = explode(",", $this->settings["catalog"]["catalog_delete_selector_props_symb_preview"]);

            foreach($deleteSymb as $i=>&$symb)
            {
                $symb = trim($symb);
                $symb = htmlspecialcharsBack($symb);
                if(empty($symb))
                {
                    unset($deleteSymb[$i]);
                    continue;
                }
                if($symb=="\\\\")
                {
                    $deleteSymb[$i] = ",";
                }

            }
        }
 
        foreach($arProperties as $code=>$val)
        {
            /*$text = pq($el)->find($this->settings["catalog"]["selector_prop_preview"][$code])->html();
            
            $arProp = $this->arProperties[$code];
            if($arProp["USER_TYPE"]!="HTML")
                $text = strip_tags($text);
            $text = str_replace($deleteSymb, "", $text);
            $this->parseCatalogProp($code, $val, $text);*/
            
            $arProp = $this->arProperties[$code];
            if($arProp["PROPERTY_TYPE"]=="F")
            {
                $this->parseCatalogPropFilePreview($code, $el);
            }else{
                $ar = $this->GetArraySrcAttr($this->settings["catalog"]["selector_prop_preview"][$code]);
                $path = $ar["path"];
                $attr = $ar["attr"];
                if($attr)
                    $text = pq($el)->find($path)->attr($attr);
                else
                    $text = pq($el)->find($this->settings["catalog"]["selector_prop_preview"][$code])->html();

                if($arProp["USER_TYPE"]!="HTML")
                    $text = strip_tags($text);
                $text = str_replace($deleteSymb, "", $text);
                $this->parseCatalogProp($code, $val, $text);
            }

        }
    }

    protected function parseCatalogFindProperties(&$el)
    {   
        $arProperties = $this->arFindProperties;
        if(!$arProperties) return false;
        $find = $this->settings["catalog"]["selector_find_props"];
        if($this->settings["catalog"]["catalog_delete_selector_find_props_symb"])
        {
            $deleteSymb = explode(",", $this->settings["catalog"]["catalog_delete_selector_find_props_symb"]);

            foreach($deleteSymb as $i=>&$symb)
            {
                $symb = trim($symb);
                $symb = htmlspecialcharsBack($symb);  
                if(empty($symb))
                {
                    unset($deleteSymb[$i]);
                    continue;
                }
                if($symb=="\\\\")
                {
                    $deleteSymb[$i] = ",";
                }

            }
        }
        $arFind = explode(",", $find);
        foreach($arFind as $vFind)
        {             
            if(strpos($vFind, " br")!==false || strpos($vFind, "<br/>") || strpos($vFind, "<br />"))
            {
                $vFind = str_replace(array(" br", "<br/>", "<br />"), "", $vFind);
                $vFind = trim($vFind);
                $arBr = array("<br>", "<br/>", "<br />");
                
                foreach(pq($el)->find($vFind) as $prop)
                {
                    $text = pq($prop)->html();
                    $text = str_replace($arBr, "<br>", $text);
                    unset($arBr[1]);
                    unset($arBr[2]);
                    foreach($arBr as $br)
                    {
                        $arTextBr = explode($br, $text);
                        if(!empty($arTextBr) && count($arTextBr)>1)
                        {
                            foreach($arTextBr as $textBr)
                            {   
                                $textBr = strip_tags($textBr);
                                $textBr = str_replace($deleteSymb, "", $textBr); 
                                foreach($arProperties as $code=>$val)
                                {
                                    //if(preg_match("/".$val."/", $textBr))
                                    if($this->CheckFindProps($code, $val, $textBr))
                                    {
                                        $this->parseCatalogProp($code, $val, $textBr);
                                    }    
                                }
                                
                            }      
                        }
                    }
                    
                }
            }else
            { 
                foreach(pq($el)->find($vFind) as $prop)
                {
                    $text = pq($prop)->html();
                    //$text = strip_tags($text);
                    $text = str_replace($deleteSymb, "", $text);
                    $text1 = $text;
                    foreach($arProperties as $code=>$val)
                    {
                        //if(preg_match("/".$val."/", $text))
                        $text1 = $text;
                        $arProp = $this->arProperties[$code];
                        if($arProp["USER_TYPE"]!="HTML")
                            $text1 = strip_tags($text);
                
                        if($this->CheckFindProps($code, $val, $text1))
                        {   
                            $this->parseCatalogProp($code, $val, $text1);
                        }
                    }
                }
            }
        }
    }

    protected function parseCatalogFindPropertiesPreview(&$el)
    {   
        $arProperties = $this->arFindPropertiesPreview;
        if(!$arProperties) return false;
        $find = $this->settings["catalog"]["selector_find_props_preview"];
        if($this->settings["catalog"]["catalog_delete_selector_find_props_symb_preview"])
        {
            $deleteSymb = explode(",", $this->settings["catalog"]["catalog_delete_selector_find_props_symb_preview"]);

            foreach($deleteSymb as $i=>&$symb)
            {
                $symb = trim($symb);
                $symb = htmlspecialcharsBack($symb);  
                if(empty($symb))
                {
                    unset($deleteSymb[$i]);
                    continue;
                }
                if($symb=="\\\\")
                {
                    $deleteSymb[$i] = ",";
                }

            }
        }
        $arFind = explode(",", $find);
        foreach($arFind as $vFind)
        {
            if(strpos($vFind, " br")!==false || strpos($vFind, "<br/>") || strpos($vFind, "<br />"))
            {
                $vFind = str_replace(array(" br", "<br/>", "<br />"), "", $vFind);
                $vFind = trim($vFind);
                $arBr = array("<br>", "<br/>", "<br />");
                
                foreach(pq($el)->find($vFind) as $prop)
                {
                    $text = pq($prop)->html();
                    $text = str_replace($arBr, "<br>", $text);
                    unset($arBr[1]);
                    unset($arBr[2]);
                    foreach($arBr as $br)
                    {
                        $arTextBr = explode($br, $text);
                        if(!empty($arTextBr) && count($arTextBr)>1)
                        {
                            foreach($arTextBr as $textBr)
                            {   
                                $textBr = strip_tags($textBr);
                                $textBr = str_replace($deleteSymb, "", $textBr); 
                                foreach($arProperties as $code=>$val)
                                {
                                    //if(preg_match("/".$val."/", $textBr))
                                    if($this->CheckFindPropsPreview($code, $val, $textBr))
                                    {
                                        $this->parseCatalogProp($code, $val, $textBr);
                                    }    
                                }
                                
                            }      
                        }
                    }
                    
                }
            }else
            {
                foreach(pq($el)->find($vFind) as $prop)
                {   
                    $text = pq($prop)->html();
                    //$text = strip_tags($text);
                    $text = str_replace($deleteSymb, "", $text);
                    $text1 = $text;
                    foreach($arProperties as $code=>$val)
                    {
                        //if(preg_match("/".$val."/", $text))
                        $text1 = $text;
                        $arProp = $this->arProperties[$code];
                        if($arProp["USER_TYPE"]!="HTML")
                            $text1 = strip_tags($text);
                        if($this->CheckFindPropsPreview($code, $val, $text1))
                        {   
                            $this->parseCatalogProp($code, $val, $text1);
                        }
                    }

                }    
            }    
        }
    }
    
    protected function parseCatalogDefaultProperties(&$el)
    {
        if(isset($this->settings["catalog"]["default_prop"]) && !empty($this->settings["catalog"]["default_prop"]))
        {
            foreach($this->settings["catalog"]["default_prop"] as $code=>$val)
            {
                if($val)$this->parseCatalogDefaultProp($code, $val);
            }
        }    
    }
    
    protected function CheckFindProps($code, $val, $text)
    {   
        $arDubleProperties = $this->arDubleFindProperties;
        $bool = false;
        if(isset($arDubleProperties[$code]))
        {   
            foreach($arDubleProperties[$code] as $prop)
            {
                $v = $this->arFindProperties[$prop];
                //if(preg_match("/".$v."/", $text))
                if(strpos($text, $v)!==false)
                {
                    $bool  = true;    
                }
            }
            if($bool) return false;
        }
        
        //if(preg_match("/".$val."/", $text)) return true;
        if(strpos($text, $val)!==false) return true;
        else return false;
    }
    
    protected function CheckFindPropsOffer($code, $val, $text)
    {
        $arDubleProperties = $this->arDubleFindPropertiesOffer;
        $bool = false;
        if(isset($arDubleProperties[$code]))
        {   
            foreach($arDubleProperties[$code] as $prop)
            {
                $v = $this->arFindPropertiesOffer[$prop];
                //if(preg_match("/".$v."/", $text))
                if(strpos($text, $v)!==false)
                {
                    $bool  = true;
                }    
            }
            if($bool) return false;
        }
        
        return true;
    }
    
    protected function CheckFindPropsPreview($code, $val, $text)
    {
        $arDubleProperties = $this->arDubleFindPropertiesPreview;
        $bool = false;
        if(isset($arDubleProperties[$code]))
        {   
            foreach($arDubleProperties[$code] as $prop)
            {
                $v = $this->arFindPropertiesPreview[$prop];
                //if(preg_match("/".$v."/", $text))
                if(strpos($text, $v)!==false)
                {
                    $bool  = true;
                }    
            }
            if($bool) return false;
        }
        //if(preg_match("/".$val."/", $text)) return true;
        if(strpos($text, $val)!==false) return true;
        else return false;
    }

    protected function parseCatalogMeta()
    {
        if($this->checkUniq()) return false;
        if($this->meta_description!="N" || $this->meta_keywords!="N")
        {
            foreach($this->detailHtml["meta"] as $meta)
            {
                if($this->meta_description!="N" && strtolower(pq($meta)->attr("name"))=="description")
                {
                    $meta_text = pq($meta)->attr("content");
                    if(!$meta_text)$meta_text = pq($meta)->attr("value");
                    if(strtoupper(LANG_CHARSET)=="WINDOWS-1251"/* && strtoupper(LANG_CHARSET)!=strtoupper($shs_DOC_ENCODING)*/) {
                        $meta_text = mb_convert_encoding($meta_text, LANG_CHARSET, "utf-8");
                    }
                    $this->arFields["PROPERTY_VALUES"][$this->meta_description] = strip_tags($meta_text);
                }elseif($this->meta_keywords!="N" && strtolower(pq($meta)->attr("name"))=="keywords")
                {
                    $meta_text = pq($meta)->attr("content");
                    if(!$meta_text)$meta_text = pq($meta)->attr("value");
                    if(strtoupper(LANG_CHARSET)=="WINDOWS-1251"/* && strtoupper(LANG_CHARSET)!=strtoupper($shs_DOC_ENCODING)*/) {
                        $meta_text = mb_convert_encoding($meta_text, LANG_CHARSET, "utf-8");
                    }
                    $this->arFields["PROPERTY_VALUES"][$this->meta_keywords] = strip_tags($meta_text);
                }
                unset($meta_text);
            }
        }

        if($this->meta_title!="N")
        {
            $meta_title = pq($this->detailHtml["head:eq(0) title:eq(0)"])->text();
            $meta_title = strip_tags($meta_title);
            if(strtoupper(LANG_CHARSET)=="WINDOWS-1251"/* && strtoupper(LANG_CHARSET)!=strtoupper($shs_DOC_ENCODING)*/) {
                $meta_title = mb_convert_encoding($meta_title, LANG_CHARSET, "utf-8");
            }
            $this->arFields["PROPERTY_VALUES"][$this->meta_title] = $meta_title;
        }

    }

    protected function parseCatalogFirstUrl()
    {
        if($this->checkUniq()) return false;
        if($this->first_title!="N")
        {
            $this->arFields["PROPERTY_VALUES"][$this->first_title] = $this->arFields["LINK"];
        }
    }

    protected function parseCatalogDate()
    {

    }

    protected function parseCatalogPropFile($code, $el)
    {
        if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["props"])) return false;
        $ar = $this->GetArraySrcAttr($this->settings["catalog"]["selector_prop"][$code]);
        $file = $ar["path"];
        $attr = $ar["attr"];
        $n = 0;

        $isElement = $this->checkUniq();

        foreach(pq($el)->find($file) as $f)
        {
            if(!empty($attr)) 
            {
                $src = pq($f)->attr($attr);
            }
            elseif(empty($attr)) 
            {
                $src = pq($f)->html();
                $src = strip_tags(pq($f)->html());
            }
            $descr = strip_tags(pq($f)->html());
            $src = $this->parseCaralogFilterSrc($src);
            $src = $this->getCatalogLink($src);
            $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["VALUE"] = CFile::MakeFileArray($src);
            $this->arrFilesTemp[] = $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["VALUE"]["tmp_name"];
            $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["DESCRIPTION"] = $descr;
            $n++;
        }
        if($isElement)
        {
            $arFiles = $this->arFields["PROPERTY_VALUES"][$code];
            unset($this->arFields["PROPERTY_VALUES"][$code]);
            $obElement = new CIBlockElement;
            $rsProperties = $obElement->GetProperty($this->iblock_id, $isElement, "sort", "asc",  Array("CODE"=>$code));
            while($arProperty = $rsProperties->Fetch())
            {
                $arFiles[$arProperty["PROPERTY_VALUE_ID"]] = array(
                        "tmp_name" => "",
                        "del" => "Y",
                );
            }
            CIBlockElement::SetPropertyValueCode($isElement, $code, $arFiles);
            unset($obElement);
        }
    }
    
    protected function parseCatalogPropFilePreview($code, $el)
    {
        if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["props"])) return false;
        $ar = $this->GetArraySrcAttr($this->settings["catalog"]["selector_prop_preview"][$code]);
        $file = $ar["path"];
        $attr = $ar["attr"];
        $n = 0;

        $isElement = $this->checkUniq();

        foreach(pq($el)->find($file) as $f)
        {
            $src = pq($f)->attr($attr);
            $descr = strip_tags(pq($f)->html());
            $src = $this->parseCaralogFilterSrc($src);
            $src = $this->getCatalogLink($src);
            $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["VALUE"] = CFile::MakeFileArray($src);
            $this->arrFilesTemp[] = $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["VALUE"]["tmp_name"];
            $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["DESCRIPTION"] = $descr;
            $n++;
        }
        
        if($isElement)
        {
            $arFiles = $this->arFields["PROPERTY_VALUES"][$code];
            unset($this->arFields["PROPERTY_VALUES"][$code]);
            $obElement = new CIBlockElement;
            $rsProperties = $obElement->GetProperty($this->iblock_id, $isElement, "sort", "asc",  Array("CODE"=>$code));
            while($arProperty = $rsProperties->Fetch())
            {
                $arFiles[$arProperty["PROPERTY_VALUE_ID"]] = array(
                        "tmp_name" => "",
                        "del" => "Y",
                );
            }
            CIBlockElement::SetPropertyValueCode($isElement, $code, $arFiles);
            unset($obElement);
        }
    }

    public function parseCatalogProp($code, $val, $text)
    {
        //$text = str_replace($val, "", $text);
        
        $val = preg_quote($val, "/");
        $text = preg_replace("/(".$val.")/", "", $text, 1);
        
        $val = trim($text);
        
        if(empty($val)) return false;
        $val = html_entity_decode($val);
        $arProp = $this->arProperties[$code];
        
        //$default = $this->settings["catalog"]["default_prop"][$code];
        
        if($arProp["PROPERTY_TYPE"]!="N" && isset($this->settings["loc"]["f_props"]) && $this->settings["loc"]["f_props"])
            $val = $this->locText($val, $arProp["USER_TYPE"]=="HTML"?"html":"plain");
        
        if($arProp["USER_TYPE"]=="HTML" && $arProp["MULTIPLE"]!="Y")
        {
            $this->arFields["PROPERTY_VALUES"][$code] = Array("VALUE" => Array ("TEXT" => $val, "TYPE" => "html"));
        }elseif($arProp["USER_TYPE"]=="HTML" && $arProp["MULTIPLE"]=="Y")
        {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = Array("VALUE" => Array ("TEXT" => $val, "TYPE" => "html"));
        }
        elseif($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]!="Y" && $arProp["USER_TYPE"]=="directory")
        {
            $this->arFields["PROPERTY_VALUES"][$code] = $this->CheckPropsDirectory($arProp, $code, $val);;
        }
        elseif($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]=="Y" && $arProp["USER_TYPE"]=="directory")
        {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $this->CheckPropsDirectory($arProp, $code, $val);;    
        }
        elseif($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]!="Y")
        {
            $val = $this->actionFieldProps($code, $val);
            $this->arFields["PROPERTY_VALUES"][$code] = $val;
        }elseif($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]=="Y")
        {
            $val = $this->actionFieldProps($code, $val);
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $val;
        }
        elseif($arProp["PROPERTY_TYPE"]=="N")
        {
            $val =  str_replace(",", ".", $val);
            $val = preg_replace("/\.{1}$/", "", $val);
            $val = preg_replace('/[^0-9.]/', "", $val);
            $this->arFields["PROPERTY_VALUES"][$code] = $val;    

        }elseif($arProp["PROPERTY_TYPE"]=="L" && $arProp["MULTIPLE"]!="Y")
        {
            $this->arFields["PROPERTY_VALUES"][$code] = $this->CheckPropsL($arProp["ID"], $code, $val);
        }elseif($arProp["PROPERTY_TYPE"]=="L" && $arProp["MULTIPLE"]=="Y")
        {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $this->CheckPropsL($arProp["ID"], $code, $val);
        }elseif($arProp["PROPERTY_TYPE"]=="E" && $arProp["MULTIPLE"]!="Y")
        {   
            $this->arFields["PROPERTY_VALUES"][$code] = $this->CheckPropsE($arProp, $code, $val);
        }elseif($arProp["PROPERTY_TYPE"]=="E" && $arProp["MULTIPLE"]=="Y")
        {   
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $this->CheckPropsE($arProp, $code, $val);
        }
    }

    public function parseCatalogPropOffer($code, $val, $text)
    {
        //$text = str_replace($val, "", $text);
        
        $val = preg_quote($val, "/");
        $text = preg_replace("/(".$val.")/", "", $text, 1);
        
        $val = trim($text);
        
        if(empty($val)) return false;
        $val = html_entity_decode($val);
        $arProp = $this->arPropertiesOffer[$code];
        
        if($arProp["PROPERTY_TYPE"]!="N" && isset($this->settings["loc"]["f_props"]) && $this->settings["loc"]["f_props"])
            $val = $this->locText($val, $arProp["USER_TYPE"]=="HTML"?"html":"plain");
        
        //$default = $this->settings["catalog"]["default_prop"][$code];
        if($arProp["USER_TYPE"]=="HTML" && $arProp["MULTIPLE"]!="Y")
        {
            $this->arOffer["PROPERTY_VALUES"][$code] = Array("VALUE" => Array ("TEXT" => $val, "TYPE" => "html"));
        }elseif($arProp["USER_TYPE"]=="HTML" && $arProp["MULTIPLE"]=="Y")
        {
            $this->arOffer["PROPERTY_VALUES"][$code]["n0"] = Array("VALUE" => Array ("TEXT" => $val, "TYPE" => "html"));
        }
        elseif($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]!="Y" && $arProp["USER_TYPE"]=="directory")
        {
            $this->arOffer["PROPERTY_VALUES"][$code] = $this->CheckPropsDirectory($arProp, $code, $val);;    
        }
        elseif($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]=="Y" && $arProp["USER_TYPE"]=="directory")
        {
            $this->arOffer["PROPERTY_VALUES"][$code]["n0"] = $this->CheckPropsDirectory($arProp, $code, $val);;    
        }
        elseif($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]!="Y")
        {
            $this->arOffer["PROPERTY_VALUES"][$code] = $val;
        }elseif($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]=="Y")
        {
            $this->arOffer["PROPERTY_VALUES"][$code]["n0"] = $val;
        }
        elseif($arProp["PROPERTY_TYPE"]=="N")
        {
            $val =  str_replace(",", ".", $val);
            $val = preg_replace("/\.{1}$/", "", $val);
            $val = preg_replace('/[^0-9.]/', "", $val);
            $this->arOffer["PROPERTY_VALUES"][$code] = $val;    
            
        }elseif($arProp["PROPERTY_TYPE"]=="L" && $arProp["MULTIPLE"]!="Y")
        {
            $this->arOffer["PROPERTY_VALUES"][$code] = $this->CheckPropsLOffer($arProp["ID"], $code, $val);
        }elseif($arProp["PROPERTY_TYPE"]=="L" && $arProp["MULTIPLE"]=="Y")
        {
            $this->arOffer["PROPERTY_VALUES"][$code]["n0"] = $this->CheckPropsLOffer($arProp["ID"], $code, $val);
        }elseif($arProp["PROPERTY_TYPE"]=="E" && $arProp["MULTIPLE"]!="Y")
        {   
            $this->arOffer["PROPERTY_VALUES"][$code] = $this->CheckPropsE($arProp, $code, $val);
        }elseif($arProp["PROPERTY_TYPE"]=="E" && $arProp["MULTIPLE"]=="Y")
        {   
            $this->arOffer["PROPERTY_VALUES"][$code]["n0"] = $this->CheckPropsE($arProp, $code, $val);
        }
    }
    
    public function parseCatalogDefaultProp($code, $val)
    {
        $val = trim($val);
        $arProp = $this->arProperties[$code];
        if(empty($val)) return false;
        if($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]!="Y" && $arProp["USER_TYPE"]=="directory")
        {
            $this->arFields["PROPERTY_VALUES"][$code] = $val;    
        }
        elseif($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]=="Y" && $arProp["USER_TYPE"]=="directory")
        {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $val;    
        }
        elseif($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]!="Y")
        {
            $this->arFields["PROPERTY_VALUES"][$code] = $val;
        }
        elseif($arProp["PROPERTY_TYPE"]=="S" && $arProp["MULTIPLE"]=="Y")
        {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $val;    
        }
        elseif($arProp["PROPERTY_TYPE"]=="N")
        {
            $val =  str_replace(",", ".", $val);
            $val = preg_replace("/\.{1}$/", "", $val);
            $val = preg_replace('/[^0-9.]/', "", $val);
            $this->arFields["PROPERTY_VALUES"][$code] = $val;    

        }elseif($arProp["PROPERTY_TYPE"]=="L" && $arProp["MULTIPLE"]!="Y")
        {
            $this->arFields["PROPERTY_VALUES"][$code] = $val;
        }elseif($arProp["PROPERTY_TYPE"]=="L" && $arProp["MULTIPLE"]=="Y")
        {
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $val;
        }elseif($arProp["PROPERTY_TYPE"]=="E" && $arProp["MULTIPLE"]!="Y")
        {   
            $this->arFields["PROPERTY_VALUES"][$code] = $this->CheckPropsE($arProp, $code, $val);
        }elseif($arProp["PROPERTY_TYPE"]=="E" && $arProp["MULTIPLE"]=="Y")
        {   
            $this->arFields["PROPERTY_VALUES"][$code]["n0"] = $this->CheckPropsE($arProp, $code, $val);
        }
    }

    public function CheckPropsDirectory($arProp, $code, $val)
    {
        if (empty($val) || ($arProp["USER_TYPE"] != "directory")) return false;
        $element_xml_id = Cutil::translit($val, 'ru', array('change_case' => 'U'));
        $element_xml_id = self::GetCleanCode($element_xml_id);
        /*$this->getDirectoryValues($arProp);
        $nameTable = $arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"];
        if(empty($this->arDirectory[$nameTable]))
        {
            if(isset($this->arDirectory[$nameTable][$element_xml_id]) && !empty($this->arDirectory[$nameTable][$element_xml_id]))
            {
                return  $element_xml_id;
            }else{
                $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList(array(
                    "filter" => array(
                    "TABLE_NAME" => $arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"],
                )))->fetch();
                $arFields = array(
                        "UF_XML_ID" => $element_xml_id,
                        "UF_NAME" => $val,
                );
                $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
                $entity_data_class = $entity->getDataClass();
                $entity_data_class::add($arFields);
                return $element_xml_id;    
            }    
        }*/
        $rssData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('TABLE_NAME'=>$arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"])));
        $arr = $rssData->Fetch();
        $hlblock_id = $arr["ID"];
        $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getById($hlblock_id)->fetch();
        $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $entity_table_name = $hlblock['TABLE_NAME'];

        $arFilter = array("UF_NAME"=>$val); 

        $sTableID = 'tbl_'.$entity_table_name;
        $rsData = $entity_data_class::getList(array(
            "select" => array('UF_XML_ID'), 
            "filter" => $arFilter,
            "order" => array("UF_SORT"=>"ASC") 
        ));
        $rsData = new CDBResult($rsData, $sTableID);
        $arRes = $rsData->Fetch();
        if($arRes)
        {
            return $arRes["UF_XML_ID"];
        }     
        else
        {
            $data = array(
              "UF_NAME" => $val,
              "UF_SORT" => 100,
              "UF_XML_ID" => $element_xml_id,
            );
            $result = $entity_data_class::add($data);
            return $data["UF_XML_ID"];
        }
    }
    
    public function GetCleanCode($code) {

        if(is_numeric($code[0])) {
            $code = 'N'.$code;
        }

        return $code;
    }
    
    public function getDirectoryValues($arProp)
    {
        $nameTable = $arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"];
        if(isset($this->arDirectory[$nameTable])) return false;
        $directorySelect = array("*");
        $directoryOrder = array();
        $entityGetList = array(
            'select' => $directorySelect,
            'order' => $directoryOrder
        );
        $arProperty = array();
        $highBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array("filter" => array('TABLE_NAME' => $nameTable)))->fetch();
        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($highBlock);
        $entityDataClass = $entity->getDataClass();
        $propEnums = $entityDataClass::getList($entityGetList);
        $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE"][] = GetMessage("parser_prop_default");
        $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE_ID"][] = "";
        while ($oneEnum = $propEnums->fetch())
        {
            $arProperty[$oneEnum["UF_XML_ID"]] = $oneEnum["UF_XML_ID"];
        } 
        $this->arDirectory[$nameTable] = $arProperty;
    }
    
    public function CheckPropsE($arProp, $code, $val)
    {
        $IBLOCK_ID = $arProp["LINK_IBLOCK_ID"];

        $rsProp = CIBlockElement::GetList(Array(), array("IBLOCK_ID"=>$IBLOCK_ID, "%NAME"=>$val), false, false, array("ID", "NAME"));
        while($arIsProp = $rsProp->Fetch())
        {
            $arIsProp["NAME"] = mb_strtolower($arIsProp["NAME"], LANG_CHARSET); 
            $val0 = mb_strtolower($val, LANG_CHARSET);
            if($val0==$arIsProp["NAME"])
            {
                $isProp = $arIsProp["ID"];
            }
        }
        
        if($isProp) return $isProp;
        else{
            $codeText = CUtil::translit($val, "ru", array(
                        "max_len" => 100,
                        "change_case" => 'L', // 'L' - toLower, 'U' - toUpper, false - do not change
                        "replace_space" => '_',
                        "replace_other" => '_',
                        "delete_repeat_replace" => true,
            ));
            $arFields = array(
                "NAME"=>$val,
                "ACTIVE" => "Y",
                "IBLOCK_ID" => $IBLOCK_ID,
                "CODE" => $codeText
            );
            $el = new CIBlockElement;
            $id = $el->Add($arFields);
            if(!$id)
            {
                $this->errors[] = GetMessage("error_add_prop_e").$this->arFields["NAME"]."[".$this->arFields["LINK"]."] - ".$el->LAST_ERROR;
            }
            unset($el);
            return $id;
        }
    }

    public function CheckPropsL($id, $code, $val)
    {
        $res2 = CIBlockProperty::GetPropertyEnum(
            $id,
            array(),
            array("IBLOCK_ID" => $this->iblock_id, "VALUE" => $val)
        );

        if ($arRes2 = $res2->Fetch())
        {
            $kz = $arRes2["ID"];
        }
        else
        {
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
    
    public function CheckPropsLOffer($id, $code, $val)
    {
        $res2 = CIBlockProperty::GetPropertyEnum(
            $id,
            array(),
            array("IBLOCK_ID" => $this->iblockOffer, "VALUE" => $val)
        );

        if ($arRes2 = $res2->Fetch())
        {
            $kz = $arRes2["ID"];
        }
        else
        {
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

    public function getFindProduct()
    {
        if(isset($this->settings["catalog"]["find_product"]) && !empty($this->settings["catalog"]["find_product"]))foreach($this->settings["catalog"]["find_product"] as $i=>$prop)
        {
            $prop = trim($prop);
            if(!empty($prop))
            {
                $arProps[$i] = $prop;
            }
        }
        if(!$arProps) return false;
        return $arProps;
    }

    public function getSelectorProduct()
    {
        if(isset($this->settings["catalog"]["selector_product"]) && !empty($this->settings["catalog"]["selector_product"]))foreach($this->settings["catalog"]["selector_product"] as $i=>$prop)
        {
            $prop = trim($prop);
            if(!empty($prop))
            {
                $arProps[$i] = $prop;
            }
        }
        if(!$arProps) return false;
        return $arProps;
    }
    
    public function getFindDubleProperties()
    {
        $arFindProps = $this->arFindProperties;
        if(!empty($arFindProps))foreach($arFindProps as $code=>$prop)
        {
            foreach($arFindProps as $code1=>$prop1)
            {
                if(strpos($prop1, $prop)!==false && $code1!=$code && $prop1!=$prop)
                {
                    $arDubleProps[$code][] = $code1;
                }
            }        
        }
        if(isset($arDubleProps)) return $arDubleProps;
        else return false;    
    }

    public function getFindDublePropertiesOffer()
    {
        $arFindProps = $this->arFindPropertiesOffer;
        if(!empty($arFindProps))foreach($arFindProps as $code=>$prop)
        {
            foreach($arFindProps as $code1=>$prop1)
            {
                if(strpos($prop1, $prop)!==false && $code1!=$code && $prop1!=$prop)
                {
                    $arDubleProps[$code][] = $code1;
                }    
            }
        }
        if(isset($arDubleProps)) return $arDubleProps;
        else return false;    
    }
    
    protected function getFindDublePropertiesPreview()
    {
        $arFindProps = $this->arFindPropertiesPreview;
        if(!empty($arFindProps))foreach($arFindProps as $code=>$prop)
        {
            foreach($arFindProps as $code1=>$prop1)
            {
                if(strpos($prop1, $prop)!==false && $code1!=$code && $prop1!=$prop)
                {
                    $arDubleProps[$code][] = $code1;
                }    
            }        
        }
        if(isset($arDubleProps)) return $arDubleProps;
        else return false;    
    }

    protected function getFindProperties()
    {
        if(isset($this->settings["catalog"]["find_prop"]) && !empty($this->settings["catalog"]["find_prop"]))
        {
            foreach($this->settings["catalog"]["find_prop"] as $i=>$prop)
            {
                $prop = trim($prop);
                if(!empty($prop))
                {
                    $arProps[$i] = $prop;
                }
            }
            if(!isset($arProps)) return false;
            return $arProps;
        }
        return false;        
    }
    
    protected function getFindPropertiesOffer()
    {
        if(isset($this->settings["offer"]["find_prop"]) && !empty($this->settings["offer"]["find_prop"]))
        {
            foreach($this->settings["offer"]["find_prop"] as $i=>$prop)
            {
                $prop = trim($prop);
                if(!empty($prop))
                {
                    $arProps[$i] = $prop;
                }
            }
            if(!isset($arProps)) return false;
            return $arProps;
        }
        return false;        
    }
    
    protected function getFindPropertiesPreview()
    {
        if(isset($this->settings["catalog"]["find_prop_preview"]) && !empty($this->settings["catalog"]["find_prop_preview"]))
        {
            foreach($this->settings["catalog"]["find_prop_preview"] as $i=>$prop)
            {
                $prop = trim($prop);
                if(!empty($prop))
                {
                    $arProps[$i] = $prop;
                }
            }
            if(!isset($arProps)) return false;
            return $arProps;
        }
        return false;
        
    }

    protected function getSelectorProperties()
    {
        if(isset($this->settings["catalog"]["selector_prop"]) && !empty($this->settings["catalog"]["selector_prop"]))
        {
            $arProps = false;
            foreach($this->settings["catalog"]["selector_prop"] as $i=>$prop)
            {
                $prop = trim($prop);
                if(!empty($prop))
                {
                    $arProps[$i] = $prop;
                }
            }
            if(!$arProps) return false;
            return $arProps;    
        }
        
        return false;

    }
    
    protected function getSelectorPropertiesOffer()
    {
        if(isset($this->settings["offer"]["selector_prop"]) && !empty($this->settings["offer"]["selector_prop"]))
        {
            $arProps = false;
            foreach($this->settings["offer"]["selector_prop"] as $i=>$prop)
            {
                $prop = trim($prop);
                if(!empty($prop))
                {
                    $arProps[$i] = $prop;
                }
            }
            if(!$arProps) return false;
            return $arProps;    
        }
        elseif(isset($this->settings["offer"]["selector_prop_more"]) && !empty($this->settings["offer"]["selector_prop_more"]))
        {
            $arProps = false;
            foreach($this->settings["offer"]["selector_prop_more"] as $i=>$prop)
            {
                $prop = trim($prop);
                if(!empty($prop))
                {
                    $arProps[$i] = $prop;
                }
            }
            if(!$arProps) return false;
            return $arProps;
        }
        
        return false;
    }
    
    protected function getSelectorPropertiesPreview()
    {
        if(isset($this->settings["catalog"]["selector_prop_preview"]) && !empty($this->settings["catalog"]["selector_prop_preview"]))
        {
            $arProps = false;
            foreach($this->settings["catalog"]["selector_prop_preview"] as $i=>$prop)
            {
                $prop = trim($prop);
                if(!empty($prop))
                {
                    $arProps[$i] = $prop;
                }
            }
            if(!$arProps) return false;
            return $arProps;    
        }
       
        return false;
    }

    protected function parseCatalogDetailMorePhoto(&$el)
    {
        if($this->settings["catalog"]["more_image_props"])
        {
            if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["more_img"])) return false;
            $code = $this->settings["catalog"]["more_image_props"];
            $ar = $this->GetArraySrcAttr($this->settings["catalog"]["more_image"]);
            $image = $ar["path"];
            $attr = $ar["attr"];
            $n = 0;

            $isElement = $this->checkUniq();
            foreach(pq($el)->find($image) as $img)
            {   
                if(!empty($attr))
                {
                    $src = pq($img)->attr($attr);
                    $src = $this->parseSelectorStyle($attr, $src);
                }
                elseif(empty($attr))
                {
                    $src = strip_tags(pq($img)->html());
                }
                $src = $this->parseCaralogFilterSrc($src);
                $src = $this->getCatalogLink($src);
                if(isset($this->arPhoto[$src])) continue 1;
                $this->arPhoto[$src] = 1;
                $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["VALUE"] = CFile::MakeFileArray($src);
                $this->arrFilesTemp[] = $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["VALUE"]["tmp_name"];
                $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["DESCRIPTION"] = "";
                $n++;
            }
            if($isElement)
            {
                $arImages = $this->arFields["PROPERTY_VALUES"][$code];
                unset($this->arFields["PROPERTY_VALUES"][$code]);
                $obElement = new CIBlockElement;
                $rsProperties = $obElement->GetProperty($this->iblock_id, $isElement, "sort", "asc",  Array("CODE"=>$code));
                while($arProperty = $rsProperties->Fetch())
                {
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
        $this->detailPage = $this->fileHtml->file_get_html($this->arFields["LINK"], $this->settings["catalog"]["proxy"], $this->auth, $this); 
        foreach(GetModuleEvents("shs.parser", "parserCatalogDetailPageAfter", true) as $arEvent)
            ExecuteModuleEventEx($arEvent, array(&$this));
        $this->DeleteCharsetHtml5($this->detailPage);
        $this->detailHttpCode = $this->fileHtml->httpCode;
        if($this->detailHttpCode!=200 && $this->detailHttpCode!=301 && $this->detailHttpCode!=302 && $this->detailHttpCode!=303)
        {
            $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."]".GetMessage("parser_error_connect")."[".$this->detailHttpCode."]";
            //return false;
        }
        $this->detailHtml = phpQuery::newDocument($this->detailPage, "text/html;charset=".LANG_CHARSET);
        $this->base = $this->GetMetaBase($this->detailHtml);
        //if($this->detail_delete_element)$this->deleteCatalogElement($this->detail_delete_element, $this->detail_dom, $this->detailHtml[$this->detail_dom]);
        //if($this->detail_delete_attribute)$this->deleteCatalogAttribute($this->detail_delete_attribute, $this->detail_dom, $this->detailHtml[$this->detail_dom]);

        foreach($this->detailHtml[$this->detail_dom] as $detail)
        {
            return $detail;
        }
        $this->errors[] = GetMessage("parser_error_selecto_detail_notfound");


    }

    protected function parseCatalogUrlPreview($el)
    {           
        if($this->settings["catalog"]["href"]=="a:parent")
        {
            $p = pq($el)->attr("href");        
        }else{
            $url = $this->settings["catalog"]["href"]?$this->settings["catalog"]["href"]:"a:eq(0)";
            $this->settings["catalog"]["href"] = $url;
            $p = pq($el)->find($url)->attr("href");  
        }
        if(!$p)
        { 
            $this->errors[] = GetMessage("parser_error_href_notfound");
            return false;
        }

        $p = $this->getCatalogLink($p);
        //$this->convetCyrillic($p);
        $this->arFields["LINK"] = $p;
        if(isset($this->pagePrevElement[$p])) return false;
        return true;
    }

    protected function parseCatalogNamePreview($el)
    {
        if(isset($this->settings["catalog"]["detail_name"]) && $this->settings["catalog"]["detail_name"]) return false;
        $name = $this->settings["catalog"]["name"]?$this->settings["catalog"]["name"]:$this->settings["catalog"]["href"];
        $ar = $this->GetArraySrcAttr($name); 
        $img = $ar["path"];
        $attr = $ar["attr"];
        if (empty($attr)){
            $this->arFields["NAME"] = trim(htmlspecialchars_decode(trim(strip_tags(pq($el)->find($img)->html()))));
        }
        elseif (!empty($attr)){
            $this->arFields["NAME"] = trim(htmlspecialchars_decode(trim(strip_tags(pq($el)->find($img)->attr($attr)))));
        }
        
        if($this->arFields["NAME"])
        {
            $this->arFields["NAME"] = $this->actionFieldProps("SOTBIT_PARSER_NAME_E", $this->arFields["NAME"]);
            if(isset($this->settings["loc"]["f_name"]) && $this->settings["loc"]["f_name"]=="Y")
            {
                $this->arFields["NAME"] = $this->locText($this->arFields["NAME"]);    
            }
            
        }
        
        if(!$this->arFields["NAME"])
        {
            $this->errors[] = GetMessage("parser_error_name_notfound");
            return false;
        }
    }
    
    protected function actionFieldProps($code, $val)
    {
        if(isset($this->settings["catalog"]["action_props_val"][$code]) && $this->settings["catalog"]["action_props_val"][$code])
        {
            foreach($this->settings["catalog"]["action_props_val"][$code] as $i=>$v)
            {   
                {   
                    if($this->settings["catalog"]["action_props"][$code][$i]=="") continue 1;
                    if($this->settings["catalog"]["action_props"][$code][$i]=="delete")
                    {
                        $val = str_replace($v, "", $val);    
                    }
                    if($this->settings["catalog"]["action_props"][$code][$i]=="add_b")
                    {
                        $val = $v.$val;    
                    }
                    if($this->settings["catalog"]["action_props"][$code][$i]=="add_e")
                    {
                        $val = $val.$v;    
                    }    
                }
            }
                    
        }
        return trim($val);
    }
    
    protected function parseCatalogNameDetail($el)
    {
        if($this->detail_delete_element)$this->deleteCatalogElement($this->detail_delete_element, $this->detail_dom, $this->detailHtml[$this->detail_dom]);
        if($this->detail_delete_attribute)$this->deleteCatalogAttribute($this->detail_delete_attribute, $this->detail_dom, $this->detailHtml[$this->detail_dom]);
        
        if(!isset($this->settings["catalog"]["detail_name"]) || !$this->settings["catalog"]["detail_name"]) return false;
        $name = $this->settings["catalog"]["detail_name"];

        $this->arFields["NAME"] = htmlspecialchars_decode(trim(strip_tags(pq($el)->find($name)->html())));
        if($this->arFields["NAME"])
        {
            $this->arFields["NAME"] = $this->actionFieldProps("SOTBIT_PARSER_NAME_E", $this->arFields["NAME"]);
            if(isset($this->settings["loc"]["f_name"]) && $this->settings["loc"]["f_name"]=="Y")
            {
                $this->arFields["NAME"] = $this->locText($this->arFields["NAME"]);
            }
        }
        if(!$this->arFields["NAME"])
        {
            $this->errors[] = GetMessage("parser_error_name_notfound");
            return false;
        }
    }
    
    protected function parseCatalogPriceFormat($price)
    {
        $price = trim($price);
        
        if(isset($this->settings["catalog"]["price_format1"]) && $this->settings["catalog"]["price_format1"] && isset($this->settings["catalog"]["price_format2"]) && $this->settings["catalog"]["price_format2"])
        {
            $price = str_replace($this->settings["catalog"]["price_format1"], "", $price);
            $price = str_replace($this->settings["catalog"]["price_format2"], ".", $price);
        }
        else $price = str_replace(",", ".", $price);
        
        $price = preg_replace("/\.{1}$/", "", $price);
        $price = preg_replace('/[^0-9.]/', "", $price);
        return $price;
    }
    
    protected function parseCatalogPriceOkrug($price)
    {   
        $price = trim($price);
        if($price)
        {
            if(isset($this->settings["catalog"]["price_okrug"]))
            {
                if($this->settings["catalog"]["price_okrug"]=="up")
                {
                    if(!isset($this->settings["catalog"]["price_okrug_delta"]) || !$this->settings["catalog"]["price_okrug_delta"])
                        $delta = 0;
                    else
                        $delta = $this->settings["catalog"]["price_okrug_delta"];
                        
                    $price = round($price, $delta);
                }elseif($this->settings["catalog"]["price_okrug"]=="ceil")
                {
                    $price = ceil($price);    
                }elseif($this->settings["catalog"]["price_okrug"]=="floor")
                {
                    $price = floor($price);    
                }
            }
        }
        
        return $price;
    }

    protected function parseCatalogPricePreview(&$el)
    {
        if($this->settings["catalog"]["preview_price"])
        {
            if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["price"])) return false;
            $price = $this->settings["catalog"]["preview_price"];
            $price = $this->GetArraySrcAttr($price);
            $path = $price["path"];
            $attr = $price["attr"];
            if (empty($attr)) $price = strip_tags(pq($el)->find($path)->html());
            elseif(!empty($attr)) $price = trim(pq($el)->find($path)->attr($attr));
            $price = $this->parseCatalogPriceFormat($price);
            //$price = $this->parseCatalogPriceOkrug($price);
            
            $this->arPrice["PRICE"] = $price;
            $this->arPrice["PRICE"] = trim($this->arPrice["PRICE"]);
            if(!$this->arPrice["PRICE"])
            {
                $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."]".GetMessage("parser_error_price_notfound");
                unset($this->arPrice["PRICE"]);
                return false;
            }
            $this->arPrice["CATALOG_GROUP_ID"] = $this->settings["catalog"]["price_type"];
            $this->arPrice["CURRENCY"] = $this->settings["catalog"]["currency"];
        }

    }

    protected function parseCatalogPriceDetail(&$el)
    {

        if($this->settings["catalog"]["detail_price"])
        {
            if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["price"])) return false;
            $price = $this->settings["catalog"]["detail_price"];
            $price = strip_tags(pq($el)->find($price)->html());
            $price = $this->parseCatalogPriceFormat($price);
            //$price = $this->parseCatalogPriceOkrug($price);
            
            $this->arPrice["PRICE"] = $price;
            $this->arPrice["PRICE"] = trim($this->arPrice["PRICE"]);
            if(!$this->arPrice["PRICE"])
            {
                $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."]".GetMessage("parser_error_price_notfound");
                unset($this->arPrice["PRICE"]);
                return false;
            }
            $this->arPrice["CATALOG_GROUP_ID"] = $this->settings["catalog"]["price_type"];
            $this->arPrice["CURRENCY"] = $this->settings["catalog"]["currency"];
        }

    }

    protected function parseCatalogDescriptionPreview(&$el)
    {
        if($this->checkUniq() && (!$this->isUpdate || $this->isUpdate["preview_descr"]=="N")) return false;
        if($this->settings["catalog"]["preview_text_selector"] && $this->settings["catalog"]["text_preview_from_detail"]!="Y")
        {
            $preview = $this->settings["catalog"]["preview_text_selector"];
            foreach(pq($el)->find($preview." img") as $img)
            {
                $src = pq($img)->attr("src");
                $src = $this->parseCaralogFilterSrc($src);
                $src = $this->getCatalogLink($src);
                $this->parseCatalogSaveImgServer($img, $src);
            }

            if($this->bool_preview_delete_tag=="Y")$preview_text = strip_tags(pq($el)->find($preview)->html(), htmlspecialcharsBack($this->preview_delete_tag));
            else $preview_text = pq($el)->find($preview)->html();
            
            
            $preview_text = trim($preview_text);
            if(isset($this->settings["loc"]["f_preview_text"]) && $this->settings["loc"]["f_preview_text"]=="Y")
            {
                $preview_text = $this->locText($preview_text, $this->preview_text_type=="html"?"html":"plain");    
            }
            
            $this->arFields["PREVIEW_TEXT"] = trim($preview_text);
            $this->arFields["PREVIEW_TEXT_TYPE"] = $this->preview_text_type;
        }
    }

    protected function parseCatalogDescriptionDetail(&$el)
    {
        //if($this->detail_delete_element)$this->deleteCatalogElement($this->detail_delete_element, $this->detail_dom, $this->detailHtml[$this->detail_dom]);
        //if($this->detail_delete_attribute)$this->deleteCatalogAttribute($this->detail_delete_attribute, $this->detail_dom, $this->detailHtml[$this->detail_dom]);
        if($this->checkUniq() && (!$this->isUpdate || (!$this->isUpdate["detail_descr"] && (!$this->isUpdate["preview_descr"] && !$this->settings["catalog"]["text_preview_from_detail"]!="Y")))) return false;
        if($this->settings["catalog"]["detail_text_selector"])
        {
            $detail = $this->settings["catalog"]["detail_text_selector"];
            $arDetail = explode(",", $detail);
            $detail_text = "";
            if($arDetail && !empty($arDetail))
            {
                foreach($arDetail as $detail)
                {
                    $detail = trim($detail);
                    if(!$detail) continue 1;

                    foreach(pq($el)->find($detail." img") as $img)
                    {
                        $src = pq($img)->attr("src");
                        $src = $this->parseCaralogFilterSrc($src);
                        $src = $this->getCatalogLink($src);
                        $this->parseCatalogSaveImgServer($img, $src);
                    }

                    if($this->bool_detail_delete_tag=="Y")$detail_text .= strip_tags(pq($el)->find($detail)->html(), htmlspecialcharsBack($this->detail_delete_tag));
                    else $detail_text .= pq($el)->find($detail)->html();

                }
            }

            /*foreach(pq($el)->find($detail." img") as $img)
            {
                $src = pq($img)->attr("src");
                $src = $this->parseCaralogFilterSrc($src);
                $src = $this->getCatalogLink($src);
                $this->parseCatalogSaveImgServer($img, $src);
            }
            if($this->bool_detail_delete_tag=="Y")$detail_text = strip_tags(pq($el)->find($detail)->html(), htmlspecialcharsBack($this->detail_delete_tag));
            else $detail_text = pq($el)->find($detail)->html();*/
            $detail_text = trim($detail_text);
            if(isset($this->settings["loc"]["f_detail_text"]) && $this->settings["loc"]["f_detail_text"]=="Y")
            {
                $detail_text = $this->locText($detail_text, $this->detail_text_type=="html"?"html":"plain");    
            }
            $this->arFields["DETAIL_TEXT"] = $detail_text;
            $this->arFields["DETAIL_TEXT_TYPE"] = $this->detail_text_type;
            if($this->settings["catalog"]["text_preview_from_detail"]=="Y")
            {
                $this->arFields["PREVIEW_TEXT"] = $this->arFields["DETAIL_TEXT"];
                $this->arFields["PREVIEW_TEXT_TYPE"] = $this->arFields["DETAIL_TEXT_TYPE"];
            }
        }
    }

    public function parseCatalogDetailPicture(&$el) //27.10.2015
    {   
        if($this->checkUniq() && (!$this->isUpdate || (!$this->isUpdate["detail_img"] && (!$this->isUpdate["preview_img"] && !$this->settings["catalog"]["img_preview_from_detail"]!="Y")))) return false;
        if($this->settings["catalog"]["detail_picture"])
        {    
            $arSelPic = explode(",", $this->settings["catalog"]["detail_picture"]);

            foreach($arSelPic as $sel)
            {
                $sel = trim($sel);
                if(empty($sel)) continue;
                $ar = $this->GetArraySrcAttr($sel); 
                $img = $ar["path"];
                $attr = $ar["attr"];
                if(!empty($attr))
                {
                    $src = pq($el)->find($img)->attr($attr);
                    $src = $this->parseSelectorStyle($attr, $src);  
                }
                elseif(empty($attr))
                {
                    $src = pq($el)->find($img)->text();  
                }
                /*
                **????? ??? ??????? base64 ????????? ????????
                **$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));
                **file_put_contents('image.png', $data);
                */
                
                $src = $this->parseCaralogFilterSrc($src);
                $src = $this->getCatalogLink($src);
                foreach(GetModuleEvents("shs.parser", "ParserDetailPicture", true) as $arEvent) //27.10.2015
                    ExecuteModuleEventEx($arEvent, array(&$this, $src));
                if(!self::CheckImage($src)) continue;
                $this->arPhoto[$src] = 1;
                //$src = str_replace("cdn.", "", $src);
                $this->arFields["DETAIL_PICTURE"] = CFile::MakeFileArray($src);
                
                $this->arrFilesTemp[] = $this->arFields["DETAIL_PICTURE"]["tmp_name"];

                if($this->settings["catalog"]["img_preview_from_detail"]=="Y")
                {
                    $this->arFields["PREVIEW_PICTURE"] = $this->arFields["DETAIL_PICTURE"];
                }
            }

        }
    }

    protected function CheckImage($src)
    {
        if(!empty($src) && preg_match("/(jpeg|jpg|gif|png|JPEG|JPG|GIF|PNG)$/", $src))
        {
            return true;
        }else return false;
    }

    public function parseCatalogPreviewPicturePreview(&$el) //27.10.2015
    {
        if($this->checkUniq() && (!$this->isUpdate || !$this->isUpdate["preview_img"])) return false;
        if($this->settings["catalog"]["preview_picture"] && $this->settings["catalog"]["img_preview_from_detail"]!="Y")
        {   
            $ar = $this->GetArraySrcAttr($this->settings["catalog"]["preview_picture"]);
            $img = $ar["path"];
            $attr = $ar["attr"];
            if(!empty($attr)) 
            {
                $src = pq($el)->find($img)->attr($attr);
                $src = $this->parseSelectorStyle($attr, $src);
            }
            elseif(empty($attr))
            {
                $src = pq($el)->find($img)->text();
            }
            $src = $this->parseCaralogFilterSrc($src);
            $src = $this->getCatalogLink($src);
            foreach(GetModuleEvents("shs.parser", "ParserPreviewPicture", true) as $arEvent) //27.10.2015
                    ExecuteModuleEventEx($arEvent, array(&$this, $src));
            //$src = str_replace("cdn.", "", $src);
            if(!self::CheckImage($src)) return;
            $this->arFields["PREVIEW_PICTURE"] = CFile::MakeFileArray($src);
            $this->arrFilesTemp[] = $this->arFields["PREVIEW_PICTURE"]["tmp_name"];
        }
    }

    protected function parseSelectorStyle($attr, $src)
    {
        if($attr=="style" && $src)
        {
            preg_match("/url\(([^)]*)\)/", $src, $matches);
            if(isset($matches[1]) && $matches[1])
            {
                $src = str_replace(array('"', "'"), "", $matches[1]);
            }
        }

        return $src;
    }

    protected function parseCatalogSaveImgServer($img, $src){
        
        $arImg = CFile::MakeFileArray($src);
        $this->arrFilesTemp[] = $arImg["tmp_name"];
        
        if(isset($this->albumID) && $this->albumID)
            $this->addAlbumCollection($arImg, $img);
        else{
            $fid = CFile::SaveFile($arImg, "shs.parser");
            pq($img)->attr('src', CFile::GetPath($fid));    
        }
        

    }
    
    protected function createAlbum()
    {   
        CModule::IncludeModule("fileman");
        CMedialib::Init();
        $arCollections = CMedialibCollection::GetList(array('arOrder'=>Array('NAME'=>'ASC'),'arFilter' => array('ACTIVE' => 'Y', "NAME"=>"SOTBIT_PARSER")));
        if(!$arCollections)
        {
            $this->albumID = CMedialibCollection::Edit(array("arFields" => array("NAME"=>"SOTBIT_PARSER")));    
        }else
            $this->albumID = $arCollections[0]["ID"];
    }
    
    protected function addAlbumCollection($arImg, $img)
    {
        $res = CMedialibItem::Edit(array(
                'file' => $arImg,
                'arFields' => array(
                    'ID' => 0,
                    'NAME' => $arImg["name"],
                    'DESCRIPTION' => "",
                    'KEYWORDS' => ""
                ),
                'arCollections' => array($this->albumID)
        ));
        if($res)
        {
            pq($img)->attr('src', $res['PATH']);    
        }
    }

    public function parseCaralogFilterSrc($src){
        $src = preg_replace('/#.+/', '', $src);
        $src = preg_replace('/\?.+/', '', $src);
        $countPoint = substr_count($src, ".");
        if($countPoint>=2 && preg_match("/^\/{2}/", $src) && !preg_match("/^\/{2}www\./", $src) && !preg_match("/http:\//", $src) && !preg_match("/https:\//", $src))
            $src = preg_replace("/^\/{2}/", "http://", $src);
        //$src = str_replace('http:/', 'http://', $src); 
        $src = str_replace('//', '/', $src);
        $src = str_replace('http:/', 'http://', $src);
        $src = str_replace('https:/', 'https://', $src);
        if(preg_match("/www\./", $src) || preg_match("/http:\//", $src) || preg_match("/https:\//", $src))
        {
            if(preg_match("/https:\//", $src))
                $src = preg_replace("/^\/{2}/", "https://", $src);
            elseif(preg_match("/http:\//", $src) || preg_match("/www\./", $src))
                $src = preg_replace("/^\/{2}/", "http://", $src);
        }
        
        if(preg_match("/www\./", $src) || preg_match("/http:\//", $src) || preg_match("/https:\//", $src))
        {
            if(preg_match("/https:\//", $src))
                $src = preg_replace("/^\/{1}/", "https://", $src);
            elseif(preg_match("/http:\//", $src) || preg_match("/www\./", $src))
                $src = preg_replace("/^\/{1}/", "http://", $src);    
        }
        
        //$src = str_replace('//', '/', $src);
        return $src;
    }

    protected function GetArraySrcAttr($path)
    {
        $ar["path"] = $image = preg_replace('#\[[^\[]+$#','',$path);
       
        preg_match('#\[[^\[]+$#', $path, $matches);
        $ar["attr"] = $attr = str_replace(array("[", "]"), "", $matches[0]);
        return $ar;
    }

    protected function parseCatalogPages()
    {
        global $zis; 
        foreach($this->pagenavigation as $id=>$page)
        {
            $this->clearHtml();
            
            try{
                if(isset($this->pagenavigationPrev[$page]) || isset($this->pagenavigationPrev[$id]) || empty($page)) continue;
            }catch(Exception $e)
            {
                continue;    
            } 

            
            $zis++;
           
            if($this->currentPage>=self::DEFAULT_DEBUG_LIST && $this->settings["catalog"]["mode"]=="debug") return;
            $this->connectCatalogPage($page);
            $this->parseCatalogNavigation($page);
            if($this->IsNumberPageNavigation() && $this->CheckPageNavigation($id))
            {
                $this->parseCatalogProducts();
            }elseif(!$this->IsNumberPageNavigation())
            {
                $this->parseCatalogProducts();
            }

            $i++;

        }
        foreach($this->pagenavigationPrev as $i=>$v)
        {
            foreach($this->pagenavigation as $i1=>$v1)
            {
                if($v1==$v) unset($this->pagenavigation[$i1]);
            }
        }
        
        if(count($this->pagenavigation)>0)
        {
            $this->parseCatalogPages();
        }

    }
    
    protected function SaveCopyPage()
    {
        if(!$this->agent && $this->settings["catalog"]["mode"]!="debug")
        {
            file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_copy_page".$this->id.".txt", $this->page);
            file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_copy_url".$this->id.".txt", $this->fileHtml->headerUrl);   
        }     
    }
    
    protected function DeleteCopyPage()
    {
        if(!$this->agent && $this->settings["catalog"]["mode"]!="debug" && file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_copy_page".$this->id.".txt"))
        {
            unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_copy_page".$this->id.".txt");
            unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_copy_url".$this->id.".txt"); 
        }
    }
    
    protected function GetCopyPage()
    {
        if(!$this->agent && $this->settings["catalog"]["mode"]!="debug" && file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_copy_page".$this->id.".txt"))
        {
            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_copy_page".$this->id.".txt");
            $this->httpCode = 200;
            return $file;  
        }
        $this->httpCode = 0;
        return false;
    }
    
    protected function GetCopyUrl()
    {
        if(!$this->agent && $this->settings["catalog"]["mode"]!="debug" && file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_copy_url".$this->id.".txt"))
        {
            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_copy_url".$this->id.".txt");
            return $file;  
        }
        $this->httpCode = 0;
        return false;     
    }
    
    protected function connectCatalogPage($page)
    {
        $this->catalogSleep();
        $this->sectionPage = $page;
        $this->fileHtml = new FileGetHtml();
        $this->page = $this->GetCopyPage();
        if(!$this->page)
        {   
            if ($this->ValidateUrl($page) === true)
            {   
                $this->page = $this->fileHtml->file_get_html($page, $this->settings["catalog"]["proxy"], $this->auth, $this);
            }
            elseif ($this->ValidateUrl($page) === false) 
            {
                $this->page = $this->fileHtml->file_get_local_html($page);
            }
               
           
        }else{
            $this->fileHtml->httpCode = 200;
            $this->fileHtml->headerUrl = $this->GetCopyUrl();    
        }
        $this->DeleteCharsetHtml5($this->page);
        $this->SaveCopyPage();
        $this->httpCode = $this->fileHtml->httpCode;
        if($this->httpCode!=200 && $this->httpCode!=301 && $this->httpCode!=302 && $this->httpCode!=303)
        {   
            {   
                
                $this->errors[] = "[".$page."]".GetMessage("parser_error_connect")."[".$this->httpCode."]";
                //if($this->agent || $this->settings["catalog"]["mode"]=="debug")
                {   
                    $this->SaveLog();
                    unset($this->errors);
                }
                
                if($this->settings["catalog"]["404"]!="Y")
                {
                    if(!$this->agent && $this->settings["catalog"]["mode"]!="debug")
                    {
                        $this->stepStart = 1;
                        $this->SavePrevPage($page);
                        if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt"))
                            unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt");
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
        if(!$this->agent && $this->settings["catalog"]["mode"]!="debug")$this->SavePrevPage($page);
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
        if($sleep)
        {
            sleep($sleep);
        }
    }
    
    protected function SavePrevPage($page)
    {
        if(!$this->agent && $this->settings["catalog"]["mode"]!="debug" && $this->stepStart)
        {
            file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_prev_page".$this->id.".txt", $page."|", FILE_APPEND);  
        }  
    }
    
    protected function SavePrevPageDetail($page)
    {
        if(!$this->agent && $this->settings["catalog"]["mode"]!="debug")
        {
            file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_prev_element".$this->id.".txt", $page."|", FILE_APPEND);  
        }  
    }
    
    protected function SaveCurrentPage($arPage)
    {
        if(!$this->agent && $this->settings["catalog"]["mode"]!="debug" && $this->stepStart)
        {    
            $page = implode("|", $arPage);
            if(!empty($arPage))file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_current_page".$this->id.".txt", $page."|"); 
            elseif($this->IsEndSectionUrl()) file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_current_page".$this->id.".txt", "");
            else file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_current_page".$this->id.".txt", "0");   
        }
            
    }
    
    protected function ClearAjaxFiles()
    {
        if(!$this->agent && $_GET["begin"])
        {
            if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_prev_page".$this->id.".txt"))unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_prev_page".$this->id.".txt");
            if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_prev_element".$this->id.".txt"))unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_prev_element".$this->id.".txt");
            if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_current_page".$this->id.".txt"))unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_current_page".$this->id.".txt"); 
            if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog".$this->id.".txt"))unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog".$this->id.".txt");
            if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_log_".$this->id.".txt"))unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_log_".$this->id.".txt");
            if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser".$this->id.".txt"))unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser".$this->id.".txt");  
            if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt"))unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt");
            if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_copy_page".$this->id.".txt"))unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_copy_page".$this->id.".txt"); 
            if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/loc".$this->id.".txt"))unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/loc".$this->id.".txt"); 
            if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_section".$this->id.".txt")) unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_section".$this->id.".txt");

        }elseif($this->agent)
        {
            if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/loc".$this->id.".txt"))unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/loc".$this->id.".txt");
            if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_section".$this->id.".txt")) unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_section".$this->id.".txt");
        }
           
    }
    
    protected function checkActionBegin()
    {
        //if($this->updateActive && isset($this->settings["catalog"]["uniq"]["action"]) && $this->settings["catalog"]["uniq"]["action"]!="N")
        {
            if((!$this->agent && $_GET["begin"]) || $this->agent)
            {
                
                $filterValues = array("PARSER_ID"=>$this->id);
                $arr = array(
                    "select" => array('ID'),
                    "filter" => $filterValues
                );
                
                if($this->tmp=="b_shs_parser_tmp" || !$this->tmp)
                {
                    $rsData = ShsParserTmpTable::GetList($arr);
                    
                    while($arData = $rsData->Fetch())
                    {
                        ShsParserTmpTable::Delete($arData["ID"]);
                    }        
                }elseif($this->tmp=="b_shs_parser_tmp_old")
                {
                    $rsData = ShsParserTmpOldTable::GetList($arr);
                    
                    while($arData = $rsData->Fetch())
                    {
                        ShsParserTmpOldTable::Delete($arData["ID"]);    
                    }
                }
                
            }
        }
            
    }
    
    protected function checkActionAgent($agent = true)
    {
        if((($this->agent && $agent) || !$agent) && $this->updateActive && isset($this->settings["catalog"]["uniq"]["action"]) && $this->settings["catalog"]["uniq"]["action"]!="N")
        {   
            $filterValues = array("PARSER_ID"=>$this->id);
            $arr = array(
                "select" => array('ID', "PRODUCT_ID"),
                "filter" => $filterValues
            );
            if($this->tmp=="b_shs_parser_tmp" || !$this->tmp)
            {
                $rsData = ShsParserTmpTable::GetList($arr);
                
                while($arData = $rsData->Fetch())
                {
                    $arProd[$arData["PRODUCT_ID"]] = $arData["PRODUCT_ID"];    
                }
                
                $rsDataOld = ShsParserTmpOldTable::GetList($arr);

                while($arDataOld = $rsDataOld->Fetch())
                {
                    $arProdOld[$arDataOld["PRODUCT_ID"]] = $arDataOld["PRODUCT_ID"];    
                }
                
                if(isset($arProdOld) && !empty($arProdOld))
                {
                    foreach($arProdOld as $p)
                    {
                        if(!isset($arProd[$p]))
                            $this->doProductAction($p);
                    }
                }
                $parser = new ShsParserContent();
                $arFields['TMP'] = "b_shs_parser_tmp_old";
                $parser->Update($this->id, $arFields); 
                unset($parser);
            }elseif($this->tmp=="b_shs_parser_tmp_old")
            {
                $rsData = ShsParserTmpOldTable::GetList($arr);
                
                while($arData = $rsData->Fetch())
                {
                    $arProd[$arData["PRODUCT_ID"]] = $arData["PRODUCT_ID"];
                }
                
                $rsDataOld = ShsParserTmpTable::GetList($arr);
                
                while($arDataOld = $rsDataOld->Fetch())
                {
                    $arProdOld[$arDataOld["PRODUCT_ID"]] = $arDataOld["PRODUCT_ID"];    
                }
                
                if(isset($arProdOld) && !empty($arProdOld))
                {
                    foreach($arProdOld as $p)
                    {
                        if(!isset($arProd[$p]))
                            $this->doProductAction($p);
                    }
                }
                $parser = new ShsParserContent();
                $arFields['TMP'] = "b_shs_parser_tmp";
                $parser->Update($this->id, $arFields);
                unset($parser);
            }    
        }
    }
    
    protected function doProductAction($ID)
    {
        if($this->settings["catalog"]["uniq"]["action"]=="D")
        {
            CIBlockElement::Delete($ID);
        }elseif($this->settings["catalog"]["uniq"]["action"]=="A")
        {
            $el = new CIBlockElement;
            $res = $el->Update($ID, array("ACTIVE"=>"N"));
            unset($el);
        }
        elseif($this->settings["catalog"]["uniq"]["action"]=="NULL")
        {
            CCatalogProduct::Update($ID, array("QUANTITY" => 0));
        }
    }
    
    protected function addTmp($ID)
    {   
        if(/*!$this->debug && */$ID && $this->updateActive && isset($this->settings["catalog"]["uniq"]["action"]) && $this->settings["catalog"]["uniq"]["action"]!="N")
        {
            $arFields["PARSER_ID"] = $this->id;
            $arFields["PRODUCT_ID"] = $ID;
            if($this->tmp=="b_shs_parser_tmp" || !$this->tmp)
            {   
                ShsParserTmpTable::add($arFields);    
            }elseif($this->tmp=="b_shs_parser_tmp_old")
            {   
                ShsParserTmpOldTable::add($arFields);    
            }
        }    
    }

    protected function getCatalogUrlSite(){
        if(preg_match("/http:/", $this->rss))
        {
            $url = str_replace("http://", "", $this->rss);
            $url = preg_replace("/\/.*/", "", $url);
            $url = "http://".$url;
        }elseif(preg_match("/https:/", $this->rss))
        {
            $url = str_replace("https://", "", $this->rss);
            $url = preg_replace("/\/.*/", "", $url);
            $url = "https://".$url;    
        }
        else{
            $url = preg_replace("/\/.*/", "", $this->rss);
        }
        return $url;
    }

    protected function searchCatalogNavigation()
    {

    }

    protected function parseCatalogNavigation($pageHref)
    {   
        $this->html = phpQuery::newDocument($this->page, "text/html;charset=".LANG_CHARSET);
        $this->base = $this->GetMetaBase($this->html);

        if($this->settings["catalog"]["pagenavigation_selector"])
        {
            $this->deleteCatalogElement($this->settings["catalog"]["pagenavigation_delete"], $this->settings["catalog"]["pagenavigation_selector"]);

            if(!$this->settings["catalog"]["pagenavigation_one"])
                $this->settings["catalog"]["pagenavigation_one"] = "a[href]";

            $arPath = $this->GetArraySrcAttr($this->settings["catalog"]["pagenavigation_one"]);
            $attr = $arPath["attr"]?$arPath["attr"]:"href";
            $element = $this->settings["catalog"]["pagenavigation_selector"]." ".$arPath["path"];

            unset($this->pagenavigation[$pageHref]);
            unset($this->pagenavigation[$this->currentPage]);
            $this->pagenavigationPrev[$pageHref] = $pageHref;


            foreach($this->html[$element] as $page)
            {

                $p = pq($page)->attr($attr);
                $p = $this->getCatalogLink($p);
                $p1 = $p."\\r\\n";
                $n = pq($page)->text();
                
                $n = $this->ValidatePageNavigation($n);

                if(!$p || empty($p) || (isset($this->settings["catalog"]["pagenavigation_var"]) && $this->settings["catalog"]["pagenavigation_var"]))
                {
                    if(isset($this->settings["catalog"]["pagenavigation_var"]) && $this->settings["catalog"]["pagenavigation_var"])
                    {   
                        $nV = (int)$this->settings["catalog"]["pagenavigation_page_count"];
                        $pV = $this->settings["catalog"]["pagenavigation_var"];
                        $other = $this->settings["catalog"]["pagenavigation_other_var"];
                        
                        $req = $pV."=".$n;
                        if($other)
                            $req .= "&".$other;
                            
                        $p = $this->getUrlPageNavigation($req, $pV);
                    }
                    else continue 1;    
                }
                  
                //$this->convetCyrillic($p);
                
                if(isset($this->pagenavigationPrev[$p])) continue;
                
                if($this->IsNumberPageNavigation())
                {   
                    if(!$this->CheckValidatePageNavigation($n) && !$this->CheckPageNavigation($n)) continue;

                    if(($this->currentPage+5)<$n)continue;
                    if($this->CheckPageNavigation($n))
                    {   
                        if(isset($this->pagenavigationPrev[$p])) continue;
                        if(isset($this->pagenavigationPrev[$n])) continue;
                        $this->pagenavigation[$n] = $p;
                    }elseif($this->CheckPageNavigationLess($n)){
                        if(isset($this->pagenavigationPrev[$p])) continue;
                        if(isset($this->pagenavigationPrev[$n])) continue;
                        $this->pagenavigation[$n] = $p;
                    } else{

                    }
                }else{

                    $this->pagenavigation[$p] = $p;
                }
            }
            
            return true;
        }elseif(isset($this->settings["catalog"]["pagenavigation_var"]) && $this->settings["catalog"]["pagenavigation_var"] && isset($this->settings["catalog"]["pagenavigation_page_count"]) && $this->settings["catalog"]["pagenavigation_page_count"])
        {
            $n = (int)$this->settings["catalog"]["pagenavigation_page_count"];
            $p = $this->settings["catalog"]["pagenavigation_var"];
            $other = $this->settings["catalog"]["pagenavigation_other_var"];
            $step = trim(intval($this->settings["catalog"]["pagenavigation_var_step"]));
            
            if($step == 1)
            {
                $step = 2;
                $step1 = 1;
            }
            else{
                if($step != 0)
                {
                    $step1 = $step;
                }
            }
            
            /*for($i=$step; $i<=$n*$step;$i+$step1)
            {
                file_put_contents(dirname(__FILE__)."/44444.log", $i."\n", FILE_APPEND);
                $req = $p."=".$i;
                if($other)
                    $req .= "&".$other;
                $page = $this->getUrlPageNavigation($req, $p, $other);
                $this->pagenavigation[$page] = $page;
            }*/
            $i = $step;
            while($i<=$n*$step)
            {
                $req = $p."=".$i;
                if($other)
                    $req .= "&".$other;
                $page = $this->getUrlPageNavigation($req, $p, $other);
                $this->pagenavigation[$page] = $page;
                $i = $i+$step1;
            }
            return true;    
        }
         
        return false;



    }
    
    protected function locText($text="", $format="plain", $test = false)
    {
        global $APPLICATION;
        if(isset($this->settings["loc"]["type"]) && $this->settings["loc"]["type"] && $text)
        {
            if($this->settings["loc"]["type"]=="yandex")
            {   
                $key = $this->settings["loc"]["yandex"]["key"];
                $lang = $this->settings["loc"]["yandex"]["lang"];
                $text0 = $text;
                $charset = strtolower(SITE_CHARSET);
                if($charset!="utf-8")
                {
                     $text = $APPLICATION->ConvertCharset($text, $charset, "utf-8");    
                }
                $text = urlencode($text);
                $url = "https://translate.yandex.net/api/v1.5/tr/translate?key=".$key."&text=".$text."&lang=".$lang."&format=".$format;
                $ch = curl_init();
                //curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_URL, "https://translate.yandex.net/api/v1.5/tr/translate");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "key=".$key."&text=".$text."&lang=".$lang."&format=".$format);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                
                $arrorLoc[401] = GetMessage("shs_parser_loc_401");
                $arrorLoc[402] = GetMessage("shs_parser_loc_402");
                $arrorLoc[403] = GetMessage("shs_parser_loc_403");
                $arrorLoc[404] = GetMessage("shs_parser_loc_404");
                $arrorLoc[413] = GetMessage("shs_parser_loc_413");
                $arrorLoc[422] = GetMessage("shs_parser_loc_422");
                $arrorLoc[501] = GetMessage("shs_parser_loc_501");
                $data = curl_exec($ch);
                $xml = new CDataXML();
                $xml->LoadString($data);
                $arData = $xml->GetArray();
                
                if(isset($arData["Translation"]["#"]["text"]["0"]["#"]))
                {
                    $data = $arData["Translation"]["#"]["text"]["0"]["#"];
                    $charset = strtolower(SITE_CHARSET);
                    if($charset!="utf-8")
                    {
                         $APPLICATION->ConvertCharset($data, "utf-8", $charset);    
                    }
                    
                }elseif(isset($arData["Error"]["@"]["code"]))
                {
                    $httpCode = $arData["Error"]["@"]["code"];
                    $this->errors[] = $arrorLoc[$httpCode];
                    $data = $text0;
                }else
                    $data = $text0;
                unset($xml);
                unset($arData);
                unset($arrorLoc);
                curl_close($ch);
                return $data;
            }    
        }        
    }

    protected function deleteCatalogElement($element, $parentElement=false, $dom=false)
    {
        if($parentElement)
        {
            $arElement = explode(",", $element);
            $parentElement = trim($parentElement);
            $element = "";
            foreach($arElement as $i=>$el)
            {
                $el = trim($el);
                if(empty($el)){
                    unset($arElement[$i]);
                    continue 1;
                }
                $element.=$parentElement." ".$el;
                if(($i+1)!=count($arElement))$element.=",";
            }
        } 
        pq($element)->remove();
    }

    protected function deleteCatalogAttribute($element, $parentElement=false, $dom=false)
    {
        if($parentElement)
        {
            $arElement = explode(",", $element);
            $parentElement = trim($parentElement);
            $element = "";
            foreach($arElement as $i=>$el)
            {
                $el = trim($el);
                if(empty($el)){
                    unset($arElement[$i]);
                    continue;
                }

                preg_match('#\[[^\[]+$#', $el, $matches);
                $el = preg_replace('#\[[^\[]+$#','',$el);
                $attr = str_replace(array("[", "]"), "", $matches[0]);

                $element = $parentElement." ".$el;
                pq($element)->removeAttr($attr);
            }
        }

    }
    
    protected function DeleteParam($url="", $p="", $other="")
    {
        if(empty($url) || empty($p)) return false;
        
        $url = str_replace($other, "", $url);
        $reg = "/\?".$p."\=(\d)/";
        $url = preg_replace($reg, "", $url);
        $reg = "/".$p."\=(\d)/";
        $url = preg_replace($reg, "", $url);
        
        $url = str_replace("?&", "?", $url);
        $url = str_replace("&&", "&", $url);
        $url = str_replace("/&", "/?", $url);
        $url = preg_replace("/\?{1}$/", "", $url);
        
        return $url;
            
    }
    
    protected function getUrlPageNavigation($url="", $p="", $other="")
    {
        $url = trim($url);
        $p = trim($p);
        
        if(empty($url) || empty($p)) return false;
        
        $this->urlCatalog = $this->DeleteParam($this->urlCatalog, $p, $other);


        if(preg_match("/\/{1}$/", $this->urlCatalog))
        {
            $url = $this->urlCatalog."?".$url;
        }elseif(!preg_match("/\/{1}$/", $this->urlCatalog) && !preg_match("/\?/", $this->urlCatalog))
        {
            $url = $this->urlCatalog."?".$url;
        }
        elseif(preg_match("/\?/", $this->urlCatalog))
        {
            $url = $this->urlCatalog."&".$url;
        }
        return $url;
    }

    protected function getCatalogLink($url)
    {
        $url = trim($url);

        
        if(empty($url)) return false;
        elseif(preg_match("/^\/{2}www/", $url))
        {
            $url = preg_replace("/^\/{2}www/", "www", $url);
        }
        elseif(preg_match('/^http:/', $url) || preg_match('/www\./', $url) || preg_match('/^https:/', $url))
        {
            $url = $url;
        }elseif(preg_match("/^\//", $url))
        {   
            $url = $this->urlSite.$url;
        }elseif(!preg_match("/^\//", $url) && preg_match("/\/{1}$/", $this->urlCatalog))
        {
            if($this->base) $url = $this->base.$url;
            else $url = $this->urlCatalog.$url;
        }
        elseif(!preg_match("/^\?/", $url) && !preg_match("/^\//", $url) && !preg_match("/\/{1}$/", $this->urlCatalog))
        {   
            //$site
            if($this->base)
            {
                if(!preg_match("/\/{1}$/", $this->base))
                    $this->base = $this->base."/";
                $url = $this->base.$url;    
            }else{
                $uri = preg_replace('#/[^/]+$#','',$this->urlCatalog);
                $url = $uri."/".$url;    
            }

        }elseif(preg_match("/\?/", $url) && preg_match("/\?/", $this->urlCatalog))
        {   
            if(preg_match("/^\?/", $url))
            {
                $uri = preg_replace("/\?.+/", "", $this->urlCatalog);
                $url = $uri.$url;
            }else{
                $uri = preg_replace('#/[^/]+$#','',$this->urlCatalog);
                $url = $uri."/".$url;    
            }
        }
        
        $this->convetCyrillic($url);
        //$url = $this->deletePointUrl($url);
        return $url;
    }

    public function deletePointUrl($url)
    {
        $count = substr_count($url, "../");
        if($count>0)
        {
            for($i=0;$i<$count;$i++)
            {
                $url = preg_replace("/[-\w]+\/\.{2}\//", "", $url);
            }
        }
        return $url;
    }

    public function DeleteCharsetHtml5(&$data)
    {
        $data = preg_replace("/\s*<meta\s+charset=[\"|']{0,1}.+?[\"|']{0,1}\s*\/{0,1}\>/i", "", $data);
    }
    
    public function parserSelector(&$html, $selector, $nextSelector=0){
        global $shs_DOC_ENCODING;
        phpQuery::selectDocument($html);
        if($nextSelector==0 && $this->meta_description!="N")foreach($html['meta'] as $meta){
            if(strtolower(pq($meta)->attr("name"))=="description"){
                $this->meta_description_text = pq($meta)->attr("content");
                if(!$this->meta_description_text)$this->meta_description_text = pq($meta)->attr("value");
                if(strtoupper(LANG_CHARSET)=="WINDOWS-1251"/* && strtoupper(LANG_CHARSET)!=strtoupper($shs_DOC_ENCODING)*/) $this->meta_description_text = mb_convert_encoding($this->meta_description_text, LANG_CHARSET, "utf-8");
                if($this->meta_description_text)
                {
                    $this->meta_description_text = strip_tags($this->meta_description_text);
                    break;
                }

            }
        }
        if($nextSelector==0 && $this->meta_keywords!="N")foreach($html['meta'] as $meta){
            if(strtolower(pq($meta)->attr("name"))=="keywords"){
                $this->meta_keywords_text = pq($meta)->attr("content");
                if(!$this->meta_keywords_text)$this->meta_keywords_text = pq($meta)->attr("value");
                if(strtoupper(LANG_CHARSET)=="WINDOWS-1251"/* && strtoupper(LANG_CHARSET)!=strtoupper($shs_DOC_ENCODING)*/) $this->meta_keywords_text = mb_convert_encoding($this->meta_keywords_text, LANG_CHARSET, "utf-8");
                if($this->meta_keywords_text)
                {
                    $this->meta_keywords_text = strip_tags($this->meta_keywords_text);
                    break;
                }

            }
        }
        if($nextSelector==0 && $this->meta_title!="N")
        {
            $this->meta_title_text = pq($html['title'])->text();
            $this->meta_title_text = strip_tags($this->meta_title_text);
            if(strtoupper(LANG_CHARSET)=="WINDOWS-1251"/* && strtoupper(LANG_CHARSET)!=strtoupper($shs_DOC_ENCODING)*/) {
                //$this->meta_title_text = mb_convert_encoding($this->meta_title_text, LANG_CHARSET, $shs_DOC_ENCODING);
                $this->meta_title_text = mb_convert_encoding($this->meta_title_text, LANG_CHARSET, "utf-8");
            }
        }


        if(empty($selector)) return $html->htmlOuter();
        else
        {
          $out = '<meta http-equiv="Content-Type" content="text/html;charset='.LANG_CHARSET.'">'.pq($selector)->html();
          return $out;
        }


    }

    public function changeImgSrc($html){
        phpQuery::selectDocument($html);
        $site = $this->getUrlSite();
        foreach($html["img"] as $img){
          $src = $this->filterSrc(pq($img)->attr("src"));
          if(!preg_match('/^http:/', $img->getAttribute('src')) && !preg_match('/^https:/', $img->getAttribute('src')) && !preg_match('/^www/', $img->getAttribute('src')) && !preg_match('/^\/{2}/', $img->getAttribute('src'))){
            if(preg_match("/^\/{1}/", $src))$src = $site.$src;
            else $src = $site."/".$src;
            $img->setAttribute('src', $src);
          }else{
            $img->setAttribute('src', $src);
          }
        }
        return $html;
    }

    public function parserFirstImg($html){
        phpQuery::selectDocument($html);
        $site = $this->getUrlSite();
        foreach($html["img"] as $img){
          $first_img = $this->filterSrc(pq($img)->attr("src"));
          if(!preg_match('/^http:/', $first_img) && !preg_match('/^www/', $first_img) && !preg_match('/^https:/', $first_img)){

            if(preg_match("/^\/{1}/", $first_img))$first_img = $site.$first_img;
            else $first_img = $site."/".$first_img;
            $arWidth = getimagesize($first_img);
            if($arWidth[0]<40) continue;
            return $first_img;
          }else{
            $arWidth = getimagesize($first_img);
            if($arWidth[0]<40) continue;
            return $first_img;
          }
        }
        return $first_img;
    }

    public function saveImgServer($html){
        foreach($html["img"] as $img){
            $arImg = CFile::MakeFileArray(pq($img)->attr("src"));
            $this->arrFilesTemp[] = $arImg["tmp_name"];
            
            if(isset($this->albumID) && $this->albumID)
                $this->addAlbumCollection($arImg, $img);
            else{
                $fid = CFile::SaveFile($arImg, "shs.parser");
                $img->setAttribute('src', CFile::GetPath($fid));    
            }
            /*$fid = CFile::SaveFile($arImg, "shs.parser");
            $img->setAttribute('src', CFile::GetPath($fid));*/
        }
        return $html->htmlOuter();
    }

    public function deleteElementStart(&$html, $selector_delete_element){
        phpQuery::selectDocument($html);
        $arElements = explode(',', $selector_delete_element);
        foreach($arElements as $selector){
          if(empty($selector)) continue;
          $selector = trim($selector);
          $html[$selector]->remove();
        }
        return $html;
    }

    public function deleteElements(&$html, $selector, $nextSelector=0){
        $arSelector = $this->arraySelector($selector);
        $n = 0;
        if(!isset($arSelector[$nextSelector])){
          $html->outertext = "";
          return;
        }
        if(strpos($arSelector[$nextSelector], '[')!==false && preg_match("/\[[0-9]{1,3}\]/", $arSelector[$nextSelector])){
            $sel = $arSelector[$nextSelector];
            $arSelector[$nextSelector] = preg_replace('/\[[0-9]{1,3}\]/', '', $sel);
            preg_match_all('/\[[0-9]{1,3}\]/', $sel, $matches);
            $n = str_replace(array('[', ']'), "", $matches[0][0]);
            $item = $html->find($arSelector[$nextSelector], $n);
            if(gettype($item)=="NULL"){
                return false;
            }
            $data = $this->deleteElements($item, $selector, $nextSelector+1);

        }else{
            foreach($html->find($arSelector[$nextSelector]) as $item){
                $data = $this->deleteElements($item, $selector, $nextSelector+1);
            }
        }

    }

    public function deleteAttributeStart(&$html, $selector_delete_attribute){

        $arElements = explode(',', $selector_delete_attribute);

        foreach($arElements as $selector){
          if(empty($selector)) continue;
          preg_match('/\[[a-zA-Z]+\]$/', $selector, $attribute);
          $attributes = str_replace(array(']', '['), "", $attribute[0]);
          $selector = preg_replace('/\[[a-zA-Z]+\]$/', "", trim($selector));
          $this->deleteAttributes($html, trim($selector), $attributes);
        }
        return $html;
    }

    public function deleteAttributes(&$html, $selector, $attribute, $nextSelector=0){
        phpQuery::selectDocument($html);
        pq($selector)->removeAttr($attribute);
    }
    
    
    
    protected function getPageRssLink($url, $path, $site)
    {
        $url = trim($url);
        $urlCatalog = $site.$path;
        /*if(empty($url)) return false;
        elseif(preg_match("/^\/{2}www/", $url))
        {
            $url = preg_replace("/^\/{2}www/", "www", $url);
        }
        elseif(preg_match('/^http:/', $url) || preg_match('/www\./', $url) || preg_match('/^https:/', $url))
        {
            $url = $url;
        }elseif(preg_match("/^\//", $url))
        {
            $url = $site.$url;
        }elseif(!preg_match("/^\//", $url) && preg_match("/\/{1}$/", $urlCatalog))
        {    
            
            if($this->base)$url = $this->base.$url;
            else $url = $urlCatalog.$url;
        }
        elseif(!preg_match("/\?/", $url) && !preg_match("/^\//", $url) && !preg_match("/\/{1}$/", $urlCatalog))
        {   
            //$site
            if($this->base) $url = $this->base.$url;
            else{
                $uri = preg_replace('#/[^/]+$#','',$urlCatalog);
                $url = $uri."/".$url;    
            }
        }elseif(preg_match("/\?/", $url) && preg_match("/\?/", $urlCatalog))
        {   
            if(preg_match("/^\?/", $url))
            {
                $uri = preg_replace("/\?.+/", "", $urlCatalog);
                $url = $uri.$url;    
            }else{
                $uri = preg_replace('#/[^/]+$#','',$urlCatalog);
                $url = $uri."/".$url;
            }
        }*/
        
        if(empty($url)) return false;
        elseif(preg_match("/^\/{2}www/", $url))
        {
            $url = preg_replace("/^\/{2}www/", "www", $url);
        }
        elseif(preg_match('/^http:/', $url) || preg_match('/www\./', $url) || preg_match('/^https:/', $url))
        {
            $url = $url;
        }elseif(preg_match("/^\//", $url))
        {   
            $url = $site.$url;
        }elseif(!preg_match("/^\//", $url) && preg_match("/\/{1}$/", $urlCatalog))
        {
            if($this->base) $url = $this->base.$url;
            else $url = $urlCatalog.$url;
        }
        elseif(!preg_match("/^\?/", $url) && !preg_match("/^\//", $url) && !preg_match("/\/{1}$/", $urlCatalog))
        {   
            //$site
            if($this->base)
            {
                if(!preg_match("/\/{1}$/", $this->base))
                    $this->base = $this->base."/";
                $url = $this->base.$url;    
            }else{
                $uri = preg_replace('#/[^/]+$#','',$urlCatalog);
                $url = $uri."/".$url;    
            }

        }elseif(preg_match("/\?/", $url) && preg_match("/\?/", $urlCatalog))
        {   
            if(preg_match("/^\?/", $url))
            {
                $uri = preg_replace("/\?.+/", "", $urlCatalog);
                $url = $uri.$url;
            }else{
                $uri = preg_replace('#/[^/]+$#','',$urlCatalog);
                $url = $uri."/".$url;    
            }
        }
        
        
        //$this->convetCyrillic($url);
        return $url;
    }

    public function getContentsArray($site='', $port=80, $path='', $query=''){
        if(!$this->typeN || $this->typeN=="rss")
        {   
            $arContent = CIBlockRSS::GetNewsEx($site, $port, $path, $query);
            
            return CIBlockRSS::FormatArray($arContent);
        }elseif($this->typeN=="page")
        {
            $url = $site.$path;
            if($query)$url = $url."?".$query;
            $fileHtml = new FileGetHtml();
            $data = $fileHtml->file_get_html($url, $this->proxy, $this->auth, $this);
            $this->header_url = $url = $fileHtml->headerUrl;
            $this->DeleteCharsetHtml5($data);
            $html = phpQuery::newDocument($data, "text/html;charset=".LANG_CHARSET);
            $dom = htmlspecialcharsBack(trim($this->settings["page"]["selector"]));
            $href = htmlspecialcharsBack(trim($this->settings["page"]["href"]));
            $name = htmlspecialcharsBack(trim($this->settings["page"]["name"]));
            $href = $href?$href:"a:eq(0)";
            $name = $name?$name:$href;
            $this->base = $this->GetMetaBase($html);
            $i = 0;
            $site = $this->getUrlSite();
            foreach($html[$dom] as $val)
            {
                $strName =  strip_tags(pq($val)->find($name)->html());

                if($name=="a:parent")$strHref =  $strName =  strip_tags(pq($val)->html());
                else $strName =  strip_tags(pq($val)->find($name)->html());

                //$strHref =  mb_strtolower(pq($val)->find($href)->attr("href"));
                if($href=="a:parent")$strHref =  pq($val)->attr("href");
                else $strHref =  pq($val)->find($href)->attr("href");

                /*if(!preg_match('/^http:/', $strHref) && !preg_match('/^www/', $strHref) && !preg_match('/^\/{2}/', $strHref))
                {
                    if(preg_match('/^\//', $strHref))$strHref = $site.$strHref;
                    else $strHref = $site.$path.$strHref;
                }*/
                $strHref = $this->getPageRssLink($strHref, $path, $site);
                if(empty($strName)) $this->errors[] = GetMessage("parser_error_noname");
                if(empty($strHref)) $this->errors[] = GetMessage("parser_error_nohref");
                if(empty($strName) || empty($strHref)) continue;

                $arContent["item"][$i]["title"] = $strName;
                $arContent["item"][$i]["link"] = $strHref;
                $arContent["item"][$i]["description"] = pq($val)->html();
                $i++;
            }     
            if($i>0)
            {
                $arContent['title'] = $site;
                $arContent['link'] = $site;
                return  $arContent;
            }
        }
    }
    
    public function getMetaBase($html)
    {
        if(isset($this->base)) unset($this->base);
        if($this->typeN == "catalog")
        {
            $base = pq($html)->find("base:eq(0)")->attr("href");    
        }
        elseif ($this->typeN == "xml")
        {
            $base = mb_detect_encoding($html, "auto");
        }
        return $base;
    }

    public function getUrlSite(){
        $this->header_url = strtolower($this->header_url);
        $site = str_replace(array('http://', "https://", 'www.', "HTTP://", "WWW."), "", $this->header_url);
        $site = preg_replace('/\/(.)+/', '', $site);
        $arLevel = explode(".", $site);
        if(preg_match("/https:\//", $this->header_url))
        {
            if(count($arLevel)==2) return 'https://www.'.$site;
            else return 'https://'.$site;    
        }else{
            if(count($arLevel)==2) return 'http://www.'.$site;
            else return 'http://'.$site;    
        }
        
    }

    public function filterSrc($src){
        $src = preg_replace('/#.+/', '', $src);
        $src = preg_replace('/\?.+/', '', $src);
        //$src = str_replace('http:/', 'http://', $src);
        $src = str_replace('//', '/', $src);
        $src = str_replace('http:/', 'http://', $src);
        $src = str_replace('https:/', 'https://', $src);
        if(preg_match("/www\./", $src) || preg_match("/http:\//", $src) || preg_match("/https:\//", $src))
        {
            if(preg_match("/https:\//", $src))
                $src = preg_replace("/^\/{2}/", "https://", $src);
            elseif(preg_match("/http:\//", $src) || preg_match("/www\./", $src))
                $src = preg_replace("/^\/{2}/", "http://", $src);
        }
        //$src = preg_replace("/^\/{2}/", "http://", $src);
        if(preg_match("/www\./", $src) || preg_match("/http:\//", $src) || preg_match("/https:\//", $src))
        {
            if(preg_match("/https:\//", $src))
                $src = preg_replace("/^\/{1}/", "https://", $src);
            elseif(preg_match("/http:\//", $src) || preg_match("/www\./", $src))
                $src = preg_replace("/^\/{1}/", "http://", $src);    
        }
        //$src = str_replace('//', '/', $src);
        return $src;
    }

    public function parseImgFromRss($arItem){
        foreach($arItem as $item){
            if(is_array($item)) $preview = $this->parseImgFromRss($item);
            elseif(preg_match("/^(http:)(.)+(jpg|JPG|gif|GIF|png|PNG|JPEG|jpeg)$/", $item, $match) || preg_match("/^(https:)(.)+(jpg|JPG|gif|GIF|png|PNG|JPEG|jpeg)$/", $item, $match)){
              $preview = $match[0];
              break;
            }
        }
        return $preview;
    }

    public function arraySelector($selector, $debug=0){
        $bool = false;
        $selector = trim($selector);
        $arSel = explode(' ', $selector);
        $newArSel = array();
        $selStr = "";
        foreach($arSel as $i=>$val){
          if(preg_match('/\[/', $val) && preg_match('/\]/', $val) && !$bool) $newArSel[] = $val;
          elseif(!preg_match('/\[/', $val) && !preg_match('/\]/', $val) && !$bool) $newArSel[] = $val;
          elseif(preg_match('/\[/', $val) && !preg_match('/\]/', $val)){
            $bool = true;
            $selStr .= $val;
          }elseif(!preg_match('/\[/', $val) && !preg_match('/\]/', $val) && $bool){
            $selStr .= " ".$val;
          }elseif(preg_match('/\]/', $val) && $bool){
            $selStr .= " ".$val;
            $bool = false;
            $newArSel[] = $selStr;
            $selStr = "";
          }
        }
        return $newArSel;
    }
}
?>