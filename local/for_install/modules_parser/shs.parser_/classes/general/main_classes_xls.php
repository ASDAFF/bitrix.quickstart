<?
use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity;
use Bitrix\Seo\Engine;
use Bitrix\Main\Text\Converter;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\IO\Path;

\Bitrix\Main\Loader::includeModule('seo');
\Bitrix\Main\Loader::includeModule('socialservices');

if(!class_exists('PHPExcel')){
    require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/classes/PHPExcel/PHPExcel.php");  
}
if(!class_exists('PHPExcel_IOFactory')){
    require_once ($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/classes/PHPExcel/PHPExcel/IOFactory.php");
}

class ChunkXmlFilter implements PHPExcel_Reader_IReadFilter {
    
    private $_startRow = 0;
    private $_endRow = 0;

    public function setRows($startRow, $chunkSize) {
        $this->_startRow    = $startRow;
        $this->_endRow      = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '') {
        if (($row >= $this->_startRow && $row < $this->_endRow)) {
            return true;
        }
        return false;
    }
}


class SotbitXlsParser extends SotbitCsvParser{
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
    const PREVIEW_ITEM = 20;
    
    private $sectionStyle = array();
    private $efile;
    private $pagenavigationList = array();
    private $draws = array();
    
    public function __construct()
    {
        parent::__construct();    
    }

    protected function parseXlsCatalog()
    {   
        set_time_limit(0);
        parent::ClearAjaxFiles(); 
        parent::DeleteLog(); 
        parent::checkActionBegin(); 
        $this->arUrl = array(); 
        if(isset($this->settings["catalog"]["url_dop"]) && !empty($this->settings["catalog"]["url_dop"]))$this->arUrl = explode("\r\n", $this->settings["catalog"]["url_dop"]);
        
        $this->arUrl = array_merge(array($this->rss), $this->arUrl);
        $this->arUrlSave = $this->arUrl; 
        
        if(!$this->PageFromFileXls()) return false;    
        parent::CalculateStep();                                                       
        if($this->settings["catalog"]["mode"]!="debug" && !$this->agent) $this->arUrlSave = array($this->rss);
        else $this->arUrlSave = $this->arUrl;                                                                    
                                                                                 
        foreach($this->arUrlSave as $rss):
            $rss = trim($rss);    
            if(empty($rss)) continue;
            $this->rss = $rss;
            parent::convetCyrillic($this->rss);
            parent::connectCatalogPage($this->rss);
            
            if(!$this->agent && $this->settings["catalog"]["mode"]!="debug" && isset($this->errors) && count($this->errors)>0)
            {
                parent::SaveLog();
                unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt");
                unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_copy_page".$this->id.".txt");
                return false;    
            }
            $n = $this->currentPage;
            $this->parseCatalogXlsProducts();
            
            if($this->settings["catalog"]["mode"]!="debug" && !$this->agent)
            {
                $this->stepStart = true;
                parent::SavePrevPage($this->rss);
            }
                                            
            parent::SaveCurrentPage($this->pagenavigation);
            if($this->stepStart)
            {
                if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt"))
                    unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser_catalog_step".$this->id.".txt");
                parent::DeleteCopyPage();
            }                                    
            if( (!$this->CheckOnePageNavigation() && $this->agent) || 
                (!$this->CheckOnePageNavigation() && !$this->agent && 
                $this->settings["catalog"]["mode"]=="debug")) 
                    parent::parseCatalogPages();
                    
            if($this->settings['smart_log']['enabled']=='Y'){                                                                                                              
                $this->settings['smart_log']['result_id'] = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/result_id".$this->id.".txt");
                $this->settings['smart_log']['result_id'] = \Bitrix\Shs\ParserResultTable::updateEndTime($this->settings['smart_log']['result_id']);
            }
                    
            if($this->CheckOnePageNavigation() && $this->stepStart)
            {            
                if(parent::IsEndSectionUrl()) parent::ClearBufferStop();
                else parent::ClearBufferStep();
                return false;    
            }
        endforeach; 
        
        parent::checkActionAgent($this->agent);
        
        if($this->agent || $this->settings["catalog"]["mode"]=="debug"){
            foreach(GetModuleEvents("shs.parser", "EndPars", true) as $arEvent)
                ExecuteModuleEventEx($arEvent, array($this->id));
        }
    }
    
    protected function saveCurrentList($listindex)
    {
        if(!$this->agent && $this->settings["catalog"]["mode"]!="debug" && $this->stepStart)
        {
            file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_current_list".$this->id.".txt", $listindex."|", FILE_APPEND);  
        }  
    }
    
    protected function PageFromFileXls()
    {   
        if($this->settings["catalog"]["mode"]=="debug" || $this->agent || $_GET["begin"]) return true;
        
        $prevPage = $prevElement = $currentPage = $currentList = 0;
        if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_prev_page".$this->id.".txt"))
            $prevPage = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_prev_page".$this->id.".txt");
        if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_prev_element".$this->id.".txt"))
            $prevElement = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_prev_element".$this->id.".txt");
        if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_current_page".$this->id.".txt"))
            $currentPage = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_current_page".$this->id.".txt");
        if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_current_list".$this->id.".txt"))
            $currentList = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/catalog_parser_current_list".$this->id.".txt");
        
        if($prevPage)
        {
            $arPrevPage = explode("|", $prevPage);
            $arPrevElement = explode("|", $prevElement);
            $arCurrentPage = explode("|", $currentPage);    
            $arCurrentList = explode("|", $currentList);
        }else{
            $arPrevPage = array();
            $arCurrentPage = array();
            $arCurrentList = array();
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
        
        if(isset($arCurrentList) && is_array($arCurrentList))foreach($arCurrentList as $p)
        {
            $p = trim($p);
            if(empty($p)) continue;
            $this->pagenavigationList[$p] = $p;    
        }             
        
        if(isset($this->pagenavigationPrev) && is_array($this->pagenavigationPrev))foreach($this->pagenavigationPrev as $i=>$v)
        {
            foreach($this->pagenavigation as $i1=>$v1)
            {
                if($v1==$v) unset($this->pagenavigation[$i1]);   
            }
        }                                                                                  
        
        $isContinue = false;
        
        if(isset($this->pagenavigation) && is_array($this->pagenavigation))
        foreach($this->pagenavigation as $p)
        {
            $isContinue = true;
            $this->rss = $p;
            break;
        }               
        
        if(!$isContinue && !empty($this->pagenavigationPrev) && $this->IsEndSectionUrl())
        {                                 
            $this->ClearBufferStop();                                                
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
    
    protected function getOneCellStyle($list, $row, $cell){
        $chunkFilter = new ChunkXmlFilter();    
        $chunkFilter->setRows($row, 2);
        
        $objReader = PHPExcel_IOFactory::createReaderForFile($this->rss);
        $objReader->setReadFilter($chunkFilter);
        $efile = $objReader->load($this->rss);
        $efile->setActiveSheetIndex($list);
        $worksheet = $efile->getActiveSheet();    
        $val = $worksheet->getCellByColumnAndRow($cell, $row+1);  
                
        unset($objReader);
        return SotbitXlsParserUtils::getCellStyle($val); 
    }
    
    protected function getStyleHash($style = array()){
        $str='';
        foreach($style as $elS){
            $str.=$elS;
        }
        return md5($str);
    }
    
    protected function parseCatalogXlsProducts()
    {           
        $count = 0;
                                                                   
        $this->activeCurrentPage++;                                                                         
        $this->SetCatalogElementsResult($this->activeCurrentPage);
                                                                         
        $i = 0;
        $ci = 0;       
        $debug_item = self::DEFAULT_DEBUG_ITEM;

        if(!$this->ValidateUrl($this->rss)) {
            $this->rss = $_SERVER["DOCUMENT_ROOT"].'/'.$this->rss;
        } else{
            $auth = isset($this->settings["catalog"]["auth"]["active"])?true:false;  
            $gets = new FileGetHtml();  
            $ext = pathinfo($this->rss);
            $this->rss = $gets->file_get_image($this->rss,$this->proxy,$auth,false,$_SERVER["DOCUMENT_ROOT"].'/upload/parser_id'.$this->id.'.'.$ext['extension']);
      
        } 
         
        $catalogMaxLevel = (int)$this->settings['max_level_catalog'];
        $arCatalogStyles = array();
           
        foreach($this->settings['catalog_level'] as $keyList => $rows){
            foreach($rows as $keyRow => $lvl){
                if(empty($lvl))
                    continue;
                $propetiesIndex = array();
                $styleHash='';
                foreach($this->settings['catalog_level_p']['property']['lvl_'.$lvl] as $index => $val){
                    if(!empty($val)){
                        $propetiesIndex[$val] = $index;
                        if($val=='CATALOG_NAME'){
                            $styleCell = $this->getOneCellStyle($keyList, $keyRow, $index);
                            $styleHash = $this->getStyleHash($styleCell);
                        }
                    }
                }
                if(empty($styleHash)){
                    $this->errors[] = GetMessage("parser_catalog_name_not_found", array('#LVL#'=>$lvl));
                    $this->clearFields();
                }
                
                $arCatalogStyles[$styleHash] = array(
                    'list' => $keyList,
                    'row' => $keyRow,
                    'prop_index' => $propetiesIndex,
                    'level' => $lvl,
                );
            }
        }
        
        $objReader = PHPExcel_IOFactory::createReaderForFile($this->rss);
        $p=array();
        $selfobj = new SotbitXlsParserStatic($p);
        
        $spreadsheetInfo = $objReader->listWorksheetInfo($this->rss);
        
        $listCount = count($spreadsheetInfo);
        
        $arCatalogs = array();
        $arWorksheets = array();        
        
        $parent_id=1;
        $arParentsCatalogId = array();
        for($key=0;$key<$listCount;$key++){
            if(!isset($this->settings['list']['load'][$key])){
                continue;
            }
            $startRow=$this->settings['list'][$key]['first_item']?:0;
            $startRow++;        
            $totalRows = $spreadsheetInfo[$key]['totalRows'];
            $chunkSize = ($this->settings["catalog"]["step"]!='')?(int)$this->settings["catalog"]["step"]:30;
            $chunkFilter = new ChunkXmlFilter();    
            $arLines = array();
            while($startRow<=$totalRows){
       
                $objReader = PHPExcel_IOFactory::createReaderForFile($this->rss);
        
                $chunkFilter->setRows($startRow,$chunkSize);
                $objReader->setReadFilter($chunkFilter);             
        
                if($this->settings['catalog']['load_style']!=='Y') {
                    $objReader->setReadDataOnly(true);
                }
                $this->efile = $objReader->load($this->rss);
                $this->efile->setActiveSheetIndex($key);
                $worksheet = $this->efile->getActiveSheet();
                  
                
                $this->saveCurrentList($key);
                $columns_count = PHPExcel_Cell::columnIndexFromString($worksheet->getHighestDataColumn());
                $rows_count = $worksheet->getHighestDataRow();

                $cntLines = $emptyLines = 0;
                for ($row = $startRow; ($row <= $rows_count); $row++) {
                    $arLine = array();
                    $bEmpty = true;
                    for ($column = 0; $column < $columns_count; $column++) {
                        $val = $worksheet->getCellByColumnAndRow($column, $row);
                        $valText = $selfobj->getCalculatedValue($val); 
                        if(strlen(trim($valText)) > 0) $bEmpty = false;
                    
                        $curLine = array('VALUE' => $valText);
                        if($this->settings['catalog']['load_style']=="Y") {
                            $curLine['STYLE'] = SotbitXlsParserUtils::getCellStyle($val);
                        }
                        $arLine[] = $curLine; 
                    }
                    
                    $isCategoryRow = false;
                    foreach($arCatalogStyles as $key4=>$styleLvl){
                        $index = $styleLvl['prop_index']['CATALOG_NAME'];
                        $hash = $this->getStyleHash($arLine[$index]['STYLE']);
                        if(isset($arCatalogStyles[$hash])){
                            $name = $arLine[$index]['VALUE'];
                            $level = $arCatalogStyles[$hash]['level'];
                            $isCategoryRow = true;
                            break;
                        }
                    }
                    
                    if($this->settings['catalog']['load_style']=="Y" && $this->settings['create_catalog'] && $isCategoryRow){
                        $arParentsCatalogId[$level] = $parent_id;
                        $catalog = array();
                        $catalog['id'] = $parent_id;
                        $catalog['name'] = $name;
                        $catalog['parent_id'] = $arParentsCatalogId[$level-1]?:0;
                        $arCatalogs[$parent_id] = $catalog;
                        $parent_id++;
                    } else{
                        $arLine['parent_id']=$parent_id-1;
                        $arLines[$row] = $arLine;
                        $cntLines++;
                    }
                    if($bEmpty){
                        $emptyLines++;
                    }                    
                }
                //get images
                if($this->settings['image_file']['enable']=='Y'){
                    $drawCollection = $worksheet->getDrawingCollection();        
                    if($drawCollection)
                    {                           
                        foreach($drawCollection as $drawItem)
                        {                                                  
                            if ($drawItem instanceof PHPExcel_Worksheet_MemoryDrawing) {
                                $image = $drawItem->getImageResource();
                                //$renderingFunction = $image->getRenderingFunction(); 
                                $cell = $worksheet->getCell($drawItem->getCoordinates()); 
                                $colIndex = PHPExcel_Cell::columnIndexFromString($cell->getColumn());
                                $rowIndex = $cell->getRow();                
                                $arLines[$rowIndex][$colIndex-1]['resource'] = $image; 
                                $arLines[$rowIndex][$colIndex-1]['to'] = $drawItem->getIndexedFilename(); 
                                $arLines[$rowIndex][$colIndex-1]['function'] = $drawItem->getRenderingFunction(); 
                            }
                        }                  
                    }                     
                }                   
            
                $arCells = explode(':', $worksheet->getSelectedCells());
                $heghestRow = intval(preg_replace('/\D+/', '', end($arCells)));
                if(is_callable(array($worksheet, 'getRealHighestRow'))) $heghestRow = intval($worksheet->getRealHighestRow());
                elseif($worksheet->getHighestDataRow() > $heghestRow) $heghestRow = intval($worksheet->getHighestDataRow());
                
                $this->efile->__destruct();
            
                unset($objReader); 
                unset($objPHPExcel);         
                $startRow+=$chunkSize;
            }
            $arWorksheets[] = array(
                'title' => self::getCorrectCalculatedValue($worksheet->GetTitle()),
                'show_more' => ($row < $rows_count - 1),
                'lines_count' => $heghestRow,
                'lines' => $arLines
                );
        }
                
        $arRes=array();
        foreach($arWorksheets as $key=>$list){
            $arRes = array_merge($arRes,$list['lines']);
        }
        
        if ($this->settings["create_catalog"] == "Y" && $this->settings["catalog"]["load_style"] == "Y")
        {                              
            if ($this->parseCatalogSectionXls($arCatalogs) === false)
            {
                parent::SaveLog();
                return false; 
            }                           
        }
        
        $count=count($arRes);                                                         
        
        if($this->settings["catalog"]["mode"]!="debug" && !$this->agent)
        {                                                                                         
            if($count > $this->settings["catalog"]["step"] && ($this->settings["catalog"]["mode"]!="debug" && !$this->agent))
                $countStep = $this->settings["catalog"]["step"];
            else{
                $this->stepStart = true;
                if($this->CheckOnePageNavigation() || $this->CheckAlonePageNavigation($this->currentPage)) 
                    $this->pagenavigation[$this->rss] = $this->rss;
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
            $this->errors[] = GetMessage("parser_error_empty")."[".$this->rss."]";
            $this->clearFields();
        } 
                                                     
        foreach($arRes as $el){
            $ci++;
            if($this->StepContinue($ci, $count)) continue;   
            
            if($i==$debug_item && $this->settings["catalog"]["mode"]=="debug") 
                break;       
            
            $this->parseCatalogProductElementXls($el);
            
            $i++;
            file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser".$this->id.".txt", $countStep."|".$i);
            $this->CalculateStep($count);
        }
        unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser".$this->id.".txt");
    }
    
    protected function parseCatalogSectionXls(&$arCatalogs)            //parse catalogs
    {   
        if ($this->settings["create_catalog"] != "Y") return false;   
        $arr = $this->GetArrSectionXls($arCatalogs);
        
        if ($arr !== false)
        {
            $new_section_arr = $this->GetTreeArrSectionXml($arr);
        }        
        
        if(is_array($new_section_arr))
        {
             $this->AddExtFieldSection();
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
    
    protected function GetArrSectionXls(&$arCatalogs)
    {                
        $arr = array();
        
        $id = 'id';
        $name = 'name';
        $parent = 'parent_id';        
        
        foreach ($arCatalogs as $el_section)
        {
            if(isset($el_section[$id])){
                $section_id = (int)trim($el_section[$id]);
            } else {
                $this->errors[] = GetMessage("parser_no_index_category_file");
            }
            if (empty($section_id))
            {
                continue 1; 
            }
            
            if(isset($el_section[$parent])){
                $parentId = (int)trim($el_section[$parent]);
            } else {
                $this->errors[] = GetMessage("parser_no_parent_category_file");
            }
            if (!isset($parentId) || empty($parentId))
            {
                $parentId = 0;
            } 
            
            if(empty($name))
            {
                $arr[$section_id]['text'] = GetMessage("noname");
            }
            elseif(!empty($name))
            {
                if(isset($el_section[$name])){
                    $arr[$section_id]['text'] = $el_section[$name];
                } else {
                    $this->errors[] = GetMessage("parser_no_name_category_file");
                }
            }
            $arr[$section_id]['parentId'] = $parentId;
        }
        
        ksort($arr);
        return $arr;
    }
             
    protected function parseCatalogProductElementXls(&$el)
    {                              
        $this->countItem++;
        if(!$this->parserCatalogPreviewXls($el)) 
        {                              
            parent::SaveCatalogError();
            parent::clearFields();
            return false;    
        }              

        if (($this->settings["create_catalog"] != "Y" || $this->settings["catalog"]["load_style"] != "Y") && $this->settings["catalog"]["section_by_name"] != "Y")
            $this->parseCatalogSection();                           
                    
        parent::parseCatalogDate();
        $this->parseCatalogAllFieldsCsv(); 

        $db_events = GetModuleEvents("shs.parser", "parserBeforeAddElementXLS", true);
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
                          
        if(!$error && !$error_isad)
        {
            parent::AddElementCatalog();
            foreach(GetModuleEvents("shs.parser", "parserAfterAddElementXLS", true) as $arEvent)
                ExecuteModuleEventEx($arEvent, array(&$this, &$el));
        }

        if($this->isCatalog && $this->elementID)
        {   
            
            if($this->isOfferCatalog && !$this->boolOffer)
            {                                  
                parent::AddElementOfferCatalog();
                $this->elementID = $this->elementOfferID;
                $this->elementUpdate = $this->elementOfferUpdate;
            }
            if($this->boolOffer)
            {
                parent::addProductPriceOffers();
            }else{
                
                parent::AddProductCatalog();
                parent::AddMeasureCatalog();
                parent::AddPriceCatalog(); 
                $this->addAvailable();   
            }
            
            $this->parseStore();
            $this->updateQuantity();
            
        }/*else{
            $this->AddElementOfferCatalog();
            $this->AddProductCatalog();
            $this->AddMeasureCatalog();
            $this->AddPriceCatalog();    
        }*/
                                                                   
        if($this->settings['smart_log']['enabled']=='Y') {        
            $this->settings['smart_log']['result_id'] = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/result_id".$this->id.".txt");
            SmartLogs::saveNewValues($this->elementID, $this->settings["smart_log"], $this->arFields, isset($this->arPrice['PRICE'])?$this->arPrice['PRICE']:null, $this->arProduct);
        }

        parent::SetCatalogElementsResult();
        parent::clearFilesTemp();
        parent::clearFields();   
    } 

    protected function parserCatalogPreviewXls(&$el)
    {                                                 
        $this->parseCatalogIdElementXls($el);
        $this->parseCatalogXmlIdElementXls($el);
        $this->parseCatalogNamePreviewXls($el);
        //$this->parseCatalogPropertiesPreview($el);
        if($this->isCatalog) {
            $this->parseCatalogPricePreviewXls($el);        
            $this->parseCatalogAdditionalPricePreviewXls($el);        
            $this->parseCatalogAvailablePreviewXls($el);
        }         
        if (($this->settings["create_catalog"] == "Y" && $this->settings["catalog"]["load_style"] == "Y") || $this->settings["catalog"]["section_by_name"] == "Y") {
            $this->parseCatalogParrentSectionXls($el);
        }
        $this->parseCatalogPreviewPicturePreviewXls($el);
        $this->parseCatalogDetailPictureXls($el);     
        $this->parseCatalogDescriptionXls($el); 
        $this->parseCatalogDetailMorePhotoXls($el);  
        $this->parseCatalogPropertiesXls($el);      
        //$this->parserOffersCSV($el);                          //!!!!!!!!!!!!!!!!!!!!!!     
        return true;     
    }
    
    protected function parseCatalogParrentSectionXls(&$el)             //parse parent section
    {
        if($this->checkUniqCsv()) return false;
        if($this->settings["catalog"]["section_by_name"] == "Y"){
            $index_parent = $this->settings["catalog"]["id_section"];
            if(isset($el[$index_parent])){
                $parent_name = $el[$index_parent]['VALUE'];
            } else {
                $this->errors[] = GetMessage("parser_no_parent_id_category");
                return false;
            }                                   
            if(empty($parent_name)) return false; 
            else {                                                                                                                                                                 
                $parent_id = CIBlockSection::GetList(array(),array('IBLOCK_ID'=>$this->iblock_id, 'NAME'=>$parent_name))->fetch();    
                if(!empty($parent_id)){
                    $parent_id = $parent_id['ID'];
                } else {                        
                    $section = new CIBlockSection;    
                    $parent_id = $section->Add(array(
                        'NAME' => $parent_name,
                        'CODE' => $this->GetCodeSection($parent_name),
                        'ACTIVE' => 'Y',
                        'IBLOCK_ID' => $this->iblock_id,
                        'IBLOCK_SECTION_ID'=> $this->section_id,
                    ));                                                                                               
                }
            }    
                            
        } else {
            if ($this->settings["create_catalog"] != "Y" || $this->settings["catalog"]["load_style"] != "Y") return false;
        
            $index_parent = 'parent_id';
            if(isset($el[$index_parent])){
                $parent_id = $el[$index_parent];
            } else {
                $this->errors[] = GetMessage("parser_no_parent_id_category");
                return false;
            }          
            if($parent_id==='') return false;
            elseif($parent_id!=='') {
                $parent_id = $this->issetSectionCatalog($parent_id);
            } 
        }
            
        if($parent_id !== false)
        {
            $this->arFields["IBLOCK_SECTION_ID"] = $parent_id;
        } else {
            $this->parseCatalogSection();
        }   
    }
    
    protected function parseCatalogIdElementXls($el)
    {   
        $id_element = $this->settings["catalog"]["id_selector"];
        
        if ($id_element==''){         
            return false;
        }
                                                
        if(!isset($el[$id_element]))
        {                  
            //$this->errors[] = GetMessage("parser_error_id_element_notfound").GetMessage("parser_error_csv_invalid_index");                                                  
            return false;
        }
        $this->arFields["LINK"] = $el[$id_element]['VALUE'];          
        return true;
    }
    
    protected function parseCatalogXmlIdElementXls($el)
    {   
        $id_element = $this->settings["catalog"]["xml_id_selector"];
        
        if ($id_element==''){         
            return false;
        }
                                                
        if(!isset($el[$id_element]))
        {                                                                 
            return false;
        }
        $this->arFields["XML_ID"] = $el[$id_element]['VALUE'];          
        return true;
    }
    
    protected function parseCatalogNamePreviewXls($el)
    {                                                           
        if(isset($this->settings["catalog"]["detail_name"]) && $this->settings["catalog"]["detail_name"]) return false;
        $name = $this->settings["catalog"]["name"]!=''?$this->settings["catalog"]["name"]:'';
        if($name!='') {
            $detail = $this->settings["catalog"]["name"];       
            
            $arDetail = explode(",", $detail);     
            $name_text = "";
            if($arDetail && !empty($arDetail))
            {
                foreach($arDetail as $detail)
                {
                    $detail = trim($detail);
                    if($detail=="") continue 1;  
                    if(isset($el[$detail])){
                        $t=$el[$detail]['VALUE'];
                        $name_text .= $t.' ';
                    } else {
                        $this->errors[] = $this->arFields["NAME"].' '.GetMessage("parser_error_name_notfound_csv");        
                        return false;
                    }
                }
            }    
            $this->arFields["NAME"] = trim(htmlspecialchars_decode(trim(strip_tags($name_text))));
        } else {
           $this->errors[] = GetMessage("parser_error_name_notfound_csv");                                                  
           return false; 
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
    
    protected function parseCatalogPricePreviewXls(&$el)
    {                                                                                                              
        if($this->settings["catalog"]["preview_price"])
        {          
            if($this->checkUniqCsv() && (!$this->isUpdate || !$this->isUpdate["price"])) return false;
            $index_price = $this->settings["catalog"]["preview_price"];
            if($index_price!='' && isset($el[$index_price])){
                $price = $el[$index_price]['VALUE'];    
            } else {                    
               $this->errors[] = $this->arFields["NAME"].GetMessage("parser_error_price_notfound_csv");        
               return false; 
            }
            $price = $this->parseCatalogPriceFormat($price);                                       
            $price = $this->parseCatalogPriceOkrug($price);
            
            $this->arPrice["PRICE"] = trim($price);           
            if(!$this->arPrice["PRICE"])
            {
                $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."]".GetMessage("parser_error_price_notfound_csv");
                unset($this->arPrice["PRICE"]);
                return false;
            }
            $this->arPrice["CATALOG_GROUP_ID"] = $this->settings["catalog"]["price_type"];
            $this->arPrice["CURRENCY"] = $this->settings["catalog"]["currency"];
        } 
    }
    
    protected function parseCatalogAdditionalPricePreviewXls(&$el)
    {                                                                                                              
        if($this->settings["prices_preview"] && !empty($this->settings["prices_preview"]))
        {  
            $this->arAdditionalPrice = array();         
            foreach($this->settings["prices_preview"] as $id_price => $price_arr){          
                if($this->checkUniqCsv() && (!$this->isUpdate || !$this->isUpdate["price"])) return false;
                
                $index_price = $price_arr['value'];
                if($index_price!='' && isset($el[$index_price])){
                    $price = $el[$index_price]['VALUE'];    
                } else {                    
                    $this->errors[] = $this->arFields["NAME"].' ['.$price_arr['name'].'] '.GetMessage("parser_error_price_notfound_csv");        
                    continue; 
                }
                $addit_price = array();
                $price = $this->parseCatalogPriceFormat($price);                                       
                $price = $this->parseCatalogPriceOkrug($price);
            
                $addit_price["PRICE"] = trim($price);           
                if(!$addit_price["PRICE"])
                {
                    $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."]".' ['.$price_arr['name'].'] '.GetMessage("parser_error_price_notfound_csv");
                    continue;
                }                                                                  
                $addit_price["CATALOG_GROUP_ID"] = $id_price;
                $addit_price["CURRENCY"] = $this->settings['adittional_currency'][$id_price]; 
                $this->arAdditionalPrice[$id_price] = $addit_price;  
            }  
        }
    }
    
    protected function parseCatalogAvailablePreviewXls(&$el)
    {
        if($this->checkUniqCsv() && (!$this->isUpdate || !$this->isUpdate["count"])) return false;
        if(!empty($this->settings["catalog"]["preview_count"]))
        {                                                         
            $index_count = $this->settings["catalog"]["preview_count"];
            if(isset($el[$index_count])){
                $t = $el[$index_count]['VALUE']; 
            } else {
                $this->errors[] = $this->arFields["NAME"].' '.GetMessage("parser_error_count_notfound_csv");        
                return false;
            }
            $available = trim(strip_tags($t));     
            $value = $this->findAvailabilityValue($available);
            if($value){                                             
                $available = $value['count']; 
            }           
            $available = preg_replace('/[^0-9.]/', "", $available);
            
            if(is_numeric($available))
            {
                $available = intval($available);
                if($available == 0)
                {
                    $this->arFields["AVAILABLE_PREVIEW"] = 0;
                }
                else
                {
                    $this->arFields["AVAILABLE_PREVIEW"] = $available;
                }
            }
        }
        if(!isset($this->arFields["AVAILABLE_PREVIEW"])){
            if(is_numeric($this->settings["catalog"]["count_default"]))
            {
                $this->arFields["AVAILABLE_PREVIEW"] = intval($this->settings["catalog"]["count_default"]);
            }        
        }   
    }
    
    public function parseCatalogPreviewPicturePreviewXls(&$el)
    {                      
        if($this->checkUniqCsv() && (!$this->isUpdate || !$this->isUpdate["preview_img"])) return false;    
        if($this->settings["catalog"]["preview_picture"] && $this->settings["catalog"]["img_preview_from_detail"]!="Y")
        {   
            $index_img = $this->settings["catalog"]["preview_picture"];    
                                   
            if(isset($el[$index_img])){
                $price = $el[$index_img];    
            } else {                    
               $this->errors[] = $this->arFields["NAME"].GetMessage("parser_error_prev_img_notfound_csv");        
               return false; 
            }                              
            if($this->settings['image_file']['enable']!='Y'){
                //file contains links                    
                $src = isset($el[$index_img])?$el[$index_img]['VALUE']:'';           
                $src = $this->parseCaralogFilterSrc($src);
                $src = $this->getCatalogLink($src);
                /*foreach(GetModuleEvents("shs.parser", "ParserPreviewPicture", true) as $arEvent)
                    ExecuteModuleEventEx($arEvent, array(&$this, $src));*/       
                if(!self::CheckImage($src)) return;                             
                if($this->settings['image_ftp']['enable']!='Y') {
                    if(!$this->ValidateUrl($src)){
                        $src = $_SERVER["DOCUMENT_ROOT"].'/'.$src; 
                    }              
                } else { 
                    if(!$this->ValidateFtpUrl($src)){
                        $src = $_SERVER["DOCUMENT_ROOT"].'/'.$src; 
                    }     
                    if(!empty($this->settings['image_ftp']['login']) && !empty($this->settings['image_ftp']['password'])){
                        $str = explode('://',$src);
                        $src = $str[0].'://'.$this->settings['image_ftp']['login'].':'.$this->settings['image_ftp']['password'].'@'.$str[1];
                        unset($str);                        
                    }      
                }
            } elseif($el[$index_img]['to']!='' && $el[$index_img]['resource']!=null) {
                //file contains pictures object     
                // save image to disk
                $src = $this->saveImageFromResourse($el[$index_img]);
                if(!$src){
                    $this->errors[] = $this->arFields["NAME"].GetMessage("parser_error_cannot_load_image_from_file");        
                    return false;
                }
                   
            } else return false;            
            $this->arFields["PREVIEW_PICTURE"] = CFile::MakeFileArray($src);  
            $this->arrFilesTemp[] = $this->arFields["PREVIEW_PICTURE"]["tmp_name"];               
        }
    }
    
    public function saveImageFromResourse($arResource){
        $img = $arResource; 
        CheckDirPath($_SERVER["DOCUMENT_ROOT"].'/upload/shs.parser/');   
        $src = $_SERVER["DOCUMENT_ROOT"].'/upload/shs.parser/' . $img['to'];      
        switch ($img['function']) {
            case PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG:
                $ok = imagejpeg($img['resource'], $src);
                break;
            case PHPExcel_Worksheet_MemoryDrawing::RENDERING_GIF:
                $ok = imagegif($img['resource'], $src);
                break;
            case PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG:
            case PHPExcel_Worksheet_MemoryDrawing::RENDERING_DEFAULT:
                $ok = imagepng($img['resource'], $src);
                break;
        }                                                     
        if($ok)
            return $src;        
        else
            return false;
    }
    
    public function parseCatalogDetailPictureXls(&$el)
    {   
        if($this->checkUniqCsv() && (!$this->isUpdate || (!$this->isUpdate["detail_img"] && (!$this->isUpdate["preview_img"] && !$this->settings["catalog"]["img_preview_from_detail"]!="Y")))) return false;
        if($this->settings["catalog"]["detail_picture"])
        {
            $index_img = $this->settings["catalog"]["detail_picture"];    
            
            if(isset($el[$index_img])){
                $srcs = $el[$index_img]['VALUE'];    
            } else {                    
               $this->errors[] = $this->arFields["NAME"].GetMessage("parser_error_img_notfound_csv");        
               return false; 
            }         
            if($this->settings['image_file']['enable']!='Y'){
                //file contains links                                                 
                $arSelPic = explode(",", $srcs);

                foreach($arSelPic as $src)
                {
                    $src = trim($src);   
                    $src;                         
                    if(empty($src)) continue;                     
                    $src = $this->parseCaralogFilterSrc($src);
                    $src = $this->getCatalogLink($src);   
                    if($this->settings['image_ftp']['enable']!='Y') {
                        if(!$this->ValidateUrl($src)){
                            $src = $_SERVER["DOCUMENT_ROOT"].'/'.$src; 
                        }              
                    } else { 
                        if(!$this->ValidateFtpUrl($src)){
                            $src = $_SERVER["DOCUMENT_ROOT"].'/'.$src; 
                        }     
                        if(!empty($this->settings['image_ftp']['login']) && !empty($this->settings['image_ftp']['password'])){
                            $str = explode('://',$src);
                            $src = $str[0].'://'.$this->settings['image_ftp']['login'].':'.$this->settings['image_ftp']['password'].'@'.$str[1];
                            unset($str);                        
                        }      
                    }
                
                    /*foreach(GetModuleEvents("shs.parser", "ParserDetailPicture", true) as $arEvent)
                        ExecuteModuleEventEx($arEvent, array(&$this, $src));*/

                    if(!self::CheckImage($src)) continue;
                        $this->arPhoto[$src] = 1;

                    $this->arFields["DETAIL_PICTURE"] = CFile::MakeFileArray($src);
                
                    $this->arrFilesTemp[] = $this->arFields["DETAIL_PICTURE"]["tmp_name"];

                    if($this->settings["catalog"]["img_preview_from_detail"]=="Y")
                    {
                        $this->arFields["PREVIEW_PICTURE"] = $this->arFields["DETAIL_PICTURE"];
                    }
                }  
            } elseif($el[$index_img]['to']!='' && $el[$index_img]['resource']!=null) {
                //file contains pictures object     
                // save image to disk
                $src = $this->saveImageFromResourse($el[$index_img]);
                if(!$src){
                    $this->errors[] = $this->arFields["NAME"].GetMessage("parser_error_cannot_load_image_from_file");        
                    return false;
                }
                $this->arFields["DETAIL_PICTURE"] = CFile::MakeFileArray($src);     
                $this->arrFilesTemp[] = $this->arFields["DETAIL_PICTURE"]["tmp_name"]; 
                if($this->settings["catalog"]["img_preview_from_detail"]=="Y")
                {
                    $this->arFields["PREVIEW_PICTURE"] = $this->arFields["DETAIL_PICTURE"];
                }
                   
            } else return false;                                                 
        }
    }
    
    protected function parseCatalogDescriptionXls(&$el)
    {   
        if( $this->checkUniqCsv() && (!$this->isUpdate || (!$this->isUpdate["detail_descr"] && (!$this->isUpdate["preview_descr"] && !$this->settings["catalog"]["text_preview_from_detail"]!="Y")))) return false;
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
                    
                    if(isset($el[$detail])){
                        $t=$el[$detail]['VALUE'];
                        $detail_text .= $t.' ';
                    } else {
                        $this->errors[] = $this->arFields["NAME"].' '.GetMessage("parser_error_preview_text_notfound_csv");        
                        return false;
                    }
                }
            }                          

            $detail_text = trim($detail_text);
            if(isset($this->settings["loc"]["f_detail_text"]) && $this->settings["loc"]["f_detail_text"]=="Y")
            {
                $detail_text = parent::locText($detail_text, $this->detail_text_type=="html"?"html":"plain");    
            }
            $this->arFields["DETAIL_TEXT"] = $detail_text;
            $this->arFields["DETAIL_TEXT_TYPE"] = $this->detail_text_type=="html"?"html":"plain";
            if($this->settings["catalog"]["text_preview_from_detail"]=="Y")
            {
                $this->arFields["PREVIEW_TEXT"] = $this->arFields["DETAIL_TEXT"];
                $this->arFields["PREVIEW_TEXT_TYPE"] = $this->arFields["DETAIL_TEXT_TYPE"];
            }                                                  
        }
    }
    
    protected function parseCatalogDetailMorePhotoXls(&$el)
    {
        if($this->settings["catalog"]["more_image_props"])       
        {                                                  
            if($this->checkUniqCsv() && (!$this->isUpdate || !$this->isUpdate["more_img"])) return false;  
            
            if(empty($this->settings["catalog"]["delimiter_imgs"]))
                $this->settings["catalog"]["delimiter_imgs"]=',';         
            $delimiter = $this->settings["catalog"]["delimiter_imgs"];
            
            $code = $this->settings["catalog"]["more_image_props"];
            $index = $this->settings["catalog"]["more_image"];          
            $isElement = $this->checkUniqCsv();
            if($this->settings['image_file']['enable']!='Y'){                           
                //file contains links         
                if($index!='' && isset($el[$index])){
                    $srcs = $el[$index]['VALUE'];    
                } else {                    
                    $this->errors[] = $this->arFields["NAME"].GetMessage("parser_error_more_img_notfound_csv");        
                   return false; 
                }                                                                                                         
                $srcs = explode($delimiter,$srcs);      
                $n = 0;                            
                foreach($srcs as $src)
                {   
                    $src = $this->parseCaralogFilterSrc($src);
                    $src = $this->getCatalogLink($src);    
                    if($this->settings['image_ftp']['enable']!='Y') {
                        if(!$this->ValidateUrl($src)){
                            $src = $_SERVER["DOCUMENT_ROOT"].'/'.$src; 
                        }              
                    } else { 
                        if(!$this->ValidateFtpUrl($src)){
                            $src = $_SERVER["DOCUMENT_ROOT"].'/'.$src; 
                        }     
                        if(!empty($this->settings['image_ftp']['login']) && !empty($this->settings['image_ftp']['password'])){
                            $str = explode('://',$src);
                            $src = $str[0].'://'.$this->settings['image_ftp']['login'].':'.$this->settings['image_ftp']['password'].'@'.$str[1];
                            unset($str);                        
                        }      
                    }
                    if(isset($this->arPhoto[$src])) continue 1;   
                    $this->arPhoto[$src] = 1;    
                    $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["VALUE"] = CFile::MakeFileArray($src);
                    $this->arrFilesTemp[] = $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["VALUE"]["tmp_name"];
                    $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["DESCRIPTION"] = "";
                    $n++;
                }                   
            
            } else {
                //file contains pictures object     
                // save image to disk
                
                $indexs = explode($delimiter,$index);   
                if(is_array($indexs) && !empty($indexs)){                            
                } else {                    
                    $this->errors[] = $this->arFields["NAME"].GetMessage("parser_error_more_img_notfound_csv");        
                    return false; 
                }                                  
                $n = 0;                                    
                foreach($indexs as $ind){
                    if(!isset($el[$ind]) || empty($el[$ind]))
                        continue;
                    $src = $this->saveImageFromResourse($el[$ind]);
                                                                   
                    if(!$src){
                        $this->errors[] = $this->arFields["NAME"].GetMessage("parser_error_cannot_load_image_from_file");        
                        return false;
                    }                          
                    if(isset($this->arPhoto[$src])) continue 1;   
                    $this->arPhoto[$src] = 1;    
                    $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["VALUE"] = CFile::MakeFileArray($src);
                    $this->arrFilesTemp[] = $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["VALUE"]["tmp_name"];
                    $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["DESCRIPTION"] = "";
                    $n++;   
                }  
                   
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
    
    protected function parseCatalogPropertiesXls(&$el)
    {
        if($this->checkUniqCsv() && !$this->isUpdate) return false;     
        parent::parseCatalogDefaultProperties($el);  
        $this->parseCatalogIndexPropertiesXls($el);   
        parent::AllDoProps();                                       
        if($this->isCatalog) $this->parseCatalogSelectorProductXls($el); 
    }
    
    protected function parseCatalogIndexPropertiesXls(&$el)
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
                $this->parseCatalogPropFileXls($code, $el);
            } else {                                                                       
                $index = $this->settings["catalog"]["selector_prop"][$code];
                
                if(isset($el[$index])){
                    $text = $el[$index]['VALUE']; 
                } else {                                                                   
                    $this->errors[] = $code.' '.GetMessage("parser_error_prop_notfound_csv");        
                    return false; 
                }
                 
                if($arProp["USER_TYPE"]!="HTML")
                    $text = strip_tags($text);
                $text = str_replace($deleteSymb, "", $text);   
                $this->parseCatalogProp($code, "", $text);
            }                                             
        }                               
    }
    
    protected function parseCatalogPropFileXls($code, $el)
    {                              
        if($this->checkUniqCsv() && (!$this->isUpdate || !$this->isUpdate["props"])) return false;
        $index = $this->settings["catalog"]["selector_prop"][$code];             
        if(empty($this->settings["catalog"]["delimiter_imgs"]))
                $this->settings["catalog"]["delimiter_imgs"]=',';         
        $delimiter = $this->settings["catalog"]["delimiter_imgs"];  
        
        if(isset($el[$index])){
            $srcs = $el[$index]['VALUE'];    
        } else {                                                                   
            $this->errors[] = $code.' '.GetMessage("parser_error_prop_notfound_csv");        
            return false; 
        }
        $srcs = explode($delimiter,$srcs);
        
        $n = 0;

        $isElement = $this->checkUniqCsv();
                          
        foreach($srcs as $src)
        {                                
            $src = $this->parseCaralogFilterSrc($src);
            $src = $this->getCatalogLink($src);
            if(!$this->ValidateUrl($src)){
                    $src = $_SERVER["DOCUMENT_ROOT"].'/'.$src; 
            }
            
            $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["VALUE"] = CFile::MakeFileArray($src);
            $this->arrFilesTemp[] = $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["VALUE"]["tmp_name"];
            $this->arFields["PROPERTY_VALUES"][$code]["n".$n]["DESCRIPTION"] = '';
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
    
    protected function parseCatalogSelectorProductXls(&$el)
    {
        $arProperties = $this->arSelectorProduct;      
        if(!$arProperties) return false;
        if($this->checkUniqCsv() && (!$this->isUpdate || !$this->isUpdate["param"])) return false;
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
            $index = $this->settings["catalog"]["selector_product"][$code];
            $text = isset($el[$index])?$el[$index]['VALUE']:'';                                             
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
    
    public function getCalculatedValue($val) {
        try{
            if($this->params['ELEMENT_NOT_LOAD_FORMATTING']=='Y') $val = $val->getCalculatedValue();
            else $val = $val->getFormattedValue();
        }catch(Exception $ex){}
        return self::getCorrectCalculatedValue($val);
    }
      
    public static function getCorrectCalculatedValue($val)
    {
        $val = str_ireplace('_x000D_', '', $val);
        if((!defined('BX_UTF') || !BX_UTF) && CUtil::DetectUTF8($val)/*function_exists('mb_detect_encoding') && (mb_detect_encoding($val) == 'UTF-8')*/)
        {
            $val = strtr($val, array('O'=>'&#216;', '™'=>'&#153;', '®'=>'&#174;', '©'=>'&#169;'));
            $val = utf8win1251($val);
        }
        return $val;
    }
    
}

class SotbitXlsParserUtils  extends SotbitXlsParser {
    
    public function __construct($setting) {
        parent::__construct();    
        $this->settings = $setting;
    }
    
    public function getPreviewData($params = array(), $colsCount = false){
        $selfobj = new SotbitXlsParserStatic($params);
        //SotbitXlsParser::convetCyrillic($filename);
        if(!$this->ValidateUrl($this->rss)){
            $this->rss = $_SERVER["DOCUMENT_ROOT"].'/'.$this->rss;       
        } else {
            $auth = isset($this->settings["catalog"]["auth"]["active"])?true:false;  
            $gets= new FileGetHtml();  
            $ext = pathinfo($this->rss);
            $this->rss = $gets->file_get_image($this->rss,$this->proxy,$auth,false,$_SERVER["DOCUMENT_ROOT"].'/upload/parser_id'.$this->id.'.'.$ext['extension']);
        }
        
        $objReader = PHPExcel_IOFactory::createReaderForFile($this->rss);   
        if($params['load_style']!=='Y')
        {
            $objReader->setReadDataOnly(true);
        } 
        $chunkFilter = new ChunkXmlFilter();
        $objReader->setReadFilter($chunkFilter);
        if(!$colsCount)
        {
            $chunkFilter->setRows(1, max(self::PREVIEW_ITEM, 50));
        }
        else
        {
            $chunkFilter->setRows(1, 1000);
        }
        $efile = $objReader->load($this->rss);
        $arWorksheets = array();
        foreach($efile->getWorksheetIterator() as $worksheet) 
        {
            $columns_count = PHPExcel_Cell::columnIndexFromString($worksheet->getHighestDataColumn());
            $rows_count = $worksheet->getHighestDataRow();

            $arLines = array();
            $cntLines = $emptyLines = 0;
            for ($row = 0; ($row < $rows_count && count($arLines) < self::PREVIEW_ITEM+$emptyLines); $row++) 
            {
                $arLine = array();
                $bEmpty = true;
                for ($column = 0; $column < $columns_count; $column++) 
                {
                    $val = $worksheet->getCellByColumnAndRow($column, $row+1);
                    $valText = $selfobj->getCalculatedValue($val);
                    if(strlen(trim($valText)) > 0) $bEmpty = false;
                    
                    $curLine = array('VALUE' => $valText);
                    if($params["load_style"])
                    {
                        $curLine['STYLE'] = self::getCellStyle($val);
                    }
                    $arLine[] = $curLine;
                }

                $arLines[$row] = $arLine;
                if($bEmpty)
                {
                    $emptyLines++;
                }
                $cntLines++;
            }
            
            if($colsCount)
            {
                $columns_count = $colsCount;
                $arLines = array();
                $lastEmptyLines = 0;
                for ($row = $cntLines; $row < $rows_count; $row++) 
                {
                    $arLine = array();
                    $bEmpty = true;
                    for ($column = 0; $column < $columns_count; $column++) 
                    {
                        $val = $worksheet->getCellByColumnAndRow($column, $row+1);
                        $valText = $selfobj->getCalculatedValue($val);
                        if(strlen(trim($valText)) > 0) $bEmpty = false;
                        
                        $curLine = array('VALUE' => $valText);
                        if($params['load_style']!='Y')
                        {
                            $curLine['STYLE'] = self::getCellStyle($val);
                        }
                        $arLine[] = $curLine;
                    }
                    if($bEmpty) $lastEmptyLines++;
                    else $lastEmptyLines = 0;
                    $arLines[$row] = $arLine;
                }
                
                if($lastEmptyLines > 0)
                {
                    $arLines = array_slice($arLines, 0, -$lastEmptyLines, true);
                }
            }
            
            $arCells = explode(':', $worksheet->getSelectedCells());
            $heghestRow = intval(preg_replace('/\D+/', '', end($arCells)));
            if(is_callable(array($worksheet, 'getRealHighestRow'))) $heghestRow = intval($worksheet->getRealHighestRow());
            elseif($worksheet->getHighestDataRow() > $heghestRow) $heghestRow = intval($worksheet->getHighestDataRow());
          
            $arWorksheets[] = array(
                'title' => self::getCorrectCalculatedValue($worksheet->GetTitle()),
                'show_more' => ($row < $rows_count - 1),
                'lines_count' => $heghestRow,
                'lines' => $arLines
            );
        }
        return $arWorksheets;
    }
    
    public static function getCellStyle($val)
    {
        $style = $val->getStyle();
        $arStyle = array(
            'COLOR' => '#'.$style->getFont()->getColor()->getRGB(),
            //'FONT-FAMILY' => $style->getFont()->getName(),
            //'FONT-SIZE' => $style->getFont()->getSize().'px',
            'FONT-WEIGHT' => $style->getFont()->getBold()?'bold':'inherit',
            'FONT-STYLE' => $style->getFont()->getItalic()?'italic':'inherit',
            'TEXT-DECORATION' => $style->getFont()->getUnderline(),
            'BACKGROUND' => ($style->getFill()->getFillType()=='solid' ? '#'.$style->getFill()->getStartColor()->getRGB() : '')
        );
        return $arStyle;
    }
    
    public function printHtmlTable($arTable = array(), $useNum = false, $params = false, $blockId = null, $listnum){
        echo '<table class="preview-xls-data" style="border-collapse: collapse;width: 100%;">';
        if($listnum==0)
            $this->printHtmlTableSettingRow($params, count($arTable[0]), $blockId, $useNum);
            
        foreach($arTable as $i=>$row){
            $this->printHtmlTableRow($row, $i, $useNum, $listnum);            
        }
        echo '</table>';
    }
    
    public function printHtmlTableSettingRow($params, $count, $blockId = null, $useNum = false){
        if($params){
            echo '<tr class="params-row">';
                if($useNum){
                    echo '<td class="num-cell" style="min-width:30px;max-width:50px">№</td>';
                    if($this->settings['catalog']['load_style']=='Y' && $this->settings['create_catalog']=='Y'){
                        echo '<td class="menu-cell">'.GetMessage('catalog_level_xls').'</td>';
                    }
                }
            for($i=0;$i<$count;$i++){
                echo '<td><div>';
                $this->printSettingSelect('SETTINGS[index_prop]['.$i.']',$i, $blockId);
                /*=SelectBoxFromArray('SETTINGS[index_prop]['.$params['list'].']['.$i.']', 
                                      $arParamIndex, 
                                      $this->settings["catalog"]["index_prop"][$params['list']][$i], 
                                      GetMessage("parser_chose_prop"), 
                                      " style='min-width: 150px;' class='select-chosen'");*/
                echo '</div></td>';
            }
            echo '</tr>';
        }
    }
    
    public function printSettingSelect($nameSelect, $colNum, $blockId = null){
        $fields = $this->getFields($blockId);
        $value = $this->settings['index_prop'][$colNum];
                
        ?><select name="<?echo $nameSelect;?>" style='min-width: 150px;' class='select-chosen' data-placeholder="<?php echo GetMessage("parser_chose_prop");?>" data-column="<?php echo $colNum?>"><?
        echo "<option value>".GetMessage("parser_chose_prop")."</option>";
        foreach($fields as $k2=>$v2)
        {
            ?><optgroup label="<?echo $v2['title']?>" data-property="<?php echo $v2['value'];?>"><?
            foreach($v2['items'] as $k=>$v)
            {
                ?><option value="<?echo $k; ?>" <?if($k==$value){echo ' selected';}?>><?echo htmlspecialcharsbx($v); ?></option><?
            }
            ?></optgroup><?
        }
        ?></select><?
    }
    
    public function printHtmlTableRow($row, $rowNum, $useNum = false, $listnum = 0){
        if($this->settings['catalog']['load_style']=='Y' && $this->settings['create_catalog']=='Y' && (isset($this->settings['catalog_level'][$listnum][$rowNum]) && $this->settings['catalog_level'][$listnum][$rowNum]!='')){
            $this->printSettingCatalogRow($this->settings['catalog_level'][$listnum][$rowNum], count($row), $useNum, $listnum, $rowNum);
        }
        echo '<tr>';
            if($useNum){
                $this->printHtmlTableSettingCell($rowNum, $listnum);
            }
            foreach($row as $cell){
                echo '<td';
                if($this->settings["catalog"]["load_style"]){
                    echo ' style="';
                    foreach($cell['STYLE'] as $key => $param){
                        if($param)
                            echo $key.': '.$param.';';
                    }
                    echo '"';
                }
                echo '>';
                echo $cell['VALUE']?:' ';
                echo '</td>';
            }
            echo '</tr>';
    }
    
    public function printSettingCatalogRow($catalogLevel = null, $cellCount = 0, $useNum = false, $listnum = 0, $rowNum = 0){
        echo '<tr class="catalog-row">';
        if($useNum){
            echo '<td class="num-cell"></td>';
        }
        echo '<td class="menu-cell">'.GetMessage('catalog_setting_xls').'</td>';
        for($i=0;$i<$cellCount;$i++){
            echo '<td>'.SelectBoxFromArray('SETTINGS[catalog_level_p][property][lvl_'.$this->settings['catalog_level'][$listnum][$rowNum].']['.$i.']', $this->getCatalogFieldsArr(), $this->settings['catalog_level_p']['property']['lvl_'.$this->settings['catalog_level'][$listnum][$rowNum]][$i], GetMessage("change_props_xls")).'</td>';
        }
        echo '</tr>';
    }
    
    public function getCatalogFieldsArr(){
        $arr = array();
        $arr['REFERENCE']=array(GetMessage('XLS_ISECT_FI_NAME'));        
        $arr['REFERENCE_ID']=array('CATALOG_NAME');
        return $arr;
    }
    
    public function printHtmlTableSettingCell($i,$listNum = 0){
        echo '<td class="num-cell" style="min-width:30px;max-width:50px">
               <input type="radio" name="SETTINGS[list]['.$listNum.'][first_item]" '.($this->settings["list"][$listNum]["first_item"]==$i?' checked':'').' value='.($i).' title="'.GetMessage("parser_checkfirst_string_xls").'">
              </td>';
        if($this->settings['catalog']['load_style']=='Y' && $this->settings['create_catalog']=='Y'){
            echo '<td class="menu-cell">
                    <select name="SETTINGS[catalog_level]['.$listNum.']['.$i.']" class="select_catalog_level">
                        <option value="">'.GetMessage('check_catalog').'</option>';
                    for($k=1;$k<=$this->settings['max_level_catalog'];$k++){
                        echo '<option value="'.$k.'"';
                        if($this->settings['catalog_level'][$listNum][$i]==$k)
                            echo ' selected';
                        echo '>'.GetMessage('catalog_level_option_xls', array('#LEVEL#'=>$k)).'</option>';
                    }
            echo '  </select>
                  </td>';
        }
    }
    
    public function getFields($IBLOCK_ID, $offers = false)
    {
            $aFields = array();
        
            $aFields[$IBLOCK_ID]['element'] = array(
                'title' => ($offers ? GetMessage("XLS_IE_GROUP_OFFER") : GetMessage("XLS_IE_GROUP_ELEMENT")),
                'value' => 'element',
                'items' => array(),
            );
            foreach(self::getIblockElementFields() as $k=>$ar)
            {
                if($this->uid && ((is_array($this->uid) && !in_array('IE_ID', $this->uid)) || (!is_array($this->uid) && $this->uid!='IE_ID')) && $k=='IE_ID') continue;
                if($offers) $k = 'OFFER_'.$k;
                $aFields[$IBLOCK_ID]['element']['items'][$k] = $ar["name"];
            }
            
            if(!$offers) {
                foreach(self::getIblockSectionElementFields() as $k=>$ar)
                {
                    $aFields[$IBLOCK_ID]['element']['items'][$k] = $ar["name"];
                }
                
                
                for($i=1; $i<$this->sectionLevels+1; $i++)
                {
                    $aFields[$IBLOCK_ID]['section'.$i] = array(
                        'title' => sprintf(GetMessage("XLS_IE_GROUP_SECTION_LEVEL"), $i),
                        'items' => array()
                    );
                    foreach($this->getIblockSectionFields($i, $IBLOCK_ID) as $k=>$ar)
                    {
                        $aFields[$IBLOCK_ID]['section'.$i]['items'][$k] = $ar["name"];
                    }
                }
            }
            
            if($arPropFields = self::getIblockProperties($IBLOCK_ID))
            {
                $aFields[$IBLOCK_ID]['prop'] = array(
                    'title' => ($offers ? GetMessage("XLS_IE_GROUP_OFFER").' ('.GetMessage("XLS_IE_GROUP_PROP").')' : GetMessage("XLS_IE_GROUP_PROP")),
                    'value' => 'property',
                    'items' => array()
                );
                foreach($arPropFields as $ar)
                {
                    if($offers)
                    {
                        if(preg_match('/\D'.$offers.'$/', $ar["value"])) continue;
                        $ar["value"] = 'OFFER_'.$ar["value"];
                    } 
                    $aFields[$IBLOCK_ID]['prop']['items'][$ar["value"]] = $ar["name"];
                    if($ar["wdesc"])
                    {
                        $aFields[$IBLOCK_ID]['prop']['items'][$ar["value"].'_DESCRIPTION'] = $ar["name"].' ('.GetMessage("XLS_IE_PROP_DESCRIPTION").')';
                    }
                }
            }
            
            if($arCatalogFields = self::getCatalogFields($IBLOCK_ID))
            {
                $aFields[$IBLOCK_ID]['catalog'] = array(
                    'title' => GetMessage("XLS_IE_GROUP_CATALOG"),
                    'value' => 'catalog',
                    'items' => array()
                );
                foreach($arCatalogFields as $ar)
                {
                    if($offers) $ar["value"] = 'OFFER_'.$ar["value"];
                    $aFields[$IBLOCK_ID]['catalog']['items'][$ar["value"]] = $ar["name"];
                }
            }
        return $aFields[$IBLOCK_ID];
    }
    
    public static function getCatalogFields($IBLOCK_ID)
    {
        $arCatalogFields = array();
        if(CModule::IncludeModule('catalog'))
        {
            $dbRes = CCatalog::GetList(array("ID"=>"ASC"), array("IBLOCK_ID"=>$IBLOCK_ID));
            $arCatalog = $dbRes->Fetch();
            
            if($arCatalog)
            {
                $arCatalogFields[] = array(
                    "value" => "ICAT_PREVIEW_PRICE",
                    "name" => GetMessage("XLS_IE_FI_PRICE_NAME"),
                );
                
                $arCatalogFields[] = array(
                    "value" => "ICAT_QUANTITY",
                    "name" => GetMessage("XLS_IE_FI_QUANTITY"),
                );
                
                $arCatalogFields[] = array(
                    "value" => "ICAT_WEIGHT",
                    "name" => GetMessage("XLS_IE_FI_WEIGHT"),
                );
                
                $arCatalogFields[] = array(
                    "value" => "ICAT_LENGTH",
                    "name" => GetMessage("XLS_IE_FI_LENGTH"),
                );
                
                $arCatalogFields[] = array(
                    "value" => "ICAT_WIDTH",
                    "name" => GetMessage("XLS_IE_FI_WIDTH"),
                );
                
                $arCatalogFields[] = array(
                    "value" => "ICAT_HEIGHT",
                    "name" => GetMessage("XLS_IE_FI_HEIGHT"),
                );
            }
        }
        return (!empty($arCatalogFields) ? $arCatalogFields : false);
    }   
    
    public static function getIblockProperties($IBLOCK_ID)
    {
        $arProperties = array();
        if(CModule::IncludeModule('iblock'))
        {
            $dbRes = CIBlockProperty::GetList(array(
                "sort" => "asc",
                "name" => "asc",
            ) , array(
                "ACTIVE" => "Y",
                "IBLOCK_ID" => $IBLOCK_ID,
                "CHECK_PERMISSIONS" => "N",
            ));
            while($arr = $dbRes->Fetch())
            {
                $bUid = (in_array($arr['PROPERTY_TYPE'], array('S', 'N', 'L', 'E')) && $arr['MULTIPLE']=='N');
                $arProperties[] = array(
                    "value" => $arr["CODE"],
                    "name" => $arr["NAME"].' ['.$arr["CODE"].']',
                    "uid" => ($bUid ? "Y" : "N"),
                    "wdesc" => ($arr["WITH_DESCRIPTION"]=='Y')
                );
            }
        }
        return (!empty($arProperties) ? $arProperties : false);
    }
    
    public static function getIblockSectionElementFields()
    {
        $arFields = array(
           /* 'IE_SECTION_PATH' => array(
                "name" => GetMessage("XLS_IE_FI_SECTION_PATH")
            )*/
        );
        return $arFields;
    }
    
    public static function getIblockElementFields()
    {
        return array(
            "IE_NAME" => array(
                "uid" => "Y",
                "name" => GetMessage("XLS_IE_FI_NAME"),
            ),
            "IE_ID" => array(
                "uid" => "Y",
                "name" => GetMessage("XLS_IE_FI_ID"),
            ),
            "IE_XML_ID" => array(
                "uid" => "Y",
                "name" => GetMessage("XLS_IE_FI_UNIXML"),
            ),
            "IE_PREVIEW_PICTURE" => array(
                "name" => GetMessage("XLS_IE_FI_CATIMG"),
            ),
            "IE_DETAIL_PICTURE" => array(
                "name" => GetMessage("XLS_IE_FI_DETIMG"),
            ),
            "IE_DETAIL_TEXT" => array(
                "name" => GetMessage("XLS_IE_FI_DETDESCR"),
            ),
            "IE_PARENT_NAME" => array(
                "name" => GetMessage("XLS_IE_FI_PARENT_NAME"),
            ),
        );
    }    
    
    public function GetIblockSectionFields($i, $IBLOCK_ID = false)
    {
        $arSections = array(
            'ISECT'.$i.'_NAME' => array(
                "name" => GetMessage("XLS_ISECT_FI_NAME")
            ),
            'ISECT'.$i.'_CODE' => array(
                "name" => GetMessage("XLS_ISECT_FI_CODE")
            ),
            'ISECT'.$i.'_ID' => array(
                "name" => GetMessage("XLS_ISECT_FI_ID")
            ),
            'ISECT'.$i.'_XML_ID' => array(
                "name" => GetMessage("XLS_ISECT_FI_XML_ID")
            ),
            'ISECT'.$i.'_SORT' => array(
                "name" => GetMessage("XLS_ISECT_FI_SORT")
            ),
            'ISECT'.$i.'_PICTURE' => array(
                "name" => GetMessage("XLS_ISECT_FI_PICTURE")
            ),
            'ISECT'.$i.'_DETAIL_PICTURE' => array(
                "name" => GetMessage("XLS_ISECT_FI_DETAIL_PICTURE")
            ),
            'ISECT'.$i.'_DESCRIPTION' => array(
                "name" => GetMessage("XLS_ISECT_FI_DESCRIPTION"),
            ) ,
            'ISECT'.$i.'_DESCRIPTION|DESCRIPTION_TYPE=html' => array(
                "name" => GetMessage("XLS_ISECT_FI_DESCRIPTION").' (html)',
            ) ,
        );
        
        if($IBLOCK_ID)
        {
            if(!isset($this->arSectionsProps)) $this->arSectionsProps = array();
            if(!isset($this->arSectionsProps[$IBLOCK_ID]))
            {
                $dbRes = CUserTypeEntity::GetList(array(), array('ENTITY_ID' => 'IBLOCK_'.$IBLOCK_ID.'_SECTION', 'LANG' => LANGUAGE_ID));
                $arProps = array();
                while($arr = $dbRes->Fetch())
                {
                    $name = ($arr['EDIT_FORM_LABEL'] ? $arr['EDIT_FORM_LABEL'].' ('.$arr['FIELD_NAME'].')' : $arr['FIELD_NAME']);
                    $arProps[$arr['FIELD_NAME']] = array('name' => $name);
                }
                $this->arSectionsProps[$IBLOCK_ID] = $arProps;
            }
            
            if(!empty($this->arSectionsProps[$IBLOCK_ID]))
            {
                foreach($this->arSectionsProps[$IBLOCK_ID] as $k=>$v)
                {
                    $arSections['ISECT'.$i.'_'.$k] = $v;
                }
            }
        }
        
        return $arSections;
    }
}

class SotbitXlsParserStatic extends SotbitXlsParser
{
    function __construct($params)
    {
        $this->params = $params;
    }
}