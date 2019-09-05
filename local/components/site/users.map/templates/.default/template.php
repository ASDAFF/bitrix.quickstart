<? use Bitrix\Main\Localization\Loc;
use Site\Main\User;

if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true ) {
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
$this->setFrameMode(true);
?>
<?
if( empty( $arResult['ITEMS'] ) ) {
	return;
}
?>

<section class="map">
	<div class="container">
		<h2 class="map__title"><?=Loc::getMessage('HEADER')?></h2>
	</div>

	<div class="js-map users-map" id="users-map" data-points='<?=json_encode($arResult['CITIES'])?>'></div>
	<div class="map__under">
		<div class="container map__container">
			<div class="map__num-left">
				<div class="cout-mob__wrap"><span class="cout-mobcount cout-mobcount_map flai"><?=number_format($arResult['PROGRAMS_COUNT'], 0, '', ' ')?></span><span class="cout-mobus cout-mobus_map">афиш и программ <br> проверено</span>
				</div>
				<div class="cout-mob__wrap cout-mob__wrap_purpl"><span class="cout-mobcount cout-mobcount_map flai"><?=number_format($arResult['USERS_COUNT'], 0, '', ' ')?></span><span class="cout-mobus cout-mobus_map">участников</span>
				</div>
			</div>
			<div class="map__num-right"><a href="/ajax/form/getRegForm/" class="js-fansibox btn btn-bordo btn-bordo_map">Присоединиться</a>
			</div>
		</div>
	</div>
</section>