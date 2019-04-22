<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
use Bitrix\Main\Entity\ExpressionField;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/shs.parser/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/shs.parser/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/shs.parser/lib/result_parser.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/shs.parser/lib/result_parser_product.php");

if(!CModule::IncludeModule('iblock')) return false;
IncludeModuleLangFile(__FILE__);
global $shs_DEMO, $DB;
$POST_RIGHT = $APPLICATION->GetGroupRight("shs.parser");
if($POST_RIGHT=="D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
//$arrErrors[] = $DB->RunSqlBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/shs.parser/install/db/".strtolower($DB->type)."/uninstall_logs.sql");
//$arrErrors[] = $DB->RunSqlBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/shs.parser/install/db/".strtolower($DB->type)."/install_logs.sql");
$ID = intval($ID);
$sTableID = "tbl_parser_result";

function CheckFilter()
{
    global $FilterArr, $lAdmin;
    foreach ($FilterArr as $f) global $$f;
    if ($_REQUEST['del_filter']=='Y')
        return false;
    return true;
}

$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$FilterArr = Array(
    "find",
    "find_id",
    "find_name",
    "find_start_last_time",
);
$parentID = 0;
if(isset($_REQUEST["parent"]) && $_REQUEST["parent"])
{
    $parentID = $_REQUEST["parent"];
}

$lAdmin->InitFilter($FilterArr);

$arFilter = array();

if (CheckFilter())
{
    if($find!="" && $find_type == "id")
        $arFilter['ID']=$find;
    if($find_id!="")
        $arFilter['ID']=$find_id;
    if($find!="" && $find_type == "parser_id")
        $arFilter['PARSER_ID']=$find;
    if($find_parser_id!="")
        $arFilter['PARSER_ID']=$find_parser_id;
    if($find_start_last_time!="" && $DB->IsDate($find_start_last_time)) {
        $arFilter['>=START_LAST_TIME']= new \Bitrix\Main\Type\DateTime($find_start_last_time.' 00:00:00');
    }
    if ($find_start_last_time_2!="" && $DB->IsDate($find_start_last_time_2)){
        $arFilter['<=START_LAST_TIME']= new \Bitrix\Main\Type\DateTime($find_start_last_time_2.' 23:59:59');        
    }
}

if(($arID = $lAdmin->GroupAction()) && $POST_RIGHT=="W")
{
    if($_REQUEST['action_target']=='selected')
    {
        $rsDataRes = \Bitrix\Shs\ParserResultTable::getList(array(
            'select'=>array('ID'),
            'filter' => array($arFilter),
        ));
        while($arRes = $rsDataRes->Fetch())
            $arID[] = $arRes['ID'];
    }
    
    foreach($arID as $id)
    {
        if(strlen($id)<=0)
            continue;
        $id = intval($id);

        switch($_REQUEST['action'])
        {
        case "delete":
            @set_time_limit(0);
            $DB->StartTransaction();
                $res = \Bitrix\Shs\ParserResultTable::delete($id);
                $resPr = \Bitrix\Shs\ParserResultProductTable::deleteByResultId($id);
                
                if(!$res->isSuccess() || !$resPr)
                {
                    $DB->Rollback();
                    $lAdmin->AddGroupError(GetMessage("parser_del_err"), $id);
                }
            $DB->Commit();
            break;
        }
    }
}

$lAdmin->AddHeaders(array(
    array(    "id"        =>"ID",
        "content"    =>"ID",
        "sort"        =>"id",
        "align"        =>"right",
        "default"    =>true,
    ),
    array(    "id"        =>"PARSER_ID",
        "content"    =>GetMessage('PARSER_F_PARSER_ID'),
        "sort"        =>"parser_id",
        "align"        =>"right",
        "default"    =>true,
    ),
    array(    "id"        =>"PARSER_NAME",
        "content"    =>GetMessage('PARSER_F_NAME_PARSER'),
        "sort"        =>"parser_name",
        "align"        =>"right",
        "default"    =>true,
    ),
    array(    "id"        =>"PRODUCT_COUNT",
        "content"    =>GetMessage('PARSER_F_PRODUCT_COUNT'),
        "sort"        =>"product_count",
        "align"        =>"right",
        "default"    =>true,
    ),
    array(    "id"        =>"START_LAST_TIME",
        "content"    =>GetMessage("PARSER_F_START_LAST_TIME"),
        "sort"        =>"start_last_time",
        "default"    =>true,
    ),
    array(    "id"        =>"END_LAST_TIME",
        "content"    =>GetMessage("PARSER_F_END_LAST_TIME"),
        "sort"        =>"end_last_time",
        "default"    =>true,
    ),
    array(    "id"        =>"STATUS",
        "content"    =>GetMessage("PARSER_F_STATUS"),
        "sort"        =>"status",
        "default"    =>true,
    ),
));
$rsData = \Bitrix\Shs\ParserResultTable::getList(array(
    'select'=>array('*'),
    'filter' => $arFilter,
    'order' => array(
            strtoupper($by)=>strtoupper($order)
            ),
));

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("parser_nav")));

$p = new ShsParserContent();
while($arRes = $rsData->NavNext(true, "f_")):

    $parser = $p->GetList(array(),array('ID'=>$arRes['PARSER_ID']));
    $parser = $parser->fetch();
    $productCount = \Bitrix\Shs\ParserResultProductTable::getList(
        array(
            'select'=>array('CNT'),
            'filter' => array('RESULT_ID'=>$arRes['ID']),
            'runtime' => array(
                new Entity\ExpressionField('CNT', 'COUNT(*)')
            )
        ));
    $productCount = $productCount->fetch();
    
    $row =& $lAdmin->AddRow($f_ID, $arRes);
    $row->AddViewField("PARSER_NAME", '<a target="_blank" href="parser_edit.php?ID='.$arRes['PARSER_ID'].'&lang='.LANG.'" title="'.GetMessage("parser_act_edit").'">'.$parser['NAME'].'</a>');
    $row->AddViewField("PRODUCT_COUNT", $productCount['CNT']);
    
    switch($arRes['STATUS']){
        case -1:
            $color_status = 'color-red';
            break;
        case 0:  
            $color_status = 'color-yellow';
            break;
        case 1: 
            $color_status = 'color-green';
            break;
    }
    
    $row->AddViewField("STATUS", '<span class="'.$color_status.'"><span class="badge '.$color_status.'"></span>'.(\Bitrix\Shs\ParserResultTable::getStatus($arRes['STATUS'])).'</span>');

    $arActions = Array();
    if($POST_RIGHT=="W"){
        $arActions[] = array(
            "ICON"=>"delete",
            "TEXT"=>GetMessage("parser_act_del"),
            "ACTION"=>"if(confirm('".GetMessage("parser_act_del_conf")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
        );
        $arActions[] = array(
            "ICON"=>"",
            "DEFAULT"=>true,
            "TEXT"=>GetMessage("parser_act_see"),
            "ACTION"=>$lAdmin->ActionRedirect("shs_one_parser_result.php?RESULT_ID=".$f_ID)
        );
    }

    $arActions[] = array("SEPARATOR"=>true);

    if(is_set($arActions[count($arActions)-1], "SEPARATOR"))
        unset($arActions[count($arActions)-1]);
    $row->AddActions($arActions);

endwhile;
unset($p);

$lAdmin->AddFooter(
    array(
        array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
        array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
    )
);
$lAdmin->AddGroupActionTable(Array(
    "delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
    ));
$aContext = array();
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("post_title"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); // второй общий пролог
$oFilter = new CAdminFilter(
    $sTableID."_filter",
    array(
        "id" => GetMessage("PARSER_F_ID"),
        "parser_id" => GetMessage("PARSER_F_PARSER_ID"),
        "start_last_time" => GetMessage("PARSER_F_START_LAST_TIME"),
    )
);
if($shs_DEMO==2)CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("parser_demo")));
if($shs_DEMO==3)CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("parser_demo_end")));
?>
<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
<?
$oFilter->Begin();
?>
<tr>
    <td><b><?=GetMessage("PARSER_FIND")?>:</b></td>
    <td>
        <input type="text" size="25" name="find" value="<?echo htmlspecialchars($find)?>" title="<?=GetMessage("POST_FIND_TITLE")?>">
        <?
        $arr = array(
            "reference" => array(
                GetMessage("PARSER_F_ID"),
                GetMessage("PARSER_F_PARSER_ID"),
                GetMessage("PARSER_F_NAME"),
            ),
            "reference_id" => array(
                "id",
                "parser_id",
                "name",
            )
        );
        echo SelectBoxFromArray("find_type", $arr, $find_type, "", "");
        ?>
    </td>
</tr>
<tr>
    <td><?=GetMessage("PARSER_F_ID")?>:</td>
    <td>
        <input type="text" name="find_id" size="47" value="<?echo htmlspecialchars($find_id)?>">
        &nbsp;<?=ShowFilterLogicHelp()?>
    </td>
</tr>
<tr>
    <td><?=GetMessage("PARSER_F_PARSER_ID")?>:</td>
    <td>
        <input type="text" name="find_parser_id" size="47" value="<?echo htmlspecialchars($find_parser_id)?>">
        &nbsp;<?=ShowFilterLogicHelp()?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("PARSER_F_START_LAST_TIME")." (".FORMAT_DATE."):"?></td>
    <td><?echo CalendarPeriod("find_start_last_time", $find_start_last_time, "find_start_last_time_2", $find_start_last_time_2, "find_form","Y")?></td>
</tr>

<?
$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(), "form" => "find_form"));
$oFilter->End();
?>
<input type="hidden" name="parent" value="<?=$_REQUEST["parent"]?>" />
</form>

<?
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");