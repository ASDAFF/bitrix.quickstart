<?defined("B_PROLOG_INCLUDED")&&B_PROLOG_INCLUDED or die();

$arServices = array(
	"main" => array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => array(
			"files.php", // Copy site files
			"template.php", // Install templates
		),
	),
	"iblock" => array(
		"NAME" => GetMessage("SERVICE_IBLOCK"),
		"STAGES" => array(
			"types.php", //IBlock types
			"news.php",// install iblock news
			"offers.php",// install iblock offers
			"catalog.php",// install iblock catalog
			"colorscheme.php",// install iblock colorscheme
		),
	),

	"sale" => Array(
		"NAME" => GetMessage("SERVICE_SALE_DEMO_DATA"),
		"STAGES" => Array(
			"step1.php", "step2.php",
		),
	),

	"complete" => array(
		"NAME" => GetMessage("SERVICE_COMPLETE"),
		"MODULE_ID" => 'main',
		"STAGES" => array(
			"complete.php", // Copy tmp site files to site dir and cleanup tmp files
		),
	),


);

?>