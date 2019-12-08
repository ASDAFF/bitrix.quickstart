<?
global $DBType, $DB, $MESS, $APPLICATION;
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	"redsign.devcom",
	array(

	)
);

function REDSIGNDevComGetProfiSize( $nowW, $nowH, $maxW, $maxH )
{
	if($nowW>0 && $nowH>0 && $maxW>0 && $maxH>0)
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
	} else {
		return array();
	}
}