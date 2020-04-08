<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

use Bitrix\Main\Page\FrameStatic;
use Bitrix\Main\Localization\Loc; 

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

Loc::loadMessages(__FILE__);

if(!count($arResult["RUBRICS"])) {
	ShowError(Loc::getMessage('subscr_form_rubrics_ERROR'));
	return;
}

$dynamicArea = new FrameStatic("sender-subscribe-info");
$dynamicArea->setContainerID("subscribe-info");
$dynamicArea->startDynamicArea();

?>
<? if (isset($arResult['MESSAGE'])) {
	?>
	<div class="modal fade bs-example-modal-sm" id="subscribe-info">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-body">
					<h4 class="modal-title"><?=Loc::getMessage('subscr_form_rubrics_MODAL_TITLE')?></h4>
					<?
					if (strlen($arResult["MESSAGE"]["TEXT"]) && $arResult['MESSAGE']['TYPE'] != 'ERROR') {
						ShowNote($arResult["MESSAGE"]["TEXT"]);
					} elseif (strlen($arResult["MESSAGE"]["TEXT"])) {
						ShowError($arResult["MESSAGE"]["TEXT"]);
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<script>
		$('#subscribe-info').modal('show');
	</script>
	<?
}
?>
<form role="form" method="post" action="<?=$arResult["FORM_ACTION"]?>" class="form">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="sender_subscription" value="add">
	<? if (count($arResult["RUBRICS"]) == 1) {
		$itemValue = reset($arResult["RUBRICS"]);
		?>
		<input type="hidden" name="SENDER_SUBSCRIBE_RUB_ID[]" id="SENDER_SUBSCRIBE_RUB_ID_<?=$itemValue["ID"]?>"
		       value="<?=$itemValue["ID"]?>" />
		<?
	}
	else {
		?>
		<div><?=Loc::getMessage("subscr_form_title_desc")?></div>
		<?
		foreach ($arResult["RUBRICS"] as $itemID => $itemValue) {
			?>
			<div>
				<input type="checkbox" name="SENDER_SUBSCRIBE_RUB_ID[]" id="SENDER_SUBSCRIBE_RUB_ID_<?=$itemValue["ID"]?>"
				       value="<?=$itemValue["ID"]?>" checked />
				<label
					for="SENDER_SUBSCRIBE_RUB_ID_<?=$itemValue["ID"]?>"><?=htmlspecialcharsbx($itemValue["NAME"])?></label>
			</div>
			<?
		}
	}

	?>
	<div>
		<input class="field" type="email" name="SENDER_SUBSCRIBE_EMAIL" value=""
		       title="<?=Loc::getMessage("subscr_form_email_title")?>"
		       placeholder="<?=htmlspecialcharsbx(Loc::getMessage('subscr_form_email_title'))?>">
		<button class="btn"><?=Loc::getMessage("subscr_form_button")?></button>
	</div>
</form>
<?
$dynamicArea->finishDynamicArea();
?>
</div>