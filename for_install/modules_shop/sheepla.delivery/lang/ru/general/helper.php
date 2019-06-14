<?
if(LANG_CHARSET){ header('Content-Type: text/html; charset='.LANG_CHARSET); }
global $MESS;
$MESS ['SHEEPLA_REGEXP_IS_STREET'] = '/^(ул|ул\.|улица|пр|пр\.|пр\-т|проспект|пр\-зд|пр\-д|проезд)$/iu';
$MESS ['SHEEPLA_REGEXP_IS_HOUSE']  = '/(д|Д|д\.|Д\.|дом|Дом)$/u';
?>