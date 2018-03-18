<?
//error_reporting(E_WARNING);
global $APPLICATION, $DB, $USER, $CACHE_MANAGER;

$MODULE_ID = "yakus.yml";

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$APPLICATION->SetAdditionalCSS('/bitrix/themes/.default/'.$MODULE_ID.'.css');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$MODULE_ID."/include.php");

include($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$MODULE_ID.'/lang/ru/yakus.php');

include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/yakus.yml/classes/general/functions.php');

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

$APPLICATION->SetTitle(GetMessage('YAKUS_IMPORT_NAME'));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>
    <script src="/bitrix/tools/<?=$MODULE_ID?>/js/jquery-2.1.1.min.js"></script>

<?
$redirect_on_off = true;
$redirect = false;
$redirect_time_step = 10;
$redirect_start_time = time();

//��������� ������� ��������� � ini-����
if(isset($_REQUEST['SAVE']) || (isset($_REQUEST['GET_PROPS'])) || (isset($_REQUEST['step']) && isset($_REQUEST['IMPORT']))){
    $arSettings = array();

    if($SECTION_RESTRUCTURE_FROM_YML!='Y')$SECTION_RESTRUCTURE_FROM_YML = 'N';
    if($ELEMENT_RESTRUCTURE_FROM_YML!='Y')$ELEMENT_RESTRUCTURE_FROM_YML = 'N';
    if($UPDATE_PICTURE!='Y')$UPDATE_PICTURE = 'N';
    if($UPDATE_NAME!='Y')$UPDATE_NAME = 'N';
    if($UPDATE_MODE!='Y')$UPDATE_MODE = 'N';
    if($FILE_UTF_8!='Y')$FILE_UTF_8 = 'N';
    if(empty($AFTER_IMPORT_ELEMENT) || $AFTER_IMPORT_ELEMENT == '')$AFTER_IMPORT_ELEMENT = 'Q0';

    $arSettings['IBLOCK_ID'] = $IBLOCK_ID; //���� �����������
    $arSettings['NEW_IN_SECTION'] = $NEW_IN_SECTION; //���� �����������
    $arSettings['SECTION_RESTRUCTURE_FROM_YML'] = $SECTION_RESTRUCTURE_FROM_YML; //���� ������ � ����� UR_YML_ID ��� ����, �� ����������� ��� � ������������ �� ���������� �� YML
    $arSettings['ELEMENT_RESTRUCTURE_FROM_YML'] = $ELEMENT_RESTRUCTURE_FROM_YML; //���� ������� � ����� XML_ID ��� ����, �� ����������� ��� � ������������ �� ���������� �� YML
    $arSettings['UPDATE_PICTURE'] = $UPDATE_PICTURE; //������� �������� ��������, �������� ���������� �� YML
    $arSettings['UPDATE_NAME'] = $UPDATE_NAME; //������� �������� ��������, �������� ��������� �� YML

    $arSettings['STRING_PROPS'] = implode('|', $STRING_PROPS);
    $arSettings['PROP_SINHR'] = $PROP_SINHR;
    $arSettings['LIST_PROPS'] = implode('|', $LIST_PROPS);
    $arSettings['PROP_QUANTITY'] = $PROP_QUANTITY;
    $arSettings['UPDATE_MODE'] = $UPDATE_MODE;
    $arSettings['FILE_UTF_8'] = $FILE_UTF_8;
    $arSettings['AFTER_IMPORT_ELEMENT'] = $AFTER_IMPORT_ELEMENT;
    $arSettings['AFTER_IMPORT_ELEMENT_DIACTIVATED'] = $AFTER_IMPORT_ELEMENT_DIACTIVATED;

    $arSettings['URL_FILE'] = $URL_FILE;

    CheckDirPath($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$MODULE_ID."/settings/");
    $ini_file = fopen ($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$MODULE_ID."/settings/profile.ini", "w");
    $str_ini_file = '';
    foreach($arSettings as $ini_key=>$ini_value){
        $str_ini_file .= $ini_key." = ".$ini_value."\n";
    }
    fwrite($ini_file, $str_ini_file);
    fclose($ini_file);
}



//����� ��������� �� ini �����
//�� ����� ������ �� �������� parse_ini_file, ������� ����� ���� ������ ini �����
$arSettingsTmp = file_get_contents($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$MODULE_ID."/settings/profile.ini");
$arSettingsTmp = explode("\n", $arSettingsTmp);
$arSettings = array();
foreach($arSettingsTmp as $setting){
    if(empty($setting))continue;
    $setting = explode(' = ', $setting);
    $arSettings[$setting[0]] = $setting[1];
}

$IBLOCK_ID = $arSettings['IBLOCK_ID']; //���� �����������
$NEW_IN_SECTION = $arSettings['NEW_IN_SECTION'];
$SECTION_RESTRUCTURE_FROM_YML = $arSettings['SECTION_RESTRUCTURE_FROM_YML']; //���� ������ � ����� UR_YML_ID ��� ����, �� ����������� ��� � ������������ �� ���������� �� YML
$ELEMENT_RESTRUCTURE_FROM_YML = $arSettings['ELEMENT_RESTRUCTURE_FROM_YML']; //���� ������� � ����� XML_ID ��� ����, �� ����������� ��� � ������������ �� ���������� �� YML
$UPDATE_PICTURE = $arSettings['UPDATE_PICTURE']; //������� �������� ��������, �������� ���������� �� YML
$UPDATE_NAME = $arSettings['UPDATE_NAME']; //������� �������� ��������, �������� ��������� �� YML
$UPDATE_MODE = $arSettings['UPDATE_MODE'];
$FILE_UTF_8 = $arSettings['FILE_UTF_8'];
$AFTER_IMPORT_ELEMENT = $arSettings['AFTER_IMPORT_ELEMENT'];
$AFTER_IMPORT_ELEMENT_DIACTIVATED = $arSettings['AFTER_IMPORT_ELEMENT_DIACTIVATED'];
$STRING_PROPS = explode('|', $arSettings['STRING_PROPS']);
$PROP_SINHR = $arSettings['PROP_SINHR'];
$LIST_PROPS = explode('|', $arSettings['LIST_PROPS']);
$PROP_QUANTITY = $arSettings['PROP_QUANTITY'];


$URL_FILE = $arSettings['URL_FILE']; // "/svetlomarket.xml" ���� "http://svetlomarket.ru/svetlomarket.xml"

if(defined('BX_UTF')){
    $charset_site = 'utf8';
}else{
    $charset_site = 'cp1251';
}

if($FILE_UTF_8 == 'Y'){
    $charset_file = 'utf8';
}else{
    $charset_file = 'cp1251';
}



if(isset($_REQUEST['GET_PROPS'])){
    $_REQUEST['STEP_GET_PROPS']++;

    $arAllPropsINI = searchAllPropsInYMLFile($URL_FILE, intval($_REQUEST['STEP_GET_PROPS']), $charset_site, $charset_file, true);
    if(!is_array($arAllPropsINI)){
        ?>
        <script>
            $(function(){
                setTimeout("$('.btn-get-props').click();", 1000);
            })
            ShowWaitWindow();
        </script>
        <?
        $search_props_propgerss = round($arAllPropsINI)."%";
        if($search_props_propgerss=="0%")$search_props_propgerss = GetMessage('YAKUS_FILE_LOADED_GO_PROCESS...');//������ ��� ���� � ����� ������� ���� 0%, ������ ���� ������ ���, ��� �������� �� ������� ����� �� ���������� �������.
    }else{
        unset($_SESSION['LAST_POSITION_PROP']);
        $_REQUEST['STEP_GET_PROPS'] = 0;
    }
}else{


    $arAllPropsINI = array();
    $arAllPropsTmp = file_get_contents($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/yakus.yml/settings/all_props.ini");
    $arAllPropsTmp = explode("\n", $arAllPropsTmp);
    $arAllPropsINI = array();
    foreach($arAllPropsTmp as $prop){
        if(empty($prop))continue;
        $prop = explode(' = ', $prop);
        $arAllPropsINI[$prop[0]] = $prop[1];
    }
}



if(isset($_REQUEST['step']) && isset($_REQUEST['IMPORT'])){
    if(intval($_SESSION['LAST_POSITION'])==0){
        $_SESSION['TIME_START'] = time();
        $_SESSION['TMP_ID'] = rand(10000, 1000000000);
    }

    $obXML = new CDataXML;
    $el = new CIBlockElement;
    $bs = new CIBlockSection;
    $ibp = new CIBlockProperty;

    $_SESSION['STEP']++;

    if(strpos($URL_FILE, 'http://')===false){//���� ������� �� ������ �� ��������� ����
        $URL_FILE = $_SERVER['DOCUMENT_ROOT'].$arSettings['URL_FILE'];
    }else{//���� ������� ������ �� ��������� ����, �� �������� ������ ����, ��������� �� ���� ������ � ����� �������� � ���
        if(intval($_SESSION['LAST_POSITION'])==0){

            $sourceFileName=$URL_FILE;
            $origFileName=$_SERVER['DOCUMENT_ROOT'].'/upload/yakus_yml_tmp.xml';

            $fp = fopen($sourceFileName, "rb");
            $fd = fopen($origFileName, "w");
            if ($fp && $fd) {
                while (!feof($fp)) {
                    $st = fread($fp, 4096);
                    fwrite($fd, $st);
                }
            }
            fclose($fp);
            fclose($fd);
        }
        $URL_FILE = $_SERVER['DOCUMENT_ROOT'].'/upload/yakus_yml_tmp.xml';
    }

    if(intval($_SESSION['LAST_POSITION'])==0){
        $_SESSION['COUNT_OFFERS_IN_YML_FILE'] = countOffersInYMLFile($URL_FILE);
    }

    // ��������� ����
    $fp = fopen($URL_FILE, "r");
    //���� ��� �� ������, �� ������� ��������� ���� ��� ���������� ���������� ���
    fseek($fp, intval($_SESSION['LAST_POSITION']-1));

    $data="";  // ���� �������� ������� ������ �� ����� � ���������� � ��������� xml
    $arTranslitParams = array("replace_space"=>"-","replace_other"=>"-");
    // ���� ���� �� ������ ����� �����
    $count = 0;

    //������� ���������������� ����, � ������� ����� ������� id � parentId ��������
    if(intval($_SESSION['LAST_POSITION'])==0){
        addUserPropIfNotExists('IBLOCK_'.$IBLOCK_ID.'_SECTION', 'UF_YML_ID', 'YML_ID', GetMessage('YAKUS_ID_CATEGORIES_IN_YML_FILE'), "Section ID in the YML file");
        addUserPropIfNotExists('IBLOCK_'.$IBLOCK_ID.'_SECTION', 'UF_YML_PARENT_ID', 'YML_PARENT_ID', GetMessage('YAKUS_ID_PARENT_CATEGORIES_IN_YML_FILE'), "ID of the parent section in the YML file");
    }


    if($msg_type != 'ERROR'){

        while (!feof ($fp) and $fp && $_SESSION['STEP_NAME'] != 'NULLED_ELEMENTS')
        {
            $simvol = fgetc($fp); // ������ ���� ������ �� �����
            $count++;
            $data .= $simvol; // ��������� ���� ������ � ������ ��� ��������
            $_SESSION['LAST_POSITION']++;


            if($_SESSION['STEP_NAME'] != 'IMPORT_CATEGORIES' && strpos($data, "<categories>") > 0){ //���� ������� ��� �� "������ ���������" � �������� ����������� ���� ������ ��������.
                $_SESSION['STEP_NAME'] = 'IMPORT_CATEGORIES';
                $data = "";
                continue;
            }

            if($_SESSION['STEP_NAME'] != 'IMPORT_OFFERS' && strpos($data, "<offers>") > 0){ //���� ������� ��� �� "������ ���������" � �������� ����������� ���� ������ ��������.
                $_SESSION['STEP_NAME'] = 'IMPORT_OFFERS';
                $data = "";
                continue;
            }

            if(strpos($data, '<currency ') !== false){//��������� ������ �������� �������
                $data = '<currency '; //����� �� ������ �������� ������� ����� �� ������ ����.
                while (!feof ($fp) and $fp)
                {
                    $simvol = fgetc($fp);
                    $data .= $simvol;
                    $_SESSION['LAST_POSITION']++;

                    if(strpos($data, "/>") > 0){//��������� ���� ���� ������
                        if($charset_site != $charset_file){
                            $data = iconv($charset_file, $charset_site, $data);
                        }

                        $obXML->LoadString($data);
                        $arXML = $obXML->GetArray();

                        $currency = $arXML['currency']['@']['id'];
                        if(!$arCurrency = CCurrency::GetByID($currency)){
                            CCurrency::Add(array(
                                "CURRENCY" => $currency,
                                "AMOUNT" => $arXML['currency']['@']['rate'],
                                "AMOUNT_CNT" => 1,
                                "SORT" => 111
                            ));
                        }

                        $data = "";
                        break;
                    }
                }
            }elseif(strpos($data, '<category ') !== false){//��������� ������ �������� �������
                $data = '<category '; //����� �� ������ �������� ������� ����� �� ������ ����.
                while (!feof ($fp) and $fp)
                {
                    $simvol = fgetc($fp);
                    $data .= $simvol;
                    $_SESSION['LAST_POSITION']++;

                    if(strpos($data, "</category>") > 0){//��������� ���� ���� �������
                        if($charset_site != $charset_file){
                            $data = iconv($charset_file, $charset_site, $data);
                        }

                        $obXML->LoadString($data);
                        $arXML = $obXML->GetArray();
                        $counter_categories++;
                        $_SESSION['COUNTER_CATEGORIES']++;

                        $ID = 0;
                        $PARENT_ID = '';
                        //���� ������ � ����� UF_XML_ID ��� ����, �� ����� ���������
                        $obS = CIBlockSection::GetList(array(), array("IBLOCK_ID"=>$IBLOCK_ID, 'UF_YML_ID'=>$arXML['category']['@']['id']), false, array('ID', 'IBLOCK_SECTION_ID'));
                        if($arS = $obS->GetNext()){
                            $ID = $arS['ID'];
                            $PARENT_ID = $arS['IBLOCK_SECTION_ID'];
                        }


                        $code = Cutil::translit($arXML['category']['#'], "ru", $arTranslitParams);

                        $arFields = Array(
                            "ACTIVE" => 'Y',
                            "IBLOCK_ID" => $IBLOCK_ID,
                            "NAME" => $arXML['category']['#'],
                            "SORT" => 111,
                            "CODE" => $code,
                            "UF_YML_ID" => $arXML['category']['@']['id'],
                            "UF_YML_PARENT_ID" => $arXML['category']['@']['parentId'],
                            "TMP_ID"=>$_SESSION['TMP_ID'],
                        );

                        if($ID > 0)
                        {
                            unset($arFields['CODE']);
                            //unset($arFields['ACTIVE']);
                            unset($arFields['SORT']);

                            if($SECTION_RESTRUCTURE_FROM_YML == 'Y'){
                                if(!empty($arXML['category']['@']['parentId'])){
                                    $obS = CIBlockSection::GetList(array(), array("IBLOCK_ID"=>$IBLOCK_ID, 'UF_YML_ID'=>$arXML['category']['@']['parentId']), false, array('ID'));
                                    if($arS = $obS->GetNext()){
                                        $PARENT_ID = $arS['ID'];
                                        if($PARENT_ID>0){
                                            $arFields['IBLOCK_SECTION_ID']=$PARENT_ID;
                                        }
                                    }
                                }else{
                                    $arFields['IBLOCK_SECTION_ID']=intval($arSettings['NEW_IN_SECTION']); //��� ������ ��� �������� ����� ��������
                                }
                            }

                            $res = $bs->Update($ID, $arFields);
                        }
                        elseif($UPDATE_MODE == 'N')
                        {
                            if(!empty($arXML['category']['@']['parentId'])){
                                $obS = CIBlockSection::GetList(array(), array("IBLOCK_ID"=>$IBLOCK_ID, 'UF_YML_ID'=>$arXML['category']['@']['parentId']), false, array('ID'));
                                if($arS = $obS->GetNext()){
                                    $PARENT_ID = $arS['ID'];
                                    if($PARENT_ID>0){
                                        $arFields['IBLOCK_SECTION_ID']=$PARENT_ID;
                                    }
                                }
                            }else{
                                $arFields['IBLOCK_SECTION_ID']=intval($arSettings['NEW_IN_SECTION']); //��� ������ ��� �������� ����� ��������
                            }

                            $ID = $bs->Add($arFields);

                            //���� ������ � ����� ����� ��� ���������� �� ��������� ������� � ��� ���
                            if(!$ID && $bs->LAST_ERROR == GetMessage('YAKUS_ERROR_CATEGORY_EXISTS')){ //������ � ����� ���������� ����� ��� ����������.<br>
                                while(!$ID){
                                    $arFields['CODE'] = $arFields['CODE'].'-';
                                    $ID = $bs->Add($arFields);
                                }
                            }

                        }

                        $data = "";
                        break;
                    }
                }
            }elseif(strpos($data, '<offer ') !== false){//��������� ������ ���� ������
                $data = '<offer '; //����� �� ������ �������� ������� ����� �� ������ ����.
                while (!feof ($fp) and $fp)
                {
                    $simvol = fgetc($fp);
                    $data .= $simvol;
                    $_SESSION['LAST_POSITION']++;

                    if(strpos($data, "</offer>") > 0){//��������� ���� ���� ������
                        if($charset_site != $charset_file){
                            $data = iconv($charset_file, $charset_site, $data);
                        }

                        $obXML->LoadString($data);
                        $arXML = $obXML->GetArray();
                        $counter_offers++;
                        $_SESSION['COUNTER_OFFERS']++;


                        $PROPS = array();
                        $arCreatedStringProperties = array();
                        $arPROPTranslitParams = array("replace_space"=>"_","replace_other"=>"_");
                        $QUANTITY = 0;
                        foreach($arXML['offer']['#']['param'] as $prop){
                            if($prop['@']['name'] == $PROP_SINHR){
                                $PROP_SINHR_VALUE = $prop['#'];
                            }

                            $code = 'YML_'.strtoupper(Cutil::translit($prop['@']['name'], "ru", $arPROPTranslitParams));

                            if($prop['@']['name'] == $PROP_QUANTITY){
                                $QUANTITY = $prop['#'];
                                $QUANTITY = trim(str_replace(' ', '', $QUANTITY));
                            }

                            if(in_array($prop['@']['name'], $LIST_PROPS)){//���� �������� ������ ���� �������/"��������� ��������" ��� ������
                                //��������� ���� �� ����� ��������, ���� ���, �� ������� � ������������� ��������. ���� �������� ���,�� ������� � ������������� ��������.
                                $prop_val_id = setListValueAndCreatePropList($IBLOCK_ID, $prop['@']['name'], $code, $prop['#']);
                                $PROPS[$code] = $prop_val_id;
                            }elseif(in_array($prop['@']['name'], $STRING_PROPS)){//���� �������� ������ ���� �������/"��������� ��������" ��� ������
                                CreatePropStringIfNotExist($IBLOCK_ID, $prop['@']['name'], $code);
                                $PROPS[$code] =  $prop['#'];
                                $arCreatedStringProperties[] = $code;
                            }
                        }

                        if(in_array('ALL_OTHER', $LIST_PROPS)){//���� � ������ �������, ������� ������ �������������� ��� ��� ������ �������� "ALL_OTHER", ����� ��� �� ��������� ������ ���� ����������
                            foreach($arXML['offer']['#']['param'] as $prop){
                                $code = 'YML_'.strtoupper(Cutil::translit($prop['@']['name'], "ru", $arPROPTranslitParams));

                                if(!in_array($code, $arCreatedStringProperties)){//���� �������� �� ��������� ��� ���������
                                    //��������� ���� �� ����� ��������, ���� ���, �� ������� � ������������� ��������. ���� �������� ���,�� ������� � ������������� ��������.
                                    $prop_val_id = setListValueAndCreatePropList($IBLOCK_ID, $prop['@']['name'], $code, $prop['#']);
                                    $PROPS[$code] = $prop_val_id;
                                }
                            }

                        }





                        $ID = 0;
                        $PARENT_ID_FROM_YML = 0;
                        $code = Cutil::translit($arXML['offer']['#']['name'][0]['#'], "ru", $arTranslitParams);
                        //���� ������� � ����� ������� ����� ��� ����, �� ����� ���������

                        if(strlen($PROP_SINHR)>0){
                            $XML_ID = $PROP_SINHR_VALUE;
                        }else{
                            $XML_ID = $arXML['offer']['@']['id'];
                        }

                        $arFilter = array(
                            "IBLOCK_ID" => $IBLOCK_ID,
                            'XML_ID' => $XML_ID,
                        );

                        $obE = CIBlockElement::GetList(array(), $arFilter, false, array('ID', 'IBLOCK_SECTION_ID'));
                        if($arE = $obE->GetNext()){
                            $ID = $arE['ID'];
                            $CUR_PARENT_ID = $arE['IBLOCK_SECTION_ID'];
                        }else{//vamsvet (���������) ������ �������� � yml ����� id �������. ���������� ��� ������ ������ ���, ���� �� ����. ��� ������ ������ ��������� ��� � �� ����������� ����. ���� �� �������� �� ������.
                            $arFilter = array(
                                "IBLOCK_ID" => $IBLOCK_ID,
                                'CODE' => $code,
                            );
                            $obE = CIBlockElement::GetList(array(), $arFilter, false, array('ID', 'IBLOCK_SECTION_ID'));
                            if($arE = $obE->GetNext()){
                                $ID = $arE['ID'];
                                $CUR_PARENT_ID = $arE['IBLOCK_SECTION_ID'];
                            }
                        }

                        if($arXML['offer']['#']['categoryId'][0]['#'] > 0){
                            $obS = CIBlockSection::GetList(array(), array("IBLOCK_ID"=>$IBLOCK_ID, 'UF_YML_ID'=>$arXML['offer']['#']['categoryId'][0]['#']), false, array('ID'));
                            if($arS = $obS->GetNext()){
                                $PARENT_ID_FROM_YML = $arS['ID'];
                            }
                        }

                        $arLoadProductArray = Array(
                            "MODIFIED_BY"    => $USER->GetID(), // ������� ������� ������� �������������
                            "IBLOCK_ID"      => $IBLOCK_ID,
                            "PROPERTY_VALUES"=> $PROPS,
                            "NAME" => $arXML['offer']['#']['name'][0]['#'],
                            "ACTIVE" => "Y",            // �������
                            "CODE" => $code,
                            "SORT" => 111,
                            //"PREVIEW_TEXT"   => "����� ��� ������ ���������",
                            //"DETAIL_TEXT"    => "����� ��� ���������� ���������",
                            "XML_ID" => $arXML['offer']['@']['id'],
                            "TMP_ID" => $_SESSION['TMP_ID'],
                        );

                        if($ID > 0){
                            if($ELEMENT_RESTRUCTURE_FROM_YML == 'Y'){
                                $arLoadProductArray['IBLOCK_SECTION_ID'] = $PARENT_ID_FROM_YML;
                            }else{
                                $arLoadProductArray['IBLOCK_SECTION_ID'] = $CUR_PARENT_ID;
                            }
                            if($UPDATE_PICTURE == 'Y' || empty($arE['DETAIL_PICTURE'])){
                                /*if(!fopen($arXML['offer']['#']['picture'][0]['#'], 'r')){
                                    $page = file_get_contents('http://vokruglamp.ru/detail/wunderlicht_torsher_wl5245/');
                                    $start = strpos($page, '/upload/'); //������ �������� �� �������� � ������� ��� ������� ����������� ������
                                    $end = strpos($page, '"', $start);

                                    $url_img = 'http://vokruglamp.ru/'.substr($page, $start, $end-$start);
                                    $arLoadProductArray["DETAIL_PICTURE"] = CFile::MakeFileArray($url_img);
                                }else{
                                    $arLoadProductArray["DETAIL_PICTURE"] = CFile::MakeFileArray($arXML['offer']['#']['picture'][0]['#']);
                                }*/
                                if(!fopen($arXML['offer']['#']['image'][0]['#'], 'r')){
                                    //������ ��� ����� ��������.
                                }else{
                                    $arLoadProductArray["DETAIL_PICTURE"] = CFile::MakeFileArray($arXML['offer']['#']['image'][0]['#']);
                                }
                            }

                            if($UPDATE_NAME == 'N'){
                                unset($arLoadProductArray["NAME"]);
                            }
                            unset($arLoadProductArray["SORT"]);
                            unset($arLoadProductArray["CODE"]);
                            unset($arLoadProductArray['PROPERTY_VALUES']);

                            if($AFTER_IMPORT_ELEMENT_DIACTIVATED == 'A' && $QUANTITY>0){//�.�. ������������
                                $arLoadProductArray["ACTIVE"] = 'Y';
                            }elseif($AFTER_IMPORT_ELEMENT_DIACTIVATED == 'N'){//�.�. ������
                                unset($arLoadProductArray["ACTIVE"]);
                            }


                            if($el->Update($ID, $arLoadProductArray)){
                                $PRODUCT_ID = $ID;
                            }else{
                                mp("Update error element: ".$el->LAST_ERROR);
                            }

                            CCatalogProduct::Update($PRODUCT_ID, array('QUANTITY'=>$QUANTITY));
                        }elseif($UPDATE_MODE == 'N'){
                            $arLoadProductArray['IBLOCK_SECTION_ID'] = $PARENT_ID_FROM_YML;
                            $arLoadProductArray["DETAIL_PICTURE"] = CFile::MakeFileArray($arXML['offer']['#']['picture'][0]['#']);
                            if(!$PRODUCT_ID = $el->Add($arLoadProductArray))
                                mp("Add error element: ".$el->LAST_ERROR);

                            CCatalogProduct::Add(array('ID'=>$PRODUCT_ID, 'QUANTITY'=>$QUANTITY));
                        }

                        CPrice::SetBasePrice($PRODUCT_ID, $arXML['offer']['#']['price'][0]['#'], $arXML['offer']['#']['currencyId'][0]['#']);

                        $data = "";
                        break;
                    }
                }

            }


            //��� �����������
            $time_step = time()-$redirect_start_time;
            if($redirect_on_off == true && $time_step > $redirect_time_step){
                $redirect = true;
                break;
            }
        }

        if($redirect == false || $_SESSION['STEP_NAME'] == 'NULLED_ELEMENTS'){//������ ������ ��������
            $_SESSION['STEP_NAME'] = 'NULLED_ELEMENTS';

            //�������� c �������� �������������� � �����
            $obE = CIBlockElement::GetList(array('ID'=>'asc'), array('IBLOCK_ID'=>$IBLOCK_ID, '!TMP_ID'=>$_SESSION['TMP_ID'], '>ID'=>$_SESSION['LAST_ELEMENT_ID']), false, false, array('ID'));
            while($arE = $obE->GetNext()){
                if($AFTER_IMPORT_ELEMENT=='Q0'){
                    CCatalogProduct::Update($arE['ID'], array('QUANTITY'=>0));
                }elseif($AFTER_IMPORT_ELEMENT=='D'){
                    $el->Update($arE['ID'], array('ACTIVE'=>'N'));
                }
                $_SESSION['LAST_ELEMENT_ID'] = $arE['ID'];

                //��� �����������
                $time_step = time()-$redirect_start_time;
                if($redirect_on_off == true && $time_step > $redirect_time_step){
                    $redirect = true;
                    break;
                }
            }
        }


        $all_elements = $_SESSION['ELEMENTS_ALL_FILES'];
        $cur_count = $_SESSION['count'];
        if($redirect==true){
            $msg_type = 'PROGRESS';
            $msg_title = GetMessage("YAKUS_IMPORT_STARTED");
            $msg_details = '';
            $msg_details .= GetMessage("YAKUS_IS_IMPORT").": ".($_SESSION['STEP_NAME']=='IMPORT_CATEGORIES'?GetMessage("YAKUS_CATEGORIES"):GetMessage("YAKUS_PRODUCTS")).'<br/>';
            $msg_details .= GetMessage("YAKUS_CUR_STEP").": ".$_SESSION['STEP'].'<br/>';
            $msg_details .= GetMessage("YAKUS_PROGRESS").": ".(round(intval($_SESSION['COUNTER_OFFERS'])*100/intval($_SESSION['COUNT_OFFERS_IN_YML_FILE'],2))).'%<br/>';
            $msg_details .= GetMessage("YAKUS_PROCESSED_CATEGORIES").": ".(intval($_SESSION['COUNTER_CATEGORIES'])).'<br/>';
            $msg_details .= GetMessage("YAKUS_PROCESSED_PRODUCTS").": ".(intval($_SESSION['COUNTER_OFFERS'])).'<br/>';
            $msg_details .= GetMessage("YAKUS_ALL_TIME").": ".(time()-$_SESSION['TIME_START']).' cek.<br/>';

            $_SESSION['FILES_FOR_IMPORT'] = serialize($_SESSION['FILES_FOR_IMPORT']);
            ?>
            <script>
                $(function(){
                    <?if($_SESSION['STEP_NAME'] == 'NULLED_ELEMENTS'){?>
                    $('.adm-progress-bar-inner-text').html('<span style="font-size: 15px;"><?=GetMessage('YAKUS_100_PROCESS_OF_THE_PRODUCTS')?></span>');
                    <?}?>
                    setTimeout("$('.adm-btn-save').click();", 1000);
                })
                ShowWaitWindow();
            </script>
        <?}else{
            $msg_type = 'OK';
            $msg_title = GetMessage("YAKUS_FINISH_YML_IMPORT_HEAD");
            $msg_details = '';
            $msg_details .= GetMessage("YAKUS_FINISH_YML_IMPORT").'<br/>';
            $msg_details .= GetMessage("YAKUS_ALL_STEP").": ".$_SESSION['STEP'].'<br/>';
            $msg_details .= GetMessage("YAKUS_PROCESSED_CATEGORIES").": ".(intval($_SESSION['COUNTER_CATEGORIES'])).'<br/>';
            $msg_details .= GetMessage("YAKUS_PROCESSED_PRODUCTS").": ".(intval($_SESSION['COUNTER_OFFERS'])).'<br/>';
            $msg_details .= GetMessage("YAKUS_ALL_TIME").": ".(time()-$_SESSION['TIME_START']).' cek.<br/>';

            unset($_SESSION['LAST_POSITION']);
            unset($_SESSION['STEP']);
            unset($_SESSION['STEP_NAME']);
            unset($_SESSION['COUNTER_CATEGORIES']);
            unset($_SESSION['COUNTER_OFFERS']);
            unset($_SESSION['TIME_START']);
            unset($_SESSION['COUNT_OFFERS_IN_YML_FILE']);
            unset($_SESSION['TMP_ID']);
            unset($_SESSION['LAST_ELEMENT_ID']);

            ?>
            <script>
                CloseWaitWindow();
            </script>
        <?
        }
    }else{
        unset($_SESSION['STEP']);
    }

    CAdminMessage::ShowMessage(array(
        "MESSAGE"=>$msg_title,
        "DETAILS"=> (intval($_SESSION['STEP'])>0?GetMessage("YAKUS_DONT_GO_AWAY_FROM_THE_PAGE")."<br/><br/>#PROGRESS_BAR#<br/>":"")
            .$msg_details
    ,
        "HTML"=>true,
        "TYPE"=>$msg_type,
        "PROGRESS_TOTAL" => $_SESSION['COUNT_OFFERS_IN_YML_FILE'],
        "PROGRESS_VALUE" => $_SESSION['COUNTER_OFFERS'],
    ));

}else{
    unset($_SESSION['LAST_POSITION']);
    unset($_SESSION['STEP']);
    unset($_SESSION['STEP_NAME']);
    unset($_SESSION['COUNTER_CATEGORIES']);
    unset($_SESSION['COUNTER_OFFERS']);
    unset($_SESSION['TIME_START']);
    unset($_SESSION['COUNT_OFFERS_IN_YML_FILE']);
    unset($_SESSION['TMP_ID']);
    unset($_SESSION['LAST_ELEMENT_ID']);
}

?>
    <form action="" name="dataload" ENCTYPE="multipart/form-data" method=POST>
        <?
        $arTabs = array(
            array(
                "DIV" => "settings",
                "TAB" => GetMessage("YAKUS_SIMPLE_TAB_NAME"),
                "TITLE" => GetMessage("YAKUS_SIMPLE_TAB_NAME")
            )
        );

        $tabControl = new CAdminTabControl("tabControl", $arTabs);
        $tabControl->Begin();
        $tabControl->BeginNextTab();

        ?>
        <tr>

            <td width="40%"><?= GetMessage("YAKUS_DATA_FILE") ?></td>
            <td width="60%">
                <input type="text" name="URL_FILE" size="40" value="<? echo htmlspecialcharsbx($arSettings['URL_FILE']); ?>">
                <input type="button" value="<?=GetMessage("YAKUS_CHOOSE");?>" onclick="cmlBtnSelectClick();"><?
                CAdminFileDialog::ShowScript(
                    array(
                        "event" => "cmlBtnSelectClick",
                        "arResultDest" => array("FORM_NAME" => "dataload", "FORM_ELEMENT_NAME" => "URL_FILE"),
                        "arPath" => array("PATH" => "/upload/catalog", "SITE" => SITE_ID),
                        "select" => 'F',// F - file only, D - folder only, DF - files & dirs
                        "operation" => 'O',// O - open, S - save
                        "showUploadTab" => true,
                        "showAddToMenuTab" => false,
                        "fileFilter" => 'xml',
                        "allowAllFiles" => true,
                        "SaveConfig" => true
                    )
                );
                ?>
                <?/*
                <input class="adm-btn-save" type="submit" name="SAVE" value="<?= GetMessage("YAKUS_SAVE_FILE") ?>" title="<?= GetMessage("YAKUS_SAVE_FILE") ?>">
                */?>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("YAKUS_FILE_UTF_8");?></td>
            <td width="60%">
                <input type="checkbox" name="FILE_UTF_8" value="Y" <?=$FILE_UTF_8 == 'Y' ? "checked" : "" ?>/>
            </td>
        </tr>
        <tr>
            <td width="40%"><?= GetMessage("YAKUS_INFOBLOCK") ?></td>
            <td width="60%">
                <?
                echo GetIBlockDropDownListEx(
                    $IBLOCK_ID,
                    'IBLOCK_TYPE_ID',
                    'IBLOCK_ID',
                    array('CHECK_PERMISSIONS' => 'Y','MIN_PERMISSION' => 'W'),
                    "",
                    "",
                    'class="adm-detail-iblock-types"',
                    'class="adm-detail-iblock-list"'
                );
                ?>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("YAKUS_NEW_IN_SECTION");?></td>
            <td width="60%">
                <input type="text" name="NEW_IN_SECTION" value="<?=$NEW_IN_SECTION?>" />
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("YAKUS_UPDATE_MODE");?></td>
            <td width="60%">
                <input type="checkbox" name="UPDATE_MODE" value="Y" <?=$UPDATE_MODE == 'Y' ? "checked" : "" ?>/>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("YAKUS_SECTION_RESTRUCTURE_FROM_YML");?></td>
            <td width="60%">
                <input type="checkbox" name="SECTION_RESTRUCTURE_FROM_YML" value="Y" <?=$SECTION_RESTRUCTURE_FROM_YML == 'Y' ? "checked" : "" ?>/>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("YAKUS_ELEMENT_RESTRUCTURE_FROM_YML");?></td>
            <td width="60%">
                <input type="checkbox" name="ELEMENT_RESTRUCTURE_FROM_YML" value="Y" <?=$ELEMENT_RESTRUCTURE_FROM_YML == 'Y' ? "checked" : "" ?>/>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("YAKUS_UPDATE_PICTURE");?></td>
            <td width="60%">
                <input type="checkbox" name="UPDATE_PICTURE" value="Y" <?=$UPDATE_PICTURE == 'Y' ? "checked" : "" ?>/>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("YAKUS_UPDATE_NAME");?></td>
            <td width="60%">
                <input type="checkbox" name="UPDATE_NAME" value="Y" <?=$UPDATE_NAME == 'Y' ? "checked" : "" ?>/>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("YAKUS_AFTER_IMPORT_ELEMENT");?></td>
            <td width="60%">
                <label for="AFTER_IMPORT_ELEMENT_D"><input type="radio" id="AFTER_IMPORT_ELEMENT_D" name="AFTER_IMPORT_ELEMENT" value="D" <?=$AFTER_IMPORT_ELEMENT == 'D' ? "checked" : "" ?>/><?=GetMessage('YAKUS_AFTER_IMPORT_ELEMENT_D')?></label>
                <label for="AFTER_IMPORT_ELEMENT_Q0"><input type="radio" id="AFTER_IMPORT_ELEMENT_Q0" name="AFTER_IMPORT_ELEMENT" value="Q0" <?=$AFTER_IMPORT_ELEMENT != 'D' ? "checked" : "" ?>/><?=GetMessage('YAKUS_AFTER_IMPORT_ELEMENT_Q0')?></label>        </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("YAKUS_AFTER_IMPORT_ELEMENT_DIACTIVATED");?></td>
            <td width="60%">
                <label for="AFTER_IMPORT_ELEMENT_DIACTIVATED_A"><input type="radio" id="AFTER_IMPORT_ELEMENT_DIACTIVATED_A" name="AFTER_IMPORT_ELEMENT_DIACTIVATED" value="A" <?=$AFTER_IMPORT_ELEMENT_DIACTIVATED == 'A' ? "checked" : "" ?>/><?=GetMessage('YAKUS_AFTER_IMPORT_ELEMENT_DIACTIVATED_A')?></label>
                <label for="AFTER_IMPORT_ELEMENT_DIACTIVATED_N"><input type="radio" id="AFTER_IMPORT_ELEMENT_DIACTIVATED_N" name="AFTER_IMPORT_ELEMENT_DIACTIVATED" value="N" <?=$AFTER_IMPORT_ELEMENT_DIACTIVATED != 'A' ? "checked" : "" ?>/><?=GetMessage('YAKUS_AFTER_IMPORT_ELEMENT_DIACTIVATED_N')?></label>        </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input class="adm-btn-save btn-get-props <?if(isset($search_props_propgerss))echo "adm-btn-load"?>" onclick="$(this).addClass('adm-btn-load'); ShowWaitWindow();" type="submit" name="GET_PROPS" value="<?= GetMessage("YAKUS_GET_PROPS")?>" title="<?= GetMessage("YAKUS_GET_PROPS") ?>">
                <?if(isset($search_props_propgerss))echo "<b>".$search_props_propgerss."</b>"?>
                <input type="text" name="STEP_GET_PROPS" value="<?=intval($_REQUEST['STEP_GET_PROPS'])?>" />
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("YAKUS_PROP_SINHR");?></td>
            <td width="60%">
                <select name="PROP_SINHR">
                    <option value=""><?=GetMessage("YAKUS_DEFAULT_PARAMETR_ID");?></option>
                    <?foreach($arAllPropsINI as $prop){?>
                        <option value="<?=$prop?>" <?if($prop == $PROP_SINHR){?>selected="selected"<?}?>><?=$prop?></option>
                    <?}?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("YAKUS_STRING_PROPS");?></td>
            <td width="60%">
                <select multiple name="STRING_PROPS[]" size="10">
                    <?foreach($arAllPropsINI as $prop){?>
                        <option value="<?=$prop?>" <?if(in_array($prop, $STRING_PROPS)){?>selected="selected"<?}?>><?=$prop?></option>
                    <?}?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("YAKUS_LIST_PROPS");?></td>
            <td width="60%">
                <select multiple name="LIST_PROPS[]" size="10">
                    <?foreach($arAllPropsINI as $prop){?>
                        <option value="<?=$prop?>" <?if(in_array($prop, $LIST_PROPS)){?>selected="selected"<?}?>><?=$prop?></option>
                    <?}?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("YAKUS_PROP_QUANTITY");?></td>
            <td width="60%">
                <select name="PROP_QUANTITY">
                    <?foreach($arAllPropsINI as $prop){?>
                        <option value="<?=$prop?>" <?if($PROP_QUANTITY == $prop){?>selected="selected"<?}?>><?=$prop?></option>
                    <?}?>
                </select>
            </td>
        </tr>



        <!-- controls -->
        <? $tabControl->Buttons(); ?>
        <input type="hidden" name="step" value="<?=intval($_SESSION['STEP'])?>">
        <input class="" type="submit" name="SAVE" value="<?= GetMessage("YAKUS_SAVE") ?>" title="<?= GetMessage("YAKUS_SAVE") ?>">
        <input class="adm-btn-save" type="submit" name="IMPORT" onclick="ShowWaitWindow();" value="<?= GetMessage("YAKUS_IMPORT") ?>" title="<?= GetMessage("YAKUS_IMPORT") ?>">
        <?= bitrix_sessid_post(); ?>
        <? $tabControl->End();?>
    </form>



<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>