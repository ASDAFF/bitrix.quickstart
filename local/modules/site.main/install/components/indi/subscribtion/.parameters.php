<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arComponentParameters = array(
	"PARAMETERS" => array(
			"SET_TITLE" => Array(
				"PARENT" => "BASE",
				"NAME" => "Устанавливать заголовок",
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
			),
			"SHOW_FORM_TITLE" => Array(
				"PARENT" => "BASE",
				"NAME" => "Показывать заголовок формы",
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
			),
			"FORM_SUBSCRIBE_TITLE" => Array(
				"PARENT" => "BASE",
				"NAME" => "Текст заголовка формы подписки",
				"TYPE" => "STRING",
				"DEFAULT" => "",
			),
			"FORM_UNSUBSCRIBE_TITLE" => Array(
				"PARENT" => "BASE",
				"NAME" => "Текст заголовка формы отписки",
				"TYPE" => "STRING",
				"DEFAULT" => "",
			),
			"FORM_CLASS" => Array(
				"PARENT" => "BASE",
				"NAME" => "CSS класс для формы подписки",
				"TYPE" => "STRING",
				"DEFAULT" => "",
			),
			"FIELD_CLASS" => Array(
				"PARENT" => "BASE",
				"NAME" => "CSS класс для поля ввода подписки",
				"TYPE" => "STRING",
				"DEFAULT" => "",
			),
			"BUTTON_CLASS" => Array(
				"PARENT" => "BASE",
				"NAME" => "CSS класс для кнопки подписки",
				"TYPE" => "STRING",
				"DEFAULT" => "",
			),
	)
);