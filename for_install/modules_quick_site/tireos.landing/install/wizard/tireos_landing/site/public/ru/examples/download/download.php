<?
$sCurUrl = urldecode($_SERVER["REQUEST_URI"]);
$sCurUrl = str_replace("\0", "", $sCurUrl);

if($p = strpos($sCurUrl, "?"))
{
	$sParams = substr($sCurUrl, $p+1);
	parse_str($sParams, $arParams);
	$GLOBALS += $arParams;
	$_GET = $arParams;
	$HTTP_GET_VARS = $_GET;
	$_REQUEST += $arParams;
	$sCurUrl = substr($sCurUrl, 0, $p);
}

$sCurUrl = preg_replace("/\.+/", ".", $sCurUrl);
$sCurUrl = preg_replace("'[\\/]+'", "/", $sCurUrl);

$DIR = dirname($sCurUrl);
$file = substr($sCurUrl, strlen($DIR)+1);
$filename = $_SERVER["DOCUMENT_ROOT"].$DIR."/files/".$file;

if(file_exists($filename) && is_file($filename))
{
	session_cache_limiter('');
	session_start();

	$cur_pos = 0;
	$p = (isset($_SERVER["HTTP_RANGE"])) ? strpos($_SERVER["HTTP_RANGE"], "=") : 0;
	if(intval($p)>0)
	{
		$bytes = substr($_SERVER["HTTP_RANGE"], $p+1);
		$p = strpos($bytes, "-");
		if($p!==false) $cur_pos = intval(substr($bytes, 0, $p));
	}
	include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	if(CModule::IncludeModule("statistic"))
	{
		if($cur_pos<=0) // проверим скачивается ли с самого начала
		{
			if(strlen($event1)<=0 && strlen($event2)<=0)
			{
				$event1 = "download";
				$event2 = $file;
			}
			$e = $event1."/".$event2."/".$event3;
			if(!in_array($e, $_SESSION["DOWNLOAD_EVENTS"])) // проверим не скачивался ли в данной сессии
			{
				if (intval($_SESSION["SESS_SEARCHER_ID"]) <= 0) 
				{
					$w = CStatEvent::GetByEvents($event1, $event2);
					$wr = $w->Fetch();
					$z = CStatEvent::GetEventsByGuest($_SESSION["SESS_GUEST_ID"], $wr["EVENT_ID"], $event3, 21600);
					if(!($zr=$z->Fetch())) // проверим не скачивал ли посетитель за последние 6 часов
					{
						CStatistic::Set_Event($event1, $event2, $event3);
						$_SESSION["DOWNLOAD_EVENTS"][] = $e;
					}
				}
			}
		}
	}
	ob_end_clean();
	session_write_close();
	LocalRedirect($DIR."/files/".$file);
}
else
{
	include($_SERVER["DOCUMENT_ROOT"]."/404.php");
}
?>
