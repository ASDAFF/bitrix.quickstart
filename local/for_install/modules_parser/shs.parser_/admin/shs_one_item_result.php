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
global $shs_DEMO;
$POST_RIGHT = $APPLICATION->GetGroupRight("shs.parser");
if($POST_RIGHT=="D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$ID = intval($ITEM_RESULT_ID);
$sTableID = "tbl_parser_result_product";

$parserResult = \Bitrix\Shs\ParserResultProductTable::getList(array(
    'select'=>array('*', 'IBLOCK.NAME', 'IBLOCK.IBLOCK_ID', 'PARSER.START_LAST_TIME', 'PARSER.SETTINGS'),
    'filter' => array('ID'=>$ID),
));
$parserResult = $parserResult->fetch();

$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

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

$m = $parserResult['OLD_PRICE'] - $parserResult['NEW_PRICE'];
if($m<0){
    $css='color-red';
    $price = GetMessage('price_inc');
} elseif($m>0){
    $css='color-green';
    $price = GetMessage('price_dec');
} elseif($m==0){
    $css='color-gray';
    $price = GetMessage('price_not_changed');
}
$prop = unserialize(base64_decode($parserResult['PROPERTIES']));
$settings = unserialize(base64_decode($parserResult['SHS_PARSER_RESULT_PRODUCT_PARSER_SETTINGS']));
$lAdmin->BeginPrologContent();
echo '<b>'.GetMessage('PARSER_F_UPDATE_TIME').': '.$parserResult['UPDATE_TIME'].'</b><br><br>';

echo '<b>'.GetMessage('parser_price_change').'</b><br>';
if($settings['save_price']=='Y'){
    echo '<b class='.$css.'>'.$price.'</b><br>';
    echo'<b>'.GetMessage('parser_old_price').':</b> '.$parserResult['OLD_PRICE'].'<br>';
    echo'<b>'.GetMessage('parser_new_price').':</b> '.$parserResult['NEW_PRICE'].'<br><br>';
} else {
    echo '<span>'.GetMessage('parser_no_history').'</span><br><br>';
}
$m = $prop['count']['old'] - $prop['count']['new'];
if($m<0){
    $css='color-green';
    $count = GetMessage('count_inc');
} elseif($m>0){
    $css='color-red';
    $count = GetMessage('count_dec');
} elseif($m==0){
    $css='color-gray';
    $count = GetMessage('count_not_changed');
}
echo '<b>'.GetMessage('parser_count_change').'</b><br>';
if($settings['save_count']=='Y'){
    echo '<b class='.$css.'>'.$count.'</b><br>';
    echo'<b>'.GetMessage('parser_old_count').':</b> '.$prop['count']['old'].'<br>';
    echo'<b>'.GetMessage('parser_new_count').':</b> '.$prop['count']['new'].'<br><br>';
} else {
    echo '<span>'.GetMessage('parser_no_history').'</span><br><br>';
}

echo '<b>'.GetMessage('parser_prop_change').'</b>';
echo '<br><table class="shs_table_properties">';
echo '<tr><th>'.GetMessage('parser_prop').'</th><th>'.GetMessage('parser_old_value').'</th><th>'.GetMessage('parser_new_value').'</th></tr>';
if(!empty($prop['properties'])){
    foreach($prop['properties'] as $prop_name => $values){
        echo '<tr>';
        echo '<td>'.$prop_name.'</td>';
        foreach($values as $key => $value){
            if(!is_array($value)){
                echo '<td>'.(($value!='')?$value:'-').'</td>';
            } else {
                echo '<td>';
                foreach($value as $val){
                    echo $val.' ';
                };
                echo '</td>';
            }
        }
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan=3>'.($settings['save_props']=="Y"?GetMessage('parser_prop_empty'):GetMessage('parser_no_history')).'</td></tr>';
}
echo '</table>';

echo '<b>'.GetMessage('parser_catalog_change').'</b>';
echo '<br><table class="shs_table_properties">';
echo '<tr><th>'.GetMessage('parser_param').'</th><th>'.GetMessage('parser_old_value').'</th><th>'.GetMessage('parser_new_value').'</th></tr>';
if(!empty($prop['catalog'])){
    foreach($prop['catalog'] as $prop_name => $values){
        echo '<tr>';
        echo '<td>'.$prop_name.'</td>';
        foreach($values as $key => $value){
            if(!is_array($value)){
                echo '<td>'.(($value!='')?$value:'-').'</td>';
            } else {
                echo '<td>';
                foreach($value as $val){
                    echo $val.' ';
                };
                echo '</td>';
            }
        }
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan=3>'.($settings['save_props']=="Y"?GetMessage('parser_catalog_empty'):GetMessage('parser_no_history')).'</td></tr>';
}
echo '</table>';          
if($settings['save_descr']=='Y'){
    echo '<h4>'.GetMessage('parser_descr_detail').'</h4>';
    echo '<span>'.($prop['descr']['detail']['text']!=''?$prop['descr']['detail']['text']:'--').'</span>';
}
if($settings['save_prev_descr']=='Y'){
    echo '<h4>'.GetMessage('parser_descr_prev').'</h4>';
    echo '<span>'.($prop['descr']['prev']['text']!=''?$prop['descr']['prev']['text']:'--').'</span>';
}
if($settings['save_img']=='Y'){
    echo '<h4>'.GetMessage('parser_detail_image').'</h4>';
    $img = CFile::GetFileArray($prop['images']['detail']);
    if($img['SRC'])
        echo '<img class="detail-img" src="'.$img['SRC'].'">';
    else echo '--';
}
if($settings['save_prev_img']=='Y'){
    echo '<h4>'.GetMessage('parser_prev_image').'</h4>';
    $img = CFile::GetFileArray($prop['images']['prev']);
    if($img['SRC'])
        echo '<img class="prev-img" src="'.$img['SRC'].'">';
    else echo '--';
}
echo '<h4>'.GetMessage('parser_change_history').'</h4>';
$lAdmin->EndPrologContent();
$properties = array();

$props = CIBlockProperty::GetList(array(),array('IBLOCK_ID'=>$parserResult['SHS_PARSER_RESULT_PRODUCT_IBLOCK_IBLOCK_ID']));  
$pr = $props->fetch();    
while($pr = $props->fetch()){                
  $properties[]=array(
        "id"        =>"PROP_".$pr['CODE'],
        "content"    =>$pr['NAME'],    
        "default"    =>false,
        );  
}
if(CModule::IncludeModule('catalog')) {
    $block = CCatalogSKU::GetInfoByProductIBlock($parserResult['SHS_PARSER_RESULT_PRODUCT_IBLOCK_IBLOCK_ID']);    
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
        "id"        =>"RESULT_ID",
        "content"    =>GetMessage("PARSER_F_RESULT_ID"),
        "sort"        =>"RESULT_ID",
        "align"        =>"right",
        "default"    =>true,
    ),
    array(    
        "id"        =>"PARSER_ID",
        "content"    =>GetMessage("PARSER_F_PARSER_ID"),
        "sort"        =>"parser.id",
        "align"        =>"right",
        "default"    =>true,
    ),
    array(    
        "id"        =>"START_LAST_TIME",
        "content"    =>GetMessage("PARSER_F_START_LAST_TIME"),
        "sort"        =>"PARSER.START_LAST_TIME",
        "align"        =>"right",
        "default"    =>true,
    ),
    array(    
        "id"        =>"UPDATE_TIME",
        "content"    =>GetMessage("PARSER_F_UPDATE_TIME"),
        "sort"        =>"UPDATE_TIME",
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
        //"sort"        =>"old_count",
        "default"    =>true,
    ),
    array(    
        "id"        =>"NEW_COUNT",
        "content"    =>GetMessage("parser_new_count"),
        //"sort"        =>"new_count",
        "default"    =>true,
    ),
),$properties));           

$rsData = \Bitrix\Shs\ParserResultProductTable::getList(array(
    'select'=>array('*','PARSER.PARSER_ID', 'PARSER.START_LAST_TIME'),
    'filter' => array('PRODUCT_ID'=>$parserResult['PRODUCT_ID']),
    'order' => array(
            strtoupper($by)=>strtoupper($order)
            ),
));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("parser_nav")));
while($arRes = $rsData->NavNext(true, "f_")):
    $row =& $lAdmin->AddRow($f_ID, $arRes);
    
    $row->AddViewField('RESULT_ID','<a href="shs_one_parser_result.php?RESULT_ID='.$arRes['RESULT_ID'].'&lang='.LANG.'">'.$arRes['RESULT_ID'].'</a>');
    
    $m = $arRes['OLD_PRICE']-$arRes['NEW_PRICE'];
    $price = '<span class="'.($m>0?'color-green':($m==0?'color-gray':'color-red')).'" title="'.($m>0?GetMessage('price_dec'):($m==0?GetMessage('price_not_changed'):GetMessage('price_inc'))).'">'.$arRes['NEW_PRICE'].'</span>';
    $row->AddViewField('NEW_PRICE',$price);
    
    $prop = unserialize(base64_decode($arRes['PROPERTIES']));
    $m = $prop['count']['old']-$prop['count']['new'];
    $count = '<span class="'.($m<0?'color-green':($m==0?'color-gray':'color-red')).'" title="'.($m>0?GetMessage('count_dec'):($m==0?GetMessage('count_not_changed'):GetMessage('count_inc'))).'">'.$prop['count']['new'].'</span>';
    $row->AddViewField('NEW_COUNT',$count);
    $row->AddViewField('OLD_COUNT',$prop['count']['old']);
    
    $row->AddViewField('START_LAST_TIME',$arRes['SHS_PARSER_RESULT_PRODUCT_PARSER_START_LAST_TIME']);
    $row->AddViewField('PARSER_ID',$arRes['SHS_PARSER_RESULT_PRODUCT_PARSER_PARSER_ID']);
    foreach($prop['properties'] as $code=>$vals){
       $row->AddViewField('PROP_'.$code,$vals['new']); 
    }
    
    $arActions = Array();
    if($POST_RIGHT=="W"){
        $arActions[] = array(
            "ICON"=>"delete",
            "TEXT"=>GetMessage("parser_act_del"),
            "ACTION"=>"if(confirm('".GetMessage("parser_act_del_conf")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete",'ITEM_RESULT_ID='.$ID)
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

$APPLICATION->SetTitle(GetMessage("post_title").$parserResult['PRODUCT_ID'].' "'.$parserResult['SHS_PARSER_RESULT_PRODUCT_IBLOCK_NAME'].'"');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); // второй общий пролог

if($shs_DEMO==2)CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("parser_demo")));
if($shs_DEMO==3)CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("parser_demo_end")));
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");