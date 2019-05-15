<?php
namespace Mlife\Smsservices;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Fields {
	
	public static function getOrderCodes($MCR_EXT){
		
		$str = '';
		$r = \Bitrix\Sale\Internals\OrderPropsTable::getList(array('select'=>array("CODE","NAME","PERSON"=>"PERSON_TYPE.NAME"),"order"=>array("PERSON_TYPE.NAME"=>"ASC","ID"=>"ASC")));
		while($data = $r->fetch()){
			$str .= "#PROPERTY_".$data["CODE"]."# - ".$data["NAME"]."(".$data["PERSON"].")"."<br>";
		}
		$str .= "";
		
		$MCR_EXT = str_replace("#ORDER_SUM#",$str."#ORDER_SUM#",$MCR_EXT);
		
		return $MCR_EXT;
	}
	
	public static function newOrderHtml($value=""){
		
		if(!$value) serialize(array());
		
		$data = unserialize($value);
		
		$macros = '';
		$MCR_EXT = self::getOrderCodes(Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_MACROS_NEWORDER"));
		$html = '<tr><td>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_MACROS").'</td><td>'.$MCR_EXT.'</td></tr><tr>';
		$html .= '<td><b>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_TO").'</b></td>';
		$html .= '<td><input type="text" name="PARAMS_PHONE" value="'.$data['PHONE'].'"/></td>';
		$html .= '</tr>';
		
		$html .= '<td><b>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_APPSMS").'</b></td>';
		if($data['APPSMS'] == 'Y') $checked = ' checked="checked"';
		$html .= '<td><input type="checkbox" name="PARAMS_APPSMS" value="Y"'.$checked.'/></td>';
		$html .= '</tr>';
		
		return $html;
	}
	
	public static function newOrderSave($arFields=array()){
		
		$PARAMS = array(
			"PHONE"=>trim($_REQUEST['PARAMS_PHONE']),
			"APPSMS"=>trim($_REQUEST['PARAMS_APPSMS']),
		);
		$arFields['PARAMS'] = serialize($PARAMS);
		
		return $arFields;
	}
	
	public static function payedOrderHtml($value=""){
		
		if(!$value) serialize(array());
		
		$data = unserialize($value);
		
		$macros = '';
		$MCR_EXT = self::getOrderCodes(Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_MACROS_NEWORDER"));
		$html = '<tr><td>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_MACROS").'</td><td>'.$MCR_EXT.'</td></tr><tr>';
		$html .= '<td><b>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_TO").'</b></td>';
		$html .= '<td><input type="text" name="PARAMS_PHONE" value="'.$data['PHONE'].'"/></td>';
		$html .= '</tr>';
		
		$html .= '<td><b>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_APPSMS").'</b></td>';
		if($data['APPSMS'] == 'Y') $checked = ' checked="checked"';
		$html .= '<td><input type="checkbox" name="PARAMS_APPSMS" value="Y"'.$checked.'/></td>';
		$html .= '</tr>';
		
		$statusAr = array(array("NAME"=>Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_PAYED_Y"),"ID"=>"Y"), array("NAME"=>Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_PAYED_N"),"ID"=>"N"));
		
		$html .= '<td><b>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_PAYED").'</b></td>';
		$html .= '<td>';
		$html .= '<select name="PARAMS_PAYED">';
			foreach($statusAr as $stat){
				$selected = '';
				if($stat['ID'] == $data['PAYED']) $selected = ' selected="selected"';
				$html .= '<option value="'.$stat['ID'].'"'.$selected.'>['.$stat['ID'].'] - '.$stat['NAME'].'</option>';
			}
		$html .= '</select>';
		$html .= '</td>';
		$html .= '</tr>';
		
		return $html;
	}
	
	public static function payedOrderSave($arFields=array()){
		
		$PARAMS = array(
			"PHONE"=>trim($_REQUEST['PARAMS_PHONE']),
			"APPSMS"=>trim($_REQUEST['PARAMS_APPSMS']),
			"PAYED"=>trim($_REQUEST['PARAMS_PAYED']),
		);
		$arFields['PARAMS'] = serialize($PARAMS);
		
		return $arFields;
	}
	
	public static function statusOrderHtml($value=""){
		
		if(!$value) serialize(array());
		
		$data = unserialize($value);
		
		$macros = '';
		$MCR_EXT = self::getOrderCodes(Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_MACROS_NEWORDER"));
		$html = '<tr><td>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_MACROS").'</td><td>'.$MCR_EXT.'</td></tr><tr>';
		
		$html .= '<td><b>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_TO").'</b></td>';
		$html .= '<td><input type="text" name="PARAMS_PHONE" value="'.$data['PHONE'].'"/></td>';
		$html .= '</tr>';
		
		$html .= '<td><b>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_APPSMS").'</b></td>';
		if($data['APPSMS'] == 'Y') $checked = ' checked="checked"';
		$html .= '<td><input type="checkbox" name="PARAMS_APPSMS" value="Y"'.$checked.'/></td>';
		$html .= '</tr>';
		
		$statusOb = \CSaleStatus::GetList();
		$statusAr = array(array("NAME"=>Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_STATUS_ALL"),"ID"=>"ALL"));
		while($d = $statusOb->fetch()){
			$statusAr[$d['ID']] = $d;
		}
		
		$html .= '<td><b>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_STATUS_FROM").'</b></td>';
		$html .= '<td>';
		$html .= '<select name="PARAMS_STATUS_FROM">';
			foreach($statusAr as $stat){
				$selected = '';
				if($stat['ID'] == $data['STATUS_FROM']) $selected = ' selected="selected"';
				$html .= '<option value="'.$stat['ID'].'"'.$selected.'>['.$stat['ID'].'] - '.$stat['NAME'].'</option>';
			}
		$html .= '</select>';
		$html .= '</td>';
		$html .= '</tr>';
		
		$html .= '<td><b>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_STATUS_TO").'</b></td>';
		$html .= '<td>';
		$html .= '<select name="PARAMS_STATUS_TO">';
			foreach($statusAr as $stat){
				$selected = '';
				if($stat['ID'] == $data['STATUS_TO']) $selected = ' selected="selected"';
				$html .= '<option value="'.$stat['ID'].'"'.$selected.'>['.$stat['ID'].'] - '.$stat['NAME'].'</option>';
			}
		$html .= '</select>';
		$html .= '</td>';
		$html .= '</tr>';
		
		return $html;
	}
	
	public static function statusOrderSave($arFields=array()){
		
		$PARAMS = array(
			"PHONE"=>trim($_REQUEST['PARAMS_PHONE']),
			"APPSMS"=>trim($_REQUEST['PARAMS_APPSMS']),
			"STATUS_FROM"=>trim($_REQUEST['PARAMS_STATUS_FROM']),
			"STATUS_TO"=>trim($_REQUEST['PARAMS_STATUS_TO'])
		);
		$arFields['PARAMS'] = serialize($PARAMS);
		
		return $arFields;
	}
	
	public static function eventSendHtml($value=""){
		
		if(!$value) serialize(array());
		
		$data = unserialize($value);
		
		$macros = '';
		
		
		
		$defaultMactosText = "*".Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_MACROS_BXEVENT");
		
		if(!$data['EVENT_NAME'] && $_REQUEST['EVENT']) {
			$data['EVENT_NAME'] = str_replace('MSMS_BXEVENT_','',$_REQUEST['EVENT']);
		}else{
			//print_r($_REQUEST['ID']);
			$dataAr = \Mlife\Smsservices\EventlistTable::getRowById($_REQUEST['ID']);
			$data['EVENT_NAME'] = str_replace('MSMS_BXEVENT_','',$dataAr['EVENT']);
		}
		//print_r($data);die();
		if($data['EVENT_NAME']){
			$resType = \Bitrix\Main\Mail\Internal\EventTypeTable::getList(array(
				'select' => array('NAME','DESCRIPTION'),
				'filter' => array('EVENT_NAME'=>$data['EVENT_NAME'],'LID'=>'ru')
			))->fetch();
			if($resType){
				$defaultMactosText = '<b>'.$resType['NAME'].'</b><br><pre>'.htmlspecialcharsBack($resType['DESCRIPTION']).'</pre>';
			}
			if(strpos($resType['DESCRIPTION'],'#ORDER_ID#')!==false && strpos($data['EVENT_NAME'],'SALE')!==false) {
				$defaultMactosText .= '<br>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_MACROS_NEWORDER_");
			}elseif(strpos($resType['DESCRIPTION'],'#USER_ID#')!==false) {
				$defaultMactosText .= '<br>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_MACROS_USER_");
			}
		}
		
		$allType = \Bitrix\Main\Mail\Internal\EventTypeTable::getList(array(
			'select' => array('NAME','EVENT_NAME'),
			'filter' => array('LID'=>'ru')
		));
		$arAllType = array();
		while($dt = $allType->fetch()){
			$arAllType[$dt['EVENT_NAME']] = '['.$dt['EVENT_NAME'].'] - '.$dt['NAME'];
		}
		
		$html = '<tr><td>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_MACROS").'</td><td>'.$defaultMactosText.'<br>*'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_MACROS_BXEVENT_NOTE").'</td></tr><tr>';
		
		$html .= '<td><b>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_TO").'</b></td>';
		$html .= '<td><input type="text" name="PARAMS_PHONE" value="'.$data['PHONE'].'"/></td>';
		$html .= '</tr>';
		
		$html .= '<td><b>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_APPSMS").'</b></td>';
		if($data['APPSMS'] == 'Y') $checked = ' checked="checked"';
		$html .= '<td><input type="checkbox" name="PARAMS_APPSMS" value="Y"'.$checked.'/></td>';
		$html .= '</tr>';
		
		$html .= '<td><b>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_EVENT_NAME").'</b></td>';
		$html .= '<td>';
		$html .= '<input type="hidden" name="PARAMS_EVENT_NAME" value="'.$data['EVENT_NAME'].'">';
			foreach($arAllType as $statKey=>$stat){
				if($statKey == $data['EVENT_NAME']) {
				$html .= $stat;
				}
			}
		$html .= '';
		$html .= '</td>';
		$html .= '</tr>';
		
		$html .= '<td>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_BXEVENTID").'</td>';
		
		$r_site = \Bitrix\Main\Mail\Internal\EventMessageTable::getList(array(
			'select' => array("ID","SUBJECT"),
			'filter' => array(/*"EVENT_MESSAGE_SITE.SITE_ID"=>$_REQUEST['SITE_ID'],*/ "EVENT_NAME" => $data['EVENT_NAME'], "ACTIVE"=>"Y")
		));
		$opt = '';
		while($d = $r_site->fetch()){
			$opt .= '<option value="'.$d['ID'].'"'.($d['ID']==$data['ID'] ? ' selected="selected"' : '').'>'.$d['ID'].' - '.$d["SUBJECT"].'</option>';
		}
		
		//$html .= '<td><input type="text" name="PARAMS_ID" value="'.$data['ID'].'"/></td>';
		$html .= '<td><select name="PARAMS_ID">'.$opt.'</select></td>';
		$html .= '</tr>';
		
		$html .= '<td>'.Loc::getMessage("MLIFE_SMSSERVICES_FIELDS_BXEVENT_BREAK").'</td>';
		$checked = '';
		if($data['BREAK'] == 'Y') $checked = ' checked="checked"';
		$html .= '<td><input type="checkbox" name="PARAMS_BREAK" value="Y"'.$checked.'/></td>';
		$html .= '</tr>';
		
		return $html;
	}
	
	public static function eventSendSave($arFields=array()){
		
		$PARAMS = array(
			"PHONE"=>trim($_REQUEST['PARAMS_PHONE']),
			"APPSMS"=>trim($_REQUEST['PARAMS_APPSMS']),
			"BREAK"=>($_REQUEST['PARAMS_BREAK'] == "Y") ? "Y" : "N",
			"ID"=>(trim($_REQUEST['PARAMS_ID']) ? trim($_REQUEST['PARAMS_ID']) : 'ALL'),
			"EVENT_NAME"=> trim($_REQUEST['PARAMS_EVENT_NAME'])
		);
		if($PARAMS["EVENT_NAME"]){
			$r_site = \Bitrix\Main\Mail\Internal\EventMessageTable::getList(array(
				'select' => array("ID"),
				'filter' => array(/*"EVENT_MESSAGE_SITE.SITE_ID"=>$_REQUEST['SITE_ID'], */"EVENT_NAME" => $PARAMS["EVENT_NAME"], "ACTIVE"=>"Y")
			));
			$exists = false;
			$exists_ = false;
			while($dt = $r_site->fetch()){
				$exists = $dt['ID'];
				if($PARAMS["ID"] == $dt['ID']) $exists_ = true;
			}
			if(!$exists_ && $exists) $PARAMS["ID"] = $exists;
			if(!$exists) {
				$emess = new \CEventMessage;
				$id = $emess->Add(array('ACTIVE'=>"Y","EVENT_NAME"=>$PARAMS["EVENT_NAME"],"LID"=>array($_REQUEST['SITE_ID']),"EMAIL_FROM"=>"EMPTY@emptu.ru","EMAIL_TO"=>"EMPTY@emptu.ru","SUBJECT"=>"mlife.smsservices","BODY_TYPE"=>"text","MESSAGE"=>"mlife.smsservices"));
				if($PARAMS["ID"] == 'ALL') $PARAMS["ID"] = $id;
				//print_r($emess->LAST_ERROR);die();
			}
		}
		$arFields['PARAMS'] = serialize($PARAMS);
		
		return $arFields;
	}
	
}