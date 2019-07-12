<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$phones = $arResult["PROPERTY_PHONES_VALUE"];
$phones = is_array($phones) ? $phones : array($phones);

foreach ($phones as &$phone)
{
	$tmp = $phone;
	$phone = preg_replace('#(\(.*?\))\s*(.*)#', '$1 <span class="contact-feedback-phone-number">$2</span>', $phone);
	if ($phone == $tmp)
		$phone = '<span class="contact-feedback-phone-number">' . $phone . '</span>';
}
unset($phone);

/** @var $this CBitrixComponentTemplate */
if ($this->__component->getParent())
	$this->AddEditAction($arResult['ID'], $arResult['PANEL']['EDIT_LINK'], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_EDIT"));

?><div class="contact-feedback" id="<?=$this->GetEditAreaId($arResult["ID"])?>">
	<div class="contact-feedback-text"><?=GetMessage("CITRUS_REALTY_FEEDBACK_TEXT")?></div>
	<div class="contact-feedback-phone">
		<p><?=implode('<br>', $phones)?></p>
	</div>
	<div class="contact-corner-button">
		<a href="<?=SITE_DIR?>ajax/request.php" class="ajax-popup"><?=GetMessage("CITRUS_REALTY_FEEDBACK_BUTTON")?></a>
	</div>
</div>
<?
