<?
$iModuleID = "mibix.yamexport";
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage('MIBIX_INSTRUCTION_TITLE'));

//Проверка прав
$POST_RIGHT = $APPLICATION->GetGroupRight($iModuleID);
if($POST_RIGHT=="D")
{
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin.php');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$iModuleID."/include.php");

$image_path = '/bitrix/images/'.$iModuleID.'/instr/';
$site = 'http://'.$_SERVER['HTTP_HOST'];
?>
    <style>
        .mibix_ins_page {
            max-width: 1000px;
        }
        .mibix_ins_page img {
            border: 1px solid #95B3B9;
            max-width: 1000px;
        }
        .mibix_ins_note {
            color: red;
        }
        .mibix_ins_note.bold {
            font-weight: bold;
        }
    </style>
    <div class="mibix_ins_page">
        <?=GetMessage('MIBIX_CONTENT_TEXT', array('#image_path#' => $image_path, '#site#' => $site));?>
    </div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>