<?php
$APPLICATION->IncludeComponent(
	"bitrix:news",
	"",
	array(
		"SEF_FOLDER" => $arResult["URL_TEMPLATES_REPLACED"]["parent_page_index"],
		"SEF_URL_TEMPLATES" => array(
			"news" => "",
			"section" => "",
			"detail" => "#ELEMENT_CODE#/",
		)
	),
	false
);