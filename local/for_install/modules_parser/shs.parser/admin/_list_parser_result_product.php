<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


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
global $shs_DEMO;
$POST_RIGHT = $APPLICATION->GetGroupRight("shs.parser");
if($POST_RIGHT=="D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$ID = intval($ID);
$sTableID = "tbl_parser_result";

function CheckFilter()
{
    global $FilterArr, $lAdmin;
    foreach ($FilterArr as $f) global $$f;
    if (strlen(trim($find_last_executed_1))>0 || strlen(trim($find_last_executed_2))>0)
    {
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
    //"find_type",
    "find_name",
    //"find_timestamp_1",
    //"find_rss",
    //"find_active",
    //"find_iblock_id",
    //"find_section_id",
    //"find_encoding",
    "find_start_last_time",
    //"find_show_sp"
);
$parentID = 0;
if(isset($_REQUEST["parent"]) && $_REQUEST["parent"])
{
    $parentID = $_REQUEST["parent"];
}

$lAdmin->InitFilter($FilterArr);

if (CheckFilter())
{
    $arFilter = array(
        "ID" => ($find!="" && $find_type == "id"? $find:$find_id),
        "ID" => ($find!="" && $find_type == "parser_id"? $find:$find_parser_id),
        //"TYPE" => $find_main_type,
        //"TIMESTAMP_1" => $find_timestamp_1,
        "NAME" => $find_name,
        //"RSS" => $find_rss,
        //"ACTIVE" => $find_active,
        //"IBLOCK_ID" => $find_iblock_id,
        //"SECTION_ID" => $find_section_id,
        //"ENCODING" => $find_from,
        //"START_AGENT" => $find_start_agent,
        //"TIME_AGENT" => $find_time_agent,
        "START_LAST_TIME" => $find_start_last_time,
        //"CATEGORY_ID" => $parentID
    );
}


if(($arID = $lAdmin->GroupAction()) && $POST_RIGHT=="W")
{
    if($_REQUEST['action_target']=='selected')
    {
        $rsDataRes = \Bitrix\Shs\ParserResultTable::getList(array(
            'select'=>array('ID'),
            'filter' => array(),
        ));
        while($arRes = $rsDataRes->Fetch())
            $arID[] = $arRes['ID'];
    }
    
    foreach($arID as $ID)
    {
        if(strlen($ID)<=0)
            continue;
        $TYPE = substr($ID, 0, 1);
        $ID = intval(substr($ID,1));

        switch($_REQUEST['action'])
        {
        case "delete":
            @set_time_limit(0);
            $DB->StartTransaction();
                $res = \Bitrix\Shs\ParserResultTable::delete($ID);
                if(!$res->isSuccess())
                {
                    $DB->Rollback();
                    $lAdmin->AddGroupError(GetMessage("parser_del_err"), $ID);
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
));
$rsData = \Bitrix\Shs\ParserResultTable::getList(array(
    'select'=>array('*'),
    'filter' => array(),
    'order' => array(
            strtoupper($by)=>strtoupper($order)
            ),
));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("parser_nav")));
/*$rsIBlock = CIBlock::GetList(Array("name" => "asc"), Array("ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch()){
    $arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
    $arIBlockFilter['REFERENCE'][] = "[".$arr["ID"]."] ".$arr["NAME"];
    $arIBlockFilter['REFERENCE_ID'][] = $arr["ID"];
}*/

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
    
    $row =& $lAdmin->AddRow($f_T.$f_ID, $arRes);
    $row->AddViewField("PARSER_NAME", '<a target="_blank" href="parser_edit.php?ID='.$arRes['PARSER_ID'].'&lang='.LANG.'" title="'.GetMessage("parser_act_edit").'">'.$parser['NAME'].'</a>');
    $row->AddViewField("PRODUCT_COUNT", $productCount['CNT']);

    $arActions = Array();
    if($POST_RIGHT=="W")
        $arActions[] = array(
            "ICON"=>"delete",
            "TEXT"=>GetMessage("parser_act_del"),
            "ACTION"=>"if(confirm('".GetMessage("parser_act_del_conf")."')) ".$lAdmin->ActionDoGroup("S".$f_ID, "delete")
        );

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

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("post_title"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); // второй общий пролог
$oFilter = new CAdminFilter(
    $sTableID."_filter",
    array(
        "id" => GetMessage("PARSER_F_ID"),
        "parser_id" => GetMessage("PARSER_F_PARSER_ID"),
        //"timestamp" => GetMessage("PARSER_F_TIMESTAMP"),
        "name" => GetMessage("PARSER_F_NAME"),
        //"type" => GetMessage("PARSER_F_TYPE"),
        //"rss" => GetMessage("PARSER_F_RSS"),
        //"active" => GetMessage("PARSER_F_ACTIVE"),
        //"iblock_id" => GetMessage("PARSER_F_IBLOCK_ID"),
        //"section_id" => GetMessage("PARSER_F_SECTION_ID"),
        //"encoding" => GetMessage("PARSER_F_ENCODING"),
        //"start_agent" => GetMessage("PARSER_F_START_AGENT"),
        //"time_agent" => GetMessage("PARSER_F_TIME_AGENT"),
        "start_last_time" => GetMessage("PARSER_F_START_LAST_TIME"),
        //"show_s_p" => GetMessage("PARSER_SHOW_SECTION_PARSER")
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
    <td><?echo GetMessage("PARSER_F_NAME")?>:</td>
    <td><input type="text" name="find_name" size="47" value="<?echo htmlspecialchars($find_name)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
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
//******************************
// Send message and show progress
//******************************
if(isset($_REQUEST['parser_end']) && $_REQUEST['parser_end']==1 && isset($_REQUEST['parser_id']) && $_REQUEST['parser_id']>0){
    if(isset($_GET['SUCCESS'][0])){
      foreach($_GET['SUCCESS'] as $success) CAdminMessage::ShowMessage(array("MESSAGE"=>$success, "TYPE"=>"OK"));
    }
    if(isset($_GET['ERROR'][0])){
        foreach($_GET['ERROR'] as $error) CAdminMessage::ShowMessage($error);
    }

}
if(isset($_REQUEST['action']) && $_REQUEST['action']=="parser"):
$parser = ShsParserContent::GetByID($_REQUEST['ID']);
if(!$parser->ExtractFields("shs_"))
    $ID=0;

if($ID>0){
    $rssParser = new RssContentParser();
    $result = $rssParser->startParser();
    if(isset($result[SUCCESS][0]))
    foreach($result[SUCCESS] as $i=>$success){
        $resultUrl .= "&SUCCESS[".$i."]=".urlencode($success);
    }
    if(isset($result[ERROR][0]))
     foreach($result[ERROR] as $i=>$error){
     $resultUrl .= "&ERROR[".$i."]=".urlencode($error);
    }
    LocalRedirect($APPLICATION->GetCurPageParam("parser_end=1&parser_id=".$ID.$resultUrl, array("action")));
}

endif;

$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");