<?
global $DBType, $DB, $MESS, $APPLICATION;
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	'redsign.daysarticle2',
	array(
		'CRSDA2Tab' => 'classes/general/tab.php',
		'CRSDA2Main' => 'classes/general/main.php',
		'CRSDA2Elements' => 'classes/'.$DBType.'/daysarticle2.php',
	)
);

if(!function_exists('RSDA2_GetProfiSize'))
{
	function RSDA2_GetProfiSize( $nowW, $nowH, $maxW, $maxH )
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

if(!function_exists('RSDA2_QBGEndWord'))
{
	function RSDA2_QBGEndWord($num, $end1, $end2, $end3)
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