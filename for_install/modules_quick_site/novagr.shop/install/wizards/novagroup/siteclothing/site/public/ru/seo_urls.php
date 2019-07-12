<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (CModule::IncludeModule('iblock') && CModule::IncludeModule("highloadblock"))
{
    $parameters = array( "filter" => array("=NAME" => "SeoReference") );
    $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList($parameters)->fetch();
    if (!empty($hlblock))
    {
        $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);

        // pagination
        $limit = array('nPageSize' => 1, 'iNumPage' => 1);

        // sort
        $sort_id = 'ID';
        $sort_type = 'DESC';

        // execute query
        $main_query = new Bitrix\Main\Entity\Query($entity);
        $main_query->setSelect(array('ID', 'UF_NAME'));

        $main_query->setFilter(array('ID' => (int)$_REQUEST['SEO_ID']));
        $main_query->setOrder(array($sort_id => $sort_type));
        //$main_query->setSelect($select)
        //	->setFilter($filter)
        //	->setGroup($group)
        //	->setOrder($order)
        //	->setOptions($options);


        /*if (isset($limit['nPageTop']))
        {
            $main_query->setLimit($limit['nPageTop']);
        }
        else
        {*/
        $main_query->setLimit($limit['nPageSize']);
        // $main_query->setOffset(($limit['iNumPage']-1) * $limit['nPageSize']);
        //}

        //$main_query->setLimit($limit['nPageSize']);
        //$main_query->setOffset(($limit['iNumPage']-1) * $limit['nPageSize']);

        $result = $main_query->exec();
        $result = new CDBResult($result);

        // build results
        $rows = array();

        $tableColumns = array();

        if ($arElement = $result->Fetch())
        {
            if(trim($arElement['UF_NAME'])<>"")
            {
                $_SERVER['REQUEST_URI'] = $arElement['UF_NAME'];
                $requestUri = urldecode($_SERVER['REQUEST_URI']);
                $parseUrl = parse_url($requestUri);

                $pathInfo = pathinfo($parseUrl['path']);
                if(empty($pathInfo['extension']))
                {
                    $parseUrl['path'] = $parseUrl['path'] . '/index.php';
                    $parseUrl['path'] = str_replace('//','/',$parseUrl['path']);
                }
                $APPLICATION->SetCurPage($parseUrl['path'], $parseUrl['query']);
                // функция в строке выше теряет геты
                // хотя должна их задавать, поэтому перепишем _REQUEST
                parse_str( $parseUrl['query'], $_REQUEST );

                include($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/urlrewrite.php');


                if(isset($parseUrl['path']))
                {
                    $absPath = getenv('DOCUMENT_ROOT').$parseUrl['path'];
                    $realPath = realpath($absPath);
                    $bitrixPath = realpath(getenv('DOCUMENT_ROOT').DIRECTORY_SEPARATOR.'bitrix');
                    if(strlen($realPath)>0 and is_dir($realPath) and strlen($bitrixPath)>0 and  strpos($realPath,$bitrixPath)===false)
                    {
                        $directoryIndex = 'index.php';
                        if(file_exists($path = $realPath. DIRECTORY_SEPARATOR . $directoryIndex))
                        {
                            CHTTP::SetStatus("200 OK");
                            include_once($path);
                            die();
                        }
                    }
                    if(strlen($realPath)>0 and is_file($realPath) and strlen($bitrixPath)>0 and  strpos($realPath,$bitrixPath)===false)
                    {
                        if(file_exists($realPath))
                        {
                            CHTTP::SetStatus("200 OK");
                            include_once($realPath);
                            die();
                        }
                    }
                }
            }
        }


/*
        $arFilter = array('IBLOCK_CODE' => 'seo_urls', 'ID'=>(int)$_REQUEST['SEO_ID']);
        $arSelect = array('ID', 'NAME', 'PROPERTY_NEW_URL', 'PROPERTY_SITE_ID');

        //deb($arFilter);
        $rsElement = CIBlockElement::GetList(array('NAME'), $arFilter, false, false, $arSelect);

        while ($arElement = $rsElement -> Fetch()) {
            //deb($arElement);
            if(trim($arElement['NAME'])<>"")
            {

                $_SERVER['REQUEST_URI'] = $arElement['NAME'];
                $requestUri = urldecode($_SERVER['REQUEST_URI']);
                $parseUrl = parse_url($requestUri);

                $pathInfo = pathinfo($parseUrl['path']);
                if(empty($pathInfo['extension']))
                {
                    $parseUrl['path'] = $parseUrl['path'] . '/index.php';
                    $parseUrl['path'] = str_replace('//','/',$parseUrl['path']);
                }
                $APPLICATION->SetCurPage($parseUrl['path'], $parseUrl['query']);
                // функция в строке выше теряет геты
                // хотя должна их задавать, поэтому перепишем _REQUEST
                parse_str( $parseUrl['query'], $_REQUEST );

                include($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/urlrewrite.php');


                if(isset($parseUrl['path']))
                {
                    $absPath = getenv('DOCUMENT_ROOT').$parseUrl['path'];
                    $realPath = realpath($absPath);
                    $bitrixPath = realpath(getenv('DOCUMENT_ROOT').DIRECTORY_SEPARATOR.'bitrix');
                    if(strlen($realPath)>0 and is_dir($realPath) and strlen($bitrixPath)>0 and  strpos($realPath,$bitrixPath)===false)
                    {
                        $directoryIndex = 'index.php';
                        if(file_exists($path = $realPath. DIRECTORY_SEPARATOR . $directoryIndex))
                        {
                            CHTTP::SetStatus("200 OK");
                            include_once($path);
                            die();
                        }
                    }
                    if(strlen($realPath)>0 and is_file($realPath) and strlen($bitrixPath)>0 and  strpos($realPath,$bitrixPath)===false)
                    {
                        if(file_exists($realPath))
                        {
                            CHTTP::SetStatus("200 OK");
                            include_once($realPath);
                            die();
                        }
                    }
                }
            }
        }*/
    }

}
include($_SERVER["DOCUMENT_ROOT"]."/404.php");
?>