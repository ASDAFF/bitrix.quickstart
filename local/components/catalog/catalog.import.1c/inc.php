<?php
set_time_limit(1800);
   
class myCIBlockCMLImport extends CIBlockCMLImport {
    
    var $iblocks = array("00007" => 4, "00006" => 3, "14872" => 2, "00005" => 1);
 
    var $shops = array( 1 => array('Р'=>20, 'П'=>21),
                        2 => array('Р'=>18, 'П'=>19),
                        3 => array('Р'=>16, 'П'=>17),
                        4 => array('Р'=>11, 'П'=>12) );
    
    function ru2Lat($string){
        $tr = array(
              "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
              "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
              "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
              "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
              "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
              "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
              "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
              "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
              "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
              "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
              "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
              "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
              "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya", " "=>"_", "-"=>"_" ,
            "."=>"_", "+"=>"_", "/"=>"_", '"'=>"_","%"=>"_" ,"*"=>"_", "#"=>"_", "="=>"_"
          ); 
          return strtolower(strtr($string,$tr));
    }

 
    function ImportSections() {
         
        foreach ($this->iblocks as $value=>$ibl) {
          
            $properties = CIBlockProperty::GetList(Array("sort"=>"asc"), 
                    Array("IBLOCK_ID"=>$ibl));
            while ($prop_fields = $properties->GetNext()) {
                if($prop_fields["XML_ID"])
                    $allprops[$ibl][$prop_fields["XML_ID"]] = $prop_fields;
            }
            
       }
   
        $tmp = array();
        
        $arP = array(
           "max_len" => 255,   
           "change_case" => false, 
           "replace_space" => '-',  
           "replace_other" => '-',   
           "delete_repeat_replace" => true  
        );

        global $DB;
        $iblock_id = false; 
        $q = 'select PARENT_ID from b_xml_tree where NAME LIKE "ЭтоГруппа" AND VALUE LIKE "истина"';
        $res = $DB->Query($q); 
        if($res->SelectedRowsCount() > 0){  
            while ($l = $res->Fetch()){ 
                $PARENT_ID = $l["PARENT_ID"];
                $res1 = $DB->Query(" select * from b_xml_tree where PARENT_ID = " . $PARENT_ID);
                $ob = array();
                while($l1 = $res1->Fetch())
                    $ob[$l1["NAME"]] = $l1["VALUE"];
                if(!$ob['КодГруппы']){
                    $iblock_id = $this->iblocks[$ob["КодТовара"]];
                    $tmp[$ob["КодТовара"]] = false;
                    continue;
                    } 
 
                $arSection = array();
                $arSection["XML_ID"] = $ob['КодТовара'];
                $arSection["NAME"] = $ob['ПолноеНаименованиеТовара'];

                $arSection['IBLOCK_SECTION_ID'] = $tmp[$ob['КодГруппы']];
                $arSection['CODE'] = $arSection["XML_ID"];//$this->ru2Lat($arSection["NAME"]);

                $obSection = new CIBlockSection;
                $rsSection = $obSection->GetList(array(), array(//"IBLOCK_ID"=>$iblock_id, 
                    "XML_ID"=>$arSection["XML_ID"]), false); 

                if($arDBSection = $rsSection->Fetch())
                {
                        if(!array_key_exists("CODE", $arSection) && is_array($this->translit_on_update))
                                $arSection["CODE"] = CUtil::translit($arSection["NAME"], LANGUAGE_ID, $this->translit_on_update);

                        $bChanged = false;
                        foreach($arSection as $key=>$value)
                        {
                                if(is_array($arDBSection[$key]) || ($arDBSection[$key] != $value))
                                {
                                        $bChanged = true;
                                        break;
                                }
                        }
                        if($bChanged)
                        {
                                foreach($arUserFields as $arField)
                                {
                                        if($arField["USER_TYPE"]["BASE_TYPE"] == "file")
                                        {
                                                $sectionUF = $USER_FIELD_MANAGER->GetUserFields("IBLOCK_".$IBLOCK_ID."_SECTION", $arDBSection["ID"]);
                                                foreach($sectionUF as $arField)
                                                {
                                                        if(
                                                                $arField["USER_TYPE"]["BASE_TYPE"] == "file"
                                                                && isset($arSection[$arField["FIELD_NAME"]])
                                                        )
                                                        {
                                                                if($arField["MULTIPLE"] == "Y" && is_array($arField["VALUE"]))
                                                                        foreach($arField["VALUE"] as $i => $old_file_id)
                                                                                $arSection[$arField["FIELD_NAME"]][] = array("del"=>true,"old_id"=>$old_file_id);
                                                                elseif($arField["MULTIPLE"] == "N" && $arField["VALUE"] > 0)
                                                                        $arSection[$arField["FIELD_NAME"]]["old_id"] = $arField["VALUE"];
                                                        }
                                                }
                                                break;
                                        }
                                }

                                $res8 = $obSection->Update($arDBSection["ID"], $arSection);
                                if(!$res8)
                                {
                                        $this->LAST_ERROR = $obSection->LAST_ERROR;
                                        return $this->LAST_ERROR;
                                }
                        }
                        $arSection["ID"] = $arDBSection["ID"];
                }
                else
                {   
                        if(!array_key_exists("CODE", $arSection))
                                $arSection["CODE"] = CUtil::translit($arSection["NAME"],
                                        LANGUAGE_ID, $arP);

                        $arSection["IBLOCK_ID"] = $iblock_id;
                        if(!isset($arSection["SORT"]))
                                $arSection["SORT"] = 500;

                        $arSection["ID"] = $obSection->Add($arSection);
                        if(!$arSection["ID"]) { 
                                $this->LAST_ERROR = $obSection->LAST_ERROR;
                             //   echo $this->LAST_ERROR; 
                        }


                }

                $tmp[$ob["КодТовара"]] = $arSection["ID"];
                $tmp_ib[$ob["КодТовара"]] = $iblock_id;
            } 
    }
        
          
   // -=============================     
        
          
        $q = 'select PARENT_ID from b_xml_tree where NAME LIKE "ЭтоГруппа" AND VALUE LIKE "ложь"';
        $res = $DB->Query($q);
        while ($l = $res->Fetch()){ 
            $PARENT_ID = $l["PARENT_ID"];
            $res1 = $DB->Query(" select * from b_xml_tree where PARENT_ID = " . $PARENT_ID);
            $ob = array();
            $ar = false;
            while($l1 = $res1->Fetch()){  
                $ob[$l1["NAME"]] = $l1["VALUE"];
                if($l1["NAME"]=="ЗначенияСвойствТовара"){
                    $prId = $l1['ID'];
                    $r = $DB->Query('select * from b_xml_tree where PARENT_ID = ' . $prId);
                    $arProps_ = false;
                    while($re = $r->Fetch())
                        $arProps_[] = $re['VALUE'];
                    for($a=0; $a<count($arProps_); $a+=3){
                        $ar[$arProps_[$a]] = array('VAL'=>$arProps_[$a+1],
                                                   "IZM"=>$arProps_[$a+2], 
                                                   "XML"=>$this->getXML($arProps_[$a])    );
                    } 
                }
            }
             
            $arEl = array();
            $arEl["XML_ID"] = $ob['КодТовара'];
            $arEl["NAME"] = $ob['НаименованиеТовара'];
            $arEl['IBLOCK_SECTION_ID'] = $tmp[$ob['КодГруппы']];
            
            $arEl["PREVIEW_TEXT"] = $ob['ПолноеНаименованиеТовара'];     
            $arEl['IBLOCK_ID'] = $tmp_ib[$ob['КодГруппы']];
            $arEl['CODE'] = $this->ru2Lat($arEl["NAME"]);
         
            $arFilter = Array(
               "IBLOCK_ID"=>$arEl['IBLOCK_ID'], 
               "XML_ID"=>$arEl["XML_ID"] );

        
              
            $res2 = CIBlockElement::GetList(Array(), $arFilter, Array("IBLOCK_ID", "ID"));
            
            if($ar_fields = $res2->GetNextElement()) {
             ///  если товар найден 
                 $ar_f = $ar_fields->GetFields();
                 
                 $PRODUCT_ID = $ar_f['ID'];
                 
                 $PRICE_TYPE_ID = 2; 
                 $arFields = Array(
                            "PRODUCT_ID" => $PRODUCT_ID,
                            "CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
                            "PRICE" => $ob['ЦенаТовара'],
                            "CURRENCY" => "RUB",  
                            "QUANTITY_FROM" => 1,
                            "QUANTITY_TO" => 10
                        );  
                  $res3 = CPrice::GetList( array(),
                        array("PRODUCT_ID" => $PRODUCT_ID,
                                "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
                            ) ); 
                            if ($arr = $res3->Fetch())  {
                                CPrice::Update($arr["ID"], $arFields); }
                            else  {
                                CPrice::Add($arFields); }
                                        
                            
                                        
            } else {
                
                 
                $el = new CIBlockElement;
   
                if(file_exists($_SERVER["DOCUMENT_ROOT"]."/upload/products/big/" . $arEl["XML_ID"] . '.jpg'))
                   $prew_pic = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/upload/products/big/" . $arEl["XML_ID"] . '.jpg');
                else 
                    $prew_pic = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/upload/no-photo.jpg"); 
             
               $arLoadProductArray = Array(
                    "MODIFIED_BY"     => 1, // элемент изменен текущим пользователем
                    "IBLOCK_SECTION_ID" => $arEl['IBLOCK_SECTION_ID'],          // элемент лежит в корне раздела
                    "IBLOCK_ID"       => $arEl['IBLOCK_ID'],
                    "CODE"            => $arEl['XML_ID'], 
                    "NAME"            => $arEl["NAME"], 
                    "XML_ID"          => $arEl["XML_ID"], 
                    "ACTIVE"          => "Y",            // активен
                    "PREVIEW_TEXT"    => $arEl["PREVIEW_TEXT"],
                    "DETAIL_TEXT"     => $arEl["PREVIEW_TEXT"],
                    "PREVIEW_PICTURE" =>$prew_pic//CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/upload/products/big/" . $arEl["XML_ID"] . '.jpg')
                    );
     
                 
                   if($PRODUCT_ID = $el->Add($arLoadProductArray))  {
                      echo "New ID: ".$PRODUCT_ID;
                    
                        $PRICE_TYPE_ID = 2;
                    
                        $arFields = Array(
                            "PRODUCT_ID" => $PRODUCT_ID,
                            "CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
                            "PRICE" => $ob['ЦенаТовара'],
                            "CURRENCY" => "RUB",
                            "QUANTITY_FROM" => 1,
                            "QUANTITY_TO" => 10
                        );
   
                        $res3 = CPrice::GetList(
                                array(),
                                array(
                                        "PRODUCT_ID" => $PRODUCT_ID,
                                        "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
                                    )
                            ); 
                                    if ($arr = $res3->Fetch())
                                    {
                                        CPrice::Update($arr["ID"], $arFields);
                                    }
                                    else
                                    {
                                        CPrice::Add($arFields); }

                  }
                  else{   
                    echo "Error: ".$el->LAST_ERROR;
                  }
   
            }       
            
            
 
            foreach($ar as $propName => $propArr){
                
                if(!$allprops[$arEl['IBLOCK_ID']][$propArr["XML"]]){
                    
                    $ibp = new CIBlockProperty;
                    $PropID = $ibp->Add(Array(
                        "NAME" => $propName,
                        "ACTIVE" => "Y",
                        "SORT" => "500",
                        "CODE" => "PROP_" . $propArr["XML"],
                        "PROPERTY_TYPE" => "S",
                        "IBLOCK_ID" => $arEl['IBLOCK_ID'],
                        "XML_ID" => $propArr["XML"]
                        ));
  
                    if($PropID) 
                        $allprops[$arEl['IBLOCK_ID']][$propArr["XML"]] = array('ID'=>$PropID);
            
                } 
                        CIBlockElement::SetPropertyValuesEx($PRODUCT_ID,
                         $arEl['IBLOCK_ID'], 
                         array($allprops[$arEl['IBLOCK_ID']][$propArr["XML"]]['ID'] =>
                               $propArr['VAL']))  ;     
            }
      
            
            
            $mp = array();
                 $picNum = 0;
                while (true) {
                    $picNum++;
                    if(file_exists($_SERVER["DOCUMENT_ROOT"]."/upload/products/big/" . $arEl["XML_ID"] . '-' . $picNum. '.jpg')){
                       $mp[] = CFile::MakeFileArray(
                              $_SERVER["DOCUMENT_ROOT"]."/upload/products/big/" .
                              $arEl["XML_ID"] . '-' . $picNum. '.jpg'); 
                    }
                    else {
                        break;
                    }
                } 
            if(count($mp)){
                 CIBlockElement::SetPropertyValuesEx($PRODUCT_ID,  $arEl['IBLOCK_ID'], 
                         array('MORE_PHOTO' => $mp));
                 echo "<br>картинки есть у  $PRODUCT_ID ";
            }
             
            
            
            if($ob['БукваСклада']){ 
                CIBlockElement::SetPropertyValuesEx($PRODUCT_ID,  $arEl['IBLOCK_ID'], 
                         array('SHOP' => $this->shops[$arEl['IBLOCK_ID']][$ob['БукваСклада']]));
            }
        
            CCatalogProduct::Add(array("ID"=>$PRODUCT_ID,
                                       "QUANTITY" => $ob['КоличествоТовара']));
              
        }   
           
     
        return true;
    }
      
    
    
        
    function getXML($str){
        return substr(md5(strtolower($str)), 10, 15);
    } 
    
     
    
}