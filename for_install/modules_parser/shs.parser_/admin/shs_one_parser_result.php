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

$ID = intval($RESULT_ID);
$sTableID = "tbl_parser_result_product";

function CheckFilter()
{
    global $FilterArr, $lAdmin;
    foreach ($FilterArr as $f) global $$f;
    if ($_REQUEST['del_filter']=='Y')
        return false;
    return true;
}

$parserResult = \Bitrix\Shs\ParserResultTable::getList(array(
    'select'=>array('*'),
    'filter' => array('ID'=>$ID),
));
$parserResult = $parserResult->fetch();

$parser = new ShsParserContent();
$parser = $parser->GetList(array(),array('ID'=>$parserResult['PARSER_ID']));
$parser = $parser->fetch();

$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$lAdmin->BeginPrologContent();
?>
<h4><? echo GetMessage("PARSER_INFO") ?></h4>
<table class="shs_parser_result_info">
    <tr>
        <td><?php echo GetMessage('PARSER_NAME');?></td>
        <td><?php echo $parser["NAME"];?></td>
    </tr>
    <tr>
        <td><?php echo GetMessage('PARSER_TYPE');?></td>
        <td><?php echo $parser["TYPE"];?></td>
    </tr>
    <tr>
        <td><?php echo GetMessage('PARSER_RSS');?></td>
        <td><?php echo $parser["RSS"];?></td>
    </tr>
    <tr>
        <td><?php echo GetMessage('PARSER_START');?></td>
        <td><?php echo $parserResult['START_LAST_TIME'];?></td>
    </tr>
    <tr>
        <td><?php echo GetMessage('PARSER_END');?></td>
        <td><?php echo $parserResult['END_LAST_TIME'];?></td>
    </tr>
</table>
<?
$lAdmin->EndPrologContent();

$FilterArr = Array(
    "find",
    "find_id",
);

$lAdmin->InitFilter($FilterArr);
$arFilter = array();
$arFilter['RESULT_ID']=$ID;

if (CheckFilter())
{
    /*if($find!="" && $find_type == "id")
        $arFilter['ID']=$find;*/
    if($find_product_id!="")
        $arFilter['PRODUCT_ID']=$find_product_id;
    /*if($find!="" && $find_type == "parser_id")
        $arFilter['PARSER_ID']=$find;
    if($find_parser_id!="")
        $arFilter['PARSER_ID']=$find_parser_id;
    if($find_start_last_time!="")
        $arFilter['START_LAST_TIME']=$find_start_last_time;*/
}

if(($arID = $lAdmin->GroupAction()) && $POST_RIGHT=="W")
{
    if($_REQUEST['action_target']=='selected')
    {
        $rsDataRes = \Bitrix\Shs\ParserResultProductTable::getList(array(
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
        $ID = intval($ID);

        switch($_REQUEST['action'])
        {
        case "delete":
            @set_time_limit(0);
            $DB->StartTransaction();
                $res = \Bitrix\Shs\ParserResultProductTable::delete($ID);
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
$properties = array();
              

$props = CIBlockProperty::GetList(array(),array('IBLOCK_ID'=>$parser['IBLOCK_ID']));  
$pr = $props->fetch();    
while($pr = $props->fetch()){                
  $properties[]=array(
        "id"        =>"PROP_".$pr['CODE'],
        "content"    =>$pr['NAME'],    
        "default"    =>false,
        );  
}
if(CModule::IncludeModule('catalog')) {
    $block = CCatalogSKU::GetInfoByProductIBlock($parser['IBLOCK_ID']);    
    $props = CIBlockProperty::GetList(array(),array('IBLOCK_ID'=>$block['IBLOCK_ID']));  
    $pr = $props->fetch();    
    while($pr = $props->fetch()){                
        $properties[]=array(
            "id"        =>"PROP_".$pr['CODE'],
            "content"    =>$pr['NAME'],    
            "default"    =>false,
        );  
    }                
}

$lAdmin->AddHeaders(array_merge(array(
    array(    
        "id"        =>"ID",
        "content"    =>"ID",
        "sort"        =>"id",
        "align"        =>"right",
        "default"    =>true,
    ),
    array(    
        "id"        =>"PRODUCT_TYPE",
        "content"    =>GetMessage("PARSER_F_PRODUCT_TYPE"),
        "sort"        =>"PRODUCT_TYPE",
        "align"        =>"right",
        "default"    =>true,
    ),
    array(    
        "id"        =>"PRODUCT_ID",
        "content"    =>GetMessage("PARSER_F_PRODUCT_ID"),
        "sort"        =>"PRODUCT_ID",
        "align"        =>"right",
        "default"    =>true,
    ),
    array(    
        "id"        =>"IBLOCK_NAME",
        "content"    =>GetMessage('PARSER_F_PRODUCT_NAME'),
        "sort"        =>"iblock.name",
        "align"        =>"right",
        "default"    =>true,
    ),
    array(    
        "id"        =>"OLD_PRICE",
        "content"    =>GetMessage("PARSER_F_START_OLD_PRICE"),
        "sort"        =>"old_price",
        "default"    =>true,
    ),
    array(    
        "id"        =>"NEW_PRICE",
        "content"    =>GetMessage("PARSER_F_START_NEW_PRICE"),
        "sort"        =>"new_price",
        "default"    =>true,
    ),
    array(    
        "id"        =>"OLD_COUNT",
        "content"    =>GetMessage("parser_old_count"),    
        "default"    =>true,
    ),
    array(    
        "id"        =>"NEW_COUNT",
        "content"    =>GetMessage("parser_new_count"),  
        "default"    =>true,
    ),
    array(    
        "id"        =>"UPDATE_TIME",
        "content"    =>GetMessage("parser_update_time"),
        "sort"        =>"update_time",
        "default"    =>true,
    ),
),$properties));                                                         
//$lAdmin->AddHeaders($properties);

$rsData = \Bitrix\Shs\ParserResultProductTable::getList(array(
    'select'=>array('*','IBLOCK.NAME','IBLOCK.IBLOCK_ID','IBLOCK.ID','IBLOCK.IBLOCK_SECTION_ID','IBLOCK.IBLOCK.TYPE.ID'),
    'filter' => $arFilter,
    'order' => array(
            strtoupper($by)=>strtoupper($order)
            ),
));

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("parser_nav")));

while($arRes = $rsData->NavNext(true, "f_")):
    $row =& $lAdmin->AddRow($f_ID, $arRes);   
    $prop = unserialize(base64_decode($arRes['PROPERTIES']));  
    
    $row->AddViewField('PRODUCT_TYPE',$prop['type']);
    
    $m = $arRes['OLD_PRICE']-$arRes['NEW_PRICE'];
    $price = '<span class="'.($m>0?'color-green':($m==0?'color-gray':'color-red')).'" title="'.($m>0?GetMessage('price_dec'):($m==0?GetMessage('price_not_changed'):GetMessage('price_inc'))).'">'.$arRes['NEW_PRICE'].'</span>';
    $row->AddViewField('NEW_PRICE',$price);
    $row->AddViewField("IBLOCK_NAME", '<a target="_blank" href="iblock_element_edit.php?IBLOCK_ID='.$arRes['SHS_PARSER_RESULT_PRODUCT_IBLOCK_IBLOCK_ID'].'&type='.$arRes['SHS_PARSER_RESULT_PRODUCT_IBLOCK_IBLOCK_TYPE_ID'].'&ID='.$arRes['PRODUCT_ID'].'&lang='.LANG.'&find_section_section='.$arRes['SHS_PARSER_RESULT_PRODUCT_IBLOCK_IBLOCK_SECTION_ID'].'&WF=Y">'.$arRes['SHS_PARSER_RESULT_PRODUCT_IBLOCK_NAME'].'</a>');
                                                             
    $m = $prop['count']['old']-$prop['count']['new'];
    $count = '<span class="'.($m<0?'color-green':($m==0?'color-gray':'color-red')).'" title="'.($m>0?GetMessage('count_dec'):($m==0?GetMessage('count_not_changed'):GetMessage('count_inc'))).'">'.$prop['count']['new'].'</span>';
    $row->AddViewField('NEW_COUNT',$count);
    $row->AddViewField('OLD_COUNT',$prop['count']['old']);    
    foreach($prop['properties'] as $code=>$vals){
       $row->AddViewField('PROP_'.$code,$vals['new']); 
    }
    
    $arActions = Array();
    if($POST_RIGHT=="W"){
        $arActions[] = array(
            "ICON"=>"delete",
            "TEXT"=>GetMessage("parser_act_del"),
            "ACTION"=>"if(confirm('".GetMessage("parser_act_del_conf")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete",'RESULT_ID='.intval($RESULT_ID))
        );
        $arActions[] = array(
            "ICON"=>"",
            "DEFAULT"=>true,
            "TEXT"=>GetMessage("parser_act_see"),
            "ACTION"=>$lAdmin->ActionRedirect("shs_one_item_result.php?ITEM_RESULT_ID=".$f_ID)
        );
    }
    $row->AddActions($arActions);

endwhile;

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

$APPLICATION->SetTitle(GetMessage("post_title").$parserResult['PARSER_ID']);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); // второй общий пролог

$oFilter = new CAdminFilter(
    $sTableID."_filter",
    array(
        "product_id" => GetMessage("PARSER_F_PRODUCT_ID"),
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
    <td><?=GetMessage("PARSER_F_PRODUCT_ID")?>:</td>
    <td>
        <input type="text" name="find_product_id" size="47" value="<?echo htmlspecialchars($find_product_id)?>">
        &nbsp;<?=ShowFilterLogicHelp()?>
    </td>
</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(), "form" => "find_form"));
$oFilter->End();
?>
<input type="hidden" name="RESULT_ID" value="<?=intval($RESULT_ID)?>" />
</form>

<?php
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");