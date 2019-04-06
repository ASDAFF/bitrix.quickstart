<?
CModule::AddAutoloadClasses("mailtrig.events",
	array(
		"CMailTrigClient" => "classes/general/client.php",
		"CMailTrigEventsHandler" => "classes/general/eventshandler.php",
		"CMailTrigLogger" => "classes/general/logger.php",
		"CMailTrigAPI" => "classes/general/api.php",
	)
);
?>