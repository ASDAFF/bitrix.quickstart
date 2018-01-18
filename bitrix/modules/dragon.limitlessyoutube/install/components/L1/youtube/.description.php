<?

	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	$arComponentDescription = array(
			"START_TYPE" => "WINDOW",
			"WIZARD_TYPE" => "INSTALL",
			"NAME" => "HTML5 YouTube Player",
			"DESCRIPTION" => "HTML5 player YouTube",
			"PATH" => array(
				"ID" => "youtube_component",
				"NAME" => "L1mitless",
				"CHILD" => array(
					"ID" => "youtube_block",
					"NAME" => 'Player YouTube'
				)
			)
		);

?>