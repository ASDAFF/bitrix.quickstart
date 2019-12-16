<?
global $DBType, $DB, $MESS, $APPLICATION;
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	"redsign.quickbuy",
	array(
		"CRSQUICKBUYTab" => "classes/general/tab.php",
		"CRSQUICKBUYMain" => "classes/general/main.php",
		"CRSQUICKBUYElements" => "classes/".$DBType."/quickbuy.php",
	)
);

if(!function_exists('GetProfiSize'))
{
	function GetProfiSize( $nowW, $nowH, $maxW, $maxH )
	{
		if($nowW>$maxW || $nowH>$maxH)
		{
			$factorW = $nowW/$maxW;
			$factorH = $nowH/$maxH;
			if($factorW>$factorH)
			{
				$trueW = $maxW;
				$trueH = floor($nowH/$factorW);
			} elseif($factorW<$factorH) {
				$trueW = floor($nowW/$factorH);
				$trueH = $maxH;
			} else {
				$trueW = $maxW;
				$trueH = $maxH;
			}
		} else {
			$trueW = $nowW;
			$trueH = $nowH;
		}
		return array( $trueW, $trueH );
	}
}

if(!function_exists('QBGEndWord'))
{
	function QBGEndWord($num, $end1, $end2, $end3)
	{
		$val = $num % 100;
		if ($val > 10 && $val < 20) return $end3;
		else {
			$val = $num % 10;
			if ($val == 1) return $end1;
			elseif ($val > 1 && $val < 5) return $end2;
			else return $end3;
		}
	}
}