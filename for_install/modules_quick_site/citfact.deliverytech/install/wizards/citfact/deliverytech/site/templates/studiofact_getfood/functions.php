<?
if (!function_exists("int_format")) {
	function int_format ($number, $decimals) {
		if ($decimals < 1) { $decimals = 0; }
		$number = number_format(floatVal($number), $decimals, ',', ' ');
		return $number;
	}
}
?>