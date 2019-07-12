<?php



class _emisc
{


	static function ruscomp($number, $compl)
	{

		$comp = explode(',', $compl);
		//$comp[0]=""; # 0, 5 минут/секунд
		//$comp[1]="а"; # 1 минута/секунда
		//$comp[2]="ы"; # 3 минуты/секунды

	    if ($number==0 or ($number%10)==0) {return $comp[0];}
	    if ($number>=5 && $number<=20) {return $comp[0];}
	    if ($number%10>=5 && $number%10<=9) {return $comp[0];}
	    if (($number%10)==1) {return $comp[1];}
	    if (($number%10)>=2 && ($number%10)<=4) {return $comp[2];}
	}





	static function zerofill($number, $threshold) // function to add leading zeros when necessary
	{
		return sprintf('%0'.$threshold.'s', $number);
	}





	static function nf($price, $decimals = 2)
	{
		$n = rtrim(number_format($price, $decimals, '.', ''), '0');

		return rtrim($n, '.');
	}


	static function pf($price, $decimals = 0, $dsep = '.', $tsep = '&nbsp;')
	{		return str_replace('#', $tsep, number_format($price, $decimals, $dsep, '#'));	}


	static function df($date, $format = 'd.m.Y')
	{		if (!is_numeric($date)) $date = strtotime($date);

		return date($format, $date);	}




	static function atrim($array)
	{
		return array_map('trim', $array);
	}

	static function _explode($delim, $str)
	{
		return _emisc::atrim(explode($delim, $str));
	}


}





?>