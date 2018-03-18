<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(count($arParams['MESSENGER'])){

	//ICQ
	function telIcq($uin){
			$a = @get_headers('http://status.icq.com/online.gif?icq='.$uin);
			foreach($a as $Header) { if (is_int(strpos($Header, 'Location'))) { $Status = substr($Header, -5, 1); } }
			switch ($Status){
				case '1': $icqstatus = true; break;
				default: $icqstatus = false; break;
			}
			return $icqstatus;
	}
	//Jabber
	function telJabber($jidHash){
			$a = @file_get_contents('http://web-apps.ru/jabber-presence/html/xid/'.$jidHash);
   			switch ($a){
        		case 'available': case 'chat': $jabberstatus = true; break;
        		case 'away': case 'xa': case 'dnd': $jabberstatus = true; break;
        		default: $jabberstatus = false; break;
   			}
		return $jabberstatus;
	}
	//Mra
	function telMra($mail){
		$a = @md5(file_get_contents("http://status.mail.ru/?".$mail));
   		switch($a) {
        	case '0318014f28082ac7f2806171029266ef': $mrastatus = true; break;
        	case '89d1bfcdbf238e7faa6aeb278c27b676': $mrastatus = true; break;
        	case 'a46f044e175e9b1b28c8d9a9f66f4495': $mrastatus = false; break;
        	default: $mrastatus = false; break;
    }
		return $mrastatus;
	}
	//Skype
	function telSkype($nickName){
		$a = @file_get_contents("http://mystatus.skype.com/".$nickName.".txt");
    	switch($a) {
        	case 'Online': $skypestatus = true; break;
        	case 'Away': case 'Do Not Disturb': $skypestatus = true; break;
        	case 'Offline': $skypestatus = false; break;
        	default: $skypestatus = false; break;
    }
		return $skypestatus;
	}
	//Vk
	function telVk($id){
		$ch = curl_init('https://api.vkontakte.ru/method/getProfiles?uids='.$id.'&fields=online');
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	$result=curl_exec($ch);
    	curl_close($ch);
    	$a = substr($result, -4, 1);
    	switch ($a){
        	case '1': $vkstatus = true; break;
        	case '0': $vkstatus = false; break;
        	default: $vkstatus = false; break;
    	}
		return $vkstatus;
	}
	
		$arResult['PROPERTIES']['SIZE']=$arParams['SIZE'].'px';
		($arParams['POSITION']=='Y')?$arResult['PROPERTIES']['POSITION']='vertical':$arResult['PROPERTIES']['POSITION']='horizontal';
		
		foreach($arParams['MESSENGER'] as $messenger){
			switch ($messenger) {
				case 'ICQ':
					$status=telIcq($arParams['UIN']); $title=$arParams['UIN']; $protocole='tel:'; break;
				case 'JABBER':
					$status=telJabber($arParams['JID_HASH']); $title=$arParams['JID']; $protocole='tel:'; break;
				case 'MRA':
					$status=telMra($arParams['MAIL']); $title=$arParams['MAIL']; $protocole='mailto:'; break;
				case 'SKYPE':
					$status=telSkype($arParams['NICK']); $title=$arParams['NICK']; $protocole='tel:'; break;
				case 'VK':
					$status=telVk($arParams['VK_ID']); $title=$arParams['VK_ID']; $protocole='http://vk.com/id'; break;
			}
			
			$arResult['PROPERTIES']['MESSENGER'][$messenger]['TITLE']=$title;
			$arResult['PROPERTIES']['MESSENGER'][$messenger]['PROTOCOLE']=$protocole;
			
			if($status==true){
				if($arParams[$messenger.'_Y']=='Y' && $arParams[$messenger.'_ONLINE']){
					$arResult['PROPERTIES']['MESSENGER'][$messenger]['ICONS']=$arParams[$messenger.'_ONLINE'];
				}
				else{
					$arResult['PROPERTIES']['MESSENGER'][$messenger]['ICONS']=$componentPath.'/templates/.default/img/'.$messenger.'_ONLINE.png';
				}
			}
			else{
				if($arParams[$messenger.'_Y']=='Y' && $arParams[$messenger.'_OFFLINE'] && $status=false){
					$arResult['PROPERTIES']['MESSENGER'][$messenger]['ICONS']=$arParams[$messenger.'_OFFLINE'];
				}
				else{
					$arResult['PROPERTIES']['MESSENGER'][$messenger]['ICONS']=$componentPath.'/templates/.default/img/'.$messenger.'_OFFLINE.png';
				}
			}
		}
	
	if($_REQUEST['socialAjax']=="Y"){
		ob_end_clean();
		ob_start();
	}
 	$this->IncludeComponentTemplate();
 	
 	if($_REQUEST['socialAjax']=="Y"){
 		$request['data']=ob_get_contents();
 		ob_end_clean();

 		if(json_encode($_REQUEST['html'])!=json_encode($request['data']))
 			echo json_encode($request);
 		die();
 	}
 	if($arParams["MYAJAX"]=="Y")
 		$APPLICATION->AddHeadScript($componentPath.'/js/script.js');
}?>