<? 
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Sale\Location\Admin\LocationHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */

$component = $this->__component;
$component::scaleImages($arResult['JS_DATA'], $arParams['SERVICES_IMAGES_SCALING']);

foreach ($arResult['JS_DATA']['ORDER_PROP']['properties'] as &$property) {
	if ($property['TYPE'] === 'LOCATION' && $property['IS_LOCATION'] === 'Y') {
		if ($property['MULTIPLE'] === 'Y') {

		} else {
			$property['DISPLAY_VALUE'] = [
				LocationHelper::getLocationStringByCode($property['VALUE'][0], ['INVERSE' => true])
			];
		}
	}
}