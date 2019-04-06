<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $DB;

$res = $DB->Query( "SELECT `b_event_message`.`ID`, `b_event_message`.`EVENT_NAME`, `b_event_message`.`MESSAGE`, `b_event_type`.`NAME`
	FROM `b_event_type`,`b_event_message`
	WHERE `b_event_type`.`EVENT_NAME`=`b_event_message`.`EVENT_NAME` AND `b_event_type`.`LID` = '".LANGUAGE_ID."' ORDER BY `b_event_type`.`NAME`" );
$messages = array();
$events = array();
while( $row = $res->getNext() ){
    $events[ $row['ID'] ] = array( 'name'=>$row['NAME'], 'eventname'=>$row['EVENT_NAME'] );
    $messages[ $row['EVENT_NAME'] ] = $row['MESSAGE'];
}

//Params
$arParams["MAIL_TEMPLATE_TYPE"] = ($arParams["MAIL_TEMPLATE_TYPE"]!=0) ? $arParams["MAIL_TEMPLATE_TYPE"] : '';
$arParams["MAIL_TEMPLATE_EVENT_ID"] = (isset($arParams["MAIL_TEMPLATE_EVENT_ID"])) ? $arParams["MAIL_TEMPLATE_EVENT_ID"] : 0;


if( $arParams['MAIL_TEMPLATE_EVENT_ID'] > 0 ){
    $res = $DB->Query( "SELECT * FROM `b_event` WHERE `ID`='".$arParams['MAIL_TEMPLATE_EVENT_ID']."'" );
}else{
    if( !empty($arParams['MAIL_TEMPLATE_TYPE']) && isset( $events[$arParams['MAIL_TEMPLATE_TYPE']] ) ){
        $tmplName = $events[ $arParams['MAIL_TEMPLATE_TYPE'] ]['eventname'];
        $res = $DB->Query( "SELECT * FROM `b_event` WHERE `EVENT_NAME`='".$tmplName."' ORDER BY `DATE_INSERT` DESC" );
    }else{
        $res = $DB->Query( "SELECT * FROM `b_event` ORDER BY `DATE_INSERT` DESC" );
    }
}

if($res->SelectedRowsCount()>0){
    $i=0;
    $dt = 0;
    $color = 1;
    while( $row = $res->getNext() ){
        if( isset($messages[ $row['EVENT_NAME'] ]) ){
            $mess = str_replace( "\n", '<br/>', $messages[ $row['EVENT_NAME'] ] );
            parse_str( $row['C_FIELDS'], $fields );
            foreach( $fields as $key => $val ){
                $mess = str_replace( '#'.$key.'#', $val, $mess );
            }
        }else{
            $mess = str_replace( '&', '; ', $row['C_FIELDS'] );
        }
        $tmp = explode( ' ', $row['DATE_INSERT'] );
        if( $dt != $tmp[0] ){
            $dt = $tmp[0];
            $color = intval( !(bool)$color );
        }

        $event_name = $row['EVENT_NAME'];
        foreach($events as $key=>$val){
            //dump($val);dump($row['EVENT_NAME']);
            if($val['eventname']==$row['EVENT_NAME']){
                $event_name = $val['name'];
            }else{
                continue;
            }
        }

        $arResult['ITEMS'][$i]['ID'] = $row['ID'];
        $arResult['ITEMS'][$i]['EVENT_NAME'] = $event_name;
        $arResult['ITEMS'][$i]['DATE_INSERT'] = preg_replace('/(\d{4})-(\d{2})-(\d{2})\s+(.*)/', '${3}.${2}.${1}<br/>${4}', $row['DATE_INSERT']);
        $arResult['ITEMS'][$i]['SUCCESS_EXEC'] = $row['SUCCESS_EXEC'] == 'Y' ? GetMessage("ITLOGIC_MESSAGES_DA") : '<b><u>'.GetMessage("ITLOGIC_MESSAGES_NET").'</u></b>';
        $arResult['ITEMS'][$i]['DATE_EXEC'] = preg_replace('/(\d{4})-(\d{2})-(\d{2})\s+(.*)/', '${3}.${2}.${1}<br/>${4}', $row['DATE_EXEC']);
        $arResult['ITEMS'][$i]['DATE_EXEC'] = preg_replace('/(\d{4})-(\d{2})-(\d{2})\s+(.*)/', '${3}.${2}.${1}<br/>${4}', $row['DATE_EXEC']);
        $arResult['ITEMS'][$i]['MESS'] = $mess;
        $arResult['ITEMS'][$i]['COLOR'] = $color;

        $i++;
    }
    $arResult['itemsCount'] = $i;
}else{
    $arResult['itemsCount'] = -1;
}

$this->IncludeComponentTemplate();
?>
