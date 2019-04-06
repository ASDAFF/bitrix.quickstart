<?php

/**
 * Created by PhpStorm.
 * User: darkfriend <hi@darkfriend.ru>
 * Date: 04.05.2017
 * Time: 18:51
 */
namespace Dev2fun\ImageCompress;

use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

//Loc::loadMessages(__FILE__);
IncludeModuleLangFile(__FILE__);

class ImageCompressTable extends Entity\DataManager {

    static $module_id = "dev2fun.imagecompress";

    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'b_d2f_imagecompress_files';
    }

    public static function getTableTitle()
    {
        return Loc::getMessage('DEV2FUN_IMAGECOMPRESS_REDIRECTS_TITLE');
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('SIZE_BEFORE'),
            new Entity\IntegerField('SIZE_AFTER'),
            new Entity\IntegerField('FILE_ID', array(
                'primary' => true,
            )),
            new Entity\ReferenceField(
                'FILE',
                'Bitrix\Main\FileTable',
                array(
                    '=this.FILE_ID' => 'ref.ID'
                )
            ),
        );
    }
}