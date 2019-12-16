<?
/************************************
*
* Extension for Filter
* last update 27.06.2014
*
************************************/

IncludeModuleLangFile(__FILE__);

class RSDevFuncFilterExtension extends RSDevFunc
{
	function GetTo4round($number)
	{
		if($number<=100)
		{
			return 100;
		} elseif($number<=250)
		{
			return 100;
		} elseif($number<=500)
		{
			return 250;
		} elseif($number<=1000)
		{
			return 500;
		} elseif($number<=2500)
		{
			return 1000;
		} elseif($number<=5000)
		{
			return 2500;
		} elseif($number<=10000)
		{
			return 5000;
		} elseif($number<=25000)
		{
			return 10000;
		} elseif($number<=50000)
		{
			return 25000;
		} elseif($number<=100000)
		{
			return 50000;
		} elseif($number<=250000)
		{
			return 100000;
		} elseif($number<=500000)
		{
			return 250000;
		} elseif($number<=750000)
		{
			return 500000;
		} elseif($number<=1000000)
		{
			return 750000;
		} elseif($number<=1250000)
		{
			return 1000000;
		} elseif($number<=1500000)
		{
			return 1250000;
		} elseif($number<=1750000)
		{
			return 1500000;
		} elseif($number<=10000000)
		{
			return 1750000;
		}
	}
	
	function RoundCustom($number,$to,$direction="simple")
	{
		if($direction=="simple")
		{
			$i = round($number/$to,0)*$to;
			return $i>0 ? $i : $to;
		} elseif($direction=="big")
		{
			$i = round(ceil($number/$to),0)*$to;
			return $i>0 ? $i : $to;
		} elseif($direction=="small")
		{
			$i = round(floor($number/$to),0)*$to;
			return $i>0 ? $i : $to;
		}
	}
}