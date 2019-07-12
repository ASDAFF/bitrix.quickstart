<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       # 
* # mailto:info@smartrealt.com      #
* ###################################
*/

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$bSelected = false;

if (count($arResult) > 0)
{
    ?><ul class="top-menu"><?
    
    foreach ($arResult as $iIndex => $arMenuItem)
    {
        ?><li><a href="<?php echo $arMenuItem['LINK'] ?>" <?php if ($arMenuItem['SELECTED'] && !$bSelected) { echo 'class="selected"'; $bSelected = true; } ?>><?php echo $arMenuItem['TEXT'] ?></a></li><?
    }
        
    ?></ul><?
}
?>
