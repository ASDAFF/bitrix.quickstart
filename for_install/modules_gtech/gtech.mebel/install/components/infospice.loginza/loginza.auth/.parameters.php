<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arProvides = array(
	"yandex" 	=> "Yandex",
	"google" 	=> "Google",
	"odnoklassniki" => "odnoklassniki",
	"mailru"	=> "Mail.ru",
	"vkontakte"	=> "Vkontakte",
	"facebook"	=> "Facebook",
	"twitter"	=> "Twitter",
	"loginza"	=> "Loginza",
	"mailruapi" => "Mail.ru OpenID",
	"myopenid"	=> "MyOpenID",
	"webmoney"	=> "WebMoney",
	"rambler"	=> "Rambler",
	"flickr"	=> "Flickr",
	"lastfm"	=> "LastFM",
	"verisign"	=> "VeriSign",
	"aol"		=> "AOL",
	"steam"		=> "Steam",
	"openid"	=> "OpenID"
);

$rsGroups = CGroup::GetList(
	($by="c_sort"),
	($order="desc"),
	array(
		"ACTIVE" => "Y"
	)
);
while($itemGroup = $rsGroups->GetNext()) {
	$arGroup[$itemGroup["ID"]] = $itemGroup["NAME"];
}


$arComponentParameters = array(
	"GROUPS" => array(
		"SETTINGS_WIDGET" => array(
			"NAME" => GetMessage("MESS_SETTINGS_WIDGET")
		)
	),
	'PARAMETERS' => array(
		"GROUP_ID" => array(
			"PARENT" 	=> "BASE",
			"TYPE" 	=> "LIST",
			"NAME"	=> GetMessage("MESS_GROUP_ID"),
			"VALUES"=> $arGroup,
			"MULTIPLE" 	=> "Y",
			"DEFAULT" 	=> "2"
		),
		"TEXT_LINK" => array(
			"PARENT" 	=> "SETTINGS_WIDGET",
			"TYPE" 	=> "STRING",
			"NAME"	=> GetMessage("MESS_TEXT_LINK"),
		),
		"IMAGE_LINK" => array(
			"PARENT" 	=> "SETTINGS_WIDGET",
			"TYPE" 	=> "STRING",
			"NAME"	=> GetMessage("MESS_IMAGE_LINK"),
		),
	)
);

$arComponentParameters["PARAMETERS"]["PROVIDERS_SET"] = Array(
	"PARENT" 	=> "BASE",
	"NAME" 		=> GetMessage("MESS_PROVIDERS_SET"),
	"TYPE" 		=> "LIST",
	"VALUES" 	=> $arProvides,
	"MULTIPLE" 	=> "Y",
	"DEFAULT" 	=> array_keys($arProvides)
);

$arComponentParameters["PARAMETERS"]["PROVIDER"] = Array(
	"PARENT" 	=> "BASE",
	"NAME" 		=> GetMessage("MESS_PROVIDER"),
	"TYPE" 		=> "LIST",
	"VALUES" 	=> array_merge(array(""=>GetMessage("MESS_NOT")), $arProvides),
	"MULTIPLE" 	=> "N",
	"DEFAULT" 	=> "",
	"REFRESH" => "Y"
);

$arComponentParameters["PARAMETERS"]["REDIRECT_PAGE"] = Array(
	"PARENT" 	=> "BASE",
	"NAME" 		=> GetMessage("MESS_REDIRECT_PAGE"),
	"TYPE" 		=> "STRING"
);


$arComponentParameters["PARAMETERS"]["LANG"] = Array(
	"PARENT" 	=> "BASE",
	"NAME" 		=> GetMessage("MESS_LANG"),
	"TYPE" 		=> "LIST",
	"VALUES" 	=> array(
		"ru"	=> GetMessage("MESS_RUS"),
		"uk"	=> GetMessage("MESS_UKR"),
		"en"	=> GetMessage("MESS_EN")
	),
	"MULTIPLE" 	=> "N",
	"DEFAULT" 	=> "ru"
);


?>