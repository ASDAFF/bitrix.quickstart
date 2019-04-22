<?
use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity;
use Bitrix\Seo\Engine;
use Bitrix\Main\Text\Converter;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\IO\Path;

\Bitrix\Main\Loader::includeModule('seo');
\Bitrix\Main\Loader::includeModule('socialservices');

class SotbitCsvParser extends SotbitXmlParser{
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
        if(empty($this->settings["catalog"]["delimiter"]))
            $this->settings["catalog"]["delimiter"]=';';
            
        if($this->settings["catalog"]["header"])
            $this->settings["catalog"]["header"]=true;
        else
            $this->settings["catalog"]["header"]=false;
    }

    protected function parseCsvCatalog()
    {                        
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/csv_data.php");
        set_time_limit(0);
        parent::ClearAjaxFiles(); 
        parent::DeleteLog(); 
        parent::checkActionBegin(); 
        $this->arUrl = array(); 
        if(isset($this->settings["catalog"]["url_dop"]) && !empty($this->settings["catalog"]["url_dop"]))$this->arUrl = explode("\r\n", $this->settings["catalog"]["url_dop"]);
        
        $this->arUrl = array_merge(array($this->rss), $this->arUrl);
        $this->arUrlSave = $this->arUrl; 
        
        if(!$this->PageFromFileCSV()) return false;    
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
            $this->parseCatalogCsvProducts();
            
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
            
            if( (!parent::CheckOnePageNavigation() && $this->agent) || 
                (!parent::CheckOnePageNavigation() && !$this->agent && 
                $this->settings["catalog"]["mode"]=="debug")) 
                    parent::parseCatalogPages();  
                           
            if($this->settings['smart_log']['enabled']=='Y'){                                                                                                              
                $this->settings['smart_log']['result_id'] = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/result_id".$this->id.".txt");
                $this->settings['smart_log']['result_id'] = \Bitrix\Shs\ParserResultTable::updateEndTime($this->settings['smart_log']['result_id']);
            }
                    
            if(parent::CheckOnePageNavigation() && $this->stepStart)
            {            
                if(parent::IsEndSectionUrl()) parent::ClearBufferStop();
                else parent::ClearBufferStep();
                return false;    
            }
        endforeach;   
        
        parent::checkActionAgent($this->agent);   
                                        
        
        if($this->agent || $this->settings['catalog']['mode']=='debug'){                                                 
            foreach(GetModuleEvents("shs.parser", "EndPars", true) as $arEvent)
                ExecuteModuleEventEx($arEvent, array($this->id)); 
        } 
    }
    
    protected function parseCatalogCsvProducts()
    {      
        $count = 0;
                                                                   
        $this->activeCurrentPage++;                                                                         
        $this->SetCatalogElementsResult($this->activeCurrentPage);
                                                                         
        $i = 0;
        $ci = 0;       
        $debug_item = self::DEFAULT_DEBUG_ITEM;                                  
            
        $csvFile = new CCSVData('R', false);     
        if(!$this->ValidateUrl($this->rss))
            $this->rss = $_SERVER["DOCUMENT_ROOT"].'/'.$this->rss;       
        else{
            $auth = isset($this->settings["catalog"]["auth"]["active"])?true:false;                
            $gets= new FileGetHtml();  
            $this->rss = $gets->file_get_image($this->rss,$this->proxy,$auth,false,$_SERVER["DOCUMENT_ROOT"].'/upload/parser_id'.$this->id.'.csv');
        }                                                                                         
        $csvFile->LoadFile($this->rss);
        if($this->encoding!="utf-8") {
            $csvFile->__buffer = mb_convert_encoding($csvFile->__buffer , "UTF-8", strtoupper($this->encoding));
        }
        $csvFile->SetDelimiter($this->settings["catalog"]["delimiter"]);
        $arRes=array();       
        while ($el = $csvFile->Fetch()) {
            $arRes[]=$el;  
        }  
        
        if ($this->settings["catalog"]["add_parser_section"] == "Y" && intval($this->settings["catalog"]["attr_id_category"])!==null)
        {                              
            if ($this->parseCatalogSectionCsv($arRes) === false)
            {
                parent::SaveLog();
                return false; 
            }                           
        }
        
        $headers=array();
        if($this->settings["catalog"]["header"])
            $headers=array_shift($arRes);  
                         
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
                                                     
        foreach($arRes as $el)
        {
            $ci++;
            if($this->StepContinue($ci, $count)) continue;   
            
            if($i==$debug_item && $this->settings["catalog"]["mode"]=="debug") 
                break;       
            
            $this->parseCatalogProductElementCsv($el);
            
            $i++;
            file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser".$this->id.".txt", $countStep."|".$i);
            $this->CalculateStep($count);
        }       
                           
        unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/shs.parser/include/count_parser".$this->id.".txt");
    }
             
    protected function parseCatalogProductElementCsv(&$el)
    {                              
        $this->countItem++;
        //$this->parseCatalogSection(); 
        if(!$this->parserCatalogPreviewCsv($el)) 
        {                              
            parent::SaveCatalogError();
            parent::clearFields();
            return false;    
        }              
        /*$this->parserCatalogDetail();
        $this->parseCatalogSection();
        $this->parseCatalogMeta();
        $this->parseCatalogFirstUrl();*/  
        if($this->settings["catalog"]["add_parser_section"] != "Y" && $this->settings["catalog"]["section_by_name"] != "Y") $this->parseCatalogSection();                                 
                    
        parent::parseCatalogDate();
        $this->parseCatalogAllFieldsCsv(); 
        //$this->parseCatalogSection(); 

        $db_events = GetModuleEvents("shs.parser", "parserBeforeAddElementCSV", true);
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
            foreach(GetModuleEvents("shs.parser", "parserAfterAddElementCSV", true) as $arEvent)
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

    protected function parserCatalogPreviewCsv(&$el)
    {                                                 
        $this->parseCatalogIdElementCSV($el);         
        $this->parseCatalogXmlIdElementCSV($el);
        $this->parseCatalogNamePreviewCSV($el);
        //$this->parseCatalogPropertiesPreview($el);
        if($this->isCatalog) {   
            $this->parseCatalogPricePreviewCSV($el);        
            $this->parseCatalogAdditionalPricePreviewCSV($el);        
            $this->parseCatalogAvailablePreviewCSV($el);
        }         
        if ($this->settings["catalog"]["add_parser_section"] == "Y" || $this->settings["catalog"]["section_by_name"] == "Y") {
            $this->parseCatalogParrentSectionCsv($el);
        }
        $this->parseCatalogPreviewPicturePreviewCSV($el);
        $this->parseCatalogDetailPictureCSV($el);     
        $this->parseCatalogDescriptionCSV($el); 
        $this->parseCatalogDetailMorePhotoCSV($el);  
        $this->parseCatalogPropertiesCSV($el);      
        $this->parserOffersCSV($el);                          //!!!!!!!!!!!!!!!!!!!!!!               
        return true;     
    }
    
    protected function parseCatalogIdElementCSV($el)
    {   
        $id_element = $this->settings["catalog"]["id_selector"];
        
        if ($id_element==''){         
            return false;
        }
                                                
        if(!isset($el[$id_element]) or $el[$id_element]=='')
        {                  
            return false;
        }
        $this->arFields["LINK"] = $el[$id_element];          
        return true;
    }
    
    protected function parseCatalogXmlIdElementCsv($el)
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
    
    protected function parseCatalogNamePreviewCSV($el)
    {                                                           
        if(isset($this->settings["catalog"]["detail_name"]) && $this->settings["catalog"]["detail_name"]) return false;
        $name = $this->settings["catalog"]["name"]!=''?$this->settings["catalog"]["name"]:'';
          
        if(isset($el[$name]) and $name!='') 
            $this->arFields["NAME"] = trim(htmlspecialchars_decode(trim(strip_tags($el[$name]))));
        else{
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
    
    protected function parseCatalogPricePreviewCSV(&$el)
    {                                                                                                              
        if($this->settings["catalog"]["preview_price"]!='')
        {          
            if($this->checkUniqCsv() && (!$this->isUpdate || !$this->isUpdate["price"])) return false;
            $index_price = $this->settings["catalog"]["preview_price"];
            if($index_price!='' && isset($el[$index_price])){
                $price = $el[$index_price];    
            } else {                    
               $this->errors[] = $this->arFields["NAME"].GetMessage("parser_error_price_notfound_csv");        
               return false; 
            }
            $price = $this->parseCatalogPriceFormat($price);                                       
            //$price = $this->parseCatalogPriceOkrug($price);
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
    
    protected function parseCatalogAdditionalPricePreviewCSV(&$el)
    {                                                                                                                                                       
        if($this->settings["prices_preview"] && !empty($this->settings["prices_preview"]))
        {          
            if($this->checkUniqCsv() && (!$this->isUpdate || !$this->isUpdate["price"])) return false;
            $this->arAdditionalPrice = array();         
            foreach($this->settings["prices_preview"] as $id_price => $price_arr){   
                $index_price = $price_arr['value'];
                if($index_price!=='' && isset($el[$index_price])){
                    $price = $el[$index_price];    
                } else {                    
                    $this->errors[] = $this->arFields["NAME"].GetMessage("parser_error_price_notfound_csv");        
                    continue; 
                }
                
                $addit_price = array();
                $price = $this->parseCatalogPriceFormat($price);                                       
                //$price = $this->parseCatalogPriceOkrug($price);
                
                $addit_price["PRICE"] = trim($price);           
                if(!$addit_price["PRICE"])
                {
                    $this->errors[] = $this->arFields["NAME"]."[".$this->arFields["LINK"]."]".GetMessage("parser_error_price_notfound_csv");
                    unset($addit_price["PRICE"]);                                                                 
                    continue;
                }
                $addit_price["CATALOG_GROUP_ID"] = $id_price;
                $addit_price["CURRENCY"] = $this->settings['adittional_currency'][$id_price]; 
                $this->arAdditionalPrice[$id_price] = $addit_price;   
            }
        }                                     
    }
    
    protected function parseCatalogAvailablePreviewCSV(&$el)
    {
        if($this->settings["catalog"]["preview_count"]!='')
        {                 
            if($this->checkUniqCsv() && (!$this->isUpdate || !$this->isUpdate["count"])) 
                return false;
            $index_count = $this->settings["catalog"]["preview_count"];
            if(isset($el[$index_count])){
                $t = $el[$index_count]; 
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
            
            if(is_numeric($available)){
                $available = intval($available);
                if($available == 0){
                    $this->arFields["AVAILABLE_PREVIEW"] = 0;
                } else {
                    $this->arFields["AVAILABLE_PREVIEW"] = $available;
                }
            }
        } elseif(is_numeric($this->settings["catalog"]["count_default"])) {
            $this->arFields["AVAILABLE_PREVIEW"] = intval($this->settings["catalog"]["count_default"]);
        }                                        
    }
    
    public function parseCatalogPreviewPicturePreviewCSV(&$el)
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
            
            $src = isset($el[$index_img])?$el[$index_img]:'';           
            $src = $this->parseCaralogFilterSrc($src);
            $src = $this->getCatalogLink($src);
            /*foreach(GetModuleEvents("shs.parser", "ParserPreviewPicture", true) as $arEvent)
                    ExecuteModuleEventEx($arEvent, array(&$this, $src));*/
            //$src = str_replace("cdn.", "", $src);    
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
            
            $this->arFields["PREVIEW_PICTURE"] = CFile::MakeFileArray($src);
            $this->arrFilesTemp[] = $this->arFields["PREVIEW_PICTURE"]["tmp_name"];               
        }
    }
    
    public function parseCatalogDetailPictureCSV(&$el)
    {   
        if($this->checkUniqCsv() && (!$this->isUpdate || (!$this->isUpdate["detail_img"] && (!$this->isUpdate["preview_img"] && !$this->settings["catalog"]["img_preview_from_detail"]!="Y")))) return false;
        if($this->settings["catalog"]["detail_picture"])
        {
            $index_img = $this->settings["catalog"]["detail_picture"];    
            
            if(isset($el[$index_img])){
                $srcs = $el[$index_img];    
            } else {                    
               $this->errors[] = $this->arFields["NAME"].GetMessage("parser_error_img_notfound_csv");        
               return false; 
            }                                                  
            $arSelPic = explode(",", $srcs);

            foreach($arSelPic as $src)
            {
                $src = trim($src);                            
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
        }
    }
    
    protected function parseCatalogDescriptionCSV(&$el)
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
                        $t=$el[$detail];
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
    
    protected function parseCatalogDetailMorePhotoCSV(&$el)
    {
        if($this->settings["catalog"]["more_image_props"])       
        {                                                  
            if($this->checkUniqCsv() && (!$this->isUpdate || !$this->isUpdate["more_img"])) return false;  
            
            if(empty($this->settings["catalog"]["delimiter_imgs"]))
                $this->settings["catalog"]["delimiter_imgs"]=',';         
            $delimiter = $this->settings["catalog"]["delimiter_imgs"];
            
            $code = $this->settings["catalog"]["more_image_props"];
            $index = $this->settings["catalog"]["more_image"];
            
            if($index!='' && isset($el[$index])){
                $srcs = $el[$index];    
            } else {                    
               $this->errors[] = $this->arFields["NAME"].GetMessage("parser_error_more_img_notfound_csv");        
               return false; 
            } 
                                                                                                                          
            $srcs = explode($delimiter,$srcs);      
                        
            $n = 0;

            $isElement = $this->checkUniqCsv();
            
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
    
    protected function parseCatalogPropertiesCSV(&$el)
    {
        if($this->checkUniqCsv() && !$this->isUpdate) return false;     
        parent::parseCatalogDefaultProperties($el);  
        $this->parseCatalogIndexProperties($el);   
        parent::AllDoProps();                                       
        if($this->isCatalog) $this->parseCatalogSelectorProductCSV($el); 
    }
    
    protected function parseCatalogIndexProperties(&$el)
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
                $this->parseCatalogPropFileCSV($code, $el);
            } else {                                                                       
                $index = $this->settings["catalog"]["selector_prop"][$code];
                
                if(isset($el[$index])){
                    $text = $el[$index];    
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
    
    protected function parseCatalogPropFileCSV($code, $el)
    {                              
        if($this->checkUniqCsv() && (!$this->isUpdate || !$this->isUpdate["props"])) return false;
        $index = $this->settings["catalog"]["selector_prop"][$code];             
        if(empty($this->settings["catalog"]["delimiter_imgs"]))
                $this->settings["catalog"]["delimiter_imgs"]=',';         
        $delimiter = $this->settings["catalog"]["delimiter_imgs"];  
        
        if(isset($el[$index])){
            $srcs = $el[$index];    
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
    
    protected function parseCatalogSelectorProductCSV(&$el)
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
            $text = isset($el[$index])?$el[$index]:'';                                             
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
    
    protected function parserOffersCSV($el)
    {
        $this->boolOffer = false;
        if($this->settings["offer"]["load"]=="table" && $this->isOfferParsing && isset($this->settings["offer"]["selector_item"]) && $this->settings["offer"]["selector_item"])
        {
            $this->parserOffersCsvTable($el);
        }elseif($this->settings["offer"]["load"]=="one" && $this->isOfferParsing && isset($this->settings["offer"]["one"]["selector"]) && $this->settings["offer"]["one"]["selector"])
        {
            $this->parserOffersCsvOne($el);
        }
    }   
       
    protected function PageFromFileCSV()
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
        
        $isContinue = false;
        
        if(isset($this->pagenavigation) && is_array($this->pagenavigation))foreach($this->pagenavigation as $p)
        {
            $isContinue = true;
            $this->rss = $p;
            break;
        }
                             
        
        if(!$isContinue && !empty($this->pagenavigationPrev) && $this->IsEndSectionUrl())
        {                                 
            $this->ClearBufferStop();                                                                  //<-   
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
    
    protected function parseCatalogSectionCsv(&$arRes)            //parse catalogs
    {   
        if ($this->settings["catalog"]["add_parser_section"] != "Y") return false;   
        if (!isset($this->settings['catalog']['attr_id_category']) || $this->settings['catalog']['attr_id_category']=='')
        {   
            $this->errors[] = GetMessage("parser_no_index_category");
            return false;
        }
        
        $arr = $this->GetArrSectionCsv($arRes);
        
        if ($arr !== false)
        {
            $new_section_arr = $this->GetTreeArrSectionXml($arr);
        }
        
        if(is_array($new_section_arr))
        {
            if($this->settings["catalog"]["field_id_category"] == "")
            {
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
    
    protected function GetArrSectionCsv(&$arRes)
    {
        if(empty($this->settings['catalog']['catalog_file_end'])) {
            $this->errors[] = GetMessage("parser_no_end_category");
            return false;
        }
        
        $end_section = $this->settings['catalog']['catalog_file_end'];
        
        $arr_section = array_slice($arRes, 0, $end_section);
        $arRes = array_slice($arRes, $end_section);     
        
        $headers=array();
        if($this->settings["catalog"]["catalog_file_header"])
            $headers=array_shift($arr_section);    
        
        $arr = array();
        $id = $this->settings['catalog']["attr_id_category"];
        $name = $this->settings['catalog']["attr_category"];
        $parent = $this->settings['catalog']["attr_id_parrent_category"];
        
        
        foreach ($arr_section as $el_section)
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
    
   protected function parseCatalogParrentSectionCsv(&$el)             //parse parent section
   {
        if($this->checkUniqCsv()) return false;
        if($this->settings["catalog"]["section_by_name"] == "Y"){
            $index_parent = $this->settings["catalog"]["id_section"];
            if(isset($el[$index_parent])){
                $parent_name = $el[$index_parent];
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
            if (($this->settings["catalog"]["add_parser_section"] != "Y") || empty($this->settings["catalog"]["id_section"])) return false;
        
            $index_parent = $this->settings["catalog"]["id_section"];
            if(isset($el[$index_parent])){
                $parent_id = $el[$index_parent];
            } else {
                $this->errors[] = GetMessage("parser_no_parent_id_category");
                return false;
            }
    
            if(empty($parent_id)) return false;   
            elseif(!empty($parent_id)) {
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
    
    protected function checkUniqCsv()
    {                                   
        if($this->elementUpdate) return $this->elementUpdate;
        if(!isset($this->arSortUpdate)) $this->arSortUpdate = array();
        if(
            (isset($this->uniqFields["LINK"]) && $this->uniqFields["LINK"] && isset($this->arFields["LINK"]) && $this->arFields["LINK"]!=='') or
            (isset($this->uniqFields["NAME"]) && $this->uniqFields["NAME"] && isset($this->arFields["NAME"]) && $this->arFields["NAME"]!=='') or
            (isset($this->uniqFields["XML_ID"]) && $this->uniqFields["XML_ID"])
        ){
            $str='';
            if(isset($this->uniqFields["LINK"]) && $this->uniqFields["LINK"] && isset($this->arFields["LINK"]) && $this->arFields["LINK"]!==''){
                $str=$str.$this->arFields["LINK"];
            }
            if(isset($this->uniqFields["NAME"]) && $this->uniqFields["NAME"] && isset($this->arFields["NAME"]) && $this->arFields["NAME"]!==''){
                $str=$str.$this->arFields["NAME"];
            }
            
            $uniq = md5($str);
            
            if(isset($this->uniqFields["XML_ID"]) && $this->uniqFields["XML_ID"] ){
                if(isset($this->arFields["XML_ID"]) && $this->arFields["XML_ID"]!==''){
                    $uniq=$this->arFields["XML_ID"];
                }
                else{
                    if(isset($this->arFields["NAME"]) && $this->arFields["NAME"]!==''){
                        $str=$this->arFields["NAME"];
                        $uniq = md5($str);
                    }                
                }  
            }
            
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
    
    protected function parseCatalogAllFieldsCsv()
    {
        if($this->updateActive && isset($this->settings["catalog"]["uniq"]["action"]) && $this->settings["catalog"]["uniq"]["action"]=="A")
            $this->arFields["ACTIVE"] = $this->active_element;
        if($this->checkUniqCsv()) return false;
        
        $this->arFields["IBLOCK_ID"] = $this->iblock_id;
        $this->arFields["ACTIVE"] = $this->active_element;
        if($this->code_element=="Y")
        {
            $this->arFields["CODE"] = $this->getCodeElement($this->arFields["NAME"]);
        }
        if($this->uniqFields["LINK"] or $this->uniqFields["NAME"] or $this->uniqFields["XML_ID"])
        {            
            $str='';
            if(isset($this->uniqFields["LINK"]) && $this->uniqFields["LINK"] && isset($this->arFields["LINK"]) && $this->arFields["LINK"]!==''){
                $str.=$this->arFields["LINK"];
            }
            if(isset($this->uniqFields["NAME"]) && $this->uniqFields["NAME"] && isset($this->arFields["NAME"]) && $this->arFields["NAME"]!==''){
                $str.=$this->arFields["NAME"];
            }
            
            $uniq = md5($str);
            
            if(isset($this->uniqFields["XML_ID"]) && $this->uniqFields["XML_ID"] ){
                if(isset($this->arFields["XML_ID"]) && $this->arFields["XML_ID"]!==''){
                    $uniq=$this->arFields["XML_ID"];
                }
                else{
                    if(isset($this->arFields["NAME"]) && $this->arFields["NAME"]!==''){
                        $str=$this->arFields["NAME"];
                        $uniq = md5($str);
                    }                
                }  
            }
            $this->arFields["XML_ID"] = $uniq;
        }
        if($this->date_active=="NOW") $this->arFields["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "SHORT");
        elseif($this->date_active=="NOW_TIME") $this->arFields["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "FULL");
        //elseif($this->date_active=="PUBLIC" && $unix) $this->arFields["DATE_ACTIVE_FROM"] = ConvertTimeStamp($unix, "FULL");
    }
    
}
?>