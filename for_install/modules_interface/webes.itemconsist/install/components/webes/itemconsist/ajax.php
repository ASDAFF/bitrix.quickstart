<?php

ini_set("display_errors","Off");

ob_start();
ob_clean();
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
ob_end_clean();

global $USER, $APPLICATION;

GLOBAL $PATH,$connection,$USER,$DB,$AUTH;
if(is_file($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog.php"))require_once ('./includes/init_bitrix.php');


require_once('./classies.php');


use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

GLOBAL $sale_module;
$sale_module=true;

if(!Loader::includeModule('sale')) {
    $sale_module=false;
}


if(mb_strtolower(LANG_CHARSET)!='utf-8' && isset($_POST))
{
    foreach($_POST as $key => $a)
    {
        if(is_array($a))
            foreach($a as $k => $v)
            {
                if(is_array($v))
                {
                    foreach($v as $k1 => $v1)
                        $_POST[$key][$k][$k1]=mb_convert_encoding($v1,LANG_CHARSET,'UTF-8');
                }
                else $_POST[$key][$k]=mb_convert_encoding($v,LANG_CHARSET,'UTF-8');
            }
        else $_POST[$key]=mb_convert_encoding($a,LANG_CHARSET,'UTF-8');
    }
}


$act=(isset($_GET['act'])?$_GET['act']:'main');

switch ($act)
{
    case "get_all_elements_statuses": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::get_all_elements_statuses(explode(',',$_GET['ids'])));break;}
    case "get_element_data": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::get_element_data($_GET['element_id']));break;}
    case "get_iblocks": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::get_iblocks());break;} //CStepUseIC::get_sections_ibock($_GET['ib'])
    case "get_sections_ibock": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::get_sections_ibock($_GET['ib']));break;} //CStepUseIC::get_sections_ibock($_GET['ib'])
    case "add_iblock_config": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::add_iblock_config(intval($_GET['ib']),intval($_GET['sid'])));break;}
    case "get_all_ib_settings": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::get_all_ib_settings());break;}
    case "save_ib_params": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::save_ib_params($_GET['cid'],$_POST['config']));break;}
    case "get_all_ingridients": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::get_all_ingridients());break;}
    case "add_group": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::add_group($_POST['name']));break;}
    case "add_ingridient": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::add_ingridient($_GET['group_id']));break;}
    case "save_ingridient": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::save_ingridient($_GET['i_id']));break;}
    case "delete_ingridient": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::delete_ingridient($_GET['i_id']));break;}
    case "save_group_params": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::save_group_params($_GET['group_id']));break;}
    case "delete_ib_config": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::delete_ib_config($_GET['cid']));break;}
    case "save_consist": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::save_consist($_GET['id'],$_POST['configcnt'],$_GET['parent_id'])); CStepUseIC::calc_item(($_GET['parent_id']>0 && $sale_module?$_GET['parent_id']:$_GET['id']),$_GET['price_param_id']); break;}
    case "calc-item": {if($AUTH)print \Bitrix\Main\Web\Json::encode(CStepUseIC::calc_item($_GET['item_id']));break;}
    case "recalc_all": {if($AUTH)print CStepUseIC::recalc_all(intval($_GET['id_price']));break;}
    default : {print \Bitrix\Main\Web\Json::encode(array()); break;}
}


?>