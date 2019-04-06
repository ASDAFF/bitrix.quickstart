<?
function addUserPropIfNotExists($ENTITY_ID, $UF_CODE, $YML_ID, $NAME, $EN_NAME){
    $obUF = CUserTypeEntity::GetList(array(), array('ENTITY_ID'=>$ENTITY_ID, 'FIELD_NAME'=>$UF_CODE));
    if(!$arUF = $obUF->GetNext()){
        /**
         * Добавление пользовательского свойства
         */
        $oUserTypeEntity    = new CUserTypeEntity();

        $aUserFields    = array(
            'ENTITY_ID'         => $ENTITY_ID, //* Для секция формат следующий - IBLOCK_{IBLOCK_ID}_SECTION
            'FIELD_NAME'        => $UF_CODE, /* Код поля. Всегда должно начинаться с UF_ */
            'USER_TYPE_ID'      => 'string',
            /*
            * XML_ID пользовательского свойства.
            * Используется при выгрузке в качестве названия поля
            */
            'XML_ID'            => $YML_ID, //'YML_ID_PARENT',
            'SORT'              => 500,
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'N', /* Обязательное или нет свойство */
            /*
            * Показывать в фильтре списка. Возможные значения:
            * не показывать = N, точное совпадение = I,
            * поиск по маске = E, поиск по подстроке = S
            */
            'SHOW_FILTER'       => 'N',
            /*
            * Не показывать в списке. Если передать какое-либо значение,
            * то будет считаться, что флаг выставлен (недоработка разработчиков битрикс).
            */
            'SHOW_IN_LIST'      => '',
            /*
            * Не разрешать редактирование пользователем.
            * Если передать какое-либо значение, то будет считаться,
            * что флаг выставлен (недоработка разработчиков битрикс).
            */
            'EDIT_IN_LIST'      => '',
            'IS_SEARCHABLE'     => 'N', /* Значения поля участвуют в поиске */
            /*
            * Дополнительные настройки поля (зависят от типа).
            * В нашем случае для типа string
            */
            'SETTINGS'          => array(
                /* Значение по умолчанию */
                'DEFAULT_VALUE' => '',
                /* Размер поля ввода для отображения */
                'SIZE'          => '20',
                /* Количество строчек поля ввода */
                'ROWS'          => '1',
                /* Минимальная длина строки (0 - не проверять) */
                'MIN_LENGTH'    => '0',
                /* Максимальная длина строки (0 - не проверять) */
                'MAX_LENGTH'    => '0',
                /* Регулярное выражение для проверки */
                'REGEXP'        => '',
            ),
            /* Подпись в форме редактирования */
            'EDIT_FORM_LABEL'   => array(
                'ru'    => $NAME,
                'en'    => $EN_NAME,
            ),
            /* Заголовок в списке */
            'LIST_COLUMN_LABEL' => array(
                'ru'    => $NAME,
                'en'    => $EN_NAME,
            ),
            /* Подпись фильтра в списке */
            'LIST_FILTER_LABEL' => array(
                'ru'    => $NAME,
                'en'    => $EN_NAME,
            ),
            /* Сообщение об ошибке (не обязательное) */
            'ERROR_MESSAGE'     => array(
                'ru'    => 'Ошибка при заполнении '.$NAME,
                'en'    => 'An error in completing '.$EN_NAME,
            ),
            /* Помощь */
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

        //если свойство обрабатываем как список, а оно не список, то изменяем тип
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
        //создаем новое значение свойства типа список
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

        //если свойство обрабатываем как строка, а оно не строка, то изменяем тип
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
        $str = fgets($fp);//читаем построчно файл
        $count += substr_count($str, '<offer ');
    }

    return $count;
}

function searchAllPropsInYMLFile($URL_FILE, $step_get_props, $charset_site, $charset_file, $redirect_on_off=true){//ПОШАГОВАЯ ФУНКЦИЯ
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

    if(strpos($URL_FILE, 'http://')===false){//если указана не ссылка на сторонний сайт
        $URL_FILE = $_SERVER['DOCUMENT_ROOT'].$URL_FILE;
    }else{//если указана ссылка на сторонний сайт, то забираем оттуда файл, сохраняем на этот сервер и далее работаем с ним
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

        if(strpos($data, '<param ') !== false){//встретили начало узла товара
            $data = '<param '; //чтобы на всякий пожарный удалить мусор до начала узла.
            while (!feof ($fp) and $fp)
            {
                $simvol = fgetc($fp);
                $data .= $simvol;
                $_SESSION['LAST_POSITION_PROP']++;

                if(strpos($data, "</param>") > 0){//полностью взят узел свойства
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