<?

IncludeModuleLangFile(__FILE__);

class CCetacs_sd {

    function OnAdminListDisplay(&$list) {
        global $APPLICATION;

        $AJAX_REDIRECT = true;

        $MODE = false;
        if (strpos($list->table_id, "tbl_iblock_list_") !== false)
            $MODE = "M";
        if (strpos($list->table_id, "tbl_iblock_element_") !== false)
            $MODE = "E";
        if (strpos($list->table_id, "tbl_iblock_section_") !== false)
            $MODE = "S";
        if ($list->table_id == "tbl_user")
            $MODE = "USER";


        if ($MODE) {
            $list->arActions["cetacs_stepdelete"] = array(
                "value" => "cetacs_stepdelete",
                "name" => GetMessage("MENU_ITEM_NAME"),
            );
        } else
            return;

        //если выбрана команда "удалить по шагам"
        if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] == "cetacs_stepdelete") {
            unset($_SESSION["cetacs_stepdelete"][$list->table_id]);

            if ($_POST["action_target"] == "selected") {
                //если отмечена опция, "для всех"
                $_SESSION["cetacs_stepdelete"][$list->table_id]["COUNT"] = $GLOBALS["rsData"]->SelectedRowsCount();
            } else {
                $_SESSION["cetacs_stepdelete"][$list->table_id]["COUNT"] = count($GLOBALS["arID"]);
            }
            $_SESSION["cetacs_stepdelete"][$list->table_id]["ID"] = $GLOBALS["arID"];
            $_SESSION["cetacs_stepdelete"][$list->table_id]["CURRENT"] = 0;
            $_SESSION["cetacs_stepdelete"][$list->table_id]["TIME"] = microtime(true);
            $_SESSION["cetacs_stepdelete"][$list->table_id]["ERRORS"] = array();

            CCetacs_sd::ShowMessageInit();

            $url = $APPLICATION->GetCurPageParam("cetacs_stepdelete=process&sessid=" . bitrix_sessid(), array("mode", "cetacs_stepdelete", "sessid"));
            CCetacs_sd::ShowRedirectScript($list->table_id, $url, false, $AJAX_REDIRECT);

            return;
        } elseif ($_REQUEST["cetacs_stepdelete"] == "process" &&
                is_array($_SESSION["cetacs_stepdelete"][$list->table_id])
                && check_bitrix_sessid()
        ) {
            CCetacs_sd::ProcessDelete($list, $MODE, $AJAX_REDIRECT);
        }
    }

    function ProcessDelete(&$list, $MODE, $AJAX_REDIRECT) {
        global $APPLICATION;

        $SESS = $_SESSION["cetacs_stepdelete"][$list->table_id];

        $STEP_TIME = COption::GetOptionInt("cetacs.stepdelete", "step_time", "");
        $STEP_DELAY = COption::GetOptionInt("cetacs.stepdelete", "step_delay", "");

        @set_time_limit(4800);
        $COUNT = $SESS["COUNT"];
        $CUR = $SESS["CURRENT"];

        if ($CUR < $COUNT) {
            if ($CUR == 0)
                CCetacs_sd::ShowMessageBegin();
            else
                CCetacs_sd::ShowMessageProcess($list);
        } else {
            CCetacs_sd::ShowMessageFinish($list);
            unset($_SESSION["cetacs_stepdelete"][$list->table_id]);
            return;
        }

        $time = microtime(true);
        $arCompeteId = array();
        $ERRORS = $_SESSION["cetacs_stepdelete"][$list->table_id]["ERRORS"];
        foreach ($SESS["ID"] as $ID) {
            if (strlen(trim($ID)) == 0)
                continue;
            switch ($MODE) {
                case "E":
                    if (!CIBlockElement::Delete($ID))
                        $ERRORS[] = GetMessage("ERROR_DELETE_ELEMENT") . " #$ID";
                    break;
                case "S":
                    if (!CIBlockSection::Delete($ID))
                        $ERRORS[] = GetMessage("ERROR_DELETE_ELEMENT") . " #$ID";
                    break;
                case "M":
                    if (strpos($ID, "E") !== false) {
                        $tID = substr($ID, 1);
                        if (!CIBlockElement::Delete($tID))
                            $ERRORS[] = GetMessage("ERROR_DELETE_ELEMENT") . " #$ID";
                    }
                    if (strpos($ID, "S") !== false) {
                        $tID = substr($ID, 1);
                        if (!CIBlockSection::Delete($tID))
                            $ERRORS[] = GetMessage("ERROR_DELETE_ELEMENT") . " #$ID";
                    }
                    break;
                case "USER":
                    if (!CUser::Delete($ID))
                        $ERRORS[] = GetMessage("ERROR_DELETE_ELEMENT") . " #$ID";
            }
//                sleep(1);
            $arCompeteId[] = $ID;
            $CUR++;
            $cur_time = microtime(true);
            if ($cur_time - $time > $STEP_TIME)
                break;
        }
        $_SESSION["cetacs_stepdelete"][$list->table_id]["CURRENT"] = $CUR;
        $_SESSION["cetacs_stepdelete"][$list->table_id]["ID"] = array_diff($SESS["ID"], $arCompeteId);

        $_SESSION["cetacs_stepdelete"][$list->table_id]["ERRORS"] = $ERRORS;

        $timout = 2000;
        if ($CUR < $COUNT) {
            $timout = $STEP_DELAY * 1000;
        }
        $url = $APPLICATION->GetCurPageParam("cetacs_stepdelete=process&sessid=" . bitrix_sessid(), array("mode", "cetacs_stepdelete", "sessid"));
        CCetacs_sd::ShowRedirectScript($list->table_id, $url, $timout, $AJAX_REDIRECT);
    }

    function ShowRedirectScript($table_id, $url, $delay = false, $AJAX_REDIRECT = true) {
        echo "<script>";
        if ($delay)
            echo "window.setTimeout(function(){";

        if ($AJAX_REDIRECT)
            echo "var tbl = new top.JCAdminList('$table_id');
            tbl.GetAdminList('$url');";
        else
            echo "top.jsUtils.Redirect(false,'$url');";

        if ($delay)
            echo "  },$delay);";
        echo "</script>";
    }

    function ShowMessageProcess(&$list) {
        if (!is_array($_SESSION["cetacs_stepdelete"][$list->table_id]))
            return false;
        $COUNT = $_SESSION["cetacs_stepdelete"][$list->table_id]["COUNT"];
        $CURRENT = $_SESSION["cetacs_stepdelete"][$list->table_id]["CURRENT"];
        $TIME = round(microtime(true) - $_SESSION["cetacs_stepdelete"][$list->table_id]["TIME"], 0);

        $msg = GetMessage("DELETING_PROCESS") . "...\n\n";
        $msg.=GetMessage("DELETED_RECORDS") . ": $CURRENT " . GetMessage("IZ") . " $COUNT\n";
        $msg.=GetMessage("TIME") . ": " . $TIME . " " . GetMessage("SEC");
        CAdminMessage::ShowNote($msg);
    }

    function ShowMessageFinish(&$list) {
        if (!is_array($_SESSION["cetacs_stepdelete"][$list->table_id]))
            return false;
        $COUNT = $_SESSION["cetacs_stepdelete"][$list->table_id]["COUNT"];
        $TIME = $_SESSION["cetacs_stepdelete"][$list->table_id]["TIME"];
        $ALL_TIME = round(microtime(true) - $TIME, 0);
        $ERRORS = $_SESSION["cetacs_stepdelete"][$list->table_id]["ERRORS"];


        if (count($ERRORS) == 0) {
            $msg = GetMessage("DONE") . "\n\n";
            $msg.=GetMessage("DELETED_RECORDS") . ": $COUNT\n";
            $msg.=GetMessage("TIME") . ": " . $ALL_TIME . " " . GetMessage("SEC");
            CAdminMessage::ShowNote($msg);
        } else {
            $COUNT_ERROR = count($ERRORS);
            $COUNT_OK = $COUNT - $COUNT_ERROR;
            $msg = GetMessage("DONE_WITH_ERRORS") . "\n\n";
            $msg.=GetMessage("DELETED_RECORDS") . ": $COUNT_OK\n";
            $msg.=GetMessage("DELETED_ERRORS") . ": $COUNT_ERROR\n";

            if (count($ERRORS) > 10) {
                $ERRORS = array_slice($ERRORS, 0, 10);
                $ERRORS[] = "...";
            }

            $message = new CAdminMessage(array(
                        "MESSAGE" => $msg,
                        "TYPE" => "ERROR",
                        "DETAILS" => "\n" . implode("\n", $ERRORS),
                        "HTML" => false
                    ));
            echo $message->Show();


//            CAdminMessage::ShowMessage($msg . "\n" . implode("\n", $ERRORS));
        }
    }

    function ShowMessageInit() {
        CAdminMessage::ShowNote(GetMessage("INIT_PROCESS"));
    }

    function ShowMessageBegin() {
        CAdminMessage::ShowNote(GetMessage("BEGIN_PROCESS"));
    }

}

?>