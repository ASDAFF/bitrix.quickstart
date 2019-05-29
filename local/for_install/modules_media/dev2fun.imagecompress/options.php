<?
/**
* @author dev2fun (darkfriend)
* @copyright darkfriend
* @version 0.1.7
*/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use \Dev2fun\ImageCompress\Check;

if (!$USER->isAdmin()) {
    $APPLICATION->authForm('Nope');
}
$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();
$curModuleName = "dev2fun.imagecompress";
Loc::loadMessages($context->getServer()->getDocumentRoot()."/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);

$aTabs = array(
    array(
        "DIV" => "edit1",
        "TAB" => Loc::getMessage("MAIN_TAB_SET"),
        "ICON" => "main_settings",
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_SET")
    ),
//    array(
//        "DIV" => "edit2",
//        "TAB" => Loc::getMessage("MAIN_TAB_6"),
//        "ICON" => "main_settings",
//        "TITLE" => Loc::getMessage("MAIN_OPTION_REG")
//    ),
//    array(
//        "DIV" => "edit3",
//        "TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"),
//        "ICON" => "main_settings",
//        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")
//    ),
//    array("DIV" => "edit8", "TAB" => GetMessage("MAIN_TAB_8"), "ICON" => "main_settings", "TITLE" => GetMessage("MAIN_OPTION_EVENT_LOG")),
//    array("DIV" => "edit5", "TAB" => GetMessage("MAIN_TAB_5"), "ICON" => "main_settings", "TITLE" => GetMessage("MAIN_OPTION_UPD")),
//    array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "main_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
);

//$tabControl = new CAdminTabControl("tabControl", array(
//    array(
//        "DIV" => "edit1",
//        "TAB" => Loc::getMessage("MAIN_TAB_SET"),
//        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_SET"),
//    ),
//));

$tabControl = new CAdminTabControl("tabControl", $aTabs);

if ($request->isPost() && check_bitrix_sessid()) {

    if($request->getPost('test_module')) {
        $text = array();
        $error = false;
        if(!Check::isJPEGOptim()) {
            $text[] = Loc::getMessage('D2F_IMAGECOMPRESS_ERROR_CHECK_NOFOUND',array('#MODULE#'=>'jpegoptim'));
        }
        if(!Check::isPNGOptim()) {
            $text[] = Loc::getMessage('D2F_IMAGECOMPRESS_ERROR_CHECK_NOFOUND',array('#MODULE#'=>'optipng'));
        }
        if(!$text) {
            $text = Loc::getMessage("D2F_COMPRESS_OPTIONS_TESTED");
        } else {
            $error = true;
            $text = implode (
                PHP_EOL,
                array_merge(
                    array(Loc::getMessage("D2F_COMPRESS_OPTIONS_NO_TESTED")),
                    $text
                )
            );
        }
        CAdminMessage::showMessage(array(
            "MESSAGE" => $text,
            "TYPE" => (!$error?'OK':'ERROR'),
        ));
    } else {
        if($pthJpeg = $request->getPost('path_to_jpegoptim')) {
            $pthJpeg = rtrim($pthJpeg,'/');
            Option::set($curModuleName,'path_to_jpegoptim',$pthJpeg);
        }

        if($pthPng = $request->getPost('path_to_optipng')) {
            $pthPng = rtrim($pthPng,'/');
            Option::set($curModuleName,'path_to_optipng',$pthPng);
        }

        $enableElement = $request->getPost('enable_element');
        Option::set($curModuleName,'enable_element',($enableElement?'Y':'N'));

        $enableSection = $request->getPost('enable_section');
        Option::set($curModuleName,'enable_section',($enableSection?'Y':'N'));

        $enableResize = $request->getPost('enable_resize');
        Option::set($curModuleName,'enable_resize',($enableResize?'Y':'N'));

        $enableSave = $request->getPost('enable_save');
        Option::set($curModuleName,'enable_save',($enableSave?'Y':'N'));

        Option::set($curModuleName,'jpegoptim_compress',$request->getPost('jpegoptim_compress'));
        Option::set($curModuleName,'optipng_compress',$request->getPost('optipng_compress'));

        $jpegCompress = $request->getPost('jpeg_progressive');
        Option::set($curModuleName,'jpeg_progressive',($jpegCompress?'Y':'N'));

        CAdminMessage::showMessage(array(
            "MESSAGE" => Loc::getMessage("D2F_COMPRESS_REFERENCES_OPTIONS_SAVED"),
            "TYPE" => "OK",
        ));
    }
}
$tabControl->begin();
?>

<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <?php
    echo bitrix_sessid_post();
    $tabControl->beginNextTab();
    ?>
<!--    <tr class="heading">-->
<!--        <td colspan="2"><b>--><?//echo GetMessage("D2F_COMPRESS_HEADER_SETTINGS")?><!--</b></td>-->
<!--    </tr>-->
    <tr>
        <td width="40%">
            <label for="path_to_jpegoptim">
                <?=Loc::getMessage("D2F_COMPRESS_REFERENCES_PATH_JPEGOPTI") ?>:
            </label>
        </td>
        <td width="60%">
            <input type="text"
                   size="50"
                   name="path_to_jpegoptim"
                   value="<?=Option::get($curModuleName, "path_to_jpegoptim", '/usr/bin');?>"
            />
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label for="path_to_optipng">
                <?=Loc::getMessage("D2F_COMPRESS_REFERENCES_PATH_PNGOPTI") ?>:
            </label>
        </td>
        <td width="60%">
            <input type="text"
                   size="50"
                   name="path_to_optipng"
                   value="<?=Option::get($curModuleName, "path_to_optipng", '/usr/bin');?>"
            />
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label for="enable_element">
                <?=Loc::getMessage("D2F_COMPRESS_REFERENCES_ENABLE_ELEMENT") ?>:
            </label>
        </td>
        <td width="60%">
            <input type="checkbox"
                   name="enable_element"
                   value="Y"
                   <?
                   if(Option::get($curModuleName, "enable_element")=='Y') {
                       echo 'checked';
                   }
                   ?>
            />
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label for="enable_section">
                <?=Loc::getMessage("D2F_COMPRESS_REFERENCES_ENABLE_SECTION") ?>:
            </label>
        </td>
        <td width="60%">
            <input type="checkbox"
                   name="enable_section"
                   value="Y"
                <?
                if(Option::get($curModuleName, "enable_section")=='Y') {
                    echo 'checked';
                }
                ?>
            />
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label for="enable_resize">
                <?=Loc::getMessage("D2F_COMPRESS_REFERENCES_ENABLE_RESIZE") ?>:
            </label>
        </td>
        <td width="60%">
            <input type="checkbox"
                   name="enable_resize"
                   value="Y"
                <?
                if(Option::get($curModuleName, "enable_resize")=='Y') {
                    echo 'checked';
                }
                ?>
            />
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label for="enable_save">
                <?=Loc::getMessage("D2F_COMPRESS_REFERENCES_ENABLE_SAVE") ?>:
            </label>
        </td>
        <td width="60%">
            <input type="checkbox"
                   name="enable_save"
                   value="Y"
                <?
                if(Option::get($curModuleName, "enable_save")=='Y') {
                    echo 'checked';
                }
                ?>
            />
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label for="jpegoptim_compress">
                <?=Loc::getMessage("D2F_COMPRESS_REFERENCES_JPEG_COMPRESS") ?>:
            </label>
        </td>
        <td width="60%">
            <select name="jpegoptim_compress">
                <?
                $jpgCompress = Option::get($curModuleName, "jpegoptim_compress", '80');
                for($i=0;$i<=100;$i+=5){ ?>
                    <option value="<?=$i?>" <?=($i==$jpgCompress?'selected':'')?>><?=$i?></option>
                <? } ?>
            </select>
<!--            <input type="text"-->
<!--                   name="jpegoptim_compress"-->
<!--                   value="--><?//=Option::get($curModuleName, "jpegoptim_compress", '80');?><!--"-->
<!--            />-->
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label for="enable_element">
                <?=Loc::getMessage("D2F_COMPRESS_REFERENCES_JPEG_PROGRESSIVE") ?>:
            </label>
        </td>
        <td width="60%">
            <input type="checkbox"
                   name="jpeg_progressive"
                   value="Y"
                <?
                if(Option::get($curModuleName, "jpeg_progressive")=='Y') {
                    echo 'checked';
                }
                ?>
            />
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label for="optipng_compress">
                <?=Loc::getMessage("D2F_COMPRESS_REFERENCES_PNG_COMPRESS") ?>:
            </label>
        </td>
        <td width="60%">
            <select name="optipng_compress">
                <?
                $pngCompress = Option::get($curModuleName, "optipng_compress", '3');
                for($i=1;$i<=7;$i++){ ?>
                    <option value="<?=$i?>" <?=($i==$pngCompress?'selected':'')?>><?=$i?></option>
                <? } ?>
            </select>
        </td>
    </tr>

    <?php
    $tabControl->buttons();
    ?>
    <input type="submit"
           name="save"
           value="<?=Loc::getMessage("MAIN_SAVE") ?>"
           title="<?=Loc::getMessage("MAIN_OPT_SAVE_TITLE") ?>"
           class="adm-btn-save"
    />
    <input type="submit"
           name="test_module"
           value="<?=Loc::getMessage("D2F_COMPRESS_REFERENCES_TEST_BTN") ?>"
    />
    <?/* ?>
    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
    <? */ ?>
    <? $tabControl->end(); ?>
</form>