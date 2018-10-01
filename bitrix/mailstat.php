<?php
require "modules/main/include/prolog_before.php";
global $DB;

// считываем шаблоны почтовых сообщений
$tmplId = @$_GET['tmplid'];
$evtId = intval( @$_GET['evtid'] );
$res = $DB->Query( "SELECT `b_event_message`.`ID`, `b_event_message`.`EVENT_NAME`, `b_event_message`.`MESSAGE`, `b_event_type`.`NAME`
	FROM `b_event_type`,`b_event_message`
	WHERE `b_event_type`.`EVENT_NAME`=`b_event_message`.`EVENT_NAME` ORDER BY `b_event_type`.`NAME`" );
$messages = array();
$events = array();
$select = '<option value="">- не важно -</option>';
while( $row = $res->getNext() ){
	if( $row['ID'] == $tmplId ){
		$selected = 'selected="selected"';
	}else{
		$selected = '';
	}
	$events[ $row['ID'] ] = array( 'name'=>$row['NAME'], 'eventname'=>$row['EVENT_NAME'] );
	$messages[ $row['EVENT_NAME'] ] = $row['MESSAGE'];
	$select .= '<option value="'.$row['ID'].'" '.$selected.'>'.$row['NAME'].'</option>';
}

?><!DOCTYPE html>
<html>
<head>
	<title>Список почтовых сообщений Bitrix</title>
	<meta name="author" content="Куцевалов Константин">
	<meta name="author-E-mail" content="Konstantin@Kutsevalov.name">
	<style>
		body {
			font-family: Arial;
			font-size: 14px;
		}
		.main-block {
			width: 1000px;
			margin: 0 auto;
		}
		table.border1px {
			border-collapse: collapse;
			border-spacing: 0px;
			border-top: 1px solid gray;
			border-left: 1px solid gray;
		}
		table.border1px td, table.border1px th {
			border-right: 1px solid gray;
			border-bottom: 1px solid gray;
			padding: 8px 4px;
			text-align: center;
		}
		table.border1px th {
			background: #262626;
			font-weight: bold;
			color: #fff;
		}
		table.border1px .c1, table.border1px .c3 { width: 80px; }
		.color-0 {  background: #D1FFD4;  }
		.color-1 {  background: #BFCFFF;  }
		.form {
			margin: 0 0 10px 0;
			border: 2px solid gray;
			padding: 6px;
			font-size: 14px;
		}
		.overflow {
			overflow: auto;
			height: 80px;
			font-size: 11px;
		}
	</style>
<head>

<body>
<div class="main-block">
	<h1>Список почтовых сообщений CMS Битрикс</h1>
	<div class="form">
		<form action="" method="get">
		Выберите тип почтового шаблона: <select name="tmplid"><?= $select ?></select>
		<br/>или укажите идентификатор (b_events ID): <input type="text" name="evtid" value="<?= @$_GET['evtid'] ?>" />
		<br/><input type="submit" value="Показать" />
		</form>
	</div>
<?php
// список таблиц
// $res = $DB->Query( "SHOW TABLES" );
// while( $row = $res->getNext() ){
	// $v = array_values( $row );
	// print_r( $v[0] ); echo '<br/>';
// }
// записи из таблицы
// $res = $DB->Query( "SELECT * FROM `b_event_type` LIMIT 1,30" );
// while( $row = $res->getNext() ){
	// print_r( $row ); echo '<br/>';
// }

if( $evtId > 0 ){
	$res = $DB->Query( "SELECT * FROM `b_event` WHERE `ID`='{$evtId}'" );
	$tmplId = 0;
}else{
	if( !empty($tmplId) && isset( $events[$tmplId] ) ){
		$tmplName = $events[ $tmplId ]['eventname'];
		$res = $DB->Query( "SELECT * FROM `b_event` WHERE `EVENT_NAME`='{$tmplName}' ORDER BY `DATE_INSERT` DESC LIMIT 1,200" );
	}else{
		$res = $DB->Query( "SELECT * FROM `b_event` ORDER BY `DATE_INSERT` DESC LIMIT 1,100" );
	}
}
if( $res->SelectedRowsCount() > 0 ){
	?>
	<table class="border1px" width="1000px">
		<tr>
			<th>ID</th>
			<th>Код шаблона</th>
			<th class="c1">Дата установки<br/>в очередь отправки</th>
			<th>Отправлено?</th>
			<th class="c3">Дата отправки</th>
			<th>Содержание сообщения</th>
		</tr><?
	$dt = 0;
	$color = 1;
	while( $row = $res->getNext() ){
		if( isset($messages[ $row['EVENT_NAME'] ]) ){
			$mess = str_replace( "\n", '<br/>', $messages[ $row['EVENT_NAME'] ] );
			parse_str( $row['C_FIELDS'], $fields );
			foreach( $fields as $key => $val ){
				$mess = str_replace( '#'.$key.'#', $val, $mess );
				// $mess .= '#'.$key.'# = '.$val.'<br/>';
			}
		}else{
			$mess = str_replace( '&', '; ', $row['C_FIELDS'] );
		}
		$tmp = explode( ' ', $row['DATE_INSERT'] );
		if( $dt != $tmp[0] ){
			$dt = $tmp[0];
			$color = intval( !(bool)$color );
		}
		?><tr class="color-<?= $color ?>">
			<td><?= $row['ID'] ?></td>
			<td><?= $row['EVENT_NAME'] ?></td>
			<td><?= preg_replace( '/(\d{4})-(\d{2})-(\d{2})\s+(.*)/', '${3}.${2}.${1}<br/>${4}', $row['DATE_INSERT'] ) ?></td>
			<td><?= $row['SUCCESS_EXEC'] == 'Y' ? 'Да' : '<b><u>Нет</u></b>' ?></td>
			<td><?= preg_replace(  '/(\d{4})-(\d{2})-(\d{2})\s+(.*)/', '${3}.${2}.${1}<br/>${4}', $row['DATE_EXEC'] ) ?></td>
			<td><div class="overflow"><?= $mess ?></div></td>
		</tr><?
	}
	?></table><?
}else{
	?><p><b>Сообщений не найдено</b></p><?
}

?></div>
</body>
</html><?

exit;
