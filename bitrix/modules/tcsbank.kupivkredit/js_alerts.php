<?
	IncludeModuleLangFile(__FILE__);
	$arAlerts = Array(
		"TCS_NO_COURIER"=>GetMessage("TCS_NO_COURIER")
	);
	echo "<script>var TCSAlerts=".$obModule->PHPArrayToJS($arAlerts)."</script>";
?>