<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("XML");
?><?

if (file_exists( 'contragents.xml')) {
    $xml = simplexml_load_file('contragents.xml');
	
	foreach ($xml->{'Контрагенты'}->{'Контрагент'} as $key => $val) {
				
		$arUserGroupIDs = array( 
			'OPT_PRICE'=>8, 
			'DEALERS'=>9, 
			'RETAIL'=>10 
		);	
		$email 			= $val->{'Ид'};
		$userCodeGroup  = $val->{'ИдТипЦен'};		//$idPriceType==$userCodeGroup
			
		if( !empty($email) ){
			$order 					= array('sort' => 'asc');
			$tmp 					= 'sort'; 	
			$resUser 				= CUser::GetList( $order, $tmp, array('EMAIL'=>$email), array() )->fetch();
			$resOldUserGroupIDs		= CUser::GetUserGroup($resUser['ID']);
			array_push($resOldUserGroupIDs, $arUserGroupIDs["$userCodeGroup"]);
	
	
			$user   	= new CUser;
			$fields 	= Array( 'EMAIL'=>$email, "GROUP_ID" => $resOldUserGroupIDs );
			$resUpdate  = $user->Update($resUser['ID'], $fields);
			
			if( !$resUpdate ){
				exit('Ошибка обновления груп пользователя');
			}
			
		}else{
			exit('Нет email');
		}		
	}
	
}else{
    exit('Не удалось открыть файл test.xml.');
}




?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
