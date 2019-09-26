<?

	$arComponentParameters = array(

		"GROUPS" => array(
			"SETTINGS" => array(
				"NAME" => GetMessage("SETTINGS_T")
			),
			"IMAGE" => array(
				"NAME" => GetMessage("IMAGE_T")
			),
			"PARAMS" => array(
				"NAME" => GetMessage("PARAMS_T")
			)
		),

		"PARAMETERS" => array(

			"URL" => array(
				"PARENT" => "SETTINGS",
				"TYPE" => "TEXT",
				"NAME" => GetMessage("URL"),
			),

			"W" => array(
				"PARENT" => "SETTINGS",
				"TYPE" => "TEXT",
				"NAME" => GetMessage("W"),
				"DEFAULT" => "100"
			),

			"H" => array(
				"PARENT" => "SETTINGS",
				"TYPE" => "TEXT",
				"NAME" => GetMessage("H"),
				"DEFAULT" => "100"
			),

			"IMG" => array(
				"PARENT" => "IMAGE",
				"TYPE" => "TEXT",
				"NAME" => GetMessage("IMG_T")
			),

			"Control" => array(
				"PARENT" => "PARAMS",
				"TYPE" => "LIST",
				"NAME" => GetMessage("Control"),
				"MULTIPLE" => "N",
				"VALUES" => array( GetMessage("NO"), GetMessage("YES") ),
				"DEFAULT" => "1"
			),

			"IV" => array(
				"PARENT" => "PARAMS",
				"TYPE" => "LIST",
				"NAME" => GetMessage("IV"),
				"MULTIPLE" => "N",
				"VALUES" => array( GetMessage("NO"), GetMessage("YES") ),
				"DEFAULT" => "0"
			),

			"CC" => array(
				"PARENT" => "PARAMS",
				"TYPE" => "LIST",
				"NAME" => GetMessage("CC"),
				"MULTIPLE" => "N",
				"VALUES" => array( GetMessage("NO"), GetMessage("YES") ),
				"DEFAULT" => "0"
			),

			"FULL" => array(
				"PARENT" => "PARAMS",
				"TYPE" => "LIST",
				"NAME" => GetMessage("FULL"),
				"MULTIPLE" => "N",
				"VALUES" => array( GetMessage("NO"), GetMessage("YES") ),
				"DEFAULT" => "1"
			),

			"LOGO" => array(
				"PARENT" => "PARAMS",
				"TYPE" => "LIST",
				"NAME" => GetMessage("LOGO"),
				"MULTIPLE" => "N",
				"VALUES" => array( GetMessage("NO"), GetMessage("YES") ),
				"DEFAULT" => "0"
			),

			"Autoplay" => array(
				"PARENT" => "PARAMS",
				"TYPE" => "LIST",
				"NAME" => GetMessage("Autoplay"),
				"MULTIPLE" => "N",
				"VALUES" => array( GetMessage("NO"), GetMessage("YES") ),
				"DEFAULT" => "0"
			),

			"LOOP" => array(
				"PARENT" => "PARAMS",
				"TYPE" => "LIST",
				"NAME" => GetMessage("LOOP"),
				"MULTIPLE" => "N",
				"VALUES" => array( GetMessage("NO"), GetMessage("YES") ),
				"DEFAULT" => "0"
			),

		)
	);

?>