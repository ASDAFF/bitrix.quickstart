<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//$arParams['TITLE'];
//$arParams['ACCOUNTS'];

$arResult['WIDTH'] = intval($arParams['WIDTH']);
$arResult['HEIGHT'] = intval($arParams['HEIGHT']);
$arResult['TITLE'] = $arParams['TITLE'];

if($arParams['JQUERY'] == 'Y') $APPLICATION->AddHeadString('<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>');

$arResult['ACCOUNTS'] = explode(',', $arParams['ACCOUNTS']);
if(count($arResult['ACCOUNTS']) > 1)
{
	$str = '';
	foreach($arResult['ACCOUNTS'] as $account) $str .= "'".trim($account)."',";
	$arResult['ACCOUNTS'] = substr($str, 0, strlen($str) - 1);
}
else
{
	$arResult['ACCOUNTS'] = "'".$arResult['ACCOUNTS'][0]."'";
}

$js = '
<script type="text/javascript">
var tweetUsers = ['.$arResult['ACCOUNTS'].'];
var TWLINE_NOW = "'.GetMessage('NOW').'";
var TWLINE_SECONDS_AGO = "'.GetMessage('SECONDS_AGO').'";
var TWLINE_ONE_MINUTE = "'.GetMessage('ONE_MINUTE').'";
var TWLINE_MINUTE = "'.GetMessage('MINUTE').'";
var TWLINE_MINUTES = "'.GetMessage('MINUTES').'";
var TWLINE_AGO = "'.GetMessage('AGO').'";
var TWLINE_HOUR = "'.GetMessage('HOUR').'";
var TWLINE_ONE_HOUR = "'.GetMessage('ONE_HOUR').'";
var TWLINE_HOURS = "'.GetMessage('HOURS').'";
var TWLINE_YESTERDAY = "'.GetMessage('YESTERDAY').'";

var TWLINE_JAN = "'.GetMessage('JAN').'";
var TWLINE_FEB = "'.GetMessage('FEB').'"; 
var TWLINE_MAR = "'.GetMessage('MAR').'";
var TWLINE_APR = "'.GetMessage('APR').'";
var TWLINE_MAY = "'.GetMessage('MAY').'";
var TWLINE_JUN = "'.GetMessage('JUN').'";
var TWLINE_JUL = "'.GetMessage('JUL').'";
var TWLINE_AUG = "'.GetMessage('AUG').'";
var TWLINE_SEP = "'.GetMessage('SEP').'";
var TWLINE_OCT = "'.GetMessage('OCT').'";
var TWLINE_NOV = "'.GetMessage('NOV').'";
var TWLINE_DEC = "'.GetMessage('DEC').'";
</script>';

$APPLICATION->AddHeadString($js);

$this->IncludeComponentTemplate();
?>