<? 
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)  die();
CModule::IncludeModule('sale');
CModule::IncludeModule('iblock');
 
foreach ($arResult['SECTIONS'] as $section)
    if ($section['DEPTH_LEVEL'] == 1) {
        $arResult['FIRST_LEVEL_SECTIONS'][] = $section;

        $arFilter = Array(  "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                            "SECTION_ID" => $section['ID'],
                            "INCLUDE_SUBSECTIONS" => "Y" );

        $res = CIBlockElement::GetList(Array("RAND" => "ASC"),
                $arFilter, 
                false,
                array("nTopCount" => 1),
                array('IBLOCK_ID', "ID"));
        if ($ar_fields = $res->GetNext()) { 
             
//            $ar_fields['PRICE_FORMATED'] = SaleFormatCurrency($ar_fields["CATALOG_PRICE_2"], $ar_fields["CATALOG_CURRENCY_2"]);
//        
//            if ($ar_fields['PREVIEW_PICTURE']) {
//
//                $file = CFile::ResizeImageGet($ar_fields['PREVIEW_PICTURE'], array('width' => 123,
//                            'height' => 106), BX_RESIZE_IMAGE_PROPORTIONAL, true);
//
//                $ar_fields['PREVIEW_PICTURE'] = array();
//                $ar_fields['PREVIEW_PICTURE']['SRC'] = $file["src"];
//                $ar_fields['PREVIEW_PICTURE']['WIDTH'] = $file['width'];
//                $ar_fields['PREVIEW_PICTURE']['HEIGHT'] = $file['height'];
//            }
            $arResult['ITEMS'][$section['ID']] = $ar_fields;
        }
         
    }
    
     