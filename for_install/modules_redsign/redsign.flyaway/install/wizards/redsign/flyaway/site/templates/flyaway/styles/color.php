<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use \Bitrix\Main\Loader;

if(!Loader::includeModule('redsign.flyaway')) {
    return;
}

$color = $_SESSION['gencolor'];
$secondColor = $_SESSION['secondColor'];

$darketPersent = 15;
list($rr,$gg,$bb) = sscanf($color, '%2x%2x%2x');
if( $rr>0 ) { $rr = $rr - ( floor($rr/100*$darketPersent) ); }
if( $gg>0 ) { $gg = $gg - ( floor($gg/100*$darketPersent) ); }
if( $bb>0 ) { $bb = $bb - ( floor($bb/100*$darketPersent) ); }
$darkenColorRR = dechex($rr);
$darkenColorGG = dechex($gg);
$darkenColorBB = dechex($bb);
if( strlen($darkenColorRR)<2 ) { $darkenColorRR = '0'.$darkenColorRR; }
if( strlen($darkenColorGG)<2 ) { $darkenColorGG = '0'.$darkenColorGG; }
if( strlen($darkenColorBB)<2 ) { $darkenColorBB = '0'.$darkenColorBB; }
$darketnColor = $darkenColorRR.$darkenColorGG.$darkenColorBB;

if(!empty($color)) {
    echo RSFlyaway::getSelectors($color, $secondColor,  $darketnColor);
}

header("Content-type: text/css");