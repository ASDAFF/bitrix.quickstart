<?
$sCurUrl = urldecode($_SERVER["REQUEST_URI"]);
$sCurUrl = str_replace("\0", "", $sCurUrl);
$sCurUrl = preg_replace("#[\\\\\\/]+#", "/", $sCurUrl);
$sCurUrl = preg_replace("#\\.+[\\/]#", "", $sCurUrl);

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

$DIR = dirname($sCurUrl);
$file = substr($sCurUrl, strlen($DIR)+1);
$filename = $_SERVER["DOCUMENT_ROOT"].$DIR."/files/".$file;

if(file_exists($filename) && is_file($filename))
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	@set_time_limit(0);
	$FILE_PERM = $APPLICATION->GetFileAccessPermission($DIR."/files/".$file, $USER->GetUserGroupArray());
	$FILE_PERM = (strlen($FILE_PERM)>0 ? $FILE_PERM : "D");
	if($FILE_PERM<"R")
		LocalRedirect($DIR."/auth.php?fname=".urlencode($file)."&DIR=".urlencode($DIR));
	else
	{
		$filesize = filesize($filename);
		$f = fopen($filename, "rb");
		$cur_pos = 0;
		$size = $filesize-1;

		if($_SERVER["REQUEST_METHOD"]=="HEAD")
		{
			CHTTP::SetStatus("200 OK");
			header("Accept-Ranges: bytes");
			header("Content-Length: ".$filesize);
			header("Content-Type: application/force-download; name=\"".$file."\"");
			header("Last-Modified: ".date("r",filemtime($filename)));
		}
		else
		{
			$p = strpos($_SERVER["HTTP_RANGE"], "=");
			if(intval($p)>0)
			{
				$bytes = substr($_SERVER["HTTP_RANGE"], $p+1);
				$p = strpos($bytes, "-");
				if($p!==false)
				{
					$cur_pos = IntVal(substr($bytes, 0, $p));
					$size = IntVal(substr($bytes, $p+1));
					if($size<=0)
						$size = $filesize - 1;
					if($cur_pos>$size)
					{
						$cur_pos = 0;
						$size = $filesize - 1;
					}
					fseek($f, $cur_pos);
				}
			}

			if(intval($cur_pos)>0)
			{
				CHTTP::SetStatus("206 Partial Content");
			}
			else
			{
				session_cache_limiter('');
				session_start();
				if(CModule::IncludeModule("statistic") && intval($_SESSION["SESS_SEARCHER_ID"]) <= 0)
				{
					if(strlen($event1)<=0 && strlen($event2)<=0)
					{
						$event1 = "download";
						$event2 = "private";
						$event3 = $file;
					}
					$e = $event1."/".$event2."/".$event3;
					if(!in_array($e, $_SESSION["DOWNLOAD_EVENTS"])) // проверим не скачивался ли в данной сессии
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
				ob_end_clean();
				session_write_close();
				CHTTP::SetStatus("200 OK");
			}

			header("Content-Type: application/force-download; name=\"".$file."\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".($size-$cur_pos+1));
			header("Accept-Ranges: bytes");
			header("Content-Range: bytes ".$cur_pos."-".$size."/".$filesize);
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Expires: 0");
			header("Pragma: public");

			$str = "";
			while($cur_pos<=$size)
			{
				$bufsize = 32768;
				if($bufsize+$cur_pos>$size)
					$bufsize = $size - $cur_pos + 1;
				$cur_pos += $bufsize;
				$p = fread($f, $bufsize);
				echo $p;
				flush();
			}
			fclose ($f);
			die();
		}
	}
}
else
	include($_SERVER["DOCUMENT_ROOT"]."/404.php");
?>
