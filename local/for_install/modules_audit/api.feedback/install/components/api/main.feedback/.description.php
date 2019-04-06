<?
	if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true )
		die();

	$arComponentDescription = array(
		"NAME"        => GetMessage("CP_NAME"),
		"DESCRIPTION" => GetMessage("CP_DESCRIPTION"),
		"ICON"        => "/images/feedback.png",
		"PATH"        => array(
			"ID"    => "tuning-soft",
			"NAME"  => GetMessage("CP_NODE_NAME"),
			"CHILD" => array(
				"ID"   => "folder.other",
				"NAME" => GetMessage("CP_FOLDER_NAME"),
				"SORT" => 20,
			),
		),
	);