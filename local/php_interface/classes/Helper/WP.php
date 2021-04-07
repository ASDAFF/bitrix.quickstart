<?php
/**
 * Copyright (c) 7/4/2021 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Helper;

use Webprofy\Bitrix\IBlock\IBlock;
use Webprofy\Bitrix\IBlock\Element;
use Webprofy\Bitrix\IBlock\Section;

use Webprofy\Bitrix\Getter;

class WP{
    static function lastUpdate($iblock, $of = 'element'){
        if($iblock == null && $of == null){
            return file_get_contents(__DIR__.'/last_update_time.txt');
        }

        return self::bit(array(
            'of' => $of,
            'f' => array(
                'IBLOCK_ID' => $iblock
            ),
            'sort' => array(
                'TIMESTAMP_X' => 'desc'
            ),
            'sel' => 'TIMESTAMP_X',
            'one' => 'f.TIMESTAMP_X'
        ));
    }

    static function repeatConsole(){
        echo '<script> setTimeout(function(){__FPHPSubmit()}, 100);  </script>';
    }

    // Проверка, есть ли уменьшенная копия у изображения
    static function hasResized($file, $size){
        if(!is_array($file) && intval($file) > 0){
            $file = CFile::GetFileArray($file);
        }

        if(!is_array($file) || !array_key_exists("FILE_NAME", $file) || strlen($file["FILE_NAME"]) <= 0){
            return false;
        }

        if ($resizeType !== BX_RESIZE_IMAGE_EXACT && $resizeType !== BX_RESIZE_IMAGE_PROPORTIONAL_ALT)
            $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL;

        if(!is_array($size)){
            $size = array();
        }

        if(!array_key_exists("width", $size) || intval($size["width"]) <= 0){
            $size["width"] = 0;
        }

        if (!array_key_exists("height", $size) || intval($size["height"]) <= 0){
            $size["height"] = 0;
        }

        $size["width"] = intval($size["width"]);
        $size["height"] = intval($size["height"]);

        $uploadDirName = COption::GetOptionString("main", "upload_dir", "upload");
        $bFilters = is_array($arFilters) && !empty($arFilters);

        if(
            ($size["width"] <= 0 || $size["width"] >= $file["WIDTH"])
            && ($size["height"] <= 0 || $size["height"] >= $file["HEIGHT"])
        ){
            if($bFilters){
                //Only filters. Leave size unchanged
                $size["width"] = $file["WIDTH"];
                $size["height"] = $file["HEIGHT"];
                $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL;
            }
            else{
                return false;
            }
        }

        $io = CBXVirtualIo::GetInstance();
        $cacheImageFile = "/".$uploadDirName."/resize_cache/".$file["SUBDIR"]."/".$size["width"]."_".$size["height"]."_".$resizeType.(is_array($arFilters)? md5(serialize($arFilters)): "")."/".$file["FILE_NAME"];
        $cacheImageFileCheck = $cacheImageFile;
        if ($file["CONTENT_TYPE"] == "image/bmp")
            $cacheImageFileCheck .= ".jpg";

        static $cache = array();
        $cache_id = $cacheImageFileCheck;
        if(
            isset($cache[$cache_id]) ||
            file_exists(
                $io->GetPhysicalName($_SERVER["DOCUMENT_ROOT"].$cacheImageFileCheck
                ))
        ){
            return true;
        }
        return false;
    }
    static function lastEdited($data){
        global $DB;
        switch($data['type']){
            case 'element':
            case 'e':
                $iblock = '';
                if(!empty($data['iblock'])){
                    if(
                        is_int($data['iblock']) ||
                        is_string($data['iblock'])
                    ){
                        $iblock = 'IBLOCK_ID = '.intval($data['iblock']);
                    }
                    elseif(is_array($data['iblock'])){
                        $iblock = 'IBLOCK_ID IN ('.implode(',', array_map('intval', $data['iblock'])).')';
                    }
                }

                $q = '
						SELECT
							`TIMESTAMP_X` AS time
						FROM
							b_iblock_element WHERE 
							'.$iblock.'
						ORDER BY
							TIMESTAMP_X DESC
					';

                $r = $DB->Query($q);
                $r = $r->Fetch();
                return $r['time'];
        }
    }

    static function randomString($length = 16){
        $symbols = 'abcdefghijklmnopqrstuvwxyz0123456789'; //``-=~!@#$%^&*()_+,./<>?;:[]{}\|';
        $result = '';
        $max = strlen($symbols) - 1;
        for($i=0; $i < $length; $i++){
            $symbol = $symbols[mt_rand(0, $max)];
            if(mt_rand(1, 3) == 2){
                $symbol = strtoupper($symbol);
            }
            $result .= $symbol;
        }
        return $result;
    }
    static function matchDir($dir, $index = null){
        global $APPLICATION;
        $dir = strtr($dir, array(
            '*' => '(.*?)'
        ));
        preg_match(
            '#'.$dir.'$#',
            $APPLICATION->GetCurDir(),
            $m
        );
        array_shift($m);
        if(is_int($index)){
            return $m[$index];
        }
        return $m;
    }

    static function includeArea($path){
        $path = SITE_TEMPLATE_PATH."/include_areas/".$path.".php";
        global $APPLICATION;
        $APPLICATION->IncludeComponent(
            "bitrix:main.include",
            "",
            Array(
                "AREA_FILE_SHOW" => "file",
                "PATH" => $path,
                "EDIT_TEMPLATE" => "includearea.php"
            )
        );
    }

    static function iblockByElement($id){
        $a = self::element(array(
            'f' => array(
                'ID' => $id
            ),
            'select' => array(
                'IBLOCK_ID'
            )
        ));
        return $a['IBLOCK_ID'];
    }

    static function mail($data){

        if(!isset($data['to'])){
            $data['to'] = WP::get('var')->one('contact-mail');
        }
        elseif(is_string($data['to']) && (strpos($data['to'], '@') === FALSE)){
            $data['to'] = WP::get('var')->one($data['to']);
        }
        else{
            return;
        }

        if(is_array($data['text'])){
            $a = $data['text'];
            $text = '<h1>'.$a['title'].'</h1><p>'.$a['description'].'</p>';
            foreach($a['fields'] as $field){
                list($name, $value, $type) = $field;
                $name = '<b>'.$name.'</b>: ';
                switch($type){
                    case 'phone':
                        $value = '<a href="tel:'.$value.'">'.$value.'</a>';
                        break;
                    case 'email':
                        $value = '<a href="mailto:'.$value.'">'.$value.'</a>';
                        break;
                }
                $text .= '<div>'.$name.$value.'</div>';
            }
            $data['text'] = $text;
        }

        mail(
            $data['to'],
            $data['name'],
            $data['text']
        );
    }

    static function getSmartFilterName($data){
        $filter = isset($data['filter']) ? $data['filter'] : 'arrFilter';
        $property = isset($data['property']) ? $data['property'] : 0;
        $key = isset($data['id']) ? abs(crc32($data['id'])) : 0;
        $name = htmlspecialcharsbx($filter."_".$property."_".$key);
        if(!$data['full']){
            return $name;
        }
        return $data['full'].$name.'=Y&set_filter=Подобрать';
    }

    static function js($o){
        return CUtil::PhpToJSObject($o);
    }

    static function whereIs($name, $type = 'class'){
        switch($type){
            default:
                $o = new ReflectionClass($name);
                return $o ? $o->getFileName() : '';
        }
        return '';
    }

    static function addElement($data){
        \Bitrix\Main\Loader::IncludeModule('iblock');
        $e = new CIBlockElement();
        $properties = array();
        if(isset($data['p'])){
            if(is_string($data['p'])){
                $data['p'] = self::getListStringToArray($data['p']);
            }
            self::replaceShortenIndeces($data['p']);
            foreach($data['p'] as $name => $value){
                list($name, $type, $other1) = explode(':', $name);
                switch($type){
                    case 'html':
                    case 'text':
                        $value = array(
                            'VALUE' => array(
                                'TYPE' => strtoupper($type),
                                'TEXT' => $value
                            )
                        );
                        break;

                    case 'file':
                        if(is_array($value) && isset($value['tmp_name'])){
                            break;
                        }
                        $value = array(
                            'name' => $other1,
                            'tmp_name' => $value,
                        );
                        break;
                }
                $properties[$name] = $value;
            }
        }
        $fields = array(
            'MODIFIED_BY' => 1,
            'IBLOCK_ID' => 57,
            'ACTIVE' => 'N',
            'CODE' => 'random_'.mt_rand(0, 10000),
            'NAME' => '(без названия)',
            'PROPERTY_VALUES' => $properties
        );
        if(isset($data['f'])){
            if(is_string($data['f'])){
                $data['f'] = self::getListStringToArray($data['f']);
            }
            self::replaceShortenIndeces($data['f']);
            $fields = array_merge($fields, $data['f']);
        }
        if($data['debug']){
            WP::log($fields);
        }
        return $e->Add($fields);
    }

    static function &last(&$a){
        return $a[count($a) - 1];
    }
    /*
        WP::attr(array(
            'href' => 'http://ya.ru',
            'data-no-need' => null,
            'class' => 'super',
            'data-empty' => ''
        )); // возвращает ' href="http://ya.ru" class="super" data-empty=""'
    */
    static function el($type, $a){
        echo '<'.$type.' '.self::attr($a).'/>';
    }
    static function attr($a){
        $result = '';
        foreach($a as $i => $v){
            if($v === null){
                continue;
            }
            $result .= ' '.$i.'="'.$v.'"';
        }
        return $result;
    }
    static function loadScript($name){
        global $APPLICATION;
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.$name);
    }
    /*
        Функция для кеширования
        Пример:
        $arResult = WP::cache('c_component_name', 3600000, function(){
            return superHardCalculation();
        });
    */
    static function cache($name, $time, $callback){
        if(is_array($name)){
            $sname = '';
            foreach($name as $value){
                $sname .= '_'.((string)$value);
            }
            $name = substr($sname, 1);
        }

        $cache = new CPHPCache;
        if($time === null){
            $time = 3600000;
        }

        if($cache->InitCache($time, $name, "/cache_dir") && !(isset($_REQUEST['clear_cache']) && $_REQUEST['clear_cache'] == 'Y')){
            extract($cache->GetVars());
        } else {
            if($cache->StartDataCache($time, $name, "/cache_dir")){
                $result = $callback();
                $cache->EndDataCache(array(
                    "result" => $result
                ));
            }
        }
        return $result;
    }

    /*
        Получить id для редактирования элемента в "Эрмитаже" битрикса.
        Пример:
        <div id="<?= WP::getEditElementID(1, 124, $this) ?>">
    */
    static function editID($iblockID, $elementID, $template){
        return self::getEditElementID($iblockID, $elementID, $template, true);
    }
    static function getEditElementID($iblockID, $elementID = 0, $template = null, $isAttribute = false){
        if(!$template || !$elementID){
            return 0;
        }
        \Bitrix\Main\Loader::IncludeModule('iblock');

        $buttons = CIBlock::GetPanelButtons(
            $iblockID,
            $elementID,
            0,
            array(
                "SECTION_BUTTONS" => false,
                "SESSID" => false
            )
        );

        $template->AddEditAction(
            $elementID,
            $buttons["edit"]["edit_element"]["ACTION_URL"],
            CIBlock::GetArrayByID(
                $iblockID,
                "ELEMENT_EDIT"
            )
        );

        $template->AddDeleteAction(
            $elementID,
            $buttons["edit"]["delete_element"]["ACTION_URL"],
            CIBlock::GetArrayByID(
                $iblockID,
                "ELEMENT_DELETE"
            ),
            array(
                "CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')
            )
        );

        $id = $template->GetEditAreaId($elementID);
        if($isAttribute){
            $id = $id ? 'id="'.$id.'"' : '';
        }
        return $id;
    }

    static function getListPropertyValues($iblock, $id, $xml = null, $value = null){
        \Bitrix\Main\Loader::IncludeModule('iblock');
        $filter =  array(
            'IBLOCK_ID' => $iblock,
        );
        if(preg_match('/^[\d+]$/', $id)){
            $filter['CODE'] = $id;
        }
        else{
            $filter['PROPERTY_ID'] = $id;
        }

        if($xml){
            $filter['XML_ID'] = $xml;
        }

        $list = CIBlockPropertyEnum::GetList(array(
            'SORT' => 'ASC'
        ), $filter);

        $result = array();
        while(($element = $list->Fetch()) !== false){
            $result[$element['XML_ID']] = $element;
        }
        if($xml){
            if($value){
                return $result[$xml][$value];
            }
            return $result[$xml];
        }
        return $result;
    }

    private static $signs = array(
        '\>\=',
        '\<\=',
        '\=',
        '\>',
        '\<',
        '\>\<',
        '\!'
    );

    private static function getListStringToArray($s){
        $result = array();
        foreach(explode(';', $s) as $expression){
            $expression = trim($expression);
            preg_match(
                '/^(.*?)('.implode('|', self::$signs).')(.*)$/',
                $expression,
                $m
            );

            list($noneed, $key, $func, $value) = array_map('trim', $m);

            if(strpos($value, ',') > 0){
                $value = array_map('trim', explode(',', $value));
            }

            if($func == '='){
                $func = '';
            }
            $result[$func.$key] = $value;
        }
        return $result;
    }

    private static function replaceShortenIndeces(&$a){
        foreach(array(
                    'iblock' => 'IBLOCK_ID',
                    'section' => 'SECTION_ID',
                    'id' => 'ID'
                ) as $before => $after){
            if(isset($a[$before])){
                $a[$after] = $a[$before];
                unset($a[$before]);
            }
        }
    }

    private static $getListSelects = array();

    private static function prepareGetListData($d){
        if(isset($d['offers'])){
            \Bitrix\Main\Loader::IncludeModule('catalog');
            $info = \CCatalogSKU::GetInfoByProductIBlock($d['offers']['iblock']);
            if(!empty($info)){
                $d['f']['iblock'] = $info['IBLOCK_ID'];
                $d['p'][$info['SKU_PROPERTY_ID']] = $d['offers']['id'];
            }
            unset($d['offers']);
        }

        foreach(array(
                    array(
                        array(
                            'sel',
                            's',
                            'where'
                        ),

                        'select',
                    ),

                    array(
                        array(
                            array(
                                'nav',
                                'page'
                            ),
                            'page'
                        ),

                        array(
                            'nav',
                            'iNumPage'
                        )
                    ),

                    array(
                        'max',

                        array(
                            'nav',
                            'nTopCount'
                        )
                    ),

                    array(
                        array(
                            'per-page',
                            array(
                                'nav',
                                'per-page'
                            ),
                        ),

                        array(
                            'nav',
                            'nPageSize'
                        )
                    ),

                    array(
                        array(
                            'order'
                        ),

                        'sort'
                    ),
                ) as $a){
            list($froms, $to) = $a;
            if(is_string($froms)){
                $froms = array(array($froms));
            }
            if(is_string($to)){
                $to = array($to);
            }
            foreach($froms as $from){
                if(is_string($from)){
                    $from = array($from);
                }
                $b = $d;
                $set = true;
                foreach($from as $i){
                    if(!isset($b[$i])){
                        $set = false;
                        break;
                    }
                    $b = $b[$i];
                }

                if(!$set){
                    continue;
                }

                $c = &$d;
                foreach($to as $i){
                    $c = &$c[$i];
                }
                $c = $b;
            }
        }

        // select
        if(is_string($d['select'])){
            $d['select'] = array_map('trim', explode(',', $d['select']));
        }
        elseif(
            is_array($d['select']) &&
            !empty($d['select'])
        ){
            if(is_string($d['select']['f'])){
                $d['select']['f'] = array_map('trim', explode(',', $d['select']['f']));
            }
            if(
                is_array($d['select']['f']) &&
                !empty($d['select']['f'])
            ){
                foreach($d['select']['f'] as $v){
                    $d['select'][] = $v;
                }
                unset($d['select']['f']);
            }


            if(is_string($d['select']['p'])){
                $d['select']['p'] = array_map('trim', explode(',', $d['select']['p']));
            }
            if(
                is_array($d['select']['p']) &&
                !empty($d['select']['p'])
            ){
                foreach($d['select']['p'] as $v){
                    $d['select'][] = 'PROPERTY_'.$v;
                }
                unset($d['select']['p']);
            }
        }

        self::$getListSelects = array();
        foreach($d['select'] as $i => $select){
            if(strpos($select, ':') > 0){
                list($name, $newName) = explode(':', $select);
                self::$getListSelects[$name] = $newName;
                $d['select'][$i] = $name;
            }
        }

        // filter

        if(!isset($d['filter'])){
            $d['filter'] = array();
        }

        if(isset($d['f'])){
            if(is_string($d['f'])){
                $d['f'] = self::getListStringToArray($d['f']);
            }
            $d['filter'] = array_merge($d['filter'], $d['f']);
        }

        if(isset($d['p'])){
            if(is_string($d['p'])){
                $d['p'] = self::getListStringToArray($d['p']);
            }
            foreach($d['p'] as $i => $v){

                preg_match(
                    '/^\s*('.implode('|', self::$signs).'|)(.*)$/',
                    $i,
                    $m
                );
                list($all, $func, $code) = $m;
                $d['filter'][$func.'PROPERTY_'.$code] = $v;
            }
        }

        if(empty($d['filter']) && !$d['skip-filter']){
            return null;
        }

        if(is_string($d['filter'])){
            $d['filter'] = self::getListStringToArray($d['filter']);
        }

        self::replaceShortenIndeces($d['filter']);

        // sort

        if(isset($d['sort'])){
            if(is_string($d['sort'])){
                $name = $d['sort'];
                $d['sort'] = array(
                    $name => 'ASC'
                );
            }
        }
        else{
            $d['sort'] = array(
                'SORT' => 'ASC'
            );
        }


        // count

        if(isset($d['count'])){
            switch($d['count']){
                case 'active':
                    $d['count'] = CNT_ACTIVE;
                    break;
            }
        }

        return $d;
    }

    private static function getList($data){
        \Bitrix\Main\Loader::IncludeModule('iblock');

        if(!$data['noprepare']){
            $data['data'] = self::prepareGetListData($data['data']);
        }
        $d = &$data['data'];

        $attrs = array();
        foreach($data['args'] as $i){
            if(!isset($d[$i])){
                $attrs[] = false;
                continue;
            }

            if(preg_match('#^\&#', $i)){
                $attrs[] = &$d[$i];
                continue;
            }

            $attrs[] = $d[$i];
        }

        if($d['debug']){
            WP::log(array(
                $data['func'],
                $attrs
            ));
        }

        $result = call_user_func_array($data['func'], $attrs);
        if($d['debug'] && $result === false){
            WP::log('ERROR CALLING GETLIST');
        }
        return $result;
    }

    static function getCompareIDs(){
        $ids = array();
        foreach($_SESSION['CATALOG_COMPARE_LIST'] as $e){
            foreach($e['ITEMS'] as $id => $o){
                $ids[] = intval($id);
            }
        }
        sort($ids);
        return $ids;
    }

    private static $lastList = null;


    static function bit($data){
        if(is_string($data)){
            $list = self::$lastList;
            switch($data){
                case 'list':
                    return $list;

                case 'pages':
                    return $list->NavPageCount;

                case 'page':
                    return $list->NavPageNomer;

                case 'total':
                case 'count':
                case 'amount':
                    return $list->SelectedRowsCount();
            }
        }

        $settings = array();

        $data = self::prepareGetListData($data);
        if($data === null){
            return false;
        }

        switch($data['of']){
            case 'prices':
            case 'price':
            case 'pr':
                $settings = array(
                    'class' => 'CPrice',
                    'args' => array(
                        'sort',
                        'filter',
                        'group',
                        'nav',
                        'select',
                    ),
                    'next-method' => 'Fetch',
                    'module' => 'catalog',
                );
                break;

            case 'user':
            case 'users':
            case 'u':
                $settings = array(
                    'class' => 'CUser',
                    'args' => array(
                        '&by',
                        '&order',
                        'filter',
                        'params'
                    ),
                    'prepare-data' => function(&$data){
                        if(isset($data['sort'])){
                            $data['&by'] = $data['sort'];
                            $data['&order'] = reset($data['sort']);
                        }
                        if(!isset($data['&by'])){
                            $data['&by'] = array(
                                'sort' => 'asc'
                            );
                        }
                        if(!isset($data['&order'])){
                            $data['&order'] = 'asc';
                        }
                    }
                    //'next-method' => array('NavNext', true, 'f_'),
                );
                break;

            case 'sale-user':
            case 'sale-users':
            case 'su':
                $settings = array(
                    'class' => 'CSaleUserAccount',
                    'args' => array(
                        'sort',
                        'filter',
                        'group',
                        'nav',
                        'select',
                    ),
                    'next-method' => 'Fetch',
                    'module' => 'sale',
                );
                break;

            case 'order':
            case 'orders':
            case 'o':
                $settings = array(
                    'class' => 'CSaleOrder',
                    'args' => array(
                        'sort',
                        'filter',
                        'group',
                        'nav',
                        'select',
                    ),
                    'next-method' => 'Fetch',
                    'module' => 'sale',
                );
                break;

            case 'basket':
            case 'baskets':
            case 'b':
                $settings = array(
                    'class' => 'CSaleBasket',
                    'args' => array(
                        'sort',
                        'filter',
                        'group',
                        'nav',
                        'select',
                    ),
                    'values' => array(
                        '%BASKET_USER_ID' => function(){
                            \Bitrix\Main\Loader::IncludeModule('sale');
                            return CSaleBasket::GetBasketUserID();
                        }
                    ),
                    'next-method' => 'Fetch',
                    'module' => 'sale',
                );
                break;

            case 'e':
            case 'element':
            case 'elements':
                $settings = array(
                    'class' => 'CIBlockElement',
                    'args' => array(
                        'sort',
                        'filter',
                        'group',
                        'nav',
                        'select'
                    ),
                    'next-method' => empty($data['select']) ? 'GetNextElement' : 'GetNext',
                    'modify-arguments' => function($f, &$arguments, $data){
                        if(empty($data['select'])){
                            $arguments['p'] = $p = $f->GetProperties();
                            $arguments['f'] = $f = $f->GetFields();
                        }
                        else{
                            $p = array();
                            if(isset($f['PROPERTIES'])){
                                $p = $f['PROPERTIES'];
                            }
                            foreach($f as $i => $v){
                                foreach(array(
                                            array('/^PROPERTY_(.*?)_VALUE$/', '%NAME'),
                                            array('/^~PROPERTY_(.*?)_VALUE$/', '~%NAME'),
                                        ) as $a){
                                    list($pattern, $template) = $a;
                                    if(preg_match($pattern, $i, $m)){
                                        $i_ = $m[1];
                                        $p[strtr($template, array(
                                            '%NAME' => $i_
                                        ))]['VALUE'] = $v;
                                        unset($f[$i]);
                                    }
                                }
                            }

                            $arguments['f'] = $f;
                            $arguments['p'] = $p;
                        }

                        if(!$data['object']){
                            return;
                        }
                        $object = new Element($f['ID']);
                        $arguments['f'] = $object->setData(array(
                            'f' => $f,
                            'p' => $p
                        ));
                        unset($arguments['p']);
                    }
                );
                break;

            case 'p':
            case 'property':
            case 'properties':
                $settings = array(
                    'class' => 'CIBlockProperty',
                    'args' => array(
                        'sort',
                        'filter',
                    ),
                );
                break;

            case 'lv':
            case 'list-value':
            case 'list-values':
                $settings = array(
                    'class' => 'CIBlockPropertyEnum',
                    'args' => array(
                        'sort',
                        'filter'
                    )
                );
                break;

            case 's':
            case 'section':
            case 'sections':
                $settings = array(
                    'class' => 'CIBlockSection',
                    'args' => array(
                        'sort',
                        'filter',
                        'count',
                        'select',
                        'nav'
                    ),
                    'modify-arguments' => function($f, &$arguments, $data){
                        $u = array();

                        foreach($f as $i => $v){
                            if(preg_match('/^UF_/', $i)){
                                $j = substr($i, 3);
                                $u[$j] = $v;
                                // unset($f[$i]);
                            }
                        }

                        if($data['object']){
                            $arguments['f'] = new Section($f['ID']);
                            $arguments['f']->setData(array(
                                'f' => $f,
                                'u' => $u
                            ));
                            return;
                        }

                        $arguments['f']  = $f;
                        $arguments['p']  = $u;
                    }
                );
                break;

            case 'st':
            case 'section-tree':
            case 'sections-tree':
                $settings = array(
                    'class' => 'CIBlockSection',
                    'args' => array(
                        'filter',
                        'select',
                    ),
                    'modify-arguments' => function($f, &$arguments, $data){
                        if($data['object']){
                            $arguments['f'] = new Section($f['ID']);
                            $arguments['f']->setData($f);
                        }
                    }
                );

                break;

            case 'es':
            case 'element-section':
            case 'element-sections':
                $settings = array(
                    'class' => null,
                    'get-list' => function(&$data){
                        \Bitrix\Main\Loader::IncludeModule('iblock');
                        return CIBlockElement::GetElementGroups(
                            $data['id'],
                            false,
                            is_array($data['select']) ? $data['select'] : false
                        );
                    },
                    'next-method' => 'Fetch'
                );
                break;

            case 'ib':
            case 'iblock':
            case 'iblocks':
                $settings = array(
                    'class' => 'CIBlock',
                    'args' => array(
                        'sort',
                        'filter',
                        'group',
                        'nac',
                        'select'
                    ),
                    'modify-arguments' => function($f, &$arguments, $data){
                        if(!$data['object']){
                            return;
                        }
                        $object = new IBlock($f['ID']);
                        $object->setData($f);
                        $arguments['f'] = $object;
                    }
                );
                break;

            case 'ibt':
            case 'iblock-type':
            case 'iblock-types':
                $settings = array(
                    'class' => 'CIBlockType',
                    'args' => array(
                        'sort',
                        'filter'
                    ),
                    'modify-arguments' => function($f, &$arguments){
                        $arguments['f']['LANG'] = \CIBlockType::GetByIDLang($f["ID"], LANG);
                    }
                );
                break;

            case 'highload':
            case 'hl':
                $settings = array(
                    'class' => null,
                    'get-list' => function(&$data){
                        if(empty($data['filter']['table'])){
                            return null;
                        }

                        global $DB;

                        $q = array(
                            'select' => '*',
                            'where' => '1'
                        );

                        if(!empty($data['select'])){
                            $q['select'] = '';
                            foreach($data['select'] as $row){
                                $q['select'] .= ', `'.$DB->ForSql($data['select']).'`';
                            }
                            $q['select'] = substr($q['select'], 2);
                        }

                        if(count($data['filter']) > 1){
                            $q['where'] = '';
                            foreach($data['filter'] as $field => $value){
                                if($field == 'table'){
                                    continue;
                                }
                                $q['where'] .= ' AND `'.$DB->ForSql($field).'` = "'.$DB->ForSql($value).'"';
                            }
                            $q['where'] = substr($q['where'], 5);
                        }

                        return $DB->Query('SELECT '.$q['select'].' FROM '.$data['filter']['table'].' WHERE '.$q['where']);
                    },
                    'next-method' => 'Fetch'
                );
                break;
        }

        if(isset($settings['prepare-data'])){
            $settings['prepare-data']($data);
        }

        if(!empty($settings['values']) && !empty($data['filter'])){
            foreach($data['filter'] as $i => $v){
                if(is_string($v) && is_callable($settings['values'][$v])){
                    $data['filter'][$i] = $settings['values'][$v]();
                }
            }
        }

        if(empty($settings['get-list'])){
            $list = self::getList(array(
                'data' => $data,
                'func' => '\\'.$settings['class'].'::GetList',
                'args' => $settings['args'],
                'noprepare' => true
            ));
        }
        else{
            $list = $settings['get-list']($data);
        }


        if($list === null){
            if($data['debug']){
                self::log('no list');
            }
            return;
        }

        if($data['get-count']){
            return $list->SelectedRowsCount();
        }

        if(isset($settings['module'])){
            \Bitrix\Main\Loader::IncludeModule($settings['module']);
        }

        self::$lastList = $list;

        if(empty($settings['next-method'])){
            $settings['next-method'] = 'GetNext';
        }
        elseif(is_array($settings['next-method'])){
            $method = array_shift($settings['next-method']);
            $settings['next-method-args'] = $settings['next-method-args'];
        }

        if(empty($settings['next-method-args'])){
            $settings['next-method-args'] = array();
        }

        if(empty($settings['modify-arguments'])){
            $settings['modify-arguments'] = function($element) use (&$arguments){
                $arguments['f'] = $element;
            };
        }

        foreach(array(
                    'map',
                    'one'
                ) as $i){
            if(is_callable($data[$i])){
                $data['each'] = $data[$i];
                $data[$i] = true;
                break;
            }

            if(!is_string($data[$i])){
                continue;
            }

            $indeces = explode('.', $data[$i]);
            $data['each'] = function($d) use (&$data, $indeces){
                $o = $d;
                foreach($indeces as $i){
                    $o = $o[$i];
                }
                return $o;
            };
            $data[$i] = true;
            break;
        }

        $arguments = array(
            'i' => 0,
            'si' => 0,
            'event' => array(
                'skip' => false,
                'break' => false
            )
        );

        $output = array();

        $any = false;
        while(($element = call_user_func(
                array(
                    $list,
                    $settings['next-method']
                ),
                $settings['next-method-args']
            )) !== false){
            $any = true;
            foreach(self::$getListSelects as $prev => $new){
                foreach(array('', '~') as $prefix){
                    $element[$prefix.$new] = $element[$prefix.$prev];
                    unset($element[$prefix.$prev]);
                }
            }
            $arguments['f'] = $element;

            $settings['modify-arguments']($element, $arguments, $data);

            $result = $data['each'](
                $arguments,
                $arguments['f'],
                $arguments['p']
            );
            if($data['one']){
                $output = $result;
                break;
            }

            if($data['map']){
                if($arguments['event']['skip'] || $arguments['event']['break']){
                    $arguments['event']['skip'] = false;
                    $arguments['si']--;
                }
                else{
                    $output[] = $result;
                }
                $result = true;
            }

            if($result === false || $arguments['event']['break'] == true){
                $arguments['event']['break'] = true;
                if(!$data['map']){
                    $output = false;
                }
                break;
            }

            $arguments['i']++;
            $arguments['si']++;
        }

        if(!$any && $data['one']){
            return null;
        }

        if($data['get-list']){
            return $list;
        }

        if(!$data['map'] && !$data['one'] && $data['each']){
            return !$arguments['event']['break'];
        }

        return $output;
    }

    /*
        Упрощение CIBlockElements::GetList
    */
    static function elements($data){
        $list = self::getList(array(
            'data' => $data,
            'func' => 'CIBlockElement::GetList',
            'args' => array(
                'sort',
                'filter',
                'group',
                'nav',
                'select'
            ),
        ));

        if(is_callable($data['map'])){
            $data['each'] = $data['map'];
            $data['map'] = true;
        }

        if(is_callable($data['one'])){
            $data['each'] = $data['one'];
            $data['one'] = true;
        }

        $i = 0;
        $event = array(
            'break' => false
        );
        $output = array();

        $step = function($f, $p) use (&$data, &$i, &$event, &$output){
            if(!$data['each']){
                $f['PROPERTIES'] = $p;
                $output[] = $f;
                return;
            }

            $result = $data['each']($f, $p, $i, $event);

            if($data['one']){
                $output = $result;
                return false;
            }

            if($data['map']){
                if($event['skip'] || $event['break']){
                    $event['skip'] = false;
                }
                else{
                    $output[] = $result;
                }
                $result = true;
            }

            if($result === false || $event['break'] == true){
                $event['break'] = true;
                return false;
            }

            $i++;
        };


        if($data['select']){
            while(($f = $list->GetNext()) !== false){
                $p = array();
                if(isset($f['PROPERTIES'])){
                    $p = $f['PROPERTIES'];
                }
                foreach($f as $i => $v){
                    foreach(array(
                                array('/^PROPERTY_(.*?)_VALUE$/', '%NAME'),
                                array('/^~PROPERTY_(.*?)_VALUE$/', '~%NAME'),
                            ) as $a){
                        list($pattern, $template) = $a;
                        if(preg_match($pattern, $i, $m)){
                            $i_ = $m[1];
                            $p[strtr($template, array(
                                '%NAME' => $i_
                            ))] = $v;
                            unset($f[$i]);
                        }
                    }
                }

                if($step(
                        $f,
                        $p
                    ) === false){
                    break;
                }
            }
        }
        else{
            while(($element = $list->GetNextElement()) !== false){
                if($step(
                        $element->GetFields(),
                        $element->GetProperties()
                    ) === false){
                    break;
                }
            }
        }

        if($data['get-list']){
            return $list;
        }

        if(!$data['map'] && !$data['one'] && $data['each']){
            return !$event['break'];
        }
        return $output;
    }

    /*
        Упрощение CIBlockProperty::GetList
    */
    static function properties($data){

        if(!count($data['filter'])){
            if($data['skip-filter']){
                $data['filter'] = array();
            }
            else{
                return null;
            }
        }

        $list = self::getList(array(
            'data' => $data,
            'func' => 'CIBlockProperty::GetList',
            'args' => array(
                'sort',
                'filter',
            ),
        ));


        $result = $data['each'] ? true : array();

        $i = 0;
        while(($property = $list->GetNext()) !== false){
            if($data['each']){
                $result = $data['each']($property, $i);
                $i++;
            }
            else{
                $result[] = $property;
            }
        }

        return $data['get-list'] ? $list : $result;
    }

    static function element($data){$result = self::elements($data); if(is_array($result)){return $result[0];}}
    static function iblock($data){$result = self::iblocks($data); if(is_array($result)){return $result[0];}}
    static function section($data){$result = self::sections($data); if(is_array($result)){return $result[0];}}

    static function query($query, $data = false, $callback = false){
        global $DB;
        switch($query){
            case 'id':
                return $DB->LastID();;
        }

        if(is_callable($data)){
            $callback = $data;
            $data = null;
        }

        foreach($data as $i => $value){
            $value = $DB->ForSql($value);
            $value = '"'.$value.'"';
            if(is_int($i)){
                $query = preg_replace('/\?/', $value, $query, 1);
                continue;
            }

            $query = preg_replace('/\:'.$i.'/', $value, $query);
        }

        $r = $DB->Query($query);
        $all = array();
        while(($row = $r->Fetch()) !== false){
            if(is_callable($callback)){
                $result = $callback($row);
                if($result === false){
                    break;
                }
                continue;
            }

            $all[] = $row;
        }

        if(is_callable($callback)){
            return true;
        }

        return $all;
    }

    /*
        Упрощение CIBlockSections
    */
    static function sections($data){
        $list = self::getList(array(
            'data' => $data,
            'func' => 'CIBlockSection::GetList',
            'args' => array(
                'sort',
                'filter',
                'count',
                'select',
                'nav'
            )
        ));

        $i = 0;

        $result = $data['each'] ? true : array();

        while(($section = $list->GetNext()) !== false){
            if($data['each']){
                $result = $data['each']($section, $i);
                if($result === false){
                    return false;
                }
                $i++;
            }
            else{
                $result[] = $section;
            }
        }

        return $result;
    }

    /*
        Упрощение CIBlock
    */
    static function iblocks($data){
        $list = self::getList(array(
            'data' => $data,
            'func' => 'CIBlock::GetList',
            'args' => array(
                'sort',
                'filter',
                'group',
                'nac',
                'select'
            )
        ));

        $i = 0;
        $result = $data['each'] ? true : array();

        while(($iblock = $list->GetNext()) !== false){
            if($data['each']){
                $result = $data['each']($iblock, $i);
                if($result === false){
                    return false;
                }
                $i++;
            }
            else{
                $result[] = $iblock;
            }
        }

        return $result;
    }

    /* Получаем экземпляры объектов */
    static function get($name){
        switch($name){
            case 'region':
                return Webprofy\Regional\Main::getInstance();

            case 'var':
                return Webprofy\SiteVariables::getInstance();

            case 'wideimage':
                if(class_exists('WideImage')){
                    return;
                }
                include $_SERVER['DOCUMENT_ROOT'].'/local/classes/WideImage/WideImage.php';
                break;;
        }
        return null;
    }

    static function parsePhone($value){
        $value = trim($value);
        $phone = array(
            'html' => '',
            'number' => ''
        );
        foreach(explode(' ', $value) as $i => $v){
            if($i == 0){
                $phone['html'] .= '<span class="phone_code">';
            }
            $phone['html'] .= $v.' ';
            if($i == 1){
                $phone['html'] .= '</span>';
            }
        }
        $phone['number'] = preg_replace('/[^\d]/', '', $value);
        $phone['number'] = preg_replace('/^8/', '+7', $phone['number']);
        return $phone;
    }

    /*
        Добавить события.

        Пример:

        WP::addEvents(array(
            'main' => array(
                'OnPageStart' => array(
                    'callback' => function(){
                        echo 'ok';
                    },
                    'priority' => 50
                )
            )
        ));
    */
    static function addEvents($modules){
        foreach($modules as $module => $events){
            foreach($events as $event => $data){
                if(is_array($data) && isset($data['callback'])){
                    $callback = $data['callback'];
                    $priority = isset($data['priority']) ? $data['priority'] : 50;
                }
                elseif(is_callable($data)){
                    $callback = $data;
                    $priority = 50;
                }

                AddEventHandler($module, $event, $callback, $priority);
            }
        }
    }

    /*
        Получаем список highload-элементов по ID.
    */
    private static $getHLElementsCache = array();
    static function getHLElements($id){
        if(isset(self::$getHLElementsCache[$id])){
            return self::$getHLElementsCache[$id];
        }
        \Bitrix\Main\Loader::IncludeModule("highloadblock");
        $hldata = \Bitrix\Highloadblock\HighloadBlockTable::getById($id)->fetch();
        $hlentity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hldata);
        $hlDataClass = $hldata['NAME'].'Table';
        $list = $hlDataClass::getList();

        $result = array();
        while($element = $list->Fetch()){
            $result[] = $element;
        }

        self::$getHLElementsCache[$id] = $result;
        return $result;
    }

    /*
        Создать папки в структуре сайта.
        WP::makeStructure(array(
            'names' => array(
                // 'Самокаты',
                'О магазине' => array(
                    'Гарантии',
                    'Книга отзывов',
                ),
                'Услуги',
                'Статьи',
                // ...
            )
        ));
    */
    static function makeStructure($data){
        $names = $data['names'];
        $debug = $data['debug'];
        $parent = isset($data['parent']) ? $data['parent'] : '/';
        $result = '';
        $depth = isset($data['depth']) ? $data['depth'] : 0;

        foreach($names as $k => $v){
            if(is_array($v)){
                $name = $k;
            }
            else{
                $name = $v.'';
            }

            $ename = CUtil::translit(trim($name), 'ru', array('change_case' => 'L'));
            $path = $_SERVER['DOCUMENT_ROOT'].$parent.$ename;

            mkdir($path, 0755, true);

            $pathIndex = $path.'/index.php';
            $pathSection = $path.'/.section.php';
            $added = false;

            foreach(array(
                        array(
                            'index.php',
                            '<?'.$nl.'	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");'.$nl.'	$APPLICATION->SetTitle("'.$name.'");'.$nl.'?>'.$nl.'	Text.'.$nl.'<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>'
                        ),
                        array(
                            '.section.php',
                            '<? $sSectionName="'.$name.'"; ?>'
                        )
                    ) as $a){
                list($filename, $content) = $a;
                $file = $path.'/'.$filename;
                if(file_exists($file)){
                    WP::log('file "'.$file.'" exists');
                    continue;
                }
                $added = true;
                if(!$debug){
                    WP::log('creating "'.$file.'"');
                    file_put_contents($file, $content);
                }
            }

            if($added){
                WP::log('<a href="'.$parent.$ename.'">'.$name.'</a> '.$parent.$ename.'/');
                $result .= '
	Array(
		"'.str_repeat('-', $depth).' '.$name.'", 
		"'.$parent.$ename.'", 
		Array(), 
		Array(), 
		"" 
	),';
            }

            if(is_array($v)){
                $result .= self::makeStructure(array(
                    'names' => $v,
                    'debug' => $debug,
                    'parent' => $parent.$ename.'/',
                    'depth' => $depth + 1
                ));
            }
        }
        return $result;
    }

    private static $timeToWordsData = array(
        'ru' => array(
            'instantly' => 'мгновенно',
            'times' => array(
                array('d', 'д', 'ней', 'ень', 'ня'),
                array('h', 'час', 'ов', '', 'а'),
                array('m', 'минут', '', 'а', 'ы'),
                array('s', 'секунд', '', 'а', 'ы'),
                array('ms', 'миллисекунд', '', 'а', 'ы'),
            )
        ),
        'en' => array(
            'instantly' => 'instantly',
            'times' => array(
                array('d', 'day', 'days'),
                array('h', 'hour', 'hours'),
                array('m', 'minute', 'minutes'),
                array('s', 'second', 'seconds'),
                array('ms', 'millisecond', 'milliseconds'),
            )
        )
    );

    private static $measures = array();
    static function measure($data){
        $to = isset($data['to']) ? $data['to'] : '_';
        $m = &self::$measures;
        if(isset($data['add'])){
            $data['do'] = 'add';
            $data['name'] = $data['add'];
        }

        switch($data['do']){
            case 'add':
                $name = isset($data['name']) ? $data['name'] : 'без названия';
                $m[$to][] = array(
                    'name' => $name,
                    'ms' => round(microtime(true) * 1000)
                );
                break;

            case 'clear':
                unset($m[$to]);
                break;

            case 'log':
                self::measure(array(
                    'do' => 'diff',
                    'to' => $to
                ));
                $result = array();
                $a = &$m[$to];
                $result['each'] = $a;
                $total = $a[count($a) - 1]['ms'] - $a[0]['ms'];
                $result['total'] = array(
                    'delta' => $total,
                    'delta-ru' => self::timeToWords($total, 'ru', false)
                );
                WP::log($result);
                break;

            case 'diff':
                $previous = null;
                foreach($m[$to] as &$measure){
                    if($previous !== null){
                        $measure['delta'] = $measure['ms'] - $previous['ms'];
                        $measure['delta-ru'] = self::timeToWords($measure['delta'], 'ru', false);
                    }
                    $previous = $measure;
                }
                break;
        }
    }

    private static $previousTimes = array();
    static function measureTime($name = '', $log = false){
        self::$previousTimes[] = array(
            'name' => $name,
            'value' => microtime()
        );
        if(!$log){
            return self::$previousTimes;
        }
        $result = '';
        $prev = null;
        foreach(self::$previousTimes as $time){
            if($prev === null){
                $prev = $time;
                continue;
            }
            $delta = $time['value'] - $prev['value'];
            $prev = $time;
            $result .= $time['name'].' '.$delta."\n";
        }
        $result .= "-----------------\n";
        $result .= "Итого '$name':";
        $result .= self::$previousTimes[count(self::$previousTimes)]['value'] - self::$previousTimes[0]['value'];
        return $result;
    }

    private static $times = array(
        array('ms', 1000),
        array('s', 60),
        array('m', 60),
        array('h', 24),
        array('d', 31),
        array('mon', 12),
    );

    static function time($value, $from = 'm', $to = 's'){
        $t = self::$times;
        $n = count($t);

        $active = false;
        $multiplier = 1;

        for(
            $i = $n - 1;
            $i >= 0;
            $i--
        ){
            list($name, $value_) = $t[$i];
            if(!$active){
                if($name == $from){
                    $active = true;
                }
                else{
                    continue;
                }
            }

            if($active){
                $multiplier *= $value_;
            }
        }

        $active = false;
        $value *= $multiplier;

        $multiplier = 1;

        for(
            $i = $n - 1;
            $i >= 0;
            $i--
        ){
            list($name, $value_) = $t[$i];
            if(!$active){
                if($name == $to){
                    $active = true;
                }
                else{
                    continue;
                }
            }

            if($active){
                $multiplier *= $value_;
            }
        }

        $value /= $multiplier;

        return $value;
    }

    /*
        Переводит время в миллисекундах в словесное представление:
        WP::timeToWords(7453862, 'ru'); // '2 часа 4 минуты 13 секунд'
        WP::timeToWords(7453862, 'ru', false); // '2 часа 4 минуты 13 секунд 862 миллисекунды'
    */
    static function timeToWords($value /* in ms */, $language = 'ru', $exclude = array('ms')){
        $language = self::$timeToWordsData[$language];

        $neg = false;
        if($value < 0){
            $value = abs($value);
            $neg = true;
        }

        if($value < 1){
            return $language['instantly'];
        }

        $data = array();
        foreach(self::$times as $a){
            $divider = $a[1];
            $remainder = $value % $divider;
            $data[$a[0]] = $remainder;
            $value = ($value - $remainder) / $divider;
            if($value < 1){
                $value = 0;
                break;
            }
        }


        $data['d'] = $value;

        $s = '';

        foreach($language['times'] as $a){
            $measure = $a[0];
            if(is_array($exclude) && in_array($measure, $exclude)){
                continue;
            }
            $v = $data[$measure];
            if(!$v){
                continue;
            }
            $s .= ' '.$v.' ';
            switch(count($a)){
                case 5:
                    list($measure, $before, $word0, $word1, $word2) = $a;
                    $s .= $before;
                    $s .= WP::russianCountName(
                        $v,
                        $word0,
                        $word1,
                        $word2
                    );
                    break;

                case 3:
                    list($measure, $wordOne, $wordMany) = $a;
                    $s .= abs($v) > 1 ? $wordMany : $wordOne;
                    break;
            }
        }
        return $s;
    }

    /*
        Сортирует массив по значению ключа.

        Пример:
        WP::sortBy(array(
            array(
                'priority' => 1,
                'name' => 'a'
            ),
            array(
                'priority' => 3,
                'name' => 'c'
            ),
            array(
                'priority' => 2,
                'name' => 'b'
            ),
        ), 'priority');

        Результат:

        array(
            array(
                'priority' => 1,
                'name' => 'a'
            ),
            array(
                'priority' => 2,
                'name' => 'b'
            ),
            array(
                'priority' => 3,
                'name' => 'c'
            ),
        );
    */
    static function sortBy(&$a, $keys, $desc = false){
        $keys = array_map(
            function($key){
                return array_map('trim', explode('.', $key));
            },
            preg_split(
                '/\s+/',
                trim($keys)
            )
        );

        usort($a, function($a, $b) use ($keys, $desc){
            foreach($keys as $key){
                $av = $a;
                $bv = $b;
                foreach($key as $i){
                    $av = $av[$i];
                    $bv = $bv[$i];
                    if($av == $bv){
                        continue;
                    }
                    $result = $av > $bv ? 1 : -1;
                    if($desc){
                        $result *= -1;
                    }
                    return $result;
                }
            }
            return 0;
        });
        return $a;
    }

    /*
        Получает широту и долготу по адресу.
    */
    static function getLatLng($address){
        $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false');
        $output = json_decode($geocode);
        $loc = $output->results[0]->geometry->location;
        return array(
            'lat' => $loc->lat,
            'lng' => $loc->lng
        );
    }

    /*
        Получает дерево из массива.

        Пример:

        WP::treeFromArray(
            array(
                array(
                    'name' => 'root',
                    'id_' => 1,
                    'parent' => null,
                    'priority' => 100
                ),
                array(
                    'name' => 'deep2',
                    'id_' => 3,
                    'parent' => 1,
                    'priority' => 200
                ),
                array(
                    'name' => 'deep1',
                    'id_' => 2,
                    'parent' => 1,
                    'priority' => 100
                ),
                array(
                    'name' => 'deeper',
                    'id_' => 4,
                    'parent' => 2,
                    'priority' => 100
                )
            )
        ), null, 'parent', 'id_', 'priority');


        Вернёт:
        array(
            'id' => 1,
            'element' => array(
                'name' => 'root',
                'id_' => 1,
                'parent' => null,
                'priority' => 100
            ),
            'childs' => array(
                array(
                    'id' => 2,
                    'element' => array(
                        'id_' => 2,
                        'name' => 'deep1',
                        'parent' => 1,
                        'priority' => 100
                    ),
                    'childs' => array(
                        ...
                    )
                ),
                array(
                    'id' => 3,
                    'element' => array(
                        'id_' => 3,
                        'name' => 'deep2',
                        'parent' => 1,
                        'priority' => 100
                    ),
                    'childs' => array()
                )
            )
        )
    */
    static function showTree($tree, $levels, $curLevel = 0){
        $level = empty($levels[$curLevel]) ? $levels[count($levels) - 1] : $levels[$curLevel];
        foreach($tree as $node){
            $element = $node['element'];
            if(is_callable($level['before'])){
                $level['before']($element, $node);
            }

            self::showTree($node['childs'], $levels, $curLevel + 1);

            if(is_callable($level['after'])){
                $level['after']($element, $node);
            }
        }
    }

    static function treeFromArray(
        $elements,
        $parentValue = 0,
        $parentField = 'parent',
        $childField = 'id',
        $sortField = 'sort'
    ){
        $result = array();
        foreach($elements as $element){
            if($element[$parentField] != $parentValue){
                continue;
            }
            $id = $element[$childField];
            $result[] = array(
                'id' => $id,
                'element' => $element,
                'childs' => self::treeFromArray(
                    $elements,
                    $id,
                    $parentField,
                    $childField,
                    $sortField
                )
            );
        }

        if($sortField){
            usort($result, function($a, $b) use ($sortField){
                return $a['element'][$sortField] - $b['element'][$sortField];
            });
        }
        return $result;
    }


    /*
        Проходится по дереву из предыдущей функции.
        WP::mapTree(WP::treeFromArray($a), function($element, $parentData){
            $parentID = $parentData ? $parentData['id'] : null;
            $currentID = $element['id_'];

            echo $currentID.' имеет родителя '.$parentID;

            return array( // этот массив попадёт в $parentData раздела-"дитя".
                'id' => $currentID;
            );
        })
    */
    static function mapTree($array, $callback, $parentData = false){
        foreach($array as $node){
            $childData = $callback($node['element'], $parentData);
            if($childData === false){
                return false;
            }
            if(!is_array($node['childs'])){
                return true;
            }
            $result = self::mapTree($node['childs'], $callback, $childData);
            if($result === false){
                return false;
            }
        }
        return true;
    }


    function AddOrderProperty($code, $value, $order) {
        if (!strlen($code)) {
            return false;
        }
        if (\Bitrix\Main\Loader::IncludeModule('sale')) {
            if ($arProp = CSaleOrderProps::GetList(array(), array('CODE' => $code))->Fetch()) {
                return CSaleOrderPropsValue::Add(array(
                    'NAME' => $arProp['NAME'],
                    'CODE' => $arProp['CODE'],
                    'ORDER_PROPS_ID' => $arProp['ID'],
                    'ORDER_ID' => $order,
                    'VALUE' => $value,
                ));
            }
        }
    }

    /*
        Получаем актуальный курс валют.
    */
    static function getCurrency($from = 'EUR', $to = 'RUB'){
        $content = file_get_contents('http://www.cbr.ru/scripts/XML_daily.asp?date_req='.date("d/m/Y"));
        $xml = simplexml_load_string($content);

        $node = $xml->xpath('/ValCurs/Valute/CharCode[text()="'.$from.'"]/../Value');
        $from = floatval(strtr($node[0], array(',' => '.')));

        if($to != 'RUB'){
            $node = $xml->xpath('/ValCurs/Valute/CharCode[text()="'.$to.'"]/../Value');
            $to = floatval(strtr($node[0], array(',' => '.')));
        }
        else{
            $to = 1;
        }

        return $from / $to;
    }

    /*
        Корректно работающий strrpos. Не помню, для чего он мне был нужен. :(
    */
    function strrpos($haystack, $needle, $offset = 0){
        $needleLength = strlen($needle);
        for(
            $i = $offset ? $offset : strlen($haystack);
            $i >= 0;
            $i--
        ){
            if(substr($haystack, $i, $needleLength) == $needle){
                return $i;
            }
        }
        return false;
    }

    private static $amIBuffer = array();

    /*
        Проверка, принадлежит ли пользователь определённой группе.
    */
    static function amI($type = 'admin'){
        global $USER;

        if(isset(self::$amIBuffer[$type])){
            return self::$amIBuffer[$type];
        }

        switch($type){
            case 'admin':
                return in_array(1, $USER->GetUserGroupArray());
        }
    }

    /*
        Обрезает текст.
    */
    static function cutText($text, $maxLength = 100, $stripTags = false, $min = 40){
        $text = trim($text);
        $text = preg_replace('/(\s|&nbsp;)+/', ' ', $text);


        if($stripTags){
            $text = strip_tags($text);
        }

        if(strlen($text) <= $maxLength){
            return $text;
        }

        $pos = f::strrpos($text, '.', $maxLength - 1) + 1;

        if($pos < $min){
            $pos = f::strrpos($text, ' ', $maxLength - 1);
        }

        if($pos < $min){
            $pos = $maxLength;
        }


        $text = substr($text, 0, $pos);

        return $text;
    }

    /*
        Распаковка CSV-файла.
    */
    static function uncsv($path, $delimiter = "\t"){
        $result = array();
        $handle = fopen($_SERVER['DOCUMENT_ROOT'].$path, "r");
        if($handle === FALSE){
            return $result;
        }

        $titles = array_map('trim', fgetcsv($handle, 0, $delimiter));
        while(($a = fgetcsv($handle, 0, $delimiter)) !== FALSE){
            $row = array();
            foreach($a as $i => $v){
                $row[$titles[$i]] = $v;
            }
            $result[] = $row;
        }

        fclose($handle);
        return $result;
    }

    /*
        Вывод лога.
    */
    static function log($object, $types = ''){
        $objectDump = print_r($object, 1);
        $types = explode(' ', $types);

        ob_start();

        if(in_array('clr', $types)){
            $GLOBALS['APPLICATION']->RestartBuffer();
        }

        if(!in_array('nopre', $types)){
            echo '<pre style="background-color:#fff; overflow:visible;">';
        }

        echo "\n\n".str_repeat('=', 10).'LOG START'.str_repeat('=', 10);
        foreach(debug_backtrace() as $i => $b){
            printf("\n%s:%s", $b['file'], $b['line']);
            if(in_array('all', $types)){
                continue;
            }
            break;
        }
        printf(":\n\"%s\"\n", $objectDump)."\n";
        echo str_repeat('=', 10).'LOG END'.str_repeat('=', 10);

        if(!in_array('nopre', $types)){
            echo '</pre>';
        }

        $html = ob_get_clean();

        if(in_array('str', $types)){
            return $html;
        }

        echo $html;

        if(in_array('clr', $types)){
            die();
        }
        return $object;
    }


    static function clog($object){
        echo '<script> console.log('.CUtil::PhpToJSObject($object).') </script>';
    }

    /*
        $n = 133;
        $n.' товар'.WP::russianCountName($n, 'ов', '', 'а'); // '133 товара'
    */
    static function russianCountName($n, $w0, $w1, $w2){
        $n00 = $n % 100;
        $n0 = $n00 % 10;
        if($n0 == 0 || $n00 > 10 && $n00 < 20){
            return $w0;
        }
        if($n0 == 1){
            return $w1;
        }
        if($n0 > 1 && $n0 < 5){
            return $w2;
        }
        return $w0;
    }

    private static $months = array(
        'im' => array(
            'январь', 'февраль',
            'март', 'апрель', 'май',
            'июнь', 'июль', 'август',
            'сентябрь', 'октябрь', 'ноябрь',
            'декабрь'
        ),
        'rod' => array(
            'января', 'февраля',
            'марта', 'апреля', 'мая',
            'июня', 'июля', 'августа',
            'сентября', 'октября', 'ноября',
            'декабря'
        )
    );

    /*
        Получить русское название месяца. 1 - январь.
    */
    static function russianMonthName($number = NULL, $pad = 'im'){
        if(!isset(self::$months[$pad])){
            $pad = 'im';
        }
        if($number === NULL){
            $number = date('n');
        }
        $index = intval($number) - 1;
        $index = $index < 0 ? 0 : ($index > 11 ? 11 : $index);
        return self::$months[$pad][$index];
    }

    /* Кодирование JSON с сохранением русских букв*/
    // НЕ ИСПОЛЬЗОВАТЬ - РАБОТАЕТ НЕКОРРЕКТНО
    static function json_encode($object){
        $isAssociative = (array_keys($object) !== range(0, count($object) - 1));

        $string = '';

        if($isAssociative){
            foreach($object as $key => $value){
                $key = strtr($key, array(
                    '"' => '\"'
                ));

                if(is_array($value)){
                    $value = self::json_encode($value);
                }
                else{
                    $value = '"'.strtr($value, array(
                            '"' => '\"'
                        )).'"';
                }
                $string .= ',"'.$key.'":'.$value;
            }
            return '{'.substr($string, 1).'}';
        }


        foreach($object as $value){
            if(is_array($value)){
                $value = self::json_encode($value);
            }
            else{
                $value = '"'.strtr($value, array(
                        '"' => '\"'
                    )).'"';
            }
            $string .= ','.$value;
        }
        return '['.substr($string, 1).']';
    }

    /*
        Пошаговое удаление инфоблока, выполняется в /local/admin/php_iterator.php
    */
    static function removeIBlock($IBLOCK_ID = 0, $step = null){
        \Bitrix\Main\Loader::IncludeModule('iblock');
        $result = array(
            'repeat' => false
        );

        if(!$IBLOCK_ID){
            $result['info'] = 'no iblock is set';
            return $result;
        }

        switch($step){
            case 1:
            case 'elements':
                $LIMIT = 200;

                $ids = WP::bit(array(
                    'of' => 'element',
                    'max' => $LIMIT,
                    'f' => 'iblock='.$IBLOCK_ID,
                    'sel' => 'ID',
                    'map' => 'f.ID'
                ));

                foreach($ids as $id){
                    CIBlockElement::Delete($id);
                }

                if(count($ids) < $LIMIT){
                    $result['info'] = 'removed elements (1/4)';
                    break;
                }

                $result['repeat'] = true;
                $result['info'] = 'removing elements... (1/4...)';
                break;

            case 2:
            case 'properties':
                $ids = WP::bit(array(
                    'of' => 'property',
                    'f' => 'iblock='.$IBLOCK_ID,
                    'map' => 'f.ID'
                ));

                foreach($ids as $id){
                    CIBlockProperty::Delete($id);
                }

                $result['info'] = "removed ".count($ids)." properties (2/4)";
                break;

            case 3:
            case 'sections':
                $LIMIT = 50;
                $ids = WP::bit(array(
                    'of' => 'section',
                    'sort' => array(
                        'depth_level' => 'DESC'
                    ),
                    'sel' => 'ID',
                    'f' => 'iblock='.$IBLOCK_ID,
                    'max' => $LIMIT,
                    'map' => 'f.ID'
                ));

                foreach($ids as $id){
                    CIBlockSection::Delete($id);
                }

                if(count($data) < $LIMIT){
                    $result['info'] = 'removed sections (3/4)';
                    break;
                }
                $result['info'] = 'removing sections... (3/4...)';
                $result['repeat'] = true;
                break;

            case 4:
            case 'iblock':
                \Bitrix\Main\Loader::IncludeModule('catalog');
                $o = new CCatalog();
                $o->UnLinkSKUIBlock($IBLOCK_ID);
                $o->Delete($IBLOCK_ID);

                global $DB;
                $DB->StartTransaction();
                if(!CIBlock::Delete($IBLOCK_ID)){
                    $result['info'] = 'ERROR removing iblock (4/4!)';
                    $DB->Rollback();
                    break;
                }
                $DB->Commit();
                $result['info'] = 'removed iblock (4/4)';
                break;

            case 'iterate-clear':
                $o = new Webprofy\Session('everything_deleter');
                $o->clear();
                echo 'cleared';
                break;

            case 'iterate':
                $clear = 0;

                $o = new Webprofy\Session('everything_deleter');

                $total = $o->get('total', 0);
                $o->set('total', $total + 1);

                $index = $o->get('index', 0);

                $ids = WP::bit(array(
                    'of' => 'iblock',
                    'f' => 'TYPE=mht_products',
                    'sel' => 'ID',
                    'map' => 'f.ID'
                ));

                if($o->get('remove-iblocks', false)){
                    if(!$ids[0]){
                        return;
                    }
                    echo '
							iblocks left: '.count($ids).'<br/>
							iteration: '.$total.'<br/>
						';
                    WP::removeIBlock($ids[0], 4);
                    echo '#ITERATOR_REPEAT#';
                    return;
                }

                $step = $o->get('step', 1);

                echo '
						index: '.$index.'/'.count($ids).'<br/>
						step: '.$step.'/3<br/>
						iteration: '.$total.'<br/>
					';

                $result = WP::removeIBlock(
                    $ids[$index],
                    $step
                );

                echo $result['info'].'<br/>';

                if($result['repeat']){
                    echo '#ITERATOR_REPEAT#';
                    return;
                }

                if($index == (count($ids) - 1) && $step == 3){
                    $o->set('remove-iblocks', true);
                    echo '#ITERATOR_REPEAT#';
                    return;
                }

                if($step == 3){
                    $index++;
                    $step = 1;
                }
                else{
                    $step++;
                }

                $o
                    ->set('index', $index)
                    ->set('step', $step);


                echo '#ITERATOR_REPEAT#';
                break;
        }

        return $result;
    }
}