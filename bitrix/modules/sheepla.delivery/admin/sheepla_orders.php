<?php
if (!defined('SHEEPLA_DIR')) {
	define('SHEEPLA_DIR', $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sheepla.delivery/");
}
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("main") < "R") 
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

$APPLICATION->SetTitle('Sheepla');


require_once ($DOCUMENT_ROOT.BX_ROOT."/modules/main/include/prolog_admin_after.php");
//add sheepla admin headers
include_once(SHEEPLA_DIR . DIRECTORY_SEPARATOR . 'include.php');

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'send' && isset($_REQUEST['SID']) && is_numeric($_REQUEST['SID'])) {
	$OrderId = intval($_REQUEST['SID']);
	switch($_REQUEST['action']) {
		case 'send':
			CSheeplaDb::MarkOrderSent($OrderId);
			break;
		case 'resend':
			CSheeplaDb::MarkOrderReSend($OrderId);
			break;
		/*case 'forsesync':
			CSheeplaDb::MarkOrdersToForce($amount);
			break;*/
		case 'markerror':
			CSheeplaDb::MarkOrderError($OrderId);
			break;
	}
	LocalRedirect($APPLICATION->GetCurPage().'?mid='.htmlspecialcharsbx($mid).'&amp;lang='.LANG);
}

?>
<h2><?=GetMessage('SHEEPLA_ORDERS_TITLE')?></h2>
<?
$_orders = CSheeplaDb::GetSheeplaOrdersEx(false, false);

if (count($_orders) > 0) {
    $lOrders = new CAdminList('tbl_sheepla_orders', false);
    $lOrders->AddHeaders(array(
        array("id"=>"ID", "content" => '№', "sort"=>"ID", "default"=>true),
        array("id"=>"STATUS", "content" => GetMessage("SHEEPLA_ORDER_STATUS"), "sort"=>"STATUS", "default"=>true),
    ));

    //TODO: make pagination
    //$from = ((!empty($_REQUEST['page']) ? $_REQUEST['page'] : 0) * 20) + 1;

    $statuses = array(
        0 => GetMessage('SHEEPLA_ORDER_STATUS_NOTSENT'),
        1 => GetMessage('SHEEPLA_ORDER_STATUS_SENT')
    );

    foreach (CSheeplaDb::GetSheeplaOrdersEx(false, false) as $order) {
        $row =$lOrders->AddRow($order['order_id']);
        $row->AddField("ID", $order['order_id']);
        $row->AddField("STATUS", isset($statuses[$order['send']]) ? $statuses[$order['send']] : GetMessage('SHEEPLA_ORDER_STATUS_UNKNOWN'));
        $arActions = array();
	$arActions[] = array("TEXT"=>GetMessage("SHEEPLA_ORDER_MARK_SEND"), "ACTION"=>$lOrders->ActionRedirect($APPLICATION->GetCurPage().'?mid='.htmlspecialcharsbx($mid)."&SID=".$order['order_id']."&lang=".LANG."&action=send"), "DEFAULT"=>true);
	$arActions[] = array("TEXT"=>GetMessage("SHEEPLA_ORDER_MARK_RESEND"), "ACTION"=>$lOrders->ActionRedirect($APPLICATION->GetCurPage().'?mid='.htmlspecialcharsbx($mid)."&SID=".$order['order_id']."&lang=".LANG."&action=resend"), "DEFAULT"=>true);
	//$arActions[] = array("TEXT"=>GetMessage("SHEEPLA_ORDER_MARK_FORSESYNC"), "ACTION"=>$lOrders->ActionRedirect($APPLICATION->GetCurPage().'?mid='.htmlspecialcharsbx($mid)."&SID=".$order['order_id']."&lang=".LANG."&action=forsesync"), "DEFAULT"=>true);
	$arActions[] = array("TEXT"=>GetMessage("SHEEPLA_ORDER_MARK_ERROR"), "ACTION"=>$lOrders->ActionRedirect($APPLICATION->GetCurPage().'?mid='.htmlspecialcharsbx($mid)."&SID=".$order['order_id']."&lang=".LANG."&action=markerror"), "DEFAULT"=>true);
        //$arActions[] = array("TEXT"=>GetMessage("SHEEPLA_ORDER_" .($order['send'] > 0 ? "RE" : ""). "SEND"), "ACTION"=>$lOrders->ActionRedirect($APPLICATION->GetCurPage().'?mid='.htmlspecialcharsbx($mid)."&SID=".$order['order_id']."&lang=".LANG."&action=send"), "DEFAULT"=>true);
        $row->AddActions($arActions);
    }

    $lOrders->DisplayList();
} else {
    echo('<div>'.GetMessage('SHEEPLA_NO_ORDERS').'</div>');
}
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");

?>