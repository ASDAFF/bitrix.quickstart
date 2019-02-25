<?
IncludeModuleLangFile(__FILE__);
class QuickPay{
	
	function Show() {
		$included = CModule::IncludeModuleEx('ambersite.quickpay');
		if($included=='1') {CAdminNotify::DeleteByTag('AMBERSITE_QUICKPAY_DEMO'); CAdminNotify::DeleteByTag('AMBERSITE_QUICKPAY_DEMO_EXPIRED'); CAdminNotify::DeleteByTag('AMBERSITE_QUICKPAY_NOT_FOUND');}
		if($included=='1' || ($included=='2' && defined('ambersite_quickpay_OLDSITEEXPIREDATE'))) return true;
		else return false;
	}
	
	function DBWhere($arFilter) {
	if(CModule::IncludeModuleEx('ambersite.quickpay')=='0') {CAdminNotify::Add(array('MESSAGE'=>GetMessage("NOTIFY_NOT_FOUND"), 'TAG'=>'AMBERSITE_QUICKPAY_NOT_FOUND', 'MODULE_ID'=>'ambersite.quickpay', 'ENABLE_CLOSE'=>'Y'));}
	if(CModule::IncludeModuleEx('ambersite.quickpay')=='2') {CAdminNotify::Add(array('MESSAGE'=>GetMessage("NOTIFY_DEMO"), 'TAG'=>'AMBERSITE_QUICKPAY_DEMO', 'MODULE_ID'=>'ambersite.quickpay', 'ENABLE_CLOSE'=>'Y'));}
	if(CModule::IncludeModuleEx('ambersite.quickpay')=='3') {CAdminNotify::DeleteByTag('AMBERSITE_QUICKPAY_DEMO'); CAdminNotify::Add(array('MESSAGE'=>GetMessage("NOTIFY_DEMO_EXPIRED"), 'TAG'=>'AMBERSITE_QUICKPAY_DEMO_EXPIRED', 'MODULE_ID'=>'ambersite.quickpay', 'ENABLE_CLOSE'=>'Y'));}
		global $DB;
		$where = "WHERE 1=1"; 
		foreach($arFilter as $n=>$arItem) {
			if($arItem) {
				if($n == 'ID') $where .= " AND F.".$n." = ".$arItem;
				elseif($n == 'DATEFROM') $where .= " AND F.DATE >= ".$DB->CharToDateFunction($arItem);
				elseif($n == 'DATETO') $where .= " AND F.DATE <= ".$DB->CharToDateFunction($arItem); 
				else $where .= " AND F.".$n." LIKE ('%".trim($DB->ForSql($arItem))."%')";
			}
		}
		return $where;
	}
	
	function QuickpayResult($arParams, $Order) {
	if(CModule::IncludeModuleEx('ambersite.quickpay')=='0') {CAdminNotify::Add(array('MESSAGE'=>GetMessage("NOTIFY_NOT_FOUND"), 'TAG'=>'AMBERSITE_QUICKPAY_NOT_FOUND', 'MODULE_ID'=>'ambersite.quickpay', 'ENABLE_CLOSE'=>'Y'));}
	if(CModule::IncludeModuleEx('ambersite.quickpay')=='2') {CAdminNotify::Add(array('MESSAGE'=>GetMessage("NOTIFY_DEMO"), 'TAG'=>'AMBERSITE_QUICKPAY_DEMO', 'MODULE_ID'=>'ambersite.quickpay', 'ENABLE_CLOSE'=>'Y'));}
	if(CModule::IncludeModuleEx('ambersite.quickpay')=='3') {CAdminNotify::DeleteByTag('AMBERSITE_QUICKPAY_DEMO'); CAdminNotify::Add(array('MESSAGE'=>GetMessage("NOTIFY_DEMO_EXPIRED"), 'TAG'=>'AMBERSITE_QUICKPAY_DEMO_EXPIRED', 'MODULE_ID'=>'ambersite.quickpay', 'ENABLE_CLOSE'=>'Y'));}
	if (self::Show()) {
		global $DB, $APPLICATION;
		$arResult['PRICE'] = '0';
		$arResult['THISPATH'] = $APPLICATION->GetCurPage();
		if(CModule::IncludeModule('iblock') && $arParams['IBLOCK_ID_CATALOG'] && $arParams['ELEMENT_ID_CATALOG'] && $arParams['IBLOCK_PAYPROP_ID'] && !$Order) {
			$dbElement = CIBlockElement::GetList(Array(), Array('IBLOCK_ID' => $arParams['IBLOCK_ID_CATALOG'], 'ACTIVE' => 'Y', 'ACTIVE_DATE'=>'Y', 'ID' => $arParams['ELEMENT_ID_CATALOG']), false, Array("nPageSize"=>1, "iNumPage"=>1), Array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL', 'IBLOCK_TYPE_ID', 'PROPERTY_'.$arParams['IBLOCK_PAYPROP_ID']));
			while ($arElement = $dbElement->GetNext()) {
				$arResult['ID'] = $arElement['ID'];
				$arResult['NAME'] = $arElement['NAME']; 
				$arResult['PRICE'] = intval($arElement['PROPERTY_'.$arParams['IBLOCK_PAYPROP_ID'].'_VALUE']);
			}
		} elseif($arParams['ENABLE_ALT']=='Y' && $arParams['ALT_NAME'] && $arParams['ALT_PRICE'] && !$Order) {
			$arResult['NAME'] = $arParams['ALT_NAME'];
			$arResult['PRICE'] = $arParams['ALT_PRICE'];
		}
		if($Order) {
			$arResult['ORDER'] = $Order;
			$arFilter = Array("CODE" => $Order);
			$where = self::DBWhere($arFilter);
			$rsData = $DB->Query("SELECT F.* FROM b_ambersite_quickpay F $where", false, $err_mess.__LINE__);
			if($arData = $rsData->Fetch()) {
				$arResult['ORDERCORRECT'] = 'Y';
				$arResult['NAME'] = $arData['PRODUCT'];
				$arResult['NAMEZAKAZA'] = $arData['ID'].' '.GetMessage("OT").' '.ToLower(FormatDate('j F Y', MakeTimeStamp($DB->FormatDate($arData['DATE'], 'YYYY-MM-DD HH:MI:SS', 'DD.MM.YYYY HH:MI:SS'))));
				$arResult['PRICE'] = $arData['SUM'];
				$arResult['PAYTYPE'] = $arData['PAYTYPE'];
				$arResult['STATUS'] = '<font color="orange">'.GetMessage("OGIDAETSYA_PODTVERGDENIE_OPLATU").'</font>';
				if($arData['PAID']=='Y') $arResult['STATUS'] = '<font color="green">'.GetMessage("OPLACHEN").'</font>';
			}
		}
		return $arResult;
	}}
	
	function Add($request) {
	if (self::Show()) {
		global $DB;
		$DB->PrepareFields('b_ambersite_quickpay');  
		foreach($request as $n=>$arItem) { 
			$arFields[$n] = "'".trim($DB->ForSql($arItem))."'";
		}
		$arFields['DATE'] = $DB->GetNowFunction();
		$arFields['CODE'] = "'".trim($DB->ForSql(randString(10)))."'";
		$DB->StartTransaction(); 
		$ID = $DB->Insert('b_ambersite_quickpay', $arFields, $err_mess.__LINE__); $ID = intval($ID); 
		if ($ID>0) {$DB->Commit(); return $ID;} else {$DB->Rollback(); return false;}
	}}
	
	function Update($id, $request) {
	if (self::Show()) {
		global $DB;
		$DB->PrepareFields('b_ambersite_quickpay');  
		foreach($request as $n=>$arItem) { 
			$arFields[$n] = "'".trim($DB->ForSql($arItem))."'";
		}
		$DB->StartTransaction(); 
		$COUNT = $DB->Update('b_ambersite_quickpay', $arFields, "WHERE ID='".$id."'", $err_mess.__LINE__); $COUNT = intval($COUNT); 
		if ($COUNT>0) {$DB->Commit(); return $COUNT;} else {$DB->Rollback(); return false;}
	}}
	
	function Send($request) {
	if (self::Show()) {
		if(SITE_CHARSET == 'windows-1251') {
			foreach($request as $n=>$arItem) { 
				$request[$n] = iconv('UTF-8', 'CP1251', $arItem); 
			}
		}
		
		$json = array(); 
		$phone_simply = str_replace(array(' (', ') ', '-'), '', $request['phone']); 
		$thispath = $request['thispath'];
		
		$request = array('PRODUCT' => $request['productname'], 'FIO' => $request['fio'], 'PHONE' => $request['phone'], 'EMAIL' => $request['email'], 'KOMM' => $request['comment'], 'PAYTYPE' => $request['paymentType'], 'SUM' => $request['sum'], 'PAID' => 'N', 'COUNT' => $request['count']);
		if($ID = self::Add($request)) {
			global $DB;
			$arFilter = Array("ID" => $ID);
			$where = self::DBWhere($arFilter);
			$rsData = $DB->Query("SELECT F.* FROM b_ambersite_quickpay F $where", false, $err_mess.__LINE__);
			if($arData = $rsData->Fetch()) {
				$json['ORDERCODE'] = $arData['CODE'];
				if(SITE_CHARSET == 'windows-1251') $json['TARGET'] = iconv('CP1251', 'UTF-8', GetMessage("OPLATA_ZAKAZA").' N'.$arData['ID']); else $json['TARGET'] = GetMessage("OPLATA_ZAKAZA").' N'.$arData['ID'];
				$newpath = self::GetCurUrlParam($thispath, 'qp_order='.$arData['CODE']);
				$json['SUCCESSURL'] = 'http://'.$_SERVER['HTTP_HOST'].$newpath;
				$arEventFields = $arData; 
				if($arEventFields['PAYTYPE']=='AC') $arEventFields['PAYTYPE'] = GetMessage("BANKOVSKOJ_KARTOJ"); 
				if($arEventFields['PAYTYPE']=='PC') $arEventFields['PAYTYPE'] = GetMessage("YANDEX_DENGAMI"); 
				if($arEventFields['PAYTYPE']=='MC') $arEventFields['PAYTYPE'] = GetMessage("S_BALANSA_MOBILNOGO"); 
				if(CEvent::Send("AS_QUICKPAY_ADD", SITE_ID, $arEventFields, "N")) $json['SEND'] = 'Y';
			}
		} 
		
		return json_encode($json);
	}}
	
	function Confirm($secretkey, $request) {
	if (self::Show()) {
		global $DB;
		$sha1_hash = sha1($request['notification_type'].'&'.$request['operation_id'].'&'.$request['amount'].'&'.$request['currency'].'&'.$request['datetime'].'&'.$request['sender'].'&'.$request['codepro'].'&'.$secretkey.'&'.$request['label']);
		if($request['sha1_hash'] == $sha1_hash) {
			$DB->PrepareFields('b_ambersite_quickpay');
			$arFields['PAID'] = "'Y'";
			$DB->StartTransaction();
			$COUNT = $DB->Update('b_ambersite_quickpay', $arFields, "WHERE CODE LIKE ('%".trim($DB->ForSql($request['label']))."%')", $err_mess.__LINE__); $COUNT = intval($COUNT); 
			if ($COUNT>0) {
				$DB->Commit();
				$arFilter = Array("CODE" => $request['label']);
				$where = self::DBWhere($arFilter);
				$rsData = $DB->Query("SELECT F.* FROM b_ambersite_quickpay F $where", false, $err_mess.__LINE__);
				$arEventFields = array();
				if($arData = $rsData->Fetch()) {
					$arEventFields = $arData;
					if($arEventFields['PAYTYPE']=='AC') $arEventFields['PAYTYPE'] = GetMessage("BANKOVSKOJ_KARTOJ"); 
					if($arEventFields['PAYTYPE']=='PC') $arEventFields['PAYTYPE'] = GetMessage("YANDEX_DENGAMI"); 
					if($arEventFields['PAYTYPE']=='MC') $arEventFields['PAYTYPE'] = GetMessage("S_BALANSA_MOBILNOGO");
					CEvent::Send("AS_QUICKPAY_CONFIRM", SITE_ID, $arEventFields, "N");
				}
				echo 'OK';
				return true;}
			else {$DB->Rollback(); echo 'ERROR DB'; return false;}
		} else {echo 'ERROR SHA'; return false;}
	}}
	
	function GetCurUrlParam($url, $strParam="", $arParamKill=array(), $get_index_page=null) {
        $sUrlPath = $url;
        $strNavQueryString = DeleteParam($arParamKill);
        if($strNavQueryString <> "" && $strParam <> "")
            $strNavQueryString = "&".$strNavQueryString;
        if($strNavQueryString == "" && $strParam == "")
            return $sUrlPath;
        else
            return $sUrlPath."?".$strParam.$strNavQueryString;
    }
}
?>