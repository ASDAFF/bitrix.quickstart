<?
if(LANG_CHARSET){ header('Content-Type: text/html; charset='.LANG_CHARSET); }
global $MESS;
$MESS ['SHEEPLA_REGEXP_IS_STREET'] = '/^(��|��\.|�����|��|��\.|��\-�|��������|��\-��|��\-�|������)$/iu';
$MESS ['SHEEPLA_REGEXP_IS_HOUSE']  = '/(�|�|�\.|�\.|���|���)$/u';
?>