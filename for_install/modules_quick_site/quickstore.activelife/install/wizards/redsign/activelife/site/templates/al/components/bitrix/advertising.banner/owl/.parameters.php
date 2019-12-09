<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (!\Bitrix\Main\Loader::includeModule('iblock')) {
	return;
}

$arAnimationTypes = array('bounce' => 'bounce','flash' => 'flash','pulse' => 'pulse', 'rubberBand' => 'rubberBand', 'shake' => 'shake', 'swing' => 'swing', 'tada' => 'tada', 'wobble' => 'wobble', 'bounceIn' => 'bounceIn', 'bounceInDown' => 'bounceInDown', 'bounceInLeft' => 'bounceInLeft', 'bounceInRight' => 'bounceInRight', 'bounceInUp' => 'bounceInUp', 'bounceOut' => 'bounceOut', 'bounceOutDown' => 'bounceOutDown', 'bounceOutLeft' => 'bounceOutLeft', 'bounceOutRight' => 'bounceOutRight', 'bounceOutUp' => 'bounceOutUp', 'fadeIn' => 'fadeIn', 'fadeInDown' => 'fadeInDown', 'fadeInDownBig' => 'fadeInDownBig', 'fadeInLeft' => 'fadeInLeft', 'fadeInLeftBig' => 'fadeInLeftBig', 'fadeInRight' => 'fadeInRight', 'fadeInRightBig' => 'fadeInRightBig', 'fadeInUp' => 'fadeInUp', 'fadeInUpBig' => 'fadeInUpBig', 'fadeOut' => 'fadeOut', 'fadeOutDown' => 'fadeOutDown', 'fadeOutDownBig' => 'fadeOutDownBig', 'fadeOutLeft' => 'fadeOutLeft', 'fadeOutLeftBig' => 'fadeOutLeftBig', 'fadeOutRight' => 'fadeOutRight', 'fadeOutRightBig' => 'fadeOutRightBig', 'fadeOutUp' => 'fadeOutUp', 'fadeOutUpBig' => 'fadeOutUpBig', 'flip' => 'flip', 'flipInX' => 'flipInX', 'flipInY' => 'flipInY','flipOutX' => 'flipOutX', 'flipOutY' => 'flipOutY', 'lightSpeedIn' => 'lightSpeedIn', 'lightSpeedOut' => 'lightSpeedOut', 'rotateIn' => 'rotateIn', 'rotateInDownLeft' => 'rotateInDownLeft', 'rotateInDownRight' => 'rotateInDownRight', 'rotateInUpLeft' => 'rotateInUpLeft', 'rotateInUpRight' => 'rotateInUpRight', 'rotateOut' => 'rotateOut', 'rotateOutDownLeft' => 'rotateOutDownLeft', 'rotateOutDownRight' => 'rotateOutDownRight', 'rotateOutUpLeft' => 'rotateOutUpLeft', 'rotateOutUpRight' => 'rotateOutUpRight', 'hinge' => 'hinge', 'rollIn' => 'rollIn', 'rollOut' => 'rollOut', 'zoomIn' => 'zoomIn', 'zoomInDown' => 'zoomInDown', 'zoomInLeft' => 'zoomInLeft', 'zoomInRight' => 'zoomInRight', 'zoomInUp' => 'zoomInUp', 'zoomOut' => 'zoomOut', 'zoomOutDown' => 'zoomOutDown', 'zoomOutLeft' => 'zoomOutLeft', 'zoomOutRight' => 'zoomOutRight', 'zoomOutUp' => 'zoomOutUp');

$arProperty = array();
if (0 < intval($arCurrentValues['IBLOCK_ID'])) {
	$rsProp = CIBlockProperty::GetList(Array('sort' => 'asc', 'name' => 'asc'), Array('IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], 'ACTIVE' => 'Y'));
	while ($arr = $rsProp->Fetch()) {
		$arProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
	}
}

$defaultListValues = array('-' => getMessage('RS_BH.UNDEFINED'));

$arTemplateParameters = array(
		'SLIDER_AUTOPLAY' => Array(
				'NAME' => getMessage('RS_BH.SLIDER_AUTOPLAY'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'Y',
		),
		'SLIDER_ITEMS' => array(
				'NAME' => getMessage('RS_BH.SLIDER_ITEMS'),
				'TYPE' => 'STRING',
				'DEFAULT' => '1',
		),
		'SLIDER_CENTER' => array(
				'NAME' => getMessage('RS_BH.SLIDER_CENTER'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'Y',
		),
		'SLIDER_LAZYLOAD' => Array(
				'NAME' => getMessage('RS_BH.SLIDER_LAZYLOAD'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'N',
		),
		'SLIDER_LOOP' => Array(
				'NAME' => getMessage('RS_BH.SLIDER_LOOP'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'Y',
		),
		'SLIDER_SMARTSPEED' => Array(
				'NAME' => getMessage('RS_BH.SLIDER_SMARTSPEED'),
				'TYPE' => 'STRING',
				'DEFAULT' => '2000',
		),
		'SLIDER_ANIMATEIN' => array(
				'NAME' => getMessage('RS_BH.SLIDER_ANIMATEIN'),
				'TYPE' => 'LIST',
				'VALUES' => $arAnimationTypes,
				'DEFAULT' => 'fadeIn',
				'ADDITIONAL_VALUES' => 'Y',
		),
		'SLIDER_ANIMATEOUT' => array(
				'NAME' => getMessage('RS_BH.SLIDER_ANIMATEOUT'),
				'TYPE' => 'LIST',
				'VALUES' => $arAnimationTypes,
				'DEFAULT' => 'fadeOut',
				'ADDITIONAL_VALUES' => 'Y',
		),
);

if($arCurrentValues['SLIDER_AUTOPLAY'] == 'Y'){
	$arTemplateParameters['SLIDER_AUTOPLAY_SPEED'] = array(
			'NAME' => getMessage('RS_BH.SLIDER_AUTOPLAY_SPEED'),
			'TYPE' => 'STRING',
			'DEFAULT' => '2000',
	);
	$arTemplateParameters['SLIDER_AUTOPLAY_TIMEOUT'] = array(
			'NAME' => getMessage('RS_BH.SLIDER_AUTOPLAY_TIMEOUT'),
			'TYPE' => 'STRING',
			'DEFAULT' => '5000',
	);
}