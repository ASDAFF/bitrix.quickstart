<?php

$set = array(
    'LINK_VK' => '—сылка на Vkontakte',
    'LINK_INSTAGRAM' => '—сылка на Instagram',
    'LINK_YOUTUBE' => '—сылка на YouTube',
);


$arTemplateParameters = array();
foreach ($set as $k => $val) {
	$arTemplateParameters[$k] = array(
		'NAME' => $val,
		'COLS' => 35,
		'ROWS' => 3
	);
}
