<?
function addUserPropIfNotExists($ENTITY_ID, $UF_CODE, $YML_ID, $NAME, $EN_NAME){
    $obUF = CUserTypeEntity::GetList(array(), array('ENTITY_ID'=>$ENTITY_ID, 'FIELD_NAME'=>$UF_CODE));
    if(!$arUF = $obUF->GetNext()){
        /**
         * ���������� ����������������� ��������
         */
        $oUserTypeEntity    = new CUserTypeEntity();

        $aUserFields    = array(
            'ENTITY_ID'         => $ENTITY_ID, //* ��� ������ ������ ��������� - IBLOCK_{IBLOCK_ID}_SECTION
            'FIELD_NAME'        => $UF_CODE, /* ��� ����. ������ ������ ���������� � UF_ */
            'USER_TYPE_ID'      => 'string',
            /*
            * XML_ID ����������������� ��������.
            * ������������ ��� �������� � �������� �������� ����
            */
            'XML_ID'            => $YML_ID, //'YML_ID_PARENT',
            'SORT'              => 500,
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'N', /* ������������ ��� ��� �������� */
            /*
            * ���������� � ������� ������. ��������� ��������:
            * �� ���������� = N, ������ ���������� = I,
            * ����� �� ����� = E, ����� �� ��������� = S
            */
            'SHOW_FILTER'       => 'N',
            /*
            * �� ���������� � ������. ���� �������� �����-���� ��������,
            * �� ����� ���������, ��� ���� ��������� (����������� ������������� �������).
            */
            'SHOW_IN_LIST'      => '',
            /*
            * �� ��������� �������������� �������������.
            * ���� �������� �����-���� ��������, �� ����� ���������,
            * ��� ���� ��������� (����������� ������������� �������).
            */
            'EDIT_IN_LIST'      => '',
            'IS_SEARCHABLE'     => 'N', /* �������� ���� ��������� � ������ */
            /*
            * �������������� ��������� ���� (������� �� ����).
            * � ����� ������ ��� ���� string
            */
            'SETTINGS'          => array(
                /* �������� �� ��������� */
                'DEFAULT_VALUE' => '',
                /* ������ ���� ����� ��� ����������� */
                'SIZE'          => '20',
                /* ���������� ������� ���� ����� */
                'ROWS'          => '1',
                /* ����������� ����� ������ (0 - �� ���������) */
                'MIN_LENGTH'    => '0',
                /* ������������ ����� ������ (0 - �� ���������) */
                'MAX_LENGTH'    => '0',
                /* ���������� ��������� ��� �������� */
                'REGEXP'        => '',
            ),
            /* ������� � ����� �������������� */
            'EDIT_FORM_LABEL'   => array(
                'ru'    => $NAME,
                'en'    => $EN_NAME,
            ),
            /* ��������� � ������ */
            'LIST_COLUMN_LABEL' => array(
                'ru'    => $NAME,
                'en'    => $EN_NAME,
            ),
            /* ������� ������� � ������ */
            'LIST_FILTER_LABEL' => array(
                'ru'    => $NAME,
                'en'    => $EN_NAME,
            ),
            /* ��������� �� ������ (�� ������������) */
            'ERROR_MESSAGE'     => array(
                'ru'    => '������ ��� ���������� '.$NAME,
                'en'    => 'An error in completing '.$EN_NAME,
            ),
            /* ������ */
            'HELP_MESSAGE'      => array(
                'ru'    => '',
                'en'    => '',
            ),
        );
        $iUserFieldId   = $oUserTypeEntity->Add( $aUserFields ); // int
    }
}

function setListValueAndCreatePropList($IBLOCK_ID, $NAME, $CODE, $VALUE){
    $ibp = new CIBlockProperty;
    $obP = CIBlockProperty::GetByID($CODE, $IBLOCK_ID);
    if($arP = $obP->GetNext()){
        $PROPERTY_ID = $arP['ID'];

        //���� �������� ������������ ��� ������, � ��� �� ������, �� �������� ���
        if($arP['PROPERTY_TYPE']!='L'){
            foreach($arP as $key=>$value){
                if(empty($value) || strpos($key, '~')!==false){
                    unset($arP[$key]);
                }
            }

            $arP["PROPERTY_TYPE"] = 'L';
            $ibp->Update($PROPERTY_ID, $arP);
        }
    }else{
        $arFields = Array(
            "NAME" => $NAME,
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => $CODE,
            "PROPERTY_TYPE" => "L",
            "IBLOCK_ID" => $IBLOCK_ID
        );

        $PROPERTY_ID = $ibp->Add($arFields);
    }

    $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array('PROPERTY_ID'=>$PROPERTY_ID, "VALUE"=>$VALUE));
    $enum_fields = $property_enums->GetNext();
    if($enum_fields){
        $enum_fields_id = $enum_fields['ID'];
    }else{
        //������� ����� �������� �������� ���� ������
        $ibpenum = new CIBlockPropertyEnum;
        $enum_fields_id = $ibpenum->Add(Array('PROPERTY_ID'=>$PROPERTY_ID, 'VALUE'=>$VALUE));
    }

    $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array('PROPERTY_ID'=>$PROPERTY_ID, "VALUE"=>$VALUE));
    $enum_fields = $property_enums->GetNext();
    if($enum_fields){
        $enum_fields_id = $enum_fields['ID'];
    }

    return $enum_fields_id;

}

function CreatePropStringIfNotExist($IBLOCK_ID, $NAME, $CODE){
    $ibp = new CIBlockProperty;
    $obP = CIBlockProperty::GetByID($CODE, $IBLOCK_ID);
    if($arP = $obP->GetNext()){
        $PROPERTY_ID = $arP['ID'];

        //���� �������� ������������ ��� ������, � ��� �� ������, �� �������� ���
        if($arP['PROPERTY_TYPE']!='S' || $arP['PROPERTY_TYPE']!='N'){
            foreach($arP as $key=>$value){
                if(empty($value) || strpos($key, '~')!==false){
                    unset($arP[$key]);
                }
            }

            $arP["PROPERTY_TYPE"] = 'S';
            $ibp->Update($PROPERTY_ID, $arP);
        }
    }else{
        $arFields = Array(
            "NAME" => $NAME,
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => $CODE,
            "PROPERTY_TYPE" => "S",
            "IBLOCK_ID" => $IBLOCK_ID
        );

        $PROPERTY_ID = $ibp->Add($arFields);
    }


    return $PROPERTY_ID;

}

function countOffersInYMLFile($URL_FILE){
    $fp = fopen($URL_FILE, "r");
    $count = 0;
    while(!feof($fp)){
        $str = fgets($fp);//������ ��������� ����
        $count += substr_count($str, '<offer ');
    }

    return $count;
}

function searchAllPropsInYMLFile($URL_FILE, $step_get_props, $charset_site, $charset_file, $redirect_on_off=true){//��������� �������
    $redirect_start_time = time();
    $redirect_time_step = 10;

    $arAllPropsTmp = '';
    $arAllPropsINI = array();

    if($step_get_props > 1){
        $arAllPropsTmp = file_get_contents($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/yakus.yml/settings/all_props.ini");
    }

    if($arAllPropsTmp != ''){
        $arAllPropsTmp = explode("\n", $arAllPropsTmp);
        foreach($arAllPropsTmp as $prop){
            if(empty($prop))continue;
            $prop = explode(' = ', $prop);
            $arAllPropsINI[$prop[0]] = $prop[1];
        }
    }

    if(strpos($URL_FILE, 'http://')===false){//���� ������� �� ������ �� ��������� ����
        $URL_FILE = $_SERVER['DOCUMENT_ROOT'].$URL_FILE;
    }else{//���� ������� ������ �� ��������� ����, �� �������� ������ ����, ��������� �� ���� ������ � ����� �������� � ���
        if($_SESSION['LAST_POSITION_PROP']==0){
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
        }else{
            $URL_FILE = $_SERVER['DOCUMENT_ROOT'].'/upload/yakus_yml_tmp.xml';
        }
    }

    $obXML = new CDataXML;
    $fp = fopen($URL_FILE, "r");
    fseek($fp, intval($_SESSION['LAST_POSITION_PROP']-1));

    $data = '';

    $arAllPropsNEW = array();
    while(!feof($fp)){
        $simvol = fgetc($fp);
        $data .= $simvol;
        $_SESSION['LAST_POSITION_PROP']++;

        if(strpos($data, '<param ') !== false){//��������� ������ ���� ������
            $data = '<param '; //����� �� ������ �������� ������� ����� �� ������ ����.
            while (!feof ($fp) and $fp)
            {
                $simvol = fgetc($fp);
                $data .= $simvol;
                $_SESSION['LAST_POSITION_PROP']++;

                if(strpos($data, "</param>") > 0){//��������� ���� ���� ��������
                    if($charset_site != $charset_file){
                        $data = iconv($charset_file, $charset_site, $data);
                    }

                    $obXML->LoadString($data);
                    $arXML = $obXML->GetArray();

                    $prop_name = $arXML['param']['@']['name'];


                    if(!empty($prop_name) && !in_array($prop_name, $arAllPropsINI)){
                        $arAllPropsNEW[$prop_name] = $prop_name;
                    }

                    $data = '';
                    break;
                }
            }
        }

        $time_step = time()-$redirect_start_time;
        if($redirect_on_off == true && $time_step > $redirect_time_step){
            $redirect = true;
            break;
        }

    }

    $ini_file = fopen ($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/yakus.yml/settings/all_props.ini", "w");

    $str_ini_file = '';


    $arAllPropsINI = array_merge($arAllPropsINI, $arAllPropsNEW);

    if(!empty($arAllPropsINI)){

        foreach($arAllPropsINI as $ini_key=>$ini_value){
            $str_ini_file .= $ini_key." = ".$ini_value."\n";
        }
    }

    fwrite($ini_file, $str_ini_file);
    fclose($ini_file);

    if($redirect){
        return round(intval($_SESSION['LAST_POSITION_PROP'])*100/intval(filesize($URL_FILE)),2);
    }else{
        return $arAllPropsINI;
    }

}

?>