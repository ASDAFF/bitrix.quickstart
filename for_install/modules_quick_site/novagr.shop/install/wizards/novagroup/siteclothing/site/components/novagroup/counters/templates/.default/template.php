<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @var CBitrixComponentTemplate $this
 */
$this->setFrameMode(true);
if (!empty($arResult["DETAIL_TEXT"])) {
    ?>
    <?=$arResult['~DETAIL_TEXT']?>
    <?
}
?><??>