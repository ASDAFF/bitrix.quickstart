<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class SotaParserTmpTable extends Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'b_sota_parser_tmp';
    }

    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => "ID",
            ),
            'PARSER_ID' => array(
                'data_type' => 'integer',
                'title' => "PARSER_ID",
            ),
            'PRODUCT_ID' => array(
                'data_type' => 'integer',
                'title' => "PRODUCT_ID",
            ),
        );
    }
}

?>