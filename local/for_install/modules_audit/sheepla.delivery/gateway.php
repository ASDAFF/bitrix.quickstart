<?php
/** 
 * @author Sheepla (sales{at}sheepla.com) 
 * */
ini_set('display_errors', 'On');
header('Content-Type: text/html; charset=utf-8');


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");




CModule::IncludeModule("sale");
CModule::IncludeModule("sheepla.delivery");


$Model = new SheeplaProxyDataModel();
$config = $Model->getConfig();
$Client = new SheeplaClient($config);
$Proxy = new SheeplaProxy();

if(isset($_REQUEST['SheeplaKey']) && !isset($_REQUEST['cmd'])){
    if(isset($config)&&$config['adminApiKey']!=''&&$config['adminApiKey']==$_REQUEST['SheeplaKey']){
        $Proxy->setDebug(true);
    }else{
        $Proxy->setDebug(false);    
    }    
}
$Proxy->setClient($Client);
$Proxy->setModel($Model);
$Proxy->proccessCmd();
$result = $Proxy->syncOrders();
foreach ($result as $order){
	if ($order['status'] == 'ok'){
	   $Model->MarkOrderAsSent($order['orderId']);
	}else{
	   $Model->MarkOrderAsError($order['orderId']);
	}
}