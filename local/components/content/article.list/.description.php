<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => 'Статьи деревом',
	"ICON" => "/images/icon.gif",
	"COMPLEX" => "N",
	"PATH" => array(
		"ID" => "ASDAFF",
        "NAME" => "ASDAFF",
		"CHILD" => array(
            "ID" => 'Статьи и новости',
			"NAME" => 'Статьи и новости',
			"SORT" => 30
		),
	),
);
?>
