<?
define("STOP_STATISTICS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/elipseart.siteposition/colors.php");

if(!CModule::IncludeModule("elipseart.siteposition"))
	die();

$module_id = "elipseart.siteposition";

$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($POST_RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/img.php");

$width = COption::GetOptionInt($module_id, "GRAPH_WEIGHT");
$height = COption::GetOptionInt($module_id, "GRAPH_HEIGHT");

$graph_type = ($_GET["graph_type"] == "TOP10") ? "TOP10" : "STD";

$param = UrlDecode($_GET["param"]);
$param = htmlspecialchars_decode($param);
$param = unserialize($param);

$date_end = $_GET["end_date"];

$period = intval($_GET["period"]);
if($period > 90)
	$period = 90;

$date_end = explode("-",ConvertDateTime($date_end, "YYYY-MM-DD"));
$date_end = mktime(0,0,0,$date_end[1],$date_end[2],$date_end[0]);
$find_DATE2 = ConvertTimeStamp($date_end+86400, "SHORT");
$find_DATE1 = ConvertTimeStamp($date_end-86400*$period/*+86400*/, "SHORT");

$rsData = CEASitePosition::GetList(
	array(
		"DATE" => "DESC"
	),
	array(
		"KEYWORD_ID" => $param["KEYWORD_ID"],
		"SEARCH_NAME" => $param["SEARCH_NAME"],
		">DATE" => ConvertDateTime($find_DATE1, "YYYY-MM-DD"),
		"<DATE" => ConvertDateTime($find_DATE2, "YYYY-MM-DD"),
	),
	false
);
while($res = $rsData->Fetch())
{
	$arrPosition[] = array(
		$res["DATE"],
		$res["POSITION"],
		$res["SEARCH_SYSTEM"],
	);
}

$ssDB = CEASitePositionSearchSystem::GetList(array(),array("ACTIVE"=>"Y"));
while($res = $ssDB->Fetch())
{
	$arSearchSystem[] = $res;
}

foreach($arSearchSystem as $val)
{
	$arrParam = array();
	
	foreach($arrPosition as $arParam)
	{
		if($val["NAME"] == $arParam[2])
		{
			$arrParam[] = $arParam;
		}
	}
		
	$arGraphParam[] = $arrParam;
}




$ImageHandle = CreateImageHandle($width, $height, "FFFFFF", true);

$colorFFFFFF = ImageColorAllocate($ImageHandle,255,255,255);
ImageFill($ImageHandle, 0, 0, $colorFFFFFF);

$arrX=Array();
$arrY=Array();
$arrayX=Array();
$arrayY=Array();



$date = array();

foreach($arGraphParam as $key=>$val)
{
	foreach($val as $key2=>$val2)
	{
		$d = explode(" ",$val2[0]); 
		$arGraphParam[$key][$key2][0] = $d[0];
		$date[] = $d[0]; 
	}
}

$date = array_unique($date);
sort($date);

if(count($date) > 15 && count($date) <= 30)
	$num = 2;
elseif(count($date) > 30 && count($date) <= 60)
	$num = 4;
elseif(count($date) > 60 && count($date) <= 90)
	$num = 8;
else
	$num = 1;
	
$dateR = array_reverse($date);

$i = 0;
$x = 0;
foreach($dateR as $key=>$val)
{
	if($i == $num)
	{
		$i = 0;
	}
	if($i == 0)
	{
		$d = explode("-",$val);
		$dateN[$x] = $d[2].".".$d[1];
		$dateP[$val] = $x+1;
		
		++$x;
	}
	
	++$i;
}
foreach($date as $key=>$val)
{
	$dateFP[$val] = $key+1;
}

$arrayX = array_reverse($dateN);

$MinX = min($dateP);
$MaxX = max($dateP);

if($graph_type == "TOP10")
{
	$arrayY = array(100,50,40,30,20,10,9,8,7,6,5,4,3,2,1);
	$MinY = 1;
	$MaxY = 15;
}
else
{
	$arrayY = array(100,90,80,70,60,50,40,30,20,10,0);
	$MinY = 0;
	$MaxY = 100;
}

$arrTTF_FONT = array();

DrawCoordinatGrid($arrayX, $arrayY, $width, $height, $ImageHandle, "FFFFFF", "B1B1B1", "000000", 15, 2, $arrTTF_FONT);

$i = 0;
foreach($arGraphParam as $val)
{
	$arrX = "";
	$arrY = "";
	$color = "";
	
	foreach($val as $val2)
	{
		if($graph_type == "TOP10")
		{
			if($val2[1] <= 0)
			{
				$coordY = 0.8;
			}
			elseif($val2[1] <= 10 && $val2[1] > 0)
			{
				$coordY = 15-$val2[1]+1;
			}
			elseif($val2[1] > 10 && $val2[1] <= 50)
			{
				$coordY = 15 - ($val2[1]/10 + 9) + 1;
			}
			elseif($val2[1] > 50 && $val2[1] <= 100)
			{
				$coordY = 15 - (15 - 1/(50 / (100 - $val2[1])) ) + 1;
			}
		}
		else
		{
			$coordY = $val2[1] > 0 ? 100-$val2[1] : -1;
		}
		
		$x = $dateFP[$val2[0]];
		$xx = $x - ($x - 1)*($num - 1)*(1/$num);
		$arrX[] = $xx;
		$arrY[] = $coordY;
	}
	
	if($MaxX < $arrX[0])
	{
		$arrXx = array();
		unset($arrX[count($arrX)-1]);
		foreach($arrX as $val)
			$arrXx[] = $val - 1/$num;
		$arrX = $arrXx;
		
		$arrYy = array();
		unset($arrY[count($arrY)-1]);
		foreach($arrY as $val)
			$arrYy[] = $val;
		$arrY = $arrYy;
	}
	
	Graf($arrX, $arrY, $ImageHandle, $MinX, $MaxX, $MinY, $MaxY, $arColor[$i], "N");
	
	++$i;
}

ShowImageHeader($ImageHandle);
?>