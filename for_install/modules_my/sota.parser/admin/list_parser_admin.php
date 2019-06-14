<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
use Bitrix\Main\Entity\ExpressionField;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sota.parser/include.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sota.parser/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sota.parser/classes/mysql/list_parser.php");
//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/classes/general/rss_content_parser.php");
if (!CModule::IncludeModule('iblock')) return false;
IncludeModuleLangFile(__FILE__);
global $sota_DEMO;
$POST_RIGHT = $APPLICATION->GetGroupRight("sota.parser");
if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$ID = intval($ID);
$sTableID = "tbl_parser";
function CheckFilter()
{
    global $FilterArr, $lAdmin;
    foreach ($FilterArr as $f) global $$f;
    if (strlen(trim($find_last_executed_1)) > 0 || strlen(trim($find_last_executed_2)) > 0) {
        /*$date_1_ok = false;
        $date1_stm = MkDateTime(FmtDate($find_last_executed_1,"D.M.Y"),"d.m.Y");
        $date2_stm = MkDateTime(FmtDate($find_last_executed_2,"D.M.Y")." 23:59","d.m.Y H:i");
        if (!$date1_stm && strlen(trim($find_last_executed_1))>0)
            $lAdmin->AddFilterError(GetMessage("rub_wrong_generation_from"));
        else $date_1_ok = true;
        if (!$date2_stm && strlen(trim($find_last_executed_2))>0)
            $lAdmin->AddFilterError(GetMessage("rub_wrong_generation_till"));
        elseif ($date_1_ok && $date2_stm <= $date1_stm && strlen($date2_stm)>0)
            $lAdmin->AddFilterError(GetMessage("rub_wrong_generation_from_till"));*/
    }
    //return count($lAdmin->arFilterErrors)==0;
    return true;
}

$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$FilterArr = Array(
    "find",
    "find_id",
    "find_type",
    "find_name",
    "find_timestamp_1",
    "find_rss",
    "find_active",
    "find_iblock_id",
    "find_section_id",
    "find_encoding",
    "find_start_last_time_1",
    "find_show_sp"
);
$parentID = 0;
if (isset($_REQUEST["parent"]) && $_REQUEST["parent"]) {
    $parentID = $_REQUEST["parent"];
}

$lAdmin->InitFilter($FilterArr);

if (CheckFilter()) {
    $arFilter = array(
        "ID" => ($find != "" && $find_type == "id" ? $find : $find_id),
        "TYPE" => $find_main_type,
        "TIMESTAMP_1" => $find_timestamp_1,
        "NAME" => $find_name,
        "RSS" => $find_rss,
        "ACTIVE" => $find_active,
        "IBLOCK_ID" => $find_iblock_id,
        "SECTION_ID" => $find_section_id,
        "ENCODING" => $find_from,
        "START_AGENT" => $find_start_agent,
        "TIME_AGENT" => $find_time_agent,
        "START_LAST_TIME_1" => $find_start_last_time_1,
        "CATEGORY_ID" => $parentID
    );
}

if ($lAdmin->EditAction() && $POST_RIGHT == "W") {
    foreach ($FIELDS as $ID => $arFields) {
        if (!$lAdmin->IsUpdated($ID))
            continue;
        $DB->StartTransaction();
        $ID = IntVal($ID);
        $ob = new SotaParserContent;
        if (!$ob->Update($ID, $arFields)) {
            $lAdmin->AddUpdateError(GetMessage("parser_save_err") . $ID . ": " . $ob->LAST_ERROR, $ID);
            $DB->Rollback();
        }
        $DB->Commit();
    }
}

if (($arID = $lAdmin->GroupAction()) && $POST_RIGHT == "W") {
    if ($_REQUEST['action_target'] == 'selected') {
        $cData = new SotaParserContent;
        //$rsData = $cData->GetList(array($by=>$order), $arFilter);
        $rsData = $cData->GetMixedList(array($by => $order), $arFilter);
        while ($arRes = $rsData->Fetch())
            $arID[] = $arRes['ID'];
    }

    foreach ($arID as $ID) {
        if (strlen($ID) <= 0)
            continue;
        $TYPE = substr($ID, 0, 1);
        $ID = intval(substr($ID, 1));

        switch ($_REQUEST['action']) {
            case "delete":
                @set_time_limit(0);
                $DB->StartTransaction();
                if ($TYPE == "P") {
                    if (!SotaParserContent::Delete($ID)) {
                        $DB->Rollback();
                        $lAdmin->AddGroupError(GetMessage("parser_del_err"), $ID);
                    }
                }
                if ($TYPE == "S") {
                    if (!SotaParserSectionTable::Delete($ID)) {
                        $DB->Rollback();
                        $lAdmin->AddGroupError(GetMessage("parser_del_err"), $ID);
                    }
                }

                $DB->Commit();
                break;
            case "activate":
            case "deactivate":
                $cData = new SotaParserContent;
                if ($TYPE == "P" && ($rsData = $cData->GetByID($ID)) && ($arFields = $rsData->Fetch())) {
                    $arFields["ACTIVE"] = ($_REQUEST['action'] == "activate" ? "Y" : "N");
                    unset($arFields["TIME_AGENT"]);
                    unset($arFields["TIME_AGENT"]);
                    unset($arFields["START_LAST_TIME_X"]);
                    unset($arFields["TIMESTAMP_X"]);
                    if (!$cData->Update($ID, $arFields))
                        $lAdmin->AddGroupError(GetMessage("parser_save_error") . $cData->LAST_ERROR, $ID);
                } elseif ($TYPE == "S") {
                    $arFields["ACTIVE"] = ($_REQUEST['action'] == "activate" ? "Y" : "N");
                    if (!SotaParserSectionTable::Update($ID, $arFields))
                        $lAdmin->AddGroupError(GetMessage("parser_save_error") . $cData->LAST_ERROR, $ID);
                } else
                    $lAdmin->AddGroupError(GetMessage("parser_save_error") . " " . GetMessage("rub_no_rubric"), $ID);
                break;

        }
    }
}

$lAdmin->AddHeaders(array(
    array("id" => "ID",
        "content" => "ID",
        "sort" => "id",
        "align" => "right",
        "default" => true,
    ),
    array("id" => "NAME",
        "content" => GetMessage("parser_name"),
        "sort" => "name",
        "default" => true,
    ),
    array("id" => "TYPE",
        "content" => GetMessage("parser_type"),
        "sort" => "type",
        "default" => true,
    ),
    array("id" => "TIMESTAMP_X",
        "content" => GetMessage("parser_updated"),
        "sort" => "timestamp",
        "default" => true,
    ),
    array("id" => "RSS",
        "content" => GetMessage("parser_rss"),
        "sort" => "rss",
        "default" => true,
    ),
    array("id" => "SORT",
        "content" => GetMessage("parser_sort"),
        "sort" => "sort",
        "default" => true,
    ),
    array("id" => "ACTIVE",
        "content" => GetMessage("parser_active"),
        "sort" => "active",
        "default" => true,
    ),
    array("id" => "IBLOCK_ID",
        "content" => GetMessage("parser_iblock_id"),
        "sort" => "iblock_id",
        "default" => false,
    ),
    array("id" => "SECTION_ID",
        "content" => GetMessage("parser_section_id"),
        "sort" => false,
        "default" => false,
    ),
    array("id" => "SELECTOR",
        "content" => GetMessage("parser_selector"),
        "sort" => "selector",
        "default" => false,
    ),
    array("id" => "ENCODING",
        "content" => GetMessage("parser_encoding"),
        "sort" => "encoding",
        "default" => true,
    ),

    array("id" => "START_AGENT",
        "content" => GetMessage("parser_start_agent"),
        "sort" => "start_agent",
        "default" => true,
    ),
    array("id" => "TIME_AGENT",
        "content" => GetMessage("parser_time_agent"),
        "sort" => "time_agent",
        "default" => false,
    ),

    array("id" => "START_LAST_TIME_X",
        "content" => GetMessage("parser_start_last_time"),
        "sort" => "start_last_time",
        "default" => true,
    ),
));

$cData = new SotaParserContent;
//$rsData = $cData->GetList(array($by=>$order), $arFilter);
//printr($arFilter);
$show = "all";
if (isset($_REQUEST["show_sp"]) && $_REQUEST["show_sp"] == "all") {
    unset($arFilter["CATEGORY_ID"]);
    $show = "all";
} elseif (isset($_REQUEST["show_sp"]) && $_REQUEST["show_sp"] == "section") {
    $show = "section";
    unset($arFilter["CATEGORY_ID"]);
} elseif (isset($_REQUEST["show_sp"]) && $_REQUEST["show_sp"] == "parser") {
    $show = "parser";
    unset($arFilter["CATEGORY_ID"]);
}

$rsData = $cData->GetMixedList(array($by => $order), $arFilter, $show);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("parser_nav")));
$rsIBlock = CIBlock::GetList(Array("name" => "asc"), Array("ACTIVE" => "Y"));
while ($arr = $rsIBlock->Fetch()) {
    $arIBlock[$arr["ID"]] = "[" . $arr["ID"] . "] " . $arr["NAME"];
    $arIBlockFilter['REFERENCE'][] = "[" . $arr["ID"] . "] " . $arr["NAME"];
    $arIBlockFilter['REFERENCE_ID'][] = $arr["ID"];
}


$arShowSP['REFERENCE'] = array(GetMessage("parser_show_sp_tree"), GetMessage("parser_show_sp_all"), GetMessage("parser_show_sp_section"), GetMessage("parser_show_sp_parser"));
$arShowSP['REFERENCE_ID'] = array("tree", "all", "section", "parser");


while ($arRes = $rsData->NavNext(true, "f_")):
    $row =& $lAdmin->AddRow($f_T . $f_ID, $arRes);
    if ($f_T == "S") {
        $row->AddViewField("NAME", '<a href="list_parser_admin.php?parent=' . $f_ID . '&lang=' . LANG . '" class="adm-list-table-icon-link" title="' . GetMessage("IBLIST_A_LIST") . '"><span class="adm-submenu-item-link-icon adm-list-table-icon iblock-section-icon"></span><span class="adm-list-table-link">' . $f_NAME . '</span></a>');
    } else {
        $row->AddViewField("NAME", '<a href="parser_edit.php?ID=' . $f_ID . '&parent=' . $parentID . '&lang=' . LANG . '" title="' . GetMessage("parser_act_edit") . '">' . $f_NAME . '</a>');
    }


    $row->AddInputField("NAME", array("size" => 20));

    $row->AddViewField("TYPE", $f_TYPE);

    $row->AddViewField("RSS", '<a href="parser_edit.php?ID=' . $f_ID . '&amp;lang=' . LANG . '" title="' . GetMessage("parser_act_edit") . '">' . $f_RSS . '</a>');
    $row->AddInputField("RSS", array("size" => 20));

    $row->AddInputField("SORT", array("size" => 20));

    $row->AddCheckField("ACTIVE");

    $row->AddSelectField("IBLOCK_ID", $arIBlock);
    $row->AddSelectField("ENCODING", array('utf-8' => 'utf-8', 'windows-1251' => 'windows-1251'));
    $row->AddCheckField("START_AGENT");
    $row->AddInputField("TIME_AGENT", array("size" => 20));

    $arActions = Array();

    if ($POST_RIGHT == "W" && $f_T == "P")
        $arActions[] = array(
            "ICON" => "edit",
            "DEFAULT" => true,
            "TEXT" => GetMessage("parser_act_edit"),
            "ACTION" => $lAdmin->ActionRedirect("parser_edit.php?parent=" . $parentID . "&ID=" . $f_ID)
        );
    if ($POST_RIGHT == "W" && $f_T == "P")
        $arActions[] = array(
            "ICON" => "delete",
            "TEXT" => GetMessage("parser_act_del"),
            "ACTION" => "if(confirm('" . GetMessage("parser_act_del_conf") . "')) " . $lAdmin->ActionDoGroup("P" . $f_ID, "delete")
        );
    if ($POST_RIGHT == "W" && $f_T == "P")
        $arActions[] = array(
            "ICON" => "copy",
            "DEFAULT" => true,
            "TEXT" => GetMessage("parser_act_copy"),
            "ACTION" => $lAdmin->ActionRedirect("parser_edit.php?copy=" . $f_ID)
        );

    if ($POST_RIGHT == "W" && $f_T == "S")
        $arActions[] = array(
            "ICON" => "edit",
            "DEFAULT" => true,
            "TEXT" => GetMessage("parser_act_edit"),
            "ACTION" => $lAdmin->ActionRedirect("parser_section_edit.php?parent=" . $parentID . "&ID=" . $f_ID)
        );
    if ($POST_RIGHT == "W" && $f_T == "S")
        $arActions[] = array(
            "ICON" => "delete",
            "TEXT" => GetMessage("parser_act_del"),
            "ACTION" => "if(confirm('" . GetMessage("parser_act_del_conf") . "')) " . $lAdmin->ActionDoGroup("S" . $f_ID, "delete")
        );
    if ($POST_RIGHT == "W" && $f_T == "S")
        $arActions[] = array(
            "ICON" => "copy",
            "DEFAULT" => true,
            "TEXT" => GetMessage("parser_act_copy"),
            "ACTION" => $lAdmin->ActionRedirect("parser_section_edit.php?copy=" . $f_ID)
        );

    $arActions[] = array("SEPARATOR" => true);

    /*if($POST_RIGHT=="W")
        $arActions[] = array(
            "ICON"=>"",
            "TEXT"=>GetMessage("parser_act_start"),
            "ACTION"=>"if(confirm('".GetMessage("parser_conf")."')) window.location='".$APPLICATION->GetCurPage()."?ID=".$f_ID."&action=parser&lang=".LANG."&".bitrix_sessid_get()."'"
        );*/

    if (is_set($arActions[count($arActions) - 1], "SEPARATOR"))
        unset($arActions[count($arActions) - 1]);
    $row->AddActions($arActions);

endwhile;

$lAdmin->AddFooter(
    array(
        array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value" => $rsData->SelectedRowsCount()),
        array("counter" => true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"),
    )
);
$lAdmin->AddGroupActionTable(Array(
    "delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"), // удалить выбранные элементы
    "activate" => GetMessage("MAIN_ADMIN_LIST_ACTIVATE"), // активировать выбранные элементы
    "deactivate" => GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"), // деактивировать выбранные элементы
));

$aContext = array(
    array(
        "TEXT" => GetMessage("SOTA_PARSER_MAIN_ADD"),
        "LINK" => "parser_edit.php?parent=" . $parentID . "&lang=" . LANG,
        "TITLE" => GetMessage("PARSER_ADD_TITLE"),
        "ICON" => "btn_new",
    ),
    array(
        "TEXT" => GetMessage("SOTA_PARSER_SECTION_MAIN_ADD"),
        "LINK" => "parser_section_edit.php?parent=" . $parentID . "&lang=" . LANG,
        "TITLE" => GetMessage("PARSER_SECTION_ADD_TITLE"),
        "ICON" => "btn_sect_new",
    ),
);
$lAdmin->AddAdminContextMenu($aContext);

// и прикрепим его к списку
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("post_title"));

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$oFilter = new CAdminFilter(
    $sTableID . "_filter",
    array(
        "id" => GetMessage("PARSER_F_ID"),
        "timestamp" => GetMessage("PARSER_F_TIMESTAMP"),
        "name" => GetMessage("PARSER_F_NAME"),
        "type" => GetMessage("PARSER_F_TYPE"),
        "rss" => GetMessage("PARSER_F_RSS"),
        "active" => GetMessage("PARSER_F_ACTIVE"),
        "iblock_id" => GetMessage("PARSER_F_IBLOCK_ID"),
        "section_id" => GetMessage("PARSER_F_SECTION_ID"),
        "encoding" => GetMessage("PARSER_F_ENCODING"),
        "start_agent" => GetMessage("PARSER_F_START_AGENT"),
        "time_agent" => GetMessage("PARSER_F_TIME_AGENT"),
        "start_last_time" => GetMessage("PARSER_F_START_LAST_TIME"),
        "show_s_p" => GetMessage("PARSER_SHOW_SECTION_PARSER")
    )
);
if ($sota_DEMO == 2) CAdminMessage::ShowMessage(array("MESSAGE" => GetMessage("parser_demo")));
if ($sota_DEMO == 3) CAdminMessage::ShowMessage(array("MESSAGE" => GetMessage("parser_demo_end")));

?>
<form name="find_form" method="get" action="<? echo $APPLICATION->GetCurPage(); ?>">
    <?
    $oFilter->Begin();
    ?>
    <tr>
        <td><b><?= GetMessage("PARSER_FIND") ?>:</b></td>
        <td>
            <input type="text" size="25" name="find" value="<? echo htmlspecialchars($find) ?>"
                   title="<?= GetMessage("POST_FIND_TITLE") ?>">
            <?
            $arr = array(
                "reference" => array(
                    GetMessage("PARSER_F_ID"),
                    GetMessage("PARSER_F_NAME"),
                    GetMessage("PARSER_F_RSS"),
                ),
                "reference_id" => array(
                    "id",
                    "name",
                    "rss",
                )
            );
            echo SelectBoxFromArray("find_type", $arr, $find_type, "", "");
            ?>
        </td>
    </tr>
    <tr>
        <td><?= GetMessage("PARSER_F_ID") ?>:</td>
        <td>
            <input type="text" name="find_id" size="47" value="<? echo htmlspecialchars($find_id) ?>">
            &nbsp;<?= ShowFilterLogicHelp() ?>
        </td>
    </tr>
    <tr>
        <td><? echo GetMessage("PARSER_F_TIMESTAMP") . " (" . FORMAT_DATE . "):" ?></td>
        <td><? echo CalendarPeriod("find_timestamp_1", $find_timestamp_1, "find_timestamp_2", $find_timestamp_2, "find_form", "Y") ?></td>
    </tr>
    <tr>
        <td><? echo GetMessage("PARSER_F_NAME") ?>:</td>
        <td><input type="text" name="find_name" size="47"
                   value="<? echo htmlspecialchars($find_name) ?>">&nbsp;<?= ShowFilterLogicHelp() ?></td>
    </tr>
    <tr>
        <td><?= GetMessage("PARSER_F_TYPE") ?>:</td>
        <td>
            <?
            $arrType = array(
                "reference" => array(
                    "rss",
                    "page",
                ),
                "reference_id" => array(
                    "rss",
                    "page",
                )
            );
            echo SelectBoxFromArray("find_main_type", $arrType, "", GetMessage("MAIN_ALL"), "");
            ?>
        </td>
    </tr>
    <tr>
        <td><? echo GetMessage("PARSER_F_RSS") ?>:</td>
        <td><input type="text" name="find_rss" size="47"
                   value="<? echo htmlspecialchars($find_rss) ?>">&nbsp;<?= ShowFilterLogicHelp() ?></td>
    </tr>
    <tr>
        <td><?= GetMessage("PARSER_F_ACTIVE") ?>:</td>
        <td><?
            $arr = array("reference" => array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), "reference_id" => array("Y", "N"));
            echo SelectBoxFromArray("find_active", $arr, htmlspecialchars($find_active), GetMessage("MAIN_ALL"));
            ?></td>
    </tr>
    <tr>
        <td><?= GetMessage("PARSER_F_IBLOCK_ID") ?>:</td>
        <td>

            <?
            echo SelectBoxFromArray("find_iblock_id", $arIBlockFilter, "", GetMessage("MAIN_ALL"), "");
            ?>
        </td>
    </tr>
    <tr>
        <td><?= GetMessage("PARSER_F_SECTION_ID") ?>:</td>
        <td>
            <input type="text" name="find_section_id" size="47" value="<? echo htmlspecialchars($find_section_id) ?>">
            &nbsp;<?= ShowFilterLogicHelp() ?>
        </td>
    </tr>
    <tr>
        <td><?= GetMessage("PARSER_F_ENCODING") ?>:</td>
        <td>
            <?
            $arr = array(
                "reference" => array(
                    "utf-8",
                    "windows-1251",
                ),
                "reference_id" => array(
                    "utf-8",
                    "windows-1251",
                )
            );
            echo SelectBoxFromArray("find_encoding", $arr, "", GetMessage("MAIN_ALL"), "");
            ?>
        </td>
    </tr>
    <tr>
        <td><?= GetMessage("PARSER_F_START_AGENT") ?>:</td>
        <td><?
            $arr = array("reference" => array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), "reference_id" => array("Y", "N"));
            echo SelectBoxFromArray("find_active", $arr, htmlspecialchars($find_start_agent), GetMessage("MAIN_ALL"));
            ?></td>
    </tr>
    <tr>
        <td><? echo GetMessage("PARSER_F_TIME_AGENT") ?>:</td>
        <td><input type="text" name="find_time_agent" size="10" value="<? echo htmlspecialchars($find_time_agent) ?>">&nbsp;<?= ShowFilterLogicHelp() ?>
        </td>
    </tr>
    <tr>
        <td><? echo GetMessage("PARSER_F_START_LAST_TIME") . " (" . FORMAT_DATE . "):" ?></td>
        <td><? echo CalendarPeriod("find_start_last_time_1", $find_start_last_time_1, "find_start_last_time_2", $find_start_last_time_2, "find_form", "Y") ?></td>
    </tr>
    <tr>
        <td><?= GetMessage("PARSER_SHOW_SECTION_PARSER") ?>:</td>
        <td>

            <?
            echo SelectBoxFromArray("show_sp", $arShowSP, $find_show_sp, "", "");
            ?>
        </td>
    </tr>

    <?
    $oFilter->Buttons(array("table_id" => $sTableID, "url" => $APPLICATION->GetCurPage(), "form" => "find_form"));
    $oFilter->End();
    ?>
    <input type="hidden" name="parent" value="<?= $_REQUEST["parent"] ?>"/>
</form>

<?
//******************************
// Send message and show progress
//******************************
if (isset($_REQUEST['parser_end']) && $_REQUEST['parser_end'] == 1 && isset($_REQUEST['parser_id']) && $_REQUEST['parser_id'] > 0) {
    if (isset($_GET['SUCCESS'][0])) {
        foreach ($_GET['SUCCESS'] as $success) CAdminMessage::ShowMessage(array("MESSAGE" => $success, "TYPE" => "OK"));
    }
    if (isset($_GET['ERROR'][0])) {
        foreach ($_GET['ERROR'] as $error) CAdminMessage::ShowMessage($error);
    }

}
if (isset($_REQUEST['action']) && $_REQUEST['action'] == "parser"):
    $parser = SotaParserContent::GetByID($_REQUEST['ID']);
    if (!$parser->ExtractFields("sota_"))
        $ID = 0;

    if ($ID > 0) {
        $rssParser = new RssContentParser();
        $result = $rssParser->startParser();
        if (isset($result[SUCCESS][0]))
            foreach ($result[SUCCESS] as $i => $success) {
                $resultUrl .= "&SUCCESS[" . $i . "]=" . urlencode($success);
            }
        if (isset($result[ERROR][0]))
            foreach ($result[ERROR] as $i => $error) {
                $resultUrl .= "&ERROR[" . $i . "]=" . urlencode($error);
            }
        LocalRedirect($APPLICATION->GetCurPageParam("parser_end=1&parser_id=" . $ID . $resultUrl, array("action")));
    }

endif; ?>

<? $lAdmin->DisplayList(); ?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>
