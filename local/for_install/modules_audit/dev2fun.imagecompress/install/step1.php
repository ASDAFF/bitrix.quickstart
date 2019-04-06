<?
/**
 * Created by PhpStorm.
 * User: darkfriend <hi@darkfriend.ru>
 * Date: 04.05.2017
 */
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
if(!check_bitrix_sessid()) return;

use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Config\Option;

IncludeModuleLangFile(__FILE__);

Loader::includeModule('main');
$moduleName = 'dev2fun.imagecompress';

Loader::registerAutoLoadClasses(
    $moduleName,
    array(
        'Dev2fun\ImageCompress\ImageCompressTable' => 'classes/general/ImageCompressTable.php',
        'Dev2fun\ImageCompress\AdminList' => 'lib/AdminList.php',
        'Dev2fun\ImageCompress\Check' => 'lib/Check.php',
        'Dev2fun\ImageCompress\Compress' => 'lib/Compress.php',
        "Dev2funImageCompress" => 'include.php'
    )
);
echo BeginNote();
echo Loc::getMessage('D2F_MODULE_IMAGECOMPRESS_STEP1_NOTES');
echo EndNote();
?>
<form action="<?= $APPLICATION->GetCurPageParam('STEP=2',array('STEP'))?>" method="post">
    <?= bitrix_sessid_post()?>
    <table width="400" border="0" class="table">
        <tr>
            <td>
                <label for="path_to_jpegoptim"><?= Loc::getMessage('D2F_COMPRESS_REFERENCES_PATH_JPEGOPTI')?>:</label>
            </td>
            <td>
                <input type="text" name="D2F_FIELDS[path_to_jpegoptim]" value="<?=Option::get($moduleName, "path_to_jpegoptim", '/usr/bin');?>">
            </td>
        </tr>
        <tr>
            <td>
                <label for="path_to_optipng"><?= Loc::getMessage('D2F_COMPRESS_REFERENCES_PATH_PNGOPTI')?>:</label>
            </td>
            <td>
                <input type="text" name="D2F_FIELDS[path_to_optipng]" value="<?=Option::get($moduleName, "path_to_optipng", '/usr/bin');?>">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" name="save" value="<?= Loc::getMessage('D2F_COMPRESS_REFERENCES_GOTO_INSTALL')?>">
            </td>
        </tr>
    </table>
</form>
