<?php

class remainUpdater{
    static $params = array();
    var $errors = array();
    
    function __construct() {
        foreach(array('DIR', 'EMAIL', 'RAZDELITEL') as $paramName)
            self::$params[$paramName] = COption::GetOptionString("remains", $paramName, "");
    }

    private function checkValidFileName($filename){
        if(end(explode(".", $filename))!='csv')
             return false;
        return true;
    }

    static function formatVal($el){
        if($el[0] == '"') 
            $el = substr($el, 1);
        if(substr($el, strlen($el)-1) == '"')
            $el = substr($el, 0, -1); 
        return $el; 
    }
    
    function file2arr($file){
        $result = array('FILENAME' => $file,
                        'SUPPLIER' => $this->getSupplierFromFilename($file));
        $row = 1;  
        if (($handle = fopen($file, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, self::$params['RAZDELITEL'])) !== false) { 
                $data = array_map(__CLASS__ . "::formatVal", $data);
                if($row == 1){ // заголовок
                    for($j = 2; $j < count($data); $j++) 
                        $sklad[$j] = $data[$j];
                } else {
                    $tmp = array('NAME' => $data[0],
                                 'COST' => $data[1]);
                    for($j = 2; $j < count($data); $j++)
                        $tmp['SKLAD'][$sklad[$j]] = $data[$j]; 
                
                    $result['ITEMS'][] = $tmp;
                }
                $row++;
            }
            fclose($handle);
            return $result;  
        }
    }
     
    private function getSupplierFromFilename($file){
        $filename = basename($file);
        $filenameArr = explode('-', $filename); 
        if(count($filenameArr) < 2)
            $this->errors[] = 'Ошибка в имени файла. Файл должен иметь название: '.
                              '%Код поставщика%-%Прочая информация%.csv ';
        return $filenameArr[0];
    }
    
    function scanDir($dir){
        if(!$dir)
            $dir = self::$params['DIR'];
        $dir = $_SERVER["DOCUMENT_ROOT"] . $dir;
        $dh  = opendir($dir);
        while (false !== ($filename = readdir($dh)))
            if(is_file($dir . $filename))
                if($this->checkValidFileName($filename))
                    $files[] = $dir . $filename;

        if($files)
            foreach ($files as $file){
                $arr = $this->file2arr($file);  
                $this->Update($arr); 
                unlink($file);    
            }
    }
    
    function Update($arr){ 
        if(!$arr)
            return;
        
        foreach (GetModuleEvents("remains", "OnBeforeRemainUpdate", true) as $arEvent)
            ExecuteModuleEventEx($arEvent, array(&$arr));
        
        $arr['SUPPLIER'] = matching::getSupplierIdByCode($arr['SUPPLIER']);
         
        if(!$arr['SUPPLIER']) $this->errors[] = 'Поставщик не найден'; 
        
        if(!$this->errors){
            $matching = new matching();
            foreach ($arr['ITEMS'] as $item){
                $hash = $matching->getHash($item['NAME']);
                $res = $matching->GetList(array(), 
                                          array('HASH' => $hash, 
                                                'SUPPLIER_ID' => $arr['SUPPLIER']));
                if($res->SelectedRowsCount() > 0){
                    $availability = new availability();
                    while($result = $res->Fetch()){
                        foreach($item['SKLAD'] as $skladCode => $count){
                            $storeId = matching::getStoreIdByCode($skladCode);
                            $av = $availability->GetList(array(),
                                                         array('MATCHING_ID'=> $result['ID'],
                                                               'ITEM_ID' => $result['ITEM_ID'],  
                                                               'SUPPLIER_ID' => $arr['SUPPLIER'],
                                                               'STORE_ID' => $storeId));
                            if($r = $av->Fetch()){
                                $availability->Update($r['ID'], array('AVIABLE' => $count));
                            }
                        }
                    }
                } else {
                    $without_match++; 
                    $id = $matching->Add(array('ITEM_ID' => 0,
                                               'HASH' => $hash,
                                               'SUPPLIER_ID' => $arr['SUPPLIER'],
                                               'NAME' => $item['NAME']));
                    $availability = new availability();
                    foreach($item['SKLAD'] as $skladCode => $count){
                        $storeId = matching::getStoreIdByCode($skladCode);
                        $availability->Add(
                                array('ITEM_ID'    =>  0,
                                      'MATCHING_ID'=>  $id,
                                      'STORE_ID'   =>  $storeId,
                                      'AVIABLE'    =>  $count,
                                      'SUPPLIER_ID'=>  $arr['SUPPLIER']));
                        }
                }
            }
        } 
        foreach (GetModuleEvents("remains", "OnAfterRemainUpdate", true) as $arEvent)
            ExecuteModuleEventEx($arEvent, array(&$arr, 
                                                 $this->errors,
                                                 array('WITHOUT_MATCH' => $without_match)));
    }
}