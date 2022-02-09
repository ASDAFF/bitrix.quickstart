<?php
  
AddEventHandler("main", "OnUserTypeBuildList", array("FilterPropsType", "GetUserTypeDescription"));
AddEventHandler("iblock", "OnBeforeIBlockSectionAdd", array("FilterPropsType", "updateUserField"));
AddEventHandler("iblock", "OnBeforeIBlockSectionUpdate", array("FilterPropsType", "updateUserField"));
 
class FilterPropsType extends CUserTypeString {

    static $catalog_iblocks = array(1, 2, 3, 4); // мои инфоблоки с каталогами 
 
    static function getBySectionID($section_id){
        CModule::IncludeModule('iblock'); 
        $res = CIBlockSection::GetByID($section_id)->GetNext();
        return unserialize( 
            self::GetUserField('IBLOCK_' . $res["IBLOCK_ID"] . '_SECTION',    
                              $section_id,             
                             'UF_FILTER' . $res["IBLOCK_ID"])
            );
    }
 
    function GetUserTypeDescription() {
        return array(
            "USER_TYPE_ID" => "c_filter",
            "CLASS_NAME" => __CLASS__,
            "DESCRIPTION" => "Cписок свойств для фильтра",
            "BASE_TYPE" => "string",
            "GetEditFormHTML" => array(__CLASS__, "GetEditFormHTML"),
            'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml')
        );
    }

    function GetEditFormHTML($arUserField, $arHtmlControl) {
        CModule::IncludeModule('iblock');
        $res = CIBlockSection::GetByID($arUserField["VALUE_ID"])->GetNext();
        $properties = CIBlockProperty::GetList(Array("sort"   => "asc",
                                                     "name"   => "asc"),
                                               Array("ACTIVE"    => "Y",
                                                     "IBLOCK_ID" => $res['IBLOCK_ID']));
        ob_start();
        $values = unserialize($arUserField['VALUE']); // прости меня господи за эту тупость (
        echo "<table><tr><td></td><td>чекбокс</td><td>радио</td><td>селект</td><td>слайдер</td></tr>";
        while ($prop_fields = $properties->GetNext()) { 
            echo "<tr><td><input type='checkbox' name='c_filter[{$prop_fields["ID"]}]' " .
                 "value='{$prop_fields["ID"]}'"; 
            if(in_array($prop_fields['ID'], $values)) 
                 echo " checked='checked' ";
            echo ">"; 
            echo "{$prop_fields["NAME"]}</td><td>";
            for($a = 1; $a <= 4; $a++){
                echo "<input "; 
                if((!$values['CONFIG'][$prop_fields["ID"]] && $a == 1) || $values['CONFIG'][$prop_fields["ID"]] == $a)
                     echo " checked='checked' "; 
                echo " type='radio' name='c_filter[CONFIG][{$prop_fields["ID"]}]' value='{$a}'></td><td>";
            }
        }
        echo "</table>"; 
        $buf = ob_get_clean(); 
        return $buf;
    }
 
    static function SetUserField($entity_id, $value_id, $uf_id, $uf_value) {  
        return $GLOBALS["USER_FIELD_MANAGER"]->Update($entity_id, $value_id, Array ($uf_id => $uf_value));
    }
    
    static function GetUserField ($entity_id, $value_id, $uf_id) {
        $arUF = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields ($entity_id, $value_id);
        return $arUF[$uf_id]["VALUE"];
    }
 
    function updateUserField(&$arFields){  
        if(in_array($arFields["IBLOCK_ID"], self::$catalog_iblocks) 
           &&
           isset($_REQUEST['c_filter'])){      //    ссори что через жопу но для админки - сойдёт
            self::SetUserField('IBLOCK_' . $arFields["IBLOCK_ID"] . '_SECTION',    
                                $arFields["ID"],             
                               'UF_FILTER' . $arFields["IBLOCK_ID"],
                                serialize($_REQUEST['c_filter']));  
        }
    }
}

 

function getPropsCodes($iblock_id, $section_id, $ffilter = false){
    // $ffilter - признак что нужно тянуть только те которые показывать в фильтре
 
    $property_codes = array();
    CModule::IncludeModule('iblock');
    $arPropLinks = CIBlockSectionPropertyLink::GetArray($iblock_id,
                                                        $section_id,
                                                        true); 
    if($arPropLinks){
        foreach($arPropLinks as $prop) 
            if( ($prop["SMART_FILTER"] == 'Y')
                    || !$ffilter
                  )  $arrProps[] = $prop["PROPERTY_ID"];
  
        $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"),
                Array("ACTIVE"=>"Y", "IBLOCK_ID" => $iblock_id));
        
        while ($prop_fields = $properties->GetNext()){  
            if(in_array($prop_fields['ID'], $arrProps) && 
            in_array($prop_fields["PROPERTY_TYPE"], array('L', 'N')))
                        $property_codes[] = $prop_fields['CODE'];
        }
    }
    return $property_codes;
}



function add2compare($id){
 
    if (!isset($_SESSION["CATALOG_COMPARE_LIST"]["ITEMS"][$id])) {
        CModule::IncludeModule('iblock');
        $arSelect = array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "DETAIL_PAGE_URL");

        $arFilter = array("ID" => $id, "IBLOCK_ACTIVE" => "Y", "ACTIVE_DATE" => "Y",
                          "ACTIVE" => "Y", "CHECK_PERMISSIONS" => "Y");

        $rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $arElement = $rsElement->GetNext();

        $_SESSION["CATALOG_COMPARE_LIST"]["ITEMS"][$id] = $arElement;
    }
    
} 


function in_compare($id){
    if($_SESSION["CATALOG_COMPARE_LIST"]["ITEMS"][$id])
        return true;
    
    return false;
}


function removeFromCompare($id){
    if($_SESSION["CATALOG_COMPARE_LIST"]["ITEMS"][$id])
        unset($_SESSION["CATALOG_COMPARE_LIST"]["ITEMS"][$id]);
}
  

function prent($arr){
    echo "<pre>";
    var_dump($arr);
    echo "</pre>";
}
 
AddEventHandler("main", "OnEpilog", "OnEpilogHandler");
  
function OnEpilogHandler() {
    if (defined('ERROR_404') && ERROR_404 == 'Y') { 
        $template = '404';
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        include $_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/' . $template . '/header.php';
        include $_SERVER['DOCUMENT_ROOT'] . '/404.php';
        include $_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/' . $template . '/footer.php';
    }
}


function scandirs($start){
    $files = array();
    $handle = opendir($start);
    while (false !== ($file = readdir($handle)))
    {
        if ($file != '.' && $file != '..')
        {
            if (is_dir($start.'/'.$file))
            {
                $dir = scandirs($start.'/'.$file);
                $files[$file] = $dir;
            }
            else 
            {
                array_push($files, $file);
            }
        }
    }
    closedir($handle);
    return $files; 
} 


function agent1c(){ return "agent1c();";
    $dir = $_SERVER['DOCUMENT_ROOT'] . '/upload/1c_catalog/';
    $files = scandirs($dir);
    CModule::IncludeModule('catalog');
    require_once '../components/devteam/catalog.import.1c/inc.php';
    foreach ($files as $file) {
        if($file == 'Goods.xml')
            continue;
        $ABS_FILE_NAME = $dir . $file;
        CIBlockXMLFile::DropTemporaryTables();
        CIBlockXMLFile::CreateTemporaryTables();
        $fp = fopen($ABS_FILE_NAME, "rb");
        $total = filesize($ABS_FILE_NAME);
        if(($total > 0) && is_resource($fp))
        {
                $obXMLFile = new CIBlockXMLFile;
                if($obXMLFile->ReadXMLToDatabase($fp, $NS, $arParams["INTERVAL"]))
                {
                        $NS["STEP"] = 3;
                        $strMessage = GetMessage("CC_BSC1_FILE_READ");
                }
                else
                {
                        $strMessage = GetMessage("CC_BSC1_FILE_PROGRESS", array("#PERCENT#"=>$total > 0? round($obXMLFile->GetFilePosition()/$total*100, 2): 0));
                }
                fclose($fp);
        }
        CIBlockXMLFile::IndexTemporaryTables();
        $obCatalog = new myCIBlockCMLImport;
        $result = $obCatalog->ImportSections();  
        unlink($ABS_FILE_NAME); 
    } 
    return "agent1c();";
}


function getProps2Card($IBLOCK_ID, $section){
    CModule::IncludeModule('iblock');

    $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), 
                                           Array("IBLOCK_ID"=>$IBLOCK_ID));
    while ($prop_fields = $properties->GetNext()) {
      
        if(!in_array($prop_fields["CODE"], array('MORE_PHOTO', 'RECOMMENDED_LIST')))
            $props[] = $prop_fields["CODE"];
    
    }
 
    return $props; 
}
  
AddEventHandler("main", "OnBeforeUserRegister", "OnBeforeUserRegisterHandler"); 
function OnBeforeUserRegisterHandler(&$arFields) {
        $arEventFields = array(
                "LOGIN"       =>      $arFields["LOGIN"],
                "PASSWORD"   =>     $arFields["PASSWORD"],
                "EMAIL"       =>      $arFields["EMAIL"],
                "NAME"       =>     $arFields["NAME"],
                "LAST_NAME"   =>      $arFields["LAST_NAME"],
             ); 
        CEvent::Send("REG", 's1', $arEventFields);
}
 
AddEventHandler("sale", "OnBeforeBasketAdd", "OnBeforeBasketAdd"); 
function OnBeforeBasketAdd(&$arFields){  
     global $USER;
     if($USER->IsAuthorized()){
        CModule::IncludeModule('tc');
        $rsUser = CUser::GetByID($USER->GetID());
        $arUser = $rsUser->Fetch();
        if($arUser['UF_CARD_MODERATED']){
            $tcCards = new tcCards();
            $res = $tcCards->GetByNum($arUser['UF_CARD']);
            if($card = $res->Fetch()){
                 $cardnomer = $card["nomer"];
                 $proc = $card["procent"];  
                 if($proc){
                     $arFields["PRICE"] = $arFields["PRICE"] / 100 * (100 - $proc);
                     $arFields["CALLBACK_FUNC"] = '';
                 }
            }
        }
    }
}