<?
function Number2Word_Rus($source, $IS_MONEY = "Y")
{
	$result = "";

	// k - копейки
	if ($IS_MONEY == "Y")
	{
		$source = DoubleVal($source);

		$dotpos = strpos($source, ".");
		if ($dotpos === false)
		{
			$ipart = $source;
			$fpart = "";
		}
		else
		{
			$ipart = substr($source, 0, $dotpos);
			$fpart = substr($source, $dotpos + 1);
		}

		$fpart = substr($fpart, 0, 2);
		while (strlen($fpart)<2) $fpart .= "0";
	}
	else
	{
		$source = IntVal($source);
		$ipart = $source;
		$fpart = "";
	}

	while ($ipart[0]=="0") $ipart = substr($ipart, 1);

	$ipart1 = StrRev($ipart);
	$ipart = "";
	$i = 0;
	while ($i<strlen($ipart1))
	{
		$ipart_tmp = $ipart1[$i];
		// t - тысячи; m - милионы; b - миллиарды;
		// e - единицы; d - десятки; c - сотни;
		if ($i % 3 == 0)
		{
			if ($i==0) $ipart_tmp .= "e";
			elseif ($i==3) $ipart_tmp .= "et";
			elseif ($i==6) $ipart_tmp .= "em";
			elseif ($i==9) $ipart_tmp .= "eb";
			else $ipart_tmp .= "x";
		}
		elseif ($i % 3 == 1) $ipart_tmp .= "d";
		elseif ($i % 3 == 2) $ipart_tmp .= "c";
		$ipart = $ipart_tmp.$ipart;
		$i++;
	}

	if ($IS_MONEY == "Y")
	{
		$result = $ipart.".".$fpart."k";
	}
	else
	{
		$result = $ipart;
	}

	if ($result[0] == ".")
		$result = "ноль ".$result;

	$result = str_replace("0c0d0et", "", $result);
	$result = str_replace("0c0d0em", "", $result);
	$result = str_replace("0c0d0eb", "", $result);

	$result = str_replace("0c", "", $result);
	$result = str_replace("1c", "сто ", $result);
	$result = str_replace("2c", "двести ", $result);
	$result = str_replace("3c", "триста ", $result);
	$result = str_replace("4c", "четыреста ", $result);
	$result = str_replace("5c", "пятьсот ", $result);
	$result = str_replace("6c", "шестьсот ", $result);
	$result = str_replace("7c", "семьсот ", $result);
	$result = str_replace("8c", "восемьсот ", $result);
	$result = str_replace("9c", "девятьсот ", $result);

	$result = str_replace("1d0e", "десять ", $result);
	$result = str_replace("1d1e", "одиннадцать ", $result);
	$result = str_replace("1d2e", "двенадцать ", $result);
	$result = str_replace("1d3e", "тринадцать ", $result);
	$result = str_replace("1d4e", "четырнадцать ", $result);
	$result = str_replace("1d5e", "пятнадцать ", $result);
	$result = str_replace("1d6e", "шестнадцать ", $result);
	$result = str_replace("1d7e", "семнадцать ", $result);
	$result = str_replace("1d8e", "восемнадцать ", $result);
	$result = str_replace("1d9e", "девятнадцать ", $result);

	$result = str_replace("0d", "", $result);
	$result = str_replace("2d", "двадцать ", $result);
	$result = str_replace("3d", "тридцать ", $result);
	$result = str_replace("4d", "сорок ", $result);
	$result = str_replace("5d", "пятьдесят ", $result);
	$result = str_replace("6d", "шестьдесят ", $result);
	$result = str_replace("7d", "семьдесят ", $result);
	$result = str_replace("8d", "восемьдесят ", $result);
	$result = str_replace("9d", "девяносто ", $result);

	$result = str_replace("0e", "", $result);
	$result = str_replace("5e", "пять ", $result);
	$result = str_replace("6e", "шесть ", $result);
	$result = str_replace("7e", "семь ", $result);
	$result = str_replace("8e", "восемь ", $result);
	$result = str_replace("9e", "девять ", $result);

	if ($IS_MONEY == "Y")
	{
		$result = str_replace("1e.", "один рубль ", $result);
		$result = str_replace("2e.", "два рубля ", $result);
		$result = str_replace("3e.", "три рубля ", $result);
		$result = str_replace("4e.", "четыре рубля ", $result);
	}
	else
	{
		$result = str_replace("1e", "один ", $result);
		$result = str_replace("2e", "два ", $result);
		$result = str_replace("3e", "три ", $result);
		$result = str_replace("4e", "четыре ", $result);
	}

	$result = str_replace("1et", "одна тысяча ", $result);
	$result = str_replace("2et", "две тысячи ", $result);
	$result = str_replace("3et", "три тысячи ", $result);
	$result = str_replace("4et", "четыре тысячи ", $result);
	$result = str_replace("1em", "один миллион ", $result);
	$result = str_replace("2em", "два миллиона ", $result);
	$result = str_replace("3em", "три миллиона ", $result);
	$result = str_replace("4em", "четыре миллиона ", $result);
	$result = str_replace("1eb", "один миллиард ", $result);
	$result = str_replace("2eb", "два миллиарда ", $result);
	$result = str_replace("3eb", "три миллиарда ", $result);
	$result = str_replace("4eb", "четыре миллиарда ", $result);

	if ($IS_MONEY == "Y")
	{
		$result = str_replace("11k", "11 копеек", $result);
		$result = str_replace("12k", "12 копеек", $result);
		$result = str_replace("13k", "13 копеек", $result);
		$result = str_replace("14k", "14 копеек", $result);
		$result = str_replace("1k", "1 копейка", $result);
		$result = str_replace("2k", "2 копейки", $result);
		$result = str_replace("3k", "3 копейки", $result);
		$result = str_replace("4k", "4 копейки", $result);
	}

	if ($IS_MONEY == "Y")
		$result = str_replace(".", "рублей ", $result);

	$result = str_replace("t", "тысяч ", $result);
	$result = str_replace("m", "миллионов ", $result);
	$result = str_replace("b", "миллиардов ", $result);

	if ($IS_MONEY == "Y")
		$result = str_replace("k", " копеек", $result);

	return (ToUpper(substr($result, 0, 1)) . substr($result, 1));
}
?>
