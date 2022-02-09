<?
    function pr($text,$adm=1){
        GLOBAL $USER;
        if($adm==1){
            if($USER->IsAdmin()){
                echo "<pre>";
                print_r($text);
                echo "</pre>";
            }
        } else {
            echo "<pre>";
            print_r($text);
            echo "</pre>";
        }
    }
    AddEventHandler("main", "OnBeforeUserRegister", "OnBeforeUserUpdateHandler");
    AddEventHandler("main", "OnBeforeUserUpdate", "OnBeforeUserUpdateHandler");



    function OnBeforeUserUpdateHandler(&$arFields)
    {
        $arFields["LOGIN"] = $arFields["EMAIL"];
        return $arFields;
    }

    // ������ ��������� ��� ��������
    class CSubsections
    {
        function Init()
        {
            global $APPLICATION;

            $sef_folder = (isset($_SERVER["REAL_FILE_PATH"])  &&  $_SERVER["REAL_FILE_PATH"] != "" 
                ?  str_replace("index.php", "", $_SERVER["REAL_FILE_PATH"])
                :  $APPLICATION->GetCurDir(false)
            );
            $uri = $GLOBALS["BACK_REQUEST_URI"] = $APPLICATION->GetCurDir(false);
            $uri = str_replace($sef_folder, "", $uri);
            $uri = trim($uri, "/");
            $tmp_uri = "";

            if($uri  &&  CModule::IncludeModule("iblock")) 
            {
                $rs = CIBlockElement::GetList(
                    array(), 
                    array("=CODE" => $uri), 
                    false, 
                    array("nTopCount" => 1), 
                    array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME")
                );
                if($ar = $rs->Fetch()) 
                {
                    $tmp_uri = $sef_folder.intval($ar["IBLOCK_SECTION_ID"])."/".$ar["ID"]."/";
                }
                else 
                {
                    $rs = CIBlockSection::GetList(
                        array(), 
                        array("=CODE" => $uri), 
                        false, 
                        array("ID")
                    );
                    if($ar = $rs->Fetch()) 
                    {
                        $tmp_uri = $sef_folder.$ar["ID"]."/";
                    }
                }
            }

            if($tmp_uri)
            {
                $_SERVER["REQUEST_URI"] = $REQUEST_URI = $tmp_uri; 
                $APPLICATION->sDocPath2 = $tmp_uri."index.php";
                CSubsections::InitParser();
            }
            elseif($sef_folder == $APPLICATION->GetCurDir(false))
            {
                CSubsections::InitParser();
            }
        }

        function Back($arResult)
        {
            global $APPLICATION;

            $arResult["URL_TEMPLATES"]["section"] = "#SECTION_CODE#/";
            $arResult["URL_TEMPLATES"]["element"] = "#ELEMENT_CODE#/";
            $_SERVER["REQUEST_URI"] = $REQUEST_URI = $GLOBALS["BACK_REQUEST_URI"];
            $APPLICATION->sDocPath2 = $GLOBALS["BACK_REQUEST_URI"]."index.php";
            unset($GLOBALS["BACK_REQUEST_URI"]);
        }

        function Parse($html)
        {
            $html = str_ireplace("%"."2f", "/", $html);
        }

        function InitParser()
        {
            if(!defined("CSUBSECTIONS_INIT_HANDLER"))
            {
                define("CSUBSECTIONS_INIT_HANDLER", true);
                AddEventHandler("main", "OnEndBufferContent", array("CSubsections", "Parse"), 10000);
            }
        }

        function GetCode($iblock_id, $section_id, $element_name="")
        {
            $arPath = array();

            if(CModule::IncludeModule("iblock"))
            {
                if($element_name)
                {
                    $element_name = CUtil::translit($element_name, LANGUAGE_ID);
                    $element_name = $element_name?  "/".$element_name:  "";
                }

                if($rs = CIBlockSection::GetNavChain($iblock_id, $section_id))
                {
                    while($ar = $rs->Fetch()) 
                    {
                        $arPath[] = CUtil::translit($ar["NAME"], LANGUAGE_ID);
                    }
                }
            }

            return trim(implode("/", $arPath).$element_name, "/");
        }

        function ElementAddHandler($arFields)
        {
            //if($GLOBALS["IBLOCK_ID"] == $IBLOCK_ID)
            //{
            $oElement = new CIBlockElement();
            $oElement->Update($arFields["ID"], array("CODE" => "recalculate"));
            //}
        }

        function ElementUpdateHandler($arFields)
        {
            //if($GLOBALS["IBLOCK_ID"] == $IBLOCK_ID)
            //{
            if($rs = CIBlockElement::GetByID($arFields["ID"]))
            {
                if($ar = $rs->Fetch()) 
                {
                    $arFields["CODE"] = CSubsections::GetCode(
                        $ar["IBLOCK_ID"], 
                        $ar["IBLOCK_SECTION_ID"],
                        $ar["NAME"]
                    );
                }
            }
            //}
        }

        function SectionAddHandler($arFields)
        {
            //if($GLOBALS["IBLOCK_ID"] == $IBLOCK_ID)
            //{
            $oSection = new CIBlockSection();
            $oSection->Update($arFields["ID"], array("CODE" => "recalculate"));
            //}
        }

        function SectionUpdateHandler($arFields)
        {
            //if($GLOBALS["IBLOCK_ID"] == $IBLOCK_ID)
            //{
            $arFields["CODE"] = CSubsections::GetCode(
                $arFields["IBLOCK_ID"], 
                $arFields["ID"]
            );
            //}
        }

        function Recalculate($iblock_id)
        {
            if(CModule::IncludeModule("iblock"))
            {
                $oSection = new CIBlockSection();
                if($rs = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $iblock_id), false, array("ID")))
                {
                    while($ar = $rs->Fetch()) 
                    {
                        $oSection->Update($ar['ID'], array("CODE" => "recalculate"));
                    }
                }         

                $oElement = new CIBlockElement();
                if($rs = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $iblock_id), false, false, array("ID")))
                {
                    while($ar = $rs->Fetch()) 
                    {
                        $oElement->Update($ar['ID'], array("CODE" => "recalculate"));
                    }
                }
            }
        }
    }

    AddEventHandler("iblock", "OnAfterIBlockElementAdd", array("CSubsections", "ElementAddHandler"));
    AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", array("CSubsections", "ElementUpdateHandler"));
    AddEventHandler("iblock", "OnAfterIBlockSectionAdd", array("CSubsections", "SectionAddHandler"));
    AddEventHandler("iblock", "OnBeforeIBlockSectionUpdate", array("CSubsections", "SectionUpdateHandler"));

    CSubsections::InitParser();




    function parserAgent(){
        return "parserAgent();";
        CModule::IncludeModule('yandexparser');

        yandexSoap::startAgent();


    }


    // echo $count . ' ' . formatByCount($count, 'день', 'дня', 'дней');
    function formatByCount($count, $form1, $form2, $form3){ 
        $count = abs($count) % 100;
        $lcount = $count % 10;
        if ($count >= 11 && $count <= 19) return($form3);
        if ($lcount >= 2 && $lcount <= 4) return($form2);
        if ($lcount == 1) return($form1);
        return $form3;
    }


    function prent($arr){
        echo "<pre>";
        var_dump($arr);  
        echo "</pre>";
    }


    AddEventHandler('iblock', 'OnIBlockPropertyBuildList', 
        array('YandesPricesProp', 'GetUserTypeDescription'));


    class YandesPricesProp extends CUserTypeString{

        function GetUserTypeDescription(){
            return array(
                "PROPERTY_TYPE"         =>  "S",
                "USER_TYPE"             =>  "YPR",
                "DESCRIPTION"           =>  "Цены ЯндексМаркет",  
                "GetPropertyFieldHtml"  =>  array(__CLASS__ , "GetPropertyFieldHtml"), 
            );
        }  
        function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName){ 
            CModule::IncludeModule('yandexparser');
            $rsData = yandexPrices::GetList(array(),array('ITEM_ID'=>$_REQUEST['ID']));

            $rsData->NavStart(500);
            if($rsData->NavRecordCount){
            ?>
            <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
            <script src="/js/tablesort.js"></script> 
            <link rel="stylesheet" href="/css/tablesort.css" type="text/css"> 
            <script>
                $(document).ready(function() { 
                        $('#tr_PROPERTY_26').appendTo('#catalog_vat_table'); 
                        $("#myTable").tablesorter(); 
                    } 
                ); 
            </script>
            <table id="myTable" class="tablesorter">
                <thead><tr><th> Название ИМ</th>
                        <th>Стоимость</th><th>Стоимость доставки</th><th>Дата добавления</th></tr></thead> 
                <tbody> 
                    <? 
                        while ($data = $rsData->fetch()) {
                        ?><TR>
                            <td><?=$data["SHOP_NAME"]?></td><td><?=$data["PRICE"]?></td>
                            <td><?=$data["DELIVERY"]?></td><td><?=$data["DATE"]?></td></TR>
                        <?
                        }   
                ?></tbody>
            </table>
            <? } else { 
            ?>

            <?
            }
        } 
    }


    AddEventHandler("remains", "OnAfterRemainUpdate", "OnAfterRemainUpdate");
    AddEventHandler("remains", "OnBeforeRemainUpdate", "OnBeforeRemainUpdate");

    function OnBeforeRemainUpdate(){

        define('START', microtime(true));

    }


    function OnAfterRemainUpdate($arr, $errorsArr, $params){  
        $path_parts = pathinfo($arr['FILENAME']);   
        if(count($errorsArr)){ 
            $strError = implode(', ', $errorsArr);
            $remainsLog = new remainsLog();  
            $remainsLog->Add(array('N1'  =>  0,   "N2"  =>  0,  "N3"  =>  0,  
                    'TYPE'=> 'ERROR',
                    'STR' =>  "Не обработан {$path_parts["basename"]}: {$strError}",
                    'TIME'=>  microtime(true) - START  ));                              
        } else {  
            $tm0 = count($arr['ITEMS']);
            $tm1 = count($arr['ITEMS']) - $params['WITHOUT_MATCH'];
            if($tm0 > 0){ 
                $remainsLog = new remainsLog(); 
                $remainsLog->Add(array('N1'  =>  $tm0, 
                        "N2"  =>  $params['WITHOUT_MATCH'],
                        "N3"  =>  $tm1,  
                        'STR' =>  "Обработан {$path_parts["basename"]}",
                        'TIME'=>  microtime(true) - START  )); 
            }
        }
    }

    function remainUpdate() { 
        CModule::IncludeModule('remains');
        $remainUpdater = new remainUpdater();
        $remainUpdater->scanDir(); 
    }



    AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('RemainsProp', 'GetUserTypeDescription'));

    class RemainsProp extends CUserTypeString{

        function GetUserTypeDescription(){
            return array(
                "PROPERTY_TYPE"         =>  "S",
                "USER_TYPE"             =>  "REMAINS",
                "DESCRIPTION"           =>  "Наличие",  
                "GetPropertyFieldHtml"  =>  array(__CLASS__ , "GetPropertyFieldHtml"), 
            );
        }  

        function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName){ 
            CModule::IncludeModule('remains'); 
            CModule::IncludeModule('iblock');
            $remains = new remainsHelper();
            $matching = new matching();
            $av = new availability();
            $rsData = $av->GetList(array('MATCHING_ID'=>'ASC'), array('ITEM_ID'=>$_REQUEST['ID']));
            $rsData->NavStart(500); 
            if($rsData->NavRecordCount){
            ?>
            <table style="font-size: 13px;"><tr><td><b>Склад</b></td>
                    <td><b>Наличие</b></td><td><b>Поставщик</b></td><td><b>Дата обновления</b></td> </td></TR>
                <?
                    $last_matchingId = false;
                    while ($data = $rsData->fetch()) {

                        if( $data['MATCHING_ID'] != $last_matchingId){
                            $last_matchingId = $data['MATCHING_ID']
                        ?>
                        <tr> 
                            <td colspan="4" style="padding-top: 11px;">
                                <i><?
                                        $r = $matching->GetByID($data['MATCHING_ID'])->Fetch();
                                        if($r)
                                        echo $r['NAME'] ;?></i>
                            </td>

                        </tr>

                        <?
                        }

                    ?>

                    <TR>
                        <td><?  
                                $res = CIBlockElement::GetByID($data["STORE_ID"]);
                                if($ar_res = $res->GetNext())
                                    echo $ar_res['NAME'];
                        ?></td><td><input type="text" name="avail[<?=$data['ID'];?>]" value="<?=$data["AVIABLE"]?>"></td>
                        <td><?                 
                                $res = CIBlockElement::GetByID($data["SUPPLIER_ID"]);
                                if($ar_res = $res->GetNext())
                                    echo $ar_res['NAME'];
                        ?></td>
                        <td><?=$data["DATE"]?></td> </td></TR>
                    <?
                    }
                ?>
            </table>
            <?  } else {echo "-";}
        } 
    }


    AddEventHandler("iblock", "OnAfterIBlockElementUpdate", array("SetHiddenProperty", "OnAfterIBlockElementUpdateHandler"));
    AddEventHandler("iblock", "OnAfterIBlockElementAdd", array("SetHiddenProperty", "OnAfterIBlockElementUpdateHandler"));

    class SetHiddenProperty {
        function OnAfterIBlockElementUpdateHandler(&$arFields) {
            //file_put_contents($_SERVER["DOCUMENT_ROOT"].'/file.txt',var_export($arFields,true)); 
            //$arFields["PRICE"] = 2000;

            foreach ($arFields["PROPERTY_VALUES"][19] as $value) {
                if(!empty($value["VALUE"])):
                //file_put_contents($_SERVER["DOCUMENT_ROOT"].'/file2.txt',var_export($value["VALUE"],true), FILE_APPEND);
                $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "CATALOG_GROUP_1");
                $arFilter = Array("IBLOCK_ID"=>1, "ID"=>$value["VALUE"], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
                $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                while($ob = $res->GetNextElement())
                {
                    $arFieldss = $ob->GetFields();
                    $price_all += $arFieldss["CATALOG_PRICE_1"];
                    //file_put_contents($_SERVER["DOCUMENT_ROOT"].'/file3.txt',var_export($arFieldss,true), FILE_APPEND);
                }
                endif;
            }

            $arField = Array(
                "PRODUCT_ID" => $arFields["ID"],
                "CATALOG_GROUP_ID" => 1,
                "PRICE" => $price_all,
                "CURRENCY" => "RUB",
                "QUANTITY_FROM" => 1,
                "QUANTITY_TO" => 10
            );
if (CModule::IncludeModule("catalog")){
            $res = CPrice::GetList(
                array(),
                array(
                    "PRODUCT_ID" => $arFields["ID"],
                    "CATALOG_GROUP_ID" => 1
                )
            );

            if ($arr = $res->Fetch())
            {
                CPrice::Update($arr["ID"], $arField);
            }
            else
            {
                CPrice::Add($arField);
            }

            if ($arFields['IBLOCK_ID'] == 1) {
                $avail = $_POST["avail"];
                if(isset($arFields["ID"]) && is_array($avail)){
                    if(CModule::IncludeModule('remains')){
                        $remain = new remainsHelper();
                        $remain->setHiddenProperty($avail);   


                        $remain->removePastDueDate($arFields["ID"]);
                    }
                } 
            }
		}
        }



        //if ($arFields['IBLOCK_ID'] == 5) {

        //}


    }


    FUNCTION get_script_url() 
    {
        $script_url = NULL;

        IF (!EMPTY($_SERVER['SCRIPT_URL']))   
            $script_url = $_SERVER['SCRIPT_URL'];

        ELSEIF (!EMPTY($_SERVER['REDIRECT_URL'])) 
            $script_url = $_SERVER['REDIRECT_URL'];

        ELSEIF (!EMPTY($_SERVER['REQUEST_URI'])) {
            $p = PARSE_URL($_SERVER['REQUEST_URI']);
            $script_url = $p['path'];
        }

        $_SERVER['SCRIPT_URL'] = $script_url;

        return $script_url;

    } 

    get_script_url();
    function cutString($string, $maxlen) {
        $len = (mb_strlen($string) > $maxlen)
        ? mb_strripos(mb_substr($string, 0, $maxlen), ' ')
        : $maxlen
        ;
        $cutStr = mb_substr($string, 0, $len);
        return (mb_strlen($string) > $maxlen)
        ? '' . $cutStr . ' ...'
        : '' . $cutStr . ''
        ;
}