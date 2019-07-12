<?php

$set = array(
	'LINK_VK' => 'Р РЋРЎРѓРЎвЂ№Р В»Р С”Р В° Р Р…Р В° Vkontakte',
	'LINK_INSTAGRAM' => 'Р РЋРЎРѓРЎвЂ№Р В»Р С”Р В° Р Р…Р В° Instagram',
	'LINK_YOUTUBE' => 'Р РЋРЎРѓРЎвЂ№Р В»Р С”Р В° Р Р…Р В° YouTube',
);

$arTemplateParameters = array();
foreach ($set as $k => $val) {
	$arTemplateParameters[$k] = array(
		'NAME' => $val,
		'COLS' => 35,
		'ROWS' => 3
	);
}
