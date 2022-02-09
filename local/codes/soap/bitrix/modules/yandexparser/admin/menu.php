<?
IncludeModuleLangFile(__FILE__);


  


        $items[]=array( 
            "url"         => "yandexprices.php", 
            "text"        => 'Результаты', 
            "title"       => 'Результаты', 
            "icon"        => "iblock_menu_icon_types", 
            "page_icon"   => "iblock_page_icon_types", 
            "items_id"    => "menu_yand_options", 
            "items" =>  array(
                array(
                            "url"         => "yandexresults.php", 
                            "text"        => 'Успешно обработаные', 
                            "title"       => 'Успешно обработаные',
                            "icon"        => "iblock_menu_icon_types", 
                            "page_icon"   => "iblock_page_icon_types", 
                            "items_id"    => "menu_yand_options3" 
                            )
                ,
            
                array(
                            "url"         => "yandexresults_fail.php", 
                            "text"        => 'Не обработанные', 
                            "title"       => 'Не обработанные',
                            "icon"        => "iblock_menu_icon_types", 
                            "page_icon"   => "iblock_page_icon_types", 
                            "items_id"    => "menu_yand_options4" 
                            )
                )
            );
  
    
  $aMenu = array(
    "parent_menu" => "global_menu_services", 
    "sort"        => "10",  
    "url"         => "yandexoptions.php?lang=".LANGUAGE_ID,  
    "text"        => 'Парсер yandex-маркета',     
    "title"       => 'Парсер yandex-маркетаа',
    "icon"        => "sale_menu_icon_statistic", 
    "page_icon"   => "sale_menu_icon_statistic", 
    "items_id"    => "menu_kudin_options",  
    "items"       => $items, 
  );

return $aMenu;
 

//$items = array();
//
// 
//$res = CPropertyTypes::GetList(array(),array('ACTIVE'=>'Y'));
//while($prop_type = $res->Fetch())
//    $property_types[] = $prop_type;
//
//if ($arrid) {
//    $res = CIBlock::GetList(Array(),Array('ACTIVE'=>'Y',"ID"=>$arrid), true);
//
//    while($ar_res = $res->Fetch())
//        $iblocks[] = $ar_res;
//
//    foreach ($iblocks as $iblock)
//        {
//        $sections = array();
//        $sectMenu = array();
//        
//        $arFilter = Array('IBLOCK_ID'=>$iblock['ID'], 'GLOBAL_ACTIVE'=>'Y');
//        $db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true);
//
//        while($ar_result = $db_list->GetNext())
//            $sections[]=$ar_result;
//        
//        foreach ($sections as $section) {
//            $prop = $arrMoreUrl = array();
//            foreach ($property_types as $ptype) {
//                if ($ptype['SECTION_ID'] == $section['ID']){
//                    $prop[] = array (
//                    "sort"        => $ptype['SORT'],   
//                    "url"         => "properties_group.php?id=".$ptype['ID']."&lang=".LANGUAGE_ID,  // ссылка на пункте меню
//                    "text"        => $ptype['NAME'], 
//                    "icon"        => "kudinoptions_group_small", 
//                    "items_id"    => "menu_kudin_options_proptype_".$ptype['ID'], 
//                    );
//                $arrMoreUrl[]='property_types_edit.php?ID='.$ptype['ID'].'&lang='.LANG;
//                }
//            }
//            
//            $arrMoreUrl[] = "property_types_edit.php?section_id=".$section['ID']."&lang=".LANGUAGE_ID;
//            $sectMenu[] = array( 
//                "url"         => "options.php?id=".$iblock['ID']."&section=".$section['ID']."&lang=".LANGUAGE_ID,  // ссылка на пункте меню
//                "text"        => $section['NAME'], 
//                "title"       => 'Характеритики раздела "'.$section['NAME'].'"', 
//                "icon"        => "iblock_menu_icon_sections", 
//                "page_icon"   => "iblock_page_icon_sections", 
//                "items_id"    => "menu_kudin_options_section_".$section['ID'], 
//                "items"       => $prop, 
//                "more_url"  => $arrMoreUrl
//                );
//            }
//    
//        $items[]=array( 
//            "url"         => "options.php?id=".$iblock['ID']."&lang=".LANGUAGE_ID, 
//            "text"        => $iblock['NAME'], 
//            "title"       => 'Характеритики инфоблока "'.$iblock['NAME'].'"',
//            "icon"        => "iblock_menu_icon_types", 
//            "page_icon"   => "iblock_page_icon_types", 
//            "items_id"    => "menu_kudin_options_iblock_".$iblock['ID'],  
//            "items"       => $sectMenu,       
//            );
//        }
//    }
