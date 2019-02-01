<?
global $DBType;
IncludeModuleLangFile(__FILE__);

$arClassesList = array(
        "HlExport" => "classes/general/HlExport.php",
);
CModule::AddAutoloadClasses(
    "alfa1c.hlexim",
		$arClassesList
    );
?>
