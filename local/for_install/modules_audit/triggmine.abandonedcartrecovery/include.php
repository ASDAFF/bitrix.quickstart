<?php
require_once(dirname(__FILE__) . "/classes/triggmine.class.php");
require_once(dirname(__FILE__) . "/classes/triggmine_debug.class.php");

CModule::AddAutoloadClasses("triggmine", array(
	"CTriggMine" => dirname(__FILE__)."/classes/triggmine.class.php",
    "CTriggMineDebug" => dirname(__FILE__)."/classes/triggmine_debug.class.php"
  )
);


?>
