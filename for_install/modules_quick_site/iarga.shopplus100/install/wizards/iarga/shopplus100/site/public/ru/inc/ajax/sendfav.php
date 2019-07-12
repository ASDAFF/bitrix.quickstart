<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
$template = '/bitrix/templates/iarga.shopplus100.main';
IncludeTemplateLangFile($template.'/header.php');
include($_SERVER['DOCUMENT_ROOT'].$template."/inc/functions.php");

if(sizeof($_GET['id'])>0){
	foreach($_GET['id'] as $id){
		$el = CIBlockElement::GetById($id)->GetNext();
		if($el) $_SESSION['CATALOG_COMPARE_LIST'][$el['IBLOCK_ID']]['ITEMS'][$el['ID']] = $el;
	}
	LocalRedirect('/favorite/');
}else{
	// Test of mail
	if($_POST['mail']=='' || !check_email($_POST['mail'])) die('error '.GetMessage("FAV_WRONGMAIL"));

	$items = Array();
	$cnt = 0;
	$n = each($_SESSION['CATALOG_COMPARE_LIST']);

	// Test of num
	if(!$n) die('error '.GetMessage("EMPTY_FAV"));

	$link = 'http://'.$_SERVER['SERVER_NAME'].'/inc/ajax/sendfav.php?';
	foreach($_SESSION['CATALOG_COMPARE_LIST'][$n[0]]['ITEMS'] as $item){
		$link .= 'id[]='.$item['ID'].'&';
		$items .= $item['NAME'].' <br>';
		$cnt ++;
	}

	$message = Array("LINK"=>$link,"EMAIL"=>$_POST['mail']);
	
	if(!$USER->IsAuthorized()){
		$USER->Add(Array(
			"EMAIL"=>$message['EMAIL'],
			"LOGIN"=>$message['EMAIL'],
			"PASSWORD"=>$message['EMAIL'],
			"PASSWORD_CONFIRM"=>$message['EMAIL'],	
			"GROUPS_ID"=>Array(5,6),
			"UF_FAVORITE"=>$items
		));
	}else{
		$user = CUser::GetByID($USER->GetID())->GetNext();
		$USER->Update($user['ID'],Array("NAME"=>$user['NAME'],"ADMIN_NOTES"=>$items));
		$message['NAME'] = $user['NAME'];
		$message["SALE_EMAIL"] = COption::GetOptionString("sale", "order_email", "sale@".$SERVER_NAME);
	}

	CEvent::SendImmediate("YOUR_FAV",SITE_ID,$message);

	print 'success '.GetMessage("FAV_SUCCESS").' nodelete';
}
?>
