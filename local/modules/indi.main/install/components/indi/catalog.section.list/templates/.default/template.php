<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

foreach($arResult["SECTIONS"] as $arSection) {
    ?>
    <p><?=$arSection["NAME"]?></p>
    <?
}
?>