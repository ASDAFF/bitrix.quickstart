<?
class CmcartTasks
{
	function TaskautotimeSet(&$arFields)
	{ 
	/*
	$f=fopen($_SERVER["DOCUMENT_ROOT"]."/logtask.txt", "a+");
		fwrite($f, print_r($arFields,1)."\n========\n");
		fclose($f);
	*/
		//if ($arFields["ALLOW_TIME_TRACKING"]=="N")
			$arFields["ALLOW_TIME_TRACKING"]="Y";
	}

}
?>