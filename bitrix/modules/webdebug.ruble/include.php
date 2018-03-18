<?
CModule::AddAutoloadClasses(
	"webdebug.ruble",
	array(
		"CWebdebugRuble" => "classes/general/CWebdebugRuble.php",
	)
);
function CurrencyFormat_Ruble($Value, $Currency="RUB", $Decimals=0, $dec_point='.', $thousands_sep=' ') {
	$Title = COption::GetOptionString("webdebug.ruble", "webdebug_ruble_title", "");
	if ($Title) $Title = ' title=\''.$Title.'\'';
	$Price = number_format($Value, $Decimals, $dec_point, $thousands_sep);
	$RubleChar = COption::GetOptionString("webdebug.ruble", "webdebug_ruble_font_char", "a");
	$RubleChar = '<span class=\'webdebug-ruble-symbol\''.$Title.'>'.$RubleChar.'</span>';
	$Space = COption::GetOptionString("webdebug.ruble", "webdebug_ruble_add_space", "Y")=="Y" ? " " : "";
	if (COption::GetOptionString("webdebug.ruble", "webdebug_ruble_symbol_location", "R")=="R") {
		$Price = $Price.$Space.$RubleChar;
	} else {
		$Price = $RubleChar.$Space.$Price;
	}
	return $Price;
}
function Webdebug_RubleSymbol($Style="font-size:120%") {
	$Title = COption::GetOptionString("webdebug.ruble", "webdebug_ruble_title", "");
	if ($Title) $Title = ' title=\''.$Title.'\'';
	$RubleChar = COption::GetOptionString("webdebug.ruble", "webdebug_ruble_font_char", "a");
	if ($Style!==false) {
		$Style = " style=\"{$Style}\"";
	} else {
		$Style = "";
	}
	$RubleChar = '<span class=\'webdebug-ruble-symbol\''.$Style.$Title.'>'.$RubleChar.'</span>';
	return $RubleChar;
}
?>