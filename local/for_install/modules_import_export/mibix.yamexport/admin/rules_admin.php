<?
$iModuleID = "mibix.yamexport";
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$iModuleID."/include.php");

// ��������� �������� ����
IncludeModuleLangFile(__FILE__);

// ������� ����� ������� �������� ������������ �� ������
$POST_RIGHT = $APPLICATION->GetGroupRight($iModuleID);
if($POST_RIGHT <= "D")
{
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$sTableID = "tbl_rules";
$oSort = new CAdminSorting($sTableID, "id", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

// �������� �������� �������
function CheckFilter()
{
    global $FilterArr, $lAdmin;
    foreach ($FilterArr as $f) global $$f;

    if(strlen(trim($find_update_1)) > 0 || strlen(trim($find_update_2)) > 0)
    {
        $date_1_ok = false;
        $date1_stm = MkDateTime(FmtDate($find_update_1,"D.M.Y"),"d.m.Y");
        $date2_stm = MkDateTime(FmtDate($find_update_2,"D.M.Y")." 23:59","d.m.Y H:i");
        if(!$date1_stm && strlen(trim($find_update_1)) > 0)
        {
            $lAdmin->AddFilterError(GetMessage("MIBIX_YAM_POST_WRONG_UPDATE_FROM"));
        }
        else
        {
            $date_1_ok = true;
        }
        if(!$date2_stm && strlen(trim($find_update_2)) > 0)
        {
            $lAdmin->AddFilterError(GetMessage("MIBIX_YAM_POST_WRONG_UPDATE_TILL"));
        }
        elseif($date_1_ok && $date2_stm <= $date1_stm && strlen($date2_stm) > 0)
        {
            $lAdmin->AddFilterError(GetMessage("MIBIX_YAM_POST_FROM_TILL_UPDATE"));
        }

    }
    if(strlen(trim($find_insert_1)) > 0 || strlen(trim($find_insert_2)) > 0)
    {
        $date_1_ok = false;
        $date1_stm = MkDateTime(FmtDate($find_insert_1,"D.M.Y"),"d.m.Y");
        $date2_stm = MkDateTime(FmtDate($find_insert_2,"D.M.Y")." 23:59","d.m.Y H:i");
        if(!$date1_stm && strlen(trim($find_insert_1)) > 0)
        {
            $lAdmin->AddFilterError(GetMessage("MIBIX_YAM_POST_WRONG_INSERT_FROM"));
        }
        else
        {
            $date_1_ok = true;
        }
        if(!$date2_stm && strlen(trim($find_insert_2)) > 0)
        {
            $lAdmin->AddFilterError(GetMessage("MIBIX_YAM_POST_WRONG_INSERT_TILL"));
        }
        elseif($date_1_ok && $date2_stm <= $date1_stm && strlen($date2_stm) > 0)
        {
            $lAdmin->AddFilterError(GetMessage("MIBIX_YAM_POST_FROM_TILL_INSERT"));
        }
    }
    return count($lAdmin->arFilterErrors) == 0;
}

// ������ �������� �������
$FilterArr = Array(
    "find",
    "find_type",
    "find_id",
    "find_name_rule",
    "find_active",
    "find_update_1",
    "find_update_2",
    "find_insert_1",
    "find_insert_2",
);

// �������������� ������
$lAdmin->InitFilter($FilterArr);

// ���� ��� �������� ������� ���������, ���������� ���
if(CheckFilter())
{
    $arFilter = Array(
        "ID"		=> ($find!="" && $find_type == "id"? $find : $find_id),
        "NAME_RULE"	=> ($find!="" && $find_type == "data_name"? $find : $find_name_rule),
        "NAME_DATA"	=> $find_name_data,
        "UPDATE_1"	=> $find_update_1,
        "UPDATE_2"	=> $find_update_2,
        "INSERT_1"	=> $find_insert_1,
        "INSERT_2"	=> $find_insert_2,
        "ACTIVE"	=> $find_active,
    );
}

// ��������� �������� ��� ����������
if($lAdmin->EditAction() && $POST_RIGHT == "W")
{
    // ������� �� ������ ���������� ���������
    foreach($FIELDS as $ID=>$arFields)
    {
        if(!$lAdmin->IsUpdated($ID)) continue;

        // �������� ��������� ������� ��������
        $DB->StartTransaction();
        $ID = IntVal($ID);
        $ob = new CMibixModelRules();
        if(!$ob->Update($ID, $arFields))
        {
            $lAdmin->AddUpdateError(GetMessage("MIBIX_YAM_POST_SAVE_ERROR").$ID.": ".$ob->LAST_ERROR, $ID);
            $DB->Rollback();
        }
        $DB->Commit();
    }
}

$strError = $strOk = "";

// ��������� ��������� � ��������� ��������
if(($arID = $lAdmin->GroupAction()) && $POST_RIGHT=="W")
{
    // ���� ������� "��� ���� ���������"
    if($_REQUEST['action_target']=='selected')
    {
        $cData = new CMibixModelRules();
        $rsData = $cData->GetList(array($by=>$order), $arFilter);
        while($arRes = $rsData->Fetch())
            $arID[] = $arRes['ID'];
    }

    // ������� �� ������ ���������
    foreach($arID as $ID)
    {
        if(strlen($ID)<=0) continue;
        $ID = IntVal($ID);

        // ��� ������� �������� �������� ��������� ��������
        switch($_REQUEST['action'])
        {
            // ��������
            case "delete":
                @set_time_limit(0);
                $DB->StartTransaction();
                if(!CMibixModelRules::Delete($ID))
                {
                    $DB->Rollback();
                    $lAdmin->AddGroupError(GetMessage("MIBIX_YAM_RA_DEL_ERR"), $ID);
                }
                $DB->Commit();
                break;
            // ���������/�����������
            case "activate":
            case "deactivate":
                $ob = new CMibixModelRules();
                $arFields = Array("active"=>($_REQUEST['action']=="activate"?"Y":"N"));
                if(!$ob->Update($ID, $arFields))
                    $lAdmin->AddGroupError(GetMessage("MIBIX_YAM_RA_SAVE_ERROR").$ob->LAST_ERROR, $ID);
                break;
        }
    }
}

// ������� ���������
$cData = new CMibixModelRules();
$rsData = $cData->GetList(array($by=>$order), $arFilter, array("nPageSize" => CAdminResult::GetNavSize($sTableID)));

// ����������� ������ � ��������� ������ CAdminResult
$rsData = new CAdminResult($rsData, $sTableID);

// ���������� CDBResult �������������� ������������ ���������.
$rsData->NavStart();

// �������� ����� ������������� ������� � �������� ������ $lAdmin
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("MIBIX_YAM_NAV")));

// ���������� ������ ��������� � ������ (��������)
$lAdmin->AddHeaders(array(
    array(
        "id"		=> "id", // ������������� �������
        "content"	=> "ID", // ��������� �������
        "sort"		=> "id", // �������� ��������� GET-������� ��� ����������
        "align"		=> "right", // ����� �� ������� �� ��������� ������������ � ������
        "default"	=> true,
    ),
    array(
        "id"		=> "date_insert",
        "content"	=> GetMessage("MIBIX_YAM_POST_DATE_INSERT"),
        "sort"		=> "date_insert",
        "default"	=> true,
    ),
    array(
        "id"		=> "name_rule",
        "content"	=> GetMessage("MIBIX_YAM_NAME_RULE"),
        "sort"		=> "name_rule",
        "default"	=> true,
    ),
    array(
        "id"		=> "name_data",
        "content"	=> GetMessage("MIBIX_YAM_NAME_DATA"),
        "sort"		=> "name_data",
        "default"	=> true,
    ),
    array(
        "id"		=> "active",
        "content"	=> GetMessage("MIBIX_YAM_ACT"),
        "sort"		=> "act",
        "default"	=> true,
    ),
    array(
        "id"		=> "date_update",
        "content"	=> GetMessage("MIBIX_YAM_POST_DATE_UPDATE"),
        "sort"		=> "date_update",
        "default"	=> false,
    ),
));

// �������� ������ ��������� � �������� ������
while($arRes = $rsData->NavNext(true, "f_"))
{
    // ������� ������. ��������� - ��������� ������ CAdminListRow
    $row =& $lAdmin->AddRow($f_id, $arRes);

    // ������������� ��� �����
    $row->AddInputField("name_rule", array("size"=>20));
    $row->AddViewField("name_rule", '<a href="mibix.yamexport_rules_edit.php?ID='.$f_id.'&lang='.LANG.'">'.$f_name_rule.'</a>');
    // ������������� ��� �������
    $row->AddCheckField("active");

    $arActions = Array();
    $arActions[] = array(
        "ICON"=>"edit",
        "DEFAULT"=>true,
        "TEXT"=>GetMessage("MIBIX_YAM_RA_UPD"),
        "ACTION"=>$lAdmin->ActionRedirect($iModuleID."_rules_edit.php?ID=".$f_id)
    );
    if ($POST_RIGHT>="W")
    {
        $arActions[] = array(
            "ICON"=>"delete",
            "TEXT"=>GetMessage("MIBIX_YAM_RA_DEL"),
            "ACTION"=>"if(confirm('".GetMessage("MIBIX_YAM_RA_DEL_CONF")."')) ".$lAdmin->ActionDoGroup($f_id, "delete")
        );
    }

    // ��������� ����������� ���� ��� ������
    $row->AddActions($arActions);
}

// ������ �������
$lAdmin->AddFooter(
    array(
        array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value" => $rsData->SelectedRowsCount()),
        array("counter" => true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"),
    )
);

// ��������� ��������
$lAdmin->AddGroupActionTable(Array(
    "activate" => GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
    "deactivate" => GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
    //"dublicate" => GetMessage("MAIN_ADMIN_LIST_DUBLICATE"),
    "delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
));

// ���������� ���� �� ������ ������ - �������� ������
$aContext = array(
    array(
        "TEXT" => GetMessage("MAIN_ADD"),
        "LINK" => $iModuleID."_rules_edit.php?lang=".LANG,
        "TITLE" => GetMessage("MIBIX_YAM_ADD_TITLE"),
        "ICON" => "btn_new",
    ),
);
// � ��������� ��� � ������
$lAdmin->AddAdminContextMenu($aContext);

// ��������� ��������������� ������ ������ ������
$lAdmin->CheckListMode();

// ��������� ��������� ��������
$APPLICATION->SetTitle(GetMessage("MIBIX_YAM_RA_TITLE"));

// ��������� ���������� ������ � ����� ������������ ����������������� �����
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// ������������� ������� � �������� � ���� ������ �����
$oFilter = new CAdminFilter(
    $sTableID."_filter",
    array(
        GetMessage("MIBIX_YAM_POST_F_ID"),
        GetMessage("MIBIX_YAM_POST_F_INSERT"),
        GetMessage("MIBIX_YAM_POST_F_UPDATE"),
        GetMessage("MIBIX_YAM_POST_F_NAME_RULE"),
        GetMessage("MIBIX_YAM_POST_F_NAME_DATA"),
        GetMessage("MIBIX_YAM_POST_F_ACTIVE"),
    )
);

// ����� ������ ������������ ����� �������
?>
    <form name="find_form" method="get" action="<?=$APPLICATION->GetCurPage();?>">
        <?$oFilter->Begin();?>
        <tr>
            <td><b><?=GetMessage("MIBIX_YAM_POST_F_FIND")?>:</b></td>
            <td>
                <input type="text" size="25" name="find" value="<?=htmlspecialchars($find)?>" title="<?=GetMessage("MIBIX_YAM_POST_F_FIND_TITLE")?>">
                <?
                $arr = array(
                    "reference" => array(
                        GetMessage("MIBIX_YAM_POST_F_NAME_RULE"),
                        GetMessage("MIBIX_YAM_POST_F_ID"),
                    ),
                    "reference_id" => array(
                        "name_rule",
                        "id",
                    )
                );
                echo SelectBoxFromArray("find_type", $arr, $find_type, "", "");
                ?>
            </td>
        </tr>
        <tr>
            <td><?=GetMessage("MIBIX_YAM_POST_F_ID")?>:</td>
            <td><input type="text" name="find_id" size="47" value="<?=htmlspecialchars($find_id)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
        </tr>
        <tr>
            <td><?=GetMessage("MIBIX_YAM_POST_F_INSERT")." (".FORMAT_DATE."):"?></td>
            <td><?=CalendarPeriod("find_insert_1", htmlspecialchars($find_insert_1), "find_insert_2", htmlspecialchars($find_insert_2), "find_form","Y")?></td>
        </tr>
        <tr>
            <td><?=GetMessage("MIBIX_YAM_POST_F_UPDATE")." (".FORMAT_DATE."):"?></td>
            <td><?=CalendarPeriod("find_update_1", htmlspecialchars($find_update_1), "find_update_2", htmlspecialchars($find_update_2), "find_form","Y")?></td>
        </tr>
        <tr>
            <td><?=GetMessage("MIBIX_YAM_POST_F_NAME_RULE")?>:</td>
            <td><input type="text" name="find_data_rule" size="47" value="<?=htmlspecialchars($find_data_rule)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
        </tr>
        <tr>
            <td><?=GetMessage("MIBIX_YAM_POST_F_NAME_DATA")?>:</td>
            <td><input type="text" name="find_data_name" size="47" value="<?=htmlspecialchars($find_data_name)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
        </tr>
        <tr>
            <td><?=GetMessage("MIBIX_YAM_POST_F_ACTIVE")?>:</td>
            <td><?
                $arr = array("reference"=>array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), "reference_id"=>array("Y","N"));
                echo SelectBoxFromArray("find_active", $arr, htmlspecialchars($find_active), GetMessage("MAIN_ALL"));
                ?></td>
        </tr>
        <?
        $oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
        $oFilter->End();
        ?>
    </form>

<?
// ������� ������� ������ ���������
$lAdmin->DisplayList();
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>